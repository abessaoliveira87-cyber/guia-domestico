<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: usuario_logout.php
***** Conteúdo: Logout de usuário
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
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
/*
  // Inclusão do acesso
  $strsql = "
  insert 
  into 
  tusuarioacesso
  (chave_usuario
  ,acao_usuarioacesso
  ,ip_usuarioacesso
  ) values 
  (:vchave_usuario
  ,:vacao_usuarioacesso
  ,:vip_usuarioacesso
  )";
  $qusuarioacesso = $pdo->prepare($strsql);
  $qusuarioacesso->bindParam(":vchave_usuario", $_SESSION["CHAVE_USUARIO"]);
  $qusuarioacesso->bindParam(":vacao_usuarioacesso", $acao_usuarioacesso);		
  $qusuarioacesso->bindParam(":vip_usuarioacesso", $_SERVER['REMOTE_ADDR']);		
  $qusuarioacesso->execute();
  // EOF Inclusao do acesso
*/
$_SESSION["CHAVE_USUARIO"] = "";
$_SESSION["NOME_USUARIO"] = "";
$_SESSION["LOGIN_USUARIO"] = "";
$_SESSION["TIPO_USUARIO"] = "";
$_SESSION["CHAVE_USUARIOLOGIN"] = "";
$_SESSION["NOME_USUARIOLOGIN"] = "";
$_SESSION["LOGIN_USUARIOLOGIN"] = "";
$_SESSION["SESSAO_INICIO"] = "";
$_SESSION["SESSAO_EXPIRA"] = 0;
$_SESSION["CHAVE_CARGO"] = "";
$_SESSION["DESCR_CARGO"] = "";
$_SESSION["CBO_CARGO"] = "";
$_SESSION["SALARIO_USUARIO"] = "";
$_SESSION["DTI_USUARIO"] = "";
$_SESSION["HRDIA_USUARIO"] = "";
$_SESSION["HRDIASAB_USUARIO"] = "";
$_SESSION["DIASEMANA_USUARIO"] = "";
fecha_db();
session_destroy();
header("Location: /index.php");
die();
?>