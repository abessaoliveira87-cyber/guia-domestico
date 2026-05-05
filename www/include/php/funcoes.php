<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: funcoes.php
***** Conteúdo: funções de usuário
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

/*
VerificaSessao();
VerificaAdmin($url);
abre_diario($tabelanome, $tabelachavecampo, $tabelachavevalor, $camposexcluidos);
fecha_diario($tabelanome, $tabelachavecampo, $tabelachavevalor, $abre_diario, $camposexcluidos);
FormataData($data, $mascara);
cpf($cpf);
cnpj($cnpj);
so_numeros($cnumero, $cdefault);
mascara($mascara_valor, $mascara_mascara);
maiusculo($ucase);
minusculo($lcase);
Modal_Exclusao($ModalExcRaiz, $ModalExcLnk);
*/

//********************
//********************
//**** Verifica sessão
//********************
//********************
function VerificaSessao() {
	$VerificaSessaoLogin = "NAO";
	if (isset($_SESSION['SESSAO_INICIO']) && isset($_SESSION['SESSAO_EXPIRA'])) {
		if ((time() - $_SESSION['SESSAO_INICIO']) > $_SESSION['SESSAO_EXPIRA']) {
			session_destroy();
			$VerificaSessaoLogin = "SIM";
		}
		else {
			$_SESSION['SESSAO_INICIO'] = time();
		}
	}
	else {
		$VerificaSessaoLogin = "SIM";
	}
	if ($VerificaSessaoLogin == "SIM") {
		header("Location: /publico/usuario/usuario_login.php");
		die();		
	}	
}
//********************
//********************
//**** EOF - Verifica sessão
//********************
//********************

//********************
//********************
//**** Verifica login do usuário (ADMINISTRADOR)
//********************
//********************
function VerificaAdmin($url) {
	$ok = true;
	if (!isset($_SESSION["CHAVE_USUARIO"])) {
		$ok = false;
	}
	else {
		if ($_SESSION["CHAVE_USUARIO"] == "") {
			$ok = false;
		}
	}
	if ($ok) {
		if ($_SESSION["TIPO_USUARIO"] != "ADMINISTRADOR") {
			$ok = false;
		}
	}
	if (!$ok) {
		header("Location: " . $url . "publico/usuario/usuario_login.php");
		die();
	}
}
//********************
//********************
//**** EOF - Verifica login do usuário (ADMINISTRADOR)
//********************
//********************

//********************
//********************
//**** Verifica login do usuário (REPRESENTANTE)
//********************
//********************
function VerificaRepr($url) {
	$ok = true;
	if (!isset($_SESSION["CHAVE_USUARIO"])) {
		$ok = false;
	}
	else {
		if ($_SESSION["CHAVE_USUARIO"] == "") {
			$ok = false;
		}
	}
	if ($ok) {
		if ($_SESSION["TIPO_USUARIO"] != "REPRESENTANTE") {
			$ok = false;
		}
	}
	if (!$ok) {
		header("Location: " . $url . "publico/usuario/usuario_login.php");
		die();
	}
}
//********************
//********************
//**** EOF - Verifica login do usuário (REPRESENTANTE)
//********************
//********************

//********************
//********************
//**** Captura valor inicial e transfere para array multidimensional antes do insert/update da tabela
//********************
//********************
function abre_diario($tabelanome, $tabelachavecampo, $tabelachavevalor, $camposexcluidos) {
	global $StrConexao;
	global $BaseDados;
	$pdo_diario = new PDO($StrConexao, $BaseDados['usuario'], $BaseDados['senha']);	
	$pdo_diario->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
	$strsql = "
	select
	*
	from
	{$tabelanome}
	where 	
	{$tabelachavecampo} = :vtabelachavevalor
	";
	$query = $pdo_diario->prepare($strsql);		
	$query->bindParam(":vtabelachavevalor", $tabelachavevalor);	
	$query->execute();
	$abre_diario = array();
	if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if (count($camposexcluidos) > 0) {
			foreach ($row as $field => $value) {
				for ($i = 0; $i < count($camposexcluidos); $i++) {
					if ($field != $camposexcluidos[$i]) {
						array_push($abre_diario, array($field, $value));
					}
				}
			}
		}
		else {
			foreach ($row as $field => $value) {
				array_push($abre_diario, array($field, $value));
			}
		}
	}
	return $abre_diario;
}
//********************
//********************
//**** EOF - Captura valor inicial e transfere para array multidimensional antes do insert/update da tabela
//********************
//********************

