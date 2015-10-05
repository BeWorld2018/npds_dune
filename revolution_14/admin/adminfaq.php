<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2015 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
if (!stristr($_SERVER['PHP_SELF'],"admin.php")) { Access_Error(); }
$f_meta_nom ='FaqAdmin';
$f_titre = adm_translate("Faq");
//==> controle droit
admindroits($aid,$f_meta_nom);
//<== controle droit

global $language, $adminimg, $admf_ext;
$hlpfile = "manuels/$language/faqs.html";

function FaqAdmin() {
   global $hlpfile, $NPDS_Prefix, $admf_ext, $f_meta_nom, $f_titre, $adminimg;
   include ("header.php");
   GraphicAdmin($hlpfile);
   adminhead ($f_meta_nom, $f_titre, $adminimg);
   echo '
   <h3>'.adm_translate("Liste des cat�gories").'</h3>
   <table id="tad_faq" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
      <thead class="thead-default">
         <tr>
            <th data-sortable="true" class="">'.adm_translate("Cat�gories").'</th>
            <th class="">'.adm_translate("Fonctions").'</th>
         </tr>
      </thead>
      <tbody>';
   $result = sql_query("select id_cat, categories from ".$NPDS_Prefix."faqcategories order by id_cat ASC");
   while(list($id_cat, $categories) = sql_fetch_row($result)) {
      echo '
         <tr>
            <td><span title="ID : '.$id_cat.'">'.aff_langue($categories).'</span><br /><a href="admin.php?op=FaqCatGo&amp;id_cat='.$id_cat.'" class="noir"><i class="fa fa-level-up fa-lg fa-rotate-90 " title="'.adm_translate("Voir").'"></i>&nbsp;&nbsp;'.adm_translate("Questions & R�ponses").'&nbsp;</a></td>
            <td align="right"><a href="admin.php?op=FaqCatEdit&amp;id_cat='.$id_cat.'"><i class="fa fa-edit fa-lg" title="Editer"></i></a> <a href="admin.php?op=FaqCatDel&amp;id_cat='.$id_cat.'&amp;ok=0"><i class="fa fa-trash-o fa-lg text-danger" title="'.adm_translate("Effacer").'" data-toggle="tooltip"></a></td>
         </tr>';
   }
   echo '
      </tbody>
   </table>
   <h3>'.adm_translate("Ajouter une cat�gorie").'</h3>
   <form id="fad_faqcatad" action="admin.php" method="post">
      <fieldset>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-sm-12" for="categories">'.adm_translate("Nom").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" type="text" name="categories" id="categories" maxlength="255" placeholder="'.adm_translate("Cat�gories").'" rows="3" required="required" ></textarea>
                  <span class="help-block text-right"><span id="countcar_categories"></span></span>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="row">
               <div class="col-sm-12">
                  <button class="btn btn-primary-outline col-xs-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter une cat�gorie").'</button>
                  <input type="hidden" name="op" value="FaqCatAdd" />
               </div>
            </div>
         </div>
      </fieldset>
   </form>';
   adminfieldinp($result);
   adminfoot('fv','','','');
}

