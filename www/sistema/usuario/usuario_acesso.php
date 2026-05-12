<?php
/*
rever
*/
session_start();
$Raiz = "../../";
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
VerificaAdmin($Raiz);
$ok = true;
$chave_usuario = "0";
if (!isset($_GET['ID'])) {
  $ok = false;
}
else {
  $chave_usuario = $_GET['ID'];
}
if ($ok) {
  if (!is_numeric($chave_usuario)) {
    $ok = false;
  }
}
if (!$ok) {
  header("Location:" . $Raiz . "sistema/usuario/usuario.php");
  die();
}

// Variáveis de inicialização
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
// RESET
//********************
//********************
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if ($_POST["RESET_USUARIOACESSO"] == "SIM") {
    $_SESSION["FIRSTPAGE_USUARIOACESSO"] = "0";
    $_SESSION["LASTPAGE_USUARIOACESSO"] = "0";
    $_SESSION["CURRENTPAGE_USUARIOACESSO"] = "1";
    $_SESSION["SEARCHSTRING_USUARIOACESSO"] = "";
    $_SESSION["SEARCHEXCETO_USUARIOACESSO"] = "";
    $_SESSION["ORDEM_USUARIOACESSO"] = "chave_usuarioacesso";
    $_SESSION["ORDEMPOS_USUARIOACESSO"] = "desc";
    $_SESSION["CAIXA_USUARIOACESSO"] = 1;
    header("Location:" . $Raiz . "sistema/usuario/usuarioacesso.php");
    die();
  }
}
//********************
//********************
// EOF RESET
//********************
//********************

// Controle de paginação
if (!isset($_SESSION["FIRSTPAGE_USUARIOACESSO"])) {
  $_SESSION["FIRSTPAGE_USUARIOACESSO"] = "0";
  $_SESSION["LASTPAGE_USUARIOACESSO"] = "0";
  $_SESSION["CURRENTPAGE_USUARIOACESSO"] = "1";
  $_SESSION["SEARCHSTRING_USUARIOACESSO"] = "";
  $_SESSION["SEARCHEXCETO_USUARIOACESSO"] = "";
  $_SESSION["ORDEM_USUARIOACESSO"] = "CHAVE_USUARIOACESSO";
  $_SESSION["ORDEMPOS_USUARIOACESSO"] = "desc";
}
if (isset($_GET["PG"])) {
  $pagina = $_GET["PG"];
}
else {
  if ($_SESSION["CURRENTPAGE_USUARIOACESSO"] != "") {
    $pagina = $_SESSION["CURRENTPAGE_USUARIOACESSO"];
  }
}
if (isset($_POST['PROCURAR'])) {
  if ($_POST["PROCURAR"] != "") {
    $procurar = $_POST['PROCURAR'];
    $pagina = 1;
    $_SESSION["SEARCHSTRING_USUARIOACESSO"] = $procurar;
  }
}
$procurar = $_SESSION["SEARCHSTRING_USUARIOACESSO"];
if (isset($_POST['PROCURAR_EXCETO'])) {
  if ($_POST['PROCURAR_EXCETO'] != "") {
    $procurar_exceto = $_POST['PROCURAR_EXCETO'];
    $pagina = 1;
    $_SESSION["SEARCHEXCETO_USUARIOACESSO"] = $procurar_exceto;
  }
}
$procurar_exceto = $_SESSION["SEARCHEXCETO_USUARIOACESSO"];
if (isset($_GET["ORDEM"])) {
  $ordem = strtolower($_GET["ORDEM"]);
  if ($ordem != $_SESSION["ORDEM_USUARIOACESSO"]) {
    $_SESSION["ORDEM_USUARIOACESSO"] = $ordem;
    $_SESSION["ORDEMPOS_USUARIOACESSO"] = "";
  }
  else {
    if ($_SESSION["ORDEMPOS_USUARIOACESSO"] == "") {
      $_SESSION["ORDEMPOS_USUARIOACESSO"] = "desc";
    }    
    else {
      $_SESSION["ORDEMPOS_USUARIOACESSO"] = "";
    }
  }
}
if (isset($_GET["ORDEMPOS"])) {
  $ordempos = strtolower($_GET["ORDEMPOS"]);
  if ($ordempos != $_SESSION["ORDEMPOS_USUARIOACESSO"]) {
    $_SESSION["ORDEMPOS_USUARIOACESSO"] = $ordempos;
  }
}
$ordem = $_SESSION["ORDEM_USUARIOACESSO"];
$ordempos = $_SESSION["ORDEMPOS_USUARIOACESSO"];

