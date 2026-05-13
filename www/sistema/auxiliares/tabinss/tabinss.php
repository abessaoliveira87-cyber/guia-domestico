<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/auxiliares/tabinss/tabinss.php
***** Conteúdo: Tabela de Contribuição do INSS
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
$Raiz = "../../../";
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
include($Raiz . "conexao/db.php");
$Home = $Raiz . "sistema/auxiliares/tabinss/tabinss.php";
VerificaAdmin($Raiz);

// Variáveis de inicialização
$ok = true;
$pagina_titulo = "Tabela de Contribuição do INSS";
$lnk_retorno = $Raiz . "sistema/index.php";
$link_main = $Home;
$btn_retorno = Btn_Retorno($lnk_retorno);
$qtdlimite = 20;
$qtdregpagina = 0;
$qtdreg = 0;
$qtdregtotal = 0;
$apagina = array();
$pagina = 1;
$qtdpagina = 0;
$paginainicial = 1;
$paginafinal = 1;
$html = "";
$procurar = "";
$procurar_exceto = "";
$ordem = "";
$ordempos = "";

//********************
//********************
//**** RESET POST
//********************
//********************
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if ($_POST["RESET_TABINSS"] == "SIM") {
    $_SESSION["FIRSTPAGE_TABINSS"] = "0";
    $_SESSION["LASTPAGE_TABINSS"] = "0";
    $_SESSION["CURRENTPAGE_TABINSS"] = "1";
    $_SESSION["SEARCHSTRING_TABINSS"] = "";
    $_SESSION["SEARCHEXCETO_TABINSS"] = "";
    $_SESSION["ORDEM_TABINSS"] = "chave_tabinss";
    $_SESSION["ORDEMPOS_TABINSS"] = "";
    $_SESSION["CAIXA_TABINSS"] = 1;
    header("Location:" . $Home);
    die();
  }
}
//********************
//********************
//**** EOF - RESET
//********************
//********************

//********************
//********************
//**** RESET GET
//********************
//********************
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  if (isset($_GET["RESET_TABINSS"])) {
    if ($_GET["RESET_TABINSS"] == "SIM") {
      $_SESSION["FIRSTPAGE_TABINSS"] = "0";
      $_SESSION["LASTPAGE_TABINSS"] = "0";
      $_SESSION["CURRENTPAGE_TABINSS"] = "1";
      $_SESSION["SEARCHSTRING_TABINSS"] = "";
      $_SESSION["SEARCHEXCETO_TABINSS"] = "";
      $_SESSION["ORDEM_TABINSS"] = "chave_tabinss";
      $_SESSION["ORDEMPOS_TABINSS"] = "";
      $_SESSION["CAIXA_TABINSS"] = 1;
      header("Location:" . $Home);
      die();
    }
  }
}
//********************
//********************
//**** EOF - RESET
//********************
//********************

//********************
//********************
//**** Controle de paginação
//********************
//********************
if (!isset($_SESSION["FIRSTPAGE_TABINSS"])) {
  $_SESSION["FIRSTPAGE_TABINSS"] = "0";
  $_SESSION["LASTPAGE_TABINSS"] = "0";
  $_SESSION["CURRENTPAGE_TABINSS"] = "1";
  $_SESSION["SEARCHSTRING_TABINSS"] = "";
  $_SESSION["SEARCHEXCETO_TABINSS"] = "";
  $_SESSION["ORDEM_TABINSS"] = "CHAVE_TABINSS";
  $_SESSION["ORDEMPOS_TABINSS"] = "";
}
if (isset($_GET["PG"])) {
  $pagina = $_GET["PG"];
}
else {
  if ($_SESSION["CURRENTPAGE_TABINSS"] != "") {
    $pagina = $_SESSION["CURRENTPAGE_TABINSS"];
  }
}
if (isset($_POST['PROCURAR'])) {
  if ($_POST["PROCURAR"] != "") {
    $procurar = $_POST['PROCURAR'];
    $pagina = 1;
    $_SESSION["SEARCHSTRING_TABINSS"] = $procurar;
  }
}
$procurar = $_SESSION["SEARCHSTRING_TABINSS"];

