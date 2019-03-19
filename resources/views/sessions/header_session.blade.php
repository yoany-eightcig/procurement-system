<div class="d-flex justify-content-between">
    <div class="card-header">Parts</div>
    @php
        $exportOptions = false;
        if (count($_GET)) {
            if (array_key_exists('search', $_GET) && array_key_exists('filter_sku', $_GET) && array_key_exists('filter_name', $_GET)) {
                $exportOptions = "search:{$_GET['search']};filter_sku:{$_GET['filter_sku']};filter_name:{$_GET['filter_name']}";    
            }
        }
    @endphp
    <a href="{{ route('exportToExcel', [$reportName, "exportOptions" => $exportOptions]) }}">[ Export To Excel]</a>
</div>
