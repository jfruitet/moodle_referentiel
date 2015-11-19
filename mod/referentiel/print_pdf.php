<?php // $Id: print_pdf.php,v 3.0 2013/04/14 11:32:00 jf Exp $

/**
 * Produces a sample PDF using lib/pdflib.php
 *
 * @package    core
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/pdflib.php');

/**
 * Extend the standard PDF class to get access to some protected values we want to display
 * at the test page.
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Referentiel_PDF extends pdf
{
    // Une colonnes
    var $col=0;

    public function returnFontsList() {
        return $this->fontlist;
    }
    public function _getfontpath() {
        return parent::_getfontpath();
    }

    public function SetCol($col)
    {
    //Move position to a column
    $this->col=$col;
    $x=15+$col*95;
    $this->SetLeftMargin($x);
    $this->SetX($x);
    }

    public function AcceptPageBreak()
    {
        //Go back to first column and issue page break
        $this->SetCol(0);
        return true;
    }


    //En-tête
    public function Header()
    {


    // Police Arial 9
    $this->SetFont('helvetica','',9);
    // Décalage à droite
    $this->Cell(80);
    // Texte
    $this->Cell(120,0,'Certificat',0,0,'L');

    }

    //Pied de page

    public function Footer()
    {

    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    //Police Arial italique 8
    $this->SetFont('helvetica','I',8);

    // nb de pages
    if (empty($this->pagegroups)) {
			$nbpages = $this->getAliasNbPages();
	} else {
			$nbpages = $this->getPageGroupAlias();
	}
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/'.$nbpages,0,0,'C');
    }

    // affiche element
    public function affiche_element($X, $Y, $largeur, $texte, $cadre=0, $alignement="L", $remplissage=0) {
        $this->SetXY($X,$Y);
        $this->MultiCell($largeur,6,$texte,$cadre,$alignement,$remplissage);
    } // fin affiche element

}


	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string printted text
     */



function pdf_write_etablissement( $record ) {
    // initial string;
	global $Refpdf;
		if ($record){
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo=$record->logo_etablissement;
			$Refpdf->SetFont('helvetica','',10); 
			$texte=(get_string('num_etablissement','referentiel').' : '.$num_etablissement);
			$Refpdf->Write(6,$texte);
			$Refpdf->Ln(6);
			$Refpdf->SetFont('helvetica','B',12); 
			$texte=(get_string('nom_etablissement','referentiel').' : '.$nom_etablissement);
			$Refpdf->Write(6,$texte);
			$Refpdf->Ln(6);
			$texte=(get_string('adresse_etablissement','referentiel').' : '.$adresse_etablissement);
			$Refpdf->SetFont('helvetica','',10); 
			$Refpdf->Write(6,$texte);
			$Refpdf->Ln(10);
    }

 }
	
function pdf_write_etudiant( $record, $param ) {
	global $Refpdf;
		if ($record){
			// DEBUG
			// echo "";
			// print_r($record);
	    	// add header
			//
			$id = trim( $record->id );
			$userid = trim( $record->userid );
            $ref_etablissement = trim( $record->ref_etablissement);
			$num_etudiant = trim( $record->num_etudiant);
			$ddn_etudiant = trim( $record->ddn_etudiant);
			$lieu_naissance = trim( $record->lieu_naissance);
			$departement_naissance = trim( $record->departement_naissance);
			$adresse_etudiant = trim( $record->adresse_etudiant);			

                $login = trim(referentiel_get_user_login($record->userid));

                if ($num_etudiant==$login){
                    $snum=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $snum=$login;
                }
                else{
                    $snum=$num_etudiant." (".$login.")";
                }

			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				if ($param->certificat_sel_etudiant_etablissement){
					pdf_write_etablissement( $record_etablissement );
				}
			}
			if ($param->certificat_sel_etudiant_numero){
                $Refpdf->SetFont('helvetica','',10);
                $texte=(get_string('num_etudiant','referentiel')." : ".$snum);
				$Refpdf->Write(6,$texte);
				$Refpdf->Ln(6);
			}
			
			if ($param->certificat_sel_etudiant_nom_prenom){
				$Refpdf->SetFont('helvetica','',12); 
				$Refpdf->Write(6,(referentiel_get_user_info($record->userid)));
				$Refpdf->SetFont('helvetica','',10); 
				$Refpdf->Ln(6);
			}
			if ($param->certificat_sel_etudiant_ddn || $param->certificat_sel_etudiant_lieu_naissance){
				$texte='';
				if ($param->certificat_sel_etudiant_ddn){
					$texte.=(get_string('ddn_etudiant','referentiel')." ".$ddn_etudiant." ");
				}
				if ($param->certificat_sel_etudiant_lieu_naissance){
					$texte.=(get_string('lieu_naissance','referentiel')." : ".$lieu_naissance.", ".get_string('departement_naissance','referentiel')." : ".$departement_naissance);
				}
				$Refpdf->Write(6,$texte);
				$Refpdf->Ln(6);
            }
			if ($param->certificat_sel_etudiant_adresse){
				$texte=(get_string('adresse_etudiant','referentiel'). " : ".$adresse_etudiant);
				$Refpdf->Write(6, $texte);
				$Refpdf->Ln(6);
			}
    }
}


