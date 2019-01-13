function selectModuleData(data){
    $(".modal-body #device_type").val(data.device_type);
    $(".modal-body #device_name").val(data.device_name);
    $(".modal-body #device_order").val(data.device_order);
    $(".modal-body input[name=device_status][value=" + data.device_status + "]").prop('checked', true);
    $(".modal-body #device_voice_on").text(data.device_voice_on);
    $(".modal-body #device_voice_off").text(data.device_voice_off);
}

function rowStyle(row, index){
    var classes = ['active', 'success', 'info', 'warning', 'danger'];
    if(row.device_status == 1) return{ classes: classes[1] };
    if(row.device_status == 0) return{ classes: classes[4] };
    return {};
}

function operateFormatter(value, row, index){
    return [
        //'<a class="like" href="javascript:void(0)" title="">',
            //'<i class="glyphicon glyphicon-heart"></i>',
        //'</a>',
        '<a class="remove ml10" style="float:right;margin-left:10px" href="javascript:void(0)" onclick="deleteRow(' + row.device_id + ');" title="">',
            '<i class="glyphicon glyphicon-remove"></i>',
        '</a>',
        '<a class="edit ml10" style="float:right" href="javascript:void(0)" title="" onclick="hideAdvancedForm($(\'#form-type\'));window.actionForm=\'isAjax=2&moduleName='+moduleName+'&moduleAction=edit&id=' + row.device_id + '\';selectRowToUpdate(' + row.device_id + ');">',
            '<i class="glyphicon glyphicon-edit"></i>',
        '</a>'
    ].join('');
}

var rowDeleteMsg = 'Deseja mesmo excluir este dispositivo?';