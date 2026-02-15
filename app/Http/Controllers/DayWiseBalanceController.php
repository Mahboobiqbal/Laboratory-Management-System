<?php


namespace App\Http\Controllers;

use App\Models\Bills;
use App\Models\Payments;
use App\Models\ReferralCommission;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DayWiseBalanceController extends Controller
{
    /**
     * Display the day-wise balance page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('balance.day_wise_balance');
    }

    /**
     * Get balance data for a specific date
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalanceForDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
        ]);

        $date = Carbon::parse($request->date);
        $dateStart = $date->copy()->startOfDay();
        $dateEnd = $date->copy()->endOfDay();

        $hasPaymentsDateColumn = Schema::hasColumn('payments', 'date');

        // Calculate billed amount for the selected date
        $billedAmount = Bills::where(function ($q) use ($dateStart, $dateEnd) {
            $q->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orWhereBetween('updated_at', [$dateStart, $dateEnd]);
        })->sum('amount') ?? 0;

        // Calculate payments for the selected date
        if ($hasPaymentsDateColumn) {
            $paymentsAmount = Payments::where(function ($q) use ($dateStart, $dateEnd, $date) {
                $q->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->orWhereDate('date', $date);
            })->sum('amount') ?? 0;

            $paymentsCount = Payments::where(function ($q) use ($dateStart, $dateEnd, $date) {
                $q->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->orWhereDate('date', $date);
            })->count();

            $paymentsList = Payments::where(function ($q) use ($dateStart, $dateEnd, $date) {
                $q->whereBetween('created_at', [$dateStart, $dateEnd])
                    ->orWhereDate('date', $date);
            })->with('bill.patient')->get();
        } else {
            $paymentsAmount = Payments::whereBetween('created_at', [$dateStart, $dateEnd])->sum('amount') ?? 0;
            $paymentsCount = Payments::whereBetween('created_at', [$dateStart, $dateEnd])->count();
            $paymentsList = Payments::whereBetween('created_at', [$dateStart, $dateEnd])->with('bill.patient')->get();
        }

        // Calculate paid commissions for the selected date
        $paidCommissions = ReferralCommission::where('status', 'paid')
            ->whereBetween('updated_at', [$dateStart, $dateEnd])
            ->with('referral')
            ->get();

        $paidCommissionsAmount = $paidCommissions->sum('commission_amount') ?? 0;
        $paidCommissionsCount = $paidCommissions->count();

        // Calculate pending commissions for the selected date
        $pendingCommissions = ReferralCommission::where('status', 'pending')
            ->whereBetween('created_at', [$dateStart, $dateEnd])
            ->with('referral')
            ->get();

        $pendingCommissionsAmount = $pendingCommissions->sum('commission_amount') ?? 0;
        $pendingCommissionsCount = $pendingCommissions->count();

        // Calculate total paid (payments + commissions)
        $totalPaid = $paymentsAmount + $paidCommissionsAmount;

        // Calculate expenses for the selected date
        $expensesAmount = Expense::whereDate('expense_date', $date)->sum('amount') ?? 0;
        $expensesList = Expense::whereDate('expense_date', $date)->get();
        $expensesCount = $expensesList->count();

        // Calculate balance: billed - expenses (NOT deducting commissions)
        $balance = $billedAmount - $expensesAmount;

        // Calculate bills count
        $billsCount = Bills::where(function ($q) use ($dateStart, $dateEnd) {
            $q->whereBetween('created_at', [$dateStart, $dateEnd])
                ->orWhereBetween('updated_at', [$dateStart, $dateEnd]);
        })->count();

        return response()->json([
            'success' => true,
            'date' => $date->format('Y-m-d'),
            'formatted_date' => $date->format('F d, Y'),
            'data' => [
                'billed_amount' => $billedAmount,
                'payments_amount' => $paymentsAmount,
                'paid_commissions_amount' => $paidCommissionsAmount,
                'pending_commissions_amount' => $pendingCommissionsAmount,
                'total_paid' => $totalPaid,
                'expenses_amount' => $expensesAmount,
                'balance' => $balance,
                'bills_count' => $billsCount,
                'payments_count' => $paymentsCount,
                'paid_commissions_count' => $paidCommissionsCount,
                'pending_commissions_count' => $pendingCommissionsCount,
                'expenses_count' => $expensesCount,
            ],
            'breakdown' => [
                'payments' => $paymentsList->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'bill_id' => $payment->bill_id,
                        'patient_name' => $payment->bill && $payment->bill->patient ? $payment->bill->patient->name : 'N/A',
                        'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'paid_commissions' => $paidCommissions->map(function ($commission) {
                    return [
                        'id' => $commission->id,
                        'amount' => $commission->commission_amount,
                        'referral_name' => $commission->referral ? $commission->referral->name : 'N/A',
                        'bill_id' => $commission->bill_id,
                        'updated_at' => $commission->updated_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'pending_commissions' => $pendingCommissions->map(function ($commission) {
                    return [
                        'id' => $commission->id,
                        'amount' => $commission->commission_amount,
                        'referral_name' => $commission->referral ? $commission->referral->name : 'N/A',
                        'bill_id' => $commission->bill_id,
                        'created_at' => $commission->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'expenses' => $expensesList->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'amount' => $expense->amount,
                        'category' => $expense->category ?? 'General',
                        'description' => $expense->description ?? 'N/A',
                        'expense_date' => $expense->expense_date,
                    ];
                }),
            ],
        ]);
    }
}

