var $task_frequency = $('#task_frequency'), $task_date = $('#task_date'), $task_date_label = $("label[for='task_date']");
$task_frequency.change(function() {
	if($task_frequency.val() == '4'){
		$task_date.removeAttr('disabled');
        $task_date.show();
        $task_date_label.show();
	}
	else{
		$task_date.attr('disabled', 'disabled').val('');
        $task_date.hide();
        $task_date_label.hide();
	}
}).change();

var $task_device_id = $('#task_device_id'), $task_action = $('#task_action'), elemOpt = new Array();
elemOpt[0] = document.createElement("option");
elemOpt[1] = document.createElement("option");
elemOpt[2] = document.createElement("option");

elemOpt[0].value = "2";
elemOpt[1].value = "3";
elemOpt[2].value = "4";

elemOpt[0].id = "tarefa_ligar";
elemOpt[1].id = "tarefa_desligar";
elemOpt[2].id = "tarefa_abrir";

elemOpt[0].innerHTML = "Ligar";
elemOpt[1].innerHTML = "Desligar";
elemOpt[2].innerHTML = "Abrir";

$task_device_id.change(function(){
    $task_action.html("");
    for(elem in elemOpt){
        $task_action.append(elemOpt[elem]);
    }
    $task_device_id.find('option:selected').each(function(){
	    if($(this).data("device-type") == "2"){
            $task_action.find("option").each(function(index){
                if(index != 2) $(this).remove();
            });
	    } 
	    else{
            $task_action.find("option").each(function(index){
                if(index == 2) $(this).remove();
            });
	    }
    });
}).change();

function selectModuleData(data){
    $(".modal-body #task_device_id").val(data.task_device_id);
    $(".modal-body #task_action").val(data.task_action);
    $(".modal-body #task_frequency").val(data.task_frequency);
    if(data.task_frequency == '4'){ 
        $('.modal-body #task_date').removeAttr('disabled');
        $('.modal-body #task_date').show();
        $("label[for='task_date']").show();
    } 
    else{
        $('.modal-body #task_date').attr('disabled', 'disabled');
        $('.modal-body #task_date').hide();
        $("label[for='task_date']").hide();
    }
    $task_device_id.change(function(){
        $task_action.html("");
        for(elem in elemOpt){
            $task_action.append(elemOpt[elem]);
        }
        $task_device_id.find('option:selected').each(function(){
            if($(this).data("device-type") == "2"){
                $task_action.find("option").each(function(index){
                    if(index != 2) $(this).remove();
                });
            } 
            else{
                $task_action.find("option").each(function(index){
                    if(index == 2) $(this).remove();
                });
            }
        });
    }).change();
    $(".modal-body #task_date").val(data.task_date);
    $(".modal-body #task_time").val(data.task_time);
    $(".modal-body input[name=task_status][value=" + data.task_status + "]").prop('checked', true);
}

function rowStyle(row, index) {
    var classes = ['active', 'success', 'info', 'warning', 'danger'];
    if(row.task_status == 1) return{ classes: classes[1] };
    if(row.task_status == 0) return{ classes: classes[4] };
    return {};
}

function operateFormatter(value, row, index) {
    return [
        //'<a class="like" href="javascript:void(0)" title="">',
            //'<i class="glyphicon glyphicon-heart"></i>',
        //'</a>',
        '<a class="remove ml10" style="float:right;margin-left:10px" href="javascript:void(0)" onclick="deleteRow(' + row.task_id + ');" title="">',
            '<i class="glyphicon glyphicon-remove"></i>',
        '</a>',
        '<a class="edit ml10" style="float:right" href="javascript:void(0)" title="" onclick="hideAdvancedForm($(\'#form-type\'));window.actionForm=\'isAjax=2&moduleName='+moduleName+'&moduleAction=edit&id=' + row.task_id + '\';selectRowToUpdate(' + row.task_id + ');">',
            '<i class="glyphicon glyphicon-edit"></i>',
        '</a>'
    ].join('');
}

var rowDeleteMsg = 'Deseja mesmo excluir esta tarefa?';