//********************
//********************
// CAIXA
//********************
//********************
$caixa_usuarioacesso = 1;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if ($_POST["CAIXA_USUARIOACESSO"] != "") {
    if (strval($_POST["CAIXA_USUARIOACESSO"]) != strval($_SESSION["CAIXA_USUARIOACESSO"])) {
      $pagina = 1;
    }
    $caixa_usuarioacesso = intval($_POST["CAIXA_USUARIOACESSO"]);
    $_SESSION["CAIXA_USUARIOACESSO"] = $caixa_usuarioacesso;
  }
}
if (isset($_SESSION["CAIXA_USUARIOACESSO"])) {
  $caixa_usuarioacesso = intval($_SESSION["CAIXA_USUARIOACESSO"]);
}
else {
  $_SESSION["CAIXA_USUARIOACESSO"] = $caixa_usuarioacesso;
}
//********************
//********************
// EOF CAIXA
//********************
//********************

// EOF Controle de paginação
// EOF Variaveis e inicializacao

abre_db();

// Captura de paginação
$strsql = "
select 
sum(1) as qtdreg
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = {$chave_usuario}
";
$qusuarioacesso = $pdo->prepare($strsql);
$qusuarioacesso->execute();
if ($tusuarioacesso = $qusuarioacesso->fetch(PDO::FETCH_ASSOC)) {
  $qtdregtotal = $tusuarioacesso["qtdreg"];
}
$strsql = "
select 
sum(1) as qtdreg_filtragem 
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = {$chave_usuario}
";
if ($procurar != "") {
  $strsql = $strsql . " and " . (($procurar_exceto != "1") ? " " : " not ") .
    "(
    tusuarioacesso.ip_usuarioacesso like '%{$procurar}%'
    )";
}
$qusuarioacesso = $pdo->prepare($strsql);
$qusuarioacesso->execute();
if ($tusuarioacesso = $qusuarioacesso->fetch(PDO::FETCH_ASSOC)) {
  $qtdreg = $tusuarioacesso['qtdreg_filtragem'];
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
    $_SESSION["FIRSTPAGE_USUARIOACESSO"] = "0";
    $_SESSION["LASTPAGE_USUARIOACESSO"] = "0";
  }
  if ($pagina == "FIRST") {
    $pagina = "1";
  }
  if ($pagina == "LAST") {
    $pagina = $qtdpagina;
  }
  $pagina = intval($pagina);
  $paginainicial = $_SESSION["FIRSTPAGE_USUARIOACESSO"];
  $paginafinal = $_SESSION["LASTPAGE_USUARIOACESSO"];
  // Início padrão, sem página informada
  if (intval($paginainicial) <= 0 || $pagina == 1) {
    $paginainicial = 1;
    $paginafinal = ($paginainicial + 5) - 1;
    $_SESSION["FIRSTPAGE_USUARIOACESSO"] = $paginainicial;
    $_SESSION["LASTPAGE_USUARIOACESSO"] = $paginafinal;
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
      $_SESSION["FIRSTPAGE_USUARIOACESSO"] = $paginainicial;
      $_SESSION["LASTPAGE_USUARIOACESSO"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $paginafinal) {
      $paginainicial = $paginainicial + 1;
      $paginafinal = $paginafinal + 1;
      $_SESSION["FIRSTPAGE_USUARIOACESSO"] = $paginainicial;
      $_SESSION["LASTPAGE_USUARIOACESSO"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $qtdpagina) {
      $paginafinal = $qtdpagina;
      $paginainicial = ($paginafinal - 5) + 1;
      $_SESSION["FIRSTPAGE_USUARIOACESSO"] = $paginainicial;
      $_SESSION["LASTPAGE_USUARIOACESSO"] = $paginafinal;
      $paginado = true;
    }
  }
  $_SESSION["CURRENTPAGE_USUARIOACESSO"] = $pagina;
}
// EOF Captura de paginação

