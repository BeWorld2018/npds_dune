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
if (!function_exists("Mysql_Connexion")) {
   include ("mainfile.php");
}

settype($sid, "integer");
settype($archive, "integer");
if (!isset($sid) && !isset($tid)) {
   header ("Location: index.php");
}

   if (!$archive)
      $xtab=news_aff("libre","where sid='$sid'",1,1);
   else
      $xtab=news_aff("archive","where sid='$sid'",1,1);

   list($sid, $catid, $aid, $title, $time, $hometext, $bodytext, $comments, $counter, $topic, $informant, $notes) = $xtab[0];
   if (!$aid) {
      header ("Location: index.php");
   }
   sql_query("UPDATE ".$NPDS_Prefix."stories SET counter=counter+1 where sid='$sid'");

   include ("header.php");
   // Include cache manager
   global $SuperCache;
   if ($SuperCache) {
      $cache_obj = new cacheManager();
      $cache_obj->startCachingPage();
   } else {
      $cache_obj = new SuperCacheEmpty();
   }
   if (($cache_obj->genereting_output==1) or ($cache_obj->genereting_output==-1) or (!$SuperCache)) {
      $title = aff_langue(stripslashes($title));
      $hometext = aff_code(aff_langue(stripslashes($hometext)));
      $bodytext = aff_code(aff_langue(stripslashes($bodytext)));
      $notes = aff_code(aff_langue(stripslashes($notes)));

      if ($notes!= "") $notes="<div>".translate("Note")." : \"$notes\"</div>";
      if ($bodytext == "") {
         $bodytext = meta_lang("$hometext<br />$notes");
      } else {
         $bodytext = meta_lang("$hometext<br />$bodytext<br />$notes");
      }
      if ($informant == "") {$informant = $anonymous;}

      getTopics($sid);

      if ($catid != 0) {
         $resultx = sql_query("select title from ".$NPDS_Prefix."stories_cat where catid='$catid'");
         list($title1) = sql_fetch_row($resultx);
         $title = "<a href=\"index.php?op=newindex&amp;catid=$catid\">".aff_langue($title1)."</a> : $title";
      }

      $boxtitle=translate("Related Links");
      $boxstuff="<ul>";
      $result=sql_query("select name, url from ".$NPDS_Prefix."related where tid='$topic'");
      while(list($name, $url) = sql_fetch_row($result)) {
         $boxstuff .= "<li><a href=\"$url\" target=\"new\">".$name."</a></li>";
      }
      $boxstuff .= "</ul>
      <ul>
      <li><a href=\"search.php?topic=$topic\">".translate("More about")." ".aff_langue($topictext)."</a></li>
      <li><a href=\"search.php?member=$informant\">".translate("News by")." ".$informant."</a></li>
      </ul>
      <br /><br /><div>".translate("Most read story about")."&nbsp;&nbsp;".aff_langue($topictext)." :</div>";
      $xtab=news_aff("big_story","where topic=$topic",0,1);
      list($topstory, $ttitle) = $xtab[0];
      $boxstuff .= "<br />
      <ul>
      <li><a href=\"article.php?sid=$topstory\">".aff_langue($ttitle)."</a></li>
      </ul>
      <br /><br /><div>".translate("Last news about")." ".aff_langue($topictext)." :</div><br />";

      if (!$archive)
         $xtab=news_aff("libre","where topic=$topic and archive='0' order by sid DESC LIMIT 0,5",0,5);
      else
         $xtab=news_aff("archive","where topic=$topic and archive='1' order by sid DESC LIMIT 0,5",0,5);

      $story_limit=0;
      $boxstuff .="<ul>";
      while (($story_limit<5) and ($story_limit<sizeof($xtab))) {
         list($sid1,$catid1,$aid1,$title1) = $xtab[$story_limit];
         $story_limit++;
         $title1=aff_langue(addslashes($title1));
         $boxstuff.="<li><a href=\"article.php?sid=".$sid1."&amp;archive=$archive\">".aff_langue(stripslashes($title1))."</a></li>";
      }
      $boxstuff .="</ul>";

      $boxstuff .= "<br /><p align=\"center\">";
      $boxstuff .= "<a href=\"print.php?sid=$sid&amp;archive=$archive\" title=\"".translate("Printer Friendly Page")."\">";

      $boxstuff .= "<i class=\"fa fa-print\"></i></a>&nbsp;&nbsp;";
      if ($ibid=theme_image("box/friend.gif")) {$imgtmp=$ibid;} else {$imgtmp="images/friend.gif";}
      $boxstuff .= "<a href=\"friend.php?op=FriendSend&amp;sid=$sid&amp;archive=$archive\"><img src=\"$imgtmp\" border=\"0\" alt=\"".translate("Send this Story to a Friend")."\" align=\"center\" /></a>&nbsp;";
      $boxstuff .= "</p>";

      if (!$archive) {
         $previous_tab=news_aff("libre","WHERE sid<'$sid' ORDER by sid DESC ",0,1);
         $next_tab=news_aff("libre","WHERE sid>'$sid' ORDER by sid ASC ",0,1);
      } else {
         $previous_tab=news_aff("archive","WHERE sid<'$sid' ORDER by sid DESC",0,1);
         $next_tab=news_aff("archive","WHERE sid>'$sid' ORDER by sid ASC ",0,1);
      }

      if (array_key_exists(0,$previous_tab))
         list($previous_sid) = $previous_tab[0];
      else
         $previous_sid=0;

      if (array_key_exists(0,$next_tab))
         list($next_sid) = $next_tab[0];
      else
         $next_sid=0;
      themearticle($aid, $informant, $time, $title, $bodytext, $topic, $topicname, $topicimage, $topictext, $sid, $previous_sid, $next_sid, $archive);
      // theme sans le syst�me de commentaire en meta-mot !
      if (!function_exists("Caff_pub")) {
         if (file_exists("modules/comments/article.conf.php")) {
            include ("modules/comments/article.conf.php");
            include ("modules/comments/comments.php");
         }
      }
   }
   if ($SuperCache) {
      $cache_obj->endCachingPage();
   }
   include ("footer.php");
?>