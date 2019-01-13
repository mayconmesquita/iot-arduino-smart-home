/* 
  Arduino Smart Home
  Project started: 02/12/2014
  Author: Maycon Mesquita
*/

#include <SPI.h>
#include <SD.h>
#include <Ethernet.h>
// #include <utility/w5100.h> // Lib para reduzir delay na tentativa de conexão.
#include <sha1.h>
#include <WebSocketClient.h> // Cliente de websocket em node.js
#include <IRremote.h> // Lib para leitura de comandos infravermelho
#include "IRCodes.h"  // Códigos IR de um Ar condicionado
#include <EmonLib.h> // Lib para medição de uso de corrente e tensão

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };  
IPAddress ip(192, 168, 2, 50);
EthernetServer server(80);

EnergyMonitor emon1; // Inicia interface para ler corrente e tensão elétrica

String http_req; // Guarda a requisição HTTP do cliente via webserver
const char* msgResponse = ""; // String de Response
String msgReceived = ""; // String de Recepção
float temperatura[10]; // Buffer auxiliar de temperatura

int inicio = 0; // Etapa de execução do código
boolean debug_on = false; // Modo Debug
boolean show_headers = false; // Mostrar headers http do cliente web na Serial
boolean net = false; // Estado do web socket
boolean is_local = false; // Tipo de conexão Local (Webserver) ou Remota (Websocket)
const char* ws_ip = "192.157.238.204"; // Ip remoto do WebSocket
#define ws_port 80

unsigned long previousMillis  = 0;  // Leitura de temperatura (milis anterior)
unsigned long previousMillis2 = 0;  // Tentativa de conexão   (milis anterior)
unsigned long previousMillis3 = 0;  // Leitura de corrente    (milis anterior)
unsigned long timems;

unsigned long int update_sensor  = 1000;  // Leitura de temperatura (1s  , milis)
unsigned long int update_current = 60000; // Leitura de corrente    (1m  , milis3)
unsigned long int try_time       = 9000; // Tentativa de conexão   (8s , milis2)

long int local_refresh_speed = 1000; // Velocidade do refresh do webserver

int initial_try_number = 3; // Quantidade multiplicativa extra de tentativas de conexão ao iniciar sistema
int try_number = 3; // Quantidade de tentativas de conexão

EthernetClient client;
const int chipSelect = 53; // Pino para SD Card

int buttonState[10]      = {0,0,0,0,0,0,0,0,0,0}; // Estado atual dos botões.
int buttonLastState[10]  = {5,5,5,5,5,5,5,5,5,5}; // Estado anterior dos botões.

int redState[10]     = {0,0,0,0,0,0,0,0,0,0}; // Estado atual dos Red Sw.
int redLastState[10] = {5,5,5,5,5,5,5,5,5,5}; // Estado anterior dos Red Sw.

char* lamp = "RL00000000000000000000000000000000"; // Lâmpadas
char* port = "RP00000000000000000000000000000000"; // Portas
char* disp = "RD00000000000000000000000000000000"; // Dispositivos diversos
char* irem = "RI00000000000000000000000000000000"; // IRemote
char* airt = "RK00000000000000000000000000000000"; // Temperatura de Ar-condicionado
char* temp = "RT#00.0#00.0#00.0#00.0#00.0#00.0#00.0#00.0#00.0#00.0#00.0#00.0#"; // Sensores de temperatura
char* sean = "RS#000000#000000#000000#000000#000000#000000#"; // Sensores analógicos, corrente
char* sedi = "RZ00000000000000000000000000000000"; // Sensores digitais
char* mode = "RM00000000000000000000000000000000"; // Modos
char* conf = "RC00000000000000000000000000000000"; // Configurações
char* seco = "RQ000000000";

//char* macr = "RN00000000000000000000000000000000"; // Macros RETORNO!
//char* aces = "RA#00000#0#0000000000#00000000000000#0000000000#0000000000#"; // ID, Perm, User, Pass, Rfid, Bio RETORNO!

// PWM: 2-13 e 44-46 || SPI: 50 (miso), 51 (mosi), 52 (sck), 53 (ss)

