<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/cargo/cargo_edita.php
***** Conteúdo: Edição do Cargo
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
$Raiz = "../../";
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
include($Raiz . "conexao/db.php");
$Home = $Raiz . "sistema/cargo/cargo.php";
$ok = true;
$link_main = $Home;
VerificaSessao();
VerificaAdmin($Raiz);
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$ok = false;
}
if (!isset($_POST["chave_cargo"])) {
	$ok = false;
}
if (!$ok) {
	header("Location: " . $Home);
	die();
}
$pagina_titulo = "Inclusão do Cargo";
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = $_POST["acao_cargo"];
}
if ($Acao == "GRAVAR") {
	$chave_cargo = $_POST["chave_cargo"];
	$descr_cargo = $_POST["descr_cargo"];  
	$cbo_cargo = $_POST["cbo_cargo"];
	$esocial_cargo = $_POST["esocial_cargo"];
  $sit_cargo = $_POST["sit_cargo"];
	$descrdetalhada_cargo = $_POST["descrdetalhada_cargo"];  
	abre_db();
	if ($chave_cargo == "0") {
		// Inclusão do registro	   
		$strsql = "
		insert 
		into 
		tcargo 
		(descr_cargo
		,cbo_cargo
		,esocial_cargo
		,descrdetalhada_cargo
		,sit_cargo
		) values 
		(:vdescr_cargo
		,:vcbo_cargo
		,:vesocial_cargo
		,:vdescrdetalhada_cargo
		,:vsit_cargo
		)";
		$qcargo = $pdo->prepare($strsql);
		$qcargo->bindParam(":vdescr_cargo", $descr_cargo);
		$qcargo->bindParam(":vcbo_cargo", $cbo_cargo);
		$qcargo->bindParam(":vesocial_cargo", $esocial_cargo);
		$qcargo->bindParam(":vdescrdetalhada_cargo", $descrdetalhada_cargo);
		$qcargo->bindParam(":vsit_cargo", $sit_cargo);
		$qcargo->execute();
		$chave_cargo = $pdo->lastInsertId();
		fecha_diario("tcargo", "chave_cargo", $chave_cargo, $abre_diario = array(), $campoexcluidos = array("dta_cargo"));
		// EOF Inclusao do registro	   
	}
	else {
		// Alteção do registro	   
		$abre_diario = abre_diario("tcargo", "chave_cargo", $chave_cargo, $campoexcluidos = array("dta_cargo"));
		$strsql = "
		update 
		tcargo 
		set
		descr_cargo = :vdescr_cargo
		,cbo_cargo = :vcbo_cargo
		,esocial_cargo = :vesocial_cargo
		,descrdetalhada_cargo = :vdescrdetalhada_cargo
		,sit_cargo = :vsit_cargo
		where 
		chave_cargo = :vchave_cargo
		";
		$qcargo = $pdo->prepare($strsql);
		$qcargo->bindParam(":vchave_cargo", $chave_cargo);
		$qcargo->bindParam(":vdescr_cargo", $descr_cargo);
		$qcargo->bindParam(":vcbo_cargo", $cbo_cargo);
		$qcargo->bindParam(":vesocial_cargo", $esocial_cargo);
		$qcargo->bindParam(":vdescrdetalhada_cargo", $descrdetalhada_cargo);
		$qcargo->bindParam(":vsit_cargo", $sit_cargo);
		$qcargo->execute();
		fecha_diario("tcargo", "chave_cargo", $chave_cargo, $abre_diario, $campoexcluidos = array("dta_cargo"));
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
	$chave_cargo = "0";
	$descr_cargo = "";
	$cbo_cargo = "";
	$esocial_cargo = "";
	$sit_cargo = "ATIVO";
	$descrdetalhada_cargo = "";
	if ($ok) {
		if (!isset($_POST["chave_cargo"])) {
			$ok = false;
		}
	}
	if ($ok) {
		$chave_cargo = $_POST["chave_cargo"];
	}
	if ($ok) {
		if (!is_numeric($chave_cargo)) {
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
	$chave_cargo = intval($chave_cargo);
	if ($chave_cargo > 0) {
		$strsql = "
		select 
		tcargo.* 	
		from 
		tcargo 
		where 
		tcargo.chave_cargo = :vchave_cargo";
		$query = $pdo->prepare($strsql);
		$query->bindParam(":vchave_cargo", $chave_cargo);
		$query->execute();
		if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$chave_cargo  = $row["chave_cargo"];
			$descr_cargo = $row["descr_cargo"];
			$cbo_cargo = $row["cbo_cargo"];
			$esocial_cargo = $row["esocial_cargo"];		
			$sit_cargo = $row["sit_cargo"];
			$descrdetalhada_cargo = $row["descrdetalhada_cargo"];		
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
	//**** Carrega opções do select de situações de cargos
	//********************
	//********************
	$html_sit_cargo = "";
	$html_sit_cargo = $html_sit_cargo . "<option value='VIGENTE' " . (($sit_cargo == "VIGENTE") ? ' selected="selected"' : "") . ">Vigente</option>";
	$html_sit_cargo = $html_sit_cargo . "<option value='REVOGADO' " . (($sit_cargo == "REVOGADO") ? ' selected="selected"' : "") . ">Revogado</option>";
  //********************
	//********************
	//**** EOF - Carrega opções do select de situações de cargos
	//********************
	//********************

	//********************
	//********************
	//**** Define título da página (e links relacionados)
	//********************
	//********************
	if ($chave_cargo <= 0) {
		$pagina_titulo = "Inclusão do Cargo";
	}
	else {
		$pagina_titulo = "Alteração do Cargo";
	}	
	$html_submenu = "";
  $html_submenu .= '<div class="row mb-1">' . "\n";
  $html_submenu .= '  <div class="col-12">' . "\n";
  $html_submenu .= '    <h5>Cadastro de Cargos</h5>' . "\n";
	$html_submenu .= '  </div>' . "\n";
	$html_submenu .= '</div>' . "\n";
	$html_submenu .= '<div class="row mb-1">' . "\n";
	$html_submenu .= '  <div class="col-12">' . "\n";
	$html_submenu .= '    <a href="' . $Home . '" class="btn btn-sm btn-custom2"><i class="fa-solid fa-arrow-left"></i> Voltar</a>' . "\n";
	if ($chave_cargo > 0) {	
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO_EXC" data-id="' . $chave_cargo . '" data-campo-id="chave_cargo" data-tabela-id="tcargo" data-caixa-id="caixa_cargo" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC"><i class="fa-regular fa-trash-can"></i> Excluir</a>' . "\n";
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO" data-id="' . $chave_cargo . '" data-campo-id="chave_cargo" data-tabela-id="tcargo" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"><i class="fa-solid fa-database"></i> Diário</a>' . "\n";
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
<title>Guia Doméstico - Edição de Cargo</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
function submeter() {
	var vok = true;	
	if (document.FCARGO_EDITA.descr_cargo.value == "") {
		vok = false;
		alert("Nome inválido.");
		document.FCARGO_EDITA.descr_cargo.focus();
	}
	if (vok) {
		if (document.FCARGO_EDITA.cbo_cargo.value == "") {
			vok = false;
            alert("E-mail inválido.");
            document.FCARGO_EDITA.cbo_cargo.focus();
		}
	}
	if (vok) {
		if (document.FCARGO_EDITA.esocial_cargo.value == "") {
			vok = false;
            alert("Senha inválida.");
            document.FCARGO_EDITA.esocial_cargo.focus();
		}   
	}
	if (vok) {
		document.FCARGO_EDITA.submit();
	}
};

$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "cargo.php";
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
              <form method="POST" action="<?php echo $Raiz ?>sistema/cargo/cargo_edita.php" id="FCARGO_EDITA" name="FCARGO_EDITA">
                <input type="hidden" id="chave_cargo" name="chave_cargo" value="<?php echo $chave_cargo ?>">
                <input type="hidden" id="acao_cargo" name="acao_cargo" value="GRAVAR">
                <div class="row mb-1">
                  <label for="descr_cargo" class="col-sm-2">Descrição</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="descr_cargo" name="descr_cargo" placeholder="Informe a descrição do cargo" value="<?php echo $descr_cargo ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="cbo_cargo" class="col-sm-2">CBO</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="cbo_cargo" name="cbo_cargo" placeholder="Informe o Código Brasileiro de Ocupação" value="<?php echo $cbo_cargo ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="esocial_cargo" class="col-sm-2">E-social</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="esocial_cargo" name="esocial_cargo" placeholder="Informe o número do E-social" value="<?php echo $esocial_cargo ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="descrdetalhada_cargo" class="col-sm-2">Descrição Detalhada</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="descrdetalhada_cargo" name="descrdetalhada_cargo" placeholder="Informe a descrição detalhada do cargo" rows="3"><?php echo $descrdetalhada_cargo ?></textarea>            
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="sit_cargo" class="col-sm-2">Situação</label>
                  <div class="col-sm-10">
                    <select class="form-select form-select-sm" id="sit_cargo" name="sit_cargo">
                      <?php echo $html_sit_cargo; ?>
                    </select>
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