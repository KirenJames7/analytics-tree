<div class="header">
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-2 bg-blue hover-zoom-effect admin align-center waves-effect" id="rolemanagement">
                <div class="icon">
                    <i class="material-icons">how_to_reg</i>
                </div>
                <div class="content">
                    <div class="text">ROLE MANAGEMENT</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-2 bg-red hover-zoom-effect admin align-center waves-effect" id="systemlogs">
                <div class="icon">
                    <i class="material-icons">data_usage</i>
                </div>
                <div class="content">
                    <div class="text">SYSTEM LOGS</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    $.ajax({
        url: '{{ URL::asset('/js/administration.js') }}',
        dataType: 'script',
        cache: true
    });
</script>