//                             0,1,2,3,4,5, 6,7,  8,   9, 10,11,12,13,   14,15,16,17,18,19,20,21,  22,23,24,25,26,  27,28,29,30,31,     32,       33
unsigned long int pind[34] = { 3,5,6,7,8,9,44,45, 46,  22,23,24,25,26,   27,28,29,30,31,32,33,34,  35,36,37,38,39,  40,41,42,43,47,     48,       49};
//                           | ------Lamp------ | IR | -----Port-----   | --------Butt--------  |  -----ReedSw----- | --Presença-- | -Connect- | Buzzer |  

//                             0, 1, 2, 3, 4,  5, 6, 7, 8, 9,   10,     11, 12,  13, 14, 15
unsigned long int pina[16] = { A0,A1,A2,A3,A4, A5,A6,A7,A8,A9,  A10,  A11,A12, A13,A14,A15 };
//                           | --- Temp ---   | --- Sean ----| -IR-  |

IRsend irsend;
IRrecv irrecv(pina[10]);
decode_results results;

void red_read(){
  for (int i = 22; i <= 26; i++){
    redState[i-22] = digitalRead(pind[i]);
  }

  for (int i = 0; i <= 4; i++){
    if (redState[i] != redLastState[i] && redLastState[i] != 5){
      if (redState[i] == LOW){
        port[i+2] = '0';
        sd_write();
        if(net)wssend(port);
      }
      if (redState[i] == HIGH){
        port[i+2] = '1';
        sd_write();
        if(net)wssend(port);
      }
    }
  }

  for (int i = 0; i <= 9; i++){
    redLastState[i] = redState[i];
  }
}

void but_read(){
  for (int i = 14; i <= 21; i++){
    buttonState[i-14] = digitalRead(pind[i]);
  }

  for (int i = 0; i <= 7; i++){
    if (buttonState[i] != buttonLastState[i] && buttonLastState[i] != 5){
      if(lamp[i+2] == '0'){
        digitalWrite(pind[i], HIGH);
        acionaDisp("SL2");
      }
      else{
        digitalWrite(pind[i], LOW);
        acionaDisp("SL3");
      }
    }
  }

  for (int i = 0; i <= 9; i++){
    buttonLastState[i] = buttonState[i];
  }
}

void clear_ir(){
  irsend.sendNEC(0x00000000, 32);
  delay(200);
}

char temp_send(char cmd){
  prog_uint16_t signal[230];
  
  for(int i = 0; i<231; i++){
    if(cmd == '1') signal[i] = pgm_read_word(ArTemp16_1 + i);
    if(cmd == '2') signal[i] = pgm_read_word(ArTemp17_1 + i);
    if(cmd == '3') signal[i] = pgm_read_word(ArTemp18_1 + i);
    if(cmd == '4') signal[i] = pgm_read_word(ArTemp19_1 + i);
    if(cmd == '5') signal[i] = pgm_read_word(ArTemp20_1 + i);
    if(cmd == '6') signal[i] = pgm_read_word(ArTemp21_1 + i);
    if(cmd == '7') signal[i] = pgm_read_word(ArTemp22_1 + i);
    if(cmd == '8') signal[i] = pgm_read_word(ArTemp23_1 + i);
    if(cmd == '9') signal[i] = pgm_read_word(ArTemp24_1 + i);
    if(cmd == 'A') signal[i] = pgm_read_word(ArTemp25_1 + i);
    if(cmd == 'B') signal[i] = pgm_read_word(ArTemp26_1 + i);
    if(cmd == 'C') signal[i] = pgm_read_word(ArTemp27_1 + i);
    if(cmd == 'D') signal[i] = pgm_read_word(ArTemp28_1 + i);
    if(cmd == 'E') signal[i] = pgm_read_word(ArTemp29_1 + i);
    if(cmd == 'F') signal[i] = pgm_read_word(ArTemp30_1 + i);
    if(cmd == 'G') signal[i] = pgm_read_word(ArTemp31_1 + i);
  }
  clear_ir();
  irsend.sendRaw(signal,230,38);
  airt[2] = cmd;
  if(net)wssend(airt);
  sd_write();
  
  irrecv.enableIRIn();
}