// Browse
$strsql = "
select 
tusuarioacesso.*
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = {$chave_usuario} 
";
if ($procurar != "") {
  $strsql = $strsql . " and " . (($procurar_exceto != "1") ? " " : " not ") .
    "(
    tusuarioacesso.ip_usuarioacesso like '%{$procurar}%'
    )";
}
$strsql = $strsql . "
order by {$ordem} " . ($ordempos == "desc" ? " " . $ordempos : $ordempos) . "
limit {$qtdlimite}
";
if (intval($pagina) > 1) {
  $offset = ((intval($pagina) - 1) * 20);
  $strsql = $strsql . ' offset ' . $offset;
}
$qusuarioacesso = $pdo->prepare($strsql);
$qusuarioacesso->execute();
$contetq = 0;
$etq = "";
$qtdregpagina = 0;
while ($tusuarioacesso = $qusuarioacesso->fetch(PDO::FETCH_ASSOC)) {
  $chave_usuarioacesso = $tusuarioacesso["chave_usuarioacesso"];
  $ip_usuarioacesso = $tusuarioacesso["ip_usuarioacesso"];
  $dta_usuarioacesso = formatadata($tusuarioacesso["dta_usuarioacesso"], "d/m/Y H:i");
  $etq = $etq . '              <tr>' . "\n";
  $etq = $etq . '                <td><a href="#" id="SHOW_USUARIOACESSO" data-id="' . $chave_usuarioacesso . '">' . str_pad($chave_usuarioacesso, 9, "0", STR_PAD_LEFT) . '</a></td>' . "\n";
  $etq = $etq . '                <td data-id_dbg="' . $chave_usuario . '" class="SELECIONA_LINHA">' . $ip_usuarioacesso . '</td>' . "\n";
  $etq = $etq . '                <td data-id_dbg="' . $chave_usuario . '" class="SELECIONA_LINHA">' . $dta_usuarioacesso . '</td>' . "\n";
  $etq = $etq . '              </tr>' . "\n";
  $contetq = $contetq + 1;
  $qtdregpagina = $qtdregpagina + 1;
  if ($contetq == 4) {
    $contetq = 0;
  }
}
// EOF Browse

$html = "";
$html = $html . '        <h5 class="card-title mt-0 mb-0 text-center">Usuários</h5>' . "\n";

// Pesquisa
$html = $html . '        <form class="mb-0" action="usuario_edita.php" id="FUSUARIOACESSO_FILTRO" name="FUSUARIOACESSO_FILTRO" method="POST">' . "\n";
$html = $html . '          <input type="hidden" id="RESET_USUARIOACESSO" name="RESET_USUARIOACESSO" value="">' . "\n";
$html = $html . '          <input type="hidden" id="CAIXA_USUARIOACESSO" name="CAIXA_USUARIOACESSO" value="">' . "\n";
$html = $html . '          <div class="row">' . "\n";
$html = $html . '            <div class="col-sm-12 ml-2 mr-2">' . "\n";
$html = $html . '              <div class="form-row align-items-center">' . "\n";
$html = $html . '                <div class="col-sm-3 my-1">' . "\n";
$html = $html . '                  <label class="sr-only" for="PROCURAR">Procurar</label>' . "\n";
$html = $html . '                  <input type="text" class="form-control form-control-sm' . ($procurar != "" ? ' form-danger' : '') . '" onkeyup="javascript:procurar_avaliatecla(event);" id="PROCURAR" name="PROCURAR" value="' . $procurar . '" placeholder="Pesquisar">' . "\n";
$html = $html . '                </div>' . "\n";
$html = $html . '                <div class="col-auto my-1">' . "\n";
$html = $html . '                  <div class="form-check">' . "\n";
$html = $html . '                    <input class="form-check-input" type="checkbox" id="PROCURAR_EXCETO" name="PROCURAR_EXCETO" onclick="javascript:procurar_usuarioacesso();" ' . (($procurar_exceto == "1") ? ' checked' : '') . '>' . "\n";
$html = $html . '                    <label class="form-check-label" for="PROCURAR_EXCETO">Exceto</label>' . "\n";
$html = $html . '                  </div>' . "\n";
$html = $html . '                </div>' . "\n";
$html = $html . '                <div class="col-auto my-1">' . "\n";
$html = $html . '                  <button type="button" class="btn btn-sm btn-outline-success" id="BTN_OK" name="BTN_OK" onclick="javascript:procurar_usuarioacesso();">Procurar</button>' . "\n";
$html = $html . '                  <button type="button" class="btn btn-sm btn-info" id="BTN_MAISFILTROS" name="BTN_MAISFILTROS" onclick="javascript:parent.location=\'cliente_exportar.php\'">+ Filtros</button>' . "\n";
if ($procurar != "") {
  $html = $html . '                  <button type="button" class="btn btn-sm btn-warning" id="BTN_REDEFINIR" name="BTN_REDEFINIR" onclick="javascript:redefinir_usuarioacesso();">Redefinir</button>' . "\n";
}
$html = $html . '                </div>' . "\n";
$html = $html . '              </div>' . "\n";
$html = $html . '            </div>' . "\n";
$html = $html . '          </div>' . "\n";
$html = $html . '        </form>' . "\n";
// EOF Pesquisa

