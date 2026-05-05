<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: /publico/usuario_config.php
***** Conteúdo: Configuração de conta do usuário
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
VerificaSessao();
$home = $Raiz . "/index.php";
$ok = true;
$pagina_titulo = "Configuração da conta do usuário";
$acao = "EDITAR";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_POST["ACAO"])) {
	  $acao = $_POST["ACAO"];
  }
}
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  if (isset($_GET["ACAO"])) {
	  $acao = $_GET["ACAO"];
  }
}
//********************
//********************
//**** Grava registro
//********************
//********************
if ($acao == "GRAVAR") {	
  $chave_usuario = strval($_SESSION["CHAVE_USUARIO"]);  
	$nome_usuario = $_POST["NOME_USUARIO"];
	$email_usuario = $_POST["EMAIL_USUARIO"];	
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
		tusuario.nome_usuario = :vnome_usuario
		,tusuario.email_usuario = :vemail_usuario
		where 
		tusuario.chave_usuario = :vchave_usuario and 
    tusuario.caixa_usuario = 1
		";
		$qusuario = $pdo->prepare($strsql);
		$qusuario->bindParam(":vchave_usuario", $chave_usuario);
		$qusuario->bindParam(":vnome_usuario", $nome_usuario);
		$qusuario->bindParam(":vemail_usuario", $email_usuario);
		$qusuario->execute();
		fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario, $campoexcluidos = array());
		// EOF Alteração do registro	   
	}
	// EOF Avalia informação para inclusão
  header("Location: " . "/");
	die();
}
//********************
//********************
//**** EOF Grava registro
//********************
//********************

