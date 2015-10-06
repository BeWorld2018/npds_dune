<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Kill the Ereg by JPB on 24-01-2011 and cast MySql engine type        */
/* This version name NPDS Copyright (c) 2001-2015 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/************************************************************************/
/* Module-Install Version 1.1 - Mai 2005                                */
/* --------------------------                                           */
/* Copyright (c) 2005 Boris L'Ordi-D�panneur & Hotfirenet               */
/*                                                                      */
/* Version 1.2 - 22 Avril 2009                                          */
/* --------------------------                                           */
/*                                                                      */
/* Modifi� par jpb et phr pour le rendre compatible avec Evolution      */
/*                                                                      */
/* --------------------------                                           */
/* Version 2.0 - 30/08/2015 by jpb                                      */
/************************************************************************/

if (!stristr($_SERVER['PHP_SELF'],"admin.php")) { Access_Error(); }
$f_meta_nom ='modules';
$f_titre = adm_translate("Gestion, Installation Modules");
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit

// *****************************
// * Fonctions de l'installeur *
// *****************************

function nmig_copyright() {
      $clspin =' text-success';
      if ($ModInstall == "" && $ModDesinstall != "") {
      $clspin =' text-danger';
      }
   $display = '
   <br /><div class="text-center">
      <i class="fa fa-spinner fa-pulse '.$clspin.' "></i> NPDS Module Installer v2.0
   </div>';
   return $display;
}

// e1
function nmig_Start($name_module,$txtdeb) {
   include("header.php");
   global $ModInstall, $display;
   $display = '
   <div class="">';
   if (isset($txtdeb) && $txtdeb != "") {
      $display .= aff_langue($txtdeb);
   } else {
      $display .= '
      <p><strong>'.adm_translate("Bonjour et bienvenue dans l'installation automatique du module").' "'.$name_module.'"</strong></p>
      <p>'.adm_translate("Ce programme d'installation va configurer votre site internet pour utiliser ce module.").'</p>
      <p><em>'.adm_translate("Cliquez sur \"Etape suivante\" pour continuer.").'</em></p>';
   }
   $display .= '
   </div>
   <div style="text-align: center;">
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e2" class="btn btn-primary col-xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4">'.adm_translate("Etape suivante").'</a><br />
   </div>
   '.nmig_copyright();
}

// e2
function nmig_License($licence_file) {
   include("header.php");
   global $ModInstall, $display;
   $myfile = fopen($licence_file,"r");
   $licence_text = fread($myfile, filesize($licence_file));
   fclose($myfile);
   $display = '
   <div class="">
      <p><strong>'.adm_translate("L'utilisation de NPDS et des modules est soumise � l'acceptation des termes de la licence GNU/GPL :").'</strong></p>
      <div style="text-align: center;">
         <textarea class="form-control" name="licence" rows="12" readonly="readonly">'.htmlentities($licence_text).'</textarea>
         <br /><a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e3" class="btn btn-primary">'.adm_translate("Oui").'</a>&nbsp;<a href="admin.php?op=modules" class="btn btn-danger">'.adm_translate("Non").'</a><br />
      </div>
      '.nmig_copyright();
}

//e3

function nmig_AlertSql($sql,$tables) {
   include("header.php");
   global $ModInstall, $display, $NPDS_Prefix;
   $type_engine=(int)substr(mysql_get_server_info(), 0, 1);
   for ($i = 0; $i < count($sql); $i++) {
      for ($j = 0; $j < count($tables); $j++) {
         $sql[$i] = preg_replace("#$tables[$j]#i", $NPDS_Prefix.$tables[$j],$sql[$i]);
      }
      if ($type_engine>=5)
         $sql[$i] = preg_replace('#TYPE=MyISAM#i', 'ENGINE=MyISAM', $sql[$i]);
      $reqsql.='<code>'.$sql[$i].'</code><br />';
   }
   $display = '
   <div class="">
      <p><strong>'.adm_translate("Le programme d'installation va maintenant ex�cuter le script SQL pour configurer la base de donn�es MySql.").'</strong></p>
      <p>'.adm_translate("Si vous le souhaitez, vous pouvez ex�cuter ce script vous m�me, si vous souhaitez par exemple l'ex�cuter sur une autre base que celle du site. Dans ce cas, pensez � reparam�trer le fichier de configuration du module.").'</p>
      <p>'.adm_translate("Voici le script SQL :").'</p>
   </div>
   '.$reqsql.'
   <br />
   <div style="text-align: center;">
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e4" class="btn btn-primary">'.adm_translate("Configurer MySql").'</a>&nbsp;<a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e5" class="btn btn-danger">'.adm_translate("Sauter cette �tape").'</a><br />
   </div>
   <br />
   '.nmig_copyright();
}

