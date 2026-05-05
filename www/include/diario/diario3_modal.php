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
session_start();
$Raiz = "../../";
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
if ($_SERVER['REQUEST_METHOD'] != "GET") {
   header("Location: " . $Raiz . "sistema/index.php");
   die();	
}
// Avalia informação para inclusão
$chave_diario = 0;
$obs_diario = "";
$ok = true;
if (!isset($_GET["ID"])) {
	$ok = false;
}
if ($ok) {
	$chave_diario = $_GET["ID"];
  abre_db();
	$strsql = "
  select 
  obs_diario
  from 
  tdiario
  where 
  chave_diario = :vchave_diario
  ";
  $qdiario = $pdo->prepare($strsql);
  $qdiario->bindParam(":vchave_diario", $chave_diario);
  $qdiario->execute();
  if ($tdiario = $qdiario->fetch(PDO::FETCH_ASSOC)){
    $obs_diario = $tdiario['obs_diario'];
  }
  fecha_db();
}
echo $obs_diario;
?>