// -------------------
function pdf_referentiel_affiche_certificat_consolide($ref_referentiel, $separateur1, $separateur2, $liste_code, $font1=10, $font2=9, $font3=8, $params=NULL){
// ce certificat comporte des pourcentages par domaine et competence
// decalque de referentiel_affiche_certificat_consolide() de lib.php
global $Refpdf;

global $OK_REFERENTIEL_DATA;
global $t_domaine;
global $t_domaine_coeff;
		
// COMPETENCES
global $t_competence;
global $t_competence_coeff;
		
// ITEMS
global $t_item_code;
global $t_item_coeff; // coefficient poids determeine par le modele de calcul (soit poids soit poids / empreinte)
global $t_item_domaine; // index du domaine associé à un item 
global $t_item_competence; // index de la competence associée à un item 
global $t_item_poids; // poids
global $t_item_empreinte;
global $t_nb_item_domaine;
global $t_nb_item_competence;

	// nom des domaines, compétences, items
	$label_d="";
	$label_c="";
	$label_i="";
	if (isset($params) && !empty($params)){
		if (isset($params->label_domaine)){
					$label_d=$params->label_domaine;
		}
		if (isset($params->label_competence)){
					$label_c=$params->label_competence;
		}
		if (isset($params->label_item)){
					$label_i=$params->label_item;
		}
	}
	$t_certif_item_valeur=array();	// table des nombres d'items valides 
	$t_certif_item_coeff=array(); // somme des poids du domaine
	$t_certif_competence_poids=array(); // somme des poids de la competence
	$t_certif_domaine_poids=array(); // poids certifies
	for ($i=0; $i<count($t_item_code); $i++){
		$t_certif_item_valeur[$i]=0.0;
		$t_certif_item_coeff[$i]=0.0;
	}
	for ($i=0; $i<count($t_competence); $i++){
		$t_certif_competence_poids[$i]=0.0;
	}
	for ($i=0; $i<count($t_domaine); $i++){
		$t_certif_domaine_poids[$i]=0.0;
	}
		
	// donnees globales du referentiel
	if ($ref_referentiel){
		
		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){		
		// recuperer les items valides
		$tc=array();
		$liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);

		if (!empty($liste_code) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste_code);
			for ($i=0; $i<count($t_item_domaine); $i++){
				$t_certif_domaine_poids[$i]=0.0;
			}
			for ($i=0; $i<count($t_item_competence); $i++){
				$t_certif_competence_poids[$i]=0.0;
			}

			$i=0;
			while ($i<count($tc)){
				$t_cc=explode($separateur2, $tc[$i]); // tableau des items valides
				if (isset($t_cc[1])){
					if (isset($t_item_poids[$i]) && isset($t_item_empreinte[$i])){
						if (($t_item_poids[$i]>0) && ($t_item_empreinte[$i]>0)){
							// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
							$t_certif_item_valeur[$i]=min($t_cc[1],$t_item_empreinte[$i]);
							// calculer le taux
							$coeff=(float)$t_certif_item_valeur[$i] * (float)$t_item_coeff[$i];
							// stocker la valeur pour l'item
							$t_certif_item_coeff[$i]=$coeff;
							// stocker le taux pour la competence
							$t_certif_domaine_poids[$t_item_domaine[$i]]+=$coeff;
							// stocker le taux pour le domaine
							$t_certif_competence_poids[$t_item_competence[$i]]+=$coeff;
						}
						else{
							// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
							$t_certif_item_valeur[$i]=0.0;
							$t_certif_item_coeff[$i]=0.0;
							// $t_certif_domaine_poids[$t_item_domaine[$i]]+=0.0;
							// $t_certif_competence_poids[$t_item_competence[$i]]+=0.0;
						}
					}
				}
				
				$i++;
			}
			
			// Affichage
			// DOMAINES
      $Refpdf->SetFont('helvetica','B',$font1);
      $Refpdf->Write(6,(get_string('domaine','referentiel')));
      $Refpdf->Ln(6);
      $nd=count($t_domaine_coeff);
			$espaced=80 / $nd;
      // $s.= '<table width="100%" cellspacing="0" cellpadding="2"><tr valign="top" >'."\n";
			for ($i=0; $i<$nd; $i++){
				if ($t_domaine_coeff[$i]){
					// $s.='<td  align="center" colspan="'.$t_nb_item_domaine[$i].'"><b>'.$t_domaine[$i].'</b> ('.referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%)</td>';
          $Refpdf->SetFont('helvetica','',$font2);
          for ($j=0; $j < $espaced; $j++){
            $Refpdf->Write(6," ");
          }
          $Refpdf->SetFont('helvetica','B',$font2);
          $Refpdf->Write(6,$t_domaine[$i]);
          $Refpdf->SetFont('helvetica','',$font3);
          $Refpdf->Write(6," (".referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i])."%) ");
        }
				else{
					// $s.='<td  align="center" colspan="'.$t_nb_item_domaine[$i].'"><b>'.$t_domaine[$i].'</b> (0%)</td>';
				  $Refpdf->SetFont('helvetica','',$font2);
           for ($j=0; $j < $espaced; $j++){
            $Refpdf->Write(6,"   ");
          }
          $Refpdf->SetFont('helvetica','B',$font2);
          $Refpdf->Write(6, $t_domaine[$i]);
          $Refpdf->SetFont('helvetica','',$font3);
          $Refpdf->Write(6," (0%) ");
        }
			}
			//$s.='</tr>'."\n";
      $Refpdf->Ln(6);
      $Refpdf->SetFont('helvetica','B',$font1);
      $Refpdf->Write(6,(get_string('competence','referentiel')));
      $Refpdf->Ln(6);
      
      $nc=count($t_competence);
			$espacec= 80 / $nc;

			// $s.=  '<tr valign="top"  >'."\n";
			for ($i=0; $i<$nc; $i++){
				if ($t_competence_coeff[$i]){
					// $s.='<td align="center" colspan="'.$t_nb_item_competence[$i].'"><b>'.$t_competence[$i].'</b> ('.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%)</td>'."\n";
				  $Refpdf->SetFont('helvetica','',$font2);
          for ($j=0; $j < $espacec; $j++){
            $Refpdf->Write(6," ");
          }
          $Refpdf->SetFont('helvetica','B',$font2);
          $Refpdf->Write(6, $t_competence[$i]);
          $Refpdf->SetFont('helvetica','',$font3);
          $Refpdf->Write(6," (".referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i])."%) ");									
				}
				else{
					// $s.='<td align="center" colspan="'.$t_nb_item_competence[$i].'"><b>'.$t_competence[$i].'</b> (0%)</td>'."\n";
				  $Refpdf->SetFont('helvetica','',$font2);
          for ($j=0; $j < $espacec; $j++){
            $Refpdf->Write(6," ");
          }
          $Refpdf->SetFont('helvetica','B',$font2);
          $Refpdf->Write(6, $t_competence[$i]);
          $Refpdf->SetFont('helvetica','',$font3);
          $Refpdf->Write(6," (0%) ");					
				}
			}
			// $s.='</tr>'."\n";
			$Refpdf->Ln(6);
						
			// ITEMS
      $Refpdf->SetFont('helvetica','B',$font1);
      $Refpdf->Write(6,(get_string('item','referentiel')));
      $Refpdf->Ln(6);
			
			// $s.= '<tr valign="top" >'."\n";
			for ($i=0; $i<count($t_item_code); $i++){
				if ($t_item_empreinte[$i]){
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]) {
						// $s.='<td'.$bgcolor.'><span  class="valide">'.$t_item_code[$i].'</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','B',$font2);
            $Refpdf->Write(6,$t_item_code[$i]." ");
					}	
					else {
						// $s.='<td'.$bgcolor.'><span class="invalide">'.$t_item_code[$i].'</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','',$font2);
            $Refpdf->Write(6,$t_item_code[$i]." ");
          }
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						// $s.='<td'.$bgcolor.'><span class="valide">100%</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','B',$font3);
            $Refpdf->Write(6,"(100%) ");				
					}
					else{
						// $s.='<td'.$bgcolor.'><span class="invalide">'.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','',$font3);
            $Refpdf->Write(6,"(".referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i])."%) ");				
					}  
				}
				else{
					// $s.='<td class="nondefini"><span class="nondefini"><i>'.$t_item_code[$i].'</i></span></td>'."\n";
				    $Refpdf->SetFont('helvetica','I',$font2);
            $Refpdf->Write(6,$t_item_code[$i]." ");		
				}
			}
			// $s.='</tr><tr valign="top" >'."\n";
			$Refpdf->Ln(6);
			/*
      // <td  width="5%">'.get_string('coeff','referentiel').'</td>'."\n";
			for ($i=0; $i<count($t_item_coeff); $i++){
				if ($t_item_empreinte[$i]){
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						// $s.='<td'.$bgcolor.'><span class="valide">100%</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','B',$font1);
            $Refpdf->Write(6,"   100% ");				
					}
					else{
						// $s.='<td'.$bgcolor.'><span class="invalide">'.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%</span></td>'."\n";
				    $Refpdf->SetFont('helvetica','',$font1);
            $Refpdf->Write(6,"    ".referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i])." ");				
					}
				}
				else {
					// $s.='<td class="nondefini"><span class="nondefini">&nbsp;</span></td>'."\n";
				}
			}
			// $s.='</tr></table>'."\n";
		
			*/
				$Refpdf->Ln(6);
		}
	}
	}
}

