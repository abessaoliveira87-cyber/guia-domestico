<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-type: text/html; charset=windows-1252');
/*
 **************************************************
 **************************************************
 ***** 
 ***** OR�AMENTO EM PDF
 *****
 **************************************************
 **************************************************
 *****
 ***** DESENVOLVIDO POR:
 *****
 ***** ONTOP SOFTWARE
 ***** Elenir Freitas de Sousa
 ***** Celular: +55 (11) 9-8236-3076
 ***** Website: www.ontop.com.br
 ***** E-mail: elenircombr@gmail.com
 **************************************************
 **************************************************
 */
//********************
//********************
//***** Prepara ambiente
//********************
//********************
//session_start();
function criptohexoct($cstr, $lmodo)
{
  $ccrp = "";
  if ($lmodo) {
    $nadd = 7;
    $cstr = strrev($cstr);
    echo "<br>" . $cstr . "<br>";
    $ntam = strlen($cstr);
    for ($i = 0; $i < $ntam; $i++) {
      $mid = strval(ord($cstr[$i]) + $i + $nadd);
      if ($i % 2 == 0) {
        $mid = padraol(dechex($mid), 3, '0');
      }
      else {
        $mid = padraol(decoct($mid), 3, '0');
      }
      $ccrp = $ccrp . $mid;
    }
  }
  else {
    $nadd = 7;
    $tam = strlen($cstr);
    $index = 0;
    for ($i = 0; $i < $tam; $i += 3) {
      $mid = substr($cstr, $i, 3);
      if ($index % 2 == 0) {
        $mid = substr($mid, 1, 2);
        $mid = hexdec($mid);
      }
      else {
        $mid = octdec($mid);
      }
      $mid = chr(intval($mid) - $index - $nadd);
      $ccrp = $ccrp . $mid;
      $index += 1;
    }
    $ccrp = strrev($ccrp);
  }
  return $ccrp;
}
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
$Raiz = "../../";
include($Raiz . "include/php/funcoes.php"); // Funcoes de Usu�rio PHP
include($Raiz . "conexao/conexao.php");
$ok = true;
$chave_orca = "";
$chave_repr = "";
$drive_fisico = "";
$reg_selec = "";
$origem = "";
$ordem = "";
$ordpos = "";
$marcar = "";
$msgerror = "";
if (isset($_GET['ID'])) {
  $chave_orca = $_GET['ID'];
}
if (isset($_GET['IDR'])) {
  $chave_repr = $_GET['IDR'];
}
if (isset($_GET['REG_SELEC'])) {
  $vreg_selec = $_GET['REG_SELEC'];
}
if (isset($_GET['ORDEM'])) {
  $ordem = $_GET['ORDEM'];
}
if (isset($_GET['ORDPOS'])) {
  $ordpos = $_GET['ORDPOS'];
}
if (isset($_GET['ORIGEM'])) {
  $origem = $_GET['ORIGEM'];
}
if (isset($_GET['MARCAR'])) {
  $marcar = $_GET['MARCAR'];
}
if ($chave_orca == "") {
  $ok = false;
  $msgerror = "Or�amento n�o informado na URL.";
}
if ($chave_repr == "") {
  $ok = false;
  $msgerror = "Representante n�o informado na URL.";
}
if ($ok) {
  if (strlen($chave_repr) > 10) {
    $chave_repr = criptohexoct($chave_repr, false);
  }
  $chave_repr = intval($chave_repr) - 345;
  $chave_repr = str_pad($chave_repr, 6, "0", STR_PAD_LEFT);
  if (strlen($chave_orca) > 10) {
    $chave_orca = criptohexoct($chave_orca, false);
  }
}
if ($ok) {
  if (strlen($reg_selec) > 1) {
    if (substr($reg_selec, strlen($reg_selec) - 1, 1) != ";") {
      $reg_selec .= ";";
    }
    $reg_selec = "'" . $reg_selec;
    $reg_selec = substr($reg_selec, 0, strlen($reg_selec) - 1) . "'";
    $reg_selec = str_replace(";", "','", $reg_selec);
  }
}
if ($ok) {
  if ($ordem == "") {
    $ordem = "TORCAPRO.CHAVE_ORCAPRO";
  }
  if ($ordpos == "") {
    $ordpos = "";
  }
}
if ($ok) {
  $_SESSION["chave_repr"] = $chave_repr;
}
//********************
//********************
//***** EOF - Prepara ambiente 
//********************
//********************
if ($ok) {
  $permitepersonal_reprcfg = false;
  $logotipo_reprcfg = "https://unitycorp.com.br/corp2/design/logotipo.png";
  $drive_fisico = "C:\\SitesHospedados\\portal_representante\\v2\\PDF\\";
  //********************
  //********************
  //**** PRE-SET / VERIFICA��O OR�AMENTO X REPRESENTANTE
  //********************
  //********************
  abre_empresa();
  $lingua_moeda = "PT";
  $abrev_moeda = "BRL";
  $separadormilhar_moeda = ".";
  $separadordecimal_moeda = ",";
  $vlcotacao_orca = 0;
  $vlcotacao2_orca = 0;
  $chave_emp = "";
  $chave_moeda = "";
  $chave_assinadocto = "1";
  $imp_logotipo = true;
  $imp_razao = false;
  $imp_endereco = false;
  $imp_cpfcnpj = false;
  $imp_tel = false;
  $imp_email = false;
  $nomerepr = "";    
  $strsql = "
    SELECT 
    TORCA.CHAVE_MOEDA
    ,TORCA.CHAVE_ASSINADOCTO
    ,TORCA.CHAVE_EMP
    ,TFOR.NOME_FOR AS NOMEREPR
    FROM 
    TORCA 
    LEFT JOIN TFOR ON TORCA.CHAVE_REPR = TFOR.CHAVE_FOR 
    WHERE 
    TORCA.CHAVE_ORCA = :VCHAVE_ORCA AND 
    TORCA.CHAVE_REPR = :VCHAVE_REPR AND 
    CAIXA_ORCA = 'CADASTRADO'
  ";
  $qorca = $pdo_empresa->prepare($strsql);
  $qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorca->bindParam(":VCHAVE_REPR", $chave_repr);
  $qorca->execute();
  if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
    $chave_moeda = $torca["CHAVE_MOEDA"];
    $chave_assinadocto = $torca["CHAVE_ASSINADOCTO"];
    $chave_emp = $torca["CHAVE_EMP"];
    $nomerepr = $torca["NOMEREPR"];
  }
  else {
    $ok = false;
    $msgerror = "Or�amento n�o encontrado ou n�o pertence ao Representante.";
  }
  //********************
  //********************
  //**** EOF PRE-SET / VERIFICA��O OR�AMENTO X REPRESENTANTE
  //********************
  //********************
}
if ($ok) {
  //********************
  //********************
  //**** INFORMA��ES DO REPRESENTANTE   
  //********************
  //********************
  $tipo_tiporepr = "REPRESENTANTE";
  $richtext_reprcfg = 0;
  $strsql = "
    SELECT 
    TFORWEB.CHAVE_TIPOREPR 
    ,TTIPOREPR.TIPO_TIPOREPR
    FROM 
    TFORWEB
    LEFT JOIN TTIPOREPR ON TFORWEB.CHAVE_TIPOREPR = TTIPOREPR.CHAVE_TIPOREPR
    WHERE 
    TFORWEB.CHAVE_FOR = :VCHAVE_REPR AND 
    TFORWEB.CHAVE_WEB = '002' AND 
    TFORWEB.SIT_FORWEB = 'ATIVO' AND 
    TFORWEB.CAIXA_FORWEB = 'CADASTRADO'
  ";
  $qforweb = $pdo_empresa->prepare($strsql);
  $qforweb->bindParam(":VCHAVE_REPR", $chave_repr);
  $qforweb->execute();
  if ($tforweb = $qforweb->fetch(PDO::FETCH_ASSOC)) {
    $tipo_tiporepr = $tforweb["TIPO_TIPOREPR"];
    $strsql = "
      SELECT 
      TREPRCFG.RICHTEXT_REPRCFG 
      FROM 
      TREPRCFG      
      WHERE 
      TREPRCFG.CHAVE_FOR = :VCHAVE_REPR AND 
      TREPRCFG.CAIXA_REPRCFG = 'CADASTRADO'
    ";  
    $qreprcfg = $pdo_empresa->prepare($strsql);
    $qreprcfg->bindParam(":VCHAVE_REPR", $chave_repr);
    $qreprcfg->execute();
    if ($treprcfg = $qreprcfg->fetch(PDO::FETCH_ASSOC)) {
      $richtext_reprcfg = $treprcfg["RICHTEXT_REPRCFG"];
    }
    if ($tipo_tiporepr == "REVENDA") {
      $strsql = "
        SELECT 
        TREPRCFG.LOGOTIPO_REPRCFG
        ,TREPRCFG.PERMITEPERSONAL_REPRCFG 
        FROM 
        TREPRCFG
        WHERE 
        TREPRCFG.CHAVE_FOR = :VCHAVE_REPR AND 
        TREPRCFG.CAIXA_REPRCFG = 'CADASTRADO'
      ";
      $qreprcfg = $pdo_empresa->prepare($strsql);
      $qreprcfg->bindParam(":VCHAVE_REPR", $chave_repr);
      $qreprcfg->execute();
      if ($treprcfg = $qreprcfg->fetch(PDO::FETCH_ASSOC)) {
        $permitepersonal_reprcfg = $treprcfg["PERMITEPERSONAL_REPRCFG"];
        if ($permitepersonal_reprcfg == true) {
          if (trim(strval($treprcfg["LOGOTIPO_REPRCFG"])) != "") {
            $logotipo_reprcfg = $treprcfg["LOGOTIPO_REPRCFG"];
            $logotipo_reprcfg = str_replace("U:\\FOTOS_PRODUTOS\\", "https://unitycorp.com.br/fotos/", $logotipo_reprcfg);
            if (strpos($logotipo_reprcfg, "\\") > 0) {
              $logotipo_reprcfg = str_replace("\\", "/", $logotipo_reprcfg);
            }
          }
        }
      }
    }
  }
  else {
    $ok = false;
    $msgerror = "Representante n�o cadastrado ou configura��o incompleta.";
  }
  //********************
  //********************
  //**** EOF INFORMA��ES DO REPRESENTANTE   
  //********************
  //********************
}
if (!$ok) {
  echo $msgerror;
  die();
}
if ($ok) {
  //********************
  //********************
  //**** INICIO
  //********************
  //********************
  $strsql = "
    SELECT 
    TASSINADOCTO.* 
    FROM 
    TASSINADOCTO 
    WHERE 
    TASSINADOCTO.CHAVE_ASSINADOCTO = :VCHAVE_ASSINADOCTO AND 
    TASSINADOCTO.BASE_ASSINADOCTO = 'ORCAMENTO' AND 
    TASSINADOCTO.CAIXA_ASSINADOCTO = 'CADASTRADO'
  ";
  $qorca = $pdo_empresa->prepare($strsql);
  $qorca->bindParam(":VCHAVE_ASSINADOCTO", $chave_assinadocto);
  $qorca->execute();
  if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
    $imp_logotipo = $torca["LOGOTIPO_ASSINADOCTO"];
    $imp_razao = $torca["RAZAO_ASSINADOCTO"];
    $imp_endereco = $torca["ENDERECO_ASSINADOCTO"];
    $imp_cpfcnpj = $torca["CPFCNPJ_ASSINADOCTO"];
    $imp_tel = $torca["TEL_ASSINADOCTO"];
    $imp_email = $torca["EMAIL_ASSINADOCTO"];
  }
  $qrl_assina_logotipo = "";
  $qrl_assina_razao = "";
  $qrl_assina_end1 = "";
  $qrl_assina_end2 = "";
  $qrl_assina_cpfcnpj = "";
  $qrl_assina_tel = "";
  $qrl_assina_email = "";
  if ($permitepersonal_reprcfg) {
    $strsql = "
      SELECT
      TFOR.NOME_FOR
      ,TFOR.END_FOR
      ,TFOR.NUM_FOR
      ,TFOR.COMPL_FOR
      ,TFOR.BAIRRO_FOR
      ,TFOR.CID_FOR
      ,TFOR.UF_FOR
      ,TFOR.CEP_FOR
      ,TFOR.CPFCNPJ_FOR
      ,TFOR.RGIE_FOR
      ,TFOR.FONEAREA_FOR
      ,TFOR.FONE_FOR
      ,TFOR.SITE_FOR
      ,TFOR.EMAIL_FOR
      FROM 
      TFOR 
      WHERE 
      TFOR.CHAVE_FOR = :VCHAVE_REPR AND
      TFOR.CAIXA_FOR = 'CADASTRADO'
    ";        
    $qfor = $pdo_empresa->prepare($strsql);
    $qfor->bindParam(":VCHAVE_REPR", $chave_repr);
    $qfor->execute();
    if ($tfor = $qfor->fetch(PDO::FETCH_ASSOC)) {
      if ($imp_logotipo) {
      }
      if ($imp_razao) {
        $qrl_assina_razao = mb_convert_encoding($tfor["NOME_FOR"], "windows-1252");
      }
      if ($imp_endereco) {
        $qrl_assina_end1 = mb_convert_encoding(trim($tfor["END_FOR"] . ", " . $tfor["NUM_FOR"] . " " . $tfor["COMPL_FOR"]), "windows-1252");
        $qrl_assina_end2 = mb_convert_encoding($tfor["BAIRRO_FOR"] . " - " . $tfor["CID_FOR"] . " - CEP: " . $tfor["CEP_FOR"], "windows-1252");
      }
      $cpfcnpj_assina = so_numeros($tfor["CPFCNPJ_FOR"], "");
      if ($imp_cpfcnpj) {
        if (strlen($cpfcnpj_assina) > 12) {
          $qrl_assina_cpfcnpj = trim("CNPJ: " . transforma($cpfcnpj_assina, "!!.!!!.!!!/!!!!-!!") . " - Inscr. Estadual: " . $tfor["RGIE_FOR"]);
        }
        else {
          $qrl_assina_cpfcnpj = trim("CPF: " . transforma($cpfcnpj_assina, "!!!.!!!.!!!-!!") . " - RG: " . $tfor["RGIE_FOR"]);
        }
      }
      if ($imp_tel) {
        $qrl_assina_tel = "Tel: " . ($tfor["FONEAREA_FOR"] != "" ? "(" . $tfor["FONEAREA_FOR"] . ") " : "") . $tfor["FONE_FOR"];
      }
      if ($imp_email) {
        if ($tfor["SITE_FOR"] != "") {
          $qrl_assina_email = "Site: " . $tfor["SITE_FOR"];
        }
        if ($tfor["EMAIL_FOR"] != "") {
          if ($qrl_assina_email != "") {
            $qrl_assina_email .= " - ";
          }
          $qrl_assina_email = "E-mail: " . $tfor["EMAIL_FOR"];
        }
      }
    }
  }
  else {
    $strsql = "
      SELECT 
      TEMPRESA.* 
      FROM 
      TEMPRESA 
      WHERE 
      TEMPRESA.CHAVE_EMP = :VCHAVE_EMP AND 
      TEMPRESA.CAIXA_EMP = 'CADASTRADO'
    ";
    $qemp = $pdo_empresa->prepare($strsql);
    $qemp->bindParam(":VCHAVE_EMP", $chave_emp);
    $qemp->execute();
    if ($temp = $qemp->fetch(PDO::FETCH_ASSOC)) {
      if ($imp_logotipo) {
      }
      if ($imp_razao) {
        $qrl_assina_razao = mb_convert_encoding($temp["RAZAO_EMP"], "windows-1252");
      }
      if ($imp_endereco) {
        $qrl_assina_end1 = mb_convert_encoding(trim($temp["END_EMP"] . ", " . $temp["NUM_EMP"] . " " . $temp["COMPL_EMP"]), "windows-1252");
        $qrl_assina_end2 = mb_convert_encoding($temp["BAIRRO_EMP"] . " - " . $temp["CID_EMP"] . " - CEP: " . $temp["CEP_EMP"], "windows-1252");
      }
      if ($imp_cpfcnpj) {
        if (strlen($temp["CPFCNPJ_EMP"]) > 12) {
          $qrl_assina_cpfcnpj = trim("CNPJ: " . transforma($temp["CPFCNPJ_EMP"], "!!.!!!.!!!/!!!!-!!") . " - Inscr. Estadual: " . $temp["RGIE_EMP"]);
        }
        else {
          $qrl_assina_cpfcnpj = trim("CPF: " . transforma($temp["CPFCNPJ_EMP"], "!!!.!!!.!!!-!!") . " - RG: " . $temp["RGIE_EMP"]);
        }
      }
      if ($imp_tel) {
        $qrl_assina_tel = "Tel: " . ($temp["FONEAREA_EMP"] != "" ? "(" . $temp["FONEAREA_EMP"] . ") " : "") . $temp["FONE_EMP"];
      }
      if ($imp_email) {
        if ($temp["SITE_EMP"] != "") {
          $qrl_assina_email = "Site: " . $temp["SITE_EMP"];
        }
        if ($temp["EMAIL_EMP"] != "") {
          if ($qrl_assina_email != "") {
            $qrl_assina_email .= " - ";
          }
          $qrl_assina_email = "E-mail: " . $temp["EMAIL_EMP"];
        }
      }
    }
  }
  if ($chave_moeda != "01" && $chave_moeda != "") {
    $strsql = "
      SELECT 
      TMOEDA.* 
      FROM 
      TMOEDA 
      WHERE 
      TMOEDA.CHAVE_MOEDA = :VCHAVE_MOEDA AND 
      TMOEDA.CAIXA_MOEDA = 'CADASTRADO'
    ";
    $qmoeda = $pdo_empresa->prepare($strsql);
    $qmoeda->bindParam(":VCHAVE_MOEDA", $chave_moeda);
    $qmoeda->execute();
    if ($tmoeda = $qmoeda->fetch(PDO::FETCH_ASSOC)) {
      $lingua_moeda = $tmoeda["LINGUA_MOEDA"];
      $abrev_moeda = $tmoeda["ABREV_MOEDA"];
      $separadormilhar_moeda = $tmoeda["SEPARADORMILHAR_MOEDA"];
      $separadordecimal_moeda = $tmoeda["SEPARADORDECIMAL_MOEDA"];
    }
  }
  $strsql = "
  SELECT 
  TOP 1 
  LIMITLISTAGEMCORP_VAR
  ,PATHFISICOIMGPRO_VAR
  ,PATHWEBIMGPRO_VAR
  ,IPDEDSERVWEB_VAR
  ,HOSTSERVWEB_VAR
  ,OBSFRETE1_VAR
  ,OBSFRETE2_VAR
  ,OBSFRETEEN1_VAR
  ,OBSFRETEES2_VAR
  ,OBSFRETEES1_VAR
  ,OBSFRETEEN2_VAR
  ,ORCACAB_VAR
  ,ORCACABEN_VAR
  ,ORCACABES_VAR
  ,ORCAROD_VAR
  ,ORCARODEN_VAR
  ,ORCARODES_VAR
  FROM 
  TVAR
  ";
  $qvar = $pdo_empresa->prepare($strsql);
  $qvar->execute();
  if ($tvar = $qvar->fetch(PDO::FETCH_ASSOC)) {
    $pathfisicoimgpro_var = $tvar["PATHFISICOIMGPRO_VAR"];
    $pathwebimgpro_var = $tvar["PATHWEBIMGPRO_VAR"];
    $ipdedservweb_var = $tvar["IPDEDSERVWEB_VAR"];
    $hostservweb_var = $tvar["HOSTSERVWEB_VAR"];
    $obsfrete1_var = $tvar["OBSFRETE1_VAR"];
    $obsfrete2_var = $tvar["OBSFRETE2_VAR"];
    $orcacab_var = $tvar["ORCACAB_VAR"];
    $orcarod_var = $tvar["ORCAROD_VAR"];
    if ($lingua_moeda == "EN") {
      $obsfrete1_var = $tvar["OBSFRETEEN1_VAR"];
      $obsfrete2_var = $tvar["OBSFRETEEN2_VAR"];
      $orcacab_var = $tvar["ORCACABEN_VAR"];
      $orcarod_var = $tvar["ORCARODEN_VAR"];
    }
    if ($lingua_moeda == "ES") {
      $obsfrete1_var = $tvar["OBSFRETEES1_VAR"];
      $obsfrete2_var = $tvar["OBSFRETEES2_VAR"];
      $orcacab_var = $tvar["ORCACABES_VAR"];
      $orcarod_var = $tvar["ORCARODES_VAR"];
    }
    if ($ipdedservweb_var != "" && $hostservweb_var != "") {
      $pathwebimgpro_var = str_replace($ipdedservweb_var, "www.unitycorp.com.br", $pathwebimgpro_var);
    }
  }
  //********************
  //********************
  //**** EOF INICIO
  //********************
  //********************
}
$ano = "";
$mes = "";
if ($ok) {
  $strsql = "
    SELECT 
    TORCA.*
    ,TFOR.NOME_FOR
    ,TFOR.EMAIL_FOR
    ,TFOR.CELAREA_FOR
    ,TFOR.CEL_FOR
    ,TFOR.CELWHATSAPP_FOR
    ,TFOR.CELAREA2_FOR
    ,TFOR.CEL2_FOR
    ,TFOR.CELWHATSAPP2_FOR
    ,TFOR.CELAREA3_FOR
    ,TFOR.CEL3_FOR
    ,TFOR.CELWHATSAPP3_FOR
    ,TFOR.CELAREA4_FOR
    ,TFOR.CEL4_FOR
    ,TFOR.CELWHATSAPP4_FOR
    ,TFOR.FONEAREA_FOR
    ,TFOR.FONE_FOR    
    FROM 
    TORCA 
    INNER JOIN TFOR ON TORCA.CHAVE_REPR = TFOR.CHAVE_FOR 
    WHERE 
    TORCA.CHAVE_ORCA = :VCHAVE_ORCA AND 
    TORCA.CHAVE_REPR = :VCHAVE_REPR AND 
    CAIXA_ORCA = 'CADASTRADO'
  ";
  $qorca = $pdo_empresa->prepare($strsql);
  $qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorca->bindParam(":VCHAVE_REPR", $chave_repr);
  $qorca->execute();
  if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
    $chave_ped = $torca["CHAVE_PED"];
    $nome_repr = $torca["NOME_FOR"];
    $email_repr = mb_strtolower($torca["EMAIL_FOR"]);
    $chave_origemorca = $torca["CHAVE_ORIGEMORCA"];
    $fone_repr = "";
    $cel1_repr = "";
    $cel2_repr = "";
    $cel3_repr = "";
    $cel4_repr = "";
    $label_cel1_repr = "Celular:";
    $label_cel2_repr = "Celular:";
    $label_cel3_repr = "Celular:";
    $label_cel4_repr = "Celular:";
    if ($lingua_moeda == "EN") {
      $label_cel1_repr = "Mobile:";
      $label_cel2_repr = "Mobile:";
      $label_cel3_repr = "Mobile:";
      $label_cel4_repr = "Mobile:";
    }
    if ($lingua_moeda == "ES") {
      $label_cel1_repr = "M�vil:";
      $label_cel2_repr = "M�vil:";
      $label_cel3_repr = "M�vil:";
      $label_cel4_repr = "M�vil:";
    }
    if ($torca["CEL_FOR"] != "") {
      $cel1_repr = "(" . $torca["CELAREA_FOR"] . ") " . $torca["CEL_FOR"];
      if ($torca["CELWHATSAPP_FOR"]) {
        $label_cel1_repr = "WhatsApp:";
      }
    }
    if ($torca["CEL2_FOR"] != "") {
      $cel2_repr = "(" . $torca["CELAREA2_FOR"] . ") " . $torca["CEL2_FOR"];
      if ($torca["CELWHATSAPP2_FOR"]) {
        $label_cel2_repr = "WhatsApp:";
      }
    }
    if ($torca["CEL3_FOR"] != "") {
      $cel3_repr = "(" . $torca["CELAREA3_FOR"] . ") " . $torca["CEL3_FOR"];
      if ($torca["CELWHATSAPP3_FOR"]) {
        $label_cel3_repr = "WhatsApp:";
      }
    }
    if ($torca["CEL4_FOR"] != "") {
      $cel4_repr = "(" . $torca["CELAREA4_FOR"] . ") " . $torca["CEL4_FOR"];
      if ($torca["CELWHATSAPP4_FOR"]) {
        $label_cel4_repr = "WhatsApp:";
      }
    }
    if (($cel1_repr . $cel2_repr . $cel3_repr . $cel4_repr) == "") {
      if ($torca["FONE_FOR"] != "") {
        if ($torca["FONEAREA_FOR"] != "") {
          $fone_repr = $fone_repr . so_numeros($torca["FONEAREA_FOR"], '');
        }
        if ($torca["FONE_FOR"] != "") {
          $fone_repr = $fone_repr . so_numeros($torca["FONE_FOR"], '');
        }
        if ($torca["CEL_FOR"] != "") {
          if (strpos($fone_repr, so_numeros($torca["CEL_FOR"]), '') >= 0) {
            $fone_repr = "";
          }
        }
        if ($fone_repr != "") {
          if ($torca["CEL2_FOR"] != "") {
            if (strpos($fone_repr, so_numeros($torca["CEL2_FOR"]), '') >= 0) {
              $fone_repr = "";
            }
          }
        }
        if ($fone_repr != "") {
          if ($torca["CEL3_FOR"] != "") {
            if (strpos($fone_repr, so_numeros($torca["CEL3_FOR"]), '') >= 0) {
              $fone_repr = "";
            }
          }
        }
        if ($fone_repr != "") {
          if ($torca["CEL4_FOR"]) {
            if (strpos($fone_repr, so_numeros($torca["CEL4_FOR"]), '') >= 0) {
              $fone_repr = "";
            }
          }
        }
        if ($fone_repr != "") {
          $fone_repr = "(" . $torca["FONEAREA_FOR"] . ") " . $torca["FONE_FOR"];
        }
      }
    }
    $condpagto_orca = mb_convert_encoding($torca["CONDPAGTO_ORCA"], "windows-1252");    
    $validade_orca = mb_convert_encoding($torca["VALIDADE_ORCA"], "windows-1252");
    $frete_orca = mb_convert_encoding($torca["FRETE_ORCA"], "windows-1252");
    $entrega_orca = mb_convert_encoding($torca["ENTREGA_ORCA"], "windows-1252");
    $orca_frete1 = $torca["VLFRE_ORCA"];
    $orca_frete2 = $torca["VLFRE2_ORCA"];
    $orca_frete3 = $torca["VLFRE3_ORCA"];
    $orca_frete4 = $torca["VLFRE4_ORCA"];
    $orca_frete5 = $torca["VLFRE5_ORCA"];
  }
  $strsql = "
    SELECT 
    TORCA.*
    ,TFOR.NOME_FOR
    ,TFOR.FANTASIA_FOR
    ,TFOR.END_FOR
    ,TFOR.NUM_FOR
    ,TFOR.COMPL_FOR
    ,TFOR.BAIRRO_FOR
    ,TFOR.CID_FOR
    ,TFOR.UF_FOR
    ,TFOR.CEP_FOR
    ,TFOR.FONEAREA_FOR
    ,TFOR.FONE_FOR
    ,TFOR.EMAIL_FOR
    ,TFOR.EMAILCOB_FOR
    ,TFOR.EMAILXML_FOR
    ,TFOR.CPFCNPJ_FOR
    ,TFOR.RGIE_FOR
    ,TFOR.FONE_FOR
    ,TFOR.CONTATO_FOR    
    FROM 
    TORCA 
    INNER JOIN TFOR ON TORCA.CHAVE_FOR = TFOR.CHAVE_FOR 
    WHERE 
    TORCA.CHAVE_ORCA = :VCHAVE_ORCA AND 
    TORCA.CHAVE_REPR = :VCHAVE_REPR AND 
    TORCA.CAIXA_ORCA = 'CADASTRADO'
  ";
  $qorca = $pdo_empresa->prepare($strsql);
  $qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorca->bindParam(":VCHAVE_REPR", $chave_repr);
  $qorca->execute();
  if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
    $chave_ped = $torca["CHAVE_PED"];
    $nome_for = mb_convert_encoding($torca["NOME_FOR"], "windows-1252");
    $fantasia_for = mb_convert_encoding($torca["FANTASIA_FOR"], "windows-1252");
    $end_for = mb_convert_encoding($torca["END_FOR"], "windows-1252");
    $num_for = mb_convert_encoding($torca["NUM_FOR"], "windows-1252");
    $compl_for = mb_convert_encoding($torca["COMPL_FOR"], "windows-1252");
    $bairro_for = mb_convert_encoding($torca["BAIRRO_FOR"], "windows-1252");
    $cid_for = mb_convert_encoding($torca["CID_FOR"], "windows-1252");
    $uf_for = $torca["UF_FOR"];
    $cep_for = $torca["CEP_FOR"];
    $fonearea_for = mb_convert_encoding($torca["FONEAREA_FOR"], "windows-1252");
    $fone_for = mb_convert_encoding($torca["FONE_FOR"], "windows-1252");
    $email_for = mb_strtolower($torca["EMAIL_FOR"]);
    $emailcob_for = $torca["EMAILCOB_FOR"];
    $emailxml_for = $torca["EMAILXML_FOR"];
    $cpfcnpj_for = $torca["CPFCNPJ_FOR"];
    $rgie_for = $torca["RGIE_FOR"];
    $contato_for = mb_convert_encoding($torca["CONTATO_FOR"], "windows-1252");
    $vlcotacao_orca = $torca["VLCOTACAO_ORCA"];
    $vlcotacao2_orca = $torca["VLCOTACAO2_ORCA"];
    $nomecnt_orca = mb_convert_encoding($torca["NOMECNT_ORCA"], "windows-1252");
    $foneareacnt_orca = mb_convert_encoding($torca["FONEAREACNT_ORCA"], "windows-1252");
    $fonecnt_orca = mb_convert_encoding($torca["FONECNT_ORCA"], "windows-1252");
    $emailcnt_orca = mb_strtolower($torca["EMAILCNT_ORCA"]);
    $vlbruto_orca = moeda($torca["VLBRUTO_ORCA"], 2, false);
    $vlbruto2_orca = moeda($torca["VLBRUTO2_ORCA"], 2, false);
    $vlbruto3_orca = moeda($torca["VLBRUTO3_ORCA"], 2, false);
    $vlbruto4_orca = moeda($torca["VLBRUTO4_ORCA"], 2, false);
    $vlbruto5_orca = moeda($torca["VLBRUTO5_ORCA"], 2, false);
    $perdesc_orca = moeda($torca["PERDESC_ORCA"], 4, false) . "%";
    $perdesc2_orca = moeda($torca["PERDESC2_ORCA"], 4, false) . "%";
    $perdesc3_orca = moeda($torca["PERDESC3_ORCA"], 4, false) . "%";
    $perdesc4_orca = moeda($torca["PERDESC4_ORCA"], 4, false) . "%";
    $perdesc5_orca = moeda($torca["PERDESC5_ORCA"], 4, false) . "%";
    $vldesc_orca = moeda($torca["VLDESC_ORCA"], 2, false);
    $vldesc2_orca = moeda($torca["VLDESC2_ORCA"], 2, false);
    $vldesc3_orca = moeda($torca["VLDESC3_ORCA"], 2, false);
    $vldesc4_orca = moeda($torca["VLDESC4_ORCA"], 2, false);
    $vldesc5_orca = moeda($torca["VLDESC5_ORCA"], 2, false);
    $vlliq_orca = moeda($torca["VLLIQ_ORCA"], 2, false);
    $vlliq2_orca = moeda($torca["VLLIQ2_ORCA"], 2, false);
    $vlliq3_orca = moeda($torca["VLLIQ3_ORCA"], 2, false);
    $vlliq4_orca = moeda($torca["VLLIQ4_ORCA"], 2, false);
    $vlliq5_orca = moeda($torca["VLLIQ5_ORCA"], 2, false);
    $vlfre_orca = moeda($torca["VLFRE_ORCA"], 2, true);
    $vlfre2_orca = moeda($torca["VLFRE2_ORCA"], 2, true);
    $vlfre3_orca = moeda($torca["VLFRE3_ORCA"], 2, true);
    $vlfre4_orca = moeda($torca["VLFRE4_ORCA"], 2, true);
    $vlfre5_orca = moeda($torca["VLFRE5_ORCA"], 2, true);
    $vlrec_orca = moeda($torca["VLREC_ORCA"], 2, true);
    $vlrec2_orca = moeda($torca["VLREC2_ORCA"], 2, true);
    $vlrec3_orca = moeda($torca["VLREC3_ORCA"], 2, true);
    $vlrec4_orca = moeda($torca["VLREC4_ORCA"], 2, true);
    $vlrec5_orca = moeda($torca["VLREC5_ORCA"], 2, true);
    $nomeent_orca = mb_convert_encoding(mb_strtoupper($torca["NOMEENT_ORCA"]), "windows-1252");
    $cepentreg_orca = mb_convert_encoding(mb_strtoupper($torca["CEPENTREG_ORCA"]), "windows-1252");
    $cmunentreg_orca = mb_convert_encoding(mb_strtoupper($torca["CODIBGEENTREG_ORCA"]), "windows-1252");
    $endentreg_orca = mb_convert_encoding(mb_strtoupper($torca["ENDENTREG_ORCA"]), "windows-1252");
    $numentreg_orca = mb_convert_encoding(mb_strtoupper($torca["NUMENTREG_ORCA"]), "windows-1252");
    $complentreg_orca = mb_convert_encoding(mb_strtoupper($torca["COMPLENTREG_ORCA"]), "windows-1252");
    $bairroentreg_orca = mb_convert_encoding(mb_strtoupper($torca["BAIRROENTREG_ORCA"]), "windows-1252");
    $cidentreg_orca = mb_convert_encoding(mb_strtoupper($torca["CIDENTREG_ORCA"]), "windows-1252");
    $ufentreg_orca = mb_convert_encoding(mb_strtoupper($torca["UFENTREG_ORCA"]), "windows-1252");
    $foneareaentreg_orca = mb_convert_encoding(mb_strtoupper($torca["FONEAREAENTREG_ORCA"]), "windows-1252");
    $foneentreg_orca = mb_convert_encoding(mb_strtoupper($torca["FONEENTREG_ORCA"]), "windows-1252");
    $cpfcnpjentreg_orca = mb_convert_encoding(mb_strtoupper($torca["CPFCNPJENTREG_ORCA"]), "windows-1252");
    $cepcob_orca = mb_convert_encoding(mb_strtoupper($torca["CEPCOB_ORCA"]), "windows-1252");
    $cmuncob_orca = mb_convert_encoding(mb_strtoupper($torca["CODIBGECOB_ORCA"]), "windows-1252");
    $endcob_orca = mb_convert_encoding(mb_strtoupper($torca["ENDCOB_ORCA"]), "windows-1252");
    $numcob_orca = mb_convert_encoding(mb_strtoupper($torca["NUMCOB_ORCA"]), "windows-1252");
    $complcob_orca = mb_convert_encoding(mb_strtoupper($torca["COMPLCOB_ORCA"]), "windows-1252");
    $bairrocob_orca = mb_convert_encoding(mb_strtoupper($torca["BAIRROCOB_ORCA"]), "windows-1252");
    $cidcob_orca = mb_convert_encoding(mb_strtoupper($torca["CIDCOB_ORCA"]), "windows-1252");
    $ufcob_orca = mb_convert_encoding(mb_strtoupper($torca["UFCOB_ORCA"]), "windows-1252");
    $foneareacob_orca = mb_convert_encoding(mb_strtoupper($torca["FONEAREACOB_ORCA"]), "windows-1252");
    $fonecob_orca = mb_convert_encoding(mb_strtoupper($torca["FONECOB_ORCA"]), "windows-1252");
    $emailcob_orca = mb_convert_encoding(mb_strtolower($torca["EMAILCOB_ORCA"]), "windows-1252");
    $cab_orca = mb_convert_encoding($torca["CAB_ORCA"], "windows-1252");
    $obs_orca = mb_convert_encoding($torca["OBS_ORCA"], "windows-1252");
    $dtc_orca = formatadata($torca["DTC_ORCA"], "d/m/Y");
    $dte_orca = formatadata($torca["DTE_ORCA"], "d/m/Y");
    if ($lingua_moeda == "EN") {
      $dtc_orca = $torca["DTC_ORCA"];
      $dtc_orca = date("d", strtotime($dtc_orca)) . "/" . nomemes_en(date("m", strtotime($dtc_orca))) . "/" . date("Y", strtotime($dtc_orca));
    }
    if ($lingua_moeda == "ES") {
      $dtc_orca = formatadata($torca["DTC_ORCA"], "d/m/Y");
    }
    $ano = $torca["DTC_ORCA"];
    $ano = date("Y", strtotime($ano));
    $mes = $torca["DTC_ORCA"];
    $mes = date("m", strtotime($mes));
    $cc1post_orca = $torca["CC1POST_ORCA"];
    $cc2post_orca = $torca["CC2POST_ORCA"];
    $chave_modeloorcaimp = $torca["CHAVE_MODELOORCAIMP"];
    $imptotal_orca = $torca["IMPTOTAL_ORCA"];
    $freteremovido_orca = $torca["FRETEREMOVIDO_ORCA"];
    $especial_orca = ($torca["ESPECIAL_ORCA"] == true ? "1" : "0");
    if ($torca["TIPOCALCFRETE_ORCA"] == "DISTRIBUIDO") {
      $obscalcfrete = $obsfrete2_var;
    }
    else {
      $obscalcfrete = $obsfrete1_var;
    }
    if (strpos($cc1post_orca, "@promocional.net") >= 0) {
      $cc1post_orca = "";
    }
    if (strpos($cc2post_orca, "@promocional.net") >= 0) {
      $cc2post_orca = "";
    }
    if ($emailcnt_orca == "") {
      $emailcnt_orca = $email_for;
    }
    if ($nomecnt_orca == "") {
      $nomecnt_orca = $contato_for;
    }
    if ($fonecnt_orca == "") {
      if ($fone_for != "") {
        $fonecnt_orca = "(" . $fonearea_for . ") " . $fone_for;
      }
    }
    else {
      $fonecnt_orca = "(" . $foneareacnt_orca . ") " . $fonecnt_orca;
    }
    if (strpos($emailxml_for, ";") >= 0) {
      $emailxml_for = str_replace(";", PHP_EOL, $emailxml_for);
    }
    if (strpos($emailcob_orca, ";") >= 0) {
      $emailcob_orca = str_replace(";", PHP_EOL, $emailcob_orca);
    }
    if ($vlcotacao_orca == "") {
      $vlcotacao_orca = 1;
    }
    if ($vlcotacao2_orca == "") {
      $vlcotacao2_orca = 1;
    }
    $vlcotacao_orca = floatval($vlcotacao_orca);
    $vlcotacao2_orca = floatval($vlcotacao2_orca);
    //********************
    //********************
    //**** APLICA COTACAO DA MOEDA NO TOTAL DO ORCAMENTO
    //********************
    //********************
    if ($lingua_moeda != "01" && $lingua_moeda != "" && $vlcotacao_orca != 1) {
      if ($vlbruto_orca > 0) {
        $vlbruto_orca = moeda($vlbruto_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlbruto2_orca > 0) {
        $vlbruto2_orca = moeda($vlbruto2_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlbruto3_orca > 0) {
        $vlbruto3_orca = moeda($vlbruto3_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlbruto4_orca > 0) {
        $vlbruto4_orca = moeda($vlbruto4_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlbruto5_orca > 0) {
        $vlbruto5_orca = moeda($vlbruto5_orca * $vlcotacao_orca, 2, false);
      }
      if ($vldesc_orca > 0) {
        $vldesc_orca = moeda($vldesc_orca * $vlcotacao_orca, 2, false);
      }
      if ($vldesc2_orca > 0) {
        $vldesc2_orca = moeda($vldesc2_orca * $vlcotacao_orca, 2, false);
      }
      if ($vldesc3_orca > 0) {
        $vldesc3_orca = moeda($vldesc3_orca * $vlcotacao_orca, 2, false);
      }
      if ($vldesc4_orca > 0) {
        $vldesc4_orca = moeda($vldesc4_orca * $vlcotacao_orca, 2, false);
      }
      if ($vldesc5_orca > 0) {
        $vldesc5_orca = moeda($vldesc5_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlliq_orca > 0) {
        $vlliq_orca = moeda($vlliq_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlliq2_orca > 0) {
        $vlliq2_orca = moeda($vlliq2_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlliq3_orca > 0) {
        $vlliq3_orca = moeda($vlliq3_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlliq4_orca > 0) {
        $vlliq4_orca = moeda($vlliq4_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlliq5_orca > 0) {
        $vlliq5_orca = moeda($vliq5_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlfre_orca > 0) {
        $vlfre_orca = moeda($vlfre_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlfre2_orca > 0) {
        $vlfre2_orca = moeda($vlfre2_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlfre3_orca > 0) {
        $vlfre3_orca = moeda($vlfre3_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlfre4_orca > 0) {
        $vlfre4_orca = moeda($vlfre4_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlfre5_orca > 0) {
        $vlfre5_orca = moeda($vlfre5_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlrec_orca > 0) {
        $vlrec_orca = moeda($vlrec_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlrec2_orca > 0) {
        $vlrec2_orca = moeda($vlrec2_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlrec3_orca > 0) {
        $vlrec3_orca = moeda($vlrec3_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlrec4_orca > 0) {
        $vlrec4_orca = moeda($vlrec4_orca * $vlcotacao_orca, 2, false);
      }
      if ($vlrec5_orca > 0) {
        $vlrec5_orca = moeda($vlrec5_orca * $vlcotacao_orca, 2, false);
      }
      $vlbruto_orca = formatalocal($vlbruto_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto2_orca = formatalocal($vlbruto2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto3_orca = formatalocal($vlbruto3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto4_orca = formatalocal($vlbruto4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto5_orca = formatalocal($vlbruto5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc_orca = formatalocal($perdesc_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc2_orca = formatalocal($perdesc2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc3_orca = formatalocal($perdesc3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc4_orca = formatalocal($perdesc4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc5_orca = formatalocal($perdesc5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vldesc_orca = formatalocal($vldesc_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vldesc2_orca = formatalocal($vldesc2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vldesc3_orca = formatalocal($vldesc3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vldesc4_orca = formatalocal($vldesc4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vldesc5_orca = formatalocal($vldesc5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq_orca = formatalocal($vlliq_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq2_orca = formatalocal($vlliq2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq3_orca = formatalocal($vlliq3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq4_orca = formatalocal($vlliq4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq5_orca = formatalocal($vlliq5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfre_orca = formatalocal($vlfre_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfre2_orca = formatalocal($vlfre2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfre3_orca = formatalocal($vlfre3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfre4_orca = formatalocal($vlfre4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfre5_orca = formatalocal($vlfre5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlrec_orca = formatalocal($vlrec_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlrec2_orca = formatalocal($vlrec2_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlrec3_orca = formatalocal($vlrec3_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlrec4_orca = formatalocal($vlrec4_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlrec5_orca = formatalocal($vlrec5_orca, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    }
  }
}
$logotipo = "";
if ($imp_logotipo) {
  if ($logotipo_reprcfg != "") {
    $logotipo = $logotipo_reprcfg;
  }
  else {
    $logotipo = "https://unitycorp.com.br/corp2/design/logotipo.png";
  }
  if (strpos($logotipo, 'unitycorp.com.br') <= 0) {
    $logotipo = "https://unitycorp.com.br/corp2/design/logotipo.png";
  }
}
$qrl_orca_titulo = "Or�amento";
$qrl_orca_pagina = "P�g";
$qrl_orca_data2 = "Data";
$qrl_orca_num = "N�";
$qrl_orca_data = "<font face='Verdana' style='font-size: 8pt; font-weight: 700;'>Cadastro:&nbsp;</font><font face='Verdana' style='font-size: 8pt'>" . $dtc_orca . "</font>";
$qrl_nomeent = "Nome:";
$qrl_end = "Endere�o:";
$qrl_num = "N�mero:";
$qrl_compl = "Complemento:";
$qrl_bairro = "Bairro:";
$qrl_cid = "Cidade:";
$qrl_uf = "UF:";
$qrl_cep = "CEP:";
$qrl_endtel = "Telefone:";
$qrl_emailnfe = "E-mail NF-e:";
$qrl_nome = "Raz�o:";
$qrl_fantasia = "Fantasia:";
$qrl_cliente = "Cliente:";
$qrl_email = "E-mail:";
$qrl_ac = "A/C:";
$qrl_tel = "Telefone:";
$qrl_repr_nome = "Representante:";
$qrl_repr_email = "E-mail:";
$qrl_repr_tel = "Telefone:";
$qrl_endfat = "Endere�o de Faturamento";
$qrl_endcob = "Endere�o de Cobran�a";
$qrl_endent = "Endere�o de Entrega";
$qrl_obs = "Condi��es e Observa��es Gerais";
$qrl_pagto = "Pagamento:";
$qrl_entrega = "Entrega:";
$qrl_frete = "Frete:";
$qrl_validade = "Validade:";
$qrl_pro_cor = "Cor:";
$qrl_pro_opc = "Op��o";
$qrl_pro_opc1 = "1�";
$qrl_pro_opc2 = "2�";
$qrl_pro_opc3 = "3�";
$qrl_pro_opc4 = "4�";
$qrl_pro_opc5 = "5�";
$qrl_pro_qtd = "Qtd";
$qrl_pro_vlu = "R$ Unit�rio";
$qrl_pro_bruto = "R$ Bruto";
$qrl_pro_vlt = "R$ Total";
$qrl_pro_comfrete = "com Frete";
$qrl_pro_frete = "R$ Frete";
$qrl_pro_perdesc = "Desc %";
$qrl_pro_vludesc = "R$ Desc Unit";
$qrl_pro_vluliq = "R$ L�q Unit";
$qrl_orca_titulototal = "Total do Or�amento";
$qrl_orca_bruto = "R$ Bruto";
$qrl_orca_perdesc = "Desc %";
$qrl_orca_vltdesc = "R$ Desconto";
$qrl_orca_subtotal = "R$ Subtotal";
$qrl_orca_frete = "R$ Frete";
$qrl_orca_total = "R$ Total";
$qrl_kit = "Conjunto:";
$qrl_cotacao = "";
$qrl_cotacao2 = "";
if ($lingua_moeda == "EN") {
  $qrl_orca_titulo = "QUOTE";
  $qrl_orca_pagina = "Page";
  $qrl_orca_data2 = "Date";  
  $qrl_orca_num = "Nr";
  $qrl_orca_data = "<font face='Verdana' style='font-size: 8pt; font-weight: 700;'>Date:&nbsp;</font><font face='Verdana' style='font-size: 8pt'>" . $dtc_orca . "</font>";
  $qrl_nomeent = "Name:";
  $qrl_nome = "Customer:";
  $qrl_fantasia = "Trade Name:";
  $qrl_end = "Address:";
  $qrl_num = "Number:";
  $qrl_compl = "Extended:";
  $qrl_bairro = "Neighborhood:";
  $qrl_cid = "City:";
  $qrl_uf = "State:";
  $qrl_cep = "ZIP Code:";
  $qrl_endtel = "Phone Number:";
  $qrl_emailnfe = "Invoice E-mail:";
  $qrl_cliente = "Customer:";
  $qrl_email = "E-mail:";
  $qrl_ac = "c/o:";
  $qrl_tel = "Phone Number:";
  $qrl_repr_nome = "Sales repr:";
  $qrl_repr_email = "E-mail:";
  $qrl_repr_tel = "Phone Number:";
  $qrl_endfat = "Invoice Address";
  $qrl_endcob = "Billing Address";
  $qrl_endent = "Shipping Address";
  $qrl_obs = "Terms and Condictions";
  $qrl_pagto = "Payment terms:";
  $qrl_entrega = "Delivery:";
  $qrl_frete = "Shipping:";
  $qrl_validade = "The estimated price/quote:";
  $qrl_pro_cor = "Color:";
  $qrl_pro_opc = "Option";
  $qrl_pro_opc1 = "1st";
  $qrl_pro_opc2 = "2nd";
  $qrl_pro_opc3 = "3rd";
  $qrl_pro_opc4 = "4th";
  $qrl_pro_opc5 = "5th";
  $qrl_pro_qtd = "Qty";
  $qrl_pro_vlu = $abrev_moeda . " Price";
  $qrl_pro_bruto = $abrev_moeda . " Amount";
  $qrl_pro_vlt = $abrev_moeda . " Total";
  $qrl_pro_comfrete = "with Shipping";
  $qrl_pro_frete = $abrev_moeda . " Shipping";
  $qrl_pro_perdesc = "Disc %";
  $qrl_pro_vludesc = $abrev_moeda . " Unit<br>Discount";
  $qrl_pro_vluliq = $abrev_moeda . " Unit<br>Net Value";
  $qrl_orca_titulototal = "Quote Total";
  $qrl_orca_bruto = $abrev_moeda . " Amount";
  $qrl_orca_perdesc = "Disc %";
  $qrl_orca_vltdesc = $abrev_moeda . " Discount";
  $qrl_orca_subtotal = $abrev_moeda . " Subtotal";
  $qrl_orca_frete = $abrev_moeda . " Shipping";
  $qrl_orca_total = $abrev_moeda . " Total";
  $qrl_kit = "Set:";
  $qrl_cotacao = "Exchange rate:";
  $qrl_cotacao2 = "The dollar exchange rate is valued at BRL " . formatalocal(moeda($vlcotacao2_orca, 4, false), ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO") . " (brazilian reais) per dollar.";
}
if ($lingua_moeda == "ES") {
  $qrl_orca_titulo = "COTIZACI�N";
  $qrl_orca_pagina = "P�g";
  $qrl_orca_data2 = "Fecha";  
  $qrl_orca_num = "Nro";
  $qrl_orca_data = "<font face='Verdana' style='font-size: 8pt; font-weight: 700;'>Fecha:&nbsp;</font><font face='Verdana' style='font-size: 8pt'>" . $dtc_orca . "</font>";
  $qrl_nomeent = "Nombre:";
  $qrl_nome = "Raz�n:";
  $qrl_fantasia = "Fantas�a:";
  $qrl_end = "Direcci�n:";
  $qrl_num = "N�mero:";
  $qrl_compl = "Complemento:";
  $qrl_bairro = "Barrio:";
  $qrl_cid = "Ciudad:";
  $qrl_uf = "Estado:";
  $qrl_cep = "C�digo Postal:";
  $qrl_endtel = "Tel�fono:";
  $qrl_emailnfe = "E-mail Factura:";
  $qrl_cliente = "Para:";
  $qrl_email = "E-mail:";
  $qrl_ac = "A/A:";
  $qrl_tel = "Tel�fono:";
  $qrl_repr_nome = "Asesor:";
  $qrl_repr_email = "E-mail:";
  $qrl_repr_tel = "Tel�fono:";
  $qrl_endfat = "Direcci�n de Factura";
  $qrl_endcob = "Direcci�n de Envio";
  $qrl_endent = "Direcci�n de Entrega";
  $qrl_obs = "Condiciones de Pago";
  $qrl_pagto = "Pago:";
  $qrl_entrega = "Entrega:";
  $qrl_frete = "Env�o:";
  $qrl_validade = "Validad:";
  $qrl_pro_cor = "Color:";
  $qrl_pro_opc = "Opci�n";
  $qrl_pro_opc1 = "1�";
  $qrl_pro_opc2 = "2�";
  $qrl_pro_opc3 = "3�";
  $qrl_pro_opc4 = "4�";
  $qrl_pro_opc5 = "5�";
  $qrl_pro_qtd = "Cantidad";
  $qrl_pro_vlu = $abrev_moeda . " Precio";
  $qrl_pro_bruto = $abrev_moeda . " Monto";
  $qrl_pro_vlt = $abrev_moeda . " Total";
  $qrl_pro_comfrete = "com Env�o";
  $qrl_pro_frete = $abrev_moeda . " Env�o";
  $qrl_pro_perdesc = "Desc %";
  $qrl_pro_vludesc = $abrev_moeda . " Desct<br>Unitario";
  $qrl_pro_vluliq = $abrev_moeda . " Neto<br>Unitario";
  $qrl_orca_titulototal = "Cotizaci�n Total";
  $qrl_orca_bruto = $abrev_moeda . " Monto";
  $qrl_orca_perdesc = "Desc %";
  $qrl_orca_vltdesc = $abrev_moeda . " Descuento";
  $qrl_orca_subtotal = $abrev_moeda . " Subtotal";
  $qrl_orca_frete = $abrev_moeda . " Env�o";
  $qrl_orca_total = $abrev_moeda . " Total";
  $qrl_kit = "Conjunto:";
  $qrl_cotacao = "Tipo de cambio:";
  $qrl_cotacao2 = "El tipo de cambio del d�lar est� valorado en BRL " . formatalocal(moeda($vlcotacao2_orca, 4, false), ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO") . " (reais brasile�os) por d�lar.";
}
require($Raiz . 'fpdf/fpdf.php');
class PDF extends FPDF
{
  // Tem que haver o abre e fecha chaves para n�o dar erro, mesmo que n�o exista Header e/ou Footer.

  //********************
  //********************
  //**** CONFIGURA��O DE HEADER E FOOTER DA PAGINA PDF
  //********************
  //********************   
  // Page header
  function Header()
  {
    global $chave_orca;
    global $dtc_orca;
    global $logotipo;
    global $qrl_orca_titulo;
    global $qrl_orca_pagina;
    global $qrl_orca_data2;
    // Logo
    //$this->Image('https://unitycorp.com.br/corp2/design/logotipo.png',10,6,60);
    $this->Image($logotipo, 10, 6, 60);
    // Arial bold 15
    $this->AddFont('Segoe UI', 'B', 'Segoeuib.php');
    $this->AddFont('Segoe UI', '', 'Segoeui.php');
    $this->SetFont('Segoe UI', 'B', 14);
    $this->Cell(150); // Move to the right
    // Title      
    $this->Cell(30, 0, $qrl_orca_titulo, 0, 0, 'C');
    $this->SetFont('Segoe UI', 'B', 12);
    $this->Ln(6);
    $this->Cell(150);
    $this->Cell(30, 0, 'N� ' . $chave_orca, 0, 0, 'C');
    $this->SetFont('Segoe UI', '', 10);
    $this->Ln(5);
    $this->Cell(128);
    $this->Cell(22, 0, '' . $qrl_orca_pagina . ': ' . $this->PageNo() . '/{nb}', 0, 0, 'L'); // set page number and total number of pages
    $this->Cell(30, 0, $qrl_orca_data2 . ': ' . $dtc_orca, 0, 0, 'C');
    $this->Ln(6); // Line break
  }

  function Footer()
  {
    global $qrl_assina_razao;
    global $qrl_assina_end1;
    global $qrl_assina_end2;
    global $qrl_assina_cpfcnpj;
    global $qrl_assina_email;
    global $qrl_assina_tel;
    $this->SetY(-20); // Position at 30 mm from bottom
    $this->SetFont('Segoe UI', 'B', 10);
    $this->Cell(190, 5, $qrl_assina_razao, 0, 1, 'R');
    $this->SetFont('Segoe UI', '', 8);
    //if ($qrl_assina_end1 != "") {
    //   $this->Cell(190,3.5,$qrl_assina_end1,0,1,'R');         
    //}
    if (($qrl_assina_end2 . $qrl_assina_end2) != "") {
      $this->Cell(190, 3.5, $qrl_assina_end1 . ' - ' . $qrl_assina_end2, 0, 1, 'R');
    }
    if ($qrl_assina_cpfcnpj != "") {
      $this->Cell(190, 3.5, $qrl_assina_cpfcnpj, 0, 1, 'R');
    }
    if ($qrl_assina_email != "") {
      $this->Cell(190, 3.5, $qrl_assina_email, 0, 1, 'R');
    }
    if ($qrl_assina_tel != "") {
      $this->Cell(190, 3.5, $qrl_assina_tel, 0, 1, 'R');
    }
  }
  //********************
  //********************
  //**** EOF CONFIGURA��O DE HEADER E FOOTER DA PAGINA PDF
  //********************
  //********************
}
//********************
//********************
//**** CRIA PASTAS / DIRETORIOS (ANO/MES DENTRO DA PASTA U:\PDF\ORCAMENTO)
//********************
//********************
$arq = $drive_fisico . "ORCAMENTO\\{$ano}\\{$mes}\\ORCA_{$chave_orca}.PDF";
$check_dir = "C:\\SitesHospedados\\portal_representante\\v2\\PDF\\ORCAMENTO\\{$ano}";
if (!file_exists($check_dir)) {
  mkdir($check_dir);
}
$check_dir = "C:\\SitesHospedados\\portal_representante\\v2\\PDF\\ORCAMENTO\\{$ano}\\{$mes}";
if (!file_exists($check_dir)) {
  mkdir($check_dir);
}
//********************
//********************
//**** GERA PDF
//********************
//********************
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(40, 5, $qrl_cliente, 0, 0, 'R');
$pdf->SetFont('Segoe UI', '', 10);
$pdf->Cell(80, 5, $nome_for, 0, 0, 'L');
$pdf->Ln(5);
$pdf->Cell(40, 4.3, $qrl_ac, 0, 0, 'R');
$pdf->Cell(80, 4.3, $nomecnt_orca, 0, 0, 'L');
$pdf->Ln(4.3);
$pdf->Cell(40, 4.3, $qrl_email, 0, 0, 'R');
$pdf->Cell(80, 4.3, $emailcnt_orca, 0, 0, 'L');
$pdf->Ln(4.3);
$pdf->Cell(40, 4.3, $qrl_tel, 0, 0, 'R');
$pdf->Cell(80, 4.3, $fonecnt_orca, 0, 0, 'L');
$pdf->Ln(4);
$pdf->Ln(4);
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(40, 4.3, $qrl_repr_nome, 0, 0, 'R');
$pdf->SetFont('Segoe UI', '', 10);
$pdf->Cell(80, 4.3, $nome_repr, 0, 0, 'L');
$pdf->Ln(4.3);
$pdf->Cell(40, 4.3, $qrl_repr_email, 0, 0, 'R');
$pdf->Cell(80, 4.3, $email_repr, 0, 0, 'L');
$pdf->Ln(4.3);
if ($cel1_repr != "") {
  $pdf->Cell(40, 4.3, $label_cel1_repr, 0, 0, 'R');
  $pdf->Cell(80, 4.3, $cel1_repr, 0, 0, 'L');
  $pdf->Ln(4.3);
}
if ($cel2_repr != "") {
  $pdf->Cell(40, 4.3, $label_cel2_repr, 0, 0, 'R');
  $pdf->Cell(80, 4.3, $cel2_repr, 0, 0, 'L');
  $pdf->Ln(4.3);
}
if ($cel3_repr != "") {
  $pdf->Cell(40, 4.3, $label_cel3_repr, 0, 0, 'R');
  $pdf->Cell(80, 4.3, $cel3_repr, 0, 0, 'L');
  $pdf->Ln(4.3);
}
if ($cel4_repr != "") {
  $pdf->Cell(40, 4.3, $label_cel4_repr, 0, 0, 'R');
  $pdf->Cell(80, 4.3, $cel4_repr, 0, 0, 'L');
  $pdf->Ln(4.3);
}
if ($fone_repr != "") {
  $pdf->Cell(40, 4.3, $qrl_repr_tel, 0, 0, 'R');
  $pdf->Cell(80, 4.3, $fone_repr, 0, 0, 'L');
  $pdf->Ln(4.3);
}
$pdf->Ln(5);
$pdf->MultiCell(0, 4.3, $cab_orca, 0, 'L', false);
$pdf->Ln(3);
//********************
//********************
//**** VERIFICA SE EXISTE KIT NO OR�AMENTO
//********************
//********************
$f_kit = 1;
$strsql = "
SELECT 
TORCAPRO.CHAVE_ORCAKITPRO 
FROM 
TORCAPRO 
INNER JOIN TORCA ON TORCAPRO.CHAVE_ORCA = TORCA.CHAVE_ORCA AND TORCA.CAIXA_ORCA = 'CADASTRADO' 
WHERE 
TORCAPRO.CHAVE_ORCA = :VCHAVE_ORCA  AND 
TORCA.CHAVE_REPR = :VCHAVE_REPR  AND 
TORCAPRO.CHAVE_ORCAKITPRO > 0 AND ";
if (strlen($reg_selec) > 0) {
  $strsql .= "TORCAPRO.CHAVE_ORCAPRO IN (" . $reg_selec . ") AND ";
}
$strsql .= "
TORCAPRO.INATIVO_ORCAPRO = 0 AND 
TORCAPRO.CAIXA_ORCAPRO = 'CADASTRADO'";
$qorca = $pdo_empresa->prepare($strsql);
$qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
$qorca->bindParam(":VCHAVE_REPR", $chave_repr);
$qorca->execute();
if ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
  $f_kit = 2;
}
//********************
//********************
//**** EXISTE KIT / IMPRESSAO KIT
//********************
//********************
$imprime = false;
$contetq = 1;
$contetqpag = 0;
$strsql = "
SELECT 
TORCAPRO.*
,TPRO.NCM_PRO
,TPRO.ICONE_PRO
,TORCA.CHAVE_REPR
,TORCAKITPRO.COD_ORCAKITPRO
,TORCAKITPRO.DESCR_ORCAKITPRO
FROM 
TORCAPRO 
INNER JOIN TORCA ON TORCAPRO.CHAVE_ORCA = TORCA.CHAVE_ORCA 
LEFT JOIN TPRO ON TORCAPRO.CHAVE_PRO = TPRO.CHAVE_PRO 
LEFT JOIN TORCAKITPRO ON TORCAPRO.CHAVE_ORCAKITPRO = TORCAKITPRO.CHAVE_ORCAKITPRO 
WHERE 
TORCAPRO.CHAVE_ORCA = :VCHAVE_ORCA AND 
TORCAPRO.CHAVE_ORCAKITPRO > 0 AND 
TORCAPRO.IMPORTPEDPRO_ORCAPRO = 0 AND 
TORCAPRO.VLT_ORCAPRO > 0 AND 
TORCA.CHAVE_REPR = :VCHAVE_REPR AND ";
if (strlen($reg_selec) > 0) {
  $strsql .= "TORCAPRO.CHAVE_ORCAPRO IN (" . $reg_selec . ") AND ";
}
$strsql .= "
TORCAPRO.INATIVO_ORCAPRO = 0 AND 
TORCAPRO.CAIXA_ORCAPRO = 'CADASTRADO' AND 
TORCAKITPRO.CAIXA_ORCAKITPRO = 'CADASTRADO' AND 
TORCA.CAIXA_ORCA = 'CADASTRADO' 
ORDER BY TORCAKITPRO.COD_ORCAKITPRO, " . $ordem . " " . $ordpos;
$qorca = $pdo_empresa->prepare($strsql);
$qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
$qorca->bindParam(":VCHAVE_REPR", $chave_repr);
$qorca->execute();
$cod_orcakitpro = "";
$reset_kit = false;
$contkit = 1;
$recno = 0;
$y = 0;
$qtd_orcapro = 0;
$qtd2_orcapro = 0;
$qtd3_orcapro = 0;
$qtd4_orcapro = 0;
$qtd5_orcapro = 0;
$achave_orcapro = "";
// produtos do or�amento / next
while ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
  //********************
  //********************
  //**** IMPRIME TOTAL DO KIT
  //********************
  //********************
  if ($cod_orcakitpro != "" && $cod_orcakitpro != $torca["COD_ORCAKITPRO"]) {
    if ($qtd_orcapro > 0) {
      $espacamento += 4;
    }
    if ($qtd2_orcapro > 0) {
      $espacamento += 4;
    }
    if ($qtd3_orcapro > 0) {
      $espacamento += 4;
    }
    if ($qtd4_orcapro > 0) {
      $espacamento += 4;
    }
    if ($qtd5_orcapro > 0) {
      $espacamento += 4;
    }
    if ($chave_moeda != "01" && $chave_moeda != "" && $vlcotacao_orca != 1) {
      $vlu_orcapro = $vlu_orcapro * $vlcotacao_orca;
      $vlu2_orcapro = $vlu2_orcapro * $vlcotacao_orca;
      $vlu3_orcapro = $vlu3_orcapro * $vlcotacao_orca;
      $vlu4_orcapro = $vlu4_orcapro * $vlcotacao_orca;
      $vlu5_orcapro = $vlu5_orcapro * $vlcotacao_orca;
      if ($vltdesc_orcapro > 0) {
        $vludesc_orcapro = round($vltdesc_orcapro / $qtd_orcapro, 4);
      }
      if ($vltdesc2_orcapro > 0) {
        $vludesc2_orcapro = round($vltdesc2_orcapro / $qtd2_orcapro, 4);
      }
      if ($vltdesc3_orcapro > 0) {
        $vludesc3_orcapro = round($vltdesc3_orcapro / $qtd3_orcapro, 4);
      }
      if ($vltdesc4_orcapro > 0) {
        $vludesc4_orcapro = round($vltdesc4_orcapro / $qtd4_orcapro, 4);
      }
      if ($vltdesc5_orcapro > 0) {
        $vludesc5_orcapro = round($vltdesc5_orcapro / $qtd5_orcapro, 4);
      }
      if ($vludesc_orcapro > 0) {
        $vludesc_orcapro = $vludesc_orcapro * $vlcotacao_orca;
      }
      if ($vludesc2_orcapro > 0) {
        $vludesc2_orcapro = $vludesc2_orcapro * $vlcotacao_orca;
      }
      if ($vludesc3_orcapro > 0) {
        $vludesc3_orcapro = $vludesc3_orcapro * $vlcotacao_orca;
      }
      if ($vludesc4_orcapro > 0) {
        $vludesc4_orcapro = $vludesc4_orcapro * $vlcotacao_orca;
      }
      if ($vludesc5_orcapro > 0) {
        $vludesc5_orcapro = $vludesc5_orcapro * $vlcotacao_orca;
      }
      if ($vlbruto_orcapro > 0) {
        $vlbruto_orcapro = $vlbruto_orcapro * $vlcotacao_orca;
      }
      if ($vlbruto2_orcapro > 0) {
        $vlbruto2_orcapro = $vlbruto2_orcapro * $vlcotacao_orca;
      }
      if ($vlbruto3_orcapro > 0) {
        $vlbruto3_orcapro = $vlbruto3_orcapro * $vlcotacao_orca;
      }
      if ($vlbruto4_orcapro > 0) {
        $vlbruto4_orcapro = $vlbruto4_orcapro * $vlcotacao_orca;
      }
      if ($vlbruto5_orcapro > 0) {
        $vlbruto5_orcapro = $vlbruto5_orcapro * $vlcotacao_orca;
      }
      if ($vuliq_orcapro > 0) {
        $vuliq_orcapro = $vuliq_orcapro * $vlcotacao_orca;
      }
      if ($vuliq2_orcapro > 0) {
        $vuliq2_orcapro = $vuliq2_orcapro * $vlcotacao_orca;
      }
      if ($vuliq3_orcapro > 0) {
        $vuliq3_orcapro = $vuliq3_orcapro * $vlcotacao_orca;
      }
      if ($vuliq4_orcapro > 0) {
        $vuliq4_orcapro = $vuliq4_orcapro * $vlcotacao_orca;
      }
      if ($vuliq5_orcapro > 0) {
        $vuliq5_orcapro = $vuliq5_orcapro * $vlcotacao_orca;
      }
      if ($vlliq_orcapro > 0) {
        $vlliq_orcapro = $vlliq_orcapro * $vlcotacao_orca;
      }
      if ($vlliq2_orcapro > 0) {
        $vlliq2_orcapro = $vlliq2_orcapro * $vlcotacao_orca;
      }
      if ($vlliq3_orcapro > 0) {
        $vlliq3_orcapro = $vlliq3_orcapro * $vlcotacao_orca;
      }
      if ($vlliq4_orcapro > 0) {
        $vlliq4_orcapro = $vlliq4_orcapro * $vlcotacao_orca;
      }
      if ($vlliq5_orcapro > 0) {
        $vlliq5_orcapro = $vlliq5_orcapro * $vlcotacao_orca;
      }
      if ($vlfrete_orcapro > 0) {
        $vlfrete_orcapro = $vlfrete_orcapro * $vlcotacao_orca;
      }
      if ($vlfrete2_orcapro > 0) {
        $vlfrete2_orcapro = $vlfrete2_orcapro * $vlcotacao_orca;
      }
      if ($vlfrete3_orcapro > 0) {
        $vlfrete3_orcapro = $vlfrete3_orcapro * $vlcotacao_orca;
      }
      if ($vlfrete4_orcapro > 0) {
        $vlfrete4_orcapro = $vlfrete4_orcapro * $vlcotacao_orca;
      }
      if ($vlfrete5_orcapro > 0) {
        $vlfrete5_orcapro = $vlfrete5_orcapro * $vlcotacao_orca;
      }
      if ($vlt_orcapro > 0) {
        $vlt_orcapro = $vlt_orcapro * $vlcotacao_orca;
      }
      if ($vlt2_orcapro > 0) {
        $vlt2_orcapro = $vlt2_orcapro * $vlcotacao_orca;
      }
      if ($vlt3_orcapro > 0) {
        $vlt3_orcapro = $vlt3_orcapro * $vlcotacao_orca;
      }
      if ($vlt4_orcapro > 0) {
        $vlt4_orcapro = $vlt4_orcapro * $vlcotacao_orca;
      }
      if ($vlt5_orcapro > 0) {
        $vlt5_orcapro = $vlt5_orcapro * $vlcotacao_orca;
      }
/*      
      $qtd_orcapro = moeda($qtd_orcapro, 0, false);
      $qtd2_orcapro = moeda($qtd2_orcapro, 0, false);
      $qtd3_orcapro = moeda($qtd3_orcapro, 0, false);
      $qtd4_orcapro = moeda($qtd4_orcapro, 0, false);
      $qtd5_orcapro = moeda($qtd5_orcapro, 0, false);
      $vlu_orcapro = moeda($vlu_orcapro, 6, false);
      $vlu2_orcapro = moeda($vlu2_orcapro, 6, false);
      $vlu3_orcapro = moeda($vlu3_orcapro, 6, false);
      $vlu4_orcapro = moeda($vlu4_orcapro, 6, false);
      $vlu5_orcapro = moeda($vlu5_orcapro, 6, false);
      $vludesc_orcapro = moeda($vludesc_orcapro, 4, false);
      $vludesc2_orcapro = moeda($vludesc2_orcapro, 4, false);
      $vludesc3_orcapro = moeda($vludesc3_orcapro, 4, false);
      $vludesc4_orcapro = moeda($vludesc4_orcapro, 4, false);
      $vludesc5_orcapro = moeda($vludesc5_orcapro, 4, false);
      $vlbruto_orcapro = moeda($vlbruto_orcapro, 2, false);
      $vlbruto2_orcapro = moeda($vlbruto2_orcapro, 2, false);
      $vlbruto3_orcapro = moeda($vlbruto3_orcapro, 2, false);
      $vlbruto4_orcapro = moeda($vlbruto4_orcapro, 2, false);
      $vlbruto5_orcapro = moeda($vlbruto5_orcapro, 2, false);
      $vuliq_orcapro = moeda($vuliq_orcapro, 4, false);
      $vuliq2_orcapro = moeda($vuliq2_orcapro, 4, false);
      $vuliq3_orcapro = moeda($vuliq3_orcapro, 4, false);
      $vuliq4_orcapro = moeda($vuliq4_orcapro, 4, false);
      $vuliq5_orcapro = moeda($vuliq5_orcapro, 4, false);
      $vlliq_orcapro = moeda($vlliq_orcapro, 2, false);
      $vlliq2_orcapro = moeda($vlliq2_orcapro, 2, false);
      $vlliq3_orcapro = moeda($vlliq3_orcapro, 2, false);
      $vlliq4_orcapro = moeda($vlliq4_orcapro, 2, false);
      $vlliq5_orcapro = moeda($vlliq5_orcapro, 2, false);
      $vlfrete_orcapro = moeda($vlfrete_orcapro, 2, false);
      $vlfrete2_orcapro = moeda($vlfrete2_orcapro, 2, false);
      $vlfrete3_orcapro = moeda($vlfrete3_orcapro, 2, false);
      $vlfrete4_orcapro = moeda($vlfrete4_orcapro, 2, false);
      $vlfrete5_orcapro = moeda($vlfrete5_orcapro, 2, false);
      $vlt_orcapro = moeda($vlt_orcapro, 2, false);
      $vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
      $vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
      $vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
      $vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
*/
      $qtd_orcapro = formatalocal($qtd_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $qtd2_orcapro = formatalocal($qtd2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $qtd3_orcapro = formatalocal($qtd3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $qtd4_orcapro = formatalocal($qtd4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $qtd5_orcapro = formatalocal($qtd5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlu_orcapro = formatalocal($vlu_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlu2_orcapro = formatalocal($vlu2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlu3_orcapro = formatalocal($vlu3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlu4_orcapro = formatalocal($vlu4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlu5_orcapro = formatalocal($vlu5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc_orcapro = formatalocal($perdesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc2_orcapro = formatalocal($perdesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc3_orcapro = formatalocal($perdesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc4_orcapro = formatalocal($perdesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $perdesc5_orcapro = formatalocal($perdesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vludesc_orcapro = formatalocal($vludesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vludesc2_orcapro = formatalocal($vludesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vludesc3_orcapro = formatalocal($vludesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vludesc4_orcapro = formatalocal($vludesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vludesc5_orcapro = formatalocal($vludesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto_orcapro = formatalocal($vlbruto_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto2_orcapro = formatalocal($vlbruto2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto3_orcapro = formatalocal($vlbruto3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto4_orcapro = formatalocal($vlbruto4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlbruto5_orcapro = formatalocal($vlbruto5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vuliq_orcapro = formatalocal($vuliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vuliq2_orcapro = formatalocal($vuliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vuliq3_orcapro = formatalocal($vuliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vuliq4_orcapro = formatalocal($vuliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vuliq5_orcapro = formatalocal($vuliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq_orcapro = formatalocal($vlliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq2_orcapro = formatalocal($vlliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq3_orcapro = formatalocal($vlliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq4_orcapro = formatalocal($vlliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlliq5_orcapro = formatalocal($vlliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfrete_orcapro = formatalocal($vlfrete_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfrete2_orcapro = formatalocal($vlfrete2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfrete3_orcapro = formatalocal($vlfrete3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfrete4_orcapro = formatalocal($vlfrete4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlfrete5_orcapro = formatalocal($vlfrete5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlt_orcapro = formatalocal($vlt_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlt2_orcapro = formatalocal($vlt2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlt3_orcapro = formatalocal($vlt3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlt4_orcapro = formatalocal($vlt4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
      $vlt5_orcapro = formatalocal($vlt5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");           
    }
    else {
      $qtd_orcapro = moeda($qtd_orcapro, 0, false);
      $qtd2_orcapro = moeda($qtd2_orcapro, 0, false);
      $qtd3_orcapro = moeda($qtd3_orcapro, 0, false);
      $qtd4_orcapro = moeda($qtd4_orcapro, 0, false);
      $qtd5_orcapro = moeda($qtd5_orcapro, 0, false);
      $vlu_orcapro = moeda($vlu_orcapro, 2, false);
      $vlu2_orcapro = moeda($vlu2_orcapro, 2, false);
      $vlu3_orcapro = moeda($vlu3_orcapro, 2, false);
      $vlu4_orcapro = moeda($vlu4_orcapro, 2, false);
      $vlu5_orcapro = moeda($vlu5_orcapro, 2, false);
      $vludesc_orcapro = moeda($vludesc_orcapro, 2, false);
      $vludesc2_orcapro = moeda($vludesc2_orcapro, 2, false);
      $vludesc3_orcapro = moeda($vludesc3_orcapro, 2, false);
      $vludesc4_orcapro = moeda($vludesc4_orcapro, 2, false);
      $vludesc5_orcapro = moeda($vludesc5_orcapro, 2, false);
      $vlbruto_orcapro = moeda($vlbruto_orcapro, 2, false);
      $vlbruto2_orcapro = moeda($vlbruto2_orcapro, 2, false);
      $vlbruto3_orcapro = moeda($vlbruto3_orcapro, 2, false);
      $vlbruto4_orcapro = moeda($vlbruto4_orcapro, 2, false);
      $vlbruto5_orcapro = moeda($vlbruto5_orcapro, 2, false);
      $vuliq_orcapro = moeda($vuliq_orcapro, 2, false);
      $vuliq2_orcapro = moeda($vuliq2_orcapro, 2, false);
      $vuliq3_orcapro = moeda($vuliq3_orcapro, 2, false);
      $vuliq4_orcapro = moeda($vuliq4_orcapro, 2, false);
      $vuliq5_orcapro = moeda($vuliq5_orcapro, 2, false);
      $vlliq_orcapro = moeda($vlliq_orcapro, 2, false);
      $vlliq2_orcapro = moeda($vlliq2_orcapro, 2, false);
      $vlliq3_orcapro = moeda($vlliq3_orcapro, 2, false);
      $vlliq4_orcapro = moeda($vlliq4_orcapro, 2, false);
      $vlliq5_orcapro = moeda($vlliq5_orcapro, 2, false);
      $vlfrete_orcapro = moeda($vlfrete_orcapro, 2, false);
      $vlfrete2_orcapro = moeda($vlfrete2_orcapro, 2, false);
      $vlfrete3_orcapro = moeda($vlfrete3_orcapro, 2, false);
      $vlfrete4_orcapro = moeda($vlfrete4_orcapro, 2, false);
      $vlfrete5_orcapro = moeda($vlfrete5_orcapro, 2, false);
      $vlt_orcapro = moeda($vlt_orcapro, 2, false);
      $vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
      $vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
      $vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
      $vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
    }
    $pdf->Ln(0);
    //********************
    //********************
    //**** Unit�rio/Total ou Subtotal/Total
    //********************
    //********************
    if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
      $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
      $pdf->Cell(30, 4, $qrl_pro_vlu, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, $qrl_pro_frete, 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $qrl_pro_vlt, 0, 0, 'R');
      $pdf->Ln(4);
      if ($qtd_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
        $pdf->Cell(30, 4, $vlu_orcapro, 0, 0, 'R');
        if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
          $pdf->Cell(30, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
        }
        $pdf->Cell(30, 4, $vlt_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd2_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
        $pdf->Cell(30, 4, $vlu2_orcapro, 0, 0, 'R');
        if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
          $pdf->Cell(30, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
        }
        $pdf->Cell(30, 4, $vlt2_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd3_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
        $pdf->Cell(30, 4, $vlu3_orcapro, 0, 0, 'R');
        if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
          $pdf->Cell(30, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
        }
        $pdf->Cell(30, 4, $vlt3_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd4_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
        $pdf->Cell(30, 4, $vlu4_orcapro, 0, 0, 'R');
        if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
          $pdf->Cell(30, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
        }
        $pdf->Cell(30, 4, $vlt4_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd5_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
        $pdf->Cell(30, 4, $vlu5_orcapro, 0, 0, 'R');
        if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
          $pdf->Cell(30, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
        }
        $pdf->Cell(30, 4, $vlt5_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
    }
    //********************
    //********************
    //**** EOF Unit�rio/Total ou Subtotal/Total
    //********************
    //********************

    //********************
    //********************
    //**** Unit�rio/Bruto/Total
    //********************
    //********************
    if ($chave_modeloorcaimp == "2") {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
      $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
      $pdf->Cell(20, 4, $qrl_pro_vlu, 0, 0, 'R');
      $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
      $pdf->Cell(20, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
      $pdf->Ln(4);
      if ($qtd_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlu_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd2_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlu2_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd3_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlu3_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd4_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlu4_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd5_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlu5_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
    }
    //********************
    //********************
    //**** EOF Unit�rio/Bruto/Total
    //********************
    //********************

    //********************
    //********************
    //**** Unit�rio/Bruto/Descto/Total
    //********************
    //********************
    if ($chave_modeloorcaimp == "3") {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
      $pdf->Cell(15, 4, $qrl_pro_qtd, 0, 0, 'R');
      $pdf->Cell(16, 4, $qrl_pro_vlu, 0, 0, 'R');
      $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
      $pdf->Cell(15, 4, $qrl_pro_perdesc, 0, 0, 'R');
      $pdf->Cell(18, 4, $qrl_pro_vludesc, 0, 0, 'R');
      $pdf->Cell(16, 4, $qrl_pro_vluliq, 0, 0, 'R');
      $pdf->Cell(18, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
      $pdf->Ln(4);
      if ($qtd_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
        $pdf->Cell(15, 4, $qtd_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vlu_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
        $pdf->Cell(15, 4, $perdesc_orcapro . "%", 0, 0, 'R');
        $pdf->Cell(18, 4, $vludesc_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vuliq_orcapro, 0, 0, 'R');
        $pdf->Cell(18, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd2_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
        $pdf->Cell(15, 4, $qtd2_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vlu2_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
        $pdf->Cell(15, 4, $perdesc2_orcapro . "%", 0, 0, 'R');
        $pdf->Cell(18, 4, $vludesc2_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vuliq2_orcapro, 0, 0, 'R');
        $pdf->Cell(18, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd3_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
        $pdf->Cell(15, 4, $qtd3_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vlu3_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
        $pdf->Cell(15, 4, $perdesc3_orcapro . "%", 0, 0, 'R');
        $pdf->Cell(18, 4, $vludesc3_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vuliq3_orcapro, 0, 0, 'R');
        $pdf->Cell(18, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd4_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
        $pdf->Cell(15, 4, $qtd4_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vlu4_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
        $pdf->Cell(15, 4, $perdesc4_orcapro . "%", 0, 0, 'R');
        $pdf->Cell(18, 4, $vludesc4_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vuliq4_orcapro, 0, 0, 'R');
        $pdf->Cell(18, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd5_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
        $pdf->Cell(15, 4, $qtd5_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vlu5_orcapro, 0, 0, 'R');
        $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
        $pdf->Cell(15, 4, $perdesc5_orcapro . "%", 0, 0, 'R');
        $pdf->Cell(18, 4, $vludesc5_orcapro, 0, 0, 'R');
        $pdf->Cell(16, 4, $vuliq5_orcapro, 0, 0, 'R');
        $pdf->Cell(18, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
        $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
    }
    //********************
    //********************
    //**** EOF Unit�rio/Bruto/Descto/Total
    //********************
    //********************
    if ($chave_modeloorcaimp == "5") {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
      $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
      $pdf->Cell(40, 4, $qrl_pro_vlu . " " . $qrl_pro_comfrete, 1, 0, 'R');
      $pdf->Cell(40, 4, $qrl_pro_vlt . " " . $qrl_pro_comfrete, 0, 0, 'R');
      $pdf->Ln(4);
      if ($qtd_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlu_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlt_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd2_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlu2_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlt2_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd3_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlu3_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlt3_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd4_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlu4_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlt4_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
      if ($qtd5_orcapro > 0) {
        $pdf->Cell(42, 4, "", 0, 0, 'L');
        $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
        $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlu5_orcapro, 0, 0, 'R');
        $pdf->Cell(40, 4, $vlt5_orcapro, 0, 0, 'R');
        $pdf->Ln(4);
      }
    }
    $cod_orcakitpro = "";
    $reset_kit = false;
    $contkit += 1;
    $pdf->Ln(2);
  }
  //********************
  //********************
  //**** EOF IMPRIME TOTAL DO KIT
  //********************
  //********************

  //********************
  //********************
  //**** IMPRIME CORPO DO PRODUTO DO KIT
  //********************
  //********************
  $recno++;
  if ($cod_orcakitpro != $torca["COD_ORCAKITPRO"]) {
    $imprime = true;
    $chave_orcakitpro = $torca["CHAVE_ORCAKITPRO"];
    $qtd_orcapro = 0;
    $qtd2_orcapro = 0;
    $qtd3_orcapro = 0;
    $qtd4_orcapro = 0;
    $qtd5_orcapro = 0;
    $vlu_orcapro = 0;
    $vlu2_orcapro = 0;
    $vlu3_orcapro = 0;
    $vlu4_orcapro = 0;
    $vlu5_orcapro = 0;
    $vlbruto_orcapro = 0;
    $vlbruto2_orcapro = 0;
    $vlbruto3_orcapro = 0;
    $vlbruto4_orcapro = 0;
    $vlbruto5_orcapro = 0;
    $perdesc_orcapro = 0;
    $perdesc2_orcapro = 0;
    $perdesc3_orcapro = 0;
    $perdesc4_orcapro = 0;
    $perdesc5_orcapro = 0;
    $vludesc_orcapro = 0;
    $vludesc2_orcapro = 0;
    $vludesc3_orcapro = 0;
    $vludesc4_orcapro = 0;
    $vludesc5_orcapro = 0;
    $vltdesc_orcapro = 0;
    $vltdesc2_orcapro = 0;
    $vltdesc3_orcapro = 0;
    $vltdesc4_orcapro = 0;
    $vltdesc5_orcapro = 0;
    $vuliq_orcapro = 0;
    $vuliq2_orcapro = 0;
    $vuliq3_orcapro = 0;
    $vuliq4_orcapro = 0;
    $vuliq5_orcapro = 0;
    $vlliq_orcapro = 0;
    $vlliq2_orcapro = 0;
    $vlliq3_orcapro = 0;
    $vlliq4_orcapro = 0;
    $vlliq5_orcapro = 0;
    $vlfrete_orcapro = 0;
    $vlfrete2_orcapro = 0;
    $vlfrete3_orcapro = 0;
    $vlfrete4_orcapro = 0;
    $vlfrete5_orcapro = 0;
    $frete1 = 0;
    $frete2 = 0;
    $frete3 = 0;
    $frete4 = 0;
    $frete5 = 0;
    $vlt_orcapro = 0;
    $vlt2_orcapro = 0;
    $vlt3_orcapro = 0;
    $vlt4_orcapro = 0;
    $vlt5_orcapro = 0;
    $vlucomfrete_orcapro = 0;
    $vlucomfrete2_orcapro = 0;
    $vlucomfrete3_orcapro = 0;
    $vlucomfrete4_orcapro = 0;
    $vlucomfrete5_orcapro = 0;
    $strsql2 = "
    SELECT 
    TORCAKITPRO.* 
    FROM 
    TORCAKITPRO 
    WHERE 
    CHAVE_ORCA = :VCHAVE_ORCA AND 
    CHAVE_ORCAKITPRO = :VCHAVE_ORCAKITPRO AND 
    CAIXA_ORCAKITPRO = 'CADASTRADO'
    ORDER BY CHAVE_ORCAKITPRO";
    $qorcakitpro = $pdo_empresa->prepare($strsql2);
    $qorcakitpro->bindParam(":VCHAVE_ORCA", $chave_orca);
    $qorcakitpro->bindParam(":VCHAVE_ORCAKITPRO", $chave_orcakitpro);
    $qorcakitpro->execute();
    if ($torcakitpro = $qorcakitpro->fetch(PDO::FETCH_ASSOC)) {
      $qtd_orcapro = $torcakitpro["QTD1_ORCAKITPRO"];
      $qtd2_orcapro = $torcakitpro["QTD2_ORCAKITPRO"];
      $qtd3_orcapro = $torcakitpro["QTD3_ORCAKITPRO"];
      $qtd4_orcapro = $torcakitpro["QTD4_ORCAKITPRO"];
      $qtd5_orcapro = $torcakitpro["QTD5_ORCAKITPRO"];
    }
  }
  $cod_orcakitpro = $torca["COD_ORCAKITPRO"];
  $descr_orcakitpro = mb_convert_encoding($torca["DESCR_ORCAKITPRO"], "windows-1252");
  $chave_orcapro = $torca["CHAVE_ORCAPRO"];
  $chave_proimg = $torca["CHAVE_PROIMG"];
  $chave_pro = $torca["CHAVE_PRO"];
  $cod_pro = $torca["COD_PRO"];
  $descr2_pro = mb_convert_encoding(trim($torca["DESCR2_PRO"]), "windows-1252");
  if (strlen($descr2_pro) > 1024) {
    $descr2_pro = substr($descr2_pro, 0, 1024) . "...";
  }
  $config_orcapro = $torca["CONFIG_ORCAPRO"];
  $obs_orcapro = mb_convert_encoding($torca["OBS_ORCAPRO"], "windows-1252");
  $ncm_pro = $torca["NCM_PRO"];
  $especial_orcapro = $torca["ESPECIAL_ORCAPRO"];
  $vlbruto_orcapro = $vlbruto_orcapro + $torca["VLBRUTO_ORCAPRO"];
  $vlbruto2_orcapro = $vlbruto2_orcapro + $torca["VLBRUTO2_ORCAPRO"];
  $vlbruto3_orcapro = $vlbruto3_orcapro + $torca["VLBRUTO3_ORCAPRO"];
  $vlbruto4_orcapro = $vlbruto4_orcapro + $torca["VLBRUTO4_ORCAPRO"];
  $vlbruto5_orcapro = $vlbruto5_orcapro + $torca["VLBRUTO5_ORCAPRO"];
  $vludesc_orcapro = $vludesc_orcapro + $torca["VLUDESC_ORCAPRO"];
  $vludesc2_orcapro = $vludesc2_orcapro + $torca["VLUDESC2_ORCAPRO"];
  $vludesc3_orcapro = $vludesc3_orcapro + $torca["VLUDESC3_ORCAPRO"];
  $vludesc4_orcapro = $vludesc4_orcapro + $torca["VLUDESC4_ORCAPRO"];
  $vludesc5_orcapro = $vludesc5_orcapro + $torca["VLUDESC5_ORCAPRO"];
  $vltdesc_orcapro = $vltdesc_orcapro + $torca["VLTDESC_ORCAPRO"];
  $vltdesc2_orcapro = $vltdesc2_orcapro + $torca["VLTDESC2_ORCAPRO"];
  $vltdesc3_orcapro = $vltdesc3_orcapro + $torca["VLTDESC3_ORCAPRO"];
  $vltdesc4_orcapro = $vltdesc4_orcapro + $torca["VLTDESC4_ORCAPRO"];
  $vltdesc5_orcapro = $vltdesc5_orcapro + $torca["VLTDESC5_ORCAPRO"];
  $vuliq_orcapro = $vuliq_orcapro + $torca["VLUT_ORCAPRO"];
  $vuliq2_orcapro = $vuliq2_orcapro + $torca["VLUT2_ORCAPRO"];
  $vuliq3_orcapro = $vuliq3_orcapro + $torca["VLUT3_ORCAPRO"];
  $vuliq4_orcapro = $vuliq4_orcapro + $torca["VLUT4_ORCAPRO"];
  $vuliq5_orcapro = $vuliq5_orcapro + $torca["VLUT5_ORCAPRO"];
  $vlliq_orcapro = $vlliq_orcapro + $torca["VLLIQ_ORCAPRO"];
  $vlliq2_orcapro = $vlliq2_orcapro + $torca["VLLIQ2_ORCAPRO"];
  $vlliq3_orcapro = $vlliq3_orcapro + $torca["VLLIQ3_ORCAPRO"];
  $vlliq4_orcapro = $vlliq4_orcapro + $torca["VLLIQ4_ORCAPRO"];
  $vlliq5_orcapro = $vlliq5_orcapro + $torca["VLLIQ5_ORCAPRO"];
  $vlfrete_orcapro = $vlfrete_orcapro + $torca["VLFRETE_ORCAPRO"];
  $vlfrete2_orcapro = $vlfrete2_orcapro + $torca["VLFRETE2_ORCAPRO"];
  $vlfrete3_orcapro = $vlfrete3_orcapro + $torca["VLFRETE3_ORCAPRO"];
  $vlfrete4_orcapro = $vlfrete4_orcapro + $torca["VLFRETE4_ORCAPRO"];
  $vlfrete5_orcapro = $vlfrete5_orcapro + $torca["VLFRETE5_ORCAPRO"];
  $frete1 = $frete1 + $torca["VLFRETE_ORCAPRO"];
  $frete2 = $frete2 + $torca["VLFRETE2_ORCAPRO"];
  $frete3 = $frete3 + $torca["VLFRETE3_ORCAPRO"];
  $frete4 = $frete4 + $torca["VLFRETE4_ORCAPRO"];
  $frete5 = $frete5 + $torca["VLFRETE5_ORCAPRO"];
  $vlt_orcapro = $vlt_orcapro + $torca["VLT_ORCAPRO"];
  $vlt2_orcapro = $vlt2_orcapro + $torca["VLT2_ORCAPRO"];
  $vlt3_orcapro = $vlt3_orcapro + $torca["VLT3_ORCAPRO"];
  $vlt4_orcapro = $vlt4_orcapro + $torca["VLT4_ORCAPRO"];
  $vlt5_orcapro = $vlt5_orcapro + $torca["VLT5_ORCAPRO"];
  $vuliq_orcapro = 0;
  $vuliq2_orcapro = 0;
  $vuliq3_orcapro = 0;
  $vuliq4_orcapro = 0;
  $vuliq5_orcapro = 0;
  if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
    $vlu_orcapro = $vlu_orcapro + $torca["VLU_ORCAPRO"] + $torca["VLUOVER_ORCAPRO"] + $torca["VLUBV_ORCAPRO"] - $torca["VLUDESC_ORCAPRO"];
    $vlu2_orcapro = $vlu2_orcapro + $torca["VLU2_ORCAPRO"] + $torca["VLUOVER2_ORCAPRO"] + $torca["VLUBV2_ORCAPRO"] - $torca["VLUDESC2_ORCAPRO"];
    $vlu3_orcapro = $vlu3_orcapro + $torca["VLU3_ORCAPRO"] + $torca["VLUOVER3_ORCAPRO"] + $torca["VLUBV3_ORCAPRO"] - $torca["VLUDESC3_ORCAPRO"];
    $vlu4_orcapro = $vlu4_orcapro + $torca["VLU4_ORCAPRO"] + $torca["VLUOVER4_ORCAPRO"] + $torca["VLUBV4_ORCAPRO"] - $torca["VLUDESC4_ORCAPRO"];
    $vlu5_orcapro = $vlu5_orcapro + $torca["VLU5_ORCAPRO"] + $torca["VLUOVER5_ORCAPRO"] + $torca["VLUBV5_ORCAPRO"] - $torca["VLUDESC5_ORCAPRO"];
  }
  else {
    if ($chave_modeloorcaimp == "5") {
      $vlu_orcapro = $vlu_orcapro + $torca["VLU_ORCAPRO"] + $torca["VLUOVER_ORCAPRO"] + $torca["VLUBV_ORCAPRO"] + $torca["VLUFRETE_ORCAPRO"] - $torca["VLUDESC_ORCAPRO"];
      $vlu2_orcapro = $vlu2_orcapro + $torca["VLU2_ORCAPRO"] + $torca["VLUOVER2_ORCAPRO"] + $torca["VLUBV2_ORCAPRO"] + $torca["VLUFRETE2_ORCAPRO"] - $torca["VLUDESC2_ORCAPRO"];
      $vlu3_orcapro = $vlu3_orcapro + $torca["VLU3_ORCAPRO"] + $torca["VLUOVER3_ORCAPRO"] + $torca["VLUBV3_ORCAPRO"] + $torca["VLUFRETE3_ORCAPRO"] - $torca["VLUDESC3_ORCAPRO"];
      $vlu4_orcapro = $vlu4_orcapro + $torca["VLU4_ORCAPRO"] + $torca["VLUOVER4_ORCAPRO"] + $torca["VLUBV4_ORCAPRO"] + $torca["VLUFRETE4_ORCAPRO"] - $torca["VLUDESC4_ORCAPRO"];
      $vlu5_orcapro = $vlu5_orcapro + $torca["VLU5_ORCAPRO"] + $torca["VLUOVER5_ORCAPRO"] + $torca["VLUBV5_ORCAPRO"] + $torca["VLUFRETE5_ORCAPRO"] - $torca["VLUDESC5_ORCAPRO"];
    }
    else {
      if ($qtd_orcapro > 0 && $vlt_orcapro > 0) {
        $vlu_orcapro = round($vlbruto_orcapro / $qtd_orcapro, 6);
        $vuliq_orcapro = round($vlliq_orcapro / $qtd_orcapro, 4);
      }
      if ($qtd2_orcapro > 0 && $vlt2_orcapro > 0) {
        $vlu2_orcapro = round($vlbruto2_orcapro / $qtd2_orcapro, 6);
        $vuliq2_orcapro = round($vlliq2_orcapro / $qtd2_orcapro, 4);
      }
      if ($qtd3_orcapro > 0 && $vlt3_orcapro > 0) {
        $vlu3_orcapro = round($vlbruto3_orcapro / $qtd3_orcapro, 6);
        $vuliq3_orcapro = round($vlliq3_orcapro / $qtd3_orcapro, 4);
      }
      if ($qtd4_orcapro > 0 && $vlt4_orcapro > 0) {
        $vlu4_orcapro = round($vlbruto4_orcapro / $qtd4_orcapro, 6);
        $vuliq4_orcapro = round($vlliq4_orcapro / $qtd4_orcapro, 4);
      }
      if ($qtd5_orcapro > 0 && $vlt5_orcapro > 0) {
        $vlu5_orcapro = round($vlbruto5_orcapro / $qtd5_orcapro, 6);
        $vuliq5_orcapro = round($vlliq5_orcapro / $qtd5_orcapro, 4);
      }
    }
  }
  $perdesc_orcapro = 0;
  $perdesc2_orcapro = 0;
  $perdesc3_orcapro = 0;
  $perdesc4_orcapro = 0;
  $perdesc5_orcapro = 0;
  if ($vludesc_orcapro > 0) {
    $perdesc_orcapro = round(($vludesc_orcapro / $vlu_orcapro) * 100, 4);
  }
  if ($vludesc2_orcapro > 0) {
    $perdesc2_orcapro = round(($vludesc2_orcapro / $vlu2_orcapro) * 100, 4);
  }
  if ($vludesc3_orcapro > 0) {
    $perdesc3_orcapro = round(($vludesc3_orcapro / $vlu3_orcapro) * 100, 4);
  }
  if ($vludesc4_orcapro > 0) {
    $perdesc4_orcapro = round(($vludesc4_orcapro / $vlu4_orcapro) * 100, 4);
  }
  if ($vludesc5_orcapro > 0) {
    $perdesc5_orcapro = round(($vludesc5_orcapro / $vlu5_orcapro) * 100, 4);
  }
  $perdesc_orcapro = moeda($perdesc_orcapro, 4, false);
  $perdesc2_orcapro = moeda($perdesc2_orcapro, 4, false);
  $perdesc3_orcapro = moeda($perdesc3_orcapro, 4, false);
  $perdesc4_orcapro = moeda($perdesc4_orcapro, 4, false);
  $perdesc5_orcapro = moeda($perdesc5_orcapro, 4, false);
  $cor_orcapro = mb_convert_encoding($torca["COR_ORCAPRO"], "windows-1252");
  $corgrava_orcapro = $torca["CORGRAVA_ORCAPRO"];
  $unity_orcapro = $torca["UNITY_ORCAPRO"];
  $icone_pro = trim($torca["ICONE_PRO"]);
  $bloco = array(
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
  );
  if ($obs_orcapro != "") {
    if ($richtext_reprcfg) {
      $decupada = $obs_orcapro;
      for ($i = 0; $i <= 9; $i++) {
        if (strlen($decupada) > 0) { 
          $colchete = -1;
          $chave = -1;
          if (strpos($decupada, '[') !== false) {
            $colchete = strpos($decupada, '[');
          }
          if (strpos($decupada, '{') !== false) {
            $chave = strpos($decupada, '{');
          }
          if ($colchete > (-1)) {
            if ($colchete < $chave || $chave == (-1)) {
              $bloco1 = '';
              $bloco2 = '';
              $bloco3 = '';
              $bloco1 = substr($decupada, 0, strpos($decupada, '['));
              $decupada = substr($decupada, strpos($decupada, '[') + 1);
              $bloco2 = substr($decupada, 0, strpos($decupada, ']'));
              $decupada = substr($decupada, strpos($decupada, ']') + 1);
              $bloco[$i][0] = $bloco1;
              $bloco[$i][1] = $bloco2;
              $bloco[$i][2] = $bloco3;
              $bloco[$i][3] = 'ffff00';
            }
          }
          if ($chave > (-1)) {
            if ($chave < $colchete || $colchete == (-1)) {
              $bloco1 = '';
              $bloco2 = '';
              $bloco3 = '';
              $bloco1 = substr($decupada, 0, strpos($decupada, '{'));
              $decupada = substr($decupada, strpos($decupada, '{') + 1);
              $bloco2 = substr($decupada, 0, strpos($decupada, '}'));
              $decupada = substr($decupada, strpos($decupada, '}') + 1);
              $bloco[$i][0] = $bloco1;
              $bloco[$i][1] = $bloco2;
              $bloco[$i][2] = $bloco3;
              $bloco[$i][3] = '00ff00';
            }        
          }
          if ($colchete == (-1) && $chave == (-1)) {
            $bloco1 = substr($decupada, 0);
            $bloco2 = '';
            $bloco3 = '';            
            $decupada = '';
            $bloco[$i][0] = $bloco1;
            $bloco[$i][1] = $bloco2;
            $bloco[$i][2] = $bloco3;
            $bloco[$i][3] = 'ffffff';
          }
        }
      }
    }
    else {
      $descr2_pro = $descr2_pro . PHP_EOL . $obs_orcapro;
    }
  }
  if ($especial_orcapro) {
    $strsql2 = "
    SELECT 
    CHAVE_RESP_ORCAPROPRJ
    ,IMG_ORCAPROPRJ 
    FROM 
    TORCAPROPRJ 
    WHERE 
    CHAVE_ORCAPRO = :VCHAVE_ORCAPRO AND 
    CAIXA_ORCAPROPRJ = 'CADASTRADO'
    ORDER BY CHAVE_ORCAPROPRJ";
    $qorcaproprj = $pdo_empresa->prepare($strsql2);
    $qorcaproprj->bindParam(":VCHAVE_ORCAPRO", $chave_orcapro);
    $qorcaproprj->execute();
    if ($torcaproprj = $qorcaproprj->fetch(PDO::FETCH_ASSOC)) {
      $icone_pro = trim($torcaproprj["IMG_ORCAPROPRJ"]);
      if (vazio($icone_pro)) {
        $chave_resp_orcaproprj = trim($torcaproprj["CHAVE_RESP_ORCAPROPRJ"]);
        $strsql2 = "
        SELECT 
        CHAVE_RESP_ORCAPROPRJ
        ,IMG_ORCAPROPRJ 
        FROM 
        TORCAPROPRJ 
        WHERE 
        CHAVE_ORCAPRO = :VCHAVE_ORCAPRO AND 
        CAIXA_ORCAPROPRJ = 'CADASTRADO'
        ORDER BY CHAVE_ORCAPROPRJ";
        $qorcaproprj = $pdo_empresa->prepare($strsql2);
        $qorcaproprj->bindParam(":VCHAVE_ORCAPRO", $chave_resp_orcaproprj);
        $qorcaproprj->execute();
        if ($torcaproprj = $qorcaproprj->fetch(PDO::FETCH_ASSOC)) {
          $icone_pro = trim($torcaproprj["IMG_ORCAPROPRJ"]);
        }
      }
    }
  }
  else {
    $strsql2 = "
    SELECT 
    CHAVE_PROIMG
    ,URL40_PROIMG
    ,URL120_PROIMG
    ,URL_PROIMG
    FROM 
    TPROIMG 
    WHERE 
    CHAVE_PRO = :VCHAVE_PRO AND 
    TIPOARQ_PROIMG = 'IMAGEM' AND 
    HOME_PROIMG = 1 AND  
    CAIXA_PROIMG = 'CADASTRADO'
    ORDER BY CHAVE_PROIMG";
    $qproimg = $pdo_empresa->prepare($strsql2);
    $qproimg->bindParam(":VCHAVE_PRO", $chave_pro);
    $qproimg->execute();
    if ($tproimg = $qproimg->fetch(PDO::FETCH_ASSOC)) {
      $icone_pro = $tproimg["URL120_PROIMG"];
    }
    if ($chave_proimg != "" && $chave_proimg != "0") {
      $chave_proimg = padraol($chave_proimg, 6, "0");
      $strsql2 = "
      SELECT 
      CHAVE_PROIMG
      ,URL40_PROIMG
      ,URL120_PROIMG
      ,URL_PROIMG
      FROM 
      TPROIMG 
      WHERE 
      CHAVE_PRO = :VCHAVE_PRO AND
      CHAVE_PROIMG = :VCHAVE_PROIMG AND
      TIPOARQ_PROIMG = 'IMAGEM' AND 
      CAIXA_PROIMG = 'CADASTRADO' 
      ORDER BY CHAVE_PROIMG";
      $qproimg = $pdo_empresa->prepare($strsql2);
      $qproimg->bindParam(":VCHAVE_PRO", $chave_pro);
      $qproimg->bindParam(":VCHAVE_PROIMG", $chave_proimg);
      $qproimg->execute();
      if ($tproimg = $qproimg->fetch(PDO::FETCH_ASSOC)) {
        $icone_pro = $tproimg["URL120_PROIMG"];
      }
    }
  }
  if (!vazio($icone_pro)) {
    if (!strpos(mb_strtoupper($icone_pro), '.JPG')) {
      if (!strpos(mb_strtoupper($icone_pro), '.JPEG')) {
        if (!strpos(mb_strtoupper($icone_pro), '.PNG')) {
          if (!strpos(mb_strtoupper($icone_pro), '.GIF')) {
            $icone_pro = "";
          }
        }
      }
    }
  }
  if ($icone_pro == "") {
    $icone_pro = "/corp2/media/semimagem180_moldura.png";
  }
  if (strpos($icone_pro, $pathfisicoimgpro_var) >= 0) {
    $icone_pro = str_replace($pathfisicoimgpro_var, $pathwebimgpro_var, $icone_pro);
  }
  if (strpos($icone_pro, "\\") >= 0) {
    $icone_pro = str_replace("\\", "/", $icone_pro);
  }
  $icone_pro = "https://unitycorp.com.br" . $icone_pro;
  $espacamento = 0; // pula uma linha at� a descri�ao
  $descrchr13 = $descr2_pro;
  if (substr($descrchr13, strlen($descrchr13) - 1, 1) != chr(13)) {
    $descrchr13 .= chr(13);
  }
  while (strpos($descrchr13, chr(13)) > 0) {
    $descr = substr($descrchr13, 0, strpos($descrchr13, chr(13)));
    $espacamento += 3.4;
    while (strlen($descr) > 120) {
      $espacamento += 3.4;
      $descr = substr($descr, 120);
    }
    $descrchr13 = substr($descrchr13, strpos($descrchr13, chr(13)) + 1);
  }
  $espacamento = round($espacamento);
  if (!vazio($corgrava_orcapro)) {
    $espacamento += 4;
  }
  if (!vazio($ncm_pro)) {
    $espacamento += 4;
  }
  if ($unity_orcapro == true) {
    $espacamento += 4;
  }
  $espacamento += 4;
  if ($imprime) {
    $espacamento += 10;
  }
  if ($espacamento <= 40) {
    $espacamento = 40;
  }
  if ($achave_orcapro != "") {
    $achave_orcapro .= ",";
  }
  $achave_orcapro .= $chave_orcapro;
  $strsql = "
  SELECT 
  TORCAPRO.*
  ,TPRO.NCM_PRO
  ,TPRO.ICONE_PRO
  ,TORCA.CHAVE_REPR
  ,TORCAKITPRO.COD_ORCAKITPRO
  ,TORCAKITPRO.DESCR_ORCAKITPRO
  FROM 
  TORCAPRO 
  INNER JOIN TORCA ON TORCAPRO.CHAVE_ORCA = TORCA.CHAVE_ORCA 
  LEFT JOIN TPRO ON TORCAPRO.CHAVE_PRO = TPRO.CHAVE_PRO 
  LEFT JOIN TORCAKITPRO ON TORCAPRO.CHAVE_ORCAKITPRO = TORCAKITPRO.CHAVE_ORCAKITPRO 
  WHERE 
  TORCAPRO.CHAVE_ORCA = :VCHAVE_ORCA AND 
  TORCAPRO.CHAVE_ORCAKITPRO = {$chave_orcakitpro} AND 
  TORCAPRO.CHAVE_ORCAPRO NOT IN ($achave_orcapro) AND 
  TORCAPRO.IMPORTPEDPRO_ORCAPRO = 0 AND         
  TORCAPRO.VLT_ORCAPRO > 0 AND ";
  if (strlen($reg_selec) > 0) {
    $strsql .= "TORCAPRO.CHAVE_ORCAPRO IN (" . $reg_selec . ") AND ";
  }
  $strsql .= "
  TORCAPRO.CHAVE_ORCAPRO <> {$chave_orcapro} AND 
  TORCAPRO.INATIVO_ORCAPRO = 0 AND 
  TORCAPRO.CAIXA_ORCAPRO = 'CADASTRADO' AND 
  TORCAKITPRO.CAIXA_ORCAKITPRO = 'CADASTRADO' AND 
  TORCA.CHAVE_REPR = :VCHAVE_REPR AND 
  TORCA.CAIXA_ORCA = 'CADASTRADO' 
  ORDER BY TORCAKITPRO.COD_ORCAKITPRO, " . $ordem . " " . $ordpos;
  $qorcaeof = $pdo_empresa->prepare($strsql);
  $qorcaeof->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorcaeof->bindParam(":VCHAVE_REPR", $chave_repr);
  $qorcaeof->execute();
  if (!$torcaeof = $qorcaeof->fetch(PDO::FETCH_ASSOC)) {
    if ((intval($pdf->GetY()) + $espacamento) > 190) {
      $contetq = 0;
    }
  }
  if ((intval($pdf->GetY()) + $espacamento) > 255) {
    $contetq = 0;
  }
  if ($contetq == 0) {
    $pdf->AddPage();
    $pdf->Ln(0);
    $contetq = 1;
  }
  if ($imprime) {
    $y = $pdf->GetY();
    $x = $pdf->GetX();
    if ($contkit > 1) {
      $y += 1;
      $pdf->SetXY($x, $y);
    }
    $pdf->SetFont('Segoe UI', 'B', 8);
    $pdf->SetFillColor(204, 204, 204); //#CCCCCC
    $pdf->Rect(10, $y, 190, 6, 'F');
    $pdf->Ln(8);
    $pdf->Cell(42, 4, '', 0, 0, 'L');
    $pdf->Cell(147, 4, $qrl_kit . ' ' . $cod_orcakitpro . ' ' . $descr_orcakitpro, 0, 0, 'L');
    $pdf->Ln(5);
    $imprime = false;
  }
  $pdf->SetFont('Segoe UI', '', 8);
  $y = $pdf->GetY();
  $icone_pro = str_replace(" ", "%20", $icone_pro);
  $pdf->Ln(1);
  $pdf->Cell(42, 42, '', 0, 0, 'L');
  $pdf->Image($icone_pro, 11, $y + 2, 40, 40);
  //if ($chave_repr == "007924") {
  //if ($chave_repr == "037249") {
  if ($richtext_reprcfg) {    
    $pdf->MultiCell(147, 3.4, $descr2_pro, 0, 'L', false);    
    for ($i = 0; $i <= 9; $i++) {
      if ($bloco[$i][0] != "") {
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell(147, 3.4, trim($bloco[$i][0]), 0, 'L', false); // First part of the MultiCell with no background
      }
      if ($bloco[$i][1] != "") {
        $largura = 147;
        if ((strlen($bloco[$i][1]) * 2) > 147) {
          $largura = 147;
        }
        else {
          if (strlen($bloco[$i][1]) <= 10) {
            $largura = strlen($bloco[$i][1]) * 3;
          }
          else {
            $largura = strlen($bloco[$i][1]) * 2;
          }
          if ($largura > 147) {
            $largura = 147;
          }
        }
        if ($bloco[$i][3] == "ffff00") {          
          $pdf->SetFillColor(255, 255, 0); // Set fill color for the partial background            
        }
        if ($bloco[$i][3] == "00ff00") {
          $pdf->SetFillColor(0, 255, 0); // Set fill color for the partial background            
        }
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell($largura, 3.4, $bloco[$i][1], 0, 'L', true); // Second part with background
      }
      $pdf->SetFillColor(255, 255, 255); // White background or default
      if ($bloco[$i][2] != "") {
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell(147, 3.4, trim($bloco[$i][2]), 0, 'L', false);
      }      
    }
  }
  else {
    $pdf->MultiCell(147, 3.4, $descr2_pro, 0, 'L', false);
  }    

//  $pdf->MultiCell(147, 3.4, $descr2_pro, 0, 'L', false);

  if (!vazio($corgrava_orcapro)) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(147, 4, "Grava��o {$corgrava_orcapro}", 0, 0, 'L');
    $pdf->Ln(4);
  }
  if (!vazio($ncm_pro)) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(148, 4, "NCM: {$ncm_pro}", 0, 0, 'L');
    $pdf->Ln(4);
  }
  if ($unity_orcapro == true) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $xx = $pdf->GetX();
    $yy = $pdf->GetY();
    $pdf->SetXY($xx + 1, $yy);
    $xx = $pdf->GetX();
    $yy = $pdf->GetY();
    $pdf->Image("https://unitycorp.com.br/corp2/media/unity16x16.png");
    $pdf->SetXY($xx + 6, $yy);
    $pdf->Cell(140, 4, "Produto sugerido pela Unity Brindes", 0, 0, 'L');
    $pdf->Ln(4);
  }
  if (vazio($ncm_pro) && vazio($corgrava_orcapro) && $unity_orcapro == false) {
    $pdf->Ln(4);
  }
  $contetq = $contetq + 1;
  if ($y != 0) {
    $pdf->SetXY(0, $y + $espacamento);
    $pdf->Ln(5);
  }
  //********************
  //********************
  //**** EOF IMPRIME CORPO DO PRODUTO DO KIT
  //********************
  //********************
}
if ($recno > 0) {
  //********************
  //********************
  //**** IMPRIME TOTAL DO KIT - QUANDO FOR EOF
  //********************
  //********************
  if ($chave_moeda != "01" && $chave_moeda != "" && $vlcotacao_orca != 1) {
    $vlu_orcapro = $vlu_orcapro * $vlcotacao_orca;
    $vlu2_orcapro = $vlu2_orcapro * $vlcotacao_orca;
    $vlu3_orcapro = $vlu3_orcapro * $vlcotacao_orca;
    $vlu4_orcapro = $vlu4_orcapro * $vlcotacao_orca;
    $vlu5_orcapro = $vlu5_orcapro * $vlcotacao_orca;
    if ($vltdesc_orcapro > 0) {
      $vludesc_orcapro = round($vltdesc_orcapro / $qtd_orcapro, 4);
    }
    if ($vltdesc2_orcapro > 0) {
      $vludesc2_orcapro = round($vltdesc2_orcapro / $qtd2_orcapro, 4);
    }
    if ($vltdesc3_orcapro > 0) {
      $vludesc3_orcapro = round($vltdesc3_orcapro / $qtd3_orcapro, 4);
    }
    if ($vltdesc4_orcapro > 0) {
      $vludesc4_orcapro = round($vltdesc4_orcapro / $qtd4_orcapro, 4);
    }
    if ($vltdesc5_orcapro > 0) {
      $vludesc5_orcapro = round($vltdesc5_orcapro / $qtd5_orcapro, 4);
    }
    if ($vludesc_orcapro > 0) {
      $vludesc_orcapro = $vludesc_orcapro * $vlcotacao_orca;
    }
    if ($vludesc2_orcapro > 0) {
      $vludesc2_orcapro = $vludesc2_orcapro * $vlcotacao_orca;
    }
    if ($vludesc3_orcapro > 0) {
      $vludesc3_orcapro = $vludesc3_orcapro * $vlcotacao_orca;
    }
    if ($vludesc4_orcapro > 0) {
      $vludesc4_orcapro = $vludesc4_orcapro * $vlcotacao_orca;
    }
    if ($vludesc5_orcapro > 0) {
      $vludesc5_orcapro = $vludesc5_orcapro * $vlcotacao_orca;
    }
    if ($vlbruto_orcapro > 0) {
      $vlbruto_orcapro = $vlbruto_orcapro * $vlcotacao_orca;
    }
    if ($vlbruto2_orcapro > 0) {
      $vlbruto2_orcapro = $vlbruto2_orcapro * $vlcotacao_orca;
    }
    if ($vlbruto3_orcapro > 0) {
      $vlbruto3_orcapro = $vlbruto3_orcapro * $vlcotacao_orca;
    }
    if ($vlbruto4_orcapro > 0) {
      $vlbruto4_orcapro = $vlbruto4_orcapro * $vlcotacao_orca;
    }
    if ($vlbruto5_orcapro > 0) {
      $vlbruto5_orcapro = $vlbruto5_orcapro * $vlcotacao_orca;
    }
    if ($vuliq_orcapro > 0) {
      $vuliq_orcapro = $vuliq_orcapro * $vlcotacao_orca;
    }
    if ($vuliq2_orcapro > 0) {
      $vuliq2_orcapro = $vuliq2_orcapro * $vlcotacao_orca;
    }
    if ($vuliq3_orcapro > 0) {
      $vuliq3_orcapro = $vuliq3_orcapro * $vlcotacao_orca;
    }
    if ($vuliq4_orcapro > 0) {
      $vuliq4_orcapro = $vuliq4_orcapro * $vlcotacao_orca;
    }
    if ($vuliq5_orcapro > 0) {
      $vuliq5_orcapro = $vuliq5_orcapro * $vlcotacao_orca;
    }
    if ($vlliq_orcapro > 0) {
      $vlliq_orcapro = $vlliq_orcapro * $vlcotacao_orca;
    }
    if ($vlliq2_orcapro > 0) {
      $vlliq2_orcapro = $vlliq2_orcapro * $vlcotacao_orca;
    }
    if ($vlliq3_orcapro > 0) {
      $vlliq3_orcapro = $vlliq3_orcapro * $vlcotacao_orca;
    }
    if ($vlliq4_orcapro > 0) {
      $vlliq4_orcapro = $vlliq4_orcapro * $vlcotacao_orca;
    }
    if ($vlliq5_orcapro > 0) {
      $vlliq5_orcapro = $vlliq5_orcapro * $vlcotacao_orca;
    }
    if ($vlfrete_orcapro > 0) {
      $vlfrete_orcapro = $vlfrete_orcapro * $vlcotacao_orca;
    }
    if ($vlfrete2_orcapro > 0) {
      $vlfrete2_orcapro = $vlfrete2_orcapro * $vlcotacao_orca;
    }
    if ($vlfrete3_orcapro > 0) {
      $vlfrete3_orcapro = $vlfrete3_orcapro * $vlcotacao_orca;
    }
    if ($vlfrete4_orcapro > 0) {
      $vlfrete4_orcapro = $vlfrete4_orcapro * $vlcotacao_orca;
    }
    if ($vlfrete5_orcapro > 0) {
      $vlfrete5_orcapro = $vlfrete5_orcapro * $vlcotacao_orca;
    }
    if ($vlt_orcapro > 0) {
      $vlt_orcapro = $vlt_orcapro * $vlcotacao_orca;
    }
    if ($vlt2_orcapro > 0) {
      $vlt2_orcapro = $vlt2_orcapro * $vlcotacao_orca;
    }
    if ($vlt3_orcapro > 0) {
      $vlt3_orcapro = $vlt3_orcapro * $vlcotacao_orca;
    }
    if ($vlt4_orcapro > 0) {
      $vlt4_orcapro = $vlt4_orcapro * $vlcotacao_orca;
    }
    if ($vlt5_orcapro > 0) {
      $vlt5_orcapro = $vlt5_orcapro * $vlcotacao_orca;
    }
    /*
    $qtd_orcapro = moeda($qtd_orcapro, 0, false);
    $qtd2_orcapro = moeda($qtd2_orcapro, 0, false);
    $qtd3_orcapro = moeda($qtd3_orcapro, 0, false);
    $qtd4_orcapro = moeda($qtd4_orcapro, 0, false);
    $qtd5_orcapro = moeda($qtd5_orcapro, 0, false);
    $vlu_orcapro = moeda($vlu_orcapro, 6, false);
    $vlu2_orcapro = moeda($vlu2_orcapro, 6, false);
    $vlu3_orcapro = moeda($vlu3_orcapro, 6, false);
    $vlu4_orcapro = moeda($vlu4_orcapro, 6, false);
    $vlu5_orcapro = moeda($vlu5_orcapro, 6, false);
    $vludesc_orcapro = moeda($vludesc_orcapro, 4, false);
    $vludesc2_orcapro = moeda($vludesc2_orcapro, 4, false);
    $vludesc3_orcapro = moeda($vludesc3_orcapro, 4, false);
    $vludesc4_orcapro = moeda($vludesc4_orcapro, 4, false);
    $vludesc5_orcapro = moeda($vludesc5_orcapro, 4, false);
    $vlbruto_orcapro = moeda($vlbruto_orcapro, 2, false);
    $vlbruto2_orcapro = moeda($vlbruto2_orcapro, 2, false);
    $vlbruto3_orcapro = moeda($vlbruto3_orcapro, 2, false);
    $vlbruto4_orcapro = moeda($vlbruto4_orcapro, 2, false);
    $vlbruto5_orcapro = moeda($vlbruto5_orcapro, 2, false);
    $vuliq_orcapro = moeda($vuliq_orcapro, 4, false);
    $vuliq2_orcapro = moeda($vuliq2_orcapro, 4, false);
    $vuliq3_orcapro = moeda($vuliq3_orcapro, 4, false);
    $vuliq4_orcapro = moeda($vuliq4_orcapro, 4, false);
    $vuliq5_orcapro = moeda($vuliq5_orcapro, 4, false);
    $vlliq_orcapro = moeda($vlliq_orcapro, 2, false);
    $vlliq2_orcapro = moeda($vlliq2_orcapro, 2, false);
    $vlliq3_orcapro = moeda($vlliq3_orcapro, 2, false);
    $vlliq4_orcapro = moeda($vlliq4_orcapro, 2, false);
    $vlliq5_orcapro = moeda($vlliq5_orcapro, 2, false);
    $vlfrete_orcapro = moeda($vlfrete_orcapro, 2, false);
    $vlfrete2_orcapro = moeda($vlfrete2_orcapro, 2, false);
    $vlfrete3_orcapro = moeda($vlfrete3_orcapro, 2, false);
    $vlfrete4_orcapro = moeda($vlfrete4_orcapro, 2, false);
    $vlfrete5_orcapro = moeda($vlfrete5_orcapro, 2, false);
    $vlt_orcapro = moeda($vlt_orcapro, 2, false);
    $vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
    $vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
    $vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
    $vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
    */
    $qtd_orcapro = formatalocal($qtd_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd2_orcapro = formatalocal($qtd2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd3_orcapro = formatalocal($qtd3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd4_orcapro = formatalocal($qtd4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd5_orcapro = formatalocal($qtd5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu_orcapro = formatalocal($vlu_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu2_orcapro = formatalocal($vlu2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu3_orcapro = formatalocal($vlu3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu4_orcapro = formatalocal($vlu4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu5_orcapro = formatalocal($vlu5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc_orcapro = formatalocal($perdesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc2_orcapro = formatalocal($perdesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc3_orcapro = formatalocal($perdesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc4_orcapro = formatalocal($perdesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc5_orcapro = formatalocal($perdesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc_orcapro = formatalocal($vludesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc2_orcapro = formatalocal($vludesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc3_orcapro = formatalocal($vludesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc4_orcapro = formatalocal($vludesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc5_orcapro = formatalocal($vludesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto_orcapro = formatalocal($vlbruto_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto2_orcapro = formatalocal($vlbruto2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto3_orcapro = formatalocal($vlbruto3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto4_orcapro = formatalocal($vlbruto4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto5_orcapro = formatalocal($vlbruto5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq_orcapro = formatalocal($vuliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq2_orcapro = formatalocal($vuliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq3_orcapro = formatalocal($vuliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq4_orcapro = formatalocal($vuliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq5_orcapro = formatalocal($vuliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq_orcapro = formatalocal($vlliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq2_orcapro = formatalocal($vlliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq3_orcapro = formatalocal($vlliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq4_orcapro = formatalocal($vlliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq5_orcapro = formatalocal($vlliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete_orcapro = formatalocal($vlfrete_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete2_orcapro = formatalocal($vlfrete2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete3_orcapro = formatalocal($vlfrete3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete4_orcapro = formatalocal($vlfrete4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete5_orcapro = formatalocal($vlfrete5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt_orcapro = formatalocal($vlt_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt2_orcapro = formatalocal($vlt2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt3_orcapro = formatalocal($vlt3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt4_orcapro = formatalocal($vlt4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt5_orcapro = formatalocal($vlt5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
  }
  else {
    $qtd_orcapro = moeda($qtd_orcapro, 0, false);
    $qtd2_orcapro = moeda($qtd2_orcapro, 0, false);
    $qtd3_orcapro = moeda($qtd3_orcapro, 0, false);
    $qtd4_orcapro = moeda($qtd4_orcapro, 0, false);
    $qtd5_orcapro = moeda($qtd5_orcapro, 0, false);
    $vlu_orcapro = moeda($vlu_orcapro, 2, false);
    $vlu2_orcapro = moeda($vlu2_orcapro, 2, false);
    $vlu3_orcapro = moeda($vlu3_orcapro, 2, false);
    $vlu4_orcapro = moeda($vlu4_orcapro, 2, false);
    $vlu5_orcapro = moeda($vlu5_orcapro, 2, false);
    $vludesc_orcapro = moeda($vludesc_orcapro, 2, false);
    $vludesc2_orcapro = moeda($vludesc2_orcapro, 2, false);
    $vludesc3_orcapro = moeda($vludesc3_orcapro, 2, false);
    $vludesc4_orcapro = moeda($vludesc4_orcapro, 2, false);
    $vludesc5_orcapro = moeda($vludesc5_orcapro, 2, false);
    $vlbruto_orcapro = moeda($vlbruto_orcapro, 2, false);
    $vlbruto2_orcapro = moeda($vlbruto2_orcapro, 2, false);
    $vlbruto3_orcapro = moeda($vlbruto3_orcapro, 2, false);
    $vlbruto4_orcapro = moeda($vlbruto4_orcapro, 2, false);
    $vlbruto5_orcapro = moeda($vlbruto5_orcapro, 2, false);
    $vuliq_orcapro = moeda($vuliq_orcapro, 2, false);
    $vuliq2_orcapro = moeda($vuliq2_orcapro, 2, false);
    $vuliq3_orcapro = moeda($vuliq3_orcapro, 2, false);
    $vuliq4_orcapro = moeda($vuliq4_orcapro, 2, false);
    $vuliq5_orcapro = moeda($vuliq5_orcapro, 2, false);
    $vlliq_orcapro = moeda($vlliq_orcapro, 2, false);
    $vlliq2_orcapro = moeda($vlliq2_orcapro, 2, false);
    $vlliq3_orcapro = moeda($vlliq3_orcapro, 2, false);
    $vlliq4_orcapro = moeda($vlliq4_orcapro, 2, false);
    $vlliq5_orcapro = moeda($vlliq5_orcapro, 2, false);
    $vlfrete_orcapro = moeda($vlfrete_orcapro, 2, false);
    $vlfrete2_orcapro = moeda($vlfrete2_orcapro, 2, false);
    $vlfrete3_orcapro = moeda($vlfrete3_orcapro, 2, false);
    $vlfrete4_orcapro = moeda($vlfrete4_orcapro, 2, false);
    $vlfrete5_orcapro = moeda($vlfrete5_orcapro, 2, false);
    $vlt_orcapro = moeda($vlt_orcapro, 2, false);
    $vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
    $vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
    $vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
    $vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
  }
  $pdf->Ln(0);
  //********************
  //********************
  //**** Unit�rio/Total ou Subtotal/Total
  //********************
  //********************
  if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(30, 4, $qrl_pro_vlu, 0, 0, 'R');
    if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
      $pdf->Cell(30, 4, $qrl_pro_frete, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu2_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu3_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu4_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu5_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Total ou Subtotal/Total
  //********************
  //********************

  //********************
  //********************
  //**** Unit�rio/Bruto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "2") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlu, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
    $pdf->Cell(20, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Bruto/Total
  //********************
  //********************

  //********************
  //********************
  //**** Unit�rio/Bruto/Descto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "3") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(15, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(16, 4, $qrl_pro_vlu, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
    $pdf->Cell(15, 4, $qrl_pro_perdesc, 0, 0, 'R');
    $pdf->Cell(18, 4, $qrl_pro_vludesc, 0, 0, 'R');
    $pdf->Cell(16, 4, $qrl_pro_vluliq, 0, 0, 'R');
    $pdf->Cell(18, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc2_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc2_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq2_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc3_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc3_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq3_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc4_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc4_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq4_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc5_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc5_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq5_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Bruto/Descto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "5") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(40, 4, $qrl_pro_vlu . " " . $qrl_pro_comfrete, 0, 0, 'R');
    $pdf->Cell(40, 4, $qrl_pro_vlt . " " . $qrl_pro_comfrete, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF IMPRIME TOTAL DO KIT
  //********************
  //********************
  $pdf->Ln(2);
}

//********************
//********************
//**** PRODUTOS NORMAIS / NAO KIT / N�O KIT
//********************
//********************
$y = 0;
if ($recno > 0) {
  $pdf->Ln(2);
}
else {
  $contetq = 1;
}
$strsql = "
SELECT 
TORCAPRO.*
,TPRO.NCM_PRO
,TPRO.ICONE_PRO
,TORCA.CHAVE_REPR
,TORCAKITPRO.COD_ORCAKITPRO
FROM 
TORCAPRO 
INNER JOIN TORCA ON TORCAPRO.CHAVE_ORCA = TORCA.CHAVE_ORCA 
LEFT JOIN TPRO ON TORCAPRO.CHAVE_PRO = TPRO.CHAVE_PRO 
LEFT JOIN TORCAKITPRO ON TORCAPRO.CHAVE_ORCAKITPRO = TORCAKITPRO.CHAVE_ORCAKITPRO 
WHERE 
TORCAPRO.CHAVE_ORCA = :VCHAVE_ORCA AND 
TORCAPRO.IMPORTPEDPRO_ORCAPRO = 0 AND 
TORCAPRO.CHAVE_ORCAKITPRO <= 0 AND 
TORCAPRO.VLT_ORCAPRO > 0 AND ";
if (strlen($reg_selec) > 0) {
  $strsql .= "TORCAPRO.CHAVE_ORCAPRO IN (" . $reg_selec . ") AND ";
}
$strsql .= "
TORCAPRO.INATIVO_ORCAPRO = 0 AND 
TORCAPRO.CAIXA_ORCAPRO = 'CADASTRADO' AND 
TORCA.CHAVE_REPR = :VCHAVE_REPR 
ORDER BY " . $ordem . " " . $ordpos;
$qorca = $pdo_empresa->prepare($strsql);
$qorca->bindParam(":VCHAVE_ORCA", $chave_orca);
$qorca->bindParam(":VCHAVE_REPR", $chave_repr);
$qorca->execute();
while ($torca = $qorca->fetch(PDO::FETCH_ASSOC)) {
  $chave_orcapro = $torca["CHAVE_ORCAPRO"];
  $chave_proimg = $torca["CHAVE_PROIMG"];
  $chave_pro = $torca["CHAVE_PRO"];
  $cod_pro = $torca["COD_PRO"];
  $descr2_pro = mb_convert_encoding(trim($torca["DESCR2_PRO"]), "windows-1252");
  if (strlen($descr2_pro) > 1024) {
    $descr2_pro = substr($descr2_pro, 0, 1024) . "...";
  }
  $config_orcapro = $torca["CONFIG_ORCAPRO"];
  $obs_orcapro = mb_convert_encoding($torca["OBS_ORCAPRO"], "windows-1252");
  $ncm_pro = $torca["NCM_PRO"];
  $especial_orcapro = $torca["ESPECIAL_ORCAPRO"];
  $qtd_orcapro = 0;
  $qtd2_orcapro = 0;
  $qtd3_orcapro = 0;
  $qtd4_orcapro = 0;
  $qtd5_orcapro = 0;
  $vlu_orcapro = 0;
  $vlu2_orcapro = 0;
  $vlu3_orcapro = 0;
  $vlu4_orcapro = 0;
  $vlu5_orcapro = 0;
  $vlbruto_orcapro = 0;
  $vlbruto2_orcapro = 0;
  $vlbruto3_orcapro = 0;
  $vlbruto4_orcapro = 0;
  $vlbruto5_orcapro = 0;
  $perdesc_orcapro = 0;
  $perdesc2_orcapro = 0;
  $perdesc3_orcapro = 0;
  $perdesc4_orcapro = 0;
  $perdesc5_orcapro = 0;
  $vludesc_orcapro = 0;
  $vludesc2_orcapro = 0;
  $vludesc3_orcapro = 0;
  $vludesc4_orcapro = 0;
  $vludesc5_orcapro = 0;
  $vltdesc_orcapro = 0;
  $vltdesc2_orcapro = 0;
  $vltdesc3_orcapro = 0;
  $vltdesc4_orcapro = 0;
  $vltdesc5_orcapro = 0;
  $vuliq_orcapro = 0;
  $vuliq2_orcapro = 0;
  $vuliq3_orcapro = 0;
  $vuliq4_orcapro = 0;
  $vuliq5_orcapro = 0;
  $vlliq_orcapro = 0;
  $vlliq2_orcapro = 0;
  $vlliq3_orcapro = 0;
  $vlliq4_orcapro = 0;
  $vlliq5_orcapro = 0;
  $vlfrete_orcapro = 0;
  $vlfrete2_orcapro = 0;
  $vlfrete3_orcapro = 0;
  $vlfrete4_orcapro = 0;
  $vlfrete5_orcapro = 0;
  $frete1 = 0;
  $frete2 = 0;
  $frete3 = 0;
  $frete4 = 0;
  $frete5 = 0;
  $vlt_orcapro = 0;
  $vlt2_orcapro = 0;
  $vlt3_orcapro = 0;
  $vlt4_orcapro = 0;
  $vlt5_orcapro = 0;
  $qtd_orcapro = moeda($torca["QTD_ORCAPRO"], 0, false);
  $qtd2_orcapro = moeda($torca["QTD2_ORCAPRO"], 0, false);
  $qtd3_orcapro = moeda($torca["QTD3_ORCAPRO"], 0, false);
  $qtd4_orcapro = moeda($torca["QTD4_ORCAPRO"], 0, false);
  $qtd5_orcapro = moeda($torca["QTD5_ORCAPRO"], 0, false);
  if ($chave_moeda != "01" && $chave_moeda != "" && $vlcotacao_orca != 1) {  
    if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
      $vlu_orcapro = floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]) - floatval($torca["VLUDESC_ORCAPRO"]);
      $vlu2_orcapro = floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]) - floatval($torca["VLUDESC2_ORCAPRO"]);
      $vlu3_orcapro = floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]) - floatval($torca["VLUDESC3_ORCAPRO"]);
      $vlu4_orcapro = floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]) - floatval($torca["VLUDESC4_ORCAPRO"]);
      $vlu5_orcapro = floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]) - floatval($torca["VLUDESC5_ORCAPRO"]);
    }
    else {
      if ($chave_modeloorcaimp == "5") {
        $vlu_orcapro = floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]) + floatval($torca["VLUFRETE_ORCAPRO"]) - floatval($torca["VLUDESC_ORCAPRO"]);
        $vlu2_orcapro = floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]) + floatval($torca["VLUFRETE2_ORCAPRO"]) - floatval($torca["VLUDESC2_ORCAPRO"]);
        $vlu3_orcapro = floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]) + floatval($torca["VLUFRETE3_ORCAPRO"]) - floatval($torca["VLUDESC3_ORCAPRO"]);
        $vlu4_orcapro = floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]) + floatval($torca["VLUFRETE4_ORCAPRO"]) - floatval($torca["VLUDESC4_ORCAPRO"]);
        $vlu5_orcapro = floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]) + floatval($torca["VLUFRETE5_ORCAPRO"]) - floatval($torca["VLUDESC5_ORCAPRO"]);
      }
      else {
        $vlu_orcapro = floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]);
        $vlu2_orcapro = floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]);
        $vlu3_orcapro = floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]);
        $vlu4_orcapro = floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]);
        $vlu5_orcapro = floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]);
      }
    }
    $vlbruto_orcapro = floatval($torca["VLBRUTO_ORCAPRO"]);
    $vlbruto2_orcapro = floatval($torca["VLBRUTO2_ORCAPRO"]);
    $vlbruto3_orcapro = floatval($torca["VLBRUTO3_ORCAPRO"]);
    $vlbruto4_orcapro = floatval($torca["VLBRUTO4_ORCAPRO"]);
    $vlbruto5_orcapro = floatval($torca["VLBRUTO5_ORCAPRO"]);
    //$perdesc_orcapro = $torca["PERDESC_ORCAPRO"],4,false);
    //$perdesc2_orcapro = $torca["PERDESC2_ORCAPRO"],4,false);
    //$perdesc3_orcapro = $torca["PERDESC3_ORCAPRO"],4,false);
    //$perdesc4_orcapro = $torca["PERDESC4_ORCAPRO"],4,false);
    //$perdesc5_orcapro = $torca["PERDESC5_ORCAPRO"],4,false);

    $perdesc_orcapro = floatval($torca["PERDESC_ORCAPRO"]);
    $perdesc2_orcapro = floatval($torca["PERDESC2_ORCAPRO"]);
    $perdesc3_orcapro = floatval($torca["PERDESC3_ORCAPRO"]);
    $perdesc4_orcapro = floatval($torca["PERDESC4_ORCAPRO"]);
    $perdesc5_orcapro = floatval($torca["PERDESC5_ORCAPRO"]);
    if ($perdesc_orcapro > 0) {
      $perdesc_orcapro = moeda($perdesc_orcapro, 4, false);
    }
    if ($perdesc2_orcapro > 0) {
      $perdesc2_orcapro = moeda($perdesc2_orcapro, 4, false);
    }
    if ($perdesc3_orcapro > 0) {
      $perdesc3_orcapro = moeda($perdesc3_orcapro, 4, false);
    }
    if ($perdesc4_orcapro > 0) {
      $perdesc4_orcapro = moeda($perdesc4_orcapro, 4, false);
    }
    if ($perdesc5_orcapro > 0) {
      $perdesc5_orcapro = moeda($perdesc5_orcapro, 4, false);
    }    

    $vludesc_orcapro = floatval($torca["VLUDESC_ORCAPRO"]);
    $vludesc2_orcapro = floatval($torca["VLUDESC2_ORCAPRO"]);
    $vludesc3_orcapro = floatval($torca["VLUDESC3_ORCAPRO"]);
    $vludesc4_orcapro = floatval($torca["VLUDESC4_ORCAPRO"]);
    $vludesc5_orcapro = floatval($torca["VLUDESC5_ORCAPRO"]);
    $vltdesc_orcapro = floatval($torca["VLTDESC_ORCAPRO"]);
    $vltdesc2_orcapro = floatval($torca["VLTDESC2_ORCAPRO"]);
    $vltdesc3_orcapro = floatval($torca["VLTDESC3_ORCAPRO"]);
    $vltdesc4_orcapro = floatval($torca["VLTDESC4_ORCAPRO"]);
    $vltdesc5_orcapro = floatval($torca["VLTDESC5_ORCAPRO"]);
    $vuliq_orcapro = floatval($torca["VLUT_ORCAPRO"]);
    $vuliq2_orcapro = floatval($torca["VLUT2_ORCAPRO"]);
    $vuliq3_orcapro = floatval($torca["VLUT3_ORCAPRO"]);
    $vuliq4_orcapro = floatval($torca["VLUT4_ORCAPRO"]);
    $vuliq5_orcapro = floatval($torca["VLUT5_ORCAPRO"]);
    $vlliq_orcapro = floatval($torca["VLLIQ_ORCAPRO"]);
    $vlliq2_orcapro = floatval($torca["VLLIQ2_ORCAPRO"]);
    $vlliq3_orcapro = floatval($torca["VLLIQ3_ORCAPRO"]);
    $vlliq4_orcapro = floatval($torca["VLLIQ4_ORCAPRO"]);
    $vlliq5_orcapro = floatval($torca["VLLIQ5_ORCAPRO"]);
    $vlfrete_orcapro = floatval($torca["VLFRETE_ORCAPRO"]);
    $vlfrete2_orcapro = floatval($torca["VLFRETE2_ORCAPRO"]);
    $vlfrete3_orcapro = floatval($torca["VLFRETE3_ORCAPRO"]);
    $vlfrete4_orcapro = floatval($torca["VLFRETE4_ORCAPRO"]);
    $vlfrete5_orcapro = floatval($torca["VLFRETE5_ORCAPRO"]);
    $vlufrete_orcapro = floatval($torca["VLUFRETE_ORCAPRO"]);
    $vlufrete2_orcapro = floatval($torca["VLUFRETE2_ORCAPRO"]);
    $vlufrete3_orcapro = floatval($torca["VLUFRETE3_ORCAPRO"]);
    $vlufrete4_orcapro = floatval($torca["VLUFRETE4_ORCAPRO"]);
    $vlufrete5_orcapro = floatval($torca["VLUFRETE5_ORCAPRO"]);
    $frete1 = floatval($torca["VLFRETE_ORCAPRO"]);
    $frete2 = floatval($torca["VLFRETE2_ORCAPRO"]);
    $frete3 = floatval($torca["VLFRETE3_ORCAPRO"]);
    $frete4 = floatval($torca["VLFRETE4_ORCAPRO"]);
    $frete5 = floatval($torca["VLFRETE5_ORCAPRO"]);
    $vlt_orcapro = floatval($torca["VLT_ORCAPRO"]);
    $vlt2_orcapro = floatval($torca["VLT2_ORCAPRO"]);
    $vlt3_orcapro = floatval($torca["VLT3_ORCAPRO"]);
    $vlt4_orcapro = floatval($torca["VLT4_ORCAPRO"]);
    $vlt5_orcapro = floatval($torca["VLT5_ORCAPRO"]);
    //$vlt_orcapro = moeda($vlt_orcapro, 2, false);
    //$vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
    //$vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
    //$vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
    //$vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
    $vlu_orcapro = moeda($vlu_orcapro * $vlcotacao_orca, 4, false);
    $vlu2_orcapro = moeda($vlu2_orcapro * $vlcotacao_orca, 4, false);
    $vlu3_orcapro = moeda($vlu3_orcapro * $vlcotacao_orca, 4, false);
    $vlu4_orcapro = moeda($vlu4_orcapro * $vlcotacao_orca, 4, false);
    $vlu5_orcapro = moeda($vlu5_orcapro * $vlcotacao_orca, 4, false);
    if ($vludesc_orcapro > 0) {
      $vludesc_orcapro = moeda($vludesc_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vludesc2_orcapro > 0) {
      $vludesc2_orcapro = moeda($vludesc2_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vludesc3_orcapro > 0) {
      $vludesc3_orcapro = moeda($vludesc3_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vludesc4_orcapro > 0) {
      $vludesc4_orcapro = moeda($vludesc4_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vludesc5_orcapro > 0) {
      $vludesc5_orcapro = moeda($vludesc5_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlbruto_orcapro > 0) {
      $vlbruto_orcapro = moeda($vlbruto_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlbruto2_orcapro > 0) {
      $vlbruto2_orcapro = moeda($vlbruto2_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlbruto3_orcapro > 0) {
      $vlbruto3_orcapro = moeda($vlbruto3_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlbruto4_orcapro > 0) {
      $vlbruto4_orcapro = moeda($vlbruto4_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlbruto5_orcapro > 0) {
      $vlbruto5_orcapro = moeda($vlbruto5_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vuliq_orcapro > 0) {
      $vuliq_orcapro = moeda($vuliq_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vuliq2_orcapro > 0) {
      $vuliq2_orcapro = moeda($vuliq2_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vuliq3_orcapro > 0) {
      $vuliq3_orcapro = moeda($vuliq3_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vuliq4_orcapro > 0) {
      $vuliq4_orcapro = moeda($vuliq4_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vuliq5_orcapro > 0) {
      $vuliq5_orcapro = moeda($vuliq5_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlliq_orcapro > 0) {
      $vlliq_orcapro = moeda($vlliq_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlliq2_orcapro > 0) {
      $vlliq2_orcapro = moeda($vlliq2_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlliq3_orcapro > 0) {
      $vlliq3_orcapro = moeda($vlliq3_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlliq4_orcapro > 0) {
      $vlliq4_orcapro = moeda($vlliq4_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlliq5_orcapro > 0) {
      $vlliq5_orcapro = moeda($vlliq5_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlfrete_orcapro > 0) {
      $vlfrete_orcapro = moeda($vlfrete_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlfrete2_orcapro > 0) {
      $vlfrete2_orcapro = moeda($vlfrete2_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlfrete3_orcapro > 0) {
      $vlfrete3_orcapro = moeda($vlfrete3_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlfrete4_orcapro > 0) {
      $vlfrete4_orcapro = moeda($vlfrete4_orcapro * $vlcotacao_orca, 4, false);
    }
    if ($vlfrete5_orcapro > 0) {
      $vlfrete5_orcapro = moeda($vlfrete5_orcapro * $vlcotacao_orca, 4, false);
    }
    
    if ($vlt_orcapro > 0) {
      $vlt_orcapro = moeda($vlt_orcapro * $vlcotacao_orca, 2, false);      
    }
    if ($vlt2_orcapro > 0) {
      $vlt2_orcapro = moeda($vlt2_orcapro * $vlcotacao_orca, 2, false);
    }
    if ($vlt3_orcapro > 0) {
      $vlt3_orcapro = moeda($vlt3_orcapro * $vlcotacao_orca, 2, false);
    }
    if ($vlt4_orcapro > 0) {
      $vlt4_orcapro = moeda($vlt4_orcapro * $vlcotacao_orca, 2, false);
    }
    if ($vlt5_orcapro > 0) {
      $vlt5_orcapro = moeda($vlt5_orcapro * $vlcotacao_orca, 2, false);
    }
  }
  else {
    if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
      $vlu_orcapro = moeda(floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]) - floatval($torca["VLUDESC_ORCAPRO"]), 2, false);
      $vlu2_orcapro = moeda(floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]) - floatval($torca["VLUDESC2_ORCAPRO"]), 2, false);
      $vlu3_orcapro = moeda(floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]) - floatval($torca["VLUDESC3_ORCAPRO"]), 2, false);
      $vlu4_orcapro = moeda(floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]) - floatval($torca["VLUDESC4_ORCAPRO"]), 2, false);
      $vlu5_orcapro = moeda(floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]) - floatval($torca["VLUDESC5_ORCAPRO"]), 2, false);      
    }
    else {
      if ($chave_modeloorcaimp == "5") {
        $vlu_orcapro = moeda(floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]) + floatval($torca["VLUFRETE_ORCAPRO"]) - floatval($torca["VLUDESC_ORCAPRO"]), 2, false);
        $vlu2_orcapro = moeda(floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]) + floatval($torca["VLUFRETE2_ORCAPRO"]) - floatval($torca["VLUDESC2_ORCAPRO"]), 2, false);
        $vlu3_orcapro = moeda(floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]) + floatval($torca["VLUFRETE3_ORCAPRO"]) - floatval($torca["VLUDESC3_ORCAPRO"]), 2, false);
        $vlu4_orcapro = moeda(floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]) + floatval($torca["VLUFRETE4_ORCAPRO"]) - floatval($torca["VLUDESC4_ORCAPRO"]), 2, false);
        $vlu5_orcapro = moeda(floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]) + floatval($torca["VLUFRETE5_ORCAPRO"]) - floatval($torca["VLUDESC5_ORCAPRO"]), 2, false);
      }
      else {
        $vlu_orcapro = moeda(floatval($torca["VLU_ORCAPRO"]) + floatval($torca["VLUOVER_ORCAPRO"]) + floatval($torca["VLUBV_ORCAPRO"]), 2, false);
        $vlu2_orcapro = moeda(floatval($torca["VLU2_ORCAPRO"]) + floatval($torca["VLUOVER2_ORCAPRO"]) + floatval($torca["VLUBV2_ORCAPRO"]), 2, false);
        $vlu3_orcapro = moeda(floatval($torca["VLU3_ORCAPRO"]) + floatval($torca["VLUOVER3_ORCAPRO"]) + floatval($torca["VLUBV3_ORCAPRO"]), 2, false);
        $vlu4_orcapro = moeda(floatval($torca["VLU4_ORCAPRO"]) + floatval($torca["VLUOVER4_ORCAPRO"]) + floatval($torca["VLUBV4_ORCAPRO"]), 2, false);
        $vlu5_orcapro = moeda(floatval($torca["VLU5_ORCAPRO"]) + floatval($torca["VLUOVER5_ORCAPRO"]) + floatval($torca["VLUBV5_ORCAPRO"]), 2, false);
      }
    }
    $vlbruto_orcapro = moeda(floatval($torca["VLBRUTO_ORCAPRO"]), 2, false);
    $vlbruto2_orcapro = moeda(floatval($torca["VLBRUTO2_ORCAPRO"]), 2, false);
    $vlbruto3_orcapro = moeda(floatval($torca["VLBRUTO3_ORCAPRO"]), 2, false);
    $vlbruto4_orcapro = moeda(floatval($torca["VLBRUTO4_ORCAPRO"]), 2, false);
    $vlbruto5_orcapro = moeda(floatval($torca["VLBRUTO5_ORCAPRO"]), 2, false);
    
    $perdesc_orcapro = floatval($torca["PERDESC_ORCAPRO"]);
    $perdesc2_orcapro = floatval($torca["PERDESC2_ORCAPRO"]);
    $perdesc3_orcapro = floatval($torca["PERDESC3_ORCAPRO"]);
    $perdesc4_orcapro = floatval($torca["PERDESC4_ORCAPRO"]);
    $perdesc5_orcapro = floatval($torca["PERDESC5_ORCAPRO"]);
    if ($perdesc_orcapro > 0) {
      $perdesc_orcapro = moeda($perdesc_orcapro, 4, false);
    }
    if ($perdesc2_orcapro > 0) {
      $perdesc2_orcapro = moeda($perdesc2_orcapro, 4, false);
    }
    if ($perdesc3_orcapro > 0) {
      $perdesc3_orcapro = moeda($perdesc3_orcapro, 4, false);
    }
    if ($perdesc4_orcapro > 0) {
      $perdesc4_orcapro = moeda($perdesc4_orcapro, 4, false);
    }
    if ($perdesc5_orcapro > 0) {
      $perdesc5_orcapro = moeda($perdesc5_orcapro, 4, false);
    }

    $vludesc_orcapro = moeda(floatval($torca["VLUDESC_ORCAPRO"]), 2, false);
    $vludesc2_orcapro = moeda(floatval($torca["VLUDESC2_ORCAPRO"]), 2, false);
    $vludesc3_orcapro = moeda(floatval($torca["VLUDESC3_ORCAPRO"]), 2, false);
    $vludesc4_orcapro = moeda(floatval($torca["VLUDESC4_ORCAPRO"]), 2, false);
    $vludesc5_orcapro = moeda(floatval($torca["VLUDESC5_ORCAPRO"]), 2, false);
    $vltdesc_orcapro = moeda(floatval($torca["VLTDESC_ORCAPRO"]), 2, false);
    $vltdesc2_orcapro = moeda(floatval($torca["VLTDESC2_ORCAPRO"]), 2, false);
    $vltdesc3_orcapro = moeda(floatval($torca["VLTDESC3_ORCAPRO"]), 2, false);
    $vltdesc4_orcapro = moeda(floatval($torca["VLTDESC4_ORCAPRO"]), 2, false);
    $vltdesc5_orcapro = moeda(floatval($torca["VLTDESC5_ORCAPRO"]), 2, false);
    $vuliq_orcapro = moeda(floatval($torca["VLUT_ORCAPRO"]), 2, false);
    $vuliq2_orcapro = moeda(floatval($torca["VLUT2_ORCAPRO"]), 2, false);
    $vuliq3_orcapro = moeda(floatval($torca["VLUT3_ORCAPRO"]), 2, false);
    $vuliq4_orcapro = moeda(floatval($torca["VLUT4_ORCAPRO"]), 2, false);
    $vuliq5_orcapro = moeda(floatval($torca["VLUT5_ORCAPRO"]), 2, false);
    $vlliq_orcapro = moeda(floatval($torca["VLLIQ_ORCAPRO"]), 2, false);
    $vlliq2_orcapro = moeda(floatval($torca["VLLIQ2_ORCAPRO"]), 2, false);
    $vlliq3_orcapro = moeda(floatval($torca["VLLIQ3_ORCAPRO"]), 2, false);
    $vlliq4_orcapro = moeda(floatval($torca["VLLIQ4_ORCAPRO"]), 2, false);
    $vlliq5_orcapro = moeda(floatval($torca["VLLIQ5_ORCAPRO"]), 2, false);
    $vlfrete_orcapro = moeda(floatval($torca["VLFRETE_ORCAPRO"]), 2, false);
    $vlfrete2_orcapro = moeda(floatval($torca["VLFRETE2_ORCAPRO"]), 2, false);
    $vlfrete3_orcapro = moeda(floatval($torca["VLFRETE3_ORCAPRO"]), 2, false);
    $vlfrete4_orcapro = moeda(floatval($torca["VLFRETE4_ORCAPRO"]), 2, false);
    $vlfrete5_orcapro = moeda(floatval($torca["VLFRETE5_ORCAPRO"]), 2, false);
    $vlufrete_orcapro = floatval($torca["VLUFRETE_ORCAPRO"]);
    $vlufrete2_orcapro = floatval($torca["VLUFRETE2_ORCAPRO"]);
    $vlufrete3_orcapro = floatval($torca["VLUFRETE3_ORCAPRO"]);
    $vlufrete4_orcapro = floatval($torca["VLUFRETE4_ORCAPRO"]);
    $vlufrete5_orcapro = floatval($torca["VLUFRETE5_ORCAPRO"]);
    $frete1 = floatval($torca["VLFRETE_ORCAPRO"]);
    $frete2 = floatval($torca["VLFRETE2_ORCAPRO"]);
    $frete3 = floatval($torca["VLFRETE3_ORCAPRO"]);
    $frete4 = floatval($torca["VLFRETE4_ORCAPRO"]);
    $frete5 = floatval($torca["VLFRETE5_ORCAPRO"]);
    $vlt_orcapro = floatval($torca["VLT_ORCAPRO"]);
    $vlt2_orcapro = floatval($torca["VLT2_ORCAPRO"]);
    $vlt3_orcapro = floatval($torca["VLT3_ORCAPRO"]);
    $vlt4_orcapro = floatval($torca["VLT4_ORCAPRO"]);
    $vlt5_orcapro = floatval($torca["VLT5_ORCAPRO"]);
    $vlt_orcapro = moeda($vlt_orcapro, 2, false);
    $vlt2_orcapro = moeda($vlt2_orcapro, 2, false);
    $vlt3_orcapro = moeda($vlt3_orcapro, 2, false);
    $vlt4_orcapro = moeda($vlt4_orcapro, 2, false);
    $vlt5_orcapro = moeda($vlt5_orcapro, 2, false);
  }
  $cor_orcapro = mb_convert_encoding($torca["COR_ORCAPRO"], "windows-1252");
  $corgrava_orcapro = mb_convert_encoding($torca["CORGRAVA_ORCAPRO"], "windows-1252");
  $unity_orcapro = $torca["UNITY_ORCAPRO"];
  $icone_pro = trim($torca["ICONE_PRO"]);
  $bloco = array(
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
    array("","","",""),
  );
  if ($obs_orcapro != "") {
    //if ($chave_repr == "007924") {
    //if ($chave_repr == "037249") {
    if ($richtext_reprcfg) {
      $decupada = $obs_orcapro;
      for ($i = 0; $i <= 9; $i++) {
        if (strlen($decupada) > 0) { 
          $colchete = -1;
          $chave = -1;
          if (strpos($decupada, '[') !== false) {
            $colchete = strpos($decupada, '[');
          }
          if (strpos($decupada, '{') !== false) {
            $chave = strpos($decupada, '{');
          }
          if ($colchete > (-1)) {
            if ($colchete < $chave || $chave == (-1)) {
              $bloco1 = '';
              $bloco2 = '';
              $bloco3 = '';
              $bloco1 = substr($decupada, 0, strpos($decupada, '['));
              $decupada = substr($decupada, strpos($decupada, '[') + 1);
              $bloco2 = substr($decupada, 0, strpos($decupada, ']'));
              $decupada = substr($decupada, strpos($decupada, ']') + 1);
              $bloco[$i][0] = $bloco1;
              $bloco[$i][1] = $bloco2;
              $bloco[$i][2] = $bloco3;
              $bloco[$i][3] = 'ffff00';
            }
          }
          if ($chave > (-1)) {
            if ($chave < $colchete || $colchete == (-1)) {
              $bloco1 = '';
              $bloco2 = '';
              $bloco3 = '';
              $bloco1 = substr($decupada, 0, strpos($decupada, '{'));
              $decupada = substr($decupada, strpos($decupada, '{') + 1);
              $bloco2 = substr($decupada, 0, strpos($decupada, '}'));
              $decupada = substr($decupada, strpos($decupada, '}') + 1);
              $bloco[$i][0] = $bloco1;
              $bloco[$i][1] = $bloco2;
              $bloco[$i][2] = $bloco3;
              $bloco[$i][3] = '00ff00';
            }        
          }
          if ($colchete == (-1) && $chave == (-1)) {
            $bloco1 = substr($decupada, 0);
            $bloco2 = '';
            $bloco3 = '';            
            $decupada = '';
            $bloco[$i][0] = $bloco1;
            $bloco[$i][1] = $bloco2;
            $bloco[$i][2] = $bloco3;
            $bloco[$i][3] = 'ffffff';
          }
        }
      }
    }
    else {
      $descr2_pro = $descr2_pro . PHP_EOL . $obs_orcapro;
    }    
  }
  if ($especial_orcapro) {
    $strsql2 = "
    SELECT 
    CHAVE_RESP_ORCAPROPRJ
    ,IMG_ORCAPROPRJ 
    FROM 
    TORCAPROPRJ 
    WHERE 
    CHAVE_ORCAPRO = :VCHAVE_ORCAPRO AND 
    CAIXA_ORCAPROPRJ = 'CADASTRADO'
    ORDER BY CHAVE_ORCAPROPRJ";
    $qorcaproprj = $pdo_empresa->prepare($strsql2);
    $qorcaproprj->bindParam(":VCHAVE_ORCAPRO", $chave_orcapro);
    $qorcaproprj->execute();
    if ($torcaproprj = $qorcaproprj->fetch(PDO::FETCH_ASSOC)) {
      $icone_pro = trim($torcaproprj["IMG_ORCAPROPRJ"]);
      if (vazio($icone_pro)) {
        $chave_resp_orcaproprj = trim($torcaproprj["CHAVE_RESP_ORCAPROPRJ"]);
        $strsql2 = "
        SELECT 
        CHAVE_RESP_ORCAPROPRJ
        ,IMG_ORCAPROPRJ 
        FROM 
        TORCAPROPRJ 
        WHERE 
        CHAVE_ORCAPRO = :VCHAVE_ORCAPRO AND 
        CAIXA_ORCAPROPRJ = 'CADASTRADO'
        ORDER BY CHAVE_ORCAPROPRJ";
        $qorcaproprj = $pdo_empresa->prepare($strsql2);
        $qorcaproprj->bindParam(":VCHAVE_ORCAPRO", $chave_resp_orcaproprj);
        $qorcaproprj->execute();
        if ($torcaproprj = $qorcaproprj->fetch(PDO::FETCH_ASSOC)) {
          $icone_pro = trim($torcaproprj["IMG_ORCAPROPRJ"]);
        }
      }
    }
  }
  else {
    $strsql2 = "
    SELECT 
    CHAVE_PROIMG
    ,URL40_PROIMG
    ,URL120_PROIMG
    ,URL_PROIMG
    FROM 
    TPROIMG 
    WHERE 
    CHAVE_PRO = :VCHAVE_PRO AND 
    TIPOARQ_PROIMG = 'IMAGEM' AND 
    HOME_PROIMG = 1 AND 
    CAIXA_PROIMG = 'CADASTRADO'
    ORDER BY CHAVE_PROIMG";
    $qproimg = $pdo_empresa->prepare($strsql2);
    $qproimg->bindParam(":VCHAVE_PRO", $chave_pro);
    $qproimg->execute();
    if ($tproimg = $qproimg->fetch(PDO::FETCH_ASSOC)) {
      $icone_pro = $tproimg["URL120_PROIMG"];
    }
    if ($chave_proimg != "" && $chave_proimg != "0") {
      $chave_proimg = padraol($chave_proimg, 6, "0");
      $strsql2 = "
      SELECT 
      CHAVE_PROIMG
      ,URL40_PROIMG
      ,URL120_PROIMG
      ,URL_PROIMG 
      FROM 
      TPROIMG 
      WHERE 
      CHAVE_PRO = :VCHAVE_PRO AND 
      CHAVE_PROIMG = :VCHAVE_PROIMG AND 
      TIPOARQ_PROIMG = 'IMAGEM' AND 
      CAIXA_PROIMG = 'CADASTRADO' 
      ORDER BY CHAVE_PROIMG";
      $qproimg = $pdo_empresa->prepare($strsql2);
      $qproimg->bindParam(":VCHAVE_PRO", $chave_pro);
      $qproimg->bindParam(":VCHAVE_PROIMG", $chave_proimg);
      $qproimg->execute();
      if ($tproimg = $qproimg->fetch(PDO::FETCH_ASSOC)) {
        $icone_pro = $tproimg["URL120_PROIMG"];
      }
    }
  }
  if (!vazio($icone_pro)) {
    if (!strpos(mb_strtoupper($icone_pro), '.JPG')) {
      if (!strpos(mb_strtoupper($icone_pro), '.JPEG')) {
        if (!strpos(mb_strtoupper($icone_pro), '.PNG')) {
          if (!strpos(mb_strtoupper($icone_pro), '.GIF')) {
            $icone_pro = "";
          }
        }
      }
    }
  }
  if ($icone_pro == "") {
    $icone_pro = "/corp2/media/semimagem180_moldura.png";
  }
  if (strpos($icone_pro, $pathfisicoimgpro_var) >= 0) {
    $icone_pro = str_replace($pathfisicoimgpro_var, $pathwebimgpro_var, $icone_pro);
  }
  if (strpos($icone_pro, "\\") >= 0) {
    $icone_pro = str_replace("\\", "/", $icone_pro);
  }
  $icone_pro = "https://unitycorp.com.br" . $icone_pro;
  if ($chave_moeda != "01" && $chave_moeda != "" && $vlcotacao_orca != 1) {
    $qtd_orcapro = formatalocal($qtd_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd2_orcapro = formatalocal($qtd2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd3_orcapro = formatalocal($qtd3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd4_orcapro = formatalocal($qtd4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $qtd5_orcapro = formatalocal($qtd5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu_orcapro = formatalocal($vlu_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu2_orcapro = formatalocal($vlu2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu3_orcapro = formatalocal($vlu3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu4_orcapro = formatalocal($vlu4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlu5_orcapro = formatalocal($vlu5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto_orcapro = formatalocal($vlbruto_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto2_orcapro = formatalocal($vlbruto2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto3_orcapro = formatalocal($vlbruto3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto4_orcapro = formatalocal($vlbruto4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlbruto5_orcapro = formatalocal($vlbruto5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc_orcapro = formatalocal($perdesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc2_orcapro = formatalocal($perdesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc3_orcapro = formatalocal($perdesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc4_orcapro = formatalocal($perdesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $perdesc5_orcapro = formatalocal($perdesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc_orcapro = formatalocal($vludesc_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc2_orcapro = formatalocal($vludesc2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc3_orcapro = formatalocal($vludesc3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc4_orcapro = formatalocal($vludesc4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vludesc5_orcapro = formatalocal($vludesc5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq_orcapro = formatalocal($vuliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq2_orcapro = formatalocal($vuliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq3_orcapro = formatalocal($vuliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq4_orcapro = formatalocal($vuliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vuliq5_orcapro = formatalocal($vuliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq_orcapro = formatalocal($vlliq_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq2_orcapro = formatalocal($vlliq2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq3_orcapro = formatalocal($vlliq3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq4_orcapro = formatalocal($vlliq4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlliq5_orcapro = formatalocal($vlliq5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete_orcapro = formatalocal($vlfrete_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete2_orcapro = formatalocal($vlfrete2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete3_orcapro = formatalocal($vlfrete3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete4_orcapro = formatalocal($vlfrete4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlfrete5_orcapro = formatalocal($vlfrete5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt_orcapro = formatalocal($vlt_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt2_orcapro = formatalocal($vlt2_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt3_orcapro = formatalocal($vlt3_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt4_orcapro = formatalocal($vlt4_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
    $vlt5_orcapro = formatalocal($vlt5_orcapro, ".", $separadormilhar_moeda, ",", $separadordecimal_moeda, "NUMERO");
  }
  $espacamento = 8;
  $descrchr13 = $descr2_pro;
  if (substr($descrchr13, strlen($descrchr13) - 1, 1) != chr(13)) {
    $descrchr13 .= chr(13);
  }
  while (strpos($descrchr13, chr(13)) > 0) {
    $descr = substr($descrchr13, 0, strpos($descrchr13, chr(13)));
    $espacamento += 3.4;
    while (strlen($descr) > 120) {
      $espacamento += 3.4;
      $descr = substr($descr, 120);
    }
    $descrchr13 = substr($descrchr13, strpos($descrchr13, chr(13)) + 1);
  }
  $espacamento = round($espacamento);

  if (!vazio($corgrava_orcapro)) {
    $espacamento += 4;
  }
  if (!vazio($ncm_pro)) {
    $espacamento += 4;
  }
  if ($unity_orcapro == true) {
    $espacamento += 4;
  }
  $espacamento += 4;
  if ($qtd_orcapro > 0) {
    $espacamento += 4;
  }
  if ($qtd2_orcapro > 0) {
    $espacamento += 4;
  }
  if ($qtd3_orcapro > 0) {
    $espacamento += 4;
  }
  if ($qtd4_orcapro > 0) {
    $espacamento += 4;
  }
  if ($qtd5_orcapro > 0) {
    $espacamento += 4;
  }
  if ($espacamento <= 40) { // era 50
    $espacamento = 40;
  }
  if ((intval($pdf->GetY()) + $espacamento) > 263) {
    $contetq = 0;
  }
  if ($contetq == 0) {
    $pdf->AddPage();
    $pdf->Ln(0);
    $contetq = 1;
  }
  $pdf->SetFont('Segoe UI', '', 8);
  $y = $pdf->GetY();
  $pdf->SetFillColor(204, 204, 204); //#CCCCCC
  $pdf->Rect(10, $y, 190, 6, 'F');
  $pdf->Ln(8);
  $y = $pdf->GetY();
  $icone_pro = str_replace(" ", "%20", $icone_pro);
  $pdf->Cell(42, 42, '', 0, 0, 'L');
  $pdf->Image($icone_pro, 11, $y + 0.5, 40, 40);
  $pdf->Cell(62, 4, "Ref: {$cod_pro}", 0, 0, 'L');
  $pdf->Cell(85, 4, "{$qrl_pro_cor} {$cor_orcapro}", 0, 0, 'L');
  $pdf->Ln(6);
  $pdf->Cell(42, 3.4, "", 0, 0, 'L');  
  //if ($chave_repr == "007924") {
  //if ($chave_repr == "037249") {
  if ($richtext_reprcfg) {    
    $pdf->MultiCell(147, 3.4, $descr2_pro, 0, 'L', false);    
    for ($i = 0; $i <= 9; $i++) {
      if ($bloco[$i][0] != "") {
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell(147, 3.4, trim($bloco[$i][0]), 0, 'L', false); // First part of the MultiCell with no background
      }
      if ($bloco[$i][1] != "") {
        $largura = 147;
        if ((strlen($bloco[$i][1]) * 2) > 147) {
          $largura = 147;
        }
        else {
          if (strlen($bloco[$i][1]) <= 10) {
            $largura = strlen($bloco[$i][1]) * 3;
          }
          else {
            $largura = strlen($bloco[$i][1]) * 2;
          }
          if ($largura > 147) {
            $largura = 147;
          }
        }
        if ($bloco[$i][3] == "ffff00") {          
          $pdf->SetFillColor(255, 255, 0); // Set fill color for the partial background            
        }
        if ($bloco[$i][3] == "00ff00") {
          $pdf->SetFillColor(0, 255, 0); // Set fill color for the partial background            
        }
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell($largura, 3.4, $bloco[$i][1], 0, 'L', true); // Second part with background
      }
      $pdf->SetFillColor(255, 255, 255); // White background or default
      if ($bloco[$i][2] != "") {
        $pdf->Cell(42, 3.4, "", 0, 0, 'L');
        $pdf->MultiCell(147, 3.4, trim($bloco[$i][2]), 0, 'L', false);
      }      
    }
  }
  else {
    $pdf->MultiCell(147, 3.4, $descr2_pro, 0, 'L', false);
  }    
  $pdf->Ln(0);
  if (strlen($descr2_pro) <= 120) {
    $pdf->Ln(2);
  }
  if (!vazio($corgrava_orcapro)) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(147, 4, "Grava��o: {$corgrava_orcapro}", 0, 0, 'L');
    $pdf->Ln(6);
  }
  if (!vazio($ncm_pro)) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(148, 4, "NCM: {$ncm_pro}", 0, 0, 'L');
    $pdf->Ln(6);
  }
  if ($unity_orcapro == true) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $xx = $pdf->GetX();
    $yy = $pdf->GetY();
    $pdf->SetXY($xx + 1, $yy);
    $xx = $pdf->GetX();
    $yy = $pdf->GetY();
    $pdf->Image("https://unitycorp.com.br/corp2/media/unity16x16.png");
    $pdf->SetXY($xx + 6, $yy);
    $pdf->Cell(140, 4, "Produto sugerido pela Unity Brindes", 0, 0, 'L');
    $pdf->Ln(6);
  }
  if (vazio($ncm_pro) && vazio($corgrava_orcapro) && $unity_orcapro == false) {
    $pdf->Ln(2.5);
  }
  //********************
  //********************
  //**** Unit�rio/Total ou Subtotal/Total
  //********************
  //********************
  if (vazio($chave_modeloorcaimp) || ($chave_modeloorcaimp == "1" || $chave_modeloorcaimp == "4")) {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(30, 4, $qrl_pro_vlu, 0, 0, 'R');
    if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
      $pdf->Cell(30, 4, $qrl_pro_frete, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu2_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu3_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu4_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(30, 4, $vlu5_orcapro, 0, 0, 'R');
      if (($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0) {
        $pdf->Cell(30, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      }
      $pdf->Cell(30, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Total ou Subtotal/Total
  //********************
  //********************

  //********************
  //********************
  //**** Unit�rio/Bruto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "2") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlu, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
    $pdf->Cell(20, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Bruto/Total
  //********************
  //********************

  //********************
  //********************
  //**** Unit�rio/Bruto/Descto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "3") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(15, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(16, 4, $qrl_pro_vlu, 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_bruto, 0, 0, 'R');
    $pdf->Cell(15, 4, $qrl_pro_perdesc, 0, 0, 'R');
    $pdf->Cell(18, 4, $qrl_pro_vludesc, 0, 0, 'R');
    $pdf->Cell(16, 4, $qrl_pro_vluliq, 0, 0, 'R');
    $pdf->Cell(18, 4, iif(($frete1 + $frete2 + $frete3 + $frete4 + $frete5) > 0, $qrl_pro_frete, ""), 0, 0, 'R');
    $pdf->Cell(20, 4, $qrl_pro_vlt, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete1 > 0, $vlfrete_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto2_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc2_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc2_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq2_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete2 > 0, $vlfrete2_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto3_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc3_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc3_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq3_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete3 > 0, $vlfrete3_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto4_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc4_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc4_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq4_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete4 > 0, $vlfrete4_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(15, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(20, 4, $vlbruto5_orcapro, 0, 0, 'R');
      $pdf->Cell(15, 4, $perdesc5_orcapro . "%", 0, 0, 'R');
      $pdf->Cell(18, 4, $vludesc5_orcapro, 0, 0, 'R');
      $pdf->Cell(16, 4, $vuliq5_orcapro, 0, 0, 'R');
      $pdf->Cell(18, 4, iif($frete5 > 0, $vlfrete5_orcapro, ""), 0, 0, 'R');
      $pdf->Cell(20, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  //********************
  //********************
  //**** EOF Unit�rio/Bruto/Descto/Total
  //********************
  //********************
  if ($chave_modeloorcaimp == "5") {
    $pdf->Cell(42, 4, "", 0, 0, 'L');
    $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
    $pdf->Cell(20, 4, $qrl_pro_qtd, 0, 0, 'R');
    $pdf->Cell(40, 4, $qrl_pro_vlu . " " . $qrl_pro_comfrete, 0, 0, 'R');
    $pdf->Cell(40, 4, $qrl_pro_vlt . " " . $qrl_pro_comfrete, 0, 0, 'R');
    $pdf->Ln(4);
    if ($qtd_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd2_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd2_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu2_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt2_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd3_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd3_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu3_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt3_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd4_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd4_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu4_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt4_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
    if ($qtd5_orcapro > 0) {
      $pdf->Cell(42, 4, "", 0, 0, 'L');
      $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
      $pdf->Cell(20, 4, $qtd5_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlu5_orcapro, 0, 0, 'R');
      $pdf->Cell(40, 4, $vlt5_orcapro, 0, 0, 'R');
      $pdf->Ln(4);
    }
  }
  $contetq = $contetq + 1;
  if ($y != 0) {
    $pdf->SetXY(0, $y + $espacamento);
    $pdf->Ln(5);
  }
  //rs.movenext()
}
$pdf->AddPage();
if ($imptotal_orca == true) {
  $pdf->SetFont('Segoe UI', 'B', 10);
  $pdf->Cell(130, 4, $qrl_orca_titulototal, 0, 0, 'L');
  $pdf->Ln(5);
  $pdf->SetFillColor(204, 204, 204); //#CCCCCC   
  $y = $pdf->GetY();
  $pdf->Rect(10, $y, 190, 6, 'F');
  $pdf->Ln(8);
  $pdf->SetFont('Segoe UI', 'B', 8);
  $pdf->Cell(10, 4, $qrl_pro_opc, 0, 0, 'L');
  if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
    $pdf->Cell(30, 4, $qrl_orca_bruto, 0, 0, 'R');
  }
  if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
    $pdf->Cell(30, 4, $qrl_orca_perdesc, 0, 0, 'R');
    $pdf->Cell(30, 4, $qrl_orca_vltdesc, 0, 0, 'R');
  }
  $pdf->Cell(30, 4, $qrl_orca_subtotal, 0, 0, 'R');
  $pdf->Cell(30, 4, iif(($orca_frete1 + $orca_frete2 + $orca_frete3 + $orca_frete4 + $orca_frete5) > 0, $qrl_orca_frete, ""), 0, 0, 'R');
  $pdf->Cell(30, 4, $qrl_orca_total, 0, 0, 'R');
  $pdf->Ln(4);
  $pdf->SetFont('Segoe UI', '', 8);
  if ($vlbruto_orca > 0) {
    $pdf->Cell(10, 4, $qrl_pro_opc1, 0, 0, 'C');
    if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
      $pdf->Cell(30, 4, $vlbruto_orca, 0, 0, 'R');
    }
    if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
      $pdf->Cell(30, 4, $perdesc_orca, 0, 0, 'R');
      $pdf->Cell(30, 4, $vldesc_orca, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $vlliq_orca, 0, 0, 'R');
    $pdf->Cell(30, 4, iif($orca_frete1 > 0, $vlfre_orca, ""), 0, 0, 'R');
    $pdf->Cell(30, 4, $vlrec_orca, 0, 0, 'R');
    $pdf->Ln(4);
  }
  if ($vlbruto2_orca > 0) {
    $pdf->Cell(10, 4, $qrl_pro_opc2, 0, 0, 'C');
    if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
      $pdf->Cell(30, 4, $vlbruto2_orca, 0, 0, 'R');
    }
    if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
      $pdf->Cell(30, 4, $perdesc2_orca, 0, 0, 'R');
      $pdf->Cell(30, 4, $vldesc2_orca, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $vlliq2_orca, 0, 0, 'R');
    $pdf->Cell(30, 4, iif($orca_frete2 > 0, $vlfre2_orca, ""), 0, 0, 'R');
    $pdf->Cell(30, 4, $vlrec2_orca, 0, 0, 'R');
    $pdf->Ln(4);
  }
  if ($vlbruto3_orca > 0) {
    $pdf->Cell(10, 4, $qrl_pro_opc3, 0, 0, 'C');
    if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
      $pdf->Cell(30, 4, $vlbruto3_orca, 0, 0, 'R');
    }
    if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
      $pdf->Cell(30, 4, $perdesc3_orca, 0, 0, 'R');
      $pdf->Cell(30, 4, $vldesc3_orca, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $vlliq3_orca, 0, 0, 'R');
    $pdf->Cell(30, 4, iif($orca_frete3 > 0, $vlfre3_orca, ""), 0, 0, 'R');
    $pdf->Cell(30, 4, $vlrec3_orca, 0, 0, 'R');
    $pdf->Ln(4);
  }
  if ($vlbruto4_orca > 0) {
    $pdf->Cell(10, 4, $qrl_pro_opc4, 0, 0, 'C');
    if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
      $pdf->Cell(30, 4, $vlbruto4_orca, 0, 0, 'R');
    }
    if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
      $pdf->Cell(30, 4, $perdesc4_orca, 0, 0, 'R');
      $pdf->Cell(30, 4, $vldesc4_orca, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $vlliq4_orca, 0, 0, 'R');
    $pdf->Cell(30, 4, iif($orca_frete4 > 0, $vlfre4_orca, ""), 0, 0, 'R');
    $pdf->Cell(30, 4, $vlrec4_orca, 0, 0, 'R');
    $pdf->Ln(4);
  }
  if ($vlbruto5_orca > 0) {
    $pdf->Cell(10, 4, $qrl_pro_opc5, 0, 0, 'C');
    if ($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") {
      $pdf->Cell(30, 4, $vlbruto5_orca, 0, 0, 'R');
    }
    if (($chave_modeloorcaimp != "4" && $chave_modeloorcaimp != "5") && ($perdesc_orca != "0,0000%")) {
      $pdf->Cell(30, 4, $perdesc5_orca, 0, 0, 'R');
      $pdf->Cell(30, 4, $vldesc5_orca, 0, 0, 'R');
    }
    $pdf->Cell(30, 4, $vlliq5_orca, 0, 0, 'R');
    $pdf->Cell(30, 4, iif($orca_frete5 > 0, $vlfre5_orca, ""), 0, 0, 'R');
    $pdf->Cell(30, 4, $vlrec5_orca, 0, 0, 'R');
    $pdf->Ln(4);
  }
  $pdf->Ln(4);
}
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(130, 4, $qrl_endfat, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFillColor(204, 204, 204); //#CCCCCC
$y = $pdf->GetY();
$pdf->Rect(10, $y, 190, 6, 'F');
$pdf->Ln(8);
$pdf->SetFont('Segoe UI', '', 8);
$pdf->Cell(30, 3.3, $qrl_nome, 0, 0, 'R');
$pdf->Cell(130, 3.3, $nome_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_fantasia, 0, 0, 'R');
$pdf->Cell(130, 3.3, $fantasia_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_end, 0, 0, 'R');
$pdf->Cell(130, 3.3, $end_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_num, 0, 0, 'R');
$pdf->Cell(130, 3.3, $num_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_compl, 0, 0, 'R');
$pdf->Cell(130, 3.3, $compl_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_bairro, 0, 0, 'R');
$pdf->Cell(130, 3.3, $bairro_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cid, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cid_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_uf, 0, 0, 'R');
$pdf->Cell(130, 3.3, $uf_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cep, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cep_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_tel, 0, 0, 'R');
$pdf->Cell(130, 3.3, iif($fonearea_for != "", $fonearea_for . " " . $fone_for, $fone_for), 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, "CPF/CNPJ:", 0, 0, 'R');
$pdf->Cell(130, 3.3, $cpfcnpj_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, "RG/IE:", 0, 0, 'R');
$pdf->Cell(130, 3.3, $rgie_for, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3, $qrl_emailnfe, 0, 0, 'R');
$pdf->MultiCell(130, 3, $emailxml_for, 0, 'L', false);
$pdf->Ln(3);
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(130, 4, $qrl_endent, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFillColor(204, 204, 204); //#CCCCCC
$y = $pdf->GetY();
$pdf->Rect(10, $y, 190, 6, 'F');
$pdf->Ln(8);
$pdf->SetFont('Segoe UI', '', 8);
$pdf->Cell(30, 3.3, $qrl_nomeent, 0, 0, 'R');
$pdf->Cell(130, 3.3, $nomeent_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_end, 0, 0, 'R');
$pdf->Cell(130, 3.3, $endentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_num, 0, 0, 'R');
$pdf->Cell(130, 3.3, $numentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_compl, 0, 0, 'R');
$pdf->Cell(130, 3.3, $complentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_bairro, 0, 0, 'R');
$pdf->Cell(130, 3.3, $bairroentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cid, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cidentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_uf, 0, 0, 'R');
$pdf->Cell(130, 3.3, $ufentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cep, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cepentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_tel, 0, 0, 'R');
$pdf->Cell(130, 3.3, iif($foneareaentreg_orca != "", $foneareaentreg_orca . " " . $foneentreg_orca, $foneentreg_orca), 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, "CPF/CNPJ:", 0, 0, 'R');
$pdf->Cell(130, 3.3, $cpfcnpjentreg_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Ln(3);
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(130, 4, $qrl_endcob, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFillColor(204, 204, 204); //#CCCCCC
$y = $pdf->GetY();
$pdf->Rect(10, $y, 190, 6, 'F');
$pdf->Ln(8);
$pdf->SetFont('Segoe UI', '', 8);
$pdf->Cell(30, 3.3, $qrl_end, 0, 0, 'R');
$pdf->Cell(130, 3.3, $endcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_num, 0, 0, 'R');
$pdf->Cell(130, 3.3, $numcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_compl, 0, 0, 'R');
$pdf->Cell(130, 3.3, $complcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_bairro, 0, 0, 'R');
$pdf->Cell(130, 3.3, $bairrocob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cid, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cidcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_uf, 0, 0, 'R');
$pdf->Cell(130, 3.3, $ufcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_cep, 0, 0, 'R');
$pdf->Cell(130, 3.3, $cepcob_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_tel, 0, 0, 'R');
$pdf->Cell(130, 3.3, iif($foneareacob_orca != "", $foneareacob_orca . " " . $fonecob_orca, $fonecob_orca), 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3, $qrl_email, 0, 0, 'R');
$pdf->MultiCell(130, 3, $emailcob_orca, 0, 'L', false);
$pdf->Ln(3);
//********************
//********************
//**** CONDI��ES E OBS GERAIS
//********************
//********************
$pdf->SetFont('Segoe UI', 'B', 10);
$pdf->Cell(130, 4, $qrl_obs, 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFillColor(204, 204, 204); //#CCCCCC
$y = $pdf->GetY();
$pdf->Rect(10, $y, 190, 6, 'F');
$pdf->Ln(8);
$pdf->SetFont('Segoe UI', '', 8);
if ($chave_moeda != "01" && $chave_moeda != "" && $vlcotacao_orca != 1) {
  $pdf->Cell(30, 3.3, $qrl_cotacao, 0, 0, 'R');
  $pdf->Cell(130, 3.3, $qrl_cotacao2, 0, 0, 'L');
  $pdf->Ln(3);
}
$pdf->Cell(30, 3.3, $qrl_pagto, 0, 0, 'R');
$pdf->Cell(130, 3.3, $condpagto_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_entrega, 0, 0, 'R');
$pdf->Cell(130, 3.3, $entrega_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Cell(30, 3.3, $qrl_frete, 0, 0, 'R');
$pdf->Cell(130, 3.3, $frete_orca, 0, 0, 'L');
$pdf->Ln(3);
if ($freteremovido_orca = false) {
  $pdf->Cell(30, 3.3, "", 0, 0, 'R');
  $pdf->Cell(130, 3.3, $obscalcfrete, 0, 0, 'L');
  $pdf->Ln(3);
}
$pdf->Cell(30, 3.3, $qrl_validade, 0, 0, 'R');
$pdf->Cell(130, 3.3, $validade_orca, 0, 0, 'L');
$pdf->Ln(3);
$pdf->Ln(3);
$pdf->MultiCell(160, 3, $obs_orca, 0, 'L', false);
$pdf->Ln(3);
//$pdf->Output('F', "GUIADOMESTICO.PDF");
$pdf->Output('D', 'GuiaDomestico.pdf');

//********************
//********************
//**** MARCA COMO ENIVADO
//********************
//********************
$marcar = false;
$chave_orcasit = "";
$descr_sitorca = "";
if ($chave_orca != "" && $origem == "") {
  $strsql = "
  SELECT 
  TOP 1
  TORCASIT.CHAVE_ORCASIT
  ,TORCASIT.CHAVE_SITORCA
  ,TORCASIT.CHECADO_ORCASIT 
  FROM 
  TORCASIT 
  LEFT JOIN TORCA ON TORCASIT.CHAVE_ORCA = TORCA.CHAVE_ORCA 
  WHERE 
  TORCASIT.CHAVE_ORCA = :VCHAVE_ORCA AND 
  TORCASIT.CHAVE_SITORCA = 3 AND 
  TORCASIT.CAIXA_ORCASIT = 'CADASTRADO' AND 
  TORCA.CHAVE_REPR = :VCHAVE_REPR AND 
  TORCA.CAIXA_ORCA = 'CADASTRADO'
  ORDER BY TORCASIT.CHAVE_ORCASIT DESC
  ";
  $qorcasit = $pdo_empresa->prepare($strsql);
  $qorcasit->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorcasit->bindParam(":VCHAVE_REPR", $chave_repr);
  $qorcasit->execute();
  if ($torcasit = $qorcasit->fetch(PDO::FETCH_ASSOC)) {    
    if ($torcasit["CHECADO_ORCASIT"] == 0) {
      $chave_orcasit = $torcasit["CHAVE_ORCASIT"];
      $marcar = true;
    }
  }
  else {
    $marcar = true;
  }
}
if ($marcar == true && $chave_orcasit != "" && $origem == "") {
  $usuario_orcasit = substr("PORTAL:" . $nomerepr,0,50);
  $abre_diario = abre_diario_empresa('TORCASIT', 'CHAVE_ORCASIT', $chave_orcasit);
  $strsql = "
  UPDATE 
  TORCASIT 
  SET 
  DTA_ORCASIT = CAST(GETDATE() AS DATE)
  ,HRA_ORCASIT = CAST(GETDATE() AS TIME)
  ,USUARIO_ORCASIT = :VUSUARIO_ORCASIT
  ,CHECADO_ORCASIT = 1
  WHERE 
  TORCASIT.CHAVE_ORCASIT = :VCHAVE_ORCASIT AND
  TORCASIT.CHAVE_ORCA = :VCHAVE_ORCA 
  ";
  $qorcasit = $pdo_empresa->prepare($strsql);
  $qorcasit->bindParam(":VCHAVE_ORCASIT", $chave_orcasit);
  $qorcasit->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorcasit->bindParam(":VUSUARIO_ORCASIT", $usuario_orcasit);
  $qorcasit->execute();
  fecha_diario_empresa_portal("TORCASIT", "CHAVE_ORCASIT", $chave_orcasit, $abre_diario);
  $sit_orca = "NOVO";
  $strsql = "
  SELECT 
  TOP 1
  TORCASIT.CHAVE_ORCASIT
  ,TSITORCA.DESCR_SITORCA
  FROM 
  TORCASIT 
  LEFT JOIN TSITORCA ON TORCASIT.CHAVE_SITORCA = TSITORCA.CHAVE_SITORCA 
  WHERE 
  TORCASIT.CHAVE_ORCA = :VCHAVE_ORCA AND 
  TORCASIT.CHECADO_ORCASIT = 1 AND 
  TORCASIT.CAIXA_ORCASIT = 'CADASTRADO' 
  ORDER BY TORCASIT.ORDEM_ORCASIT DESC
  ";
  $qorcasit = $pdo_empresa->prepare($strsql);
  $qorcasit->bindParam(":VCHAVE_ORCA", $chave_orca);
  $qorcasit->execute();
  if ($torcasit = $qorcasit->fetch(PDO::FETCH_ASSOC)) {    
    $descr_sitorca = $torcasit["DESCR_SITORCA"];
  }
  else {
    $marcar = true;
  }  
//    if cstr(vchave_origemorca) = "11" then
//        DTFEEDBKREPR_ORCA = " & datatoaccess(date()) & ","
//        HRFEEDBKREPR_ORCA = " & horatoaccess(time()) & ","
//    end if
  $abre_diario = abre_diario_empresa('TORCA', 'CHAVE_ORCA', $chave_orca);
  $strsql = "
  UPDATE 
  TORCA 
  SET 
  ENVIOEXTRAPORTAL_ORCA = 1
  ,DTE_ORCA = CAST(GETDATE() AS DATE)
  WHERE 
  TORCA.CHAVE_ORCA = :VCHAVE_ORCA
  ";
  $qorca = $pdo_empresa->prepare($strsql);
  //$qorca->bindParam(":VDESCR_SITORCA", $descr_sitorca);
  $qorca->bindParam(":VCHAVE_ORCA", $chave_orca);  
  $qorca->execute();
  fecha_diario_empresa_portal("TORCA", "CHAVE_ORCA", $chave_orca, $abre_diario);
}
//********************
//********************
//**** EOF MARCA COMO ENIVADO
//********************
//********************
?>