//********************
//********************
//**** Captura valor final e compara com valores inicais para gravar diario do registro
//********************
//********************
function fecha_diario($tabelanome, $tabelachavecampo, $tabelachavevalor, $abre_diario, $camposexcluidos) {
	global $StrConexao;
	global $BaseDados;
	$pdo_diario = new PDO($StrConexao, $BaseDados['usuario'], $BaseDados['senha']);	
	$pdo_diario->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$strsql = "
	select
	*
	from
	{$tabelanome}
	where
	{$tabelachavecampo} = :vtabelachavevalor	
	";
	$qdiario = $pdo_diario->prepare($strsql);		
	$qdiario->bindParam(":vtabelachavevalor", $tabelachavevalor);	
	$qdiario->execute();
	$fecha_diario = array();
	if ($row = $qdiario->fetch(PDO::FETCH_ASSOC)) {
		if (count($camposexcluidos) > 0) {
			foreach ($row as $field => $value) {
				for ($i = 0; $i < count($camposexcluidos); $i++) {
					if ($field != $camposexcluidos[$i]) {
						array_push($fecha_diario, array($field, $value));
					}
				}
			}
		}
		else {
			foreach ($row as $field => $value) {
				array_push($fecha_diario, array($field, $value));
			}
		}
	}
	$obs_diario = "";	
	if (count($abre_diario) > 0) {
		for ($i = 0; $i < count($abre_diario); $i++) {
			if ($abre_diario[$i][0] = $fecha_diario[$i][0]) {
				if ($abre_diario[$i][1] != $fecha_diario[$i][1]) {
					if ($obs_diario != "") {
						$obs_diario = $obs_diario . "\r\n";
					}
					$obs_diario = $obs_diario . "<". $abre_diario[$i][0] . ">". "\r\n";
					$obs_diario = $obs_diario . "  <->". $abre_diario[$i][1] . "</->". "\r\n";
					$obs_diario = $obs_diario . "  <+>". $fecha_diario[$i][1] . "</+>";
				}
			}
		}
	}
	else {
		for ($i = 0; $i < count($fecha_diario); $i++) {
			if ($obs_diario != "") {
				$obs_diario = $obs_diario . "\r\n";
			}
			$obs_diario = $obs_diario. "<" . $fecha_diario[$i][0] . ">" . "\r\n";
			$obs_diario = $obs_diario. "  " . $fecha_diario[$i][1];
		}
	}
	$usuario_diario = "SITE";
	if ($obs_diario != "") {
		$tipo_diario = (count($abre_diario) > 0 ? "ALTERACAO" : "INCLUSAO" ); // ALTERACAO/INCLUSAO
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
		(:vtabelanome
		,:vtabelachavevalor
		,:vtipo_diario
		,:vusuario_diario
		,:vobs_diario
		)";
		$qdiario = $pdo_diario->prepare($strsql);
		$qdiario->bindParam(":vtabelanome", $tabelanome);
		$qdiario->bindParam(":vtabelachavevalor", $tabelachavevalor);
		$qdiario->bindParam(":vtipo_diario", $tipo_diario);
		$qdiario->bindParam(":vusuario_diario", $usuario_diario);
		$qdiario->bindParam(":vobs_diario", $obs_diario);
		$qdiario->execute();

    if (strtoupper($tabelanome) == "TCLIENTE" || strtoupper($tabelanome) == "TPED" || strtoupper($tabelanome) == "TORCA") { 
      $strsql = "
      insert
      into 
      ttransacao
      (tabelaext_transacao
      ,chaveext_transacao
      ) values 
      (:vtabelanome
      ,:vtabelachavevalor
      )";
      $qdiario = $pdo_diario->prepare($strsql);
      $qdiario->bindParam(":vtabelanome", $tabelanome);
      $qdiario->bindParam(":vtabelachavevalor", $tabelachavevalor);		
      $qdiario->execute();
    }
	}
	return $obs_diario;
}
//********************
//********************
//**** EOF - Captura valor final e compara com valores inicais para gravar diario do registro
//********************
//********************

