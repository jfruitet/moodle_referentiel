<?php 

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/locallib.php');

if ($ADMIN->fulltree) {

// Valeurs par defaut
// scol:0;creref:0;selref:0;impcert:0;graph:0;refcert:1;instcert:0;numetu:1;nometu:1;etabetu:0;ddnetu:0;lieuetu:0;adretu:0;pourcent:0;compdec:0;compval:1;refcert:0;jurycert:1;comcert:0;

/*
// exemples d'usage
    $options = array(ASSIGNMENT_COUNT_WORDS   => trim(get_string('numwords', '', '?')),
                     ASSIGNMENT_COUNT_LETTERS => trim(get_string('numletters', '', '?')));
    $settings->add(new admin_setting_configselect('assignment_itemstocount', get_string('itemstocount', 'assignment'),
                       get_string('configitemstocount', 'assignment'), ASSIGNMENT_COUNT_WORDS, $options));

    $settings->add(new admin_setting_configcheckbox('assignment_showrecentsubmissions', get_string('showrecentsubmissions', 'assignment'),
                       get_string('configshowrecentsubmissions', 'assignment'), 1));

    $settings->add(new admin_setting_configselect('forum_displaymode', get_string('displaymode', 'forum'),
                       get_string('configdisplaymode', 'forum'), FORUM_MODE_NESTED, forum_get_layout_modes()));

    $settings->add(new admin_setting_configcheckbox('forum_replytouser', get_string('replytouser', 'forum'),
                       get_string('configreplytouser', 'forum'), 1));

    // Less non-HTML characters than this is short
    $settings->add(new admin_setting_configtext('forum_shortpost', get_string('shortpost', 'forum'),
                       get_string('configshortpost', 'forum'), 300, PARAM_INT));

*/
// use scale

$settings->add(new admin_setting_heading('referentiel_heading', get_string('generalconfig', 'referentiel'),
                       get_string('explaingeneralconfig', 'referentiel')));

$settings->add(new admin_setting_heading('scale_setting', '', get_string('scale_setting', 'referentiel')));
unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->referentiel_use_scale)){
    $settings->add(new admin_setting_configselect('referentiel_use_scale', get_string('use_scale', 'referentiel'),
                   get_string('config_scale', 'referentiel'), $CFG->referentiel_use_scale, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_use_scale', get_string('use_scale', 'referentiel'),
                   get_string('config_scale', 'referentiel'), 0, $options));                   
}


// parametrage affichage certification
$settings->add(new admin_setting_heading('certif_setting', '', get_string('certif_setting', 'referentiel')));

// config la certification
unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_certif_config)){
    $settings->add(new admin_setting_configselect('referentiel_certif_config', get_string('certif_config', 'referentiel'),
                   get_string('config_certif_config', 'referentiel'), $CFG->referentiel_certif_config, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_certif_config', get_string('certif_config', 'referentiel'),
                   get_string('config_certif_config', 'referentiel'), 0, $options));
}

// etat de la certification
unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->referentiel_certif_state)){
    $settings->add(new admin_setting_configselect('referentiel_certif_state', get_string('certif_state', 'referentiel'),
                   get_string('config_certif_state', 'referentiel'), $CFG->referentiel_certif_state, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_certif_state', get_string('certif_state', 'referentiel'),
                   get_string('config_certif_state', 'referentiel'), 1, $options));
}


// affichage hierarchique des compétences
$settings->add(new admin_setting_heading('input_setting', '', get_string('input_setting', 'referentiel')));

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;

if (isset($CFG->referentiel_hierarchy)){
    $settings->add(new admin_setting_configselect('referentiel_hierarchy', get_string('input_hierarchy', 'referentiel'),
                   get_string('config_input', 'referentiel'), $CFG->referentiel_hierarchy, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_hierarchy', get_string('input_hierarchy', 'referentiel'),
                   get_string('config_input', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_light_display)){
    $settings->add(new admin_setting_configselect('referentiel_light_display', get_string('light_display', 'referentiel'),
                   get_string('config_light', 'referentiel'), $CFG->referentiel_light_display, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_light_display', get_string('light_display', 'referentiel'),
                   get_string('config_light', 'referentiel'), 0, $options));
}

// echo "<h3>Deshérance<h3>\n";
$settings->add(new admin_setting_heading('delai_setting', '', get_string('delai_setting', 'referentiel')));

// desherence
$settings->add(new admin_setting_configtext('delaidesherence', get_string('delaidesherence', 'referentiel'),
        get_string('config_delaidesherence', 'referentiel'), 28, PARAM_INT));


$options = array();

// archives
// echo "<h3>Gestion des archives<h3>\n";
$settings->add(new admin_setting_heading('archive_setting', '', get_string('archive_setting', 'referentiel')));

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->referentiel_purge_archives)){
    $settings->add(new admin_setting_configselect('referentiel_purge_archives', get_string('purge_archives', 'referentiel'),
                   get_string('config_archives', 'referentiel'), $CFG->referentiel_purge_archives, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_purge_archives', get_string('purge_archives', 'referentiel'),
                   get_string('config_archives', 'referentiel'), 0, $options));
}

