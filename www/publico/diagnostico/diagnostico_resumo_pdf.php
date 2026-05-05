<?php 
//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Methods: GET, OPTIONS");
//header("Access-Control-Allow-Headers: Content-Type, Authorization");
//header('Content-Type: text/html; charset=utf-8');
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_resumo_pdf.php
***** Conteúdo: Resumo do Diagnóstico do Usuário em PDF
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
include($Raiz . "conexao/db.php");
include($Raiz . "include/php/header.php");
include($Raiz . "include/php/funcoes.php");
VerificaSessao();
//********************
//********************
//**** Prepara ambiente para execuçao...incluir na pagina de login tambem.
//********************
//********************
if (!isset($_SESSION['AMBIENTE'])) {
	if (strtoupper($_SERVER['SERVER_NAME']) === "LOCALHOST") {
		$_SESSION['AMBIENTE'] = "DESENVOLVIMENTO";
	}
	else {
		$_SESSION['AMBIENTE'] = "PRODUCAO";
	}
}
//********************
//********************
//**** EOF Prepara ambiente para execuçao...incluir na pagina de login tambem.
//********************
//********************

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}
$chave_cargo = $_SESSION["CHAVE_CARGO"];
$descr_cargo = $_SESSION["DESCR_CARGO"];
$descr_cargo = mb_convert_encoding($descr_cargo, "Windows-1252", "UTF-8");
$cbo_cargo = $_SESSION["CBO_CARGO"];
$salario_usuario = $_SESSION["SALARIO_USUARIO"];
$dti_usuario = $_SESSION["DTI_USUARIO"];
$hrdia_usuario = floatval($_SESSION["HRDIA_USUARIO"]);
$hrdiasab_usuario = floatval($_SESSION["HRDIASAB_USUARIO"]);
$diasemana_usuario = floatval($_SESSION["DIASEMANA_USUARIO"]);

//********************
//********************
//**** Pega cargos
//********************
//********************
abre_db();
$strsql = "
select 
tcargo.chave_cargo
,tcargo.descr_cargo
,tcargo.descrdetalhada_cargo
,tcargo.cbo_cargo
from 
tcargo
where 
tcargo.chave_cargo = :vchave_cargo and 
tcargo.caixa_cargo = 1
order by tcargo.chave_cargo
";
$qcargo = $pdo->prepare($strsql);
$qcargo->bindParam(":vchave_cargo", $chave_cargo);
$qcargo->execute();
if ($tcargo = $qcargo->fetch(PDO::FETCH_ASSOC)) {    
  $cbo_cargo = $tcargo["cbo_cargo"];  
  $descrdetalhada_cargo = $tcargo["descrdetalhada_cargo"];  
  $descrdetalhada_cargo = mb_convert_encoding($descrdetalhada_cargo, "Windows-1252", "UTF-8");
}
//********************
//********************
//**** EOF Pega cargos
//********************
//********************