//********************
//********************
//**** Avalia CPF
//********************
//********************
function cpf($cpf) {  
	$cpf = preg_replace( '/[^0-9]/is', '', (string) $cpf );
	if (strlen($cpf) > 0 && strlen($cpf) < 11) {
		$cpf = str_pad($cpf, 11, "0", STR_PAD_LEFT);
	}
	if (strlen($cpf) != 11) {
		return false;
	}
	// Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11	
	if (preg_match('/(\d)\1{10}/', $cpf)) {
		return false;
	}
	// Faz o calculo para validar o CPF
	for ($t = 9; $t < 11; $t++) {
		for ($d = 0, $c = 0; $c < $t; $c++) {
			$d += $cpf[$c] * (($t + 1) - $c);
		}
		$d = ((10 * $d) % 11) % 10;
		if ($cpf[$c] != $d) {
			return false;
		}
	}
	return true;
}
//********************
//********************
//**** EOF - Avalia CPF
//********************
//********************

//********************
//********************
//**** Avalia CNPJ
//********************
//********************
function cnpj($cnpj) {
	$cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
	if (strlen($cnpj) > 0 && strlen($cnpj) < 14) {
		$cnpj = str_pad($cnpj, 14, "0", STR_PAD_LEFT);
	}			
	// Valida tamanho
	if (strlen($cnpj) != 14) {
		return false;
	}
	// Valida primeiro dígito verificador
	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
		return false;
	}
	// Valida segundo dígito verificador
	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
		$soma += $cnpj[$i] * $j;
		$j = ($j == 2) ? 9 : $j - 1;
	}
	$resto = $soma % 11;
	return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}
//********************
//********************
//**** EOF - Avalia CNPJ
//********************
//********************

//********************
//********************
//**** Somente números
//********************
//********************
function so_numeros($cnumero, $cdefault) {
	$sonumeros = preg_replace('/\D/', '', $cnumero);
	if ($sonumeros == '') {
		if ($cdefault != '') {
			$sonumeros = $cdefault;
		}
	}
	return $sonumeros;	
}
//********************
//********************
//**** EOF - Somente números
//********************
//********************

//********************
//********************
//**** Mascaras CPF/CNPJ/DATA ETC
//********************
//********************
function mascara($mascara_valor, $mascara_mascara) {
	$retorno = "";
	$i = 0;
	$ii = 0;
	for ($i = 0; $i <= strlen($mascara_mascara) - 1; $i++) {
		if ($mascara_mascara[$i] == '#') {
			if (isset($mascara_valor[$ii])) {
				$retorno .= $mascara_valor[$ii++];
			} 
		}
		else {
			if (isset($mascara_mascara[$i])) {
				$retorno .= $mascara_mascara[$i];
			}
		}
	}
	return $retorno;
}
//********************
//********************
//**** EOF - Mascaras CPF/CNPJ/DATA ETC
//********************
//********************

//********************
//********************
//**** Converte para maísculo (com acentuação)
//********************
//********************
function maiusculo($ucase) {
	$retorno = mb_strtoupper($ucase, "UTF-8"); 
	return $retorno;
}
//********************
//********************
//**** EOF - Converte para maísculo (com acentuação)
//********************
//********************

//********************
//********************
//**** Converte para minúsculo (com acentuação)
//********************
//********************
function minusculo($lcase) {
	$retorno = mb_strtolower($lcase, "UTF-8"); 
	return $retorno;
}
//********************
//********************
//**** EOF - Converte para minúsculo (com acentuação)
//********************
//********************

