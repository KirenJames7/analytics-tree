var whereArr = {};
var bunames;
var rows;
var pae;
var pbe;
function report(){
$(document).ready(function (){
        getLatestPeriod();
    //Refactor SELECT
    $('.utilization-filter .multidropdown').each(function(index){
        $('.utilization-filter .multiselect-container')[index].id = 'filter' + $('select')[index].id.substr(3);
        $.get({
            async:false,
            url: '/getreportfilters/' + $('select')[index].id.substr(3),
            dataType: 'JSON'
        }).done((response) => {
            $.each(response.filterdata, function(key, data){
                $('.utilization-filter #' + $('select')[index].id).append('<option value="' + data +'">' + data +'</option>');
                $('.utilization-filter #filter' + $('select')[index].id.substr(3)).append('<li><a tabindex="0"><label class="checkbox" title="' + data + '"><input value="' + data + '" type="checkbox">' + data + '</label></a></li>');
            });
            if($('select')[index].id.substr(3) === 'year'){
                $('#selyear').multiselect('select', [ latestyear ]);
                if(latestyear === 2018){
                    $('table').find('th:contains("Project Department")')[0].innerText = 'Project HOD';
                    $('table').find('th:contains("Resource Department")')[0].innerText = 'Business Unit';
                }
            }
            if($('select')[index].id.substr(3) === 'month'){
                $('#selmonth').multiselect('select', [ latestmonth ]);
            }
        });
    });
    
    $.get({
        async: false,
        url: '/getrolescope',
        dataType: 'JSON',
        data: { buid: session.rolescope }
    }).done((response)=>{
        bunames = response.buscope;
        var selecthtml = '<select class="multidropdown" id="selbuname" multiple="multiple" data-role="multiselect">';
        $.each(response.buscope, (key, bu)=>{
            selecthtml += '<option value="' + bu + '">' + bu + '</option>';
        });
        selecthtml += '</select>';
        $('.scope-filter').last().append(selecthtml);
        $('#selbuname').multiselect({
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
                $('.multiselect-reset div a').addClass('test');
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
    
    var curTable = '#' + $('table').attr('id');
    $('.multidropdown').each(function(){
        whereArr[$(this).parent().context.id.substr(3)] = $(this).val();
    });
    getReportData(curTable);
    $('.multidropdown').change(function(){
        if($('#selyear').val() === null){
            swal({
                title: 'Please Select Year to Generate Report',
                text: 'The report will not be generated until the a Year is selected. If year is different to what was selected, the selecting year will be displayed disregarding the month.',
                icon: 'warning'
            });
        }else{
            $('.multidropdown').each(function(index){
                if($('select')[index].id.substr(3) === 'year' && Array.isArray($(this).val()) && $(this).val().includes('2018') && $(this).val().length > 1){
                    $('#selyear').multiselect('deselect', [ '2018' ]);
                    if($('table').find('th:contains("Project HOD")')[0] !== undefined){
                        $('table').find('th:contains("Project HOD")')[0].innerText = 'Project Department';
                        $('table').find('th:contains("Business Unit")')[0].innerText = 'Resource Department';
                        $('#selmonth').multiselect('deselectAll', false);
                        $('#selmonth').multiselect('refresh');
                    }
                    whereArr[$(this).parent().context.id.substr(3)] = $(this).val();
                    swal({
                        title: 'Structure Difference',
                        text: 'Due to stucture change the year 2018 cannot be viewed in the same report. Showing the newer data.',
                        icon: 'warning'
                    });
                }else{
                    if($('select')[index].id.substr(3) === 'year' && $(this).val().includes('2018')){
                        if($('table').find('th:contains("Project Department")')[0] !== undefined){
                            $('table').find('th:contains("Project Department")')[0].innerText = 'Project HOD';
                            $('table').find('th:contains("Resource Department")')[0].innerText = 'Business Unit';
                            $('#selmonth').multiselect('deselectAll', false);
                        }
                        $('#selmonth').multiselect('refresh');
                    }
                    if($('#selyear').val().includes(String(latestyear))){
                        $('#selmonth').multiselect('select', [ latestmonth ]);
                    }
                    whereArr[$(this).parent().context.id.substr(3)] = $(this).val();
                }
            });
            getReportData(curTable);
        }
        $('.multiselect-search').focus();
        $('li.multiselect-filter input').val('').trigger('keydown');
    });
    //PROBLEM WITH BU RESET BUTTON
//    document.getElementsByClassName("a.test").onclick = function(){
//        console.log("Test")
//    };
//    $(document).on('mousedown', 'a.test', function(){
//        console.log("TEST")
//        $('#selbuname').val(null);
//        whereArr[$('#selbuname').attr('id')] = null; 
//        getReportData(curTable);
//    });
    $('.utilization-filter .multiselect-reset a').click(function(){
        $('#sel' + $(this).parents().eq(2).attr('id').substr(6)).val(null);
        whereArr[$(this).parents().eq(2).attr('id').substr(6)] = null; 
        getReportData(curTable);
    });
    $(curTable + " tbody").on("mousedown", "tr", function() {
        $(".selected").not(this).removeClass("selected");
        $(this).toggleClass("selected");
    });
    $(document).on('click', curTable + 'expand', function(){
        $(curTable).treetable('expandAll');
        $('#' + $('table').attr('id') + 'collapse').removeClass();
        $('#' + $('table').attr('id') + 'collapse').addClass('btn btn-default excol');
        $(this).removeClass();
        $(this).addClass('btn btn-primary excol');
        return false;
    });
    $('#' + $('table').attr('id') + 'collapse').click(() => {
        $(curTable).treetable('collapseAll');
        $('#' + $('table').attr('id') + 'expand').removeClass();
        $('#' + $('table').attr('id') + 'expand').addClass('btn btn-default excol');
        $(this).removeClass();
        $(this).addClass('btn btn-primary excol');
        return false;
    });
    $('#xlsx').click(function(){
        if(rows > 1500)
            exportToExcel('#' + $('table').attr('id'), $('table').attr('id'), 'xlsx');
        else{
            $(curTable).treetable('expandAll');
            exportToExcel('#' + $('table').attr('id'), $('table').attr('id'), 'xlsx');
            $(curTable).treetable('collapseAll');
        }
        return false;
    });
});
}
async function getReportData(table){
    $('.card').block({
        message: 'Generating... Please wait...',
        css: {
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff'
        }
    });
    if(!whereArr.buname){
        whereArr.buname = bunames;
    }
    $.get({
        url: '/getreportdata' + $('table').attr('id'),
        dataType: 'JSON',
        data: whereArr
    }).done((response) => {
        if((rows = Object.keys(response.reportdata).length) > 1500)
            $('.excol').hide(), $('.excolmsg').show();
        else
            $('.excol').show(), $('.excolmsg').hide();
        generateTreeTable(response.reportdata, table + ' tbody');
        $(table).treetable({ expandable: true }, true);
    }).then(() => {
        getMax();
        $('.card').unblock();
    });
}

async function getMax(){
    $.get({
        url: 'getmax',
        dataType: 'JSON',
        data: whereArr
    }).done((response)=>{
        if(pae && pbe){
            pae.update({
                from: 70
            });
            pbe.update({
                from: 70
            });
            pae.destroy();
            pbe.destroy();
        }
        $('#pae').ionRangeSlider({
            grid: true,
            grid_num: response.maxpae/10,
            min: 0,
            max: response.maxpae,
            from: 70,
            step: 10,
            onStart: function (data){
                $.each($('tr td:nth-child(10)'), (index,td)=>{
                    if(td.innerText < data.from){
                        td.style['background-color'] = '#a94442';
                        td.style['color'] = 'white';
                    }else{
                        td.style['background-color'] = '';
                        td.style['color'] = '';
                    }
                });
            },
            onFinish: function (data){
                $.each($('tr td:nth-child(10)'), (index,td)=>{
                    if(td.innerText < data.from){
                        td.style['background-color'] = '#a94442';
                        td.style['color'] = 'white';
                    }else{
                        td.style['background-color'] = '';
                        td.style['color'] = '';
                    }
                });
            }
        });
        $('#pbe').ionRangeSlider({
            grid: true,
            grid_num: response.maxpbe/10,
            min: 0,
            max: response.maxpbe,
            from: 70,
            step: 10,
            onStart: function (data){
                $.each($('tr td:nth-child(12)'), (index,td)=>{
                    if(td.innerText < data.from){
                        td.style['background-color'] = '#a94442';
                        td.style['color'] = 'white';
                    }else{
                        td.style['background-color'] = '';
                        td.style['color'] = '';
                    }
                });
            },
            onFinish: function (data){
                $.each($('tr td:nth-child(12)'), (index,td)=>{
                    if(td.innerText < data.from){
                        td.style['background-color'] = '#a94442';
                        td.style['color'] = 'white';
                    }else{
                        td.style['background-color'] = '';
                        td.style['color'] = '';
                    }
                });
            }
        });
        pae = $('#pae').data("ionRangeSlider");
        pbe = $('#pbe').data("ionRangeSlider");
    });
}

function exportToExcel(table, name, type, fn, dl){
    var wb = XLSX.utils.table_to_book(table,{sheet:name});
    return dl ? XLSX.write(wb, {bookType:'xlsx',  bookSST:true, type: 'binary', skipHidden: true}) : XLSX.writeFile(wb, fn || (name + '.' + (type || 'xlsx')));
}

function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}