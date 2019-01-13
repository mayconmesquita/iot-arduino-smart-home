function abreURL(url,metodo,onde){
   if(metodo == 'POST'){
       $.post(url, function(data) {
       $("#carregador").show();
       $("#"+onde).load(url);
      });
   }
   else if(metodo == 'GET'){
      $.get(url, function(data) {
      $("#carregador").show();
        $.ajax({
            url: url,
            cache: false,
            dataType: "html",
            success: function(data) {
            }
        });
    });
  }
}

function recognizeSpeech(){
    var lampOn  =  ['acender lâmpada','acende lâmpada','lâmpada acende','lâmpada acende','lâmpada acender','acende luz','acender luz','liga luz','ligar luz','luz liga','luz ligar','acender', 'acende'];
    
    var lampOff =  ['apagar lâmpada','apaga lâmpada','lâmpada apaga','lâmpada apagar','apaga luz','apagar luz','apaga luz','apagar luz','luz apaga','desligar luz','desligar lâmpada','apagar','apaga'];

    var portOn  =  ['abrir porta','abre porta','porta abre','porta abra','abra porta','quero entrar','abrir'];
    var coming  =  ['estou chegando','estou entrando','entrando','chegando','cheguei'];
    var leaving =  ['estou saindo','estou indo','saindo','fui'];

    var airOn   =  ['está calor','está calor','ligar ar','liga ar'];
    var airOff  =  ['tá frio','está frio','desligar ar','desliga ar'];

    var maxMatches   = 1;
    var promptString = "Fale agora";
    var language     = "pt-BR";
    window.plugins.speechrecognizer.startRecognize(function(result){
        result = result[0];

        if ($.inArray(result, lampOn) > -1) abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=1&action=2','GET','conteudoHidden');
        if ($.inArray(result, lampOff) > -1) abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=1&action=3','GET','conteudoHidden');
        if ($.inArray(result, portOn) > -1) abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=2&action=1','GET','conteudoHidden');
        if ($.inArray(result, coming) > -1){
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=1&action=2','GET','conteudoHidden');
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=2&action=1','GET','conteudoHidden');
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=3&action=2&offset=5','GET','conteudoHidden'); 
        }
        if ($.inArray(result, leaving) > -1){
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=1&action=3','GET','conteudoHidden');
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=3&action=3&offset=5','GET','conteudoHidden');
        }
        if ($.inArray(result, airOn) > -1){
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=3&action=2&offset=5','GET','conteudoHidden');
        }
        if ($.inArray(result, airOff) > -1){
            abreURL('http://m.autocasa.tk/api/index.php?key=12qwaszx&device=3&action=3&offset=5','GET','conteudoHidden');
        }

    }, function(errorMessage){
        console.log("Erro: " + errorMessage);
    }, maxMatches, promptString, language);
}

function getSupportedLanguages(){
    window.plugins.speechrecognizer.getSupportedLanguages(function(languages){
        alert(languages);
    }, function(error){
        alert("Não há suporte às seguintes mensagens: " + error);
    });
}