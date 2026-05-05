<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/auxiliares/tabirpf/tabirpf_edita.php
***** Conteúdo: Edição da tabela
***** Data criação: abr/2026
***** Disponível em: guiadomestico.com.br
*****
**************************************************
*****
***** DESENVOLVEDOR
***** Turma A2026S1N2
***** Projeto Integrador 
***** Univesp
***** Universidade Virtual do Estado de São Paulo
***** Matéria obrigatória do curso de
***** Análise de Sistemas e 
***** Engenharia da Computação
***** São Paulo - SP - 2026
***** 
**************************************************
**************************************************
*/
session_start();
$Raiz = "../../../";
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
include($Raiz . "conexao/db.php");
$Home = $Raiz . "sistema/auxiliares/tabirpf/tabirpf.php";
$ok = true;
$link_main = $Home;
VerificaSessao();
VerificaAdmin($Raiz);
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$ok = false;
}
if (!isset($_POST["chave_tabirpf"])) {
	$ok = false;
}
if (!$ok) {
	header("Location: " . $Home);
	die();
}
$pagina_titulo = "Inclusão do Tabirpf";
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = $_POST["acao_tabirpf"];
}
if ($Acao == "GRAVAR") {
	$chave_tabirpf = $_POST["chave_tabirpf"];
	$vli_tabirpf = $_POST["vli_tabirpf"];  
	$vlf_tabirpf = $_POST["vlf_tabirpf"];
	$aliq_tabirpf = $_POST["aliq_tabirpf"];
  $vlded_tabirpf = $_POST["vlded_tabirpf"];
  $ano_tabirpf = $_POST["ano_tabirpf"];  
	abre_db();
	if ($chave_tabirpf == "0") {
		// Inclusão do registro	   
		$strsql = "
		insert 
		into 
		ttabirpf 
		(vli_tabirpf
		,vlf_tabirpf
		,aliq_tabirpf
		,vlded_tabirpf
		,ano_tabirpf
		) values 
		(:vvli_tabirpf
		,:vvlf_tabirpf
		,:valiq_tabirpf		
		,:vvlded_tabirpf
    ,:vano_tabirpf
		)";
		$qtabirpf = $pdo->prepare($strsql);
		$qtabirpf->bindParam(":vvli_tabirpf", $vli_tabirpf);
		$qtabirpf->bindParam(":vvlf_tabirpf", $vlf_tabirpf);
		$qtabirpf->bindParam(":valiq_tabirpf", $aliq_tabirpf);
    $qtabirpf->bindParam(":vvlded_tabirpf", $vlded_tabirpf);    
		$qtabirpf->bindParam(":vano_tabirpf", $ano_tabirpf);		
		$qtabirpf->execute();
		$chave_tabirpf = $pdo->lastInsertId();
		fecha_diario("ttabirpf", "chave_tabirpf", $chave_tabirpf, $abre_diario = array(), $campoexcluidos = array("dta_tabirpf"));
		// EOF Inclusao do registro	   
	}
	else {
		// Alteção do registro	   
		$abre_diario = abre_diario("ttabirpf", "chave_tabirpf", $chave_tabirpf, $campoexcluidos = array("dta_tabirpf"));
		$strsql = "
		update 
		ttabirpf 
		set
		vli_tabirpf = :vvli_tabirpf
		,vlf_tabirpf = :vvlf_tabirpf
		,aliq_tabirpf = :valiq_tabirpf
    ,vlded_tabirpf = :vvlded_tabirpf    
		,ano_tabirpf = :vano_tabirpf		
		where 
		chave_tabirpf = :vchave_tabirpf
		";
		$qtabirpf = $pdo->prepare($strsql);
		$qtabirpf->bindParam(":vchave_tabirpf", $chave_tabirpf);
		$qtabirpf->bindParam(":vvli_tabirpf", $vli_tabirpf);
		$qtabirpf->bindParam(":vvlf_tabirpf", $vlf_tabirpf);
		$qtabirpf->bindParam(":valiq_tabirpf", $aliq_tabirpf);		
		$qtabirpf->bindParam(":vvlded_tabirpf", $vlded_tabirpf);    
    $qtabirpf->bindParam(":vano_tabirpf", $ano_tabirpf);
		$qtabirpf->execute();
		fecha_diario("ttabirpf", "chave_tabirpf", $chave_tabirpf, $abre_diario, $campoexcluidos = array("dta_tabirpf"));
		// EOF Alteração do registro	   
	}   
	fecha_db();   	
	header("Location: " . $Home);
	die();
	// EOF Avalia informação para inclusão
}
//********************
//********************
//**** EOF - Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************

