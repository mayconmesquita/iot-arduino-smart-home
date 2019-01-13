function createAutoClosingAlert(selector, delay) {
	var alert 	 = $(selector).alert();
	timeoutAlert = setTimeout(function(){alert.hide();},delay);
	window.timeoutAlertOn = 1;
}

$(document).ready(function(){
	var pathname = window.location.pathname;
	var url      = window.location.href;

	pathname = pathname.replace('/', "");

	$(function(){
	    $("[data-hide]").on("click", function(){
	        $("." + $(this).attr("data-hide")).hide();
	        // -or-, unique alert
	        // $(this).closest("." + $(this).attr("data-hide")).hide();
	    });
	});
	$("#save-button").on("click",function(event){
		event.preventDefault();
	    var formData = $("form").serialize();
	    $.ajax({
	        type:'post',
	        url:pathname,
	        data:formData,
	        dataType:'json',
	        beforeSend:function(){
	        	$("#save-button").html("Salvando <i class=\"fa fa-refresh fa-spin\"></i>");
	        	$("#save-button").attr('disabled', true);
	        },
	        complete:function(){
				$("#save-button").html("Salvar");
				$("#save-button").attr('disabled', false);
				window.scrollTo(0,0);
	        },
	        success:function(data){
	        	if(data.status == 'success'){
	        		if(window.timeoutAlertOn == 1){
						window.timeoutAlertOn = 0;
						clearTimeout(timeoutAlert);
					}
					$("#error-alert").css('display', 'none', 'important');
					$("#error-alert-content").html("");
	        		$("#success-alert").css('display', 'block', 'important');
					$("#success-alert-content").html(data.message);


					createAutoClosingAlert("#success-alert", 4000);
		        } else if(data.status == 'error'){
		        	if(window.timeoutAlertOn == 1){
						window.timeoutAlertOn = 0;
						clearTimeout(timeoutAlert);
					}
	        		$("#success-alert").css('display', 'none', 'important');
					$("#success-alert-content").html("");
		        	$("#error-alert").css('display', 'block', 'important');
					$("#error-alert-content").html(data.message);
					createAutoClosingAlert("#error-alert", 4000);
		        }
	        }
	    });
	});
});