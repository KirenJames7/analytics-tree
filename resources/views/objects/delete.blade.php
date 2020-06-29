<div class="body">
    <div>
        <form method="POST" class="dropzone align-center" id="dataDemolisher">
            <div class="fallback">
                <div class="dz-message">
                <i class="material-icons" style="font-size: 48px">delete_forever</i>
                <br />
                <label style="font-size: 26px">Delete Data Set</label>
            </div>
                <table class="fileGrid">
                    <tr>
                        <td>
                            <div class="form-group">
                                <strong>Select Year&nbsp;<span class="text-danger">*</span></strong>
                                <div class="input-group">
                                    <div class="form-line">
                                        <select class="form-control show-tick bootstrap-select align-center" data-live-search="true" style="z-index: 10000" required name="year" id="year" autofocus>
                                            <option value="">-- Please Select Year --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="form-group">
                                <strong>Select Month&nbsp;<span class="text-danger">*</span></strong>
                                <div class="input-group">
                                    <div class="form-line">
                                        <select class="form-control show-tick bootstrap-select align-center" data-live-search="true" style="z-index: 10000"  required name="month" id="month" autofocus disabled>
                                            <option value="">-- Please Select Month --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                @csrf
            </div>
            <button type="submit" class="btn btn-danger waves-effect" id="delete">Delete Data</button>
        </form>
    </div>
</div>
<script type="text/javascript" async="">
    $.ajax({
        url: '{{ URL::asset('/js/delete.js') }}',
        dataType: 'script',
        cache: true
    });
</script>