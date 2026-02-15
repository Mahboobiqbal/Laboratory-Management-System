<div class="left-side-menu bg-cards text-body" style="background: var(--bg-cards) !important;">

    <style>
        .enlarged .sidebar-close-btn {
            display: none;
        }
        @media (max-width: 767px) {
            .sidebar-enable .button-menu-mobile {
                display: none;
            }
            body:not(.sidebar-enable) .sidebar-close-btn {
                display: none;
            }
        }
        
        /* Custom styles to make sidebar icons and text larger and stronger */
        #side-menu i {
            font-size: 1.4em;
            font-weight: bold;
        }
        #side-menu span {
            font-size: 1.1em;
            font-weight: bold;
        }
        #side-menu a {
            font-size: 1.1em;
        }
        #side-menu .nav-second-level a {
            font-size: 1em;
            font-weight: 500;
        }
    </style>

    <!-- Sidebar Toggle Button -->
    <!-- <buttonson> -->

    <div class="slimscroll-menu">

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul class="metismenu" id="side-menu">

                <!-- <li class="menu-title text-heading">Home</li> -->

                @if (App\Models\MainCompanys::get()->count() == 0)
                    <li>
                        <a href="{{ route('dashboard') }}" class="waves-effect">
                            <i class="mdi mdi-account-plus"></i>
                            <span class="badge badge-pill badge-primary float-right">New</span>
                            <span>Add Lab Details</span>
                        </a>
                    </li>
                @elseif (Auth::check())
                    @if (Auth::user()->user_type == 'Super Admin' || Auth::user()->user_type == 'Admin')
                        <li>
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-ball-pile"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        

                        

                        <li>
                            <a href="javascript: void(0);">
                                <i class="fad fa-asterisk"></i>
                                <span> Referral Management </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('referrals.patients') }}">
                                        <!-- <i class="fas fa-users"></i> -->
                                        Referrals
                                        
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('referrels.list') }}">
                                        <!-- <i class="fad fa-list"></i> -->
                                        Referral List
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('commissions.dashboard') }}">
                                        <i class="fas fa-chart-line"></i>
                                        Commission Dashboard
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li>
                            <a href="{{ route('employees') }}">
                                <i class="fas fa-user-friends"></i>
                                <span> Employee </span>
                            </a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('activities') }}">
                                <i class="fas fa-user-friends"></i>
                                <span> Activities </span>
                            </a>
                        </li> --}}

                        <!-- <li>
                            <a href="{{ route('Attendance') }}">
                                <i class="fas fa-user-friends"></i>
                                <span> Attendance </span>
                            </a>
                        </li> -->

                        <li>
                            <a href="{{ route('user') }}">
                                <i class="fas fa-users"></i>
                                <span> User Management </span>
                            </a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('activities') }}">
                                <i class="fas fa-user-friends"></i>
                                <span> Activities </span>
                            </a>
                        </li> --}}

                        <!-- <li>
                            <a href="{{ route('Attendance') }}">
                                <i class="fas fa-user-friends"></i>
                                <span> Attendance </span>
                            </a>
                        </li> -->

                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-user-injured"></i>
                                <span> Patient </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('patients.create') }}">Patient Register</a>
                                </li>
                                <li>
                                    <a href="{{ route('patients.list') }}">Patient List</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-vial"></i>
                                <span> Test & Department </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('labtest.index') }}">
                                        <i class="fas fa-flask"></i>
                                        Test Categories
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('departments.index') }}">
                                        <i class="fas fa-building"></i>
                                        Departments
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-boxes"></i>
                                <span> Inventory Managemen </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('inventories') }}">Inventories</a>
                                </li>
                                <li>
                                    <a href="{{ route('inventories.history') }}">Purchase History</a>
                                </li>
                            </ul>
                        </li> -->

                        {{-- <li>
                            <a href="{{ route('referrels.list') }}">
                                <i class="fad fa-asterisk"></i>
                                <span> Referral </span>
                            </a>
                        </li> --}}

                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-money-bill"></i>
                                <span> Patient Billing System </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                {{-- <li>
                                    <a href="{{ route('billing') }}">Bill Create</a>
                                </li> --}}
                                <li>
                                    <a href="{{ route('allbills') }}">All Bill</a>
                                </li>
                            </ul>
                        </li>

                        <!-- <li>
                            <a href="{{ route('pathology') }}">
                                <i class="fas fa-vial"></i>
                                <span> Pathology </span>
                            </a>
                        </li> -->

                        <!-- <li>
                            <a href="{{ route('radiology') }}">
                                <i class="fas fa-skeleton"></i>
                                <span> Radiology </span>
                            </a>
                        </li> -->

                        <!-- <li>
                            <a href="{{ route('Electrocardiography') }}">
                                <i class="fas fa-monitor-heart-rate"></i>
                                <span> Electrocardiography </span>
                            </a>
                        </li> -->
                        <!-- <li>
                            <a href="{{ route('ultrasonography') }}">
                                <i class="fas fa-monitor-heart-rate"></i>
                                <span> Ultrasonography </span>
                            </a>
                        </li> -->

                        {{-- <li>
                            <a href="{{ route('reportbooth') }}">
                                <i class="dripicons-meter"></i>
                                <span> Report Booth </span>
                            </a>
                        </li> --}}

                        <!-- <li>
                            <a href="javascript: void(0);">
                                <i class="dripicons-mail"></i>
                                <span> Financial Management </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('all.bills') }}">Billing History</a>
                                </li>
                                <li>
                                    <a href="{{ route('other.transection') }}">Other Transaction</a>
                                </li>
                                <li>
                                    <a href="{{ route('transection.record') }}">Transaction History</a>
                                </li>

                            </ul>
                        </li> -->

                        <!-- <li>
                            <a href="javascript: void(0);">
                                <i class="dripicons-mail"></i>
                                <span> Report Genaration </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('patientreport') }}">Patient List</a>
                                </li>
                                <li>
                                    <a href="{{ route('ledger') }}">Accounts Statement</a>
                                </li>
                                {{-- <li>
                                <a href="{{ route('expanseledger') }}">Expenses Record</a>
                            </li> --}}
                                <li>
                                    <a href="{{ route('referralreport') }}">Referral Report</a>
                                </li> -->
                        {{-- <li>
                                <a href="#">Test Report</a>
                            </li> --}}

                        {{-- ========================================== --}}
                        {{-- Financial Management & Analysis --}}
                        {{-- ========================================== --}}
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-chart-pie"></i>
                                <span> Financial & Analysis </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li>
                                    <a href="{{ route('financial.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Overview Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('financial.revenue') }}">
                                        <i class="fas fa-chart-line"></i> Revenue Analysis
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('financial.expense-analysis') }}">
                                        <i class="fas fa-money-bill-wave"></i> Expense Analysis
                                    </a>
                                </li>
                                <!-- <li>
                                    <a href="{{ route('financial.doctor-commissions') }}">
                                        <i class="fas fa-user-md"></i> Doctor Commissions
                                    </a>
                                </li> -->
                                <li>
                                    <a href="{{ route('financial.wages') }}">
                                        <i class="fas fa-hand-holding-usd"></i> Wages & Salaries
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('financial.profit-loss') }}">
                                        <i class="fas fa-balance-scale"></i> Profit & Loss
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('financial.monthly-report') }}">
                                        <i class="fas fa-file-invoice-dollar"></i> Monthly Report
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('expenses.index') }}">
                                        <i class="fas fa-receipt"></i> Manage Expenses
                                    </a>
                                </li>
                            </ul>
                        </li>

                            <li>
                            <a href="{{ route('labdetails.show') }}">
                                <i class="fas fa-building"></i>
                                <span> Lab Information </span>
                            </a>
                        </li>


                        <li>
                            <a href="{{ route('settings.index') }}">
                                <i class="fas fa-cog"></i>
                                <span> Settings </span>
                            </a>
                        </li>
            </ul>
            </li>
        @else
            <li>
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-ball-pile"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            @if (Auth::user()->patients == 1)
                <li>
                    <a href="javascript: void(0);">
                        <i class="fas fa-user-injured"></i>
                        <span> Patient </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('patients.create') }}">Patient Register</a>
                        </li>
                        <li>
                            <a href="{{ route('patients.list') }}">Patient List</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->testcategory == 1)
                <li>
                    <a href="{{ route('labtest.index') }}">
                        <i class="fas fa-vial"></i>
                        <span> Test Category </span>
                    </a>

                </li>
            @endif
            @if (Auth::user()->referral == 1)
                <li>
                    <a href="{{ route('referrels.list') }}">
                        <i class="fad fa-asterisk"></i>
                        <span> Referral </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->inventory == 1)
                <li>
                    <a href="javascript: void(0);">
                        <i class="fas fa-boxes"></i>
                        <span> Inventory Managemen </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('inventories') }}">Inventories</a>
                        </li>
                        <li>
                            <a href="{{ route('inventories.history') }}">Purchase History</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->billing == 1 || Auth::user()->billing_add == 1 || Auth::user()->billing_edit == 1 || Auth::user()->billing_delete == 1)
                <li>
                    <a href="javascript: void(0);">
                        <i class="fas fa-money-bill"></i>
                        <span> Patient Billing System </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        @if((Auth::user()->billing_add == 1 || Auth::user()->billing == 1) && Auth::user()->user_type != "Employees")
                        <li>
                            <a href="{{ route('billing') }}">Bill Create</a>
                        </li>
                        @endif
                        @if(Auth::user()->billing_edit == 1 || Auth::user()->billing == 1)
                        <li>
                            <a href="{{ route('allbills') }}">All Bill</a>
                        </li>
                        @endif

                    </ul>
                </li>
            @endif
            @if ((Auth::user()->pathology == 1 || Auth::user()->pathology_add == 1 || Auth::user()->pathology_edit == 1 || Auth::user()->pathology_delete == 1) && Auth::user()->user_type != 'Employees')
                <li>
                    <a href="{{ route('pathology') }}">
                        <i class="fas fa-vial"></i>
                        <span> Pathology </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->radiology == 1 && Auth::user()->user_type != 'Employees')
                <li>
                    <a href="{{ route('radiology') }}">
                        <i class="fas fa-skeleton"></i>
                        <span> Radiology </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->ultrasonography == 1 && Auth::user()->user_type != 'Employees')
                <li>
                    <a href="{{ route('ultrasonography') }}">
                        <i class="fas fa-monitor-heart-rate"></i>
                        <span> Ultrasonography </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->electrocardiography == 1 && Auth::user()->user_type != 'Employees')
                <li>
                    <a href="{{ route('Electrocardiography') }}">
                        <i class="fas fa-monitor-heart-rate"></i>
                        <span> Electrocardiography </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->reportbooth == 1 && Auth::user()->user_type != 'Employees')
                <li>
                    <a href="{{ route('reportbooth') }}">
                        <i class="dripicons-meter"></i>
                        <span> Report Booth </span>
                    </a>
                </li>
            @endif
            @if (Auth::user()->financial == 1)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-mail"></i>
                        <span> Financial Management </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <li>
                            <a href="{{ route('all.bills') }}">Billing History</a>
                        </li>
                        <li>
                            <a href="{{ route('other.transection') }}">Other Transaction</a>
                        </li>
                        <li>
                            <a href="{{ route('transection.record') }}">Transaction History</a>
                        </li>
                        <li>
                            <a href="{{ route('expenses.index') }}">
                                <i class="fas fa-money-bill-wave"></i>
                                Expenses
                            </a>
                        </li>

                    </ul>
                </li>
            @endif

            @if (Auth::user()->report_g == 1)
                <li>
                    <a href="javascript: void(0);">
                        <i class="dripicons-mail"></i>
                        <span> Report Genaration </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        <!-- <li>
                                        <a href="{{ route('patients.list') }}">Patient List</a>
                                    </li> -->
                        <li>
                            <a href="{{ route('ledger') }}">Accounts Statement</a>
                        </li>
                        <li>
                            <a href="{{ route('referralreport') }}">Referral Report</a>
                        </li>
                    </ul>
                </li>
            @endif
            @endif


            @endif


            </ul>

            <!-- User Settings/Profile Section -->
            <!-- <li class="menu-title text-heading">Account</li>
            <li>
                <a href="{{ route('user.profile') }}">
                    <i class="fas fa-user-cog"></i>
                    <span> My Profile </span>
                </a>
            </li> -->

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>