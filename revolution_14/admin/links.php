<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2012 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
if (!stristr($_SERVER['PHP_SELF'],"admin.php")) { Access_Error(); }
$f_meta_nom ='links';
$f_titre = 'Liens';
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit
global $language, $NPDS_Prefix;
$hlpfile = "manuels/$language/weblinks.html";

// valeur du pas de pagination
$rupture=4;//100

function links() {
   global $NPDS_Prefix, $hlpfile, $f_meta_nom, $f_titre, $adminimg;
   include ("header.php");
   GraphicAdmin($hlpfile);
   adminhead ($f_meta_nom, $f_titre, $adminimg);
   
   $results=sql_query("select * from ".$NPDS_Prefix."links_links");
   $numrows = sql_num_rows($results);
   $result = sql_query("select * from ".$NPDS_Prefix."links_modrequest where brokenlink=1");
   $totalbrokenlinks = sql_num_rows($result);
   $result2 = sql_query("select * from ".$NPDS_Prefix."links_modrequest where brokenlink=0");
   $totalmodrequests = sql_num_rows($result2);

   echo adm_translate("Il y a").' <b>'.$numrows.'</b> '.adm_translate("Liens");
   echo '[ <a href="admin.php?op=LinksListBrokenLinks">'.adm_translate("Soumission de Liens bris�s").' ('.$totalbrokenlinks.')</a> -
   <a href="admin.php?op=LinksListModRequests" class="noir">'.adm_translate("Proposition de modifications de Liens").' ('.$totalmodrequests.')</a> ]';

   $result = sql_query("select lid, cid, sid, title, url, description, name, email, submitter from ".$NPDS_Prefix."links_newlink ORDER BY lid ASC LIMIT 0,1");
   $numrows = sql_num_rows($result);
   $adminform="";
   if ($numrows>0) {
    $adminform="adminForm";
    echo '<h3>'.adm_translate("Liens en attente de validation").'</h3>';
    list($lid, $cid, $sid, $title, $url, $xtext, $name, $email, $submitter) = sql_fetch_row($result);
       echo "<form action=\"admin.php\" method=\"post\" name=\"$adminform\">";
       echo adm_translate("Lien N� : ")."<b>$lid</b> - ".adm_translate("Auteur")." : $submitter <br /><br />";
       echo adm_translate("Titre de la Page : ")."<br /><input class=\"textbox\" type=\"text\" name=\"title\" value=\"$title\" size=\"50\" maxlength=\"100\"><br /><br />";
       echo adm_translate("URL de la Page : ")."<br /><input class=\"textbox\" type=\"text\" name=\"url\" value=\"$url\" size=\"50\" maxlength=\"100\"> - [ <a href=\"$url\" target=\"_blank\" class=\"noir\">".adm_translate("Visite")."</a> ]<br /><br />";
       echo adm_translate("Description : ")."<br /><textarea class=\"textbox\" name=\"xtext\" cols=\"60\" rows=\"10\" style=\"width: 100%;\">$xtext</textarea><br /><br />";
       echo aff_editeur("xtext","false");
       echo adm_translate("Nom : ")."<input class=\"textbox_standard\" type=\"text\" name=\"name\" size=\"40\" maxlength=\"100\" value=\"$name\"> - ";
       echo adm_translate("E-mail : ")."<input class=\"textbox_standard\" type=\"text\" name=\"email\" size=\"40\" maxlength=\"100\" value=\"$email\"><br />";
       $result2=sql_query("select cid, title from ".$NPDS_Prefix."links_categories order by title");
       echo "<input type=\"hidden\" name=\"new\" value=\"1\">";
       echo "<input type=\"hidden\" name=\"lid\" value=\"$lid\">";
       echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">";
       echo "<br />".adm_translate("Cat�gorie : ")."<select class=\"textbox_standard\" name=\"cat\">";
       while(list($ccid, $ctitle) = sql_fetch_row($result2)) {
          $sel = "";
          if ($cid==$ccid AND $sid==0) {
             $sel = "selected";
          }
          echo "<option value=\"$ccid\" $sel>".aff_langue($ctitle)."</option>";
          $result3=sql_query("select sid, title from ".$NPDS_Prefix."links_subcategories where cid='$ccid' order by title");
          while (list($ssid, $stitle) = sql_fetch_row($result3)) {
             $sel = "";
             if ($sid==$ssid) {
                $sel = "selected";
             }
             echo "<option value=\"$ccid-$ssid\" $sel>".aff_langue($ctitle)." / ".aff_langue($stitle)."</option>";
          }
       }
       echo "<input type=\"hidden\" name=\"submitter\" value=\"$submitter\">";
       echo "</select> - <input type=\"hidden\" name=\"op\" value=\"LinksAddLink\">
       <input class=\"btn btn-primary\" type=\"submit\" value=".adm_translate("Ajouter")."> - [ <a href=\"admin.php?op=LinksDelNew&amp;lid=$lid\" class=\"rouge\">".adm_translate("Effacer")."</a> ]";
       echo "</form>";
    // Fin de List
   }

   // Add a Link to Database
   $result = sql_query("select cid, title from ".$NPDS_Prefix."links_categories");
   $numrows = sql_num_rows($result);
   if ($numrows>0) {
      echo '
   <h3>'.adm_translate("Ajouter un lien").'</h3>';
      if ($adminform=="") {
       echo '<form method="post" action="admin.php" name="adminForm">';
      } else {
       echo '<form method="post" action="admin.php">';
      }
    echo '
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title">'.adm_translate("Titre de la Page").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" id="title" maxlength="100" required="required" />
               <span class="help-block text-right"><span id="countcar_title"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="url">'.adm_translate("URL de la Page").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="url" name="url" id="url" maxlength="100" placeholder="http://" required="required" />
               <span class="help-block text-right"><span id="countcar_url"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">';

    $result=sql_query("select cid, title from ".$NPDS_Prefix."links_categories order by title");
    echo '
            <label class="form-control-label col-sm-4 col-md-4" for="cat">'.adm_translate("Cat�gorie").'</label>
            <div class="col-sm-8 col-md-8">
               <select class="form-control" name="cat">';
    while(list($cid, $title) = sql_fetch_row($result)) {
      echo '
                  <option value="'.$cid.'">'.aff_langue($title).'</option>';
      $result2=sql_query("select sid, title from ".$NPDS_Prefix."links_subcategories where cid='$cid' order by title");
      while(list($sid, $stitle) = sql_fetch_row($result2)) {
         echo "<option value=\"$cid-$sid\">".aff_langue($title)." / ".aff_langue($stitle)."</option>";
      }
    }
   echo '
               </select>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="xtext">'.adm_translate("Description :  (255 caract�res max)").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="xtext" rows="6"></textarea>
            </div>
         </div>
      </div>';
   if ($adminform=="") echo aff_editeur("xtext","false");  //what this ??
   echo '
      <div class="form-group">
         <div class="row">
         <label class="form-control-label col-sm-4 col-md-4" for="name">'.adm_translate("Nom").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="name" id="name" maxlength="60" />
               <span class="help-block text-right"><span id="countcar_name"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="email">'.adm_translate("E-mail").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="email" name="email" id="email" maxlength="60" />
               <span class="help-block text-right"><span id="countcar_email"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="op" value="LinksAddLink">
               <input type="hidden" name="new" value="0">
               <input type="hidden" name="lid" value="0">
               <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter une URL").'</button>
            </div>
         </div>
      </div>
   </form>';
   }

   // Add a Main category
   echo '
   <h3>'.adm_translate("Ajouter une Cat�gorie").'</h3>
   <form action="admin.php" method="post">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title" >'.adm_translate("Nom").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" maxlength="100" required="required"/>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="cdescription">'.adm_translate("Description").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="cdescription" rows="7"></textarea>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="op" value="LinksAddCat">
               <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter une Cat�gorie").'</button>
            </div>
         </div>
      </div>
   </form>';

   // Add a New Sub-Category
   $result = sql_query("select * from ".$NPDS_Prefix."links_categories");
   $numrows = sql_num_rows($result);
   if ($numrows>0) {
    echo '
    <h3>'.adm_translate("Ajouter une Sous-cat�gorie").'</h3>
    <form method="post" action="admin.php">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title">'.adm_translate("Nom").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" maxlength="100">
            </div>
         </div>
      </div>';

    $result=sql_query("select cid, title from ".$NPDS_Prefix."links_categories order by title");
    echo '
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="cid">'.adm_translate("Cat�gorie").'</label>
            <div class="col-sm-8 col-md-8">
               <select class="form-control" name="cid">';
    while(list($ccid, $ctitle) = sql_fetch_row($result)) {
       echo '
                  <option value="'.$ccid.'">'.aff_langue($ctitle).'</option>';
    }
    echo '
               </select>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="op" value="LinksAddSubCat">
               <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter une Sous-cat�gorie").'</button>
            </div>
         </div>
      </div>
   </form>';
   }

   // Modify Category
   $result = sql_query("select * from ".$NPDS_Prefix."links_categories");
   $numrows = sql_num_rows($result);
   if ($numrows>0) {
    $result=sql_query("select cid, title from ".$NPDS_Prefix."links_categories order by title");
    echo '
   <h3>'.adm_translate("Modifier la Cat�gorie").'</h3>
   <form method="post" action="admin.php">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="cat">'.adm_translate("Cat�gorie").'</label>
            <div class="col-sm-8 col-md-8">
               <select class="form-control" name="cat">';
    while(list($cid, $title) = sql_fetch_row($result)) {
       echo '
                  <option value="'.$cid.'">'.aff_langue($title).'</option>';
       $result2=sql_query("select sid, title from ".$NPDS_Prefix."links_subcategories where cid='$cid' order by title");
       while(list($sid, $stitle) = sql_fetch_row($result2)) {
          echo "<option value=\"$cid-$sid\">".aff_langue($title)." / ".aff_langue($stitle)."</option>";
       }
    }
    echo '
             </select>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="op" value="LinksModCat">
               <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-edit fa-lg"></i>&nbsp;'.adm_translate("Editer une Cat�gorie").'</button>
            </div>
         </div>
      </div>
   </form>';
   }

   // Modify Links
   $result=sql_query("select lid from ".$NPDS_Prefix."links_links");
   $numrow=sql_num_rows($result);
   echo '
   <h3>'.adm_translate("Liste des Liens").' ('.$numrow.')</h3>
   <table id="tad_link" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
      <thead>
         <tr>
            <th data-sortable="true" class="">ID</th>
            <th data-sortable="true" class="">'.adm_translate('Titre').'</th>
            <th data-sortable="true" class="">URL</th>
            <th class="">'.adm_translate('Fonctions').'</th>
         </tr>
      </thead>
      <tbody>
   ';
   global $rupture,$deja_affiches;
   settype ($deja_affiches, "integer");
   if ($deja_affiches<0) {$sens=-1;} else {$sens=+1;}
   $deja_affiches=abs($deja_affiches);
   $result = sql_query("select lid, title, url from ".$NPDS_Prefix."links_links order by lid ASC LIMIT $deja_affiches,$rupture");
   while (list($lid, $title, $url) = sql_fetch_row($result)) {
    echo '
            <tr>
               <td>'.$lid.'</td>
               <td><a href="admin.php?op=LinksModLink&amp;lid='.$lid.'" >'.$title.'</a></td>
               <td>'.$url.'</td>
               <td>
                  <a href="admin.php?op=LinksModLink&amp;lid='.$lid.'" ><i class="fa fa-edit fa-lg"></i></a>&nbsp;
                  <a href="'.$url.'" target="_blank"><i class="fa fa-external-link fa-lg"></i></a>&nbsp;
                  <a href="admin.php?op=LinksDelLink&amp;lid='.$lid.'" class="text-danger"><i class="fa fa-trash-o fa-lg"></i></a>
               </td>
            </tr>';
   }
   echo '
      </tbody>
   </table>';


   $deja_affiches_plus=$deja_affiches+$rupture;
   $deja_affiches_moin=$deja_affiches-$rupture;
   $precedent=false;
   echo '
   <ul class="pagination">
      <li class="active"><a href="#">'.$numrow.'</a></li>
   ';
   
   if ($deja_affiches>=$rupture) {
    echo "
      <li><a href=\"admin.php?op=suite_links&amp;deja_affiches=-".$deja_affiches_moin."\" >".adm_translate("Pr�c�dent")."</a></li>";
    $precedent=true;
   }
   if ($deja_affiches_plus<$numrow) {
    if ($precedent) echo "&nbsp;|&nbsp;";
    echo "<li><a href=\"admin.php?op=suite_links&amp;deja_affiches=".$deja_affiches_plus."\" >".adm_translate("Suivant")."</a></li>";
   }
   echo '
   </ul>';
   
   adminfieldinp($results);
   adminfoot('fv','','','');
}

