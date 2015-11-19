Block referentiel is part of referentiel module

Module Moodle - Référentiel / Skills repository
jean.fruitet@univ-nantes.fr
2007/2013

Type: Block Module
Requires: Moodle 2.4 or 2.5
Status: Contributed
Maintainer(s): jean.fruitet@univ-nantes.fr


PRESENTATION (Français)
-----------------------
Le bloc
./blocks/referentiel/
est le complément du module Référentiel
./mod/referentiel/

Il permet de gérer les occurrences de référentiel existantes et de créer / importer
de nouvelles occurrences.


PRESENTATION (English)
----------------------
This Block is part of Referentiel Module
It allows management / creation / importation of Referential occurrences


INSTALLATION (Français)
-----------------------
Ce block doit être intégré dans le répertoire ./blocks/ d'un serveur Moodle

La procédure suivante s'applique à toute installation Moodle
VOTRE_DOSSIER_MOODLE = le nom du dossier où est placé votre moodle, en général "moodle"
URL_SERVEUR_MOODLE = le nom de votre serveur moodle, en général "http://machine.domaine.fr/moodle/"

A. Installer le module Référentiel
Le module referentiel doit être installé AVANT le block referentiel
1. Décomprimer l'archive "mod_referentiel_xxx.zip" dans le dossier
VOTRE_DOSSIER_MOODLE/mod/

B. Installer le bloc Référentiel
1. Décomprimer l'archive "block_referentiel_xxx.zip" dans le dossier
VOTRE_DOSSIER_MOODLE/blocks/

2. Se loger avec le role admin sur "URL_SERVEUR_MOODLE"
3. installer le nouveau module (admin Notification)
4. Paramétrer le module :
Administration / Plugins / Activity / Repository

ERREUR FREQUENTE LORS DES MISE A JOUR
------------------------------------------------
L'erreur à éviter est de créer une copie de sauvegarde (sous un autre nom) dans le dossier
VOTRE_DOSSIER_MOODLE/blocks/
VOTRE_DOSSIER_MOODLE/mod/

INSTALLATION (English)
----------------------

The following steps should get you up and running with this module code.
---------------------------------------------------------
A. Install Referentiel module
1. Unzip the archive in moodle/mod/ directory
Languages files can be left in the moodle/mod/referentiel/lang/ directory.

B. Install Referentiel block
1. Unzip the archive "block_referentiel_xxx.zip" in moodle/blocks/ directory

2. log on with admin role
3. install new module as usual (admin Notification)
4. Set module parameters:
Administration / Plugins / Activity / Repository

---------------------------------------------------
Referentiel Report functions
---------------------------------------------------
Functionnality "Skills repository report" (Referentiel report) for administrators
gives to administrators the opportunity to manage occurrences and instances of the referentiel module
and make archives of users numerical data

Moodle 1.9, 2.0, 2.1
--------------------
Unzip
YOUR_MOODLE/mod/referentiel/report/referentiel-report.zip
in
YOUR_MOODLE/admin/report/
directory


Moodle 2.2, 2.3, 2.4, 2.5
-------------------------
Unzip
YOUR_MOODLE/mod/referentiel/report/referentiel-report.zip
in
YOUR_MOODLE/report/


---------------------------------------------------------
Documentation et mises à jours sous forme d'archive ZIP
---------------------------------------------------------
    * MoodleMoot2009 : http://moodlemoot2009.insa-lyon.fr/course/view.php?id=24
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
After you get runing the referentiel module, go to "./mod/referentiel/sauvegarde_referentiel" directory
to import some ready made repositories. (In french)
Après installation du module sur un serveur Moodle, le dossier "./mod/referentiel/sauvegarde_referentiel"
contient les exports/imports suivants :

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
http://moodlemoot2009.insa-lyon.fr/mod/resource/view.php?id=849&subdir=/Referentiels_C2i2_Metiers


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