// ----------------------------------------------------
function pdf_referentiel_affiche_detail_competences($separateur1, $separateur2, $liste, $liste_empreintes, $liste_poids, $font1=10, $font2=9){
// decalque de referentiel_affiche_detail_competences() de print_lib_certificat.php
global $Refpdf;

	$t_empreinte=explode($separateur1, $liste_empreintes);
	$t_poids=explode('|', $liste_poids);	
	// DEBUG
	// echo "<br />DEBUG : print_lib_certificat.php :: 105<br />LISTE EMPREINTES : $liste_empreintes<br />\n";
	// print_r($t_empreinte);
	// DEBUG
	// echo "<br />DEBUG : print_lib_certificat.php :: 108<br />LISTE POIDS : $liste_poids<br />\n";
	// print_r($t_poids);
	// exit;

	$tc=array();
	$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
		if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			$i=0;
			while ($i<count($tc)){
				if ($tc[$i]!=''){
					$tcc=explode($separateur2, $tc[$i]);					
					if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
            $Refpdf->SetFont('helvetica','B',$font1);
          }
					else{
            $Refpdf->SetFont('helvetica','I',$font1);
					}
          $Refpdf->Write(6,$tcc[0]." : ");
          $Refpdf->SetFont('helvetica','',$font2);
          $Refpdf->Write(6," ".(str_replace('#',"\n".get_string('p_item','referentiel').":",$t_poids[$i])." ".get_string('approved','referentiel').":".$tcc[1]." ".get_string('e_item','referentiel').":".$t_empreinte[$i]." "));						
					$Refpdf->Ln(6);
				}
				$i++;
			} 
		}
	  $Refpdf->Ln(6);
}



