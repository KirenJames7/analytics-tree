$(document).ready(function (){
    $.ajax({
        url: '/utilizationsummary',
        dataType: 'html'
    }).done(function(page){
        $('.container-fluid div div .card').html(page);
    }).then(function(){
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
    });
});