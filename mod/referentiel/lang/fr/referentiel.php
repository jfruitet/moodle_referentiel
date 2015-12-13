<?php
// ----------------
// UTF-8 French
//
$string['passdelete'] = 'Supprimer tout mot de passe associé à ce référentiel';

$string['notsameoccurrence'] = 'Cette occurrence de référentiel n\'est pas compatible';
$string['notsameinstance'] = 'Cette instance n\'est pas compatible';

$string['erreur1']= 'Paramètre manquant. Vous ne pouvez pas appeler cette page (<i> {$a} </i>) sans lui fournir une liste de paramètres obligatoires... ';
$string['type_aide']= 'Au plus 20 caractères S.V.P.';
// Events
$string['eventeditpageviewed'] = 'Page éditée';
$string['eventreferentielcomplete'] = 'Referentiel complété';
$string['eventactivitycreated'] = 'Activité  crée';
$string['eventactivitydeleted'] = 'Activité  supprimée';
$string['eventactivityupdated'] = 'Activité  mise à jour';
$string['eventactivityviewed'] = 'Activité affichée';
$string['eventcertificatviewed'] = 'Certificat affiché';
$string['eventallpedagogydeleted'] = 'Toutes les Pédagogies sont supprimées';
$string['eventconfigupdated'] = 'Configuration mise à jour';
$string['eventinstancedeleted'] = 'Instance supprimée';
$string['eventinstanceupdated'] = 'Instance mise à jour';
$string['eventinstanceviewed'] = 'Instance affichée';
$string['eventdomaincreated'] = 'Domaine créé';
$string['eventdomaindeleted'] = 'Domaine supprimé';
$string['eventdomainupdated'] = 'Domaine mis à jour';
$string['eventcompetencycreated'] = 'Compétence créée';
$string['eventcompetencydeleted'] = 'Compétence supprimée';
$string['eventcompetencyupdated'] = 'Compétence mise à jour';
$string['eventitemcreated'] = 'Item crée';
$string['eventitemdeleted'] = 'Item supprimé';
$string['eventitemupdated'] = 'Item mis à jour';
$string['eventoccurrencecreated'] = 'Occurrence créée';
$string['eventoccurrencedeleted'] = 'Occurrence supprimée';
$string['eventoccurrenceupdated'] = 'Occurrence mise à jour';
$string['eventinstanceupdated'] = 'Referentiel instance mise à jour';
$string['eventpedagogydeleted'] = 'Pedagogie supprimée';
$string['eventpedagogyauserdeleted'] = 'Association de pedagogie supprimée';
$string['eventstudentcreated'] = 'Student record created';
$string['eventstudentdeleted'] = 'Student record deleted';
$string['eventstudentupdated'] = 'Student record updated';
$string['eventtaskcreated'] = 'Tâche créé';
$string['eventtaskdeleted'] = 'Tâche supprimée';
$string['eventtaskupdated'] = 'Tâche mise à jour';
$string['eventtaskviewed'] = 'Tâche affcihée';

$string['pagination_yes'] = 'Affichage paginé';
$string['pagination_no'] = 'Affichage non paginé';
$string['explaingeneralconfig'] = 'Ces paramètres sont <strong>toujours</strong> utilisés';
$string['generalconfig'] = 'Configuration générale';
$string['settings'] = 'Configuration Referentiel';
$string['bilanactivity'] = 'Bilan';
$string['activitynumber'] = '{$a} activités.';
$string['modifactivity'] = 'Modifier';
$string['eval_bareme'] = 'Evaluer avec barème';
$string['eval_sans_bareme'] = 'Evaluer sans barème';
$string['click_to_load'] = 'Cliquer ici pour charger la page';
$string['block_ref'] = 'Pour créer une nouvelle occurrence passez par le Block Référentiel...';
$string['assignementdoc'] = 'Document du devoir';
$string['commentby'] = 'Commentaire par ';
$string['submission'] = 'Soumission : ';
$string['config']= 'Configuration';
$string['non_evaluated'] = 'Non évalué';
$string['referentiel_no_config_local'] = 'Configuration désactivée : ';
$string['referentiel_no_print_certif'] = 'Impression des certificats désactivée ';
$string['instance_level'] = ' au niveau de l\'Instance';
$string['certif_setting'] = 'Paramétrage de l\'affichage de l\'onglet "Certificat"';
$string['ok_config_certif_occ']='autorisée au niveau de l\'occurrence';
$string['no_config_certif_occ']='interdite au niveau de l\'occurrence';
$string['ok_config_certif']='autorisée au niveau de l\'instance';
$string['no_config_certif']='interdite au niveau de l\'instance';
$string['yes_config_certif_locale']='Autoriser au niveau de l\'instance ?';
$string['no_config_certif_locale']='Interdire au niveau de l\'instance ?';
$string['cfcertif']='Interdire la configuration de l\'affichage de l\'onglet "Certificat"';
$string['certif']=' &nbsp; &nbsp; &nbsp; Afficher l\'onglet "Certificat"';

$string['nocertif']=' &nbsp; &nbsp; &nbsp; Désactiver l\'affichage de l\'onglet "Certificat"';
$string['certif_config']='Configuration de l\'affichage de l\'onglet "Certificat"';
$string['config_certif_config']='Ce paramètre commande l\'accès des enseignants au paramétrage de l\'onglet "Certificat"...
<ul>
<li>Si <i>referentiel_certif_config</i> vaut <b><i>0</i></b> les enseignants avec droit d\'édition peuvent activer ou désactiver librement l\'affichage de l\'onglet "Certificat", que ce soit au niveau de l\'occurrence ou des instances d\'un référentiel ;</li>
<li>Si <i>referentiel_certif_config</i> vaut <b><i>1</i></b> les enseignants ne peuvent pas modifier l\'état de l\'affichage de l\'onglet "Certificat" pour l\'occurrence de référentiel, mais ils peuvent le faire pour l\'instance du cours ;</li>
<li>Si <i>referentiel_certif_config</i> vaut <b><i>2</i></b> cet état est verrouillé au niveau du site et donc n\'est pas modifiable par les enseignants.
</li>
</ul>';

$string['certif_state']='Etat de l\'onglet "Certificat"';
$string['config_certif_state']='En désactivant de l\'affichage de l\'onglet "Certificat" vous masquez aux étudiants et aux enseignants toutes les données de certification et de protocole...
<ul>
<li>Si <i>referentiel_certif_state</i> vaut <b><i>0</i></b> l\'affichage de l\'onglet "Certificat" est <b><i>désactivé</i></b> ;</li>
<li>Si <i>referentiel_certif_state</i> vaut <b><i>1</i></b>  l\'affichage de l\'onglet "Certificat" est <b><i>activé</i></b> ;</li>
</ul>
La valeur <i>0</i> n\'est légitime au niveau du site que si le module référentiel ne participe d\'aucune certification pour l\'ensemble du site.
Dans le cas contraire il vaut mieux laisser le choix de paramétrage au niveau de l\'occurrence ou de l\'instance';

$string['certif_activee']='Affichage de l\'onglet "Certificat" activé';
$string['certif_desactivee']='Affichage de l\'onglet "Certificat" désactivé';

$string['incorrect_userid']='Incorrect userid {$a}';
$string['incorrect_activity_id'] = 'Incorrect activity ID';
$string['incorrect_id'] = 'Incorrect ID {$a}';
$string['acces_interdit'] = 'Vous n\'avez pas accès à cette déclaration d\'activité.';

$string['unknowformat'] = 'Format de donné inconnu : {$a}';

$string['configuration_impression'] = 'Configuration impression';
$string['hierarchy'] = 'Affichage hiérarchique dans la saisie des compétences';
$string['input_setting'] = 'Mode d\'affichage';
$string['input_hierarchy'] = 'Affichage hiérarchique des compétences';
$string['config_input'] =  'En fonction de la valeur de <i>referentiel_hierarchy</i>
les enseignants avec droit d\'édition peuvent sélectionner un mode d\'affichage pour la saisie des compétences.

* Si <i>referentiel_hierarchy</i> vaut 0 ils peuvent sélectionner ce mode au niveau de l\'instance ;

* Si <i>referentiel_hierarchy</i> vaut 1 seul un gestionnaire ou le créateur du référentiel peuvent imposer ce mode au niveau de l\'occurrence du référentiel ;

* Si <i>referentiel_hierarchy</i> vaut 2, le choix du mode d\'affichage est verrouillé au niveau du site.';



$string['filtre_valide'] = 'Dossier de certification';
$string['confirmclore'] = 'Confirmer la fermeture du dossier de certification';
$string['confirmouvrir'] = 'Confirmer la réouverture du dossier de certification';

$string['c_ouvrir'] = 'Ouvrir le dossier';
$string['c_clore'] = 'Fermer le dossier';

$string['create_global_scale']= 'Créer un nouveau barème';
$string['editscale']= 'Editer ce barème';
$string['deletealldeclarations'] = 'Suppression de toutes les déclarations d\'activités et des tâches';
$string['scale_setting'] ='Utilisation des barèmes';
$string['delai_setting'] ='Deshérence';
$string['archive_setting'] ='Gestion des archives';
$string['light_setting'] ='Affichage léger du référentiel';
$string['light_setting'] ='Affichage léger du référentiel';
$string['ref_setting'] = 'Référentiel';
$string['scol_setting'] = 'Données de scolarité';
$string['profil_setting'] = 'Utilisation des profils pour les données étudiants';
$string['stat_setting'] = 'Statistiques';
$string['cert_setting'] = 'Certificats';

$string['confirmdelete']= 'Confirmez la suppression de l\'association de ce barème à cette occurrence de référentiel';
$string['liste_competence_cochees']= 'Compétences à vérifier';
$string['deletescale']= 'Supprimer ce barème';
$string['deletescaleh']= 'Suppression du barème';
$string['deletescaleh_help']= 'La suppression du barème supprime les évaluations associées aux compétences déclarées dans les activités. 
Autrement dit une fois le barème supprimé l\'évaluation des Items de compétence redevient purement binaire (0: non validé ; 1 : validé).

Cela n\'a donc pas de conséquence pour la certification.';
$string['use_scale']= 'Utiliser un barème';
$string['config_scale']= 'Si ce paramètre est activé le module Référentiel proposera d\'utiliser un barème pour l\'évaluation des Items de compétence dans les déclarations d\'activité.
Les barèmes de référentiel sont imités des barèmes standard au niveau du site et des barèmes personnalisés au niveau du cours.';

$string['scalelocalh']= 'Barèmes personnalisés';
$string['scalelocalh_help']= 'Un barème personnalisé est barème propre au cours où il est défini. C\'est une liste de valeurs séparées par une virgule utilisée 
pour évaluer des activités dans ce seul cours Moodle.

Vous pouvez associer un barème personnalisé à l\'évaluation des items de compétence d\'une occurrence de référentiel.

