<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* DYNAMIC THEME engine for NPDS                                        */
/* NPDS Copyright (c) 2002-2017 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
global $meta_glossaire;
function local_var($Xcontent) {
   if (strstr($Xcontent,"!var!")) {
      $deb=strpos($Xcontent,"!var!",0)+5;
      $fin=strpos($Xcontent,' ',$deb);
      if ($fin) {$H_var=substr($Xcontent,$deb,$fin-$deb);}
      else {$H_var=substr($Xcontent,$deb);}
      return ($H_var);
   }
}
function themeindex ($aid, $informant, $time, $title, $counter, $topic, $thetext, $notes, $morelink, $topicname, $topicimage, $topictext, $id) {
   global $tipath, $theme, $nuke_url;
   $inclusion=false;
   if (file_exists("themes/".$theme."/html/index-news.html")) {$inclusion="themes/".$theme."/html/index-news.html";}
   elseif (file_exists("themes/default/html/index-news.html")) {$inclusion="themes/default/html/index-news.html";}
   else {
      echo 'index-news.html manquant / not find !<br />';
      die();
   }
   $H_var=local_var($thetext);
   if ($H_var!='') {
      ${$H_var}=true;
      $thetext=str_replace("!var!$H_var","",$thetext);
   }
   if ($notes!= '') $notes='<div class="note">'.translate("Note").' : '.$notes.'</div>';
   ob_start();
   include($inclusion);
   $Xcontent=ob_get_contents();
   ob_end_clean();

   $lire_la_suite='';
   if ($morelink[0]) $lire_la_suite=$morelink[1].' '.$morelink[0].' | ';
   $commentaire='';
   if ($morelink[2])
      $commentaire=$morelink[2].' '.$morelink[3].' | ';
   else
      $commentaire=$morelink[3].' | ';
   $categorie='';
   if ($morelink[6]) $categorie=' : '.$morelink[6];
   $morel=$lire_la_suite.$commentaire.$morelink[4].' '.$morelink[5].$categorie;

   if (!$imgtmp=theme_image('topics/'.$topicimage)) {$imgtmp=$tipath.$topicimage;}
   $timage=$imgtmp;

   $npds_METALANG_words=array(
   "'!N_publicateur!'i"=>$aid,
   "'!N_emetteur!'i"=>userpopover($informant).'<a href="user.php?op=userinfo&amp;uname='.$informant.'">'.$informant.'</a>',

   "'!N_date!'i"=>formatTimestamp($time),
   "'!N_date_y!'i"=>substr($time,0,4),
   "'!N_date_m!'i"=>strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
   "'!N_date_d!'i"=>substr($time,8,2),
   "'!N_date_h!'i"=>substr($time,11),
   "'!N_print!'i"=>$morelink[4],
   "'!N_friend!'i"=>$morelink[5],

   "'!N_nb_carac!'i"=>$morelink[0],
   "'!N_read_more!'i"=>$morelink[1],
   "'!N_nb_comment!'i"=>$morelink[2],
   "'!N_link_comment!'i"=>$morelink[3],
   "'!N_categorie!'i"=>$morelink[6],

   "'!N_titre!'i"=>$title,
   "'!N_texte!'i"=>$thetext,
   "'!N_id!'i"=>$id,
   "'!N_sujet!'i"=>'<a href="search.php?query=&amp;topic='.$topic.'"><img class="img-fluid" src="'.$timage.'" alt="'.translate("Search in").'&nbsp;'.$topictext.'" /></a>',
   "'!N_note!'i"=>$notes,
   "'!N_nb_lecture!'i"=>$counter,
   "'!N_suite!'i"=>$morel
   );
   echo meta_lang(aff_langue(preg_replace(array_keys($npds_METALANG_words),array_values($npds_METALANG_words), $Xcontent)));
}
function themearticle ($aid, $informant, $time, $title, $thetext, $topic, $topicname, $topicimage, $topictext, $id, $previous_sid, $next_sid, $archive) {
   global $tipath, $theme, $nuke_url, $counter;
   global $boxtitle, $boxstuff, $short_user,$user;
   $inclusion=false;
   if (file_exists("themes/".$theme."/html/detail-news.html")) {$inclusion="themes/".$theme."/html/detail-news.html";}
   elseif (file_exists("themes/default/html/detail-news.html")) {$inclusion="themes/default/html/detail-news.html";}
   else {
      echo 'detail-news.html manquant / not find !<br />';
      die();
   }
   $H_var=local_var($thetext);
   if ($H_var!='') {
      ${$H_var}=true;
      $thetext=str_replace("!var!$H_var",'',$thetext);
   }
   ob_start();
   include($inclusion);
   $Xcontent=ob_get_contents();
   ob_end_clean();
   if ($previous_sid)
      $prevArt='<a href="article.php?sid='.$previous_sid.'&amp;archive='.$archive.'" ><i class="fa fa-chevron-left fa-lg mr-2" title="'.translate("Previous").'" data-toggle="tooltip"></i><span class="d-none d-sm-inline">'.translate("Previous").'</span></a>';
   else $prevArt='';
   if ($next_sid) $nextArt='<a href="article.php?sid='.$next_sid.'&amp;archive='.$archive.'" ><span class="d-none d-sm-inline">'.translate("Next").'</span><i class="fa fa-chevron-right fa-lg ml-2" title="'.translate("Next").'" data-toggle="tooltip"></i></a>';
   else $nextArt='';

   $printP = '<a href="print.php?sid='.$id.'" title="'.translate("Printer Friendly Page").'" data-toggle="tooltip"><i class="fa fa-2x fa-print"></i></a>';
   $sendF = '<a href="friend.php?op=FriendSend&amp;sid='.$id.'" title="'.translate("Send this Story to a Friend").'" data-toggle="tooltip"><i class="fa fa-lg fa-envelope-o"></i></a>';

   if (!$imgtmp=theme_image('topics/'.$topicimage)) $imgtmp=$tipath.$topicimage;
   $timage=$imgtmp;

   $npds_METALANG_words=array(
   "'!N_publicateur!'i"=>$aid,
   "'!N_emetteur!'i"=>userpopover($informant).'<a href="user.php?op=userinfo&amp;uname='.$informant.'"><span class="">'.$informant.'</span></a>',
   "'!N_date!'i"=>formatTimestamp($time),
   "'!N_date_y!'i"=>substr($time,0,4),
   "'!N_date_m!'i"=>strftime("%B", mktime(0,0,0, substr($time,5,2),1,2000)),
   "'!N_date_d!'i"=>substr($time,8,2),
   "'!N_date_h!'i"=>substr($time,11),
   "'!N_print!'i"=>$printP,
   "'!N_friend!'i"=>$sendF,
   "'!N_boxrel_title!'i"=>$boxtitle,
   "'!N_boxrel_stuff!'i"=>$boxstuff,
   "'!N_titre!'i"=>$title,
   "'!N_id!'i"=>$id,
   "'!N_previous_article!'i"=>$prevArt,
   "'!N_next_article!'i"=>$nextArt,
   "'!N_sujet!'i"=>'<a href="search.php?query=&amp;topic='.$topic.'"><img class="img-fluid" src="'.$timage.'" alt="'.translate("Search in").'&nbsp;'.$topictext.'" /></a>',
   "'!N_texte!'i"=>$thetext,
   "'!N_nb_lecture!'i"=>$counter
   );
   echo meta_lang(aff_langue(preg_replace(array_keys($npds_METALANG_words),array_values($npds_METALANG_words), $Xcontent)));
}
function themesidebox($title, $content) {
   global $theme, $B_class_title, $B_class_content, $bloc_side, $htvar;
   $inclusion=false;
   if (file_exists("themes/".$theme."/html/bloc-right.html") and ($bloc_side=="RIGHT")) {$inclusion='themes/'.$theme.'/html/bloc-right.html';}
   if (file_exists("themes/".$theme."/html/bloc-left.html") and ($bloc_side=="LEFT")) {$inclusion='themes/'.$theme.'/html/bloc-left.html';}
   if (!$inclusion) {
      if (file_exists("themes/".$theme."/html/bloc.html")) {$inclusion='themes/'.$theme.'/html/bloc.html';}
      elseif (file_exists("themes/default/html/footer.html")) {$inclusion='themes/default/html/bloc.html';}
      else {
         echo 'bloc.html manquant / not find !<br />';
         die();
      }
   }
   ob_start();
   include($inclusion);
   $Xcontent=ob_get_contents();
   ob_end_clean();
   if ($title=='no-title') {
      $Xcontent=str_replace('<div class="LB_title">!B_title!</div>','',$Xcontent);
      $title='';
   }
   $npds_METALANG_words=array(
   "'!B_title!'i"=>$title,
   "'!B_class_title!'i"=>$B_class_title,
   "'!B_class_content!'i"=>$B_class_content,
   "'!B_content!'i"=>$content
   );
   echo $htvar;// modif ji fantôme block
   echo meta_lang(preg_replace(array_keys($npds_METALANG_words),array_values($npds_METALANG_words), $Xcontent));
   echo '
            </div>';// modif ji fantôme block
}
function themedito($content) {
   global $theme;
   $inclusion=false;
   if (file_exists("themes/".$theme."/html/editorial.html")) {$inclusion="themes/".$theme."/html/editorial.html";}
   if ($inclusion) {
      ob_start();
      include($inclusion);
      $Xcontent=ob_get_contents();
      ob_end_clean();
      $npds_METALANG_words=array(
      "'!editorial_content!'i"=>$content
      );
      echo meta_lang(aff_langue(preg_replace(array_keys($npds_METALANG_words),array_values($npds_METALANG_words), $Xcontent)));
   }
   return ($inclusion);
}
function userpopover($who) {
   global $short_user, $user, $NPDS_Prefix;
   $result=sql_query("SELECT uname FROM ".$NPDS_Prefix."users WHERE uname ='$who'");
   include_once('functions.php');
   if (sql_num_rows($result)) {
   $a = 0;
   $temp_user = get_userdata($who);
   $my_rsos=array();
   $socialnetworks=array(); $posterdata_extend=array();$res_id=array();$my_rs='';
      if (!$short_user) {
         if($temp_user['uid']!= 1) {
            $posterdata_extend = get_userdata_extend_from_id($temp_user['uid']);
            include('modules/reseaux-sociaux/reseaux-sociaux.conf.php');
            include('modules/geoloc/geoloc_conf.php');

            if ($posterdata_extend['M2']!='') {
               $socialnetworks= explode(';',$posterdata_extend['M2']);
               foreach ($socialnetworks as $socialnetwork) {
                  $res_id[] = explode('|',$socialnetwork);
               }
               sort($res_id);
               sort($rs);
               foreach ($rs as $v1) {
                  foreach($res_id as $y1) {
                     $k = array_search( $y1[0],$v1);
                     if (false !== $k) {
                        $my_rs.='<a class="mr-3" href="';
                        if($v1[2]=='skype') $my_rs.= $v1[1].$y1[1].'?chat'; else $my_rs.= $v1[1].$y1[1];
                        $my_rs.= '" target="_blank"><i class="fa fa-'.$v1[2].' fa-2x text-primary"></i></a> ';
                        break;
                     } 
                     else $my_rs.='';
                  }
               }
               $my_rsos[]=$my_rs;
            }
            else $my_rsos[]='';
         }
      }
   settype($ch_lat,'string');
   $useroutils = '';
   if ($temp_user['uid']!= 1 and $temp_user['uid']!='')
      $useroutils .= '<a class="list-group-item text-primary" href="user.php?op=userinfo&amp;uname='.$temp_user['uname'].'" target="_blank" title="'.translate("Profile").'" ><i class="fa fa-2x fa-user"></i><span class="ml-3 d-none d-sm-inline">'.translate("Profile").'</span></a>';
   if ($temp_user['uid']!= 1 and $temp_user['uid']!='')
      $useroutils .= '<a class="list-group-item text-primary" href="powerpack.php?op=instant_message&amp;to_userid='.urlencode($temp_user['uname']).'" title="'.translate("Send internal Message").'" ><i class="fa fa-2x fa-envelope-o"></i><span class="ml-3 d-none d-sm-inline">'.translate("Message").'</span></a>';
   if ($temp_user['femail']!='')
      $useroutils .= '<a class="list-group-item text-primary" href="mailto:'.anti_spam($temp_user['femail'],1).'" target="_blank" title="'.translate("Email").'" ><i class="fa fa-at fa-2x"></i><span class="ml-3 d-none d-sm-inline">'.translate("Email").'</span></a>';
   if ($temp_user['url']!='')
      $useroutils .= '<a class="list-group-item text-primary" href="'.$temp_user['url'].'" target="_blank" title="'.translate("Visit this Website").'"><i class="fa fa-2x fa-external-link"></i><span class="ml-3 d-none d-sm-inline">'.translate("Visit this Website").'</span></a>';
   if ($temp_user['mns'])
       $useroutils .= '<a class="list-group-item text-primary" href="minisite.php?op='.$temp_user['uname'].'" target="_blank" target="_blank" title="'.translate("Visit the Mini Web Site !").'" ><i class="fa fa-2x fa-desktop"></i><span class="ml-3 d-none d-sm-inline">'.translate("Visit the Mini Web Site !").'</span></a>';
   if ($user and $temp_user['uid']!= 1) {
      if ($posterdata_extend[$ch_lat] !='')
         $useroutils .= '<a class="list-group-item text-primary" href="modules.php?ModPath=geoloc&amp;ModStart=geoloc&op=u'.$temp_user['uid'].'" title="'.translate("Location").'" ><i class="fa fa-map-marker fa-2x">&nbsp;</i><span class="ml-3 d-none d-sm-inline">'.translate("Location").'</span></a>';
   }
   if (stristr($temp_user['user_avatar'],'users_private')) 
      $imgtmp=$temp_user['user_avatar'];
   else
      if ($ibid=theme_image('forum/avatar/'.$temp_user['user_avatar'])) {$imgtmp=$ibid;} else {$imgtmp='images/forum/avatar/'.$temp_user['user_avatar'];}
   $userpop ='<a tabindex="0" data-toggle="popover" data-trigger="focus" data-html="true" data-title="<h4>'.$temp_user['uname'].'</h4>" data-content=\'<div class="list-group">'.$useroutils.'</div><hr />'.$my_rsos[$a].'\'></i><img data-html="true" title="" data-toggle="tooltip" class="btn-secondary img-thumbnail img-fluid n-ava-small mr-2" src="'.$imgtmp.'" alt="'.$temp_user['uname'].'" /></a>';

   return $userpop;
   }
}
?>