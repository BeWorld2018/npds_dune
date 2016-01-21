<?php
/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2010 by Philippe Brunier   */
/*                                                                      */
/* New Links.php Module with SFROM extentions                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

// Titre de la Grille de Formulaire
$m->add_title("Fiche Compl�mentaire");

// Champ text : Longueur = 40 / Pas de v�rification
$m->add_field('NW', "Nom du Webmestre","",'text',false,50,"","");

// Champ text : Longueur = 50 / Email seulement
$m->add_field('email', "Adresse de messagerie","",'text',false,50,"","email");

// Champ text : Longueur = 200 / TextArea / Pas de V�rification
$m->add_field('AR', "Autres R�alisations","",'textarea_no_mceEditor',false,200,4,"","");

// Champ text : Longueur = 200 / TextArea / Pas de V�rification
$m->add_field('AF', "Autres Informations","",'textarea_no_mceEditor',false,200,4,"","");

// Commentaire
$m->add_comment("<p align=\"center\">Ces informations sont publiques, mais vous disposez d'un droit permanent de modification.</p>");

?>