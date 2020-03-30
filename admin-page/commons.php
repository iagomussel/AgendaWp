
   <style>
   
 .header-fixed tr {
width: 100%;
display: inline-table;
table-layout: fixed;
}

table.header-fixed {
 height:400px;              // <-- Select the height of the table
 display: -moz-groupbox;    // Firefox Bad Effect
}
.header-fixed  tbody{
  overflow-y: scroll;      
  height: 300px;            //  <-- Select the height of the body
  width: 100%;
  position: absolute;
}
.table>tbody>tr>td,
.table>tbody>tr>th, 
.table>tfoot>tr>td,
.table>tfoot>tr>th,
.table>thead>tr>td,
.table>thead>tr>th {
padding: 1px;
}

.form-group {
    margin-bottom: 5px;
}
.col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-xs-1, .col-xs-10, .col-xs-11, .col-xs-12, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9
{
padding-right: 5px;
padding-left: 5px;
}

.form-control,.input-group-addon {
    height: 28px;
    padding: 2px 12px;
    font-size: 12px;
}
table form-control{
	border:none;
	border-radius:none;
	height: 100%;
	width:100%;
	padding: 0px 0px;
}
   </style>

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
      $(document).ready(function(){
         /* corrige toggle radio button*/
         $("[data-toggle='buttons'] .btn").click(function(){
               if($(this).hasClass("active")){
                  $(this).find("input").attr("checked","false");
               } else{
                 $(this).find("input").attr("checked","true");
            }
         })
         
		 
		  //faz pesquisa em tabelas
  
		 $(".search").keyup(function() {
			var target_id = $(this).attr("target")
			if (target_id == "") {
			  return false
			}
			// When value of the input is not blank
			if ('' != this.value) {
			  var reg = new RegExp(this.value, 'i'); // case-insesitive
			  $(target_id + ' tbody').find('tr').each(function() {
				var $me = $(this);
				if (!$me.children('td').text().match(reg)) {
				  $me.hide();
				} else {
				  $me.show();
				}
			  });
			} else {
			  $(target_id + ' tbody').find('tr').show();
			}
		  })

      })
	  
      function carregar_datas_agendamento(){
           $("#carregando").hide();
		   tipo_view = $('input:radio[name=view_table_hour]:checked').val()
		   console.log(tipo_view)
		   $.ajax({
            url:"<?php echo LD_AGENDA_SITE_REQUEST?>",
            method:"GET",
            success:function(servico){
                  $("#carregando").hide();
                  if(servico.length==0) return false;
                  for(a in servico){
                  //  insere_servicos_table(servico[a])
                  }
                  console.log(a);
            },
            data:{"action":"consultar_datas_agendamento"}
         })
	  }
	  
	function mensagem(a){
		console.log(a)
	}
  </script>
   