$vlded_inss = RetornaINSS($salario_usuario, $pdo);
$irpf = RetornaIRPF($salario_usuario, $pdo);
$vlded_irpf = $irpf["valor"];
$vlliq_estimado = $salario_usuario - $vlded_inss;
$lbl_descr_cargo = $descr_cargo;
$lbl_chave_cargo = $chave_cargo;
$lbl_cbo_cargo = $cbo_cargo;
$lbl_dti_usuario = FormataData($dti_usuario, "d/m/Y");
$lbl_salario_usuario = "R$ " . FormataNumero($salario_usuario, 2);
if (strlen($lbl_descr_cargo) > 35) {
  $lbl_descr_cargo = substr($lbl_descr_cargo, 0, 35) . "...";
}
$lbl_vlded_inss = "-R$ " . FormataNumero($vlded_inss, 2);
$lbl_vlded_irpf = "-R$ " . FormataNumero($vlded_irpf, 2);
if ($vlliq_estimado < 0) {
  $lbl_vlliq_estimado = "<span class='text-danger'>-R$ " . FormataNumero($salario_usuario - $vlded_inss - $vlded_irpf, 2) . "</span>";
}
else {
  $lbl_vlliq_estimado = "R$ " . FormataNumero($salario_usuario - $vlded_inss - $vlded_irpf, 2);
}
$qtd_hrsemana = ($hrdia_usuario * $diasemana_usuario) + $hrdiasab_usuario;
$lbl_qtd_hrsemana = $qtd_hrsemana . "h";
$lbl_txt_hrsemana = $hrdia_usuario . "/h dia + " . $hrdiasab_usuario . "/h sáb";
//-----
$val_hr = round($salario_usuario / ((($hrdia_usuario * $diasemana_usuario) + $hrdiasab_usuario) * 5), 2);
$lbl_val_hr = "R$ " . FormataNumero($val_hr, 2);
$val_hrextra_50 = round($val_hr + ($val_hr * 0.5), 2);
$lbl_val_hrextra_50 = "R$ " . FormataNumero($val_hrextra_50, 2);
$val_hrextra_100 = round($val_hr + $val_hr, 2);
$lbl_val_hrextra_100 = "R$ " . FormataNumero($val_hrextra_100, 2);
$val_hradic = round($val_hr + ($val_hr * 0.2), 2);
$lbl_val_hradic = "R$ " . FormataNumero($val_hradic, 2) . "/h";
$val_hrviagem = round($val_hr + ($val_hr * 0.25), 2);
$lbl_val_hrviagem = "R$ " . FormataNumero($val_hrviagem, 2) . "/h";
$val_dia = round($salario_usuario / 30, 2);
$lbl_val_dia = "R$ " . FormataNumero($val_dia, 2);
//-----
$vlparcela1_13 = round($salario_usuario / 2, 2);
$vlparcela2_13 = round($salario_usuario / 2, 2);
$vlparcela2_13 = $vlparcela2_13 + round($salario_usuario - ($vlparcela1_13 + $vlparcela2_13), 2);
$vlded_inss_13 = RetornaINSS(round($vlparcela1_13 + $vlparcela2_13, 2), $pdo);
$vlliq_estimado_13 = round($vlparcela1_13 + $vlparcela2_13 - $vlded_inss_13, 2);
$vlbruto_13 = round($salario_usuario, 2);
$lbl_vlparcela1_13 = "R$ " . FormataNumero($vlparcela1_13, 2);
$lbl_vlparcela2_13 = "R$ " . FormataNumero($vlparcela2_13, 2);
$lbl_vlbruto_13 = "R$ " . FormataNumero($vlbruto_13, 2);
$lbl_vlded_inss_13 = "-R$ " . FormataNumero($vlded_inss_13, 2);
$lbl_vlliq_estimado_13 = "R$ " . FormataNumero($vlliq_estimado_13, 2);
//-----
$vlsalbase_ferias = $salario_usuario;
$vlterco_salbase_ferias = round($salario_usuario / 3, 2);
$vlterco_salbase_ferias = $vlterco_salbase_ferias + ($vlsalbase_ferias - round($vlterco_salbase_ferias * 3,2));
$vlsalbruto_ferias = round($vlsalbase_ferias + $vlterco_salbase_ferias, 2);
$vlded_inss_ferias = RetornaINSS($vlsalbruto_ferias, $pdo);
$vlliq_estimado_ferias = round($vlsalbruto_ferias - $vlded_inss_ferias, 2);
$lbl_vlsalbase_ferias = "R$ " . FormataNumero($vlsalbase_ferias, 2);
$lbl_vlterco_salbase_ferias = "R$ " . FormataNumero($vlterco_salbase_ferias, 2);
$lbl_vlsalbruto_ferias = "R$ " . FormataNumero($vlsalbruto_ferias, 2);
$lbl_vlded_inss_ferias = "-R$ " . FormataNumero($vlded_inss_ferias, 2);
$lbl_vlliq_estimado_ferias = "R$ " . FormataNumero($vlliq_estimado_ferias, 2);
//-----
$vlfgts_mensal = RetornaFGTS($salario_usuario, $pdo);
$lbl_vlfgts_mensal = "R$ " . FormataNumero($vlfgts_mensal, 2);
//-----
$vlafasta = RetornaAFASTA($salario_usuario, $pdo);
$lbl_vlafasta = "R$ " . FormataNumero($vlafasta, 2);
//-----


