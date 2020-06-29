async function generateTable(tableObj, location){
    if($(location).children().length !== 0){
        $(location).children().remove();
    }
    var table = '';
    var rowhtml = '';
    $.each(tableObj, function(key, rows){
        rowhtml = '<tr>';
        $.each(rows, function(row, data){
            if(data === null)
                data = "";
            rowhtml += '<td>' + data + '</td>';
        });
        rowhtml += '</tr>';
        table += rowhtml;
    });
    $(location).html(table);
}