char ir_send(char cmd, int ir_id = -1){
  prog_uint16_t signal[230];
  
  if(cmd == '1'){
    if(irem[2] = '1') cmd = '2';
    else cmd = '3';
  }
  if(cmd == '2'){
    for(int i = 0; i<231; i++){
      signal[i] = pgm_read_word(ArPowerOn_1 + i);
    }
    clear_ir();
    irsend.sendRaw(signal,230,38);
    irem[2] = '1';
    airt[2] = '5';
    if(net)wssend(irem);
    sd_write();
  }
  else if(cmd == '3'){
    for(int i = 0; i<231; i++){
      signal[i] = pgm_read_word(ArPowerOff_1 + i);
    }
    clear_ir();
    irsend.sendRaw(signal,230,38);
    irem[2] = '0';
    airt[2] = '0';
    if(net)wssend(irem);
    sd_write();
  }
  
  irrecv.enableIRIn();
}

void ir_read(){
  if (irrecv.decode(&results)){
    if (debug_on && results.value != 0xFFFFFFFF){
      Serial.print("0x");
      Serial.print(results.value, HEX);
      Serial.println(' ');
    }
    irrecv.resume();
  }

  switch(results.value){
    case 0x61F4F807:
      results.value = NULL;
      acionaDisp("SL1");
    break;

    case 0x61F47887:
      results.value = NULL;
      acionaDisp("SP1");
    break;

    case 0x61F420DF:
      results.value = NULL;
      acionaDisp("SI2");
    break;

    case 0x61F4906F:
      results.value = NULL;
      acionaDisp("SI3");
    break;

    case 0x94C0DF89:
      results.value = NULL;
      acionaDisp("SI2");
    break;

    case 0x25D5AC5F:
      results.value = NULL;
      acionaDisp("SI3");
    break;

    case 0x61F4619E:
      results.value = NULL;
      if(!is_local)websocket_conn();
      if(net)acionaDisp("S*");
    break;

    case 0x61F4C03F:
      results.value = NULL;
      if(is_local){
        digitalWrite(pind[32], HIGH);
        is_local = false;
        client.stop();
        websocket_conn();
      }
      else{
        is_local = true;
        client.stop();
        webserver_conn();
      }
    break;
  }
}

int error(char* error, int error_cod = 0){
  if (debug_on){
    if(error_cod > 0){
      Serial.print("Error(");
      Serial.print(error_cod);
      Serial.print("): ");
    }
    Serial.println(error);
  }
}

char previousTemp[10];

void temp_read(){
  char temperatura_b[10];
  int valorLido = 0;
  valorLido = analogRead(pina[0]); // Coloca na variável valorLido o que está a ser lido pelo sensor de temperatura
  temperatura[0] = (valorLido * 0.4887585532746823069403714565); // Base de conversão para Graus Celsius ((5/1023) * 100)
  
  dtostrf(temperatura[0], 2, 1, temperatura_b);

  temp[3] = temperatura_b[0];
  temp[4] = temperatura_b[1];
  temp[6] = temperatura_b[3];
  // temp[7] = temperatura_b[4]; Foi retirado o 4º digito de precisão

  if(strcmp(previousTemp, temperatura_b)){
    if(net && is_local == false)wssend(temp);
    dtostrf(temperatura[0], 2, 1, previousTemp);
  }
}

double irms1; // Sensor de Corrente 1
double consumo1;


void corrente_calc(){
  irms1  = emon1.calcIrms(1480); // Calcular IRMS 1
  
  char consumo1_b[20];
  
  // Cálculo do consumo em kWh de hoje
  consumo1 = consumo1 + (((irms1*220.0)/60000.0) * 1.0/3600.0);

  // Converte double em string
  dtostrf(consumo1, 8, 7, consumo1_b);

  seco[2] = consumo1_b[0];
  seco[3] = consumo1_b[1];
  seco[4] = consumo1_b[2];
  seco[5] = consumo1_b[3];
  seco[6] = consumo1_b[4];
  seco[7] = consumo1_b[5];
  seco[8] = consumo1_b[6];
  seco[9] = consumo1_b[8];
  
  // if(debug_on)Serial.println(seco);
  if(net && is_local == false)wssend(seco);
}


