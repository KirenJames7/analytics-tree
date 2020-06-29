var session = {};
var activesession;
$(document).ready(function () {
    $.get({
        url: '/checksession',
        async: false
    }).done((response)=>{
        if(response.currentuser){
            $.each(response.session, (key, sessiondata)=>{
                session[key] = sessiondata;
            });
            if(!session.systemadmin){
                $('#admin').remove();
            }
            if(!session.filemanager){
                $('#file').remove();
            }
            activesession = true;
            setTimeout(()=>{
                report();
            },1000);
        }
    });
    var csrf_js_var = $('meta[name="csrf-token"]').attr('content');
    if(!activesession){
        login(csrf_js_var);
    }
    
    const username = $('#username');
    const password = $('#password');
    $('body').goTop();
    $(document).keydown(function(event){
        var key = event.keyCode || event.which;
        if(key === 116){   //F5
            event.preventDefault();
            return false;
        }
        if(!$('#month').is(':focus') && !$('.multiselect-search')){
            if(key === 8){   //backspace
                event.preventDefault();
                return false;
            }
        }
        if(key === 82 && event.ctrlKey){   //ctrl+R
            event.preventDefault();
            return false;
        }
    });
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
        history.go(1);
    };
    $(window).bind('beforeunload', function(e) { 
        return false;
    });
    $(document).on('contextmenu', function(e){
        return false;
    });
//    function checkSession(){
//        setTimeout(function(){
//            $.ajax({
//                async: false,
//                url: BASE_URL+"auth/checkSession",
//                type: 'POST',
//                dataType: 'JSON',
//                success: function(session){
//                    if(session == 0){;
//                        location.reload();
//                    }else{
//                        checkSession();
//                    }
//                }
//            });
//        }, 300000);
//    }
//    checkSession();
    //--------------------------------AUTHENTICATION--------------------------------------
    $(document).on('click', '#signin', (e)=>{
        e.preventDefault();
        e.stopImmediatePropagation();
        if (loginInputTextValidator([ username.val() , password.val() ], [ username , password ])){
            var formData = $('#signin-credentials').serializeArray();
            $.ajax({
                type: 'POST',
                url: '/auth',
                dataType: 'JSON',
                data: formData
            }).done((response)=>{
                $.each(response.session, (key, sessiondata)=>{
                    session[key] = sessiondata;
                });
                if(response.authentication){
                    if(!session.systemadmin){
                        $('#admin').remove();
                    }
                    if(!session.filemanager){
                        $('#file').remove();
                    }
                    $.unblockUI();
                    report();
                }
                if(response.authentication === false) {
                    var content = document.createElement("p");
                    content.innerHTML = 'You are <b>NOT Authorized!</b>';
                    swal({
                        title: 'UNAUTHORIZED!',
                        content: content,
                        icon: 'error',
                        dangerMode: true,
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: {
                            confirm: {
                                text: "Try Again"
                            }
                        }
                    }).then((tryagain)=>{
                        if(tryagain){
                            $("form")[0].reset();
                            $('#username').focus();
                        }
                    });
                }
                if(response.invalidcredentials){
                    $('#password').parent().css({ 'border-bottom': '#a94442 2px solid' });
                    $('#password').parent().parent().css({ 'margin-bottom': '0px' });
                    $('#password').parent().parent().after('<span class="text-danger align-center" style="display:block;margin-bottom:10px">Password Incorrect! Try Again</span>');
                    $('#password').val('');
                    $('#password').focus();
                }
            }).fail(()=>{
                $(window).unbind('beforeunload');
                location.reload();
            });
        }
    });
    $('#signout').click(()=>{
        swal({
            title: "Sign Out?",
            text: "Are you sure you want to end your session?",
            icon: "info",
            closeOnClickOutside: false,
            closeOnEsc: false,
            buttons: {
                cancel: {
                    text: "Keep Working",
                    visible: true
                },
                confirm: {
                    text: "Sign Out"
                }
            }
        }).then((signout)=>{
            if(signout){
                $.get({
                    url: '/signout'
                }).done((response)=>{
                    if(response.signout){
                        $(window).unbind('beforeunload');
                        location.reload();
                    }
                });
            }
        });
    });
    
    //----------------------------------MENU HOVER-------------------------------------
    $("#signout").removeAttr("class", "active");
    $("#signout").attr("class", "tag");
    $(document).on("mouseover", ".tag", function () {
        var tagid = "#" + this.id;
        $(document).on("mouseover", tagid, function () {
            $(tagid).addClass("active");
        });
        $(document).on("mouseout", tagid, function () {
            $(tagid).removeAttr("class");
            $(tagid).addClass("tag");
        });
    });
    var presubtagid = "";
    var preparent ="";
    var pretag;
    var pretext = "";
    var menuitem = "";
    //add function for main menu tag
    $('.tag').click(function (){
        menuitem = "#" + this.id;
        if(menuitem && $(menuitem).children().length === 1){
            if(pretag){
                $(pretag).children().css("color", "");
            }
            $(preparent).children().first().children().first().css("color", "");
            $('.ml-menu:visible').slideUp();
            if(presubtagid){
                $(presubtagid).children().first().css("color", "");
                //$(presubtagid).children().first().text(lastsubtag);
                $(presubtagid).parent().siblings().first().children().last().css("color", "");
            }
            $(menuitem).children().css("color", "#001");
            pretag = menuitem;
            contentController(this.id);
        }
    });
    $('.subtag').click(function (){
        var parent = "#" + this.parentElement.parentElement.id;
        if(parent === preparent){
            $(parent).children().first().children().first().css("color", "#001");
            $(parent).children().first().children().last().css("color", "#001");
            preparent = parent;
        }else{
            $(preparent).children().first().children().first().css("color", "");
            $(preparent).children().first().children().last().css("color", "");
            $(parent).children().first().children().first().css("color", "#001");
            $(parent).children().first().children().last().css("color", "#001");
            preparent = parent;
        }
        var subtageid = "#" + this.id;
        if(presubtagid !== ""){
            $(presubtagid).children().first().css("color", "");
            pretext = $(presubtagid).children().first().text();
            $(presubtagid).children().first().text(pretext.substring(3));
            $(subtageid).children().first().css("color", "black");
            $(subtageid).children().first().prepend(">&nbsp;&nbsp;");
            presubtagid = "#" + this.id;
            contentController(this.id);            
        }else{
            $(subtageid).children().first().css("color", "black");
            $(subtageid).children().first().prepend(">&nbsp;&nbsp;");
            presubtagid = "#" + this.id;
            contentController(this.id);
        }
    });
});
function loginInputTextValidator(conditions, element){
    var result = [];
    if($('.text-danger').is(':visible')){
        $('.text-danger').remove();
    }
    $.each(conditions, (key,condition)=>{
        if(!condition){
            element[key].parent().css({ 'border-bottom': '#a94442 2px solid' });
            element[key].parent().parent().css({ 'margin-bottom': '0px' });
            element[key].parent().parent().after('<span class="text-danger align-center" style="display:block;margin-bottom:10px">Please Enter ' + element[key][0].id.charAt(0).toUpperCase() + element[key][0].id.slice(1) +'</span>');
            result[key] = false;
        }else{
            element[key].parent().parent().css({ 'margin-bottom': '20px' });
            element[key].parent().removeAttr("style");
            result[key] = true;
        }
    });
    $('#username').focus();
    return result.every((bool)=>{ return true && bool; });
}

