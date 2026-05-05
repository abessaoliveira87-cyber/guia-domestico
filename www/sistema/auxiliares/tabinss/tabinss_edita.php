<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/auxiliares/tabinss/tabinss_edita.php
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
$Home = $Raiz . "sistema/auxiliares/tabinss/tabinss.php";
$ok = true;
$link_main = $Home;
VerificaSessao();
VerificaAdmin($Raiz);
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$ok = false;
}
if (!isset($_POST["chave_tabinss"])) {
	$ok = false;
}
if (!$ok) {
	header("Location: " . $Home);
	die();
}
$pagina_titulo = "Inclusão do Tabinss";
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = $_POST["acao_tabinss"];
}
if ($Acao == "GRAVAR") {
	$chave_tabinss = $_POST["chave_tabinss"];
	$vli_tabinss = $_POST["vli_tabinss"];  
	$vlf_tabinss = $_POST["vlf_tabinss"];
	$aliq_tabinss = $_POST["aliq_tabinss"];
  $vlded_tabinss = $_POST["vlded_tabinss"];
  $vlfixo_tabinss = $_POST["vlfixo_tabinss"];
	$ano_tabinss = $_POST["ano_tabinss"];  
	abre_db();
	if ($chave_tabinss == "0") {
		// Inclusão do registro	   
		$strsql = "
		insert 
		into 
		ttabinss 
		(vli_tabinss
		,vlf_tabinss
		,aliq_tabinss
		,vlded_tabinss
    ,vlfixo_tabinss
		,ano_tabinss
		) values 
		(:vvli_tabinss
		,:vvlf_tabinss
		,:valiq_tabinss		
		,:vvlded_tabinss
    ,:vvlfixo_tabinss
    ,:vano_tabinss
		)";
		$qtabinss = $pdo->prepare($strsql);
		$qtabinss->bindParam(":vvli_tabinss", $vli_tabinss);
		$qtabinss->bindParam(":vvlf_tabinss", $vlf_tabinss);
		$qtabinss->bindParam(":valiq_tabinss", $aliq_tabinss);
    $qtabinss->bindParam(":vvlded_tabinss", $vlded_tabinss);
    $qtabinss->bindParam(":vvlfixo_tabinss", $vlfixo_tabinss);
		$qtabinss->bindParam(":vano_tabinss", $ano_tabinss);		
		$qtabinss->execute();
		$chave_tabinss = $pdo->lastInsertId();
		fecha_diario("ttabinss", "chave_tabinss", $chave_tabinss, $abre_diario = array(), $campoexcluidos = array("dta_tabinss"));
		// EOF Inclusao do registro	   
	}
	else {
		// Alteção do registro	   
		$abre_diario = abre_diario("ttabinss", "chave_tabinss", $chave_tabinss, $campoexcluidos = array("dta_tabinss"));
		$strsql = "
		update 
		ttabinss 
		set
		vli_tabinss = :vvli_tabinss
		,vlf_tabinss = :vvlf_tabinss
		,aliq_tabinss = :valiq_tabinss
    ,vlded_tabinss = :vvlded_tabinss
    ,vlfixo_tabinss = :vvlfixo_tabinss
		,ano_tabinss = :vano_tabinss		
		where 
		chave_tabinss = :vchave_tabinss
		";
		$qtabinss = $pdo->prepare($strsql);
		$qtabinss->bindParam(":vchave_tabinss", $chave_tabinss);
		$qtabinss->bindParam(":vvli_tabinss", $vli_tabinss);
		$qtabinss->bindParam(":vvlf_tabinss", $vlf_tabinss);
		$qtabinss->bindParam(":valiq_tabinss", $aliq_tabinss);		
		$qtabinss->bindParam(":vvlded_tabinss", $vlded_tabinss);
    $qtabinss->bindParam(":vvlfixo_tabinss", $vlfixo_tabinss);
    $qtabinss->bindParam(":vano_tabinss", $ano_tabinss);
		$qtabinss->execute();
		fecha_diario("ttabinss", "chave_tabinss", $chave_tabinss, $abre_diario, $campoexcluidos = array("dta_tabinss"));
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
	$chave_tabinss = "0";
	$vli_tabinss = 0;
	$vlf_tabinss = 0;
	$aliq_tabinss = 0;
	$vlded_tabinss = 0;
  $vlfixo_tabinss = 0;
	$ano_tabinss = strval(date("Y"));
	if ($ok) {
		if (!isset($_POST["chave_tabinss"])) {
			$ok = false;
		}
	}
	if ($ok) {
		$chave_tabinss = $_POST["chave_tabinss"];
	}
	if ($ok) {
		if (!is_numeric($chave_tabinss)) {
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
	$chave_tabinss = intval($chave_tabinss);
	if ($chave_tabinss > 0) {
		$strsql = "
		select 
		ttabinss.* 	
		from 
		ttabinss 
		where 
		ttabinss.chave_tabinss = :vchave_tabinss";
		$qtabinss = $pdo->prepare($strsql);
		$qtabinss->bindParam(":vchave_tabinss", $chave_tabinss);
		$qtabinss->execute();
		if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
			$chave_tabinss  = $ttabinss["chave_tabinss"];
			$vli_tabinss = $ttabinss["vli_tabinss"];
			$vlf_tabinss = $ttabinss["vlf_tabinss"];
			$aliq_tabinss = $ttabinss["aliq_tabinss"];		
			$vlded_tabinss = $ttabinss["vlded_tabinss"];
      $vlfixo_tabinss = $ttabinss["vlfixo_tabinss"];
			$ano_tabinss = $ttabinss["ano_tabinss"];		
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
	if ($chave_tabinss <= 0) {
		$pagina_titulo = "Inclusão da Tabela de Contribuição do INSS";
	}
	else {
    $pagina_titulo = "Alteração da Tabela de Contribuição do INSS";		
	}	
	$html_submenu = "";
  $html_submenu .= '<div class="row mb-1">' . "\n";
  $html_submenu .= '  <div class="col-12">' . "\n";
  $html_submenu .= '    <h5>Cadastro da Tabela de Contribuição do INSS</h5>' . "\n";
	$html_submenu .= '  </div>' . "\n";
	$html_submenu .= '</div>' . "\n";
	$html_submenu .= '<div class="row mb-1">' . "\n";
	$html_submenu .= '  <div class="col-12">' . "\n";
	$html_submenu .= '    <a href="' . $Home . '" class="btn btn-sm btn-custom2"><i class="fa-solid fa-arrow-left"></i> Voltar</a>' . "\n";
	if ($chave_tabinss > 0) {	
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO_EXC" data-id="' . $chave_tabinss . '" data-campo-id="chave_tabinss" data-tabela-id="ttabinss" data-caixa-id="caixa_tabinss" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC"><i class="fa-regular fa-trash-can"></i> Excluir</a>' . "\n";
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO" data-id="' . $chave_tabinss . '" data-campo-id="chave_tabinss" data-tabela-id="ttabinss" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"><i class="fa-solid fa-database"></i> Diário</a>' . "\n";
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
<title>Guia Doméstico - Edição da Tabela de Contribuição do INSS</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
function submeter() {
	var vok = true;	
	if (document.FTABINSS_EDITA.vli_tabinss.value == "") {
		vok = false;
		alert("Nome inválido.");
		document.FTABINSS_EDITA.vli_tabinss.focus();
	}
	if (vok) {
		if (document.FTABINSS_EDITA.vlf_tabinss.value == "") {
			vok = false;
            alert("E-mail inválido.");
            document.FTABINSS_EDITA.vlf_tabinss.focus();
		}
	}
	if (vok) {
		if (document.FTABINSS_EDITA.aliq_tabinss.value == "") {
			vok = false;
            alert("Senha inválida.");
            document.FTABINSS_EDITA.aliq_tabinss.focus();
		}   
	}
	if (vok) {
		document.FTABINSS_EDITA.submit();
	}
};

$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "tabinss.php";
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
              <form method="POST" action="<?php echo $Raiz ?>sistema/auxiliares/tabinss/tabinss_edita.php" id="FTABINSS_EDITA" name="FTABINSS_EDITA">
                <input type="hidden" id="chave_tabinss" name="chave_tabinss" value="<?php echo $chave_tabinss ?>">
                <input type="hidden" id="acao_tabinss" name="acao_tabinss" value="GRAVAR">
                <div class="row mb-1">
                  <label for="vli_tabinss" class="col-sm-2">De</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vli_tabinss" name="vli_tabinss" placeholder="Valor inicial" value="<?php echo $vli_tabinss ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="vlf_tabinss" class="col-sm-2">Até</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vlf_tabinss" name="vlf_tabinss" placeholder="Valor final" value="<?php echo $vlf_tabinss ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="aliq_tabinss" class="col-sm-2">Alíquota</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="aliq_tabinss" name="aliq_tabinss" placeholder="Alíquota" value="<?php echo $aliq_tabinss ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="vlded_tabinss" class="col-sm-2">Dedução</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vlded_tabinss" name="vlded_tabinss" placeholder="Dedução" value="<?php echo $vlded_tabinss ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="vlfixo_tabinss" class="col-sm-2">Valor fixo</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="vlfixo_tabinss" name="vlfixo_tabinss" placeholder="Valor fixo" value="<?php echo $vlfixo_tabinss ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="ano_tabinss" class="col-sm-2">Ano</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="ano_tabinss" name="ano_tabinss" placeholder="Ano" value="<?php echo $ano_tabinss ?>">
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