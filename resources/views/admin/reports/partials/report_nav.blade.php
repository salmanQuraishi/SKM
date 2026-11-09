<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-bold text-uppercase d-none d-md-inline" style="font-size: 11px; letter-spacing: 1px;">Reports:</span>
            <ul class="nav nav-pills gap-1 p-1 bg-light rounded-3 border">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 fw-bold font-sm {{ request()->routeIs('admin.reports.business*') || request()->routeIs('admin.reports.sales*') ? 'active bg-danger text-white shadow-sm' : 'text-secondary' }}" href="{{ route('admin.reports.business') }}">
                        <i class="fa-solid fa-briefcase me-1"></i> Business Rep.
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 fw-bold font-sm {{ request()->routeIs('admin.reports.inventory*') ? 'active bg-danger text-white shadow-sm' : 'text-secondary' }}" href="{{ route('admin.reports.inventory') }}">
                        <i class="fa-solid fa-boxes-stacked me-1"></i> Inventory Rep.
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 fw-bold font-sm {{ request()->routeIs('admin.reports.tax*') ? 'active bg-danger text-white shadow-sm' : 'text-secondary' }}" href="{{ route('admin.reports.tax') }}">
                        <i class="fa-solid fa-receipt me-1"></i> Tax Rep. (GST)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 fw-bold font-sm {{ request()->routeIs('admin.reports.payment*') ? 'active bg-danger text-white shadow-sm' : 'text-secondary' }}" href="{{ route('admin.reports.payment') }}">
                        <i class="fa-solid fa-credit-card me-1"></i> Payment Rep.
                    </a>
                </li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-semibold">
                <i class="fa-solid fa-print me-1"></i> Print
            </button>
        </div>
    </div>
</div>