function LinksModLink($lid) {
   global $NPDS_Prefix, $hlpfile, $f_meta_nom, $f_titre, $adminimg;
   include ("header.php");
   GraphicAdmin($hlpfile);
   global $anonymous;
   $result = sql_query("select cid, sid, title, url, description, name, email, hits from ".$NPDS_Prefix."links_links where lid='$lid'");
   adminhead($f_meta_nom, $f_titre, $adminimg);
   echo '<h3>'.adm_translate("Modifier le lien").' - '.$lid.'</h3>';
    list($cid, $sid, $title, $url, $xtext, $name, $email, $hits) = sql_fetch_row($result);
    $title = stripslashes($title); $xtext = stripslashes($xtext);
    echo '
   <form action="admin.php" method="post" name="adminForm">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title">'.adm_translate("Titre de la Page").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" id="title" value="'.$title.'" maxlength="100" required="required" />
               <span class="help-block text-right"><span id="countcar_title"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="url">'.adm_translate("URL de la Page").'</label>
            <div class="col-sm-8 col-md-8">
               <div class="input-group">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" ><a href="'.$url.'" target="_blank"><i class="fa fa-external-link fa-lg"></i></a></button>
                  </span>
                  <input class="form-control" type="text" name="url" id="url" value="'.$url.'" maxlength="100" required="required" />
                </div>
                <span class="help-block text-right"><span id="countcar_url"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="xtext">'.adm_translate("Description").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="xtext" rows="10">'.$xtext.'</textarea>
            </div>
         </div>
      </div>';
    echo aff_editeur("xtext","false");
    echo '
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="name">'.adm_translate("Nom").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="name" id="name" maxlength="100" value="'.$name.'" />
               <span class="help-block text-right"><span id="countcar_name"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="email">'.adm_translate("E-mail").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="email" name="email" id="email" maxlength="100" value="'.$email.'" />
               <span class="help-block text-right"><span id="countcar_email"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="hits">'.adm_translate("Nombre de Hits").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="number" name="hits" value="'.$hits.'" min="0" max="99999999999" />
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">';
    $result2=sql_query("select cid, title from ".$NPDS_Prefix."links_categories order by title");
    echo '
            <input type="hidden" name="lid" value="'.$lid.'" />
            <label class="form-control-label col-sm-4 col-md-4" for="hits">'.adm_translate("Cat�gorie").'</label>
            <div class="col-sm-8 col-md-8">
               <select class="form-control" name="cat">';
    while (list($ccid, $ctitle) = sql_fetch_row($result2)) {
       $sel = "";
       if ($cid==$ccid AND $sid==0) {
          $sel = "selected";
       }
       echo '<option value="'.$ccid.'" '.$sel.'>'.aff_langue($ctitle).'</option>';
       $result3=sql_query("select sid, title from ".$NPDS_Prefix."links_subcategories where cid='$ccid' order by title");
       while (list($ssid, $stitle) = sql_fetch_row($result3)) {
          $sel = "";
          if ($sid==$ssid) {
             $sel = "selected";
          }
          echo "<option value=\"$ccid-$ssid\" $sel>".aff_langue($ctitle)." / ".aff_langue($stitle)."</option>";
       }
    }

    echo '
               </select>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="op" value="LinksModLinkS" />
               <button class="btn btn-primary col-xs-6" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'.adm_translate("Modifier").' </button>
               <button href="admin.php?op=LinksDelLink&amp;lid='.$lid.'" class="btn btn-danger col-xs-6"><i class="fa fa-trash-o fa-lg"></i>&nbsp;'.adm_translate("Effacer").'</button>
            </div>
         </div>
      </div>
   </form>';

    //Modify or Add Editorial
    $resulted2 = sql_query("select adminid, editorialtimestamp, editorialtext, editorialtitle from ".$NPDS_Prefix."links_editorials where linkid='$lid'");
    $recordexist = sql_num_rows($resulted2);
    if ($recordexist == 0) {
       echo '
   <h3>'.adm_translate("Ajouter un Editorial").'</h3>
   <form action="admin.php" method="post">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="editorialtitle">'.adm_translate("Titre").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="editorialtitle" id="editorialtitle" maxlength="100" />
               <span class="help-block text-right"><span id="countcar_editorialtitle"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="editorialtext">'.adm_translate("Texte complet").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="editorialtext" rows="10"></textarea>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="linkid" value="'.$lid.'" />
               <input type="hidden" name="op" value="LinksAddEditorial" />
               <button class="btn btn-primary col-xs-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter un Editorial").'</button>
            </div>
         </div>
      </div>';
    } else {
      while(list($adminid, $editorialtimestamp, $editorialtext, $editorialtitle) = sql_fetch_row($resulted2)) {
         $editorialtitle = stripslashes($editorialtitle);
         $editorialtext = stripslashes($editorialtext);

         echo '
   <h3>'.adm_translate("Modifier l'Editorial").'</h3> - '.adm_translate("Auteur").' : '.$adminid.' : '.formatTimeStamp($editorialtimestamp);

         echo '
   <form action="admin.php" method="post">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="editorialtitle">'.adm_translate("Titre").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="editorialtitle" id="editorialtitle" value="'.$editorialtitle.'" maxlength="100" />
               <span class="help-block text-right"><span id="countcar_editorialtitle"></span></span>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="editorialtext">'.adm_translate("Texte complet").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="editorialtext" rows="10">'.$editorialtext.'</textarea>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="linkid" value="'.$lid.'" />
               <input type="hidden" name="op" value="LinksModEditorial" />
               <button class="btn btn-primary col-xs-6" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'.adm_translate("Modifier").'</button>
               <button href="admin.php?op=LinksDelEditorial&amp;linkid='.$lid.'" class="btn btn-danger col-xs-6"><i class="fa fa-trash-o fa-lg"></i>&nbsp;'.adm_translate("Effacer").'</button>
            </div>
         </div>
      </div>';
      }
    }
    echo '
   </form>';
   adminfieldinp($result);
   adminfieldinp($resulted2);
   adminfoot('fv','','','');
}

