<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_ferias13.php
***** Conteúdo: Cálculo de Férias e 13º Salário
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
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$Acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$Acao = "DIAGNOSTICAR";
}
if ($Acao == "DIAGNOSTICAR") {
	//$chave_usuario = $_POST["chave_usuario"];
	//$nome_usuario = $_POST["nome_usuario"];
	//$email_usuario = $_POST["email_usuario"];
	//$senha_usuario = $_POST["senha_usuario"];
  //$sit_usuario = $_POST["sit_usuario"];
	//$tipo_usuario = 'ADMINISTRADOR';

	header("Location: /publico/diagnostico/diagnostico_menu.php");
	die();
}

abre_db();

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
$hrdia_usuario = $_SESSION["HRDIA_USUARIO"];
$diasemana_usuario = $_SESSION["DIASEMANA_USUARIO"];

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

/*

$lbl_descr_cargo = $descr_cargo;
$lbl_chave_cargo = $chave_cargo;
$lbl_cbo_cargo = $cbo_cargo;
$lbl_dti_usuario = FormataData($dti_usuario, "d/m/Y");
$lbl_salario_usuario = FormataNumero($salario_usuario, 2);
if (strlen($lbl_descr_cargo) > 35) {
  $lbl_descr_cargo = substr($lbl_descr_cargo, 0, 35) . "...";
}
$lbl_vlded_inss = "-R$ " . ($vlded_inss);
if ($vlliq_estimado < 0) {
  $lbl_vlliq_estimado = "<span class='text-danger'>-R$ " . FormataNumero($salario_usuario - $vlded_inss, 2) . "</span>";
}
else {
  $lbl_vlliq_estimado = "R$ " . FormataNumero($salario_usuario - $vlded_inss, 2);
}

*/


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Cálculo de Férias e 13º Salário</title>
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
    <div class="row mt-2 ps-3 pe-3">
      <div class="col-12">
        <h2>Férias e 13º Salário</h2>      
      </div>     
    </div>
    <div class="row mt-2 ps-3 pe-3">
      <div class="col-12">
        <span class="texto-corpo">Detalhamento completo dos benefícios trabalhistas calculados com base no salário mensal de <?php echo $lbl_vlsalbase_ferias; ?>.</span>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="card-body" style="padding-top:0px;">
          <div class="row">
            <div class="col-12">
              <div class="card" style="min-height: 460px;">
                <div class="card-body" style="min-height:100px; padding-top:30px;">
                  <div class="row mb-4">
                    <div class="col-md-12">
                      <div class="d-flex">
                        <div class="pe-2">
                          <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#afc2cf; float:left;">
                            <i class="fa-regular fa-money-bill-1 fa-2x" style="color:#1A5275;"></i>                                                          
                          </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                          <h4 style="margin:0px;">13º Salário</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-4">
                    <div class="col-12">
                      <span class="texto-corpo texto-menor">Gratificação natalina devida a todo trabalhador sob regime CLT. Corresponde a 1/12 da remuneração por mês trabalhado.</span>
                    </div>
                  </div>
                  <div class="row mt-2 ps-2 pe-2">
                    <div class="col-md-12 rounded" style="min-height:80px; background-color: #F8F6F6">
                      <div class="row align-items-center" style="height: 80px;">
                        <div class="col-md-6">
                          <h5 class="texto-corpo texto-menor" style="margin:0">1ª PARCELA (50%)</h5>
                          <span class="texto-corpo texto-menor">Até 30 de Novembro</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlparcela1_13 ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3 ps-2 pe-2">
                    <div class="col-md-12 rounded" style="min-height:80px; background-color: #F8F6F6">
                      <div class="row align-items-center" style="height: 80px;">
                        <div class="col-md-6">
                          <h5 class="texto-corpo texto-menor" style="margin:0">1ª PARCELA (50%)</h5>
                          <span class="texto-corpo texto-menor">Até 30 de Novembro</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlparcela2_13 ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row ps-2 pe-2">
                    <div class="col-md-12 rounded">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <span class="texto-corpo texto-menor">Desconto INSS (Estimado)</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="text-danger"><?php echo $lbl_vlded_inss_13 ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-4 ps-2 pe-2">
                    <div class="col-md-12 rounded">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <span class="h4 texto-negrito">Total Líquido Estimado</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlliq_estimado_13 ?></span>
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

      <div class="col-md-6">
        <div class="card-body" style="padding-top:0px;">
          <div class="row">
            <div class="col-12">            
              <div class="card" style="min-height: 460px;">
                <div class="card-body" style="min-height:100px; padding-top:30px;">
                  <div class="row mb-4">
                    <div class="col-md-12">
                      <div class="d-flex">
                        <div class="pe-2">
                          <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#afc2cf; float:left;">
                            <i class="fa-solid fa-umbrella-beach fa-2x" style="color:#1A5275;"></i>
                          </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center">
                          <h4 style="margin:0px;">Férias</h4>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-4">
                    <div class="col-md-12">
                      <span class="texto-corpo texto-menor">Direito ao descanso anual remunerado após cada período de 12 meses de vigência do contrato de trabalho (período aquisitivo).</span>
                    </div>
                  </div>
                  <div class="row mt-2 ps-2 pe-2">
                    <div class="col-md-12 rounded" style="min-height:80px; background-color: #F8F6F6">
                      <div class="row align-items-center" style="height: 80px;">
                        <div class="col-md-6">
                          <h5 class="texto-corpo texto-menor" style="margin:0">SALÁRIO BASE DE FÉRIAS</h5>
                          <span class="texto-corpo texto-menor">Período: 1 ano trabalhado</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlsalbase_ferias ?></span>
                        </div>
                      </div>                                      
                    </div>
                  </div>
                  <div class="row mt-3 ps-2 pe-2">
                    <div class="col-md-12 rounded" style="min-height:80px; background-color: #F8F6F6">
                      <div class="row align-items-center" style="height: 80px;">
                        <div class="col-md-6">
                          <h5 class="texto-corpo texto-menor" style="margin:0">1/3 CONSTITUCIONAL</h5>
                          <span class="texto-corpo texto-menor">Adicional obrigatório</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlterco_salbase_ferias ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row ps-2 pe-2">
                    <div class="col-md-12 rounded">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <span class="texto-corpo texto-menor">Valor Bruto (Salário + 1/3)</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="texto-negrito"><?php echo $lbl_vlsalbruto_ferias ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row ps-2 pe-2">
                    <div class="col-md-12 rounded">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <span class="texto-corpo texto-menor">Desconto INSS (Estimado)</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="text-danger"><?php echo $lbl_vlded_inss_ferias ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-4 ps-2 pe-2">
                    <div class="col-md-12 rounded">
                      <div class="row align-items-center">
                        <div class="col-md-6">
                          <span class="h4 texto-negrito">Total Líquido de Férias</span>
                        </div>
                        <div class="col-md-6 text-end">
                          <span class="h4 texto-negrito"><?php echo $lbl_vlliq_estimado_ferias ?></span>
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
    <div class="row mt-4 ps-5 pe-5">
      <div class="col-12">
        <div class="row ps-5 pe-5">
          <div class="col-md-12 rounded pt-4" style="background-color:#f4f1f3; min-height: 100px;">
            <div class="row mb-2">
              <div class="col">
                <h5><i class="fa-solid fa-circle-info fa-lg" style="color:#EC5B13"></i>&nbsp;Observações Importantes</h5>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <ul>
                  <li class="mb-2">
                    <span class="texto-corpo texto-menor">Os valores de INSS são calculados com base nas tabelas progressivas vigentes e podem variar conforme atualizações governamentais.</span>
                  </li>
                  <li class="mb-2">
                    <span class="texto-corpo texto-menor">O 13º salário gera saldo de FGTS para a conta do funcionário, e o empregador paga este imposto  na guia mensal eSocial.</span>
                  </li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul>
                  <li class="mb-2">
                    <span class="texto-corpo texto-menor">O pagamento das férias deve ser efetuado até 2 dias antes do início do período de gozo do descanso.</span>
                  </li>
                  <li class="mb-2">
                    <span class="texto-corpo texto-menor">A simulação assume 30 dias de férias e zero faltas injustificadas no período aquisitivo.</span>
                  </li>
                </ul>
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