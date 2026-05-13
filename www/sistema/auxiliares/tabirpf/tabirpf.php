<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/auxiliares/tabirpf/tabirpf.php
***** Conteúdo: Tabela de IRPF
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
$Home = $Raiz . "sistema/auxiliares/tabirpf/tabirpf.php";
VerificaAdmin($Raiz);

// Variáveis de inicialização
$ok = true;
$pagina_titulo = "Tabela de IRPF";
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
  if ($_POST["RESET_TABIRPF"] == "SIM") {
    $_SESSION["FIRSTPAGE_TABIRPF"] = "0";
    $_SESSION["LASTPAGE_TABIRPF"] = "0";
    $_SESSION["CURRENTPAGE_TABIRPF"] = "1";
    $_SESSION["SEARCHSTRING_TABIRPF"] = "";
    $_SESSION["SEARCHEXCETO_TABIRPF"] = "";
    $_SESSION["ORDEM_TABIRPF"] = "chave_tabirpf";
    $_SESSION["ORDEMPOS_TABIRPF"] = "";
    $_SESSION["CAIXA_TABIRPF"] = 1;
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
  if (isset($_GET["RESET_TABIRPF"])) {
    if ($_GET["RESET_TABIRPF"] == "SIM") {
      $_SESSION["FIRSTPAGE_TABIRPF"] = "0";
      $_SESSION["LASTPAGE_TABIRPF"] = "0";
      $_SESSION["CURRENTPAGE_TABIRPF"] = "1";
      $_SESSION["SEARCHSTRING_TABIRPF"] = "";
      $_SESSION["SEARCHEXCETO_TABIRPF"] = "";
      $_SESSION["ORDEM_TABIRPF"] = "chave_tabirpf";
      $_SESSION["ORDEMPOS_TABIRPF"] = "";
      $_SESSION["CAIXA_TABIRPF"] = 1;
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
if (!isset($_SESSION["FIRSTPAGE_TABIRPF"])) {
  $_SESSION["FIRSTPAGE_TABIRPF"] = "0";
  $_SESSION["LASTPAGE_TABIRPF"] = "0";
  $_SESSION["CURRENTPAGE_TABIRPF"] = "1";
  $_SESSION["SEARCHSTRING_TABIRPF"] = "";
  $_SESSION["SEARCHEXCETO_TABIRPF"] = "";
  $_SESSION["ORDEM_TABIRPF"] = "CHAVE_TABIRPF";
  $_SESSION["ORDEMPOS_TABIRPF"] = "";
}
if (isset($_GET["PG"])) {
  $pagina = $_GET["PG"];
}
else {
  if ($_SESSION["CURRENTPAGE_TABIRPF"] != "") {
    $pagina = $_SESSION["CURRENTPAGE_TABIRPF"];
  }
}
if (isset($_POST['PROCURAR'])) {
  if ($_POST["PROCURAR"] != "") {
    $procurar = $_POST['PROCURAR'];
    $pagina = 1;
    $_SESSION["SEARCHSTRING_TABIRPF"] = $procurar;
  }
}
$procurar = $_SESSION["SEARCHSTRING_TABIRPF"];

if (isset($_POST['PROCURAR_EXCETO'])) {
  if ($_POST['PROCURAR_EXCETO'] != "") {
    $procurar_exceto = $_POST['PROCURAR_EXCETO'];
    $pagina = 1;
    $_SESSION["SEARCHEXCETO_TABIRPF"] = $procurar_exceto;
  }
}
else {
  $procurar_exceto = "";
  $_SESSION["SEARCHEXCETO_TABIRPF"] = $procurar_exceto;
}
$procurar_exceto = $_SESSION["SEARCHEXCETO_TABIRPF"];
if (isset($_GET["ORDEM"])) {
  $ordem = strtolower($_GET["ORDEM"]);
  if ($ordem != $_SESSION["ORDEM_TABIRPF"]) {
    $_SESSION["ORDEM_TABIRPF"] = $ordem;
    $_SESSION["ORDEMPOS_TABIRPF"] = "";
  }
  else {
    if ($_SESSION["ORDEMPOS_TABIRPF"] == "") {
      $_SESSION["ORDEMPOS_TABIRPF"] = "desc";
    }
    else {
      $_SESSION["ORDEMPOS_TABIRPF"] = "";
    }
  }
}
if (isset($_GET["ORDEMPOS"])) {
  $ordempos = strtolower($_GET["ORDEMPOS"]);
  if ($ordempos != $_SESSION["ORDEMPOS_TABIRPF"]) {
    $_SESSION["ORDEMPOS_TABIRPF"] = $ordempos;
  }
}
$ordem = $_SESSION["ORDEM_TABIRPF"];
$ordempos = $_SESSION["ORDEMPOS_TABIRPF"];

//********************
//********************
//**** Cadastrados ou Excluídos
//********************
//********************
$caixa_tabirpf = 1;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if ($_POST["CAIXA_TABIRPF"] != "") {
    if (strval($_POST["CAIXA_TABIRPF"]) != strval($_SESSION["CAIXA_TABIRPF"])) {
      $pagina = 1;
    }
    $caixa_tabirpf = intval($_POST["CAIXA_TABIRPF"]);
    $_SESSION["CAIXA_TABIRPF"] = $caixa_tabirpf;
  }
}
if (isset($_SESSION["CAIXA_TABIRPF"])) {
  $caixa_tabirpf = intval($_SESSION["CAIXA_TABIRPF"]);
}
else {
  $_SESSION["CAIXA_TABIRPF"] = $caixa_tabirpf;
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
ttabirpf
where 
ttabirpf.caixa_tabirpf = {$caixa_tabirpf}
";
$qtabirpf = $pdo->prepare($strsql);
$qtabirpf->execute();
if ($ttabirpf = $qtabirpf->fetch(PDO::FETCH_ASSOC)) {
  $qtdregtotal = $ttabirpf["qtdreg"];
}
$strsql = "
select 
sum(1) as qtdreg_filtragem 
from 
ttabirpf
where 
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
  "(
    ttabirpf.ano_tabirpf like '%{$procurar}%'
  ) and ";
}
$strsql = $strsql . "
ttabirpf.caixa_tabirpf = " . $caixa_tabirpf;
$qtabirpf = $pdo->prepare($strsql);
$qtabirpf->execute();
if ($ttabirpf = $qtabirpf->fetch(PDO::FETCH_ASSOC)) {
  $qtdreg = $ttabirpf['qtdreg_filtragem'];
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
    $_SESSION["FIRSTPAGE_TABIRPF"] = "0";
    $_SESSION["LASTPAGE_TABIRPF"] = "0";
  }
  if ($pagina == "FIRST") {
    $pagina = "1";
  }
  if ($pagina == "LAST") {
    $pagina = $qtdpagina;
  }
  $pagina = intval($pagina);
  $paginainicial = $_SESSION["FIRSTPAGE_TABIRPF"];
  $paginafinal = $_SESSION["LASTPAGE_TABIRPF"];
  // Início padrão, sem página informada
  if (intval($paginainicial) <= 0 || $pagina == 1) {
    $paginainicial = 1;
    $paginafinal = ($paginainicial + 5) - 1;
    $_SESSION["FIRSTPAGE_TABIRPF"] = $paginainicial;
    $_SESSION["LASTPAGE_TABIRPF"] = $paginafinal;
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
      $_SESSION["FIRSTPAGE_TABIRPF"] = $paginainicial;
      $_SESSION["LASTPAGE_TABIRPF"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $paginafinal) {
      $paginainicial = $paginainicial + 1;
      $paginafinal = $paginafinal + 1;
      $_SESSION["FIRSTPAGE_TABIRPF"] = $paginainicial;
      $_SESSION["LASTPAGE_TABIRPF"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $qtdpagina) {
      $paginafinal = $qtdpagina;
      $paginainicial = ($paginafinal - 5) + 1;
      $_SESSION["FIRSTPAGE_TABIRPF"] = $paginainicial;
      $_SESSION["LASTPAGE_TABIRPF"] = $paginafinal;
      $paginado = true;
    }
  }
  $_SESSION["CURRENTPAGE_TABIRPF"] = $pagina;
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
ttabirpf.*
from 
ttabirpf
where 
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
    "(
    ttabirpf.ano_tabirpf like '%{$procurar}%'
    ) and ";
}
$strsql = $strsql . "
ttabirpf.caixa_tabirpf = {$caixa_tabirpf} 
order by {$ordem} " . ($ordempos == "desc" ? " " . $ordempos : $ordempos) . "
limit {$qtdlimite}
";
if (intval($pagina) > 1) {
  $offset = ((intval($pagina) - 1) * 20);
  $strsql = $strsql . ' offset ' . $offset;
}
$qtabirpf = $pdo->prepare($strsql);
$qtabirpf->execute();
$contetq = 0;
$etq = '';
$qtdregpagina = 0;
while ($ttabirpf = $qtabirpf->fetch(PDO::FETCH_ASSOC)) {
  $chave_tabirpf = $ttabirpf["chave_tabirpf"];
  $vli_tabirpf = FormataNumero($ttabirpf["vli_tabirpf"],2);
  $vlf_tabirpf = FormataNumero($ttabirpf["vlf_tabirpf"],2);
  $aliq_tabirpf = FormataPercentual($ttabirpf["aliq_tabirpf"],2);
  $vlded_tabirpf = FormataNumero($ttabirpf["vlded_tabirpf"],2);  
  $ano_tabirpf = $ttabirpf["ano_tabirpf"];
  $etq .= '              <tr>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA d-none d-md-table-cell">' . str_pad($chave_tabirpf, 6, "0", STR_PAD_LEFT) . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA">' . $vli_tabirpf . '</td>' . "\n";  
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA">' . $vlf_tabirpf . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA">' . $aliq_tabirpf . '</td>' . "\n";  
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA d-none d-md-table-cell">' . $vlded_tabirpf . '</td>' . "\n";  
  $etq .= '                <td data-id_dbg="' . $chave_tabirpf . '" class="SELECIONA_LINHA">' . $ano_tabirpf . '</td>' . "\n";
  $etq .= '                <td class="text-center"><a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO_EXC" id="SHOW_DIARIO_EXC" data-id="' . $chave_tabirpf . '" data-campo-id="chave_tabirpf" data-tabela-id="ttabirpf" data-url-id="' . $Raiz . '" data-caixa-id="caixa_tabirpf"><i class="fa-regular fa-trash-can" title="Excluir"></a></td>' . "\n";
  $etq .= '                <td class="text-center"><a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"     id="SHOW_DIARIO"     data-id="' . $chave_tabirpf . '" data-campo-id="chave_tabirpf" data-tabela-id="ttabirpf" data-url-id="' . $Raiz . '"><i class="fas fa-database" title="Diário do registro"></i></a></td>' . "\n";
  $etq .= '              </tr>' . "\n";
  $contetq = $contetq + 1;
  $qtdregpagina = $qtdregpagina + 1;
  if ($contetq == 4) {
    $contetq = 0;
  }
}
if ($procurar != "") {
  $btn_redefinir = '<button type="button" class="btn btn-sm btn-warning" id="BTN_REDEFINIR" name="BTN_REDEFINIR" onclick="javascript:redefinir_tabirpf();">Redefinir</button>' . "\n";
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
$html_pesq .= '      <form class="row row-cols-lg-auto float-end" action="tabirpf.php" id="FTABIRPF_FILTRO" name="FTABIRPF_FILTRO" method="POST">' . "\n"; // Pesquisa
$html_pesq .= '        <input type="hidden" id="RESET_TABIRPF" name="RESET_TABIRPF" value="">' . "\n";
$html_pesq .= '        <input type="hidden" id="CAIXA_TABIRPF" name="CAIXA_TABIRPF" value="">' . "\n";
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
$html_pesq .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_PROCURAR" name="BTN_PROCURAR" onclick="javascript:procurar_tabirpf();">Ok</button>' . "\n";
$html_pesq .= '          </div>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '        <div class="col-sm-6">' . "\n";
$html_pesq .= $btn_redefinir;
$html_pesq .= '          <button type="button" class="btn btn-sm btn-secondary" id="BTN_LIXEIRA" name="BTN_LIXEIRA" onclick="javascript:caixa_tabirpf(\'' . $caixa_tabirpf . '\')">' . (($caixa_tabirpf == 1) ? 'Lixeira' : 'Cadastrados') . '</button>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '      </form>' . "\n";
// EOF Pesquisa

// Corpo
$html .= '      <h5 class="Texto-Titulo">Cadastro de Tabela do IRPF</h5>' . "\n";
$html .= '      <div class="card shadow">' . "\n";
$html .= '        <div class="card-header m-0">' . "\n";
$html .= '          ' . $html_pesq . "\n";
$html .= '        </div>' . "\n";
$html .= '        <form action="tabirpf_edita.php" id="FTABIRPF" name="FTABIRPF" method="POST">' . "\n";
$html .= '          <input type="hidden" id="chave_tabirpf" name="chave_tabirpf" value="">' . "\n";
$html .= '          <input type="hidden" id="acao_tabirpf" name="acao_tabirpf" value="EDITAR">' . "\n";
if ($etq != "") {
  $html .= '          <table class="table table-hover table-sm mb-0">' . "\n";
  $html .= '            <thead class="thead-light">' . "\n";
  $html .= '              <tr>' . "\n";
  $html .= '                <th class="d-none d-md-table-cell"><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=CHAVE_TABIRPF">Chave</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=VLI_TABIRPF">De</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=VLF_TABIRPF">Até</a></th>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=ALIQ_TABIRPF">Alíquota</a></th>' . "\n";
  $html .= '                <th class="d-none d-md-table-cell"><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=VLDED_TABIRPF">Dedução</a></th>' . "\n";  
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=ANO_TABIRPF">Ano</a></th>' . "\n";  
  $html .= '                <th><a href="' . $Raiz . 'sistema/auxiliares/tabirpf/tabirpf.php?ORDEM=CAIXA_TABIRPF"> </a></th>' . "\n";
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
  $html .= '                <li class="page-item"><a class="page-link" href="tabirpf.php?PG=FIRST' . (($_SESSION['SEARCHSTRING_TABIRPF'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABIRPF'] : "") . '">&laquo;</a></li>' . "\n";
  if ($qtdpagina > 0) {
    $cont_apagina = count($apagina);
    for ($i = 0; $i <= $cont_apagina - 1; $i++) {
      if (($i + 1) >= $paginainicial && ($i + 1) <= $paginafinal) {
        if (($i + 1) == intval($pagina)) {
          $html .= '                <li class="page-item active"><a class="page-link" href="tabirpf.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_TABIRPF'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABIRPF'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
        else {
          $html .= '                <li class="page-item"><a class="page-link" href="tabirpf.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_TABIRPF'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABIRPF'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
      }
    }
  }
  $html .= '                <li class="page-item"><a class="page-link" href="tabirpf.php?PG=LAST' . (($_SESSION['SEARCHSTRING_TABIRPF'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_TABIRPF'] : "") . '" aria-label="Último"><span aria-hidden="true">&raquo;</span></a></li>' . "\n";
  $html .= '              </ul>' . "\n";
  $html .= '            </nav>' . "\n";
  $html .= '          </div>' . "\n";
}
// Resumo da página
if ($qtdregtotal > 0 and ($qtdregtotal != $qtdreg)) {
  $html .= '          <div class="col-sm-12 mb-2">' . "\n";
  if ($qtdregtotal - $qtdreg == 1) {
    $html .= '            <a href="javascript:redefinir_tabirpf();"><span>Há ' . ($qtdregtotal - $qtdreg) . ' registro que não satisfaz a pesquisa.</span></a><br />' . "\n";
  }
  else {
    $html .= '            <a href="javascript:redefinir_tabirpf();"><span>Há mais ' . ($qtdregtotal - $qtdreg) . ' registros que não satisfazem a pesquisa.</span></a><br />' . "\n";
  }
  $html .= '          </div>' . "\n";
}
// EOF Resumo da página
$html .= '          <div class="col-sm-12 text-center">' . "\n";
$html .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_TABIRPF_INCLUIR" name="BTN_TABIRPF_INCLUIR">Incluir</button>' . "\n";
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
<title>Guia Doméstico - Tabela do IRPF</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function caixa_tabirpf(ccaixa) {
    var vcaixa = 1;
    if (ccaixa == 1) {
      vcaixa = 0;
    }
    document.getElementById("CAIXA_TABIRPF").value = vcaixa;
    document.getElementById("FTABIRPF_FILTRO").submit();
    //	parent.location='<?php echo $Raiz; ?>sistema/auxiliares/tabirpf/tabirpf.php?CAIXA_TABIRPF=' + vcaixa;
  }

  function procurar_tabirpf() {
    if (document.getElementById("PROCURAR").value != "") {
      document.getElementById("RESET_TABIRPF").value = "NAO";
      document.getElementById("FTABIRPF_FILTRO").submit();
    }
    else {
      document.getElementById("RESET_TABIRPF").value = "SIM";
      document.getElementById("FTABIRPF_FILTRO").submit();
    }
  }

  function redefinir_tabirpf() {
    document.getElementById("RESET_TABIRPF").value = "SIM";
    document.getElementById("FTABIRPF_FILTRO").submit();
  }

  function procurar_avaliatecla(e) {
    if (e.key == "Enter") {
      procurar_tabirpf();
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
        document.getElementById("chave_tabirpf").value = $(this).data("id_dbg");
        document.getElementById("FTABIRPF").submit();
      });
      $("#BTN_TABIRPF_INCLUIR").click(function () {
        document.getElementById("chave_tabirpf").value = "0";
        document.getElementById("FTABIRPF").submit();
      });
    });
  </script>
</body>

</html>