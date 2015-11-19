<?php

// -----------------------
function referentiel_liste_autres_baremes($course, $cm, $context, $roles, $idbaremeexclus=0, $selection=0){
// idbaremeexclus : celui à ne pas afficher
global $DB;
global $OUTPUT;
global $CFG;
	$ok=0;
    $strscales     = get_string('newotherbaremes','referentiel');
    $strscale      = get_string('newbareme','referentiel');
    $strname       = get_string('name');
    $strdescription= get_string('description');
    $strthreshold  = get_string('seuil','referentiel');    
    $strselect         = get_string('select');


    $table = new html_table();
	if ($selection){
    	$table->head  = array($strscale, $strdescription, $strthreshold, $strselect);
    	$table->size  = array('20%', '60%', '10%', '10%');
    	$table->align = array('left', 'left', 'center', 'center');
	}
	else{    
    	$table->head  = array($strscale, $strdescription, $strthreshold);
    	$table->size  = array('20%', '70%', '10%');
    	$table->align = array('left', 'left', 'center');
    }
	$table->attributes['class'] = 'scaletable localscales generaltable';

    $heading = $strscales;

    $data = array();
    $line = array();
    
    if ($baremes=$DB->get_records('referentiel_scale',NULL)){
        foreach ($baremes as $bareme){
        	if (empty($idbaremeexclus) || (!empty($idbaremeexclus) && ($idbaremeexclus!=$bareme->id))){
                $line = array();
				$ok++;
        		//print_object($bareme);
        		$tscales=array();
				$ticons=array();
				$tlabels=array();
        		$thresholdlabel=0;
        		if ($tscales=explode(',',$bareme->scale)){
        			$thresholdlabel=$tscales[$bareme->threshold];
        		}                                                                        
			
				$strgrades='';
				$strgrades='<div class="scale_options">'.str_replace(",",", ",$bareme->scale).'</div>';

        		if ($bareme->labels){
					$tlabels=explode(',',$bareme->labels);
        		}

				$stricons='<b>'.get_string('grades').'</b><br />';
				// $stricons=str_replace(",",", ",$bareme->icons);
        	
        		if ($bareme->icons){
					if ($ticons=explode(',',$bareme->icons)){
						while (list($key, $val) = each($ticons)) {
            				if ($val){
                				if ($key>=$bareme->threshold){
                    				$stricons.='<span class="valide">';
	                    		}
    	                		else{
									$stricons.='<span class="invalide">';
								}
            					if (!empty($tlabels) && !empty($tlabels[$key])){
									$stricons.=$tlabels[$key].' ';
								}
								else{
									$stricons.=$tscales[$key].' ';						            					
        	    				}
            	       			$stricons.='</span>';
            					$stricons.=' &nbsp; '.$val;
                    			$stricons.='<br />';
                    		}
						}
					}
				}				
	        	$stricons='<div class="scale_options">'.$stricons.'</div>'."\n";       				
        
	        	$line[] = '<b>'.format_string($bareme->name).'</b>'.$strgrades.$stricons;
			    $line[] = $bareme->description;
				$line[] = $thresholdlabel;
				if ($selection){
			        $menu = "";
        			if (has_capability('mod/referentiel:writereferentiel', $context)) {
            			$menu.= '<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?id='.$cm->id.'&amp;scaleid='.$bareme->id.'&amp;mode=selectbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
				        $line[] = $menu;
        			}			
				}
        		$data[] = $line;
    	    }   
    	}
		if ($ok){
	    	$table->data  = $data;
    		echo $OUTPUT->heading($strscales.' '.$OUTPUT->help_icon('baremeechangeh','referentiel'), 3, 'main');
    		echo html_writer::table($table);
    	}
	}
	else{
		echo '<div align="center">'.get_string('no_scales','referentiel').'</div>'."\n";
	}

}


// -----------------------
function referentiel_affiche_bareme($bareme){
global $DB;
global $OUTPUT;
global $CFG;
    $strscales	= get_string('newbaremes','referentiel');
    $strscale	= get_string('newbareme','referentiel');
    $strname	= get_string('name');
    $strdescription= get_string('description');
    $strthreshold = get_string('seuil','referentiel');
    
    $table = new html_table();
    $table->head  = array($strscale, $strdescription, $strthreshold);
    $table->size  = array('20%', '70%', '10%');
    $table->align = array('left', 'left', 'center');
    $table->attributes['class'] = 'scaletable localscales generaltable';

    $heading = $strscales;

    $data = array();
    $line = array();

    if (!empty($bareme)){
        //print_object($bareme);
        $tscales=array();
		$ticons=array();
		$tlabels=array();
        $thresholdlabel=0;
        if ($tscales=explode(',',$bareme->scale)){
        	$thresholdlabel=$tscales[$bareme->threshold];
        }
		$strgrades='';
		$strgrades='<div class="scale_options">'.str_replace(",",", ",$bareme->scale).'</div>';

       	if ($bareme->labels){
			$tlabels=explode(',',$bareme->labels);
       	}

		$stricons='<b>'.get_string('grades').'</b><br />';
		// $stricons=str_replace(",",", ",$bareme->icons);
        	
       	if ($bareme->icons){
			if ($ticons=explode(',',$bareme->icons)){
				while (list($key, $val) = each($ticons)) {
           			if ($val){
               			if ($key>=$bareme->threshold){
                   			$stricons.='<span class="valide">';
	                   	}
                    	else{
							$stricons.='<span class="invalide">';
						}
           				if (!empty($tlabels[$key])){
							$stricons.=$tlabels[$key].' ';
						}
						else{
							$stricons.=$tscales[$key].' ';
       	    			}
           	       		$stricons.='</span>';
           				$stricons.=' &nbsp; '.$val;
                   		$stricons.='<br />';
					}
				}
			}
        }
				
       	$stricons='<div class="scale_options">'.$stricons.'</div>'."\n";
        
       	$line[] = '<b>'.format_string($bareme->name).'</b>'.$strgrades.$stricons;
	    $line[] = $bareme->description;
		$line[] = $thresholdlabel;

        $data[] = $line;

    }

    $table->data  = $data;
    echo $OUTPUT->heading($strscale, 3, 'main');
    echo html_writer::table($table);

}

