<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/usuario_cadastro.php
***** Conteúdo: Cadastro do usuário
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
include($Raiz . "include/php/header.php"); // Configurações HEADER
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usuário PHP
include($Raiz . "conexao/db.php");
$home = $Raiz . "/index.php";
$ok = true;
$pagina_titulo = "Cadastro do usuário";
//********************
//********************
//**** Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************
$acao = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$acao = "GRAVAR";
}
if ($acao == "GRAVAR") {	
  $chave_usuario = "0";
	$nome_usuario = $_POST["NOME_USUARIO"];
	$email_usuario = $_POST["EMAIL_USUARIO"];
	$senha_usuario = $_POST["SENHA_USUARIO"];
  $sit_usuario = "ATIVO";
  $tipo_usuario = "USUARIO";  	
	abre_db();
  $strsql = "
  select  
  tusuario.chave_usuario
  from
  tusuario
  where 
  tusuario.email_usuario = :vemail_usuario and  
  tusuario.caixa_usuario = 1
  ";
  $qusuario = $pdo->prepare($strsql);
  $qusuario->bindParam(":vemail_usuario", $email_usuario);
  $qusuario->execute();
  if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {
    $chave_usuario = $tusuario["chave_usuario"];
    $acao = "JA_CADASTRADO";
  }
	if ($chave_usuario == "0") {
		// Inclusão do registro	   
		$strsql = "
		insert 
		into 
		tusuario 
		(nome_usuario		
    ,email_usuario
		,senha_usuario
		,tipo_usuario
		,sit_usuario
		) values 
		(:vnome_usuario		
    ,:vemail_usuario
		,:vsenha_usuario
		,:vtipo_usuario
		,:vsit_usuario
		)";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vnome_usuario", $nome_usuario);		
    $qusuario->bindParam(":vemail_usuario", $email_usuario);    
		$qusuario->bindParam(":vsenha_usuario", $senha_usuario);
		$qusuario->bindParam(":vtipo_usuario", $tipo_usuario);
		$qusuario->bindParam(":vsit_usuario", $sit_usuario);
		$qusuario->execute();
		$chave_usuario = $pdo->lastInsertId();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario = array(), $campoexcluidos = array("dta_usuario"));
		// EOF Inclusao do registro	   
  	fecha_db();   	
	  header("Location: " . $home);
	  die();
	}
	else {
/*    
		// Alteção do registro	   
		$abre_diario = abre_diario("tusuario", "chave_usuario", $chave_usuario, $campoexcluidos = array("dta_usuario"));
		$strsql = "
		update 
		tusuario 
		set
		nome_usuario = :vnome_usuario
		,email_usuario = :vemail_usuario
		,senha_usuario = :vsenha_usuario
		,tipo_usuario = :vtipo_usuario
		,sit_usuario = :vsit_usuario
		where 
		chave_usuario = :vchave_usuario
		";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vchave_usuario", $chave_usuario);
		$qusuario->bindParam(":vnome_usuario", $nome_usuario);
		$qusuario->bindParam(":vemail_usuario", $email_usuario);
		$qusuario->bindParam(":vsenha_usuario", $senha_usuario);
		$qusuario->bindParam(":vtipo_usuario", $tipo_usuario);
		$qusuario->bindParam(":vsit_usuario", $sit_usuario);
		$qusuario->execute();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario, $campoexcluidos = array("dta_usuario"));
		// EOF Alteração do registro	   
*/
	}
	// EOF Avalia informação para inclusão
}
//********************
//********************
//**** EOF - Grava registro caso POST chamado contenha diretiva GRAVAR
//********************
//********************

