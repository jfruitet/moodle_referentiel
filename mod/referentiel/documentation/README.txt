Moodle module referentiel 
Module Moodle - Référentiel / Skills repository - Documentation
jean.fruitet@univ-nantes.fr  (jusqu'en 2016)
jean.fruitet@free.fr (après 2016)

2007/2015

Type: Activity Module
Requires: Moodle 2.9
Avalaible: Moodle 2.9, 3.0
Status: Contributed
Maintainer(s): jean.fruitet@univ-nantes.fr

PRESENTATION (Français)
-----------------------
"referentiel" est un module Moodle destiné à implanter une activité de type certification
de compétences.
Ce module permet :
- de spécifier un référentiel de compétences (ou de le télécharger) ;
- de déclarer des activités et d'associer celles-ci aux compétences du référentiel ;
- de gérer l'accompagnement ;
- de définir des tâches (mission, consignes, liste de compétences mobilisées pour accomplir la tâche, documents attachés) ;
- d'émettre des certificats basés sur le dit référentiel ;

- Si le site active les Objectifs, vous pouvez exporter le référentiel sous forme d'une
liste d'objectifs qui serviront alors à évaluer toute forme d'activité
(forum, BD, devoir, etc.)
Ces notations sont récupérées dans le module référentiel sous forme de compétences
validées dans des déclaration d'activité.

PRESENTATION (English)
----------------------
Skills repository ("referentiel") is a Moodle module for skill certification.
You can:
- specify a repository or import it
- declare activities linked with competencies
- follow students declarations
- propose tasks (a mission, list of competencies, linked documents...)
- export an print certificates

- If your site enables Outcomes (also known as Competencies, Goals, Standards or Criteria),
you can now export a list of Outcomes from referentiel module then grade things using
that scale (forum, database, assigments, etc.) throughout the site.
These grades will be integrated in Referentiel module.


INSTALLATION (Français)
-----------------------
ce plugin ets constitué de tros modules :

Le module activité Référentie qui doit être intégré dans le répertoire ./mod/ d'un serveur Moodle
Le bloc Référentiel qui doit être intégré dans le répertoire ./blocks/ du serveur
Le rapport Référentie qui doit être intégré dans le répertoire ./report/ du serveur

La procédure suivante s'applique à toute installation Moodle
VOTRE_DOSSIER_MOODLE = le nom du dossier où est placé votre moodle, en général "moodle"
URL_SERVEUR_MOODLE = le nom de votre serveur moodle, en général "http://machine.domaine.fr/moodle/"

1.
A) Décomprimer l'archive "referentiel-mod.zip" dans le dossier "VOTRE_DOSSIER_MOODLE/mod/"
Les fichiers de langue peuvent être laissés dans le dossier
"VOTRE_DOSSIER_MOODLE/mod/referentiel/lang/"
B) Décomprimer l'archive "referentiel-block.zip" dans le dossier "VOTRE_DOSSIER_MOODLE/blocks/"
Les fichiers de langue peuvent être laissés dans le dossier "VOTRE_DOSSIER_MOODLE/blocks/referentiel/lang/"
C) Décomprimer l'archive "referentiel-report.zip" dans le dossier "VOTRE_DOSSIER_MOODLE/report/"
Les fichiers de langue peuvent être laissés dans le dossier "VOTRE_DOSSIER_MOODLE/report/referentiel/lang/"

2. se loger avec le role admin sur "URL_SERVEUR_MOODLE"

3. Installer les différents éléments (ou les mises à jour) en passant par la rubrique
Administration / Notification
S'il y a des messages d'erreur m'avertir aussitôt par mail en m'envoyant une copie d'écran du message d'erreur.

4. paramétrer le module au niveau du site en passant par la rubrique
Administration du site / Plugins / Activités / Référentiel
Administration du site / Plugins / Bloc / Référentiel
Administration du site / Plugins / Rapport / Référentiel


ERREUR FREQUENTE LORS DES MISE A JOUR
------------------------------------------------
L'erreur à éviter est de créer des copies de sauvegarde (sous un autre nom) dans les dossiers
VOTRE_DOSSIER_MOODLE/mod/, VOTRE_DOSSIER_MOODLE/blocks/ ou VOTRE_DOSSIER_MOODLE/report/
car les sauvegardes entrent en conflit avec la version utilisée.
Si vous voulez garder des sauvegardes dans ces dossiers comprimez les en archives.

