moodle_referentiel
==================

Referentiel plugin (mod / block / report) for Moodle  2.9, 3.0, 3.1 (and surely not further!)

## Module Moodle - Référentiel / Skills repository - Documentation

jean.fruitet@univ-nantes.fr   (jusqu'en décembre 2015)
jean.fruitet@free.fr (après 2015)

2007/2016

Type: Activity Module

"master" branch requires Moodle 3.1

"moodle29" branch requires Moodle 2.9

Avalaible: Moodle 2.9, 3.0, 3.1

Status: Contributed

Maintainer(s): jean.fruitet@univ-nantes.fr (until January 2016) - jean.fruitet@free.fr

**NEW FEATURE with Moodle 3.1 VERSION**

You can now export a skills repository occurrence and scale to Moodle Competency framework CSV format file 
Go to *Referentiel / Export -> Export to Competency framework*

**NOUVEAUTE avec la version pour Moodle 3.1**

Vous pouvez désormais exporter une occurrence de référentiel et son barème vers fichier au format CSV de l'implantation des compétences dans le noyau Moodle.

Aller à *Referentiel / Exporter -> Exporter vers Competency framework*

Si vous avez de plus installé le plugin Import / Export de rférentiels de compétences cela vous évitera d'avoir à ressaisir tous vos réféentiels maison...

### PRESENTATION (Français)

"referentiel" est un module Moodle destiné à implanter une activité de type certification
de compétences.

Ce module permet :

* de spécifier un référentiel de compétences (ou de le télécharger) ;
* de déclarer des activités et d'associer celles-ci aux compétences du référentiel ;
* de gérer l'accompagnement ;
* de définir des tâches (mission, consignes, liste de compétences mobilisées pour accomplir la tâche, documents attachés) ;
* d'émettre des certificats basés sur le dit référentiel ;
* Si le site active les Objectifs, vous pouvez exporter le référentiel sous forme d'une
liste d'objectifs qui serviront alors à évaluer toute forme d'activité(forum, BD, devoir, etc.).
Ces notations sont récupérées dans le module référentiel sous forme de compétences validées dans des déclaration d'activité.
* A pertir de la verion Moodle 3.1 vous pouvez exporter le référentiel au format  "Competency Framework CSV file format" pour intégration dans les Plans de formation.

#### Pour utiliser pleinement cette version installez les composants suivants dans leurs dossiers respectifs

./mod/referentiel

./blocks/referentiel

./report/referentiel

Lisez la [documentation d'installation détaillée] (http://moodlemoot2012.unimes.fr/mod/resource/view.php?id=882 "Installation" après Connexion anonyme )

http://jfreferentiels.blogspot.fr/2015_10_01_archive.html

### PRESENTATION (English)

Skills repository ("referentiel") is a Moodle module for skill certification.

You can:

* specify a repository or import it
* declare activities linked with competencies
* follow students declarations
* propose tasks (a mission, list of competencies, linked documents...)
* export an print certificates
* If your site enables Outcomes (also known as Competencies, Goals, Standards or Criteria), you can now export a list of Outcomes from referentiel module then grade things using
that scale (forum, database, assigments, etc.) throughout the site. Then these evaluations will be integrated in the Referentiel instance of the course.
* You ca export the Skills repository to Competency Framework CSV file format

### Sources

[Moodle 3.1 and further] (https://github.com/jfruitet/moodle_referentiel, master branch)
[Moodle 2.9, 3.0 ] (https://github.com/jfruitet/moodle_referentiel, moodle29 branch)

### Installation

Install all referentiel components in is directory

./mod/referentiel

./blocks/referentiel

./report/referentiel

Read the [install documentation] (http://moodlemoot2012.unimes.fr/mod/resource/view.php?id=882 "Installation, in French, with Anonymous connexion")

### Documentation

MoodleMoot francophones
[MoodleMoot 2012] (http://moodlemoot2012.unimes.fr/course/view.php?id=33) Connexion anonyme

Le dossier
./mod/referentiel/documentation
sur votre serveur Moodle contient une documentation élémentaire

Look at
./mod/referentiel/documentation directory on your Moodle server

### Blog
[Les Référentiels de compétence] (http://jfreferentiels.blogspot.fr/)