//********************
//********************
//**** Edita registro caso POST chamado contenha diretiva EDITAR
//********************
//********************
if ($acao == "EDITAR") {
	$chave_usuario = "0";
	$nome_usuario = "";
	$email_usuario = "";
	$senha_usuario = "";
	$sit_usuario = "ATIVO";
	$tipo_usuario = "";
	if ($ok) {
		if (!isset($_POST["chave_usuario"])) {
			$ok = false;
		}
	}
	if ($ok) {
		$chave_usuario = $_POST["chave_usuario"];
	}
	if ($ok) {
		if (!is_numeric($chave_usuario)) {
			$ok = false;
		}
	}
	if (!$ok) {
		header("Location: " . $home);
		die();	
	}
	//********************
	//********************
	//**** Pega registro se já esitver cadastrado
	//********************
	//********************
	abre_db();
	$chave_usuario = intval($chave_usuario);
	if ($chave_usuario > 0) {
		$strsql = "
		select 
		tusuario.* 	
		from 
		tusuario 
		where 
		tusuario.chave_usuario = :vchave_usuario";
		$query = $pdo->prepare($strsql);
		$query->bindParam(":vchave_usuario", $chave_usuario);
		$query->execute();
		if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$chave_usuario  = $row["chave_usuario"];
			$nome_usuario = $row["nome_usuario"];
			$email_usuario = $row["email_usuario"];
			$senha_usuario = $row["senha_usuario"];		
			$sit_usuario = $row["sit_usuario"];
			$tipo_usuario = $row["tipo_usuario"];		
		}
	}
	fecha_db();
	//********************
	//********************
	//**** EOF - Pega registro se já esitver cadastrado
	//********************
	//********************

	//********************
	//********************
	//**** Carrega opções do select de tipos de usuários
	//********************
	//********************
	$html_tipo_usuario = "";
	$html_tipo_usuario = $html_tipo_usuario . "<option" . (($tipo_usuario == "USUARIO") ? ' selected="selected"' : "") . ">Usuário</option>";
	$html_tipo_usuario = $html_tipo_usuario . "<option" . (($tipo_usuario == "ADMINISTRADOR") ? ' selected="selected"' : "") . ">Administrador</option>";
	//********************
	//********************
	//**** EOF - Carrega opções do select de tipos de usuários
	//********************
	//********************

	//********************
	//********************
	//**** Carrega opções do select de situações de usuários
	//********************
	//********************
	$html_sit_usuario = "";
	$html_sit_usuario = $html_sit_usuario . "<option" . (($sit_usuario == "ATIVO") ? ' selected="selected"' : "") . ">Ativo</option>";
	$html_sit_usuario = $html_sit_usuario . "<option" . (($sit_usuario == "INATIVO") ? ' selected="selected"' : "") . ">Inativo</option>";
  $html_sit_usuario = $html_sit_usuario . "<option" . (($sit_usuario == "EXCLUIDO") ? ' selected="selected"' : "") . ">Conta excluída</option>";
	//********************
	//********************
	//**** EOF - Carrega opções do select de situações de usuários
	//********************
	//********************

	//********************
	//********************
	//**** Define título da página (e links relacionados)
	//********************
	//********************
	if ($chave_usuario <= 0) {
		$pagina_titulo = "Inclusão do Usuário";
	}
	else {
		$pagina_titulo = "Alteração do Usuário";
	}	
	$html_titulo_form = "";
	$html_titulo_form .= '<div class="row mb-1">' . "\n";
	$html_titulo_form .= '  <div class="col-12">' . "\n";
	$html_titulo_form .= '    <div class="dropdown">' . "\n";
	$html_titulo_form .= '      <span class="Texto-Titulo h5">' . $pagina_titulo  .  '</span>' . "\n";
	$html_titulo_form .= '      <a href="' . $home . '" class="Link-Titulo text-decoration-none" style="padding-left:20px"><span>Voltar</span></a>' . "\n";
	if ($chave_usuario > 0) {	
		$html_titulo_form .= '      <a href="#" class="Link-Titulo text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="padding-left:20px">' . "\n";
		$html_titulo_form .= '        Mais' . "\n";
		$html_titulo_form .= '      </a>' . "\n";
		$html_titulo_form .= '      <ul class="dropdown-menu">' . "\n";
		$html_titulo_form .= '        <li><a class="dropdown-item" href="#">Excluir</a></li>' . "\n";
		$html_titulo_form .= '        <li><hr class="dropdown-divider"></li>' . "\n";
		$html_titulo_form .= '        <li><a class="dropdown-item" href="#" id="SHOW_DIARIO" data-id="' . $chave_usuario . '" data-campo-id="chave_usuario" data-tabela-id="tusuario" data-url-id="' . $Raiz . '" data-bs-toggle="modal" data-bs-target="#MODAL_DIARIO">Diário Registro</a></li>' . "\n";
		$html_titulo_form .= '      </ul>' . "\n";
		$html_titulo_form .= '    </div>' . "\n";
	}
	$html_titulo_form .= '  </div>' . "\n";
	$html_titulo_form .= '</div>' . "\n";
	//********************
	//********************
	//**** EOF - Define título da página (e links relacionados)
	//********************
	//********************
}
//********************
//********************
//**** EOF - Edita registro caso POST chamado contenha diretiva EDITAR
//********************
//********************