// -----------------------
function referentiel_modifier_bareme($mode, $courseid, $cmid, $bareme, $occurrence_id, $detail=false){
global $DB;
global $CFG;
$strscale        = get_string('bareme_utilise','referentiel');
$strname         = get_string('name');
$stroccurrence   = get_string('occurrence','referentiel');
$strmsg          = get_string('modif_bareme','referentiel');
 
    if ($occurrence_id){
           if (!$occurence = $DB->get_record('referentiel_referentiel', array('id'=>$occurrence_id))){
                print_error();
                exit;
            }
    }
    if (!empty($bareme)){
        // DEBUG
        //echo "<br />DEBUG :: lib_bareme.php :: 208 :: BAREME UTILISE<br />\n";
        //print_object($bareme);
        //echo "<br />\n";
		$tscales=explode(',',$bareme->scale);
        $ticons=explode(',',$bareme->icons);
        $tlabels=explode(',',$bareme->labels);

		echo '<form name="form" method="post" action="bareme.php?id='.$cmid.'"> '."\n";
		echo '<h3 align="center"><br />'.$strmsg.'</h3>'."\n";
		echo '<div class="ref_saisie1">'."\n";
		echo '<span class="bold">'.$strname.'</span>'.$bareme->name;
		if ($detail){
			echo ' <span class="bold">'.get_string('description','referentiel').'</span>
<div class="ref_aff1">'.$bareme->description.'</div>'."\n";
			echo '	<span class="bold">'.get_string('grades').'</span>'.$bareme->scale."<br />\n";
		}
		echo '<span class="bold">'.get_string('aide_saisie_seuil','referentiel').'</span>'."\n";
		echo '<br />'."\n";
	    echo '<select name="seuilid" id="seuilid" size="4">'."\n";
        //echo '<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";

        while (list($key, $val) = each($tscales)) {
            // echo "$key => $val\n";
            if (isset($val)){
                if ($key==$bareme->threshold){
                    echo '<option value="'.$key.'" selected="selected">'.$val.'</option>'."\n";
                }
                else{
                   echo '<option value="'.$key.'" >'.$val.'</option>'."\n";
                }
            }
        }
        echo '</select><br />'."\n";
        reset($tscales);
        echo '<br /><span class="bold">'.get_string('aide_saisie_icones','referentiel').'</span>'."\n";
        echo '<br />'."\n";
        //echo '<option value="0" selected="selected">'.get_string('choisir', 'referentiel').'</option>'."\n";

        while (list($key, $val) = each($tscales)) {
            // echo "$key => $val\n";
            if (isset($val)){
                if (!empty($ticons[$key])){
	               echo ' <input type="text" size="60" maxlength="255" name="iconscale[]" value="'.htmlspecialchars($ticons[$key]).'" />'.$val.'<br />'."\n";
                }
                else{
                    if ($key<$bareme->threshold){
	                    echo ' <input type="text" size="60" maxlength="255" name="iconscale[]" value="'.htmlspecialchars(get_string('label_rouge','referentiel')).'" />'.' '.get_string('label_rouge','referentiel').' '.$val.'<br />'."\n";
                    }
                    else{
	                    echo ' <input type="text" size="60" maxlength="255" name="iconscale[]" value="'.htmlspecialchars(get_string('label_vert','referentiel')).'" />'.' '.get_string('label_vert','referentiel').' '.$val.'<br />'."\n";
                    }
                }
            }
        }
        if (($mode=='modifbareme') || ($mode=='editbareme')) {
			echo '<input type="hidden" name="scaleid" value="'.$bareme->scaleid.'" />'."\n";
		}
		elseif (isset($bareme->id)){
			echo '<input type="hidden" name="baremeid" value="'.$bareme->id.'" />'."\n";		
		}
?>
<br />
<input type="hidden" name="action"  value="<?php p($mode) ?>" />
<input type="hidden" name="ref_referentiel" value="<?php  p($occurrence_id) ?>" />

<!-- These hidden variables are always the same -->
<input type="hidden" name="courseid"        value="<?php  p($courseid) ?>" />
<input type="hidden" name="sesskey"     value="<?php  p(sesskey()) ?>" />
<input type="hidden" name="mode"          value="<?php  p($mode) ?>" />
<input type="submit" value="<?php  print_string("savechanges") ?>" />
<input type="reset" value="<?php  print_string("cancel") ?>" />
<input type="submit" name="cancel" value="<?php  print_string("quit","referentiel") ?>" />

</form>
<?php

    }
}