//********************
//********************
//**** Edita registro caso POST chamado contenha diretiva EDITAR
//********************
//********************
if ($Acao == "EDITAR") {
	$chave_tabirpf = "0";
	$vli_tabirpf = 0;
	$vlf_tabirpf = 0;
	$aliq_tabirpf = 0;
	$vlded_tabirpf = 0;
	$ano_tabirpf = strval(date("Y"));
	if ($ok) {
		if (!isset($_POST["chave_tabirpf"])) {
			$ok = false;
		}
	}
	if ($ok) {
		$chave_tabirpf = $_POST["chave_tabirpf"];
	}
	if ($ok) {
		if (!is_numeric($chave_tabirpf)) {
			$ok = false;
		}
	}
	if (!$ok) {
		header("Location: " . $Home);
		die();	
	}
	//********************
	//********************
	//**** Pega registro se já esitver cadastrado
	//********************
	//********************
	abre_db();
	$chave_tabirpf = intval($chave_tabirpf);
	if ($chave_tabirpf > 0) {
		$strsql = "
		select 
		ttabirpf.* 	
		from 
		ttabirpf 
		where 
		ttabirpf.chave_tabirpf = :vchave_tabirpf";
		$qtabirpf = $pdo->prepare($strsql);
		$qtabirpf->bindParam(":vchave_tabirpf", $chave_tabirpf);
		$qtabirpf->execute();
		if ($ttabirpf = $qtabirpf->fetch(PDO::FETCH_ASSOC)) {
			$chave_tabirpf  = $ttabirpf["chave_tabirpf"];
			$vli_tabirpf = $ttabirpf["vli_tabirpf"];
			$vlf_tabirpf = $ttabirpf["vlf_tabirpf"];
			$aliq_tabirpf = $ttabirpf["aliq_tabirpf"];		
			$vlded_tabirpf = $ttabirpf["vlded_tabirpf"];
			$ano_tabirpf = $ttabirpf["ano_tabirpf"];		
		}
	}
	fecha_db();
	//********************
	//********************
	//**** EOF - Pega registro se já esitver cadastrado
	//********************
	//********************

	//********************
	//********************
	//**** Define título da página (e links relacionados)
	//********************
	//********************
	if ($chave_tabirpf <= 0) {
		$pagina_titulo = "Inclusão da Tabela do IRPF";
	}
	else {
    $pagina_titulo = "Alteração da Tabela do IRPF";		
	}	
	$html_submenu = "";
  $html_submenu .= '<div class="row mb-1">' . "\n";
  $html_submenu .= '  <div class="col-12">' . "\n";
  $html_submenu .= '    <h5>Cadastro da Tabela do IRPF</h5>' . "\n";
	$html_submenu .= '  </div>' . "\n";
	$html_submenu .= '</div>' . "\n";
	$html_submenu .= '<div class="row mb-1">' . "\n";
	$html_submenu .= '  <div class="col-12">' . "\n";
	$html_submenu .= '    <a href="' . $Home . '" class="btn btn-sm btn-custom2"><i class="fa-solid fa-arrow-left"></i> Voltar</a>' . "\n";
	if ($chave_tabirpf > 0) {	
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO_EXC" data-id="' . $chave_tabirpf . '" data-campo-id="chave_tabirpf" data-tabela-id="ttabirpf" data-caixa-id="caixa_tabirpf" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC"><i class="fa-regular fa-trash-can"></i> Excluir</a>' . "\n";
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO" data-id="' . $chave_tabirpf . '" data-campo-id="chave_tabirpf" data-tabela-id="ttabirpf" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"><i class="fa-solid fa-database"></i> Diário</a>' . "\n";
	}
	$html_submenu .= '  </div>' . "\n";
	$html_submenu .= '</div>' . "\n";
	//********************
	//********************
	//**** EOF - Define título da página (e links relacionados)
	//********************
	//********************
}
//********************
//********************
//**** EOF - Edita registro caso POST chamado contenha diretiva EDITAR
//********************
//********************
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Edição da Tabela do IRPF</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
function submeter() {
	var vok = true;	
	if (document.FTABIRPF_EDITA.vli_tabirpf.value == "") {
		vok = false;
		alert("Nome inválido.");
		document.FTABIRPF_EDITA.vli_tabirpf.focus();
	}
	if (vok) {
		if (document.FTABIRPF_EDITA.vlf_tabirpf.value == "") {
			vok = false;
            alert("E-mail inválido.");
            document.FTABIRPF_EDITA.vlf_tabirpf.focus();
		}
	}
	if (vok) {
		if (document.FTABIRPF_EDITA.aliq_tabirpf.value == "") {
			vok = false;
            alert("Senha inválida.");
            document.FTABIRPF_EDITA.aliq_tabirpf.focus();
		}   
	}
	if (vok) {
		document.FTABIRPF_EDITA.submit();
	}
};