if (isset($_POST['PROCURAR_EXCETO'])) {
  if ($_POST['PROCURAR_EXCETO'] != "") {
    $procurar_exceto = $_POST['PROCURAR_EXCETO'];
    $pagina = 1;
    $_SESSION["SEARCHEXCETO_TABINSS"] = $procurar_exceto;
  }
}
else {
  $procurar_exceto = "";
  $_SESSION["SEARCHEXCETO_TABINSS"] = $procurar_exceto;
}
$procurar_exceto = $_SESSION["SEARCHEXCETO_TABINSS"];
if (isset($_GET["ORDEM"])) {
  $ordem = strtolower($_GET["ORDEM"]);
  if ($ordem != $_SESSION["ORDEM_TABINSS"]) {
    $_SESSION["ORDEM_TABINSS"] = $ordem;
    $_SESSION["ORDEMPOS_TABINSS"] = "";
  }
  else {
    if ($_SESSION["ORDEMPOS_TABINSS"] == "") {
      $_SESSION["ORDEMPOS_TABINSS"] = "desc";
    }
    else {
      $_SESSION["ORDEMPOS_TABINSS"] = "";
    }
  }
}
if (isset($_GET["ORDEMPOS"])) {
  $ordempos = strtolower($_GET["ORDEMPOS"]);
  if ($ordempos != $_SESSION["ORDEMPOS_TABINSS"]) {
    $_SESSION["ORDEMPOS_TABINSS"] = $ordempos;
  }
}
$ordem = $_SESSION["ORDEM_TABINSS"];
$ordempos = $_SESSION["ORDEMPOS_TABINSS"];

//********************
//********************
//**** Cadastrados ou Excluídos
//********************
//********************
$caixa_tabinss = 1;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if ($_POST["CAIXA_TABINSS"] != "") {
    if (strval($_POST["CAIXA_TABINSS"]) != strval($_SESSION["CAIXA_TABINSS"])) {
      $pagina = 1;
    }
    $caixa_tabinss = intval($_POST["CAIXA_TABINSS"]);
    $_SESSION["CAIXA_TABINSS"] = $caixa_tabinss;
  }
}
if (isset($_SESSION["CAIXA_TABINSS"])) {
  $caixa_tabinss = intval($_SESSION["CAIXA_TABINSS"]);
}
else {
  $_SESSION["CAIXA_TABINSS"] = $caixa_tabinss;
}
//********************
//********************
// EOF - Cadastrados ou Excluídos
//********************
//********************

