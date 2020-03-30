<?php
/*
Plugin Name: LDAgenda
Plugin URI: http://ld.com/agendawp
Description: Cria uma agenda wordpress
Version: 2.0
Author: LanceDesign
Author URI: http://facebook.com/iago.mussel
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2005-2015 Automattic, Inc.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('LD_AGENDA',					true);
define('LD_AGENDA_VERSION',			'2.0');
define('LD_AGENDA_URL',				plugin_dir_url(__FILE__));
define('LD_AGENDA_DIR',				plugin_dir_path(__FILE__));
define('LD_AGENDA_SCRIPTS_DIR',		LD_AGENDA_URL.'js/');
define('LD_AGENDA_STYLES_DIR',		LD_AGENDA_URL.'css/');
define('LD_AGENDA_ICONS_DIR',		LD_AGENDA_URL.'icons/');
define('LD_AGENDA_SITE_REQUEST',	get_site_url().'/wp-admin/admin-post.php');

/**
class LDAgenda
**/


class LDAgenda{
	public static $instance;
	public function __construct(){
		self::init_hooks();
		self::init_shortcodes();
	}	
	
	public static function getInstance(){
		if(!self::$instance){	
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function init(){
		
	}
	
	public function add_admin_menu(){
		 add_menu_page(
        'Agenda WP',
        'Agenda',
        'publish_posts',
        __FILE__,
        array($this,'menu_agenda'),
        plugins_url( LD_AGENDA_ICONS_DIR.'/icon.png' ),
        2
    );
	add_submenu_page( __FILE__, "AgendaWp - Serviços", "Serviços", "publish_posts", __FILE__."_servicos", array($this,"menu_servicos"));
	add_submenu_page( __FILE__, "AgendaWp - Categorias de Serviços", "Categorias", "publish_posts", __FILE__."_servicos_categorias", array($this,"menu_servicos_categorias"));
	add_submenu_page( __FILE__, "AgendaWp - Profissionais", "Profissionais", "publish_posts", __FILE__."_profissionais", array($this,"menu_profissionais"));
	add_submenu_page( __FILE__, "AgendaWp - Configurações","Configurações", "publish_posts", __FILE__."_config", array($this,"menu_configuracoes"));
	}
	
	public function menu_agenda(){
		require_once(LD_AGENDA_DIR."admin-page/commons.php");
		require_once(LD_AGENDA_DIR."admin-page/agenda.php");;
	}

	public function menu_servicos(){
		require_once(LD_AGENDA_DIR."admin-page/commons.php");
		require_once(LD_AGENDA_DIR."admin-page/servicos.php");
		}

	public function menu_servicos_categorias(){
		require_once(LD_AGENDA_DIR."admin-page/commons.php");
		require_once(LD_AGENDA_DIR."admin-page/categorias.php");
	}
	public function menu_profissionais(){
		require_once(LD_AGENDA_DIR."admin-page/commons.php");
		require_once(LD_AGENDA_DIR."admin-page/profissionais.php");}
	public function menu_configuracoes(){
		require_once(LD_AGENDA_DIR."admin-page/commons.php");
		require_once(LD_AGENDA_DIR."admin-page/configuracoes.php");
	}

		// Cria as tabelas do banco ao ativar
	public function activate() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		//tabela agendamentos
		$res = array();
		$res[] = "CREATE TABLE ".$wpdb->prefix."ag_agendamentos (
		id INT(11) NOT NULL AUTO_INCREMENT,
		servico INT(11) NULL DEFAULT NULL,
		profissional INT(11) NULL DEFAULT NULL,
		usuario INT(11) NULL DEFAULT NULL,
		data DATE NULL DEFAULT NULL,
		hora_inicio int(11) NULL DEFAULT NULL,
		hora_final int(11) NULL DEFAULT NULL,
		status INT(11) NULL DEFAULT NULL,
	PRIMARY KEY(`id`)
	)".$wpdb->get_charset_collate();
	//profissionais
	$res[] ="CREATE TABLE ".$wpdb->prefix."ag_profissionais (
	id INT(11) NOT NULL AUTO_INCREMENT,
	data_create INT(11) NOT NULL DEFAULT '0',
	nome VARCHAR(50) NOT NULL,
	PRIMARY KEY(`id`)
	)".$wpdb->get_charset_collate();
	// servicos
	$res[] ="CREATE TABLE ".$wpdb->prefix."ag_servicos (
	id INT(11) NOT NULL AUTO_INCREMENT,
	nome VARCHAR(50) NULL DEFAULT NULL,
	tempo INT(11) NULL DEFAULT NULL,
	categoria INT(11) NULL DEFAULT NULL,
	valor DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY(`id`)
	)".$wpdb->get_charset_collate();

	// categorias de servicos
	$res[] = "CREATE TABLE ".$wpdb->prefix."ag_categorias_servicos (
	id int(11) NOT NULL AUTO_INCREMENT,
	nome varchar(25) NOT NULL,
	PRIMARY KEY(id)
	)".$wpdb->get_charset_collate();

	//profissional_servicos
	$res[] ="CREATE TABLE ".$wpdb->prefix."ag_profissional_servico (
		id_profissional INT(11) NULL DEFAULT NULL,
		id_servico INT(11) NULL DEFAULT NULL
	)".$wpdb->get_charset_collate();

	// status
	$res[] ="CREATE TABLE ".$wpdb->prefix."ag_status (
	id INT(11) NOT NULL AUTO_INCREMENT,
	descricao VARCHAR(50) DEFAULT 0,
	PRIMARY KEY(id)
	) ".$wpdb->get_charset_collate();

	$res[] ="CREATE TABLE ".$wpdb->prefix."ag_datas_indisponiveis (
		data DATE NULL DEFAULT NULL
	) ".$wpdb->get_charset_collate();
	dbDelta($res);
	/* criando as opções de configuração */
	add_option("LDAgenda",array(
							"horario_funcionamento"=>array("abre"=>"07:00","fecha"=>"17:00"),
							"intervalo_minimo"=>30,
							"max_agendamentos"=>3,
							"dias_funcionamento"=>array(0,1,1,1,1,1,0)
							));
							
	}
	public function deactive(){
		global $wpdb;
		$res =$wpdb->query("DROP TABLE  ".$wpdb->prefix."ag_profissional_servico,
										".$wpdb->prefix."ag_servicos,
										".$wpdb->prefix."ag_categorias_servicos,
										".$wpdb->prefix."ag_agendamentos,
										".$wpdb->prefix."ag_profissionais,
										".$wpdb->prefix."ag_datas_indisponiveis,
										".$wpdb->prefix."ag_status;"
										);
		delete_option("LDAgenda");
	}
	public function enqueue_styles(){
		wp_enqueue_style( 'bootstrap',  					LD_AGENDA_STYLES_DIR . 'bootstrap.css');
		wp_enqueue_style( 'bootstrap.datepicker',  			LD_AGENDA_STYLES_DIR . 'bootstrap-datepicker.css');
		wp_enqueue_style( 'bootstrap.datepicker.theme',  	LD_AGENDA_STYLES_DIR . 'bootstrap-datepicker-theme.css');
		wp_enqueue_style( 'bootstrap.select', 				LD_AGENDA_STYLES_DIR . 'bootstrap-select.min.css');
	}
	public function enqueue_scripts(){
		wp_enqueue_script('jquery',							LD_AGENDA_SCRIPTS_DIR . 'jquery.js');
		wp_enqueue_script('bootstrap',						LD_AGENDA_SCRIPTS_DIR . 'bootstrap.min.js');
		wp_enqueue_script('bootstrap-datepicker',			LD_AGENDA_SCRIPTS_DIR . 'bootstrap-datepicker.js');
		wp_enqueue_script('bootstrap-datepicker-pt-BR',		LD_AGENDA_SCRIPTS_DIR . 'bootstrap-datepicker.pt-BR.min.js');
		wp_enqueue_script('bootstrap-datepicker-template',	LD_AGENDA_SCRIPTS_DIR . 'bootstrap-datepicker-template.js');
		wp_enqueue_script('bootstrap-select',				LD_AGENDA_SCRIPTS_DIR . 'bootstrap-select.min.js');
	}
	public function init_hooks(){
		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook(__FILE__, array($this,'deactive'));
		
		add_action("init",array($this,"init"));
		add_action('wp_enqueue_scripts', array($this,'enqueue_scripts') );
		add_action('admin_enqueue_scripts', array($this,'enqueue_scripts') );
		add_action('wp_head', array($this,'enqueue_styles') );
		add_action('admin_head', array($this,'enqueue_styles') );
		add_action("admin_menu",array($this,"add_admin_menu"));
				
		add_action("admin_post_agendar",array($this,"agendar"));
		add_action("admin_post_consultar_profissionais",array($this,"consultar_profissionais"));
		add_action("admin_post_consultar_servicos",array($this,"consultar_servicos"));
		add_action("admin_post_consultar_datas",array($this,"consultar_datas"));
		add_action("admin_post_consultar_horas",array($this,"consultar_horas"));
		add_action("admin_post_novo_servico",array($this,"novo_servico"));
		add_action("admin_post_consultar_tempos_servicos",array($this,"consultar_tempo_servico"));
		add_action("admin_post_consulta_servico_id",array($this,"consultar_servico_id"));
		add_action("admin_post_editar_servico_id",array($this,"editar_servico_id"));
		add_action("admin_post_deletar_servico_id",array($this,"deletar_servico_id"));
		add_action("admin_post_novo_categoria",array($this,"novo_categoria"));
		add_action("admin_post_consultar_tempos_categorias",array($this,"consultar_tempo_categoria"));
		add_action("admin_post_consulta_categoria_id",array($this,"consultar_categoria_id"));
		add_action("admin_post_editar_categoria_id",array($this,"editar_categoria_id"));
		add_action("admin_post_deletar_categoria_id",array($this,"deletar_categoria_id"));
		add_action("admin_post_consultar_agendamentos",array($this,"consultar_agendamentos_dia"));
		add_action("admin_post_consultar_datas_indisponiveis",array($this,"consultar_datas_indisponiveis"));
		add_action("admin_post_salvar_datas_indisponiveis",array($this,"salvar_datas_indisponiveis"));
		add_action("admin_post_salvar_configuracoes",array($this,"salvar_configuracoes"));
		add_action("admin_post_novo_profissional",array($this,"novo_profissional"));
		add_action("admin_post_consulta_profissional_id",array($this,"consultar_profissional_id"));
		add_action("admin_post_editar_profissional_id",array($this,"editar_profissional_id"));
		add_action("admin_post_deletar_profissional_id",array($this,"deletar_profissional_id"));
		add_action("admin_post_teste_ldagenda",array($this,"debug"));
	}
	public function debug(){
		echo "<h1> area de depuração</h1><h2>teste de saidas de funções</h2><br>";
		$r = self::pegar_horarios_por_dia();
		echo json_encode($r);
	}
	public function formulario_agendamento(){
	global $wpdb,$userdata;
	if ( is_user_logged_in() ) {
		//verificar se existe agendamento para o usuar
		$usuario_agendado = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_ag_agendamentos` WHERE `usuario` = '%d' AND data >= %d",$userdata->ID,current_time("Y-m-d")));
		if(count($wpdb->last_result)>=(get_option("LDAgenda")["max_agendamentos"])){
			echo "<h4>Você já atingiu o limite de agendamentos!</h4>";
			echo "<h5>Possui ".count($wpdb->last_result)." horarios agendados.</h5>";
		}else{
			require("formulario_agendamento.php");
		}
	} else {
		echo '<h4>É nescessario fazer login para agendar!</h4>	';
	}
	}
	public function init_shortcodes(){
		add_shortcode("form-agenda",array($this,"formulario_agendamento"));
	}
	public function getServico($id){
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM `wp_ag_servicos` WHERE `id` = '%d' ",$id));
		
	}	
	public function getProfissional($id){
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM `wp_ag_profissionais` WHERE `id` = '%d' ",$id));
	}
	//trata as requisições da pagina
	public function agendar(){	
		header("Content-Type:text/json");
		global $current_user,$wpdb;
		$response = array();
		/*  --checar se usuario esta registrado --*/
		if(!isset($current_user->ID)){
			$response["erro"] = "O usuario não esta registrado";
			echo json_encode($response);
			die();
		}
		
		/* -- Vericar se os dados existem -- */
		if(!isset($_REQUEST['cod_servico'])) {
			$response["erro"] = "Servico selecionado é invalido";
			echo json_encode($response);
			die();
		};
		$servico = $_REQUEST['cod_servico'];
		
		if(!isset($_REQUEST['cod_profissional'])) {
			$response["erro"] = "Profissional selecionado é invalido";
			echo json_encode($response);
			die();
		};
		$profissional = $_REQUEST['cod_profissional'];
		
		if(!isset($_REQUEST['data_agendamento'])) {
			$response["erro"] = "Seleciona uma data";
			echo json_encode($response);
			die();
		};
		$data_agendamento = $_REQUEST['data_agendamento'];
		
		if(!isset($_REQUEST['horario_agendamento'])) {
			$response["erro"] = "Seleciona um horario";
			echo json_encode($response);
			die();
		};
		$hora_agendamento = $_REQUEST['horario_agendamento'];
		
		/* -- Verificar se os dados estão corretos -- */
		
			//servico existe?
			$servico_db = self::getServico($servico);
			
			if(empty($servico_db)) {
				
				$response["erro"] = "O servico não existe";
				echo json_encode($response);
				die();
			}
			//profissional existe?
			$profissional_db = self::getProfissional($profissional);
			if(empty($profissional_db)) {
				$response["erro"] = "O profissional não existe no banco de dados";
				echo json_encode($response);
				die();
			}
			
			//data valida?
			$dataValide = explode("/",$data_agendamento); // fatia a string $dat em pedados, usando / como referência
			$d = (int)$dataValide[0];
			$m = (int)$dataValide[1];
			$y = (int)$dataValide[2];
			if(!checkdate($m,$d,$y)){
				$response["erro"] = "A data não é valida";
				echo json_encode($response);
				die();
			}
			
			
		/* -- Vericar se os se a data e hora esta disponivel -- */
			//a data esta na lista de indisponiveis?
				$data_formatada =date("Y-m-d",mktime(0,0,0,$m,$d,$y));			
				$wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_ag_datas_indisponiveis` WHERE `data` = '%s' ",$data_formatada));
				if(count($wpdb->last_result)>0){
					$response["erro"] = "A data esta indisponivel, por favor escolha outra data.";
					echo json_encode($response);
					die();
				}
			// a data esta fora dos dias de funcionamento (FALTA VER SE È DIA DE SEMANA DE ACORDO COM A CONFIGURAÇÂO)
			if(in_array(date("w",mktime(0,0,0,$m,$d,$y)),self::get_disabled_days_of_week())){
				$response["erro"] = "No dia inforado o estabelecimento estará fechado.";
				echo json_encode($response);
				die();
			}
			
			//ver se o dia esta esgotado
			$dias_disponiveis = self::verificar_dias_esgotados($servico_db->tempo,$profissional,$data_agendamento);
			
			if(!empty($dias_disponiveis)){
				$response["erro"] = "Os horarios para este dia estão esgotados";
					echo json_encode($response);
					die();
			}
			// o periodo disponivel no dia é suficiente para o servico a ser realizado?
			$hora_disponivel = false;
			$horarios_disponiveis = self::verificar_horarios_disponiveis($servico_db->tempo,$profissional,$data_agendamento);
			foreach($horarios_disponiveis as $hor){
				if($hor["id"] == $hora_agendamento) $hora_disponivel = true;
			}
			if(!$hora_disponivel){
				$response["erro"] = "Horario Indisponivel";
					echo json_encode($response);
					die();
			};
			
		
		
		/* -- Vericar se o profissional faz aquele servico -- */

		$serv_pro = $wpdb->get_row($wpdb->prepare("SELECT * FROM `wp_ag_profissional_servico` WHERE `id_servico` = '%d' AND `id_profissional` = '%d'",$servico,$profissional));	
		if(!$serv_pro){
			$response["erro"] = "O profissional não faz esse serviço, contate o administrador";
					echo json_encode($response);
					die();
		}
		/* fazer o agendamento */
		$insert_array = array(
			"id"=>NULL,
			"servico"=>$servico,
			"profissional"=>$profissional,
			"data"=>self::convert_date_sql($data_agendamento),
			"hora_inicio"=>($hora_agendamento),
			"hora_final"=>($hora_agendamento+$servico_db->tempo),
			"status"=>1,/**1 = agendado**/
			"usuario"=>$current_user->ID
		);
		$wpdb->insert("wp_ag_agendamentos",$insert_array);
		//insere o agendamento
		$hora_agend = "";
		foreach($horarios_disponiveis as $h){
			if($h["id"]==$hora_agendamento)$hora_agend = $h["text"];
		}
		$response = array("id"=>$wpdb->insert_id,"data"=>$data_agendamento,"hora"=>$hora_agend,"profissional"=>$profissional_db->nome,"servico"=>$servico_db->nome);
		echo json_encode($response);
		
	}
	public function consultar_tempos_servico(){//ok
		$horarios = get_option("LDAgenda")["horario_funcionamento"];
		$intervalo = get_option("LDAgenda")["intervalo_minimo"];
		$hora_final = 	strtotime($horarios["fecha"])-strtotime($horarios["abre"]);
		$horarios_dia = array();
		$k=0;
			for (
				$hora_cursor = strtotime("+".$intervalo." minute","0");
				$hora_cursor<=$hora_final;
				$hora_cursor=strtotime("+".$intervalo." minute",$hora_cursor))
			{
				$k++;
				$horarios_dia[$k]=date("H:i",$hora_cursor);
			}
		return $horarios_dia;
	}
	public function consultar_profissionais(){
		header("Content-Type:text/json");
		global $wpdb;
		if(isset($_REQUEST["filter"]))
		{
			switch($_REQUEST["filter"]){
				case 'por_servico':
					if(!isset($_REQUEST["cod_servico"])) {
						die("{'erro':'codigo do servico não encontrado'}");
					}
					$servico_id = $_REQUEST["cod_servico"];
					echo json_encode(
						$wpdb->get_results(
							$wpdb->prepare("SELECT wp_ag_profissional_servico.id_profissional,wp_ag_profissionais.nome FROM `wp_ag_profissional_servico` left join wp_ag_profissionais on wp_ag_profissionais.id = wp_ag_profissional_servico.id_profissional WHERE wp_ag_profissional_servico.id_servico=%d ",$servico_id)),
						JSON_PRETTY_PRINT
					);
				break;
			}
		} else {
			echo json_encode($wpdb->get_results("SELECT * FROM `wp_ag_profissionais` "),JSON_PRETTY_PRINT);
		}
	}
	public function consultar_servicos(){
		header("Content-Type:text/json");
		global $wpdb;
		if(isset($_REQUEST["filter"]))
		{
			/** futuramente acrescentar filtros **/
			die("em breve...");
		} else {
			echo json_encode($wpdb->get_results("SELECT `wp_ag_servicos`.id,
											`wp_ag_servicos`.nome,
											`wp_ag_servicos`.valor,
											`wp_ag_categorias_servicos`.nome as categoria
											FROM `wp_ag_servicos` left join `wp_ag_categorias_servicos` on `wp_ag_categorias_servicos`.id = `wp_ag_servicos`.categoria"),JSON_PRETTY_PRINT);
		}
	}
	public function consultar_datas(){
		header("Content-Type:text/json");
		global $wpdb;
		if(isset($_REQUEST["filter"]))
		{
			switch($_REQUEST["filter"]){
				case 'por_profissional':
					if(!isset($_REQUEST["cod_servico"])) {
						die("{'erro':'codigo do servico não encontrado'}");
					}
					if(!isset($_REQUEST["cod_profissional"])) {
						die("{'erro':'codigo do profissional não encontrado'}");
					}
					$resposta = array();
					$datas_indisponiveis = array();
					$profissional_id = $_REQUEST["cod_profissional"];
					$servico_id = $_REQUEST["cod_servico"];
					// carrega data indisponiveis no banco
					
					foreach ($wpdb->get_results("SELECT DATE_FORMAT( data, '%d\/%m\/%Y' ) as data FROM wp_ag_datas_indisponiveis WHERE data > NOW()") as $data){
						$datas_indisponiveis[] = $data->data;
					};
					// pegar tempo de servico
					
					$servico = $wpdb->get_row($wpdb->prepare("SELECT tempo FROM wp_ag_servicos WHERE id = '%d'",$servico_id));
					$resposta["daysOfWeekDisabled"]= self::get_disabled_days_of_week();
					$resposta["datesDisabled"] = self::join_array( $datas_indisponiveis,self::verificar_dias_esgotados((int)$servico->tempo,(int)$profissional_id));
					echo json_encode($resposta);	
				break;
			}
		} else {
			//echo json_encode($wpdb->get_results("SELECT * FROM `wp_ag_servicos` "),JSON_PRETTY_PRINT);
		}
	}
	public function consultar_horas(){
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["cod_servico"])) {
			die("{'erro':'codigo do servico não encontrado'}");
		}
		if(!isset($_REQUEST["cod_profissional"])) {
			die("{'erro':'codigo do profissional não encontrado'}");
		}
		if(!isset($_REQUEST["data_agendamento"])) {
			die("{'erro':'data não encontrada'}");
		}
		$resposta = array();
		$profissional_id = $_REQUEST["cod_profissional"];
		$servico_id = $_REQUEST["cod_servico"];
		$data_agendamento = $_REQUEST["data_agendamento"];
		// pegar tempo de servico
		$servico = $wpdb->get_row($wpdb->prepare("SELECT tempo FROM wp_ag_servicos WHERE id = '%d'",$servico_id));
		
		$resposta = self::verificar_horarios_disponiveis((int)$servico->tempo,(int)$profissional_id,$data_agendamento);
		
		echo json_encode($resposta,JSON_PRETTY_PRINT);	
	}
	public function get_disabled_days_of_week(){

		$resposta = array();
		$dias = get_option("LDAgenda")["dias_funcionamento"];
		foreach($dias as $c=>$d){
			if($d == 0){
				$resposta[]=$c;
			}
		}
		return $resposta;
	}
	public function verificar_dias_esgotados($tempo_servico,$profissional_id,$dia=false){
		//obter dias que o profissional trabalha

			global $wpdb;
		$horarios = get_option("LDAgenda")["horario_funcionamento"];

			$agendamentos = $wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT( data, '%%d\/%%m\/%%Y' ) as data,hora_inicio,hora_final FROM wp_ag_agendamentos WHERE profissional = %d AND data > NOW() ORDER BY `data` ASC, `hora_inicio` ASC",$profissional_id));
			//			GERAR TODOS OS HORARIO BASEADO EM PERIODO MINIMO
		if($dia)
			$agendamentos = $wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT( data, '%%d\/%%m\/%%Y' ) as data,hora_inicio,hora_final FROM wp_ag_agendamentos WHERE profissional = %d AND data = %s ORDER BY `hora_inicio` ASC",$profissional_id,self::convert_date_sql($dia)));
		if(!$agendamentos) return array();
		$dias_lotados = array();
		$dias_verificados_disponiveis = array();
		$horarios_dia =  self::pegar_horarios_por_dia();
		foreach($agendamentos as $ch=>$agendamento){
				//verificando primeiro horario
			
				if(in_array($agendamento->data,$dias_verificados_disponiveis))continue;
				
				if((!isset($agendamentos[$ch-1]))||$agendamentos[$ch-1]->data!=$agendamento->data)
				{
					//primeira  hora do dia
					
					if(isset($agendamentos[$ch-1])){// ver se o dia anterior tinha horas disponiveis
						if(!in_array($agendamentos[$ch-1]->data,$dias_verificados_disponiveis)){
							if(!in_array($agendamentos[$ch-1]->data,$dias_lotados))
								$dias_lotados[]=$agendamentos[$ch-1]->data;
						}
					}
					
					
					if($agendamento->hora_inicio > 1){// ver se tem tempo no inicio do dia
						if($agendamento->hora_inicio>$tempo_servico){
							$dias_verificados_disponiveis[]=$agendamento->data;
							continue;
						}
					}
					
					
				} else {
					
					if(($agendamento->hora_inicio-$agendamentos[$ch-1]->hora_final)>=$tempo_servico)
					{
						$dias_verificados_disponiveis[]=$agendamento->data;
						continue;
					}
					
				}
				
				if(((!isset($agendamentos[$ch+1])) || ((isset($agendamentos[$ch+1]))&&($agendamentos[$ch+1]->data!=$agendamento->data)))){// ultimo agendamento
					//verificar se o servico cabes
					$tempo_disponivel = count($horarios_dia)- $agendamento->hora_final;
					if($tempo_disponivel>=$tempo_servico){
						if(!in_array($agendamento->data,$dias_verificados_disponiveis))
							$dias_verificados_disponiveis[]=$agendamento->data;
						continue;
					} else {
						if(!in_array($agendamento->data,$dias_lotados))
							$dias_lotados[]=$agendamento->data;
					}
				}
		};
		
		return $dias_lotados;
	}
	public function verificar_horarios_disponiveis($tempo_servico,$profissional_id,$data_agendamento){
			//obter horarios disponiveis do dia
			$resposta = array();
		$data_agendamento = self::convert_date_sql($data_agendamento);
			global $wpdb;
		$horarios = get_option("LDAgenda")["horario_funcionamento"];
		$agendamentos = $wpdb->get_results($wpdb->prepare("SELECT hora_inicio,hora_final FROM wp_ag_agendamentos WHERE profissional = %d AND data = '%s' ORDER BY  `hora_inicio` ASC",$profissional_id,$data_agendamento));
			//	GERAR TODOS OS HORARIO BASEADO EM PERIODO MINIMO
		
		$horarios_dia =  self::pegar_horarios_por_dia();
		foreach($agendamentos as $ch=>$agendamento){
				//verificando primeiro horario
				for($elimina = $agendamento->hora_inicio-1;$elimina<$agendamento->hora_final-1;$elimina++)
				{
					unset($horarios_dia[$elimina]);
				}
		};
		foreach($horarios_dia as $hor=>$as){
			//checar continuidade
			$tem_espaco = true;
			for($a=0;$a<$tempo_servico;$a++){
				if(!isset($horarios_dia[($hor+$a)])){
					$tem_espaco = false;
				}
			}
			if(!$tem_espaco){			
					unset($horarios_dia[($hor)]);
			}
		}
		//limpar_chaver do array
		foreach($horarios_dia as $ch=>$val)
		{
			$resposta[]=array("id"=>($ch+1),"text"=>$val);
		}
		return $resposta;		
	}
	public function pegar_horarios_por_dia(){
		$horarios = get_option("LDAgenda")["horario_funcionamento"];
		$intervalo = get_option("LDAgenda")["intervalo_minimo"];
		$horarios_dia = array();
			for (
				$hora_cursor = strtotime($horarios["abre"]);
				$hora_cursor<strtotime($horarios["fecha"]);
				$hora_cursor=strtotime("+".$intervalo." minute",$hora_cursor))
			{
				$horarios_dia[]=date("H:i",$hora_cursor);
			}
		return $horarios_dia;
	}
	public function join_array($a,$b){
		$c = array();
		foreach($a as $ch=>$val){
			$c[] = $val;
		}
		
		foreach($b as $ch=>$val){
			$c[] = $val;
		}
		return $c;
	}
	public function convert_date_sql($date_comun){
		$da = explode("/",$date_comun);
		$m = $da[1];
		$d = $da[0];
		$y = $da[2];
		return	date("Y-m-d",mktime(0,0,0,$m,$d,$y));
	}
	public function novo_servico(){
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["servico_nome"])) {
			die("{'erro':'nome do servico não encontrado'}");
		}
		if(!isset($_REQUEST["servico_valor"])) {
			die("{'erro':'valor do servico não encontrado'}");
		}
		if(!isset($_REQUEST["servico_tempo"])) {
			die("{'erro':'tempo do servico não encontrado'}");
		}
		if(isset($_REQUEST["categoria_tempo"]))
			die("{'erro':'categoria do servico não encontrada'}");

		
		$novo_servico = array(
		"nome"=>$_REQUEST["servico_nome"],
		"valor"=>$_REQUEST["servico_valor"],
		"tempo"=>$_REQUEST["servico_tempo"],
		"categoria"=>$_REQUEST["servico_categoria"],
		);
		
		if($wpdb->insert("wp_ag_servicos",$novo_servico,array("%s","%f","%d")))
		{
			
			//incluir id no resultado;
			$novo_servico["id"] = $wpdb->insert_id;
			//vincular  serviço / profissional;
			
			$qr_vincula = array();
			$profissionais = $wpdb->get_results("SELECT id from wp_ag_profissionais where 1");
			foreach($profissionais as $profissional)
			{
				//var_dump($profissional->id);
				$wpdb->insert("wp_ag_profissional_servico",array("id_profissional"=>$profissional->id,"id_servico"=>$novo_servico["id"]));
			}
			
			echo json_encode($novo_servico);//*/
		};
	}
	public function editar_servico_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do servico não encontrado'}");
		}
		$edit_servico = array();
		if(isset($_REQUEST["servico_nome"]))
			$edit_servico["nome"]=$_REQUEST["servico_nome"];
		
		if(isset($_REQUEST["servico_valor"]))
			$edit_servico["valor"]=$_REQUEST["servico_valor"];
		
		if(isset($_REQUEST["servico_tempo"]))
			$edit_servico["tempo"]=$_REQUEST["servico_tempo"];

		if(isset($_REQUEST["servico_categoria"]))
			$edit_servico["categoria"]=$_REQUEST["servico_categoria"];


		if($wpdb->update("wp_ag_servicos",$edit_servico,array("id"=>$_REQUEST["id"])))
		{
			//incluir id no resultado;
			$edit_servico["id"] = $_REQUEST["id"];
			echo json_encode($edit_servico);
		};
	}
	public function deletar_servico_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do servico não encontrado'}");
		}

		if($wpdb->delete("wp_ag_servicos",array("id"=>$_REQUEST["id"])))
		{
			//incluir id no resultado;
			$wpdb->delete("wp_ag_profissional_servico",array("id_servico"=>$_REQUEST["id"]));
			echo json_encode(array("id"=>$_REQUEST["id"]));
		};
	}
	public function consultar_servico_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(isset($_REQUEST['id']))
		$servico = $wpdb->get_row($wpdb->prepare("select * from wp_ag_servicos where id='%d'",$_REQUEST['id']));
		echo json_encode($servico);
	}
	public function novo_categoria(){
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["categoria_nome"])) {
			die("{'erro':'nome do categoria não encontrado'}");
		}
		$novo_categoria = array(
		"nome"=>$_REQUEST["categoria_nome"]
		);
		
		if($req = $wpdb->insert("wp_ag_categorias_servicos",$novo_categoria,array("%s")))
		{
			$novo_categoria["id"] = $wpdb->insert_id;
			echo json_encode($novo_categoria);
		} else {
			die("ocorreu um erro inesperado");
		};
	}
	public function editar_categoria_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do categoria não encontrado'}");
		}
		$edit_categoria = array();
		if(isset($_REQUEST["categoria_nome"]))
			$edit_categoria["nome"]=$_REQUEST["categoria_nome"];

		if($wpdb->update("wp_ag_categorias_servicos",$edit_categoria,array("id"=>$_REQUEST["id"])))
		{
			//incluir id no resultado;
			$edit_categoria["id"] = $_REQUEST["id"];
			echo json_encode($edit_categoria);
		};
	}
	public function deletar_categoria_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do categoria não encontrado'}");
		}

		if($wpdb->delete("wp_ag_categorias_servicos",array("id"=>$_REQUEST["id"])))
		{
			//incluir id no resultado;
			echo json_encode(array("id"=>$_REQUEST["id"]));
		};
	}
	public function consultar_categoria_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(isset($_REQUEST['id']))
		$categoria = $wpdb->get_row($wpdb->prepare("select * from wp_ag_categorias_servicos where id='%d'",$_REQUEST['id']));
		echo json_encode($categoria);
	}
	public function novo_profissional(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["profissional_nome"])) {
			die("{'erro':'nome do servico não encontrado'}");
		}
		$servicos=array();
		if(isset($_REQUEST["servicos"])) {
			$servicos=explode("_",$_REQUEST["servicos"]);
		}
		$response = array("nome"=>$_REQUEST["profissional_nome"]);
		if($wpdb->insert("wp_ag_profissionais",	array("nome"=>$_REQUEST["profissional_nome"]),array("%s"))){
			$response["id"]=$wpdb->insert_id;
			
			if(count($servicos)>0){
				foreach($servicos as $serv){
					$wpdb->insert("wp_ag_profissional_servico",	array("id_profissional"=>$response["id"],"id_servico"=>$serv),array("%d","%d"));
				}
			}
			echo json_encode($response);
			
		};
		
		
	}
	public function editar_profissional_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do servico não encontrado'}");
		}
		$edit_profissional = array();
		
		if(isset($_REQUEST["profissional_nome"]))
			$edit_profissional["nome"]=$_REQUEST["profissional_nome"];


		$wpdb->update("wp_ag_profissionais",$edit_profissional,array("id"=>$_REQUEST["id"]));
		
			//incluir id no resultado;
			$edit_profissional["id"] = $_REQUEST["id"];
			$servicos=array();
			if(isset($_REQUEST["servicos"])) {
				$servicos=empty($_REQUEST["servicos"])?array():explode("_",$_REQUEST["servicos"]);
			}
			$servicos_db = $wpdb->get_results(
							$wpdb->prepare("SELECT * FROM wp_ag_profissional_servico WHERE id_profissional=%d ",$edit_profissional["id"])
							);
						$servicos_processados=array();
			if(count($servicos)>0){						
				foreach($servicos_db as $serv){
					if(in_array($serv->id_servico,$servicos)){
						$servicos_processados[]=$serv->id_servico;
					} else {
						$wpdb->delete("wp_ag_profissional_servico",array("id_servico"=>$serv->id_servico,"id_profissional"=>$edit_profissional["id"]));
					};
				}
				foreach($servicos as $serv){
					if(!in_array($serv,$servicos_processados)){
						$wpdb->insert("wp_ag_profissional_servico",	array("id_profissional"=>$edit_profissional["id"],"id_servico"=>$serv),array("%d","%d"));
					}
				}
			} else if(count($servicos_db)>0){
				$wpdb->delete("wp_ag_profissional_servico",array("id_profissional"=>$edit_profissional["id"]));
				/* deletar entradas*/
			}
			echo json_encode($edit_profissional);
	}
	public function deletar_profissional_id(){//ok
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do profissional não encontrado'}");
		}

		if($wpdb->delete("wp_ag_profissionais",array("id"=>$_REQUEST["id"])))
		{
			//incluir id no resultado;
			$wpdb->delete("wp_ag_profissional_servico",array("id_profissional"=>$_REQUEST["id"]));
			echo json_encode(array("id"=>$_REQUEST["id"]));
		};
	}
	public function consultar_profissional_id(){
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["id"])) {
			die("{'erro':'id do profissional não encontrado'}");
		}
		$profissional = $wpdb->get_row($wpdb->prepare("select * from wp_ag_profissionais where id='%d'",$_REQUEST['id']));

		$servicos_db = $wpdb->get_results(
			$wpdb->prepare("SELECT id_servico as id FROM wp_ag_profissional_servico WHERE id_profissional=%d ",$profissional->id)
		);
		$profissional->servicos = array();
		foreach($servicos_db as $serv){
			$profissional->servicos[]=$serv->id;
		}
		echo json_encode($profissional);
	}
	public function consultar_agendamentos_dia(){
		header("Content-Type:text/json");
		global $wpdb;
		$data_str = (isset($_REQUEST["data"]))?$_REQUEST["data"]:date("d/m/Y");
		$data_arr = explode("/",$data_str);
		if(count($data_arr)<3){
					die("{'erro':'formato de data errado	'}");
		}
		$data_req = mktime(0,0,0,$data_arr[1],$data_arr[0],$data_arr[2]);
		$data_inicial = date("Y-m-d",strtotime("-".date("w",$data_req)." days",$data_req)); //domingo
		$data_final = date("Y-m-d",strtotime("+".(6-((int)date("w",$data_req)))." days",$data_req)); //sabado
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
	$horarios = self::pegar_horarios_por_dia();
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
		
		if($ag->data == date("Y-m-d",$data_req)){
			if(!isset($table[$horarios[$ag->hora_inicio-1]])) $table[$horarios[$ag->hora_inicio-1]]= array();
			$table[$horarios[$ag->hora_inicio-1]][] = array("profissional"=>($ag->profissional),"cliente"=>(get_user_meta($ag->usuario)["nickname"][0]),"servico"=>($ag->servico));
		}
	}
	echo json_encode(array("data"=>date("d/m/Y",$data_req),"por_dia"=>$table,"por_semana"=>$table_sem));
	}
	public function consultar_datas_indisponiveis(){
		global $wpdb;
		header("Content-Type:text/json");
		$datas = array();
		foreach($wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT( data, '%%d\/%%m\/%%Y' ) as data FROM `wp_ag_datas_indisponiveis` WHERE `data` > '%s' ",current_time("mysql")),ARRAY_A) as $ch=>$dat){
			$datas[]=$dat['data'];
		}
		echo json_encode($datas,JSON_PRETTY_PRINT);

	}
	public function salvar_configuracoes(){
		$config = get_option("LDAgenda");
		if(isset($_REQUEST["week"]))$config["dias_funcionamento"]=explode(",",$_REQUEST["week"]);
		if(isset($_REQUEST["abre"])&&isset($_REQUEST["fecha"]))$config["horario_funcionamento"]=array(	"abre"=>$_REQUEST["abre"],"fecha"=>$_REQUEST["fecha"]);
		if(isset($_REQUEST["intervalo"]))$config["intervalo_minimo"]=$_REQUEST["intervalo"];
		if(isset($_REQUEST["max_agendamentos"]))$config["max_agendamentos"]=$_REQUEST["max_agendamentos"];
		echo update_option("LDAgenda",$config);;
	}
	public function salvar_datas_indisponiveis(){
		header("Content-Type:text/json");
		global $wpdb;
		if(!isset($_REQUEST["datas"])) {
			die("{'erro':'datas não encontradas'}");
		}
		$datas = explode(",",$_REQUEST["datas"]);
		
			$datas_ok = array();
			//incluir id no resultado;
			foreach($wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT( data, '%%d\/%%m\/%%Y' ) as data,data as data_mysql FROM `wp_ag_datas_indisponiveis` WHERE `data` > '%s' ",current_time("mysql")),ARRAY_A) as $ch=>$dat){
				if(!in_array($dat["data"],$datas)){
					$wpdb->delete("wp_ag_datas_indisponiveis",array("data"=>self::convert_date_sql($dat["data"])));
				} else {
					$datas_ok[]=$dat["data"];
				}
			}
			foreach($datas as $dat){
				if(!in_array($dat,$datas_ok)){
					$wpdb->insert("wp_ag_datas_indisponiveis",array("data"=>self::convert_date_sql($dat)));
					$datas_ok[]=$dat;
				}
			}
			echo json_encode($datas_ok);
	}
	public function getCategorias(){
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM `wp_ag_categorias_servicos` ");
	}
}
global $ldagenda;
$ldagenda = LDAgenda::getInstance();