* Sélectionnez le barème dans la liste des barèmes personnalisés (Vous devrez peut-être en créer un d\'abord) ;

* Il sera utilisé dans la processus de d\'évaluation des déclarations d\'activité pour toutes les instances de cette occurence de référentiel...

N.B : Pour chaque barème de référentiel vous devrez définir un seuil qui permettra de valider les items de compétence.
Lorsqu\'une évaluation atteint le seuil indiqué, l\'item en question est considéré comme acquis pour la certification.';

$string['scaleglobalh']= 'Barèmes standards';
$string['scaleglobalh_help']= 'Un barème standard est une liste de valeurs séparées par une virgule proposée pour évaluer des activités Moodle dans tous les cours du serveur.

Vous pouvez associer un barème global à l\'évaluation des items de compétence d\'une occurrence de référentiel.

* Sélectionnez le barème dans la liste des barèmes standards (Vous devrez peut-être demander à un Gestionnaire de cours de le créer pour vous) ;

* Il sera utilisé dans la processus de d\'évaluation des déclarations d\'activité pour toutes les instances de cette occurence de référentiel...

N.B : Pour chaque barème de référentiel vous devrez définir un seuil qui permettra de valider les items de compétence.
Lorsqu\'une évaluation atteint le seuil indiqué, l\'item en question est considéré comme acquis pour la certification.';

$string['confirmexchange']= 'Confirmez le changement de barème';
$string['baremeechangeh']= 'Changement de barème';
$string['baremeechangeh_help']= 'Liste de barèmes utilisés dans l\'évaluation de référentiels de compétences.

ATTENTION : Un changement de barème de référentiel affecte toutes les évaluations d\'activité associées aux instances de ce référentiel :

* Les notes égales  à l\'ancien seuil reçoivent la valeur du nouveau seuil ;  
 
* Les autres notes reçoivent une valeur calculée par interpolation linéaire sur le segment [0, Seuil] ou sur [Seuil, Max]; 
';
$string['label_gris']= '<span style="color: grey;">•</span>';
$string['label_vert']= '<span style="color: green;">•</span>';
$string['label_rouge']= '<span style="color: red;">•</span>';
$string['scaleused']= 'Utilisé pour la notation d\'activités Moodle';
$string['aide_saisie_icones']= 'Attribuez un symbole et une couleur à chaque note';
$string['aide_saisie_seuil']= 'Indiquer l\'appréciation qui sert de seuil de validation';
$string['modif_bareme']= 'Modification du barème du référentiel';
$string['newbareme']= 'Barème de référentiel';
$string['newbaremes']= 'Barèmes de référentiels';
$string['newotherbaremes']= 'Barèmes utilisés avec d\'autres occurrences de référentiels';
$string['no_scale']= 'Aucun barème n\'est associé à cette occurrence de référentiel {$a}';
$string['no_scales']= 'Aucun barème n\'est défini pour aucune occurrence de référentiel sur ce serveur';
$string['no_custom_scale']= 'Il n\'y a pas de barème personnalisé dans ce cours';
$string['no_global_scale']= 'Aucun barème standard n\'est défini sur ce serveur.';
$string['create_a_scale']= 'Commencez à en définir un';
$string['bareme_utilise']= 'Barème utilisé avec cette occurrence de référentiel';
$string['scales']= 'Barèmes standards disponibles';
$string['choose_scale']= 'Choisissez un barème standard';
$string['baremeh']= 'Barèmes';
$string['baremeh_help']= 'Un barème est une liste de valeurs croissantes séparées par une virgule.
Vous pouvez associer un barème pour l\'évaluation des items de compétence, en le choisissant dans la liste des barèmes personnalisés du cours ou dans celle des barèmes standards.

* Choisissez un barème Moodle (ou commencez par le créer en passant par l\'interface des Notes) ;

* Précisez le seuil de validation des Items de compétences ;

* Attribuez un symbole de couleur à chaque graduation du barème ;

Désormais ce barème sera utilisé pour l\'évaluation des compétences déclarées dans les activités pour toutes les instances de cette occurrence de référentiel.
';

$string['consignes_associees']= 'Consignes associées';
$string['consigne_associee']= 'Consigne associée';
$string['competences_declare']= 'Compétences déclarées';
$string['occurrencereferentiel']='Référentiel de compétence (Occurrence)';
$string['areareferentiel']='référentiel';
$string['areadocument']='document';
$string['areaconsigne']='consigne';
$string['areaactivite']='activité';
$string['areatask']='tâche';
$string['areacertificat']='certificat';
$string['areapedagogie']='pédagogie';
$string['areaoutcomes']='objectifs';
$string['areaarchive']='archives';
$string['areascolarite']='scolarite';

$string['import_pedagogie_error']='ERREUR A L\'IMPORTATION<br />Certains utilisateurs semblent ne pas être inscrits sur votre serveur Moodle';  
$string['user_unknown']='Utilisateur inconnu : {$a}';

$string['no_data'] = 'Aucune donnée pour {$a}';
$string['regenere_profil'] = 'Actualiser les numéros d\'étudiants';
$string['deleted_student'] = 'Enregistrement étudiant supprimé';
$string['delete_student'] = 'Supprimer cet enregistrement';
$string['select_student'] = 'Sélectionner cet enregistrement';
$string['student'] = 'Enregistrement';
$string['cocher_enregistrer_students'] = 'Seuls les enregistrements cochés seront modifiés...';

$string['profil_non_modifiable'] = ' &nbsp; &nbsp; &nbsp; &nbsp; (Passer par le profil pour modifier le numéro d\'étudiant...)';
$string['ref_profilecategory'] = 'Catégorie du Profil utilisateur';
$string['config_ref_profilecategory'] = 'Si le Profil utilisateur est utilisé pour enregistrer le <b>numéro d\'étudiant</b>, indiquez la <i>catégorie</i> de celui-ci.';
$string['ref_profilefield'] = 'Nom abrégé du champ utilisé pour le numéro d\'étudiant';
$string['config_ref_profilefield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>le numéro d\'étudiant</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_ddnfield'] = 'Nom abrégé du champ utilisé pour la date de naissance';
$string['config_ref_ddnfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>la date de naissance de l\'étudiant</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_ldnfield'] = 'Nom abrégé du champ utilisé pour le lieu de naissance';
$string['config_ref_ldnfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>le lieu de naissance de l\'étudiant</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_adrfield'] = 'Nom abrégé du champ utilisé pour l\'adresse de l\'étudiant';
$string['config_ref_adrfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>l\'adresse de l\'étudiant</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_dptfield'] = 'Nom abrégé du champ utilisé pour le département de naissance';
$string['config_ref_dptfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>le département de naissance de l\'étudiant</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_etabfield'] = 'Nom abrégé du champ utilisé pour le nom d\'établissement';
$string['config_ref_etabfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>le nom d\'établissement</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';

$string['ref_numetabfield'] = 'Nom abrégé du champ utilisé pour le numéro d\'établissement';
$string['config_ref_numetabfield'] = 'Si le Profil utilisateur est utilisé pour enregistrer <b>le numéro d\'établissement</b>, indiquez le <i>nom abrégé (\'shortname\')</i> du champ utilisé.';


$string['etudianth'] = 'Etudiants';
$string['etudianth_help'] = 'Informations d\'état civil destinées aux certificats

* Numéro d\'étudiant :

Vous pouvez paramétrer le module Référentiel pour qu\'un champ du profil soit consacré au numéro d\'étudiant.
[Positionnez alors les variables de configuration \'ref_profilecategory\' and \'ref_profilefield\']

Sinon la valeur du \'idnumber\' de la table \'user\' de Moodle est utilisée. A défaut le champ \'username\' est retenu.

* Lieu et département de naissance ;

* Adresse postale ;

* Etablissement ;

* Les étudiants peuvent modifier leur propre fiche.';

$string['modifdomskillitemh'] = 'Modifier Domaines / Compétences / Items';
$string['modifdomskillitemh_help'] = 'Pour ajouter des compétences à un domaine augmentez le nombre de compétences qui lui sont associées.
<br />Pour ajouter des items à une compétence, augmentez le nombre d\'items associés à celle-ci...';


$string['deletedomainhelp'] = '(Toutes les compétences et Items associés seront aussi supprimés...)';
$string['deleteskillhelp'] = '(Tous les Items associés serint aussi supprimés...)';
$string['deleteitemhelp'] = '(Cet Item sera définitivement supprimé...)';

$string['delete_domain'] = 'Supprimer ce Domaine';
$string['delete_skill'] = 'Supprimer cette Compétence';
$string['delete_item'] = 'Supprimer cet Item';

$string['select_domain'] = 'Domaine sélectionné';
$string['select_skill'] = 'Compétence sélectionnée';
$string['select_item'] = 'Item sélectionné';

$string['new_domaine'] = 'Nouveau Domaine';
$string['new_competence'] = 'Nouvelle Competence';
$string['new_item'] = 'Nouvel Item';

$string['cocher_enregistrer_domain'] = 'Seuls les Domaines / Compétences / Items sélectionnés seront modifiés...';

$string['delaidesherence']='Délai de déshérence';
$string['config_delaidesherence']='Nombre de jours au delà duquel les déclarations d\'activité non évaluées sont signalées...';
$string['jours']='jours';
$string['joursdedelai']='Delai';
$string['avertissementjoursdedelai']='Délai de déshérence pour les déclarations d\'activité (en jours) ';
$string['activitesdesherance']='Liste des déclarations d\'activité non évaluées depuis plus de {$a} jours...';
$string['pasdereferent']='Aucun référent';
$string['pasdesuivi']='Aucun suivi';
$string['activitesdesheranceh']='Activités non évaluées...';
$string['activitesdesheranceh_help']='Cette page affiche les déclarations d\'activité qui n\'ont pas été évaluées par un référent depuis plus de N jours...';

$string['deleteall']= 'Réinitialiser';
$string['deleteallh']= 'Réinitialiser';
$string['deleteallh_help']= 'La table sera vidées avant d\'être rechargée avec les nouvelles valeurs';

$string['a_evaluer'] = 'A évaluer';
$string['missingtype'] = '"Type" manquant';
$string['missingdescription'] = '"Description" manquante';

// Référentiel V 8.02
$string['referentiel:addinstance'] = 'Ajouter une instance de Référentiel';
$string['competences_graphe'] = 'La ligne rouge indique le seuil de validation';
$string['poids_graphe'] = 'La ligne noire indique le poids relatif de chaque compétence';

// Référentiel V 8.01
$string['export_pedagos'] = 'Exporter aussi les données de formation / pédagogie';
$string['import_activity'] = 'Créer un activité  avec les compétences importées';
$string['imported_activity_type'] = 'Décision du jury';
$string['imported_activity_description'] = 'Compétences validées par décision du jury';
$string['imported_activity_comment'] = 'Cette déclaration a été créée de façon automatique lors de l\'importation des décisions du jury du {$a}';
$string['importcertificat'] = 'Importer des certificats';

$string['importcertif'] = 'Importer';
$string['importcertifh'] = 'Importer les décisions du jury';
$string['importcertifh_help'] = 'Les décisions du jury peuvent être injectées dans le dossier numérique.

Les données doivent comporter l\'identifiant de connexion (login) comme clé pour chaque utilisateur
sur le modèle du format "Condensé de type 2" (fichier CSV avec séparateur ";")

Il faut donc procéder en trois étapes :

* Commencer par exporter le certificat en format CSV "<b>condensé de type 2</b>"

* Ajouter les décisions du jury dans les champs ad hoc sous éditeur (un tableur).

* Réimporter les données modifiées.

<b>N.B.</b>:  Après importation les dossiers sont verrouillés et fermés.

De façon optionnelle l\'importation crée dans le dossier numérique de l\'élève une activité spéciale reprenant les compétences certifiées.';

$string['mark_to_select'] = 'Cocher pour sélectionner';
$string['dossier_verrouille_ferme'] = 'Ce dossier de certification est verrouillé et fermé';
$string['dossier_verrouille'] = 'Ce dossier de certification est verrouillé';
$string['dossier_non_verrouille_ferme'] = 'Ce dossier de certification est fermé';

$string['gerercertificat'] = 'Gérer la certification';
$string['gerercertificath'] = 'Gérer la certification';
$string['gerercertificath_help'] = 'Il y a deux façons de mettre fin au processus de certification :

* On peut "<i>Verrouiller les certificats</i>" :
Dès lors les déclarations d\'activité ne sont plus prises en compte. Cela permet en particulier de geler le dossier en attente d\'examen par le jury.

* On peut aussi "<i>Fermer le dossier numérique</i>" : le certificat n\'est alors plus modifiable
ni par les étudiants ni par les enseignants qui ne peuvent plus ajouter ou corriger ni les synthèses ni les commentaires.
Cela est recommandé une fois que les décisisons du jury ont été enregistrées.

N.B : Quand un dossier numérique est fermé, par défaut le certificat associé est verrouillé.
';


$string['dossier_fermer_all'] = 'Tout fermer';
$string['dossier_ouvrir_all'] = 'Tout ouvrir';
$string['dossier_fermeture'] = 'Fermer / Ouvrir les dossiers de certification';
$string['dossier_fermetureh'] = 'Dossier de certification';
$string['dossier_fermetureh_help'] = 'Quand un dossier est fermé, plus aucune intervention n\'est possible.

* Les certificats ne sont plus modifiables ;

* Les déclarations d\'activité sont verrouillées ;

Il est recommandé de fermer les dossiers de certification après la saisie des décisions du jury.';


$string['non_modifiable'] = 'Ce dossier de certification n\'est pas modifiable.';
$string['debloquer_dossier'] = 'Rouvrez le dossier de certification pour modifier ce champ.';
$string['valider_certificat'] = 'Dossier de certification';

$string['dossier_ouvert'] = 'Ouvert';
$string['dossier_ferme'] = 'Fermé';

// Moodle V 7.08
$string['maxdoc'] = 'Taille maximale des documents attachés';
$string['selected_certificates'] = '{$a} certificats sélectionnés';
$string['select_print_certificat'] = 'Sélectionnez les certificats à imprimer';

$string['purge_archives'] = 'Purge automatique des archives';
$string['config_archives'] = 'L\'administrateur peut choisir de supprimer ou de conserver les archives des dossiers numériques enregistrées
par les étudiants.

Si <i>referentiel_purge_archives</i> vaut 0 les archives sont conservées jusqu\'à leur supression par l\'administrateur du serveur ;

Si <i>referentiel_purge_archives</i> vaut 1 les archives de plus d\'une semaine sont suppriméees de façon automatique ;';

$string['archive_deleted'] = 'Les archives antérieures au {$a} ont été supprimées.';

$string['format_archiveh'] = 'Archivage des activités et certificats';
$string['format_archiveh_help'] = 'Sélection HTML pour obtenir une page Web
et XML pour obtenir un document à exporter vers un portfolio de type Mahara.';

$string['archiveh'] = 'Archiver un référentiel';
$string['archiveh_help'] = 'En archivant un référentiel vous produisez une sauvegarde de l\'ensemble
des déclarations d\'activité de tous les utilisateurs sélectionnés (avec fichiers attachés aux déclarations).

Les fichiers sont conservés une semaine sur le serveur Moodle puis supprimés.

Cette sauvegarde ne peut pas être restaurée dans un cours Moodle
mais selon le format choisi elle peut être affichée comme un site Web ou intégrée dans un portfolio.';


// V 7.05
$string['l_inconnu'] = 'INCONNU';

// Begin help strings for Moodle2
$string['modulename_help'] = '"referentiel" est un module Moodle destiné à implanter une activité de type certification
de compétences.

Ce module permet :

* de spécifier un référentiel de compétences (ou de le télécharger) ;

* de déclarer des activités et d\'associer celles-ci aux compétences du référentiel ;

* de gérer l\'accompagnement ;

* de définir des tâches (mission, consignes, liste de compétences mobilisées pour accomplir la tâche, documents attachés) ;

* d\'émettre des certificats basés sur le dit référentiel ;

* Si le site active les Objectifs, vous pouvez exporter le référentiel sous forme d\'une
liste d\'objectifs qui serviront alors à évaluer toute forme d\'activité
(forum, BD, devoir, etc.)
Ces notations sont récupérées dans le module référentiel sous forme de compétences
validées dans des déclaration d\'activité.';
$string['modulename_link'] = 'mod/referentiel/view';

$string['usedoutcomesh'] = 'Compétences et Objectifs';
$string['usedoutcomesh_help'] = 'Objectifs (outcomes) utilisés sur ce serveur pour évaluer des activités (forums, devoirs, bases de donnees, wiki, etc.)
Le module Référentiel récupère ces évaluations et génère des déclarations affichées la liste des activités du module referentiel.';

$string['link_export_datah'] = 'Format d\'exportation des données attachées aux activité';

$string['select_aff_graph'] = 'Interdire d\'afficher le graphique des certificats validés';
$string['config_aff_graph'] = 'En fonction de la valeur de <i>referentiel_affichage_graphique</i>
les enseignants avec droit d\'édition peuvent ou pas autoriser l\'affichage des graphiques de certification par les étudiants.

* Si <i>referentiel_affichage_graphique</i> vaut 0 les enseignants avec droit d\'édition peuvent autoriser cet affichage ;

* Si <i>referentiel_affichage_graphique</i> vaut 1 les enseignants ne peuvent pas autoriser cet affichage,
mais le créateur du cours peut modifier ce paramètre pour son cours ;

* Si <i>referentiel_affichage_graphique</i> vaut 2, l\'affichage est interdit et de plus ce paramètre est verrouillé au niveau du site et donc n\'est pas modifiable au niveau du cours.';


$string['syntheseh'] = 'Rédiger une synthèse';
$string['syntheseh_help'] = 'Pour clore son dossier de certification l\'étudiant peut rédiger une courte synthèse
qui pourra éventuellement venir en appui de son dossier lors de l\'examen de celui-ci par le jury de certification.

Il est recommandé dans ce cas d\'indiquer, en quelques lignes, les facteurs de contexte ayant favorisé / défavorisé
la mise en oeuvre des compétences.

Le candidat peut aussi à cette occasion préciser ce qui devrait être amélioré dans le processus de certification...';

$string['protocolereferentielh'] ='Protocole de certification';
$string['protocolereferentielh_help'] ='Pour chaque référentiel listez les contraintes
attachées aux Items, Compétences et Domaines
 <b>Seuil de certification</b> : C\'est le seuil numérique au delà du quel le certificat est acquis
sous condition que d\'autres règles ne viennent modifier ce critère.
 <b>Minimum de certification</b> : c\'est le minimum d\items pour être certifié
sous condition que d\'autres règles ne viennent modifier ce critère.
 Items (compétences (domaines)) <i>obligatoires</i></b> : désignez ceux indispensables pour l\'obtention du certificat.
 <b>Les seuils par compétence (et / ou par domaine)</b> : pour chaque compétence (domaine)
indiquez le seuil au delà duquel la compétence (le domaine) est retenu pour l\'obtention du certificat
 Le protocole peut être désactivé.';


$string['format_certificath'] = 'Archivage de compétences certifiées';
$string['format_certificath_help'] = 'Les compétences peuvent être exportées :
au <b>format normal</b> : liste des items validés.
au <b>format pourcentage</b> : les pourcentages d\'Items validés consolidés par Compétence et Domaine.';

$string['aide_incompatible_competencesh'] = 'Compétences incompatibles';
$string['aide_incompatible_competencesh_help'] = 'La liste des compétence du référentiel importé est incompatible avec la version du référentiel installée...';
$string['aide_incompatible_codeh'] = 'Code incompatible';
$string['aide_incompatible_codeh_help'] = 'Le code du référentiel importé est incompatible avec la version du référentiel installée...';
$string['aide_incompatible_cleh'] = 'Clé incompatible';
$string['aide_incompatible_cleh_help'] = 'La clé du référentiel importé est incompatible avec la version du référentiel installé...';


$string['importetudianth'] = 'Importer des données de scolarité';
$string['importetudianth_help'] = 'Les données de scolarité sont indispensables pour l\'Impression des certificats.

Afin  d\'Importer des données de scolarité dans le module Référentiel de Moodle
il faut commencer par procéder à un export au format CSV qui sera ensuite complété / modifié sous éditeur
(avec un tableur).

Les données de scolarité que l\'on importe dans Moodle (par l\'onglet Importer) doivent contenir l\'Identifiant attribué par Moodle à chaque utilisateur lors de la création des comptes dans Moodle.
1) Obtenir une sauvegarde contenant les identifiants Moodle des établissements et des étudiants.
Commencez par mettre à jour la liste des établissements du cours (onglet <strong>Etablissements</strong>)
Puis affichez la liste de tous les <strong>étudiants</strong> du cours (onglet <strong>Etudiants</strong>).
<b>N.B.</b> : Inutile de compléter les champs manquants, vous le ferez hors de Moodle sous éditeur.
Exportez ensuite (onglet <strong>Exporter</strong>) les données de scolarité de votre cours au format CSV.
2) Modifier cette sauvegarde avec un éditeur (un tableur). Tous les champs marqués "INCONNU"
peuvent être modifiés sous éditeur
<b>Attention</b> : Champs à ne modifier sous aucun prétexte :
<pre> \#id_etablissement  \#id_etudiant  user_id    login </pre>
N.B : Il suffit qu\'un des trois champs "#id_etudiant", "user_id" ou "login" soit correctement renseigné pour chaque étudiant.
3) Importer ce fichier de scolarité désormais à jour (onglet <strong>Importer</strong>, format CSV avec séparateur \';\' )
4) Exporter les certificats (onglet <strong>Certificat</strong>), soit au format PDF pour impression, soit au format CSV pour injection dans un logiciel de scolarité type Apogée.
';

$string['importtaskh'] = 'Importer des tâches';
$string['importtaskh_help'] = 'Il est possible  d\'Importer un fichier de tâches, soit dans le même cours que le fichier d\'origine (chaque tâche importée prend un nouveau numéro), soit dans un cours différent.
Ne peuvent être importées que des données enregistrées au format XML.

Plusieurs contraintes doivent être respectées :

Le référentiel associé à l\'Instance courante doit être identique au référentiel de la sauvegarde.
Autrement dit, si les clé sont non vides, elles doivent être identiques ; si elles sont vides les noms et codes doivent être identiques. Faute de quoi l\'Importation est refusée.

* Les tâches importées sont associées à celui qui effectue l\'Importation

* Les dates de début et de fin ne sont pas mises à jour

* Les liens URL associés aux consignes doivent être enregistrés avec une adresse absolue, sinon ils ne sont pas correctement récupérés.

* Les fichiers associés aux consignes ne sont pas chargés, car ils n\'appartiennent pas nécessairement au même cours.


Il est donc nécessaire de les redéposer après importation des tâches.
Aucune vérification quant au doublonnage de tâches n\'est effectuée, les tâches importées prenant un nouveau numéro dans la liste des tâches.
Les tâches importées sont masquées.';


$string['importreferentielh'] = 'Importer un référentiel';
$string['importreferentielh_help'] = 'Si vous disposez d\'une sauvegarde d\'une occurrence de référentiel
au format XML oou CSV (produite par la fonction symétrique Exporter)
vous pouvez charger celle-ci dans le cours Moodle.

Deux situations peuvent alors se produire :

* Soit c\'est un nouveau référentiel pour cet espace Moodle ; il devient disponible pour être associé à de nouvelles instances.

* Soit il existe déjà une version identique (même nom, même code) sur le serveur :

* Si vous avez choisi d\'écraser la version existante, elle est remplacée par la version importée ;

Si vous avez choisi de conserver la version existante l\'Importation est interrompue ;';

$string['importpedagoh'] = 'Importer un fichier des formations';
$string['importpedagoh_help'] = 'Fichier CSV ou XML.
Format CSV, Séparateur \';\'

\#Moodle Referentiel pedagos CSV Export;;latin1;Y:2011m:03d:17;;;;;;
\#username;firstname;lastname;date_cloture;promotion;formation;pedagogie;composante;num_groupe;commentaire;referentiel;
E001326S;Severine;DUPON;2011-06-01;2011;6252;FCI2EMD;919;;;C2i2e-2011
dupuis-d;David;DUPUIS;2011-06-01;2011;6252;FCI2EME;919;a123;;
...

Une fois les pédagogies créées ou importées il est possible d\'utiliser cette information pour sélectionner les certificats à exporter...
';

$string['exportpedagoh'] = 'Exporter le fichier des formations';
$string['exportpedagoh_help'] = 'Aide à rédiger';

$string['exportetudh'] = 'Exporter des données de scolarité';
$string['exportetudh_help'] =  'Les données de scolarité sont indispensables pour l\'Impression des certificats.

Afin de pouvoir par la suite importer des données de scolarité dans le module Référentiel de Moodle
il faut commencer par procéder à un export au format CSV qui sera ensuite complété / modifié sous éditeur
(avec un tableur).

En effet les données de scolarité que l\'on importe dans Moodle (par l\'onglet Importer) doivent contenir l\'Identifiant
attribué par Moodle à chaque utilisateur lors de la création des comptes dans Moodle.';

$string['exporttaskh'] = 'Exporter les tâches';
$string['exporttaskh_help'] = 'Aide à rédiger';

$string['etablissementh'] = 'Etablissements';
$string['etablissementh_help'] = '* Numéro, nom et adresse de l\'établissement;

Seuls les enseignants avec droit d\'édition peuvent modifier les fiches d\'établissement.';

$string['pedagoh'] = 'Formations / Pédagogies';
$string['pedagoh_help'] = 'Les administrateurs du site peuvent sélectionner les certificats
des étudiants régulièrement inscrits dans une filière de formation habilitée à délivrer
le certificat concerné.

La liste des formations peut être importée ou saisie.

l\'Importation permet aussi d\'associer directement les étudiants aux formations qui les concernent.';

$string['verroucertificath'] = 'Verrouiller les certificats';
$string['verroucertificath_help'] = 'Le certificat reflète l\'état instantané
des compétences validées dans les activités. Quand de nouvelles déclarations sont validées,
les compétences associées sont immédiatement prises en compte dans le certificat.

C\'est ce processus qu\'Il faut verrouiller lorsque qu\'une campagne de certification
touche à sa fin, afin de préparer le dossier de certification à présenter au jury.';

$string['printh']= 'Formats  d\'Impression';
$string['printh_help']= '* PDF (Adobe ®) fournit un document fac-simile.

* RTF (Rich Text File) est un format pivot pour les traitements de texte.

* OpenOffice (ODT) est le format natif du traitement de texte Writer d\'OpenOffice.

* MSWord DOC est le format propriétaire du traitement de texte Word de Microsoft (version 2000).

* CSV avec séparateur \';\' est un format destiné aux tableurs.

* XHTML s\'affiche dans une page Web.';

$string['printcertificath'] = 'Imprimer les certificats';
$string['printcertificath_help'] = '
* Commencez par sélectionner les données qui seront imprimées
en tenant compte du paramétrage imposé par l\'administrateur du site ou par le créateur du référentiel à imprimer.

* Puis sélectionnez le format  d\'Impression';

$string['exportcertificath'] = 'Exporter les certificats';
$string['exportcertificath_help'] = ' Cette fonction est destinée à fournir les résulats
de la certification aux systèmes de gestion de la scolarité.

Commencer par sélectionner les certificats à exporter

Puis choisir le format de fichier :

* Le format CSV avec séparateur \';\' est destiné aux tableurs tels MSExcel ou OpenOffice-Calc.

* Le format XML destiné à la sauvegarde en vue d\'une restauration ultérieure.

* Les format HTML et format XHTML s\'affiche comme une page Web.

Vous devez ensuite choisir la liste des données exportées

* Non condensé : toutes les données disponibles sot exportées

* Condensé type 1 : login, nom, prenom, pourcentages par domaine, compétence, item

* Condensé type 2 : login, nom, prénom, liste des compétences acquises

* Formation / pédagogie :  si disponibles ces informations sont ajoutées à l\'export.
';

$string['selectcertificath'] = 'Sélectionner les certificats';
$string['selectcertificath_help'] = 'Les enseignants avec droit d\'édition peuvent sélectionner
les certificats correspondant aux étudiants du cours, soit individuellement, soit par groupe, soit accompagnés...
Les administrateurs peuvent de plus exporter dans un seul fichier tous les certificats
du site pour un référentiel donné.

<b>Utilisation des "Promotions / Formations / Composantes / Pédagogies"</b>

Les administrateurs peuvent aussi sélectionner les certificats des étudiants régulièrement
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

Le module Référentiel récupèrera ces évaluations et génèrera des déclarations qui seront dès lors accessibles dans
la liste des activités du module référentiel.';

$string['exportactiviteh'] = 'Exporter les activités';
$string['exportactiviteh_help'] = 'Pour exporter les activités d\'une instance
du référentiel vous avez le choix entre plusieurs formats :

* Le format XML destiné à la sauvegarde en vue d\'une restauration ultérieure (cette fonction n\'est pas implantée actuellement)

* Le format CSV est un format textuel de données tableur avec séparareur \';\'
* Les formats XHTML et HTML s\'affichent comme une page Web.';

$string['uploadh'] = 'Documents';
$string['uploadh_help'] = 'Les documents attachés à une activité sont destinés à fournir
des traces observables de votre pratique.

A chaque activité vous pouvez associer un ou plusieurs documents, soit en recopiant son adresse Web (URL),
soit en déposant un fichier dans l\'espace Moodle du cours.';

$string['documenth'] = 'Document';
$string['documenth_help'] = 'Description du document : Une courte notice  d\'Information.
Type de document : Texte, Exe, PDF, ZIP, Image, Audio, Video, etc.
URL :
Adresse Web du document (qui peut être un document déposé par vos soins dans l\'espace Moodle).
Titre ou une étiquette
Fenêtre cible où s\'ouvrira le document
Fichier déposé depuis votre poste de travail.';

$string['configreferentielh'] = 'Configuration';
$string['configreferentielh_help'] = 'La configuration du module Référentiel concerne :
Les données de scolarité ;
La création, l\'Importation et la modification d\'un référentiel ;
La possibilité d\'associer un référentiel existant à une nouvelle instance de celui-ci ;
l\'Impression des certificats...


En fonction de votre rôle vous avez accès à différents niveaux de configuration.

<b>N.B.</b> Un paramétrage incohérent peut vous interdire de compléter la création d\'un nouveau référentiel ou de télécharger un référentiel existant.';

$string['configsiteh'] = 'Configuration au niveau du site';
$string['configsiteh_help'] = 'Au niveau du site Moodle la configuration incombe à l\'administrateur du serveur.
Un paramétrage au niveau du site s\'Impose à toutes les occurrences et toutes les instances de tous les référentiels du site.';
$string['configrefglobalh'] = 'Configuration au niveau du référentiel';
$string['configrefglobalh_help'] = 'Pourvu que l\'administrateur du serveur l\'y autorise,
tout enseignant avec droit d\'édition disposant du mot de passe ad hoc *
peut compléter la configuration au niveau du référentiel.

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

En cochant "Filtrer les occurrences sélectionnées" vous pouvez n\'afficher que les occurrences locales
(propres au cours où l\'Instance est crée) ou bien accéder aux occurrences globales
(définies pour tous les cours du serveur).';

$string['suppreferentielh'] = 'Supprimer le référentiel ';
$string['suppreferentielh_help'] = 'Avant de supprimer un référentiel commencez par supprimer toutes ses instances dans tous les cours où celles-ci sont déclarées.

<b>Attention</b> :
Ne pas confondre la suppression d\'une instance (une activité de type certification définie au niveau d\'un cours
et qui pointe vers une occurrence de référentiel)
avec la suppression d\'une occurrence de référentiel (au niveau du site) ;
La suppression d\'une occurrence de référentiel supprime aussi toutes les déclarations d\'activité, tâches et certificats associés à celle-ci ;
Une fois un référentiel supprimé celui-ci n\'est plus disponible au niveau du site, à moins d\'être importé à nouveau. ';

$string['modifreferentielh'] = 'Modifier le référentiel ';
$string['modifreferentielh_help'] = 'Vous ne pouvez modifer une occurrence de référentiel
que si vous en êtes l\'auteur initial ou si vous avez un rôle d\'administrateur du serveur.

Tout modification au niveau d\'une occurrence de référentiel
est propagée à toutes les instances de celui-ci.

En augmentant le nombre de domaines / compétences / items d\'une occurrence
vous pouvez lui ajouter autant de rubriques correspondantes.';

$string['certificath']= 'Certificats';
$string['certificath_help']= 'La certification est le processus par lequel les compétences sont déclarées, évaluées, validées.

Le certificat fournit une vue instantanée  de l’avancement de l\'étudiant dans le processus de certification.

Un certificat contient :

* Une liste de compétences validées pour le référentiel considéré ;

* La référence de l\'enseignant ayant validé le certificat ;

* Un état du certificat (Validé / Non validé) ;

* La date et la décison du jury quant à la certification ;

* Une synthèse éventuelle rédigée par l\'étudiant certifié et un commentaire du formateur référent ;

Un certificat verrouillé ne prend plus en compte les nouvelles déclarations de compétences.

La fermeture du dossier numérique interdit toute modification ultérieure du certificat.';

$string['certificat2h']= 'Validation d\'un certificat';
$string['certificat2h_help']= 'Quand un certificat est verrouillé (liste de compétences sur fond rose),
plus aucune compétence ne peut s’y ajouter, le dossier de l\'étudiant étant considéré comme clos.

Les item d\'empreinte nulle ne sont pas retenus dans la certification

Un Item de compétence n\'est pris en compte dans la validation du certificat
que si le nombre de déclarations validées pour cet item est supérieur à l\'empreinte de l\'Item.

Pour permettre d\'apprécier le chemin qui reste à faire pour obtenir une compétence ou un domaine de compétences,
des notes sont affichées par compétence et par domaine sous forme de pourcentages.

Au niveau de l\'Item :100% * NOMBRE_ITEM_VALIDE / ENPREINTE_ITEM

Au niveau de la compétence :100 * SOMME_SUR_ITEMS_COMPETENCE(NOMBRE_ITEM_VALIDE / POIDS_ITEM) / SOMME_SUR_ITEMS_COMPETENCE(POIDS_ITEM * ENPREINTE_ITEM)

Au niveau du domaine :100% * SOMME_SUR_ITEMS_DOMAINE(NOMBRE_ITEM_VALIDE / POIDS_ITEM) / SOMME_SUR_ITEMS_DOMAINE(POIDS_ITEM * ENPREINTE_ITEM)

Autrement dit la contribution d\'un Item à la note de la compétence et du domaine qui le contiennent
est proportionnelle au produit POIDS * EMPREINTE.
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

Pour que le référentiel soit opérationnel, il doit comporter au moins un domaine, une compérence, un item
';

$string['referentiel2h'] ='Domaine / Compétence / Item';
$string['referentiel2h_help'] ='
<pre>
  <b>Nom</b> [<em>obligatoir</em>] : C\'est l\'Identité du référentiel.
  <b>Code</b> [<em>obligatoire</em>] : C\'est une étiquette commode pour identifier
    le référentiel.
  <b>URL</b> [<em>facultatif</em>] : La référence sur le Web de l\'Initiateur
    du référentiel (par exemple le N° d\'un Bulletin officiel de l\'Education nationale
    française...).
  <b>Seuil de certification</b> [<em>valeur décimale ; facultatif</em>] :
    Si un barème et des notes sont associées à l\'obtention de chaque compétence,
    le seuil est la valeur au delà de laquelle l\'Impétrant est certifié.
  <b>Référentiel global</b> [<em>oui / non ; par défaut oui</em>] : Un référentiel
    global est accessible depuis d\'autres cours que celui où il a été créé.
  <b>Nombre de domaines à enregistrer pour ce référentiel</b> [<em>valeur
    numérique ; obligatoire</em>] : Le nombre de domaines à créer. 
    <em>On peut démarrer à un et augmenter par la suite cette valeur pour définir
    de nouveaux domaines.</em>
  <b>Liste des codes</b> [<em>généré ; obligatoire</em>] : les codes des items associés. 
    <em>Cette liste est calculée à partir des items de compétence.</em>
  <b>Liste des poids</b> [<em>généré ; obligatoire</em>] : les poids des items associés. 
    <em>Cette liste est calculée à partir des items de compétence.</em>
  <b>Liste des empreintes</b> [<em>généré ; obligatoire</em>] : les empreintes des items associés. 
    <em>Cette liste est calculée à partir des items de compétence.</em>
<pre>
';

$string['referentielinstanceh'] = 'Instance de certification';
$string['referentielinstanceh_help'] ='Une instance de certification est une activité au sens Moodle
(comme le sont "Devoir", "Forum" ou "Leçon") s\'appuyant sur une occurrence de référentiel et qui permet aux élèves / étudiants :
de déclarer des activités mobilisant des savoir-faire ou habiletés qui seront évaluées par les enseignants
de pointer les compétences en regard d\'une "occurrence de référentiel"';

$string['taskh']= 'Tâches';
$string['taskh_help']= 'Les tâches sont des réalisations ou des activités proposées par les enseignants aux étudiants de leur cours.

Seuls les enseignants peuvent créer des tâches
 Titre de la tâche : pour faciliter son repérage par les étudiants
Description : Indiquez ici le contexte et les objectifs de la tâche
Compétences mobilisées par cette tâche : Celles que l\'accomplissement de la tâche légitime
Consignes et critères de réussite afin de faciliter la réalisation de la tâche
Documents attachés

Vous pouvez joindre des documents pour illustrer la tâche ou la cadrer ';
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
$string['name_instanceh_help']= 'Le titre est celui affiché pour décrire cette instance de l\'activité Référentiel, par exemple "Dossier de certification C2i des étudiants de Lettre". Soyez précis dans le choix du titre car cela permettra aux utilisateurs (et à l\'administrateur) de distinguer les différentes instances de la même occurrence de référentiel';

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


// Aide
$string['rnotificationh']= 'Liste des référents notifiés';
$string['rnotificationh_help']= 'La notification consiste à faire envoyer par Moodle
un mèl d\'information reprenant l\'essentiel du contenu de la déclaration d\'activité.

Cet écran liste pour chaque compétence du référentiel qui sera destinataire des messages de notification pour l\'étudiant considéré';

$string['repartitionh']= 'Répartition du suivi des déclarations';
$string['repartitionh_help']= 'Les enseignants d\'un cours peuvent se répartir le suivi des déclarations en fonction des compétences du référentiel.
Selon les compétences déclarées dans les activités, la notification demandée par l\'étudiant sera adressée aux enseignants qui sont désignés dans la table de répartition par compétences.

Si parmi les référents affectés à l\'étudiant dans la table d\'accompagnement
il y en a qui sont aussi désignés dans table de suivi pour les compétences pointées
dans l\'activité eux seuls recevront la notification.

Sinon tous les référents affectés à l\'étudiant sont notifiés, et à défaut tous les enseignants du cours';

// End Help strings


$string['atranscript'] = 'Exporter vers Artefact ATranscript';

// Version 7.04 - 2012/06/15
$string['retour'] = 'Retour';
$string['archive_file'] = 'Moodle Referentiel Archive';

// Version 7.03 - 2012/06/02

$string['labels'] = 'Etiquettes';
$string['labels_help'] = 'Comment faut-il nommer chaque niveau hiérarchique de ce référentiel ?Laissez vide pour utiliser les termes par défaut <i>Domaine</i>, <i>Compétence</i>, <i>Item</i>...';
$string['labels_help2'] = 'Toute redéfinition au niveau de l\'instance prend le pas sur la définition au niveau de l\'occurrence.';

$string['domo']='Dom.';
$string['compo']='Comp.';
$string['itemo']='Item';
$string['item_obligatoires'] = $string['itemo'].' oblig. <i>P.</i> E.';
$string['competences_oblig_seuil'] = '['.$string['compo'].' obl.][Min/<i>Max</i>][Seuil/<i>S(P*E)</i>]';
$string['domaines_oblig_seuil'] =    '['.$string['domo'].' obl.][Min/<i>Max</i>][Seuil/<i>S(P*E)</i>]';


// Version 7.01 - 2012/05/22
$string['incorrect_activity_id'] = 'Activité non référencée.';
$string['incorrect_id'] = 'ID incorrect.';
$string['acces_interdit'] = 'Vous n\'avez pas accès à cette déclaration d\'activité.';


// Version 2019/01/05
$string['alphabet'] = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';

// Version 2012/03/06



// Version 2012/03/30
$string['aide_minima'] = 'Nombre minimal d\'items validés sous réserve de conditions supplémentaires imposées.';
$string['minima_certificat'] = 'Nombre minimal d\'items valides pour la certification';
$string['minima_referentiel'] = 'Minimum items valides';

$string['code_unique'] = 'Ce code doit être unique pour le référentiel';
$string['minima_competence'] = 'Nb. min. items valides';
$string['minima_domaine'] = 'Nb. min. compétences valides';
$string['oblig_domaine'] = 'Liste des domaines obligatoires';
$string['seuil_domaine'] = 'Liste des seuils pour les domaines';
$string['oblig_competence'] = 'Liste des competences obligatoires';
$string['seuil_competence'] = 'Liste des seuils pour les competences';
$string['oblig_item'] = 'Liste des items obligatoires';
$string['seuil_protocole'] = 'Protocole de certificationSeuil';

// Version 2012/03/02
$string['a_completer'] ='A compléter';
$string['c_domaine'] ='D';
$string['c_competence'] ='C';
$string['c_item'] ='I';

$string['activation_protocole'] ='Activation du protocole de certification';
$string['protocole_active'] ='Protocole de certification activé ';
$string['protocole_desactive'] ='Protocole de certification désactivé ';
$string['depuis'] =' depuis le {$a} ';
$string['aide_activation'] = 'Seuils et items / compétences / domaines obligatoires ne sont pris en compte que si le protocole est activé.';
$string['non_certifiable'] = 'Non certifiable';
$string['certifiable'] = 'Certifiable';

// Version 2012/02/12
$string['declarer_activite'] = 'Déclarer une activité et des compétences';
$string['type_domaine'] = 'Domaine obligatoire (Oui, Non)';
$string['seuil_domaine'] = 'Seuil du domaine (Nb. décimal)';
$string['type_competence'] = 'Compétence obligatoire (Oui, Non)';
$string['seuil_competence'] = 'Seuil de compétence (Nb. décimal)';
$string['bilan'] = 'Bilan de compétence';
$string['competences_valides'] = 'Compétences (1:validée 0:non validée)';
$string['validite'] = '<b>Nb</b> / Total (<i>Seuil</i>) Validité';
$string['validable'] = 'Validable';
$string['non_validable'] = 'Non validable';

$string['ask_pass'] = '(Mot de passe initial créé par {$a})';
$string['error_pass'] = 'Erreur de mot de passe '.$string['ask_pass'];

$string['definir_protocole'] = 'Protocole de certification';
$string['gestion_protocole'] = 'Gestion du protocole de certification';
$string['protocole'] = 'Protocole';
$string['item_obligatoires'] = 'Items oblig. <i>P.</i> E.';
$string['competences_oblig_seuil'] = '[Comp. obl.][Min/<i>Max</i>][Seuil/<i>S(P*E)</i>]';
$string['domaines_oblig_seuil'] =    '[Dom. obl.][Min/<i>Max</i>][Seuil/<i>S(P*E)</i>]';
$string['aide_protocole'] = 'Cochez les items / compétences / domaines obligatoires
et indiquez le seuil de certification pour les compétences et les domaines... <br />
(Valeurs nulles non prises en compte dans le protocole)';
$string['aide_protocole_completer'] = 'A REMPLACER...
[En jouant sur items / compétences / domaines obligatoires et les seuils de certification vous pouvez exprimer des contraintes concernant le protocole de certification...]';
$string['aide_seuil'] = 'Valeur totale minimale des items validés (fonction des poids et empreintes) sous réserve de conditions supplémentaires imposées.';


// Outcomes
$string['occurrence'] = 'Occurrence';
$string['module'] = 'Module';
$string['titre'] = 'Titre';
$string['module_activite'] = 'Module d\'activité';
$string['usedoutcomes'] = 'Objectifs du référentiel utilisés dans les activités du site';


// repartition des competences entre enseignants
$string['notification'] = 'Notification';
$string['repartition_notification'] = 'Répartition de la notification';
$string['identite_utilisateur'] = ' pour {$a}';
$string['repartition'] = 'Suivi';
$string['liste_repartition'] = 'Répartition des compétences entre référents';
$string['aide_repartition'] = 'Affectez à chaque référent la liste des compétences à évaluer';
$string['destinataires_notification'] = 'Destinataires de la notification ';


// Mahara artefact referentiel
// From assigment_mahara plugin
// Traduction JF - jean.fruitet@univ-nantes.fr
$string['exportmahara'] = 'Export to Mahara portfolio artefact';
$string['mahara'] = 'Mahara portfolio';
$string['clicktopreview'] = 'cliquez pour un aperçu pleine taille dans un fenêtre surgissante';
$string['clicktoselect'] = 'cliquez pour sélectionner la vue';
$string['nomaharahostsfound'] = 'Aucun hôte Mahara n\'a été trouvé.';
$string['noviewscreated'] = 'Vous n\'avez créé aucune vue dans {$a}.';
$string['noviewsfound'] = 'Aucune vue ne correspond dans {$a}';
$string['preview'] = 'Aperçu';
$string['site'] = 'Site';
$string['site_help'] = 'Ce paramètre vous permet de sélectionner depuis quel site Mahara vos étudiants pourront soumettre leurs pages. (Ce site Mahara doit être déjà configuré pour fonctionner en réseau MNET avec ce site Moodle.)';
$string['selectedview'] = 'Page soumissionnée';
$string['selectmaharaview'] = 'Sélectionnez dans cette liste l\'une des vues de votre portfolio <i>{$a->name}</i> ou <a href="{$a->jumpurl}">cliquez ici</a> pour créer directement une nouvelle vue sur <i>{$a->name}</i>.';
$string['title'] = 'Titre';
$string['typemahara'] = 'Portfolio Mahara';
$string['views'] = 'Vues ';
$string['viewsby'] = 'Vues proposées par {$a}';

// portfolio

$string['mustprovidereporttype'] = 'Type rapport non fourni';
$string['proposedbynameondate'] = 'Proposition de {$a->name}, {$a->date}';
$string['evaluatedbynameondate'] = 'Evalué par {$a->name}, {$a->date}';
$string['publishporttfolio'] = 'Publier';
$string['mustprovideattachmentorcertificat'] = 'Fichier ou certificat non fourni';
$string['mustprovideinstance'] = 'Instance de référentiel non fournie';
$string['invalidinstanceid'] = 'Instance de référentiel inexistante';
$string['invalidcertificat'] = 'Certificat inexistant';
$string['invalidoccurrence'] = 'Occurrence de référentiel inexistante';
$string['decisionnotfound'] = 'Le jury n\'a pris aucune décision concernant ce certificat à la date du {$a}.';
// 2011/10/25
$string['groupe'] = 'Groupe {$a}';

// moodle/admin/report/referentiel stuff
$string['formatarchive'] = 'Sélectionnez un format d\'archive';
$string['moodleversion'] = 'Votre version de Moodle : {$a}';
$string['deletefile'] = 'Supprimer';
$string['user'] = 'Utilisateur';
$string['size'] = 'Taille';
$string['totalsize'] = 'Taille totale : {$a}';
$string['nbfile'] = 'Nombre de fichiers : {$a}';
$string['archives'] = 'Archives';
$string['users_actifs'] = 'Actifs';
$string['activites_declarees'] = 'Déclarations';
$string['instancedeleted'] = 'Instance supprimée';
$string['suppression_instance_impossible'] = 'La suppression de cette instance a échoué.';
$string['supprimer_instance'] = 'Supprimer cette instance';
$string['nonexist'] = 'Instance inexistante dans ce cours';
$string['instances'] = 'Instances';
$string['occurrences'] = 'Occurrences';
$string['occurrencedeleted'] = 'Occurrence supprimée';
$string['instancenondefinie'] = 'Aucune instance n\'est définie pour ce référentiel';
$string['adminreport'] = 'Administration du module Référentiel';
$string['majmoodlesvp'] = 'Cette version de Moodle est trop ancienne. Faites la mise à jour SVP ({$a} requis)';

// moodle/admin/report/referentiel stuff
$string['uploadproblemfilesize'] = 'Fichier trop volumineux';
$string['maxsize'] = 'Taille maximale des documents attachés : <b>{$a}</b>';
$string['export_file_number'] = '{$a} documents attachés' ;
$string['export_file_size'] = ' totalisant <b>{$a}</b>';

$string['export_documents'] = 'Exporter les documents attachés ';
$string['export_url'] = ' sous forme de liens';
$string['export_data'] = ' sous forme de fichiers';

$string['selectarchive'] = 'Sélectionner des dossiers numériques à archiver';
$string['not_deleted'] = ' non supprimée ';
$string['format_pourcentage'] = 'Exporter les pourcentages par domaine ';
$string['liste_poids_competence'] = 'Liste des poids';
$string['generatedby'] = 'Archive générée par le module Référentiel de Moodle';
$string['archivextra'] = 'L\'archive est placée dans le dossier du serveur {$a}';
$string['archivefilename'] = 'referentiel/archive';
$string['archivetemp'] = 'temp/archive';
$string['archive'] = 'Archiver';
$string['archivereferentiel'] = 'Archiver le référentiel, les certificats et les activités associées';
$string['archivingreferentiels'] = 'Le référentiel, les certificats et les activités sont exportées vers un fichier';

// help

$string['light'] = 'Affichage léger du référentiel, sans poids ni empreintes';
$string['light_display'] = 'Affichage léger du référentiel';
$string['config_light'] = 'En fonction de la valeur de <i>referentiel_light_display</i>
les enseignants avec droit d\'édition peuvent ou pas autoriser l\'affichage des poids et empreintes du référentiel.

Si <i>referentiel_light_display</i> vaut 0 les enseignants avec droit d\'édition peuvent sélectionner le mode léger;
Si <i>referentiel_light_display</i> vaut 1 les enseignants ne peuvent sélectionner le mode léger,
mais le créateur du cours peut modifier ce paramètre pour son cours ;
Si <i>referentiel_light_display</i> vaut 2, l\'affichage léger est interdit et de plus ce paramètre est verrouillé au niveau du site et donc n\'est pas modifiable au niveau du cours.';



// Version 5.6.02
$string['date_proposition']= 'Date de la proposition';
$string['confirmdeletepedagos']= 'Confirmer la suppression des formations enregistrées';
$string['vider_pedagos']= 'Supprimer toutes les formations et pédagogies associées';
$string['precedent']= 'Page précédente';
$string['suivant']= 'Page suivante';

$string['exportedoutcomes']= 'Fichiers d\'objectifs exportés';
$string['exportedtasks']= 'Fichiers de tâches exportés';
$string['exportedpedago']= 'Fichiers de formations exportés';
$string['exportedetud']= 'Fichiers de scolarité exportés';
$string['exportedreferentiel']= 'Fichiers de référentiels exportés';
$string['exportedactivities']= 'Fichiers d\'activités exportés';
$string['exportedcertificates']= 'Fichiers de certificats exportés';
$string['printedcertificates']= 'Fichiers  d\'Impression de certificats';

$string['filename']= 'Fichier';
$string['timecreated']= 'Créé le';
$string['timemodified']= 'Modifié le';
$string['mimetype']= 'MIME';
$string['filesize']= 'Taille (octets)';

$string['nondefini']= 'Indéfini';
$string['forcer_regeneration']= 'Forcer la regénération des certificats';
$string['firstname'] = 'Prénom';
$string['lastname'] = 'Patronyme';
$string['certiftraite'] = ' certificats traités...';

$string['onlyteachersimport']= 'Seuls les enseignants peuvent importer';
$string['creer_nouveau_referentiel'] = 'Créer un nouveau référentiel';
$string['submissions'] = 'Déclarations soumises';
$string['availabledate'] = 'Date de création';

$string['maximumsize'] = 'Taille maximale';
$string['invalidid'] = 'ID invalide';
$string['invalidreferentiel'] = 'Référentiel invalide';
$string['pluginname'] = 'Référentiel';
$string['pluginadministration'] = 'Administration référentiel';
$string['name_instance_obligatoire'] = 'Le nom de l\'Instance est indispensable';

// Version 5.5.8
$string['verroucertificat'] = 'Verrouiller / Déverrouiller les certificats';
$string['verrou_all'] = 'Tout verrouiller';
$string['deverrou_all'] = 'Tout déverrouiller';
$string['verroucertif'] = 'Verrous';
$string['selectverroucertificat'] = 'Sélectionner les certificats à verrouiller / déverrouiller ou à fermer / ouvrir';

// Version 5.5.6

$string['incompatible_cle'] = 'Les clés des référentiels sont incompatibles';
$string['incompatible_code'] = 'Les codes des référentiels sont incompatibles';
$string['incompatible_competences'] = 'Les listes de compétences des référentiels sont incompatibles';
$string['aide_incompatible_cle'] = 'Supprimez le champ clé dans le fichier importé';
$string['aide_incompatible_code'] = 'Remplacez le champ code_referentiel dans le fichier importé par celui dans le référentiel installé';
$string['aide_incompatible_competences'] = 'Vérifiez que les codes des compétences sont strictement identiques à ceux du référentiel installé.';

// Version 5.5.5
$string['selectcertificatprint'] = 'Sélectionner les certificats à imprimer';
$string['pedagos'] = 'Formations';
$string['pedago'] = 'Formation';
$string['selectcertificat'] = 'Sélectionner les certificats à exporter';
$string['cocher'] = 'Cocher';
$string['exportcertifpedagos'] = 'Sélectionner les formations / pédagogies à exporter';
$string['nbcertifs'] = 'Nb. certificats';
$string['aide_selection_pedago'] = 'Filtrez les formations puis cochez celles dont les certificats seront exportés. Attention : certaines combinaisons peuvent être incompatibles !';
$string['aide_association'] = 'Associez sa formation à chaque étudiant';
$string['pedagogie'] = 'Pédagogie';
$string['listpedago'] = 'Formations';
$string['creer_pedago'] = 'Enregistrer une nouvelle formation / pédagogie';
$string['addpedago'] = 'Créer';
$string['listasso'] = 'Associations';
$string['formation'] = 'Formation';
$string['exportpedago'] = 'Exporter';
$string['exportingpedagos'] = 'Formations / pédagogies exportées dans un fichier';
$string['exportpedagos'] = 'Sélectionner des formations';
$string['exportpedagogies'] = 'Exporter des données de formation / pédagogie';
$string['importpedagogies'] = 'Importer les données de formation / pédagogie';
$string['importpedagos'] = 'Importer les données de formation / pédagogie';
$string['importpedago'] = 'Importer';
$string['composante'] = 'Composante';
$string['promotion'] = 'Promotion';
$string['editpedago'] = 'Modifier';
$string['editasso'] = 'Associer';
$string['num_groupe'] = 'Groupe / discipline';
$string['date_cloture'] = 'Clôture des dossiers';
$string['modifier_pedago'] = 'Modifier la fiche de formation {$a}';
$string['nopedago'] = 'Aucune formation / pédagogie définie';

// Version 5.4.13
$string['statcertif'] = 'Graphique';
$string['certification_etat'] = 'Etat des validations';
$string['code_items'] = 'Items de Compétences';
$string['poids'] = 'Poids';
$string['moyenne'] = 'Moyenne';
$string['seuil'] = 'Seuil';
$string['graph'] = 'Interdire d\'afficher le graphique des certificats validés';

// version 5.4.10
$string['format_reduit1'] = ' Type 1 ';
$string['format_reduit2'] = ' Type 2 ';
$string['not_activite_tache_1'] = '<i>Les étudiants affectés à cette tâche recevront cette notification !</i>';
$string['not_activite_tache_2'] = '<i>Les référents affectés à cette tâche recevront cette notification !</i>';
$string['not_activite_tache_3'] = '<i>Tous les étudiants et les référents affectés à cette tâche recevront cette notification !</i>';

// version 5.4.8
$string['not_tache_1'] = '<i>Tous les étudiants du cours recevront cette notification !</i>';
$string['not_tache_2'] = '<i>Tous les enseignants du cours recevront cette notification !</i>';
$string['not_tache_3'] = '<i>Tous les étudiants et enseignants du cours recevront cette notification !</i>';


$string['referentiels'] = 'Référentiels';

// version 5.4.7
$string['login'] = 'Login';
$string['pdf'] = 'Format PDF';
$string['rtf'] = 'Format RTF';
$string['msword'] = 'Format MSWord';
$string['ooffice'] = 'Format OpenOffice';
// version 5.4.6
$string['ressources_associees'] = '{$a} ressources associées à cette activité';
$string['ressource_associee'] = '{$a} ressource associée à cette activité';
$string['notification_commentaire'] = 'Notifier par courriel ce commentaire';
$string['savedoc'] = 'Modifier';
$string['num'] = 'N° ';
// version 5.4.3
$string['release'] = 'N° ';
$string['modifier_instance_referentiel'] = 'Modifier l\'Instance <i>{$a}</i> du référentiel ';
$string['referentiel_config_local_interdite'] = '<b>Avertissement</b>
Les paramètres de configuration au niveau de l\'Instance ont été désactivés pour ce référentiel';

$string['aide_referentiel_config_local_impression'] = '<b>Avertissement</b>
<i>Les paramètres  d\'Impression déterminent la liste des informations qui sont imprimées sur le certificat.</i>';

$string['referentiel_config_impression_local_interdite'] = '<b>Avertissement</b>
Les paramètres  d\'Impression au niveau de l\'Instance ont été désactivés pour ce référentiel';
$string['pass_referentiel_admin'] = 'Saisir le mot de passe qui protègera ce référentiel';
$string['configref']= 'Configuration';
$string['referentiel_config_global_impression'] = 'Configuration  d\'Impression des certificats ';
$string['configurer_referentiel'] = 'Configuration du référentiel <i>{$a}</i> au niveau global ';
$string['referentiel_config_global'] = 'Configuration du référentiel ';
$string['aide_referentiel_config_global'] = 'Les paramètres de configuration et  d\'Impression au niveau du référentiel
dépendent d\'abord des paramètres définis au niveau du site par l\'administrateur du serveur Moodle.
Vous pouvez ensuite définir ces paramètres au niveau global du référentiel.
Enfin vous pouvez aussi définir ces paramètres au niveau de l\'Instance elle même.
Un paramétrage incohérent peut vous interdire de compléter la création d\'un nouveau référentiel ou de télécharger un référentiel existant.';

// version 5.3.7
$string['aide_activite']= 'Sélectionnez les personnes dont la tâche doit être validée';
$string['consignes']= 'Consignes';
// version 5.3.5
$string['ajouter_domaines'] = 'Augmentez cette valeur pour ajouter de nouveaux domaines à ce référentiel...';
$string['ajouter_competences'] = 'Augmentez cette valeur pour ajouter de nouvelles compétences à ce domaine...';
$string['ajouter_items'] = 'Augmentez cette valeur pour ajouter de nouveaux items à cette compétence...';
$string['importfileupload_xml_simple'] = 'Importer à partir d\'un fichier à déposer  au format XML simplifié (extension \'_sxml.xml\')...';
$string['importfilearea_xml_simple']= 'Importer à partir d\'un fichier du cours au format XML simplifié (extension \'_sxml.xml\')...';
$string['assigner'] = 'Assigner une tâche';
// version 5.3.4
$string['uploadproblemfile'] = 'Erreur au chargement du fichier';
$string['formatincompatible'] = 'Format de fichier incompatible avec votre sélection';
$string['editer_referentiel_xml'] = 'Editer un référentiel';
$string['charger_referentiel_xml'] = 'Charger un référentiel au format XML simplifié';
$string['editeur_referentiel'] = 'Ouvrir l\'éditeur XML simplifié';
$string['import_referentiel_xml'] = 'Import XML simplifié';
// version 5.3.3
$string['aide_referentiel_config_local'] = '<b>Avertissement</b>
<i>Les paramètres de configuration et  d\'Impression au niveau de l\'Instance
ne sont pris en compte qu\'après ceux définis au niveau du site par l\'administrateur du serveur Moodle
puis au niveau du référentiel par le créateur de celui-ci.
Un paramétrage incohérent peut vous interdire de compléter la création d\'un nouveau référentiel ou de télécharger un référentiel existant.</i>';
$string['not_verrou'] = 'Non verrouillé';
$string['f_auteur'] = 'Auteur';
$string['f_verrou'] = 'Verrouillé';
$string['f_date_decision'] = 'Date de proposition';

$string['decision_favorable'] = 'A Recevoir';
$string['decision_defavorable'] = 'A ajourner';
$string['decision_differee'] = 'En attente';
$string['rediger_decision'] = 'Rédiger la proposition au jury';
$string['saisie_synthese_certificat'] = 'Rédiger une synthèse';
$string['synthese_certificat'] = 'Synthèse';
$string['synthese'] = 'Synthèse';
$string['aide_synthese_certificat'] = 'La synthèse est un écrit court présentant le contexte dans lequel s\'est effectuée la certification.';
// version 5.3.2
$string['format_condense'] = 'Exporter au format condensé ';
// version 5.3.1
$string['activites_tache_delete'] = 'Cocher / décocher les activités à supprimer...';
$string['delete_all_activity_task_closed'] = 'Cette tâche est close. Supprimer en bloc toutes les activités qui lui sont associées ? ';
$string['delete_all_activities_task'] = 'Supprimer en bloc la tâche {$a} et les activités associées';
$string['delete_all_task_associations'] = 'Supprimer la tâche et ses activités associées';
$string['confirmdeleterecord_all_activities'] = 'Confirmer la suppression de la tâche {$a} <b>et de toutes les activités qui lui sont associées...</b>';

// version 5.2.5
$string['ref_course'] = 'Cours ID';
$string['teacherid'] = 'Enseignant ID';
$string['description_document'] = 'Description';
$string['ref_activite'] = 'Activité ID';


// version 5.2.4
$string['exportallcertificates'] = 'Sélectionner tous les certificats ? ';
// version 5.2.3
$string['date_modif_by'] = 'par le référent le ';
$string['date_modif_student_by'] = 'par l\'auteur le ';
// version 5.2.0
$string['select_acc'] = 'Etudiants accompagnés';
$string['select_all'] = 'Tout cocher';
$string['select_not_any'] = 'Tout décocher';
$string['REF'] = 'Référent';
$string['ACC'] = 'Accompagnateur';
$string['aide_accompagnement'] = 'Associez aux étudiants leurs enseignants référents';
$string['liste_accompagnement'] = 'Associations étudiants / enseignants référents';
$string['eleves'] = 'Etudiants';
$string['accompagnement'] = 'Accompagnement';
$string['listaccompagnement'] = 'Lister';
$string['manageaccompagnement'] = 'Modifier';
$string['addaccompagnement'] = 'Associer';
$string['addreferent'] = 'Ajouter un référent';
$string['notmatched'] = 'Aucun référent pour cet étudiant';
$string['noaccompagnement'] = 'Aucun accompagnement n\'est enregistré pour ce référentiel';
$string['referents'] = 'Référents';
$string['type_accompagnement'] = 'Type d\'accompagnement';

$string['tache_masquee_num'] = 'Tâche masquée numéro{$a}';
$string['tache_masquee'] = 'Tâche masquée';
$string['taskiscurrentlyhidden'] = 'Tâche masquée';
$string['un_enseignant'] = 'un enseignant du cours';

// version 5.1.2
$string['referentiel:addactivity']='Ajouter une activité';
$string['referentiel:exportcertif']='Exporter tous les certificats';
$string['referentiel:addtask']='Ajouter une tâche';
$string['referentiel:approve'] = 'Approuver une activité';
$string['referentiel:comment'] = 'Commenter une activité';
$string['referentiel:export'] = 'Exporter un référentiel';
$string['referentiel:import'] = 'Importer un référentiel';
$string['referentiel:managecertif'] = 'Gérer les certificats';
$string['referentiel:managecomments'] = 'Gérer les commentaires';
$string['referentiel:managescolarite'] = 'Gérer la scolarité';
$string['referentiel:rate'] = 'Noter';
$string['referentiel:select'] = 'Sélectionner';
$string['referentiel:selecttask'] = 'Sélectionner une tâche';
$string['referentiel:view'] = 'Afficher le référentiel';
$string['referentiel:viewrate'] = 'Afficher les appréciations';
$string['referentiel:viewscolarite'] = 'Voir les données de scolarité';
$string['referentiel:viewtask'] = 'Voir les tâches';
$string['referentiel:write'] = 'Modifier des activités';
$string['referentiel:writereferentiel'] = 'Modifier le référentiel';
$string['referentiel:archive'] = 'Exporter une archive (certificats et activités)';

// version 5.1.1
$string['subscribed_task'] = 'Tâche souscrite';
// version 5.1.0
$string['activer_outcomes'] = 'Pour exporter le référentiel sous forme de barème d\'objectifs, faites d\'abord activer les Objectifs sur ce site
par l\'administrateur.';
$string['help_outcomes'] = 'L\'évaluation par objectifs (compétences, buts, standards ou critères)
permet d\'évaluer les résultats de n\'Importe quelle activité (forum, devoir, etc.)
d\'après le barème du référentiel utilisé.';
$string['scale'] = 'Barème';
$string['outcome'] = 'Objectif';
$string['outcomes'] = 'Objectifs';
$string['export_bareme'] = 'Barème et Objectifs';
$string['export_outcomes'] = 'Exporter le référentiel sous forme de barème et d\'objectifs';
$string['outcome_type'] = 'Activité';
$string['outcome_date'] = 'Date : ';
$string['outcome_description'] = '{$a->name} : {$a->description} ';
$string['description_inconnue'] = 'Aucune description';
$string['nom_bareme'] = 'Item référentiel';
$string['bareme'] = 'Non pertinent,Non validé,Validé';
$string['description_bareme'] = 'Ce barème est destiné à évaluer l\'acquisition des compétences du module référentiel.';
// version 4.4.1
$string['choix_web_link'] = 'Vous pouvez soit coller un lien Internet';
$string['choiximportfilearea'] = 'Soit importer à partir d\'un fichier du cours...';
$string['choiximportfileupload'] = 'Soit déposer un fichier ';
$string['processingdigest'] = 'Traitement du courriel quotidien de l\'utilisateur {$a}';
$string['activites'] = 'Activités';
$string['notification_certificat'] = 'Notifier par courriel ce certificat';
$string['notification_tache'] = 'Notifier par courriel cette tâche';
$string['notification_activite'] = 'Notifier par courriel cette activité';
$string['digestmailheader'] = 'Ceci est le courriel quotidien des notifications du module référentiel de {$a->sitename}. Pour modifier les réglages de votre abonnement, veuillez aller sur {$a->userprefs}.';
$string['digestmailprefs'] = 'votre profil utilisateur';
$string['digestsentusers'] = 'Les notifications quotidiennes du module référentiel ont été envoyés correctement à {$a} utilisateurs.';
$string['digestmailsubject'] = 'Notification quotidienne du module référentiel de {$a}';
$string['postincontext'] = 'Voir ce message dans son contexte';
$string['postmailinfo'] = '{$a->type} du module référentiel sur le site {$a->site}. Pour consulter, cliquer sur ce lien ';
$string['bynameondate'] = 'par {$a->name}, {$a->date}';
$string['valide_empreinte'] = 'valide [/empreinte]';
$string['competence_certifiee'] = 'Compétences certifiées (0:Non 1:oui)';
// version 4.2.3
$string['cocher_enregistrer'] = 'Seules les rubriques cochées seront réenregistrées...';
$string['activites_tache'] = 'Cocher / décocher les activités à valider...';
// version 4.2.2
$string['aide_souscription'] = 'Sélectionnez les personnes à associer à cette tâche.';
$string['cle_souscription'] = 'Clé  de souscription';
$string['mode_souscription'] = 'Modalités de souscription';
$string['souscription_forcee'] = 'Imposer la souscription';
$string['aide_souscription_forcee'] = '<i>Associer cette tâche à un public désigné.</i>';
$string['aide_souscription_cle'] = '<i>Pour restreindre la souscription, saisissez une clé à ne diffuser qu\'auprès du public concerné.</i>';
$string['obtenir_cle_souscription'] = ' <i>Adressez-vous à </i> {$a} <i> pour obtenir la clé...</i>';
$string['souscription'] = 'Souscription';
$string['souscription_libre'] = 'Souscription libre';
$string['souscription_restreinte'] = 'Souscription avec clé';
$string['libre'] = 'Libre';
$string['avec_cle'] = 'Avec clé';
$string['cle'] = 'Clé';

// version 4.2.1
$string['selection_champs_etudiant'] = 'Sélectionnez les champs concernant l\'étudiant';
$string['items'] = 'Items';
$string['certificat_sauver_parametre'] = 'Enregistrer les paramètres';
$string['config_sauver_parametre'] = 'Enregistrer ces paramètres pour la prochaine impression ';
$string['detail_referentiel'] = 'Référentiel détaillé';

$string['pourcentage'] = 'Pourcentages';
$string['referentiel_config_local_impression'] = 'Configuration  d\'Impression des certificats';
$string['refcert'] = 'Inclure le référentiel en entête du certificat.';
$string['instcert'] = 'Inclure l\'Instance du référentiel en entête du certificat.';
$string['numetu'] = 'Inclure le numéro de l\'étudiant.';
$string['nometu'] = ' &nbsp; &nbsp; &nbsp; le nom et le prénom de l\'étudiant.';
$string['etabetu'] = ' &nbsp; &nbsp; &nbsp; le nom de l\'établissement.';
$string['ddnetu'] = ' &nbsp; &nbsp; &nbsp; la date de naissance de l\'étudiant.';
$string['lieuetu'] = ' &nbsp; &nbsp; &nbsp; le lieu de naissance de l\'étudiant.';
$string['adretu'] = ' &nbsp; &nbsp; &nbsp; l\'adresse de l\'étudiant.';
$string['detail'] = 'Inclure dans le certificat l\'énoncé détaillé des compétences.';
$string['pourcent'] = ' &nbsp; &nbsp; &nbsp; les pourcentages des résultats consolidés par compétences et domaines.';
$string['compdec'] = ' &nbsp; &nbsp; &nbsp; la liste des compétences déclarées dans les activités.';
$string['compval'] = ' &nbsp; &nbsp; &nbsp; la liste des compétences certifiées.';
$string['nomreferent'] = ' &nbsp; &nbsp; &nbsp; le nom et le prénom du référent.';
$string['jurycert'] = ' &nbsp; &nbsp; &nbsp; la proposition au jury.';
$string['comcert'] = ' &nbsp; &nbsp; &nbsp; les commentaires des référents.';

$string['selection_champs_certificat'] = 'Sélectionnez les champs du certificat à imprimer';
$string['certificat_sel_print_format'] = 'Sélectionnez le format  d\'Impression';
$string['certificat_sel_referentiel'] = 'Description du référentiel';
$string['certificat_sel_referentiel_instance'] = 'Intance du référentiel';
$string['certificat_sel_etudiant'] = 'Données pour l\'étudiant';
$string['certificat_sel_etudiant_numero'] = 'Numéro d\'étudiant';
$string['certificat_sel_etudiant_nom_prenom'] = 'Nom, Prénom de l\'étudiant';
$string['certificat_sel_etudiant_etablissement'] = 'Etablissement de l\'étudiant';
$string['certificat_sel_etudiant_ddn'] = 'Date de naissance de l\'étudiant';
$string['certificat_sel_etudiant_lieu_naissance'] = 'Lieu de naissance de l\'étudiant';
$string['certificat_sel_certificat'] = 'Données pour le certificat';
$string['certificat_sel_certificat_detail'] = 'Détail des données pour le certificat';
$string['certificat_sel_certificat_pourcent'] = 'Résultats en pourcentages';
$string['certificat_sel_activite_competences'] = 'Compétences déclarées';
$string['certificat_sel_certificat_competences'] = 'Compétences certifiées';
$string['certificat_sel_certificat_referents'] = 'Nom, prénom du référent';
$string['certificat_sel_decision_jury'] = 'Proposition au jury';
$string['certificat_sel_commentaire'] = 'Commentaires et synthèses';
$string['certificat_sel_etudiant_adresse'] = 'Adresse de l\'étudiant';

$string['config_certificat_sel_referentiel'] = 'Inclure le référentiel en entête du certificat.';
$string['config_certificat_sel_referentiel_instance'] = 'Inclure l\'Instance du référentiel en entête du certificat.';

$string['config_certificat_sel_etudiant_nom_prenom'] = 'Inclure le nom et le prénom de l\'étudiant.';
$string['config_certificat_sel_etudiant_numero'] = 'Inclure le numéro d\'étudiant.';
$string['config_certificat_sel_etudiant_etablissement'] = 'Inclure le nom d\'établissement.';
$string['config_certificat_sel_etudiant_ddn'] = 'Inclure la date de naissance de l\'étudiant.';
$string['config_certificat_sel_etudiant_lieu_naissance'] = 'Inclure le lieu de naissance de l\'étudiant.';

$string['config_certificat_sel_certificat_detail'] = 'Inclure dans le certificat  l\'énoncé détaillé des compétences.';
$string['config_certificat_sel_certificat_pourcent'] = 'Inclure les pourcentages des résultats consolidés par compétences et domaines.';
$string['config_certificat_sel_activite_competences'] = 'Inclure la liste des compétences déclarées dans les activités.';
$string['config_certificat_sel_certificat_competences'] = 'Inclure la liste des compétences certifiées.';
$string['config_certificat_sel_certificat_referents'] = 'Inclure le nom et le prénom du référent.';
$string['config_certificat_sel_decision_jury'] = 'Inclure la proposition au jury.';
$string['config_certificat_sel_commentaire'] = 'Inclure les commentaires des référents et les synthèses des étudiants.';
$string['config_certificat_sel_etudiant_adresse'] = 'Inclure l\'adresse de l\'étudiant.';


// version 4.2.0

$string['cible_link'] = 'Ouvrir le lien dans une nouvelle fenêtre ';
$string['etiquette_document'] = 'Titre du document';
$string['etiquette_consigne'] = 'Titre de la consigne';
$string['creer_activite_teacher'] = 'Vous êtes sur le point de déclarer une activité pour vous-même en tant qu\'enseignant....';

// version 4.1.4
$string['competences_declarees'] = 'Compétences déclarées dans les activités ';
// version 4.1.2
$string['tous'] = 'Tous';
$string['nocommentaire'] = 'Pas de commentaire';
$string['suivi'] = 'Suivi';
$string['non_examine'] = 'Non';
$string['examine'] = 'Oui';
$string['croissant'] = 'Ordre croissant';
$string['decroissant'] = 'Ordre décroissant';
$string['competences_declarees'] = 'Compétences déclarées dans les activités par {$a}';
// version 4.0.1
$string['zero_activite'] = 'Pas d\'activité déclarée à la date du {$a} par ';
$string['menu'] = 'Menu';
$string['activite_exterieure'] = 'Autre cours...';
$string['id_activite'] = 'Activité <i>{$a}</i>';
$string['f_validation'] = 'Validation';
$string['f_date_modif'] = 'Date suivi';
$string['f_date_modif_student'] = 'Date auteur';
$string['listactivityall'] = 'Détails';
$string['listactivitysingle'] = 'Détails';
$string['date_modif'] = 'Modifié par le référent le ';
$string['date_modif_student'] = 'Modifié par l\'auteur le ';

// version 3.3.4
$string['evaluation_par'] = 'Evaluation par';
$string['web_link'] = 'Coller un lien Internet';

// version 3.2.5
$string['erreur_creation'] = 'Erreur de Création : donnés manquantes (Nom et code obligatoires...). Recommencez.';
$string['exporttask'] = 'Exporter les tâches ';
$string['exportingtasks'] = 'Les tâches sont exportées vers un fichier';
$string['notask'] = 'Aucune tâche n\'est enregistrée ';
$string['instance'] = 'Instance';
$string['import_task'] = 'Importer une tâche';
$string['export_task'] = 'Exporter une tâche';
$string['importtasks'] = 'Importer des tâches';
$string['exporttasks'] = 'Exporter des tâches';
$string['incompatible_task'] = 'Référentiel incompatible avec les tâches chargées...';

// version 3.2.4
$string['filtrer'] = 'Filtrer';
// version 3.2.3
$string['supprimer_referentiel'] = 'Supprimer le référentiel';
$string['suppression_non_autorisee'] = 'Vous n\'êtes pas autorisé à supprimer ce référentiel ';
$string['cours_externe'] = 'Dans un autre cours';
$string['cours_courant'] = 'Dans ce cours';
$string['deletereferentiel'] = 'Supprimer';
$string['instance_deleted'] = 'Instance supprimée';
$string['selection_instance_referentiel'] = 'Cocher d\'abord les instances à supprimer ';
$string['ressaisir_pass_referentiel'] = 'Ressaisir le mot de passe ';
$string['suppression_pass_referentiel'] = 'Supprimer le mot de passe ';
$string['suppression_referentiel_impossible'] = 'Il reste des instances du référentiel {$a} à supprimer...';
// version 3.1.4

$string['modifier_depot_document'] = 'Modifier le document attaché ';
$string['competences_bloquees'] = 'Liste de compétences bloquée ';
$string['modifier_depot_consigne'] = 'Modifier le documentde consignes attaché ';
// Version 3.1
$string['modifier_consigne'] = 'Modifier un document de consignes';
// $string['consigne_associee'] = 'Document de consignes associé à la tâche ';
$string['modulename-intance'] = 'Instance référentiel';
$string['version'] ='Version du module référentiel : ';

// Version 3.0
$string['aide_creer_referentiel'] = 'Vous devez maintenant associer cette instance à une <b>occurrence de référentiel</b> soit <i>présente</i> sur le serveur, soit à une <i>nouvelle</i> occurrence (à saisir ou à importer).';
$string['aide_cle_referentiel'] = 'La clé est un identifiant calculé à partir du code et du mail de l\'auteur du référentiel';
$string['cle_referentiel'] = 'Valeur de la clé';
$string['check_pass_referentiel'] = 'Saisissez le mot de passe qui protége ce référentiel.';
$string['existe_pass_referentiel'] = 'Laissez vide pour conserver le mot de passe actuel.';
$string['aide_pass_referentiel'] = 'Un mot de passe permet de protéger le référentiel contre toute modification indésirable.';
$string['pass_referentiel'] = 'Mot de passe';

$string['referentiel_config'] = 'Configuration des instances de référentiel';
$string['referentiel_impression_autorisee'] = 'Interdire l\'Impression des certificats';
$string['config_impression_referentiel'] = 'Ce paramètre régit l\'Impression des certificats au niveau du cours.

Si <i>referentiel_impression_autorisee</i> vaut 0, les enseignants avec droit d\'édition peuvent imprimer des certificats depuis le cours ;
Si <i>referentiel_impression_autorisee</i> vaut 1  les enseignants ne peuvent pas imprimer de certificats, mais le créateur du cours peut modifier ce paramètre pour son cours ;
Si <i>referentiel_impression_autorisee</i> vaut 2 les enseignants ne peuvent pas imprimer de certificats et de plus ce paramètre est verrouillé au niveau du site.
';

$string['select_referentiel'] = 'Interdire d\'associer un référentiel existant à une nouvelle instance de référentiel';
$string['config_select_referentiel'] = 'En fonction de la valeur de <i>referentiel_selection_autorisee</i>
les enseignants avec droit d\'édition peuvent ou pas associer un référentiel existant (dont ils connaissent le mot de passe) à une instance de référentiel.

Si <i>referentiel_selection_autorisee</i> vaut 0 les enseignants avec droit d\'édition peuvent, après avoir créé une nouvelle instance, lui associer un référentiel existant ; 
Si <i>referentiel_selection_autorisee</i> vaut 1 les enseignants ne peuvent pas sélectionner un référentiel existant pour l\'associer à une nouvelle instance de référentiel,
mais le créateur du cours peut modifier ce paramètre pour son cours ;
Si <i>referentiel_selection_autorisee</i> vaut 2, la sélection est interdite et de plus ce paramètre est verrouillé au niveau du site et donc n\'est pas modifiable au niveau du cours.
';

$string['config_verrouillee'] = 'Configuration verrouillée par l\'administrateur';
$string['approve_all_activity_task'] = 'Valider en bloc toutes les activités associées à la tâche ';
$string['approve_all_activity_task_closed'] = 'Cette tâche est close. Valider en bloc toutes les activités qui lui sont associées ? ';
$string['confirm_validation_task'] = 'Confirmez la validation de la tâche.<b>Attention</b> :
En confirmant vous validez automatiquement toutes les souscriptions à cette Tâche !';
$string['approved_task_by'] = 'Tâche approuvée par ';
$string['closed_task'] = 'Tâche achevée';
$string['souscrire'] = 'Souscrire';
$string['configuration'] = 'Configuration';
$string['scolarite_user'] = 'Données de scolarité masquées aux utilisateurs';
$string['config_scolarite'] = 'Les données de scolarité sont nécessaires à l\'édition des certificats.
Elles sont soit saisies par les enseignants dans le cours où est situé le module Référentiel ,
auquel cas elles doivent être laissées visibles, soit elles sont injectées par l\'administrateur auquel cas elles doivent être masquées.

Si <i>referentiel_scolarite_masquee</i> vaut 0 les enseignants avec droit d\'édition <i>peuvent</i> afficher ou importer des données de scolarité au niveau du cours ;
Si <i>referentiel_creation_limitee</i> vaut 1 les enseignants <i>ne peuvent pas</i> afficher ou importer des données de scolarité , mais le créateur d\'un cours peut modifier ce paramètre pour son cours ;
Si <i>referentiel_creation_limitee</i> vaut 2 ce paramètre est verrouillé au niveau du site, les enseignants ne peuvent pas afficher ou importer des données de scolarité.

';
$string['create_referentiel'] = 'Interdire la création, l\'Importation et la modification d\'un référentiel';
$string['config_creer_referentiel'] = 'Ce paramètre régit la création, l\'Importation et la modification d\'un référentiels au niveau du site et du cours.

Si <i>referentiel_creation_limitee</i> vaut 0, les enseignants avec droit d\'édition <i>peuvent</i> créer ou importer un référentiel au niveau du cours ;
Si <i>referentiel_creation_limitee</i> vaut 1 les enseignants <i>ne peuvent pas</i> créer de référentiel, mais le créateur d\'un cours peut modifier ce paramètre pour son cours ;
Si <i>referentiel_creation_limitee</i> vaut 2 ce paramètre est verrouillé au niveau du site, les enseignants ne peuvent pas créer de référentiel.
';
$string['scol']='Données de scolarité masquées aux utilisateurs';
$string['creref']  = 'Interdire la création, l\'Importation et la modification d\'un référentiel';
$string['selref']  = 'Interdire d\'associer un référentiel existant à une nouvelle instance de référentiel';
$string['impcert']  = 'Interdire l\'Impression des certificats';

$string['task'] = 'Tâche';
$string['tasks'] = 'Tâches';
$string['listtask'] = 'Afficher';
$string['listtasksingle'] = 'Détails';
$string['selecttask'] = 'Choisir';
$string['modif_task'] = 'Modifier';
$string['modifier_task'] = 'Modifier une Tâche';
$string['addtask'] = 'Définir';
$string['updatetask'] = 'Modifier';

$string['creer_task'] = 'Ajouter une Tâche';
$string['consigne_associee'] = 'Document de consignes attaché';
$string['type_task']= 'Thème ';
$string['type_consigne']= 'Type de document';

$string['depot_consigne']= 'Attacher un document de consignes';
$string['criteres_evaluation'] = 'Critères d\'évaluation';
$string['date_modification'] = 'Modifié le';
$string['date_debut'] = 'Date de disponibilité ';
$string['date_fin'] = 'A remettre avant le ';
$string['heure'] = 'Heure';
$string['jour'] = 'Jour';
$string['mois'] = 'Mois';
$string['annee'] = 'Année';
$string['consigne']= 'Document';
$string['consigne_associe']= 'Document associé';
$string['consigne_ajout'] = 'Ajouter un document de consignes';
$string['consigne_task'] = 'Réalisation demandée';
$string['confirm_association_task'] = 'Confirmer la sélection de cette Tâche';

// Version  1.2

// A traduire ou retraduire
// scolarite
$string['referentiel_config_local'] = 'Configuration';
$string['masquee'] = 'Masquée';
// $string['visible'] = 'Visible';

$string['exportetudiant'] = 'Exporter';
$string['importetudiant'] = 'Importer';
$string['import_etudiant'] = 'Importer des adresses d\'étudiants';
$string['export_etudiant'] = 'Exporter des adresses d\'étudiants';
$string['importetudiants'] = 'Importer des données de scolarité';
$string['exportetudiants'] = 'Exporter des données de scolarité';
$string['importstudentsdone'] = 'Importation des données de scolarité achevée';
$string['exportingetudiants'] = 'Les adresses d\'établissements et d\'étudiants sont exportées vers un fichier';
$string['noetudiant'] = 'Aucun étudiant n\'est enregistré. Il faut inscrire des étudiants au cours... ';

$string['html'] = 'Format HTML';

$string['code_referentiel'] = 'Code référentiel';
$string['description_referentiel'] = 'Description';
$string['url_referentiel'] = 'URL du référentiel';
$string['nb_domaines'] = 'Nombre de domaines ';

$string['code_domaine'] = 'Code domaine';
$string['description_domaine'] = 'Description domaine';
$string['num_domaine'] = 'Numéro de domaine';
$string['nb_competences'] = 'Nombre de compétences';
$string['code_competence'] = 'Code de compétence';
$string['description_competence'] = 'Description de compétence';
$string['num_competence'] = 'Numéro  de compétence';
$string['nb_item_competences'] = 'Nombre  d\'Items';
$string['description_item'] = 'Description de l\'Item';

// Version  2008/06/17
$string['depot_document'] = 'Ajouter un document ou une ressource ';
$string['modifier_document'] = 'Modifier un document';
$string['nouvelle_fenetre'] = 'dans une fenêtre séparée.';
$string['ouvrir'] = 'Ouvrir ';
$string['nocertificat'] = 'Aucune certificat n\'est enregistré. Il faut déclarer une activité puis la faire évaluer par un enseignant... ';
$string['enseignant'] = 'Enseignant ';
$string['date_naissance'] = 'Né le ';
$string['date_signature'] = 'Le {$a} - Signature';
$string['date_instance'] = 'Date';
$string['ref_referentiel'] = 'Référentiel';
$string['visible'] = 'Visible';
// $string['iD'] = 'ID';
$string['competences'] = 'Compétences';
$string['decision'] = 'Proposition';
$string['datedecision'] = 'Date de la proposition : {$a}';
$string['userid'] = 'ID utilisateur';
$string['valide_par'] = 'Validé par';
$string['verrou'] = 'Verrouillé';

$string['num_item'] = 'Numéro';
$string['t_item'] = 'Type';
$string['p_item'] = 'Poids';
$string['e_item'] = 'Empreinte';
$string['empreinte_item']  = 'Empreinte [0..999] :';
$string['empreinte'] = 'Empreinte [0..999] : Nombre de fois où cette compétence doit être validées pour être certifiée.';
$string['etiquette_inconnue'] = 'Afficher';
$string['etiquette_url'] = 'Etiquette du lien ';
$string['liste_empreintes_competence'] = 'Liste des empreintes';
$string['liste_codes_empreintes_competence'] = 'Liste des codes, Poids et <i>Empreintes (un item d\'empreinte nulle n\'est pas pris en compte dans la certification)</i>';

$string['upload_succes'] = 'Fichier téléchargé avec succès.';



// en_utf8 traduits par Philippe V.
$string['abandonner'] = 'Abandonner';
$string['activite'] = 'Activité';
$string['addactivity'] = 'Déclarer';
$string['addcertif'] = 'Générer';
$string['aide_saisie_competences'] = 'Sélectionnez les compétences mobilisées par cette activité ';
$string['approved'] = 'Validé';
$string['approve'] = 'Valider';
$string['associer_referentiel'] = 'Associer cette instance à un référentiel ';
$string['auteur'] = 'Auteur';

$string['cannotcreatepath'] = 'Le fichier ne peut pas être créé ({$a})';
$string['cannoteditafterattempts'] = 'Vous ne pouvez pas ajouter ou retirer des compétences, car il y a déjà des tentatives.';
$string['cannotinsert'] = 'Impossible  d\'Insérer la compétence';
$string['cannotopen'] = 'Impossible d\'ouvrir le fichier d\'exportation ({$a})';
$string['cannotread'] = 'Impossible d\'ouvrir le fichier  d\'Importation (ou le fichier est vide)';
$string['cannotwrite'] = 'Impossible d\'écrire dans le fichier d\'exportation ({$a})';
$string['certificat'] = 'Certificat';
$string['certificats'] = 'Certificats';
$string['certificat_etat'] = 'Etat du certificat ';
$string['certificat_initialiser'] = 'Réinitialiser le certificat ';
$string['certificat_verrou'] = 'Verrouiller le certificat ';
$string['certification'] = 'Certification';
$string['certifier'] = 'Certifier';
$string['choisir'] = '_Choisir_';
$string['choix_newinstance'] = 'Oui';
$string['choix_notnewinstance'] = 'Non';
$string['choosefile'] = 'Choisir un fichier';
$string['choix_instance'] = 'Choix des instances ';
$string['choix_filtrerinstance'] = 'Filtrer les occurrences sélectionnées ? ';
$string['choix_oui_filtrerinstance'] = 'Oui ';
$string['choix_non_filtrerinstance'] = 'Non ';
$string['choix_localinstance'] = 'locales';
$string['choix_globalinstance'] = 'globales';
$string['choix_override'] = 'écrasée';
$string['choix_notoverride'] = 'conservée';
$string['cocher_competences_valides'] = 'Cocher les compétences validées ';
$string['code'] = 'Code ';
$string['comment'] = 'Commenter';
$string['comment_certificat'] = 'Commenter un certificat';
$string['commentaire'] = 'Commentaire';
$string['commentaire_certificat'] = 'Commentaire';
$string['competence'] = 'Compétence ';
$string['competences_activite'] = 'Compétences déclarées pour cette activité ';
$string['competences_certificat'] = 'Compétences certifiées ';
$string['competence_inconnu'] = 'Compétence inconnue ';
$string['competences_validees'] = 'Compétences validées ';
$string['completer_referentiel'] = 'Compléter le référentiel ';
$string['confirmdeleterecord'] = 'Confirmez la suppression de l\'enregistrement ';
$string['confirminitialiserecord'] = 'Confirmez la réinitilisation de l\'enregistrement Tous les contenu sera vidé et les compétences réactualisées...';
$string['confirmvalidateactivity'] = 'Confirmez la <b>validation</b> de l\'activité ';
$string['confirmdeverrouiller'] = 'Confirmez le <b>deverrouillage</b> du certificat ';
$string['confirmverrouiller'] = 'Confirmez le <b>verrouillage</b> du certificat ';
$string['confirmdevalidateactivity'] = 'Confirmez la <b>dévalidation</b> de l\'activité ';
$string['corriger'] = 'Restaurer';
$string['creation_domaine'] = 'Création des domaines ';
$string['creation_competence'] = 'Création des compétences ';
$string['creation_item'] = 'Création des items ';
$string['creer'] = 'Créer';
$string['creer_certificat'] = 'Créer un certificat';
$string['creer_referentiel'] = 'Créer un référentiel ';
$string['creer_activite'] = 'Créer une activité';
$string['creer_instance_referentiel'] = 'Créer / modifier une instance de référentiel ';
$string['csv'] = 'Format CSV';

$string['date_creation'] = 'Créé le ';
$string['date_decision'] = 'Date de la proposition';
$string['decision_jury'] = 'Proposition au jury';
$string['declarer'] = 'Déclarer';
$string['delete_activity'] = 'Supprimer';
$string['deletecertif'] = 'Supprimer';
$string['desapprove'] = 'Dévalider';
$string['description'] = 'Description ';
$string['description_instance'] = 'Description de l\'Instance ';
$string['deverrouille'] = 'Déverrouillé';
$string['deverrouiller'] = 'Déverrouiller';
$string['document'] = 'Document';
$string['document_ajout'] = 'Ajouter une ressource';
$string['document_associe'] = 'Ressources(s) associée(s) à l\'activité ';
$string['domaine_inconnu'] = 'Domaine inconnu ';
$string['domaine'] = 'Domaine ';
$string['download'] = 'Cliquer pour télécharger le fichier exporté';
$string['downloadextra'] = '(le fichier est aussi déposé dans les fichiers du cours, dans le dossier /referentiel)';

$string['edit_activity'] = 'Activités';
$string['editcertif'] = 'Editer';
$string['editetudiant'] = 'Editer';
$string['edit_an_activity'] = 'Editer une activité';
$string['editreferentiel'] = 'Modifier';
$string['erreur_referentiel'] = 'Aucun référentiel n\'est défini ';
$string['erreurscript'] = 'Erreur de script PHP :<i> {$a} </i>. Informez l\'auteur du module.';
$string['etablissement']= 'Etablissement';
$string['etablissements']= 'Etablissements';
$string['etudiant']= 'Etudiant';
$string['etudiants']= 'Etudiants';
$string['evaluation']= 'Evaluation';
$string['exportreferentiel'] = 'Exporter le référentiel';
$string['export'] = 'Exporter';
$string['exportactivite'] = 'Exporter les activités ';
$string['exportcertificat'] = 'Exporter les certificats ';
$string['exporterror'] = 'Une erreur est survenue durant l\'exportation';
$string['exportfilename'] = 'referentiel'; // attention : Ne pas traduire cette chaîne ! Voir http://tracker.moodle.org/browse/MDL-4544
$string['exportingreferentiels'] = 'Le référentiel est exporté vers un fichier';
$string['exportingactivites'] = 'Les activités sont exportées vers un fichier';
$string['exportname'] = 'Nom de fichier';
$string['exportingcertificats'] = 'Les certificats sont exportés vers un fichier';
$string['exportnameformat'] = '%Y%m%d-%H%M';

$string['filtrerlocalinstance'] = 'Si l\'option ci-dessus est cochée, choisir parmi les occurrences ';
$string['fileformat'] = 'Format de fichier';
$string['fileprint'] = 'Format  d\'Impression';
$string['formatnotfound'] = 'Le format  d\'Importation/exportation {$a} n\'a pas été trouvé';
$string['formatnotimplemented'] = 'Ce format n\'a pas été implémenté correctement';
$string['fromfile'] = 'Depuis le fichier&nbsp;:';

$string['globalinstance'] = 'Instances de référentiel globales ';
$string['global'] = '[Instance globale]';

$string['id'] = 'ID ';
$string['import'] = 'Importer';
$string['importerror_referentiel_id'] = 'Une erreur est survenue lors de l\'Importation : Numéro de référentiel inconnu';
$string['importerror'] = 'Une erreur est survenue lors de l\'Importation';
$string['importfilearea'] = 'Importer à partir d\'un fichier du cours...';
$string['importfileupload'] = 'Importer à partir d\'un fichier à déposer...';
$string['importfromthisfile'] = 'Importer à partir de ce fichier';
$string['importing'] = 'Importation du référentiel {$a} à partir du fichier';
$string['importmax10error'] = 'Cette compétence comporte une erreur. Il est impossible d\'avoir plus de 10 réponses';
$string['importmaxerror'] = 'Cette compétence comporte une erreur. Elle propose trop de réponses.';
$string['importminerror'] = 'Cette compétence comporte une erreur. Il n\'y a pas assez de réponses pour ce type de compétence';
$string['importreferentiel'] = 'Importer un référentiel';
$string['importdone'] = 'Importation du référentiel achevée...';
$string['importtodatabase'] = 'Insertion dans la base en travaux...';
$string['incompletedata'] = 'Données manquantes... Importation interrompue. ';
$string['inconnu'] = 'INCONNU';
$string['item'] = 'Item ';
$string['item_supplementaire'] = 'Item supplémentaire ';

$string['label_domaine_question'] = 'Comment faut-il dénommer un <i>domaine</i> ? ';
$string['label_competence_question'] = 'Comment faut-il dénommer une <i>compétence</i> ? ';
$string['label_item_question'] = 'Comment faut-il dénommer un <i>item</i> ? ';
$string['label_domaine'] = 'Label des domaines ';
$string['label_competence'] = 'Label des compétences';
$string['label_item'] = 'Label des items ';
$string['liste_codes_competence'] = 'Compétences ';
$string['listactivity'] = 'Lister';
$string['listcertif'] = 'Lister';
$string['listetudiant'] = 'Etudiants';
$string['lister'] = 'Lister';
$string['listreferentiel'] = 'Afficher';
$string['localinstance'] = 'Instances de référentiel locales ';
$string['local'] = '[Instance locale]';
$string['logo'] = 'Logo';

$string['managecertif'] = 'Exporter';
$string['manageetab'] = 'Nouvel établissement';
$string['modification'] = 'Modification ';
$string['modifier'] = 'Modifier';
$string['modifier_referentiel'] = 'Modifier le référentiel ';
$string['modifier_domaine_referentiel'] = 'Modifier un domaine de compétence ';
$string['modifier_competence_referentiel'] = 'Modifier une compétence ';
$string['modifier_item'] = 'Modifier un item ';
$string['modifier_item_competence_referentiel'] = 'Modifier un item ';
$string['modifier_activite'] = 'Modifier une activité';
$string['modifier_certificat'] = 'Modifier un certificat';
$string['modifier_etudiant'] = 'Modifier';
$string['modulenameplural'] = 'Référentiels ';
$string['modulename'] = 'Référentiel ';
$string['moins'] = 'Moins';

$string['name'] = 'Nom ';
$string['name_instance'] = 'Titre de l\'Instance ';
$string['newinstance'] = 'Faut-il créer dans ce cours une nouvelle instance à partir du référentiel chargé ? ';
$string['noaccess'] = 'Accès non autorisé ';
$string['noaccess_certificat'] = 'Accès non autorisé aux certificats ';
$string['noactivite'] = 'Aucune activité n\'est enregistrée ';
$string['noactivitefiltre'] = 'Pas d\'activité avec ces critères ';

$string['nombre_domaines_supplementaires'] = 'Nombre de domaines à enregistrer pour ce référentiel ';
$string['nombre_competences_supplementaires'] = 'Nombre de compétences <span class="small">à enregistrer pour ce domaine</span>';
$string['nombre_item_competences_supplementaires'] = 'Nombre  d\'items <span class="small">à enregistrer pour cette compétence</span>';
$string['numero'] = 'Numéro ';
$string['noinfile'] = 'Importation annulée(données incorrectes ou risque d\'écrasement de données existantes) ';
$string['invalide'] = 'Invalide';
$string['nologo'] = 'Logo ?';
$string['noresponse'] = 'Pas de reponse';
$string['not_approved'] = 'Non validé';

$string['override'] = 'Si une occurrence identique existe (même nom, même code) elle doit être ';
$string['overriderisk'] = ': risque d\'écrasement de la version locale ';

$string['parsing'] = 'Analyse du fichier à importer.';
$string['plus'] = 'Détails';
$string['poids_item'] = 'Poids de l\'Item (valeur décimale)';
$string['profil'] = 'Profil';
$string['print'] = 'Impression';
$string['printfilename'] = 'Impression';
$string['printcertif'] = 'Imprimer';
$string['printcertificat'] = 'Impression des certificats';

$string['quit'] = 'Quitter';

$string['recordapproved'] = 'Enregistrement effectué ';
$string['referent'] = 'Référent';
$string['referentiel'] = 'Référentiel';
$string['referentiel_global'] = 'Référentiel global ';
$string['referentiel_inconnu'] = 'Référentiel inconnu ';
$string['referentiel_instance'] = 'Instance de certification ';

$string['saisie_competence'] = 'Saisissez au moins une compétence ';
$string['saisie_competence_supplementaire'] = 'Saisir une nouvelle compétence ';
$string['saisie_domaine_competence'] = 'Saisissez au moins un domaine de compétence ';
$string['saisie_domaine_supplementaire'] = 'Saisir un nouveau domaine de compétence ';
$string['saisie_item'] = 'Saisissez au moins un item de compétence ';
$string['saisie_item_supplementaire'] = 'Saisir un nouvel item de compétence ';
$string['scolarite'] = 'Scolarité';
$string['select'] = 'Sélectionner';
$string['selectreferentiel'] = 'Sélectionner un référentiel existant ';
$string['selecterror_referentiel_id'] = 'Une erreur est survenue lors de la sélection : Numéro de référentiel inconnu';
$string['stoponerror'] = 'Stopper en cas d\'erreur';
$string['selectnoreferentiel'] = 'Aucun référentiel n\'est enregistré ';
$string['seuil_certificat'] = 'Seuil de certification (valeur décimale) ';
$string['single'] = 'Affichage activité';
$string['supprimer_activite'] = 'Supprimer une activité';

$string['type'] = 'Type ';
$string['type_activite'] = 'Type d\'activité ';
$string['type_document'] = 'Type de document';

$string['extensions_document'] = '[Texte, Exe, PDF, ZIP, Image, Audio, Video...] ';
$string['type_item'] = 'Type  d\'Item [Obligatoire, Optionnel...] ';

$string['updateactivity'] = 'Modifier';
$string['updatecertif'] = 'Modifier';
$string['url'] = 'URL ';

$string['valide'] = 'valide';
$string['validation'] = 'Validation';
$string['verrouille'] = 'Verrouillé';
$string['verrouiller'] = 'Verrouiller';
$string['visible_referentiel'] = 'Référentiel visible ? ';

$string['xhtml'] = 'Format XHTMl';
$string['xml'] = 'Format XML Moodle';
$string['xmlimportnoname'] = 'Nom du référentiel manquant dans le fichier xml';
$string['xmlimportnocode'] = 'Code de compétence manquant dans le fichier xml';

$string['nom_prenom'] = 'NOM Prénom';
$string['num_etudiant'] = 'Numéro d\'étudiant';
$string['ddn_etudiant'] = 'Date de naissance';
$string['lieu_naissance'] = 'Lieu de naissance';
$string['departement_naissance'] = 'Département de naissance';
$string['adresse_etudiant'] = 'Adresse';
$string['ref_etablissement'] = 'Etablissement';

$string['modifier_etablissement'] = 'Modifier un établissment';
$string['num_etablissement'] = 'Numéro de l\'établissement';
$string['nom_etablissement'] = 'Nom de l\'établissement';
$string['adresse_etablissement'] = 'Adresse de l\'établissement';
?>
