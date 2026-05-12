<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_fgtsinss.php
***** Conteúdo: Guia de Contribuições INSS/FGTS
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
}
//********************
//********************
//**** EOF Pega cargos
//********************
//********************

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}
$chave_cargo = $_SESSION["CHAVE_CARGO"];
$descr_cargo = $_SESSION["DESCR_CARGO"];
$cbo_cargo = $_SESSION["CBO_CARGO"];
$salario_usuario = $_SESSION["SALARIO_USUARIO"];
$dti_usuario = $_SESSION["DTI_USUARIO"];
$hrdia_usuario = floatval($_SESSION["HRDIA_USUARIO"]);
$hrdiasab_usuario = floatval($_SESSION["HRDIASAB_USUARIO"]);
$diasemana_usuario = floatval($_SESSION["DIASEMANA_USUARIO"]);
$aliq_progressiva = 0;
$strsql = "
select 
ttabinss.*  
from 
ttabinss
where 
{$salario_usuario} >= ttabinss.vli_tabinss and {$salario_usuario} <= ttabinss.vlf_tabinss and 
ttabinss.caixa_tabinss = 1
";
$qtabinss = $pdo->prepare($strsql);
$qtabinss->execute();
if ($ttabinss = $qtabinss->fetch(PDO::FETCH_ASSOC)) {
  $aliq_tabinss = floatval($ttabinss["aliq_tabinss"]);  
  $vlfixo_tabinss = floatval($ttabinss["vlfixo_tabinss"]);
  $vli_tabinss = floatval($ttabinss["vli_tabinss"]);
  if ($vlfixo_tabinss == 0) {
    if ($aliq_tabinss > 0) {
      $aliq_progressiva = round($aliq_tabinss, 2);
      $lbl_aliq_progressiva = "Alíquota progressiva (~" . FormataPercentual($aliq_progressiva, 0) . ")";
    }
  }
  else {
    $aliq_progressiva = $vlfixo_tabinss;    
    $lbl_aliq_progressiva = "Desconto fixo de (~R$ " . FormataNumero($aliq_progressiva, 2) . ") para salário acima de R$ " . FormataNumero($vli_tabinss - 0.01, 2);
  }
}
$vlded_inss = RetornaINSS($salario_usuario, $pdo);
$vlliq_estimado = $salario_usuario - $vlded_inss;
$lbl_descr_cargo = $descr_cargo;
$lbl_chave_cargo = $chave_cargo;
$lbl_cbo_cargo = $cbo_cargo;
$lbl_dti_usuario = FormataData($dti_usuario, "d/m/Y");
$lbl_salario_usuario = "R$ " . FormataNumero($salario_usuario, 2);
if (strlen($lbl_descr_cargo) > 35) {
  $lbl_descr_cargo = substr($lbl_descr_cargo, 0, 35) . "...";
}
$lbl_vlded_inss = "-R$ " . ($vlded_inss);
$per_vlded_inss = 0;
if ($vlded_inss > 0) {
  $per_vlded_inss = round($vlded_inss / $salario_usuario * 100, 2);
}
$lbl_per_vlded_inss = FormataPercentual($per_vlded_inss, 2);

