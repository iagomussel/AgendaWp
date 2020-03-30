<?php
 global $wpdb;
$servicos = $wpdb->get_results("SELECT * FROM `wp_ag_servicos` ");
$profissionais = $wpdb->get_results("SELECT * FROM `wp_ag_profissionais` ");
 ?>
   <!--tab de profissionals-->
   <div class="tab-content">
      <!--profissionais-->
      <div class="tab tab-pane in active" id="div_profissional">
         <h2>Profissionais</h2>
      
      <div class="input-group input-group-sm">
         <div data-toggle="tab" href="#formulario_novo_profissional" class="input-group-addon btn btn-primary nova_os_form">Novo<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></div>
         <input type="text" class="search form-control noprint" placeholder="Filtro de pesquisa" target="#tabela_profissionais" />
      </div>
           
      <table id="profissionais_table" class="table  header-fixed table-striped">
         <thead><tr>
            <th>#</th>
            <th>Profissionais</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></thead>
         <tfoot><tr>
            <th>#</th>
            <th>Profissionais</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></tfoot>
         
         <tbody>
		 <?php
		 foreach($profissionais as $prof):
				 echo '
				 <tr id="profissional_row_'.$prof->id.'">
                    <td>'.$prof->id.'</td>
                    <td>'.$prof->nome.'</td>
					<td><div class=""><span data-toggle="tab" href="#formulario_novo_profissional" onclick="return alterar_profissional('.$prof->id.')" 
					class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_profissional('.$prof->id.')" 
					class="glyphicon glyphicon-trash"></span></div></td></td>
                 </tr>';
				 
			endforeach;
			?>
		 
		 </tbody>
      </table>      
      </div>   
		
<!-- formulario de novo servico	-->
      <div id="formulario_novo_profissional" onsubmit="return novo_profissional()" class="tab tab-pane">
         <h3>Profissional</h3>
         
         <form class="form-horizontal">
            <div class="form-group">
               <label class="col-md-4 col-md-offset-4"><div>Nome</div>
                  <div>
                     <input type="text" id="nome_profissional" class="form-control input-md">
                  </div>
               </label></div>
			<label class="col-md-4 col-md-offset-4">Capacitação</label>
			<div class="form-group">
			 
			 <div class="btn-group btn-group-vertical col-md-4 col-md-offset-4" style="max-height:200px; overflow-Y:auto" id="servicos" data-toggle="buttons">
			 <?php
                 foreach($servicos as $serv):
				 ?>
				 <label class="btn btn-primary active btn-sm">
                    <input name="servico_<?php echo $serv->id;?>" type="checkbox" checked="checked"><?php echo $serv->nome; ?> 
                 </label>
				 <?php 
				 
					endforeach;
				 ?>
             </div>
             </div>
			<div><br /></div>
            <div class="col-md-2 col-md-offset-6">
               <button type="button" data-toggle="tab" href="#div_profissional" class="btn btn-default">Voltar</button>
               <input type="submit" id="profissional_submit" class="btn btn-primary" value="Enviar" />
               <input type="hidden" id="profissional_id"  value="0" />
            </div> 
         </form>  
      </div>
</div>

	<script>

	  function novo_profissional(){
	 // validar dados
	 var nome=$("#nome_profissional").val()
	 var id=$("#profissional_id").val()
	 var prof_servicos = [];
	 $("#servicos [type=checkbox]:checked").each(function(k,el){prof_servicos.push(el.name.substr(el.name.lastIndexOf("_")+1))})
	 console.log(prof_servicos.join("_"));
	 var dados=(id==0)?
		({"action":"novo_profissional","profissional_nome":nome,"servicos":prof_servicos.join("_")}):
		({"action":"editar_profissional_id","profissional_nome":nome,"id":id,"servicos":prof_servicos.join("_")})
	 if(nome.lenght<=0) return false;
	 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:dados,
		 success:function(a,b,c,d){
			 if($("#profissional_id").val()==0){
				insere_profissionais_table(a)			 //inclui na table)
				$("#nome_profissional").val("").focus();	
				mensagem("linha inserida")
			 } else{
				  edita_profissionais_table(a)			 //inclui na table)
				mensagem("Modificado")
			 }
		 }
	 })
	 return false;
 }
 function insere_profissionais_table(row){
		   var linha = $("<tr/>").attr("id","profissional_row_"+row.id)
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
                     $("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_profissional" onclick="return alterar_profissional('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_profissional('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
                     linha.appendTo("#profissionais_table>tbody")
	  }
	  function edita_profissionais_table(row){
		   var linha = $("#profissional_row_"+row.id).html("");
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
						$("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_profissional" onclick="return alterar_profissional('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_profissional('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
                      }
	   function remove_profissionais_table(row){
		   $("#profissional_row_"+row.id).remove();
        }
	function prepara_novo_profissional(){
		$("#profissional_id").val(0)
		$("#profissional_submit").val("Cadastrar")
		$("#nome_profissional").val("").focus();
		return true;
	}
	function alterar_profissional(id_profissional){
		$("#profissional_id").val(id_profissional);
		$("#profissional_submit").val("Salvar");
		$("#nome_profissional").val("Aguarde...");
		$("#servicos [type=checkbox]").removeAttr("checked").parent().removeClass("active");
		$.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"consulta_profissional_id","id":id_profissional},
		 success:function(a){
				for(serv in a.servicos){
					$("#servicos [name=servico_"+a.servicos[serv]+"]").attr("checked","checked").parent().addClass("active");
				}
				$("#nome_profissional").val(a.nome).focus();	
			 //retorna para a table
		 }
	 })
		return true;
	}
	
	function deletar_profissional(id_profissional){
		 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"deletar_profissional_id","id":id_profissional},
		 success:function(a){
			 remove_profissionais_table(a)
			 //retorna para a table
		 }
	 })
	}
	</script>