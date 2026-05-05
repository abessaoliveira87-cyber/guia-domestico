<?php 
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/diagnostico/diagnostico_rescisao.php
***** Conteúdo: Rescisão do Contrato de Trabalho
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

$nome_usuario = $_SESSION["NOME_USUARIO"];
$pos = strpos($nome_usuario, " ");
if ($pos > 0) {
  $nome_usuario = substr($nome_usuario, 0, $pos);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Rescisão do Contrato de Trabalho</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<style>
.cardx {
  position: relative; /* Necessário para ancorar a etiqueta */
  width: 400px;
  min-height: 250px;
  background-color: #e9f2f9; /* Tom de azul claro do fundo */
  border: 2px solid #205c7e; /* Borda externa */
  border-radius: 15px;
  padding: 20px;
  font-family: sans-serif;
  overflow: hidden; /* Garante que nada saia das bordas arredondadas */
}

.tag {
  position: absolute;
  top: 0;
  right: 0;
  background-color: #1a5372; /* Azul escuro da etiqueta */
  color: white;
  padding: 4px 15px;
  font-size: 10px;
  font-weight: bold;
  text-transform: uppercase;  
  /* Arredondamento apenas no canto inferior esquerdo para o efeito visual */
  border-bottom-left-radius: 12px;  
  /* Ajuste de profundidade opcional */
  letter-spacing: 0.5px;
}

</style>
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
        <h2>Rescião do Contrato</h2>
        <span class="texto-suave">Um guia completo para entender os direitos e obrigações no desligamento do empregado doméstico.</span>
      </div>     
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-md-12">
        <div class="rounded ps-3 pe-3 pt-3" style="background-color:#f0f3f6">
          <div class="row">
            <div class="col-md-1 mb-3">
              <div class="rounded d-flex align-items-center justify-content-center" style="width:64px; height:64px; background-color:#e6edf1; float:left;">                
                <i class="fa-solid fa-circle-info fa-2x" style="color:#1A5275;"></i>                                                          
              </div>
            </div>
            <div class="col-md-11 mb-3">
              <h5 style="margin:0px;">Entenda o aviso prévio</h5><br>
              <span class="texto-secundario">O aviso prévio é a comunicação da rescisão com 30 dias de antecedência. Pode ser <strong>Trabalhado</strong> (o empregado cumpre os dias e recebe salário normal) ou <strong>Indenizado/Compensado</strong> (a parte que rescinde paga o valor equivalente ao salário sem a prestação do serviço).</span>              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4 mb-3 ps-3 pe-3">
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF; min-height:460px;">
          <div class="row mb-3">
            <div class="col">
              <div class="d-flex">
                <div class="pe-2">
                  <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#ebf3fe; float:left;">              
                    <i class="fa-solid fa-user-minus fa-lg" style="color:#2563EB;"></i>                
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  <h5 style="margin:0px;">Pedido de Demissão</h5>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="texto-suave">Iniciativa parte do empregado. Não há direito ao saque do FGTS ou seguro- desemprego.</p>
            </div>
          </div>
          <div class="row mt-2 mb-3">
            <div class="col">
              <p class="texto-negrito texto-suave texto-menor">DIREITOS PRINCIPAIS</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Saldo de Salário</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;13º Proporcional</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Férias Vencidas + Proporcionais</p>
            </div>
          </div>
          <hr>
          <div class="row mt-2 mb-3">
            <div class="col">
              <span class="texto-negrito texto-suave">Dica:&nbsp;</span>
              <span class="texto-suave texto-menor">Se não cumprir o aviso, o valor pode ser descontado.</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3 cardx" style="background-color:#e8eff8; min-height:460px; border: 2px solid #1A5275 !important; position: relative;">
          <div class="tag">CENÁRIO COMUM</div>
          <div class="row mb-3">
            <div class="col">
              <div class="d-flex">
                <div class="pe-2">
                  <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#1A5275; float:left;">                    
                    <i class="fa-solid fa-user-xmark fa-lg" style="color:#FFF;"></i>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  <h5 style="margin:0px;">Dispensa Sem Justa Causa</h5>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="texto-suave">Iniciativa do empregador sem motivo grave. O empregado possui todos os direitos preservados.</p>
            </div>
          </div>
          <div class="row mt-2 mb-3">
            <div class="col">
              <p class="texto-negrito texto-corpo texto-menor">DIREITOS PRINCIPAIS</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Saque do FGTS + Multa 40%</p>              
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Saldo de Salário e 13º Prop.</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Aviso Prévio Indenizado</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Seguro-Desemprego</p>

            </div>
          </div>
          <hr>
          <div class="row mt-2 mb-3">
            <div class="col">              
              <span class="texto-corpo texto-menor">Modalidade com maior carga financeira para o empregador.</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="rounded border ps-3 pe-3 pt-3 pb-3" style="background-color:#FFF; min-height:460px;">
          <div class="row mb-3">
            <div class="col">
              <div class="d-flex">
                <div class="pe-2">
                  <div class="rounded d-flex align-items-center justify-content-center" style="width:48px; height:48px; background-color:#fdecec; float:left;">                                  
                    <i class="fa-solid fa-circle-exclamation fa-lg" style="color:#DC2626;"></i>                    
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-center">
                  <h5 style="margin:0px;">Justa Causa</h5>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="texto-suave">Motivada por falta grave do empregado (furto, indisciplina, abandono). Perda de quase todos os direitos.</p>
            </div>
          </div>
          <div class="row mt-2 mb-3">
            <div class="col">
              <p class="texto-negrito texto-suave texto-menor">DIREITOS PRINCIPAIS</p>
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Saldo de Salário</p>              
              <p class="texto-menor"><i class="fa-regular fa-circle-check" style="color:#1E5C7B"></i>&nbsp;Férias Vencidas (se houver)</p>
              <p class="texto-menor texto-suave" style="color:#EF4444"><i class="fa-regular fa-circle-xmark" style="color:#EF4444"></i>&nbsp;Sem 13º e FGTS</p>              
            </div>
          </div>
          <hr>
          <div class="row mt-2 mb-3">
            <div class="col">              
              <span class="texto-menor" style="color:#EF4444">Requer documentação robusta da falta grave cometida.</span>
            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-md-12">
        <h4>Resumo dos Itens da Rescião</h4>
        <span class="texto-suave">Um guia completo para entender os direitos e obrigações no desligamento do empregado doméstico.</span>
      </div>
    </div>
    <div class="row mt-2 mb-2 ps-3 pe-3">
      <div class="col-md-4">
        <div class="row mb-3">
          <div class="col-md-2">
            <i class="fa-regular fa-money-bill-1 fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">Saldo de Salário</span><br>
            <span class="texto-menor texto-suave">Valor referente aos dias trabalhados no mês do desligamento.</span>         
          </div>
        </div>       
        <div class="row mb-3">
          <div class="col-md-2">            
            <i class="fa-regular fa-calendar fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">13º Proporcional</span><br>
            <span class="texto-menor texto-suave">Contagem de meses trabalhados no ano (mínimo 15 dias para contar mês).</span>
          </div>
        </div>       
      </div>
      <div class="col-md-4">
        <div class="row mb-3">
          <div class="col-md-2">            
            <i class="fa-regular fa-calendar-check fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">Aviso Prévio</span><br>
            <span class="texto-menor texto-suave">30 dias base + 3 dias por ano completo de serviço (máximo 90 dias).</span>
          </div>
        </div>       
        <div class="row mb-3">
          <div class="col-md-2">            
            <i class="fa-solid fa-shield-halved fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">Seguro-Desemprego</span><br>
            <span class="texto-menor texto-suave">Habilitação para o benefício governamental em caso de dispensa sem justa causa.</span>
          </div>
        </div>       
      </div>
      <div class="col-md-4">
        <div class="row mb-3">
          <div class="col-md-2">                        
            <i class="fa-solid fa-user-shield fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">FGTS e Indenisação Perda</span><br>
            <span class="texto-menor texto-suave">Movimentação da conta vinculada e multa compensatória (se aplicável).</span>
          </div>
        </div>       
        <div class="row mb-3">
          <div class="col-md-2">                        
            <i class="fa-solid fa-umbrella-beach fa-2x" style="color:#1A5275;"></i>
          </div>
          <div class="col-md-10">
            <span class="texto-negrito">Férias + 1/3 Constitucional</span><br>
            <span class="texto-menor texto-suave">Valor das férias não gozadas somado ao terço constitucional obrigatório.</span>
          </div>
        </div>       
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/rodape.php"); ?>
</body>
</html>