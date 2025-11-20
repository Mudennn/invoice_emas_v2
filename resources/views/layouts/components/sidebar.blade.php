<aside>
    <div class="sidebar active">
        <div class="menu-btn">
            <span class="material-symbols-outlined"> chevron_left </span>
        </div>
        <div class="logo">
            <div class="image">
                <p class="text-white p-0 m-0"></p>
            </div>
            <div class="close" id="close-btn">
                <span class="material-symbols-outlined"> close </span>
            </div>
        </div>

        <div class="nav">
            <div class="menu" style="width: 100%;">
                <ul>

                    <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}">
                            <span class="material-symbols-outlined">
                                home
                            </span>
                            <span class="text">Dashboard</span></a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="material-symbols-outlined">
                                receipt_long
                            </span>
                            <span class="text">Payments</span>
                            <span class="material-symbols-outlined arrow">
                                keyboard_arrow_down
                            </span>
                        </a>

                        <ul class="sub-menu">

                            <li
                                class="{{ Request::routeIs('invoices.*') || request()->is('invoice_items*') ? 'active' : '' }}">
                                <a href="{{ route('invoices.index') }}">
                                    <span class="text">Invoices</span></a>
                            </li>

                            <li class="{{ Request::routeIs('payments.*') ? 'active' : '' }}">
                                <a href="{{ route('payments.index') }}">
                                    <span class="text">Payments</span></a>
                            </li>

                            <li class="{{ Request::routeIs('receipts.*') ? 'active' : '' }}">
                                <a href="{{ route('receipts.index') }}">
                                    <span class="text">Receipts</span></a>
                            </li>

                            <li class="{{ Request::routeIs('credit_notes.*') ? 'active' : '' }}">
                                <a href="{{ route('credit_notes.index') }}">
                                    <span class="text">Credit Notes</span></a>
                            </li>

                            <li class="{{ Request::routeIs('debit_notes.*') ? 'active' : '' }}">
                                <a href="{{ route('debit_notes.index') }}">
                                    <span class="text">Debit Notes</span></a>
                            </li>

                            <li class="{{ Request::routeIs('refund_notes.*') ? 'active' : '' }}">
                                <a href="{{ route('refund_notes.index') }}">
                                    <span class="text">Refund Notes</span></a>
                            </li>

                            <li class="{{ Request::routeIs('self_billed_invoices.*') ? 'active' : '' }}">
                                <a href="{{ route('self_billed_invoices.index') }}">
                                    <span class="text">Self Billed Invoices</span></a>
                            </li>
                    </li>
                </ul>

                <li class="{{ Request::routeIs('reports.*') ? 'active' : '' }}">
                    <a href="#">
                        <span class="material-symbols-outlined">
                            assessment
                        </span>
                        <span class="text">Reports</span>
                        <span class="material-symbols-outlined arrow">
                            keyboard_arrow_down
                        </span>
                    </a>

                    <ul class="sub-menu">
                        <li class="{{ request()->is('reports') || request()->is('reports/sales*') ? 'active' : '' }}">
                            <a href="{{ route('reports.index') }}"><span class="text">Sales Report</span></a>
                        </li>
                        <li class="{{ Request::routeIs('reports.company.*') ? 'active' : '' }}">
                            <a href="{{ route('reports.company') }}"><span class="text">Company Sales
                                    Report</span></a>
                        </li>
                        <li class="{{ Request::routeIs('reports.is.*') ? 'active' : '' }}">
                            <a href="{{ route('reports.is') }}"><span class="text">Pure Gold Account
                                    Report</span></a>
                        </li>
                    </ul>
                </li>


                {{-- <li class="{{ Request::routeIs('gold_prices.*') ? 'active' : '' }}">
                        <a href="{{ route('gold_prices.index') }}">
                            <span class="material-symbols-outlined">
                                price_change
                            </span>
                            <span class="text">Gold Price</span></a>
                    </li> --}}

                {{-- <li class="{{Request::routeIs('invoice_items.index') ? 'active' : ''}}">
                     <a href="{{route('invoice_items.index')}}">
                    <span class="material-symbols-outlined">
                     work
                     </span>
                     <span class="text">Invoice Items</span></a></li> --}}

                <li class="{{ Request::routeIs('is.*') ? 'active' : '' }}">
                    <a href="{{ route('is.index') }}">
                        <span class="material-symbols-outlined">
                            group_add
                        </span>
                        <span class="text">IS</span></a>
                </li>

                <div class="divider"></div>
                <li class="{{ Request::routeIs('customer_profiles.*') ? 'active' : '' }}">
                    <a href="{{ route('customer_profiles.index') }}">
                        <span class="material-symbols-outlined">
                            group_add
                        </span>
                        <span class="text">Customer Profiles</span></a>
                </li>

                <li class="{{ Request::routeIs('category_products.*') ? 'active' : '' }}">
                    <a href="{{ route('category_products.index') }}">
                        <span class="material-symbols-outlined">
                            inventory_2
                        </span>
                        <span class="text">Product Categories</span></a>
                </li>

                <li class="{{ Request::routeIs('product_details.*') ? 'active' : '' }}">
                    <a href="{{ route('product_details.index') }}">
                        <span class="material-symbols-outlined">
                            list
                        </span>
                        <span class="text">Product Details</span></a>
                </li>


                <div class="divider"></div>

                <li class="{{ Request::routeIs('company_profiles.*') ? 'active' : '' }}">
                    <a href="{{ route('company_profiles.index') }}">
                        <span class="material-symbols-outlined">
                            settings
                        </span>
                        <span class="text">Company Profile</span></a>
                </li>

                <li class="{{ Request::routeIs('password.change') ? 'active' : '' }}">
                    <a href="{{ route('password.change') }}">
                        <span class="material-symbols-outlined">
                            lock_reset
                        </span>
                        <span class="text">Change Password</span></a>
                </li>

                <li>
                    <a class="text" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                        <span class="material-symbols-outlined"> logout </span>
                        <span class="text">Logout</span></a>

                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    </a>
                </li>
            </div>
        </div>
    </div>
</aside>