// e4

function nmig_WriteSql($sql,$tables) {
   include("header.php");
   global $ModInstall, $display, $NPDS_Prefix;
   $type_engine=(int)substr(mysql_get_server_info(), 0, 1);

   $display = '
   <div class="">';
   for ($i = 0; $i < count($sql) && !isset($erreur); $i++) {
      for ($j = 0; $j < count($tables); $j++) {
         $sql[$i] = preg_replace("#$tables[$j]#i", $NPDS_Prefix.$tables[$j],$sql[$i]);
      }
      if ($type_engine>=5)
         $sql[$i] = preg_replace('#TYPE=MyISAM#i', 'ENGINE=MyISAM', $sql[$i]);
      sql_query($sql[$i]) or $erreur = sql_error();
   }
   if (isset($erreur)) {
      $display .= '
      <p class="text-danger">'.adm_translate("Une erreur est survenue lors de l'ex�cution du script SQL. Mysql a r�pondu :").'</p>
      <p><code>'.$erreur.'</code></p>
      <p>'.adm_translate("Veuillez l'ex�cuter manuellement via phpMyAdmin.").'</p>
      <p>'.adm_translate("Voici le script SQL :").'</p>';
      for ($i = 0; $i < count($sql); $i++) {
         $reqsql.='<code>'.$sql[$i].'</code><br />';
      }
      $display .= $reqsql;
      $display .= "<br />\n";
   } else {
      $display .= '<p class="text-success"><strong>'.adm_translate("La configuration de la base de donn�es MySql a r�ussie !").'</strong></p>';
   }
   $display .= '
   </div><div style="text-align: center;">
   <br /><a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e5" class="btn btn-primary">'.adm_translate("Etape suivante").'</a><br />
   </div><br />
   '.nmig_copyright();
}

// e5

function nmig_AlertConfig($list_fich) {
   include("header.php");
   global $ModInstall, $display;
   $display = '
   <div style="text-align: left;">
   <p><strong>'.adm_translate("Le programme d'installation va maintenant modifier le(s) fichier(s) suivant(s) :").'</p>';
   for ($i = 0; $i < count($list_fich[0]); $i++) {
      $display .= '<code>'.$list_fich[0][$i]."</code><br />\n";
   }
   $display .= '
   </div>
   <div style="text-align: center;"><br />';
   $display .= "<a href=\"admin.php?op=Module-Install&amp;ModInstall=".$ModInstall."&amp;nmig=e6\" class=\"rouge\">".adm_translate("Modifier le(s) fichier(s)")."</a><br />\n";
   $display .= "</div><br />\n";
   $display .= nmig_copyright();
}

// e6

