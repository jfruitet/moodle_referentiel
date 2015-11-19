<?php  // $Id: selection.php,v 1.0 2008/04/29/ 00:00:00 jfruitet Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2005 Martin Dougiamas  http://dougiamas.com             //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
* association d'un referentiel a l'instance
* @package referentiel
*/
	
    require_once('../../config.php');
    require_once('locallib.php');
	require_once('print_lib_referentiel.php');

    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentiel instance id
	$pass  = optional_param('pass', 0, PARAM_INT);    // mot de passe ok
    $checkpass = optional_param('checkpass','', PARAM_ALPHA); // mot de passe fourni

    $action  			= optional_param('action','', PARAM_ALPHA); // pour distinguer differentes formes de creation de referentiel
    $mode               = optional_param('mode','add', PARAM_ALPHANUMEXT);
    $format 			= optional_param('format','', PARAM_FILE );

	$name_instance		= optional_param('name_instance','', PARAM_ALPHANUMEXT);
	$description_instance		= optional_param('description_instance','', PARAM_ALPHANUMEXT);
	$label_domaine    = optional_param('label_domaine','', PARAM_ALPHANUMEXT);
	$label_competence = optional_param('label_competence','', PARAM_ALPHANUMEXT);
	$label_item= optional_param('label_item','', PARAM_ALPHANUMEXT);

	$instance 			= optional_param('instance', 0, PARAM_INT);

 	$select_acc = optional_param('select_acc', 0, PARAM_INT);      // accompagnement

    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/selection.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }

		if (! $course = $DB->get_record("course", array("id" => "$referentiel->course"))) {
	            print_error('Course is misconfigured');
    	}

		if (! $cm = get_coursemodule_from_instance('referentiel', $referentiel->id, $course->id)) {
    	        print_error('Course Module ID is incorrect');
		}
		$url->param('d', $d);
	}
	elseif ($id) {
        if (! $cm = get_coursemodule_from_id('referentiel', $id)) {
        	print_error('Course Module ID was incorrect');
        }
        if (! $course = $DB->get_record("course", array("id" => "$cm->course"))) {
            print_error('Course is misconfigured');
        }
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$cm->instance"))) {
            print_error('Referentiel instance is incorrect');
        }
        $url->param('id', $id);
    }
	else{
        // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : selection.php'));
	}

    $url->param('mode', $mode);

	if (!isset($action) || empty($action)){
		$action='selectreferentiel'; // une seule action possible
	}
    
	// get parameters
	
    $params = new stdClass;
	$params->filtrerinstance = optional_param('filtrerinstance', 0, PARAM_BOOL);
    $params->localinstance = optional_param('localinstance', 0, PARAM_BOOL);
	// $params->globalinstance = optional_param('localinstance', 1, PARAM_BOOL);

    // get display strings
    $txt = new stdClass();
    $txt->referentiel = get_string('referentiel','referentiel');
	$txt->modulename = get_string('modulename','referentiel');
    $txt->modulenameplural = get_string('modulenameplural','referentiel');
