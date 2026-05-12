<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_auxdoenca.php
***** Conteúdo: Auxílio-Doença e Afastamento
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
<title>Guia Doméstico - Auxílio Doença e Afastamento</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-3 pe-3">
      <div class="col-sm-12">
        <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light texto-menor" onclick="javascript:location='/publico/diagnostico/diagnostico_menu.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <h2>Auxílio-Doença e Afastamento</h2>
      </div>     
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-sm-12">
        <h4 style="margin:0px;"><i class="fa-solid fa-circle-info fa-lg" style="color:#1A5275;"></i>&nbsp;O que é o Auxílio-Doença?</h4>        
      </div>
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-sm-12">
        <span class="texto-corpo">O auxílio-doença, formalmente conhecido como auxílio por incapacidade temporária, é um benefício previdenciário concedido ao segurado do INSS que comprove, por meio de perícia médica, estar temporariamente incapaz para o seu trabalho habitual por mais de 15 dias consecutivos.</span>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <div class="rounded border ps-3 pe-3 pt-3" style="background-color:#FFF">
          <h5 class="texto-regular"><i class="fa-regular fa-calendar fa-lg" style="color:#1A5275;"></i>&nbsp;Regras de Afastamentos no emprego doméstico</h5>
          <p class="texto-corpo">Diferente das empresas privadas, no emprego doméstico, a responsabilidade pelo pagamento dos primeiros 15 dias de atestado vai depender do total de dias de atestados. Se o 
            atestado junto ou somado (últimos 60 dias) tiver mais de 15 dias , é considerado afastamento, ai não há pagamento pelo empregador dos 15 dias iniciais, é o INSS que assume o benefício desde o 1º dia de afastamento se deferido pelo INSS.</p>          
        </div>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <div class="rounded border ps-3 pe-3 pt-3" style="background-color:#000">
          <div class="row">
            <div class="col-sm-6">
              <h5 class="pb-3" style="color:#FFF">Cálculo do Benefício</h5>
            </div>
          </div>
          <div class="row pb-2">
            <div class="col-sm-6" style="color:#CBD5E1; border-right: 1px dashed #888;">
              <p class="texto-corpo" style="color:#CBD5E1;">O valor do auxílio-doença corresponde a vários fatores de remuneração dos últimos meses e quem faz esta avaliação é o INSS, nao o empregador , mas é referente  aos últimos pagamentos declarados no eSocial. Se houver algum erro precisa pedir o empregador para corrigir o quanto antes.</p>
            </div>
            <div class="col-sm-6">
              <p class="texto-corpo texto-menor" style="color:#CBD5E1; font-style: italic;">"O valor incide na remumeração mensal + férias + 13Salário ou seja, o periodo que ficar afastado nao conta para férias ou 13Sálario. * Se ficar mais de 180 dias afastados perde o periodo de férias vigente em andamento, não os ja vencidos."</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <h4>Como solicitar via 'Meu INSS'</h4>
      </div>
    </div>
    <div class="row ps-3 pe-3">
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-1 mt-2">
            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; background-color: #1A5275;">
              <span class="texto-branco-regular">1</span>
            </div>
          </div>
          <div class="col-sm-11 mt-2 mb-3">
            <span class="texto-negrito">Acesse o Portal</span><br>
            <span class="texto-suave texto-menor">Entre no site ou aplicativo <strong>Meu INSS</strong> com seu CPF e senha Gov.br.</span>
          </div>
        </div>        
        <div class="row">
          <div class="col-sm-1 mt-2">
            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; background-color: #1A5275;">
              <span class="texto-branco-regular">2</span>
            </div>
          </div>
          <div class="col-sm-11 mt-2 mb-3">
            <span class="texto-negrito">Agende a Perícia</span><br>
            <span class="texto-suave texto-menor">Clique em "<strong>Pedir Benefício por Incapacidade</strong>" e selecione "Auxílio-doença".</span>
          </div>
        </div>        
      </div>    
      <div class="col-sm-6">
        <div class="row">
          <div class="col-sm-1 mt-2">
            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; background-color: #1A5275;">
              <span class="texto-branco-regular">3</span>
            </div>
          </div>
          <div class="col-sm-11 mt-2 mb-3">
            <span class="texto-negrito">Envie Documentação</span><br>
            <span class="texto-suave texto-menor">Anexe o atestado médico legível, documento de identidade e comprovante de residência.</span>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-1 mt-2">
            <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; background-color: #1A5275;">
              <span class="texto-branco-regular">4</span>
            </div>
          </div>
          <div class="col-sm-11 mt-2 mb-3">
            <span class="texto-negrito">Acompanhe o Pedido</span><br>
            <span class="texto-suave texto-menor">Fique atento às notificações no app para saber o local e hora da perícia presencial (se necessário).</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>