$logotipo = "https://guiadomestico.com.br/design/guiadomestico.jpg";
$dta_resumo = date('d/m/Y');

require($Raiz . 'fpdf/fpdf.php');
class PDF extends FPDF
{
  // Tem que haver o abre e fecha chaves para não dar erro, mesmo que não exista Header e/ou Footer.

  //********************
  //********************
  //**** CONFIGURAÇÃO DE HEADER E FOOTER DA PAGINA PDF
  //********************
  //********************   
  // Page header
  function Header()
  {
    global $dta_resumo;
    global $logotipo;
    $this->Image($logotipo, 10, 6, 60);
    $this->SetFont('Arial', 'B', 14);
    $this->Cell(130);    
    $this->Cell(30, 0, "Resumo do Diagnóstico", 0, 0, 'C');
    $this->SetFont('Arial', '', 12);
    $this->Ln(6);
    $this->Cell(130);
    $this->Cell(30, 0, 'Protocolo: ' . '2026050217180001', 0, 0, 'C');
    $this->SetFont('Arial', '', 10);
    $this->Ln(5);
    $this->Cell(130);    
    $this->Cell(30, 0, 'Data' . ': ' . $dta_resumo , 0, 0, 'C');
    $this->Ln(7);
  }

  function Footer()
  {
    $this->SetY(-20); // Position at 30 mm from bottom    
    $this->SetFont('Arial', '', 8);    
    $this->Cell(190, 3.5, 'guiadomestico.com.br', 0, 1, 'R');   
  }
  //********************
  //********************
  //**** EOF CONFIGURAÇÃO DE HEADER E FOOTER DA PAGINA PDF
  //********************
  //********************
}