if ($acao == "") {
  $html = '
  <div class="card shadow" style="padding-bottom:80px;">
    <div class="card-body">
      <div class="row">
        <div class="col mb-4 text-center">
          <img src="/design/icone.png" class="rounded shadow" style="max-width:64px;" />      
        </div>
      </div>  
      <div class="row">
        <div class="col mb-1 text-center">
          <h1 class="mt-10"><small>Cadastro</small></h1>
        </div>  
      </div>
      <div class="row">
        <div class="col mb-3 text-center">
          <span>Seu guia completo de direitos e deveres</span>
        </div>
      </div>      
      <div class="row">
        <div class="col mx-auto mb-4" style="max-width:500px">
          <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/index.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
        </div>
      </div>
      <div class="row">
        <div class="col mx-auto" style="max-width:500px">
          <div class="card shadow">
            <div class="card-body">
              <form class="needs-validation" method="post" id="FUSUARIO_CADASTRO" name="FUSUARIO_CADASTRO" action="/publico/usuario/usuario_cadastro.php" novalidate>
                <label for="NOME_USUARIO" class="form-label">Nome completo</label>
                <div class="form-group mb-2 has-validation">                    
                  <input type="text" class="form-control" id="NOME_USUARIO" name="NOME_USUARIO" placeholder="Preencha seu nome" maxlength="50" required>
                  <div class="invalid-feedback">
                    Por favor, informe seu nome completo.
                  </div>                    
                </div>
                <div class="form-group mb-2">
                  <label for="EMAIL_USUARIO" class="form-label">E-mail</label>
                  <input type="text" class="form-control" id="EMAIL_USUARIO" name="EMAIL_USUARIO" placeholder="Preencha seu e-mail" maxlength="128" required>
                  <div class="valid-feedback">
                    Por favor, informe seu e-mail.
                  </div>                    
                </div>
                <div class="form-group mb-2">
                  <label for="SENHA_USUARIO" class="form-label">Senha</label>
                  <input type="password" class="form-control" name="SENHA_USUARIO" id="SENHA_USUARIO" maxlength="20" placeholder="Crie uma senha de 6 dígitos" />
                </div>
                <div class="form-group mb-2">
                  <label for="SENHA2_USUARIO" class="form-label">Confirmar senha</label>
                  <input type="password" class="form-control" name="SENHA2_USUARIO" id="SENHA2_USUARIO" maxlength="20" placeholder="Confirme sua senha" />
                  <span class="texto-suave texto-menor">Sua senha deve conter de 6 a 20 caracteres.</span>                    
                </div>
                <div class="form-group mb-4">
                  <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom" style="width:100%">Criar cadastro</button>
                </div>
                <div class="form-group form-check mb-2">
                  <input class="form-check-input" type="checkbox" value="" id="DECLARO_QUE_LI" required>
                  <label for="DECLARO_QUE_LI" class="form-check-label"><small>Declaro que li e concordo com os <a class="link-padrao" href="#" data-bs-toggle="modal" data-bs-target="#termos"">termos de uso</a> da plataforma</small></label>
                  <div class="invalid-feedback">
                    <small>Você deve declarar que leu os termos de uso.</small>
                  </div>
                </div>
                <hr>
                <div class="form-group mb-4 text-center">
                  <span>Já possui uma conta. <a class="link-padrao" href="/publico/usuario/usuario_login.php">Fazer login</a></span>
                </div>
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col mx-auto text-center texto-suave" style="max-width:500px; margin-top:20px">
              <i class="fa-solid fa-certificate"></i></i><span>&nbsp;100% SEGURO</span>
              <span>&nbsp;&nbsp;&nbsp;</span>
              <i class="fa-regular fa-file-lines"></i></i><span>&nbsp;BASEADO NA LEI 150/2015</span>
            </div>
          </div>
        </div>         
      </div>
    </div>
  </div>
  ';
}

if ($acao == "JA_CADASTRADO") {
  $html = '
  <div class="card shadow">    
    <div class="card-body">
      <div class="row">
        <div class="col mb-4 text-center">
          <img src="/design/icone.png" class="rounded shadow" style="max-width:64px;" />      
        </div>
      </div>  
      <div class="row mb-1">
        <div class="col text-center">
          <h1 class="mt-10"><small>Usuário já cadastrado</small></h1>
        </div>  
      </div>
      <div class="row mb-3">
        <div class="col text-center">
          <span>Atenção: já existe um usuário cadastrado com este e-mail.</span>
        </div>
      </div>      
      <div class="row mb-4">
        <div class="col mx-auto" style="max-width:500px">
          <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/index.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
        </div>
      </div>
      <div class="row mb-3 ">
        <div class="col text-center">
          <a href="" class="link-padrao">Fazer login</a>
        </div>
      </div>      
    </div>
  </div>
  ';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Cadastro do usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
function submeter() {
	var vok = true;	
	if (document.FUSUARIO_EDITA.nome_usuario.value == "") {
		vok = false;
		alert("Nome inválido.");
		document.FUSUARIO_EDITA.nome_usuario.focus();
	}
	if (vok) {
		if (document.FUSUARIO_EDITA.email_usuario.value == "") {
			vok = false;
            alert("E-mail inválido.");
            document.FUSUARIO_EDITA.email_usuario.focus();
		}
	}
	if (vok) {
		if (document.FUSUARIO_EDITA.senha_usuario.value == "") {
			vok = false;
            alert("Senha inválida.");
            document.FUSUARIO_EDITA.senha_usuario.focus();
		}   
	}
	if (vok) {
		document.FUSUARIO_EDITA.submit();
	}
};

$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "<?php echo $Raiz ?>";
	}
});
</script>
<body>
<!-- Modal -->
<div class="modal fade" id="termos" tabindex="-1" aria-labelledby="termoslabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="termoslabel">Termos de uso</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="texto-regular-negrito">Declaração de Concordância</p>
        <p class="texto-regular">O usuário reconhece que as informações apresentadas pela plataforma são geradas a partir 
          dos dados inseridos por ele próprio (funcionário doméstico), possuindo caráter meramente 
          informativo e orientador. Não se tratam de dados oficiais como folha de pagamento ou folha 
          de ponto, nem possuem validade comprobatória para fins legais ou judiciais, incluindo 
          ações trabalhistas.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>        
      </div>
    </div>
  </div>
</div>
<?php include($Raiz . "include/php/menu.php"); ?>
<div class="container">
  <?php echo $html ?>
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