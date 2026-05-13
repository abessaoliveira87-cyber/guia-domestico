<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_menu.php
***** Conteúdo: Menu do Diagnóstico do Usuário
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

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}

//********************
//********************
//**** Pega cargos
//********************
//********************
abre_db();
$html_cargo = "<option value='0' selected>Selecione o cargo</option>";
$strsql = "
select 
tcargo.chave_cargo
,tcargo.descr_cargo
,tcargo.cbo_cargo
from 
tcargo
where 
tcargo.caixa_cargo
order by tcargo.cbo_cargo
";
$qcargo = $pdo->prepare($strsql);
$qcargo->execute();
while ($tcargo = $qcargo->fetch(PDO::FETCH_ASSOC)) {
  $chave_cargo = $tcargo["chave_cargo"];
  $descr_cargo = $tcargo["descr_cargo"];
  $cbo_cargo = $tcargo["cbo_cargo"];
  $html_cargo .= "<option value='{$chave_cargo}'>{$descr_cargo} - CBO: {$cbo_cargo}</option>\n";
}
//********************
//********************
//**** EOF Pega cargos
//********************
//********************
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Menu Principal do Diagnóstico do Usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-3 pe-3">
      <div class="col-sm-12">
        <h2>Diagnóstico do Trabalhador Doméstico</h2>
        <span class="texto-corpo">Selecione um tema para entender seus direitos e deveres.</span>
      </div>     
    </div>
    <div class="row ps-3 pe-3">
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_leiemprego.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-tree fa-2x" style="color:#2563EB;"></i>                
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_leiemprego.php">Lei do emprego doméstico</a><br>            
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_leiemprego.php">Entenda a legislação atual e os principais pontos da PEC das Domésticas.</a>
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_oqueseucargofaz.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-suitcase fa-2x" style="color:#0D9488;"></i>                              
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_oqueseucargofaz.php">O que seu cargo faz</a><br>            
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_oqueseucargofaz.php">Deveres e responsabilidades específicas de cada função doméstica.</a>            
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_folhapagto.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-regular fa-money-bill-1 fa-2x" style="color:#059669;"></i>
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_folhapagto.php">Pagamentos e descontos</a><br>            
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_folhapagto.php">Cálculos detalhados sobre salário líquido, vales e retenções obrigatórias.</a>            
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_ferias13.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-regular fa-calendar fa-2x" style="color:#22C55E;"></i>                              
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_ferias13.php">Férias e 13º</a><br>
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_ferias13.php">Saiba quando e quanto você deve receber. Regras para concessão, períodos aquisitivos e abono pecuniário.</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row ps-3 pe-3">
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_rescisao.php">
              <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
                <i class="fa-solid fa-arrow-up-right-from-square fa-2x" style="color:#E11D48;"></i>
              </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_rescisao.php">Demissão</a><br>            
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_auxdoenca.php">Rescisão de contrato, aviso prévio e verbas rescisórias devidas.</a>            
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_fgtsinss.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-user-shield fa-2x" style="color:#4F46E5;"></i>                              
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_fgtsinss.php">INSS / FGTS</a><br>                        
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_fgtsinss.php">Contribuições previdenciárias e fundo de garantia do tempo de serviço.</a>            
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_auxdoenca.php">
            <div class="rounded text-center mb-3" style="max-width:48px; min-height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-hand-holding-medical fa-2x" style="color:#059669;"></i>                
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_auxdoenca.php">Auxílio-Doença e Afastamento</a><br>            
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_auxdoenca.php">Regras de incapacidade temporária e como funciona o processo de pagamento e solicitação.</a>
          </div>
        </div>
      </div>
      <div class="col-sm-3 mt-5">
        <div class="card shadow">
          <div class="card-body" style="min-height:260px;">
            <a class="sem-decoracao" href="/publico/diagnostico/diagnostico_resumo.php">
            <div class="rounded text-center mb-3" style="width:48px; height:48px; background-color:#EEF2FF; display: flex; justify-content: center; align-items: center;">
              <i class="fa-solid fa-pen-to-square fa-2x" style="color:#475569;"></i>                
            </div>
            </a>
            <a class="link-h4" href="/publico/diagnostico/diagnostico_resumo.php">Resumo de diagnóstico</a><br>
            <a class="sem-decoracao texto-corpo" href="/publico/diagnostico/diagnostico_resumo.php">Acesse o relatório completo com a síntese de todos os módulos de diagnóstico concluídos.</a>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-5 ps-3 pe-3">
      <div class="col-sm-12">        
        <h4>Links Úteis</h4>        
      </div>     
      <div class="row mt-5 ps-3 pe-3">
        <div class="col-sm-6 mb-2">
          <div class="card shadow ms-auto" style="max-width:400px;">
            <div class="card-body" style="min-height:80px;">
              <div class="row">
                <div class="col-sm-2">
                  <div class="rounded text-center" style="width:48px; height:48px; background-color:#e9eff2; display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-earth-americas fa-2x" style="color:#1A5275;"></i>
                  </div>
                </div>
                <div class="col-sm-8">
                  <a href="https://www.gov.br/pt-br/servicos/obter-a-carteira-de-trabalho" target="_blank" class="link-padrao text-nowrap">&nbsp;CTPS Digital</a>
                  <a href="https://www.gov.br/pt-br/servicos/obter-a-carteira-de-trabalho" target="_blank" class="text-nowrap sem-decoracao texto-suave texto-menor">&nbsp;Obtenha sua carteira de trabalho digital</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 mb-2">
          <div class="card shadow" style="max-width:400px;">
            <div class="card-body" style="min-height:80px;">
              <div class="row">
                <div class="col-sm-2">
                  <div class="rounded text-center" style="width:48px; height:48px; background-color:#e9eff2; display: flex; justify-content: center; align-items: center;">
                    <i class="fa-solid fa-landmark fa-2x" style="color:#1A5275;"></i>
                  </div>            
                </div>
                <div class="col-sm-8">
                  <a href="https://www.gov.br/trabalho-e-emprego/pt-br" target="_blank" class="link-padrao text-nowrap">&nbsp;Ministério do Trabalho</a>
                  <a href="https://www.gov.br/trabalho-e-emprego/pt-br" target="_blank" class="text-nowrap sem-decoracao texto-suave texto-menor">&nbsp;Consultas e denúncias</a>
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