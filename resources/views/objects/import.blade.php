<div class="body">
    <div>
        <form method="POST" enctype="multipart/form-data" class="dropzone align-center" id="fileUploader" >
            <div class="fallback">
                <div class="dz-message">
                    <label for="inputFile" ><i class="material-icons" style="font-size: 48px">cloud_upload</i></label>
                <br />
                <label for="inputFile" style="font-size: 26px">File Upload</label>
            </div>
                <table class="fileGrid">
                    <tr>
                        <td colspan="2" id="fileselect">
                            <input type="file" name="inputFile" id="inputFile" class="btn-block" required />
                        </td>
                    </tr>
                    <tr>
                        <td id="label">
                            <label for="month" class="form-input">Month&nbsp;<span class="text-danger">*</span></label>
                        </td>
                        <td id="input">
                            <input type="text" name="month" class="form-control" id="month" required autocomplete="off" disabled/>
                            <div class="results">
                                <ul tabindex="1" class="result" style="list-style-type: none;text-align: left;">
                                </ul>
                            </div>
                            <a id="editmonth" href="javascript:void(0)" >Change</a>
                        </td>
                    </tr>
                    <tr>
                        <td id="label">
                            <label for="year" class="form-input">Year&nbsp;<span class="text-danger">*</span></label>
                        </td>
                        <td id="input">
                            <input type="text" name="year" class="form-control" id="year" required autocomplete="off" disabled/> <a id="edityear" href="javascript:void(0)">Previous</a>
                        </td>
                    </tr>
                </table>
                @csrf
            </div>
            <button type="submit" class="btn btn-primary waves-effect" id="upload" disabled>Upload File</button>
        </form>
    </div>
    <div id="progressModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="plane align-center">
                    <div class="progress align-center" style="width: 80%">
                        <div class="upload progress-bar bg-blue progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        </div>
                    </div>
                    <div class="align-center progress-text">
                    </div>
                    <div class="align-center progress-percentage">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    $.ajax({
        url: '{{ URL::asset('/js/read-excel-file.min.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/upload.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/table.js') }}',
        dataType: 'script',
        cache: true
    });
</script>