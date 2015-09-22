<?php
/************************************************************************/
/* This version name NPDS Copyright (c) 2001-2011 by Philippe Brunier   */
/* ===========================                                          */
/*                                                                      */
/* UPLOAD Language File                                                 */
/*                                                                      */
/************************************************************************/

function upload_translate($phrase) {
 switch($phrase) {
   /////// fichier
   case "Pi�ces jointes :": $tmp="Attachments:"; break;
   case "Fichier :": $tmp="File:"; break;
   case "Type :": $tmp="Type:"; break;
   case "Taille :": $tmp="Size:"; break;
   case "Affichage int�gr� :": $tmp="Inline:"; break;
   case "Oui": $tmp="Yes"; break;
   case "Non": $tmp="No"; break;
   case "Supprimer les fichier s�lectionn�s": $tmp="Delete selected files"; break;
   case "Fichier joint :": $tmp="Select a file to attach:"; break;
   case "Joindre": $tmp="Send file"; break;
   case "Adapter": $tmp="Update"; break;
   case "Visible :": $tmp="Visibility:"; break;
   case "Total :": $tmp="Total:"; break;
   case "Fichier non trouv�": $tmp="File not found"; break;
   case "Fichier non visible": $tmp="File not visible"; break;
   case "T�l�charg.": $tmp="Download(s)"; break;
   case "Pr�visualisation :": $tmp="Preview:"; break;
   case "Ces Images sont disponibles sur votre site": $tmp="These Images are available on your Website."; break;
   case "Ces Documents sont disponibles sur votre site": $tmp="These Documents are available on your Website."; break;
   case "Ces Images et ces Documents sont rattach�s � votre compte.": $tmp="Your Images and Documents."; break;
   case "T�l�charger un fichier sur le serveur"; $tmp="File upload"; break;
   /////// javascript
   case "Supprimer les fichiers s�lectionn�s ?": $tmp="Delete selected files ?"; break;
   case "Cette page a d�j� �t� envoy�e, veuillez patienter": $tmp="This page has been submitted, please be patient"; break;
   case "Vous devez tout d'abord choisir la Pi�ce jointe � supprimer": $tmp="You must choose the attachment you want to delete"; break;
   case "Vous devez selectionner un fichier": $tmp="You must select a file"; break;
   case "Joindre le fichier maintenant ?": $tmp="Send file now ?"; break;
   case "Raffraichir la page": $tmp="Reload the page"; break;
   case "Mod�les": $tmp="Templates"; break;
   case "Installer": $tmp="Install"; break;
   case "Etes vous certains de vouloir installer le th�me": $tmp="Are you sure you want to install the template"; break;
   /////// class upload
   case "La taille de ce fichier exc�de la taille maximum autoris�e": $tmp="The size of this file exceeds the maximum file size"; break;
   case "Ce type de fichier n'est pas autoris�": $tmp="This type of file is not authorized"; break;
   case "Le code erreur est : %s": $tmp="Error code was: %s"; break;
   case "Attention": $tmp="Warning"; break;
   case "Session termin�e.": $tmp="Session halted."; break;
   case "Erreur de t�l�chargement du fichier <b>%s</b> (%s) - Le fichier n'a pas �t� sauv�": $tmp="Error while uploading file <b>%s</b> (%s) - File not saved"; break;
   case "<center><b>Fichier {NAME} bien recu ({SIZE} octets transf�r�s)</b></center>": $tmp="<center><b>file {NAME} uploaded ({SIZE} bytes transferred)</b></center>"; break;
   case "Erreur de t�l�chargement du fichier - fichier non sauvegard�.": $tmp="Error while uploading file - File not saved"; break;

   // NPDS Sable
   case "Images & Documents": $tmp="Images & Documents"; break;
   case "Modules Additionnels": $tmp="PlugIns"; break;
   // NPDS Sable

   default: $tmp = "n�cessite une traduction <b>[** $phrase **]</b>"; break;
 }
 if (cur_charset=="utf-8") {
    return utf8_encode($tmp);
 } else {
    return ($tmp);
 }
}
?>