$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "tabirpf.php";
	}
});

</script>
<body class="Fonte-Raleway">
<?php
Modal_Diario($Raiz);
Modal_Exclusao($Raiz, $link_main);
?>  
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <?php echo $html_submenu ?> 
  <div class="card shadow card-personalizado">
    <div class="row">
      <div class="col mx-auto" style="max-width:800px">
        <div class="card mt-4">  
          <div class="card-header">
            <?php echo Titulo_Cartao($pagina_titulo); ?>
          </div>
          <div class="card-body">
            <div class="row small" style="padding:10px">
              <form method="POST" action="<?php echo $Raiz ?>sistema/auxiliares/tabirpf/tabirpf_edita.php" id="FTABIRPF_EDITA" name="FTABIRPF_EDITA">
                <input type="hidden" id="chave_tabirpf" name="chave_tabirpf" value="<?php echo $chave_tabirpf ?>">
                <input type="hidden" id="acao_tabirpf" name="acao_tabirpf" value="GRAVAR">
                <div class="row mb-1">
                  <label for="vli_tabirpf" class="col-sm-2">De</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vli_tabirpf" name="vli_tabirpf" placeholder="Valor inicial" value="<?php echo $vli_tabirpf ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="vlf_tabirpf" class="col-sm-2">Até</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vlf_tabirpf" name="vlf_tabirpf" placeholder="Valor final" value="<?php echo $vlf_tabirpf ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="aliq_tabirpf" class="col-sm-2">Alíquota</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="aliq_tabirpf" name="aliq_tabirpf" placeholder="Alíquota" value="<?php echo $aliq_tabirpf ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="vlded_tabirpf" class="col-sm-2">Dedução</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vlded_tabirpf" name="vlded_tabirpf" placeholder="Dedução" value="<?php echo $vlded_tabirpf ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="ano_tabirpf" class="col-sm-2">Ano</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="ano_tabirpf" name="ano_tabirpf" placeholder="Ano" value="<?php echo $ano_tabirpf ?>">
                  </div>
                </div>
              </form>
            </div>
          </div>
          <div class="card-footer text-muted text-center">
            <input type="button" class="btn btn-sm btn-custom" value="Confirmar" id="BTN_GRAVAR" name="BTN_GRAVAR" onClick="javascript:submeter();">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>