//********************
//********************
//**** Cria Modal de Exclusão do Registro
//********************
//********************
function Modal_Exclusao($ModalExcRaiz, $ModalExcLnk) {
	include($ModalExcRaiz . "include/diario/diario_exc_modal.php");
	// Processa refresh do Link Main (pagina atual que chamou o modal)
	$retorno = '';
	$retorno .= '<script type="text/javascript">' . "\n";
	$retorno .= '$("#MODAL_DIARIO_EXC").on("hidden.bs.modal", function () { parent.location="' . $ModalExcLnk . '" })';
	$retorno .= '</script>';
	return $retorno;
}
//********************
//********************
//**** EOF - Cria Modal de Exclusão do Registro
//********************
//********************

//********************
//********************
//**** Cria Modal do Diário do Registro
//********************
//********************
function Modal_Diario($ModalExcRaiz) {
	include($ModalExcRaiz . "include/diario/diario_modal.php");
}
//********************
//********************
//**** EOF - Cria Modal do Diário do Registro
//********************
//********************

//********************
//********************
//**** Imprime Título da Página
//********************
//********************
function Titulo($PaginaTitulo, $ImprimePrefixo) {
	$retorno = '';
	if ($ImprimePrefixo) {
		$retorno .= 'Guia Doméstico - ';
	}
	$retorno .= $PaginaTitulo;
	return $retorno;
}
//********************
//********************
//**** EOF - Cria Modal de Exclusão do Registro
//********************
//********************

//********************
//********************
//**** Criptografa Expressão
//********************
//********************
function Cripto($Expressao) {
	return base64_encode(base64_encode($Expressao));
}
//********************
//********************
//**** EOF - Criptografa Expressão
//********************
//********************

//********************
//********************
//**** Descriptografa Expressão
//********************
//********************
function Uncripto($Expressao) {
	return base64_decode(base64_decode($Expressao));	
}
//********************
//********************
//**** EOF - Descriptografa Expressão
//********************
//********************

//********************
//********************
//**** Formata Número
//********************
//********************
function FormataNumero($Numero, $Decimais) {
	return number_format($Numero, $Decimais, ",", ".");
}
//********************
//********************
//**** EOF - Formata Número
//********************
//********************

//********************
//********************
//**** Formata Percentual
//********************
//********************
function FormataPercentual($Numero, $Decimais) {
	return number_format($Numero, $Decimais, ",", ".") . "%";
}
//********************
//********************
//**** EOF - Formata Número
//********************
//********************

//********************
//********************
//**** Formata Número para SQL
//********************
//********************
function FormataNumSQL($Numero) {
	$retorno = $Numero;
	$retorno = str_replace(".", "", $retorno);
	$retorno = str_replace(",", ".", $retorno);	
	$retorno = str_replace("%", "", $retorno);		
	return $retorno;
}
//********************
//********************
//**** EOF - Formata Número para SQL
//********************
//********************

//********************
//********************
//**** Abre Orçamento
//********************
//********************
function AbreOrcamento($Orcamento, $pdo) {
	if ($Orcamento != "") {
		$strsql_repr = "";
		if (isset($_SESSION["CHAVE_REPR"]) && isset($_SESSION["TIPO_USUARIO"])) {
			if ($_SESSION["TIPO_USUARIO"] == "REPRESENTANTE") {
				$strsql_repr = "torca.chave_repr = " . $_SESSION["CHAVE_REPR"] . " and ";
			}
		}
		$strsql = "
		select 
		torca.chave_orca
		,tcliente.nome_cliente
		,tcliente.uf_cliente
		,(select count(1) from torcapro where torca.chave_orca = torcapro.chave_orca and torcapro.caixa_orcapro = 1) as QtdItens
    	from 
		torca
		left join tcliente on torca.chave_cliente = tcliente.chave_cliente
		where 
		torca.chave_orca = :vchave_orca and " . $strsql_repr . "
		torca.caixa_orca = 1";
		$qorca = $pdo->prepare($strsql);
		$qorca->bindParam(":vchave_orca", $Orcamento);
		$qorca->execute();
		if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)){
			$_SESSION["CARRINHO_TIPO"] = 'ORCAMENTO';
			$_SESSION["CARRINHO_NUMERO"] = str_pad($Orcamento, 6, "0", STR_PAD_LEFT);
			$_SESSION["CARRINHO_NOME"] = substr($torca["nome_cliente"], 0, 20);
			$_SESSION["CARRINHO_QTDITENS"] = ($torca["QtdItens"] > 0) ? strval($torca["QtdItens"]) : '';
			$_SESSION["CARRINHO_UF"] = trim($torca["uf_cliente"]);
		}
		else {
			FechaOrcamento();
		}
	}
	else {
		FechaOrcamento();
	}
}
//********************
//********************
//**** EOF - Abre Orçamento
//********************
//********************

