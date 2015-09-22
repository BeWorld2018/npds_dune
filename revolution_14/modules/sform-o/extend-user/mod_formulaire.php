<?php
/************************************************************************/
/* SFORM Extender for NPDS USER                                         */
/* ===========================                                          */
/* NPDS Copyright (c) 2002-2011 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/* Dont modify this file is you dont know what you make                 */
/************************************************************************/
global $NPDS_Prefix;

$m->add_title(translate("User"));
$m->add_mess(translate("* for mandatory field"));
$m->add_form_field_size(50);

$m->add_field('name', translate("Real Name")." ".translate("(optional)"),$userinfo['name'],'text',false,60,"","");
$m->add_field('email', translate("Real Email")."<br /><span style=\"font-size: 10px;\">".translate("(This Email will not be public but is required, will be used to send your password if you lost it)")."</span>",$userinfo['email'],'text',true,60,"","");
$m->add_field('femail',translate("Fake Email")."<br /><span style=\"font-size: 10px;\">".translate("(This Email will be public. Just type what you want, Spam proof)")."</span>",$userinfo['femail'],'text',false,60,"","");
if ($userinfo['user_viewemail']) {$checked=true;} else {$checked=false;}
$m->add_checkbox('user_viewemail',translate("Allow other users to view my email address"), 1, false, $checked);

$m->add_field('url', translate("Your HomePage"),$userinfo['url'],'text',false,100,"","");

// ---- SUBSCRIBE and INVISIBLE
if ($subscribe) {
   if ($userinfo['send_email']==1) {$checked=true;} else {$checked=false;}
   $m->add_checkbox('usend_email',translate("Send me an email when Internal Message arrive"), 1, false, $checked);
}
if ($member_invisible) {
   if ($userinfo['is_visible']==1) {$checked=false;} else {$checked=true;}
   $m->add_checkbox('uis_visible',translate("Invisible' member")." (".translate("not showed in memberlist, members' message bloc ...").")", 1, false, $checked);
}
// ---- SUBSCRIBE and INVISIBLE

// LNL
if ($userinfo['user_lnl']) {$checked=true;} else {$checked=false;}
$m->add_checkbox('user_lnl',translate("Register to web site' mailing list"), 1, false, $checked);
// LNL

// ---- AVATAR
if ($smilies) {
   if (stristr($userinfo['user_avatar'],"users_private")) {
      $m->add_field('user_avatar', "<b>".translate("Your Avatar")."</b>", $userinfo['user_avatar'],'show-hidden',false,30,"","");
      $m->add_extender("user_avatar", "", "<img src=\"".$userinfo['user_avatar']."\" name=\"avatar\" align=\"center\" alt=\"\" />");
   } else {
      global $theme;
      $direktori="images/forum/avatar";
      if (function_exists("theme_image")) {
         if (theme_image("forum/avatar/blank.gif"))
            $direktori="themes/$theme/images/forum/avatar";
      }
      $handle=opendir($direktori);
      while (false!==($file = readdir($handle))) {
         $filelist[] = $file;
      }
      asort($filelist);
      while (list ($key, $file) = each ($filelist)) {
         if (!preg_match('#\.gif|\.jpg|\.png$#i', $file)) continue;
            $tmp_tempo[$file]['en']=$file;
            if ($userinfo['user_avatar']==$file) {$tmp_tempo[$file]['selected']=true;} else {$tmp_tempo[$file]['selected']=false;}
      }
      $m->add_select("user_avatar", "<b>".translate("Your Avatar")."</b>", $tmp_tempo, false, "", false);
      $m->add_extender("user_avatar", "onkeyup=\"showimage();\" onchange=\"showimage();\"", "&nbsp;&nbsp;<img src=\"".$direktori."/".$userinfo['user_avatar']."\" name=\"avatar\" align=\"top\" title=\"\" />");
   }

   // Permet � l'utilisateur de t�l�charger un avatar (photo) personnel
   // - si vous mettez un // devant les deux lignes B1 et raz_avatar cela �quivaut � ne pas autoriser cette fonction de NPDS
   // - le champ B1 est imp�ratif ! La taille maxi du fichier t�l�charg�able peut-�tre chang�e (le dernier param�tre) et est en octets (par exemple 20480 = 20 Ko)
   $taille_fichier=8192;
   if (!$avatar_size) {$avatar_size="80*100";}
   $m->add_upload('B1', "<span style=\"font-size: 10px;\">taille maximum du fichier d'image :<br />&nbsp;&nbsp;=>&nbsp;<b>$taille_fichier</b> octects et <b>$avatar_size</b> pixels</span>", "30", $taille_fichier);
   $m->add_checkbox('raz_avatar',translate("Re-activate the standard'avatars"), 1, false, false);
   // ----------------------------------------------------------------------------------------------
}
// ---- AVATAR

