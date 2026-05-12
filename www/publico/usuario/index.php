<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/usuario/index.php
***** Conteúdo: Home page do usuário logado
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
include($Raiz . "conexao/db.php");
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes Globais PHP
VerificaSessao();
//********************
//********************
//**** Prepara ambiente para execuçao...incluir na pagina de login tambem.
//********************
//********************
if (!isset($_SESSION['AMBIENTE'])) {
	if (strtoupper($_SERVER['SERVER_NAME']) === "LOCALHOST") {
		$_SESSION['AMBIENTE'] = "DESENVOLVIMENTO";
	}
	else {
		$_SESSION['AMBIENTE'] = "PRODUCAO";
	}
}
//********************
//********************
//**** EOF Prepara ambiente para execuçao...incluir na pagina de login tambem.
//********************
//********************

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Página do usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow" style="padding-bottom:80px;">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-12 mb-5">
          <span style="font-family: 'Arial Black'; font-size: 48pt; line-height: 1">
          <h1>Olá, <?php echo $nome_usuario ?>.</h1>
          <h2>Seja bem-vindo ao Guia Doméstico</h2>
          </span>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12 mb-5">
          <span class="texto-corpo">
            Aqui você pode gerar um diagnóstico completo sobre seus direitos como trabalhador doméstico de forma rápida e segura.
          </span>
        </div>
      </div>
      <div class="row mb-5">
        <div class="col-sm-6">
          <div class="card shadow">
            <div class="card-body" style="min-height:150px;">
              <div class="rounded text-center mb-3" style="max-width:56px; min-height:56px; background-color: #F0FDF4; display: flex; justify-content: center; align-items: center;">
                <i class="fa-regular fa-square-check fa-2x" style="color:#22C55E;"></i>
              </div>
              <h2>Começar diagnóstico</h2>
              <span class="texto-corpo">Avalie sua situação trabalhista e descubra seus direitos em poucos minutos.</span>              
              <button type="button" id="BTN_DIAGNOSTICO" name="BTN_DIAGNOSTICO" class="btn btn-lg btn-custom mt-5 mb-5" style="width:100%;" onclick="javascript:location='/publico/diagnostico/diagnostico_config.php'">Começar diagnóstico</button>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card shadow">
            <div class="card-body" style="min-height:150px;">
              <div class="rounded text-center mb-3" style="max-width:56px; min-height:56px; background-color: #E9EFF2; display: flex; justify-content: center; align-items: center;">
                <i class="fa-solid fa-user-gear fa-2x" style="color:#1A5275;"></i>
              </div>
              <h2>Configuração da conta</h2>
              <span class="texto-corpo">Gerencie seus dados pessoais, documentos e preferências de acesso.</span>
              <button type="button" id="BTN_CONFIG" name="BTN_CONFIG" class="btn btn-lg btn-custom mt-5 mb-5" style="width:100%;" onclick="javascript:location='/publico/usuario/usuario_config.php'">Configurações</button>              
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card shadow">
            <div class="card-body" style="min-height:150px;">
              <div class="row">
                <div class="col-sm-12 mt-4 text-center">
                  <i class="fa-solid fa-user-shield fa-3x" style="color:#1A5275"></i>
                  <span style="font-size:2rem; font-weight:700">100% Seguro</span><br>
                  <span class="texto-corpo">Dados protegidos por criptografia.</span>                    
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>