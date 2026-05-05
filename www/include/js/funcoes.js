/**--------------------------
//* Validate Date Field script- By JavaScriptKit.com
//* For this script and 100s more, visit http://www.javascriptkit.com
//* This notice must stay intact for usage
---------------------------**/

function datavalida(ccampo) {
	var validformat = /^\d{2}\/\d{2}\/\d{4}$/; //Basic check for format validity
	var returnval = false;
	if (!validformat.test(ccampo)) {
		alert("Data inválida.");
	}
	else { //Detailed check for valid date ranges
		var dayfield = ccampo.split("/")[0];
		var monthfield = ccampo.split("/")[1];
		var yearfield = ccampo.split("/")[2];
		var dayobj = new Date(yearfield, monthfield-1, dayfield);
		if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)) {
			alert("Dia, mês ou ano inválido para data.");
		}
		else {
			returnval = true
		}
	}
	return returnval
}

/**
 * @autor Elenir Sousa
 * @version 03/10/2007
 *
 * PROTOTIPOS:
 * método String.lpad(int pSize, char pCharPad)
 * método String.trim()
 *
 * String unformatNumber(String pNum)
 * String formatCpfCnpj(String pCpfCnpj, boolean pUseSepar, boolean pIsCnpj)
 * String dvCpfCnpj(String pEfetivo, boolean pIsCnpj)
 * boolean isCpf(String pCpf)
 * boolean isCnpj(String pCnpj)
 * boolean isCpfCnpj(String pCpfCnpj)
 */

var NUM_DIGITOS_CPF  = 11;
var NUM_DIGITOS_CNPJ = 14;
var NUM_DGT_CNPJ_BASE = 8;

/**
 * Adiciona método lpad() à classe String.
 * Preenche a String à esquerda com o caractere fornecido,
 * até que ela atinja o tamanho especificado.
 */
String.prototype.lpad = function(pSize, pCharPad)
{
	var str = this;
	var dif = pSize - str.length;
	var ch = String(pCharPad).charAt(0);
	for (; dif>0; dif--) str = ch + str;
	return (str);
} //String.lpad


/**
 * Adiciona método trim() à classe String.
 * Elimina brancos no início e fim da String.
 */
String.prototype.trim = function()
{
	return this.replace(/^\s*/, "").replace(/\s*$/, "");
} //String.trim


/**
 * Elimina caracteres de formatação e zeros à esquerda da string
 * de número fornecida.
 * @param String pNum
 *      String de número fornecida para ser desformatada.
 * @return String de número desformatada.
 */
function unformatNumber(pNum)
{
	return String(pNum).replace(/\D/g, "").replace(/^0+/, "");
} //unformatNumber

/**
 * Formata a string fornecida como CNPJ ou CPF, adicionando zeros
 * à esquerda se necessário e caracteres separadores, conforme solicitado.
 * @param String pCpfCnpj
 *      String fornecida para ser formatada.
 * @param boolean pUseSepar
 *      Indica se devem ser usados caracteres separadores (. - /).
 * @param boolean pIsCnpj
 *      Indica se a string fornecida é um CNPJ.
 *      Caso contrário, é CPF. Default = false (CPF).
 * @return String de CPF ou CNPJ devidamente formatada.
 */
function formatCpfCnpj(pCpfCnpj, pUseSepar, pIsCnpj)
{
	if (pIsCnpj==null) pIsCnpj = false;
	if (pUseSepar==null) pUseSepar = true;
	var maxDigitos = pIsCnpj? NUM_DIGITOS_CNPJ: NUM_DIGITOS_CPF;
	var numero = unformatNumber(pCpfCnpj);

	numero = numero.lpad(maxDigitos, '0');
	if (!pUseSepar) return numero;

	if (pIsCnpj)
	{
		reCnpj = /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/;
		numero = numero.replace(reCnpj, "$1.$2.$3/$4-$5");
	}
	else
	{
		reCpf  = /(\d{3})(\d{3})(\d{3})(\d{2})$/;
		numero = numero.replace(reCpf, "$1.$2.$3-$4");
	}
	return numero;
} //formatCpfCnpj


/**
 * Calcula os 2 dígitos verificadores para o número-efetivo pEfetivo de
 * CNPJ (12 dígitos) ou CPF (9 dígitos) fornecido. pIsCnpj é booleano e
 * informa se o número-efetivo fornecido é CNPJ (default = false).
 * @param String pEfetivo
 *      String do número-efetivo (SEM dígitos verificadores) de CNPJ ou CPF.
 * @param boolean pIsCnpj
 *      Indica se a string fornecida é de um CNPJ.
 *      Caso contrário, é CPF. Default = false (CPF).
 * @return String com os dois dígitos verificadores.
 */