if ($vlliq_estimado < 0) {
  $lbl_vlliq_estimado = "<span class='text-danger'>-R$ " . FormataNumero($salario_usuario - $vlded_inss, 2) . "</span>";
}
else {
  $lbl_vlliq_estimado = "R$ " . FormataNumero($salario_usuario - $vlded_inss, 2);
}
$qtd_hrsemana = ($hrdia_usuario * $diasemana_usuario) + $hrdiasab_usuario;
$lbl_qtd_hrsemana = $qtd_hrsemana . "h";
$lbl_txt_hrsemana = $hrdia_usuario . "/h dia ● " . $diasemana_usuario . " dias por semana + " . $hrdiasab_usuario . "/h sábado";
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Guia de Contribuições e Impostos (FGTS/INSS)</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<style>
  .shadow-left {
    /* box-shadow: [horizontal offset] [vertical offset] [blur] [spread] [color] */
    box-shadow: -5px 0px 0px 0px #1A5275;
}
</style>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-3 pe-3">
      <div class="col-sm-12">
        <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light texto-menor" onclick="javascript:location='/publico/diagnostico/diagnostico_menu.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-sm-12">
        <h2>Guia de Contribuições e Impostos</h2>
        <span class="texto-secundario">Entenda como funcionam os depósitos do FGTS feitos pelo seu empregador e os descontos de INSS e IRRF que impactam seu salário líquido.</span>
      </div>     
    </div>
    <div class="row mb-3 ps-3 pe-3">
      <div class="col-sm-6 mt-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="d-flex">
                <div class="pe-2">
                  <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#e8eef1; float:left;">
                    <i class="fa-solid fa-user-shield fa-lg" style="color:#1A5275;"></i>                    
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  <h4 style="margin:0px;">FGTS (Seu Fundo de Garantia)</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-12">
              <span class="texto-secundario texto-menor">O FGTS é um depósito mensal de 8% do seu salário bruto realizado pelo empregador em uma conta da Caixa Econômica Federal em seu nome. Não é descontado do seu salário.</span>
            </div>
          </div>        
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="rounded" style="min-height:80px; background-color: #F8F6F6">
                <div class="row align-items-center ps-3 pe-3" style="height: 80px;">
                  <div class="col-sm-6">
                    <h5 class="texto-secundario texto-menor" style="margin:0">Depósito Mensam (8%)</h5>
                    <span class="texto-suave texto-menor">Exemplo para salário de <?php echo $lbl_salario_usuario; ?></span>
                  </div>
                  <div class="col-sm-6 text-end">
                    <span class="h4 texto-secundario texto-negrito"><?php echo $lbl_vlfgts_mensal ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>        
          <div class="row mb-3 d-none">
            <div class="col-sm-12">
              <div class="rounded" style="min-height:80px; background-color: #F8F6F6">
                <div class="row align-items-center ps-3 pe-3" style="height: 80px;">
                  <div class="col-sm-6">
                    <h5 class="texto-secundario texto-menor" style="margin:0">Saldo Acumulado</h5>
                    <span class="texto-suave texto-menor">Consulte seu saldo total através do aplicativo FGTS oficial.</span>
                  </div>
                  <div class="col-sm-6 text-end">
                    <i class="fa-solid fa-arrow-trend-up texto-secundario"></i>                    
                  </div>
                </div>
              </div>
            </div>
          </div>        
        </div>        
      </div>
      <div class="col-sm-6 mt-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="d-flex">
                <div class="pe-2">
                  <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#e8eef1; float:left;">
                    <i class="fa-solid fa-landmark fa-lg" style="color:#1A5275;"></i>                    
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  <h4 style="margin:0px;">INSS e Imposto de Renda (Seus Descontos)</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-12">
              <span class="texto-secundario texto-menor">Estes são os valores retidos mensalmente do seu salário bruto para garantir sua aposentadoria e cumprir obrigações fiscais.</span>
            </div>
          </div>        
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="rounded" style="min-height:80px; background-color: #FFF">
                <div class="row align-items-center ps-3 pe-3" style="height: 80px;">
                  <div class="col-sm-6">
                    <h5 class="texto-secundario texto-menor" style="margin:0">INSS (Previdência)</h5>
                    <span class="texto-suave texto-menor"><?php echo $lbl_aliq_progressiva ?></span>                     
                  </div>
                  <div class="col-sm-6 text-end">
                    <span class="h4 texto-secundario texto-negrito text-danger"><?php echo $lbl_vlded_inss ?></span>
                  </div>
                  <hr>
                </div>                
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="rounded" style="min-height:80px; background-color: #FFF">
                <div class="row align-items-center ps-3 pe-3" style="height: 80px;">
                  <div class="col-sm-6">
                    <h5 class="texto-secundario texto-menor" style="margin:0">IRRF (Imposto de Renda)</h5>
                    <span class="texto-suave texto-menor">Faixa de isenção aplicada</span>
                  </div>
                  <div class="col-sm-6 text-end">
                    <span class="texto-negrito texto-suave">R$ 0,00</span>
                  </div>
                  <hr>
                </div>
              </div>
            </div>
          </div>        
          <div class="row mb-3">
            <div class="col-sm-12">
              <div class="rounded" style="min-height:80px; background-color: #FFF; border: 2px dotted  #f9cdb8 !important;">
                <div class="row align-items-center ps-3 pe-3" style="height: 80px;">
                  <div class="col-sm-12">                    
                    <span class="texto-secundario texto-menor texto-italico">"O empregador contribui com 8% e você também contribui conforme a tabela oficial, garantindo sua cobertura previdenciária total."</span>
                  </div>
                </div>                
              </div>
            </div>
          </div>
        </div>        
      </div>
    </div>    
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>