// ----------------------------------------------------
function pdf_liste_competences_certificat($referentiel_id, $separateur1, $separateur2, $liste, $liste_empreintes, $all=0, $font1=10, $font2=9){
global $Refpdf;
global $copyright;
global $registere;
global $puce;
// Affiche les codes competences en tenant compte de l'empreinte
	$t_empreinte=explode($separateur1, $liste_empreintes);
	
		$tc=array();
		$liste=referentiel_purge_dernier_separateur($liste, $separateur1);
		if (!empty($liste) && ($separateur1!="") && ($separateur2!="")){
			$tc = explode ($separateur1, $liste);
			// DEBUG 
			// echo "<br />CODE <br />\n";
			// print_r($tc);
			$i=0;
			while ($i<count($tc)){
				// CODE1:N1
				// DEBUG 
				// echo "<br />".$tc[$i]." <br />\n";
				// exit;
				$tcc=explode($separateur2, $tc[$i]);
				// echo "<br />CODE : ".$tc[$i]." <br />\n";
				// echo "<br />REFERENTIEL ID : ".$referentiel_id." <br />\n";
				// print_r($tcc);
				
				// exit;
				if ($referentiel_id){
					$descriptif_item=(referentiel_get_description_item($tcc[0], $referentiel_id));
				}
				else{
					$descriptif_item='';
				}
				if (isset($tcc[1]) && ($tcc[1]>=$t_empreinte[$i])){
					$Refpdf->SetFont('helvetica','B',$font1); 
					$Refpdf->Write(6, "    $puce ".$tcc[0]);
					$Refpdf->SetFont('helvetica','',$font2);
					$Refpdf->Write(6," : $descriptif_item");
					$Refpdf->Ln(6);
				}
				else if ($all){
					$Refpdf->SetFont('helvetica','I',$font1); 
					$Refpdf->Write(6, "     $puce ".$tcc[0]);
					$Refpdf->SetFont('helvetica','',$font2);
					$Refpdf->Write(6," : $descriptif_item");
					$Refpdf->Ln(6);
				}
				$i++;
			} 
		}
}

    /**
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

function pdf_write_item( $item ) {
    global $Refpdf;
    if ($item){
      $code = $item->code_item;
      $description_item = $item->description_item;
      $ref_referentiel = $item->ref_referentiel;
      $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$empreinte_item = $item->empreinte_item;
			$num_item = $item->num_item;
      $Refpdf->SetFont('helvetica','B',9); 
   	  $Refpdf->Write(6, (trim(stripslashes($code))));
 	   	$Refpdf->Ln(6);
 	   	$Refpdf->SetFont('helvetica','I',9);
   	  $Refpdf->Write(6, (trim(stripslashes($description_item))));
   	  $Refpdf->Ln(6);
   	  $Refpdf->SetFont('helvetica','',9);
      $Refpdf->Write(6, (trim(get_string('t_item','referentiel')." : ".$type_item.", ".get_string('p_item','referentiel')." : ".$poids_item.", ".get_string('e_item','referentiel')." : ".$empreinte_item)));
      $Refpdf->Ln(6);
    } 
}
    
	 /**
     * Turns competence into an xml segment
     * @param competence object
     * @return string xml segment
     */