function LinksListBrokenLinks() {
   global $NPDS_Prefix, $hlpfile, $anonymous, $f_meta_nom, $f_titre, $adminimg;
   $resultBrok = sql_query("select requestid, lid, modifysubmitter from ".$NPDS_Prefix."links_modrequest where brokenlink='1' order by requestid");
   $totalbrokenlinks = sql_num_rows($resultBrok);
   if ($totalbrokenlinks==0) {
      header("location: admin.php?op=links");
   }
   include ("header.php");
   GraphicAdmin($hlpfile);
   adminhead($f_meta_nom, $f_titre, $adminimg);

   echo '
   <h3>'.adm_translate("Liens cass�s rapport�s par un ou plusieurs Utilisateurs").' ('.$totalbrokenlinks.')</h3>';

   echo "- ".adm_translate("Ignorer (Efface toutes les <b>demandes</b> pour un Lien donn�)")."<br />
    - ".adm_translate("Effacer (Efface les <b>Liens cass�s</b> et <b>les avis</b> pour un Lien donn�)");

   if ($totalbrokenlinks==0) {
      echo "<br /><br /><p align=\"center\" class=\"text-danger\">".adm_translate("Aucun lien bris� rapport�.")."</span>";
   } else {
      echo '
   <table id="tad_linkbrok" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons="icons" data-icons-prefix="fa">
      <thead>
         <tr>
            <th data-sortable="true" class="col-sm-4">'.adm_translate("Liens").'</th>
            <th data-sortable="true" class="col-sm-3">'.adm_translate("Auteur").'</th>
            <th data-sortable="true" class="col-sm-3">'.adm_translate("Propri�taire").'</th>
            <th data-sortable="true" class="col-sm-1" align=center>'.adm_translate("Ignorer").'</th>
            <th data-sortable="true" class="col-sm-1" align=center>'.adm_translate("Effacer").'</th>
         </tr>
      </thead>
      <tbody>
   ';

       while (list($requestid, $lid, $modifysubmitter)=sql_fetch_row($resultBrok)) {
          $result2 = sql_query("select title, url, submitter from ".$NPDS_Prefix."links_links where lid='$lid'");
          if ($modifysubmitter != $anonymous) {
             $result3 = sql_query("select email from ".$NPDS_Prefix."users where uname='$modifysubmitter'");
             list ($email)=sql_fetch_row($result3);
          }
          list ($title, $url, $owner)=sql_fetch_row($result2);
          $result4 = sql_query("select email from ".$NPDS_Prefix."users where uname='$owner'");
          list($owneremail)=sql_fetch_row($result4);
          $rowcolor=tablos();
          echo "<tr $rowcolor><td><a href=$url target=\"_blank\" class=\"noir\">$title</a></td>";
          if ($email=='') {
             echo "<td>$modifysubmitter";
          } else {
             echo "<td><a href=\"mailto:$email\" class=\"noir\">$modifysubmitter</a>";
          }
          echo '</td>';
          if ($owneremail=='') {
             echo '<td>'.$owner;
          } else {
             echo '<td><a href="mailto:'.$owneremail.'">'.$owner.'</a>';
          }
          echo "
          </td>
               <td align=\"center\"><a href=\"admin.php?op=LinksIgnoreBrokenLinks&amp;lid=$lid\" class=\"noir\">X</a></td>
               <td align=\"center\"><a href=admin.php?op=LinksDelBrokenLinks&amp;lid=$lid\" class=\"rouge\">X</a></td>
               </tr>";
       }
    }
   echo '
      </tbody>
   </table>';
   adminfoot('','','','');
}

