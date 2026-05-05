<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: acesso.php
***** Conteúdo: Login de usuário
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
$Raiz = "../";
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
// Variáveis de controle
$ok = true;
$html = "";
$html_tit = "";
$html_msg = "";
$html_lnk = "";
$html_btn = "";
$email_usuario = "";
// EOF Variáveis de controle
$aftergopage = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  // Captura formulário
  $email_usuario = $_POST["EMAIL_USUARIO"];
  $senha_usuario = $_POST["SENHA_USUARIO"];
  $aftergopage = $_POST["AFTERGOPAGE"];
  $sit_usuario = "ATIVO";
  // EOF Captura formulário
  // Variáveis de login
  $url_redirec = "";
  // EOF Variáveis de login
  // Recaptcha
  $ok = true;
  //	$ok = Recaptcha();
  if ($ok == false) {
    $html_tit = "Falha no login";
    $html_msg = 'Clique "não sou um robô" antes de confirmar o login.';
    $html_lnk = 'javascript:location.href=\'' . $Raiz . 'acesso/acesso.php\'';
    $html_btn = 'Tentar novamente';
  }
  // EOF Recaptcha	
  // Tenta logar como representante/administrador
  if ($ok) {
    $ok = false;
    abre_db();
    $strsql = "
		select     
		tusuario.*
		from  
		tusuario		
		where  
		tusuario.email_usuario = :vemail_usuario and 
		tusuario.senha_usuario = :vsenha_usuario and 
		tusuario.sit_usuario = 'ATIVO' and 
		tusuario.caixa_usuario = 1
		";
    $qusuario = $pdo->prepare($strsql);
    $qusuario->bindParam(":vemail_usuario", $email_usuario);
    $qusuario->bindParam(":vsenha_usuario", $senha_usuario);
    $qusuario->execute();
    if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {
      $_SESSION["CHAVE_USUARIO"] = $tusuario['chave_usuario'];
      $_SESSION["NOME_USUARIO"] = $tusuario['nome_usuario'];
      $_SESSION["LOGIN_USUARIO"] = $tusuario['email_usuario'];
      $_SESSION["TIPO_USUARIO"] = $tusuario['tipo_usuario'];
      $_SESSION["CHAVE_USUARIOLOGIN"] = $tusuario['chave_usuario'];
      $_SESSION["NOME_USUARIOLOGIN"] = $tusuario['nome_usuario'];
      $_SESSION["LOGIN_USUARIOLOGIN"] = $tusuario['email_usuario'];
      $_SESSION["SESSAO_INICIO"] = time(); // INICIO DA ATIVIDADE DA SESSAO
      $_SESSION["SESSAO_EXPIRA"] = 28800; // SEGUNDOS SEM ATIVIDADE (8 HORAS)

      $_SESSION["CHAVE_CARGO"] = $tusuario['chave_cargo'];
      $_SESSION["SALARIO_USUARIO"] = $tusuario['salario_usuario'];
      $_SESSION["DTI_USUARIO"] = $tusuario['dti_usuario'];
      $_SESSION["HRDIA_USUARIO"] = $tusuario['hrdia_usuario'];
      $_SESSION["DIASEMANA_USUARIO"] = $tusuario['diasemana_usuario'];

      $url_redirec = $Raiz . "sistema/index.php";
      $ok = true;
    }
  }
  // EOF Tenta logar como representante/administrador
  fecha_db();
  if ($ok) {
    header("Location: " . $Raiz . $url_redirec);
    die();
  }
  else {
    $html_tit = 'Falha no login';
    $html_msg = 'Usuário ou senha inválida.';
    $html_lnk = 'javascript:location.href=\'' . $Raiz . 'acesso/acesso.php\'';
    $html_btn = 'Tentar novamente';
  }
}
if ($ok) {
  $html_tit = 'Guia Doméstico';
  $html_msg = 'Login';
  $html_lnk = 'javascript:location.href=\'' . $Raiz . 'acesso/acesso.php\'';
  $html_btn = 'Tentar novamente';
}
// Corpo
if ($ok) {

  //	$verifica_recaptcha = true;
  $verifica_recaptcha = false;
  if (isset($_SESSION['AMBIENTE'])) {
    if ($_SESSION['AMBIENTE'] === "DESENVOLVIMENTO") {
      $verifica_recaptcha = false;
    }
  }
  $html .= '
	<form method="post" id="FACESSO_POST" name="FACESSO_POST" action="acesso.php">
    <input type="hidden" id="AFTERGOPAGE" name="AFTERGOPAGE" value="' . $aftergopage . '">
    <div class="form-group mb-2">
      <label for="EMAIL_USUARIO" class="form-label">E-mail</label>
      <input type="text" class="form-control" id="EMAIL_USUARIO" name="EMAIL_USUARIO" placeholder="E-mail" maxlength="128" value="' . $email_usuario . '">
    </div>
    <div class="form-group mb-4">
      <label for="SENHA_USUARIO" class="form-label">Senha</label>
      <input type="password" class="form-control" name="SENHA_USUARIO" id="SENHA_USUARIO" maxlength="20" placeholder="Senha" onKeyPress="javascript:avaliatecla();" />
    </div>
    <div class="form-group mb-4">
      <button type="button" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom" onClick="javascript:submeter();" style="width:100%"' . ($verifica_recaptcha == false ? "" : " disabled") . '>Entrar</button>
    </div>
	  <script type="text/javascript">
      document.FACESSO_POST.EMAIL_USUARIO.focus();
	  </script>
	';
  if (isset($_SESSION["CHAVE_USUARIO"])) {
    if ($_SESSION["CHAVE_USUARIO"] != "") {
      $html = $html . '
			<div class="form-group mb-2">
			  <button type="button" id="BTN_SAIR" name="BTN_SAIR" class="btn btn-danger" onclick="javascript:location=\'' . $Raiz . 'acesso/logout.php\';" style="width:100%">Sair</button> 
			</div>
			';
    }
  }
  $html .= '
	    <div class="form-group mb-4 text-center">
  	    <a class="link-padrao" href="' . $Raiz . 'acesso/acesso_lembrar.php">Esqueci minha senha</a>
      </div>	    
    </form>
	';
}
else {
  $html = '
	<div class="text-center">
	<h1>' . $html_tit . '</h1>			
	<p>' . $html_msg . '</p>
	<br />
      <button type="button" class="btn btn-primary mb-2" onclick="' . $html_lnk . '">' . $html_btn . '</button>
	</div>
	<br />';
}
// EOF Corpo

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>Guia Doméstico - Login do Administrador</title>
  <?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function submeter() {
    var vok = true;
    if (document.FACESSO_POST.EMAIL_USUARIO.value.trim() == "") {
      vok = false;
      alert("E-mail inválido.");
      document.FACESSO_POST.EMAIL_USUARIO.focus();
    }
    if (vok == true) {
      if (document.FACESSO_POST.SENHA_USUARIO.value.trim() == "") {
        vok = false;
        alert("Senha inválida.");
        document.FACESSO_POST.SENHA_USUARIO.focus();
      }
    }
    if (vok == true) {
      setcookie("GuiaDomesticoUsr", document.FACESSO_POST.EMAIL_USUARIO.value.trim(), 30);
      setcookie("GuiaDomesticoPsw", document.FACESSO_POST.SENHA_USUARIO.value.trim(), 30);
      document.FACESSO_POST.submit();
    }
  }

  function avaliatecla() {
    if (window.event && window.event.keyCode == 13) {
      submeter();
    }
  }

  function acesso_tentarnovamente() {
    window.location.href = '/acesso/acesso.php';
  }

  function getcookie() {
    var cNomeCookie = "GuiaDomesticoUsr" + "=";
    var cDecodedCookie = decodeURIComponent(document.cookie);
    var aCookies = cDecodedCookie.split(';');
    var cCookie = "";
    var i = 0;
    var vUsr = "";
    var vPsw = "";
    for (i = 0; i < aCookies.length; i++) {
      cCookie = aCookies[i];
      while (cCookie.charAt(0) == ' ') {
        cCookie = cCookie.substring(1);
      }
      if (cCookie.indexOf(cNomeCookie) == 0) {
        vUsr = cCookie.substring(cNomeCookie.length, cCookie.length);
        break;
      }
    }
    cNomeCookie = "GuiaDomesticoPsw" + "=";
    cDecodedCookie = decodeURIComponent(document.cookie);
    aCookies = cDecodedCookie.split(';');
    cCookie = "";
    for (i = 0; i < aCookies.length; i++) {
      cCookie = aCookies[i];
      while (cCookie.charAt(0) == ' ') {
        cCookie = cCookie.substring(1);
      }
      if (cCookie.indexOf(cNomeCookie) == 0) {
        vPsw = cCookie.substring(cNomeCookie.length, cCookie.length);
        break;
      }
    }
    document.FACESSO_POST.EMAIL_USUARIO.value = vUsr;
    document.FACESSO_POST.SENHA_USUARIO.value = vPsw;
    return "";
  }

  function setcookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/acesso/";
  }
