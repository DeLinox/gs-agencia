var lastSelectedRow;
//var trs;
 //= document.getElementById('tableStudent').tBodies[0].getElementsByTagName('tr');

// disable text selection
document.onselectstart = function() {
    return false;
}

var RowClick = function() {
    if (window.event.ctrlKey) {
        toggleRow(this);
    }
    
    if (window.event.button === 0) {
        if (!window.event.ctrlKey && !window.event.shiftKey) {
            clearAll();
            toggleRow(this);
        }
    
        if (window.event.shiftKey) {
            selectRowsBetweenIndexes([lastSelectedRow.rowIndex, this.rowIndex])
        }
    }
}

function toggleRow(row) {
    if(row.className=='selected'){
        $(row).find('input[type=checkbox]').prop("checked", false).change();
    }else{
        $(row).find('input[type=checkbox]').prop("checked", true).change();
    }
    //row.className = row.className == 'selected' ? '' : 'selected';

    lastSelectedRow = row;
}

function selectRowsBetweenIndexes(indexes) {
    indexes.sort(function(a, b) {
        return a - b;
    });

    for (var i = indexes[0]; i <= indexes[1]; i++) {
        //trs[i-1].className = 'selected';
        //$('#mitabla tbody tr').eq(i-1).addClass('selected');
        $('#mitabla tbody tr').eq(i-1).find('input[type=checkbox]').prop("checked", true).change();
    }
}

function clearAll() {
    //$('#mitabla tbody tr').removeClass('selected');
    //$('#mitabla tbody tr input[type=checkbox]').prop("checked", false).change();
    for (var i = 0; i < $('#mitabla tbody tr').length; i++) {
        //trs[i].className = '';
        $('#mitabla tbody tr').eq(i).find('input[type=checkbox]').prop("checked", false).change();
    }
}
/*
 $(document).click(function (e) {
                    if ($(e.target).parents("#mitabla").length === 0) 
                    {
                        //console.log($(e.target).parents("#mitabla"));
                        clearAll();
                    }
                });*/