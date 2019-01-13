var moduleName = $('#data-table').data("module");
var moduleApi  = $('#data-table').data("url");

function showAdvancedForm(elm){
	$(elm).val('basic');
	$(elm).text('Básico');
	$('.advanced-form').show();
}

function hideAdvancedForm(elm){
	$(elm).val('advanced');
	$(elm).text('Avançado');
	$('.advanced-form').hide();
}

$("#form-type").on("click",function(){
	if($(this).val() == 'advanced')   showAdvancedForm(this);
	else if($(this).val() == 'basic') hideAdvancedForm(this);
});

$("#save-button-modal").click(function(event){
	event.preventDefault();
    var formData = $("#modal-form").serialize();
	$.ajax({
        type:'post',
        url:moduleName,
        data:formData + '&' + window.actionForm,
        dataType:'json',
        beforeSend:function(){
        	$("#save-button-modal").html("Salvando <i class=\"fa fa-refresh fa-spin\"></i>");
        	$("#save-button-modal").attr('disabled', true);
        },
        complete:function(){
			$("#save-button-modal").html("Salvar");
			$("#save-button-modal").attr('disabled', false);
			window.scrollTo(0,0);
        },
        success:function(data){
        	if(data.status == 'success'){
		        $('#data-table').bootstrapTable('refresh', { url: moduleApi });
        		$('#add-new-modal').modal('hide');
				$("#error-alert").css('display', 'none', 'important');
				$("#error-alert-content").html("");
				$("#error-alert-modal").css('display', 'none', 'important');
        		$("#success-alert").css('display', 'block', 'important');
				$("#success-alert-content").html(data.message);
				createAutoClosingAlert("#success-alert", 4000);
	        } else if(data.status == 'error'){
        		$("#success-alert").css('display', 'none', 'important');
				$("#success-alert-content").html("");
	        	$("#error-alert-modal").css('display', 'block', 'important');
				$("#error-alert-content-modal").html(data.message);
				createAutoClosingAlert("#error-alert-modal", 4000);
	        }
        }
    });
});

function selectRowToUpdate(id){
	$.ajax({
        type:'post',
        url:moduleName,
		data: 'isAjax=2&moduleName='+moduleName+'&moduleAction=select&id='+id,
        dataType:'json',
        success:function(data){
			if(data.status == 'error'){
				$("#success-alert").css('display', 'none', 'important');
				$("#success-alert-content").html("");
	        	$("#error-alert").css('display', 'block', 'important');
				$("#error-alert-content").html(data.message);
				createAutoClosingAlert("#error-alert", 4000);
	        }
	        else{
				$("#error-alert").css('display', 'none', 'important');
				$("#error-alert-content").html("");
				selectModuleData(data);
				$('#add-new-modal').modal('show'); 
	        }
        }
    });
}

function deleteRow(id){
	if(!confirm(rowDeleteMsg)){ return false; };

	$.ajax({
        type:'post',
        url:moduleName,
		data: 'isAjax=2&moduleName='+moduleName+'&moduleAction=delete&id='+id,
        dataType:'json',
        complete:function(){
	        $('#data-table').bootstrapTable('refresh', {
	            url: moduleApi
	        });
        },
        success:function(data){
        	if(data.status == 'success'){
				$("#error-alert").css('display', 'none', 'important');
				$("#error-alert-content").html("");
        		$("#success-alert").css('display', 'block', 'important');
				$("#success-alert-content").html(data.message);
				createAutoClosingAlert("#success-alert", 4000);
	        } else if(data.status == 'error'){
        		$("#success-alert").css('display', 'none', 'important');
				$("#success-alert-content").html("");
	        	$("#error-alert").css('display', 'block', 'important');
				$("#error-alert-content").html(data.message);
				createAutoClosingAlert("#error-alert", 4000);
	        }
        }
    });
}