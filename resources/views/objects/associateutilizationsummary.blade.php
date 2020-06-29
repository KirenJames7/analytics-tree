<div class="header">
    @include('objects.reportfilter')
</div>
<div class="body table-responsive clusterize clusterize-scroll" id='scrollArea'>
    <table class="table table-bordered table-hover table-striped treetable" id="associate-utilization-summary" style="">
        <caption>
            <button class='btn btn-default xlsx' id='xlsx' style='float:left'>Export to Excel</button>
            <span class="excolmsg" style='float:right'>Sorry, Expand All is unavailable if records are more than 1500 to avoid slowness</span>
            <button class='btn btn-default excol' id='associate-utilization-summarycollapse' style='float:right'>Collapse All</button>
            <button class='btn btn-default excol' id='associate-utilization-summaryexpand' style='float:right'>Expand All</button>
        </caption>
        <thead>
            <tr>
                <th>Resource Name</th>
                <th>Year</th>
                <th>Month</th>
                <th>Project Type</th>
                <th>Project Code</th>
                <th>Resource Department</th>
                <th>Project Department</th>
                <th>Allocated Hours</th>
                <th>Actual Effort</th>
                <th>Actual Effort %</th>
                <th>Billed Effort</th>
                <th>Billed Effort %</th>
            </tr>
        </thead>
        <tbody id="contentArea" class="clusterize-content">

        </tbody>
    </table>
</div>
<script type="text/javascript" async>
    $.ajax({
        url: '{{ URL::asset('/js/jquery.treetable.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/report.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/treetable.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/xlsx/dist/xlsx.full.min.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/FileSaver.min.js') }}',
        dataType: 'script',
        cache: true
    });
</script>