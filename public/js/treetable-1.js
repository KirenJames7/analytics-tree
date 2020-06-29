//Refactor
async function generateTreeTable(tableObj, location){
    if($(location).children().length !== 0){
        $(location).children().remove();
    }
    var rowhtml = '<tr>';
    var prevKey = [];
    var lastKey;
    var table = '';
    var len, top;
    len = top = tableObj.length;
    while(len > 0){
        let key = top - len;
        //await new Promise(resolve => setInterval(resolve, 0));
        let row = tableObj[key];
        var rowlen, left;
        rowlen = left = row.length;
        while(rowlen > 0){
            let cell = left - rowlen;
            let cellValue = row[cell] || "";
            if(cell === 0 && cellValue){
                rowhtml = '<tr data-tt-id="'+ key +'">';
            }else if(cell === 0 && prevKey[0] && !cellValue){
                rowhtml = '<tr data-tt-id="'+ key +'" data-tt-parent-id="'+ prevKey[0] +'">';
            }
            if(cellValue && cell < 7){
                if(cell === 0){
                    prevKey[0] = key;
                }else if(cell === 1){
                    prevKey[1] = key;
                }else if(cell === 2){
                    rowhtml = rowhtml.replace('data-tt-parent-id="'+ prevKey[0] +'"','data-tt-parent-id="'+ prevKey[1] +'"');
                    prevKey[2] = key;
                }else if(cell === 3){
                    rowhtml = rowhtml.replace('data-tt-parent-id="'+ prevKey[0] +'"','data-tt-parent-id="'+ prevKey[2] +'"');
                    prevKey[3] = key;
                }else if(cell === 4){
                    rowhtml = rowhtml.replace('data-tt-parent-id="'+ prevKey[0] +'"','data-tt-parent-id="'+ prevKey[3] +'"');
                    prevKey[4] = key;
                }else if(cell === 5){
                    rowhtml = rowhtml.replace('data-tt-parent-id="'+ prevKey[0] +'"','data-tt-parent-id="'+ prevKey[4] +'"');
                    lastKey = key;
                }else if(cell === 6){
                    rowhtml = rowhtml.replace('data-tt-parent-id="'+ prevKey[0] +'"','data-tt-parent-id="'+ lastKey +'"');
                }
            }
            if(cell > 6){
                rowhtml += '<td class="align-right">' + cellValue + '</td>';
            }else{
                rowhtml += '<td>' + cellValue + '</td>';
            }
            rowlen--;
        }
        len--;
        rowhtml += '</tr>';
        table += rowhtml;
    }
    $(location).html(table);
}