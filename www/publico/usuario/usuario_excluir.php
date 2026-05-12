<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: usuario_excluir.php
***** Conteúdo: Excluir conta do usuário
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
use PHPMailer\PHPMailer\PHPMailer;
Use PHPMailer\PHPMailer\Exception;
// Variáveis de controle
$ok = true;
$html = "";
$acao = "EDITAR";
// EOF Variáveis de controle
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $acao = "GRAVAR";
  $obs_exclusao = $_POST["OBS_EXCLUSAO"];     
  abre_db();
  $chave_usuario = $_SESSION["CHAVE_USUARIO"];
  $strsql = "
  select	
  tusuario.chave_usuario	
  ,tusuario.nome_usuario	
  ,tusuario.email_usuario	
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
    $nome_usuario = $tusuario["nome_usuario"];
    $email_usuario = $tusuario["email_usuario"];
    $pos = strpos($nome_usuario, ' ');
    if ($pos === false) {
    }
    else {
      $nome_usuario = substr($nome_usuario, 0, $pos);
    }	
    $nome_usuario = ucfirst(mb_strtolower($nome_usuario));
    $descr_followup = "Exclusão de conta";
    $obs_followup = "Conta excluída pelo usuário.";
    $sit_followup = "CONCLUIDO";
    $strsql = "
    insert
    into
    tfollowup
    (chave_usuario    
    ,descr_followup
    ,obs_followup
    ,sit_followup
    ) values
    (:vchave_usuario    
    ,:vdescr_followup
    ,:vobs_followup
    ,:vsit_followup
    )";	 	 	
    $qfollowup = $pdo->prepare($strsql);
    $qfollowup->bindParam(":vchave_usuario", $chave_usuario);    
    $qfollowup->bindParam(":vdescr_followup", $descr_followup);
    $qfollowup->bindParam(":vobs_followup", $obs_followup);
    $qfollowup->bindParam(":vsit_followup", $sit_followup);
    $qfollowup->execute();
    $chave_followup = $pdo->lastInsertId();
    fecha_diario("tfollowup", "chave_followup", $chave_followup, $abre_diario = array(), $campoexcluidos = array());
    if ($tfollowup = $qfollowup->fetch(PDO::FETCH_ASSOC)) {
      
    }

    $abre_diario = abre_diario("tusuario", "chave_usuario", $chave_usuario, $campoexcluidos = array());
    $strsql = "
    update    
    tusuario
    set
    caixa_usuario = 0
    where
    chave_usuario = :vchave_usuario and 
    caixa_usuario = 1
    ";	 	 	
    $qusuario = $pdo->prepare($strsql);
    $qusuario->bindParam(":vchave_usuario", $chave_usuario);
    $qusuario->execute();    
    fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario = array(), $campoexcluidos = array());

    $_SESSION["CHAVE_USUARIO"] = "";
    $_SESSION["NOME_USUARIO"] = "";
    $_SESSION["LOGIN_USUARIO"] = "";
    $_SESSION["TIPO_USUARIO"] = "";
    $_SESSION["CHAVE_USUARIOLOGIN"] = "";
    $_SESSION["NOME_USUARIOLOGIN"] = "";
    $_SESSION["LOGIN_USUARIOLOGIN"] = "";
    $_SESSION["SESSAO_INICIO"] = "";
    $_SESSION["SESSAO_EXPIRA"] = 0;
    $_SESSION["CHAVE_CARGO"] = "";
    $_SESSION["DESCR_CARGO"] = "";
    $_SESSION["CBO_CARGO"] = "";
    $_SESSION["SALARIO_USUARIO"] = "";
    $_SESSION["DTI_USUARIO"] = "";
    $_SESSION["HRDIA_USUARIO"] = "";
    $_SESSION["HRDIASAB_USUARIO"] = "";
    $_SESSION["DIASEMANA_USUARIO"] = "";

    $link_email = "https://guiadomestico.com.br";
    $email_remetente = "contato@guiadomestico.com.br"; 
    $email_destinatario = $email_usuario; 
    $email_reply = "contato@guiadomestico.com.br"; 
    $email_assunto = "Guia Doméstico - Exclusão de conta";
    $email_corpo = "";
    $email_corpo = $email_corpo . "Olá, {$nome_usuario}.<br />";
    $email_corpo = $email_corpo . "<br />";
    $email_corpo = $email_corpo . "Conforme solicitado, sua conta foi excluída.<br />";
    $email_corpo = $email_corpo . "<br />";
    $email_corpo = $email_corpo . "Para retornar e utilizar nossos serviços, clique no link e cadastre-se: <a href='{$link_email}' target='_blank'>{$link_email}</a><br />";
    $email_corpo = $email_corpo . "<br />";
    $email_corpo = $email_corpo . "Atenciosamente,<br />"; 
    $email_corpo = $email_corpo . "<br />"; 
    $email_corpo = $email_corpo . "<br />"; 
    $email_corpo = $email_corpo . "<br />"; 
    $email_corpo = $email_corpo . "<img src='cid:Assinatura_Email' alt='Guia Doméstico'><br />";
  
    //Import PHPMailer class into the global namespace
    require $Raiz . 'PHPMailer/src/Exception.php';
    require $Raiz . 'PHPMailer/src/PHPMailer.php';
    require $Raiz . 'PHPMailer/src/SMTP.php';		
    date_default_timezone_set('Etc/UTC');
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isSMTP();
    //$mail->SMTPDebug = 4;
    $mail->Host = "smtpi.uni5.net";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls'; 
    //$mail->SMTPAutoTLS = true;	
    $mail->Username = "contato@guiadomestico.com.br"; // SMTP username
    $mail->Password = 'Senh0*Cont'; // SMTP password   
    $mail->From = "contato@guiadomestico.com.br";
    $mail->FromName = "Guia Doméstico" ; // Nome de quem envia o email
    $mail->AddAddress($email_usuario, $nome_usuario);
    $mail->AddAddress('contato@guiadomestico.com.br', 'Guia Doméstico');
    $mail->addReplyTo('contato@guiadomestico.com.br', 'Guia Doméstico');
    $mail->IsHTML(); // Enviar como HTML
    $mail->AddEmbeddedImage($Raiz . "design/guiadomestico_250_transp.png", "Assinatura_Email", "Assinatura_Email.png");
    $mail->Subject = "Guia Doméstico - Exclusão da conta";
    $mail->Body = $email_corpo;

    $msg = 'Tentando enviar mensagem...';
    if (!$mail->send()) {
      $msg = 'Falha no envio da senha por e-mail.';
    }
    else {
      $msg = 'Senha enviada para e-mail informado.';
    }
    $html = '
    <div class="row">
      <div class="col mb-1 text-center">
        <h1 class="mt-10 text-danger"><small>Excluir Conta</small></h1>
      </div>  
    </div>
    <div class="row">
      <div class="col-md-12 mb-3 text-center">
        <span>Sua conta foi excluida.</span>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12 mx-auto mb-4" style="max-width:500px">
        <button type="submit" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-custom" onclick="javascript=location=\'https://guiadomestico.com.br\'" style="width:100%">Obrigado</button>
      </div>
    </div>
    ';
  }  
  fecha_db();
}
// Corpo
if ($acao == "EDITAR") {  
  $verifica_recaptcha = false;
  if (isset($_SESSION['AMBIENTE'])) {
    if ($_SESSION['AMBIENTE'] === "DESENVOLVIMENTO") {
      $verifica_recaptcha = false;
    }
  }
  $html = '
  <div class="row">
    <div class="col mb-1 text-center">
      <h1 class="mt-10 text-danger"><small>Excluir Conta</small></h1>
    </div>  
  </div>
  <div class="row">
    <div class="col mb-3 text-center">
      <p>Sua conta será excluída assim que confirmar.</p>      
    </div>
  </div>
  <div class="row">
    <div class="col mx-auto mb-4" style="max-width:500px">
      <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/publico/usuario/usuario_config.php\'"><i class="fa-solid fa-arrow-left"></i>Voltar ao início</button>
    </div>
  </div>
  <div class="row">
    <div class="col mx-auto" style="max-width:500px">
      <div class="card shadow">
        <div class="card-body">
          <form method="post" id="FEXCLUIRCONTA_POST" name="FEXCLUIRCONTA_POST" action="usuario_excluir.php">                
            <div class="form-group mb-4">
              <label for="OBS_EXCLUSAO" class="form-label">Informe o motivo que levou a essa decisão</label>              
              <textarea class="form-control" id="OBS_EXCLUSAO" name="OBS_EXCLUSAO" rows="3"></textarea>
            </div>
            <div class="form-group mb-4">
              <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom" style="width:100%">Excluir minha conta</button>
            </div>
            <script type="text/javascript">
              document.FEXCLUIRCONTA_POST.OBS_EXCLUSAO.focus();
            </script>
          </form>
        </div>
      </div>
    </div>         
  </div>';
}
// EOF Corpo
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Guia Doméstico - Excluir conta</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function avaliatecla() {
    if (window.event && window.event.keyCode == 13) {
      submeter();
    }
  }
</script>
<body>
  <script src='https://www.google.com/recaptcha/api.js' async defer></script>
  <?php include($Raiz . "include/php/menu.php"); ?>
  <div class="container">    
    <div class="card shadow">
      <div class="card-body">
        <div class="row">
          <div class="col mb-4 text-center">
            <img src="/design/icone.png" class="rounded shadow" style="max-width:64px;" />      
          </div>
        </div>  
        <?php echo $html; ?>        
        <?php include($Raiz . "include/php/siteseguro.php"); ?>
      </div>
    </div>
    <?php include($Raiz . "include/php/rodape.php"); ?>
    <script type="text/javascript">
      function validaRecaptcha() {
        document.getElementById('BTN_SUBMETER').disabled = false;
      }
    </script>
  </div>
</body>
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
</html>