<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_folhapagto.php
***** Conteúdo: Cálculo da Folha de Pagamento
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

$vlvenc_usuario = $salario_usuario;
$vltrib_usuario = $salario_usuario;
$vldesc_usuario = 0;

$vlded_inss = RetornaINSS($vltrib_usuario, $pdo);

$irpf = RetornaIRPF($vltrib_usuario, $pdo);
$vlded_irpf = $irpf["valor"];
$aliq_irpf = $irpf["aliquota"];
if ($aliq_irpf > 0) {
  $lbl_aliq_irpf = FormataPercentual($aliq_irpf, 2);
}
else {
  $lbl_aliq_irpf = "Isento";
}


$vlliq_estimado = $vlvenc_usuario - $vlded_inss;
$lbl_descr_cargo = $descr_cargo;
$lbl_chave_cargo = $chave_cargo;
$lbl_cbo_cargo = $cbo_cargo;
$lbl_dti_usuario = FormataData($dti_usuario, "d/m/Y");
$lbl_vlvenc_usuario = "R$ " . FormataNumero($vlvenc_usuario, 2);
if (strlen($lbl_descr_cargo) > 35) {
  $lbl_descr_cargo = substr($lbl_descr_cargo, 0, 35) . "...";
}
$lbl_vlded_inss = "R$ " . FormataNumero($vlded_inss, 2);
$per_vlded_inss = 0;
if ($vlded_inss > 0) {
  $per_vlded_inss = round($vlded_inss / $salario_usuario * 100, 2);
}
$lbl_per_vlded_inss = FormataPercentual($per_vlded_inss, 2);
$lbl_vlded_irpf = "R$ " . FormataNumero($vlded_irpf, 2);
$per_vlded_irpf = 0;
if ($vlded_irpf > 0) {
  $per_vlded_irpf = 0;
}
$lbl_per_vlded_irpf = FormataPercentual($per_vlded_irpf, 2);

$vldesc_usuario = $vlded_inss + $vlded_irpf;
$lbl_vldesc_usuario = "R$ " . FormataNumero($vldesc_usuario, 2);