function LinksDelBrokenLinks($lid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_modrequest where lid='$lid'");
    sql_query("delete from ".$NPDS_Prefix."links_links where lid='$lid'");

    global $aid; Ecr_Log("security", "DeleteBrokensLinks($lid) by AID : $aid", "");
    Header("Location: admin.php?op=LinksListBrokenLinks");
}

function LinksIgnoreBrokenLinks($lid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_modrequest where lid='$lid' and brokenlink='1'");
    Header("Location: admin.php?op=LinksListBrokenLinks");
}

function LinksListModRequests() {
    global $NPDS_Prefix;
    global $hlpfile;
    $resultLink = sql_query("select requestid, lid, cid, sid, title, url, description, modifysubmitter from ".$NPDS_Prefix."links_modrequest where brokenlink='0' order by requestid");
    $totalmodrequests = sql_num_rows($resultLink);
    if ($totalmodrequests==0) {
       header("location: admin.php?op=links");
    }
    include ("header.php");
    GraphicAdmin($hlpfile);
    opentable();
    echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\"><tr><td class=\"header\">\n";
    echo adm_translate("Requ�te de modification d'un Lien Utilisateur")." ($totalmodrequests)";
    echo "</td></tr></table>\n";
    while(list($requestid, $lid, $cid, $sid, $title, $url, $description, $modifysubmitter)=sql_fetch_row($resultLink)) {
       $result2 = sql_query("select cid, sid, title, url, description, submitter from ".$NPDS_Prefix."links_links where lid='$lid'");
       list($origcid, $origsid, $origtitle, $origurl, $origdescription, $owner)=sql_fetch_row($result2);
       $result3 = sql_query("select title from ".$NPDS_Prefix."links_categories where cid='$cid'");
       $result4 = sql_query("select title from ".$NPDS_Prefix."links_subcategories where cid='$cid' and sid='$sid'");
       $result5 = sql_query("select title from ".$NPDS_Prefix."links_categories where cid='$origcid'");
       $result6 = sql_query("select title from ".$NPDS_Prefix."links_subcategories where cid='$origcid' and sid='$origsid'");
       $result7 = sql_query("select email from ".$NPDS_Prefix."users where uname='$modifysubmitter'");
       $result8 = sql_query("select email from ".$NPDS_Prefix."users where uname='$owner'");
       list($cidtitle)=sql_fetch_row($result3);
       list($sidtitle)=sql_fetch_row($result4);
       list($origcidtitle)=sql_fetch_row($result5);
       list($origsidtitle)=sql_fetch_row($result6);
       list($modifysubmitteremail)=sql_fetch_row($result7);
       list($owneremail)=sql_fetch_row($result8);
       $title = stripslashes($title);
       $description = stripslashes($description);
       if ($owner=="") { $owner="administration"; }
       if ($origsidtitle=="") { $origsidtitle= "-----"; }
       if ($sidtitle=="") { $sidtitle= "-----"; }
       echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">\n";
       $rowcolor=tablos();
       echo "<tr $rowcolor><td><hr noshade class=\"ongl\">
             <table width=\"100%\"><tr>
             <td valign=\"top\" width=\"45%\"><span class=\"noir\"><b>".adm_translate("Original")."</b></span></td>
             <td rowspan=\"5\" valign=\"top\" align=\"left\" valign=\"top\"><span class=\"noir\"><b>".adm_translate("Description:")."</b></span><br />$origdescription</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Titre :")." $origtitle</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("URL : ")." <a href=\"$origurl\" target=\"_blank\" class=\"noir\">$origurl</a></td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Cat�gorie :")." $origcidtitle</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Sous-cat�gorie :")." $origsidtitle</td></tr>
             </table>
             </td></tr>";
       $rowcolor=tablos();
       echo "<tr $rowcolor><td>
             <table width=\"100%\"><tr>
             <td valign=\"top\" width=\"45%\"><b>".adm_translate("Propos�")."</b></td>
             <td rowspan=\"5\" valign=\"top\" align=\"left\" valign=\"top\"><span class=\"noir\"><b>".adm_translate("Description:")."</b></span><br />$description</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Titre :")." $title</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("URL : ")." <a href=\"$url\" target=\"_blank\" class=\"noir\">$url</a></td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Cat�gorie :")." $cidtitle</td></tr>
             <tr><td valign=\"top\" width=\"45%\">".adm_translate("Sous-cat�gorie :")." $sidtitle</td></tr>
             </table>
             </td></tr>";
       echo "</table>";
       echo "<table width=\"100%\" callspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td class=\"header\">\n";
       if ($modifysubmitteremail=="") {
          echo adm_translate("Auteur")." :  $modifysubmitter</td>";
       } else {
          echo adm_translate("Auteur")." : <a href=\"mailto:$modifysubmitteremail\" class=\"box\">$modifysubmitter</a></td>";
       }
       echo "<td class=\"header\">";
       if ($owneremail=="") {
          echo adm_translate("Propri�taire")." :  $owner</td>";
       } else {
          echo adm_translate("Propri�taire")." : <a href=\"mailto:$owneremail\" class=\"box\">$owner</a></td>";
       }
       echo "<td align=\"right\">[ <a href=\"admin.php?op=LinksChangeModRequests&amp;requestid=$requestid\" class=\"rouge\">".adm_translate("Accepter")."</a> | <a href=\"admin.php?op=LinksChangeIgnoreRequests&amp;requestid=$requestid\" class=\"noir\">".adm_translate("Ignorer")."</a> ]</td>
       </tr></table><hr noshade class=\"ongl\"><br />\n";
    }
    closetable();
    include ("footer.php");
}

