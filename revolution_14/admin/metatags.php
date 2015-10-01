<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2013 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
if (!stristr($_SERVER['PHP_SELF'],"admin.php")) { Access_Error(); }
$f_meta_nom ='MetaTagAdmin';
$f_titre = adm_translate("Administration des M�taTags");
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit

function MetaTagAdmin($saved = false) {
   global $hlpfile, $f_meta_nom, $f_titre, $adminimg;
   $tags = GetMetaTags("meta/meta.php");
   include("header.php");
   GraphicAdmin($hlpfile);
    adminhead ($f_meta_nom, $f_titre, $adminimg);
   if ($saved){
      echo '<p align="center " class="text-danger">'.adm_translate("Vos M�taTags ont �t� modifi�s avec succ�s !").'</p>';
   }
   echo '
   <form id="fad_metatags" action="admin.php" method="post">
   <div class="form-group">
      <label class="form-control-label" for="newtag[author]">'.adm_translate("Auteur(s)").'</label>
      <input class="form-control" type="text" name="newtag[author]" value="'.$tags['author'].'" maxlength="100">
      <span class="help-block">'. adm_translate("(Ex. : nom du webmaster)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[owner]">'.adm_translate("Propri�taire").'</label>
      <input class="form-control" type="text" name="newtag[owner]" value="'.$tags['owner'].'" maxlength="100" />
      <span class="help-block">'.adm_translate("(Ex. : nom de votre compagnie/service)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[reply-to]">'.adm_translate("Adresse e-mail principale").'</label>
      <input class="form-control" type="email" name="newtag[reply-to]" value="'.$tags['reply-to'].'" maxlength="100" />
      <span class="help-block">'.adm_translate("(Ex. : l'adresse e-mail du webmaster)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[language]">'.adm_translate("Langue principale").'</label>
      <input class="form-control" type="text" name="newtag[language]" value="'.$tags['language'].'" size="6" maxlength="5" />
      <span class="help-block">'.adm_translate("(Ex. : fr(Fran�ais), en(Anglais), en-us(Am�ricain), de(Allemand), it(Italien), pt(Portugais), etc)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[description]">'.adm_translate("Description").'</label>
      <input class="form-control" type="text" name="newtag[description]" value="'.$tags['description'].'" maxlength="200" />
      <span class="help-block">'.adm_translate("(Br�ve description des centres d'int�r�t du site. 200 caract�res maxi.)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[keywords]">'.adm_translate("Mot(s) cl�(s)").'</label>
      <input class="form-control" type="text" name="newtag[keywords]" value="'.$tags['keywords'].'" maxlength="1000" />
      <span class="help-block">'.adm_translate("(D�finissez un ou plusieurs mot(s) cl�(s). 1000 caract�res maxi.<br />Remarques : une lettre accentu�e �quivaut le plus souvent � 8 caract�res. La majorit� des moteurs de recherche font la distinction minuscule/majuscule. S�parez vos mots par une virgule)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[rating]">'.adm_translate("Audience").'</label>
      <select class="form-control" name="newtag[rating]">
         <option value="general"'.(!strcasecmp($tags['rating'], 'general') ? ' selected="selected"' : '').'>'.adm_translate("Tout public").'</option>
         <option value="mature"'.(!strcasecmp($tags['rating'], 'mature') ? ' selected="selected"' : '').'>'.adm_translate("Adulte").'</option>
         <option value="restricted"'.(!strcasecmp($tags['rating'], 'restricted') ? ' selected="selected"' : '').'>'.adm_translate("Acc�s restreint").'</option>
         <option value="14 years"'.(!strcasecmp($tags['rating'], '14 years') ? ' selected="selected"' : '').'>'.adm_translate("14 ans").'</option>
      </select>
      <span class="help-block">'.adm_translate("(D�finissez le public int�ress� par votre site)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[distribution]">'.adm_translate("Distribution").'</label>
      <select class="form-control" name="newtag[distribution]">
         <option value="global"'.(!strcasecmp($tags['distribution'], 'global') ? ' selected="selected"' : '').'>'.adm_translate("Large").'</option>
         <option value="local"'.(!strcasecmp($tags['distribution'], 'local') ? ' selected="selected"' : '').'>'.adm_translate("Restreinte").'</option>
      </select>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[copyright]">'.adm_translate("Copyright").'</label>
      <input class="form-control" type="text" name="newtag[copyright]" value="'.$tags['copyright'].'" maxlength="100" />
      <span class="help-block">'.adm_translate("(Informations l�gales)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[robots]">'.adm_translate("Robots/Spiders").'</label>
      <select class="form-control" name="newtag[robots]">
         <option value="all"'.(!strcasecmp($tags['robots'], 'all') ? ' selected="selected"' : '').'>'.adm_translate("Tout contenu (page/liens/etc)").'</option>
         <option value="none"'.(!strcasecmp($tags['robots'], 'none') ? ' selected="selected"' : '').'>'.adm_translate("Aucune indexation").'</option>
         <option value="index,nofollow"'.(!strcasecmp($tags['robots'], 'index,nofollow') ? ' selected="selected"' : '').'>'.adm_translate("Page courante sans liens locaux").'</option>
         <option value="noindex,follow"'.(!strcasecmp($tags['robots'], 'noindex,follow') ? ' selected="selected"' : '').'>'.adm_translate("Liens locaux sauf page courante").'</option>
         <option value="noarchive"'.(!strcasecmp($tags['robots'], 'noarchive') ? ' selected="selected"' : '').'>'.adm_translate("Pas d'affichage du cache").'</option>
         <option value="noodp,noydir"'.(!strcasecmp($tags['robots'], 'noodp,noydir') ? ' selected="selected"' : '').'>'.adm_translate("Pas d'utilisation des descriptions ODP ou YDIR").'</option>
      </select>
      <span class="help-block">'.adm_translate("(D�finissez la m�thode d'analyse que doivent adopter les robots des moteurs de recherche)").'</span>
   </div>
   <div class="form-group">
      <label class="form-control-label" for="newtag[revisit-after]">'.adm_translate("Fr�quence de visite des Robots/Spiders").'</label>
      <input class="form-control" type="text" name="newtag[revisit-after]" value="'.$tags['revisit-after'].'" maxlength="30" />
      <span class="help-block">'.adm_translate("(Ex. : 16 days. Remarque : ne d�finissez pas de fr�quence inf�rieure � 14 jours !)").'</span>
   </div>';

   if (function_exists("utf8_encode")) {
      echo '
   <div class="form-group">
      <label class="form-control-label" for="newtag[content-type]">'.adm_translate("Encodage").'</label>
      <select class="form-control" name="newtag[content-type]">
         <option value="text/html; charset=iso-8859-1"'.(!strcasecmp($tags['content-type'], 'text/html; charset=iso-8859-1') ? ' selected="selected"' : '').'>charset=ISO-8859-1</option>
         <option value="text/html; charset=utf-8"'.(!(strcasecmp($tags['content-type'], 'text/html; charset=utf-8') and strcasecmp($tags['content-type'], 'text/html')) ? ' selected="selected"' : '').'>charset=UTF-8</option>
      </select>
   </div>';
   } else {
      echo '
      <label class="form-control-label" for="">'.adm_translate("Encodage").'</label>
      <span class="text-danger">utf8_encode() '.adm_translate("non disponible").'</span>';
   }
   echo '
   <div class="form-group">
      <label class="form-control-label" for="newtag[content-type]">DOCTYPE</label>
         <select class="form-control" name="newtag[doctype]">
            <option value="HTML 4.01 Transitional"'.(!strcasecmp(doctype, 'HTML 4.01 Transitional') ? ' selected="selected"' : '').'>HTML 4.01 '.adm_translate("Transitional").' (deprecated)</option>
            <option value="HTML 4.01 Strict"'.(!strcasecmp(doctype, 'HTML 4.01 Strict') ? ' selected="selected"' : '').'>HTML 4.01 '.adm_translate("Strict").' (deprecated)</option>
            <option value="XHTML 1.0 Transitional"'.(!strcasecmp(doctype, 'XHTML 1.0 Transitional') ? ' selected="selected"' : '').'>XHTML 1.0 '.adm_translate("Transitional").'</option>
            <option value="XHTML 1.0 Strict"'.(!strcasecmp(doctype, 'XHTML 1.0 Strict') ? ' selected="selected"' : '').'>XHTML 1.0 '.adm_translate("Strict").'</option>
            <option value="HTML 5.0"'.(!strcasecmp(doctype, 'HTML 5.0') ? ' selected="selected"' : '').'>HTML 5.0 (experimental)</option>
         </select>
   </div>
   <input type="hidden" name="op" value="MetaTagSave" />
   <div class="form-group">
      <button class="btn btn-primary" type="submit">'.adm_translate("Enregistrer").'</button>
   </div>
   </form>';
    adminfoot('fv','','','');
}

if (!stristr($_SERVER['PHP_SELF'],"admin.php")) { Access_Error(); }
include ("admin/settings_save.php");

global $language;
$hlpfile = "manuels/$language/metatags.html";

   settype($meta_saved,'string');
   switch ($op) {
      case "MetaTagSave":
         $meta_saved = MetaTagSave("meta/meta.php", $newtag);
         header("location: admin.php?op=MetaTagAdmin");
         break;

       case "MetaTagAdmin":
         MetaTagAdmin($meta_saved);
         break;
    }
?>