function nmig_WriteConfig($list_fich,$try_Chmod) {
   include("header.php");
   global $ModInstall, $display;
   $writeAllFiles = 1;
   $display .= "<div style=\"text-align: left;\">\n";
   $file_created = 0;
   for ($i = 0; $i < count($list_fich[0]); $i++) {
      if (!file_exists($list_fich[0][$i])) {
         $file = fopen($list_fich[0][$i7], "w");
         fclose($file);
         $file_created = 1;
      }
      if ($list_fich[0][$i] == "admin/extend-modules.txt" || $list_fich[0][$i] == "modules/include/body_onload.inc") {
         $file = fopen($list_fich[0][$i], "r");
         $txtconfig = fread($file,filesize($list_fich[0][$i]));
         fclose($file);
         $debut = strpos($list_fich[1][$i],"[nom]") + 5;
         $fin = strpos($list_fich[1][$i],"[/nom]");
         if (preg_match("#".substr($list_fich[1][$i],$debut,$fin-$debut)."#",$txtconfig)) {
            $display .= adm_translate("Les param�tres sont d�j� inscrits dans le fichier \"").$list_fich[0][$i]."\".<br />\n";
         } else {
            if ($try_Chmod) {
               chmod($list_fich[0][$i], 666);
            }
            $file = fopen($list_fich[0][$i], "r+");
            fread($file,filesize($list_fich[0][$i]));
            if (fwrite($file, $list_fich[1][$i])) {
               fclose($file);
               $display .= adm_translate("Les param�tres ont �t� correctement �crits dans le fichier \"").$list_fich[0][$i]."\".<br />\n";
            } else {
               $writeAllFiles = 0;
               $display .= adm_translate("Impossible d'�crire dans le fichier \"").$list_fich[0][$i]."\". ".adm_translate("Veuillez �diter ce fichier manuellement ou r�essayez en tentant de faire un chmod automatique sur le(s) fichier(s) concern�s.")."<br /><br />\n";
               $display .= adm_translate("Voici le code � taper dans le fichier :")."<br /><br />\n";
               $display .= "</div>\n";
               $display .= "<div class=\"code\">\n";
               ob_start();
               highlight_string($list_fich[1][$i]);
               $display .= ob_get_contents();
               ob_end_clean();
               $display .= "<br />\n";
            }
         }
      } else {
         $file = fopen($list_fich[0][$i], "r");
         $txtconfig = fread($file,filesize($list_fich[0][$i]));
         fclose($file);
         if (!$file_created) {
            $debut = strpos($txtconfig,"?>");
            $txtconfig = substr($txtconfig,0,$debut-1).chr(13).$list_fich[1][$i].chr(13)."?>";
         } else {
            $txtconfig = "<?php \n".$list_fich[1][$i]."\n ?>";
         }
         if ($try_Chmod) {
            chmod($list_fich[0][$i], 666);
         }
         $file = fopen($list_fich[0][$i], "w");
         fread($file,filesize($list_fich[0][$i]));
         if (fwrite($file, $txtconfig)) {
            fclose($file);
            $display .= adm_translate("Les param�tres ont �t� correctement �crits dans le fichier \"").$list_fich[0][$i]."\".<br />\n";
         } else {
            $writeAllFiles = 0;
            $display .= adm_translate("Impossible d'�crire dans le fichier \"").$list_fich[0][$i]."\". ".adm_translate("Veuillez �diter ce fichier manuellement ou r�essayez en tentant de faire un chmod automatique sur le(s) fichier(s) concern�s.")."<br />\n";
            $display .= adm_translate("Voici le code � taper dans le fichier :")."<br /><br />\n";
            $display .= "</div>\n";
            $display .= "<div class=\"code\">\n";
            ob_start();
            highlight_string($list_fich[1][$i]);
            $display .= ob_get_contents();
            ob_end_clean();
            $display .= "<br />\n";
         }
      }
   }
   $display .= "</div><br />\n";
   $display .= "<div style=\"text-align: center;\">\n";
   $display .= "[";
   if (!$writeAllFiles) {
      $display .= "&nbsp;<a href=\"admin.php?op=Module-Install&amp;ModInstall=".$ModInstall."&amp;nmig=e6&amp;try_Chmod=1\" class=\"rouge\">".adm_translate("R�essayer avec chmod automatique")."</a>&nbsp;] | [";
   }
   $display .= "&nbsp;<a href=\"admin.php?op=Module-Install&amp;ModInstall=".$ModInstall."&amp;nmig=e7\"";
   if (!$writeAllFiles) {
      $display .= "class=\"noir\">";
   } else {
      $display .= "class=\"rouge\">";
   }
   $display .= adm_translate("Etape suivante")."</a>&nbsp;]<br />\n";
   $display .= "</div><br />\n";
   $display .= nmig_copyright();
}