//    $txt->onlyteachersselect = get_string('onlyteachersselect','referentiel');
    $txt->selectnoreferentiel = get_string('selectnoreferentiel', 'referentiel');
	$txt->selectreferentiel	= get_string('selectreferentiel','referentiel');
	$txt->selecterror_referentiel_id = get_string('selecterror_referentiel_id','referentiel');
	$txt->localinstance	= get_string('localinstance','referentiel');	
	$txt->choix_instance = get_string('choix_instance','referentiel');
	$txt->choix_filtrerinstance = get_string('choix_filtrerinstance','referentiel');
	$txt->choix_oui_filtrerinstance = get_string('choix_oui_filtrerinstance','referentiel');
	$txt->choix_non_filtrerinstance = get_string('choix_non_filtrerinstance','referentiel');
	$txt->choix_localinstance = get_string('choix_localinstance','referentiel');
	$txt->choix_globalinstance = get_string('choix_globalinstance','referentiel');
	$txt->select = get_string('select','referentiel');
	$txt->select2= get_string('filtrer','referentiel');
	$txt->cancel = get_string('quit','referentiel');	
	$txt->filtrerlocalinstance = get_string('filtrerlocalinstance','referentiel');
	$txt->pass	= get_string('pass_referentiel','referentiel');	

    $returnlink_ref = new moodle_url('/mod/referentiel/view.php', array('id'=>$cm->id, 'non_redirection'=>'1'));
    $returnlink_course = new moodle_url('/course/view.php', array('id'=>$course->id));
    $returnlink_add = new moodle_url('/mod/referentiel/add.php', array('d'=>$referentiel->id, 'sesskey'=>sesskey()));

    require_login($course->id, false, $cm);
    if (!isloggedin() || isguestuser()) {
        redirect($returnlink_course);
    }

    $context = context_course::instance($course->id);

    if ($referentiel->id) {    // So do you have access?
        if (!has_capability('mod/referentiel:select', $context) or !confirm_sesskey() ) {
            print_error(get_string('noaccess','referentiel'));
        }
    }
	else{
		print_error('Referentiel instance is incorrect');
	}


    // file upload form submitted
    if (!confirm_sesskey()) {
        	print_error( 'sesskey' );
    }
	
	// RECUPERER LES FORMULAIRES
    if (isset($SESSION->modform)) {   // Variables are stored in the session
        $form = $SESSION->modform;
        unset($SESSION->modform);
    }
    else {
        $form = (object)$_POST;
    }
	
	// Traitement des POST
	$msg="";
	
	if (!empty($course) && isset($form)) {   
		// select form submitted

		// variable d'action cancel
		if (!empty($form->cancel)){
			if ($form->cancel == get_string("quit", "referentiel")){
				// Abandonner
                if (!empty($SESSION->returnpage)) {
	            	$return = $SESSION->returnpage;
    		        unset($SESSION->returnpage);
   	        		redirect($return);
                }
                else {
    		      redirect($returnlink_course);
                }
                exit;
			}
		}
    }

	/// RSS and CSS and JS meta
    $meta = '';


    /// Mark as viewed  ??????????? A COMMENTER
    $completion=new completion_info($course);
    $completion->set_module_viewed($cm);

