<?php
	error_reporting(0);
	session_start();
	if($_SESSION['permissao_user'] >= 1){
?>
<?php //echo"<script src=\"assets/js/conn.js.php\"></script>"; ?>
<?php /*
<script type="text/javascript" src="assets/js/temp-graph.js.php"></script>
<div class="thermometer" style="display:none;" data-orientation="vertical" data-speed="fast" data-animate="false" data-percent=""></div>
<div class="demo-container" style="display:none;">
	<div id="placeholder" class="demo-placeholder"></div>
</div>
*/ ?>
<script>
	$(document).ready(function() {
		$('.device').tooltip({
			show:{
				effect: "fade",
				delay: 2000
			},
			hide:{
				effect: "fade",
				delay: 750
			}
		});
	});
</script>
<div class="item">
	<div class="content">
		<div class="ground-loading">
			<img class="img-responsive" src="assets/images/loading-icon.gif" />
			<div id="ground">
				<div id="air-control">
					<ul>
						<p class="tag" style="font-size:30px;margin: 20px 0px 10px -15px;">AR</p>
						<li><button class="btn btn-default" value="SI2" onclick="wsToggle(this);">Ligar</button></li>
						<li><button class="btn btn-default" value="SI3" onclick="wsToggle(this);">Desligar</button></li>
						<li><button class="btn btn-default" value="SK#18" onclick="wsToggle(this);">Esfriar</button></li>
						<li><button class="btn btn-default" value="SK#27" onclick="wsToggle(this);">Esquentar</button></li>
					</ul>
				</div>
				
				<?php
					$navWidth  = $_SESSION['screen_width'];
					$navHeight = $_SESSION['screen_height'];

					mysql_select_db($basedados, $connect);
					$sql = "SELECT * FROM tbl_devices WHERE device_type!=3 ORDER BY device_id ASC";
					$listagem = mysql_query($sql) or die ('Erro ao carregar dispositivos.');

					while ($dev = mysql_fetch_array($listagem, MYSQL_ASSOC)){
						if($dev['device_type'] == 1){
							$deviceType = 'lamp';
							$pos_x_px = ($dev['device_pos_x']/100)  * $navWidth;
							$pos_x = $pos_x_px / $navWidth  * 100;
							$pos_x = intval($pos_x_px);

							$pos_y_px = ($dev['device_pos_y']/100)  * $navHeight;
							$pos_y = ($pos_y_px) / $navHeight * 100;
							$pos_y = intval($pos_y_px);
						}
						if($dev['device_type'] == 2){
							$deviceType = 'port';
							$pos_x_px = ($dev['device_pos_x']/100)  * $navWidth;
							$pos_x_pct = ($pos_x_px / $navWidth)  * 100;
							$pos_x_pct = intval($pos_x_px);

							$pos_y_px = ($dev['device_pos_y']/100)  * $navHeight;
							$pos_y_pct = ($pos_y_px / $navHeight) * 100;
							$pos_y_pct = intval($pos_y_px);
						} 
						if($dev['device_type'] == 3) $deviceType = 'airc';

						echo"
							<button type=\"submit\" title=\"{$dev['device_name']}\" style=\"left: {$pos_x_px}px; top: {$pos_y_px}px;\" data-device-id=\"{$dev['device_id']}\" class=\"device {$deviceType}\" id=\"{$deviceType}{$dev['device_order']}\" value=\"{$dev['device_cmd_toggle']}\" onclick=\" 
						";
						if($dev['device_type'] == 2) echo "if(confirm('Tem certeza que quer abrir esta porta?')) ";

						echo "wsToggle(this);\"></button>";

					}
					//echo "<button title=\"\" style=\"font-size:23px;background:none!important;border:none!important;color:rgba(255,35,15,0.99)!important;left:70%\" data-need=\"99\" class=\"temp\" id=\"temp1\">&nbsp;</button>";
				?>
			</div>
		</div>
	</div>
</div>
<?php /*<ul class="nav navbar-nav">
<li><button class="botao-modo" id="botao-modo4" onclick="ativaBotao(this)" value="SM1">Acordar</button></li>
<li><button class="botao-modo" id="botao-modo5" onclick="ativaBotao(this)" value="SM01">Dormir</button></li>
<li><button class="botao-modo" id="botao-modo1" onclick="ativaBotao(this)" value="SM001">Reunião</button></li>
<li><button class="botao-modo" id="botao-modo2" onclick="ativaBotao(this)" value="SM0001">Cinema</button></li>
<li><button class="botao-modo" id="botao-modo3" onclick="ativaBotao(this)" value="SM00001">Leitura</button></li>
</ul>*/ ?>
<div class="col-xs-6 col-centered col-min"><div class="item"><div class="content"></div>&nbsp;</div></div>
<script>
	largura = $(window).width();
	altura  = $(window).height();

	$(window).on("resize", function(){
		largura = $(window).width();
		altura  = $(window).height();
	}).resize();

	$(function() {
	  	$("#ground button").button().draggable({ 
		  	cancel: false,
		  	containment: "body",
		    distance: 15,
		    stack: "#ground button",
			stop: function(event, ui) {
				var pos_x = (ui.offset.left / largura) * 100;
				var pos_y = (ui.offset.top / altura) * 100;
				var device_id  = ui.helper.data("device-id");

				// console.log(pos_x + '%');
				// console.log(pos_y + '%');
				// console.log(device_id);

				$.ajax({
					type: "GET",
					url: "php/posicao-botao.php",
					data: {pos_x: pos_x, pos_y: pos_y, resWidth: largura,resHeight: altura, device_id: device_id}
				}).done(function(msg) {altura
					// console.log(device_id + ', ' + pos_y + ', ' + pos_x + ', ' + largura + ', ' + altura);
				}); 
			}
		});
	});
</script>
<?php } ?>