File myFile;
struct parameters{
  int interval;
  String conf;
  String lamp;
  String port;
  String disp;
  String irem;
  String airt;
} settings;

void sd_write(){
  // Desabilitar Ethernet para Ler Cartão SD
  digitalWrite(10, HIGH);
  digitalWrite(4, LOW);
  
  if(debug_on)Serial.println("Inciando SD para escrita..."); 
  if(!SD.remove("settings.txt")) error("Falha na inicializacao do SD.",3);
  
  // Abrir o arquivo settings para escrita
  myFile = SD.open("settings.txt", FILE_WRITE);
  
  // Se o arquivo foi aberto, escreva nele
  if (myFile){
    if(debug_on)Serial.println("Escrevendo estado atual de dispositivos.");    
    
    myFile.print("conf = ");
    conf[0] = 'S';
    myFile.println(conf);
    conf[0] = 'R';
    
    myFile.print("lamp = ");
    lamp[0] = 'S';
    myFile.println(lamp);
    lamp[0] = 'R';
    
    myFile.print("port = ");
    port[0] = 'S';
    myFile.println(port);
    port[0] = 'R';
    
    myFile.print("disp = ");
    disp[0] = 'S';
    myFile.println(disp);
    disp[0] = 'R';
    
    myFile.print("irem = ");
    irem[0] = 'S';
    myFile.println(irem);
    irem[0] = 'R';
    
    myFile.print("airt = ");
    airt[0] = 'S';
    myFile.println(airt);
    airt[0] = 'R';
    
    myFile.close();
    if(debug_on)Serial.println("Dados atualizados no SD.");
  }
  else error("Erro ao abrir o arquivo de configuracoes.",2);
  
  if(debug_on)Serial.println(' ');
  
  // Habilitar Ethernet e desabilitar Cartão SD
  digitalWrite(10, LOW);
  digitalWrite(4, HIGH);
}

void sd_read(){
  // Desabilitar Ethernet para Ler Cartão SD
  digitalWrite(10, HIGH);
  digitalWrite(4, LOW);

  if(debug_on)Serial.println("Iniciando SD para leitura...");
  
  if (!SD.begin(chipSelect)) error("Falha na inicializacao do SD.",3);
  
  myFile = SD.open("settings.txt"); // Abrir arquivo para leitura
  char character;
  String description = "";
  String value = "";
  boolean valid = true;
  
  while (myFile.available()){ // Ler o arquivo enquanto ele estiver disponível
    character = myFile.read();
    if(character == '/'){ // Comentário - ignorar esta linha
      while(character != '\n'){
      character = myFile.read();
    };
    }
    else if (isalnum(character)){  // Adicionar um caractere de descrição
      description.concat(character);
    }
    else if (character =='='){  // Fazer checagem de possíveis valores
      do { // Primeiramente retirar todos os caracteres em branco
        character = myFile.read();
      } while(character == ' ');
        if(description == "interval"){
          value = "";
          while(character != '\n'){
            if(isdigit(character)){
              value.concat(character);
            }
            else if(character != '\n'){
              valid = false; // Uso de valores inválidos
            }
            character = myFile.read();            
          };
          if (valid){ 
            // Converter string em um array de caracteres
            char charBuf[value.length()+1];
            value.toCharArray(charBuf,value.length()+1);
            // Converter char para inteiro
            settings.interval = atoi(charBuf);
          } 
          else {
            // Reverter para valores padrões em caso de entrada inválida no arquivo settings
            settings.interval = 60;
          }
      }
      else if(description == "conf") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.conf = value;
      } 
      else if(description == "lamp") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.lamp = value;
      } 
      else if(description == "port") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.port = value;
      } 
      else if(description == "disp") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.disp = value;
      }
      else if(description == "irem") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.irem = value;
      }
      else if(description == "airt") {
         value = "";
         do {
           value.concat(character);
           character = myFile.read();
         } while(character != '\n');
         settings.airt = value;
      }
      else { // Em caso de parametro desconhecido
        while(character != '\n')
        character = myFile.read();
      }
      description = "";
    } 
    else {
      // Ignorar este caractere (pode ser espaço, tab, nova linha, /r, /n, ou qualquer outra coisa)
    }
  }

  myFile.close();
  
  // Habilitar Ethernet e desabilitar Cartão SD
  digitalWrite(10, LOW);
  digitalWrite(4, HIGH);
  
  if(debug_on){ 
    // Serial.print("Interval: ");
    // Serial.println(settings.interval);
    Serial.print("Conf: ");
    Serial.println(settings.conf);
    Serial.print("Lamp: ");
    Serial.println(settings.lamp);
    Serial.print("Port: ");
    Serial.println(settings.port);
    Serial.print("Disp: ");
    Serial.println(settings.disp);
    Serial.print("Irem: ");
    Serial.println(settings.irem);
    Serial.print("Airt: ");
    Serial.println(settings.airt);
  }
  if(debug_on)Serial.println(' ');
}

