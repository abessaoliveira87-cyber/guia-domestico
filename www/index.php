<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: index.php
***** Conteúdo: Home page
***** Data criação: abr/2026
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
$Raiz = "";
include($Raiz . "conexao/db.php");
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
//VerificaSessao();
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Home</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow" style="padding-bottom:80px;">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="row">
            <div class="col-sm-12 rounded mb-4" style="background-color:#EFF6FF">
              <span style="color:#2563EB">SEU GUIA DE DIREITOS</span>
            </div>
            <div class="col-sm-12 mb-5">
              <span class="manchete">
                Trabalhador Doméstico, fique por dentro dos seus <span class="texto-regular">direitos e deveres</span>. 
              </span>
            </div>
            <div class="col-sm-12 mb-4">
              <span class="texto-corpo">
                Cadastre-se agora de forma simples e 100% gratuita para ter um diagnóstico rápido e personalizado sobre sua situação trabalhista.
              </span>
            </div>
            <div class="col-sm-12">
              <button type="button" id="BTN_CADASTRO" name="BTN_CADASTRO" class="btn btn-lg btn-custom" onClick="javascript:location='/publico/usuario/usuario_cadastro.php';" style="width:100%">Cadastre-se agora</button>
            </div>
            <div class="col-sm-12 mt-4 text-center">
              <a class="link-padrao" href="/publico/usuario/usuario_login.php"><span>Já tem cadastro? Faça seu login</span></a>
            </div>
            <div class="col-sm-12 mt-4 mb-4">
              <div class="row">
                <div class="col-sm-4 text-center">
                  <i class="fa-solid fa-check" style="color:#34D399"></i>100% Gratuito
                </div>
                <div class="col-sm-4 text-center">
                  <i class="fa-solid fa-certificate" style="color:#34D399"></i>Dados protegidos
                </div>
                <div class="col-sm-4 text-center">
                  <i class="fa-solid fa-bolt" style="color:#34D399"></i>Resultado imadiato
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="row">
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_leiemprego.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #FFF7ED;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-tree fa-lg" style="color:#F97316;"></i>
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_leiemprego.php"><h5>Lei do emprego</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_leiemprego.php">Conheça a Lei 150/2015 e seus principais pilares.</a>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_folhapagto.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #EFF6FF;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-dollar-sign fa-lg" style="color:#3B82F6;"></i>                  
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_folhapagto.php"><h5>Folha de pagamento</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_folhapagto.php">Cálculos de descontos, horas extras e adicional noturno.</a>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_ferias13.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #F0FDF4;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-regular fa-calendar fa-lg" style="color:#22C55E;"></i>                  
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_ferias13.php"><h5>Férias e 13º</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_ferias13.php">Saiba quando e quanto você deve receber.</a>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_fgtsinss.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #FEF2F2;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-building-columns fa-lg" style="color:#F87171;"></i>
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_fgtsinss.php"><h5>INSS e FGTS</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_fgtsinss.php">Entenda os recolhimentos e sua segurança previdenciária.</a>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_rescisao.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #FAF5FF;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-file-lines fa-lg" style="color:#A855F7;"></i>
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_rescisao.php"><h5>Rescisão</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_rescisao.php">Pedido de demissão ou dispensa: seus direitos em cada caso.</a>
                </div>
              </div>
            </div>
            <div class="col-sm-6 mb-5">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_auxdoenca.php">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color: #ECFDF5;  display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-square-plus fa-lg" style="color:#10B981;"></i>                  
                  </div>
                  </a>
                  <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_auxdoenca.php"><h5>Auxílio-Doença</h5></a>
                  <a class="sem-decoracao texto-corpo texto-suave" href="/publico/diagnostico/diagnostico_auxdoenca.php">Como proceder em casos de afastamento por saúde.</a>
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