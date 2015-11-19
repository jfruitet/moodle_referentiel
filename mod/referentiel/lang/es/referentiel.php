<?php
// ----------------
// UTF-8 Español
// Moodle 2

$string['l_inconnu'] = 'DESCONOCIDO';


$string['syntheseh'] = 'Rédiger une synthèse';
$string['syntheseh_help'] = 'Pour clore son dossier de certification l\'étudiant peut rédiger une courte synthèse
qui pourra éventuellement venir en appui de son dossier lors de l\'examen de celui-ci par le jury de certification.
<br />
Il est recommandé dans ce cas d\'indiquer, en quelques lignes, les facteurs de contexte ayant favorisé / défavorisé
la mise en oeuvre des compétences.
<br />
Le candidat peut aussi à cette occasion préciser ce qui devrait être amélioré dans le processus de certification...';


// Version 2019/01/05
$string['alphabet'] = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';

// Version 2012/03/06


// Help strings /////////////////////////////////////////////////////////
// Aide
// Ayuda

$string['protocolereferentielh'] ='Protocolo de certificación';
$string['protocolereferentielh_help'] ='Para cada referencial ponga en una lista las limitaciones atadas a los items, las competencias y los dominios
<br /> <b>Umbrale de certificación</b> : Es el umbrale (numerico) para ser certificado bajo condición que otras reglas vienen para modificar este criterio.
<br /> <b>Minima de certificación</b> : Es el mínimo de ítems para ser certificado bajo condición que otras reglas vienen para modificar este criterio.
<br /> Items (competencias (dominios)) <i>obligatorios</i></b> : designe a los indispensables para la obtención del certificado.
<br /> <b>Los umbrales por competencia (y / o por dominio)</b> : Para cada competencia (dominio) indique el umbral más allá del cual la competencia (el dominio) es retenida para la obtención del certificado
<br /> El protocolo puede ser desactivado.';

$string['rnotificationh']= 'Lista referentes notificados';
$string['rnotificationh_help']= 'La notificación consiste en hacer enviar por Moodle un e-mail de información que repite lo esencial del contenido de la declaración de actividad.
<br />Esta pantalla pone en una lista para cada competencia de la base que será destinataria de los mensajes de notificación para el estudiante considerado';

$string['repartitionh']= 'Reparto del control de las declaraciones';
$string['repartitionh_help']= 'Los profesores de un curso pueden repartirse el seguimiento(control) de las declaraciones con arreglo a las competencias de la base.
Según las competencias declaradas en las actividades, la notificación preguntada por el estudiante será enviada(dirigida) a los profesores que son designados en la mesa de reparto por competencias.
<br /> Si entre los referentes fingidos al estudiante en la mesa de acompañamiento hay que también son designados en mesa de seguimiento(control) para las competencias apuntadas en la actividad sólo ellos recibirán la notificación.
<br /> Si no todos los referentes fingidos al estudiante son notificados, y a defecto todos los profesores del curso';


$string['format_archiveh'] = 'Archivo de las actividades y certificados';
$string['format_archiveh_help'] = 'El archivo contiene el conjunto de las declaraciones de actividad y los ficheros atados bajo forma depage Web o de fichero XML al importar en un porfolio de tipo Mahara.
Para exportar los datos que hay que tratar conviene más bien pasar por la pestaña "Exporter" o "Imprimir".';
$string['format_certificath'] = 'Archivo de competencias certificadas';
$string['format_certificath_help'] = 'Las competencias pueden ser exportadas :
<br />A <b> formato normal </b>: lista ítems validados.
<br />A <b> formato porcentaje </b>: los porcentajes de ítems validados consolidados por competencia y dominio.';

$string['aide_incompatible_competencesh'] = 'Competencias incompatibles';
$string['aide_incompatible_competencesh_help'] = 'La lista de la competencia de la base importada es incompatible con la versión instalada del referencial...';
$string['aide_incompatible_codeh'] = 'Código incompatible';
$string['aide_incompatible_codeh_help'] = 'Le code du référentiel importé est incompatible avec la version du référentiel installée...';
$string['aide_incompatible_cleh'] = 'Llave incompatible';
$string['aide_incompatible_cleh_help'] = 'La llave del referencial importado es incompatible con la versión instalada...';

$string['archiveh'] = 'Archivar un referencial';
$string['archiveh_help'] = 'Archivando una base usted produce una salvaguardia del conjunto
Declaraciones de actividad de todos los usuarios seleccionados (con ficheros atados a las declaraciones).
<br >Esta salvaguardia no puede ser restaurada en un curso Moodle pero según el formato escogido puede ser fijada como un sitio Webou integrado en un porfolio.';


$string['importetudianth'] = 'Importer des données de scolarité';
$string['importetudianth_help'] = 'Les données de scolarité sont indispensables pour l\'Impression des certificats.
<br />
Afin  d\'Importer des données de scolarité dans le module Référentiel de Moodle
il faut commencer par procéder à un export au format CSV qui sera ensuite complété / modifié sous éditeur
(avec un tableur).
<br />
Les données de scolarité que l\'on importe dans Moodle (par l\'onglet Importer) doivent contenir l\'Identifiant attribué par Moodle à chaque utilisateur lors de la création des comptes dans Moodle.
<br />1) Obtenir une sauvegarde contenant les identifiants Moodle des établissements et des étudiants.
<br />Commencez par mettre à jour la liste des établissements du cours (onglet <strong>Etablissements</strong>)
Puis affichez la liste de tous les <strong>étudiants</strong> du cours (onglet <strong>Etudiants</strong>).
<br /><b>N.B.</b> : Inutile de compléter les champs manquants, vous le ferez hors de Moodle sous éditeur.
<br />Exportez ensuite (onglet <strong>Exporter</strong>) les données de scolarité de votre cours au format CSV.
<br />2) Modifier cette sauvegarde avec un éditeur (un tableur). Tous les champs marqués "INCONNU"
peuvent être modifiés sous éditeur
<br /><b>Attention</b> : Champs à ne modifier sous aucun prétexte :
<pre> #id_etablissement  #id_etudiant  user_id    login </pre>
N.B : Il suffit qu\'un des trois champs "#id_etudiant", "user_id" ou "login" soit correctement renseigné pour chaque étudiant.
<br />3) Importer ce fichier de scolarité désormais à jour (onglet <strong>Importer</strong>, format CSV avec séparateur \';\' )
<br />4) Exporter les certificats (onglet <strong>Certificat</strong>), soit au format PDF pour impression, soit au format CSV pour injection dans un logiciel de scolarité type Apogée.
';

$string['importtaskh'] = 'Importer des tâches';
$string['importtaskh_help'] = 'Il est possible  d\'Importer un fichier de tâches, soit dans le même cours que le fichier d\'origine (chaque tâche importée prend un nouveau numéro), soit dans un cours différent.
<br />Ne peuvent être importées que des données enregistrées au format XML.
< br />Plusieurs contraintes doivent être respectées :
<br />Le référentiel associé à l\'Instance courante doit être identique au référentiel de la sauvegarde.
Autrement dit, si les clé sont non vides, elles doivent être identiques ; si elles sont vides les noms et codes doivent être identiques. Faute de quoi l\'Importation est refusée.
<br />Les tâches importées sont associées à celui qui effectue l\'Importation
<br />Les dates de début et de fin ne sont pas mises à jour
<br />Les liens URL associés aux consignes doivent être enregistrés avec une adresse absolue, sinon ils ne sont pas correctement récupérés.
<br />Les fichiers associés aux consignes ne sont pas chargés, car ils n\'appartiennent pas nécessairement au même cours.
Il est donc nécessaire de les redéposer après importation des tâches.
<br />Aucune vérification quant au doublonnage de tâches n\'est effectuée, les tâches importées prenant un nouveau numéro dans la liste des tâches.
<br />Les tâches importées sont masquées.';


$string['importreferentielh'] = 'Importer un référentiel';
$string['importreferentielh_help'] = 'Si vous disposez d\'une sauvegarde d\'une occurrence de référentiel
au format XML oou CSV (produite par la fonction symétrique Exporter)
vous pouvez charger celle-ci dans le cours Moodle.
<br />
Deux situations peuvent alors se produire :
<br />Soit c\'est un nouveau référentiel pour cet espace Moodle ; il devient disponible pour être associé à de nouvelles instances.
<br />Soit il existe déjà une version identique (même nom, même code) sur le serveur :
<br />Si vous avez choisi d\'écraser la version existante, elle est remplacée par la version importée ;
<br />Si vous avez choisi de conserver la version existante l\'Importation est interrompue ;';

$string['importpedagoh'] = 'Importer un fichier des formations';
$string['importpedagoh_help'] = 'l\'Injection des Formations / Pédagogies / Composantes se fait par importation d\'un fichier CSV ou XML.
<br />Format CSV, Séparateur \';\'
<br />Entête :
<br />
#Moodle Referentiel pedagos CSV Export;;latin1;Y:2011m:03d:17;;;;;;
<br />
#username;firstname;lastname;date_cloture;promotion;formation;pedagogie;composante;num_groupe;commentaire;referentiel;
<br />
Données :
<br />
E001326S;Severine;DUPON;2011-06-01;2011;6252;FCI2EMD;919;;;C2i2e-2011
<br />
dupuis-d;David;DUPUIS;2011-06-01;2011;6252;FCI2EME;919;a123;;
<br />
...
<br />
Des valeurs non vides pour les champs username, promotion, formation, pedagogie, composante, date_cloture sont requises.
Les autres champs peuvent être vides.
<br />Format XML
<br />
&lt;?xml version="1.0" encoding="UTF-8"?&gt;
<br />
&lt;pedagogies&gt;
<br />
   &lt;pedago&gt;
<br />    &lt;username&gt;epsilon&lt;/username&gt;
<br />    &lt;firstname&gt;&lt;text&gt;Epsilon&lt;/text&gt;&lt;/firtsname&gt;
<br />    &lt;lastname&gt;&lt;text&gt;ESPADON&lt;/text&gt;&lt;/lastname&gt;
<br />    &lt;date_cloture&gt;&lt;text&gt;2011-06-01&lt;/text&gt;&lt;/date_cloture&gt;
<br />    &lt;promotion&gt;&lt;text&gt;2011&lt;/text&gt;&lt;/promotion&gt;
<br />    &lt;formation&gt;&lt;text&gt;DIC2I2E1M&lt;/text&gt;&lt;/formation&gt;
<br />    &lt;pedagogie&gt;&lt;text&gt;FCI2EME&lt;/text&gt;&lt;/pedagogie&gt;
<br />    &lt;composante&gt;&lt;text&gt;999&lt;/text&gt;&lt;/composante&gt;
<br />    &lt;num_groupe&gt;&lt;text&gt;a123&lt;/text&gt;&lt;/num_groupe&gt;
<br />    &lt;commentaire&gt;&lt;text&gt;Formation initiale&lt;/text&gt;&lt;/commentaire&gt;
<br />   &lt;/pedago&gt;
<br />(...)
<br />&lt;pedagogies&gt;
<br />Une fois les pédagogies créées ou importées il est possible d\'utiliser cette information pour sélectionner les certificats à exporter...
';

$string['exportpedagoh'] = 'Exporter le fichier des formations';
$string['exportpedagoh_help'] = 'Aide à rédiger';

