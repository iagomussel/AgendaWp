<script>
$ = jQuery;
</script>
<style>
.datepicker table tr td.disabled, .datepicker table tr td.disabled:hover {
	color:#eee;
}
.datepicker table tr td.disabled, .datepicker table tr td.disabled:hover {
	border-radius:50%;
}
@media print {
	
  a[href]:after {
    content: "" !important;
  }
  .noprint{
	display:none;
}

}
</style>
<form id="formulario_agendamento" class="horizontal-form col-md-12 col-xs-12">
						<div class="row">
							
							<div class="form-group col-xs-12 col-md-offset-2 col-md-8 ">
								<label for="servico"> Servico:
								</label>
									<select id="servico" class="form-control"  onchange="carrega_profissional(this.value)">
										
									</select>
							</div>
						</DIV>
						<div class="row">
							<div id="div_profissional" class="form-group col-xs-12 col-md-offset-2 col-md-8 fade">
								<label for="profissional"> Profissional:
								</label>
									<select id="profissional" class="form-control" value="0" onchange="carrega_datas_disponiveis($('#servico').val(),this.value)">
										
									</select>
							</div>
							
						</div>
						<div class="row">
							<div id="div_data" class="form-group col-xs-12 col-md-6">
								<input type="hidden" id="data_agendamento" name="data_agendamento" onchange="carrega_horarios($('#servico').val(),$('#profissional').val(),this.value)">
							</div>
							<div id="div_hora" class="form-group col-md-offset-1 col-xs-12 col-md-5 fade">
								<div  rule="buttom">
								<label>Horario
									<select id="horario_agendamento" name="horario_agendamento" id="horario_agendamento" onchange="verifica_dados()" class="form-control" type="text" >
									</select>
								</label>

								</div>
							</div>
						</div>
						<div class="row">
							<div id="btn_agendar" onclick="enviar_dados_agendamento()" class="form-group col-xs-6 col-xs-offset-6 col-md-4 col-md-offset-8 btn btn-primary disabled"  rule="buttom">
									<span class="glyphicon glyphicon-calendar"></span> Agendar
							</div>
						</div>
</form>
<div id="mensagem_sucesso" class="row" style="display:none">
	<div class="text-center col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3" >
	<h3> Sucesso</h3>
	<h5><small> Codigo: <code id="success_code">__code__</code></small></h5>
	<p> O agendamento foi concluido.<br /> você esta agendado para o dia <strong id="success_day">__day__,</strong> ás <strong id="success_time">__time__</strong> para realizar <strong id="success_service">__service__</strong> com <strong id="success_profissional">__profissional__</strong></strong></p>
	<p>Obs.: Caso seja necessario cancelar o agendamento, favor entrar em contato, se possivel, com 24h de antescedência pelo telefone __telefone__ ou pelo site no painel de gerenciamento, Grato.</p>
	</div>
	<div class="noprint col-xs-12 col-sm-12 col-md-12 col-lg-12"><a href="#" onclick="location.reload()" class="btn btn-primary">Novo agendamento</a>
	<a href="#" onclick="print()" class="btn btn-secundary">Imprimir Comprovante </a></div>