function dvCpfCnpj(pEfetivo, pIsCnpj)
{
	if (pIsCnpj==null) pIsCnpj = false;
	var i, j, k, soma, dv;
	var cicloPeso = pIsCnpj? NUM_DGT_CNPJ_BASE: NUM_DIGITOS_CPF;
	var maxDigitos = pIsCnpj? NUM_DIGITOS_CNPJ: NUM_DIGITOS_CPF;
	var calculado = formatCpfCnpj(pEfetivo, false, pIsCnpj);
	calculado = calculado.substring(2, maxDigitos);
	var result = "";

	for (j = 1; j <= 2; j++)
	{
		k = 2;
		soma = 0;
		for (i = calculado.length-1; i >= 0; i--)
		{
			soma += (calculado.charAt(i) - '0') * k;
			k = (k-1) % cicloPeso + 2;
		}
		dv = 11 - soma % 11;
		if (dv > 9) dv = 0;
		calculado += dv;
		result += dv
	}

	return result;
} //dvCpfCnpj


/**
 * Testa se a String pCpf fornecida é um CPF válido.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCpf
 *      String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF válido.
 */
function isCpf(pCpf)
{
	var numero = formatCpfCnpj(pCpf, false, false);
	var base = numero.substring(0, numero.length - 2);
	var digitos = dvCpfCnpj(base, false);
	var algUnico, i;

	// Valida dígitos verificadores
	if (numero != base + digitos) return false;

	/* Não serão considerados válidos os seguintes CPF:
	 * 000.000.000-00, 111.111.111-11, 222.222.222-22, 333.333.333-33, 444.444.444-44,
	 * 555.555.555-55, 666.666.666-66, 777.777.777-77, 888.888.888-88, 999.999.999-99.
	 */
	algUnico = true;
	for (i=1; i<NUM_DIGITOS_CPF; i++)
	{
		algUnico = algUnico && (numero.charAt(i-1) == numero.charAt(i));
	}
	return (!algUnico);
} //isCpf


/**
 * Testa se a String pCnpj fornecida é um CNPJ válido.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCnpj
 *      String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CNPJ válido.
 */
function isCnpj(pCnpj)
{
	var numero = formatCpfCnpj(pCnpj, false, true);
	var base = numero.substring(0, NUM_DGT_CNPJ_BASE);
	var ordem = numero.substring(NUM_DGT_CNPJ_BASE, 12);
	var digitos = dvCpfCnpj(base + ordem, true);
	var algUnico;

	// Valida dígitos verificadores
	if (numero != base + ordem + digitos) return false;

	/* Não serão considerados válidos os CNPJ com os seguintes números BÁSICOS:
	 * 11.111.111, 22.222.222, 33.333.333, 44.444.444, 55.555.555,
	 * 66.666.666, 77.777.777, 88.888.888, 99.999.999.
	 */
	algUnico = numero.charAt(0) != '0';
	for (i=1; i<NUM_DGT_CNPJ_BASE; i++)
	{
		algUnico = algUnico && (numero.charAt(i-1) == numero.charAt(i));
	}
	if (algUnico) return false;
	/* Não será considerado válido CNPJ com número de ORDEM igual a 0000.
	 * Não será considerado válido CNPJ com número de ORDEM maior do que 0300
	 * e com as três primeiras posições do número BÁSICO com 000 (zeros).
	 * Esta crítica não será feita quando o no BÁSICO do CNPJ for igual a 00.000.000.
	 */
	if (ordem == "0000") {
		return false;	
	}
	return (base == "00000000" || parseInt(ordem, 10) <= 300 || base.substring(0, 3) != "000");
} //isCnpj

/**
 * Testa se a String pCpfCnpj fornecida é um CPF ou CNPJ válido.
 * Se a String tiver uma quantidade de dígitos igual ou inferior
 * a 11, valida como CPF. Se for maior que 11, valida como CNPJ.
 * Qualquer formatação que não seja algarismos é desconsiderada.
 * @param String pCpfCnpj
 *      String fornecida para ser testada.
 * @return <code>true</code> se a String fornecida for um CPF ou CNPJ válido.
 */
function isCpfCnpj(pCpfCnpj) {
	var numero = pCpfCnpj.replace(/\D/g, "");
	if (numero.length > NUM_DIGITOS_CPF) {
		return isCnpj(pCpfCnpj);
	} 
	else {
		return isCpf(pCpfCnpj);
	}
}
//isCpfCnpj