$string['exportetudh'] = 'Exporter des données de scolarité';
$string['exportetudh_help'] =  'Les données de scolarité sont indispensables pour l\'Impression des certificats.
<br />
Afin de pouvoir par la suite importer des données de scolarité dans le module Référentiel de Moodle
il faut commencer par procéder à un export au format CSV qui sera ensuite complété / modifié sous éditeur
(avec un tableur).
<br />
En effet les données de scolarité que l\'on importe dans Moodle (par l\'onglet Importer) doivent contenir l\'Identifiant
attribué par Moodle à chaque utilisateur lors de la création des comptes dans Moodle.';

$string['exporttaskh'] = 'Exporter les tâches';
$string['exporttaskh_help'] = 'Aide à rédiger';

$string['etablissementh'] = 'Etablissements';
$string['etablissementh_help'] = 'Aide à rédiger';

$string['etudianth'] = 'Etablissements';
$string['etudianth_help'] = 'Aide à rédiger';

$string['pedagoh'] = 'Formations / Pédagogies';
$string['pedagoh_help'] = 'Les administrateurs du site peuvent sélectionner les certificats
des étudiants régulièrement inscrits dans une filière de formation habilitée à délivrer
le certificat concerné.
<br />La liste des formations peut être importée ou saisie.
<br />l\'Importation permet aussi d\'associer directement les étudiants aux formations qui les concernent.';

$string['verroucertificath'] = 'Verrouiller les certificats';
$string['verroucertificath_help'] = 'Le certificat reflète l\'état instantané
des compétences validées dans les activités. Quand de nouvelles déclarations sont validées,
les compétences associées sont immédiatement prises en compte dans le certificat.
<br />C\'est ce processus qu\'Il faut verrouiller lorsque qu\'une campagne de certification
touche à sa fin, afin de préparer le dossier de certification à présenter au jury.';

$string['printh']= 'Formats  d\'Impression';
$string['printh_help']= '* PDF (Adobe ®) fournit un document fac-simile.
* RTF (Rich Text File) est un format pivot pour les traitements de texte.
* OpenOffice (ODT) est le format natif du traitement de texte Writer d\'OpenOffice.
* MSWord DOC est le format propriétaire du traitement de texte Word de Microsoft (version 2000).
* CSV avec séparateur \';\' est un format destiné aux tableurs.
* XHTML s\'affiche dans une page Web.';

$string['printcertificath'] = 'Imprimer les certificats';
$string['printcertificath_help'] = '* Commencez par sélectionner les données qui seront imprimées
en tenant compte du paramétrage imposé par l\'administrateur du site ou par le créateur du référentiel à imprimer.
* Puis sélectionnez le format  d\'Impression';

$string['exportcertificath'] = 'Exporter les certificats';
$string['exportcertificath_help'] = ' Cette fonction est destinée à fournir les résulats
de la certification aux systèmes de gestion de la scolarité.
<br />Commencer par sélectionner les certificats à exporter
<br />Puis choisir le format de fichier :
<br />Le format CSV avec séparateur \';\' est destiné aux tableurs tels MSExcel ou OpenOffice-Calc.
<br />Le format XML destiné à la sauvegarde en vue d\'une restauration ultérieure.
<br />Les format HTML et format XHTML s\'affiche comme une page Web.
<br />
<br />Vous devez ensuite choisir la liste des données exportées
<br />Non condensé : toutes les données disponibles sot exportées
<br />Condensé type 1 : login, nom, prenom, pourcentages par domaine, compétence, item
<br />Condensé type 2 : login, nom, prénom, liste des compétences acquises
<br />Formation / pédagogie :  si disponibles ces informations sont ajoutées à l\'export.
<br />';

$string['selectcertificath'] = 'Sélectionner les certificats';
$string['selectcertificath_help'] = '<br />Les enseignants avec droit d\'édition peuvent sélectionner
les certificats correspondant aux étudiants du cours,
soit individuellement, soit par groupe, soit accompagnés...
<br />Les administrateurs peuvent de plus exporter dans un seul fichier tous les certificats
du site pour un référentiel donné.
<br />
<b>Utilisation des "Promotions / Formations / Composantes / Pédagogies"</b>
<br />Les administrateurs peuvent aussi sélectionner les certificats des étudiants régulièrement
inscrits dans une filière de formation habilitée à délivrer le certificat concerné
en sélectionnant par "Promotion, Formation, Pédagogie, Composante"... si ces données sont disponibles (onglet "Formation")';

$string['exportreferentielh'] = 'Exporter un référentiel';
$string['exportreferentielh_help'] = 'En exportant un référentiel
vous produisez une sauvegarde que vous pouvez restaurer dans votre cours Moodle
par la fonction symétrique "Importer".';

$string['exportoutcomesh'] = 'Barème d\'objectifs';
$string['exportoutcomesh_help'] = 'Si les objectifs sont activés sur votre serveur Moodle
vous pouvez sauvegarder le référentiel sous forme d\'un barème d\'objectifs
puis utiliser ce barème pour évaluer toute forme d\'activité Moodle
(forums, devoirs, bases de données, wiki, etc.)
<br />Le module Référentiel récupèrera ces évaluations et génèrera des déclarations qui seront dès lors accessibles dans
la liste des activités du module référentiel.';

$string['exportactiviteh'] = 'Exporter les activités';
$string['exportactiviteh_help'] = 'Pour exporter les activités d\'une instance
du référentiel vous avez le choix entre plusieurs formats :
<br />Le format XML destiné à la sauvegarde en vue d\'une restauration ultérieure (cette fonction n\'est pas implantée actuellement)
<br />Le format CSV est un format textuel de données tableur avec séparareur \';\'
<br />Les formats XHTML et HTML s\'affichent comme une page Web.
';

$string['uploadh'] = 'Documents';
$string['uploadh_help'] = 'Les documents attachés à une activité sont destinés à fournir
des traces observables de votre pratique.
<br />
A chaque activité vous pouvez associer un ou plusieurs documents, soit en recopiant son adresse Web (URL),
soit en déposant un fichier dans l\'espace Moodle du cours.';

