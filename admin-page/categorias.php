<?php
 global $ldagenda;
$categorias = $ldagenda->getCategorias();

 ?>

  <div class="tab-content">
      <div class="tab tab-pane in active"  id="div_categorias"> 

      <h2>Categorias</h2>
      
      <div class="input-group input-group-sm">
         <div class="input-group-addon btn btn-primary" onclick="prepara_novo_categoria()"  data-toggle="tab" href="#formulario_novo_categoria">Novo<span class="glyphicon glyphicon-plus" aria-hidden="true"></span></div>
         <input type="text" class="search form-control noprint" placeholder="Filtro de pesquisa" target="#categorias_table" />
      </div>      
      
      <table id="categorias_table" class="table header-fixed table-striped">
         <thead><tr>
            <th>#</th>
            <th>Nome</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></thead>
<tfoot><tr>
            <th>#</th>
            <th>Nome</th>
            <th><span class="glyphicon glyphicon-wrench"></span></th>
         </tr></tfoot>
         <tbody>
			<?php
		 foreach($categorias as $prof):
				 echo '
				 <tr id="categoria_row_'.$prof->id.'">
                    <td>'.$prof->id.'</td>
                    <td>'.$prof->nome.'</td>
					<td><div class=""><span data-toggle="tab" href="#formulario_novo_categoria" onclick="return alterar_categoria('.$prof->id.')" 
					class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_categoria('.$prof->id.')" 
					class="glyphicon glyphicon-trash"></span></div></td></td>
                 </tr>';
				 
			endforeach;
			?>
		 
         </tbody>      
      </table>
      </div> 
<!-- formulario de novo servico	-->
      <div id="formulario_novo_categoria" onsubmit="return novo_categoria()" class="tab tab-pane">
         <h3>Categoria</h3>
         
         <form class="form-horizontal">
            <div class="form-group">
               <label class="col-md-4 col-md-offset-4" for="nome_categoria"><div>Nome</div>
                  <div>
                     <input type="text" id="nome_categoria" class="form-control input-md">
                  </div>
               </label></div>

            <div class="col-md-2 col-md-offset-6">
               <div><br/></div>
               <button type="button" data-toggle="tab" href="#div_categorias" class="btn btn-default">Voltar</button>
               <input type="submit" id="categoria_submit" class="btn btn-primary" value="Enviar" />
               <input type="hidden" id="categoria_id"  value="0" />
            </div> 
         </form>  
      </div>      
	</div>
   <script>
	  function novo_categoria(){
	 // validar dados
	 var nome=$("#nome_categoria").val()
	 var id=$("#categoria_id").val()
	 var dados=(id==0)?
		({"action":"novo_categoria","categoria_nome":nome}):
		({"action":"editar_categoria_id","categoria_nome":nome,"id":id})
	 if(nome.lenght<=0) return false;
	 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",	
		 method:"GET",
		 data:dados,
		 success:function(a,b,c,d){
			 console.log(a);
			 if($("#categoria_id").val()==0){
				insere_categorias_table(a)			 //inclui na table)
				$("#nome_categoria").val("").focus();	
				mensagem("linha inserida")
			 } else{
				  edita_categorias_table(a)			 //inclui na table)
				mensagem("Modificado")
			 }
		 }
	 })
	 return false;
 }
 function insere_categorias_table(row){
		   var linha = $("<tr/>").attr("id","categoria_row_"+row.id)
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
                     $("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_categoria" onclick="return alterar_categoria('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_categoria('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
                     linha.appendTo("#categorias_table>tbody")
	  }
	  function edita_categorias_table(row){
		   var linha = $("#categoria_row_"+row.id).html("");
                     $("<td/>").text(row.id).appendTo(linha);
                     $("<td/>").text(row.nome).appendTo(linha);
					 $("<td/>").html('<div class=""><span data-toggle="tab" href="#formulario_novo_categoria" onclick="return alterar_categoria('+row.id+')" class="glyphicon glyphicon-edit" aria-hidden="true"></span> | <span onclick="deletar_categoria('+row.id+')" class="glyphicon glyphicon-trash"></span></div></td>').appendTo(linha);
				   }
	   function remove_categorias_table(row){
		   $("#categoria_row_"+row.id).remove();
        }
	function prepara_novo_categoria(){
		$("#categoria_id").val(0)
		$("#categoria_submit").val("Cadastrar")
		$("#nome_categoria").val("").focus();
		return true;
	}
	function alterar_categoria(id_categoria){
		$("#categoria_id").val(id_categoria);
		$("#categoria_submit").val("Salvar");
		$("#nome_categoria").val("Aguarde...");	
	
		$.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"consulta_categoria_id","id":id_categoria},
		 success:function(a){
			 console.log(a);
				$("#nome_categoria").val(a.nome).focus();	
			 //retorna para a table
		 }
	 })
		return true;
	}
	
	function deletar_categoria(id_categoria){
		
		 $.ajax({
		 url:"<?php echo LD_AGENDA_SITE_REQUEST;?>",
		 method:"GET",
		 data:{"action":"deletar_categoria_id","id":id_categoria},
		 success:function(a){
			 remove_categorias_table(a)
			 //retorna para a table
		 }
	 })
	}
		 </script>