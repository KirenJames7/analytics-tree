$(document).ready(function(){
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const inputFile = $('#inputFile');
    var assignmonth = [];
    var currentElement;
    var curentuploadlist;
    var latestupload;
    $('#year').val((new Date()).getFullYear());
    $('#month').val(months[((new Date()).getMonth()+months.length-1)%months.length]);
    $('#edityear').click(function(){
        if ($('#year').val() == (new Date()).getFullYear()) {
            $('#year').val((new Date()).getFullYear()-1);
            $('#edityear').text('Current');
        } else {
            if (months.indexOf($('#month').val()) > (new Date()).getMonth()) {
                $('#month').removeAttr('disabled');
                $('#month').val('');
                $('#month').focus();
                $('#year').val((new Date()).getFullYear());
                $('#edityear').text('Previous');
                swal({
                    title: "Upcoming Month Selected!",
                    text: "You can't select a month that has not yet arrived!",
                    icon: "error",
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: false,
                    timer: 2000
                });
            } else {
                if ($('#month').val() !== "") {
                    $('#month').attr('disabled', true);
                    $('#year').val((new Date()).getFullYear());
                    $('#edityear').text('Previous');
                } else {
                    swal({
                        title: "Month Not Selected!",
                        text: "Please enter month to proceed!",
                        icon: "error",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                        buttons: false,
                        timer: 2000
                    });
                }
            }
        }
    });
    
    $('#editmonth').click(function(){
        $('#month').removeAttr("disabled");
        $('#month').val("");
        $('#month').focus();
    });
    
    $(document).on('keypress', '#month', function(e){
        var code = e.keyCode || e.which;
        if(code === 13){    //enter
            e.preventDefault();
            return false;
        }
        if(code === 38){    //up
            e.preventDefault();
            return false;
        }
    });
    
    $('#month').focus(function(){
        if (!$('.results').is(':visible') && $('#month').val() === "") {
            $('.results').show();
            for (i = 0; i < months.length; i++) {
                $('.result').append("<li class='result-item' id="+months[i]+">"+months[i]+"</li>");
            }
            $('.result-item').first().addClass("selected");
        }
    });
    $('#month').keyup(function(e){
        var searchstr = $("#month").val();
        if ($('.results').is(':visible') && currentElement !== searchstr && e.keyCode !== 40 && e.keyCode !== 38) {
            $('.results').hide();
            $('.result-item').remove();
            for (i = 0; i < months.length; i++) {
                var month = months[i];
                if (searchstr === month.substring(0, searchstr.length).toLowerCase() || searchstr === month.substring(0, searchstr.length)) {
                    $('.results').show();
                    $('.result').append("<li class='result-item' id="+month+">"+month+"</li>");
                    assignmonth.push(month);
                }
            }
            $('.result-item').first().addClass("selected");
            currentElement = searchstr;
        } else if (searchstr !== "" && e.keyCode !== 40 && e.keyCode !== 38) {
            $('.results').hide();
            $('.result-item').remove();
            for (i = 0; i < months.length; i++) {
                var month = months[i];
                if (searchstr === month.substring(0, searchstr.length).toLowerCase() || searchstr === month.substring(0, searchstr.length)) {
                    $('.results').show();
                    $('.result').append("<li class='result-item' id="+month+">"+month+"</li>");
                    assignmonth.push(month);
                }
            }
            $('.result-item').first().addClass("selected");
            currentElement = searchstr;
        } else if (searchstr === "" && e.keyCode === 8) {
            $('.results').hide();
            $('.result-item').remove();
            for (i = 0; i < months.length; i++) {
                var month = months[i];
                if (searchstr === month.substring(0, searchstr.length).toLowerCase() || searchstr === month.substring(0, searchstr.length)) {
                    $('.results').show();
                    $('.result').append("<li class='result-item' id="+month+">"+month+"</li>");
                    assignmonth.push(month);
                }
            }
            $('.result-item').first().addClass("selected");
        }
    });
    $('#month').keydown(function(e){
        var children = [];
        $('.result-item').each(function(){
            children.push($(this).attr('id'));
        });
        switch (e.keyCode) {
            case 9:     //tab
                if ($('.results').is(':visible')) {
                    $('#month').val($('.selected').text());
                    $('.results').hide();
                    $('.result-item').remove();
                    $('#month').blur();
                    if ((months.indexOf($('#month').val()) > (new Date()).getMonth()) && $('#year').val() == (new Date()).getFullYear()) {
                        $('#month').val('');
                        $('#month').focus();
                        swal({
                            title: "Upcoming Month Selected!",
                            text: "You can't select a month that has not yet arrived!",
                            icon: "error",
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            buttons: false,
                            timer: 2000
                        });
                    } else {
                        $('#month').attr('disabled', true);
                    }
                }
            case 13:    //enter
                if ($('.results').is(':visible')) {
                    $('#month').val($('.selected').text());
                    $('.results').hide();
                    $('.result-item').remove();
                    $('#month').blur();
                    if ((months.indexOf($('#month').val()) > (new Date()).getMonth()) && $('#year').val() == (new Date()).getFullYear()) {
                        $('#month').val('');
                        $('#month').focus();
                        swal({
                            title: "Upcoming Month Selected!",
                            text: "You can't select a month that has not yet arrived!",
                            icon: "error",
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            buttons: false,
                            timer: 2000
                        });
                    } else {
                        $('#month').attr('disabled', true);
                    }
                }
                break;
            case 27:    //escape
                if ($('.results').is(':visible')) {
                    $('.results').hide();
                    $('.result-item').remove();
                    $('#month').blur();
                }
        }
        
        if (e.keyCode === 38) { // up
            var selected = $(".selected");
            $(".results li").removeClass("selected");
            if (selected.prev().length === 0) {
                selected.siblings().last().addClass("selected");
            } else {
                selected.prev().addClass("selected");
            }
        }
        if (e.keyCode === 40) { // down
            var selected = $(".selected");
            $(".results li").removeClass("selected");
            if (selected.next().length === 0) {
                selected.siblings().first().addClass("selected");
            } else {
                selected.next().addClass("selected");
            }
        }
    });
    $(document).on('mouseover', '.results li', function(){
        $(".results li").removeClass("selected");
        $(this).addClass("selected");
    });
    $(document).on('click', '.result-item', function(){
        $('#month').val(this.id);
        $('.results').hide();
        $('.result-item').remove();
        if ((months.indexOf(this.id) > (new Date()).getMonth()) && $('#year').val() == (new Date()).getFullYear()) {
            $('#month').val('');
            $('#month').focus();
            swal({
                title: "Upcoming Month Selected!",
                text: "You can't select a month that has not yet arrived!",
                icon: "error",
                closeOnClickOutside: false,
                closeOnEsc: false,
                buttons: false,
                timer: 2000
            });
        } else {
            $('#month').attr('disabled', true);
        }
    });
    $('#month').change(function(){
        if (!$('.results').is(':visible')) {
            var result = checkMonth($('#month').val());
            if (result === false) {
                swal({
                    title: "What Was That?!",
                    text: "You just entered something that is impossible!",
                    icon: "error",
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                    buttons: false,
                    timer: 2000
                });
                $('#month').val("");
                $('#month').focus();
            }
        }
    });
    $(document).on('click', function(e){
        if (e.target['className'] !== "result-item selected" && e.target['id'] !== "editmonth" && e.target['id'] !== "month") {
            if ($('.results').is(':visible')) {
                $('.results').hide();
                $('.result-item').remove();
            }
        }
    });
    
    $(document).on('click', '#inputFile', ()=>{
        if (inputFile.siblings().length) {
            inputFile.next().remove();
            inputFile.removeAttr("style");
        }
    });
    //validation to make sure the selected file type can only be ".xlsx"
    $(document).on('change', '#inputFile', ()=>{
        if (!/(\.xlsx)$/i.exec(inputFile.val())) {
            inputFile.css({ border: '#a94442 2px solid' });
            inputFile.after('<span class="text-danger align-center" style="display:block;">Invalid file type | Input only accepts <b>Excel(.xlsx)</b> files</span>');
            inputFile.val("");
            $('#upload').attr('disabled', true);
        } else {
            checkCurrentUpload();
            $('#upload').removeAttr('disabled');
        }
    });
    //validation to make sure that the file does not have any invalid data (", ', NULL, Null, null, NA, N/A, N\A) or invalid column order
    var input = document.getElementById('inputFile');
    input.addEventListener('change', function(){
        if(/(\.xlsx)$/i.exec(inputFile.val())){
            readXlsxFile(input.files[0], { dateFormat: 'MM/DD/YY' }).then(function(data){
                const columnorder = ["Project Department", "Project Code", "Project Type", "Resource Name", "Resource Type", "EPF Number", "Resource Department", "Allocated Hours", "Actual Effort", "Billed Effort"];
                var excellength, pointer;
                excellength = pointer = data.length;
                while (pointer > 0) {
                    var row = excellength - pointer;
                    var excelrow = data[row];
                    if ((row === 1 && !compareArrays(columnorder, excelrow)) || (row > 1 && excelrow.length === 10 && (excelrow.includes("null") || excelrow.includes("NULL") || excelrow.includes("Null") || excelrow.includes("") || excelrow.includes(null) || excelrow.includes("NA") || excelrow.includes("N/A") || excelrow.includes("N\A") || excelrow.includes("na") || excelrow.includes("TBD") || excelrow.includes("TBA") || excelrow.includes("TBC") || excelrow.includes("tbd") || excelrow.includes("tba") || excelrow.includes("tbc") || excelrow.some(x => Array.from(x).some(e => e === '"')) || excelrow.some(x => Array.from(x).some(e => e === "'"))))) {
                        inputFile.css({ border: '#a94442 2px solid' });
                        inputFile.val("");
                        $('#upload').attr('disabled', true);
                        if(row === 1 && !compareArrays(columnorder, excelrow)){
                            inputFile.after('<span class="text-danger align-center" style="display:block;">Invalid data in file | <b>Unordered</b> columns detected</span>');
                            break;
                        }
                        if (row > 1 && (excelrow.includes("null") || excelrow.includes("NULL") || excelrow.includes("Null") || excelrow.includes("") || excelrow.includes(null) || excelrow.includes("NA") || excelrow.includes("N/A") || excelrow.includes("N\A") || excelrow.includes("na") || excelrow.includes("TBD") || excelrow.includes("TBA") || excelrow.includes("TBC") || excelrow.includes("tbd") || excelrow.includes("tba") || excelrow.includes("tbc") || excelrow.some(x => Array.from(x).some(e => e === '"')) || excelrow.some(x => Array.from(x).some(e => e === "'")))) {
                            inputFile.after('<span class="text-danger align-center" style="display:block;">Invalid data in file | <b>EMPTY, NULL, NA, ", \'</b> cell values detected</span>');
                            break;
                        }
                    }
                    pointer--;
                }
            });
        }
    });
    //upload the valid dataset/file
    $(document).on('submit', '#fileUploader', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        $('.upload').css('width', 0 + '%');
        $('#progressModal').css('display', 'block');
        var width = 0;
        var timer = setInterval(frame, 8);
        function frame(){
            if(width === 100){
                clearInterval(timer);
            }else{
                width++;
                $('.upload').css('width', width + '%');
                $('.progress-percentage').html(width * 1 + '%');
                if(width <= 40){
                    $('.progress-text').html("Uploading File...");
                }else if(width <= 97){
                    $('.progress-text').html("Processing Upload Data...");
                }else{
                    $('.progress-text').html("Just A Little House Keeping...");
                }
            }
        }
        var formData = new FormData($('#fileUploader')[0]);
        formData.append('month', $('#month').val());
        formData.append('year', $('#year').val());
        $.ajax({
            type:'POST',
            url:'/import',
            processData: false,
            contentType: false,
            cache: false,
            data : formData
        }).done((response)=>{
            $('#alert').text(response.success);
            $('.alert').addClass('alert-success');
            $('.block-header').show();
            if($('.card').first().next().length){
                $('.card').first().next().remove();
            }
            $('<div class="card"><div class="header"><div class="heading-left sides"></div><div class="align-center heading-center">'+ response.month + ' ' + response.year + ' data uploaded as at :' + new Date().toLocaleString() +'</div><div class="align-right sides"><button type="button" class="btn btn-danger btn-circle waves-effect waves-circle waves-float" id="deleteupload"><i class="material-icons">delete_forever</i></button></div></div><div class="body"><table class="table-bordered" id="uploadtable"><thead><tr><th>Project HOD</th><th>Project Code</th><th>Project Type</th><th>EPF</th><th>Resource Name</th><th>Resource Type</th><th>Business Unit</th><th>Allocated Hours</th><th>Acctual Effort</th><th>Billed Effort</th></tr></thead><tbody></tbody></table></div></div>').insertAfter('.card');
            latestupload = { month: response.month, year: response.year };
            $('#upload').attr('disabled', true);
            generateTable(response.upload, '#uploadtable tbody');
            hideAlert();
            clearForm();
            closeProgressBar();
        }).then((response)=>{
            if(Object.keys(response.newbus).length){
                var content = document.createElement("p");
                content.innerHTML = "The following new BUs have been added:<br /><br />" + Object.keys(response.newbus).map(function(key){ return "<b>" + response.newbus[key] + "</b><br />"; }).join('') + "<br /><small>Note: Not a System Administrator, Please notify the relevant personnel to manage BU access.</small>";
                swal({
                    title: "New BU Addition Detected",
                    content: content,
                    icon: "info",
                    closeOnClickOutside: false,
                    closeOnEsc: false
                }).then((newBUs)=>{
                    if(newBUs){
                        swal({
                            title: "Reload Required!",
                            text: "Please reload the application/page to access the new BUs on reports.",
                            icon: "info",
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                            buttons: {
                                cancel: {
                                    text: "I'll Reload Manually",
                                    visible: true
                                },
                                confirm: {
                                    text: "Reload Now!"
                                }
                            }
                        }).then((reload)=>{
                            if (reload) {
                                $(window).unbind('beforeunload');
                                location.reload();
                            }
                        });
                    }
                });
            }
            checkCurrentUpload();
        });
    });
    //option to delete the recently uploaded dataset/file
    $(document).on('click', '#deleteupload', ()=>{
        swal({
            title: "Delete?",
            text: "Are you sure you want to delete what you just uploaded?",
            icon: "error",
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
            if (deleteIt) {
                $.get({
                    url: '/deleterecent',
                    data: latestupload,
                    dataType: 'JSON'
                }).done((response)=>{
                    $('#alert').text(response.success);
                    $('.alert').addClass('alert-success');
                    $('.block-header').show();
                    hideAlert();
                    if ($('.card').first().next().length) {
                        $('.card').first().next().remove();
                    }
                });
            }
        });
    });
    //function to hide alerts which appear on page after 5 seconds
    function hideAlert(){
        setTimeout(function(){
            if ($('.block-header').is(':visible')) {
                $('.alert').removeClass('alert-danger');
                $('.alert').removeClass('alert-success');
                $('.block-header').hide();
                $('#alert').text("");
            }
        }, 5000);
    }
    //function to close the progress bar after upload process is completed
    function closeProgressBar(){
        $('.upload').css('width', 0 + '%');
        $('.progress-text').html("");
        $('#progressModal').css('display', 'none');
    }
    //function to clear the form
    function clearForm(){
        $('#fileUploader').each(function(){
            this.reset();
        });
        $('#year').val((new Date()).getFullYear());
        $('#month').val(months[((new Date()).getMonth()+months.length-1)%months.length]);
    }
    //function to check the current month that is selected is a valid month
    function checkMonth(month){
        var result = $.inArray(month, months);
        if (result === -1) {
            return false;
        }
    }
    //function to check that the current selected month and year is eligible for upload
    var time = 0;
    function checkCurrentUpload(){
        if ($('#progressModal').is(':visible') || !$('#inputFile').val()) {
            return false;
        }
        setTimeout(function(){
            if (!$('#progressModal').is(':visible')) {
                $.get({
                    url: "currentuploadlist",
                    dataType: 'JSON'
                }).done((response)=>{
                    var selectedupload = { month: $('#month').val(), year: parseInt($('#year').val()) };
                    curentuploadlist = response.currentuploadlist;
                    var result = curentuploadlist.filter( period => Object.is(JSON.stringify(period), JSON.stringify(selectedupload)) );
                    if (result.length) {
                        $('#month , #year').css({ border: '#a94442 2px solid' });
                        if (!$('#year').parent().parent().next().length) {
                            $('#year').parent().parent().after('<tr><td colspan="2"><span class="text-danger align-center" style="display:block;">The data set <b>(' + $('#month').val() + ' ' +  $('#year').val() + ')</b> you are trying to upload <b>already exists!</b><br />Please check the period and try again.</span><td></tr>');
                        }
                        $('#upload').attr('disabled', true);
                    } else {
                        $('#month , #year').removeAttr('style');
                        if ($('#inputFile').val()) {
                            $('#upload').removeAttr('disabled');
                        }
                        if ($('#year').parent().parent().next().length) {
                            $('#year').parent().parent().next().remove();
                        }
                    }
                    if ($('#fileUploader').is(':visible')) {
                        time = 1000;
                        checkCurrentUpload();
                    }
                });
            }
        }, time);
    }
    
});

//function to compare arrays with returns true if arrays are identical false if otherwise
function compareArrays(first, second){
    //write type error
    return first.every((e)=> second.includes(e, first.indexOf(e))) && second.every((e)=> first.includes(e, second.indexOf(e))) && first.length === second.length;
}