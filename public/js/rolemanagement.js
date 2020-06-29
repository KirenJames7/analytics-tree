var tableData;
const rolename = $('#rolename');
const roledescription = $('#roledescription');
const users = $('#users');
const userssearch = '#users_chosen';
const bulabel = ".bus label[for!='systemadmin'][for!='filemanager']";
const bucheckboxes = '.bus input:checkbox';
var selectedrow;
var manage = false;
var unauthenticate = [];
var authenticate = [];
$(document).ready(()=>{
    //genearate the table with existing roles
    var table;
    $.get({
        url: '/currentroles',
        dataType: 'JSON'
    }).done((response)=>{
        table = $('#role-management').DataTable({
            paging: false,
            data: response.currentroles,
            columns: [
                { title: "Role ID", data: "roleid", visible: false, searchable: false },
                { title: "Role Name", data: "rolename", className: "roleid" },
                { title: "Description", data: "roledescription" },
                { title: "Authorization ID", data: "buid", visible: false, searchable: false },
                { title: "Authorization Scope", data: "buname" },
                { title: "Authenticated Users", data: "username" },
                { title: "System Admins", data: "systemadmin", className: 'align-center', render: function(data){
                        if (data) {
                            return '<input type="checkbox" class="chk-col-red" checked disabled /><label style="width:auto"></label>';
                        } else {
                            return '<input type="checkbox" disabled /><label style="width:auto;margin-bottom:0px"></label>';
                        }
                }},
                { title: "File Managers", data: "filemanager", className: 'align-center', render: function(data){
                        if (data) {
                            return '<input type="checkbox" class="chk-col-deep-orange" checked disabled /><label style="width:auto"></label>';
                        } else {
                            return '<input type="checkbox" disabled /><label style="width:auto;margin-bottom:0px"></label>';
                        }
                }},
                { title: "View All BUs", data: "allbuaccess", className: 'align-center', render: function(data){
                        if (data) {
                            return '<input type="checkbox" class="chk-col-blue" checked disabled /><label style="width:auto"></label>';
                        } else {
                            return '<input type="checkbox" disabled /><label style="width:auto;margin-bottom:0px"></label>';
                        }
                }}
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Add Role',
                    className: 'btn-primary',
                    action: function ( e, dt, node, config ) {
                        formPreRequisits();
                        $("form")[0].reset();
                        $('input:disabled').each((key,element)=>{
                            $('#' + element.id).removeAttr('disabled');
                        });
                        $('#rolesubmit').text('ADD');
                        $('#rolesubmit').removeClass('btn-warning');
                        $('#rolesubmit').addClass('btn-primary');
                        $.blockUI({
                            message : $('#roleModal')
                        });
                    }
                },
                {
                    text: 'Delete Role',
                    className: 'btn-danger',
                    action: function ( e, dt, node, config ) {
                        var deletetext = document.createElement("p");
                        if ($('tr.selected').length) {
                            deletetext.innerHTML = "Are you sure you want to DELETE the Role:<br /><b>" + selectedrow.rolename + "</b><br /><b>WARNING!</b> In proceeding the following<br /><b>users will loose access</b><br />to the entire system unless they are added to another role.<br />Users:<br />\"<b>" + selectedrow.username + "</b>\"";
                            swal({
                                title: "Delete?",
                                content: deletetext,
                                icon: "error",
                                dangerMode: true,
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                buttons: {
                                    cancel: {
                                        text: "Nope! Not " + selectedrow.rolename,
                                        visible: true
                                    },
                                    confirm: {
                                        text: "Delete"
                                    }
                                }
                            }).then((deleteIt)=>{
                                if (deleteIt) {
                                    $.get({
                                        url: '/deleterole',
                                        data: { roleid: selectedrow.roleid },
                                        dataType: 'JSON'
                                    }).done((response)=>{
                                        if(response.success){
                                            swal({
                                                title: "Successfully Deleted",
                                                text: "Role " + selectedrow.rolename + " has been deleted successfully",
                                                icon: "success",
                                                closeOnClickOutside: false,
                                                closeOnEsc: false,
                                                buttons: false,
                                                timer: 2000
                                            });
                                            table.row($('tr.selected')).remove().draw();
                                        }
                                    });
                                }
                            });
                        } else {
                            nothingSelected();
                        }
                    }
                },
                {
                    text: 'Manage Role',
                    className: 'btn-warning',
                    action: function ( e, dt, node, config ) {
                        manage = true;
                        if ($('tr.selected').length) {
                            formPreRequisits();
                            $('#rolesubmit').text('COMMIT');
                            $('#rolesubmit').removeClass('btn-primary');
                            $('#rolesubmit').addClass('btn-warning');
                            rolename.val(selectedrow.rolename).attr('disabled', true);
                            roledescription.val(selectedrow.roledescription).attr('disabled', true);
                            var assignedbu = selectedrow.buid.split(",");
                            $.each(assignedbu, (key,bu)=>{
                                $('#'+ bu).prop("checked", true);
                            });
                            if (selectedrow.filemanager) {
                                $('#filemanager').prop("checked", true);
                            }
                            if (selectedrow.systemadmin) {
                                $('#systemadmin').prop("checked", true);
                            }
                            if (selectedrow.allbuaccess) {
                                $('#allbuaccess').prop("checked", true);
                                $(".checkall").attr("disabled", true);
                            }
                            if (selectedrow.username) {
                                var authenticated = selectedrow.username.split(",");
                                $.each(authenticated, (key,authuser)=>{
                                    users.append('<option value="' + authuser + '" selected="">' + authuser + '</option>');
                                });
                                users.chosen('destroy');
                                users.chosen({no_results_text: "Oops, nothing found!"});
                            }
                            $.blockUI({
                                message : $('#roleModal')
                            });
                        } else {
                            nothingSelected();
                        }
                    }
                }
            ]
        });
        table.on( 'click', 'tr', function(){
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
                selectedrow = table.row(this).data();
            }
        });
    });
    //making sure the role name and role description can only in take aplhabatical values
    rolename.bind('keydown', onlyTextInput);
    roledescription.bind('keydown', onlyTextInput);
    inputRetry(true);
    //cheking the availability of a new role
    rolename.keyup((e)=>{
        var successmsg = '<span class="text-success align-center" style="display:block;">Role Name Available</span>';
        var errormsg = '<span class="text-danger align-center" style="display:block;">Role Name Not Available</span>';
        if (rolename.val() && $.trim(rolename.val()).length > 4 && e.keyCode < 91 && ![9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,48,49,50,51,52,53,54,55,56,57].includes(e.keyCode)) {
            $.get({
                url: '/' + rolename.attr('id'),
                data: { rolename : rolename.val() }
            }).done((response)=>{
                if (response.rolename) {
                    rolename.parent().after($(errormsg));
                    $('#rolesubmit').attr('disabled', true);
                } else {
                    rolename.parent().after($(successmsg));
                    $('#rolesubmit').removeAttr('disabled');
                }
            });
        }
    });
    //select all BUs
    $(document).on("click", ".switch", function (){
        if ($("#butoggle").is(':checked')) {
            $(".checkall").prop("checked", true);
        } else {
            $(".checkall").prop("checked", false);
        }
    });
    //view all BUs Access
    $("#allbuaccess").click(()=>{
        if ($("#allbuaccess").is(":checked")) {
            $(".checkall").prop("checked", true);
            $(".checkall").attr("disabled", true);
        } else {
            $(".checkall").prop("checked", false);
            $(".checkall").removeAttr("disabled");
        }
    });
    //changes made to authentication scope on role management
    users.on('change', (event, option)=>{
        if (manage) {
            if (option.deselected) {
                if (!authenticate.includes(option.deselected)) {
                    var unauthtext = document.createElement("p");
                    unauthtext.innerHTML = "If you <b>COMMIT " + option.deselected + "</b> will lose access to the system until he/she is <b>assigned</b> to a role again.</br><small><b>PS: If you made a you may add the user again before COMMIT</b></small>";
                    swal({
                        title: "Unauthenticate \"" + option.deselected + "\"?",
                        content: unauthtext,
                        icon: "error",
                        dangerMode: true,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: {
                            confirm: {
                                text: "OK"
                            }
                        }
                    }).then((unauth)=>{
                        if (unauth) {
                            var unauthinfotext = document.createElement("p");
                            unauthinfotext.innerHTML = "[ <b>" + unauthenticate.join(", ") + "</b> ] will be unauthenticated on commit.";
                            swal({
                                title: "Don't Forget To Commit!",
                                content: unauthinfotext,
                                icon: "info",
                                closeOnClickOutside: false,
                                closeOnEsc: false,
                                buttons: false,
                                timer: 2000
                            });
                        }
                    });
                    unauthenticate.push(option.deselected);
                }
                authenticate = $.grep(authenticate, (auth)=>{
                    return (auth !== option.deselected);
                });
            }
            if (option.selected) {
                if(!unauthenticate.includes(option.selected)){
                    authenticate.push(option.selected);
                }
                unauthenticate = $.grep(unauthenticate, (unauth)=>{
                    return unauth !== option.selected;
                });
            }
        }
    });
    //cancel the creation or modification of a role
    $('#rolecancel').click(()=>{
        swal({
            title: "Cancel?",
            text: "Once canceled all entires will be lost!",
            icon: "warning",
            dangerMode: true,
            closeOnClickOutside: false,
            closeOnEsc: false,
            buttons: {
                cancel: {
                    text: "Keep Going",
                    visible: true
                },
                confirm: {
                    text: "Cancel"
                }
            }
        }).then((cancel)=>{
            if(cancel){
                swalPostRequisites();
            }
        });
    });
    //submit creation or modification of a role
    $('#rolesubmit').click(()=>{
        if ($("#allbuaccess").is(":checked")) {
            $(".checkall").removeAttr("disabled");
        }
        var formData = $('#role').serializeArray();
        if ($("#allbuaccess").is(":checked")) {
            $(".checkall").attr("disabled", true);
        }
        if (manage) {
            if (inputTextValidator([ users.val() ] , [ users ])) {
                var assignedbu = [];
                if ($("#allbuaccess").is(":checked")) {
                    $(".checkall").removeAttr("disabled");
                }
                assignedbu = $('.bus input[type=checkbox]:checked').map((_,el)=>{
                    return  $(el).val();
                }).get();
                if (authenticate.length || unauthenticate.length || !compareArrays(assignedbu, selectedrow.buid.split(",")) || selectedrow.systemadmin !== parseInt($('#systemadmin:checked').val() || 0) || selectedrow.filemanager !== parseInt($('#filemanager:checked').val() || 0) || selectedrow.allbuaccess !== parseInt($('#allbuaccess:checked').val() || 0)) {
                    var changesmade = document.createElement("p");
                    if (authenticate.length) {
                        changesmade.innerHTML += "To Be Authenticated :<br /><b>" + authenticate.join(', ') + "</b><br /><br />";
                    }
                    if (unauthenticate.length) {
                        changesmade.innerHTML += "To Be Unauthenticated :<br /><b>" + unauthenticate.join(', ') + "</b><br /><br />";
                    }
                    if (!compareArrays(assignedbu, selectedrow.buid.split(","))) {
                        changesmade.innerHTML += "New Role Scope :<br /><b>" + $(':checkbox:checked:not(#systemadmin,#filemanager)').map(function(){ if(this.nextElementSibling.innerText){ return this.nextElementSibling.innerText + '; '; }else{ return ''; } }).get().join('') + "</b><br /><br />";
                    }
                    if (selectedrow.systemadmin !== parseInt($('#systemadmin:checked').val() || 0)) {
                        if ($('#systemadmin:checked').val() || 0) {
                            changesmade.innerHTML += "<b>Grant</b> System Administration Access<br /><br />";
                        } else {
                            changesmade.innerHTML += "<b>Provoke</b> System Administration Access<br /><br />";
                        }
                    }
                    if (selectedrow.filemanager !== parseInt($('#filemanager:checked').val() || 0)) {
                        if ($('#filemanager:checked').val() || 0) {
                            changesmade.innerHTML += "<b>Grant</b> File Manager Access";
                        } else {
                            changesmade.innerHTML += "<b>Provoke</b> File Manager Access";
                        }
                    }
                    if (selectedrow.allbuaccess !== parseInt($('#allbuaccess:checked').val() || 0)) {
                        if ($('#allbuaccess:checked').val() || 0) {
                            changesmade.innerHTML += "<b>Grant</b> View All BU Access";
                        } else {
                            changesmade.innerHTML += "<b>Provoke</b> View All BU Access";
                        }
                    }
                    swal({
                        title: "Following Changes Will Be Made",
                        content: changesmade,
                        icon: "info",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: {
                            cancel: {
                                text: "Edit",
                                visible: true
                            },
                            confirm: {
                                text: "Commit"
                            }
                        }
                    }).then((commit)=>{
                        if (commit) {
                            if (authenticate.length) {
                                $.ajax({
                                    url: '/roleadduser',
                                    type: 'POST',
                                    data: { username: authenticate, roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            if (unauthenticate.length) {
                                $.ajax({
                                    url: '/roledeleteuser',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: { username: unauthenticate, roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            if (!compareArrays(assignedbu, selectedrow.buid.split(","))) {
                                $.ajax({
                                    url: '/rolemodifyscope',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: { buid: assignedbu, roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            if (selectedrow.systemadmin !== parseInt($('#systemadmin:checked').val() || 0)) {
                                $.ajax({
                                    url: '/rolemodifysystemadmin',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: { systemadmin: ($('#systemadmin:checked').val() || 0), roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            if (selectedrow.filemanager !== parseInt($('#filemanager:checked').val() || 0)) {
                                $.ajax({
                                    url: '/rolemodifyfilemanager',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: { filemanager: ($('#filemanager:checked').val() || 0), roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            if (selectedrow.allbuaccess !== parseInt($('#allbuaccess:checked').val() || 0)) {
                                $.ajax({
                                    url: '/rolemodifyallbuaccess',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: { allbuaccess: ($('#allbuaccess:checked').val() || 0), roleid: selectedrow.roleid, _token: $('meta[name="csrf-token"]').attr('content') }
                                }).done(()=>{
                                   return false;
                                });
                            }
                            setTimeout(function(){
                                $.get({
                                    url: '/getmodifiedrole',
                                    dataType: 'JSON',
                                    data: { roleid: selectedrow.roleid }
                                }).done((response)=>{
                                    table.row($('tr.selected')).remove().draw();
                                    table.row.add(response.modifiedrole).draw();
                                }).then(()=>{
                                    swalPostRequisites();
                                });
                            }, 500);
                        }
                    });
                } else {
                    var nothingtocommit = document.createElement("p");
                    nothingtocommit.innerHTML = "No changes have been made to<br /><b>Role: " + selectedrow.rolename + "</b>";
                    swal({
                        title: "Nothing To Commit",
                        content: nothingtocommit,
                        icon: "info",
                        dangerMode: true,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: {
                            cancel: {
                                text: "Edit",
                                visible: true
                            },
                            confirm: {
                                text: "Cancel"
                            }
                        }
                    }).then((cancel)=>{
                        if (cancel) {
                            swalPostRequisites();
                        }
                    });
                }
            }
            if ($("#allbuaccess").is(":checked")) {
            $(".checkall").attr("disabled", true);
        }
        } else {
            if (inputTextValidator([ rolename.val().length > 4 , roledescription.val().length , users.val() ] , [ rolename , roledescription , users ])) {
                $.each(users.val(), (key, user)=>{
                    formData.push({ 'name' : 'username[]' , value: user });
                });
                var content = document.createElement("p");
                content.innerHTML = "Role Description:<br /><b>" + roledescription.val() + "</b><br /><br />Authorization Scope :<br /><b>" + $(':checkbox:checked:not(#systemadmin,#filemanager)').map(function(){ if(this.nextElementSibling.innerText){ return this.nextElementSibling.innerText + '; '; }else{ return ''; } }).get().join('') + "</b><br /><br />Authenticated Users :<br /><b>" + users.val() + "</b>";
                if ($('#systemadmin:checked').val() || 0) {
                    content.innerHTML += "<br /><br /><b>Grant</b> System Administration Access";
                }
                if ($('#filemanager:checked').val() || 0) {
                    content.innerHTML += "<br /><br /><b>Grant</b> File Manager Access<br /><br />";
                }
                if ($('#allbuaccess:checked').val() || 0) {
                    content.innerHTML += "<br /><br /><b>Grant</b> View All BU Access<br /><br />";
                }
                swal({
                    title: "Create Role: " + rolename.val() + "?",
                    content: content,
                    icon: "info",
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: {
                        cancel: {
                            text: "Edit",
                            visible: true
                        },
                        confirm: {
                            text: "Create"
                        }
                    }
                }).then((create)=>{
                    if (create) {
                        $.ajax({
                            type: 'POST',
                            url: '/addrole',
                            dataType: 'JSON',
                            data: formData
                        }).done((response)=>{
                            table.row.add(response.newrole).draw();
                        }).then(()=>{
                            swalPostRequisites();
                        });
                    }
                });
            }
        }
    });
});
//function to make sure only alphabatical characters can be entered in to an input field
function onlyTextInput(event) {
    var element = $('#'+event.target.id);
    var key = event.keyCode;
    element.removeAttr('style');
    if (element.parent()[0].nextElementSibling && key !== 9) {
        element.parent()[0].nextElementSibling.remove();
    }
    return ((key >= 65 && key <= 90) || [ 8,9,32,35,36,37,38,39,40,46 ].includes(key));
}
//function to validate input text fields
function inputTextValidator(conditions, element){
    var result = [];
    $.each(conditions, (key,condition)=>{
        if (!condition) {
            element[key].css({ border: '#a94442 2px solid' });
            if (element[key].parent()[0].nextElementSibling) {
                element[key].parent()[0].nextElementSibling.remove();
            }
            element[key].parent().after('<span class="text-danger align-center" style="display:block;">Invalid Input</span>');
            result[key] = false;
        } else {
            result[key] = true;
        }
    });
    result.push(checkBoxValidator());
    return result.every((bool)=>{ return true && bool; });
}
//function to validate input checkboxes
function checkBoxValidator(){
    if ($(bucheckboxes).is(':checked')) {
        return true;
    } else {
        if ($(bucheckboxes).first().parent()[0].nextElementSibling) {
            $(bucheckboxes).first().parent()[0].nextElementSibling.remove();
        }
        $(bucheckboxes).first().parent().after('<span class="text-danger align-center" style="display:block;">At least <b>ONE</b> Authorization Scope should be selected</span>');
        return false;
    }
}
//function to reset input fields on input retry
function inputRetry(event){
    if (event) {
        $(document).on('mousedown', userssearch, ()=>{
            if ($(userssearch).parent()[0].nextElementSibling) {
                $(userssearch).parent()[0].nextElementSibling.remove();
            }
        });
        $(document).on('mousedown', "label[for!='systemadmin'][for!='filemanager']", ()=>{
            if ($(bulabel).first().parent()[0].nextElementSibling) {
                $(bulabel).first().parent()[0].nextElementSibling.remove();
            }
        });
    } else {
        if ($(userssearch).parent()[0].nextElementSibling) {
            $(userssearch).parent()[0].nextElementSibling.remove();
        }
        if ($(bulabel).first().parent()[0].nextElementSibling) {
            $(bulabel).first().parent()[0].nextElementSibling.remove();
        }
//        $. each($('input:text'), (key, element)=>{
//            console.log(element);
//        });
        $.each([rolename, roledescription], (key, element)=>{
            //console.log(element)
            element.removeAttr('style');
            if (element.parent()[0].nextElementSibling) {
                element.parent()[0].nextElementSibling.remove();
            }
        });
    }
}
//function to compare arrays with matching values returns true on match false otherwise
function compareArrays(first, second){
    //write type error
    return first.every((e)=> second.includes(e)) && second.every((e)=> first.includes(e));
}
//function to display alert when existing roles are not seleted before clicking delete role or modify role
function nothingSelected(){
    //write basic SWAL function
    swal({
        title: "Nothing Selected!",
        text: "Please select a record to proceed.",
        icon: "info",
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: {
            confirm: {
                text: "OK"
            }
        }
    });
}
//function that runs on alert cancelation
function swalPostRequisites(){
    $.unblockUI();
    inputRetry(false);
    $("form")[0].reset();
    if (manage) { manage = false;unauthenticate.length = 0;authenticate.length = 0; }
    return false;
}
//function that lists the BUs and users on role creation and modification 
function formPreRequisits(){
    $.get({
        async: false,
        url: '\getbus',
        dataType: 'JSON'
    }).done((response)=>{
        let checkBoxHtml = '';
        const checkBoxes = response.bus;
        $.each(checkBoxes, (checkBox, key)=>{
            checkBoxHtml += '<input type="checkbox" class="chk-col-blue checkall" name="buid[]" id="' + key + '" value="'+ key +'" /><label for="' + key + '">' + checkBox + '</label>';
        });
        $('.bus').html(checkBoxHtml);
    });
    
    $.get({
        async: false,
        url: '\getusers',
        dataType: 'JSON'
    }).done((response)=>{
        var selectOpts = response.users;
        var optionsCount, pointer;
        optionsCount = pointer = response.users.length;
        if (users.children().length > 1) {
            users.children().first().siblings().remove();
        }
        while (pointer > 0) {
            var index = optionsCount - pointer;
            users.append('<option value="' + selectOpts[index] + '">' + selectOpts[index] + '</option>');
            pointer--;
        }
    }).then(()=>{
        users.chosen('destroy');
        users.chosen({no_results_text: "Oops, nothing found!"});
    });
}