//********************
//********************
//**** GERA PDF
//********************
//********************
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, 'CARGO', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, $descr_cargo, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, 'CBO: ' . $cbo_cargo , 0, 0, 'L');

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(35, 5, 'ADMISSÃO', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(35, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, 'SALÁRIO BASE', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, 'INSS (DEDUÇÃO)', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(105, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, 'IRPF (DEDUÇÃO)', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(140, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, 'LÍQUIDO ESTIMADO', 0, 0, 'C');

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(35, 5, $lbl_dti_usuario, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(35, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, $lbl_salario_usuario, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, $lbl_vlded_inss, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(105, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, $lbl_vlded_irpf, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(140, 5, '', 0, 0, 'L');
$pdf->Cell(35, 5, $lbl_vlliq_estimado, 0, 0, 'C');

$pdf->Ln(7);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, 'ATIVIDADES (CBO)', 0, 0, 'L');
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, $descrdetalhada_cargo, 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'Resumo de Direitos e Encargos', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(7);
/*$pdf->Cell(70, 5, '| ADICIONAIS', 0, 0, 'L'); */
/*$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');*/
$pdf->Cell(60, 5, chr(149) . ' ESTIMATIVAS ANUAIS', 0, 0, 'L');

$pdf->Ln(7);
$pdf->Cell(70, 5, 'FÉRIAS (TOTAL LÍQUIDO)', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(50, 5, $lbl_vlliq_estimado_ferias, 0, 0, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'Salário + 1/3 - Descontos', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(7);
$pdf->Cell(70, 5, '13º SALÁRIO (ANUAL)', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(50, 5, $lbl_vlbruto_13, 0, 0, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'Valor bruto estimado', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(7);
$pdf->Cell(70, 5, 'FGTS MENSAL (8%)', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(50, 5, $lbl_vlfgts_mensal, 0, 0, 'R');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'Guia DAE Mensal', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

/*
$pdf->Ln(7);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'INFORMAÇÃO: DSR E DESCONTOS', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'DSR é o Descanso Semanal Remunerado. Faltas', 0, 0, 'L');
//$pdf->Cell(70, 5, " do DSR da semana.", 0, '');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'injustificadas podem gerar desconto do dia e', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
*/

$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, 'Indicadores de Jornada e Valor', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(26, 5, 'CARGA SEMANAL', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(26, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'VALOR HORA', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(52, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'VALOR DIÁRIA', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(78, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'H. EXTRA 50%', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(104, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'H. EXTRA 100%', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(130, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'ADIC. NOTURNO', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(156, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'AUXÍLIO VIAGEM', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(26, 5, $lbl_qtd_hrsemana, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(26, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_hr, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(52, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_dia, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(78, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_hrextra_50, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(104, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_hrextra_100, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(130, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_hradic, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(156, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, $lbl_val_hrviagem, 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(26, 5, $lbl_txt_hrsemana, 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(26, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'Base cálculo mensal', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(52, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'Base 30 dias', 0, 0, 'C');

$pdf->Ln(0);
$pdf->Cell(78, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'Segunda à sábado', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(104, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, 'Domingo e feriados', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(130, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, '+20% hora normal', 0, 0, 'C');
$pdf->Ln(0);
$pdf->Cell(156, 5, '', 0, 0, 'L');
$pdf->Cell(26, 5, '+25% hora normal', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);

/*
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 5, 'Afastamento e Auxílio-Doença', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(7);
$pdf->Cell(70, 5, 'BENEFÍCIO ESTIMADO (91%)', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->Cell(70, 5, 'REGRAS IMPORTANTES', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(70, 5, $lbl_vlafasta, 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(0);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, chr(149) . ' Primeiros 15 dias: Pagos pelo empregador.', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(4);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, 'Cálculo sobre a média salarial', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(70, 5, chr(149) . ' Após 15º dia: Pago pelo INSS via auxílio-doença.', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(70, 5, '', 0, 0, 'L');
$pdf->Cell(70, 5, chr(149) . ' Carência: Geralmente 12 contribuições mensais.', 0, 0, 'L');
*/

$pdf->Ln(12);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetLineWidth(0.1); 
$pdf->Cell(0, 5, 'Estimativas de Rescisão', 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, '1. Pedido de Demissão (Contrato de Experiência)', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, 'Direitos e Pagamentos', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, 'Ocorre durante os primeiros 45 ou 90 dias. Se o empregado', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Saldo de Salário (dias trabalhados)', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'sai antes do fim, pode haver indenização de metade dos dias', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' 13º Salário Proporcional', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'restantes ao empregador.', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Férias Proporcionais + 1/3', 0, 0, 'L');

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, '2. Pedido de Demissão (Após Experiência)', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, 'Direitos e Pagamentos', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, 'O empregado deve cumprir 30 dias de aviso prévio ou permitir o', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Saldo de Salário', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'desconto do valor correspondente em sua rescisão.', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' 13º Salário Proporcional', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Férias Proporcionais e Vencidas + 1/3', 0, 0, 'L');

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, '3. Demissão com Aviso Indenizado', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, 'Direitos e Pagamentos', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, 'O empregador encerra o contrato imediatamente e paga o valor', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Verbas Integrais (Saldo, 13º, Férias)', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'do aviso. O tempo de aviso projeta o término do contrato para ', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Aviso Prévio Indenizado', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'fins de 13º e férias.', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Saque FGTS + Multa de 40%', 0, 0, 'L');

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, '4. Demissão com Aviso Trabalhado', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, 'Direitos e Pagamentos', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, 'O empregado trabalha o aviso com redução de 2 horas na jornada', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Verbas Integrais (Saldo, 13º, Férias)', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, 'diária ou folga de 7 dias corridos ao final, sem prejuízo do salário.', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Aviso Prévio Indenizado', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Saque FGTS + Multa de 40%', 0, 0, 'L');

$pdf->Ln(7);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, '5. Demissão por Justa Causa', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, 'Direitos e Pagamentos', 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(90, 5, '-', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Saldo de salário', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, '-', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Férias Vencidas (se houver) + 1/3', 0, 0, 'L');
$pdf->Ln(4);
$pdf->Cell(90, 5, '-', 0, 0, 'L');
$pdf->Ln(0);
$pdf->Cell(90, 5, '', 0, 0, 'L');
$pdf->Cell(90, 5, chr(149) .' Sem FGTS, Multa ou Seguro-Desemprego', 0, 0, 'L');

$pdf->SetFont('Arial', '', 10);

$pdf->Ln(3);
$pdf->Output('D', 'GuiaDomestico.pdf');
?>