-------------------------------------------------
Marche à suivre pour désinstaller ce module
-------------------------------------------------
1. Logez-vous avec le role admin

2.
A) Supprimez le module
Administration du site / Plugins / Activités / Gestion des activités
Sélectionnner  Référentiel :: Supprimer
B) Supprimez le bloc
Administration du site / Plugins / Activités / Gestion des blocs
Sélectionnner  Référentiel :: Supprimer
C) Supprimez le rapport
Administration du site / Plugins / Rapports / Gestion des rapports
Sélectionnner  Référentiel :: Supprimer

3. Supprimez le dossier VOTRE_DOSSIER_MOODLE/mod/referentiel, VOTRE_DOSSIER_MOODLE/blocks/referentiel,
VOTRE_DOSSIER_MOODLE/report/referentiel

4. Supprimer les dossiers
./moodledata/#course_id/moddata/referentiel
de tous les cours où une instance a été déclarée

---------------------------------------------------
Fonctions Report
---------------------------------------------------
La nouvelle fonctionnalité "Rapport Référentiel" (report) est proposée aux administrateurs

Moodle 2.7
--------------------------------
L'archive referentiel-report.zip
doit être décomprimée dans le dossier
SERVEUR_MOODLE/report/

L'administrateur a alors la possibilité de gérer les occurrences 
et les instances du module référentiel de son serveur
depuis la rubrique "Rapports" sans passer par une instance de cours.

Il peut aussi créer des archives des dossiers numériques des utilisateurs ayant déposé 
des déclarations d'activités. 


INSTALLATION (English)
----------------------

The following steps should get you up and running with this module code.
---------------------------------------------------------
1. Unzip the archive referentiel-mod in moodle/mod/ directory
Unzip the archive referentiel-block in moodle/blocks/ directory
Unzip the archive referentiel-report in moodle/report/ directory

Languages files can be left in the ./referentiel/lang/ directories.
2. log on with admin role

3. install new module as usual (admin Notification)

4. Set module parameters
Administration / Plugins / Activity / Skills repository
Administration / Plugins / Blocks / Skills repository
Administration / Plugins / Report / Skills repository

---------------------------------------------------
Referentiel Report functions
---------------------------------------------------
Functionnality "Skills repository report" (Referentiel report) for administrators
gives to administrators the opportunity to manage occurrences and instances of the referentiel module
and make archives of users numerical data

Moodle 2.7
------------------------------
Unzip referentiel-report.zip
in 
YOUR_MOODLE/report/
 

---------------------------------------------------------
Documentation et mises à jours sous forme d'archive ZIP 
---------------------------------------------------------
sur les sites du MoodleMoot encore en activité

    * MoodleMoot2010 : http://moodlemoot2010.utt.fr/course/view.php?id=33
    * MoodleMoot2012 : http://moodlemoot2012.unimes.fr/course/view.php?id=33

Pour être tenu informé des mise à jour inscrivez-vous dans les forums de ces cours.

---------------------------------------------------
CVS / Subversion
---------------------------------------------------
Le serveur public des sources CVS / Subversion est  :
Subversion server at: 
https://subversion.cru.fr/referentiel/
https://sourcesup.renater.fr/scm/viewvc.php/?root=referentiel

---------------------------------------------------
GitHub
---------------------------------------------------
https://github.com/jfruitet/moodle_referentiel

----------------------------------------------------
Forums et Tests du module
----------------------------------------------------
A french thread is at 
http://moodle.org/mod/forum/discuss.php?d=127647

Un fil de discussion sur Moodle en Français est consacré au module : 
http://moodle.org/mod/forum/discuss.php?d=127647


---------------------------------------------------------
Liste des documents disponibles sur ces différents sites
Useful documentation
---------------------------------------------------------

    * Documentation utilisateurs / Users doc
    * Documentation développeurs / Developers doc
    * Communications au MoodleMoot2008 et MoodleMoot2009 / French MoodleMoots 
    * Captures d'écran et présentations animées / Print screens
    * Vidéos 

