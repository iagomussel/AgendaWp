<?php
global $ldagenda,$wpdb;

$categorias = $ldagenda->getCategorias();
$servicos = $wpdb->get_results("SELECT `wp_ag_servicos`.id,
										`wp_ag_servicos`.nome,
										`wp_ag_servicos`.valor,
										`wp_ag_servicos`.tempo,
										`wp_ag_categorias_servicos`.nome as categoria
										FROM `wp_ag_servicos` left join `wp_ag_categorias_servicos` on `wp_ag_categorias_servicos`.id = `wp_ag_servicos`.categoria");
$tempos_servico = $ldagenda->consultar_tempos_servico();

?>

  <div class="tab-content">
      <div class="tab tab-pane in active"  id="div_servicos"> 

      <h2>Serviços</h2>
      
      <div class="input-group input-group-sm">
         <div class="input-group-addon btn btn-primary" onclick="prepara_novo_servico()"  data-toggle="tab" href="#formulario_novo_servico">Novo<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></div>
         <input type="text" class="search form-control noprint" placeholder="Filtro de pesquisa" target="#servicos_table" />
      </div>      
      
      <table id="servicos_table" class="table header-fixed table-striped">
         <thead><tr>
            <th>#</th>
            <th>Serviço</th>
            <th>Categoria</th>
            <th>Tempo</th>
            <th>Valor</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></thead>
<tfoot><tr>
            <th>#</th>
            <th>Serviço</th>
            <th>Categoria</th>
            <th>Tempo</th>
            <th>Valor</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></tfoot>
         <tbody>
<?php
		 foreach($servicos as $prof):
				 echo '
				 <tr id="servico_row_'.$prof->id.'">
                    <td>'.$prof->id.'</td>
                    <td>'.$prof->nome.'</td>
                    <td>'.$prof->categoria.'</td>
                    <td>'.$tempos_servico[$prof->tempo].'</td>
                    <td>'.sprintf("R$. %.2f",($prof->valor)).'</td>
					<td><div class=""><span data-toggle="tab" href="#formulario_novo_servico" onclick="return alterar_servico('.$prof->id.')" 
					class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_servico('.$prof->id.')" 
					class="glyphicon glyphicon-trash"></span></div></td></td>
                 </tr>';			 
			endforeach;
			?>
		 
         </tbody>      
      </table>
      </div> 
<!-- formulario de novo servico	-->
      <div id="formulario_novo_servico" onsubmit="return novo_servico()" class="tab tab-pane">
         <h3>Serviço</h3>
         
         <form class="form-horizontal">
            <div class="form-group">
               <label class="col-md-4 col-md-offset-4" id="sevico_nome"><div>Nome</div>
                  <div>
                     <input type="text" id="nome_servico" class="form-control input-md">
                  </div>
               </label></div>

            <div class="form-group">
               <label class="col-md-4 col-md-offset-4" id="servico_categoria"><div>Categoria</div>
                  <div>
                     <select id="categoria_servico" class="form-control">
					 
					 <option value="0"> Nada Selecionado </option>
					 <?php foreach($categorias as $cat):?>
						<option value="<?php echo $cat->id;?>"><?php echo $cat->nome;?></option>
					 <?php endforeach;?>
					 </select>
               </div></label>
            </div>
			
            <div class="form-group">
               <label class="col-md-4 col-md-offset-4" for="servico_tempo"><div>Tempo do Serviço</div>
                  <div>
                     <select id="tempo_servico" class="form-control">
					 
					 <?php foreach($tempos_servico as $ch=>$tempo):?>
						<option value="<?php echo $ch;?>"><?php echo $tempo;?></option>
					 <?php endforeach;?>
		
					 </select>
               </div></label>
            </div>
			
            <div class="form-group">
               <label class="col-md-4 col-md-offset-4" for="servico_valor"><div>Valor</div>
                  <div>   
                     <input type="text" id	="valor_servico" class="form-control">
               </div></label>
            </div> 

            <div class="col-md-2 col-md-offset-6">
               <div><br/></div>
               <button type="button" data-toggle="tab" href="#div_servicos" class="btn btn-default">Voltar</button>
               <input type="submit" id="servico_submit" class="btn btn-primary" value="Enviar" />
               <input type="hidden" id="servico_id"  value="0" />
            </div> 
         </form>  
      </div>      
	</div>
   <script>
   /*carregar servicos*/
function tempos_de_servico(a){
	<?php foreach($tempos_servico as $ch=>$tempo){
		echo "\n".'if(a=='.$ch.') return "'.$tempo.'";';
	}?>
}

function categorias_de_servico(a){
	<?php foreach($categorias as $cat){
		echo "\n".'if(a=='.$cat->id.') return "'.$cat->nome.'";';
	}?>
}
   
	  function novo_servico(){
	 // validar dados
	 var nome=$("#nome_servico").val()
	 var id=$("#servico_id").val()
	 var tempo=$("#tempo_servico").val()
	 var categoria=$("#categoria_servico").val()
	 var valor=$("#valor_servico").val().replace(",",".")
	 var dados=(id==0)?
		({"action":"novo_servico","servico_nome":nome,"servico_categoria":categoria,"servico_tempo":tempo,"servico_valor":valor}):
		({"action":"editar_servico_id","servico_nome":nome,"servico_tempo":tempo,"servico_categoria":categoria,"servico_valor":valor,"id":id})
	 if(nome.lenght<=0) return false;
	 if(tempo.lenght<=0) return false;
	 if(categoria.lenght<=0) return false;
	 if(valor.lenght<=0) return false;
	 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:dados,
		 success:function(a,b,c,d){
			  console.log($("#servico_id").val())
			 if($("#servico_id").val()==0){
				insere_servicos_table(a)			 //inclui na table)
				$("#tempo_servico").val(1)
				$("#categoria_servico").val(0)
				$("#valor_servico").val("")
				$("#nome_servico").val("").focus();	
				mensagem("linha inserida")
			 } else{
				  edita_servicos_table(a)			 //inclui na table)
				mensagem("Modificado")
			 }
		 }
	 })
	 return false;
 }
 function insere_servicos_table(row){
		   var linha = $("<tr/>").attr("id","servico_row_"+row.id)
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
                     $("<td/>").text(categorias_de_servico(row.categoria)).appendTo(linha);
                     $("<td/>").text(tempos_de_servico(row.tempo)).appendTo(linha);
                     $("<td/>").text("R$. "+(parseFloat(row.valor)).formatMoney(2,',','.')).appendTo(linha);
                     $("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_servico" onclick="return alterar_servico('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_servico('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
                     linha.appendTo("#servicos_table>tbody")
	  }
	  function edita_servicos_table(row){
		   var linha = $("#servico_row_"+row.id).html("");
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
                     $("<td/>").text(categorias_de_servico(row.categoria)).appendTo(linha);
					 $("<td/>").text(tempos_de_servico(row.tempo)).appendTo(linha);
                     $("<td/>").text("R$. "+(parseFloat(row.valor)).formatMoney(2,',','.')).appendTo(linha);
                     $("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_servico" onclick="return alterar_servico('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_servico('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
                   }
	   function remove_servicos_table(row){
		   $("#servico_row_"+row.id).remove();
        }
	function prepara_novo_servico(){
		$("#servico_id").val(0)
		$("#valor_servico").val("")
		$("#tempo_servico").val(1)
		$("#categoria_servico").val(0)
		$("#servico_submit").val("Cadastrar")
		$("#nome_servico").val("").focus();
		return true;
	}
	function alterar_servico(id_servico){
		$("#servico_id").val(id_servico);
		$("#servico_submit").val("Salvar");
		$.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"consulta_servico_id","id":id_servico},
		 success:function(a){
				$("#tempo_servico").val(a.tempo)
				$("#valor_servico").val(a.valor)
				$("#categoria_servico").val(a.categoria)
				$("#nome_servico").val(a.nome).focus();	
			 //retorna para a table
		 }
	 })
		return true;
	}
	
	function deletar_servico(id_servico){
		
		 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"deletar_servico_id","id":id_servico},
		 success:function(a){
			 console.log(a);
			 remove_servicos_table(a)
			 //retorna para a table
		 }
	 })
	}
		 </script>