if ($vlliq_estimado < 0) {
  $lbl_vlliq_estimado = "<span class='text-danger'>-R$ " . FormataNumero($vlvenc_usuario - $vldesc_usuario, 2) . "</span>";
}
else {
  $lbl_vlliq_estimado = "R$ " . FormataNumero($vlvenc_usuario - $vldesc_usuario, 2);
}
$per_vldesc = round($vldesc_usuario / $vlvenc_usuario * 100, 2);
$lbl_per_vldesc = FormataPercentual($per_vldesc, 2);
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
<title>Guia Doméstico - Cálculo da Folha de Pagamento</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-3 pe-3">
      <div class="col">
        <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light texto-menor" onclick="javascript:location='/publico/diagnostico/diagnostico_menu.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-12">
        <h2>Folha de Pagamento</h2>
      </div>     
    </div>

    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <div class="d-flex">
            <div>
              <span class="texto-suave texto-menor">Salário Bruto</span>
            </div>
            <div class="ms-auto">
              <i class="fa-solid fa-money-bill-1 fa-lg texto-suave"></i>
            </div>
          </div>          
          <div class="row">
            <div class="col">
              <span class="h2 texto-negrito">
                <?php echo $lbl_vlvenc_usuario; ?>
              </span>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <span class="texto-suave texto-menor">
                Base contratual padrão
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <div class="d-flex">
            <div>
              <span class="texto-suave texto-menor">Descontos totais</span>
            </div>
            <div class="ms-auto">
              <i class="fa-solid fa-arrow-trend-down texto-suave"></i>              
            </div>
          </div>          
          <div class="row">
            <div class="col">
              <span class="h2 texto-negrito">
                <?php echo $lbl_vldesc_usuario ?>
              </span>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <span class="texto-suave texto-menor">
                <i class="fa-solid fa-arrow-up"></i>&nbsp;<?php echo $lbl_per_vldesc; ?> do bruto
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3 shadow" style="background-color:#1A5275;">
          <div class="d-flex">
            <div>
              <span class="texto-menor texto-inverso-regular">Salário líquido</span>
            </div>
            <div class="ms-auto">
              <i class="fa-solid fa-arrow-right-from-bracket texto-inverso-regular"></i>              
            </div>
          </div>          
          <div class="row">
            <div class="col">
              <span class="h2 texto-negrito texto-branco-regular">
                <?php echo $lbl_vlliq_estimado ?>
              </span>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <span class="texto-menor texto-inverso-regular">
                Valor a ser depositado
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF;">
          <h4>Detalhes da Jornada</h4>
          <span>CARGA HORÁRIA</span><br>
          <span><?php echo $lbl_qtd_hrsemana ?> semanais</span><br>
          <span><?php echo $lbl_txt_hrsemana ?></span><br>
          <hr>
          <span class="texto-suave texto-negrito texto-menor">VALOR HORA</span><br>
          <p class="texto-negrito texto-maior"><?php echo $lbl_val_hr ?></p>
          <span class="texto-suave texto-negrito texto-menor">VALOR DIA</span><br>
          <p class="texto-negrito texto-maior"><?php echo $lbl_val_dia ?></p>
          <span class="texto-suave texto-negrito texto-menor">VALOR CADA HORA EXTRA 50%</span><br>
          <p class="texto-negrito texto-maior"><?php echo $lbl_val_hrextra_50 ?></p>
          <span class="texto-suave texto-negrito texto-menor">VALOR CADA HORA EXTRA 100%</span><br>
          <p class="texto-negrito texto-maior"><?php echo $lbl_val_hrextra_100 ?></p>
          <span class="texto-suave texto-negrito texto-menor">VALOR CADA HORA ADICIONAL NOTURNO 20%</span><br>
          <p class="texto-negrito texto-maior"><?php echo $lbl_val_hradic ?></p>
          <span class="texto-suave texto-negrito texto-menor">VALOR CADA HORA DO AUXÍLIO VIAGEM 25%</span><br>
          <span class="texto-negrito texto-maior"><?php echo $lbl_val_hrviagem ?></span>
        </div>
      </div>
      <div class="col-md-8">        
        <div class="card">
          <div class="card-header bg-transparent">
            <div class="row mt-4 mb-3 ps-3 pe-3">
              <div class="col-md-6">
                <div class="d-flex">
                  <div>
                    <span class="texto-maior texto-negrito"><i class="fa-regular fa-file-lines texto-corpo"></i>&nbsp;Holerite</span>
                  </div>
                  <div class="ms-auto">
                    <span class="rounded texto-menor2 texto-negrito" style="background-color:#F1F5F9; padding:4px;">&nbsp;CALCULADO&nbsp;</span>
                  </div>
                </div>          
              </div>          
            </div>          
          </div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <thead class="table-light">
                <tr class="texto-suave texto-menor">
                  <th scope="col">DESCRIÇÃO</th>
                  <th scope="col">REFERÊNCIA</th>
                  <th scope="col" class="text-end">VENCIMENTOS</th>
                  <th scope="col" class="text-end">DESCONTOS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td scope="col" class="texto-suave">Salário base</td>
                  <td scope="col" class="texto-suave">30 Dias</td>
                  <td scope="col" class="texto-negrito text-end"><?php echo $lbl_vlvenc_usuario; ?></td>
                  <td scope="col" class="texto-negrito text-end"></td>
                </tr>
                <tr>
                  <td scope="col" class="texto-suave">INSS (Previdência Social)</td>
                  <td scope="col" class="texto-suave"><?php echo $lbl_per_vlded_inss; ?></td>
                  <td scope="col" class="texto-negrito text-end"></td>
                  <td scope="col" class="texto-negrito text-end"><?php echo $lbl_vlded_inss; ?></td>
                </tr>
                <tr>
                  <td scope="col" class="texto-suave">IRRF (Imposto de Renda)</td>
                  <td scope="col" class="texto-suave"><?php echo $lbl_aliq_irpf; ?></td>
                  <td scope="col" class="texto-negrito text-end"></td>
                  <td scope="col" class="texto-negrito text-end"><?php echo $lbl_vlded_irpf ?></td>
                </tr>
                <tr class="table-light align-middle" style="height: 80px;">
                  <td colspan="2" scope="col" class="border-bottom-0 texto-suave texto-maior">Totais</td>                    
                  <td scope="col" class="border-bottom-0 texto-negrito texto-maior text-end texto-maior"><?php echo $lbl_vlvenc_usuario; ?></td>
                  <td scope="col" class="border-bottom-0 texto-negrito texto-maior text-end texto-suave"><?php echo $lbl_vldesc_usuario; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-footer" style="background-color:#e2eaef">
            <div class="row mt-4 mb-3">
              <div class="col-md-6">
                <span class="h3 texto-negrito">
                  LÍQUIDO A RECEBER
                </span>
              </div>
              <div class="col-md-6 text-end">
                <span class="h3 texto-negrito texto-regular">
                  <?php echo $lbl_vlliq_estimado ?>
                </span>
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