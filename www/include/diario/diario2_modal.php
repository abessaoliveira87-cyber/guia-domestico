<!-- Modal Diário -->
<?php
/*
**************************************************
**************************************************
***** 
***** DIÁRIO DO REGISTRO
*****
**************************************************
**************************************************

**************************************************
**************************************************
***** DESENVOLVIDO POR:
*****
***** ONTOP SOFTWARE EIRELI
***** Elenir Freitas de Sousa
***** Celular: (++55 11) 9-8236-3076
***** Website: www.ontop.com.br
***** E-mail: elenircombr@gmail.com
**************************************************
**************************************************
*/
$Raiz = "../../";
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
//VerificaAdmin($Raiz); 
// Variáveis de inicialização
$qtdlimite = 6;
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
// Reset
if (isset($_GET["RESET"])) {
  if ($_GET["RESET"] == "SIM") {
    $_SESSION["FIRSTPAGE_DIARIO"] = "0";
    $_SESSION["LASTPAGE_DIARIO"] = "0";
    $_SESSION["CURRENTPAGE_DIARIO"] = "1";
    $_SESSION["SEARCHSTRING_DIARIO"] = "";
    $_SESSION["SEARCHEXCETO_DIARIO"] = "";
    $_SESSION["ORDEM_DIARIO"] = "CHAVE_DIARIO";
    $_SESSION["ORDEMPOS_DIARIO"] = "desc";
    header("Location:" . $Raiz . "sistema/index.php");
    die();
  }
}
// EOF Reset
// Controle de paginação
if (!isset($_SESSION["FIRSTPAGE_DIARIO"])) {
  $_SESSION["FIRSTPAGE_DIARIO"] = "0";
  $_SESSION["LASTPAGE_DIARIO"] = "0";
  $_SESSION["CURRENTPAGE_DIARIO"] = "1";
  $_SESSION["SEARCHSTRING_DIARIO"] = "";
  $_SESSION["SEARCHEXCETO_DIARIO"] = "";
  $_SESSION["ORDEM_DIARIO"] = "CHAVE_DIARIO";
  $_SESSION["ORDEMPOS_DIARIO"] = "desc";
}
if (isset($_GET['PG'])) {
  $pagina = $_GET['PG'];
}
else {
  if ($_SESSION["CURRENTPAGE_DIARIO"] != "") {
    $pagina = $_SESSION["CURRENTPAGE_DIARIO"];
  }
}
if (isset($_GET['PROCURAR'])) {
  $procurar = $_GET['PROCURAR'];
  $pagina = 1;
  $_GET['PG'] = $pagina;
  $_SESSION["SEARCHSTRING_DIARIO"] = $procurar;
}
$procurar = $_SESSION["SEARCHSTRING_DIARIO"];
if (isset($_GET['PROCURAR_EXCETO'])) {
  $procurar_exceto = $_GET['PROCURAR_EXCETO'];
  $pagina = 1;
  $_GET['PG'] = $pagina;
  $_SESSION["SEARCHEXCETO_DIARIO"] = $procurar_exceto;
}
$procurar_exceto = $_SESSION["SEARCHEXCETO_DIARIO"];
if (isset($_GET['ORDEM'])) {
  $ordem = strtolower($_GET['ORDEM']);
  if ($ordem != $_SESSION["ORDEM_DIARIO"]) {
    $_SESSION["ORDEM_DIARIO"] = $ordem;
    $_SESSION["ORDEMPOS_DIARIO"] = "";
  }
  else {
    if ($_SESSION["ORDEMPOS_DIARIO"] == "") {
      $_SESSION["ORDEMPOS_DIARIO"] = "desc";
    }
    else {
      $_SESSION["ORDEMPOS_DIARIO"] = "";
    }
  }
}
if (isset($_GET['ORDEMPOS'])) {
  $ordempos = strtolower($_GET['ORDEMPOS']);
  if ($ordempos != $_SESSION["ORDEMPOS_DIARIO"]) {
    $_SESSION["ORDEMPOS_DIARIO"] = $ordempos;
  }
}
$ordem = $_SESSION["ORDEM_DIARIO"];
$ordempos = $_SESSION["ORDEMPOS_DIARIO"];
// EOF Controle de paginação
// EOF Variaveis e inicializacao
$ok = true;
if ($_SERVER['REQUEST_METHOD'] != "GET") {
  $ok = false;
}
if ($ok) {
  if (!isset($_GET["ID"])) {
    $ok = false;
  }
}
if ($ok) {
  if (!isset($_GET["CA"])) {
    $ok = false;
  }
}
if ($ok) {
  if (!isset($_GET["TB"])) {
    $ok = false;
  }
}
if ($ok) {
  if (!isset($_GET["URL"])) {
    $ok = false;
  }
}
if (!$ok) {
  //    header("Location: " . $Raiz . "sistema/index.php");
//    die();	
}
if (isset($_GET['ID'])) {
  $chavetab_diario = $_GET["ID"];
  $_SESSION["ID_DIARIO"] = $chavetab_diario;
}
else {
  $chavetab_diario = $_SESSION["ID_DIARIO"];
}
if (isset($_GET['TB'])) {
  $nometab_diario = $_GET["TB"];
  $_SESSION["TB_DIARIO"] = $nometab_diario;
}
else {
  $nometab_diario = $_SESSION["TB_DIARIO"];
}
if (isset($_GET['CA'])) {
  $campo_chave = $_GET["CA"];
  $_SESSION["CA_DIARIO"] = $campo_chave;
}
else {
  $campo_chave = $_SESSION["CA_DIARIO"];
}
if (isset($_GET['URL'])) {
  $url = $_GET["URL"];
  $_SESSION["URL_DIARIO"] = $url;
}
else {
  $url = $_SESSION["URL_DIARIO"];
}
$dta_diario = date("d/m/Y H:i");
$obs_diario = "";
$valor_caixa = "1";
abre_db();
// Captura de paginação
$strsql = "
select 
sum(1) as qtdreg 
from 
tdiario
where 
tdiario.nometab_diario = :vnometab_diario and 
tdiario.chavetab_diario = :vchavetab_diario";
$qdiario = $pdo->prepare($strsql);
$qdiario->bindParam(":vnometab_diario", $nometab_diario);
$qdiario->bindParam(":vchavetab_diario", $chavetab_diario);
$qdiario->execute();
if ($tdiario = $qdiario->fetch(PDO::FETCH_ASSOC)) {
  $qtdregtotal = $tdiario["qtdreg"];
}
$strsql = "
select 
sum(1) as qtdreg_filtragem 
from 
tdiario
where 
tdiario.nometab_diario = :vnometab_diario and 
tdiario.chavetab_diario = :vchavetab_diario
";
if ($procurar != "") {
}
$qdiario = $pdo->prepare($strsql);
$qdiario->bindParam(":vnometab_diario", $nometab_diario);
$qdiario->bindParam(":vchavetab_diario", $chavetab_diario);
$qdiario->execute();
if ($tdiario = $qdiario->fetch(PDO::FETCH_ASSOC)) {
  $qtdreg = $tdiario['qtdreg_filtragem'];
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
    $pagina = '1';
    $_SESSION["FIRSTPAGE_DIARIO"] = "0";
    $_SESSION["LASTPAGE_DIARIO"] = "0";
  }
  if ($pagina == 'FIRST') {
    $pagina = '1';
  }
  if ($pagina == 'LAST') {
    $pagina = $qtdpagina;
  }
  $pagina = intval($pagina);
  $paginainicial = $_SESSION["FIRSTPAGE_DIARIO"];
  $paginafinal = $_SESSION["LASTPAGE_DIARIO"];
  // Início padrão, sem página informada
  if (intval($paginainicial) <= 0 || $pagina == 1) {
    $paginainicial = 1;
    $paginafinal = ($paginainicial + 5) - 1;
    $_SESSION["FIRSTPAGE_DIARIO"] = $paginainicial;
    $_SESSION["LASTPAGE_DIARIO"] = $paginafinal;
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
      $_SESSION["FIRSTPAGE_DIARIO"] = $paginainicial;
      $_SESSION["LASTPAGE_DIARIO"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $paginafinal) {
      $paginainicial = $paginainicial + 1;
      $paginafinal = $paginafinal + 1;
      $_SESSION["FIRSTPAGE_DIARIO"] = $paginainicial;
      $_SESSION["LASTPAGE_DIARIO"] = $paginafinal;
      $paginado = true;
    }
  }
  if (!$paginado) {
    if ($pagina == $qtdpagina) {
      $paginafinal = $qtdpagina;
      $paginainicial = ($paginafinal - 5) + 1;
      $_SESSION["FIRSTPAGE_DIARIO"] = $paginainicial;
      $_SESSION["LASTPAGE_DIARIO"] = $paginafinal;
      $paginado = true;
    }
  }
  $_SESSION["CURRENTPAGE_DIARIO"] = $pagina;
}
// EOF Captura de paginação
// Browse
$strsql = "
select 
tdiario.*
from 
tdiario
where 
tdiario.nometab_diario = :vnometab_diario and 
tdiario.chavetab_diario = :vchavetab_diario
 ";