function pdf_write_competence( $competence ) {
    global $Refpdf;
 		  if ($competence){
        $code = $competence->code_competence;
        $description_competence = $competence->description_competence;
        $ref_domaine = $competence->ref_domaine;
        $num_competence = $competence->num_competence;
			  $nb_item_competences = $competence->nb_item_competences;
        $Refpdf->SetFont('helvetica','B',10); 
	   	  $Refpdf->Write(6,(trim(get_string('competence','referentiel')." : ".stripslashes($code))));
        $Refpdf->Ln(6);
        $Refpdf->SetFont('helvetica','',10); 
        $Refpdf->Write(6, (trim(stripslashes($description_competence))));
	 	   	$Refpdf->Ln(6);
			
			  // ITEM
			  $records_items = referentiel_get_item_competences($competence->id);
        if ($records_items){				  
    	    $Refpdf->SetFont('helvetica','B',10); 
	        $Refpdf->Write(6,(trim(get_string('items','referentiel'))));
          $Refpdf->Ln(6);

				  foreach ($records_items as $record_i){
						pdf_write_item( $record_i );
				  }
				  $Refpdf->Ln(6);
			   }
        }
}


	 /**
     * Turns domaine into an xml segment
     * @param domaine object
     * @return string xml segment
     */

function pdf_write_domaine( $domaine ) {
    global $Refpdf;
    
		if ($domaine){
      $code = $domaine->code_domaine;
      $description_domaine = $domaine->description_domaine;
      $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
 			$Refpdf->SetFont('helvetica','B',10); 
   	  $Refpdf->Write(6,(trim(get_string('domaine','referentiel')." : ".stripslashes($code))));
      $Refpdf->Ln(6);
      $Refpdf->SetFont('helvetica','',10); 
   	  $Refpdf->Write(6, (trim(stripslashes($description_domaine))));
 	   	$Refpdf->Ln(6);
			
			// LISTE DES COMPETENCES DE CE DOMAINE
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				foreach ($records_competences as $record_c){
          pdf_write_competence( $record_c );
				}
			}
    }
}


	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