// e7

function nmig_AlertBloc($blocs) {
   include("header.php");
   global $ModInstall, $display;
   $display = '
   <div class="">
      <p>'.adm_translate("Vous pouvez choisir maintenant de cr�er automatiquement un(des) bloc(s) � droite ou � gauche. Cliquer sur \"Cr�er le(s) bloc(s) � gauche\" ou \"Cr�er le(s) bloc(s) � droite\" selon votre choix. (Vous pourrez changer leurs positions par la suite dans le panneau d'administration --> Blocs)").'</p>
      <p>'.adm_translate("Si vous pr�f�rez cr�er vous m�me le(s) bloc(s), cliquez sur 'Sauter cette �tape et afficher le code du(des) bloc(s)' pour visualiser le code � taper dans le(s) bloc(s).").'</p>
      <p>'.adm_translate("Voici la description du(des) bloc(s) qui sera(seront) cr��(s) :").'</p>
   </div>';
   ob_start();
   echo '<ul>';
   for ($i = 0; $i < count($blocs[0]); $i++) {
      echo "<li>Bloc n&#xB0; ".$i." : ".$blocs[8][$i]."</li>";
   }
   echo '</ul>';
   $display .= ob_get_contents();
   ob_end_clean();
   $display .= '
   <div class="">
      <br />
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e8&amp;posbloc=l" class="btn btn-primary col-xs-12 col-md-4">'.adm_translate("Cr�er le(s) bloc(s) � gauche").'</a>
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e8&amp;posbloc=r" class="btn btn-primary col-xs-12 col-md-4">'.adm_translate("Cr�er le(s) bloc(s) � droite").'</a>
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e8&amp;posbloc=0" class="btn btn-danger col-xs-12 col-md-4">'.adm_translate("Sauter cette �tape").'</a>
   </div><br /><br />';
   $display .= nmig_copyright();
}

// e8

function nmig_WriteBloc($blocs,$posbloc) {
   include("header.php");
   global $ModInstall, $display;
   global $NPDS_Prefix;
   $display = '<div class="">';
   if ($posbloc) {
      if ($blocs[2] == "") {
         $blocs[2] = $blocs[3];
      }
      if ($posbloc == "l") {
         $posblocM = "L";
      }
      if ($posbloc == "r") {
         $posblocM = "R";
      }
      for ($i = 0; $i < count($blocs[0]) && !isset($erreur); $i++) {

         sql_query("INSERT INTO ".$NPDS_Prefix.$posbloc."blocks (`id`, `title`, `content`, `member`, `".$posblocM."index`, `cache`, `actif`, `aide`) VALUES ('', '".$blocs[0][$i]."', '".$blocs[1][$i]."', '".$blocs[2][$i]."', '".$blocs[4][$i]."', '".$blocs[5][$i]."', '".$blocs[6][$i]."', '".$blocs[7][$i]."');") or $erreur = sql_error();
      }
      if (isset($erreur)) {
         $display .= adm_translate("Une erreur est survenue lors de la configuration automatique du(des) bloc(s). Mysql a r�pondu :");
         ob_start();
         highlight_string($erreur);
         $display .= ob_get_contents();
         ob_end_clean();
         $display .= adm_translate("Veuillez configurer manuellement le(s) bloc(s).")."<br /><br />\n";
         $display .= adm_translate("Voici le code du(des) bloc(s) :")."<br /><br />\n";
         ob_start();
         for ($i = 0; $i < count($blocs[0]); $i++) {
            echo "Bloc n&#xB0; ".$i."<br />";
            highlight_string($blocs[1][$i]);
            echo "<br />\n";
         }
         $display .= ob_get_contents();
         ob_end_clean();
      } else {
         $display .= '<p class="text-success"><strong>'.adm_translate("La configuration du(des) bloc(s) a r�ussi !").'</strong></p>';
         $display .= "<br />\n";
      }
   } else {
      $display .= '<p><strong>'.adm_translate("Vous avez choisi de configurer manuellement vos blocs. Voici le contenu de ceux-ci :").'</strong></p>';
      ob_start();
         for($i = 0; $i < count($blocs[0]); $i++) {
            echo 'Bloc n&#xB0; '.$i.'<br />
            <code>'.$blocs[1][$i].'</code>
            <br />';
         }
      $display .= ob_get_contents();
      ob_end_clean();
   }
   $display .= '
   </div><br />
   <div style="text-align: center;">
      <a href="admin.php?op=Module-Install&amp;ModInstall='.$ModInstall.'&amp;nmig=e9" class="btn btn-primary">'.adm_translate("Etape suivante").'</a><br />
   </div><br />
   '.nmig_copyright();
}

