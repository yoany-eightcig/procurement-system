<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <span>Inventory</span>
    <a class="nav-link {{ Request::path() == 'home' || Request::path() == 'search' ? 'active' : '' }}" id="v-pills-statistics-tab"  href="{{ route('home') }}" >
        <i class="fas fa-server pr-2"></i> Statistics
    </a>
    <hr class="bg-dark w-100"> <span>Re-Order Report</span>
    <a class="nav-link {{ Request::path() == 'monthlysales' || Request::path() == 'monthlysales/search'  ? 'active' : '' }}" id="v-pills-monthlysales-tab" href="{{ route('monthlySales') }}" >
        <i class="fas fa-clipboard-list pr-2"></i> Monthly Sales
    </a>

    <a class="nav-link {{ Request::path() == 'weeklysales' || Request::path() == 'weeklysales/search' ? 'active' : '' }}" id="v-pills-weeklysales-tab" href="{{ route('weeklySales') }}">
        <i class="fas fa-clipboard-list pr-2"></i> Weekly Sales
    </a>

    <hr class="bg-dark w-100"> <span>Clearance Report</span>

    <a class="nav-link {{ Request::path() == 'clearance/3' || Request::path() == 'clearance/3/search' ? 'active' : '' }}" id="v-pills-settings-tab" href="{{ route('clearance3') }}">
        <i class="fas fa-wrench pr-1"></i> 3 Months
    </a>
    <a class="nav-link {{ Request::path() == 'clearance/6' || Request::path() == 'clearance/6/search' ? 'active' : '' }}" id="v-pills-settings-tab" href="{{ route('clearance6') }}">
        <i class="fas fa-receipt pr-2"></i> 6 Months
    </a>
    <hr class="bg-dark w-100"> <span>Clearance Report</span>
    <a class="nav-link {{ Request::path() == 'home3' ? 'active' : '' }}" id="v-pills-logs-tab" >
        <i class="fas fa-receipt pr-2"></i> Zero Sales
    </a>

</div>