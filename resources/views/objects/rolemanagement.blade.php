<div class="body" id="rolemanagement">
    <div class="row clearfix">
        <!-- dataTable to display and add roles -->
        <table id="role-management" class="table table-bordered table-striped table-hover" style="width: 100%">
            
        </table>
    </div>
    <div id="roleModal" class="modal">
        <div class="modal-dialog" style="cursor:default;width:800px;margin:80px auto;">
            <div class="modal-content">
                <div class="plane align-center">
                    <div class="align-center" style="width:100%;margin:auto;">
                        <form class="form-horizontal" id="role" method="POST" enctype="multipart/form-data">
                            <div class="row clearfix">
                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 form-control-label">
                                    <label for="rolename">Role Name&nbsp;<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-8 col-xs-7">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="rolename" class="form-control" name="rolename" placeholder="Eg: System Admin { Characters only, Min 5 }" autofocus required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-5 form-control-label">
                                    <label for="roledescription">Role Description&nbsp;<span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-8 col-xs-7">
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" id="roledescription" class="form-control" name="roledescription" placeholder="Eg: Has access to all projects" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-label authorizationscope">
                                    <fieldset>
                                        <legend>Authorization Scope&nbsp;<span class="text-danger">*</span></legend>
                                        <div style="position:absolute;right:20px;">
                                            <div class="demo-switch-title">SELECT ALL</div>
                                            <div class="switch">
                                                <label><input type="checkbox" id="butoggle" ><span class="lever switch-col-blue"></span></label>
                                            </div>
                                        </div>
                                        <div class="bus">

                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control-label authenticatedusers">
                                    <fieldset>
                                        <legend>Authenticated Users&nbsp;<span class="text-danger">*</span></legend>
                                        <div class="form-group">
                                            <select data-placeholder="Type to search..." class="form-control" id="users" multiple>
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-5 form-control-label">
                                    <label for="systemadmin">Is System Administrator ?</label>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <div class="systemadmin">
                                        <input type="checkbox" class="chk-col-red" name="systemadmin" id="systemadmin" value="1" />
                                        <label for="systemadmin" style="margin-top:7px"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-5 form-control-label">
                                    <label for="filemanager">Is File Manager ?</label>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <div class="filemanager">
                                        <input type="checkbox" class="chk-col-deep-orange" name="filemanager" id="filemanager" value="1" />
                                        <label for="filemanager" style="margin-top:7px"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-5 form-control-label">
                                    <label for="allbuaccess">View All BUs ?</label>
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <div class="filemanager">
                                        <input type="checkbox" class="chk-col-blue" name="allbuaccess" id="allbuaccess" value="1" />
                                        <label for="allbuaccess" style="margin-top:7px"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row clearfix">
                                <div>
                                    @csrf
                                    <button type="button" class="btn btn-primary m-t-15 waves-effect" id="rolesubmit" style="width:125px;">ADD</button>
                                    <button type="button" class="btn btn-outline-primary m-t-15 waves-effect" id="rolecancel" style="width:125px;">CANCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" async>
    if(isFirefox){
        $.ajax({
            url: '{{ URL::asset('/plugins/jquery-datatable/jquery.dataTables.js') }}',
            dataType: 'script',
            cache: true
        });
        $.ajax({
            url: '{{ URL::asset('/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}',
            dataType: 'script',
            cache: true
        });
        $.ajax({
            url: '{{ URL::asset('/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}',
            dataType: 'script',
            cache: true
        });
        $.ajax({
            url: '{{ URL::asset('/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}',
            dataType: 'script',
            cache: true
        });
    }
    $.ajax({
        url: '{{ URL::asset('/plugins/chosen/chosen.jquery.min.js') }}',
        dataType: 'script',
        cache: true
    });
    $.ajax({
        url: '{{ URL::asset('/js/rolemanagement.js') }}',
        dataType: 'script',
        cache: true
    });
</script>