int acionaDisp(String msgClient){
  if(msgClient[0] == 'S'){
    switch(msgClient[1]){
      case 'L':
        for(int i = 2 ; i <= 9 ; i++){
          if(msgClient[i] == '1'){
            if(lamp[i] == '0'){
              digitalWrite(pind[i-2], HIGH);
              lamp[i] = '1';
              if(net)wssend(lamp);
              sd_write();
            }
            else{
              digitalWrite(pind[i-2], LOW);
              lamp[i] = '0';
              if(net)wssend(lamp);
              sd_write();
            }
          }
          
          else if(msgClient[i] == '2'){
            digitalWrite(pind[i-2], HIGH);
            lamp[i] = '1';
            if(net)wssend(lamp);
            sd_write();
          }
          else if(msgClient[i] == '3'){
            digitalWrite(pind[i-2], LOW);
            lamp[i] = '0';
            if(net)wssend(lamp);
            sd_write();
          }
        }
      break;
      case 'P':
        for(int i = 2 ; i <= 7 ; i++){
          if(msgClient[i] == '1'){
            if(port[i] == '0'){
              digitalWrite(pind[i+7], HIGH);
              delay(50);
              digitalWrite(pind[i+7], LOW);
              port[i] = '1';
              if(net)wssend(port);
              sd_write();
            }
          }
        }
      break;
      case 'I':
        ir_send(msgClient[2]);
      break;
      case 'K':
        temp_send(msgClient[2]);
      break;
      case '*':
        if(net)wssend(lamp);
        if(net)wssend(port);
        if(net)wssend(disp);
        if(net)wssend(irem);
        if(net)wssend(airt);
        if(net)wssend(temp);
        if(net)wssend(sean);
        // if(net)wssend(sedi);
        // if(net)wssend(mode);
      break;
    }
  } 
}

class MyWebSocket : 
  public websocket::WebSocket{

    public:
      MyWebSocket(Client& client) : websocket::WebSocket(client){}
        
    public:
      virtual void onClose(){
        if(debug_on)Serial.println("Websocket fechado.");
      }
      
      virtual void onError(websocket::Result error){
        if(debug_on)Serial.print("Websocket erro: ");
        Serial.println(error);
      }
      
      virtual void onTextFrame(char const* msg, uint16_t size, bool isLast){
        msgReceived = msg;
        msgReceived = msgReceived.substring(0, size);
        wsreceive(msgReceived);
      }
  };
MyWebSocket webSocketClient(client);

int wssend(const char* msgResponse){
  if(!is_local){
    webSocketClient.sendData(msgResponse);
    client.flush();
  }
}

int wsreceive(String msgClient){
  if(debug_on)Serial.println("S: "+msgClient);
  acionaDisp(msgClient);
}