// e9

function nmig_txt($txtfin) {
   include("header.php");
   global $ModInstall, $display;
   $display = "<div style=\"text-align: left;\">\n";
   $display .= "<b>".aff_langue($txtfin)."</b>\n";
   $display .= "<br /><br />\n";
   $display .= "</div><div style=\"text-align: center;\">\n";
   $display .= "<br />[&nbsp;<a href=\"admin.php?op=Module-Install&amp;ModInstall=".$ModInstall."&amp;nmig=e10\" class=\"noir\">".adm_translate("Etape suivante")."</a>&nbsp;]<br />\n";
   $display .= "</div>\n";
   $display .= "<br />\n";
   $display .= nmig_copyright();
}

// e10

function nmig_End($name_module, $end_link) {
   include("header.php");
   global $ModInstall, $display, $NPDS_Prefix;
   sql_query("UPDATE ".$NPDS_Prefix."modules SET minstall='1' WHERE mnom='".$ModInstall."'");
   $display = ' 
   <div class="">
      <p class="text-success"><strong>'.adm_translate("L'installation automatique du module").' "'.$name_module.'" '.adm_translate("est termin�e !").'</strong></p>
   </div>
   <div style="text-align: center;">
      <a href="'.$end_link.'" class="btn btn-primary">'.adm_translate("Ok").'</a>
   </div><br />
   '.nmig_copyright();
}


function nmig_clean($name_module) {
//==> a compl�ter
}

