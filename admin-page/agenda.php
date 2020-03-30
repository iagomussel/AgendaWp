<?php
global $wpdb,$ldagenda;
$data_inicial = date("Y-m-d",strtotime("-".date("w")." days")); //domingo
$data_final = date("Y-m-d",strtotime("+".(7-((int)date("w")))." days")); //sabado
$agendamentos = $wpdb->get_results($wpdb->prepare("SELECT 
	wp_ag_agendamentos.id,
	wp_ag_agendamentos.data,
	wp_ag_agendamentos.hora_inicio,
	wp_ag_agendamentos.hora_final,
	wp_ag_agendamentos.usuario,
	wp_ag_profissionais.nome as profissional,
	wp_ag_servicos.nome as servico
 FROM wp_ag_agendamentos
  left join wp_ag_profissionais on wp_ag_profissionais.id=wp_ag_agendamentos.profissional
  left join wp_ag_servicos on wp_ag_servicos.id = wp_ag_agendamentos.servico WHERE data >= %s and data <= %s ORDER BY `hora_inicio` ASC",$data_inicial,$data_final));
$horarios = $ldagenda->pegar_horarios_por_dia();
$table = array();
$table_sem = array();
foreach($agendamentos as $ag){
	if(!isset($table_sem[$horarios[$ag->hora_inicio-1]])) $table_sem[$horarios[$ag->hora_inicio-1]]= array(
																								array(),/*dom*/
																								array(),/*seg*/
																								array(),/*ter*/
																								array(),/*qua*/
																								array(),/*qui*/
																								array(),/*sex*/
																								array()/*sab*/
																								);
$table_sem[$horarios[$ag->hora_inicio-1]][date("w",strtotime($ag->data))][] = array("profissional"=>($ag->profissional),"cliente"=>(get_user_meta($ag->usuario)["nickname"][0]),"servico"=>($ag->servico));
	
	if($ag->data == current_time("Y-m-d")){
		if(!isset($table[$horarios[$ag->hora_inicio-1]])) $table[$horarios[$ag->hora_inicio-1]]= array();
		$table[$horarios[$ag->hora_inicio-1]][] = array("profissional"=>($ag->profissional),"cliente"=>(get_user_meta($ag->usuario)["nickname"][0]),"servico"=>($ag->servico));
	}
}
?>

<style>
.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
	vertical-align:middle !important;
}
</style>
<script>
	function update_table_agendamentos(exibe_carg=true){
		data = $("#dia_agendamento").val()
		if(data.lenght==0) return false;
		if(exibe_carg){
			$("#dia>table>tbody").html("<tr><td>Por favor aguarde...</td></tr>")
			$("#semana>table>tbody").html("<tr><td>Por favor aguarde...</td></tr>")
			$(".display_dia").text("Carregando..");
		}
		$.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"consultar_agendamentos","data":data},
		 success:function(a){
				var $table_dia = $("#dia table>tbody");
				var $table_semana = $("#semana table>tbody");
				var $html_table_dia = "";
				var $html_table_semana = "";
				//atualiza a visao dia;
				for (h in a.por_dia){
					$html_table_dia += "<tr>";
					$html_table_dia += "<td>"+h+"</td>";
					$html_table_dia += "<td><ul>";
					for(serv in a.por_dia[h]){
						$html_table_dia +="<li><strong>"+a.por_dia[h][serv].cliente+"</strong>, "+a.por_dia[h][serv].servico;
						$html_table_dia +=" <small>com</small> <span class=\"text-danger\">";
						$html_table_dia +=a.por_dia[h][serv].profissional+"</span></li>";
					}
					$html_table_dia += "</ul></td></tr>";
				}
				
				
				//atualiza a visao semana;
				for (h in a.por_semana){
					$html_table_semana += "<tr>";
					$html_table_semana += "<td>"+h+"</td>";		
					for(dia_semana in a.por_semana[h]){
						$html_table_semana += "<td><ul>";
						for(serv in a.por_semana[h][dia_semana]){
							$html_table_semana +="<li><strong>"+a.por_semana[h][dia_semana][serv].cliente+"</strong>, "+a.por_semana[h][dia_semana][serv].servico;
							$html_table_semana +=" <small>com</small> <span class=\"text-danger\">";
							$html_table_semana +=a.por_semana[h][dia_semana][serv].profissional+"</span></li>";
						}
						$html_table_semana += "</ul></td>";
					}
					$html_table_semana += "</tr>";
				}
				
				$(".display_dia").text(a.data);
				$table_dia.html($html_table_dia);
				$table_semana.html($html_table_semana);
			 //retorna para a table
			}
		 })
		return true;
	}
	</script>
	<!--tab de servicos-->
	<div class="tab-content">
      <!--servicos agendados	-->
		<div class="tab tab-pane in active" id="div_agendamento">
			<h2>Serviços Agendados</h2>
      
      <div class="row tab-content" style="margin: 0 auto;">
         <div class="col-md-3">
		 <small>Modo de exibição:</small>
            <div class="btn-group btn-group-justified"  data-toggle="buttons">
         <div class="btn btn-default active"  data-toggle="tab"  href="#dia" >
         <label ><input  type="radio"  value="dia" checked="checked" name="view_table_hour"/>Dia</label></div>
         

         <div class="btn btn-default" data-toggle="tab" href="#semana">
         <label  ><input type="radio" value="semana"  name="view_table_hour"/>Semana</label></div>
      </div>
      <h2 class="display_dia text-center"><?php echo current_time("d/m/Y");?></h2>
           
            <div data-provide="datepicker-inline">
               <input type="hidden" id="dia_agendamento" onchange="update_table_agendamentos()" id="data_agendamento" value="<?php echo current_time("d/m/Y");?>">
            </div>   
         </div>
         
         <div id="dia" class="col-md-9 tab fade in active tab-pane">
		 <div class="table-responsive">	
            <table class="table table-sprited table-hover header-fixed">
			    <thead>
				   <tr>
					  <th>Horas</th>
					  <th>Serviços</th>
				   </tr>
			   </thead>
				<tfoot>
				   <tr>
					  <th>Horas</th>
					  <th>Serviços</th>
				   </tr>
			   </tfoot>
               <tbody>
				   
						<?php foreach($table as $ch=>$v):?>
						<tr>
						<td><h4><?php echo $ch; ?></h4></td>
						<td><ul>
							<?php foreach($v as $sch=>$sv):?>
								<li><strong><?php echo $sv["cliente"]; ?></strong>,<span class="text-success"> <?php echo $sv["servico"]; ?></span> <small>com</small> <span class="text-danger"><?php echo $sv["profissional"]; ?></span></li>
							<?php endforeach;?>
							</ul>
						</td>
						</tr>
						<?php endforeach;?>
				   
			   </tbody>
            </table>   
         </div>
         </div>
         
         <div id="semana" class="col-md-9 tab fade tab-pane">
			<div class="table-responsive">
            <table class="table table-sprited table-hover header-fixed">
               <thead><tr>
			   
                  <th>Horas</th>
                  <th class="dom">Dom</th>
                  <th class="seg">Seg</th>
                  <th class="ter">Ter</th>
                  <th class="qua">Qua</th>
                  <th class="qui">Qui</th>
                  <th class="sex">Sex</th>
                  <th class="dom">Sab</th>
               </tr></thead>
				<tfoot><tr>
			   
                  <th>Horas</th>
                  <th class="dom">Dom</th>
                  <th class="seg">Seg</th>
                  <th class="ter">Ter</th>
                  <th class="qua">Qua</th>
                  <th class="qui">Qui</th>
                  <th class="sex">Sex</th>
                  <th class="dom">Sab</th>
               </tr></tfoot>
               <tbody>
					<?php foreach($table_sem as $ch=>$v):?>
						<tr>
						<td><h4><?php echo $ch; ?></h4></td>
						<?php foreach($v as $semana_ch=>$semana_sv):?>
						<td> <ul>
							<?php	
							foreach($semana_sv as $sch=>$sv):
								if(!empty($sv)){
							?>
							
								<li><strong><?php echo $sv["cliente"]; ?></strong>, <span class="text-success"><?php echo $sv["servico"]; ?></span> <small>com</small> <span class="text-danger"><?php echo $sv["profissional"]; ?></span></li>
							<?php
								}							
							endforeach;?></ul>
						</td>
						<?php endforeach;?>
						</tr>
					<?php endforeach;?>
			   </tbody>
            </table>
			</div>
         </div>

      </div>
		</div>
     
	</div>
	<script>
	var autoUpdater = setInterval(function(){update_table_agendamentos(false)},180000)
	</script>
	<?php 