function FaqCatGo($id_cat) {
   global $hlpfile, $NPDS_Prefix, $admf_ext, $f_meta_nom, $f_titre, $adminimg;
   include ("header.php");
   GraphicAdmin($hlpfile);
   $lst_qr ='';

   $result = sql_query("select fa.id, fa.question, fa.answer, fc.categories from ".$NPDS_Prefix."faqanswer fa left join ".$NPDS_Prefix."faqcategories fc ON fa.id_cat = fc.id_cat where fa.id_cat='$id_cat' order by id");
   while(list($id, $question, $answer, $categories) = sql_fetch_row($result)) {
      $faq_cat = aff_langue($categories);
      $answer = aff_code(aff_langue($answer));
      $lst_qr.= '
      <a id="qr_'.$id.'" href="admin.php?op=FaqCatGoEdit&amp;id='.$id.'" class="list-group-item topi" title="'.adm_translate("Editer la question r&#xE9;ponse").'" data-toggle="tooltip" >
         <h5 class="list-group-item-heading">'.aff_langue($question).'</h5>
         <p class="list-group-item-text">'.meta_lang($answer).'</p>
      </a>';
   }
   adminhead ($f_meta_nom, $f_titre, $adminimg);
   echo '
   <h3>'.$faq_cat.'</h3>
   <h4>'.adm_translate("Ajouter une question r&#xE9;ponse").'</h4>
   <form action="admin.php" method="post" name="adminForm">
      <fieldset>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-sm-12" for="question">'.adm_translate("Question").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" type="text" name="question" id="question" maxlength="255"></textarea>
                  <span class="help-block text-right"><span id="countcar_question"></span></span>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-sm-12" for="answer">'.adm_translate("R&#xE9;ponse").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" name="answer" rows="15"></textarea>
               </div>
            </div>
         </div>';
   echo aff_editeur("answer","false");
   echo '
         <div class="form-group">
            <div class="row">
               <div class="col-sm-12">
                  <input type="hidden" name="id_cat" value="'.$id_cat.'" />
                  <input type="hidden" name="op" value="FaqCatGoAdd" />'."\n".'
                  <button class="btn btn-primary col-xs-6" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'.adm_translate("Ajouter").'</button>
                  <button class="btn btn-secondary col-xs-6" href="admin.php?op=FaqAdmin">'.adm_translate("Retour en arri&#xE8;re").'</button>
               </div>
            </div>
         </div>
      </fieldset>
   </form>
   <h4>'.adm_translate("Liste des questions r&#xE9;ponses").'</h4>
   <div class="list-group">
      '.$lst_qr.'
   </div>';


echo '  
<script type="text/javascript">
   //<![CDATA[
   $(function() {
      var topid="";
      $(".topi").hover(function(){
         topid = $(this).attr("id");
         topid=topid.substr (topid.search(/\d/))
         $button = $(\'<div id="shortcut-tools" class=""><a class="text-danger btn" href="admin.php?op=FaqCatGoDel&amp;id=\'+topid+\'&amp;ok=0" ><i class="fa fa-trash-o fa-2x" title="'.adm_translate("Supprimer la question r&#xE9;ponse").'" data-toggle="tooltip" data-placement="left"></i></a></div>\')
         $(this).append($button);
         $button.show();
      }, function(){
       $button.hide();
     })
     });
   //]]>
</script>
';
   adminfieldinp($result);
   adminfoot('fv','','','');
}

function FaqCatEdit($id_cat) {
   global $hlpfile, $NPDS_Prefix, $admf_ext, $f_meta_nom, $f_titre, $adminimg;
   include ("header.php");
   GraphicAdmin($hlpfile);
   adminhead ($f_meta_nom, $f_titre, $adminimg);
   $result = sql_query("select categories from ".$NPDS_Prefix."faqcategories where id_cat='$id_cat'");
   list($categories) = sql_fetch_row($result);
   echo '
   <h3>'.adm_translate("Editer la Cat�gorie").'</h3>
   <h4><a href="admin.php?op=FaqCatGo&id_cat='.$id_cat.'">'.$categories.'</a></h4>
   <form id="fad_faqcated" action="admin.php" method="post">
      <fieldset>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-sm-12" for="categories">'.adm_translate("Nom").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" type="text" name="categories" id="categories" maxlength="255" rows="3" required="required" >'.$categories.'</textarea>
                  <span class="help-block text-right"><span id="countcar_categories"></span></span>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="row">
               <div class="col-sm-12">
                  <input type="hidden" name="op" value="FaqCatSave" />
                  <input type="hidden" name="old_id_cat" value="'.$id_cat.'" />
                  <input type="hidden" name="id_cat" value="'.$id_cat.'" />
                  <button class="btn btn-primary-outline col-xs-12" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;'.adm_translate("Sauver les modifications").'</button>
               </div>
            </div>
         </div>
      </fieldset>
   </form>';
   adminfieldinp($result);
   adminfoot('fv','','','');
}

function FaqCatGoEdit($id) {
    global $hlpfile, $NPDS_Prefix, $local_user_language, $admf_ext, $f_meta_nom, $f_titre, $adminimg;
    include ("header.php");
    GraphicAdmin($hlpfile);

    $result = sql_query("select fa.question, fa.answer, fa.id_cat, fc.categories from ".$NPDS_Prefix."faqanswer fa left join ".$NPDS_Prefix."faqcategories fc ON fa.id_cat = fc.id_cat where fa.id='$id'");
    list($question, $answer, $id_cat, $faq_cat) = sql_fetch_row($result);

    adminhead ($f_meta_nom, $f_titre, $adminimg);
    echo '
    <h3>'.$faq_cat.'</h3>
    <h4>'.$question.'</h4>
    <h4>'.adm_translate("Pr&#xE9;visualiser").'</h4>';
    echo'
    <div class="pure-g-r ligna">'."\n".'
        <div class="pure-u-1-5 ligna">'."\n".'
            <div id="lang_preview" class="l-box">'."\n"
            .aff_local_langue(adm_translate("Langue de Pr�visualisation"),"","local_user_language").'
            </div>'."\n".'
        </div>'."\n".'
        <div class="pure-u-4-5 lignb">'."\n".'
            <div class="l-box">'."\n".'<p>
            '.preview_local_langue($local_user_language, $question).'</p>'."\n";
            $answer= aff_code($answer);
            echo '<p>'.meta_lang(preview_local_langue($local_user_language, $answer)).'</p>
            </div>
        </div>
    </div>';

    echo '
   <h4>'.adm_translate("Editer Question & R�ponse").'</h4>
   <form action="admin.php" method="post" name="adminForm">
      <fieldset>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-xs-12" for="question">'.adm_translate("Question").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" type="text" name="question" id="question" maxlength="255">'.$question.'</textarea>
                  <span class="help-block text-right"><span id="countcar_question"></span></span>
               </div>
            </div>
         </div>
         <div class="form-group">
            <div class="row">
               <label class="form-control-label col-xs-12" for="answer">'.adm_translate("R�ponse").'</label>
               <div class="col-sm-12">
                  <textarea class="form-control" name="answer" rows="15">'.$answer.'</textarea>
               </div>
            </div>
         </div>';

    echo aff_editeur("answer","false");
    echo '
         <div class="form-group">
            <div class="row">
               <div class="col-sm-12">
                  <input type="hidden" name="id" value="'.$id.'" />
                  <input type="hidden" name="op" value="FaqCatGoSave" />
                  <button class="btn btn-primary-outline col-xs-12 col-sm-6" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;'.adm_translate("Sauver les modifications").'</button>
                  <button class="btn btn-secondary-outline col-xs-12 col-sm-6" href="admin.php?op=FaqCatGo&amp;id_cat='.$id_cat.'" >'.adm_translate("Retour en arri�re").'</a>
               </div>
            </div>
         </div>
      </fieldset>
   </form>';
   adminfieldinp($result);
   adminfoot('fv','','','');
}

function FaqCatSave($old_id_cat, $id_cat, $categories) {
    global $NPDS_Prefix;

    $categories = stripslashes(FixQuotes($categories));
    if ($old_id_cat!=$id_cat) {
       sql_query("update ".$NPDS_Prefix."faqanswer set id_cat='$id_cat' where id_cat='$old_id_cat'");
    }
    sql_query("update ".$NPDS_Prefix."faqcategories set id_cat='$id_cat', categories='$categories' where id_cat='$old_id_cat'");
    Header("Location: admin.php?op=FaqAdmin");
}

function FaqCatGoSave($id, $question, $answer) {
    global $NPDS_Prefix;

    $question = stripslashes(FixQuotes($question));
    $answer = stripslashes(FixQuotes($answer));
    sql_query("update ".$NPDS_Prefix."faqanswer set question='$question', answer='$answer' where id='$id'");
    Header("Location: admin.php?op=FaqCatGoEdit&id=$id");
}

function FaqCatAdd($categories) {
    global $NPDS_Prefix;

    $categories = stripslashes(FixQuotes($categories));
    sql_query("insert into ".$NPDS_Prefix."faqcategories values (NULL, '$categories')");
    Header("Location: admin.php?op=FaqAdmin");
}

function FaqCatGoAdd($id_cat, $question, $answer) {
    global $NPDS_Prefix;

    $question = stripslashes(FixQuotes($question));
    $answer = stripslashes(FixQuotes($answer));
    sql_query("insert into ".$NPDS_Prefix."faqanswer values (NULL, '$id_cat', '$question', '$answer')");
    Header("Location: admin.php?op=FaqCatGo&id_cat=$id_cat");
}

function FaqCatDel($id_cat, $ok=0) {
    global $NPDS_Prefix;

    if($ok==1) {
        sql_query("delete from ".$NPDS_Prefix."faqcategories where id_cat='$id_cat'");
        sql_query("delete from ".$NPDS_Prefix."faqanswer where id_cat='$id_cat'");
        Header("Location: admin.php?op=FaqAdmin");
    } else {
        global $hlpfile;
        include("header.php");
        GraphicAdmin($hlpfile);
        opentable();
        echo "<p align=\"center\"><br />";
        echo "<span class=\"rouge\"><b>".adm_translate("ATTENTION : �tes-vous s�r de vouloir effacer cette FAQ et toutes ses questions ?")."</b></span><br /><br />";
    }
    echo "[ <a href=\"admin.php?op=FaqCatDel&amp;id_cat=$id_cat&amp;ok=1\" class=\"rouge\">".adm_translate("Oui")."</a> | <a href=\"admin.php?op=FaqAdmin\" class=\"noir\">".adm_translate("Non")."</a> ]<br /><br /></p>";
    closetable();
    include("footer.php");
}

function FaqCatGoDel($id, $ok=0) {
    global $NPDS_Prefix;

    if($ok==1) {
        sql_query("delete from ".$NPDS_Prefix."faqanswer where id='$id'");
        Header("Location: admin.php?op=FaqAdmin");
    } else {
        global $hlpfile;
        include("header.php");
        GraphicAdmin($hlpfile);
        opentable();
        echo "<p align=\"center\"><br />";
        echo "<span class=\"rouge\"><b>".adm_translate("ATTENTION : �tes-vous s�r de vouloir effacer cette question ?")."</b></span><br /><br />";
    }
    echo "[ <a href=\"admin.php?op=FaqCatGoDel&amp;id=$id&amp;ok=1\" class=\"rouge\">".adm_translate("Oui")."</a> | <a href=\"admin.php?op=FaqAdmin\" class=\"noir\">".adm_translate("Non")."</a> ]<br /><br /></p>";
    closetable();
    include("footer.php");
}
?>