function login(csrf_js_var){
    $.blockUI({
        message: '<div class="logoimg align-center"><br /><p style="font-size:24px;margin-bottom:0px;">AIMS | Login</p><img src="../images/zone.png" style="width: 80%" /><img src="../images/zonetaglineblack.png" style="width: 75%" /></div><form id="signin-credentials" method="POST" enctype="multipart/form-data" style="padding:30px"><div class="input-group"><span class="input-group-addon"><i class="material-icons">person</i></span><div class="form-line"><input type="text" class="form-control" name="username" id="username" placeholder="Enter User Name" required autofocus /></div></div><div class="input-group"><span class="input-group-addon"><i class="material-icons">lock</i></span><div class="form-line"><input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" required></div></div><div class="align-center" style="padding:20px"><input name="_token" value="'+ csrf_js_var +'" type="hidden"><button class="btn btn-block btn-primary waves-effect" id="signin" style="width:35%">SIGN IN</button></div></form>',
        css:{
            width: '25%',
            top: '20%',
            left: '37.5%',
            'border-radius': '10px',
            cursor: 'default',
            backgroundColor:'rgb(255,255,255,0.9)',
            'z-index': 10000
        },
        overlayCSS:{ 
            'background-image': 'url("../images/login-bg.gif")',
            'background-repeat': 'no-repeat',
            '-webkit-background-size': 'cover',
            '-moz-background-size': 'cover',
            '-o-background-size': 'cover',
            'background-size': 'cover',
            backgroundColor: '#e9e9e9', 
            opacity: 1, 
            cursor: 'default' 
        },
        baseZ: 9999,
        centerX: true,
        centerY: true
    });
}