function pdf_write_referentiel( $referentiel_instance, $referentiel_referentiel, $param ) {
    global $CFG;
		global $Refpdf;
		global $image_logo;
		$ok_saut_page=false;
		
		if (($referentiel_instance) && ($referentiel_referentiel)) {
            $name = strip_tags(trim($referentiel_referentiel->name));
            $code = strip_tags(trim($referentiel_referentiel->code_referentiel));
			$description = strip_tags(trim($referentiel_referentiel->description_referentiel));
			
			$id = $referentiel_instance->id;
            $name_instance = strip_tags(trim($referentiel_instance->name));
            $description_instance = strip_tags(trim($referentiel_instance->description_instance));
            $label_domaine = strip_tags(trim($referentiel_instance->label_domaine));
            $label_competence = strip_tags(trim($referentiel_instance->label_competence));
            $label_item = strip_tags(trim($referentiel_instance->label_item));
            $date_instance = $referentiel_instance->date_instance;
            $course = $referentiel_instance->course;
            $ref_referentiel = $referentiel_instance->ref_referentiel;
			$visible = $referentiel_instance->visible;

			$Refpdf->AddPage();
			$Refpdf->SetAutoPageBreak(1, 27.0);     
			$Refpdf->SetCol(0);
			$Refpdf->SetDrawColor(128, 128, 128);    
			$Refpdf->SetLineWidth(0.4);     
			// logo
			$posy=$Refpdf->GetY();    
			
			if (isset($image_logo) && ($image_logo!="")){
				$Refpdf->Image($image_logo,150,$posy,40);
			}
            $Refpdf->SetLeftMargin(15);

			$Refpdf->SetFont('helvetica','B',14); 
    		$Refpdf->Write(6,get_string('certification','referentiel'));
			$Refpdf->Ln(6);
			$Refpdf->SetFont('helvetica','',12); 
		    $Refpdf->Write(6, $name.'('.$code.')');
			$Refpdf->Ln(6);
			$Refpdf->SetFont('helvetica','',10);
			$Refpdf->Write(6, $description);
			$Refpdf->Ln(6);
            if ($param->certificat_sel_referentiel){
				// DOMAINES
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($referentiel_referentiel->id);
		        if ($records_domaine){
					foreach ($records_domaine as $record_d){
						pdf_write_domaine($record_d );
					}
				}
                $ok_saut_page=true;
			} 

			if ($param->certificat_sel_referentiel_instance){
				$Refpdf->SetFont('helvetica','B',10); 
				// $Refpdf->Write(6,"id : $id ");
				// $Refpdf->Ln(6);
				$Refpdf->Write(6,(get_string('instance','referentiel')." : ".$name_instance));
				$Refpdf->Ln(6);
				$Refpdf->SetFont('helvetica','',10);
				$Refpdf->Write(6,($description_instance));   
				$Refpdf->Ln(6);
                $Refpdf->Write(6,($label_domaine.", ".$label_competence.", ".$label_item));
			
    	        /*
				$Refpdf->Write(6,"Cours : $course");
				$Refpdf->Ln(6);
	            $Refpdf->Write(6,"Référentiel :  $ref_referentiel");
				$Refpdf->Ln(6);
        	    $Refpdf->Write(6,"Visible : $visible");
				$Refpdf->Ln(6);
				*/
				$ok_saut_page=true;
			}
			
			$Refpdf->Ln(6);
			if ($ok_saut_page==true){ // forcer le saut de page
                $Refpdf->Ln(290);
            }
		}
}
	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */
function pdf_write_certificat( $record, $referentiel_instance, $referentiel_referentiel, $liste_empreintes, $liste_poids, $param) {
    	global $CFG;
		global $Refpdf;
    	// add comment and div tags
		
		if ($record){
			// DEBUG
			// echo "DEBUG LIGNE 1021";
			// print_r($referentiel_instance);
			$id = trim( $record->id );
            $commentaire_certificat = (trim($record->commentaire_certificat));
            $synthese_certificat = (trim($record->synthese_certificat));
			$competences_activite =  (trim($record->competences_activite)) ;
            $competences_certificat =  (trim($record->competences_certificat)) ;
            $decision_jury = (trim($record->decision_jury));
			if ($record->date_decision){
                $date_decision = userdate(trim($record->date_decision));
			}
			else{
				$date_decision ="";
			}
            $userid = trim( $record->userid);
            $teacherid = trim( $record->teacherid);
			if ($teacherid!=0){
				$nom_prenom_teacher=(referentiel_get_user_info($teacherid));
			}
			else{
				$nom_prenom_teacher="";
			}
      
            $ref_referentiel = trim( $record->ref_referentiel);
			// $ref_referentiel=$referentiel_id;
			$verrou = trim( $record->verrou );
			$valide = trim( $record->valide );
			$evaluation = trim( $record->evaluation );
			
			
			// USER
			if (isset($record->userid) && ($record->userid>0)){
				$record_etudiant = referentiel_get_etudiant_user($record->userid);
		    if ($record_etudiant){
					
					$Refpdf->SetLeftMargin(15);
					
					pdf_write_referentiel($referentiel_instance, $referentiel_referentiel, $param);
					
					pdf_write_etudiant( $record_etudiant, $param);
					
					$Refpdf->SetFont('helvetica','',12);
					if ($param->certificat_sel_decision_jury){
						if (($date_decision!="") && ($decision_jury!="")){
							$Refpdf->Write(6,$decision_jury);
						}
						$Refpdf->Ln(6);
					}
					
					// $Refpdf->SetFont('helvetica','B',10); 
					// $Refpdf->Write(6,"ID : ");
					// $Refpdf->SetFont('helvetica','',10);
					// $Refpdf->Write(6,"$id");
					// $Refpdf->Ln(6);
					
					$Refpdf->SetFont('helvetica','B',12); 
                    $Refpdf->Write(6,(get_string('competences','referentiel')).": ");
					$Refpdf->Ln(6);
					if ($param->certificat_sel_activite_competences){
						$Refpdf->SetFont('helvetica','B',9); 
                        $Refpdf->Write(6,(get_string('competences_activite','referentiel')).": ");
						$Refpdf->Ln(6); 
                        pdf_liste_competences_certificat($ref_referentiel, '/',':', $competences_activite, $liste_empreintes, 0, 9, 8);
						$Refpdf->Ln(6);
					}
					if ($param->certificat_sel_certificat_competences){
						$Refpdf->SetFont('helvetica','B',10); 
                        $Refpdf->Write(6,(get_string('competences_certificat','referentiel')).": ");
						$Refpdf->Ln(6);
                        pdf_liste_competences_certificat($ref_referentiel, '/',':', $competences_certificat, $liste_empreintes,0,10,9);
						$Refpdf->Ln(6);
					}
					if (($param->certificat_sel_certificat_competences) 
                        && ($param->certificat_sel_certificat_detail)){
						pdf_referentiel_affiche_detail_competences('/',':',$competences_certificat, $liste_empreintes, $liste_poids);
                    }
					if ($param->certificat_sel_certificat_pourcent){
                        // $Refpdf->SetFont('helvetica','B',10);
                        // $Refpdf->Write(6,(get_string('pourcentage','referentiel'))." :");
                        // $Refpdf->Ln(6);
                        pdf_referentiel_affiche_certificat_consolide($ref_referentiel, '/',':', $competences_certificat, 10,9,8);
					}
					
					if ($param->certificat_sel_commentaire){
						$Refpdf->SetFont('helvetica','B',10);
                        $Refpdf->Write(6,(get_string('commentaire','referentiel')).": ");
						$Refpdf->SetFont('helvetica','',10);
                        $Refpdf->Write(6,"$commentaire_certificat ");
						$Refpdf->Ln(6);
						$Refpdf->SetFont('helvetica','B',10);
                        $Refpdf->Write(6,(get_string('synthese_certificat','referentiel')).": ");
						$Refpdf->SetFont('helvetica','',10);
                        $Refpdf->Write(6,"$synthese_certificat ");
						$Refpdf->Ln(6);
					}
					if ($param->certificat_sel_decision_jury){
						$Refpdf->SetFont('helvetica','B',10);
                        $Refpdf->Write(6, (get_string('decision','referentiel'))." : ");
						$Refpdf->SetFont('helvetica','',10);
                        $Refpdf->Write(6,"$decision_jury");
						$Refpdf->Ln(6);
					}
					if ($param->certificat_sel_certificat_referents){
						$Refpdf->SetFont('helvetica','B',10);
						$Refpdf->Write(6,(get_string('enseignant','referentiel'))." : ");
						$Refpdf->SetFont('helvetica','',10);
						$Refpdf->Write(6,$nom_prenom_teacher);
						$Refpdf->Ln(6);
					}
					/*
					$Refpdf->Write(6," Référentiel : $ref_referentiel");
					$Refpdf->Ln(6);
		            $Refpdf->Write(6," Verrou : $verrou, Valide : $valide, Evaluation : $evaluation");
					$Refpdf->Ln(6);
					*/
					$Refpdf->Ln(20);
					$Refpdf->Write(6, get_string('date_signature','referentiel', date("d/m/Y")));
				}
			}
		}
}
	