//********************
//********************
//**** Fecha Orçamento
//********************
//********************
function FechaOrcamento() {
	$_SESSION["CARRINHO_TIPO"] = "";
	$_SESSION["CARRINHO_NUMERO"] = "";
	$_SESSION["CARRINHO_NOME"] = "";
	$_SESSION["CARRINHO_QTDITENS"] = "";
	$_SESSION["CARRINHO_UF"] = "";
}
//********************
//********************
//**** EOF - Fecha Orçamento
//********************
//********************

//********************
//********************
//**** Pega Orçamento
//********************
//********************
function PegaOrcamento() {
	$chave_orca = "";
	if (isset($_SESSION["CARRINHO_TIPO"])) {
		if ($_SESSION["CARRINHO_TIPO"] == "ORCAMENTO") {
			$chave_orca = $_SESSION["CARRINHO_NUMERO"];			
		}
	}
	return $chave_orca;
}
//********************
//********************
//**** EOF - Pega Orçamento
//********************
//********************

//********************
//********************
//**** Abre Pedido
//********************
//********************
function AbrePedido($Pedido, $pdo) {
	if ($Pedido != "") {
		$strsql_repr = "";
		if (isset($_SESSION["CHAVE_REPR"]) && isset($_SESSION["TIPO_USUARIO"])) {
			if ($_SESSION["TIPO_USUARIO"] == "REPRESENTANTE") {
				$strsql_repr = "torca.chave_repr = " . $_SESSION["CHAVE_REPR"] . " and ";
			}
		}
		$strsql = "
		select 
		torca.chave_orca
		,tcliente.nome_cliente
		,tcliente.uf_cliente
		,(select count(1) from torcapro where torca.chave_orca = torcapro.chave_orca and torcapro.caixa_orcapro = 1) as QtdItens
    	from 
		tped
		left join torca on tped.chave_ped = torca.chave_ped 
		left join tcliente on torca.chave_cliente = tcliente.chave_cliente
		where 
		tped.chave_ped = :vchave_ped and " . $strsql_repr . " 
		tped.caixa_ped = 1
		";
		$qped = $pdo->prepare($strsql);
		$qped->bindParam(":vchave_ped", $Pedido);
		$qped->execute();
		if ($tped = $qped->fetch(PDO::FETCH_ASSOC)){
			$_SESSION["CARRINHO_TIPO"] = 'PEDIDO';
			$_SESSION["CARRINHO_NUMERO"] = str_pad($Pedido, 6, "0", STR_PAD_LEFT);
			$_SESSION["CARRINHO_NOME"] = substr($tped["nome_cliente"], 0, 20);
			$_SESSION["CARRINHO_QTDITENS"] = ($tped["QtdItens"] > 0) ? strval($tped["QtdItens"]) : '';
			$_SESSION["CARRINHO_UF"] = trim($tped["uf_cliente"]);
		}
		else {
			FechaPedido();
		}
	}
	else {
		FechaPedido();
	}
}
//********************
//********************
//**** EOF - Abre Pedido
//********************
//********************

