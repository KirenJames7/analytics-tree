async function contentController(title){
    var originaltitle = "Zone Analytics |";
    switch(title){
        case 'delete':
            $(document).prop('title', originaltitle + " Delete Dataset");
            pageLoader(title);
            break;
        case 'import':
            $(document).prop('title', originaltitle + " Import File");
            pageLoader(title);
            break;
        case 'utilizationsummary':
            $(document).prop('title', originaltitle + " Utilization Summary");
            pageLoader(title);
            $('.multidropdown option').hide();
            $('.multidropdown').multiselect({
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 600,
                nonSelectedText: 'Please Select',
                includeResetOption: true,
                includeResetDivider: true,
                onDropdownShown: function(event){
                    $('.multiselect-search').focus();
                    $('.multidropdown option:not([value])').remove();
                    $('label[title=""]').remove();
                    $('.multiselect-reset div a').removeClass('btn-default');
                    $('.multiselect-reset div a').addClass('btn-info waves-effect');
                },
                onDropdownHidden: function(event){
                    $('.multiselect-search').val('').trigger('keydown');
                },
                onChange: function($option) {
                    var query = $('li.multiselect-filter input').val();
                    if (query) {
                        $('li.multiselect-filter input').val('').trigger('keydown');
                        $('.multiselect-search').focus();
                    }
                }
            });
            report();
            break;
        case 'associateutilizationsummary':
            $(document).prop('title', originaltitle + " Associate Utilization Summary");
            pageLoader(title);
            $('.multidropdown option').hide();
            $('.multidropdown').multiselect({
                includeSelectAllOption: true,
                enableCaseInsensitiveFiltering: true,
                maxHeight: 600,
                nonSelectedText: 'Please Select',
                includeResetOption: true,
                includeResetDivider: true,
                onDropdownShown: function(event){
                    $('.multiselect-search').focus();
                    $('.multidropdown option:not([value])').remove();
                    $('label[title=""]').remove();
                    $('.multiselect-reset div a').removeClass('btn-default');
                    $('.multiselect-reset div a').addClass('btn-info waves-effect');
                },
                onDropdownHidden: function(event){
                    $('.multiselect-search').val('').trigger('keydown');
                },
                onChange: function($option) {
                    var query = $('li.multiselect-filter input').val();
                    if (query) {
                        $('li.multiselect-filter input').val('').trigger('keydown');
                        $('.multiselect-search').focus();
                    }
                }
            });
            report();
            break;
        case 'admin':
            $(document).prop('title', originaltitle + " Administration");
            pageLoader(title);
            break;
    }
}

async function pageLoader(title){
    if($('.container-fluid div div .card').children().length === 0){
        pageGrabber(title, $('.container-fluid div div .card'), 'html');
    }else{
        $('.container-fluid div div .card').children().remove();
        if($('.card').length > 1){
            $('.container-fluid div div .card').not(':first').remove();
        }
        pageGrabber(title, $('.container-fluid div div .card'), 'html');
    }
}

async function pageGrabber(url, location, method){
    $.ajax({
        async: false,
        url: '/'+url,
        dataType: 'html'
    }).done(function(page){
        switch (method){
            case 'html':
                location.html(page);
                break;
            case 'after':
                $(page).insertAfter(location).hide();
                $('.body').slideDown('slow');
                break;
        }
    });
}

var latestyear;
var latestmonth;
function getLatestPeriod(){
    $.get({
        async: false,
        url: '/getlatestperiod',
        dataType: 'JSON'
    }).done((response)=>{
        if(Object.keys(response).length){
            latestyear = response.latestyear;
            latestmonth = response.latestmonth;
        }
    });
}