function LinksChangeModRequests($Xrequestid) {
    global $NPDS_Prefix;
    $result = sql_query("select requestid, lid, cid, sid, title, url, description from ".$NPDS_Prefix."links_modrequest where requestid='$Xrequestid'");
    while (list($requestid, $lid, $cid, $sid, $title, $url, $description)=sql_fetch_row($result)) {
       $title = stripslashes($title);
       $description = stripslashes($description);
       sql_query("UPDATE ".$NPDS_Prefix."links_links SET cid='$cid', sid='$sid', title='$title', url='$url', description='$description' WHERE lid = '$lid'");
    }
    sql_query("delete from ".$NPDS_Prefix."links_modrequest where requestid='$Xrequestid'");

    global $aid; Ecr_Log("security", "UpdateModRequestLinks($Xrequestid) by AID : $aid", "");
    Header("Location: admin.php?op=LinksListModRequests");
}

function LinksChangeIgnoreRequests($requestid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_modrequest where requestid='$requestid'");
    Header("Location: admin.php?op=LinksListModRequests");
}

function LinksModLinkS($lid, $title, $url, $xtext, $name, $email, $hits, $cat) {
    global $NPDS_Prefix;
    $cat = explode("-", $cat);
    if (!array_key_exists(1,$cat)) {
       $cat[1] = 0;
    }
    $title = stripslashes(FixQuotes($title));
    $url = stripslashes(FixQuotes($url));
    $xtext = stripslashes(FixQuotes($xtext));
    $name = stripslashes(FixQuotes($name));
    $email = stripslashes(FixQuotes($email));
    sql_query("update ".$NPDS_Prefix."links_links set cid='$cat[0]', sid='$cat[1]', title='$title', url='$url', description='$xtext', name='$name', email='$email', hits='$hits' where lid='$lid'");

    global $aid; Ecr_Log("security", "UpdateLinks($lid, $title) by AID : $aid", "");
    Header("Location: admin.php?op=links");
}

