$(document).ready(function(){
    if(isChrome){
        $(document).on('mousedown', '#year', function(){
            if($('#year').val() === ""){
                $('#year').empty().first().append('<option value="">-- Please Select Year --</option>');
                $.get({
                    async: false,
                    url: '/year',
                    dataType: 'JSON'
                }).done(function(response){
                    $.each(response.years, function(key, year){
                        $('#year').append('<option value="' + year +'">' + year +'</option>');
                    });
                });
            }else{
                $('#year').val($(this).val());
                $('#month').removeAttr('disabled');
            }
        });
        $(document).on('mousedown', '#month' ,function(){
            if($(this).val() === ""){
                $('#month').empty().first().append('<option value="">-- Please Select Month --</option>');
                $.get({
                    async: false,
                    url: '/month/' + $('#year').val(),
                    dataType: 'JSON'
                }).done(function(response){
                    $.each(response.months, function(key, month){
                        $('#month').append('<option value="' + month +'">' + month +'</option>');
                    });
                });
            }else{
                $('#month').val($(this).val());
            }
        });
        $(document).on('change', '#year', function(){
            $('#month').empty().first().append('<option value="">-- Please Select Month --</option>');
            if($('#year').val() !== ""){
                $('#month').removeAttr('disabled');
            }else{
                $('#month').attr('disabled', true);
            }
        });
    }else{
        $('#year').click(function(){
            if($(this).val() === ""){
                $('#year').empty().first().append('<option value="">-- Please Select Year --</option>');
                $.get({
                    async: false,
                    url: '/year',
                    dataType: 'JSON'
                }).done(function(response){
                    $.each(response.years, function(key, year){
                        $('#year').append('<option value="' + year +'">' + year +'</option>');
                    });
                });
            }else{
                $('#year').val($(this).val());
                $('#month').removeAttr('disabled');
            }
        });
    
        $('#month').click(function(){
            if($(this).val() === ""){
                $('#month').empty().first().append('<option value="">-- Please Select Month --</option>');
                $.get({
                    async: false,
                    url: '/month/' + $('#year').val(),
                    dataType: 'JSON'
                }).done(function(response){
                    $.each(response.months, function(key, month){
                        $('#month').append('<option value="' + month +'">' + month +'</option>');
                    });
                });
            }else{
                $('#month').val($(this).val());
            }
        });

        $('#year').change(function(){
            $('#month').empty().first().append('<option value="">-- Please Select Month --</option>');
            if($('#year').val() !== ""){
                $('#month').removeAttr('disabled');
            }else{
                $('#month').attr('disabled', true);
            }
        });
    }
    $(document).on('submit', '#dataDemolisher', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        swal({
            title: 'Delete?',
            text: 'Are you sure you want to delete ' + $('#year').val() + ' ' + $('#month').val() + ' data set?',
            icon: 'error',
            closeOnClickOutside: false,
            closeOnEsc: false,
            dangerMode: true,
            buttons: {
                cancel: {
                    text: "Keep It",
                    visible: true
                },
                confirm: {
                    text: "Delete It!"
                }
            }
        }).then((deleteIt)=>{
            if(deleteIt){
                $.ajax({
                    type: 'POST',
                    url: '/delete',
                    data: $(this).serialize(),
                    dataType: 'JSON'
                }).done((response)=>{
                    $('#alert').text(response.success);
                    $('.alert').addClass('alert-success');
                    $('.block-header').show();
                    hideAlert();
                    $('#year').empty().first().append('<option value="">-- Please Select Year --</option>');
                    $('#month').empty().first().append('<option value="">-- Please Select Month --</option>');
                    $('#month').attr('disabled', true);
                });
            }
        });
    });
    
    function hideAlert(){
        setTimeout(function(){
            if($('.block-header').is(':visible')){
                $('.alert').removeClass('alert-danger');
                $('.alert').removeClass('alert-success');
                $('.block-header').hide();
                $('#alert').text("");
            }
        }, 5000);
    }
});