// ---- SHORT-USER
if ($short_user=="yes") {
   $m->add_field('user_icq', translate("Your ICQ"),$userinfo['user_icq'],'text',false,50,"","");
   $m->add_field('user_aim', translate("Your AIM"),$userinfo['user_aim'],'text',false,50,"","");
   $m->add_field('user_yim', translate("Your YIM"),$userinfo['user_yim'],'text',false,50,"","");
   $m->add_field('user_msnm', translate("Your MSNM"),$userinfo['user_msnm'],'text',false,50,"","");
} else {
   $m->add_field('user_icq',"user_icq","",'hidden',false);
   $m->add_field('user_aim',"user_aim","",'hidden',false);
   $m->add_field('user_yim',"user_yim","",'hidden',false);
   $m->add_field('user_msnm',"user_msnm","",'hidden',false);
}
// ---- SHORT-USER

$m->add_field('user_from', translate("Your Location"),$userinfo['user_from'],'text',false,100,"","");
$m->add_field('user_occ', translate("Your Occupation"),$userinfo['user_occ'],'text',false,100,"","");
$m->add_field('user_intrest', translate("Your Interest"),$userinfo['user_intrest'],'text',false,150,"","");

// ---- SIGNATURE
$asig = sql_query("select attachsig from ".$NPDS_Prefix."users_status where uid='".$userinfo['uid']."'");
list($attsig) = sql_fetch_row($asig);
if ($attsig==1) {$checked=true;} else {$checked=false;}
$m->add_checkbox('attach',translate("Show signature"), 1, false, $checked);
$m->add_field('user_sig', translate("Signature")."<br /><span style=\"font-size: 10px;\">".translate("(255 characters max. Type your signature with HTML coding)")."</span>",$userinfo['user_sig'],'textarea',false,255,7,"","");
// ---- SIGNATURE

$m->add_field('bio',translate("Extra Info")."<br /><span style=\"font-size: 10px;\">".translate("(255 characters max. Type what others can know about yourself)")."</span>",$userinfo['bio'],'textarea',false,255,7,"","");

$m->add_field('pass', translate("Password"),"",'password',false,40,"","");
$m->add_field('vpass', translate("Retype Password"),"",'password',false,40,"","");

// --- EXTENDER
if (file_exists("modules/sform/extend-user/extender/formulaire.php")) {
   include("modules/sform/extend-user/extender/formulaire.php");
}
// --- EXTENDER

// ----------------------------------------------------------------
// CES CHAMPS sont indispensables --- Don't remove these fields
// Champ Hidden
$m->add_field("op","","saveuser",'hidden',false);
$m->add_field("uname","",$userinfo['uname'],'hidden',false);
$m->add_field("uid","",$userinfo['uid'],'hidden',false);

$m->add_extra("<br />");
// Submit bouton
$m->add_field('Submit',"",translate("Submit"),'submit',false);
// ----------------------------------------------------------------
?>