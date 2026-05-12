<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_config.php
***** Conteúdo: Configuração para Diagnóstico do Usuário
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
  $chave_usuario = $_SESSION["CHAVE_USUARIO"];
  $chave_cargo = $_POST["CHAVE_CARGO"];  
  $descr_cargo = "";
  $cbo_cargo = "";
  $salario_usuario = $_POST["SALARIO_USUARIO"];
  $dti_usuario = $_POST["DTI_USUARIO"];
  $hrdia_usuario = $_POST["HRDIA_USUARIO"];
  $hrdiasab_usuario = $_POST["HRDIASAB_USUARIO"];
  $diasemana_usuario = $_POST["DIASEMANA_USUARIO"];  
  $salario_usuario = FormataNumSQL($salario_usuario);
	abre_db();
  $strsql = "
  select  
  tcargo.descr_cargo
  ,tcargo.cbo_cargo
  from
  tcargo
  where 
  tcargo.chave_cargo = :vchave_cargo and  
  tcargo.caixa_cargo = 1
  ";
  $qcargo = $pdo->prepare($strsql);
  $qcargo->bindParam(":vchave_cargo", $chave_cargo);
  $qcargo->execute();
  if ($tcargo = $qcargo->fetch(PDO::FETCH_ASSOC)) {
    $descr_cargo = $tcargo["descr_cargo"];
    $cbo_cargo = $tcargo["cbo_cargo"];
  }

	abre_db();
  $strsql = "
  select  
  tusuario.chave_usuario  
  from
  tusuario
  where 
  tusuario.chave_usuario = :vchave_usuario and  
  tusuario.caixa_usuario = 1
  ";
  $qusuario = $pdo->prepare($strsql);
  $qusuario->bindParam(":vchave_usuario", $chave_usuario);
  $qusuario->execute();
  if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {
		// Alteção do registro	   
		$abre_diario = abre_diario("tusuario", "chave_usuario", $chave_usuario, $campoexcluidos = array());
		$strsql = "
		update 
		tusuario 
		set
		chave_cargo = :vchave_cargo
		,salario_usuario = :vsalario_usuario
		,dti_usuario = :vdti_usuario
		,hrdia_usuario = :vhrdia_usuario
    ,hrdiasab_usuario = :vhrdiasab_usuario
		,diasemana_usuario = :vdiasemana_usuario
		where 
		chave_usuario = :vchave_usuario and 
    caixa_usuario = 1
		";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vchave_cargo", $chave_cargo);
		$qusuario->bindParam(":vsalario_usuario", $salario_usuario);
		$qusuario->bindParam(":vdti_usuario", $dti_usuario);
		$qusuario->bindParam(":vhrdia_usuario", $hrdia_usuario);
    $qusuario->bindParam(":vhrdiasab_usuario", $hrdiasab_usuario);
		$qusuario->bindParam(":vdiasemana_usuario", $diasemana_usuario);		
    $qusuario->bindParam(":vchave_usuario", $chave_usuario);
		$qusuario->execute();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario, $campoexcluidos = array());

    $_SESSION["CHAVE_CARGO"] = $chave_cargo;
    $_SESSION["DESCR_CARGO"] = $descr_cargo;
    $_SESSION["CBO_CARGO"] = $cbo_cargo;
    $_SESSION["SALARIO_USUARIO"] = $salario_usuario;
    $_SESSION["DTI_USUARIO"] = $dti_usuario;
    $_SESSION["HRDIA_USUARIO"] = $hrdia_usuario;
    $_SESSION["HRDIASAB_USUARIO"] = $hrdiasab_usuario;
    $_SESSION["DIASEMANA_USUARIO"] = $diasemana_usuario;
    // EOF Alteração do registro
  }
	header("Location: /publico/diagnostico/diagnostico_menu.php");
	die();
}

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}

$chave_cargo = "0";
$descr_cargo = "";
$cbo_cargo = "";
$salario_usuario = "";
$dti_usuario = "";
$hrdia_usuario = "";
$hrdiasab_usuario = "";
$diasemana_usuario = "";

if (isset($_SESSION["CHAVE_CARGO"])) {
  $chave_cargo = $_SESSION["CHAVE_CARGO"];
}
if (isset($_SESSION["DESCR_CARGO"])) {
  $descr_cargo = $_SESSION["DESCR_CARGO"];
}
if (isset($_SESSION["CBO_CARGO"])) {
  $cbo_cargo = $_SESSION["CBO_CARGO"];
}
if (isset($_SESSION["SALARIO_USUARIO"])) {  
  $salario_usuario = $_SESSION["SALARIO_USUARIO"];
  if (intval($salario_usuario) > 0) {
    $salario_usuario = FormataNumero($salario_usuario, 2);
  }
  else {
    $salario_usuario = "";
  }
}
if (isset($_SESSION["DTI_USUARIO"])) {
  $dti_usuario = $_SESSION["DTI_USUARIO"];
}
if (isset($_SESSION["HRDIA_USUARIO"])) {
  $hrdia_usuario = $_SESSION["HRDIA_USUARIO"];
}
if (isset($_SESSION["HRDIASAB_USUARIO"])) {
  $hrdiasab_usuario = $_SESSION["HRDIASAB_USUARIO"];
}
if (isset($_SESSION["DIASEMANA_USUARIO"])) {
  $diasemana_usuario = $_SESSION["DIASEMANA_USUARIO"];
}