abre_db();
// Captura de paginação
$strsql = "
select 
sum(1) as qtdreg
from 
ttabinss
where 
ttabinss.caixa_tabinss = {$caixa_tabinss}
";
$qtabinss = $pdo->prepare($strsql);
$qtabinss->execute();
if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
  $qtdregtotal = $ttabinss["qtdreg"];
}
$strsql = "
select 
sum(1) as qtdreg_filtragem 
from 
ttabinss
where 
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
  "(
    ttabinss.ano_tabinss like '%{$procurar}%'
  ) and ";
}
$strsql = $strsql . "
ttabinss.caixa_tabinss = " . $caixa_tabinss;
$qtabinss = $pdo->prepare($strsql);
$qtabinss->execute();
if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
  $qtdreg = $ttabinss['qtdreg_filtragem'];
  $qtdpaginaresto = round($qtdreg / $qtdlimite, 2);
  $qtdpagina = floor($qtdreg / $qtdlimite);
  if ($qtdpaginaresto - $qtdpagina > 0) {
    $qtdpagina = floor($qtdpagina) + 1;
  }
  if ($qtdreg > 0) {
    for ($i = 0; $i <= $qtdpagina - 1; $i++) {
      array_push($apagina, $i + 1);
    }
  }
  $paginado = false;
  if (empty($pagina)) {
    $pagina = "1";
    $_SESSION["FIRSTPAGE_TABINSS"] = "0";
    $_SESSION["LASTPAGE_TABINSS"] = "0";
  }
  if ($pagina == "FIRST") {
    $pagina = "1";
  }
  if ($pagina == "LAST") {
    $pagina = $qtdpagina;
  }
  $pagina = intval($pagina);
  $paginainicial = $_SESSION["FIRSTPAGE_TABINSS"];
  $paginafinal = $_SESSION["LASTPAGE_TABINSS"];
  // Início padrão, sem página informada
  if (intval($paginainicial) <= 0 || $pagina == 1) {
    $paginainicial = 1;
    $paginafinal = ($paginainicial + 5) - 1;
    $_SESSION["FIRSTPAGE_TABINSS"] = $paginainicial;
    $_SESSION["LASTPAGE_TABINSS"] = $paginafinal;
    $paginado = true;
  }
  // EOF Início padrão, sem página informada
  if (!$paginado) {
    if ($pagina > $paginainicial && $pagina < $paginafinal) {
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $paginainicial) {
      $paginainicial = $paginainicial - 1;
      $paginafinal = $paginafinal - 1;
      if ($paginainicial < 1) {
        $paginainicial = 1;
        $paginafinal = ($paginainicial + 5) - 1;
      }
      $_SESSION["FIRSTPAGE_TABINSS"] = $paginainicial;
      $_SESSION["LASTPAGE_TABINSS"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $paginafinal) {
      $paginainicial = $paginainicial + 1;
      $paginafinal = $paginafinal + 1;
      $_SESSION["FIRSTPAGE_TABINSS"] = $paginainicial;
      $_SESSION["LASTPAGE_TABINSS"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $qtdpagina) {
      $paginafinal = $qtdpagina;
      $paginainicial = ($paginafinal - 5) + 1;
      $_SESSION["FIRSTPAGE_TABINSS"] = $paginainicial;
      $_SESSION["LASTPAGE_TABINSS"] = $paginafinal;
      $paginado = true;
    }
  }
  $_SESSION["CURRENTPAGE_TABINSS"] = $pagina;
}
//********************
//********************
//**** EOF - Controle de paginação
//********************
//********************

//********************
//********************
//**** Browse
//********************
//********************
$strsql = "
select 
ttabinss.*
from 
ttabinss
where 
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
    "(
    ttabinss.ano_tabinss like '%{$procurar}%'
    ) and ";
}
$strsql = $strsql . "
ttabinss.caixa_tabinss = {$caixa_tabinss} 
order by {$ordem} " . ($ordempos == "desc" ? " " . $ordempos : $ordempos) . "
limit {$qtdlimite}
";
if (intval($pagina) > 1) {
  $offset = ((intval($pagina) - 1) * 20);
  $strsql = $strsql . ' offset ' . $offset;
}
$qtabinss = $pdo->prepare($strsql);
$qtabinss->execute();
$contetq = 0;
$etq = '';
$qtdregpagina = 0;
while ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
  $chave_tabinss = $ttabinss["chave_tabinss"];
  $vli_tabinss = FormataNumero($ttabinss["vli_tabinss"],2);
  $vlf_tabinss = FormataNumero($ttabinss["vlf_tabinss"],2);
  $aliq_tabinss = FormataPercentual($ttabinss["aliq_tabinss"],2);
  $vlded_tabinss = FormataNumero($ttabinss["vlded_tabinss"],2);
  $vlfixo_tabinss = FormataNumero($ttabinss["vlfixo_tabinss"],2);
  $ano_tabinss = $ttabinss["ano_tabinss"];
  $etq .= '              <tr>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA d-none d-sm-block">' . str_pad($chave_tabinss, 6, "0", STR_PAD_LEFT) . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA">' . $vli_tabinss . '</td>' . "\n";  
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA">' . $vlf_tabinss . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA">' . $aliq_tabinss . '</td>' . "\n";  
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA d-none d-md-table-cell">' . $vlded_tabinss . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA d-none d-md-table-cell">' . $vlfixo_tabinss . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabinss . '" class="SELECIONA_LINHA">' . $ano_tabinss . '</td>' . "\n";
  $etq .= '                <td class="text-center"><a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC" id="SHOW_DIARIO_EXC" data-id="' . $chave_tabinss . '" data-campo-id="chave_tabinss" data-tabela-id="ttabinss" data-url-id="' . $Raiz . '" data-caixa-id="caixa_tabinss"><i class="fa-regular fa-trash-can" title="Excluir"></a></td>' . "\n";
  $etq .= '                <td class="text-center"><a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"     id="SHOW_DIARIO"     data-id="' . $chave_tabinss . '" data-campo-id="chave_tabinss" data-tabela-id="ttabinss" data-url-id="' . $Raiz . '"><i class="fas fa-database" title="Diário do registro"></i></a></td>' . "\n";
  $etq .= '              </tr>' . "\n";
  $contetq = $contetq + 1;
  $qtdregpagina = $qtdregpagina + 1;
  if ($contetq == 4) {
    $contetq = 0;
  }
}
if ($procurar != "") {
  $btn_redefinir = '<button type="button" class="btn btn-sm btn-warning" id="BTN_REDEFINIR" name="BTN_REDEFINIR" onclick="javascript:redefinir_tabinss();">Redefinir</button>' . "\n";
}
else {
  $btn_redefinir = "";
}
// Pesquisa
$html_pesq = '';
if ($qtdreg <= 0) {
  $html_pesq .= '      <a class="btn btn-light btn-sm">Nenhum registro</a>' . "\n";
}
else {
  $html_pesq .= '      <a class="btn btn-light btn-sm">Registros: ' . $qtdregpagina . '/' . $qtdreg . '</a>' . "\n";
}
$html_pesq .= '      <form class="row row-cols-lg-auto float-end" action="tabinss.php" id="FTABINSS_FILTRO" name="FTABINSS_FILTRO" method="POST">' . "\n"; // Pesquisa
$html_pesq .= '        <input type="hidden" id="RESET_TABINSS" name="RESET_TABINSS" value="">' . "\n";
$html_pesq .= '        <input type="hidden" id="CAIXA_TABINSS" name="CAIXA_TABINSS" value="">' . "\n";
$html_pesq .= '        <div class="col-auto">' . "\n";
$html_pesq .= '          <div class="input-group">' . "\n";
$html_pesq .= '            <input type="text" class="form-control form-control-sm' . ($procurar != "" ? ' form-danger' : '') . '" onkeyup="javascript:procurar_avaliatecla(event);" id="PROCURAR" name="PROCURAR" value="' . $procurar . '" placeholder="Procurar">' . "\n";
$html_pesq .= '            <span class="input-group-text">' . "\n";
$html_pesq .= '              <div class="form-check" style="padding:-6px; margin:-6px;">' . "\n";
$html_pesq .= '                <input class="form-check-input" type="checkbox" id="PROCURAR_EXCETO" name="PROCURAR_EXCETO" value="1" ' . (($procurar_exceto == "1") ? ' checked' : '') . '>' . "\n";
$html_pesq .= '                <label class="form-check-label small" style="padding-top:2px;" for="PROCURAR_EXCETO">' . "\n";
$html_pesq .= '                  Exceto' . "\n";
$html_pesq .= '                </label>' . "\n";
$html_pesq .= '              </div>' . "\n";
$html_pesq .= '            </span>' . "\n";
$html_pesq .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_PROCURAR" name="BTN_PROCURAR" onclick="javascript:procurar_tabinss();">Ok</button>' . "\n";
$html_pesq .= '          </div>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '        <div class="col-sm-6">' . "\n";
$html_pesq .= $btn_redefinir;
$html_pesq .= '          <button type="button" class="btn btn-sm btn-secondary" id="BTN_LIXEIRA" name="BTN_LIXEIRA" onclick="javascript:caixa_tabinss(\'' . $caixa_tabinss . '\')">' . (($caixa_tabinss == 1) ? 'Lixeira' : 'Cadastrados') . '</button>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '      </form>' . "\n";
// EOF Pesquisa

