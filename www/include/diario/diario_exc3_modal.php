<?php 
/*
**************************************************
**************************************************
***** 
***** DIÁRIO DO REGISTRO
***** EXECUÇÃO DA EXCLUSÃO/RECUPERAÇÃO DO REGISTRO
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
include($Raiz . "include/php/funcoes.php"); // Funcoes Globais PHP
include($Raiz . "conexao/db.php");

if ($_SERVER['REQUEST_METHOD'] != "POST") {
   header("Location: " . $Raiz . "sistema/index.php");
   die();	
}

// Avalia informação para inclusão
$html_titulo = "";
$html_texto = "";
$ok = true;
if (!isset($_POST["chavetab_diario"])) {
   $ok = false;
   $html_texto = $html_texto . "Chave primária inválida.<br>";   
}
if (!isset($_POST["nometab_diario"])) {
   $ok = false;
   $html_texto = $html_texto . "Nome da tabela inválido.<br>";   
}
if (!isset($_POST["campo_chave"])) {
   $ok = false;
   $html_texto = $html_texto . "Nome do campo chave inválido.<br>";   
}
if (!isset($_POST["campo_caixa"])) {
   $ok = false;
   $html_texto = $html_texto . "Nome do campo caixa inválido.<br>";   
}
if (!isset($_POST["valor_caixa"])) {
   $ok = false;
   $html_texto = $html_texto . "Valor do campo caixa inválido.<br>";   
}
if (!isset($_POST["obs_diario"])) {
   $ok = false;
   $html_texto = $html_texto . "Descrição do motivo inválida.<br>";   
}
if ($ok) {
   $chavetab_diario = $_POST["chavetab_diario"];
   $nometab_diario  = $_POST["nometab_diario"];
   $campo_chave     = $_POST["campo_chave"];
   $campo_caixa     = $_POST["campo_caixa"];
   $valor_caixa     = $_POST["valor_caixa"];
   $obs_diario      = $_POST["obs_diario"];
}
if (!$ok) {
   $html_titulo = "Erro";
}
else {
	abre_db();   
    // Exclusão/recuperação do registro	   
    $strsql = "
      update 
      {$nometab_diario} 
      set 
      {$campo_caixa} = :vvalor_caixa 
      where  
      {$campo_chave}  = :vchavetab_diario
    ";
	$qdiario = $pdo->prepare($strsql);
    $qdiario->bindParam(":vchavetab_diario", $chavetab_diario);
    $qdiario->bindParam(":vvalor_caixa", $valor_caixa);
    $qdiario->execute();
    // EOF Exclusão/recuperação do registro	   

	$tipo_diario = (($valor_caixa == 1) ? "RECUPERACAO" : "EXCLUSAO");
	$usuario_diario = $_SESSION["NOME_USUARIO"];
	
	$strsql = " 
      insert 
      into 
      tdiario
      (nometab_diario
      ,chavetab_diario
      ,tipo_diario
      ,usuario_diario
      ,obs_diario
      ) values 
      (:vnometab_diario
      ,:vchavetab_diario
      ,:vtipo_diario
      ,:vusuario_diario
      ,:vobs_diario
      )";
	$qdiario = $pdo->prepare($strsql);		
    $qdiario->bindParam(":vnometab_diario", $nometab_diario);	
    $qdiario->bindParam(":vchavetab_diario", $chavetab_diario);	
    $qdiario->bindParam(":vtipo_diario", $tipo_diario);	
    $qdiario->bindParam(":vusuario_diario", $usuario_diario);	
    $qdiario->bindParam(":vobs_diario", $obs_diario);	
    $qdiario->execute();
   fecha_db();   	
   $html_titulo = "Obrigado";
   $html_texto = "O registro foi " . (($valor_caixa == "1") ? "Recuperado" : "Excluído") . ".<br>"; 
}
// EOF Avalia informação para inclusão

?>
<div class="row" style="background-color:#FFF;">
  <div class="container">
    <div class="row">
      <div class="col-sm-2">
      </div>
      <div class="col-sm-8 ml-2 mr-2">
        <h3><?php echo $html_titulo; ?></h3>
        <br />
        <p class="Fonte_Open_Sans"><?php echo $html_texto; ?></p>
      </div>
      <div class="col-sm-2">
      </div>
    </div>
  </div>
</div>
