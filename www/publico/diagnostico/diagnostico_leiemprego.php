<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_leiemprego.php
***** Conteúdo: Lei do Emprego Doméstico
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
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = "DIAGNOSTICAR";
}
if ($Acao == "DIAGNOSTICAR") {
	//$chave_usuario = $_POST["chave_usuario"];
	//$nome_usuario = $_POST["nome_usuario"];
	//$email_usuario = $_POST["email_usuario"];
	//$senha_usuario = $_POST["senha_usuario"];
  //$sit_usuario = $_POST["sit_usuario"];
	//$tipo_usuario = 'ADMINISTRADOR';

	header("Location: /publico/diagnostico/diagnostico_menu.php");
	die();
}

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Lei do Emprego Doméstico</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-3 pe-3">
      <div class="col">
        <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light texto-menor" onclick="javascript:location='/publico/diagnostico/diagnostico_menu.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-12">
        <h2>Lei do Emprego Doméstico</h2>
      </div>     
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-md-12 rounded">
        <div class="d-flex">
          <div class="pe-2">
            <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#afc2cf; float:left;">
              <i class="fa-solid fa-file-circle-check fa-2x" style="color:#1A5275;"></i>                                                          
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-center">
            <h4 style="margin:0px;">Visão Geral da LC 150/2015</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-md-12">
        <div class="rounded border ps-3 pe-3 pt-3" style="background-color:#FFF">
          <p class="texto-corpo">A Lei Complementar nº 150, de 1º de junho de 2015, representou um marco histórico ao regulamentar a Emenda Constitucional nº 72/2013 (conhecida como a PEC das Domésticas).</p>
          <p class="texto-corpo">Esta legislação equiparou os direitos dos empregados domésticos aos demais trabalhadores urbanos e rurais, instituindo o regime unificado de pagamento de tributos e contribuições (Simples Doméstico) e estabelecendo regras claras para a jornada de trabalho e benefícios previdenciários.</p>
        </div>
      </div>
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-md-12 rounded">
        <div class="d-flex">
          <div class="pe-2">
            <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#afc2cf; float:left;">              
              <i class="fa-solid fa-shield-halved fa-2x" style="color:#1A5275;"></i>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-center">
            <h4 style="margin:0px;">Direitos e Deveres Básicos</h4>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col">
        <div class="d-flex justify-content-center">
          <div class="row">
          <div class="col-md-4">
            <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="width:300px; background-color:#FFF;">
              <div class="rounded-circle text-center mb-3" style="max-width:48px; min-height:48px; background-color:#afc2cf; display: flex; justify-content: center; align-items: center;">
                <i class="fa-regular fa-user fa-2x" style="color:#1A5275;"></i>                
              </div>
              <h4>Registro Formal</h4>
              <span>É obrigatória a anotação na Carteira de Trabalho (CTPS) desde o primeiro dia de serviço, especificando cargo, salário e jornada.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="width:300px; background-color:#FFF;">
              <div class="rounded-circle text-center mb-3" style="max-width:48px; min-height:48px; background-color:#afc2cf; display: flex; justify-content: center; align-items: center;">
                <i class="fa-solid fa-money-bill-1 fa-2x" style="color:#1A5275;"></i>
              </div>
              <h4>Salário Mínimo</h4>
              <span>Garantia de remuneração não inferior ao salário mínimo federal ou ao piso regional fixado por lei estadual.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="width:300px; background-color:#FFF;">
              <div class="rounded-circle text-center mb-3" style="max-width:48px; min-height:48px; background-color:#afc2cf; display: flex; justify-content: center; align-items: center;">
                <i class="fa-regular fa-clock fa-2x" style="color:#1A5275;"></i>                                
              </div>
              <h4>Jornada de Trabalho</h4>
              <span>Limite de 44 horas semanais e 8 horas diárias, com pagamento de horas extras e controle obrigatório de ponto.</span>
            </div>            
          </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4 ps-5 pe-5">
      <div class="col-12">
        <div class="row ps-5 pe-5">
          <div class="col-md-12 rounded pt-4" style="background-color:#f4f1f3; min-height: 100px;">
            <div class="row mb-2">
              <div class="col-md-8">
                <h5><i class="fa-solid fa-arrows-rotate fa-lg" style="color:#1A5275"></i>&nbsp;A Importância do empregador pagar a Guia do eSocial</h5>
                <span class="texto-corpo">Fique Atento! O empregador tem que pagar a guia do eSocial todo mes ate o dia 20 do mes seguinte. O pagamento em dia te garante o deposito do FGTS corretamente e tambem os direitos trabalhistas como auxilio doença, licenças maternidade entre outros que são concecididos com a sua contribuição previdenciaria que o empregador repassa para a previdencia atraves do pagamento da guia mensal.</span>
              </div>
              <div class="col-md-4">
                <div class="rounded border ps-3 pe-3 pt-3 pb-3 text-center" style="min-width:300px; background-color:#FFF;">
                  <p class="texto-negrito" style="color: #EC5B13; margin:0px;">ATENÇÃO</p>
                  <span class="texto-menor texto-negrito">Você pode conferir todo mes no seu saldo FGTS se está ocorrendo o deposito que é mensal, caso nao ocorra até o dia 30. Informe seu Empregador.</span>
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