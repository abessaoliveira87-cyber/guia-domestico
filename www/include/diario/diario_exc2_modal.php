<?php
/*
**************************************************
**************************************************
***** 
***** DIÁRIO DO REGISTRO
***** CORPO DO DIÁRIO DO REGISTRO - EXCLUSÃO
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

session_start();
$Raiz = "../../";
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");

VerificaAdmin($Raiz); 

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
    if (!isset($_GET["CX"])) { 
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
    header("Location: " . $Raiz . "sistema/index.php");
    die();	
}

$chavetab_diario = $_GET["ID"];
$nometab_diario  = $_GET["TB"];
$campo_chave     = $_GET["CA"];
$campo_caixa     = $_GET["CX"];
$url             = $_GET["URL"];
$dta_diario      = date("d/m/Y H:i");	
$obs_diario      = "";
$valor_caixa     = "1";

if ($chavetab_diario != "NOVO") {
    abre_db();
	$strsql = "
	  select 
	  {$campo_chave} 
	  ,{$campo_caixa}
	  from 
	  {$nometab_diario} 
	  where 
	  {$campo_chave}  = :vcampo_chave
    ";
    $qdiario = $pdo->prepare($strsql);
    $qdiario->bindParam(":vcampo_chave", $chavetab_diario);
    $qdiario->execute();
    if ($tdiario = $qdiario->fetch(PDO::FETCH_ASSOC)){
        $valor_caixa = $tdiario[$campo_caixa];
    }
    fecha_db();
}
?>
<form method="POST" action="<?php echo $url ?>include/diario/diario_exc3_modal.php" id="FORM_DIARIO_EXC" name="FORM_DIARIO_EXC" enctype="text/plain">
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="valor_caixa">Evento</label>
    <input type="hidden" class="form-control form-control-sm" id="valor_caixa" name="valor_caixa" value="<?php echo ($valor_caixa == "1") ? "0" : "1"; ?>" readonly>
  </div>
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="nometab_diario">Nome Tabela</label>
    <input type="hidden" class="form-control form-control-sm" id="nometab_diario" name="nometab_diario" value="<?php echo $nometab_diario ?>" readonly>
  </div> 
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="campo_chave">Campo Tabela</label>
    <input type="hidden" class="form-control form-control-sm" id="campo_chave" name="campo_chave" value="<?php echo $campo_chave ?>" readonly>
  </div>
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="campo_caixa">Campo Caixa</label>
    <input type="hidden" class="form-control form-control-sm" id="campo_caixa" name="campo_caixa" value="<?php echo $campo_caixa ?>" readonly>
  </div>
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="chavetab_diario">Chave Primária da Tabela</label>
    <input type="hidden" class="form-control form-control-sm" id="chavetab_diario" name="chavetab_diario" value="<?php echo $chavetab_diario ?>" readonly>
  </div>
  <div class="form-group mb-1 d-none">
    <label class="mb-0 small" for="dta_diario">Data/hora</label>
    <input type="hidden" class="form-control form-control-sm" id="dta_diario" name="dta_diario" value="<?php echo $dta_diario ?>" readonly>
  </div>
  <div class="form-group mb-1">
    <label class="mb-0 small" for="obs_diario">Motivo da <?php echo ($valor_caixa == "1") ? "Exclusão" : "Recuperação"; ?></label>
    <textarea class="form-control form-control-sm" id="obs_diario" name="obs_diario" rows="6"></textarea>
  </div>
</form>
<script type="text/javascript">

// Título da janela
$('#MODAL_DIARIO_EXC').find('.modal-header').html('<h5 class="modal-title" id="exampleModalLabel"><?php echo ($valor_caixa == "1") ? "Exclusão" : "Recuperação"; ?> de Registro</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>');
$("#BTN_FORM_DIARIO_EXC_SUBMIT").html('<?php echo ($valor_caixa == "1") ? "Excluir" : "Recuperar"; ?>');

//$("#BTN_FORM_DIARIO_EXC_SUBMIT").
//if($(this).hasClass("course-btn-tab-selected"))
//          $(".btn").removeClass("course-btn-tab-selected").addClass("course-btn-tab");               
//     $(this).addClass("course-btn-tab-selected");        

// EOF Título da janela

// Serialização de campos para aceite de submit via modal
$("#FORM_DIARIO_EXC").on("submit", function(e) {
   var postData = $(this).serializeArray();
   var formURL = $(this).attr("action");
   $.ajax({
     url: formURL,
     type: "POST",
     data: postData,
     success: function(data, textStatus, jqXHR) {
       $('#MODAL_DIARIO_EXC .modal-body').html(data);
       $("#BTN_FORM_DIARIO_EXC_SUBMIT").remove();
     },
     error: function(jqXHR, status, error) {
       console.log(status + ": " + error);
     }
   });
   e.preventDefault();
});
// EOF Serialização de campos para aceite de submit via modal

</script> 
