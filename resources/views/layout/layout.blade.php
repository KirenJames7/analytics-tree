<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        
        <title>{{ config('app.name') }} | Excel to Reports in Minutes</title>
        @include('objects.cssheader')
    </head>
    <body class='theme-black'>
        @include('objects.pageloader')
        <!-- Overlay For Sidebars -->
        <div class="overlay"></div>
        <!-- #END# Overlay For Sidebars -->
        @include('objects.topbar')
        <section>
            @include('objects.leftbar')
            @include('objects.rightbar')
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="block-header" style="display: none">
                            <div class="alert alert-dismissible align-center" role="alert">
                                <p id="alert"></p>
                            </div>
                        </div>
                        <div class="card">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @include('objects.jsheader')
    </body>
</html>