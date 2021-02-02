<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for Amanote filter.
 *
 * @package     filter_amanote
 * @copyright   2020 Amaplex Software
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['filtername'] = 'Amanote';

// Settings.
$string['pluginadministration'] = 'Administration du module Amanote';


$string['autosaveperiod'] = 'Intervalle de sauvegarde automatique';
$string['autosaveperiod_help'] = 'Configurer l\'intervalle de temps en minutes entre les sauvegardes automatiques (min. 1, max.: 30). Un intervalle de 0 signifie pas de sauvegarde automatique.';

$string['saveinprivate'] = 'Sauver les notes dans les fichiers personnels';
$string['saveinprivate_help'] = 'Sauve le fichier annoté dans les fichiers personnels de l\'utilisateur. Cela permettra à l\'utilisateur de continuer sa prise de note la prochaine fois qu\'il ouvre le fichier annotable dans Amanote.';

$string['key'] = 'Clé d\'activation';
$string['key_help'] = 'Cette clé est requise pour les fonctionalités avancées telles que le créateur de podcasts.';


$string['importantinformationheading'] = 'Important installation information';
$string['importantinformationdescription'] = 'Afin que le module fonctionne correctement, veuillez vérifier que les exigences suivantes sont respectées:

1. Le plugin Amanote est activé (Administration du site > Plugins > Filtres > Gérer les filters)

2. Les services web sont activés (Administration du site > Fonctions avancées)

3. Le service *Moodle mobile web service* est activé (Administration du site > Plugins > Web services > Services externes)

4. Le protocole REST est activé (Administration du site > Plugins > Services web > Gérer les protocoles)

5. La capacité *webservice/rest:use* est autorisée pour les *utilisateurs authentifiés* (Administration du site > Utilisateurs > Permissions > Définition des rôles > Utilisateur authentifié > Gérer les rôles)';

// Core.
$string['openinamanote'] = 'Ouvrir dans Amanote';
$string['downloadnotes'] = 'Télécharger le fichier annoté';
$string['openanalytics'] = 'Ouvrir Learning Analytics';
$string['openpodcast'] = 'Ouvrir Podcast Creator';
$string['teacher'] = 'Professeur';

// Privacy.
$string['privacy:metadata'] = 'Pour s\'intégrer avec Amanote, certaines données utilisateur doivent être envoyées à l\'application Amanote (système distant).';
$string['privacy:metadata:userid'] = 'Le userid est envoyé depuis Moodle pour accélérer le processus d\'authentification.';
$string['privacy:metadata:fullname'] = 'Le nom complet de l\'utilisateur est envoyé au système distant pour permettre une meilleure expérience utilisateur.';
$string['privacy:metadata:email'] = 'L\'adresse e-mail de l\'utilisateur est envoyée au système distant pour permettre une meilleure expérience utilisateur (partage de notes, notification, etc.).';
$string['privacy:metadata:access_token'] = 'Le jeton d\'accès est nécessaire pour sauvegarder les notes dans l\'espace privé Moodle de l\'utilisateur.';
$string['privacy:metadata:access_token_expiration'] = 'La date d\'expiration du jeton d\'accès est envoyée pour empêcher un utilisateur d\'utiliser l\'application avec un jeton expiré.';
$string['privacy:metadata:subsystem:corefiles'] = 'Les fichiers (PDF, AMA) sont stockés avec le système de fichiers Moodle.';

