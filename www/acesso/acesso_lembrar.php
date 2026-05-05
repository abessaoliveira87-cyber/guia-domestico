<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /acesso/acesso_lembrar.php
***** Conteúdo: Lembrar senha do usuário
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
$ambiente = "DESENVOLVIMENTO";
$idlogin = "";
if (isset($_GET['ID'])) {
  $idlogin = $_GET['ID'];
}
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Guia Doméstico - Lembrar Senha</title>  
  <?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function submeter() {
    var vok = true;
    if (document.FACESSO_LEMBRAR.EMAIL.value.trim() == "") {
      vok = false;
      alert("E-mail inválido.");
      document.FACESSO_LEMBRAR.EMAIL.focus();
    }
    if (vok == true) {
      document.FACESSO_LEMBRAR.submit();
    }
  }

  function avaliatecla() {
    if (window.event && window.event.keyCode == 13) {
      submeter();
    }
  }

  function validaRecaptcha() {
    document.getElementById('BTN_SUBMETER').disabled = false;
  }
</script>

<body>
  <script src='https://www.google.com/recaptcha/api.js' async defer></script>
  <?php include($Raiz . "include/php/menu.php"); ?>
  <div class="container">
    <div class="row">
      <div class="col-4">
      </div>
      <div class="col">
        <form class="form-horizontal ml-2 mr-2" method="post" id="FACESSO_LEMBRAR" name="FACESSO_LEMBRAR"
          action="acesso_lembrar_post.php">
          <div class="mb-4 text-center">
            <h1 class="text-center">Relembrar senha</h1>
            <p>Informe seu e-mail</p>
          </div>
          <div class="form-group mb-2">
            <input type="text" class="form-control" id="EMAIL" name="EMAIL" placeholder="E-mail" maxlength="128"
              value="<?php echo $idlogin ?>">
          </div>
          <div class="form-group mb-2">
            <div class="g-recaptcha" data-callback="validaRecaptcha"
              data-sitekey="6LfAv34gAAAAADnji_budTVdGyVJ5jlDAmf6kH9u">
            </div>
          </div>
          <div class="form-group mb-2">
            <button type="button" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-info"
              onClick="javascript:submeter();" style="width:100%" <?php echo (($ambiente == "DESENVOLVIMENTO") ? "" : " disabled"); ?>>Enviar</button>
          </div>
          <div class="form-group mb-2">
            <p>
              <a href="<?php echo $Raiz; ?>acesso/index.php">Voltar para tela de login</a>
            </p>
          </div>
        </form>
      </div>
      <div class="col-4">
      </div>
    </div>
  </div>
  <?php include($Raiz . "include/php/rodape.php"); ?>
  <script type="text/javascript">
    document.FACESSO_LEMBRAR.EMAIL.focus();
  </script>
</body>

</html>