// -----------------------
function referentiel_affiche_bareme_occurrence($occurrence_id, $course, $cm, $context, $roles){
global $DB;
global $OUTPUT;
global $CFG;
$strscale          = get_string('bareme_utilise','referentiel');
$strname           = get_string('name');
$stroccurrence     = get_string('occurrence','referentiel');
$strcustomscales   = get_string('scalescustom');
$strdescription= get_string('description');
$strthreshold = get_string('seuil','referentiel');
$strmenu = get_string('editscale','referentiel');

$table = new html_table();

    if ($occurrence_id){
        if (!$occurrence = $DB->get_record('referentiel_referentiel', array('id'=>$occurrence_id))){
        	print_error('Referentiel occurrence is incorrect');
            exit;
        }

        $params= array('occurrenceid'=>$occurrence->id);
        $sql="SELECT s.* FROM {referentiel_a_scale_ref} AS a, {referentiel_scale} AS s
          WHERE a.refscaleid = s.id
          AND a.refrefid=:occurrenceid ";
        if ($bareme=$DB->get_record_sql($sql, $params)){
            // DEBUG
            //echo "<br />DEBUG :: lib_bareme.php :: 195 :: BAREME UTILISE";
            //print_object($bareme);
            $heading = $strscale;

            $data = array();
            $line = array();
        	//print_object($bareme);
        	$tscales=array();
			$ticons=array();
			$tlabels=array();
        	$thresholdlabel=0;
        	if ($tscales=explode(',',$bareme->scale)){
        		$thresholdlabel=$tscales[$bareme->threshold];
        	}
			$strgrades='';
			$strgrades='<div class="scale_options">'.str_replace(",",", ",$bareme->scale).'</div>';

        	if ($bareme->labels){
				$tlabels=explode(',',$bareme->labels);
        	}

			$stricons='<b>'.get_string('grades').'</b><br />';
			// $stricons=str_replace(",",", ",$bareme->icons);
        	
        	if ($bareme->icons){
				if ($ticons=explode(',',$bareme->icons)){
					while (list($key, $val) = each($ticons)) {
            			if ($val){
                			if ($key>=$bareme->threshold){
                    			$stricons.='<span class="valide">';
	                    	}
    	                	else{
								$stricons.='<span class="invalide">';
							}
            				if (!empty($tlabels[$key])){
								$stricons.=$tlabels[$key].' ';
							}
							else{
								$stricons.=$tscales[$key].' ';						            					
        	    			}
            	       		$stricons.='</span>';
            				$stricons.=' &nbsp; '.$val;
                    		$stricons.='<br />';
						}
					}
				}
    	    }
				
        	$stricons='<div class="scale_options">'.$stricons.'</div>'."\n";       
        
        	$line[] = '<b>'.format_string($bareme->name).'</b>'.$strgrades.$stricons;
		    $line[] = $bareme->description;
			$line[] = $thresholdlabel;
			$menu = "";
        	if (has_capability('mod/referentiel:writereferentiel', $context)) {
            	$menu.= '<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?id='.$cm->id.'&amp;baremeid='.$bareme->id.'&amp;mode=reeditbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('edit','referentiel').'" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            	$menu.= '<br />'.'<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?id='.$cm->id.'&amp;scaleid='.$bareme->id.'&amp;mode=deletebareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('deleteall','referentiel').'" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
				$menu.= '<br />'.$OUTPUT->help_icon('deletescaleh','referentiel');
				$line[] = $menu;
        	}			

            $data[] = $line;
		    if (!empty($menu)){
				$table->head  = array($strscale, $strdescription, $strthreshold, $strmenu);
		    	$table->size  = array('30%', '50%', '5%', '15%');
		    	$table->align = array('left', 'left', 'center', 'center');
			}
			else{
				$table->head  = array($strscale, $strdescription, $strthreshold);
		    	$table->size  = array('30%', '60%', '10%');
		    	$table->align = array('left', 'left', 'center');
		    }
			$table->attributes['class'] = 'scaletable localscales generaltable';
            $table->data  = $data;

            echo $OUTPUT->heading($strscale, 3, 'main');
            echo html_writer::table($table);
            return $bareme->id;
        }
        else{
			echo '<div align="center">'.get_string('no_scale','referentiel',$occurrence->code_referentiel).'</div>'."\n";
		}
    }
    return 0;
}


/**
 * return  borrowed from a scale record
 *
 * @param $scale data submitted
 * @param $occurrence_id as referentiel_referentiel id
 * @return Object
 */
// -----------------------
function referentiel_scale_2_bareme($scale){
    if (!empty($scale)){
        $bareme= new Object();
        $bareme->scaleid=$scale->id;
        $bareme->name=$scale->name;
        $bareme->scale=$scale->scale;
        $bareme->maxscale=0;      
        $bareme->threshold=$bareme->maxscale;
        if ($ts=explode(',',$scale->scale)){
			$bareme->maxscale=count($ts)-1;        
            if ($bareme->maxscale>2){
                $bareme->threshold= (int) (ceil($bareme->maxscale/2)) ;
            }
            else{
                $bareme->threshold=$bareme->maxscale;
            }
        }
        $bareme->description=$scale->description;
        $bareme->descriptionformat=$scale->descriptionformat;
        $bareme->icons='';
        $bareme->labels=$scale->scale;
        $bareme->timemodified=time();
                
        return $bareme;
    }
    return NULL;
}


/**
 * update referentiel_scale record
 * and manage old evaluations if nusefull
 *
 * @param $bareme data submitted
 * @param $occurrence_id as referentiel_referentiel id
 * @return false or referentiel_scale id
 */