function LinksDelLink($lid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_links where lid='$lid'");

    global $aid; Ecr_Log("security", "DeleteLinks($lid) by AID : $aid", "");
    Header("Location: admin.php?op=links");
}

function LinksModCat($cat) {
    global $NPDS_Prefix, $hlpfile, $f_meta_nom, $f_titre, $adminimg;
    include ("header.php");
    GraphicAdmin($hlpfile);
    $cat = explode("-", $cat);
    if (!array_key_exists(1,$cat)) {
       $cat[1] = 0;
    }
    adminhead($f_meta_nom, $f_titre, $adminimg);
    if ($cat[1]==0) {
        echo '<h3>'.adm_translate("Modifier la Cat�gorie").'</h3>';
        $result=sql_query("select title, cdescription from ".$NPDS_Prefix."links_categories where cid='$cat[0]'");
        list($title,$cdescription) = sql_fetch_row($result);
        $cdescription = stripslashes($cdescription);
        echo '
   <form action="admin.php" method="get">
      <div class="form-group">
            <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title">'.adm_translate("Nom").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" value="'.$title.'" maxlength="50" />
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="cdescription">'.adm_translate("Description").'</label>
            <div class="col-sm-8 col-md-8">
               <textarea class="form-control" name="cdescription" rows="10" >'.$cdescription.'</textarea>
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="sub" value="0">
               <input type="hidden" name="cid" value="'.$cat[0].'">
               <input type="hidden" name="op" value="LinksModCatS">
               <button class="btn btn-primary col-xs-6" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'.adm_translate("Modifier").'</button>
               <button href="admin.php?op=LinksDelCat&amp;sub=0&amp;cid='.$cat[0].'" class="btn btn-danger col-xs-6"><i class="fa fa-trash-o fa-lg"></i>&nbsp;'.adm_translate("Effacer").'</button>
            </div>
         </div>
      </div>
   </form>';
    } else {
        $result=sql_query("select title from ".$NPDS_Prefix."links_categories where cid='$cat[0]'");
        list($ctitle) = sql_fetch_row($result);
        $result2=sql_query("select title from ".$NPDS_Prefix."links_subcategories where sid='$cat[1]'");
        list($stitle) = sql_fetch_row($result2);

        echo '
   <h3>'.adm_translate("Modifier la Cat�gorie")." - ".adm_translate("Nom de la Cat�gorie : ").aff_langue($ctitle).'</h3>
   <form action="admin.php" method="get">
      <div class="form-group">
         <div class="row">
            <label class="form-control-label col-sm-4 col-md-4" for="title">'.adm_translate("Nom de la Sous-cat�gorie").'</label>
            <div class="col-sm-8 col-md-8">
               <input class="form-control" type="text" name="title" value="'.$stitle.'" maxlength="50">
            </div>
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class="col-sm-offset-4 col-sm-8">
               <input type="hidden" name="sub" value="1">
               <input type="hidden" name="cid" value="'.$cat[0].'">
               <input type="hidden" name="sid" value="'.$cat[1].'">
               <input type="hidden" name="op" value="LinksModCatS">
               <button class="btn btn-primary col-xs-6" type="submit"><i class="fa fa-check fa-lg"></i>&nbsp;'.adm_translate("Modifier").'</button>
               <button href="admin.php?op=LinksDelCat&amp;sub=1&amp;cid='.$cat[0].'&amp;sid='.$cat[1].'" class="btn btn-danger col-xs-6"><i class="fa fa-trash-o fa-lg"></i>&nbsp;'.adm_translate("Effacer").'</button>
            </div>
         </div>
      </div>
   </form>';
    }
   adminfoot('','','','');
}

