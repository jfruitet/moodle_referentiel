<?php
// ----------------
// UTF-8 French

// Moodle 2
$string['pluginname'] = 'Référentiel';
$string['rank'] = 'Rang';
$string['etudiants_inscrits_referentiel'] = 'Etudiants inscrits dans des processus de certification';
$string['actualisation'] = 'Actualiser les numéro d\'étudiant d\'après le Profil utilisateur';
$string['profilcheck'] = 'Les modifications de numéro d\'étudiant intervenues dans les profils utilisateurs
seront prises en compte...';
$string['migration'] = 'Les tables du module Référentiel contiennent {$a} liens au format Moodle 1.9<br /> Confirmez la conversion au format Moodle 2.x';
$string['suppression'] = 'Confirmez la suppression des fichiers obsolètes et des liens rompus ';

$string['verbose'] = 'Mode bavard (les liens taités sont affichés) ';
$string['conversionencours'] = 'Migration des liens obsolètes en cours. Le processus peut prendre très longtemps, patientez...<br />En cas d\'interruption inoppinée relancez le processus.';
$string['conversionachevee'] = 'Migration de liens obsolètes achevée.';

$string['migrationh'] = 'Migration de liens obsolètes';
$string['migrationh_help'] = 'Lors de la migration de Moodle 1.9 vers Moodle 2.x certains liens des tables referentiel_document et referentiel_consigne ne sont pas correctement convertis.

Cette fonction force la conversion et déplace les fichiers de l\'ancien système de fichiers Moodle 1.9
vers le nouveau.

Vous pouvez aussi choisir de supprimer les fichiers obsolètes (pour épargner de l\'espace disque).

N.B. : Si le nombre de liens à convertir est de plusieurs milliers, le processus de conversion peut demander près d\'une heure.
Il convient donc d\'adapter les variables d\'environnement du fichier php.ini du serveur afin d\'augmenter la mémoire disponible :

* max_execution_time = 5000

* memory_limit = 1024M

En cas d\'erreur fatale \'(Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 284205254 bytes)\'
relancer le processus pour achever la conversion.';

?>