void websocket_conn(){
  int tryed = 0;

  if(debug_on && !is_local)Serial.println("Conectando websocket...");

  while(tryed < try_number && net == false){ // Faz 3 tentativas de conexão
    ir_read();
    but_read();
    red_read();
    if (client.connect(ws_ip, ws_port)){
      if(debug_on)Serial.println("Cliente websocket conectado.");
      delay(1);
      
      if (websocket::clientHandshake(client, ws_ip, "/ws") == websocket::Success_Ok){
        if(debug_on)Serial.println("Sucesso no handshake do websocket.");
        digitalWrite(pind[32], LOW);
        net = true;
        tryed = 5;
      }
      else error("Falha no handshake do websocket.",5);
    }
    else{
      error("Falha na conexao do websocket.",5);
      digitalWrite(pind[32], HIGH);
      net = false;
    }
    tryed++;
    MyWebSocket webSocketClient(client);
  }
  if(debug_on)Serial.println(' ');
}

void GetSwitchState(EthernetClient cl){
  if (lamp[2] == '1') cl.println("<button onclick=\"window.location='/SL1'\" class=\"button-on\">Lâmpada</button>");
  else cl.println("<button onclick=\"window.location='/SL1'\" class=\"button-off\">Lâmpada</button>");
  cl.println("<br>");
  if(port[2] == '1') cl.println("<button onclick=\"window.location='/SP0'\" class=\"button-on\">Porta</button>");
  else cl.println("<button onclick=\"window.location='/SP1'\" class=\"button-off\">Porta</button>");
  cl.println("<br>");
  if(irem[2] == '1') cl.println("<button onclick=\"window.location='/SI3'\" class=\"button-on\">Ar condicionado</button>");
  else cl.println("<button onclick=\"window.location='/SI2'\" class=\"button-off\">Ar condicionado</button>");
  cl.println("<br>");
  cl.println("Temperatura: ");
  cl.print(temperatura[0]);
  cl.println("º");
}

void webserver_conn(){
  server.begin();
  if(debug_on && is_local)Serial.println("Conectando webserver...");
  if(debug_on && is_local)Serial.print("Acesso local: ");
  if(debug_on && is_local)Serial.println(Ethernet.localIP());
  if(debug_on && is_local)Serial.println(' ');
  digitalWrite(pind[32], LOW);
  net = false;
  inicio = 2;
}

void setup(){
  Serial.begin(9600);
  pinMode(53, OUTPUT);

  sd_read();
  inicio = 1;

  for (int i = 0; i <= 13; i++){
    pinMode(pind[i], OUTPUT);
  }

  for (int i = 14; i <= 31; i++){
    pinMode(pind[i], INPUT_PULLUP);
  }

  for (int i = 0; i <= 15; i++){
    pinMode(pina[i], INPUT);
  }

  pinMode(pind[32], OUTPUT);
  pinMode(pind[33], OUTPUT);
  
  // Calibrar do Sensor CT:
  //   CT Ratio    /   Burden resistance 
  // (100A/0.05A)  /   220ohms
  //     2000      /  220 = 9.909
  // R(burden) = U(sensor)/I(sensor) = 2.5V * 0.0707A = 35.4Ω (33 nominal)
  emon1.current(5, 70); // Pino de Entrada, Calibragem

  Ethernet.begin(mac, ip);

  // setRetransmissionTime sets the Wiznet's timeout period, where each unit is 100us, so 0x07D0 (decimal 2000) means 200ms.
  // W5100.setRetransmissionTime(0x07D0); // 0x07D0 - 2000, 0x03E8 - 1000, 0x01F4 - 500, 0x012C - 300, 0x64 - 100
  // setRetransmissionCount sets the Wiznet's retry count.
  // W5100.setRetransmissionCount(3);

  irrecv.enableIRIn();
}

