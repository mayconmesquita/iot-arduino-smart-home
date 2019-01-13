function recognizer(inputElement,continuous,event){
	if(continuous){
		var voice = $.trim(inputElement);
	}
	else{
		var voice = $.trim(inputElement.value);
	}

    var lampOn  =  ['acender lâmpada','acende lâmpada','lâmpada acende','lâmpada acende','lâmpada acender','acende luz','acender luz','liga luz','ligar luz','luz liga','luz ligar','acender', 'acende'];
    var lampOff =  ['apagar lâmpada','apaga lâmpada','lâmpada apaga','lâmpada apagar','apaga luz','apagar luz','apaga luz','apagar luz','luz apaga','desligar luz','desligar lâmpada','apagar','apaga'];

    var portOn  =  ['abrir porta','abre porta','porta abre','porta abra','abra porta','quero entrar','abrir'];
    var coming  =  ['estou chegando','estou entrando','entrando','chegando','cheguei'];
    var leaving =  ['estou saindo','estou indo','saindo','fui'];

    var airOn   =  ['está calor','está calor','ligar ar','liga ar'];
    var airOff  =  ['tá frio','está frio','desligar ar','desliga ar'];

	if($.inArray(voice, lampOn) > -1) ws.send("SL2");
	if($.inArray(voice, lampOff)> -1) ws.send("SL3");
	if($.inArray(voice, portOn) > -1) ws.send("SP1");
	if($.inArray(voice, coming) > -1){
		ws.send("SP1");
		ws.send("SL2");
		ws.send("SI1");
	}
	if($.inArray(voice, leaving) > -1){
		ws.send("SL3");
		ws.send("SI2");
	}
}

$(document).ready(function() {
    try {
        var recognition = new webkitSpeechRecognition();
    } 
    catch(e) {
        var recognition = Object;
    }

    recognition.continuous = true;
    recognition.interimResults = true;
    var interimResult = '';

    $('.mySpeech').click(function(){
        var recActivated = $('#speechIcon').hasClass('speechIconEnabled');
        
        if(!recActivated){
            $('#speechIcon').removeClass('speechIconDisabled').addClass('speechIconEnabled');
            recognition.start();
        }
        else{
            $('#speechIcon').removeClass('speechIconEnabled').addClass('speechIconDisabled');
            recognition.stop();
        }
    });

    recognition.onend = function() {
        $('#speechIcon').removeClass('speechIconEnabled').addClass('speechIconDisabled');
    };

    recognition.onresult = function (event) {
        for (var i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                recognizer(event.results[i][0].transcript, true);
            }
        }
    };
});