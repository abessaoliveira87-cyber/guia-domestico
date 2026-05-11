<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: usuario_lembrar.php
***** Conteúdo: Lembrar senha de usuário
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
use PHPMailer\PHPMailer\PHPMailer;
Use PHPMailer\PHPMailer\Exception;

// Variáveis de controle
$ok = true;
$html = "";
$acao = "EDITAR";
// EOF Variáveis de controle
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $acao = $_POST["ACAO"];
  if ($acao == "SOLICITAR") {
    $email_usuario = $_POST["EMAIL_USUARIO"];     
    abre_db();
    $strsql = "
    select	
    tusuario.chave_usuario	
    ,tusuario.nome_usuario	
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
      $nome_usuario = $tusuario["nome_usuario"];
      // Envia e-mail de confirmação de alteração do cadastro do usuário
      $pos = strpos($nome_usuario, ' ');
      if ($pos === false) {
      }
      else {
        $nome_usuario = substr($nome_usuario, 0, $pos);
      }	
      $nome_usuario = ucfirst(mb_strtolower($nome_usuario));
      $param1 = password_hash($email_usuario, PASSWORD_DEFAULT);
      $param2 = password_hash(date("Ymdhis"), PASSWORD_DEFAULT);
      $descr_link = "?param1={$param1}&param2={$param2}";
      $strsql = "
      insert
      into
      tlink
      (chave_usuario
      ,descr_link
      ) values
      (:vchave_usuario
      ,:vdescr_link
      )";	 	 	
      $qlink = $pdo->prepare($strsql);
      $qlink->bindParam(":vchave_usuario", $chave_usuario);
      $qlink->bindParam(":vdescr_link", $descr_link);
      $qlink->execute();
      $chave_link = $pdo->lastInsertId();
      fecha_diario("tlink", "chave_link", $chave_link, $abre_diario = array(), $campoexcluidos = array());

      $descr_followup = "Solicitação de redefinição de senha";
      $obs_followup = "?param1={$param1}&param2={$param2}";
      $sit_followup = "AGUARDANDO";
      $strsql = "
      insert
      into
      tfollowup
      (chave_usuario
      ,chave_link
      ,descr_followup
      ,obs_followup
      ,sit_followup
      ) values
      (:vchave_usuario
      ,:vchave_link
      ,:vdescr_followup
      ,:vobs_followup
      ,:vsit_followup
      )";	 	 	
      $qfollowup = $pdo->prepare($strsql);
      $qfollowup->bindParam(":vchave_usuario", $chave_usuario);
      $qfollowup->bindParam(":vchave_link", $chave_link);
      $qfollowup->bindParam(":vdescr_followup", $descr_followup);
      $qfollowup->bindParam(":vobs_followup", $obs_followup);
      $qfollowup->bindParam(":vsit_followup", $sit_followup);
      $qfollowup->execute();
      $chave_followup = $pdo->lastInsertId();
      fecha_diario("tfollowup", "chave_followup", $chave_followup, $abre_diario = array(), $campoexcluidos = array());
      if ($tfollowup = $qfollowup->fetch(PDO::FETCH_ASSOC)) {
        
      }
      $link_email = "https://guiadomestico.com.br/publico/usuario/usuario_lembrar.php?param1={$param1}&param2={$param2}";
      $email_remetente = "contato@guiadomestico.com.br"; 
      $email_destinatario = $email_usuario; 
      $email_reply = "contato@guiadomestico.com.br"; 
      $email_assunto = "Guia Doméstico - Solicitação de senha";
      $email_corpo = "";
      $email_corpo = $email_corpo . "Olá, {$nome_usuario}.<br />";
      $email_corpo = $email_corpo . "<br />";
      $email_corpo = $email_corpo . "Conforme solicitado, segue link para troca de senha.<br />";
      $email_corpo = $email_corpo . "<br />";
      $email_corpo = $email_corpo . "Clique nesse link e redefina sua senha: <a href='{$link_email}' target='_blank'>{$link_email}</a><br />";
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
      $mail->Subject = "Guia Doméstico - Solicitação de senha";
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
          <h1 class="mt-10"><small>Lembrar Senha</small></h1>
        </div>  
      </div>
      <div class="row">
        <div class="col-md-12 mb-3 text-center">
          <span>Por favor, verifique sua caixa de mensagens.</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 mx-auto mb-4" style="max-width:500px">
          <button type="submit" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-custom" onclick="javascript=location=\'/publico/usuario/usuario_login.php\'" style="width:100%">Voltar e fazer login</button>
        </div>
      </div>
      ';
    }
  }
  if ($acao == "TROCAR") {
    $descr_link = $_POST["LNK"];
    $senha_usuario = $_POST["SENHA_USUARIO"];
    $senha2_usuario = $_POST["SENHA2_USUARIO"];

    abre_db();
    $strsql = "
    select	
    tlink.*
    ,tusuario.nome_usuario
    from
    tlink
    left join tusuario on tlink.chave_usuario = tusuario.chave_usuario 
    where 
    tlink.descr_link = :vdescr_link and 
    tlink.caixa_link = 1
    ";	 	 	
    $qlink = $pdo->prepare($strsql);
    $qlink->bindParam(":vdescr_link", $descr_link);
    $qlink->execute();
    if ($tlink = $qlink->fetch(PDO::FETCH_ASSOC)) {
      $chave_usuario = $tlink["chave_usuario"]; 
      $nome_usuario = $tlink["nome_usuario"];
      $chave_link = $tlink["chave_link"];
      $sit_link = $tlink["sit_link"];
      if ($sit_link == "AGUARDANDO") {
        $abre_diario = abre_diario("tusuario", "chave_usuario", $chave_usuario, $campoexcluidos = array());
        $strsql = "
        update 
        tusuario 
        set 
        senha_usuario = :vsenha_usuario 
        where 
        chave_usuario = :vchave_usuario and 
        caixa_usuario = 1
        ";
        $qusuario = $pdo->prepare($strsql);
        $qusuario->bindParam(":vchave_usuario", $chave_usuario);
        $qusuario->bindParam(":vsenha_usuario", $senha_usuario);
        $qusuario->execute();
        fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario = array(), $campoexcluidos = array());
        if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {

        }
        $strsql = "
        update 
        tlink 
        set 
        dtf_link = now()
        ,sit_link = 'CONCLUIDO'
        where 
        chave_link = :vchave_link and 
        chave_usuario = :vchave_usuario and 
        caixa_link = 1
        ";
        $qusuario = $pdo->prepare($strsql);
        $qusuario->bindParam(":vchave_usuario", $chave_usuario);
        $qusuario->bindParam(":vchave_link", $chave_link);
        $qusuario->execute();
        fecha_diario("tusuario", "chave_usuario", $chave_usuario, $abre_diario = array(), $campoexcluidos = array());
        if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {

        }
        header("Location: /publico/usuario/usuario_login.php");




      }
      else {
        echo "Esta solicitação já foi processada.";
        die();
      }
      $param1 = password_hash($email_usuario, PASSWORD_DEFAULT);
      $param2 = password_hash(date("Ymdhis"), PASSWORD_DEFAULT);
      $descr_link = "?param1={$param1}&param2={$param2}";
      $strsql = "
      insert
      into
      tlink
      (chave_usuario
      ,descr_link
      ) values
      (:vchave_usuario
      ,:vdescr_link
      )";	 	 	
      $qlink = $pdo->prepare($strsql);
      $qlink->bindParam(":vchave_usuario", $chave_usuario);
      $qlink->bindParam(":vdescr_link", $descr_link);
      $qlink->execute();
      $chave_link = $pdo->lastInsertId();
      fecha_diario("tlink", "chave_link", $chave_link, $abre_diario = array(), $campoexcluidos = array());

      $descr_followup = "Solicitação de redefinição de senha";
      $obs_followup = "?param1={$param1}&param2={$param2}";
      $sit_followup = "AGUARDANDO";
      $strsql = "
      insert
      into
      tfollowup
      (chave_usuario
      ,chave_link
      ,descr_followup
      ,obs_followup
      ,sit_followup
      ) values
      (:vchave_usuario
      ,:vchave_link
      ,:vdescr_followup
      ,:vobs_followup
      ,:vsit_followup
      )";	 	 	
      $qfollowup = $pdo->prepare($strsql);
      $qfollowup->bindParam(":vchave_usuario", $chave_usuario);
      $qfollowup->bindParam(":vchave_link", $chave_link);
      $qfollowup->bindParam(":vdescr_followup", $descr_followup);
      $qfollowup->bindParam(":vobs_followup", $obs_followup);
      $qfollowup->bindParam(":vsit_followup", $sit_followup);
      $qfollowup->execute();
      $chave_followup = $pdo->lastInsertId();
      fecha_diario("tfollowup", "chave_followup", $chave_followup, $abre_diario = array(), $campoexcluidos = array());
      if ($tfollowup = $qfollowup->fetch(PDO::FETCH_ASSOC)) {
        
      }
      $link_email = "https://guiadomestico.com.br/publico/usuario/usuario_lembrar.php?param1={$param1}&param2={$param2}";
      $email_remetente = "contato@guiadomestico.com.br"; 
      $email_destinatario = $email_usuario; 
      $email_reply = "contato@guiadomestico.com.br"; 
      $email_assunto = "Guia Doméstico - Solicitação de senha";
      $email_corpo = "";
      $email_corpo = $email_corpo . "Olá, {$nome_usuario}.<br />";
      $email_corpo = $email_corpo . "<br />";
      $email_corpo = $email_corpo . "Conforme solicitado, segue link para troca de senha.<br />";
      $email_corpo = $email_corpo . "<br />";
      $email_corpo = $email_corpo . "Clique nesse link e redefina sua senha: <a href='{$link_email}' target='_blank'>{$link_email}</a><br />";
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
      $mail->Subject = "Guia Doméstico - Solicitação de senha";
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
          <h1 class="mt-10"><small>Lembrar Senha</small></h1>
        </div>  
      </div>
      <div class="row">
        <div class="col-md-12 mb-3 text-center">
          <span>Por favor, verifique sua caixa de mensagens.</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 mx-auto mb-4" style="max-width:500px">
          <button type="submit" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-custom" onclick="javascript=location=\'/publico/usuario/usuario_login.php\'" style="width:100%">Voltar e fazer login</button>
        </div>
      </div>
      ';
    }

  }
  fecha_db();
}
if ($_SERVER['REQUEST_METHOD'] == "GET") {
  $email_usuario = "";
  $param1 = "";
  $param2 = "";
  if (isset($_GET['param1'])) {
    $param1 = $_GET['param1'];
  }
  if (isset($_GET['param2'])) {
    $param2 = $_GET['param2'];
  }
  if ($param1 != "" && $param2 != "") {
    $acao = "TROCAR";
    $descr_link = "?param1=" . $param1 . "&param2=" . $param2;
    abre_db();
    $strsql = "
    select	
    tlink.*
    ,tusuario.nome_usuario
    from
    tlink
    left join tusuario on tlink.chave_usuario = tusuario.chave_usuario 
    where 
    tlink.descr_link = :vdescr_link and 
    tlink.caixa_link = 1
    ";	 	 	
    $qlink = $pdo->prepare($strsql);
    $qlink->bindParam(":vdescr_link", $descr_link);
    $qlink->execute();
    if ($tlink = $qlink->fetch(PDO::FETCH_ASSOC)) {
      $chave_usuario = $tlink["chave_usuario"]; 
      $nome_usuario = $tlink["nome_usuario"];
      $sit_link = $tlink["sit_link"];
      if ($sit_link == "AGUARDANDO") {
        $html = '
        <div class="row">
          <div class="col mb-1 text-center">
            <h1 class="mt-10"><small>Trocar Senha</small></h1>
          </div>  
        </div>
        <div class="row">
          <div class="col mx-auto mb-4" style="max-width:500px">
            <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/publico/usuario/usuario_login.php\'"><i class="fa-solid fa-arrow-left"></i> Voltar ao início</button>
          </div>
        </div>
        <div class="row">
          <div class="col mx-auto" style="max-width:500px">
            <div class="card shadow">
              <div class="card-body">
                <form class="needs-validation" method="post" id="FLEMBRARSENHA_POST" name="FLEMBRARSENHA_POST" action="usuario_lembrar.php" novalidate>
                  <input type="hidden" name="LNK" id="LNK" value="?param1=' . $param1 . '&param2=' . $param2 . '" />
                  <input type="hidden" name="ACAO" id="ACAO" value="TROCAR" />
                  <div class="form-group mb-2">
                    <label for="SENHA_USUARIO" class="form-label">Nova senha</label>
                    <input type="password" class="form-control" name="SENHA_USUARIO" id="SENHA_USUARIO" maxlength="20" placeholder="Crie uma senha de 6 dígitos" required />
                  </div>
                  <div class="form-group mb-2">
                    <label for="SENHA2_USUARIO" class="form-label">Confirme a nova senha</label>
                    <input type="password" class="form-control" name="SENHA2_USUARIO" id="SENHA2_USUARIO" maxlength="20" placeholder="Confirme sua senha" required />
                    <span class="texto-suave texto-menor">Sua senha deve conter de 6 a 20 caracteres.</span>                    
                  </div>
                  <div class="form-group mb-4">
                    <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom" style="width:100%">Confirmar troca de senha</button>
                  </div>
                  <script type="text/javascript">
                    document.FLEMBRARSENHA_POST.SENHA_USUARIO.focus();
                  </script>
                </form>
              </div>
            </div>
          </div>         
        </div>';
      }
    }
    $strsql = "
    select	
    tfollowup.*
    from
    tfollowup
    left join tusuario on tfollowup.chave_usuario = tusuario.chave_usuario 
    where 
    tusuario.email_usuario = :vemail_usuario and 	
    tusuario.caixa_usuario = 1
    ";	 	 	
    $qusuario = $pdo->prepare($strsql);
    $qusuario->bindParam(":vemail_usuario", $email_usuario);
    $qusuario->execute();
    if ($tusuario = $qusuario->fetch(PDO::FETCH_ASSOC)) {
      $nome_usuario = $tusuario["nome_usuario"];
    }
  }
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
      <h1 class="mt-10"><small>Lembrar Senha</small></h1>
    </div>  
  </div>
  <div class="row">
    <div class="col mb-3 text-center">
      <span>Por favor, informe o e-mail que utilizou para cadastrar-se.</span>
    </div>
  </div>
  <div class="row">
    <div class="col mx-auto mb-4" style="max-width:500px">
      <button type="button" id="BTN_VOLTAR" name="BTN_VOLTAR" class="btn btn-light" onclick="javascript=location=\'/publico/usuario/usuario_login.php\'"><i class="fa-solid fa-arrow-left"></i>Voltar ao início</button>
    </div>
  </div>
  <div class="row">
    <div class="col mx-auto" style="max-width:500px">
      <div class="card shadow">
        <div class="card-body">
          <form method="post" id="FLEMBRARSENHA_POST" name="FLEMBRARSENHA_POST" action="usuario_lembrar.php">    
            <input type="hidden" name="ACAO" id="ACAO" value="SOLICITAR" />
            <div class="form-group mb-4">
              <label for="EMAIL_USUARIO" class="form-label">E-mail</label>
              <input type="text" class="form-control" id="EMAIL_USUARIO" name="EMAIL_USUARIO" placeholder="Preencha seu e-mail" maxlength="128" value="' . $email_usuario . '">
            </div>
            <div class="form-group mb-4">
              <button type="submit" id="BTN_SUBMETER" name="BTN_SUBMETER" class="btn btn-custom" style="width:100%">Solicitar senha</button>
            </div>
            <script type="text/javascript">
              document.FLEMBRARSENHA_POST.EMAIL_USUARIO.focus();
            </script>
            <div class="form-group mb-4 text-center">
              <span>Não tem conta?&nbsp;</span><a class="link-padrao" href="' . $Raiz . 'publico/usuario/usuario_cadastro.php">Criar cadastro</a>
            </div>
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
<title>Guia Doméstico - Lembrar senha</title>
<?php include($Raiz . "include/html/header.html"); ?>
</head>
<script type="text/javascript">
  function submeter() {
    var vok = true;
    if (document.FLEMBRARSENHA_POST.EMAIL_USUARIO.value.trim() == "") {
      vok = false;
      alert("Nome inválido.");
      document.FLEMBRARSENHA_POST.EMAIL_USUARIO.focus();
    }
    if (vok == true) {
      if (document.FLEMBRARSENHA_POST.SENHA_USUARIO.value.trim() == "") {
        vok = false;
        alert("Senha inválida.");
        document.FLEMBRARSENHA_POST.SENHA_USUARIO.focus();
      }
    }
    if (vok == true) {
      document.FLEMBRARSENHA_POST.submit();
    }
  }

  function avaliatecla() {
    if (window.event && window.event.keyCode == 13) {
      submeter();
    }
  }

  function acesso_tentarnovamente() {
    window.location.href = '/publico/usuario/usuario_login.php';
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