--------------------------------------------------------
Liste de référentiels disponibles pour importation
--------------------------------------------------------
Some ready made repositories are here. (In french)
Pour récupérer des occurrnces de référentiels prêtes à l'importation
http://moodlemoot2012.unimes.fr/mod/folder/view.php?id=881

Référentiel		Format d'import CSV			Format d'import XML
B2i Ecole		referentiel-b2i_ecole.csv	referentiel-b2i_ecole.xml
B2i Collège		referentiel-b2i_college.csv	referentiel-b2i_college.xml
B2i Lycée		referentiel-b2i_lycee.csv	referentiel-b2i_lycee.xml

C2IN1
Version 2008	referentiel-c2n1.csv		referentiel-c2in1.xml
Version 2012    referentiel-c2in1-2012_generique.csv  referentiel-c2in1-2012_generique.xml
ATTENTION :
Les versions des C2iN1 2008 et 2012 ne devraient pas être installées sur le même serveur
pour éviter des confusions lors de la certification.

C2i2
C2i2 Enseignant	version 2005     referentiel-c2i2e.csv		referentiel-c2i2e.xml
C2i2 Enseignant	version 2011/2012    referentiel-c2i2e.xml
ATTENTION :
Les versions des C2i2e 2005 et 2011/2012 ne devraient pas être installées sur le même serveur
pour éviter des confusions lors de la certification.

C2i2 Metiers du droit, de l'ingénieur, de la Santé, du développement durable


--------------------------------------------------------
Sauvegarde des données sur le serveur Moodle
et importation /  exportation et restauration du module
-------------------------------------------------------
Exports and user data are in ./moodledata/#course_id/moddata/referentiel

Ce module enregistre les exportations dans le dossier ./moodledata/#course_id/moddata/referentiel
Attention : En cas de suppression du module Référentiel ces dossier doivent être purgés à la main 
par l'administrateur système !



Outcomes used in moodle activities are integrated in Pository activity.
=========================================================================
If your site enables Outcomes (also known as Competencies, Goals, Standards or Criteria),
you can now export a list of Outcomes from referentiel module then grade things using that scale (forum, database, assigments, etc.)
throughout the site. These grades will be automatically integrated in Referentiel module.


Evaluer des activités Moodle (forum, devoirs, etc.) au regard d'un barème de référentiel.
==========================================================================================
Si les objectifs sont activés sur votre serveur Moodle (voir avec l'administrateur comment les activer)
vous pouvez sauvegarder le référentiel sous forme d'un barême d'objectifs
puis utiliser ce barême pour évaluer toute forme d'activité Moodle (forums, devoirs, bases de données, wiki, etc.)
Le module Référentiel récupèrera ces évaluations et génèrera des déclarations qui seront dès lors accessibles
dans la liste des activités du module référentiel.

Protocole

   1. Avec le rôle d'administrateur activer les Objectifs au niveau du serveur
   2. Depuis le module Référentiel Exporter les objectifs (Onglet "Référentiel / Exporter")
      Enregistrez le fichier "outcomes_referentiel_xxx.csv" sur votre disque dur.
   3. Au niveau du cours passer par Administration / Notes et sélectionner Modifier Objectifs
   4. Choisir alors Importer comme objectifs de ce cours ou Importer comme objectifs standards
puis dans la rubrique Importer des objectifs (Taille maximale : xxMo) sélectionnez le fichier
"outcomes_referentiel_xxx.csv" ci-dessus enregistré.

Désormais vous pouvez utiliser ce barême pour évaluer toute activité du cours.
Les étudiants notés selon ce barême verront leurs productions intégrées directement
dans le module référentiel sous forme de déclarations d'activité accessibles et modifiables selon les modalités usuelles.


ATTENTION : Moodle 1.9.5 to 2.2 does not permit outcomes to be imported by teachers.
http://tracker.moodle.org/browse/MDL-18506
Certaines versions de Moodle ne supportent pas correctement l'importation
des fichiers d'Objectifs.
This is corrected with this patch :
Il faut installer un patch :
http://moodle.org/file.php/5/moddata/forum/397/634415/grade_edit_outcome.zip
Commentaire à cette adresse :
Commentary about this bug :
http://moodle.org/mod/forum/discuss.php?d=145112

Lisez ./documentation/version_history.txt pour les mises à jour
Read  ./documentation/version_history.txt for updates