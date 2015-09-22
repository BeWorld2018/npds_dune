<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Session and log Viewer Copyright (c) 2004 - Tribal-Dolphin           */
/*                                                                      */
/* NPDS Copyright (c) 2002-2011 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

function SessionLog_translate($phrase) {
 switch ($phrase) {
   case "Liste des Sessions" : $tmp = "Lista de Sessions"; break;
   case "Nom" : $tmp = "Nombre"; break;
   case "@ IP" : $tmp = "@IP"; break;
   case "@ IP r�solue" : $tmp = "Resuelto @IP"; break;
   case "Infos" : $tmp = "Infos"; break;
   case "Liste des Logs" : $tmp = "Lista de Logs"; break;
   case "SECURITE" : $tmp = "SEGURIDAD"; break;
   case "TELECHARGEMENT" : $tmp = "SUBIR"; break;
   case "Gestion des Logs" : $tmp = "Gesti�n de Logs"; break;
   case "Fournisseur" : $tmp = "FAI"; break;
   case "Informations sur l'IP" : $tmp = "Informaci�n IP"; break;
   case "Vider le fichier" : $tmp = "Fichero vac�o"; break;
   case "Recevoir le fichier par mail" : $tmp = "Recibir el fichero por email"; break;
   case "Effacer les fichiers temporaires" : $tmp = "Eliminar ficheros temporales"; break;
   case "Fichier de Log de" : $tmp = "Fichero Log de"; break;
   case "" : $tmp = ""; break;

   default: $tmp = "Necesita una traducci&oacute;n <b>[** $phrase **]</b>"; break;
 }
 return $tmp;
}
?>