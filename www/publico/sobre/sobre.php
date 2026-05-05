<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/sobre/sobre.php
***** Conteúdo: Sobre o conteúdo do Site
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
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
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
<title>Guia Doméstico - Como funciona o diagnóstico</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="card-body">
      <div class="row">
        <div class="col text-center mb-5">
          <h2 class="mt-5 mb-3">Como funciona o diagnóstico</h2>
          <p class="texto-corpo">Em apenas 3 passos você terá um relatório completo em PDF com todos os seus dados estruturados.</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-10">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-pen-to-square fa-2x" style="color:#F97316;"></i>                
                  </div>
                  <h3>Preencha seus dados</h3>
                  <span class="texto-corpo">Insira cargo, salário e jornada de trabalho para demonstrações reais.</span>
                </div>
              </div>
            </div>
            <div class="col-md-2" style="display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-arrow-right fa-2x"></i>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-chart-column fa-2x" style="color:#22C55E;"></i>
                  </div>
                  <h3>Gere o dignóstico</h3>
                  <span class="texto-corpo">Nosso sistema processa as informações conforme a legislação atualizada.</span>
                </div>
              </div>            
            </div>
            <div class="col-md-1">
            </div>
          </div>
        </div>        
        <div class="col-md-4">
          <div class="row">
            <div class="col-md-2" style="display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-arrow-right fa-2x"></i>
            </div>            
            <div class="col-md-10">
              <div class="card shadow">
                <div class="card-body" style="min-height:150px;">
                  <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; display: flex; justify-content: center; align-items: center;">                
                    <i class="fa-solid fa-file-pdf fa-2x" style="color:#1E5C7B;"></i>
                  </div>
                  <h3>Baixe o PDF</h3>
                  <span class="texto-corpo">Leve o documento com você para consultas ou acionamentos legais.</span>
                </div>
              </div>
            </div>
        </div>
      </div>             
      <div class="row mt-5">
        <div class="col-md-12 text-center">
          <button type="button" id="BTN_CADASTRO" name="BTN_CADASTRO" class="btn btn-lg btn-custom" onClick="javascript:location='/publico/diagnostico/diagnostico_config.php';" style="width:50%">Vamos começar</button>      
        </div>
      </div>


    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>