//********************
//********************
//**** Fecha Pedido
//********************
//********************
function FechaPedido() {
	$_SESSION["CARRINHO_TIPO"] = "";
	$_SESSION["CARRINHO_NUMERO"] = "";
	$_SESSION["CARRINHO_NOME"] = "";
	$_SESSION["CARRINHO_QTDITENS"] = "";
	$_SESSION["CARRINHO_UF"] = "";
}
//********************
//********************
//**** EOF - Fecha Pedido
//********************
//********************

//********************
//********************
//**** Pega Pedido
//********************
//********************
function PegaPedido() {
	$chave_ped = "";
	if (isset($_SESSION["CARRINHO_TIPO"])) {
		if ($_SESSION["CARRINHO_TIPO"] == "PEDIDO") {
			$chave_ped = $_SESSION["CARRINHO_NUMERO"];
		}
	}
	return $chave_ped;
}
//********************
//********************
//**** EOF - Pega Pedido
//********************
//********************


//********************
//********************
//**** Float para Moeda
//********************
//********************
function Float2Moeda($valor, $decimais) {
	$val = 0;
	if (is_numeric($valor)) {
		try {
			$val = $valor;			
		}
		catch (Exception $e) {
			$val = 0;			
		}		
	}
	$val = number_format($val, $decimais, ',', '.');
	$retorno = $val;
	return $retorno;
}
//********************
//********************
//**** EOF - Float para Moeda
//********************
//********************

//********************
//********************
//**** Moeda para Float
//********************
//********************
function Moeda2Float($valor, $decimais) {
	$val = $valor;
	$val = str_replace(".", "", $val);
	$val = str_replace(",", ".", $val);	
	try {
		$val = floatval($val);			
	}
	catch (Exception $e) {
		$val = 0;			
	}
	$val = round($val, $decimais);
	$retorno = $val;
	return $retorno;
}
//********************
//********************
//**** EOF - Moeda para Float
//********************
//********************

//********************
//********************
//**** Float para Percentagem
//********************
//********************
function Float2Perc($valor, $decimais) {
	$val = 0;
	if (is_numeric($valor)) {
		try {
			$val = $valor;			
		}
		catch (Exception $e) {
			$val = 0;			
		}		
	}
	$val = number_format($val, $decimais, ',', '.');
	$retorno = $val . '%';
	return $retorno;
}
//********************
//********************
//**** EOF - Float para Percentagem
//********************
//********************

//********************
//********************
//**** Percentagem para Float
//********************
//********************
function Perc2Float($valor, $decimais) {
	$val = $valor;
	$val = str_replace(".", "", $val);
	$val = str_replace("%", "", $val);	
	$val = str_replace(",", ".", $val);	
	try {
		$val = floatval($val);			
	}
	catch (Exception $e) {
		$val = 0;			
	}
	$val = round($val, $decimais);
	$retorno = $val;
	return $retorno;
}
//********************
//********************
//**** EOF - Percentagem para Float
//********************
//********************

//********************
//********************
//**** Formata Data
//********************
//********************
function FormataData($data, $mascara) {
	if (is_null($data)) {
		$data = "";
	}	
	$val = 0;
	if ($data != "") {
		try {
			$val = date_format(date_create($data),$mascara);
		}
		catch (Exception $e) {
			$val = 0;
		}		
	}
	$retorno = $val;
	return $retorno;
}
//********************
//********************
//**** EOF - Formata Data
//********************
//********************

function Btn_Retorno($BtnRetornarLink) {
	$retorno = '<button type="button" class="btn btn-outline-info btn-sm" id="BTN_RETORNAR" name="BTN_RETORNAR" onclick="javascript:parent.location=\'' . $BtnRetornarLink . '\'"><i class="fa fa-angle-left fa-lg"></i>&nbsp;</button>';
	return $retorno;
}

function Btn_Customizado($BotaoLink, $BotaoCaption) {
	$retorno = '<button type="button" class="btn btn-primary btn-sm" id="BTN_BOTAO" name="BTN_BOTAO" onclick="javascript:location=\'' . $BotaoLink . '\'">' . $BotaoCaption . '</button>';
	return $retorno;
}