// AFFICHAGE DE LA PAGE Moodle 2
	/// Print the page header
	$strreferentiels = get_string('modulenameplural','referentiel');
	$strreferentiel = get_string('referentiel','referentiel');
	$strmessage = get_string('selectreferentiel','referentiel');
	$strpagename=get_string('modifier_referentiel','referentiel');
    $strlastmodified = get_string('lastmodified');
    $pagetitle = strip_tags($course->shortname.': '.$strreferentiel.': '.format_string($referentiel->name,true));
    $icon = $OUTPUT->pix_url('icon','referentiel');

    // affichage de la page
    $PAGE->set_url($url);
    $PAGE->requires->css('/mod/referentiel/referentiel.css');
    //if ($CFG->version < 2011120100) $PAGE->requires->js('/lib/overlib/overlib.js');  else
    $PAGE->requires->js($OverlibJs);
    $PAGE->requires->js('/mod/referentiel/functions.js');

    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);


    echo $OUTPUT->header();

    if (!empty($referentiel->name)){
        echo '<div align="center"><h1>'.$referentiel->name.'</h1></div>'."\n";
    }

    require_once('onglets.php'); // menus sous forme d'onglets 
    //require_once("onglets.php");
    //$tab_onglets = new Onglets($context, $referentiel, NULL, $cm, $course, 'list', $select_acc, $data_f);
    //$tab_onglets->display();

    echo '<div align="center"><h2><img src="'.$icon.'" border="0" title=""  alt="" /> '.$strmessage.' '.$OUTPUT->help_icon('selectreferentielh','referentiel').'</h2></div>'."\n";

    if (!empty($course) && isset($form)) {
		// variable d'action 
		if (empty($form->cancel) && !empty($form->action)){
			if ($form->action=="filtrerreferentiel"){
				// enregistre les modifications
				// $return=referentiel_update_referentiel($form);
				// $msg=get_string("referentiel", "referentiel")." ".$form->instance;
				// $action="update";
				if (isset($form->filtrerinstance)){
					$form->filtrerinstance = $form->filtrerinstance;
					if ($form->filtrerinstance!=0){
						if (isset($form->localinstance )){
							$params->localinstance = $form->localinstance;
						}
					}
				}
				else {
					$params->filtrerinstance = 0;
				}
				// mot de passe
				$params->referentiel_pass='';
				if (isset($form->givepass) && ($form->givepass=='1')){
					if (isset($form->referentiel_pass)){
						$params->referentiel_pass = md5($form->referentiel_pass);
					}
				}
			}
			if ($form->action=="selectreferentiel"){
				if (isset($form->referentiel_id) && ($form->referentiel_id>0)){
                    $form2 = $_POST;
                    // mot de passe
                    if (isset($form2['givepass_'.$form->referentiel_id])
                        && ($form2['givepass_'.$form->referentiel_id]=='1')){
                        if (isset($form2['referentiel_pass_'.$form->referentiel_id])){
                            $params->referentiel_pass = md5($form2['referentiel_pass_'.$form->referentiel_id]);
                        }
                    }
		  
					$new_referentiel_id=referentiel_filtrer($form->referentiel_id, $params);
	        // Verifier si  referentiel charge
					if (! $new_referentiel_id) {
    	            	// print_error( $new_referentiel_id , $returnlink_add);
						// PAS D'ERREUR on propose un autre choix
                    }
					else{
						echo "<hr />";
						// 
?>
<form name="form" method="post" action="add.php?id=<?php echo $cm->id; ?>">

<input type="hidden" name="name_instance" value="<?php  p($name_instance) ?>" />
<input type="hidden" name="description_instance" value="<?php  p($description_instance) ?>" />
<input type="hidden" name="label_domaine" value="<?php  p($label_domaine) ?>" />
<input type="hidden" name="label_competence" value="<?php  p($label_competence) ?>" />
<input type="hidden" name="label_item" value="<?php  p($label_item) ?>" />

<input type="hidden" name="new_referentiel_id" value="<?php  p($new_referentiel_id); ?>" />
<input type="hidden" name="action" value="<?php  p($action); ?>" />	

<input type="hidden" name="courseid"        value="<?php  p($course->id); ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()); ?>" />
<input type="hidden" name="instance"      value="<?php  echo $referentiel->id; ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode); ?>" />
<center>
<input type="submit" value="<?php  print_string("continue"); ?>" />
</center>
</form>
<?php
                        echo $OUTPUT->footer();
                        die();
					}
				}
			}
		}
	}
	
	//==========
    // DISPLAY
    //==========
 	
	$str_selection=referentiel_select_referentiels($params);
	if (empty($str_selection)){
?>
<p align="center"><?php  p($txt->selectnoreferentiel); ?></p>
<center>
<form name="form" method="post" action="add.php?id=<?php echo $cm->id; ?>">

<input type="hidden" name="name_instance" value="<?php  echo(($name_instance)); ?>" />
<input type="hidden" name="description_instance" value="<?php  echo(($description_instance)); ?>" />
<input type="hidden" name="label_domaine" value="<?php  echo(($label_domaine)); ?>" />
<input type="hidden" name="label_competence" value="<?php  echo(($label_competence)); ?>" />
<input type="hidden" name="label_item" value="<?php  echo(($label_item)); ?>" />

<input type="hidden" name="action" value="<?php  p($action); ?>" />	

<!-- These hidden variables are always the same -->
<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
<input type="hidden" name="courseid" value="<?php p($course->id); ?>" />
<input type="hidden" name="instance" value="<?php  echo $referentiel->id; ?>" />
<input type="hidden" name="mode" value="<?php  p($mode); ?>" />	
<input type="submit" value="<?php print_string('continue'); ?>" />
</form>
</center>
	<?php
		// print_error( $txt->selectnoreferentiel , $returnlink_add);
	}
	else {
    ?>


    <form id="form" enctype="multipart/form-data" method="post" action="selection.php?id=<?php echo $cm->id; ?>">
        <fieldset class="invisiblefieldset" style="display: block;">
            <?php
            echo $OUTPUT->box_start('generalbox  boxaligncenter');
            ?>
            <table cellpadding="5">
                <tr>					
                   <td align="left"><?php p( $txt->choix_filtrerinstance); ?></td>
				   <td>
<?php
		if (isset($params->filtrerinstance) && ($params->filtrerinstance!=0)){			   		   
			echo ('<input name="filtrerinstance" type="checkbox" checked="checked" />');
		}
		else {
			echo ('<input name="filtrerinstance" type="checkbox" />');	
		}
?>

					</td>
                </tr>
				<tr>
                   <td align="left"><?php p( $txt->filtrerlocalinstance); ?></td>
				   <td>
<?php

		if (isset($params->localinstance) && ($params->localinstance==0)){
			echo ('<input name="localinstance" type="radio" value="1" />'.$txt->choix_localinstance);
			echo (' <input name="localinstance" type="radio" value="0" checked="checked" />'.$txt->choix_globalinstance);
		}
		else {
			echo ('<input name="localinstance" type="radio" value="1" checked="checked" />'.$txt->choix_localinstance);	
			echo (' <input name="localinstance" type="radio" value="0" />'.$txt->choix_globalinstance);
		}
?>     

					</td>
                </tr>
                <tr>
                    <td>
<input type="submit" name="action" value="<?php echo( $txt->select2); ?>" />
					
<input type="submit" name="cancel" value="<?php echo( $txt->cancel); ?>" />					
					</td>
                </tr>
				
            </table>
            <?php
            echo $OUTPUT->box_end();
?>

<input type="hidden" name="name_instance" value="<?php  p($name_instance) ?>" />
<input type="hidden" name="description_instance" value="<?php  p($description_instance) ?>" />
<input type="hidden" name="label_domaine" value="<?php  p($label_domaine) ?>" />
<input type="hidden" name="label_competence" value="<?php  p($label_competence) ?>" />
<input type="hidden" name="label_item" value="<?php  p($label_item) ?>" />
		
<!-- These hidden variables are always the same -->
<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
<input type="hidden" name="courseid" value="<?php p($course->id); ?>" />
<input type="hidden" name="instance" value="<?php  echo $referentiel->id; ?>" />
<input type="hidden" name="mode" value="<?php  p($mode); ?>" />	
        </fieldset>
    </form>

	
    <form id="form" enctype="multipart/form-data" method="post" action="selection.php?id=<?php echo $cm->id; ?>">
        <fieldset class="invisiblefieldset" style="display: block;">
    <?php
			

            echo $OUTPUT->box_start('generalbox  boxaligncenter'); ?>
            <b><?php p( $txt->selectreferentiel); ?></b>
            <table cellpadding="5">
                <tr>
                    <td colspan="2"><?php echo $str_selection; ?></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>
<input type="submit" name="action" value="<?php echo( $txt->select); ?>" />
					
<input type="submit" name="cancel" value="<?php echo( $txt->cancel); ?>" />					
					</td>
                </tr>
            </table>
            <?php
            echo $OUTPUT->box_end(); ?>
<input type="hidden" name="action" value="<?php  p($action); ?>" />	

<input type="hidden" name="name_instance" value="<?php  p($name_instance) ?>" />
<input type="hidden" name="description_instance" value="<?php  p($description_instance) ?>" />
<input type="hidden" name="label_domaine" value="<?php  p($label_domaine) ?>" />
<input type="hidden" name="label_competence" value="<?php  p($label_competence) ?>" />
<input type="hidden" name="label_item" value="<?php  p($label_item) ?>" />
		
<!-- These hidden variables are always the same -->
<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
<input type="hidden" name="courseid" value="<?php p($course->id); ?>" />
<input type="hidden" name="instance" value="<?php  echo $referentiel->id; ?>" />
<input type="hidden" name="mode" value="<?php  p($mode); ?>" />	
        </fieldset>
    </form>
    <?php
    }
    echo $OUTPUT->footer();
    die();

?>