</div>	

       <script>

		               $=jQuery;
            Number.prototype.formatMoney = function(c, d, t){
			var n = this, 
				c = isNaN(c = Math.abs(c)) ? 2 : c, 
				d = d == undefined ? "." : d, 
				t = t == undefined ? "," : t, 
				s = n < 0 ? "-" : "", 
				i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
				j = (j = i.length) > 3 ? j % 3 : 0;
			   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
			 };
	   function mensagem(a){
		console.log(a);
	   }
	   impede_envio = true;
	   function enviar_dados_agendamento(){
			verifica_dados()
			if(impede_envio)return mensagem("Um ou mais campos não foram preenchidos corretamente, por favor verifique");
			$.ajax({
				url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
				method:"GET",
				success:function(a){
					if(a.erro == undefined){
						$("#success_day").text(a.data);
						$("#success_code").text(a.id);
						$("#success_time").text(a.hora);
						$("#success_profissional").text(a.profissional);
						$("#success_service").text(a.servico);
						$("#formulario_agendamento").css("display","none");;
						$("#mensagem_sucesso").css("display","block");	
					}
				},
				data:{
					"action":"agendar",
					"cod_servico":$("#servico").val(),
					"cod_profissional":$("#profissional").val(),
					"data_agendamento":$("#data_agendamento").val(),
					"horario_agendamento":$("#horario_agendamento").val()}
			})
	   }
	   function verifica_dados(){
				ret=true;
				if($("#servico").val()==0)ret=false;
				if($("#profissional").val()==0)ret=false;
				if($("#data_agendamento").val()==0)ret=false;
				if($("#horario_agendamento").val()==0)ret=false;
				if(ret){
						$("#btn_agendar").removeClass("disabled").focus()
						impede_envio = false;
					} else {
						$("#btn_agendar").addClass("disabled")
						impede_envio = true;
					}
	   }
	   function carrega_profissional(cod_servico){
			if(cod_servico=='0'){return false}
			$("#div_profissional").removeClass("fade")
						$("#profissional").val(0).html("<option>Carregando...</option>");
						$("#div_data").addClass("fade")
						$("#data_agendamento").val("");
						$("#div_hora").addClass("fade")
						$("#horario_agendamento").val(0);
			$.ajax({
				url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
				method:"GET",
				success:function(a){
					if(a.length!=undefined){
						if(a.length>0){
							$("#profissional").html("")
							$("<option/>").attr("value",0).text("-- Nada Selecionado --").appendTo("#profissional")
							for (b in a){
								$("<option/>").attr("value",a[b].id_profissional).text(""+a[b].nome).appendTo("#profissional")
							}
						} else {
							$("#profissional").html("<option>Lamentamos. Nenhum profissional disponivel...</option>")
						}
						
					} else {
						mensagem("estamos com problemas no servidor")
					}
					verifica_dados()
				},
				data:{"action":"consultar_profissionais","cod_servico":cod_servico,"filter":"por_servico"}
			})
	   }
	   
	   function carrega_datas_disponiveis(cod_servico,cod_profissional){
			if(cod_servico=='0'){return false}
			if(cod_profissional=='0'){return false}
			$("#div_data").removeClass("fade")
			$("#data_agendamento").val("");
			$("#horario_agendamento").val(0);
			$("#div_hora").addClass("fade")
			$.ajax({
				url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
				method:"GET",
				success:function(a){
					$("#div_data").datepicker("remove");
					var op = a;
					op.startDate="+1d";
					$("#div_data").datepicker(op);
					verifica_dados()
				},
				data:{"action":"consultar_datas","filter":"por_profissional","cod_servico":cod_servico,"cod_profissional":cod_profissional}
			})
			
	   }
	   function carrega_horarios(cod_servico,cod_profissional,data_agendamento){
			//faz requisição ajax para saber os horarios ocupados
			if(cod_servico=='0'){return false}
			if(cod_profissional=='0'){return false}
			if(data_agendamento==undefined){return false}
			$("#horario_agendamento").val(0).html("<option>Carregando...</option>");
			$("#div_hora").removeClass("fade")
			$.ajax({
				url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
				method:"GET",
				success:function(a){
					if(a.length>0){
						
						$("#horario_agendamento").html("")
						$("<option/>").attr("value",0).text("-- Nada Selecionado --").appendTo("#horario_agendamento")
						for (b in a){
							$("<option/>").attr("value",a[b].id).text(""+a[b].text).appendTo("#horario_agendamento")
						}
					} else {
						$("#horario_agendamento").html("<option>Lamentamos. Nenhum horario disponivel...</option>")
					}
					verifica_dados()
				},
				data:{"action":"consultar_horas","consulta":"por_data","cod_servico":cod_servico,"cod_profissional":cod_profissional,"data_agendamento":data_agendamento}
			})
			//tras tambem o horario de funcionamento e o tempo instimado deste servico
	   }
	   $(document).ready(function(){
			
			// carregar servicos
			$("#servico").html("<option>Carregando...</option>")
			$.ajax({
				url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
				method:"GET",
				success:function(a){
					if(a.length){
						$("#servico").html("")
						$("<option/>").attr("value",0).text("-- Nada Selecionado --").appendTo("#servico")
						for (b in a){
							var cat = (a[b].categoria).replace(/[ @!#$%¨&*()]/g,"_")
							console.log(cat)
							if($("#servico ."+cat).length==0){
								$("<optgroup/>").addClass(cat).attr("label",a[b].categoria).appendTo("#servico");
							}
							$("<option/>").attr("value",a[b].id).text(""+a[b].nome+" - "+(parseFloat(a[b].valor)).formatMoney(2,",",".")).appendTo("#servico ."+cat)
						}
					} else {
						$("#servico").html("")
						$("<option/>").attr("value",0).text("Nenhum serviço foi cadastrado.").appendTo("#servico")
						mensagem("estamos com problemas no servidor!")
					}
				},
				data:{"action":"consultar_servicos"}
			})			
	   })
	   
	   </script>