// -----------------------
function referentiel_set_bareme_occurrence($bareme, $occurrence_id){
global $DB;
    $ok=false;
	if (!empty($bareme) && !empty($occurrence_id)){
        // DEBUG
		//echo "<br /> lib_bareme.php :: 288:: OCCURRENCE: $occurrence_id<br />\n";
		//print_object($bareme);
		//echo "<br />\n";    
        if ($rec=$DB->get_record('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id))){
            // une association avec cette occurrence
            if (!empty($rec->refrefid)){
				if ($baremeold=$DB->get_record('referentiel_scale', array('id'=>$rec->refscaleid))){
                	// Modifier tous les usages actuels du bareme...
                	referentiel_regenere_graduation($baremeold, $bareme, $rec->refrefid);
                	// Mettre à jour le bareme associ au referentiel
                	$rec->refscaleid=$bareme->id;
                	$rec->timemodified=time();
                	$ok=$DB->update_record('referentiel_a_scale_ref', $rec);
            	}
			}
        }
		if (!$ok){	// Pas d'association : il faut la creer
            $rec=new Object();
            $rec->refscaleid=$bareme->id;
            $rec->refrefid=$occurrence_id;
            $rec->timemodified=time();
            $ok=$DB->insert_record('referentiel_a_scale_ref', $rec);
        }
    }
    return $ok;
}

/**
 * update referentiel_a_scale_ref record
 * and manage old evaluations if usefull
 *
 * @param $baremeid as referentiel_scale id
 * @param $occurrence_id as referentiel_referentiel id
 * @return false or referentiel_scale id
 */
 
// -----------------------
function referentiel_echange_bareme_occurrence($baremeid, $occurrence_id){
global $DB;
    $ok=false;
	//echo "<br /> lib_bareme.php :: 470:: BAREMEID : $baremeid, OCCURRENCE: $occurrence_id<br />\n";
    
	if (!empty($baremeid) && !empty($occurrence_id)){
	  	$bareme=$DB->get_record('referentiel_scale', array('id'=>$baremeid));	
        // DEBUG
		//echo "<br /> lib_bareme.php :: 288:: OCCURRENCE: $occurrence_id<br />\n";
		//print_object($bareme);
		//echo "<br />\n";    
        //exit;
		if ($rec=$DB->get_record('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id))){
            // une association avec cette occurrence
            if (!empty($rec->refrefid)){
				if ($baremeold=$DB->get_record('referentiel_scale', array('id'=>$rec->refscaleid))){
                	// Modifier tous les usages actuels du bareme...
                	referentiel_regenere_graduation($baremeold, $bareme, $rec->refrefid);
                	// Mettre à jour le bareme associ au referentiel
                	$rec->refscaleid=$bareme->id;
                	$rec->timemodified=time();
                	$ok=$DB->update_record('referentiel_a_scale_ref', $rec);
            	}
			}
        }
		if (!$ok){	// Pas d'association : il faut la creer
            $rec=new Object();
            $rec->refscaleid=$bareme->id;
            $rec->refrefid=$occurrence_id;
            $rec->timemodified=time();
            $ok=$DB->insert_record('referentiel_a_scale_ref', $rec);
        }
    }
    return $ok;
}


// -----------------------
function referentiel_regrade($val, $oldseuil, $newseuil, $oldmax, $newmax) {
	if ($val==$oldseuil){
        return $newseuil;
    }
    else if ($val<$oldseuil){
		if ($oldseuil>0){
        	return ceil($val * ($newseuil / $oldseuil));
		}
		else{
			return 0;
		} 
    }
    else{
		if ($oldmax>$oldseuil) {
			// recalculer une position lineaire		
			return ceil($val * ($newmax-$newseuil) / ($oldmax-$oldseuil)) + $newseuil; 
		}
		else{
			return $newmax; 				
		}
	}
}


/**
 * update all graduations with the new scale for an occurrence
 * and manage old evaluations if usefull
 *
 * @param $oldbareme data submitted
 * @param $bareme data submitted
 * @param $occurrence_id as referentiel_referentiel id
 * @return nothing
 */

// -----------------------
function referentiel_regenere_graduation($oldbareme, $bareme, $occurrence_id){
global $DB;
    if (!empty($occurrence_id)){
        $params= array('refrefid'=>$occurrence_id, 'oldbaremeid'=>$oldbareme->id);
        $sql= "SELECT s.* FROM {referentiel_activite_scale} as s, {referentiel_activite} as a
 WHERE a.ref_referentiel=:refrefid
 AND s.refscaleid=:oldbaremeid
 AND s.activiteid=a.id
 AND s.competences_bareme!='' ";
        if ($recs=$DB->get_records_sql($sql, $params)){
            foreach ($recs as $rec){
                // echo "<br />DEBUG :: lib_bareme.php :: 123 :: <br />\n";
                // print_object($rec);         
                // echo "<br />\n";
				// recalculer la graduation
                $newbareme='';
                $oldliste=$rec->competences_bareme;
                $titem=explode('/',$oldliste);
                foreach ($titem as $itembareme){
                    if (!empty($itembareme)){
                    	//echo "<br />$itembareme<br>\n";
                        $tcomp=explode(":", $itembareme);
                        if ($tcomp){
							//echo "$tcomp[0] => $tcomp[1]<br>\n";
                            if (isset($tcomp[0]) && isset($tcomp[1])){
                                $newval=referentiel_regrade($tcomp[1], $oldbareme->threshold, $bareme->threshold, $oldbareme->maxscale, $bareme->maxscale);
                                $newbareme.=$tcomp[0].":".$newval."/";
                            }
                        }
                    }
                }
                //echo "<br />DEBUG :: lib_bareme.php :: 586 :: <br />$newbareme\n";
                
				// enregistrer la modification
                $DB->set_field('referentiel_activite_scale','competences_bareme',$newbareme, array('id'=>$rec->id));
                $DB->set_field('referentiel_activite_scale','refscaleid',$bareme->id, array('id'=>$rec->id));
            }
        }
    }
}

// -----------------------
function referentiel_get_assoc_bareme_occurrence($occurrence_id){
	global $DB;
	if (!empty($occurrence_id)){
		return $DB->get_record('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id));
	}
	return NULL;
}
	
// -----------------------
function referentiel_get_bareme_id_occurrence($occurrence_id){
	global $DB;
	if (!empty($occurrence_id)){
		if ($rec=$DB->get_record('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id))){
			return $rec->refscaleid;
		}
	}
	return 0;
}



// -----------------------
function referentiel_get_bareme($bareme_id){
global $DB;
    if (!empty($bareme_id)){
        if ($rec_bareme= $DB->get_record('referentiel_scale', array('id'=> $bareme_id))){
			return $rec_bareme;
		}
	}
	return NULL;
}

// -----------------------
function referentiel_get_competences_activite($activite_id, $bareme_id){
global $DB;
    if (!empty($bareme_id) && !empty($activite_id)){
        if ($rec_activite= $DB->get_record('referentiel_activite_scale', array('activiteid'=>$activite_id, 'refscaleid'=> $bareme_id))){
			// DEBUG
			// echo "<br />Lib_bareme.php :: 606 ::<br />\n";
			// print_object($rec_activite);
			return $rec_activite->competences_bareme;
		}
	}
	return '';
}

/**
 * display scale evaluation for an activity
 *
 * @param $bareme referentiel_scale record
 * @param $liste :  // A.1-1:0/A.1-2:2/A.1-3:1/A.1-4:0/A.1-5:0
 * @return string
 */
// -----------------------
function referentiel_affiche_bareme_activite($liste, $bareme, $detail=true){
// La liste est au format
// codeItem1:indexbareme/codeItem2:indexbareme...
// A.1-1:0/A.1-2:2/A.1-3:1/A.1-4:0/A.1-5:0/A.2-1:0/A.2-2:0/A.2-3:0/A.3-1:0/A.3-2:0/A.3-3:0/A.3-4:0/B.1-1:0/B.1-2:0/B.1-3:0/B.2-1:0/B.2-2:0/B.2-3:0/B.2-4:0/B.2-5:0/B.3-1:0/B.3-2:0/B.3-3:0/B.3-4:0/B.3-5:0/B.4-1:0/B.4-2:0/B.4-3:0/
// avec un bareme [NonPertinent, NonValidé, Validé] cela corrspond à
// A.1-1:NonPertinent
// A.1-2:Validé
// A.1-3:NonValidé
// etc.
	
	$s='';
	if (!empty($liste) && !empty($bareme)){
		$tscales=explode(',',$bareme->scale);
		$ticons=explode(',',$bareme->icons);
		$tlabels=explode(',',$bareme->labels);
		$seuil=$bareme->threshold;
		
		// afficher
		if ($tcodes=explode('/',$liste)){
			while (list($key, $value) = each($tcodes)) {
				if (!empty($value)){
       				//echo "<br>$key => $value ; \n";
					if ($titem=explode(':',$value)){
						if (isset($titem[1])){
							$codeitem=$titem[0];
							$grade=$titem[1];
                            if (isset($tlabels[$grade])){
								$label=$tlabels[$grade];
							}
							else{
                                $label=get_string('inconnu','referentiel');
							}
                            if (isset($ticons[$grade])){
								$icon=$ticons[$grade];
							}
							else{
                                $icon='';;
							}
							//echo "<br />ITEM : $codeitem ; Note : $grade\n";
							// MODIF JF 2013/10/07
							if ($grade>-1){    // ne pas afficher les item non evalues
								if ($detail){
									if ($grade>=$seuil){
										$s.= $codeitem.' <span class="valide">'
										.$label.'</span> ('
										.$icon.') ';
									}
									else{
										$s.= $codeitem.' <span class="invalide">'.$label.'</span> ('.$icon.') ';
									}
								}
								else{
									$s.= $codeitem.' '.$icon.' ';
								}
							}
						}					
					}    			
				}
			}			
		}
	}
	return $s;
}

// -----------------------
function referentiel_print_scales($course, $cm, $context, $roles){
global $DB;
global $OUTPUT;
global $CFG;
$strscale          = get_string('scale');
$strstandardscale  = get_string('scalesstandard');
$strcustomscales   = get_string('scalescustom');
$srtcreatenewscale = get_string('scalescustomcreate');
$strname           = get_string('name');
$strselect         = get_string('select');
$strused           = get_string('scaleused','referentiel');


$table = new html_table();
$table2 = new html_table();
$heading = '';

if ($course->id and $scales = grade_scale::fetch_all_local($course->id)) {
    $heading = $strcustomscales;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $menu = "";
        if (has_capability('mod/referentiel:writereferentiel', $context)) {
            $menu.= '<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?id='.$cm->id.'&amp;scaleid='.$scale->id.'&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
        }
        $line[] = $menu;
        $data[] = $line;
    }
    $table->head  = array($strscale, $strused, $strselect);
    $table->size  = array('60%', '30%', '10%');
    $table->align = array('left', 'center', 'center');
    $table->attributes['class'] = 'scaletable localscales generaltable';
    $table->data  = $data;

    //print_grade_page_head($courseid, 'scale', 'scale', get_string('coursescales', 'grades'));
   
    echo $OUTPUT->heading($strcustomscales.' '.$OUTPUT->help_icon('scalelocalh','referentiel'), 3, 'main');	
	echo html_writer::table($table);    
}
else{
	echo '<div align="center">'.get_string('no_custom_scale','referentiel').'<br />'."\n";
	if ($roles->is_admin){	
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
	}
	else if ($roles->is_teacher){
		$link=new moodle_url('/grade/edit/scale/index.php?id='.$course->id, NULL);
	}
	else{
		$link='';
	}
	if ($link){
		echo '<a href="'.$link.'">'.get_string('create_a_scale','referentiel').'</a>'."\n";
	}
	echo '</div>'."\n";
}


if ($scales = grade_scale::fetch_all_global()) {
    $heading = $strstandardscale;

    $data = array();
    foreach($scales as $scale) {
        $line = array();
        $line[] = format_string($scale->name).'<div class="scale_options">'.str_replace(",",", ",$scale->scale).'</div>';

        $used = $scale->is_used();
        $line[] = $used ? get_string('yes') : get_string('no');

        $menu = "";
/*
        if (has_capability('mod/referentiel:writereferentiel', $context)) {
            $menu.= '<a href="'.$CFG->wwwroot.'/mod/referentiel/bareme.php?id='.$cm->id.'&amp;scaleid='.$scale->id.'&amp;mode=editbareme&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('square_small','referentiel').'" alt="'.get_string('select').'" title="'.get_string('select').'" /></a>';
        }
*/
        $line[] = $menu;
        $data[] = $line;
    }
    $table2->head  = array($strscale, $strused, $strselect);
    $table->attributes['class'] = 'scaletable globalscales generaltable';
    $table2->size  = array('60%', '30%', '10%');
    $table2->align = array('left', 'center', 'center');
    $table2->data  = $data;

	echo $OUTPUT->heading($strstandardscale.' '.$OUTPUT->help_icon('scaleglobalh','referentiel'), 3, 'main');	
	echo html_writer::table($table2);
}
else{
	echo '<div align="center">'.get_string('no_global_scale','referentiel')."\n";
	if ($roles->is_admin){	
		$link=new moodle_url('/grade/edit/scale/index.php', NULL);
		echo '<br /><a href="'.$link.'">'.get_string('create_global_scale','referentiel').'</a>'."\n";
	}
	echo '</div>'."\n";
}
}


/**
 * update referentiel_scale record
 * and manage old evaluations if nusefull
 *
 * @param $form data submitted
 */

// -----------------------
function referentiel_creation_modification_bareme($form){
global $DB;
    if (!empty($form) && !empty($form->scaleid)){
	    if ($scale=$DB->get_record('scale', array('id'=>$form->scaleid))){
        	//echo "<br /> 486\n";
			//print_object($form);
			//echo "<br />\n";
        	if ($rec_bareme=referentiel_scale_2_bareme($scale)){
				$rec_bareme->threshold=$form->seuilid;    			
    			$rec_bareme->icons='';
				if ($tscales=explode(',',$rec_bareme->scale)){
       				while (list($key, $val) = each($tscales)) {
            			// echo "$key => $val<br />\n";
 						if (!empty($form->iconscale[$key])){
							$rec_bareme->icons.=$form->iconscale[$key].',';
						}    			
					}
				}
				// DEBUG
				//echo "<br /> 501\n";
				//print_object($rec_bareme); 
				 
				// tester s'il existe déjà
				if ($oldbareme=$DB->get_record('referentiel_scale', array('scaleid'=>$rec_bareme->scaleid))){
					$rec_bareme->id=$oldbareme->id;
    				$DB->update_record('referentiel_scale', $rec_bareme);
				}
				else{ // sinon le créer
					$rec_bareme->id=$DB->insert_record('referentiel_scale', $rec_bareme);
				}
				// DEBUG
				//echo "<br /> 513\n";
				//print_object($rec_bareme); 					
				if ($rec_bareme->id && $form->ref_referentiel){
					referentiel_set_bareme_occurrence($rec_bareme, $form->ref_referentiel);
				}
			}
		}
	}    
}

/**
 * update referentiel_scale record
 * and manage old evaluations if usefull
 *
 * @param $form data submitted
 */

// -----------------------
function referentiel_modification_bareme($form){
global $DB;
    if (!empty($form) && !empty($form->baremeid)){ 
		// attention ici scaleid==id et scaleid n'est pas modifie  
	    if ($rec_bareme=$DB->get_record('referentiel_scale', array('id'=>$form->baremeid))){
        	//echo "<br /> 486\n";
			//print_object($form);
			//echo "<br />\n";
			$rec_bareme->threshold=$form->seuilid;    			
    		$rec_bareme->icons='';
			if ($tscales=explode(',',$rec_bareme->scale)){
       			while (list($key, $val) = each($tscales)) {
            		// echo "$key => $val<br />\n";
 					if (!empty($form->iconscale[$key])){
						$rec_bareme->icons.=$form->iconscale[$key].',';
					}    								
				}
				// DEBUG
				//echo "<br /> 501\n";
				//print_object($rec_bareme); 
				$DB->update_record('referentiel_scale', $rec_bareme);			
				if ($form->ref_referentiel){
					referentiel_set_bareme_occurrence($rec_bareme, $form->ref_referentiel);
				}			
			}
		}
	}    
}


// ----------------------------------------------------
function referentiel_formate_saisie_bareme($index, $codeitem, $grade, $tscales, $tlabels, $maxoptions, $checked=''){
// $value : ITEMCODE:Grade
	$s='';
	$maxoptions=min($maxoptions,4);
	// MODIF JF 2013/10/07
	$maxoptions++;
	if (!empty($codeitem) && !empty($tscales)){
		//echo "<br /><b>$codeitem</b> GRADE: $grade\n";
        $s.='<select id="code_item_'.$index.'" name="code_item_'.$index.'" size="'.$maxoptions.'">'."\n";
        // MODIF JF 20132/10/07
		if ($grade==-1){
	        $s.='<option value="-1" selected="selected">'.get_string('non_evaluated','referentiel').'</option>'."\n";
		}
		else{
	        $s.='<option value="-1">'.get_string('non_evaluated','referentiel').'</option>'."\n";
		}

        reset($tscales);
		while (list($key, $val) = each($tscales)) {
			//echo "<br />Key : $key ; Val : $val\n";
			if (isset($val)){
            	if ($key==$grade){
                	$s.='<option value="'.$key.'" selected="selected">'.$tlabels[$key].'</option>'."\n";
                }
                else{
                	$s.='<option value="'.$key.'">'.$tlabels[$key].'</option>'."\n";
                }
            }
        }
        $s.='</select>'."\n";			    			
	}
	return $s;
}			


/**
 *  Affichage hierarchique de la boite d'evaluation avec bareme des items de competence
 *
 *  @input
 *  bareme : a referential scale 
 *  refrefid : referentiel_referentiel id
 *  liste_saisie : string : les competences qui seront spécialement évaluées
 *  liste_evaluations :  string : les appreciatons attachees à chaque item
 *  is_task : boolean : activite de type tache, on n'affiche pas les autres items que ceux de la liste saisie
 *  id_activite : activity id , utile si l'activité est modifiée
 *  comportement : ??
 *  @author jf
 *  @output
 */

// ----------------------------------------------------
function referentiel_modifier_evaluation_codes_item($bareme, $refrefid, $liste_competences, $liste_evaluations='', $is_task=false, $id_activite=0, $comportement='', $fonction=0){
// MODIF JF 2013/05/29
// La liste est au format
// codeItem1:indexbareme/codeItem2:indexbareme...
// A.1-1:-1/A.1-2:2/A.1-3:1/A.1-4:0/A.1-5:0/A.2-1:-1/A.2-2:-1/A.2-3:-1/A.3-1:-1/A.3-2:-1/A.3-3:-1/A.3-4:-1/B.1-1:-1/B.1-2:-1/B.1-3:-1/B.2-1:-1/B.2-2:-1/B.2-3:-1/B.2-4:-1/B.2-5:-1/B.3-1:-1/B.3-2:-1/B.3-3:-1/B.3-4:-1/B.3-5:-1/B.4-1:-1/B.4-2:-1/B.4-3:-1/

// avec un bareme [NonPertinent, NonValidé, Validé] cela corrspond à
// A.1-1:INDEFINI
// A.1-2:Validé
// A.1-3:NonValidé
// A.1-4:NonPertinent
// etc.

global $OK_REFERENTIEL_DATA;
global $t_domaine;
global $t_domaine_coeff;
global $t_domaine_description;

// COMPETENCES
global $t_competence;
global $t_competence_coeff;
global $t_competence_description;

// ITEMS
global $t_item_code;
global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
global $t_item_domaine; // index du domaine associe a un item
global $t_item_competence; // index de la competence associee a un item
global $t_item_poids; // poids
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;


global $t_item_description_competence;
$maxnbitem=10; // number max of items by competencies
$s='';
$separateur='/';
$nl='';
$tgraduation=array();
$tscales=array();
$ticons=array();
$tlabels=array();

    if (!empty($bareme)){
        // DEBUG
        //echo "<br />DEBUG :: lib_bareme.php :: 218 :: BAREME UTILISE";
        //print_object($bareme);
        $tscales=explode(',',$bareme->scale);
        $ticons=explode(',',$bareme->icons);
        $tlabels=explode(',',$bareme->labels);
		$seuil=$bareme->threshold;
		$maxoptions=$bareme->maxscale+1;

	    if ($id_activite==0){
/*	    
            $s1='<input type="checkbox" id="code_item_';
            $s2='" name="code_item[]" value="';
            $s3='"';
            $s4=' />';
*/            
            $s5='<label for="code_item_';
            $s6='">';
            $s7='</label> &nbsp; &nbsp; '."\n";
		}
		else{
/*
            $s1='<input type="checkbox" id="code_item_'.$id_activite.'_';
            $s2='" name="code_item_'.$id_activite.'[]" value="';
            $s3='"';
            if (!empty($comportement)){
                $s4=' '.$comportement.' />';
            }
            else{
                $s4=' />';
            }
*/            
            $s5='<label for="code_item_'.$id_activite.'_';
	   	    $s6='">';
		    $s7='</label> &nbsp; &nbsp;'."\n";
		}


		$checked=' checked="checked"';
		/*
			$tl=explode($separateur, $liste_complete);
		*/


		if ($refrefid){
			if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
				$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($refrefid);
			}

			if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
				$tl=$t_item_code;
                $maxnbitem=referentiel_get_maxnbitem($refrefid);

//				$liste_saisie2=$liste_saisie;
//              $liste_saisie=preg_replace("/:\d*/", "", $liste_saisie);
//				$liste_saisie=trim(strtr($liste_saisie, '.', '_'));
//				$liste_saisie2=trim(strtr($liste_saisie2, '.', '_'));
				//echo "<br />DEBUG :: 980 :: COMPETENCES : $liste_competences<br />\n";
				//echo "<br />DEBUG :: 981 :: EVALUATIONS : $liste_evaluations<br />\n";

    			$liste_saisie=strtr($liste_competences, $separateur, ' ');
				$liste_saisie=trim(strtr($liste_saisie, '.', '_'));
				//echo "<br />DEBUG :: 964 :: INPUT : $liste_saisie<br />\n";

				if (!empty($liste_evaluations)){
					$liste_bareme=preg_replace("/:\d*/", "", $liste_evaluations);
					$liste_a_evaluer=trim(strtr($liste_evaluations, '.', '_'));
					//echo "<br />DEBUG :: 990 :: INPUT : $liste_bareme<br />\n";
					//echo "<br />DEBUG :: 991 :: INPUT : $liste_a_evaluer<br />\n";
					// recupérer la competences declarées
					if ($input_data=explode('/',$liste_evaluations)){
						while (list($key, $val) = each($input_data)) {
							if (!empty($val)){
       						//echo "<br>$key => $val ; \n";
								if ($titem=explode(':',$val)){
									if (isset($titem[1])){
										$tgraduation[$titem[0]]=$titem[1];
									}
								}
							}					
						}    			
					}
				}

				// DEBUG
				//echo "<br />ITEM EVALUES : <br />\n";
				//print_object($tgraduation);
				//echo "<br />\n";
				//exit;								
				$ne=count($tl);
				$select='';

    			$index_code_domaine=$t_item_domaine[0];
    			$code_domaine=$t_domaine[$index_code_domaine];

    			$index_code_competence=$t_item_competence[0];
    			$code_competence=$t_competence[$index_code_competence];

				// Moodle 2.7

                $s.= '<table><tr><td colspan="'.$maxnbitem.'">'."\n";
				$s.= '&nbsp; &nbsp; &nbsp; <span class="bold">'.$code_domaine.'</span> : '.$t_domaine_description[$index_code_domaine]."\n";      // ouvrir domaine
			    $s.= '<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n";     // ouvrir competence
				$s.= '</td></tr></table>'."\n";
                $s.= '<table><tr>'."\n";
    			$i=0;
			    while ($i<$ne){
        			//echo $code_domaine.' '.$code_competence;
        			//echo $t_item_domaine[$i].' '.$t_item_competence[$i];

        			// domaine
        			if ($t_item_domaine[$i] != $index_code_domaine){
                    	$index_code_domaine=$t_item_domaine[$i];
                    	$code_domaine=$t_domaine[$index_code_domaine];
                        $s.= '</tr></table>'."\n";
                        $s.= '<table><tr><td colspan="'.$maxnbitem.'">'."\n";
						// competence
                    	$s.='<br /> &nbsp; &nbsp; &nbsp; <span class="bold">'.$code_domaine.'</span> : '.$t_domaine_description[$index_code_domaine]."\n";  // nouveau domaine
                    	// nouvelle competence
                    	$index_code_competence=$t_item_competence[$i];
                    	$code_competence=$t_competence[$index_code_competence];
                    	$s.='<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n";
                        $s.= '</td></tr></table>'."\n";
                        $s.= '<table><tr>'."\n";
			        }

        			// competence
        			if ($t_item_competence[$i] != $index_code_competence){
                    	$index_code_competence=$t_item_competence[$i];
                    	$code_competence=$t_competence[$index_code_competence];
                        $s.= '</tr></table>'."\n";
                        $s.= '<table><tr><td colspan="'.$maxnbitem.'">'."\n";
						$s.= '<br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i>'.$code_competence.'</i> : <span class="small">'.$t_competence_description[$index_code_competence].'</span><br /> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'."\n";
                        $s.= '</td></tr></table>'."\n";
						$s.= '<table><tr>'."\n";
        			}
                	// item

					$code=trim($tl[$i]);
        			$le_code=referentiel_affiche_overlib_un_item($separateur, $code);
                    $s.= '<td>'."\n";
        	    	$s.="\n".'<input type="hidden" name="code_code[]" value="'.$code.'" />'."\n";

					if ($code!=""){
						$code_search=strtr($code, '.', '_');
						//echo "----- $code_search ";
						if (stristr($liste_saisie, $code_search)){
							if (isset($tgraduation[$code])){	
								//echo "----- $code_search ".$tgraduation[$code]."<br>";		
								$s.=referentiel_formate_saisie_bareme($id_activite.'_'.$i, $code, $tgraduation[$code], $tscales, $tlabels, $maxoptions, $checked);
							}
							else{
								// MODIF JF 2013/10/07
								// $s.=referentiel_formate_saisie_bareme($id_activite.'_'.$i, $code, 0, $tscales, $tlabels, $maxoptions, $checked);
								// echo "<br /> DEBUG :: lib_bareme.php :: 1138 :: <br />----- $code_search ".$tgraduation[$code]."<br>";
								$s.=referentiel_formate_saisie_bareme($id_activite.'_'.$i, $code, -1, $tscales, $tlabels, $maxoptions, $checked);
							}
							//$s.= $s1.$i.$s2.$code.$s3.$checked.$s4.
                            $s.=$s5.$i.$s6.'<span class="surligne">'.$le_code.'</span>'.$s7;
						}
						else {
							if (!$is_task){
        						// MODIF JF 2013/10/07
								// $s.=referentiel_formate_saisie_bareme($id_activite.'_'.$i, $code, 0, $tscales, $tlabels, $maxoptions, '');
                    			$s.=referentiel_formate_saisie_bareme($id_activite.'_'.$i, $code, -1, $tscales, $tlabels, $maxoptions, '');
								// $s.=$s1.$i.$s2.$code.$s3.$s4.$s5.$i.$s6.$le_code.$s7;
								$s.=$s5.$i.$s6.$le_code.$s7;
							}
							else{
								$s.=' &nbsp; '. $s5.$i.$s6.$le_code.$s7;
							}
						}
					}
                    $s.= '</td>'."\n";
					$i++;
				}
                $s.= '</tr></table>'."\n";

				$s.="\n".'<input type="hidden" name="userbareme" value="1" />'."\n";						
                $s.='<input type="hidden" name="nbitems" value="'.$i.'" />'."\n";
                $s.='<input type="hidden" name="seuil" value="'.$bareme->threshold.'" />'."\n";
                $s.='<input type="hidden" name="baremeid" value="'.$bareme->id.'" />'."\n";
			}
			
		}
    }
    if ($fonction) return $s; else echo $s;
}

/**
 *  mise a jour des evaluation bareme pour l'activite
 *
 *  @input
 *  activite_id : referentiel_activite id
 *  liste_evaluations : string : les competences évaluées
 *  bareme_id : referentiel_activite_scale id
 *  @author jf
 *  @output
 */

// ----------------------------------------------------
function referentiel_enregistrer_evaluation_activite($liste_evaluations, $activite_id, $baremeid){
global $DB;
	// DEBUG
	//echo "<br />DEBUG : lib_bareme.php :: 1199  <br />FORMULAIRE OUTPUT<br />\n $liste_evaluations<br /> $activite_id<br /> $baremeid\n";
	//exit;
	if (!empty($activite_id) && !empty($baremeid)){

	    if ($rec= $DB->get_record('referentiel_activite_scale', array('activiteid'=>$activite_id, 'refscaleid'=> $baremeid))){
			// maj			
			$rec->competences_bareme= $liste_evaluations;			
            //echo "<br />MAJ<br />\n";
			//print_object($rec);
			if ($DB->update_record('referentiel_activite_scale', $rec)){
				return $rec->id;
			}
		}
		else{
	    	// creation
			$rec=new Object();
			$rec->activiteid= $activite_id;
			$rec->refscaleid= $baremeid;
			$rec->competences_bareme= $liste_evaluations;			
		    return $DB->insert_record('referentiel_activite_scale', $rec);
		}			
	}

	return false;
}

/**
 * delete referentiel_a_scale_ref record
 * and delete all referentiel_activite_bareme associated to this occurrence_id
 *
 * @param $baremeid as referentiel_scale id
 * @param $occurrence_id as referentiel_referentiel id
 * @return bolean
 */

// -----------------------
function referentiel_delete_bareme_occurrence($baremeid, $occurrence_id){

global $DB;
    $ok=false;
	//echo "<br /> lib_bareme.php :: 470:: BAREMEID : $baremeid, OCCURRENCE: $occurrence_id<br />\n";
    // suppresion des evaluation par activite
	if (!empty($baremeid) && !empty($occurrence_id)){	
        $params= array('occurrenceid'=>$occurrence_id, 'baremeid'=>$baremeid);
        $sql="SELECT ras.id AS id
	FROM {referentiel_a_scale_ref} AS rsr, {referentiel_activite_scale} AS ras, {referentiel_activite} AS a
	WHERE rsr.refscaleid=:baremeid
	AND rsr.refscaleid = ras.refscaleid
	AND	rsr.refrefid=:occurrenceid
	AND	rsr.refrefid=a.ref_referentiel
	AND ras.activiteid=a.id ";
	
        if ($recs=$DB->get_records_sql($sql, $params)){
	    	foreach($recs as $rec){
				$DB->delete_records('referentiel_activite_scale', array('id'=>$rec->id));			
			}
        }
    	// suppression de l'association du bareme au referentiel
		$ok=$DB->delete_records('referentiel_a_scale_ref', array('refrefid'=>$occurrence_id, 'refscaleid'=>$baremeid));			
    }
    
    return $ok;
}

// -----------------------
function referentiel_creer_competences_activite($activite, $bareme){
global $DB;
 	$liste_evaluations='';
	if (!empty($bareme)  && !empty($activite) && !empty($activite->competences_activite)){
        if ($activite->approved){
		    $sep=":".$bareme->threshold."/";
		}
		else{
            // MODIF JF 2013/10/07
			//$sep=":0/";
            $sep=":-1/"; // non defini
		}
		$liste_codes=referentiel_get_liste_codes_competence($activite->ref_referentiel);
		$liste_a_comparer=trim(strtr($activite->competences_activite, '.', '_'));
		
		$titem=explode('/',$liste_codes);
        if ($titem){
			foreach ($titem as $item){
                if (!empty($item)){
                	$code_search=strtr($item, '.', '_');
					if (stristr($liste_a_comparer, $code_search)){
                    	$liste_evaluations.=$item.$sep;
                    }
                    else{
						// MODIF JF 2013/10/07
						// $liste_evaluations.=$item.":0/";
                        $liste_evaluations.=$item.":-1/";     // non defini
					}
                }
            }
        }
       	// creation
       	
		$rec=new Object();
		$rec->activiteid= $activite->id;
		$rec->refscaleid= $bareme->id;
		$rec->competences_bareme= $liste_evaluations;
		// DEBUG
		//print_object($rec);
		//exit;			
		if ($DB->insert_record('referentiel_activite_scale', $rec)){
			return $rec->competences_bareme;
		}
	}
	return '';
}

?>
