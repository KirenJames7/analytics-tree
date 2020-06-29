    <!-- Jquery Core Js -->
    <script src="{{ asset('/js/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" async>
        // Firefox 1.0+
        var isFirefox = typeof InstallTrigger !== 'undefined';

        // Safari 3.0+ "[object HTMLElementConstructor]" 
        var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || safari.pushNotification);

        // Internet Explorer 6-11
        var isIE = /*@cc_on!@*/false || !!document.documentMode;

        // Edge 20+
        var isEdge = !isIE && !!window.StyleMedia;

        // Chrome 1+
        var isChrome = !!window.chrome && !!window.chrome.webstore;
        if(isChrome){
            $.ajax({
                url: '{{ URL::asset('/js/jquery.blockUI.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: 'https://unpkg.com/sweetalert/dist/sweetalert.min.js',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/contentcontroller.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/bootstrap/js/bootstrap.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/node-waves/waves.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-slimscroll/jquery.slimscroll.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/admin.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/menu.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/index.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/bootstrap-multiselect.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-datatable/jquery.dataTables.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-datatable/extensions/export/buttons.html5.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/gotop/plugin/jquery.goTop.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
        }else{
            $.ajax({
                url: '{{ URL::asset('/js/jquery.blockUI.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/gotop/plugin/jquery.goTop.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: 'https://unpkg.com/sweetalert/dist/sweetalert.min.js',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/contentcontroller.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/bootstrap/js/bootstrap.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/js/bootstrap-multiselect.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}',
                dataType: 'script',
                cache: true,
                async: false
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/node-waves/waves.js') }}',
                dataType: 'script',
                cache: true,
            });
            $.ajax({
                url: '{{ URL::asset('/plugins/jquery-slimscroll/jquery.slimscroll.js') }}',
                dataType: 'script',
                cache: true
            });
            $.ajax({
                url: '{{ URL::asset('/js/admin.js') }}',
                dataType: 'script',
                cache: true
            });
            $.ajax({
                url: '{{ URL::asset('/js/menu.js') }}',
                dataType: 'script',
                cache: true
            });
            $.ajax({
                url: '{{ URL::asset('/js/index.js') }}',
                dataType: 'script',
                cache: true
            });
        }
    </script>