// Corpo
$html .= '      <h5 class="Texto-Titulo">Cadastro de Tabela de Contribuição do INSS</h5>' . "\n";
$html .= '      <div class="card shadow">' . "\n";
$html .= '        <div class="card-header m-0">' . "\n";
$html .= '          ' . $html_pesq . "\n";
$html .= '        </div>' . "\n";
$html .= '        <form action="tabinss_edita.php" id="FTABINSS" name="FTABINSS" method="POST">' . "\n";
$html .= '          <input type="hidden" id="chave_tabinss" name="chave_tabinss" value="">' . "\n";
$html .= '          <input type="hidden" id="acao_tabinss" name="acao_tabinss" value="EDITAR">' . "\n";
if ($etq != "") {
  $html .= '          <table class="table table-hover table-sm mb-0">' . "\n";
  $html .= '            <thead class="thead-light">' . "\n";
  $html .= '              <tr>' . "\n";
  $html .= '                <th class="d-none d-sm-block"><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=CHAVE_TABINSS">Chave</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=VLI_TABINSS">De</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=VLF_TABINSS">Até</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=ALIQ_TABINSS">Alíquota</a></th>' . "\n";
  $html .= '                <th class="d-none d-md-table-cell"><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=VLDED_TABINSS">Dedução</a></th>' . "\n";
  $html .= '                <th class="d-none d-md-table-cell"><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=VLFIXO_TABINSS">Valor Fixo</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=ANO_TABINSS">Ano</a></th>' . "\n";  
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabinss/tabinss.php?ORDEM=CAIXA_TABINSS"> </a></th>' . "\n";
  $html .= '                <th></th>' . "\n";
  $html .= '              </tr>' . "\n";
  $html .= '            </thead>' . "\n";
  $html .= '            <tbody>' . "\n";
  $html .= $etq . "\n";
  $html .= '            </tbody>' . "\n";
  $html .= '          </table>' . "\n";
}
$html .= '        </form>' . "\n";
// Botões de paginação inferior
$html .= '        <div class="card-footer text-muted">' . "\n";
if ($qtdpagina > 0 && ($qtdreg > $qtdlimite)) {
  $html .= '          <div class="col-sm-12 mb-2">' . "\n";
  $html .= '            <nav aria-label="Páginas de navegação">' . "\n";
  $html .= '              <ul class="pagination pagination-sm mb-0">' . "\n";
  $html .= '                <li class="page-item"><a class="page-link" href="tabinss.php?PG=FIRST' . (($_SESSION['SEARCHSTRING_TABINSS'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABINSS'] : "") . '">&laquo;</a></li>' . "\n";
  if ($qtdpagina > 0) {
    $cont_apagina = count($apagina);
    for ($i = 0; $i <= $cont_apagina - 1; $i++) {
      if (($i + 1) >= $paginainicial && ($i + 1) <= $paginafinal) {
        if (($i + 1) == intval($pagina)) {
          $html .= '                <li class="page-item active"><a class="page-link" href="tabinss.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_TABINSS'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABINSS'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
        else {
          $html .= '                <li class="page-item"><a class="page-link" href="tabinss.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_TABINSS'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABINSS'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
      }
    }
  }
  $html .= '                <li class="page-item"><a class="page-link" href="tabinss.php?PG=LAST' . (($_SESSION['SEARCHSTRING_TABINSS'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABINSS'] : "") . '" aria-label="Último"><span aria-hidden="true">&raquo;</span></a></li>' . "\n";
  $html .= '              </ul>' . "\n";
  $html .= '            </nav>' . "\n";
  $html .= '          </div>' . "\n";
}
// Resumo da página
if ($qtdregtotal > 0 and ($qtdregtotal != $qtdreg)) {
  $html .= '          <div class="col-sm-12 mb-2">' . "\n";
  if ($qtdregtotal - $qtdreg == 1) {
    $html .= '            <a href="javascript:redefinir_tabinss();"><span>Há ' . ($qtdregtotal - $qtdreg) . ' registro que não satisfaz a pesquisa.</span></a><br />' . "\n";
  }
  else {
    $html .= '            <a href="javascript:redefinir_tabinss();"><span>Há mais ' . ($qtdregtotal - $qtdreg) . ' registros que não satisfazem a pesquisa.</span></a><br />' . "\n";
  }
  $html .= '          </div>' . "\n";
}
// EOF Resumo da página
$html .= '          <div class="col-sm-12 text-center">' . "\n";
$html .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_TABINSS_INCLUIR" name="BTN_TABINSS_INCLUIR">Incluir</button>' . "\n";
$html .= '          </div>' . "\n";
$html .= '        </div>' . "\n";
// EOF Botões de paginação inferior
$html .= '      </div>' . "\n";
// EOF Corpo

//********************
//********************
//**** EOF - Browse
//********************
//********************
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>Guia Doméstico - Tabela de Contribuição do INSS</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function caixa_tabinss(ccaixa) {
    var vcaixa = 1;
    if (ccaixa == 1) {
      vcaixa = 0;
    }
    document.getElementById("CAIXA_TABINSS").value = vcaixa;
    document.getElementById("FTABINSS_FILTRO").submit();
    //	parent.location='<?php echo $Raiz; ?>sistema/auxiliares/tabinss/tabinss.php?CAIXA_TABINSS=' + vcaixa;
  }

  function procurar_tabinss() {
    if (document.getElementById("PROCURAR").value != "") {
      document.getElementById("RESET_TABINSS").value = "NAO";
      document.getElementById("FTABINSS_FILTRO").submit();
    }
    else {
      document.getElementById("RESET_TABINSS").value = "SIM";
      document.getElementById("FTABINSS_FILTRO").submit();
    }
  }

  function redefinir_tabinss() {
    document.getElementById("RESET_TABINSS").value = "SIM";
    document.getElementById("FTABINSS_FILTRO").submit();
  }

  function procurar_avaliatecla(e) {
    if (e.key == "Enter") {
      procurar_tabinss();
    }
  }
</script>

<body class="Fonte-Raleway">
  <?php include($Raiz . "include/php/menu.php"); ?>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <?php
        Modal_Diario($Raiz);
        Modal_Exclusao($Raiz, $link_main);
        ?>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <?php echo $html; ?>
      </div>
    </div>
    <div class="row mt-4 Texto-Rodape">
      <div class="col-sm-12">
        <h5>Legendas</h5>
        <span><i class="fa-regular fa-trash-can" style="min-width:40px;" align="center"></i>Excluir registro</span><br />
        <span><i class="fas fa-database" style="min-width:40px;" align="center"></i>Diário do registro</span>
      </div>
    </div>
  </div>
  <?php include($Raiz . "include/php/rodape.php"); ?>
  <script type="text/javascript">
    $(document).ready(function () {
      $(".SELECIONA_LINHA").click(function () {
        document.getElementById("chave_tabinss").value = $(this).data("id_dbg");
        document.getElementById("FTABINSS").submit();
      });
      $("#BTN_TABINSS_INCLUIR").click(function () {
        document.getElementById("chave_tabinss").value = "0";
        document.getElementById("FTABINSS").submit();
      });
    });
  </script>
</body>

</html>