if ($procurar != "") {
  /*    $strsql = $strsql . (($procurar_exceto != "1") ? " " : " not ") . "(";
   */
}
$strsql = $strsql . "
order by " . $ordem . ($ordempos == "desc" ? " " . $ordempos : $ordempos) . "
limit " . $qtdlimite;
if (intval($pagina) > 1) {
  $offset = ((intval($pagina) - 1) * 6);
  $strsql = $strsql . ' offset ' . $offset;
}
$qdiario = $pdo->prepare($strsql);
$qdiario->bindParam(":vnometab_diario", $nometab_diario);
$qdiario->bindParam(":vchavetab_diario", $chavetab_diario);
$qdiario->execute();
$contetq = 0;
$etq = '';
$qtdregpagina = 0;
$obs_diario = "";
while ($tdiario = $qdiario->fetch(PDO::FETCH_ASSOC)) {
  if ($contetq == 0) {
    $obs_diario = $tdiario["obs_diario"];
  }
  $chave_diario = $tdiario["chave_diario"];
  $dta_diario = formatadata($tdiario["dta_diario"], "d/m/Y H:i");
  $tipo_diario = $tdiario["tipo_diario"];
  $usuario_diario = $tdiario["usuario_diario"];
  $nometab_diario = $tdiario["nometab_diario"];
  $chavetab_diario = $tdiario["chavetab_diario"];
  $etq = $etq . '              <tr class="small seleciona_linha_diario" data-id_diario="' . $chave_diario . '">' . "\n";
  $etq = $etq . '                <td>' . $dta_diario . '</td>' . "\n";
  $etq = $etq . '                <td>' . $tipo_diario . '</td>' . "\n";
  $etq = $etq . '                <td>' . $usuario_diario . '</td>' . "\n";
  $etq = $etq . '                <td>' . $nometab_diario . '</td>' . "\n";
  $etq = $etq . '                <td>' . $chavetab_diario . '</td>' . "\n";
  $etq = $etq . '                <td>' . $chave_diario . '</td>' . "\n";
  $etq = $etq . '              </tr>' . "\n";
  $contetq = $contetq + 1;
  $qtdregpagina = $qtdregpagina + 1;
}
// EOF Browse
$html = "";
// Pesquisa
//$html .= '        <div class="row">' . "\n";
//$html .= '          <div class="col-12">' . "\n";
$html .= '            <div class="row align-items-center">' . "\n";
$html .= '              <div class="col-auto my-1">' . "\n";
$html .= '                <a class="btn btn-light btn-sm" id="LABEL_BROWSE" name="LABEL_BROWSE">Registros: ' . $qtdregpagina . '/' . $qtdreg . '</a>' . "\n";
$html .= '              </div>' . "\n";
$html .= '              <div class="col-auto my-1">' . "\n";
$html .= '                <label class="sr-only" for="PROCURAR">Procurar</label>' . "\n";
$html .= '                <input type="text" class="form-control form-control-sm' . ($procurar != "" ? ' form-danger' : '') . '" onkeyup="javascript:procurar_avaliatecla(event);" id="PROCURAR" name="PROCURAR" value="' . $procurar . '" placeholder="Pesquisar">' . "\n";
$html .= '              </div>' . "\n";
$html .= '              <div class="col-auto my-1">' . "\n";
$html .= '                <div class="form-check">' . "\n";
$html .= '                  <input class="form-check-input" type="checkbox" id="PROCURAR_EXCETO" name="PROCURAR_EXCETO" onclick="javascript:procurar_diario();" ' . (($procurar_exceto == "1") ? ' checked' : '') . '>' . "\n";
$html .= '                  <label class="form-check-label small" for="PROCURAR_EXCETO">Exceto</label>' . "\n";
$html .= '                </div>' . "\n";
$html .= '              </div>' . "\n";
$html .= '              <div class="col-auto my-1">' . "\n";
$html .= '                <button type="button" class="btn btn-outline-success btn-sm" id="BTN_OK" name="BTN_OK" onclick="javascript:procurar_diario();">Procurar</button>' . "\n";
if ($procurar != "") {
  $html .= '                <button type="button" class="btn btn-warning btn-sm" id="BTN_REDEFINIR" name="BTN_REDEFINIR" onclick="javascript:redefinir_diario();">Redefinir</button>' . "\n";
}
$html .= '              </div>' . "\n";
$html .= '            </div>' . "\n";
//$html .= '          </div>' . "\n";
//$html .= '        </div>' . "\n";
// EOF Pesquisa
// Corpo
//$html .= '        <div class="card" style="background-color:#EEEDDD">' . "\n";
$html .= '        <div class="card" style="background-color:#FFF">' . "\n";
if ($etq != "") {
  $html .= '          <table class="table table-bordered table-hover table-sm CorBGBrowse mb-0">' . "\n";
  $html .= '            <thead class="thead-light">' . "\n";
  $html .= '              <tr class="small">' . "\n";
  $html .= '                <th>Data/Hora</a></td>' . "\n";
  $html .= '                <th>Evento</a></td>' . "\n";
  $html .= '                <th>Usuário</a></td>' . "\n";
  $html .= '                <th>Tabela</a></td>' . "\n";
  $html .= '                <th>Chave</a></td>' . "\n";
  $html .= '                <th>Diário</a></td>' . "\n";
  $html .= '              </tr>' . "\n";
  $html .= '            </thead>' . "\n";
  $html .= '            <tbody>' . "\n";
  $html .= $etq . "\n";
  $html .= '            </tbody>' . "\n";
  $html .= '          </table>' . "\n";
  // Botões de paginação inferior
  $html .= '          <div class="card-footer text-muted">' . "\n";
  //    $html .= '            <div class="col-12">' . "\n";
  $html .= '              <label class="sr-only" for="OBS_DIARIO">Procurar</label>' . "\n";
  $html .= '              <textarea class="form-control form-control-sm" id="OBS_DIARIO" name="OBS_DIARIO" rows="6">' . $obs_diario . '</textarea>' . "\n";
  //    $html .= '            </div>' . "\n";	
  if ($qtdpagina > 0 && ($qtdreg > $qtdlimite)) {
    $html .= '            <hr>' . "\n";
    $html .= '            <nav aria-label="Páginas de navegação">' . "\n";
    $html .= '              <ul class="pagination mb-0">' . "\n";
    $html .= '                <li class="page-item"><a class="page-link" href="#" onclick="javascript:Modal_Diario_Reshow(\'FIRST\')">&laquo;</a></li>' . "\n";
    if ($qtdpagina > 0) {
      $cont_apagina = count($apagina);
      for ($i = 0; $i <= $cont_apagina - 1; $i++) {
        if (($i + 1) >= $paginainicial && ($i + 1) <= $paginafinal) {
          if (($i + 1) == intval($pagina)) {
            $html .= '                <li class="page-item active"><a class="page-link" href="#" onclick="Modal_Diario_Reshow(\' . ($i + 1) . \')">' . $apagina[$i] . '</a></li>' . "\n";
          }
          else {
            $html .= '                <li class="page-item"><a class="page-link" href="#" onclick="Modal_Diario_Reshow(\' . ($i + 1) . \')">' . $apagina[$i] . '</a></li>' . "\n";
          }
        }
      }
    }
    $html .= '                <li class="page-item"><a class="page-link" href="#" onclick="javascript:Modal_Diario_Reshow(\'LAST\')">&raquo;</a></li>' . "\n";
    $html .= '              </ul>' . "\n";
    $html .= '            </nav>' . "\n";
  }
  // EOF Botões de paginação inferior
  // Resumo da página
  if ($qtdregtotal > 0 and ($qtdregtotal != $qtdreg)) {
    if ($qtdregtotal - $qtdreg == 1) {
      $html .= '            <a href="' . $Raiz . 'include/diario/diario2_modal.php?ID=' . $chavetab_diario . '&TB=' . $nometab_diario . '&CA=' . $campo_chave . '&URL=' . $url . '&RESET=SIM"><span>Há ' . ($qtdregtotal - $qtdreg) . ' registro que não satisfaz a pesquisa.</span></a><br />' . "\n";
    }
    else {
      $html .= '            <a href="' . $Raiz . 'include/diario/diario2_modal.php?ID=' . $chavetab_diario . '&TB=' . $nometab_diario . '&CA=' . $campo_chave . '&URL=' . $url . '&RESET=SIM"><span>Há mais ' . ($qtdregtotal - $qtdreg) . ' registros que não satisfazem a pesquisa.</span></a><br />' . "\n";
    }
  }
  // EOF Resumo da página
  $html .= '          </div>' . "\n";
}
$html .= '        </div>' . "\n";
// EOF Corpo
echo $html;
?>
<script type="text/javascript">
  $(document).ready(function (e) {
    $(".seleciona_linha_diario").click(function () {
      $.ajax({
        url: '<?php echo $url; ?>include/diario/diario3_modal.php?ID=' + $(this).data("id_diario"),
        method: "GET",
        success: function (data, textStatus, jqXHR) {
          $('#OBS_DIARIO').text(data);
        },
        error: function (jqXHR, status, error) {
          console.log(status + ": " + error);
        }
      });
    });
  });
</script>
<!-- EOF Modal Diário -->