</script>

<body onload='javascript:getcookie()'>
  <script src='https://www.google.com/recaptcha/api.js' async defer></script>
  <?php include($Raiz . "include/php/menu.php"); ?>
  <div class="container">    
    <div class="card shadow" style="padding-bottom:80px;">
      <div class="card-body">
        <div class="row">
          <div class="col mb-4 text-center">
            <img src="/design/icone.png" class="rounded shadow" style="max-width:64px;" />      
          </div>
        </div>  
        <div class="row">
          <div class="col mb-1 text-center">
            <h1 class="mt-10"><small>Login Administrador</small></h1>
          </div>  
        </div>
        <div class="row">
          <div class="col mb-3 text-center">
            <span>Ferramenta exclusiva para uso do Administrador.</span>
          </div>
        </div>      
        <div class="row">
          <div class="col mx-auto mb-4" style="max-width:500px">
            <button type="button" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-light" onclick="javascript=location='/index.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
          </div>
        </div>
        <div class="row">
          <div class="col mx-auto" style="max-width:500px">
            <div class="card shadow">
              <div class="card-body">
                <?php echo $html; ?>
              </div>
            </div>
          </div>         
        </div>        
      </div>
    </div>
    <?php include($Raiz . "include/php/rodape.php"); ?>
    <script type="text/javascript">
      function validaRecaptcha() {
        document.getElementById('BTN_SUBMETER').disabled = false;
      }
    </script>
  </div>
</body>
</html>