function Btn_Diario($DiarioTabela, $DiarioCampo, $DiarioChave, $DiarioRaiz) {
	$retorno = '<a href="#" class="btn btn-light btn-sm float-right" id="SHOW_DIARIO" data-tabela-id="' . $DiarioTabela . '" data-campo-id="' . $DiarioCampo . '" data-id="' . $DiarioChave. '" data-url-id="' . $DiarioRaiz . '" data-toggle="modal" data-target="#MODAL_DIARIO">Diário</a>';
	return $retorno;	
}

function Pagina_Titulo($PaginaTitulo) {
	$retorno = "Guia Doméstico - " . $PaginaTitulo; 
	return $retorno;
}

function Titulo_Cartao($PaginaTitulo) {
	$retorno = "<titulo-cartao>" . $PaginaTitulo . "</titulo-cartao>";
	return $retorno;
}

function Resposta($pag_titulo, $pag_texto, $btn_caption, $lnk_onclick, $lnk_retorno) {
	if ($lnk_onclick == "") {
		$lnk_onclick = "javascript:parent.location=";
	}
	$retorno = '';
	$retorno .= '<div class="text-center">' . "\n";
	$retorno .= '  <h3>' . $pag_titulo . '</h3>' . "\n";
	$retorno .= '  <p>' . $pag_texto . '</p>' . "\n";
	$retorno .= '  <button type="button" class="btn btn-primary mb-2" onclick="' . $lnk_onclick . '\'' . $lnk_retorno . '\'">' . $btn_caption . '</button>' . "\n";
	$retorno .= '</div>' . "\n";
	return $retorno;
}

function DataValida($data, $formato = 'Y-m-d') {
	$ObjData = DateTime::createFromFormat($formato, $data);
	return $ObjData && $ObjData->format($formato) == $data;
}

function RetornaURL() {
	$url = "";
    if (isset($_SERVER['HTTPS'])) {
		if ($_SERVER['HTTPS'] === 'on') {
			$url = "https://";
		}
		else {
			$url = "http://";
		}
	}		
    else {
	    if (isset($_SERVER['HTTP'])) {
			$url = "http://";
		}
	}
    $url .= $_SERVER['HTTP_HOST'];    
    $url .= $_SERVER['REQUEST_URI'];    
	return $url;
}

function MensagemErro($cmsg, $cnewmsg, $ccodmsg) {
	if ($cmsg != "") {
		$cmsg .= "<br />";
	};
	$cmsg .= $cnewmsg; 
	$cmsg = str_replace('$URL', RetornaURL(), $cmsg);	
	$cmsg = str_replace('$CODERROR', $ccodmsg, $cmsg);	
	return $cmsg;
}

//********************
//********************
//**** Transforma/Formata conforme mascara
//***********************
//********************
function transforma($cexpressao, $cmascara) {
  $ni = 0;
  $ci = '';
  $cont = 1;
  for ($ni = 1; $ni <= strlen($cexpressao); $ni++) {
      $ci = $cexpressao[$ni-1];
      $cmascara = str_replace('!',$ci, $cmascara,$cont);
  }
  return $cmascara;
}
//********************
//********************
//**** EOF Transforma/Formata conforme mascara
//***********************
//********************

function formataCnpjCpf($cnpjcpf)
{
  $CPF_LENGTH = 11;
  $cnpj_cpf = preg_replace("/\D/", '', $cnpjcpf);  
  if (strlen($cnpj_cpf) === $CPF_LENGTH) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
  }   
  return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}

function yyyymmddhhnnss()
{
  $retorno = date('YmdHis');
  return $retorno;
}

function RetornaINSS($vlsalario, $pdo)
{
  $retorno = 0;
  abre_db();  
  $strsql = "
  select 
  ttabinss.*  
  from 
  ttabinss
  where 
  {$vlsalario} >= ttabinss.vli_tabinss and {$vlsalario} <= ttabinss.vlf_tabinss and 
  ttabinss.caixa_tabinss = 1
  ";
  $qtabinss = $pdo->prepare($strsql);
  $qtabinss->execute();
  if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
    $aliq_tabinss = floatval($ttabinss["aliq_tabinss"]);
    $vlded_tabinss = floatval($ttabinss["vlded_tabinss"]);
    $vlfixo_tabinss = floatval($ttabinss["vlfixo_tabinss"]);
    if ($vlfixo_tabinss == 0) {
      if ($aliq_tabinss > 0) {
        $retorno = round(($vlsalario * $aliq_tabinss / 100), 2) - $vlded_tabinss;
      }
    }
    else {
      $retorno = $vlfixo_tabinss;
    }
  }  
  return $retorno;
}