//********************
//********************
//**** Edita registro
//********************
//********************
if ($acao == "EDITAR") {
  $chave_usuario = strval($_SESSION["CHAVE_USUARIO"]);
  $nome_usuario = "";
  $email_usuario = "";
  $senha_usuario = "";
  $sit_usuario = "";
  $tipo_usuario = "";
	if ($chave_usuario == "") {
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
	$html_titulo_form .= '  <div class="col-md-12">' . "\n";
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
  $html = '
  <div class="card shadow" style="padding-bottom:80px;">
    <form class="needs-validation" method="post" id="FUSUARIO_CONFIG" name="FUSUARIO_CONFIG" action="/publico/usuario/usuario_config.php" novalidate>
      <input type="hidden" id="ACAO" name="ACAO" value="GRAVAR">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mx-auto" style="max-width:720px">
              <div class="row">
                <div class="col-md-12 mb-1">
                  <h1 class="mt-10"><small>Configuração de Conta</small></h1>
                </div>  
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <span class="texto-corpo">Gerencie suas informações pessoais e segurança</span>
                </div>
              </div>      
              <div class="card shadow">
                <div class="card-header text-bg-light" style="min-height:100px; padding-top:30px;">
                  <h5><i class="fa-regular fa-user"></i>&nbsp;Dados da Conta</h5>
                </div>
                <div class="card-body">
                <label for="NOME_USUARIO" class="form-label">Nome completo</label>
                <div class="form-group mb-2 has-validation">                    
                  <input type="text" class="form-control" id="NOME_USUARIO" name="NOME_USUARIO" placeholder="Preencha seu nome" maxlength="50" value="' . $nome_usuario . '" required>
                  <div class="invalid-feedback">
                    Por favor, informe seu nome completo.
                  </div>                    
                </div>
                <div class="form-group mb-2">
                  <label for="EMAIL_USUARIO" class="form-label">E-mail</label>
                  <input type="text" class="form-control" id="EMAIL_USUARIO" name="EMAIL_USUARIO" placeholder="Preencha seu e-mail" maxlength="128" value="' . $email_usuario . '" required>
                  <div class="invalid-feedback">
                    Por favor, informe seu e-mail.
                  </div>                    
                </div>
                <div class="form-group mb-2 mt-2 ps-2 pe-2" style="min-height:100px; background-color:#fbfcfd;">
                  <div class="row mt-2" style="min-height:100px;">
                    <div class="col-md-7 mt-3">
                      <span class="texto-maior2 texto-negrito"><i class="fa-solid fa-recycle"></i>&nbsp;Alterar senha</span><br>
                      <span class="texto-suave texto-menor">Enviaremos um link de recuperação para seu e-mail.</span>

                    </div>
                    <div class="col-md-5 mt-3 mb-3 text-end">
                      <button type="button" id="BTN_NOVASENHA" name="BTN_NOVASENHA" class="btn btn-custom-prata" onclick="javascript:renovar_senha();"><span class="text-nowrap">Solicitar nova senha</span></button>  
                    </div>
                  </div>                  
                </div>                
                <div class="form-group mb-2 mt-2 ps-2 pe-2" style="min-height:100px;">
                  <div class="row mt-2" style="min-height:100px;">
                    <div class="col-md-8 mt-3"">
                      <span class="texto-maior2 texto-negrito text-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Excluir conta</span><br>
                      <span class="texto-suave texto-menor">Tem certeza que deseja excluir sua conta? Essa ação é irreversível e todos os seus dados serão permanentemente removidos.</span>
                    </div>
                    <div class="col-md-4 text-end" style="min-height:100px; padding-top:40px;">
                        <button type="button" id="BTN_EXCLUIR" name="BTN_EXCLUIR" class="btn btn-outline-danger">Excluir conta</button>
                    </div>
                  </div>                  
                </div>              
              </div>
              <div class="card-footer" style="min-height:100px; padding-top:40px;">
                <div class="row">
                  <div class="col-md-6">
                    <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/index.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>                  
                  </div>
                  <div class="col-md-6 text-end">                  
                    <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom">Salvar alterações</button>                
                  </div>
                </div>
              </div>            
            </div>
          </div>         
        </div>
      </div>
    </form>
  </div>
  ';
}
//********************
//********************
//**** EOF Edita registro
//********************
//********************


//********************
//********************
//**** Renovar Senha / Redefinir Senha
//********************
//********************
if ($acao == "RENOVAR_SENHA") {
  $chave_usuario = strval($_SESSION["CHAVE_USUARIO"]);
  $nome_usuario = "";
  $email_usuario = "";
  $senha_usuario = "";
  $sit_usuario = "";
  $tipo_usuario = "";
	if ($chave_usuario == "") {
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
	$html_titulo_form .= '  <div class="col-md-12">' . "\n";
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
  $html = '
  <div class="card shadow" style="padding-bottom:80px;">
    <form class="needs-validation" method="post" id="FUSUARIO_CONFIG" name="FUSUARIO_CONFIG" action="/publico/usuario/usuario_config.php" novalidate>
      <input type="hidden" id="ACAO" name="ACAO" value="GRAVAR">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mx-auto" style="max-width:720px">
              <div class="row">
                <div class="col-md-12 mb-1">
                  <h1 class="mt-10"><small>Configuração de Conta</small></h1>
                </div>  
              </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                  <span class="texto-corpo">Gerencie suas informações pessoais e segurança</span>
                </div>
              </div>      
              <div class="card shadow">
                <div class="card-header text-bg-light" style="min-height:100px; padding-top:30px;">
                  <h5><i class="fa-regular fa-user"></i>&nbsp;Dados da Conta</h5>
                </div>
                <div class="card-body">
                <label for="NOME_USUARIO" class="form-label">Nome completo</label>
                <div class="form-group mb-2 has-validation">                    
                  <input type="text" class="form-control" id="NOME_USUARIO" name="NOME_USUARIO" placeholder="Preencha seu nome" maxlength="50" value="' . $nome_usuario . '" required>
                  <div class="invalid-feedback">
                    Por favor, informe seu nome completo.
                  </div>                    
                </div>
                <div class="form-group mb-2">
                  <label for="EMAIL_USUARIO" class="form-label">E-mail</label>
                  <input type="text" class="form-control" id="EMAIL_USUARIO" name="EMAIL_USUARIO" placeholder="Preencha seu e-mail" maxlength="128" value="' . $email_usuario . '" required>
                  <div class="invalid-feedback">
                    Por favor, informe seu e-mail.
                  </div>                    
                </div>
                <div class="form-group mb-2 mt-2 ps-2 pe-2" style="min-height:100px; background-color:#fbfcfd;">
                  <div class="row mt-2" style="min-height:100px;">
                    <div class="col-md-7 mt-3">
                      <span class="texto-maior2 texto-negrito"><i class="fa-solid fa-recycle"></i>&nbsp;Alterar senha</span><br>
                      <span class="texto-suave texto-menor">Enviaremos um link de recuperação para seu e-mail.</span>

                    </div>
                    <div class="col-md-5 mt-3 mb-3 text-end">
                      <button type="button" id="BTN_NOVASENHA" name="BTN_NOVASENHA" class="btn btn-custom-prata" onclick="javascript:renovar_senha();"><span class="text-nowrap">Solicitar nova senha</span></button>  
                    </div>
                  </div>                  
                </div>                
                <div class="form-group mb-2 mt-2 ps-2 pe-2" style="min-height:100px;">
                  <div class="row mt-2" style="min-height:100px;">
                    <div class="col-md-8 mt-3"">
                      <span class="texto-maior2 texto-negrito text-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Excluir conta</span><br>
                      <span class="texto-suave texto-menor">Tem certeza que deseja excluir sua conta? Essa ação é irreversível e todos os seus dados serão permanentemente removidos.</span>
                    </div>
                    <div class="col-md-4 text-end" style="min-height:100px; padding-top:40px;">
                        <button type="button" id="BTN_EXCLUIR" name="BTN_EXCLUIR" class="btn btn-outline-danger">Excluir conta</button>
                    </div>
                  </div>                  
                </div>              
              </div>
              <div class="card-footer" style="min-height:100px; padding-top:40px;">
                <div class="row">
                  <div class="col-md-6">
                    <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/index.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>                  
                  </div>
                  <div class="col-md-6 text-end">                  
                    <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom">Salvar alterações</button>                
                  </div>
                </div>
              </div>            
            </div>
          </div>         
        </div>
      </div>
    </form>
  </div>
  ';
}
//********************
//********************
//**** EOF Renovar Senha / Redefinir Senha
//********************
//********************

//********************
//********************
//**** Registro já cadastrado
//********************
//********************
if ($acao == "JA_CADASTRADO") {
  $html = '
  <div class="card shadow">    
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 mb-4 text-center">
          <img src="/design/icone.png" class="rounded shadow" style="max-width:64px;" />      
        </div>
      </div>  
      <div class="row mb-1">
        <div class="col-md-12 text-center">
          <h1 class="mt-10"><small>Usuário já cadastrado</small></h1>
        </div>  
      </div>
      <div class="row mb-3">
        <div class="col-md-12 text-center">
          <span>Atenção: já existe um usuário cadastrado com este e-mail.</span>
        </div>
      </div>      
      <div class="row mb-4">
        <div class="col-md-12 mx-auto" style="max-width:500px">
          <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/index.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
        </div>
      </div>
      <div class="row mb-3 ">
        <div class="col-md-12 text-center">
          <a href="" class="link-padrao">Fazer login</a>
        </div>
      </div>      
    </div>
  </div>
  ';
}
//********************
//********************
//**** EOF Registro já cadastrado
//********************
//********************
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Configuração da conta do usuário</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
$(document).on("keyup", function(event) {
	if (event.key == "Escape") {
		window.location.href = "<?php echo $Raiz ?>";
	}
});

function renovar_senha() {
  document.getElementById("ACAO").value = "RENOVAR_SENHA";
  document.FUSUARIO_CONFIG.submit();
}
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