// scolarite
// echo "<h3>Données de scolarité<h3>\n";

$settings->add(new admin_setting_heading('scol_setting', '', get_string('scol_setting', 'referentiel')));

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_scolarite_masquee)){
    $settings->add(new admin_setting_configselect('referentiel_scolarite_masquee', get_string('scolarite_user', 'referentiel'),
                   get_string('config_scolarite', 'referentiel'), $CFG->referentiel_scolarite_masquee, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_scolarite_masquee', get_string('scolarite_user', 'referentiel'),
                   get_string('config_scolarite', 'referentiel'), 0, $options));
}

// student profile to get student number id
// echo "<h3>Utilisation des profils pour les données étudiants<h3>\n";

$settings->add(new admin_setting_heading('profil_setting', '', get_string('profil_setting', 'referentiel')));

$settings->add(new admin_setting_configtext('ref_profilecategory', get_string('ref_profilecategory', 'referentiel'),
        get_string('config_ref_profilecategory', 'referentiel'), get_string('profiledefaultcategory', 'admin'), PARAM_TEXT));

$settings->add(new admin_setting_configtext('ref_profilefield', get_string('ref_profilefield', 'referentiel'),
        get_string('config_ref_profilefield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_ddnfield', get_string('ref_ddnfield', 'referentiel'),
        get_string('config_ref_ddnfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_ldnfield', get_string('ref_ldnfield', 'referentiel'),
        get_string('config_ref_ldnfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_dptfield', get_string('ref_dptfield', 'referentiel'),
        get_string('config_ref_dptfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_adrfield', get_string('ref_adrfield', 'referentiel'),
        get_string('config_ref_adrfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_etabfield', get_string('ref_etabfield', 'referentiel'),
        get_string('config_ref_etabfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

$settings->add(new admin_setting_configtext('ref_numetabfield', get_string('ref_numetabfield', 'referentiel'),
        get_string('config_ref_numetabfield', 'referentiel'), '', PARAM_ALPHANUMEXT));

// creation de referentiels
// echo "<h3>Référentiel<h3>\n";
$settings->add(new admin_setting_heading('ref_setting', '', get_string('ref_setting', 'referentiel')));

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_creation_limitee)){
    $settings->add(new admin_setting_configselect('referentiel_creation_limitee', get_string('create_referentiel', 'referentiel'),
                   get_string('config_creer_referentiel', 'referentiel'), $CFG->referentiel_creation_limitee, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_creation_limitee', get_string('create_referentiel', 'referentiel'),
                   get_string('config_creer_referentiel', 'referentiel'), 0, $options));
}
// selection de referentiels
unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_selection_autorisee)){
    $settings->add(new admin_setting_configselect('referentiel_selection_autorisee', get_string('select_referentiel', 'referentiel'),
                   get_string('config_select_referentiel', 'referentiel'), $CFG->referentiel_selection_autorisee, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_selection_autorisee', get_string('select_referentiel', 'referentiel'),
                   get_string('config_select_referentiel', 'referentiel'), 0, $options));
}

// echo "<h3>Statistiques<h3>\n";
// affichage graphique des competences validées
$settings->add(new admin_setting_heading('stat_setting', '', get_string('stat_setting', 'referentiel')));

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_affichage_graphique)){
    $settings->add(new admin_setting_configselect('referentiel_affichage_graphique', get_string('select_aff_graph', 'referentiel'),
                   get_string('config_aff_graph', 'referentiel'), $CFG->referentiel_affichage_graphique, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_affichage_graphique', get_string('select_aff_graph', 'referentiel'),
                   get_string('config_aff_graph', 'referentiel'), 0, $options));
}

// echo "<h3>Certificats<h3>\n";
// impression des certificats
$settings->add(new admin_setting_heading('cert_setting', '', get_string('cert_setting', 'referentiel')));

unset($options);
$options[0] = 0;
$options[1] = 1;
$options[2] = 2;
if (isset($CFG->referentiel_impression_autorisee)){
    $settings->add(new admin_setting_configselect('referentiel_impression_autorisee', get_string('referentiel_impression_autorisee', 'referentiel'),
                   get_string('config_impression_referentiel', 'referentiel'), $CFG->referentiel_impression_autorisee, $options));
}
else{
    $settings->add(new admin_setting_configselect('referentiel_impression_autorisee', get_string('referentiel_impression_autorisee', 'referentiel'),
                   get_string('config_impression_referentiel', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_referentiel)){
    $settings->add(new admin_setting_configselect('certificat_sel_referentiel', get_string('certificat_sel_referentiel', 'referentiel'),
                   get_string('config_certificat_sel_referentiel', 'referentiel'), $CFG->certificat_sel_referentiel, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_referentiel', get_string('certificat_sel_referentiel', 'referentiel'),
                   get_string('config_certificat_sel_referentiel', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_referentiel_instance) ){
    $settings->add(new admin_setting_configselect('certificat_sel_referentiel_instance', get_string('certificat_sel_referentiel_instance', 'referentiel'),
                   get_string('config_certificat_sel_referentiel_instance', 'referentiel'), $CFG->certificat_sel_referentiel_instance, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_referentiel_instance', get_string('certificat_sel_referentiel_instance', 'referentiel'),
                   get_string('config_certificat_sel_referentiel_instance', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_etudiant_numero)){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_numero', get_string('certificat_sel_etudiant_numero', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_numero', 'referentiel'), $CFG->certificat_sel_etudiant_numero, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_numero', get_string('certificat_sel_etudiant_numero', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_numero', 'referentiel'), 0, $options));
}
				 
unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_etudiant_nom_prenom) ){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_nom_prenom', get_string('certificat_sel_etudiant_nom_prenom', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_nom_prenom', 'referentiel'), $CFG->certificat_sel_etudiant_nom_prenom, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_nom_prenom', get_string('certificat_sel_etudiant_nom_prenom', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_nom_prenom', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_etudiant_etablissement) ){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_etablissement', get_string('certificat_sel_etudiant_etablissement', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_etablissement', 'referentiel'), $CFG->certificat_sel_etudiant_etablissement, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_etablissement', get_string('certificat_sel_etudiant_etablissement', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_etablissement', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_etudiant_ddn) ){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_ddn', get_string('certificat_sel_etudiant_ddn', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_ddn', 'referentiel'), $CFG->certificat_sel_etudiant_ddn, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_ddn', get_string('certificat_sel_etudiant_ddn', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_ddn', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_etudiant_lieu_naissance)){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_lieu_naissance', get_string('certificat_sel_etudiant_lieu_naissance', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_lieu_naissance', 'referentiel'), $CFG->certificat_sel_etudiant_lieu_naissance, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_lieu_naissance', get_string('certificat_sel_etudiant_lieu_naissance', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_lieu_naissance', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_etudiant_adresse)){
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_adresse', get_string('certificat_sel_etudiant_adresse', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_adresse', 'referentiel'), $CFG->certificat_sel_etudiant_adresse, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_etudiant_adresse', get_string('certificat_sel_etudiant_adresse', 'referentiel'),
                   get_string('config_certificat_sel_etudiant_adresse', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_certificat_detail)){
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_detail', get_string('certificat_sel_certificat_detail', 'referentiel'),
                   get_string('config_certificat_sel_certificat_detail', 'referentiel'), $CFG->certificat_sel_certificat_detail, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_detail', get_string('certificat_sel_certificat_detail', 'referentiel'),
                   get_string('config_certificat_sel_certificat_detail', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_certificat_pourcent)){
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_pourcent', get_string('certificat_sel_certificat_pourcent', 'referentiel'),
                   get_string('config_certificat_sel_certificat_pourcent', 'referentiel'), $CFG->certificat_sel_certificat_pourcent, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_pourcent', get_string('certificat_sel_certificat_pourcent', 'referentiel'),
                   get_string('config_certificat_sel_certificat_pourcent', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_activite_competences) ){
    $settings->add(new admin_setting_configselect('certificat_sel_activite_competences', get_string('certificat_sel_activite_competences', 'referentiel'),
                   get_string('config_certificat_sel_activite_competences', 'referentiel'), $CFG->certificat_sel_activite_competences, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_activite_competences', get_string('certificat_sel_activite_competences', 'referentiel'),
                   get_string('config_certificat_sel_activite_competences', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_certificat_competences)){
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_competences', get_string('certificat_sel_certificat_competences', 'referentiel'),
                   get_string('config_certificat_sel_certificat_competences', 'referentiel'), $CFG->certificat_sel_certificat_competences, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_competences', get_string('certificat_sel_certificat_competences', 'referentiel'),
                   get_string('config_certificat_sel_certificat_competences', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_certificat_referents)){
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_referents', get_string('certificat_sel_certificat_referents', 'referentiel'),
                   get_string('config_certificat_sel_certificat_referents', 'referentiel'), $CFG->certificat_sel_certificat_referents, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_certificat_referents', get_string('certificat_sel_certificat_referents', 'referentiel'),
                   get_string('config_certificat_sel_certificat_referents', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 1;
$options[1] = 0;
if (isset($CFG->certificat_sel_decision_jury)){
    $settings->add(new admin_setting_configselect('certificat_sel_decision_jury', get_string('certificat_sel_decision_jury', 'referentiel'),
                   get_string('config_certificat_sel_decision_jury', 'referentiel'), $CFG->certificat_sel_decision_jury, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_decision_jury', get_string('certificat_sel_decision_jury', 'referentiel'),
                   get_string('config_certificat_sel_decision_jury', 'referentiel'), 0, $options));
}

unset($options);
$options[0] = 0;
$options[1] = 1;
if (isset($CFG->certificat_sel_commentaire)){
    $settings->add(new admin_setting_configselect('certificat_sel_commentaire', get_string('certificat_sel_commentaire', 'referentiel'),
                   get_string('config_certificat_sel_commentaire', 'referentiel'), $CFG->certificat_sel_commentaire, $options));
}
else{
    $settings->add(new admin_setting_configselect('certificat_sel_commentaire', get_string('certificat_sel_commentaire', 'referentiel'),
                   get_string('config_certificat_sel_commentaire', 'referentiel'), 0, $options));
}
}
?>