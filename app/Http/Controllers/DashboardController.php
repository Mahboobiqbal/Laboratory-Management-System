<?php

namespace App\Http\Controllers;

use App\Models\MainCompanys;
use App\Models\Payments;
use App\Models\ReferralCommission;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Bills;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cacheTtl = 300;
        $company = Cache::remember('dashboard_company', $cacheTtl, function () {
            return MainCompanys::first();
        });

        if (!$company) {
            return view('maincompany.maincompany');
        }

        $totals = Cache::remember('dashboard_totals', $cacheTtl, function () {
            $totalBilled = Bills::sum('amount') ?? 0;
            $totalPaymentsPaid = Payments::sum('amount') ?? 0;
            $totalCommissionsPaid = ReferralCommission::where('status', 'paid')
                ->sum('commission_amount') ?? 0;
            $totalPaid = $totalPaymentsPaid + $totalCommissionsPaid;
            $totalExpenses = Expense::sum('amount') ?? 0;
            $netBalance = $totalPaid - $totalExpenses;
            $outstandingBalance = $totalBilled - $totalPaid;

            return compact(
                'totalBilled',
                'totalPaymentsPaid',
                'totalCommissionsPaid',
                'totalPaid',
                'totalExpenses',
                'netBalance',
                'outstandingBalance'
            );
        });

        $today = Carbon::today();
        $todayKey = $today->format('Y-m-d');
        $hasPaymentsDateColumn = Cache::remember('payments_has_date_column', 3600, function () {
            return Schema::hasColumn('payments', 'date');
        });

        $dailyStats = Cache::remember("dashboard_daily_stats_{$todayKey}", $cacheTtl, function () use ($today, $hasPaymentsDateColumn) {
            $todayStart = $today->copy()->startOfDay();
            $todayEnd = $today->copy()->endOfDay();

            $billedToday = Bills::where(function ($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->orWhereBetween('updated_at', [$todayStart, $todayEnd]);
            })->sum('amount') ?? 0;

            if ($hasPaymentsDateColumn) {
                $paidToday = Payments::where(function ($q) use ($todayStart, $todayEnd, $today) {
                    $q->whereBetween('created_at', [$todayStart, $todayEnd])
                        ->orWhereDate('date', $today);
                })->sum('amount') ?? 0;
                $paymentsCountToday = Payments::where(function ($q) use ($todayStart, $todayEnd, $today) {
                    $q->whereBetween('created_at', [$todayStart, $todayEnd])
                        ->orWhereDate('date', $today);
                })->count();
            } else {
                $paidToday = Payments::whereBetween('created_at', [$todayStart, $todayEnd])->sum('amount') ?? 0;
                $paymentsCountToday = Payments::whereBetween('created_at', [$todayStart, $todayEnd])->count();
            }

            $paidCommissionsToday = ReferralCommission::where('status', 'paid')
                ->whereBetween('updated_at', [$todayStart, $todayEnd])
                ->sum('commission_amount') ?? 0;

            $commissionsCountToday = ReferralCommission::where('status', 'paid')
                ->whereBetween('updated_at', [$todayStart, $todayEnd])
                ->count();

            $paidToday = ($paidToday ?? 0) + $paidCommissionsToday;
            $billsCountToday = Bills::where(function ($q) use ($todayStart, $todayEnd) {
                $q->whereBetween('created_at', [$todayStart, $todayEnd])
                    ->orWhereBetween('updated_at', [$todayStart, $todayEnd]);
            })->count();

            $expensesToday = Expense::whereDate('expense_date', $today)->sum('amount') ?? 0;

            $commissionsPendingToday = ReferralCommission::where('status', 'pending')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->sum('commission_amount') ?? 0;

            $balanceToday = $billedToday - $expensesToday;

            return compact(
                'billedToday',
                'paidToday',
                'expensesToday',
                'balanceToday',
                'commissionsPendingToday',
                'billsCountToday',
                'paymentsCountToday',
                'commissionsCountToday',
                'paidCommissionsToday'
            );
        });

        $end = Carbon::now()->endOfMonth();
        $monthlyKey = $end->format('Y-m');
        $monthlyStats = Cache::remember("dashboard_monthly_{$monthlyKey}", $cacheTtl, function () use ($end) {
            $start = (clone $end)->subMonths(11)->startOfMonth();

            $period = [];
            $cursor = (clone $start);
            while ($cursor->lte($end)) {
                $period[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }

            $billedRows = DB::table('bills')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
                ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $paidPaymentsRows = DB::table('payments')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
                ->whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $paidCommissionsRows = DB::table('referral_commissions')
                ->where('status', 'paid')
                ->select(DB::raw("DATE_FORMAT(updated_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(commission_amount),0) as total'))
                ->whereBetween('updated_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $paidRows = [];
            foreach ($period as $month) {
                $paidRows[$month] = ($paidPaymentsRows[$month] ?? 0) + ($paidCommissionsRows[$month] ?? 0);
            }

            $labels = array_map(function ($m) {
                return Carbon::createFromFormat('Y-m', $m)->format('M Y');
            }, $period);
            $billedData = array_map(function ($m) use ($billedRows) {
                return isset($billedRows[$m]) ? (float)$billedRows[$m] : 0;
            }, $period);
            $paidData = array_map(function ($m) use ($paidRows) {
                return isset($paidRows[$m]) ? (float)$paidRows[$m] : 0;
            }, $period);

            return compact('labels', 'billedData', 'paidData');
        });

        $dailyChart = Cache::remember("dashboard_daily_chart_{$todayKey}", $cacheTtl, function () use ($today) {
            $dailyEnd = $today->copy()->endOfDay();
            $dailyStart = $today->copy()->subDays(29)->startOfDay();

            $dailyPeriod = [];
            $dCursor = (clone $dailyStart);
            while ($dCursor->lte($dailyEnd)) {
                $dailyPeriod[] = $dCursor->format('Y-m-d');
                $dCursor->addDay();
            }

            $dailyBilledRows = DB::table('bills')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(amount),0) as total'))
                ->whereBetween('created_at', [$dailyStart, $dailyEnd])
                ->groupBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $dailyPaidPaymentsRows = DB::table('payments')
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(amount),0) as total'))
                ->whereBetween('created_at', [$dailyStart, $dailyEnd])
                ->groupBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $dailyPaidCommissionsRows = DB::table('referral_commissions')
                ->where('status', 'paid')
                ->select(DB::raw("DATE_FORMAT(updated_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(commission_amount),0) as total'))
                ->whereBetween('updated_at', [$dailyStart, $dailyEnd])
                ->groupBy('day')
                ->pluck('total', 'day')
                ->toArray();

            $dailyLabels = array_map(function ($d) {
                return Carbon::createFromFormat('Y-m-d', $d)->format('M d');
            }, $dailyPeriod);
            $dailyBilledData = array_map(function ($d) use ($dailyBilledRows) {
                return isset($dailyBilledRows[$d]) ? (float)$dailyBilledRows[$d] : 0;
            }, $dailyPeriod);

            $dailyPaidData = [];
            foreach ($dailyPeriod as $day) {
                $dailyPaidData[] = ($dailyPaidPaymentsRows[$day] ?? 0) + ($dailyPaidCommissionsRows[$day] ?? 0);
            }

            return compact('dailyLabels', 'dailyBilledData', 'dailyPaidData');
        });

        return view('dashboard', [
            'company' => $company,
            'totalBalance' => $totals['netBalance'],
            'totalBilled' => $totals['totalBilled'],
            'totalPaid' => $totals['totalPaid'],
            'totalExpenses' => $totals['totalExpenses'],
            'billedToday' => $dailyStats['billedToday'],
            'paidToday' => $dailyStats['paidToday'],
            'expensesToday' => $dailyStats['expensesToday'],
            'balanceToday' => $dailyStats['balanceToday'],
            'commissionsPendingToday' => $dailyStats['commissionsPendingToday'],
            'billsCountToday' => $dailyStats['billsCountToday'],
            'paymentsCountToday' => $dailyStats['paymentsCountToday'],
            'commissionsCountToday' => $dailyStats['commissionsCountToday'] ?? 0,
            'chartLabels' => $monthlyStats['labels'],
            'chartBilled' => $monthlyStats['billedData'],
            'chartPaid' => $monthlyStats['paidData'],
            'dailyLabels' => $dailyChart['dailyLabels'],
            'dailyBilled' => $dailyChart['dailyBilledData'],
            'dailyPaid' => $dailyChart['dailyPaidData'],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $type = $request->query('type', 'monthly');
        $filename = $type . '_data_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');

            if ($type === 'monthly') {
                fputcsv($file, ['Month', 'Billed Amount', 'Paid Amount']);

                $end = Carbon::now()->endOfMonth();
                $start = (clone $end)->subMonths(11)->startOfMonth();

                // Re-query logic for simplicity or refactor to shared service. 
                // For now, duplicating query logic for export to ensure fresh data.

                $period = [];
                $cursor = (clone $start);
                while ($cursor->lte($end)) {
                    $period[] = $cursor->format('Y-m');
                    $cursor->addMonth();
                }

                $billedRows = DB::table('bills')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();

                $paidPaymentsRows = DB::table('payments')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(amount),0) as total'))
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();

                $paidCommissionsRows = DB::table('referral_commissions')
                    ->where('status', 'paid')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COALESCE(SUM(commission_amount),0) as total'))
                    ->whereBetween('created_at', [$start, $end])
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();

                foreach ($period as $month) {
                    $billed = $billedRows[$month] ?? 0;
                    $paid = ($paidPaymentsRows[$month] ?? 0) + ($paidCommissionsRows[$month] ?? 0);
                    fputcsv($file, [$month, $billed, $paid]);
                }
            } else {
                // Daily
                fputcsv($file, ['Date', 'Billed Amount', 'Paid Amount']);

                $dailyEnd = Carbon::today()->endOfDay();
                $dailyStart = Carbon::today()->subDays(29)->startOfDay();

                $dailyPeriod = [];
                $dCursor = (clone $dailyStart);
                while ($dCursor->lte($dailyEnd)) {
                    $dailyPeriod[] = $dCursor->format('Y-m-d');
                    $dCursor->addDay();
                }

                $dailyBilledRows = DB::table('bills')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(amount),0) as total'))
                    ->whereBetween('created_at', [$dailyStart, $dailyEnd])
                    ->groupBy('day')
                    ->pluck('total', 'day')->toArray();

                $dailyPaidPaymentsRows = DB::table('payments')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(amount),0) as total'))
                    ->whereBetween('created_at', [$dailyStart, $dailyEnd])
                    ->groupBy('day')
                    ->pluck('total', 'day')->toArray();

                $dailyPaidCommissionsRows = DB::table('referral_commissions')
                    ->where('status', 'paid')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), DB::raw('COALESCE(SUM(commission_amount),0) as total'))
                    ->whereBetween('created_at', [$dailyStart, $dailyEnd])
                    ->groupBy('day')
                    ->pluck('total', 'day')->toArray();

                foreach ($dailyPeriod as $day) {
                    $billed = $dailyBilledRows[$day] ?? 0;
                    $paid = ($dailyPaidPaymentsRows[$day] ?? 0) + ($dailyPaidCommissionsRows[$day] ?? 0);
                    fputcsv($file, [$day, $billed, $paid]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lab = new MainCompanys;
        $lab->id = 1;
        $lab->lab_name = $request->lab_name;
        $lab->lab_address = $request->lab_address;
        $lab->lab_phone = $request->lab_phone;
        $lab->lab_email = $request->lab_email;
        $lab->balance = 0;
        if ($request->hasFile('lab_image')) {
            $file = $request->file('lab_image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/assets/HMS/lablogo/'), $filename);
            $lab->lab_image = $filename;
        }
        $lab->save();
        return response()->json(['success' => 'Data Add successfully.']);
    }

    public function details()
    {
        return view('maincompany.details');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