void loop(){
  ir_read();  // Leitura de IR
  but_read(); // Leitura de Botões
  red_read(); // Leitura de Red Sw.

  if(inicio == 1){
    acionaDisp(settings.lamp);
    acionaDisp(settings.port);
    acionaDisp(settings.disp);
    acionaDisp(settings.irem);
    acionaDisp(settings.airt);
    if(net)acionaDisp("S*");
    inicio = 2;
    
    if(is_local){
      webserver_conn();
    }
    else{
      for(int i = 1; i <= initial_try_number; i++){
        websocket_conn();
      }
    }
  }
  
  unsigned long currentMillis = millis();
  if(currentMillis - previousMillis >= update_sensor) {
    previousMillis = currentMillis;
    temp_read();
  }
  
  unsigned long currentMillis2 = millis();
  if(net == false && currentMillis2 - previousMillis2 >= try_time + 6000){ // 6000ms é o tempo gasto pela função websocket_conn()
    previousMillis2 = currentMillis2;  
    if(!is_local) websocket_conn();
    if(net == false){
      if (try_time == 9000){
        try_time = 10000;   // 10s
        error("Tentar reconectar em 10 segundos.",5);
      }
      else if (try_time == 10000){
        try_time = 30000;   // 30s
        error("Tentar reconectar em 30 segundos.",5);
      }
      else if (try_time == 30000){
        try_time = 60000;   // 30s
        error("Tentar reconectar em 60 segundos.",5);
      }
      else if (try_time == 60000){
        try_time = 150000;  // 1m
        error("Tentar reconectar em 5 minutos.",5);
      }
      else if (try_time == 300000){
        try_time = 600000;  // 5m
        error("Tentar reconectar em 10 minutos.",5);
      }
      else if (try_time == 600000){
        try_time = 1800000; // 10m
        error("Tentar reconectar em 30 minutos.",5);
      }
      else if (try_time == 1800000){
        try_time = 1800000; // 30m
        error("Tentar reconectar em 30 minutos.",5);
      }
    }
    else if(net == true){
      try_time = 9000;
      if(debug_on) Serial.println("Contador de reconexao atualizado para 10s.");
    }
  }
  
  unsigned long currentMillis3 = millis();
  if(currentMillis3 - previousMillis3 >= update_current){
    previousMillis3 = currentMillis3;
    
    timems = currentMillis3 - previousMillis3;
    corrente_calc();
  }
  
  if(is_local){
    EthernetClient client = server.available();
    if(client) {
      char request[10];
      int i = 0;
      request[9] = '\0';
      
      // Uma requisição http termina com uma linha branca
      boolean currentLineIsBlank = true;
      while(client.connected()){
        char c = client.read(); // Lê 1 byte (caractere) do cliente
        
        if (i < 9) {
          request[i] = c;
          i++;
        }
        
        http_req += c; // Salva 1 caractere da requisição HTTP por vez
        // Última linha da requisição do cliente é branca e termina com \n
        // Responde o cliente apenas depois de receber a última linha
        
        if (c == '\n' && currentLineIsBlank){
          // Envia um header http padrão como resposta (response)
          client.println("HTTP/1.1 200 OK");
          client.println("Content-Type: text/html");
          client.println("Connection: keep-alive");
          client.println();
          // Requisição AJAX para o estado a ser atualizado dinamicamente
          if (http_req.indexOf("index") > -1) {
            // Lê o estado atual e envia um paragrafo de texto apropriado
            GetSwitchState(client);
          }
          else {
            client.println("<!DOCTYPE html>");
            client.println("<html>");
            client.println("<head>");
            client.println("  <meta charset=\"UTF-8\">");
            client.println("  <title>Arduino Smart Home</title>");
            client.println("  <script>");
            client.println("    function GetSwitchState(){");
            client.println("      nocache=\"&nocache=\"+Math.random()*10000000;");        
            client.println("      var request=new XMLHttpRequest();");
            client.println("      request.onreadystatechange=function(){");
            client.println("        if(this.readyState==4){");
            client.println("          if (this.status==200){");
            client.println("            if(this.responseText!=null){");
            client.println("              document.getElementById(\"switch_txt\") .innerHTML = this.responseText;");
            client.println("            }");
            client.println("          }");
            client.println("        }");
            client.println("      }");
            client.println("      request.open(\"GET\",\"index\"+nocache,true);");
            client.println("      request.send(null);");
            client.print("      setTimeout('GetSwitchState()', ");
            client.print(local_refresh_speed);
            client.println(");");
            client.println("    }");
            client.println("  </script>");
            client.println("  <style>");
            client.println("    *{margin:0pt;padding:0pt;}");
            client.println("    p{text-align:center;}");
            client.println("    #box1{width:160px;height:120px;margin:50px auto;padding:60px 80px 100px;background:#fff;box-shadow:0px 0px 10px #aaa;border-radius:10px;}");
            client.println("    button{color:#222;width:160px;height:40px;margin-bottom:10px;font:normal 17px Cambria;text-align:center;}");
            client.println("    .button-on{background:#6dfa6d}");
            client.println("    .button-off{background:#e43f3f}");
            client.println("  </style>");
            client.println("</head>");
            client.println("<body onload=\"GetSwitchState()\">");
            client.println("  <div id=\"content\">");
            client.println("    <div id=\"box1\">");
            client.println("      <p id=\"switch_txt\"></p>");
            client.println("    </div>");
            client.println("  </div>");
            client.println("</body>");
            client.println("</html>");
          }
          
          if (strncmp("GET /SL1", request, 8) == 0) {
            acionaDisp("SL1");
          }
          if (strncmp("GET /SP1", request, 8) == 0) {
            acionaDisp("SP1");
          }
          if (strncmp("GET /SI2", request, 8) == 0) {
            acionaDisp("SI2");
          }
          if (strncmp("GET /SI3", request, 8) == 0) {
            acionaDisp("SI3");
          }
          
          if (strncmp("GET /SK1", request, 8) == 0) {
            acionaDisp("SK1");
          }
          if (strncmp("GET /SK2", request, 8) == 0) {
            acionaDisp("SK2");
          }
          if (strncmp("GET /SK3", request, 8) == 0) {
            acionaDisp("SK3");
          }
          if (strncmp("GET /SK4", request, 8) == 0) {
            acionaDisp("SK4");
          }
          if (strncmp("GET /SK5", request, 8) == 0) {
            acionaDisp("SK5");
          }
          if (strncmp("GET /SK6", request, 8) == 0) {
            acionaDisp("SK6");
          }
          if (strncmp("GET /SK7", request, 8) == 0) {
            acionaDisp("SK7");
          }
          if (strncmp("GET /SK8", request, 8) == 0) {
            acionaDisp("SK8");
          }
          if (strncmp("GET /SK9", request, 8) == 0) {
            acionaDisp("SK9");
          }
          if (strncmp("GET /SKA", request, 8) == 0) {
            acionaDisp("SKA");
          }
          if (strncmp("GET /SKB", request, 8) == 0) {
            acionaDisp("SKB");
          }
          if (strncmp("GET /SKC", request, 8) == 0) {
            acionaDisp("SKC");
          }
          if (strncmp("GET /SKD", request, 8) == 0) {
            acionaDisp("SKD");
          }
          if (strncmp("GET /SKE", request, 8) == 0) {
            acionaDisp("SKE");
          }
          if (strncmp("GET /SKF", request, 8) == 0) {
            acionaDisp("SKF");
          }
          if (strncmp("GET /SKG", request, 8) == 0) {
            acionaDisp("SKG");
          }
          
          if(debug_on & show_headers)Serial.print(http_req);
          http_req = ""; // Limpa string após requisição finalizada
          break;
        }
        // Toda linha de texto recebida do cliente terminar com \r\n
        if (c == '\n') {
          // Último caractere da linha do texto recebido
          // Começando nova linha com próximo caractere a ser lido
          currentLineIsBlank = true;
        } 
        else if (c != '\r') {
          // Um caractere foi recebido do cliente
          currentLineIsBlank = false;
        }
      }
      delay(1);
      client.stop();
    } 
  }
  
  else{
    if(client.connected()){
      digitalWrite(pind[32], LOW);
      net = true;
      
      if(inicio == 2){
        if(net)acionaDisp("S*"); 
        inicio = 3;
      }
      
      webSocketClient.dispatchEvents();
      
      delay(1);
    }
    else{
      if(debug_on && net) Serial.println("Conexao websocket fechada.");
      digitalWrite(pind[32], HIGH);
      net = false;
      inicio = 2;
      client.stop();
    }
  }
}
