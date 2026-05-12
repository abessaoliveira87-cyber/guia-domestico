<?php
/*
**************************************************
**************************************************
***** 
***** APLICAÇÃO
***** Guia Doméstico
***** Arquivo: menu.php
***** Conteúdo: menu principal
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
$RaizMenu = __DIR__;
$home = "/";
$html_btn_entrar = "";
$html_icone_user = "";
$html_aplicativos = "";
$html_auxiliares = "";
$diretorio = "/publico";
if (isset($_SESSION["CHAVE_USUARIO"])) {
	if ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
		$diretorio = "/sistema";
    $home = "/sistema/index.php";
	}
  if ($_SESSION["TIPO_USUARIO"] == "USUARIO") {
    $home = "/publico/usuario/index.php";
  }

}
if (isset($_SESSION["CHAVE_USUARIO"])) {
	if ($_SESSION["CHAVE_USUARIO"] != "") {		    
		if ($_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {      
			$html_btn_entrar .= '          <li class="nav-item dropdown" data-bs-theme="light">' . "\n";
			$html_btn_entrar .= '            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . "\n";
			$html_btn_entrar .= '              ' . $_SESSION["NOME_USUARIO"] . "\n";
			$html_btn_entrar .= '            </a>' . "\n";
			$html_btn_entrar .= '            <ul class="dropdown-menu">' . "\n";
      $html_btn_entrar .= '              <li><a class="dropdown-item" href="/sistema/index.php">Manu Principal Adm</a></li>' . "\n";			
			$html_btn_entrar .= '              <li><hr class="dropdown-divider"></li>' . "\n";
			$html_btn_entrar .= '              <li><a class="dropdown-item" href="/acesso/logout.php">Sair</a></li>' . "\n";
			$html_btn_entrar .= '            </ul>' . "\n";
			$html_btn_entrar .= '          </li>' . "\n";
			$html_aplicativos .= '
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Funcionalidades </a>
    			  <ul class="dropdown-menu" data-bs-theme="light">
              <li>
                <a class="dropdown-item" href="/publico/diagnostico/diagnostico_config.php">Diagnóstico</a>
              </li>
            </ul>
          </li>        
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Aplicativos </a>
    			  <ul class="dropdown-menu" data-bs-theme="light">
              <li>
                <a class="dropdown-item" href="/publico/usuario/index.php">Home Usuário Normal</a>
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/index.php">Home Usuário Administrador</a>
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/usuario/usuario.php">Usuários</a>
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/admin/admin.php">Administradores</a>
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/auxiliares/tabinss/tabinss.php">Tabela de Contribuição do INSS</a>
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/auxiliares/tabirpf/tabirpf.php">Tabela de IRPF</a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="/sistema/cargo/cargo.php">Tabela de Cargos</a>
              </li>
            </ul>
          </li>        
			';
			$html_auxiliares .= '			<li class="nav-item dropdown">' . "\n";
			$html_auxiliares .= '       <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Auxiliares </a>' . "\n";
			$html_auxiliares .= '       <ul class="dropdown-menu" data-bs-theme="light">' . "\n";
			$html_auxiliares .= '         <li>' . "\n";
			$html_auxiliares .= '           <a class="dropdown-item" href="/sistema/auxiliar/var/var_edita.php">Variáveis do Sistema</a>' . "\n";
			$html_auxiliares .= '         </li>' . "\n";			
			$html_auxiliares .= '       </ul>' . "\n";
			$html_auxiliares .= '     </li>' . "\n";
	  }
	  if ($_SESSION["TIPO_USUARIO"] == "USUARIO") {
      $home = "/publico/usuario/index.php";      
      $html_btn_entrar .= '          <li class="nav-item">' . "\n";
      $html_btn_entrar .= '            <a class="nav-link" href="/publico/usuario/index.php">Diagnóstico</a>' . "\n";
      $html_btn_entrar .= '          </li>' . "\n";
      $html_btn_entrar .= '          <li class="nav-item dropdown" data-bs-theme="light">' . "\n";
      $html_btn_entrar .= '            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . "\n";		   
      $html_btn_entrar .= '              ' . $_SESSION["NOME_USUARIO"] . "\n";
      $html_btn_entrar .= '            </a>' . "\n";	
      $html_btn_entrar .= '            <ul class="dropdown-menu">' . "\n";
      $html_btn_entrar .= '              <li><a class="dropdown-item" href="/publico/diagnostico/diagnostico_config.php">Meu Diagnóstico</a></li>' . "\n";
      $html_btn_entrar .= '              <li><a class="dropdown-item" href="/publico/usuario/usuario_config.php">Minha Conta</a></li>' . "\n";      
      $html_btn_entrar .= '              <li><hr class="dropdown-divider"></li>' . "\n";
      $html_btn_entrar .= '              <li><a class="dropdown-item" href="/acesso/logout.php">Sair</a></li>' . "\n";
      $html_btn_entrar .= '            </ul>' . "\n";
      $html_btn_entrar .= '          </li>' . "\n";		   
	  }
  }
}

if ($html_btn_entrar == "") {
	$html_btn_entrar .= "
  <li class='nav-item'>\n
    <button type='button' class='btn btn-sm btn-custom my-2 ml-2 my-sm-0' onclick=\"javascript:location='/publico/usuario/usuario_login.php'\">Entrar</button>\n
  </li>\n";
}
/*
if (isset($_SESSION["TIPO_USUARIO"])) {	
	if ($_SESSION["TIPO_USUARIO"] == "USUARIO" || $_SESSION["TIPO_USUARIO"] == "ADMINISTRADOR") {
		if (isset($BaseDados['usuario'])) {
			$pdo = new PDO($StrConexao, $BaseDados['usuario'], $BaseDados['senha']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
}
*/
?>
<nav class="navbar navbar-expand-lg fixed-top justify-content-end navbar-personalizado" data-bs-theme="light" style="background-color:#FFF;">
  <div class="container">
    <a class="navbar-brand" href="<?php echo $home ?>"><img src="/design/guiadomestico.jpg" class="img-fluid" style="max-width:200px" /></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse flex-grow-0" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?php echo $home; ?>">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/publico/sobre/sobre.php">Sobre</a>
        </li>
        <?php echo $html_aplicativos; ?>
        <?php echo $html_btn_entrar; ?>
        <?php echo ($html_icone_user != "") ? $html_icone_user : ""; ?>
      </ul>
    </div>
  </div>
</nav>