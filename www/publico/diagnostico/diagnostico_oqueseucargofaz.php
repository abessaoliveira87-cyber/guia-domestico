<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_oqueseucargofaz.php
***** Conteúdo: O Que Seu Cargo Faz
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
$chave_cargo = strval($_SESSION["CHAVE_CARGO"]);
$descr_cargo = strval($_SESSION["DESCR_CARGO"]);
$cbo_cargo = strval($_SESSION["CBO_CARGO"]);

if ($chave_cargo != "") {
  //********************
  //********************
  //**** Pega cargos
  //********************
  //********************
  abre_db();
  $strsql = "
  select 
  tcargo.chave_cargo
  ,tcargo.descr_cargo
  ,tcargo.descrdetalhada_cargo
  ,tcargo.cbo_cargo
  from 
  tcargo
  where 
  tcargo.chave_cargo = :vchave_cargo and 
  tcargo.caixa_cargo = 1
  order by tcargo.chave_cargo
  ";
  $qcargo = $pdo->prepare($strsql);
  $qcargo->bindParam(":vchave_cargo", $chave_cargo);
  $qcargo->execute();
  if ($tcargo = $qcargo->fetch(PDO::FETCH_ASSOC)) {    
    $cbo_cargo = $tcargo["cbo_cargo"];  
    $descrdetalhada_cargo = $tcargo["descrdetalhada_cargo"];  
  }
  //********************
  //********************
  //**** EOF Pega cargos
  //********************
  //********************
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - O Que Seu Cargo Faz</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<style>
  .shadow-left {
    /* box-shadow: [horizontal offset] [vertical offset] [blur] [spread] [color] */
    box-shadow: -5px 0px 0px 0px #1A5275;
}
</style>
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
        <h2>O Que Seu Cargo Faz</h2>
        <span class="texto-secundario">Entenda as atividades permitidas, os limites da função e as responsabilidades previstas para o cargo de empregada doméstica de acordo com a legislação vigente.</span>
      </div>     
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <span class="h2 texto-negrito"><?php echo $descr_cargo ?>&nbsp;</span><span class="h2 texto-negrito texto-regular"><?php echo "(" . $cbo_cargo . ")"; ?></span>
      </div>     
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <h4><i class="fa-regular fa-circle-check texto-regular"></i>&nbsp;Atividades Principais</h4>
      </div>     
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-sm-12">
        <div class="rounded shadow-right border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <span class="texto-secundario texto-maior"><?php echo $descrdetalhada_cargo; ?></span>
        </div>
      </div>
<!--      
      <div class="col-sm-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <h4>Limpeza e Organização</h4>
          <span class="texto-secundario texto-menor">Higienização de cômodos, móveis, utensílios domésticos e organização geral da residência para manter o bem-estar da família.</span>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <h4>Culinária</h4>
          <span class="texto-secundario texto-menor">Preparo de refeições diárias para os moradores, seguindo as orientações de cardápio e restrições alimentares acordadas.</span>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <h4>Lavanderia</h4>
          <span class="texto-secundario texto-menor">Lavar, passar e organizar roupas pessoais, de cama, mesa e banho, cuidando da conservação de diferentes tipos de tecidos.</span>
        </div>
      </div>
-->      
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-sm-12">
        <div class="rounded ps-3 pe-3 pt-3 pb-3" style="background-color:#f4f1f3; min-height: 100px;">
          <div class="row mb-4">
            <div class="col-sm-12">
              <h5><i class="fa-solid fa-ban" style="color:#1A5275"></i>&nbsp;O Que Não Faz Parte do Cargo (Limites)</h5>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-12">          
              <span class="texto-corpo"><i class="fa-solid fa-circle-minus"></i>&nbsp;<strong>Atividades comerciais:</strong> Auxiliar em negócios próprios do empregador, como vendas, entregas ou produção de itens para comércio.</span><br>          
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-12">          
              <span class="texto-corpo"><i class="fa-solid fa-circle-minus"></i>&nbsp;<strong>Cuidados profissionais:</strong> Atuar como pet sitter profissional, jardineiro ou cuidador de idosos/crianças de forma integral se não for a função contratada especificamente.</span><br>          
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-12">          
              <span class="texto-corpo"><i class="fa-solid fa-circle-minus"></i>&nbsp;<strong>Riscos à integridade:</strong> Atividades que exijam esforço físico extremo, uso de produtos químicos perigosos sem EPI ou reparos estruturais (eletricidade, hidráulica pesada).</span><br>
            </div>
          </div>
        </div>
      </div>
    </div>    

    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-sm-12">
        <div class="rounded shadow-left border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <div class="row mb-4">
            <div class="col-sm-12">
              <h5><i class="fa-solid fa-triangle-exclamation" style="color:#1A5275"></i>&nbsp;Importante: Desvio de Função</h5>              
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-12">          
              <span class="texto-secundario">Realizar atividades substancialmente diferentes do cargo principal de forma constante e sistemática pode caracterizar acúmulo ou desvio de função. Nestes casos, o trabalhador pode ter direito a adicionais salariais retroativos ou readequação do contrato de trabalho. É fundamental que as tarefas extras sejam acordadas formalmente e remuneradas de acordo.</span>
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