//*******************
//*******************
//**** Float para Moeda
//******************* 
function float2moeda(num) {
	var x = 0;
	if (num < 0) {
		num = Math.abs(num);
		x = 1;
	}
	if (isNaN(num)) {
		num = "0";
	}
	cents = Math.floor((num * 100 + 0.5) % 100);
	num = Math.floor((num * 100 + 0.5) / 100).toString();
	if (cents < 10) {
		cents = "0" + cents;
	}
	var i;
	for (i = 0; i < Math.floor((num.length-(1 + i)) / 3); i++) {
		num = num.substring(0,num.length-(4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
	}
	ret = num + ',' + cents;
	if (x == 1) {
		ret = '-' + ret;
	}
	return ret;
}

//*******************
//*******************
//**** Float para Porcentagem
//******************* 
function float2porc2(num) {
	var x = 0;
	if (num<0) {
		num = Math.abs(num);
		x = 1;
	}
	if (isNaN(num)) {
		num = "0";
	}
	cents = Math.floor((num * 100 + 0.5) % 100);   
	num = Math.floor((num * 100 + 0.5) / 100).toString();
	if (cents < 10) {
		cents = "0" + cents;
	}
	for (var i = 0; i < Math.floor((num.length - (1 + i))/3); i++) {
		num = num.substring(0,num.length - (4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
	}
	ret = num + ',' + cents + '%';
	if (x == 1) {
		ret = '-' + ret;
	}
	return ret;
}

//*******************
//*******************
//**** Float para Porcentagem
//******************* 
function float2porc4(num) {
	var x = 0;
	if (num<0) {
		num = Math.abs(num);
		x = 1;
	}
	if (isNaN(num)) {
		num = "0";
	}
	cents = Math.floor((num * 10000 + 0.5) % 10000); 
	num = Math.floor((num * 100 + 0.5) / 100).toString();
	if (cents < 10) {
		cents = "000" + cents;
	}
	else {
		if (cents < 100) {
			cents = "00" + cents;
		}
		else {
			if (cents < 1000) {
				cents = "0" + cents;
			}
		}
	}
	for (var i = 0; i < Math.floor((num.length - (1 + i))/3); i++) {
		num = num.substring(0,num.length - (4 * i + 3)) + '.' + num.substring(num.length - (4 * i + 3));
	}
	ret = num + ',' + cents + '%';
	if (x == 1) {
		ret = '-' + ret;
	}
	return ret;
}

//*******************
//*******************
//**** Moeda para Float
//******************* 
function moeda2float(moeda) {
	moeda = moeda.replace(".","");
	moeda = moeda.replace(".","");	
	moeda = moeda.replace(",",".");
	moeda = moeda.replace(",",".");	
	moeda = moeda.replace("%","");
	if (isNaN(moeda) || moeda == "" || moeda == null) {
		moeda = "0";
	}
	return parseFloat(moeda);
}

//*******************
//*******************
//**** Moeda para Float 4 casas decimais
//******************* 
function moeda2float4(moeda) {
	moeda = moeda.replace(".","");
	moeda = moeda.replace(".","");	
	moeda = moeda.replace(",",".");
	moeda = moeda.replace(",",".");	
	moeda = moeda.replace("%","");
	if (isNaN(moeda) || moeda == "" || moeda == null) {
		moeda = "0";
	}
	return parseFloat(moeda).toFixed(4);
}

function ValidaNumeroInteiro(objCampo) {
	var text = $(objCampo).val().replace(/\D/g,'');
	$(objCampo).focus().val(text);
}

function ValidaNumeroFlutuante(objCampo) {
	var text = $(objCampo).val().replace(/[A-Za-zçÇ.;<>´`=!@#$%]/,'');
	$(objCampo).focus().val(text);	
}

function Retornar(cURL) {
	location.href = cURL;
}

// <input type="tel" oninput="mascaraTelefone(event)" maxlength="15" placeholder="(11) 99999-9999">
/*
function mascaraTelefone(event) {
  let input = event.target;
  let value = input.value;  
  value = value.replace(/\D/g, '');
  // Limita o tamanho máximo (11 dígitos para celular com DDD)
  if (value.length > 11) value = value.slice(0, 11);  
  // Aplica a máscara (XX) XXXXX-XXXX
  if (value.length > 2) {
    value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7, 11)}`;
  } else if (value.length > 0) {
    value = `(${value.substring(0, 2)}`;
  }  
  input.value = value;
}
*/
function mascaraTel(input) {
  // Remove tudo que não é número
  let vval = input.value.replace(/\D/g, '');    
  // Aplica a máscara baseada no tamanho
  if (vval.length > 10) {
    // Celular: (99) 99999-9999
    vval = vval.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
  }
  else {
    // Fixo: (99) 9999-9999
    vval = vval.replace(/^(\d{2})(\d{4})(\d{4})$/, '($1) $2-$3');
  }    
  input.value = vval;
}