// Corpo
$html = $html . '        <div class="card ml-2 mr-2" style="background-color:#EEEDDD">' . "\n";
$html = $html . '          <div class="card-header mt-0 mb-0">' . "\n";
if ($qtdreg <= 0) {
  $html = $html . '            <a class="btn btn-light btn-sm">Nenhum registro</a>' . "\n";
}
else {
  $html = $html . '            <a class="btn btn-light btn-sm">Registros: ' . $qtdregpagina . '/' . $qtdreg . '</a>' . "\n";
}
$html = $html . '          </div>' . "\n";
$html = $html . '          <form action="usuario_edita.php" id="FUSUARIOACESSO" name="FUSUARIOACESSO" method="POST">' . "\n";
$html = $html . '            <input type="hidden" id="chave_usuarioacesso" name="chave_usuarioacesso" value="">' . "\n";
$html = $html . '            <input type="hidden" id="chave_usuario" name="chave_usuario" value="' . $chave_usuario . '">' . "\n";
if ($etq != "") {
  $html = $html . '            <table class="table table-bordered table-hover table-sm CorBGBrowse mb-0">' . "\n";
  $html = $html . '              <thead class="thead-light">' . "\n";
  $html = $html . '                <tr>' . "\n";
  $html = $html . '                  <th><a href="' . $Raiz . 'sistema/usuario/usuarioacesso.php?ORDEM=CHAVE_USUARIOACESSO">Chave</a></td>' . "\n";
  $html = $html . '                  <th><a href="' . $Raiz . 'sistema/usuario/usuarioacesso.php?ORDEM=IP_USUARIOACESSO">IP (Internet Protocol)</a></td>' . "\n";
  $html = $html . '                  <th><a href="' . $Raiz . 'sistema/usuario/usuarioacesso.php?ORDEM=DTA_USUARIOACESSO">Cadastro</a></td>' . "\n";
  $html = $html . '                  <th></td>' . "\n";
  $html = $html . '                </tr>' . "\n";
  $html = $html . '              </thead>' . "\n";
  $html = $html . '              <tbody>' . "\n";
  $html = $html . $etq . "\n";
  $html = $html . '              </tbody>' . "\n";
  $html = $html . '            </table>' . "\n";
}
$html = $html . '          </form>' . "\n";
// Botões de paginação inferior
$html = $html . '          <div class="card-footer text-muted">' . "\n";
if ($qtdpagina > 0 && ($qtdreg > $qtdlimite)) {
  $html = $html . '            <div class="col-sm-12 mb-2">' . "\n";
  $html = $html . '              <nav aria-label="Páginas de navegação">' . "\n";
  $html = $html . '                <ul class="pagination mb-0">' . "\n";
  $html = $html . '                  <li class="page-item"><a class="page-link" href="usuarioacesso.php?PG=FIRST' . (($_SESSION['SEARCHSTRING_USUARIOACESSO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIOACESSO'] : "") . '">&laquo;</a></li>' . "\n";
  if ($qtdpagina > 0) {
    $cont_apagina = count($apagina);
    for ($i = 0; $i <= $cont_apagina - 1; $i++) {
      if (($i + 1) >= $paginainicial && ($i + 1) <= $paginafinal) {
        if (($i + 1) == intval($pagina)) {
          $html = $html . '                  <li class="page-item active"><a class="page-link" href="usuarioacesso.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_USUARIOACESSO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIOACESSO'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
        else {
          $html = $html . '                  <li class="page-item"><a class="page-link" href="usuarioacesso.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_USUARIOACESSO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIOACESSO'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
      }
    }
  }
  $html = $html . '                  <li class="page-item"><a class="page-link" href="usuarioacesso.php?PG=LAST' . (($_SESSION['SEARCHSTRING_USUARIOACESSO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIOACESSO'] : "") . '" aria-label="Último"><span aria-hidden="true">&raquo;</span></a></li>' . "\n";
  $html = $html . '                </ul>' . "\n";
  $html = $html . '              </nav>' . "\n";
  $html = $html . '            </div>' . "\n";
}
// Resumo da página
if ($qtdregtotal > 0 and ($qtdregtotal != $qtdreg)) {
  $html = $html . '            <div class="col-sm-12 mb-2">' . "\n";
  if ($qtdregtotal - $qtdreg == 1) {
    $html = $html . '              <a href="javascript:redefinir_usuarioacesso();"><span>Há ' . ($qtdregtotal - $qtdreg) . ' registro que não satisfaz a pesquisa.</span></a><br />' . "\n";
  }
  else {
    $html = $html . '              <a href="javascript:redefinir_usuarioacesso();"><span>Há mais ' . ($qtdregtotal - $qtdreg) . ' registros que não satisfazem a pesquisa.</span></a><br />' . "\n";
  }
  $html = $html . '            </div>' . "\n";
}
// EOF Resumo da página
$html = $html . '            <div class="col-sm-12 text-center">' . "\n";
$html = $html . '              <button type="button" class="btn btn-sm btn-primary" id="BTN_USUARIOACESSO_INCLUIR" name="BTN_USUARIOACESSO_INCLUIR">Incluir</button>' . "\n";
$html = $html . '            </div>' . "\n";
$html = $html . '          </div>' . "\n";
// EOF Botões de paginação inferior
$html = $html . '        </div>' . "\n";
// EOF Corpo
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>HELP4U Emotion - Usuários</title>
  <?php include($Raiz . "include/html/header_adm.html"); ?>
</head>

<script type="text/javascript">

  function procurar_usuarioacesso() {
    if (document.getElementById("PROCURAR").value != "") {
      document.getElementById("RESET_USUARIOACESSO").value = "NAO";
      document.getElementById("FUSUARIOACESSO_FILTRO").submit();
    }
    else {
      document.getElementById("RESET_USUARIOACESSO").value = "SIM";
      document.getElementById("FUSUARIOACESSO_FILTRO").submit();
    }
  }

  function redefinir_usuarioacesso() {
    document.getElementById("RESET_USUARIOACESSO").value = "SIM";
    document.getElementById("FUSUARIOACESSO_FILTRO").submit();
  }

  function procurar_avaliatecla(e) {
    if (e.key == "Enter") {
      procurar_usuarioacesso();
    }
  }

  function submeter() {
    document.FUSUARIOACESSO.submit();
  };

  $(document).on("keyup", function (event) {
    if (event.key == "Escape") {


      document.FUSUARIOACESSO.submit();

      //		window.location.href = "usuarioacesso.php";
    }
  });
</script>

<body>
  <?php include($Raiz . "include/php/menu_adm.php"); ?>
  <div class="row">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background-color:#99C">
          <li class="breadcrumb-item"><a href="#">Usuário</a></li>
          <li class="breadcrumb-item"><a href="#">Acessos</a></li>
          <li class="breadcrumb-item active" aria-current="page">Data</li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="row">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <?php echo $html; ?>
        </div>
      </div>
    </div>
  </div>
  <?php include($Raiz . "include/html/rodape_adm.html"); ?>
  <script type="text/javascript">
    $(document).ready(function () {
      $(".SELECIONA_LINHA").click(function () {
        document.getElementById("chave_usuario").value = $(this).data("id_dbg");
        document.getElementById("FUSUARIOACESSO").submit();
      });
    });
  </script>
</body>

</html>