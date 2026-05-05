<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/admin/admin_edita.php
***** Conteúdo: Edição do Administrador
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
$Home = $Raiz . "sistema/admin/admin.php";
$ok = true;
$link_main = $Home;
VerificaSessao();
VerificaAdmin($Raiz);
if ($_SERVER['REQUEST_METHOD'] != "POST") {
	$ok = false;
}
if (!isset($_POST["chave_usuario"])) {
	$ok = false;
}
if (!$ok) {
	header("Location: " . $Home);
	die();
}
$pagina_titulo = "Inclusão do Administrador";
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = $_POST["acao_usuario"];
}
if ($Acao == "GRAVAR") {
	$chave_usuario = $_POST["chave_usuario"];
	$nome_usuario = $_POST["nome_usuario"];
	$email_usuario = $_POST["email_usuario"];
	$senha_usuario = $_POST["senha_usuario"];
  $sit_usuario = $_POST["sit_usuario"];
	$tipo_usuario = 'ADMINISTRADOR';
	abre_db();
	if ($chave_usuario == "0") {
		// Inclusão do registro	   
		$strsql = "
		insert 
		into 
		tusuario 
		(nome_usuario
		,email_usuario
		,senha_usuario
		,tipo_usuario
		,sit_usuario
		) values 
		(:vnome_usuario
		,:vemail_usuario
		,:vsenha_usuario
		,:vtipo_usuario
		,:vsit_usuario
		)";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vnome_usuario", $nome_usuario);
		$qusuario->bindParam(":vemail_usuario", $email_usuario);
		$qusuario->bindParam(":vsenha_usuario", $senha_usuario);
		$qusuario->bindParam(":vtipo_usuario", $tipo_usuario);
		$qusuario->bindParam(":vsit_usuario", $sit_usuario);
		$qusuario->execute();
		$chave_usuario = $pdo->lastInsertId();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario = array(), $campoexcluidos = array("dta_usuario"));
		// EOF Inclusao do registro	   
	}
	else {
		// Alteção do registro	   
		$abre_diario = abre_diario("tusuario", "chave_usuario", $chave_usuario, $campoexcluidos = array("dta_usuario"));
		$strsql = "
		update 
		tusuario 
		set
		nome_usuario = :vnome_usuario
		,email_usuario = :vemail_usuario
		,senha_usuario = :vsenha_usuario
		,tipo_usuario = :vtipo_usuario
		,sit_usuario = :vsit_usuario
		where 
		chave_usuario = :vchave_usuario
		";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vchave_usuario", $chave_usuario);
		$qusuario->bindParam(":vnome_usuario", $nome_usuario);
		$qusuario->bindParam(":vemail_usuario", $email_usuario);
		$qusuario->bindParam(":vsenha_usuario", $senha_usuario);
		$qusuario->bindParam(":vtipo_usuario", $tipo_usuario);
		$qusuario->bindParam(":vsit_usuario", $sit_usuario);
		$qusuario->execute();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario, $campoexcluidos = array("dta_usuario"));
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
	$chave_usuario = "0";
	$nome_usuario = "";
	$email_usuario = "";
	$senha_usuario = "";
	$sit_usuario = "ATIVO";
	$tipo_usuario = "";
	if ($ok) {
		if (!isset($_POST["chave_usuario"])) {
			$ok = false;
		}
	}
	if ($ok) {
		$chave_usuario = $_POST["chave_usuario"];
	}
	if ($ok) {
		if (!is_numeric($chave_usuario)) {
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
	$chave_usuario = intval($chave_usuario);
	if ($chave_usuario > 0) {
		$strsql = "
		select 
		tusuario.* 	
		from 
		tusuario 
		where 
		tusuario.chave_usuario = :vchave_usuario";
		$query = $pdo->prepare($strsql);
		$query->bindParam(":vchave_usuario", $chave_usuario);
		$query->execute();
		if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$chave_usuario  = $row["chave_usuario"];
			$nome_usuario = $row["nome_usuario"];
			$email_usuario = $row["email_usuario"];
			$senha_usuario = $row["senha_usuario"];		
			$sit_usuario = $row["sit_usuario"];
			$tipo_usuario = $row["tipo_usuario"];		
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
	//**** Carrega opções do select de situações de usuários
	//********************
	//********************
	$html_sit_usuario = "";
	$html_sit_usuario = $html_sit_usuario . "<option value='ATIVO' " . (($sit_usuario == "ATIVO") ? ' selected="selected"' : "") . ">Ativo</option>";
	$html_sit_usuario = $html_sit_usuario . "<option value='INATIVO' " . (($sit_usuario == "INATIVO") ? ' selected="selected"' : "") . ">Inativo</option>";
  $html_sit_usuario = $html_sit_usuario . "<option value='EXCLUIDO' " . (($sit_usuario == "EXCLUIDO") ? ' selected="selected"' : "") . ">Conta excluída</option>";
	//********************
	//********************
	//**** EOF - Carrega opções do select de situações de usuários
	//********************
	//********************

	//********************
	//********************
	//**** Define título da página (e links relacionados)
	//********************
	//********************
	if ($chave_usuario <= 0) {
		$pagina_titulo = "Inclusão do Administrador";
	}
	else {
		$pagina_titulo = "Alteração do Administrador";
	}	
	$html_submenu = "";
  $html_submenu .= '<div class="row mb-1">' . "\n";
  $html_submenu .= '  <div class="col-12">' . "\n";
  $html_submenu .= '    <h5>Cadastro de Administradores do Portal</h5>' . "\n";
	$html_submenu .= '  </div>' . "\n";
	$html_submenu .= '</div>' . "\n";
	$html_submenu .= '<div class="row mb-1">' . "\n";
	$html_submenu .= '  <div class="col-12">' . "\n";
	$html_submenu .= '    <a href="' . $Home . '" class="btn btn-sm btn-custom2"><i class="fa-solid fa-arrow-left"></i> Voltar</a>' . "\n";
	if ($chave_usuario > 0) {	
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO_EXC" data-id="' . $chave_usuario . '" data-campo-id="chave_usuario" data-tabela-id="tusuario" data-caixa-id="caixa_usuario" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC"><i class="fa-regular fa-trash-can"></i> Excluir</a>' . "\n";
    $html_submenu .= '    <a class="btn btn-sm btn-custom2" href="#" id="SHOW_DIARIO" data-id="' . $chave_usuario . '" data-campo-id="chave_usuario" data-tabela-id="tusuario" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"><i class="fa-solid fa-database"></i> Diário</a>' . "\n";
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
<title>Guia Doméstico - Edição de Administrador do Portal</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
function submeter() {
	var vok = true;	
	if (document.FUSUARIO_EDITA.nome_usuario.value == "") {
		vok = false;
		alert("Nome inválido.");
		document.FUSUARIO_EDITA.nome_usuario.focus();
	}
	if (vok) {
		if (document.FUSUARIO_EDITA.email_usuario.value == "") {
			vok = false;
            alert("E-mail inválido.");
            document.FUSUARIO_EDITA.email_usuario.focus();
		}
	}
	if (vok) {
		if (document.FUSUARIO_EDITA.senha_usuario.value == "") {
			vok = false;
            alert("Senha inválida.");
            document.FUSUARIO_EDITA.senha_usuario.focus();
		}   
	}
	if (vok) {
		document.FUSUARIO_EDITA.submit();
	}
};

$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "admin.php";
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
              <form method="POST" action="<?php echo $Raiz ?>sistema/admin/admin_edita.php" id="FUSUARIO_EDITA" name="FUSUARIO_EDITA">
                <input type="hidden" id="chave_usuario" name="chave_usuario" value="<?php echo $chave_usuario ?>">
                <input type="hidden" id="acao_usuario" name="acao_usuario" value="GRAVAR">
                <div class="row mb-1">
                  <label for="nome_usuario" class="col-sm-2">Nome</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="nome_usuario" name="nome_usuario" placeholder="Nome do Administrador" value="<?php echo $nome_usuario ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="email_usuario" class="col-sm-2">E-mail</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="email_usuario" name="email_usuario" placeholder="E-mail" value="<?php echo $email_usuario ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="senha_usuario" class="col-sm-2">Senha</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control form-control-sm" id="senha_usuario" name="senha_usuario" placeholder="Senha de acesso" value="<?php echo $senha_usuario ?>">
                  </div>
                </div>
                <div class="row mb-1">
                  <label for="sit_usuario" class="col-sm-2">Situação</label>
                  <div class="col-sm-10">
                    <select class="form-select form-select-sm" id="sit_usuario" name="sit_usuario">
                      <?php echo $html_sit_usuario; ?>
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