// ************************
// * Affichage de la page *
// ************************

   if (!isset($try_Chmod)) {
      $try_Chmod = 0;
   }
   if ($ModInstall != "" && $ModDesinstall == "") {
      if ($subop == "install") {
         $result = sql_query("UPDATE ".$NPDS_Prefix."modules SET minstall='1' WHERE mnom= '".$ModInstall."'");
      }
      if (file_exists("modules/".$ModInstall."/install.conf.php")) {
         include("modules/".$ModInstall."/install.conf.php");
      } else {
         include("header.php");
         GraphicAdmin($hlpfile);
         echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\"><tr><td class=\"header\">\n";
         echo adm_translate("Gestion, Installation Modules");
         echo "</td></tr></table>\n";
         echo "<div style=\"text-align: center;\">".adm_translate("Fichier de configuration automatique absent. Installation/d�sinstallation automatique impossible.")."<br /><br />[ <a href=\"JavaScript:history.go(-1)\" class=\"rouge\">".adm_translate("Retour en arri�re")."</a> ]</div><br /><br />".nmig_copyright();
         include("footer.php");
         die();
      }
      if (file_exists("modules/".$ModInstall."/licence-".$language.".txt")) {
         $licence_file = "modules/".$ModInstall."/licence-".$language.".txt";
      } else {
         $licence_file = "modules/".$ModInstall."/licence-english.txt";
      }
      switch($nmig) {
         case "e2":
            nmig_License($licence_file);
            break;

         case "e3":
            if (isset($sql[0]) && $sql[0] != "") { nmig_AlertSql($sql,$tables); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e5\";\n//]]>\n</script>"; }
            break;   

         case "e4":
            if (isset($sql[0]) && $sql[0] != "") { nmig_WriteSql($sql,$tables); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e5\";\n//]]>\n</script>"; }
            break;

         case "e5":
            if (isset($list_fich) && count($list_fich[0]) && $list_fich[0][0] != "") { nmig_AlertConfig($list_fich); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e7\";\n//]]>\n</script>"; }
            break;

         case "e6":
            if (isset($list_fich) && count($list_fich[0])) { nmig_WriteConfig($list_fich, $try_Chmod); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e7\";\n//]]>\n</script>"; }
            break;

         case "e7":
            if (isset($blocs) && count($blocs[0]) && $blocs[0][0] != "") { nmig_AlertBloc($blocs); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e9\";\n//]]>\n</script>"; }
            break;

         case "e8":
            if (isset($blocs) && count($blocs[0]) && $blocs[0][0] != "") { nmig_WriteBloc($blocs, $posbloc); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e9\";\n//]]>\n</script>"; }
            break;

         case "e9":
            if (isset($txtfin) && $txtfin != "") { nmig_txt($txtfin); }
            else { echo "<script type=\"text/javascript\">\n//<![CDATA[\nwindow.location = \"admin.php?op=Module-Install&ModInstall=".$ModInstall."&nmig=e10\";\n//]]>\n</script>"; }
            break;

         case "e10":
            if (!isset($end_link) || $end_link == "") { $end_link = "admin.php?op=modules"; }
            nmig_End($name_module, $end_link);
            break;

         default:
            nmig_Start($name_module,$txtdeb);
            break;
      }
   } elseif ($ModInstall == "" && $ModDesinstall != "") {
      if ($subop == "desinst") {
         include("header.php");
         list($fid)=sql_fetch_row(sql_query("SELECT fid FROM ".$NPDS_Prefix."fonctions WHERE fnom='".$ModDesinstall."'"));
         $result = sql_query("UPDATE ".$NPDS_Prefix."modules SET minstall='0' WHERE mnom= '".$ModDesinstall."'");
         sql_query("DELETE FROM ".$NPDS_Prefix."droits WHERE d_fon_fid=".$fid."");
         $res = sql_query("DELETE FROM ".$NPDS_Prefix."fonctions WHERE fnom='".$ModDesinstall."'");
         redirect_url("admin.php?op=modules");
      }
      include("header.php");
      $display = '
      <div style="text-align: left;">
         <h4 class="text-danger">'.adm_translate("D�sinstaller le module ").' '.$ModDesinstall.'.</h4>
         <p><strong>'.adm_translate("La d�sinstallation automatique des modules n'est pas prise en charge � l'heure actuelle.").'</strong>
         <p>'.adm_translate("Vous devez d�sinstaller le module manuellement. Pour cela, r�f�rez vous au fichier install.txt de l'archive du module, et faites les op�rations inverses de celles d�crites dans la section \"Installation manuelle\", et en partant de la fin.").'
         <p>'.adm_translate("Enfin, pour pouvoir r�installer le module par la suite avec Module-Install, cliquez sur le bouton \"Marquer le module comme d�sinstall�\".").'</p>
      </div>
      <div style="text-align: center;">
      <a href="JavaScript:history.go(-1)" class="btn btn-primary col-xs-12 col-md-6">'.adm_translate("Retour en arri�re").'</a><a href="admin.php?op=Module-Install&amp;ModDesinstall='.$ModDesinstall.'&amp;subop=desinst" class="btn btn-danger col-xs-12 col-md-6">'.adm_translate("Marquer le module comme d�sinstall�").'</a><br />
      </div>
      <br /><br />
      '.nmig_copyright();
   }

      GraphicAdmin($hlpfile);
      adminhead ($f_meta_nom, $f_titre, $adminimg);
      $clspin =' text-success';
      if ($ModInstall == "" && $ModDesinstall != "") {
      $clspin =' text-danger';
      }
      
      echo '<h3><i class="fa fa-spinner fa-pulse '.$clspin.' "></i> '.$name_module.'</h3>';
      echo $display;
      adminfoot('','','','');
?>