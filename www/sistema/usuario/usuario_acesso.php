<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /sistema/usuario/usuario_acesso.php
***** Conteúdo: Log de acessos de usuários
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
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
include($Raiz . "conexao/db.php");
$Home = $Raiz . "sistema/usuario/usuario.php";
VerificaAdmin($Raiz);

// Variáveis de inicialização
$ok = true;
$pagina_titulo = "Cadastro Log de Acessos de Usuários";
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

$chave_usuario = "";
if ($_GET["ID"]) {
  $chave_usuario = $_GET["ID"];
}

//********************
//********************
//**** RESET
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
//**** Controle de paginação
//********************
//********************
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
else {
  $procurar_exceto = "";
  $_SESSION["SEARCHEXCETO_USUARIOACESSO"] = $procurar_exceto;
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
//**** Cadastrados ou Excluídos
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
// EOF - Cadastrados ou Excluídos
//********************
//********************

abre_db();
// Captura de paginação
$strsql = "
select 
sum(1) as qtdreg
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = :vchave_usuario and 
tusuarioacesso.caixa_usuarioacesso = 1'
";
$qusuario = $pdo->prepare($strsql);
$qusuario->bindParam(":vchave_usuario", $chave_usuario);
$qusuario->execute();
if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {
  $qtdregtotal = $tusuario["qtdreg"];
}
$strsql = "
select 
sum(1) as qtdreg_filtragem 
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = :vchave_usuario
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
  "and tusuarioacesso.ip_usuarioacesso like '%{$procurar}%'";
}
$qusuarioacesso = $pdo->prepare($strsql);
$qusuarioacesso->bindParam(":vchave_usuario", $chave_usuario);
$qusuarioacesso->execute();
if ($tusuarioacesso = $qusuarioacesso->fetch(PDO::FETCH_ASSOC)) {
  $qtdreg = $tusuario['qtdreg_filtragem'];
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
tusuarioacesso.*
from 
tusuarioacesso
where 
tusuarioacesso.chave_usuario = :vchave_usuario
";
if ($procurar != "") {
  $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") .
  " and tusuarioacesso.ip_usuarioacesso like '%{$procurar}%'";
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
$etq = '';
$qtdregpagina = 0;
while ($tusuarioacesso = $qusuarioacesso->fetch(PDO::FETCH_ASSOC)) {  
  $ip_usuarioacesso = $tusuarioacesso["ip_usuarioacesso"];  
  //$dtc_usuario = formatadata($tusuario["dtc_usuario"], "d/m/Y H:i");
    
  $etq .= '              <tr>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_usuario . '" class="SELECIONA_LINHA">' . str_pad($chave_usuario, 6, "0", STR_PAD_LEFT) . '</td>' . "\n";
  $etq .= '                <td data-id_dbg="' . $chave_usuario . '" class="SELECIONA_LINHA">' . $ip_usuarioacesso . '</td>' . "\n";
  $etq .= '                <td class="text-center"><a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO"     id="SHOW_DIARIO"     data-id="' . $chave_usuario . '" data-campo-id="chave_usuario" data-tabela-id="tusuario" data-url-id="' . $Raiz . '"><i class="fas fa-database" title="Diário do registro"></i></a></td>' . "\n"; 
  $etq .= '              </tr>' . "\n";
  $contetq = $contetq + 1;
  $qtdregpagina = $qtdregpagina + 1;
  if ($contetq == 4) {
    $contetq = 0;
  }
}
if ($procurar != "") {
  $btn_redefinir = '<button type="button" class="btn btn-sm btn-warning" id="BTN_REDEFINIR" name="BTN_REDEFINIR" onclick="javascript:redefinir_usuario();">Redefinir</button>' . "\n";
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
$html_pesq .= '      <form class="row row-cols-lg-auto float-end" action="usuario.php" id="FUSUARIO_FILTRO" name="FUSUARIO_FILTRO" method="POST">' . "\n"; // Pesquisa
$html_pesq .= '        <input type="hidden" id="RESET_USUARIO" name="RESET_USUARIO" value="">' . "\n";
$html_pesq .= '        <input type="hidden" id="CAIXA_USUARIO" name="CAIXA_USUARIO" value="">' . "\n";
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
$html_pesq .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_PROCURAR" name="BTN_PROCURAR" onclick="javascript:procurar_usuario();">Ok</button>' . "\n";
$html_pesq .= '          </div>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '        <div class="col-sm-6">' . "\n";
$html_pesq .= $btn_redefinir;
$html_pesq .= '          <button type="button" class="btn btn-sm btn-secondary" id="BTN_LIXEIRA" name="BTN_LIXEIRA" onclick="javascript:caixa_usuario(\'' . $caixa_usuario . '\')">' . (($caixa_usuario == 1) ? 'Lixeira' : 'Cadastrados') . '</button>' . "\n";
$html_pesq .= '        </div>' . "\n";
$html_pesq .= '      </form>' . "\n";
// EOF Pesquisa

// Corpo
$html .= '      <h5 class="Texto-Titulo">Cadastro de Usuários</h5>' . "\n";
$html .= '      <div class="card shadow">' . "\n";
$html .= '        <div class="card-header m-0">' . "\n";
$html .= '          ' . $html_pesq . "\n";
$html .= '        </div>' . "\n";
$html .= '        <form action="usuario_edita.php" id="FUSUARIO" name="FUSUARIO" method="POST">' . "\n";
$html .= '          <input type="hidden" id="chave_usuario" name="chave_usuario" value="">' . "\n";
$html .= '          <input type="hidden" id="acao_usuario" name="acao_usuario" value="EDITAR">' . "\n";
if ($etq != "") {
  $html .= '          <table class="table table-hover table-sm mb-0">' . "\n";
  $html .= '            <thead class="thead-light">' . "\n";
  $html .= '              <tr>' . "\n";
  $html .= '                <th><a href="' . $Raiz . 'sistema/usuario/usuario.php?ORDEM=CHAVE_USUARIOACESSO">Chave</a></th>' . "\n";  
  $html .= '                <th><a href="' . $Raiz . 'sistema/usuario/usuario.php?ORDEM=IP_USUARIOACESSO">Nome</a></th>' . "\n";
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
  $html .= '                <li class="page-item"><a class="page-link" href="usuario.php?PG=FIRST' . (($_SESSION['SEARCHSTRING_USUARIO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIO'] : "") . '">&laquo;</a></li>' . "\n";
  if ($qtdpagina > 0) {
    $cont_apagina = count($apagina);
    for ($i = 0; $i <= $cont_apagina - 1; $i++) {
      if (($i + 1) >= $paginainicial && ($i + 1) <= $paginafinal) {
        if (($i + 1) == intval($pagina)) {
          $html .= '                <li class="page-item active"><a class="page-link" href="usuario.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_USUARIO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIO'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
        else {
          $html .= '                <li class="page-item"><a class="page-link" href="usuario.php?PG=' . $apagina[$i] . (($_SESSION['SEARCHSTRING_USUARIO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIO'] : "") . '">' . $apagina[$i] . '</a></li>' . "\n";
        }
      }
    }
  }
  $html .= '                <li class="page-item"><a class="page-link" href="usuario.php?PG=LAST' . (($_SESSION['SEARCHSTRING_USUARIO'] != "") ? '&PROCURAR=' . $_SESSION['SEARCHSTRING_USUARIO'] : "") . '" aria-label="Último"><span aria-hidden="true">&raquo;</span></a></li>' . "\n";
  $html .= '              </ul>' . "\n";
  $html .= '            </nav>' . "\n";
  $html .= '          </div>' . "\n";
}
// Resumo da página
if ($qtdregtotal > 0 and ($qtdregtotal != $qtdreg)) {
  $html .= '          <div class="col-sm-12 mb-2">' . "\n";
  if ($qtdregtotal - $qtdreg == 1) {
    $html .= '            <a href="javascript:redefinir_usuario();"><span>Há ' . ($qtdregtotal - $qtdreg) . ' registro que não satisfaz a pesquisa.</span></a><br />' . "\n";
  }
  else {
    $html .= '            <a href="javascript:redefinir_usuario();"><span>Há mais ' . ($qtdregtotal - $qtdreg) . ' registros que não satisfazem a pesquisa.</span></a><br />' . "\n";
  }
  $html .= '          </div>' . "\n";
}
// EOF Resumo da página
$html .= '          <div class="col-sm-12 text-center">' . "\n";
$html .= '            <button type="button" class="btn btn-sm btn-custom" id="BTN_USUARIO_INCLUIR" name="BTN_USUARIO_INCLUIR">Incluir</button>' . "\n";
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
<title>Guia Doméstico - Cadastro de Usuários</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function caixa_usuario(ccaixa) {
    var vcaixa = 1;
    if (ccaixa == 1) {
      vcaixa = 0;
    }
    document.getElementById("CAIXA_USUARIO").value = vcaixa;
    document.getElementById("FUSUARIO_FILTRO").submit();
    //	parent.location='<?php echo $Raiz; ?>sistema/usuario/usuario.php?CAIXA_USUARIO=' + vcaixa;
  }

  function procurar_usuario() {
    if (document.getElementById("PROCURAR").value != "") {
      document.getElementById("RESET_USUARIO").value = "NAO";
      document.getElementById("FUSUARIO_FILTRO").submit();
    }
    else {
      document.getElementById("RESET_USUARIO").value = "SIM";
      document.getElementById("FUSUARIO_FILTRO").submit();
    }
  }

  function redefinir_usuario() {
    document.getElementById("RESET_USUARIO").value = "SIM";
    document.getElementById("FUSUARIO_FILTRO").submit();
  }

  function procurar_avaliatecla(e) {
    if (e.key == "Enter") {
      procurar_usuario();
    }
  }

  function usuario_acesso(cchave_usuario) {
    alert(cchave_usuario);

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
        document.getElementById("chave_usuario").value = $(this).data("id_dbg");
        document.getElementById("FUSUARIO").submit();
      });
      $("#BTN_USUARIO_INCLUIR").click(function () {
        document.getElementById("chave_usuario").value = "0";
        document.getElementById("FUSUARIO").submit();
      });
    });
  </script>
</body>

</html>