$string['documenth'] = 'Document';
$string['documenth_help'] = '<br />Description du document : Une courte notice  d\'Information.
<br />Type de document : Texte, Exe, PDF, ZIP, Image, Audio, Video, etc.
<br />URL :
<br />Adresse Web du document (qui peut être un document déposé par vos soins dans l\'espace Moodle).
<br />Titre ou une étiquette
<br />Fenêtre cible où s\'ouvrira le document<br />
<br />Fichier déposé depuis votre poste de travail.';

$string['configreferentielh'] = 'Configuration';
$string['configreferentielh_help'] = 'La configuration du module Référentiel concerne :
<br />Les données de scolarité ;
<br />La création, l\'Importation et la modification d\'un référentiel ;
<br />La possibilité d\'associer un référentiel existant à une nouvelle instance de celui-ci ;
<br />l\'Impression des certificats...

<br />
En fonction de votre rôle vous avez accès à différents niveaux de configuration.
<br />
<b>N.B.</b> Un paramétrage incohérent peut vous interdire de compléter la création d\'un nouveau référentiel ou de télécharger un référentiel existant.';

$string['configsiteh'] = 'Configuration au niveau du site';
$string['configsiteh_help'] = 'Au niveau du site Moodle la configuration incombe à l\'administrateur du serveur.
Un paramétrage au niveau du site s\'Impose à toutes les occurrences et toutes les instances de tous les référentiels du site.';
$string['configrefglobalh'] = 'Configuration au niveau du référentiel';
$string['configrefglobalh_help'] = 'Pourvu que l\'administrateur du serveur l\'y autorise,
tout enseignant avec droit d\'édition disposant du mot de passe ad hoc *
peut compléter la configuration au niveau du référentiel.
<br />
Les choix de configuration s\'Imposent alors de façon globale à l\'occurrence du référentiel concerné,
mais sans incidence sur les autres occurrences de référentiels.
En résumé, la configuration au niveau du site dicte celle de chaque référentiel
au niveau global qui dicte celle au niveau de l\'Instance...';

$string['configreflocalh'] = 'Configuration au niveau de l\'Instance';
$string['configreflocalh_help'] = 'Si les paramétrages de niveau supérieur leur en laissent la possibilité,
les enseignants avec droit d\'édition peuvent
configurer chaque instance de certification d\'un référentiel.
En résumé, la configuration au niveau du site dicte celle de chaque référentiel
au niveau global qui dicte celle au niveau de l\'Instance...';

$string['selectreferentielh'] = 'Sélectionner un référentiel ';
$string['selectreferentielh_help'] = 'Si des occurrences de référentiels sont déjà installées
sur votre serveur moodle vous pouvez en sélectionnner une pour l\'associer à l\'Instance de certification
que vous venez de créer.
<br />
En cochant "Filtrer les occurrences sélectionnées" vous pouvez n\'afficher que les occurrences locales
(propres au cours où l\'Instance est crée) ou bien accéder aux occurrences globales
(définies pour tous les cours du serveur).';

$string['suppreferentielh'] = 'Supprimer le référentiel ';
$string['suppreferentielh_help'] = 'Avant de supprimer un référentiel commencez par supprimer toutes ses instances dans tous les cours où celles-ci sont déclarées.
<br />
<b>Attention</b> :
<br />Ne pas confondre la suppression d\'une instance (une activité de type certification définie au niveau d\'un cours
et qui pointe vers une occurrence de référentiel)
avec la suppression d\'une occurrence de référentiel (au niveau du site) ;
<br />La suppression d\'une occurrence de référentiel supprime aussi toutes les déclarations d\'activité, tâches et certificats associés à celle-ci ;
<br />Une fois un référentiel supprimé celui-ci n\'est plus disponible au niveau du site, à moins d\'être importé à nouveau. ';

$string['modifreferentielh'] = 'Modifier le référentiel ';
$string['modifreferentielh_help'] = 'Vous ne pouvez modifer une occurrence de référentiel
que si vous en êtes l\'auteur initial ou si vous avez un rôle d\'administrateur du serveur.
<br />Tout modification au niveau d\'une occurrence de référentiel
est propagée à toutes les instances de celui-ci.
<br />En augmentant le nombre de domaines / compétences / items d\'une occurrence
vous pouvez lui ajouter autant de rubriques correspondantes.';

$string['certificath']= 'Certificats';
$string['certificath_help']= 'La certification est le processus par lequel les compétences sont déclarées, évaluées, validées.
<br />
Le certificat fournit une vue instantanée
de l’avancement de l\'étudiant dans le processus de certification.
<br /> Un certificat contient :
<br />
<br />Une liste de compétences validées pour le référentiel considéré ;
<br />La référence de l\'enseignant ayant validé le certificat ;
<br />La date et la décison du jury quant à la certification ;
<br />Une synthèse éventuelle rédigée par l\'étudiant certifé et un commentaire du formateur référent ;
';

$string['certificat2h']= 'Validation d\'un certificat';
$string['certificat2h_help']= '<p>Quand un certificat est verrouillé (liste de compétences sur fond rose),
plus aucune compétence ne peut s’y ajouter, le dossier de l\'étudiant étant considéré comme clos.</p>
<p>Les item d\'empreinte nulle ne sont pas retenus dans la certification</p>
<p>Un Item de compétence n\'est pris en compte dans la validation du certificat
que si le nombre de déclarations validées pour cet item est supérieur à l\'empreinte de l\'Item.</p>
<p>Pour permettre d\'apprécier le chemin qui reste à faire pour obtenir une compétence ou un domaine de compétences,
des notes sont affichées par compétence et par domaine sous forme de pourcentages.</p>
<br />
<br />Au niveau de l\'Item :<br />100% * NOMBRE_ITEM_VALIDE / ENPREINTE_ITEM
<br />Au niveau de la compétence :<br />100 * SOMME_SUR_ITEMS_COMPETENCE(NOMBRE_ITEM_VALIDE / POIDS_ITEM) / SOMME_SUR_ITEMS_COMPETENCE(POIDS_ITEM * ENPREINTE_ITEM)
<br />Au niveau du domaine :<br />100% * SOMME_SUR_ITEMS_DOMAINE(NOMBRE_ITEM_VALIDE / POIDS_ITEM) / SOMME_SUR_ITEMS_DOMAINE(POIDS_ITEM * ENPREINTE_ITEM)

<p>Autrement dit la contribution d\'un Item à la note de la compétence et du domaine qui le contiennent
est proportionnelle au produit POIDS * EMPREINTE.</p>
';

$string['referentielh'] ='Référentiel';
$string['referentielh_help'] ='Un référentiel de compétence est une hiérarchie d’objets :
<pre>Référentiel
   Domaine 1 (Code, Description)
      Compétence 1.1 (Code, Description)
         Item 1.1.1 (Code, Description, Poids)
         Item 1.1.2 (Code, Description, Poids)
      Compétence 1.2 (Code, Description)
         Item 1.2.1 (Code, Description, Poids)
         Item 1.2.2 (Code, Description, Poids)
         Item 1.2.3 (Code, Description, Poids)
   Domaine 2 (Code, Description)
      Compétence 2.1 (Code, Description)
         Item 2.1.1 (Code, Description, Poids)
         Item 2.1.2 (Code, Description, Poids)
...
</pre>
<br />Pour que le référentiel soit opérationnel,
il doit comporter au moins un domaine, une compérence, un item
';

$string['referentiel2h'] ='Domaine / Compétence / Item';
$string['referentiel2h_help'] ='
<br />
  <br /><b>Nom</b> [<em>obligatoir</em>] : C\'est l\'Identité du référentiel.
  <br /><b>Code</b> [<em>obligatoire</em>] : C\'est une étiquette commode pour identifier
    le référentiel.
  <br /><b>URL</b> [<em>facultatif</em>] : La référence sur le Web de l\'Initiateur
    du référentiel (par exemple le N° d\'un Bulletin officiel de l\'Education nationale
    française...).
  <br /><b>Seuil de certification</b> [<em>valeur décimale ; facultatif</em>] :
    Si un barème et des notes sont associées à l\'obtention de chaque compétence,
    le seuil est la valeur au delà de laquelle l\'Impétrant est certifié.
  <br /><b>Référentiel global</b> [<em>oui / non ; par défaut oui</em>] : Un référentiel
    global est accessible depuis d\'autres cours que celui où il a été créé.
  <br /><b>Nombre de domaines à enregistrer pour ce référentiel</b> [<em>valeur
    numérique ; obligatoire</em>] : Le nombre de domaines à créer. <br />
    <em>On peut démarrer à un et augmenter par la suite cette valeur pour définir
    de nouveaux domaines.</em>
  <br /><b>Liste des codes</b> [<em>généré ; obligatoire</em>] : les codes des items associés. <br />
    <em>Cette liste est calculée à partir des items de compétence.</em>
  <br /><b>Liste des poids</b> [<em>généré ; obligatoire</em>] : les poids des items associés. <br />
    <em>Cette liste est calculée à partir des items de compétence.</em>
  <br /><b>Liste des empreintes</b> [<em>généré ; obligatoire</em>] : les empreintes des items associés. <br />
    <em>Cette liste est calculée à partir des items de compétence.</em>
';

$string['referentielinstanceh'] = 'Instance de certification';
$string['referentielinstanceh_help'] ='Une instance de certification est une activité au sens Moodle
(comme le sont "Devoir", "Forum" ou "Leçon") s\'appuyant sur une occurrence de référentiel et qui permet aux élèves / étudiants :
<br />de déclarer des activités mobilisant des savoir-faire ou habiletés qui seront évaluées par les enseignants
<br />de pointer les compétences en regard d\'une "occurrence de référentiel"';

$string['taskh']= 'Tâches';
$string['taskh_help']= 'Les tâches sont des réalisations ou des activités proposées par les enseignants aux étudiants de leur cours.
<br />
Seuls les enseignants peuvent créer des tâches
<br /> Titre de la tâche : pour faciliter son repérage par les étudiants
<br />Description : Indiquez ici le contexte et les objectifs de la tâche
<br />Compétences mobilisées par cette tâche : Celles que l\'accomplissement de la tâche légitime
<br />Consignes et critères de réussite afin de faciliter la réalisation de la tâche
<br />Documents attachés
<br />
<br />Vous pouvez joindre des documents pour illustrer la tâche ou la cadrer ';
$string['ataskh_link']= 'http://docs.moodle.org/fr/mod/referentiel/task';

$string['souscriptiontaskh']= 'Souscription à une tâche';
$string['souscriptiontaskh_help']= 'La souscription  à une tâche consiste pour un étudiant à cocher celle-ci dans la liste des tâches (onglet "Tâche").
Ce faisant une nouvelle activité lui est automatiquement ajoutée, portant sur le contenu de la tâche souscrite (onglet "Activité").

Modalités de souscription
*   Souscription libre : chacun décide ou pas de souscrire à une tâche ;
*   Souscription restreinte à un groupe : l\'enseignant désigne les étudiants susceptibles de souscrire librement à une tâche ; les autres ne peuvent y souscrire ;
*   Souscription imposée : l\'enseignant inscrit d\'office un groupe d\'étudiants à la tâche';


$string['accompagnementh']= 'Accompagnement et notification';
$string['accompagnementh_help']= 'L\'accompagnement consiste à désigner un ou plusieurs enseignants en regard de chaque étudiant du cours. Ce faisant vous répartirez la charge de suivi des étudiants.
Cela influe en particulier la notification des nouvelles activités.
Il faut toutefois noter que l\'affectation d\'un référent à un étudiant n\'empêche pas les autres enseignants du cours de voir les déclarations de cet étudiant et d\'éventuellement les évaluer.';

$string['notificationh']= 'Notification';
$string['notificationh_help']= 'La notification consiste à faire envoyer par Moodle un mèl  d\'Information reprenant l\'essentiel des déclarations d\'activité ou des tâches, sur le modèle de ce qui se pratique pour les messages d\'un forum Moodle
Le créateur d\'une activité doit demander explicitement la notification de celle-ci en cochant un bouton avant l\'enregistrement son enregistrement.
Si des référents sont affectés à un étudiant, ils seront les seuls à recevoir la notifications des nouvelles activités déclarées par celui-ci ;
Si par contre aucun référent n\'est affecté à un étudiant, toute notification d\'une nouvelle activité sera adressée à tous les enseignants du cours ;';

$string['activiteh']= 'Activités';
$string['activiteh_help']= 'Plutôt que de déclarer des compétences item par item l’étudiant peut pointer ses compétences en déclarant des activités.
Une activité contient une partie déclarative rédigée par l\'étudiant, elle pointe une liste de compétences de ce référentiel et un nombre illimité de documents peuvent être associés à une activité.
Pour que les compétences déclarées dans une activité soient prises en compte pour la certification cette activité doit être validée par un enseignant.';

$string['creer_activiteh']= 'Créer une nouvelle activité';
$string['creer_activiteh_help']= '*     Type d\'activité : En typant une activité vous facilitez son repérage par les enseignants.
*     Description : Indiquez ici le contexte et les objectifs de l\'activité
*     Compétences mobilisées par cette activité : Celles que l\'activité légitime.
*     Documents : Pour fournir des traces observables de votre pratique, citez un ou plusieurs documents.';

$string['creer_documenth']= 'Document attaché';
$string['creer_documenth_help']= '*        Description du document : Une courte fiche de présentation
*         Type de document [Texte, Exe, PDF, ZIP, Image, Audio, Video, etc.] : Afin d\'en faciliter l\'affichage.
*         URL : Copiez l\'adresse Web du document
*         Vous pouvez aussi déposer le document dans l\'espace Moodle';

$string['name_instanceh']= 'Titre de l\'Instance ';
$string['name_instanceh_help']= 'Le titre est celui affiché pour décrire l\'activité, par exemple "Déclarez vos compétences C2i"';

$string['formath']= 'Formats  d\'Importation / exportation';
$string['formath_help']= '* XML est un format textuel de données représentant l\'Information comme une hiérarchies de balises et de données imbriquées. Il permet la sauvegarde et la restauration  de référentiels, d\'activités et de certificats.
* Le format CSV est un format textuel de données tabulaires (comme dans un tableur).
Le format CSV utilise un séparateur, en général \';\' entre les cellules de la table.
* Les formats HTML et XHTML s\'affichent comme des pages Web.';

$string['overrider']= 'Remplacer un référentiel';
$string['overrider_help']= 'Si l\'occurrence du référentiel importé existe déjà sur le serveur
(même nom, même code) elle est alors remplacée par la version importée
si vous avez choisi d\'écraser la version existante, sinon l\'Importation est interrompue.';

$string['overrideo']= 'Remplacer l\'occurrence associée';
$string['overrideo_help']= '**Attention** : l\'Instance courante de ce cours est déjà
associée à une occurrence de référentiel.
Faut-il remplacer celle-ci par la version importée ?
Sinon l\'Importation sera interrompue.';

// End Help strings /////////////////////////////////////////////////////////

// Version 2012/03/06
$string['a_completer'] ='Al completar';
$string['c_domaine'] ='D';
$string['c_competence'] ='C';
$string['c_item'] ='I';



$string['activation_protocole'] ='Activación del protocolo de certificación';
$string['protocole_active'] ='Protocolo de certificación activo ';
$string['protocole_desactive'] ='Protocolo de certificación desactivado  ';
$string['depuis'] =' despues de {$a} ';
$string['aide_activation'] = 'Umbrales y items / competencias / dominios obligatorios son tomados en consideración sólo si el protocolo es activado.';


// Version 2012/02/12
$string['declarer_activite'] = 'Déclarar una actividad y competencias';
$string['type_domaine'] = 'Dominio obligatorio (Si, No)';
$string['seuil_domaine'] = 'Umbrale del dominio (Num. decimal)';
$string['type_competence'] = 'Competencia obligatoria (Si, No)';
$string['seuil_competence'] = 'Umbrale de competencia (Num. decimal)';
$string['bilan'] = 'Balance de competencia';
$string['competences_valides'] = 'Competencias (1:valido 0:non valido)';
$string['validite'] = '<b>Nb</b> / Total (<i>Umbrale</i>) Validez';
$string['validable'] = 'Validable';
$string['non_validable'] = 'Non validable';

$string['ask_pass'] = '(Contraseña inicial creada por {$a})';
$string['error_pass'] = 'Error de contraseña.<br />'.$string['ask_pass'];

$string['gestion_protocole'] = 'Gestión del protocolo de certificación';
$string['protocole'] = 'Protocolo';
$string['item_obligatoires'] = 'Items oblig. <i>P.</i> E.';
$string['competences_oblig_seuil'] = 'Comp. obl. Umbrale / <i>P.</i>';
$string['domaines_oblig_seuil'] =    'Dom. obl. Umbrale / <i>P.</i>';
$string['aide_protocole'] = 'Puntee los ítems / competencias / dominios obligatoires
<br />y indique el umbral de certificación para ellas competencias y ellos dominios...
<br />(Valores nulos no tomados en consideración en el protocolo)';
$string['aide_protocole_completer'] = 'Al REEMPLAZAR...
[Jugando sobre ítems / competencias / dominios obligatorios et los umbrales de certification usted se puede exprimir limitaciones que conciernen al protocolo de certificación...]';
$string['aide_seuil'] = 'Número mínimo de los ítems validados a reserva de condiciones suplementarias impuestas.';


// version '6.1.04 - 2012/01/09';
$string['usedoutcomes'] = 'Objetivos d\'el referencial utilizados en las actividades del sitio';
$string['occurrence'] = 'Occurrencia';
$string['module'] = 'Modulo';
$string['titre'] = 'Titro';
$string['module_activite'] = 'Modulo de activitad';

// Outcomes / Objectifs
$string['activer_outcomes'] = 'Para exportar la base en forma de tabla de los objetivos, haga acceso activar los Objetivos (Outcomes) sobre este sitio por el administrador.';
$string['help_outcomes'] = '<br />La evaluación por objetivos (competencias, fines, estándares o criterios)
permite de evaluar los resultados de cualquier actividad (foro, deber, etc.) después de la tabla utilizada.';
$string['scale'] = 'Tabla';
$string['outcome'] = 'Objetivo';
$string['outcomes'] = 'Objetivos';
$string['export_bareme'] = 'Tabla y Objetivos';
$string['export_outcomes'] = 'Exportar el referencial en forma de tabla y de objetivos';
$string['outcome_type'] = 'Activitad';
$string['outcome_date'] = 'Fecha : ';
$string['outcome_description'] = '$a->name : $a->description ';
$string['description_inconnue'] = 'Ninguna descripción';
$string['nom_bareme'] = 'Item referencial';
$string['bareme'] = 'Non pertinente,Non validado,Validado';
$string['description_bareme'] = 'Esta tabla es destinada a evaluar la adquisición del competencias del módulo referencial.';

// Version 6.1
// repartition des competences entre enseignants
$string['notification'] = 'Notification';
$string['repartition_notification'] = 'Reparto de la notificación';

$string['identite_utilisateur'] = ' para {$a}';
$string['repartition'] = 'Control';
$string['liste_repartition'] = 'Reparto de las competencias entre referentes';
$string['aide_repartition'] = 'Destíne le a cada referente la lista del competencias a evaluar';
$string['destinataires_notification'] = 'Destinatarios de la notificación ';

// Version 5
$string['groupe'] = 'Grupo {$a}';

// moodle/admin/report/referentiel stuff
$string['formatarchive'] = 'Seleccione un formato d\'archivo';
$string['moodleversion'] = 'Su versión de Moodle : {$a}';
$string['gerer_archives'] = 'Archivo';
$string['deletefile'] = 'Suprimir';
$string['user'] = 'Usuario';
$string['size'] = 'Talla';
$string['nbfile'] = 'Número de ficheros : {$a}';
$string['archives'] = 'Archivos';
$string['users_actifs'] = 'Activos';
$string['activites_declarees'] = 'Declaraciones';
$string['instancedeleted'] = 'Instancia suprimida';
$string['suppression_instance_impossible'] = 'La supresión de esta instancia fue suspendido.';
$string['supprimer_instance'] = 'Suprimir esta instancia';
$string['nonexist'] = 'Instancia inexistente en este curso';
$string['instances'] = 'Instancias';
$string['occurrences'] = 'Occurrencias';
$string['occurrencedeleted'] = 'Occurrencia suprimida';
$string['instancenondefinie'] = 'Alguna instancia n\'es definida para este referencial';
$string['adminreport'] = 'Administración del módulo Referencial';
$string['majmoodlesvp'] = 'Esta versión de Moodle es demasiado antigua. Haga la actualización por favor ({$a} requerida)';

// moodle/admin/report/referentiel stuff
$string['uploadproblemfilesize'] = 'Fichero demasiado voluminoso';
$string['maxsize'] = 'Talla máxima documentos atado : <b>{$a}</b>';
$string['export_file_number'] = '{$a} documentos atados ';
$string['export_file_size'] = ' totalizando <b>{$a}</b>';

$string['export_documents'] = 'Exportar los documentos agregados ';
$string['export_url'] = ' en forma de lazos';
$string['export_data'] = ' en forma de ficheros';

$string['selectarchive'] = 'Seleccionar expedientes numéricos que hay que archivar';
$string['not_deleted'] = ' non suprimada ';
$string['format_pourcentage'] = 'Exportar los porcentajes por dominio ';
$string['liste_poids_competence'] = 'Lista pesos';
$string['generatedby'] = 'Archivo generado por el módulo Referencial de Moodle';
$string['archivextra'] = 'El archivo está colocado en el expediente del servidor {$a}';
$string['archivefilename'] = 'referentiel/archive';        // NE PAS MODIFIER
$string['archivetemp'] = 'temp/archive';                   // NE PAS MODIFIER
$string['archive'] = 'Archivar';
$string['archivereferentiel'] = 'Archivar el refrencial, los certificados y las actividades asociadas';
$string['archivingreferentiels'] = 'El refrencial, los certificados y las actividades son exportados hacia un fichero';

// help
$string['format_archiveh'] = 'Archivo de las actividades y certificados';
$string['format_archiveh_help'] = 'El Archivo contiene la totalidad de las declaraciones
d\'actividad y los ficheros atados bajo forma de página web o de fichero XML
Al importar en un porfolio de tipo Mahara.
Para exportar los datos que hay que tratar conviene más bien pasar por l\'uñero \ "Exportar" o "Imprimir".
';
$string['format_certificath'] = 'Archivo de competencias certificadas';
$string['format_certificath_help'] = 'Pueden ser exportadas:
<br /> a <b> Formato normal </b>: lista ítems validados.
<br /> a <b> Formato porcentaje </b>: los porcentajes de los ítems validados consolidados por Competencia y Dominio.';

$string['light'] = 'Fijación ligera del referencial, sin peso ni huellas';
$string['light_display'] = 'Fijación ligera del referencial';
$string['config_light'] = 'Con arreglo al valor de <i>referentiel_light_display</i>
los profesores con derecho de edición pueden o no autorizar el fijación de los pesos e impregnados del référentiel.
<ul>
<li>Si <i>referentiel_light_display</i> vale 0 los profesores con derecho de edición pueden seleccionar el modo ligero;
</li><li>Si <i>referentiel_light_display</i> vale 1 los profesores no pueden seleccionar el modo ligero,
pero el creador del curso puede modificar este parámetro para su curso ;
</li><li>Si <i>referentiel_light_display</i> vale 2, el fijación ligera es prohibida y más de este parámetro encerrada al nivel del sitio y pues es no modificable al nivel del curso.
</li>
</ul>';


// Version 5.6.02
$string['date_proposition']= 'Fecha de la proposicion';
$string['confirmdeletepedagos']= 'Confirmar la supresión de las formaciones registradas';
$string['vider_pedagos']= 'Suprimir todas las formaciones y las pedagogías asociadas';
$string['precedent']= 'Página precedente';
$string['suivant']= 'Página siguiente';

// Version 5.5.8
$string['nondefini']= 'Indefinido';
$string['forcer_regeneration']= 'Forzar la generación de los certificados';

$string['firstname'] = 'Nombre';
$string['lastname'] = 'Nombre patronímico';
$string['certiftraite'] = ' Certificados tratados...';
$string['verroucertificat'] = 'Encerrar / Quitar el cierre los certificados';
$string['verrou_all'] = 'Encerrar todo';
$string['deverrou_all'] = 'Quitar el cierre todo';
$string['verroucertif'] = 'Cerrojos';
$string['selectverroucertificat'] = 'Seleccionar los certificados que hay que encerrar / quitar el cierre';

// Version 5.5.6
$string['export_pedagos'] = 'Exportar también los datos de formación / pedagogía';
$string['incompatible_cle'] = 'Las llaves del referencials son incompatibles';
$string['incompatible_code'] = 'Los códigos del referencials son incompatibles';
$string['incompatible_competences'] = 'Las listas de competencias son incompatibles';
$string['aide_incompatible_cle'] = 'Suprima el campo clave en el fichero importado';
$string['aide_incompatible_code'] = 'Reemplace el campo code_referentiel en el fichero importado por el en el referencial instalado';
$string['aide_incompatible_competences'] = 'Verifique que los códigos del competencias son estrictamente idénticos a los del referencial instalado.';

// Version 5.5.5
$string['selectcertificatprint'] = 'Seleccionar los certificados que hay que imprimir';
$string['pedagos'] = 'Formaciónes';
$string['pedago'] = 'Formación';
$string['selectcertificat'] = 'Seleccionar los certificados que hay que exportar';
$string['cocher'] = 'Escoger';
$string['exportcertifpedagos'] = 'Seleccionar las formaciones / pedagogías que hay que exportar';
$string['nbcertifs'] = 'Número de certificados';
$string['aide_selection_pedago'] = 'Filtre las formaciones luego puntee cuyos certificados serán exportados. <br /> Atención: ¡ ciertas combinaciones pueden ser incompatibles !';
$string['aide_association'] = 'Asocíele su formación a cada estudiante';
$string['pedagogie'] = 'Pedagogía';
$string['listpedago'] = 'Formaciónes';
$string['creer_pedago'] = 'Registrar una nueva formación / pedagogía';
$string['addpedago'] = 'Crear';
$string['listasso'] = 'Associations';
$string['formation'] = 'Formación';
$string['exportpedago'] = 'Exportar';
$string['exportingpedagos'] = 'Formaciones / pedagogías exportadas a un fichero';
$string['exportpedagos'] = 'Seleccionar formaciones';
$string['exportpedagogies'] = 'Exportar datos de formación / pedagogía';
$string['importpedagogies'] = 'Importar los datos de formación / pedagogía';
$string['importpedagos'] = 'Importar los datos de formación / pedagogí';
$string['importpedago'] = 'Importar';
$string['composante'] = 'Componente';
$string['promotion'] = 'Promoción';
$string['editpedago'] = 'Modificar';
$string['editasso'] = 'Asociar';
$string['num_groupe'] = 'Grupo / disciplina';
$string['date_cloture'] = 'Cierre expedientes';
$string['modifier_pedago'] = 'Modificar la ficha de formación {$a}';
$string['nopedago'] = 'Ninguna formación / pedagogía definida';



// Version 5.4.13
$string['statcertif'] = 'Grafico';
$string['certification_etat'] = 'Estado de la evaluacion competencial';
$string['code_items'] = 'Items';
$string['poids'] = 'Pesos';
$string['moyenne'] = 'Media';
$string['seuil'] = 'Umbral';
$string['select_aff_graph'] = 'Prohibir de fijar el gráfico de los certificados validados a una nueva instancia de referencial';
$string['config_aff_graph'] = 'Con arreglo al valor de <i>referentiel_affichage_graphique</i>
Los profesores con derecho de edición pueden o no autorizarles la fijación a los estudiantes de los gráficos de certificación.
<ul>
<li>Si <i>referentiel_affichage_graphique</i> vale 0 los profesores con derecho de edición pueden autorizar esta fijación ; </li>
<li>Si <i>referentiel_affichage_graphique</i> vale 1 los profesores no pueden autorizar esta fijación, pero el creador del curso puede modificar este parámetro para su curso;
</li><li>Si <i>referentiel_affichage_graphique</i> vale 2, el fijación es prohibida y más de este parámetro encerrada al nivel del sitio y pues no es modificable al nivel del curso.
</li></ul>';

$string['not_activite_tache_1'] = '<i>Todos los estudiantes del curso que tienen esta tarea recibiran el correo !</i>';
$string['not_activite_tache_2'] = '<i>Todos los docentes relacionados con esta tarea recibiran el correo !</i>';
$string['not_activite_tache_3'] = '<i>Todos los docentes y discentes relacionados con esta tarea recibiran el correo !</i>';

$string['not_tache_1'] = '<i>Todos los estudiantes del curso recibiran el correo !</i>';
$string['not_tache_2'] = '<i>Todos los profesores del curso recibiran el correo !</i>';
$string['not_tache_3'] = '<i>Docentes y discentes del curso recibiran el correo !</i>';


$string['referentiels'] = 'Referenciales';


$string['login'] = 'Login';
$string['pdf'] = 'PDF Formato';
$string['rtf'] = 'RTF Formato';
$string['msword'] = 'MSWord Formato';
$string['ooffice'] = 'OpenOffice Formato';
// version 5.4.3
$string['ressources_associees'] = "Recursos asociados";
$string['ressource_associee'] = "Recursos asocados a la actividad";
$string['notification_commentaire'] = 'Envia este comentario';
$string['savedoc'] = 'Modificar';
$string['num'] = 'N° ';

// version 5.4.3
$string['release'] = 'N° ';
$string['modifier_instance_referentiel'] = 'Modificar la instancia <i>$a</i> ';
$string['referentiel_config_local_interdite'] = '<b>Cuidado</b>
<br />Permisos a nivel de instancia deshabilitados.';
$string['aide_referentiel_config_local_impression'] = '<b>Cuidado</b>
<br /><i>Esta configuracion es la que envia los datos a los certificados.</i>';
$string['referentiel_config_impression_local_interdite'] =  '<b>Cuidado</b>
<br /><i>La configuracion para impresion esta desabilitada a nivel de instancia.</i>';
$string['pass_referentiel_admin'] = 'Palabra clave que protege este referencial';
$string['configref']= 'Settings';
$string['referentiel_config_global_impression'] = 'Configuracion impresion de certificados ';
$string['configurer_referentiel'] = 'Configuracion a nivel global ';
$string['referentiel_config_global'] = 'Configuracion ';
$string['aide_referentiel_config_global'] = 'Los parametrode de configuracion dependen de otros :
<br /> primero a nivel de sitio y del administrador de Moodle
<br /> luago a nivel global de transductor
<br /> despues a nivel de instancia.
Una inconsistencia puede impedir la modificacion o creacion de nuevas instancias o modificaciones.';

// version 5.3.7
$string['consignes']= 'Instrucciones';
// version 5.3.5
$string['ajouter_dominios'] = 'Aumentar este valor para generar nuevos dominios en este referencial...';
$string['ajouter_competences'] = 'Aumentar este valor para  nuevas competencias en este dominio...';
$string['ajouter_items'] = 'Incremetar este valor para  nuevos items en esta competencia...';
$string['importfileupload_xml_simple'] = 'Subir un archivo XML (\'_sxml.xml\' extension )...';
$string['importfilearea_xml_simple']= 'Importar de un archivo XML de curso (\'_sxml.xml\' extension )...';
$string['assigner'] = 'Asignar una tarea';
// version 5.3.4
$string['uploadproblemfile'] = 'Error en el archivo bajado';
$string['formatincompatible'] = 'No coincide el formato del archivo bajado con el especificado';

$string['editer_referentiel'] = 'Editar un referencial';
$string['editeur_referentiel'] = 'XML Editor';
$string['editer_referentiel_xml'] = 'Editar un referencial XML simplificado';
$string['charger_referentiel_xml'] = 'Cargar un referencial XML simplificado';
$string['import_referentiel_xml'] = 'Importar un referencial XML simplificado';

// version 5.3.3
$string['aide_referentiel_config_local'] = 'Los parámetros de configuracion e impresion a nivel de instancia de transductor dependen del administrador del sitio. Una inconsistencia en los parámetros de configuracion puede impedirte completar la tarea que deseas realizar en el transductor.';
$string['not_verrou'] = 'Sin bloquear';
$string['f_auteur'] = 'Autor';
$string['f_verrou'] = 'Bloqueado';
$string['f_date_decision'] = 'Fecha de decision';
$string['decision_favorable'] = 'Decision favorable';
$string['decision_defavorable'] = 'Decision desfavorable';
$string['decision_differee'] = 'En espera';
$string['rediger_decision'] = 'Derivacion de la decision a tribunal';

$string['saisie_synthese_certificat'] = 'Bosqueja tu sintesis';
$string['synthese_certificat'] = 'Sintesis';
$string['synthese'] = 'Sintesis';
$string['aide_synthese_certificat'] = 'La sintesis es un pequeño texto que presenta el entrno en el cual fue realizada la acreditacion.';
// version 5.3.2
$string['format_condense'] = 'Imprimir en formato condensado ';

// version 5.3.1
$string['activites_tache_delete'] = 'Seleccionar / Deseleccionar actividades para borrar...';
$string['delete_all_activity_task_closed'] ='Esta tarea está cerrada. Borrar todas las actividades relacionadas con la tarea ? ';
$string['delete_all_activities_task'] = 'Borrar todas las actividades relacionadas con la tarea ?';
$string['delete_all_task_associations'] = 'Borrar tarea y actividades asociadas';
$string['confirmdeleterecord_all_activities'] = 'Confirmar borrado de tarea<b>y actividades asociadas...</b>';

// version 5.2.5
$string['ref_course'] = 'ID de curso';
$string['teacherid'] = 'ID de docente';
$string['description_document'] = 'Descripcion';
$string['ref_activite'] = 'ID de actividad';

// version 5.2.4
$string['exportallcertificates'] = 'Exportar todos los certificados';
// version 5.2.3
$string['date_modif_by'] = 'el docente ';
$string['date_modif_student_by'] = 'autor  ';
$string['select_acc'] = 'Seleccionar discente';
$string['select_all'] = 'Seleccionar todos';
$string['select_not_any'] = 'Deseleccionar todos';
$string['REF'] = 'Tutor academico';
$string['ACC'] = 'Tutor practicas';
$string['aide_accompagnement'] = 'Emparejamiento discentes-tutores academicos';
$string['liste_accompagnement'] = 'Discentes / tutores';
$string['eleves'] = 'Discentes';
$string['addreferent'] = 'Adjuntar tutor/a academico/a';
$string['notmatched'] = 'No existe Tutor/a para este discente';
$string['noaccompagnement'] = 'No existen discentes asociados a este tutor/a academico';
$string['referents'] = 'Tutores academicos';
$string['type_accompagnement'] = 'Tipo de tutorizacion en las practicas';

$string['accompagnement'] = 'Tutoria practicas';
$string['listaccompagnement'] = 'Listado';
$string['manageaccompagnement'] = 'Modificar';
$string['addaccompagnement'] = 'Emparejar';
$string['tache_masquee_num'] = 'Tarea oculta nº ';
$string['tache_masquee'] = 'Tarea oculta';
$string['taskiscurrentlyhidden'] = 'Tarea oculta';
$string['un_enseignant'] = 'Docente de este curso';

$string['referentiel:addactivity']='Nueva actividad';
$string['referentiel:exportcertif']='Exportar todos los certificados';
$string['referentiel:addtask']='Nueva una tarea';  	  	  	  	  					
$string['referentiel:approve'] = 'Aprobar';
$string['referentiel:comment'] = 'Comentar';
$string['referentiel:export'] = 'Exportar referenciales';
$string['referentiel:import'] = 'Importar un referenciales';
$string['referentiel:managecertif'] = 'Gestionar certificados';
$string['referentiel:managecomments'] = 'Gestionar comentarios';
$string['referentiel:managescolarite'] = 'Gestionar escolaridad';
$string['referentiel:rate'] = 'Aprobar una actividad';
$string['referentiel:select'] = 'Seleccionar una actividad';
$string['referentiel:selecttask'] = 'Seleccionar una tarea';
$string['referentiel:view'] = 'Ver referenciales';
$string['referentiel:viewrate'] = 'Ver criterios';
$string['referentiel:viewscolarite'] = 'Ver escolaridad'; 									
$string['referentiel:viewtask'] = 'Ver tareas'; 									
$string['referentiel:write'] = 'Modificar actividades';
$string['referentiel:writereferentiel'] = 'Escribir referenciales';

// version 5.1.1
$string['subscribed_task'] = 'Tarea suscrita';

// version 5.1.0
$string['activer_outcomes'] = 'Primero pedirle al administrador que habilite los resultados (conocidos como competencias, objetivos, estándares o criterios), lo que significa que se pueden graduar varias escalas y tipos de actividades para evaluar a través de la plataforma.';
$string['help_outcomes'] = 'Resultados (conocidos también como competencias, objetivos, estándares o criterios), lo que significa que se pueden graduar varias escalas y tipos de actividades para evaluar a través de la plataforma.';

$string['scale'] = 'Escala';
$string['outcome'] = 'Resultado';
$string['outcomes'] = 'Resultados';
$string['export_bareme'] = 'Escala y resultados';
$string['export_outcomes'] = 'Exportar referenciales, escalas y resultados';
$string['outcome_type'] = 'Actividad';
$string['outcome_date'] = ' Fecha: ';
$string['outcome_description'] = '$a->nombre : $a->descripcion ';
$string['description_inconnue'] = 'Sin descripcion';
$string['nom_bareme'] = 'Almacen de items de competencias';
$string['bareme'] = 'No aplicable, No validado, Validado';
$string['description_bareme'] = 'Esta escala es para evaluacion de resultados del modulo perteneciente al referencial.';
// version 4.4.1
$string['choix_web_link'] = 'Puedes pegar un enlace a una web';
$string['choiximportfilearea'] = 'O importar de un fichero del curso......';
$string['choiximportfileupload'] = 'O subir un nuevo archivo...';

$string['processingdigest'] = 'Procesando resumen e mail para usuario $a';
$string['activites'] = 'Actividades';
$string['notification_certificat'] = 'Enviar por correo este certificado';
$string['notification_tache'] = 'Enviar por correo esta tarea';
$string['notification_activite'] = 'Enviar por correo esta actividad';

$string['digestmailheader'] = 'Este es tu resumen diario $a->sitename skills repositories. Para cambiar tus preferencias de correo, ve a $a->userprefs.';
$string['digestmailprefs'] = 'Preferencia como usuario';
$string['digestmailsubject'] = '$a: Resumen Referenciales';
$string['digestsentusers'] = 'Correo de resumen referenciales enviado con éxito $a usuarios.';
$string['postincontext'] = 'Ver este correo en su contexto';
$string['postmailinfo'] = '$a->type du module rÃ©fÃ©rentiel sur le site $a->site. Pour consulter, cliquer sur ce lien :';
$string['bynameondate'] = 'por $a->nombre - $a->fecha';

$string['valide_empreinte'] = 'validado [/footprint]';
$string['competence_certifiee'] = 'Competencias acreditadas (0:No 1:Si)';
// version 4.2.3
$string['cocher_enregistrer'] = 'Seleccionar solo actividades que se registrarán...';
$string['activites_tache'] = 'Selecionar / deseleccionar actividades para validar...';
// version 4.2.2
// To translate
$string['aide_souscription'] = 'Seleccionar estudiantes para relacionar con la tarea.';
$string['cle_souscription'] = 'clave suscripcion';
$string['mode_souscription'] = 'Modalidades de suscripcion';
$string['souscription_forcee'] = 'Suscripcion obligatoria';
$string['aide_souscription_forcee'] = '<i>Link this task to a choosen audience.</i>';
$string['aide_souscription_cle'] = '<i>To restrict the subscription, enter a key to diffuse to the concerned audience.</i>';
$string['obtenir_cle_souscription'] = " <i>Ask </i>\$a<i> to get the key...</i>";
$string['souscription'] = 'Suscripcion';
$string['souscription_libre'] = 'Suscripcion  libre ';
$string['souscription_restreinte'] = 'Suscripcion con clave';
$string['libre'] = 'Libre';
$string['avec_cle'] = 'Con clave';
$string['cle'] = 'Clave';

// version 4.2.1 
$string['selection_champs_etudiant'] = 'Seleccionar items de estudiante/s para imprimir';
$string['items'] = 'Items';
$string['certificat_sauver_parametre'] = 'Guardar parámetros';
$string['config_sauver_parametre'] = 'Guardar parámetros para la siguiente impresion';
$string['detail_referentiel'] = 'Transductor al detalle';

$string['pourcentage'] = 'Porcentajes';
$string['referentiel_config_local_impression'] = 'Imprimir configuracion de certificados';
$string['refcert'] = 'Imprimir transductor de competencias como cabecera de certificado.';
$string['instcert'] = 'Imprimir instancia de referencial de competencias como cabecera de certificado..';
$string['numetu'] = 'Imprimir nº de estudiante.';
$string['nometu'] = ' &nbsp; &nbsp; &nbsp; nombre y apellido de estudiante.';
$string['etabetu'] = ' &nbsp; &nbsp; &nbsp; escuela.';
$string['ddnetu'] = ' &nbsp; &nbsp; &nbsp; Fecha de nacimiento de estudiante.';
$string['lieuetu'] = ' &nbsp; &nbsp; &nbsp; lugar de nacimiento de estudiante.';
$string['adretu'] = ' &nbsp; &nbsp; &nbsp; direccion de estudiante.';
$string['detail'] = 'Print detailled competencies data.';
$string['pourcent'] = ' &nbsp; &nbsp; &nbsp; resultados por porcentajes de competencias y dominios.';
$string['compdec'] = ' &nbsp; &nbsp; &nbsp; listado de competencias por actividades.';
$string['compval'] = ' &nbsp; &nbsp; &nbsp;  Listado de competencias certificadas.';
$string['nomreferent'] = ' &nbsp; &nbsp; &nbsp; nombre del docente.';
$string['jurycert'] = ' &nbsp; &nbsp; &nbsp; decision del jurado.';
$string['comcert'] = ' &nbsp; &nbsp; &nbsp; comentarios del docente.';

$string['selection_champs_certificat'] = 'Seleccionar campos del certificado para imprimir.';
$string['certificat_sel_print_format'] = 'Seleccionar formato de impresion';
$string['certificat_sel_referentiel'] = 'Descripcion referencial';
$string['certificat_sel_referentiel_instance'] = 'Instancia de referencial';
$string['certificat_sel_etudiant'] = 'Datos de estudiantes';	
$string['certificat_sel_etudiant_numero'] = 'Numero de estudiante';
$string['certificat_sel_etudiant_nom_prenom'] = 'Nombre y apellidos del estudiante';
$string['certificat_sel_etudiant_etablissement'] = 'Institucion estudiante';
$string['certificat_sel_etudiant_ddn'] = 'Fecha de nacimiento del estudiante';
$string['certificat_sel_etudiant_lieu_naissance'] = 'Lugar de nacimiento del estudiante';
$string['certificat_sel_certificat'] = 'Certificate data';
$string['certificat_sel_certificat_detail'] = 'Detailled Certificate data';
$string['certificat_sel_certificat_pourcent'] = 'Resultados en porcentajes';
$string['certificat_sel_activite_competences'] = 'Competencias declaradas';
$string['certificat_sel_certificat_competences'] = 'Competencias certificadas';
$string['certificat_sel_certificat_referents'] = 'Nombre del profesor';
$string['certificat_sel_decision_jury'] = 'Decision colegiada';
$string['certificat_sel_commentaire'] = 'Comentarios y sintesis';
$string['certificat_sel_etudiant_adresse'] = 'Direccion del estudiante';	

$string['config_certificat_sel_referentiel'] = 'Mostar competencias del referencial en la cebecera del certificado.';
$string['config_certificat_sel_referentiel_instance'] = 'Mostar instancia de las competencias del referencial en la cebecera del certificado.';
	
$string['config_certificat_sel_etudiant_nom_prenom'] = 'Mostar el nombre del discente en la cabecera del certificado.';
$string['config_certificat_sel_etudiant_numero'] = 'Mostar el numero de estudiante.';
$string['config_certificat_sel_etudiant_etablissement'] = 'Mostar nombre institucion/escuela.';
$string['config_certificat_sel_etudiant_ddn'] = 'Mostrar fecha de nacimiento del estudiante.';
$string['config_certificat_sel_etudiant_lieu_naissance'] = 'Mostrar lugar de nacimiento del estudiante.';

$string['config_certificat_sel_certificat_detail'] = 'mostrar detalles.';
$string['config_certificat_sel_certificat_pourcent'] = 'Mostar porcentajes de dominios y competencias.';
$string['config_certificat_sel_activite_competences'] = 'Mostrar el listado de competencias declaradas en las actividades.';
$string['config_certificat_sel_certificat_competences'] = 'Mostar listado de competencias validadas/acreditadas.';
$string['config_certificat_sel_certificat_referents'] = 'Mostar nombre del docente.';
$string['config_certificat_sel_decision_jury'] = 'Mostar decision de la comision evaluadora/evaluador.';
$string['config_certificat_sel_commentaire'] = 'Mostar comentario de docentes/tutores.';
$string['config_certificat_sel_etudiant_adresse'] = 'Mostrar direccion de los estudiantes.';

// version 4.2.0
$string['cible_link'] = 'Abrir enlace en una nueva ventana';
$string['etiquette_document'] = 'Titulo del documento';
$string['etiquette_consigne'] = 'Titulo sugerido';
$string['creer_activite_teacher'] = '<i>Estas declarando una actividad para ti como profesor...</i>.';

// version 4.1.2
$string['competences_declarees'] = 'Competencias declaradas en las actividade por $a ';
$string['tous'] = 'Todo';
$string['nocommentaire'] = 'Sin comentario';
$string['suivi'] = 'Seguido por';
$string['non_examine'] = 'No';
$string['examine'] = 'Si';
$string['croissant'] = 'Orden creciente';
$string['decroissant'] = 'Orden decreciente';
$string['competences_declarees'] = 'Competencias declaradas en actividades por $a ';

// A traduire ou retraduire
// version 4.0.1
$string['zero_activite'] = 'Hasta ahora no hay actividad declarada $a por ';
$string['menu'] = 'Menu';
$string['activite_exterieure'] = 'Otro curso... ';
$string['id_activite'] = "Actividad <i>\$a</i>";
$string['date_modif'] = 'Modificado por el profesor el ';
$string['date_modif_student'] = 'Modificado por el autor el ';
$string['listactivityall'] = 'Detalles';
$string['listactivitysingle'] = 'Detalles';
$string['f_validation'] = 'Validacion';
$string['f_date_modif'] = 'Actualizado';
$string['f_date_modif_student'] = 'Fecha autor';

// version 3.3.4
$string['evaluation_par'] = 'Evaluacion por';
$string['web_link'] = 'Pega enlace a web';

// version 3.2.5

$string['erreur_creation'] = 'Error en la creacion, faltan datos: (Nombre y tipo codigo...). Volver.';
$string['exporttask'] = 'Exportas tareas ';
$string['exportingtasks'] = 'Tareas exportadas a archivo';
$string['notask'] = 'No hay tarea registrada ';
$string['instance'] = 'Instancia';

// version 3.2.4
$string['filtrer'] = 'Filtrar';

// version 3.2.3
$string['supprimer_referentiel'] = 'Borrar el referencial';
$string['suppression_non_autorisee'] = 'No puedes borrar este referencial';
$string['cours_externe'] = 'En otro curso';
$string['cours_courant'] = 'En este curso';
$string['deletereferentiel'] = 'Borrar';
$string['instance_deleted'] = 'Instancia borrada';
$string['selection_instance_referentiel'] = 'Selecciona las instancias para borrar';

$string['ressaisir_pass_referentiel'] = 'Entra nueva password ';
$string['suppression_pass_referentiel'] = 'Borra password ';
$string['suppression_referentiel_impossible'] = 'Borra primero las instancias de referencial';

// version 3.1.4
$string['modifier_depot_document'] = 'Modify<br />linked document ';
$string['competences_bloquees'] = 'Bloqueado listado de competencias';
$string['modifier_depot_consigne'] = 'Modify<br />linked instruction document ';

// Version 3.1

$string['modifier_consigne'] = 'Modificar documento de instrucciones';
$string['consigne_associee'] = 'Instrucciones asociadas a la tarea ';

$string['version'] ='Referencial version: ';
$string['modulename-intance'] ='Instancia del referencial';

// Version 3.0
$string['addtask'] = 'Declarar';
$string['aide_cle_referentiel'] = 'Key is an identifier computed with skills repository code and uthor mail adress';
$string['aide_creer_referentiel'] = 'You have to link that instance of skills repository with an <i>existent</i> pository or with a <i>new</i> one.';
$string['aide_pass_referentiel'] = 'A password protects that pository againts modification.';

$string['annee'] = 'Año';
$string['approve_all_activity_task'] = 'Validadas todas las actividades asociadas con esta tarea ';
$string['approve_all_activity_task_closed'] = 'Esta tarea esta cerrada. Validar todas las actividades asociadas con esta tarea ? ';
$string['approved_task_by'] = 'Tarea aprobada por ';
$string['check_pass_referentiel'] = 'Teclear password para proteger este referencial.';
$string['cle_referentiel'] = 'Key value';
$string['closed_task'] = 'Tarea cerrada';
$string['config_creer_referentiel'] = 'The value of <i>config_select_referentiel</i> determines if editingteachers may or may not select an existing skills repository.
<ul><li>If <i>referentiel_creation_limitee</i> is 0, editing teachers <i>may</i> create or import a skills repository at course level ;
</li><li>If <i>referentiel_creation_limitee</i> is 1, editing teachers <i>may not</i> create or import a skills repository at course level but but course creator may modify this parameter for his course ;
</li><li>If <i>referentiel_creation_limitee</i> is 2, editing teachers <i>may not</i> create or import a skills repository at course level and this parameter is locked at site level.</li></ul>';

$string['config_impression_referentiel'] = 'This parameter governs certificates print policy.
<ul><li>If <i>impression_referentiel_autorisee</i> is 0, editing teachers may print certificates from the course ; </li>
<li>If <i>impression_referentiel_autorisee</i> is 1, editing teachers may not print certificates from the course, but course creator may modify this parameter for his course ;</li>
<li>If <i>impression_referentiel_autorisee</i> is 2 edit editing teachers may not print certificates and this parameter is locked at site level.</li></ul>';
$string['config_scolarite'] = 'Scolarship data are needed to edit and print certificates. Sometimes thez are typed in by editing teachers of the le course. 
In that case they have to be readable. Sometimes thezy are downloaded by Moodle administrator. In that case they have to be hidden to users.
<ul><li>If <i>referentiel_scolarite_masquee</i> is 0 editing teachers <i>may</i> display or import scolarship data at course level ; </li>
<li>If <i>referentiel_creation_limitee</i> is 1 editing teachers <i>may not</i> display or import scolarship data at course level, but course creator may modify this parameter for his course ;</li>
<li>If <i>referentiel_creation_limitee</i> is 2 editing teachers <i>may not</i> display or import scolarship data at course level and this parameter is locked at site level.</li></ul>';
$string['config_select_referentiel'] = 'Parameter <i>referentiel_selection_autorisee</i> governs skills repository selection at course level.
<ul><li>If <i>referentiel_selection_autorisee</i> is 0, after a new instance of skills repository has been created editing teachers <i>may</i> link an existing skills repository to that instance ; </li>
<li>If <i>referentiel_selection_autorisee</i> is 1 editing teachers <i>may not</i> select an existing skills repository to link it with the new instance, but course creator may modify this parameter for his course ;</li>
<li>If <i>referentiel_selection_autorisee</i> is 2, selection of exiting skills repository is <i>forbidden</i> and this parameter is locked at site level.</li></ul>';
$string['config_verrouillee'] = 'Configuracion bloqueada por el administrador';
$string['configuration'] = 'Configuracion';
$string['confirm_association_task'] = 'Confirmar seleccion de tarea';
$string['confirm_validation_task'] = 'Confirmar validacion de tarea.<br /><b>Atencion</b> : La confirmacion validara automaticamente todas las suscripciones a esta tarea. !';
$string['consigne']= 'Instruccion Num.';
$string['consigne']= 'Documento';
$string['consigne_ajout'] = 'Anexar documento de instrucciones';
$string['consigne_associe']= 'Documento anexo';
$string['consigne_associee'] = 'Documento de instrucciones anexo';
$string['consigne_associee'] = 'Documento';
$string['consigne_task'] = 'Mision';
$string['create_referentiel'] = 'Creacion e importacion de referencial prohibidas.';
$string['creer_task'] = 'Crear una tarea';
$string['creref']='Prohibida la creacion e importacion de referencial';
$string['criteres_evaluation'] = 'Criterios de Evaluacion';
$string['date_debut'] = 'Fecha de comienzo';
$string['date_fin'] = 'Fecha de finalizacion';
$string['date_modification'] = 'Modificado el ';
$string['depot_consigne']= 'Anexar documento de instrucciones';
$string['existe_pass_referentiel'] = 'Leave empty to keep existing password.';
$string['heure'] = 'Hora';
$string['impcert']='Prohibida impresion de certificados';
$string['impression_referentiel_autorisee'] = 'Forbid certificates printing';
$string['jour'] = 'Dia';
$string['listtask'] = 'Mostrar';
$string['listtasksingle'] = 'Detalles';
$string['modif_task'] = 'Modificar';
$string['modifier_task'] = 'Modificar tarea';
$string['mois'] = 'Mes';
$string['pass_referentiel'] = 'Password';
$string['referentiel_config'] = 'Pository instance Configuration';
$string['scol']='Scolarship data hiddden to users';
$string['scolarite_user'] = 'Scolarship data hiddden to users';
$string['select_referentiel'] = 'Pository Selection Forbidden';
$string['selecttask'] = 'Elegir';
$string['selref']='Pository Selection Forbidden';
$string['souscrire'] = 'Suscribir';
$string['task'] = 'Tarea';
$string['tasks'] = 'Tareas';
$string['type_consigne']= 'Tipo de documento';
$string['type_task']= 'Tema';
$string['updatetask'] = 'Modificar';


// Version  1.3 2009/03/25
// A traduire ou retraduire
$string['referentiel_config_local'] = 'Practicas';
$string['masquee'] = 'Oculta';
$string['visible'] = 'Visible';

$string['scolarite_user'] = 'Fecha de escolarizacion oculta';
$string['config_scolarite'] = 'Es necesaria la fecha de escolarizacion para los certificados. 
Teachers may edit them for ther course in skills repository module, 
in this case these data have no to be hidden; if data are loaded by Administrator, they have to be hidden.';
$string['exportetudiant'] = 'Export';
$string['importetudiant'] = 'Import';
$string['import_etudiant'] = 'Import Students adresses';
$string['export_etudiant'] = 'Export Students adresses';
$string['importetudiants'] = 'Importer scholarship data';
$string['exportetudiants'] = 'Exporter cholarship data';
$string['importstudentsdone'] = 'Scholarship data import finished';
$string['exportingetudiants'] = 'Institutions an Students adresses are expoted to a file';
$string['noetudiant'] = 'Not any student registered. <br />You have to register students to this course... ';

// Version  2008/06/21
// A traduire ou retraduire
$string['html'] = 'Formato HTML ';
$string['code_referentiel'] = 'Codigo referencial'; 
$string['description_referentiel'] = 'Descripcion'; 
$string['url_referentiel'] = ' URL Referencial'; 
$string['nb_dominios'] = 'Numero de dominios'; 

$string['code_dominio'] = 'Codigo de dominio'; 
$string['description_dominio'] = 'descripcion de dominio'; 
$string['num_dominio'] = 'Numero de dominio'; 
$string['nb_competences'] = 'Numero de competencias'; 
$string['code_competence'] = 'Codigo de competencia'; 
$string['description_competence'] = 'Descripcion de competencia'; 
$string['num_competence'] = 'Numero de competencia'; 
$string['nb_item_competences'] = 'Numero de items'; 
$string['description_item'] = 'Descripcion de item';

// Version  2008/06/17

// en_utf8 traduits par Philippe V.
$string['depot_document'] = 'Subir documento asociado ';
$string['modifier_document'] = 'Modificar un documento';
$string['nouvelle_fenetre'] = ' en una nueva ventana.';
$string['ouvrir'] = 'Abrir ';

$string['nocertificat'] = 'El certificado no se ha guardado. <br />Una actividad tiene que ser declarada y evaluada por un docente... ';
$string['enseignant'] = "Docente ";
$string['date_naissance'] = "Nacido en ";
$string['date_signature'] = "En \$a - Firma";
$string['date_instance'] = 'Fecha'; 
$string['ref_referentiel'] = 'Referencial'; 
$string['visible'] = 'Visible'; 
$string['ID'] = 'ID'; 
$string['competences'] = 'Competencias'; 
$string['decision'] = 'Decision'; 
$string['date decision'] = "Fecha de decision : \$a"; 
$string['userid'] = ' ID Usuario'; 
$string['valide_par'] = 'Validado por'; 
$string['verrou'] = 'Bloqueado'; 
 
$string['num_item'] = 'Numero de Item';	
$string['t_item'] = 'Tipo';
$string['p_item'] = 'Peso';
$string['e_item'] = 'reiteraciones';
$string['empreinte_item']  = 'reiteraciones [0..999]:';
$string['empreinte'] = 'Reiteracion [0..999]: Numero de veces que ha de ser validad esta competencia para su acreditacion/certificacion.';
$string['etiquette_inconnue'] = 'Etiqueta desconocida. Click para mostrar la pagina';
$string['etiquette_url'] = 'Etiqueta para enlace ';
$string['liste_empreintes_competence'] = 'Listado de huellas o reiteraciones';
$string['liste_codes_empreintes_competence'] = ' Listado de Codigos<br />Pesos<br />y <i>Huellas</i> ';
$string['listactivitysingle'] = 'Detalles';
$string['upload_succes'] = 'Archivo subido con exito.';



$string['abandonner'] = 'Cancelar';
$string['activite'] = 'Actividad ';
$string['addactivity'] = 'Declarar';
$string['addcertif'] = 'Generar';
$string['aide_saisie_competences'] = 'Seleccionar las competencias desplegadas por esta actividad ';
$string['approved'] = 'Validado';
$string['approve'] = 'Validar';
$string['associer_referentiel'] = 'Asociar a referencial ';
$string['auteur'] = 'Autor';

$string['cannotcreatepath'] = 'El archivo no puede ser creado ($a)';
$string['cannoteditafterattempts'] = 'No se puede adicionar o borrar competencia, todavia hay envios.';
$string['cannotinsert'] = 'No adicionar competencia';
$string['cannotopen'] = 'No es posible abrir el archivo exportado ($a)';
$string['cannotread'] = 'Imposible abrir el archivo importado (o archivo vacio)';
$string['cannotwrite'] = 'No se puede editar el archivo exportado ($a)';
$string['certificat'] = 'Certificar';
$string['certificats'] = 'Certificados';
$string['certificat_etat'] = 'Estado del certificado ';
$string['certificat_initialiser'] = 'Reinicializar el certificado ';
$string['certificat_verrou'] = 'Bloquear el certificado? ';
$string['certification'] = 'Certificacion';
$string['certifier'] = 'Certificar';
$string['choisir'] = '_Elegir_';
$string['choix_newinstance'] = 'Si';
$string['choix_notnewinstance'] = 'No';
$string['choosefile'] = 'Elegir archivo';
$string['choix_instance'] = 'Elegir instancias ';
$string['choix_filtrerinstance'] = 'Deseas filtrar las instancias seleccionadas? ';
$string['choix_oui_filtrerinstance'] = 'Si ';
$string['choix_non_filtrerinstance'] = 'No ';
$string['choix_localinstance'] = 'local';
$string['choix_globalinstance'] = 'global';
$string['choix_override'] = 'Sobreescribir';
$string['choix_notoverride'] = 'Mantener (no sobreescribir)';
$string['cocher_competences_valides'] = 'Seleccionar las competencias ya validadas ';
$string['code'] = 'Codigo ';
$string['comment'] = 'Comentario';
$string['comment_certificat'] = 'Comentario en certificado';
$string['commentaire'] = 'Comentarios';
$string['commentaire_certificat'] = 'Comentarios';
$string['competence'] = 'Competencia ';
$string['competences_activite'] = 'Competencias declaradas para esta actividad ';
$string['competences_certificat'] = 'Competencias certificadas ';
$string['competence_inconnu'] = 'Competencia desconocida ';
$string['competences_validees'] = 'Competencias ya validadas ';
$string['completer_referentiel'] = 'Completar referencial ';
$string['confirmdeleterecord'] = 'Deseas borrar realmente el registro? ';
$string['confirminitialiserecord'] = 'Deseas reinicializar el registro <br />El contenido sera borrado y la competencia actualizada...';
$string['confirmvalidateactivity'] = 'Por favor, confirma la <b>validacion</b> de la activiad ';
$string['confirmdeverrouiller'] = 'Por favor, confirma el <b>desblqueo</b> del certificado ';
$string['confirmverrouiller'] = 'Por favor, confirma el <b>bloqueo</b> del certificado ';
$string['confirmdevalidateactivity'] = 'Por favor, confirma la <b>invalidacion</b> de la actividad ';
$string['corriger'] = 'Correcto';
$string['creation_dominio'] = 'Crear Dominio ';
$string['creation_competence'] = 'Crear Competencia ';
$string['creation_item'] = 'Crear Item ';
$string['creer'] = 'Crear';
$string['creer_certificat'] = 'Crear un certificado';
$string['creer_referentiel'] = 'Crear un Referencial ';
$string['creer_activite'] = 'Declarar una actividad';
$string['creer_instance_referentiel'] = 'Crear una instancia del referencial ';
$string['csv'] = 'Formato CSV';

$string['date_creation'] = 'Creado el ';
$string['date_modif'] = 'Modificado el ';
$string['date_decision'] = 'Fecha de decision'; 	
$string['decision_jury'] = 'Decision de la comision/evaluador'; 	
$string['declarer'] = 'Declarar';
$string['delete_activity'] = 'Borrar';
$string['deletecertif'] = 'Borrar';
$string['desapprove'] = 'No validar';
$string['description'] = 'Descripcion ';
$string['description_instance'] = 'Descripcion de la Instancia ';
$string['deverrouille'] = 'Desbloqueado';
$string['deverrouiller'] = 'Desbloquear';
$string['document'] = 'Documento';
$string['document_ajout'] = 'Anexar un documento';
$string['document_associe'] = 'Documentos asociados a la actividad ';
$string['dominio_inconnu'] = 'Dominio desconocido ';
$string['dominio'] = 'Dominio ';
$string['download'] = 'Click para bajar el archivo exportado';
$string['downloadextra'] = '(Archivo disponible en los ficheros del curso, en el directorio /referencial)';

$string['edit_activity'] = 'Actividades';
$string['editcertif'] = 'Editar';
$string['editetudiant'] = 'Editar';
$string['edit_an_activity'] = 'Editar una actividad';
$string['editreferentiel'] = 'Modificar';
$string['erreur_referentiel'] = 'No hay referencial definido ';
$string['erreurscript'] = "PHP script error:<i> \$a </i>. Inform module's author.";
$string['etablissement']= 'Institucion';
$string['etablissements']= 'Instituciones';
$string['etudiant']= 'Docente ';
$string['etudiants']= 'Docentes';
$string['evaluation']= 'Evaluacion';
$string['exportreferentiel'] = 'Exportar referencial';
$string['export'] = 'Exportar';
$string['exportactivite'] = 'Exportar actividades ';
$string['exportcertificat'] = 'Exportar certificados ';
$string['exporterror'] = 'Se ha prodicido un error durante la exportacion';
$string['exportfilename'] = 'referentiel'; // quiz : Ne pas traduire cette chaîne ! Voir http://tracker.moodle.org/browse/MDL-4544
$string['exportingreferentiels'] = 'Referenciales exportados a fichero';
$string['exportingactivites'] = 'Actividades exportadas a fichero';
$string['exportname'] = 'Nombre de fichero';
$string['exportingcertificats'] = 'Certificados exportados a fichero';
$string['exportnameformat'] = '%%Y%%m%%d-%%H%%M';

$string['filtrerlocalinstance'] = 'If the above option is selected, choose the instances ';
$string['fileformat'] = 'File format';
$string['fileprint'] = 'Printing format';
$string['formatnotfound'] = 'Import/export $a format not found';
$string['formatnotimplemented'] = 'This format has not been correctly implemented';
$string['fromfile'] = 'From file&nbsp;:';

$string['globalinstance'] = 'Instancia global ';
$string['global'] = '[Global instance]';

$string['id'] = 'ID ';
$string['import'] = 'Importar';
$string['importerror_referentiel_id'] = 'An error has occured during the import process: Unknown skills repository id';
$string['importerror'] = 'An error has occured during the import process';
$string['importfilearea'] = 'Import from a course file...';
$string['importfileupload'] = 'Import from a file to be uploaded...';
$string['importfromthisfile'] = 'Import from this file';
$string['importing'] = 'Import skills repository $a from file';
$string['importmax10error'] = 'Esta competencia tiene un error. No puede haber mas de 10 respuestas';
$string['importmaxerror'] = 'Esta competencia presenta un error. hay demasiadas respuestas para esta competencia.';
$string['importminerror'] = 'Esta competencia tiene un error. No existen suficientes respuestas para este tipo de competencia.';
$string['importreferentiel'] = 'Importar referencial';
$string['importdone'] = 'Referencial inportado satisfactoriamente...';
$string['importtodatabase'] = 'Importar de base de datos...';
$string['incompletedata'] = 'No se encuentran datos... Proceso de importacion interrumpido. ';
$string['inconnu'] = 'DESCONOCIDO';
$string['item'] = 'Item ';
$string['item_supplementaire'] = 'Item adicional ';

$string['label_dominio_question'] = 'Etiqueta de <i>dominio</i>? ';
$string['label_competence_question'] = 'Etiqueta de <i>competencia</i>? ';
$string['label_item_question'] = 'Etiqueta de <i>item</i>? ';
$string['label_dominio'] = 'Etiqueta de dominios ';
$string['label_competence'] = 'Etiqueta de competencias';
$string['label_item'] = 'Etiqueta de items ';
$string['liste_codes_competence'] = 'Competencias ';
$string['listactivity'] = 'Listado';
$string['listcertif'] = 'Listado';
$string['listetudiant'] = 'Estudiantes';
$string['lister'] = 'Listar';
$string['listreferentiel'] = 'Listado';
$string['listreferentiel'] = 'Listado ';
$string['localinstance'] = 'Instancias locales ';
$string['local'] = '[Local instance]';
$string['logo'] = 'Logo';

$string['managecertif'] = 'Exportar';
$string['manageetab'] = 'Nueva institucion';
$string['modification'] = 'Modificado';
$string['modifier'] = 'Modificar';

$string['modifier_referentiel'] = 'Modificar almac&en de competencias ';
$string['modifier_dominio_referentiel'] = 'Modificar dominio de competencia ';
$string['modifier_competence_referentiel'] = 'Modificar competencia ';
$string['modifier_item'] = 'Modificar item ';
$string['modifier_item_competence_referentiel'] = 'Modificar item ';
$string['modifier_activite'] = 'Modificar actividad';
$string['modifier_certificat'] = 'Modificar certificado';
$string['modifier_etudiant'] = 'Modificar';
$string['modulenameplural'] = 'Referenciales de competencias ';
$string['modulename'] = 'Referencial de competencias ';
$string['moins'] = 'Menos';

$string['name'] = 'Nombre ';
$string['name_instance'] = 'Titulo de instancia ';
$string['newinstance'] = 'Crear una nueva instancia en el curso a partir del referencial cargado? ';
$string['noaccess'] = 'Acceso no permitido ';
$string['noaccess_certificat'] = 'Acceso a los certificados no permitido ';
$string['noactivite'] = 'Actividad no guardada ';
$string['noactivitefiltre'] = 'Actividades sin filtrar ';
$string['nocertificat'] = 'El certificado no se ha guardado ';
$string['nombre_dominios_supplementaires'] = 'Numero de dominios a guardad<br />en este referencial de competencias ';
$string['nombre_competences_supplementaires'] = 'Numero de competencias<br />para guardar en este dominio';
$string['nombre_item_competences_supplementaires'] = 'Numero de items<br />para guardar en esta competencia ';
$string['numero'] = 'Numero ';
$string['noinfile'] = 'Proceso de importacion cancelado<br />(Datos incorrectos o riesgo de sobreescritura) ';
$string['invalide'] = 'invalido';
$string['nologo'] = 'Logo ?';
$string['noresponse'] = 'Sin respuesta';
$string['not_approved'] = 'No validado';

$string['override'] = 'Si existe una version local idéntica (mismo nombre, mismo codigo) hay que: ';
$string['overriderisk'] = ' : Riesgo de sobreescritura de la version local ';

$string['parsing'] = 'Análisis del archivo importado.';
$string['plus'] = 'Plus';
$string['poids_item'] = 'Peso del Item (valor decimal) ';
$string['profil'] = 'Perfil';
$string['print'] = 'Imprimiendo';
$string['printfilename'] = 'Imprimiendo';
$string['printcertif'] = 'Imprimir';
$string['printcertificat'] = 'Imprimir certificados';

$string['quit'] = 'Sallir';

$string['recordapproved'] = 'Registro guardado con éxito ';
$string['referent'] = 'Referente';
$string['referentiel'] = 'Referencial de competencias';
$string['referentiel_global'] = 'Referencial Global ';
$string['referentiel_inconnu'] = 'referencial desconocido ';
$string['referentiel_instance'] = 'Instancia del referencial ';

$string['saisie_competence'] = 'Debes introducir al menos una nueva competencia ';
$string['saisie_competence_supplementaire'] = 'Introduce una nueva competencia ';
$string['saisie_dominio_competence'] = 'Debes introducir al menos un nuevo dominio ';
$string['saisie_dominio_supplementaire'] = 'Introduce un nuevo dominio ';
$string['saisie_item'] = 'Debes introducir al menos un item ';
$string['saisie_item_supplementaire'] = 'Introducir un nuevo item ';
$string['scolarite'] = 'Institucion';
$string['select'] = 'Seleccionar';
$string['selectreferentiel'] = 'Seleccionar un referencial disponible ';
$string['selecterror_referentiel_id'] = 'Error durante el proceso: número id del referencial desconocido';
$string['stoponerror'] = 'Parada por error';
$string['selectnoreferentiel'] = 'El referencial no se ha guardado ';
$string['seuil_certificat'] = 'Nivel de certificacion (valor decimal) ';
$string['single'] = 'Mostrar actividad';
$string['supprimer_activite'] = 'Borrar actividad';

$string['type'] = 'Tipo ';
$string['type_activite'] = 'Tipo de actividad ';
$string['type_document'] = 'Tipo de documento ';
$string['extensions_document'] = '[Texto, Exe, PDF, ZIP, Imagen, Audio, Video...] ';
$string['type_item'] = 'Tipo de item [Obligatorio, Opcional...] ';

$string['updateactivity'] = 'Modificar';
$string['updatecertif'] = 'Modificar';
$string['url'] = 'URL ';

$string['valide'] = 'válido';
$string['validation'] = 'Validacion';
$string['verrouille'] = 'Bloqueado';
$string['verrouiller'] = 'Bloqueo';
$string['visible_referentiel'] = 'Es visible el referencial? ';

$string['xhtml'] = 'XHTML formato';
$string['xml'] = 'XML Moodle formato';
$string['xmlimportnoname'] = 'El nombre del referencial no aparece en el archivo xml';
$string['xmlimportnocode'] = 'Code of missing skill in the xml file';

$string['nom_prenom'] = 'Nombre'; 
$string['num_etudiant'] = 'Numero ID de estudiante'; 
$string['ddn_etudiant'] = 'Fecha de nacimiento'; 
$string['lieu_naissance'] = 'Lugar de nacimiento'; 
$string['departement_naissance'] = 'Codigo postal (lugar de nacimiento)'; 
$string['adresse_etudiant'] = 'Direccion'; 
$string['ref_etablissement'] = 'Referencia de institucion ';

$string['modifier_etablissement'] = 'Modificar institucion';
$string['num_etablissement'] = 'Número ID de la institucion'; 	
$string['nom_etablissement'] = 'Nombre de la institucion' 	;
$string['adresse_etablissement'] = 'Direccion de la institucion';
?>