function LinksModCatS($cid, $sid, $sub, $title, $cdescription) {
    global $NPDS_Prefix;
    if ($sub==0) {
        sql_query("update ".$NPDS_Prefix."links_categories set title='$title', cdescription='$cdescription' where cid='$cid'");
        global $aid; Ecr_Log("security", "UpdateCatLinks($cid, $title) by AID : $aid", "");
    } else {
        sql_query("update ".$NPDS_Prefix."links_subcategories set title='$title' where sid='$sid'");
        global $aid; Ecr_Log("security", "UpdateSubCatLinks($cid, $title) by AID : $aid", "");
    }
    Header("Location: admin.php?op=links");
}

function LinksDelCat($cid, $sid, $sub, $ok=0) {
    global $NPDS_Prefix;
    if ($ok==1) {
        if ($sub>0) {
           sql_query("delete from ".$NPDS_Prefix."links_subcategories where sid='$sid'");
           sql_query("delete from ".$NPDS_Prefix."links_links where sid='$sid'");
           global $aid; Ecr_Log("security", "DeleteSubCatLinks($sid) by AID : $aid", "");
        } else {
           sql_query("delete from ".$NPDS_Prefix."links_categories where cid='$cid'");
           sql_query("delete from ".$NPDS_Prefix."links_subcategories where cid='$cid'");
           sql_query("delete from ".$NPDS_Prefix."links_links where cid='$cid' AND sid=0");
           global $aid; Ecr_Log("security", "DeleteCatLinks($cid) by AID : $aid", "");
        }
        Header("Location: admin.php?op=links");
    } else {
        message_error("<span class=\"rouge\"><b>".adm_translate("ATTENTION : Etes-vous s�r de vouloir effacer cette Cat�gorie et tous ses Liens ?")."</b></span><br /><br />
        [ <a href=\"admin.php?op=LinksDelCat&amp;cid=$cid&amp;sid=$sid&amp;sub=$sub&amp;ok=1\" class=\"rouge\">".adm_translate("Oui")."</a> | <a href=\"admin.php?op=links\" class=\"noir\">".adm_translate("Non")."</a> ]");
    }
}

function LinksDelNew($lid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_newlink where lid='$lid'");

    global $aid; Ecr_Log("security", "DeleteNewLinks($lid) by AID : $aid", "");
    Header("Location: admin.php?op=links");
}

function LinksAddCat($title, $cdescription) {
    global $NPDS_Prefix;
    $result = sql_query("select cid from ".$NPDS_Prefix."links_categories where title='$title'");
    $numrows = sql_num_rows($result);
    if ($numrows>0) {
        message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : La Cat�gorie")." $title ".adm_translate("existe d�j� !")."</b></span>");
    } else {
        sql_query("insert into ".$NPDS_Prefix."links_categories values (NULL, '$title', '$cdescription')");

        global $aid; Ecr_Log("security", "AddCatLinks($title) by AID : $aid", "");
        Header("Location: admin.php?op=links");
    }
}

function LinksAddSubCat($cid, $title) {
    global $NPDS_Prefix;
    $result = sql_query("select cid from ".$NPDS_Prefix."links_subcategories where title='$title' AND cid='$cid'");
    $numrows = sql_num_rows($result);
    if ($numrows>0) {
        message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : La Sous-cat�gorie")." $title ".adm_translate("existe d�j� !")."</b></span>");
    } else {
        sql_query("insert into ".$NPDS_Prefix."links_subcategories values (NULL, '$cid', '$title')");

        global $aid; Ecr_Log("security", "AddSubCatLinks($title) by AID : $aid", "");
        Header("Location: admin.php?op=links");
    }
}

function LinksAddEditorial($linkid, $editorialtitle, $editorialtext) {
    global $NPDS_Prefix;
    global $aid;
    $editorialtext = stripslashes(FixQuotes($editorialtext));
    sql_query("insert into ".$NPDS_Prefix."links_editorials values ('$linkid', '$aid', now(), '$editorialtext', '$editorialtitle')");

    global $aid; Ecr_Log("security", "AddEditorialLinks($linkid, $editorialtitle) by AID : $aid", "");
    message_error("<span class=\"noir\"><b>".adm_translate("Editorial ajout� � la base de donn�es")."</b></span>");
}

function LinksModEditorial($linkid, $editorialtitle, $editorialtext) {
    global $NPDS_Prefix;
    $editorialtext = stripslashes(FixQuotes($editorialtext));
    sql_query("update ".$NPDS_Prefix."links_editorials set editorialtext='$editorialtext', editorialtitle='$editorialtitle' where linkid='$linkid'");

    global $aid; Ecr_Log("security", "ModEditorialLinks($linkid, $editorialtitle) by AID : $aid", "");
    message_error("<span class=\"noir\"><b>".adm_translate("Editorial modifi�")."</b></span>");
}

function LinksDelEditorial($linkid) {
    global $NPDS_Prefix;
    sql_query("delete from ".$NPDS_Prefix."links_editorials where linkid='$linkid'");

    global $aid; Ecr_Log("security", "DeteteEditorialLinks($linkid) by AID : $aid", "");
    message_error("<span class=\"noir\"><b>".adm_translate("Editorial supprim� de la base de donn�es")."</b></span>");
}

function message_error($ibid) {
    global $hlpfile;
    include("header.php");
    GraphicAdmin($hlpfile);
    opentable();
    echo "<p align=\"center\"><br />";
    echo $ibid;
    echo "<br /><br /><a href=\"admin.php?op=links\" class=\"noir\">".adm_translate("Retour en arri�re")."</a>";
    echo "<br /></p>";
    closetable();
    include("footer.php");
}

function LinksAddLink($new, $lid, $title, $url, $cat, $xtext, $name, $email, $submitter) {
    global $NPDS_Prefix;
    $result = sql_query("select url from ".$NPDS_Prefix."links_links where url='$url'");
    $numrows = sql_num_rows($result);
    if ($numrows>0) {
        message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : cette URL est d�j� pr�sente dans la base de donn�es !")."</b></span>");
    } else {
       // Check if Title exist
       if ($title=="") {
           message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : vous devez saisir un TITRE pour votre Lien !")."</b></span>");
       }
       // Check if URL exist
       if ($url=="") {
          message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : vous devez saisir une URL pour votre Lien !")."</b></span>");
       }
       // Check if Description exist
       if ($xtext=="") {
          message_error("<span class=\"rouge\"><b>".adm_translate("Erreur : vous devez saisir une DESCRIPTION pour votre Lien !")."</b></span>");
       }
       $cat = explode("-", $cat);
       if (!array_key_exists(1,$cat)) {
          $cat[1] = 0;
       }
       $title = stripslashes(FixQuotes($title));
       $url = stripslashes(FixQuotes($url));
       $xtext = stripslashes(FixQuotes($xtext));
       $name = stripslashes(FixQuotes($name));
       $email = stripslashes(FixQuotes($email));
       sql_query("insert into ".$NPDS_Prefix."links_links values (NULL, '$cat[0]', '$cat[1]', '$title', '$url', '$xtext', now(), '$name', '$email', '0','$submitter',0,0,0,'')");
       if ($new==1) {
          sql_query("delete from ".$NPDS_Prefix."links_newlink where lid='$lid'");
          if ($email!="") {
             global $sitename, $nuke_url;
             $subject = "".adm_translate("Votre Lien")." : $sitename";
             $message = "".adm_translate("Bonjour")." $name :\n\n".adm_translate("Nous avons approuv� votre contribution � notre moteur de recherche.")."\n\n".adm_translate("Titre de la Page : ")."$title\n".adm_translate("URL de la Page : ")."<a href=\"$url\">$url</a>\n".adm_translate("Description : ")."$xtext\n".adm_translate("Vous pouvez utiliser notre moteur de recherche sur : ")." <a href=\"$nuke_url/modules.php?ModPath=links&ModStart=links\">$nuke_url/modules.php?ModPath=links&ModStart=links</a>\n\n".adm_translate("Merci pour votre Contribution !")."\n";
             include("signat.php");
             send_email($email, $subject, $message, "", false, "html");
          }
       }
       global $aid; Ecr_Log("security", "AddLinks($title) by AID : $aid", "");
       message_error("<span class=\"noir\"><b>".adm_translate("Nouveau Lien ajout� dans la base de donn�es")."</b></span>");
    }
}

switch ($op) {
    case "links":
    case "suite_links":
         links();
         break;

    case "LinksDelNew":
         LinksDelNew($lid);
         break;

    case "LinksAddCat":
         LinksAddCat($title, $cdescription);
         break;

    case "LinksAddSubCat":
         LinksAddSubCat($cid, $title);
         break;

    case "LinksAddLink":
         LinksAddLink($new, $lid, $title, $url, $cat, $xtext, $name, $email, $submitter);
         break;

    case "LinksAddEditorial":
         LinksAddEditorial($linkid, $editorialtitle, $editorialtext);
         break;

    case "LinksModEditorial":
         LinksModEditorial($linkid, $editorialtitle, $editorialtext);
         break;

    case "LinksDelEditorial":
         LinksDelEditorial($linkid);
         break;

    case "LinksListBrokenLinks":
         LinksListBrokenLinks();
         break;

    case "LinksDelBrokenLinks":
         LinksDelBrokenLinks($lid);
         break;

    case "LinksIgnoreBrokenLinks":
         LinksIgnoreBrokenLinks($lid);
         break;

    case "LinksListModRequests":
         LinksListModRequests();
         break;

    case "LinksChangeModRequests":
         LinksChangeModRequests($requestid);
         break;

    case "LinksChangeIgnoreRequests":
         LinksChangeIgnoreRequests($requestid);
         break;

    case "LinksDelCat":
         LinksDelCat($cid, $sid, $sub, $ok);
         break;

    case "LinksModCat":
         LinksModCat($cat);
         break;

    case "LinksModCatS":
         LinksModCatS($cid, $sid, $sub, $title, $cdescription);
         break;

    case "LinksModLink":
         LinksModLink($lid);
         break;

    case "LinksModLinkS":
         LinksModLinkS($lid, $title, $url, $xtext, $name, $email, $hits, $cat);
         break;

    case "LinksDelLink":
         LinksDelLink($lid);
         break;
}
?>