function RetornaFGTS($vlsalario, $pdo)
{
  /*
  $retorno = 0;
  abre_db();  
  $strsql = "
  select 
  ttabinss.*  
  from 
  ttabinss
  where 
  {$vlsalario} >= ttabinss.vli_tabinss and {$vlsalario} <= ttabinss.vlf_tabinss and 
  ttabinss.caixa_tabinss = 1
  ";
  $qtabinss = $pdo->prepare($strsql);
  $qtabinss->execute();
  if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
    $aliq_tabinss = floatval($ttabinss["aliq_tabinss"]);
    $vlded_tabinss = floatval($ttabinss["vlded_tabinss"]);
    $vlfixo_tabinss = floatval($ttabinss["vlfixo_tabinss"]);
    if ($vlfixo_tabinss == 0) {
      if ($aliq_tabinss > 0) {
        $retorno = round(($vlsalario * $aliq_tabinss / 100), 2) - $vlded_tabinss;
      }
    }
    else {
      $retorno = $vlfixo_tabinss;
    }
  }    
  */
  $retorno = round(($vlsalario * 8 / 100), 2);
  return $retorno;
}

function RetornaAFASTA($vlsalario, $pdo)
{
  /*
  $retorno = 0;
  abre_db();  
  $strsql = "
  select 
  ttabinss.*  
  from 
  ttabinss
  where 
  {$vlsalario} >= ttabinss.vli_tabinss and {$vlsalario} <= ttabinss.vlf_tabinss and 
  ttabinss.caixa_tabinss = 1
  ";
  $qtabinss = $pdo->prepare($strsql);
  $qtabinss->execute();
  if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
    $aliq_tabinss = floatval($ttabinss["aliq_tabinss"]);
    $vlded_tabinss = floatval($ttabinss["vlded_tabinss"]);
    $vlfixo_tabinss = floatval($ttabinss["vlfixo_tabinss"]);
    if ($vlfixo_tabinss == 0) {
      if ($aliq_tabinss > 0) {
        $retorno = round(($vlsalario * $aliq_tabinss / 100), 2) - $vlded_tabinss;
      }
    }
    else {
      $retorno = $vlfixo_tabinss;
    }
  }    
  */
  $retorno = round(($vlsalario * 91 / 100), 2);
  return $retorno;
}

function RetornaIRPF($vlsalario, $pdo)
{
  $retorno = [
    "aliquota" => 0,
    "valor" => 0,
    "deducao" => 0
  ];
  abre_db();  
  $strsql = "
  select 
  ttabirpf.*  
  from 
  ttabirpf
  where 
  {$vlsalario} >= ttabirpf.vli_tabirpf and {$vlsalario} <= ttabirpf.vlf_tabirpf and 
  ttabirpf.caixa_tabirpf = 1
  ";
  $qtabirpf = $pdo->prepare($strsql);
  $qtabirpf->execute();
  if ($ttabirpf = $qtabirpf->fetch(PDO::FETCH_ASSOC)) {
    $aliq_tabirpf = floatval($ttabirpf["aliq_tabirpf"]);
    $vlded_tabirpf = floatval($ttabirpf["vlded_tabirpf"]);        
    $retorno["aliquota"] = $aliq_tabirpf;
    if ($aliq_tabirpf > 0) {
      $retorno["valor"] = round(($vlsalario * $aliq_tabirpf / 100), 2) - $vlded_tabirpf;
    }
    $retorno["deducao"] = $vlded_tabirpf;
  }  
  return $retorno;
}
?>