function pdf_write_certification($referentiel_instance, $referentiel_referentiel, $userid=0, $param, $records_certificats) {
    	global $CFG;
		global $Refpdf;
		
		if ($referentiel_instance && $referentiel_referentiel) {
			// CERTIFICATS
			if (isset($referentiel_instance->ref_referentiel) && ($referentiel_instance->ref_referentiel>0)){
				// les empreintes
				$liste_empreintes = referentiel_purge_dernier_separateur(referentiel_get_liste_empreintes_competence($referentiel_instance->ref_referentiel), '/');
                $liste_poids=referentiel_purge_dernier_separateur(referentiel_get_liste_poids($referentiel_instance->ref_referentiel), '|');
				
                if ($userid>0){
					$record = referentiel_get_certificat_user($userid, $referentiel_instance->ref_referentiel);
					pdf_write_certificat( $record, $referentiel_instance, $referentiel_referentiel, $liste_empreintes, $liste_poids, $param);
				}
				else {
					if (!$records_certificats){
                        $records_certificats = referentiel_get_certificats($referentiel_instance->ref_referentiel);
					}
                    if ($records_certificats){
						foreach ($records_certificats as $record){
							pdf_write_certificat( $record, $referentiel_instance, $referentiel_referentiel, $liste_empreintes, $liste_poids, $param);
						}
					}
				}
				// print_r($records_certificats);
		    	// exit;
			}
		}
}
 

?>