<?php  // $Id: certificat.php,v 1.0 2008/05/03 00:00:00 jfruitet Exp $
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

    require(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');
    require_once("$CFG->libdir/graphlib.php");

//-------------
function somme_valeur($t_sommes, $t_valeurs){
    for ($i=0; $i<count($t_valeurs); $i++){
        if (isset($t_sommes[$i])){
            $t_sommes[$i]=$t_sommes[$i]+$t_valeurs[$i];
        }
        else{
            $t_sommes[$i]=$t_valeurs[$i];
        }
    }
    return $t_sommes;
}

//-------------
function quotient_valeur($t_sommes, $quotient){
    if ($quotient){
        for ($i=0; $i<count($t_sommes); $i++){
            $t_sommes[$i]=$t_sommes[$i]=(float) $t_sommes[$i] / (float) $quotient;
        }
    }
    return $t_sommes;
}


// -------------
function min_max1D($tableau1D){
    $min_max=  new stdClass;
    $min_max->min=1000000000.0;
    $min_max->max=-1000000000.0;
    for ($i=0; $i<count($tableau1D); $i++){
        if ($tableau1D[$i]>$min_max->max) $min_max->max=$tableau1D[$i];
        if ($tableau1D[$i]<$min_max->min) $min_max->min=$tableau1D[$i];
    }
    return $min_max;

}
//-------------
function decalage_valeur($t_valeurs, $delta){
// decale légèrement les valeurs du tableau pour éviter superpositon sur le graphe
    for ($i=0; $i<count($t_valeurs); $i++){
        $t_valeurs[$i]+=$delta;
    }
    return $t_valeurs;
}

// ######################################## DEBUT
    $id    = optional_param('id', 0, PARAM_INT);    // course module id
    $d     = optional_param('d', 0, PARAM_INT);    // referentielbase id

    $titre   = optional_param('titre', '', PARAM_TEXT);    // course module id
    $affcomplet = optional_param('affcomplet', 1, PARAM_INT);
    $page = optional_param('page', 0, PARAM_INT);
    $npages = optional_param('npages', 0, PARAM_INT);

    // DEBUG
    // print_object($data_filtre);
    // exit;
    // nouveaute Moodle 1.9 et 2
    $url = new moodle_url('/mod/referentiel/graph_certificats.php');

	if ($d) {     // referentiel_referentiel_id
        if (! $referentiel = $DB->get_record("referentiel", array("id" => "$d"))) {
            print_error('Referentiel instance is incorrect');
        }
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Réferentiel id is incorrect');
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
        if (! $referentiel_referentiel = $DB->get_record("referentiel_referentiel", array("id" => "$referentiel->ref_referentiel"))) {
            print_error('Referentiel is incorrect');
        }
        $url->param('id', $id);
    }
	else{
    // print_error('You cannot call this script in that way');
		print_error(get_string('erreurscript','referentiel','Erreur01 : certificat.php'), 'referentiel');
	}

    // Valable pour Moodle 2.1 et Moodle 2.2
    //if ($CFG->version < 2011120100) {
        $context = context_module::instance($cm->id);
    //} else {
        // $context = context_module::instance($cm);
    //}


    require_login($course->id, false, $cm);   // pas d'autologin guest

    if (!isloggedin() or isguestuser()) {
        redirect($CFG->wwwroot.'/mod/referentiel/view.php?id='.$cm->id.'&amp;non_redirection=1');
    }

    $username=referentiel_get_user_nom($USER->id).' '.referentiel_get_user_prenom($USER->id);

    if ($titre){     // hack
        $titre=str_replace('_',' ',$titre);
    }
    if ($page){     // hack
        $titre=$titre.' (Page '.$page.'/'.$npages.' - '.date("Y/m/d").')';
    }

    $t_datas=NULL;
    $moyennes=NULL;
    $t_code=NULL;
    $t_empreinte=NULL;
    $t_poids=NULL;

    if (isset($_POST['lcode'])){
        $lcode=$_POST['lcode'];
    }
    else if (isset($_GET['lcode'])){
        $lcode=$_GET['lcode'];
    }

    if (isset($_POST['lempreinte'])){
        $lempreinte=$_POST['lempreinte'];
    }
    else if (isset($_GET['lempreinte'])){
        $lempreinte=$_GET['lempreinte'];
    }

    if (isset($_POST['lpoids'])){
        $lpoids=$_POST['lpoids'];
    }
    else if (isset($_GET['lpoids'])){
        $lpoids=$_GET['lpoids'];
    }

    if (isset($_POST['ltdata'])){
        $ltdata=$_POST['ltdata'];
    }
    else if (isset($_GET['ltdata'])){
        $ltdata=$_GET['ltdata'];
    }
    if (isset($_POST['lmoyennes'])){
        $lmoyennes=$_POST['lmoyennes'];
    }
    else if (isset($_GET['lmoyennes'])){
        $lmoyennes=$_GET['lmoyennes'];
    }

/*
            // DEBUG
        echo "<br />DEBUG  :: graph_certificat_v2.php :: 98 :: CODES<br />\n";
        echo($lcode);
        // DEBUG
        echo "<br />DEBUG  :: graph_sertificat_v2.php :: 101 :: EMPREINTES<br />\n";
        echo($lempreinte);
        // DEBUG
        echo "<br />DEBUG  :: graph_sertificat_v2.php :: 104 :: POIDS<br />\n";
        echo($lpoids);
        // DEBUG
        echo "<br />DEBUG  :: graph_sertificat_v2.php :: 110 :: MOYENNES<br />\n";
        echo($lmoyennes);

        // DEBUG
        echo "<br />DEBUG  :: graph_sertificat_v2.php :: 107 :: DATA<br />\n";
        echo($ltdata);

        //exit;
*/

    // Let's get the datas
    $separateur1='/';
    $separateur2=':';
    $separateur3='|';

    if (!empty($ltdata) && !empty($lmoyennes) && !empty($lcode) && !empty($lempreinte) && !empty($lpoids)){

        $t_code     = explode($separateur1, $lcode);
        $t_empreinte= explode($separateur1, $lempreinte);
        $t_poids    = explode($separateur1, $lpoids);
        $moyennes   = explode($separateur1, $lmoyennes);

        $t_data1   = explode($separateur3, $ltdata);
        // DEBUG
        //echo "<br />DEBUG  :: graph_certificat_v2.php :: 130 :: DATA<br />\n";
        //print_r($t_data1);

        foreach($t_data1 as $data1){
            if ($data1){
                $t_data2= explode($separateur2, $data1);
                // print_r($t_data2);
                $t_datas[$t_data2[0]]=$t_data2[1];
            }
        }
        /*
        // DEBUG
        echo "<br />DEBUG  :: graph_certificat_v2.php :: 141 ::<br />CODES<br />\n";
        print_r($t_code);
        // DEBUG
        echo "<br />EMPREINTES<br />\n";
        print_r($t_empreinte);
        // DEBUG
        echo "<br />POIDS<br />\n";
        print_r($t_poids);
        // DEBUG
        echo "<br />MOYENNES<br />\n";
        print_r($moyennes);

        // DEBUG
        echo "<br />DATA<br />\n";
        print_r($t_datas);
        */


        $min_max_empreinte = min_max1D($t_empreinte);
        $min_max_poids = min_max1D($t_poids);
        $coef_reduction_poids= ($min_max_poids->max / $min_max_empreinte->max);
        // reduction
        $t_poids=quotient_valeur($t_poids, $coef_reduction_poids);

        //
        $nbuser=count($t_datas);


        // Draw it now
        $colors=array(
'blue',
'fuchsia',
'green',
'lime',
'orange',
'olive',
'purple',
'aqua',
'navy',
'gray33',
'maroon',
'grayCC'
);
        $maxcolors=12;
        $points=array('square-open',
'circle-open',
'diamond-open',
'circle-open',
'triangle-open'
);
        $maxpoints=5;

        $color_outer_background = 'none';
        $color_inner_background = 'none';
        $color_inner_border = 'black';
        $color_axis_colour = 'black';

        //
        $deltay= min(max($nbuser,6),10);
        $coef =  0.60 / $deltay;

        $graphwidth = 1024;
        $graphheight = $deltay * 80;
        $graph = new graph($graphwidth, $graphheight);


        $graph->x_data           	= $t_code;
        $graph->y_data['poids']   	= $t_poids;
        $graph->y_format['poids'] = array('colour' => 'black', 'line' => 'line', 'point' => 'diamond', 'point_size' => 6);
        $graph->y_data['seuil']   	= $t_empreinte;
        $graph->y_format['seuil'] 	= array('colour' => 'red', 'line' => 'line', 'point' => 'circle-open', 'point_size' => 6, 'legend' => get_string('seuil', 'referentiel')); // array('colour' => $colorb, 'bar' => 'fill','bar_size' => 0.6);
        $graph->y_order[]='moyenne';       // premier affiche
        $graph->y_order[]='poids';      // second affiche
        $graph->y_order[]='seuil';

        $n=0;
        foreach($t_datas as $key=>$values){
            if ($values){
                $t_data= explode($separateur1, $values);
                // print_r($t_data);

                // confidentialite pour les etudiants
                if ($affcomplet) {
                    $legende=$key;
                }
                else {
                    if ($username!=$key){
                        $legende=$n;
                    }
                    else{
                        $legende=$key;
                    }
                }
                // leger decalage
                $valeurs=decalage_valeur($t_data, $n*$coef);
                $graph->y_data['valide_'.$key]   = $valeurs;
                $graph->y_order[]='valide_'.$key;
                if ($username==$key){
                        $color_1='red';
                        $point_1='square';
                        $psize=10;
                }
                else{
                    $color_1=$colors[$n%$maxcolors];
                    $point_1=$points[$n%$maxpoints];
                    $psize=10;
                }
                $graph->y_format['valide_'.$key]   = array('colour' => $color_1, 'point' => $point_1, 'point_size' => $psize, 'legend' => $legende);
                //    $graph->y_format['declaree_'.$t_user[$i]] = array('colour' => $color_2, 'line' => 'line');
                $n++;
            }
        }

        // decalage position
        $moyennes=decalage_valeur($moyennes, $n*$coef);
        $graph->y_data['moyenne']  =   $moyennes;

        // $graph->y_format['moyenne'] = array('colour' => 'yellow', 'line' => 'brush', 'brush_size' => 2, 'shadow_offset' => 4, 'legend' => 'Moyenne');
        $graph->y_format['moyenne'] = array('colour' => 'yellow', 'line' => 'line', 'point' => 'circle', 'point_size' => 8, 'legend' => get_string('moyenne', 'referentiel'));
            // $graph->y_format['moyenne'] = array('colour' => 'yellow', 'bar' => 'fill', 'point' => 'circle', 'point_size' => 8, 'legend' => get_string('moyenne', 'referentiel'));

        $graph->parameter['legend']        = 'outside-top';  // default. no legend.
                // otherwise: 'top-left', 'top-right', 'bottom-left', 'bottom-right',
                //   'outside-top', 'outside-bottom', 'outside-left', or 'outside-right'.

        $graph->parameter['legend_border'] = 'black';
        $graph->parameter['legend_offset'] = 4;

        $graph->parameter['title'] =$titre;
        $graph->parameter['x_label'] = get_string('code_items', 'referentiel');
        $graph->parameter['y_label_left'] = get_string('competences_graphe', 'referentiel');
        $graph->parameter['y_resolution_left']= 1;
        $graph->parameter['y_decimal_left']= 1;
        $graph->parameter['y_label_right']     = get_string('poids_graphe', 'referentiel');

        $graph->parameter['y_axis_gridlines']  =  5;
            // $graph->parameter['y_axis_text_right'] =  2;  //print a tick every 2nd grid line

        $graph->parameter['label_size']		= '10';
        $graph->parameter['x_axis_angle']		= 45;
        $graph->parameter['x_label_angle']  	= 0;
        $graph->parameter['inner_padding']  	= 8;

        $graph->parameter['y_min_left']  	= $min_max_empreinte->min-0.25;
        $graph->parameter['y_max_left']  	= $min_max_empreinte->max+0.25;
        $graph->parameter['y_grid']  	          = 'line'; //'dash'
        $graph->parameter['x_grid']  	          = 'line';

        $graph->parameter['tick_length'] 		= 10;
        $graph->parameter['outer_background'] 	= $color_outer_background;
        $graph->parameter['inner_background'] 	= $color_inner_background;
        $graph->parameter['inner_border'] 		= $color_inner_border;
        $graph->parameter['outer_border']  	    = $color_inner_border;
        $graph->parameter['axis_colour'] 		= $color_axis_colour;
        $graph->parameter['shadow']         	= 'none';
        // $graph->parameter['brush_size'] = 4;

        //error_reporting(0); // ignore most warnings such as font problems etc
        error_reporting(5);
        $graph->draw_stack();
            //$graph->draw();
    }

?>