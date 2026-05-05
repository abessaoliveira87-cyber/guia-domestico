<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_resumo.php
***** Conteúdo: Resumo do Diagnóstico do Usuário
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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Resumo do Diagnóstico do Usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row mt-2 ps-2 pe-2 gx-1">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-transparent">            
            <button class="btn btn-sm btn-light sem-decoracao texto-menor" id="BTN_VOLTAR" name="BTN_VOLTAR" onclick="javascript:location='/publico/diagnostico/diagnostico_menu.php'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
            <h6 class="texto-regular mt-2"><i class="fa-regular fa-user texto-regular"></i>&nbsp;Resumo</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <span class="texto-negrito texto-suave texto-menor2">CARGO</span><br>
                <span class="texto-negrito texto-maior texto-regular" title="<?php echo $descr_cargo; ?>"><?php echo $lbl_descr_cargo; ?></span><br>
                <span class="texto-suave texto-menor3">CBO: <?php echo $lbl_cbo_cargo; ?></span><br>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <span class="texto-negrito texto-suave texto-menor2">ADMISSÃO</span><br>
                <span class="texto-negrito"><?php echo $lbl_dti_usuario; ?></span>
              </div>
              <div class="col-md-6">
                <span class="texto-negrito texto-suave texto-menor2">SALÁRIO BASE</span><br>                
                <span class="texto-negrito"><?php echo $lbl_salario_usuario; ?></span>                
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-6">
                <span class="texto-negrito texto-suave texto-menor2">INSS (DEDUÇÃO)</span><br>
                <span class="text-danger texto-negrito"><?php echo $lbl_vlded_inss; ?></span>
              </div>
              <div class="col-md-6">
                <span class="texto-negrito texto-suave texto-menor2">IRPF (DEDUÇÃO)</span><br>                
                <span class="text-danger texto-negrito"><?php echo $lbl_vlded_irpf; ?></span>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <div class="rounded pt-2 ps-2 pe-2 pb-2" style="background-color: #EFF6FF">
                  <span class="texto-negrito texto-suave texto-menor2 texto-italico">LÍQUIDO ESTIMADO</span><br>
                  <span class="text-success"><strong><?php echo $lbl_vlliq_estimado; ?></strong></span>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer bg-transparent">
            <span class="texto-negrito texto-suave texto-menor2">ATIVIDADES (CBO)</span><br>
            <span class="texto-suave texto-menor2"><?php echo $descrdetalhada_cargo; ?></span>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-12">
            <h6 class="texto-regular mt-2"><i class="fa-solid fa-circle-dollar-to-slot"></i>&nbsp;Resumo de Direitos e Encargos</h6>
            <span class="texto-negrito texto-suave texto-menor2 d-none" style="padding-left:20px">|&nbsp;ADICIONAIS</span>
          </div>
        </div>
        <div class="row mt-2 d-none">
          <div class="col-md-6 ps-4">
            <div class="pt-2 ps-3 pe-3 pb-2 border rounded" style="background-color:#F8FAFC;">
              <span class="texto-menor2 texto-negrito texto-suave">DSR SOBRE H.E.</span><br>
              <span class="texto-negrito">???? R$ 6,00/h</span><br>
              <span class="texto-suave texto-menor2">Reflexo sobre Extras</span>
            </div>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-12">
            <span class="texto-negrito texto-suave texto-menor2" style="padding-left:20px">|&nbsp;ESTIMATIVAS ANUAIS</span>
          </div>
        </div>
        <div class="row mt-2 ps-3">
          <div class="col-12">
            <div class="pt-2 ps-3 pe-3 pb-2 border rounded" style="background-color:#EFF6FF;">
              <div class="row">
                <div class="col-md-6">
                  <span class="texto-menor2 texto-negrito texto-suave">FÉRIAS (TOTAL LÍQUIDO)</span><br>
                  <span class="texto-menor2 texto-suave">Salário + 1/3 - Descontos</span><br>
                </div>
                <div class="col-6 text-end">
                  <span class="texto-maior2 texto-regular texto-negrito"><?php echo $lbl_vlliq_estimado_ferias ?></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 mt-2">
            <div class="pt-2 ps-3 pe-3 pb-2 border rounded" style="background-color:#EFF6FF;">
              <div class="row">
                <div class="col-md-6">
                  <span class="texto-menor2 texto-negrito texto-suave">13º SALÁRIO (ANUAL)</span><br>
                  <span class="texto-menor2 texto-suave">Valor bruto estimado</span><br>
                </div>
                <div class="col-6 text-end">
                  <span class="texto-maior2 texto-regular texto-negrito"><?php echo $lbl_vlbruto_13 ?></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 mt-2">
            <div class="pt-2 ps-3 pe-3 pb-2 border rounded" style="background-color:#F8FAFC;">
              <div class="row">
                <div class="col-md-6">
                  <span class="texto-menor2 texto-negrito texto-suave">FGTS MENSAL (8%)</span><br>
                  <span class="texto-menor2 texto-suave">Guia DAE Mensal</span><br>
                </div>
                <div class="col-6 text-end">
                  <span class="texto-maior2 texto-regular texto-negrito"><?php echo $lbl_vlfgts_mensal ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-2 ps-3">
          <div class="col-12 mt-2">
            <div class="pt-2 ps-3 pe-3 pb-2 border rounded" style="background-color:#F8FAFC;">
              <div class="row">
                <div class="col-12">
                  <span class="texto-menor2 texto-negrito">INFORMAÇÃO: DSR E DESCONTOS</span><br>
                  <span class="texto-menor2 texto-suave">DSR é o Descanso Semanal Remunerado. Faltas injustificadas podem gerar desconto do dia e do DSR da semana.</span><br>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="row gx-1">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-transparent">
                <h6 class="texto-regular mt-2"><i class="fa-solid fa-calculator texto-regular"></i>&nbsp;Indicadores de Jornada e Valor</h6>
              </div>
              <div class="card-body">
                <div class="row gx-1">
                  <div class="col-md-4">
                    <div class="p-2 border rounded text-center" style="min-height: 90px;">
                      <span class="texto-menor2 texto-negrito texto-suave">CARGA SEMANAL</span><br>
                      <span class="texto-maior3 texto-negrito"><?php echo $lbl_qtd_hrsemana ?></span><br>
                      <span class="texto-suave texto-menor texto-italico"><?php echo $lbl_txt_hrsemana ?></span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-2 border rounded text-center" style="min-height: 90px;">                    
                      <span class="texto-menor2 texto-negrito texto-suave">VALOR HORA</span><br>
                      <span class="texto-maior3 texto-negrito"><?php echo $lbl_val_hr ?></span><br>
                      <span class="texto-suave texto-menor texto-italico">Base Cálculo Mensal</span>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="p-2 border rounded text-center" style="min-height: 90px;">                    
                      <span class="texto-menor2 texto-negrito texto-suave">VALOR DIÁRIA</span><br>
                      <span class="texto-maior3 texto-negrito"><?php echo $lbl_val_dia ?></span><br>
                      <span class="texto-suave texto-menor texto-italico">Base 30 dias</span>
                    </div>
                  </div>
                </div>

                <div class="row mt-2 gx-1">
                  <div class="col-md-3">
                    <div class="pt-2 pb-2 border rounded text-center" style="background-color:#F8FAFC; min-height: 80px;">
                      <span class="texto-menor2 texto-negrito texto-suave">EXTRA 50%</span><br>
                      <span class="texto-negrito"><?php echo $lbl_val_hrextra_50 ?></span><br>
                      <span class="texto-suave texto-menor2 texto-italico">Segunda à Sábado</span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="pt-2 pb-2 border rounded text-center" style="background-color:#F8FAFC; min-height: 80px;">
                      <span class="texto-menor2 texto-negrito texto-suave">EXTRA 100%</span><br>
                      <span class="texto-negrito"><?php echo $lbl_val_hrextra_100 ?></span><br>
                      <span class="texto-suave texto-menor2 texto-italico">Domingo e Feriados</span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="pt-2 pb-2 border rounded text-center" style="background-color:#F8FAFC; min-height: 80px;">
                      <span class="texto-menor2 texto-negrito texto-suave">ADIC. NOTURNO</span><br>
                      <span class="texto-negrito"><?php echo $lbl_val_hradic ?></span><br>
                      <span class="texto-suave texto-menor2 texto-italico">+20% sobre hora normal</span>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="pt-2 pb-2 border rounded text-center" style="background-color:#F8FAFC; min-height: 80px;">
                      <span class="texto-menor2 texto-negrito texto-suave">AUX. VIAGEM</span><br>
                      <span class="texto-negrito"><?php echo $lbl_val_hrviagem ?></span><br>
                      <span class="texto-suave texto-menor2 texto-italico">+25% sobre hora normal</span>
                    </div>
                  </div>
                </div>
              </div>              
            </div>         
          </div>
          <div class="col-md-5 d-none">
            <div class="card">
              <div class="card-header bg-transparent">                            
                <h6 class="texto-regular mt-2"><i class="fa-regular fa-heart texto-regular"></i>&nbsp;Afastamento e Auxílio-Doença</h6>
              </div>
              <div class="card-body">
                <div class="row gx-1">
                  <div class="col-md-12">
                    <div class="pt-2 ps-2 pe-2 pb-2 rounded" style="background-color:#EFF6FF; min-height: 100px;">                     
                      <span class="texto-menor2 texto-negrito texto-suave">BENEFÍCIO ESTIMADO (91%)</span><br>
                      <span class="texto-maior3 texto-negrito texto-regular"><?php echo $lbl_vlafasta ?></span><br>
                      <span class="texto-suave texto-menor2 texto-italico">Cálculo sobre a média salarial</span>
                    </div>
                  </div>
                </div>
                <div class="row mt-2 gx-1">
                  <div class="col-md-12">
                    <div class="pt-2 ps-2 pe-2 pb-2 rounded" style="background-color:#F8FAFC; min-height: 100px;">                     
                      <span class="texto-menor2 texto-negrito texto-suave">REGRAS IMPORTANTES</span><br>
                      <ul class="texto-menor2 texto-suave mt-2 mb-0" style="padding-left:20px;">
                        <li>Primeiros 15 dias: Pagos pelo empregador.</li>
                        <li>Após 15º dia: Pago pelo INSS via auxílio-doença.</li>
                        <li>Carência: Geralmente 12 contribuições mensais.</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>              
            </div>         
          </div>
        </div>
        <div class="row mt-2 gx-1">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-transparent">
                <h6 class="texto-regular mt-2"><i class="fa-solid fa-calculator texto-regular"></i>&nbsp;Estimativas de Rescisão</h6>
              </div>              
              <div class="card-body">
                <div class="row gx-1">
                  <div class="col-md-6">
                    <div class="rounded border p-2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-regular fa-clock" style="color:#F59E0B"></i>&nbsp;1. Pedido de Demissão (Contrato de Experiência)</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-5">
                          <span class="texto-menor2">Ocorre durante os primeiros 45 ou 90 dias. Se o empregado sai antes do fim, pode haver indenização de metade dos dias restantes ao empregador.</span>
                        </div>
                        <div class="col-7">
                          <div class="rounded border p-2" style="background-color: #F8FAFC;">
                            <p class="texto-menor texto-negrito texto-suave">Direitos e Pagamentos</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Saldo de Salário (dias trabalhados)</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;13º Salário Proporcional</p>
                            <span class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Férias Proporcionais + 1/3</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="rounded border p-2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-solid fa-person-walking-luggage" style="color:#64748B"></i>&nbsp;4. Demissão com Aviso Trabalhado</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-md-5">
                          <span class="texto-menor2">O empregado trabalha o aviso com redução de 2 horas na jornada diária ou folga de 7 dias corridos ao final, sem prejuízo do salário.</span>
                        </div>
                        <div class="col-md-7">
                          <div class="rounded border p-2" style="background-color: #F8FAFC;">
                            <p class="texto-menor texto-negrito texto-suave">Direitos e Pagamentos</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Saldo de Salário (mês do aviso)</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;13º Salário Proporcional</p>
                            <span class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Saque FGTS + Multa de 40%</span>
                          </div>
                        </div>
                      </div>
                    </div>                    
                  </div>
                </div>
                <div class="row mt-3 gx-1">
                  <div class="col-md-6">
                    <div class="rounded border p-2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-solid fa-user-minus" style="color:#3B82F6"></i>&nbsp;2. Pedido de Demissão (Após Experiência)</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-5">
                          <span class="texto-menor2">O empregado deve cumprir 30 dias de aviso prévio ou permitir o desconto do valor correspondente em sua rescisão.</span>
                        </div>
                        <div class="col-7">
                          <div class="rounded border p-2" style="background-color: #F8FAFC;">
                            <p class="texto-menor texto-negrito texto-suave">Direitos e Pagamentos</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Saldo de Salário</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;13º Salário Proporcional</p>
                            <span class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Férias Proporcionais e Vencidas + 1/3</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="rounded border p-2" style="background-color:#FEF2F2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-solid fa-gavel" style="color:#DC2626"></i>&nbsp;5. Demissão por Justa Causa</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-md-5">
                          <span class="texto-menor2"></span>
                        </div>
                        <div class="col-md-7">
                          <div class="p-2">
                            <p class="texto-menor texto-negrito" style="color: #DC2626">Direitos e Pagamentos</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-solid fa-ban" style="color:#DC2626"></i>&nbsp;Saldo de Salário</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-solid fa-ban" style="color:#DC2626"></i>&nbsp;Férias Vencidas (se houver) + 1/3</p>
                            <span class="texto-menor2 texto-suave" style="line-height: 1; color:#DC2626">Sem FGTS, Multa ou Seguro-Desemprego</span>
                          </div>
                        </div>
                      </div>
                    </div>                    
                  </div>
                </div>
                <div class="row mt-3 gx-1">
                  <div class="col-md-6">
                    <div class="rounded border p-2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-regular fa-money-bill-1" style="color:#1F5F7A"></i>&nbsp;3. Demissão com Aviso Indenizado</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-5">
                          <span class="texto-menor2">O empregador encerra o contrato imediatamente e paga o valor do aviso. O tempo de aviso projeta o término do contrato para fins de 13º e férias.</span>
                        </div>
                        <div class="col-7">
                          <div class="rounded border p-2" style="background-color: #F8FAFC;">
                            <p class="texto-menor texto-negrito texto-suave">Direitos e Pagamentos</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Verbas Integrais (Saldo, 13º, Férias)</p>
                            <p class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Aviso Prévio Indenizado</p>
                            <span class="texto-menor2 texto-suave" style="line-height: 1;"><i class="fa-regular fa-clock" style="color:#1F5F7A"></i>&nbsp;Saque FGTS + Multa de 40%</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-2">
                      <div class="row">
                        <div class="col-12">
                          <span class="texto-negrito texto-menor"><i class="fa-solid fa-link" style="color:#1A5275"></i>&nbsp;Links Úteis</span>
                        </div>
                      </div>
                      <div class="row mt-3 gx-1">
                        <div class="col-md-5">
                          <p class="d-none"><a href="#" class="sem-decoracao texto-menor2"><i class="fa-solid fa-arrow-right-from-bracket" style="color:#2563EB"></i>&nbsp;eSocial Doméstico</a></p>
                          <p><a href="#" class="sem-decoracao texto-menor2"><i class="fa-solid fa-gavel" style="color:#F97316"></i>&nbsp;Ministério do Trabalho</a></p>
                          <p><a href="#" class="sem-decoracao texto-menor2"><i class="fa-solid fa-shield-halved" style="color:#16A34A"></i>&nbsp;Portal FGTS</a></p>
                          <p class="d-none"><a href="#" class="sem-decoracao texto-menor2"><i class="fa-regular fa-circle-question" style="color:#3B82F6"></i>&nbsp;Guia de Direitos</a></p>
                        </div>
                        <div class="col-md-7">
                          <div class="p-2">
                            <button type="button" class="btn btn-custom mb-2" style="width: 100%" onclick="javascript:location='diagnostico_resumo_pdf.php'"><i class="fa-regular fa-file-pdf"></i>&nbsp;Exportar para PDF</button>                            
                            <button type="button" class="btn btn-success mt-2" style="width: 100%" onclick="javascript:location='diagnostico_config.php'"><i class="fa-solid fa-rotate-right"></i>&nbsp;Novo Diagnóstico</button>
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
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>