//********************
//********************
//**** Pega cargos
//********************
//********************
abre_db();
$html_cargo = "<option value='' selected>Selecione o cargo</option>";
$strsql = "
select 
tcargo.chave_cargo
,tcargo.descr_cargo
,tcargo.cbo_cargo
from 
tcargo
where 
tcargo.caixa_cargo
order by tcargo.cbo_cargo
";
$qcargo = $pdo->prepare($strsql);
$qcargo->execute();
while ($tcargo = $qcargo->fetch(PDO::FETCH_ASSOC)) {
  $chave_cargo_tmp = $tcargo["chave_cargo"];
  $descr_cargo = $tcargo["descr_cargo"];
  $cbo_cargo = $tcargo["cbo_cargo"];
  $html_cargo .= "<option value='{$chave_cargo_tmp}'" . ($chave_cargo == $chave_cargo_tmp ? " selected" : "") . ">{$descr_cargo} - CBO: {$cbo_cargo}</option>\n";

}
//********************
//********************
//**** EOF Pega cargos
//********************
//********************
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Configuração para Diagnóstico do Usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<body>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <div class="card shadow card-personalizado">
    <div class="row">
      <div class="col-sm-12 mx-auto" style="max-width:800px">
        <div class="card-body">
          <div class="row">
            <div class="col-sm-12">
              <div class="card shadow">
                <div class="card-header text-bg-light" style="min-height:100px; padding-top:30px;">
                  <h5>Dados para diagnóstico</h5>
                  <span class="texto-corpo">Insira os dados do seu trabalho para gerar um diagnóstico personalizado.</span>
                </div>          
                <div class="card-body">
                  <form class="needs-validation" method="post" id="FUSUARIO_DIAGNOSTICO" name="FUSUARIO_DIAGNOSTICO" action="diagnostico_config.php" novalidate>
                    <label for="CHAVE_CARGO" class="form-label">Cargo</label>
                    <div class="form-group mb-2 has-validation">
                      <select class="form-select" id="CHAVE_CARGO" name="CHAVE_CARGO" aria-label="Informe seu cargo" required>
                        <?php echo $html_cargo; ?>
                      </select>                     
                      <div class="invalid-feedback">
                        Por favor, informe seu cargo.
                      </div>
                      <span class="texto-suave texto-menor">Obs: a lista de cargos está ordenada por CBO.</span>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <label for="SALARIO_USUARIO" class="form-label">Salário</label>
                        <div class="form-group mb-2 has-validation">
                          <input type="text" class="form-control" id="SALARIO_USUARIO" name="SALARIO_USUARIO" value="<?php echo $salario_usuario ?>" placeholder="Informe o valor do salário" required>
                          <div class="invalid-feedback">
                            Por favor, informe seu salário.
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <label for="DTI_USUARIO" class="form-label">Data de início</label>
                        <div class="form-group mb-2 has-validation">
                          <input type="date" class="form-control" id="DTI_USUARIO" name="DTI_USUARIO" value="<?php echo $dti_usuario ?>" placeholder="dia/mes/ano" required>
                          <div class="invalid-feedback">
                            Por favor, informe a data de início do seu vínculo trabalhista.
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-6">
                            <label for="HRDIA_USUARIO" class="form-label">Horas por dia</label>
                            <div class="form-group mb-2 has-validation">
                              <input type="text" class="form-control" id="HRDIA_USUARIO" name="HRDIA_USUARIO" value="<?php echo $hrdia_usuario ?>" placeholder="Ex: 8" required>
                              <div class="invalid-feedback">
                                Por favor, informe quantas horas trabalha de segunda à sexta-feira.
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <label for="HRDIASAB_USUARIO" class="form-label">Horas no Sábado</label>
                            <div class="form-group mb-2 has-validation">
                              <input type="text" class="form-control" id="HRDIASAB_USUARIO" name="HRDIASAB_USUARIO" value="<?php echo $hrdiasab_usuario ?>" placeholder="Ex: 4" required>
                              <div class="invalid-feedback">
                                Por favor, informe quantas horas trabalha no sábado. Ex: 4 (meio período).
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <label for="DIASEMANA_USUARIO" class="form-label">Dias por semana</label>
                        <div class="form-group mb-2 has-validation">
                          <input type="text" class="form-control" id="DIASEMANA_USUARIO" name="DIASEMANA_USUARIO" value="<?php echo $diasemana_usuario ?>" placeholder="Ex: 5" required>
                          <div class="invalid-feedback">
                            Por favor, informe a data de início do seu vínculo trabalhista.
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group mt-5 mb-5">
                      <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-lg btn-custom" style="width:100%"><i class="fa-solid fa-chart-column"></i>&nbsp;Gerar diagnóstico</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body text-bg-light">
                  <span class="texto-corpo texto-menor"><i class="fa-solid fa-circle-info fa-lg"></i>&nbsp;Seus dados estão protegidos e serão usados apenas para calcular seus direitos e obrigações trabalhistas conforme a legislação atual.</span>
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
<script type="text/javascript">
// Desabilita submit se houver erro nos campos
(() => {
  'use strict'
  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')
  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
</body>
</html>