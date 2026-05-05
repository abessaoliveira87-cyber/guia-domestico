<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /conexao/db.php
***** Conteúdo: Configurações de conexão com banco de dados
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
//********************
//********************
//**** Variáveis de Conexao do Banco de Dados
//********************
//********************
global $BaseDados;
global $StrConexao;
$BaseDados['servidor'] = 'localhost';
$BaseDados['usuario'] = 'root';
$BaseDados['senha'] = '';
$BaseDados['banco'] = 'guiadomestico';
$StrConexao = 'mysql:host='.$BaseDados['servidor'].';dbname='.$BaseDados['banco'].';charset=utf8';
//********************
//********************
//**** EOF Variáveis de Conexao do Banco de Dados
//********************
//********************

//********************
//********************
//**** Abre Conexao Banco de Dados
//********************
//********************
function abre_db() {
  try {
     global $StrConexao; 
     global $BaseDados;
     global $pdo;
     $pdo = new PDO($StrConexao, $BaseDados['usuario'], $BaseDados['senha']);	 
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     
  }
  catch(PDOException $e) {
     //header("Location: /guiadomestico/erro/index.php?MSGERRO=" . $e->getMessage());
     echo $e->getMessage();
     die();
  }
}
//********************
//********************
//**** EOF Abre Conexao Banco de Dados
//********************
//********************

//********************
//********************
//**** Fecha Conexao Banco de Dados
//********************
//********************
function fecha_db() {
  $pdo = null;	
}
//********************
//********************
//**** EOF Fecha Conexao Banco de Dados
//********************
//*******************
?>