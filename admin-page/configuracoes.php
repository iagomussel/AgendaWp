   <!--tab de servicos-->
   <?php
   $op = get_option("LDAgenda");
   $weekdays = $op["dias_funcionamento"];
   ?>
   <div class="tab-content">
      <div class="tab tab-pane in active" id="config_tab">
         <h2>Configurações</h2>
         <form class="form-horinzantal" onsubmit="return salva_config()">
            <fieldset>
               <legend>Funcionamento</legend>
                  <div class="form-group">
                  <h4>Dias da semana</h4>
                  <div class="btn-group" id="dias_func" data-toggle="buttons">
                 <label class="btn btn-default <?php echo ($weekdays[0])?"active":""; ?>" >
                    <input name="dias" value="off" <?php echo ($weekdays[0])?"checked='checked'":""; ?> type="checkbox">Domingo 
                 </label>
                 <label class="btn btn-default <?php echo ($weekdays[1])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[1])?"checked='checked'":""; ?> type="checkbox">Segunda
                 </label>
                 <label class="btn btn-default <?php echo ($weekdays[2])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[2])?"checked='checked'":""; ?> type="checkbox">Terça   
                 </label>
                 <label class="btn btn-default <?php echo ($weekdays[3])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[3])?"checked='checked'":""; ?> type="checkbox">Quarta   
                   </label>
                 <label class="btn btn-default <?php echo ($weekdays[4])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[4])?"checked='checked'":""; ?> type="checkbox">Quinta   
                  </label>
                 <label class="btn btn-default <?php echo ($weekdays[5])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[5])?"checked='checked'":""; ?> type="checkbox">Sexta  
                 </label>
                 <label class="btn btn-default <?php echo ($weekdays[6])?"active":""; ?>">
                    <input name="dias" value="off" <?php echo ($weekdays[6])?"checked='checked'":""; ?> type="checkbox">Sábado   
                 </label>
              </div>
              </div>
              <h4>Horários</h4>
               <div class="form-group row">
                  <div class="col-xs-2">
                     <input type="text" class="form-control" id="hora_abre" placeholder="De" value="<?php echo $op["horario_funcionamento"]["abre"];?>">
                  </div>
                  <div class="col-xs-2">
                      <input type="text" class="form-control" id="hora_fecha" placeholder="Até" value="<?php echo $op["horario_funcionamento"]["fecha"];?>">
                  </div>
               </div>
               
               <h4>Intervalo minimo de serviço (<small> em minutos </small>)</h4>
               <div class="form-group row">
                  <div class="col-xs-2">
                     <input type="text" class="form-control" placeholder="" id="serv_interval" value="<?php echo $op["intervalo_minimo"];?>">
                  </div>
               </div>
				<h4>Numero maximo de agendamentos por cliente</h4>
               <div class="form-group row">
                  <div class="col-xs-2">
                     <input type="text" class="form-control" placeholder="Exemplo: 3" id="max_agend" value="<?php echo $op["max_agendamentos"];?>">
                  </div>
               </div>
               <!-- Button trigger modal -->
               <button onclick="return prepara_ind_dates()" type="button" class="btn btn-primary" data-toggle="tab" href="#datas_indisponiveis">Configura Datas Indisponiveis</button>
				<input type="submit" type="button" class="btn btn-primary" value="Salvar">
            <!-- tab -->
            
            </fieldset>
         </form>
   </div>
   <div class="tab tab-pane" id="datas_indisponiveis" >
              <form onsubmit="return  salvar_ind_dates()">
				<h4 class="modal-title" >Calendario</h4>
				<div id="div_datas_indisponiveis">
					<input type="text" id="field_data_ind">
				</div>
                
                    <button type="button" data-toggle="tab" href="#config_tab" class="btn btn-secondary" data-dismiss="modal">Voltar</button>
                    <input type="submit" type="button" class="btn btn-primary" value="Salvar">
                  </form>
               
   </div>
   </div>
   <script>
   
	   function prepara_ind_dates(){
		   $.ajax({
			 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
			 method:"GET",
			 data:{"action":"consultar_datas_indisponiveis"},
			 success:function(a,b,c,d){
				console.log(a);
				$("#field_data_ind").val(a.join(","));
				$("#div_datas_indisponiveis").datepicker({startDate:"+1d",multidate:true})
			}
		   })    
	   }
	   function salvar_ind_dates(){
		   var dates = $("#field_data_ind").val()
		   $.ajax({
			 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
			 method:"GET",
			 data:{"action":"salvar_datas_indisponiveis","datas":dates},
			 success:function(a,b,c,d){
				console.log(a);
			}
		   })
			return false		   
	   }
	   function salva_config(){
		   var dias_func = [];
		   $("#dias_func [type=checkbox]").each(function(a,b){
			   dias_func.push(($(b).attr("checked")==undefined)?0:1)
		   })
		   
		   var hora_abre = $("#hora_abre").val()
		   var hora_fecha = $("#hora_fecha").val()
		   var serv_interval = $("#serv_interval").val()
		   var max_agend = $("#max_agend").val()
		    $.ajax({
			 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
			 method:"GET",
			 data:{"action":"salvar_configuracoes","week":dias_func.join(","),"abre":hora_abre,"fecha":hora_fecha,"intervalo":serv_interval,"max_agendamentos":max_agend},
			 success:function(a,b,c,d){
				console.log(a);
				mensagem("atualizado")
			}
		   })
		   return false;
	   }
   </script>