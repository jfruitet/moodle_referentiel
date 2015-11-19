<?php 
// Based on default.php, included by ../import.php

class rformat_html extends rformat_default {

    function provide_export() {
      return true;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;
		global $USER; 
  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/html/html.css" );
		$css = implode( ' ',$css_lines ); 
		$xp = "<html>\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$xp .= "<meta author=\"".referentiel_get_user_info($USER->id)."\">\n";
  		$xp .= "<title>Moodle Referentiel HTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_h.html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
            return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
            return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=60) {
        // insere un saut de ligne apres le 80 caracter 
		$s=$raw;
		$s1="";
		$s2="";
		$out="";
		$nbcar=strlen($s);
		while ($nbcar>$nmaxcar){
			$s1=substr( $s,0,$nmaxcar);
			$pos1=strrpos($s1,$sep);
			if ($pos1>0){
				$s1=substr( $s,0,$pos1);
				$s=substr( $s,$pos1+1);
			}
			else {
				$s1=substr( $s,0,$nmaxcar);
				$s=substr( $s,$nmaxcar);
			}
			$out.=$s1. " ";
			$nbcar=strlen($s);
		}
		$out.=$s;
		return $out;
    }

    /**
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
            $code = $item->code_item;
            $description_item = $item->description_item;
            $ref_referentiel = $item->ref_referentiel;
            $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$num_item = $item->num_item;

            $expout .= "   <tr>\n";
			$expout .= "     <td class=\"item\"> ".stripslashes($code)."</td>\n";
            $expout .= "     <td class=\"item\"> ".stripslashes($description_item)."</td>\n";
            // $expout .= "   <td class=\"item\"> $ref_referentiel</td>\n";
            // $expout .= "   <td class=\"item\"> $ref_competence</td>\n";
            $expout .= "     <td class=\"item\"> ".stripslashes($type_item)."</td>\n";
            $expout .= "     <td class=\"item\"> $poids_item</td>\n";
            $expout .= "     <td class=\"item\"> $num_item</td>\n";
			$expout .= "   </tr>\n";
        }
        return $expout;
    }

	 /**
     * Turns competence into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_competence( $competence ) {
    global $CFG;
        // initial string;
        $expout = "";

		if ($competence){
            $code = $competence->code_competence;
            $description_competence = $competence->description_competence;
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;
// MODIF 2012/03/08
			$type_competence = trim($competence->type_competence);
			$seuil_competence = trim($competence->seuil_competence);
// MODIF 2012/03/26
            $minima_competence = trim($competence->minima_competence);

   			$expout .= "<table class='competence'>\n";
	        $expout .= "<tr>\n";
			$expout .= "    <th class=\"competence\"><b>".get_string('code_competence','referentiel')."</b></th>\n";
   	        $expout .= "    <th class=\"competence\"><b>".get_string('description_competence','referentiel')."</b></th>\n";
       	    // $expout .= "    <th class=\"competence\"><b>".get_string('ref_domaine','referentiel')."</b></th>\n";
// MODIF 2012/03/08
            $expout .= "   <th class=\"competence\"><b>".get_string('type_competence','referentiel')."</b></th>\n";
            $expout .= "   <th class=\"competence\"><b>".get_string('seuil_competence','referentiel')."</b></th>\n";
// MODIF 2012/03/26
            $expout .= "  <td class=\"competence\"><b>".get_string('minima_competence','referentiel')."</b></th>\n";

           	$expout .= "    <th class=\"competence\"><b>".get_string('num_competence','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('nb_item_competences','referentiel')."</b></th>\n";
			$expout .= "</tr>\n";

			$expout .= "  <tr>\n";
			$expout .= "    <td class=\"competence\"> ".stripslashes($code)."</td>\n";
            $expout .= "    <td class=\"competence\"> ".stripslashes($description_competence)."</td>\n";
            // $expout .= "  <td class=\"competence\"> $ref_domaine</td>\n";
// MODIF 2012/03/08
            $expout .= "  <td class=\"competence\">&nbsp; ".$type_competence."</td>\n";
            $expout .= "  <td class=\"competence\">&nbsp; ".$seuil_competence."</td>\n";
// MODIF 2012/03/26
            $expout .= "  <td class=\"competence\">&nbsp; ".$minima_competence."</td>\n";

            $expout .= "    <td class=\"competence\"> $num_competence</td>\n";
            $expout .= "    <td class=\"competence\"> $nb_item_competences</td>\n";
			$expout .= "  </tr>\n";
			$expout .= "</table>\n";

			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);

			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
   				$expout .= "<table class='item'>\n";
	       	 	$expout .= "   <tr>\n";
				$expout .= "     <th class=\"item\"><b>".get_string('code','referentiel')."</b></th>\n";
           		$expout .= "     <th class=\"item\"><b>".get_string('description_item','referentiel')."</b></th>\n";
		        // $expout .= "     <th class=\"item\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
    	   		// $expout .= "     <th class=\"item\"><b>".get_string('ref_competence','referentiel')."</b></th>\n";
	    	    $expout .= "     <th class=\"item\"><b>".get_string('type_item','referentiel')."</b></th>\n";
       			$expout .= "     <th class=\"item\"><b>".get_string('poids_item','referentiel')."</b></th>\n";
		        $expout .= "     <th class=\"item\"><b>".get_string('num_item','referentiel')."</b></th>\n";
				$expout .= "   </tr>\n";

				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
				$expout .= "</table>\n";
			}
        }
        return $expout;
    }


	 /**
     * Turns domaine into an xml segment
     * @param domaine object
     * @return string xml segment
     */

    function write_domaine( $domaine ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment

		if ($domaine){
            $code = $domaine->code_domaine;
            $description_domaine = $domaine->description_domaine;
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
// MODIF 2012/03/08
			$type_domaine = trim($domaine->type_domaine);
			$seuil_domaine = trim($domaine->seuil_domaine);
// MODIF 2012/03/26
            $minima_domaine = trim($domaine->minima_domaine);

			$expout .= "<br /><table class='domaine'>\n";
			$expout .= "<tr>\n";
			$expout .= "   <th class=\"domaine\"><b>".get_string('code_domaine','referentiel')."</b></th>\n";
		    $expout .= "   <th class=\"domaine\"><b>".get_string('description_domaine','referentiel')."</b></th>\n";
        	// $expout .= "   <th class=\"domaine\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
// MODIF 2012/03/08
            $expout .= "   <th class=\"domaine\"><b>".get_string('type_domaine','referentiel')."</b></th>\n";
            $expout .= "   <th class=\"domaine\"><b>".get_string('seuil_domaine','referentiel')."</b></th>\n";
// MODIF 2012/03/26
            $expout .= "   <th class=\"domaine\"><b>".get_string('minima_domaine','referentiel')."</b></th>\n";

		    $expout .= "   <th class=\"domaine\"><b>".get_string('num_domaine','referentiel')."</b></th>\n";
	        $expout .= "   <th class=\"domaine\"><b>".get_string('nb_competences','referentiel')."</b></th>\n";
			$expout .= "</tr>\n";

			$expout .= "<tr>\n";
			$expout .= "   <td class=\"domaine\"> ".stripslashes($code)."</td>\n";
            $expout .= "   <td class=\"domaine\"> ".stripslashes($description_domaine)."</td>\n";
            // $expout .= "   </td><td class=\"domaine\"> $ref_referentiel</td>\n";
// MODIF 2012/03/08
            $expout .= "  <td class=\"domaine\">&nbsp; ".$type_domaine."</td>\n";
            $expout .= "  <td class=\"domaine\">&nbsp; ".$seuil_domaine."</td>\n";
// MODIF 2012/03/26
            $expout .= "  <td class=\"domaine\">&nbsp; ".$minima_domaine."</td>\n";

            $expout .= "   <td class=\"domaine\"> $num_domaine</td>\n";
            $expout .= "   <td class=\"domaine\"> $nb_competences</td>\n";
			$expout .= "</tr>\n";
			$expout .= "</table>\n";

			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				// DEBUG
				// echo "<br/>DEBUG :: COMPETENCES <br />\n";
				// print_r($records_competences);
				foreach ($records_competences as $record_c){
					$expout .= $this->write_competence( $record_c );
				}
			}
        }
        return $expout;
    }

	 /**
     * Turns protocol into an xml segment
     * @param protocol object
     * @return string xml segment
     */
    // MODIF JF 2012/03/09
    function write_protocol( $protocole ){
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- protocole: $protocole->id  -->\n";
		//
		if ($protocole){
			$id = $protocole->id;
            $ref_occurrence = $protocole->ref_occurrence;
			$seuil_referentiel = $protocole->seuil_referentiel;
			$minima_referentiel = $protocole->minima_referentiel;
            $l_domaines_oblig = trim($protocole->l_domaines_oblig);
            $l_seuils_domaines = trim($protocole->l_seuils_domaines);
            $l_minimas_domaines = trim($protocole->l_minimas_domaines) ;
            $l_domaines_oblig = trim($protocole->l_domaines_oblig);
            $l_competences_oblig = trim($protocole->l_competences_oblig);
            $l_seuils_competences = trim($protocole->l_seuils_competences) ;
            $l_minimas_competences = trim($protocole->l_minimas_competences);
            $l_items_oblig = trim($protocole->l_items_oblig) ;
            $timemodified =$protocole->timemodified ;
			$actif = $protocole->actif;
            $commentaire = trim($protocole->commentaire);


	    	$expout .= "<br /><table class=\"referentiel\">\n";
			$expout .= "<tr>\n";
			$expout .= "   <th class=\"referentiel\">".get_string('seuil_protocole','referentiel')."</th>\n";
			$expout .= "   <th class=\"referentiel\">".get_string('minima_certificat','referentiel')."</th>\n";
		    $expout .= "   <th class=\"referentiel\">".get_string('oblig_domaine','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('seuil_domaine','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('minima_domaine','referentiel')."</th>\n";
		    $expout .= "   <th class=\"referentiel\">".get_string('oblig_competence','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('seuil_competence','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('minima_competence','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('oblig_item','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('date')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('protocole_active','referentiel')."</th>\n";
            $expout .= "   <th class=\"referentiel\">".get_string('commentaire','referentiel')."</th>\n";
			$expout .= "</tr>\n";

			$expout .= "<tr>\n";
			$expout .= "   <td class=\"referentiel\">".$seuil_referentiel."</td>\n";
			$expout .= "   <td class=\"referentiel\">".$minima_referentiel."</td>\n";
		    $expout .= "   <td class=\"referentiel\">".$l_domaines_oblig."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$l_seuils_domaines."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$l_minimas_domaines."</td>\n";
		    $expout .= "   <td class=\"referentiel\">".$l_competences_oblig."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$l_seuils_competences."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$l_minimas_competences."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$l_items_oblig."</td>\n";
            $expout .= "   <td class=\"referentiel\">".userdate($timemodified)."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$actif."</td>\n";
            $expout .= "   <td class=\"referentiel\">".$commentaire."</td>\n";
			$expout .= "</tr>\n";

			$expout .= "</table>\n";

        }
        return $expout;
    }


	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel() {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $this->rreferentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- date: ".date("Y/m/d")." referentiel:  ".$this->rreferentiel->id."  name: ".stripslashes($this->rreferentiel->name)." -->\n";
    	// add header
    	$expout .= "<h3>".stripslashes($this->rreferentiel->name)."</h3>\n";

		if ($this->rreferentiel){
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
            $minima_certificat = $this->rreferentiel->minima_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;
			$logo_referentiel = $this->rreferentiel->logo_referentiel;

	    	$expout .= "<table class=\"referentiel\">\n";
			$expout .= "<tr>\n";
			$expout .= " <th class=\"referentiel\"><b>".get_string('name','referentiel')."</b></th>\n";
			$expout .= " <th class=\"referentiel\"><b>".get_string('code_referentiel','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\" colspan=\"2\"><b>".get_string('description_referentiel','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('url_referentiel','referentiel')."</b></th>\n";
			$expout .= " </tr>\n";
			$expout .= "<tr>\n";
			$expout .= " <td class=\"referentiel\"> ".stripslashes($name)."</td>\n";
			$expout .= " <td class=\"referentiel\"> ".stripslashes($code_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\" colspan=\"2\"> ".stripslashes($description_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\"> <a href=\"".$url_referentiel."\" title=\"".$url_referentiel."\" target=\"_blank\">".$url_referentiel."</a></td>\n";
			$expout .= " </tr>\n";
			$expout .= "<tr>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('liste_codes_competence','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('liste_empreintes_competence','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('seuil_certificat','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('minima_certificat','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('nb_domaines','referentiel')."</b></th>\n";
            // $expout .= " <td class\"referentiel\"><b>".get_string('local','referentiel')."</b></td>\n";
			$expout .= "</tr>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_codes_competence,"/",60)."</td>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_empreintes_competence,"/",60)."</td>\n";
            $expout .= " <td class=\"referentiel\"> $seuil_certificat</td>\n";
            $expout .= " <td class=\"referentiel\"> $minima_certificat</td>\n";
            $expout .= " <td class=\"referentiel\"> $nb_domaines</td>\n";
            // $expout .= " <td class\"referentiel\"> $local</td>\n";
			// $expout .= " <td class\"referentiel\"> $logo_referentiel</td>\n";
			$expout .= "</tr>\n";
			$expout .= "</table>\n\n\n";

			// MODIF JF 2012/03/09
			// PROTOCOLE
            if (!empty($this->rreferentiel->id)){
                if ($record_protocol=referentiel_get_protocol($this->rreferentiel->id)){
                    $expout .= $this->write_protocol( $record_protocol );
                }
            }

			// DOMAINES
			if (isset($this->rreferentiel->id) && ($this->rreferentiel->id>0)){
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($this->rreferentiel->id);
		    	if ($records_domaine){
    				// afficher
					// DEBUG
					// echo "<br/>DEBUG ::<br />\n";
					// print_r($records_domaine);
					foreach ($records_domaine as $record_d){
						// DEBUG
						// echo "<br/>DEBUG ::<br />\n";
						// print_r($records_domaine);
						$expout .= $this->write_domaine( $record_d );
					}
				}
			}
		}
        return $expout;
    }
}

/**********************************************************************
***********************************************************************
									ACTIVITES
***********************************************************************
**********************************************************************/


// ACTIVITES : export des activites
class aformat_html extends aformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;

  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: Activite XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_h.html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
            return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
            return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=80) {
        // insere un saut de ligne apres le 80 caracter 
		$nbcar=strlen($raw);
		if ($nbcar>$nmaxcar){
			$s1=substr( $raw,0,$nmaxcar);
			$pos1=strrpos($s1,$sep);
			if ($pos1>0){
				$s1=substr( $raw,0,$pos1);
				$s2=substr( $raw,$pos1+1);
			}
			else {
				$s1=substr( $raw,0,$nmaxcar);
				$s2=substr( $raw,$nmaxcar);
			}
		    return $s1." ".$s2;
		}
		else{
			return $raw;
		}
    }

    /**
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */


	 /**
     * Turns document into an xml segment
     * @param document object
     * @return string xml segment
     */

    function write_document( $document ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment

		if ($document){
			$id = $document->id ;		
            $type_document = trim($document->type_document);
            $description_document = trim($document->description_document);
			$url_document = $document->url_document;
            $ref_activite = $document->ref_activite;
            $expout .= "  <tr>\n";
            $expout .= "     <td> $type_document</td>\n";
            $expout .= "     <td> $description_document</td>\n";
            $expout .= "     <td> $url_document</td>\n";
            $expout .= "     <td> $ref_activite</td>\n";
            $expout .= "  </tr>\n";
        }
        return $expout;
    }

    /**
     * Turns activite into an xml segment
     * @param activite object
     * @return string xml segment
     */

    function write_activite( $activite ) {
    global $CFG;
       // initial string;
        $expout = "";

		if ($activite){
			// DEBUG
			// echo "<br />\n";
			// print_r($activite);
			$id = $activite->id;
            $type_activite = trim($activite->type_activite);
            $description_activite = trim($activite->description_activite);
            $competences_activite = trim($activite->competences_activite);
            $commentaire_activite = trim($activite->commentaire_activite);
            $ref_instance = $activite->ref_instance;
            $ref_referentiel = $activite->ref_referentiel;
            $ref_course = $activite->ref_course;
			$userid = trim($activite->userid);
			$teacherid = $activite->teacherid;
			$date_creation = $activite->date_creation;
			$date_modif_student = $activite->date_modif_student;
			$date_modif = $activite->date_modif;
			$approved = $activite->approved;

            $firstname=referentiel_get_user_prenom($activite->userid);
            $lastname=referentiel_get_user_nom($activite->userid);

            $teacher_firstname=referentiel_get_user_prenom($activite->teacherid);
            $teacher_lastname=referentiel_get_user_nom($activite->teacherid);

            $expout .= "<tr>\n";
            $expout .= "<td> $id</td>\n";
			$expout .= "<td> $type_activite</td>\n";
            $expout .= "<td> $description_activite</td>\n";
            $expout .= "<td> $competences_activite</td>\n";
            $expout .= "<td> $commentaire_activite</td>\n";
            $expout .= "<td> $ref_instance</td>\n";
            $expout .= "<td> $ref_referentiel</td>\n";
            $expout .= "<td> $ref_course</td>\n";
            $expout .= "<td> $userid</td>\n";
            $expout .= "<td> $lastname</td>\n";
            $expout .= "<td> $firstname</td>\n";
            $expout .= "<td> $teacherid</td>\n";
            $expout .= "<td> $teacher_lastname</td>\n";
            $expout .= "<td> $teacher_firstname</td>\n";
            $expout .= "<td>".date("Y-m-d H:i:s",$date_creation)."</td>\n";
            $expout .= "<td>".date("Y-m-d H:i:s",$date_modif)."</td>\n";
            $expout .= "<td> $approved</td>\n";
			$expout .= "</tr>\n";
			// DOCUMENTS
			$records_documents = referentiel_get_documents($activite->id);
			
			if ($records_documents){
                $expout .= "<tr><td colspan=\"17\">\n";
                $expout .= "<table class=\"referentiel\">\n<tr>\n";
                $expout .= "<th>".get_string('type_document','referentiel')."</th>\n";
                $expout .= "<th>".get_string('description','referentiel')."</th>\n";
                $expout .= "<th>".get_string('url','referentiel')."</th>\n";
                $expout .= "<th>".get_string('ref_activite','referentiel')."</th>\n";
                $expout .= "</tr>\n";
                
				foreach ($records_documents as $record_d){
                    $expout .= $this->write_document( $record_d );
				}
				$expout .= "</table>\n</td></tr>\n";
			}
		}	

        return $expout;
    }

	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_activites() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- certification :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
    	// add header
    	$expout .= "<h3>".$this->ireferentiel->name."</h3>\n";
		// 

		// 
		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description_instance = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;

            $expout .= "<table class=\"referentiel\">\n";
		    $expout .= "<tr>\n";
			$expout .= " <td><b>".get_string('id','referentiel')."</b></td><td> $id</td>\n";
			$expout .= " <td><b>".get_string('name','referentiel')."</b></td><td> $name</td>\n";
			$expout .= " <td><b>".get_string('description_instance','referentiel')."</b></td><td> description_instance</td>\n";   
            $expout .= " <td><b>".get_string('label_domaine','referentiel')."</b></td><td> $label_domaine</td>\n";
            $expout .= " <td><b>".get_string('label_competence','referentiel')."</b></td><td> $label_competence</td>\n";
            $expout .= " <td><b>".get_string('label_item','referentiel')."</b></td><td> $label_item</td>\n";			
            $expout .= " <td><b>".get_string('date_instance','referentiel')."</b></td><td>".date("Y-m-d H:i:s",$date_instance)."</td>\n";
            $expout .= " <td><b>".get_string('course')."</b></td><td> $course</td>\n";
            $expout .= " <td><b>".get_string('ref_referentiel','referentiel')."</b></td><td> $ref_referentiel</td>\n";
            $expout .= " <td><b>".get_string('visible','referentiel')."</b></td><td> $visible</td>\n";
			$expout .= "</table>\n";
			$expout .= "<br />\n";
			
			// ACTIVITES
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_activites = referentiel_get_activites_instance($this->ireferentiel->id);
		    	if ($records_activites){
                    $expout .= "<table class=\"referentiel\"><tr>\n";
                    $expout .= "<th>".get_string('id','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('type_activite','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('description','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('competences_activite','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('commentaire','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('instance','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('ref_referentiel','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('ref_course','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('userid','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('lastname','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('firstname','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('teacherid','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('name','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('prenom','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('date_creation','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('date_modif','referentiel')."</th>\n";
                    $expout .= "<th>".get_string('approved','referentiel')."</th>\n";
                    $expout .= "</tr>\n";
					foreach ($records_activites as $record_a){
						// DEBUG
						// print_r($record_a);
						// echo "<br />\n";
						$expout .= $this->write_activite( $record_a );
					}
                    $expout .= "</table>\n";
			        $expout .= "<br />\n";
				}

            }
        }
        return $expout;
    }
}


// //////////////////////////////////////////////////////////////////////////////////////////////////////
// CERTIFICAT : export des certificats
// //////////////////////////////////////////////////////////////////////////////////////////////////////


class cformat_html extends cformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;

  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: Certificats XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_h.html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
            return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
            return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
            "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=80) {
        // insere un saut de ligne apres le 80 caracter 
		$nbcar=strlen($raw);
		if ($nbcar>$nmaxcar){
			$s1=substr( $raw,0,$nmaxcar);
			$pos1=strrpos($s1,$sep);
			if ($pos1>0){
				$s1=substr( $raw,0,$pos1);
				$s2=substr( $raw,$pos1+1);
			}
			else {
				$s1=substr( $raw,0,$nmaxcar);
				$s2=substr( $raw,$nmaxcar);
			}
		    return $s1." ".$s2;
		}
		else{
			return $raw;
		}
    }


    function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo = trim( $record->logo_etablissement);
			if (!$this->format_condense){
        		$expout .= "<tr><td colspan='9'>\n";
                $expout .= "<b>".get_string('etablissement','referentiel')."</b>\n";
                $expout .= "</td></tr><tr>\n";
                $expout .= " <td><b>".get_string('id','referentiel')."</b></td>
<td><b>".get_string('num_etablissement','referentiel')."</b></td>
<td colspan='2'><b>".get_string('nom_etablissement','referentiel')."</b></td>
<td colspan='4'><b>".get_string('adresse_etablissement','referentiel')."</b></td>
<td colspan='2'><b>".get_string('logo','referentiel')."</b></td></tr>\n";

                $expout .= "<tr>\n<td> $id</td>
            <td> $num_etablissement</td>
<td colspan='2'> $nom_etablissement</td>
<td colspan='4'> $adresse_etablissement</td>
<td colspan='2'> $logo</td>\n</tr>\n";
            }
        }
        return $expout;
    }


	
	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment

		if ($record){
			// DEBUG
			// echo "<br />\n";
			// print_r($record);
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
                    $texte=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $texte=$login;
                }
                else{
                    $texte=$num_etudiant." (".$login.")";
                }

            if (!$this->format_condense){
                $expout .= "\n\n<!-- record etudiant: $id  -->\n";
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr><td colspan='9'><b>Etudiant</b></td></tr>\n";
                $expout .= "<tr><td><b>".get_string('id','referentiel')."</b></td>
<td><b>".get_string('userid','referentiel')."</b></td>
<td><b>".get_string('nom_prenom','referentiel')."</b></td>
<td><b>".get_string('num_etudiant','referentiel')."</b></td>
<td><b>".get_string('ddn_etudiant','referentiel')."</b></td>
<td><b>".get_string('lieu_naissance','referentiel')."</b></td
<td><b>".get_string('departement_naissance','referentiel')."</b></td>
<td><b>".get_string('adresse_etudiant','referentiel')."</b></td>
<td><b>".get_string('ref_etablissement','referentiel')."</b></td>
</td>\n<tr>\n";
                $expout .= " <td> $id</td><td> $userid</td><td> ".referentiel_get_user_info($record->userid)."</td>
<td> $texte</td><td> $ddn_etudiant</td><td> $lieu_naissance</td><td> $departement_naissance</td>
<td> $adresse_etudiant</td><td> $ref_etablissement</td>\n";

                // Etablissement
                $record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
                if ($record_etablissement){
                    $expout .= $this->write_etablissement( $record_etablissement );
                }
                $expout .= "</table>\n\n";
            }
            else if ($this->format_condense==1){
                $expout .= "<tr><td> $userid</td><td>".referentiel_get_user_login($userid)."</td><td> ".referentiel_get_user_info($record->userid)."</td><td> $num_etudiant</td>";
            }
            else{
                $expout .= "<tr><td>$login</td><td>$num_etudiant</td><td>".referentiel_get_user_nom($userid)."</td><td> ".referentiel_get_user_prenom($record->userid)."</td>";
            }
        }
        return $expout;
    }

	 /**
     * Turns referentiel instance into an html segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certificat( $record ) {
    	global $CFG;
        // initial string;
        $expout = "";
    	// add comment and div tags

		if ($record){
            // DEBUG
            // echo "<br />DEBUG LIGNE 1021<br />\n";
            // print_r($referentiel_instance);
            $id = trim( $record->id );
            if (isset($record->synthese_certificat)){
                $synthese_certificat = trim($record->synthese_certificat);
            }
            else{
                $synthese_certificat = '';
            }
            $commentaire_certificat = trim($record->commentaire_certificat);
            $synthese_certificat = trim($record->synthese_certificat);
            $competences_certificat =  trim($record->competences_certificat) ;
            $decision_jury = trim($record->decision_jury);
            $date_decision = userdate(trim($record->date_decision));
            $userid = trim( $record->userid);
            $teacherid = trim( $record->teacherid);
            $ref_referentiel = trim( $record->ref_referentiel);
            $verrou = trim( $record->verrou );
            $valide = trim( $record->valide );
            $evaluation = trim( $record->evaluation );
            $synthese_certificat = trim($record->synthese_certificat);

    	    // add header
            if (!$this->format_condense){
                $expout .= "<!-- record certification :  $record->id  -->\n";
                $expout .= "<p>&nbsp;</p>\n<p align='center'><b>".get_string('certificat','referentiel')."</b> #".$record->id."</p>\n";
            }

            // USER
            if (isset($userid) && ($userid>0)){
                $record_etudiant = referentiel_get_etudiant_user($userid);
    			if (!$record_etudiant){
                    // creer l'enregistrement car on en a besoin immediatement
                    if (referentiel_add_etudiant_user($userid)){
                        $record_etudiant = referentiel_get_etudiant_user($userid);
                    }
                }

		    	if ($record_etudiant){
                    $expout .= $this->write_etudiant( $record_etudiant );
                }
                if (!$this->format_condense){
                    $expout .= "<table class=\"referentiel\">\n";

                    $expout .= "<tr>\n
<td><b>".get_string('synthese_certificat','referentiel')."</b></td>
<td><b>".get_string('commentaire_certificat','referentiel')."</b></td>
<td><b>".get_string('synthese_certificat','referentiel')."</b></td>
<td><b>".get_string('competences_certificat','referentiel')."</b></td>
<td><b>".get_string('decision_jury','referentiel')."</b></td>
<td><b>".get_string('date_decision','referentiel')."</b></td>
<!-- <td><b>".get_string('ref_referentiel','referentiel')."</b></td> -->
<td><b>".get_string('verrou','referentiel')."</b></td>
<!-- <td><b>".get_string('valide','referentiel')."</b></td> -->
<td><b>".get_string('evaluation','referentiel')."</b></td>
<td><b>".get_string('synthese','referentiel')."</b></td>
</tr>\n";
                    $expout .= "<tr>\n
<td> $synthese_certificat</td>
<td> $commentaire_certificat</td>
<td> $synthese_certificat</td>
<td> $competences_certificat</td>
<td> $decision_jury</td>
<td>".$date_decision."</td>
<!-- <td> $ref_referentiel</td>  -->
<td> $verrou</td>
<!-- <td> $valide</td>  -->
<td> $evaluation</td>\n
<td> $synthese_certificat</td>
</tr>\n";
                    $expout .= "</table>\n<br />\n<br />\n";
                }
                elseif ($this->format_condense==1){
                    $expout .= $this->certificat_pourcentage($competences_certificat, $this->ref_referentiel)."\n";
                    $expout .= "</tr>\n";
                }
                else {
                    $expout .= $this->certificat_items_binaire($competences_certificat, $this->ref_referentiel)."\n";
                    $expout .= "</tr>\n";
                }
            }
		}
        return $expout;
    }

    function write_referentiel() {
    	global $CFG;
        // initial string;
		$expout ="";
		if ($this->rreferentiel){
			$id = $this->rreferentiel->id;
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;

			// $expout = "#Referentiel : ".$this->rreferentiel->id." : ".stripslashes($this->rreferentiel->name)."\n";
            // add header
            if ($this->format_condense==1){
                // echo "DEBUG :: ".$this->format_condense."\n";
                // exit;
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr><td><b>".get_string('referentiel','referentiel')."</b></td>\n";
                $expout .= "<td>".stripslashes($name)."</td><td>".stripslashes($code_referentiel)."</td><td>".stripslashes($description_referentiel)."</td></tr>\n";
                $expout .= "</table>\n";
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr><th>user_id</th><th>".get_string('login', 'referentiel')."</th><th>".get_string('firstname')." ".get_string('lastname')."</th><th>".get_string('num_etudiant','referentiel')."</th>\n";
                $expout .= $this->liste_codes_competences($this->rreferentiel->id)."</tr>\n";
            }
            else{
                // echo "DEBUG :: ".$this->format_condense."\n";
                // exit;
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr><td><b>".get_string('referentiel','referentiel')."</b></td>\n";
                $expout .= "<td>".stripslashes($name)."</td><td>".stripslashes($code_referentiel)."</td><td>".stripslashes($description_referentiel)."</td></tr>\n";
                $expout .= "</table>\n";
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr><th>".get_string('login', 'referentiel')."</th><th>".get_string('num_etudiant','referentiel')."</th><th>".get_string('lastname')."</th><th>".get_string('firstname')."</th>\n";
                $expout .= $this->liste_items_competences($this->rreferentiel->id)."</tr>\n";
            }
            /*
            else{     // developper le referentiel ?
                $expout .= "#id_referentiel;name;code_referentiel;description_referentiel;url_referentiel;seuil_certificat;timemodified;nb_domaines;liste_codes_competences;liste_empreintes_competences;local\n";
                $expout .= "$id;".stripslashes($name).";".stripslashes($code_referentiel).";".stripslashes($description_referentiel).";$url_referentiel;$seuil_certificat;".referentiel_timestamp_date_special($timemodified).";$nb_domaines;".stripslashes($liste_codes_competence).";".stripslashes($liste_empreintes_competence).";$local\n";

                // DOMAINES
                if (isset($this->rreferentiel->id) && ($this->rreferentiel->id>0)){
                    // LISTE DES DOMAINES
                    $compteur_domaine=0;
                    $records_domaine = referentiel_get_domaines($this->rreferentiel->id);
                    if ($records_domaine){
                        // afficher
                        // DEBUG
                        // echo "<br/>DEBUG ::<br />\n";
                        // print_r($records_domaine);
                        foreach ($records_domaine as $record_d){
                            // DEBUG
                            // echo "<br/>DEBUG ::<br />\n";
                            // print_r($records_domaine);
                            $expout .= $this->write_domaine( $record_d );
                        }
                    }
				}
			}
			*/
        }
        return $expout;
  }


	
	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

  function write_certification() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

		//
		if ($this->ireferentiel){
            $id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description = trim($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
            $visible = $this->ireferentiel->visible;

            $expout .= "<!-- certification :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
            $expout .= "<h2>".$this->ireferentiel->name."</h2>\n";



            if ($this->format_condense){
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= "<tr>\n";
                $expout .= " <td><b>".get_string('instance','referentiel')."</b></td>";
                $expout .= " <td> $name</td>\n";
                $expout .= " <td> $description</td>\n";
                $expout .= "</tr>\n</table>";
            }
            else{
    	        //
                $expout .= "<table class=\"referentiel\">\n";
                $expout .= " <td colspan='10'><b>".get_string('instance','referentiel')."</b></td>";
                $expout .= "</tr>\n<tr>\n";
                $expout .= " <td><b>".get_string('id','referentiel')."</b></td><td><b>".get_string('name','referentiel')."</b></td><td><b>".get_string('description','referentiel')."</b></td>
<td><b>".get_string('label_domaine','referentiel')."</b></td><td><b>".get_string('label_competence','referentiel')."</b></td><td><b>".get_string('label_item','referentiel')."</b></td>
<td><b>".get_string('date_instance','referentiel')."</b></td><td><b>".get_string('course')."</b></td><td><b>".get_string('ref_referentiel','referentiel')."</b></td>
<td><b>".get_string('visible','referentiel')."</b></td>\n";
                $expout .= "</tr>\n<tr>\n";
                $expout .= " <td> $id</td>\n";
                $expout .= " <td> $name</td>\n";
                $expout .= " <td> $description</td>\n";
                $expout .= " <td> $label_domaine</td>\n";
                $expout .= " <td> $label_competence</td>\n";
                $expout .= " <td> $label_item</td>\n";
                $expout .= " <td>".date("Y-m-d H:i:s",$date_instance)."</td>\n";
                $expout .= " <td> $course</td>\n";
                $expout .= " <td> $ref_referentiel</td>\n";
                $expout .= " <td> $visible</td>\n";
                $expout .= "</tr>\n</table><br />";
            }
        }
            // CERTIFICATS
        if (empty($this->rreferentiel) && (!empty($this->ireferentiel->ref_referentiel) && ($this->ireferentiel->ref_referentiel>0))){
            $this->rreferentiel = referentiel_get_referentiel_referentiel($this->ireferentiel->ref_referentiel);
        }

        if (!empty($this->rreferentiel)){
                $expout .= $this->write_referentiel();
            
                if (!$this->records_certificats){
                    $this->records_certificats = referentiel_get_certificats($this->rreferentiel->id);
                }
                // print_r($this->records_certificats);
                    	
                if ($this->records_certificats){
                    foreach ($this->records_certificats as $record){
                        $expout .= $this->write_certificat( $record );
                    }
                }

        }

        return $expout;
    }

     // -------------------
    function liste_codes_competences($ref_referentiel){
    global $OK_REFERENTIEL_DATA;
    // COMPETENCES
    global $t_competence;  // codes des competences
	// affichage
	$s='';

        if ($ref_referentiel){
                    if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
                        $OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
                    }

                    if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
                        for ($i=0; $i<count($t_competence); $i++){
                    		$s.='<th>'.$t_competence[$i].'</th>';
                        }
                    }
        }
        return $s;

    }

     // -------------------
    function liste_items_competences($ref_referentiel){
    global $OK_REFERENTIEL_DATA;
    // COMPETENCES
    global $t_item_code; // codes des items competences
	// affichage
	$s='';

        if ($ref_referentiel){
                    if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
                        $OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
                    }

                    if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
                        for ($i=0; $i<count($t_item_code); $i++){
                    		$s.='<th>'.$t_item_code[$i].'</th>';
                        }
                    }
        }
        return $s;

    }


    // -------------------
    function certificat_pourcentage($liste_code, $ref_referentiel){
    // retourne les pourcentages par competence

    $separateur1='/';
    $separateur2=':';

    global $OK_REFERENTIEL_DATA;
    global $t_domaine;
    global $t_domaine_coeff;

    // COMPETENCES
    global $t_competence;
    global $t_competence_coeff;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associé à un item
    global $t_item_competence; // index de la competence associée à un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;
    global $t_nb_item_competence;

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
	// affichage
	$s='';

	// donnees globales du referentiel
	if ($ref_referentiel){

		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
                    $OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
		// DEBUG
		// echo "<br />CODE <br />\n";
		// referentiel_affiche_data_referentiel($ref_referentiel, $params);

		// recuperer les items valides
		$tc=array();
		$liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);

		// DEBUG
		// echo "<br />DEBUG :: print_lib_certificat.php :: 917 :: LISTE : $liste_code<br />\n";

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
                    /*
        	for ($i=0; $i<count($t_domaine_coeff); $i++){
                    	if ($t_domaine_coeff[$i]){
                    		$s.=$t_domaine[$i].';';
                    	}
                    	else{
                    		$s.=$t_domaine[$i].';';
                    	}
                    }
                    $s.="\n";

                    for ($i=0; $i<count($t_domaine_coeff); $i++){
                    	if ($t_domaine_coeff[$i]){
                    		$s.=referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%;';
                    	}
                    	else{
                    		$s.='0%;';
                    	}
                    }
                    $s.="\n";
                    */

                    /*
                    for ($i=0; $i<count($t_competence); $i++){
                    		$s.=$t_competence[$i].';';
                    }
                    $s.="\n";
                    */
                    for ($i=0; $i<count($t_competence); $i++){
                    		$s.='<td>'.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%</td>';
                    }
                    // $s.="\n";

                    // ITEMS
                    /*
                    for ($i=0; $i<count($t_item_code); $i++){
                    	if ($t_item_empreinte[$i]){
                    		if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i])
                                        $s.=$t_item_code[$i].';';
                    		else
                                        $s.=$t_item_code[$i].';';
                    	}
                    	else{
                    		$s.=';';
                    	}
                    }
                    $s.="\n";

                    for ($i=0; $i<count($t_item_coeff); $i++){
                    	if ($t_item_empreinte[$i]){
                    		if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
                                        $s.='100%;';
                    		}
                    		else{
                                        $s.=referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%;';
                    		}
                    	}
                    	else {
                    		$s.=';';
                    	}
                    }
                    $s.="\n";
                    */
		}
	}
	}

	return $s;
    }

    // -------------------
    function certificat_items_binaire($liste_code, $ref_referentiel){
    // retourne les valeur 0/1 pour chaque item de competence

    $separateur1='/';
    $separateur2=':';

    global $OK_REFERENTIEL_DATA;

    // ITEMS
    global $t_item_code;
    global $t_item_coeff; // coefficient poids determine par le modele de calcul (soit poids soit poids / empreinte)
    global $t_item_domaine; // index du domaine associé à un item
    global $t_item_competence; // index de la competence associée à un item
    global $t_item_poids; // poids
    global $t_item_empreinte;
    global $t_nb_item_domaine;
    global $t_nb_item_competence;

	$t_certif_item_valeur=array();	// table des nombres d'items valides
	// affichage
	$s='';

	// donnees globales du referentiel
	if ($ref_referentiel){

		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){
            // DEBUG
            // echo "<br />CODE <br />\n";
            // referentiel_affiche_data_referentiel($ref_referentiel, $params);

            // recuperer les items valides
            $tc=array();
            $liste_code=referentiel_purge_dernier_separateur($liste_code, $separateur1);

            // DEBUG
            // echo "<br />DEBUG :: print_lib_certificat.php :: 917 :: LISTE : $liste_code<br />\n";

            if (!empty($liste_code) && ($separateur1!="") && ($separateur2!="")){
                $tc = explode ($separateur1, $liste_code);

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
    						}
	       					else{
		      					// echo "<br />".min($t_cc[1],$t_item_empreinte[$i]);
			     				$t_certif_item_valeur[$i]=0.0;
				       			$t_certif_item_coeff[$i]=0.0;
						}
					}
				}
				$i++;
			}

			// ITEMS
/*
			for ($i=0; $i<count($t_item_code); $i++){
                $s.=$t_item_code[$i].';';
			}
			$s.="\n";
*/

			for ($i=0; $i<count($t_item_coeff); $i++){
				if ($t_item_empreinte[$i]){
                    //$s.='<td>'.$t_item_code[$i].': ';
                    $s.='<td>';
    				if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.='1 ';
					}
					else{
                        $s.='0 ';
					}
                    $s.='</td>';
				}
    		}
            $s.="\n";
		}
	}
	}

	return $s;
    }

} // fin de la classe

// ETUDIANTS : export des etudiants
class eformat_html extends eformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;

  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/xhtml/xhtml.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: Certificats XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_h.html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
                    return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
                    return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
                    "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=80) {
        // insere un saut de ligne apres le 80 caracter 
		$nbcar=strlen($raw);
		if ($nbcar>$nmaxcar){
                    $s1=substr( $raw,0,$nmaxcar);
                    $pos1=strrpos($s1,$sep);
                    if ($pos1>0){
                    	$s1=substr( $raw,0,$pos1);
                    	$s2=substr( $raw,$pos1+1);
                    }
                    else {
                    	$s1=substr( $raw,0,$nmaxcar);
                    	$s2=substr( $raw,$nmaxcar);
                    }
		    return $s1." ".$s2;
		}
		else{
                    return $raw;
		}
    }


	function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
    		$expout .= "<table class=\"referentiel\">\n";
                    // $expout .= "<h4>".get_string('etablissement','referentiel')."</h4>\n";
                    $expout .= "<tr>\n";
                    $id = trim( $record->id );
                    $num_etablissement = trim( $record->num_etablissement);
                    $nom_etablissement = trim( $record->nom_etablissement);
                    $adresse_etablissement = trim( $record->adresse_etablissement);
                    $logo = trim( $record->logo_etablissement);
                    
                    $expout .= " <td><b>".get_string('id','referentiel')."</b></td><td> $id</td>\n";
                    $expout .= " <td><b>".get_string('num_etablissement','referentiel')."</b></td><td> $num_etablissement</td>\n";
                    $expout .= " <td><b>".get_string('nom_etablissement','referentiel')."</b></td><td> $nom_etablissement</td>\n";                    
                    $expout .= " <td><b>".get_string('adresse_etablissement','referentiel')."</b></td><td> $adresse_etablissement</td>\n";
//                    $expout .= " <td><b>".get_string('logo','referentiel')."</b></td><td> $logo</td>\n";                    
                    $expout .= " </tr>\n";
                    $expout .= "</table>\n\n";
        }
        return $expout;
    }


	
	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- etudiant: $record->id  -->\n";
		if ($record){
	    	// add header
   		// $expout .= "<h4>".get_string('etudiant','referentiel')."</h4>\n";
                    $expout .= "<tr>\n";		// 
                    
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
                    $texte=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $texte=$login;
                }
                else{
                    $texte=$num_etudiant." (".$login.")";
                }

                    $expout .= " <td> $id</td>\n";
                    $expout .= " <td> $userid</td>\n";	
                    $expout .= " <td> ".referentiel_get_user_info($record->userid)."</td>\n";
                    $expout .= " <td> $texte</td>\n";
                    $expout .= " <td> $ddn_etudiant</td>\n";
                    $expout .= " <td> $lieu_naissance</td>\n";
                    $expout .= " <td> $departement_naissance</td>\n";                    
                    $expout .= " <td> $adresse_etudiant</td>\n";
                    $expout .= " <td> $ref_etablissement</td>\n";
                    /*
                    // Etablissement
                    $record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
                    	$expout .= $this->write_etablissement( $record_etablissement );
                    }
                    */
		    $expout .= " </tr>\n";//                    
        }
        return $expout;
    }


	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_etudiants() {
    	global $CFG;
        // initial string;
        $expout = "";
	    $id = $this->ireferentiel->id;

    	// add comment and div tags
//    	$expout .= "<!-- etudiants :  $this->ireferentiel->id  name: $this->ireferentiel->name -->\n";
    	// add header
    	$expout .= "<h2>".get_string('etudiant','referentiel')."</h2>\n";
		// 
    	$expout .= "<table class=\"referentiel\">\n";

		$expout .= "<tr>\n";
		// 
		if ($this->ireferentiel){
                    $id = $this->ireferentiel->id;
                    $name = trim($this->ireferentiel->name);
                    $description = trim($this->ireferentiel->description_instance);
                    $label_domaine = trim($this->ireferentiel->label_domaine);
                    $label_competence = trim($this->ireferentiel->label_competence);
                    $label_item = trim($this->ireferentiel->label_item);
                    $date_instance = $this->ireferentiel->date_instance;
                    $course = $this->ireferentiel->course;
                    $ref_referentiel = $this->ireferentiel->ref_referentiel;
                    $visible = $this->ireferentiel->visible;
/*
                    $expout .= " <td><b>".get_string('id','referentiel')."</b></td><td> $id</td>\n";
                    $expout .= " <td><b>".get_string('name','referentiel')."</b></td><td> $name</td>\n";
                    $expout .= " <td><b>".get_string('description','referentiel')."</b></td><td> $description</td>\n";   
                    $expout .= " <td><b>".get_string('label_domaine','referentiel')."</b></td><td> $label_domaine</td>\n";
                    $expout .= " <td><b>".get_string('label_competence','referentiel')."</b></td><td> $label_competence</td>\n";
                    $expout .= " <td><b>".get_string('label_item','referentiel')."</b></td><td> $label_item</td>\n";                    
                    $expout .= " <td><b>".get_string('date_instance','referentiel')."</b></td><td>".date("Y-m-d H:i:s",$date_instance)."</td>\n";
                    $expout .= " <td><b>".get_string('course')."</b></td><td> $course</td>\n";
                    $expout .= " <td><b>".get_string('ref_referentiel','referentiel')."</b></td><td> $ref_referentiel</td>\n";
                    $expout .= " <td><b>".get_string('visible','referentiel')."</b></td><td> $visible</td>\n";
*/                    
                    // ETUDIANTS
                    if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
                    	// ETUDIANTS
                    	$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
                    	if ($records_all_students){
                            $expout .= "<table class=\"referentiel\">\n";
                            // $expout .= "<h4>".get_string('etudiant','referentiel')."</h4>\n";
                            $expout .= "<tr>\n";		//
                            $expout .= "<th>".get_string('id','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('userid','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('nom_prenom','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('num_etudiant','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('ddn_etudiant','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('lieu_naissance','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('departement_naissance','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('adresse_etudiant','referentiel')."</th>\n";
                            $expout .= " <th>".get_string('ref_etablissement','referentiel')."</th>\n";
                            $expout .= " </tr>\n";
                    		
                    	
                    		foreach ($records_all_students as $record){
                                // USER
                                if (isset($record->userid) && ($record->userid>0)){
                                    $record_etudiant = referentiel_get_etudiant_user($record->userid);

                        			if (!$record_etudiant){
                                        // creer l'enregistrement car on en a besoin immediatement
                                        if (referentiel_add_etudiant_user($record->userid)){
                                            $record_etudiant = referentiel_get_etudiant_user($record->userid);
                                        }
                                    }
                                    if ($record_etudiant){
                                        $expout .= $this->write_etudiant( $record_etudiant );
                                    }
                                }
                            }
                    		$expout .= "</table>\n\n";		//
                    	}
                    }
    }
	  $expout .= " </tr>\n";
		$expout .= "</table>\n";
    return $expout;
  }

	function write_liste_etablissements() {
    	global $CFG;
        // initial string;
        $expout = ""; 
		// ETABLISSEMENTS
		$records_all_etablissements = referentiel_get_etablissements();
		if ($records_all_etablissements){
                    foreach ($records_all_etablissements as $record){
                    	if ($record){
                    		$expout.=$this->write_etablissement($record);
                    	}
                    }
        }
        return $expout;
    }

}

/**********************************************************************
***********************************************************************
                                                                taskS
***********************************************************************
**********************************************************************/


// taskS : export des tasks
class tformat_html extends tformat_default {

    function provide_export() {
      return true;
    }

	function provide_import() {
        return false;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text ); 
	}

	function presave_process( $content ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;

  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/html/html.css" );
		$css = implode( ' ',$css_lines ); 
		$xp =  "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n";
		$xp .= "  \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		$xp .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
  		$xp .= "<title>Moodle Referentiel :: TASK XHTML Export</title>\n";
  		$xp .= $css;
  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= $content;
		$xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

	function export_file_extension() {
  		return "_h.html";
	}

    /**
     * Include an image encoded in base 64
     * @param string imagepath The location of the image file
     * @return string xml code segment 
     */
    function writeimage( $imagepath ) {
        global $CFG;
   		
        if (empty($imagepath)) {
                    return '';
        }

        $courseid = $this->course->id;
        if (!$binary = file_get_contents( "{$CFG->dataroot}/$courseid/$imagepath" )) {
                    return '';
        }

        $content = "    <image_base64>\n".addslashes(base64_encode( $binary ))."\n".
                    "\n    </image_base64>\n";
        return $content;
    }

	
	/**
     * generates <text></text> tags, processing raw text therein 
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function write_ligne( $raw, $sep="/", $nmaxcar=80) {
        // insere un saut de ligne apres le 80 caracter 
		$nbcar=strlen($raw);
		if ($nbcar>$nmaxcar){
                    $s1=substr( $raw,0,$nmaxcar);
                    $pos1=strrpos($s1,$sep);
                    if ($pos1>0){
                    	$s1=substr( $raw,0,$pos1);
                    	$s2=substr( $raw,$pos1+1);
                    }
                    else {
                    	$s1=substr( $raw,0,$nmaxcar);
                    	$s2=substr( $raw,$nmaxcar);
                    }
		    return $s1." ".$s2;
		}
		else{
                    return $raw;
		}
    }

 
	 /**
     * Turns consigne into an html segment
     * @param consigne object
     * @return string html 
     */

    function write_consigne( $consigne ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment

		if ($consigne){
            $id = $consigne->id ;
            $type_consigne = trim($consigne->type_consigne);
            $description_consigne = trim($consigne->description_consigne);
            $url_consigne = $consigne->url_consigne;
            if (preg_match("/http/",$url_consigne)){
                $url_consigne='<a href="'.$url_consigne.'" target="_blank">'.$url_consigne.'</a>';
            }
            $ref_task = $consigne->ref_task;
            $expout .= "   <tr>\n";
            $expout .= "     <td class=\"item\"> $type_consigne</td>\n";
            $expout .= "     <td class=\"item\"> $description_consigne</td>\n";
            $expout .= "     <td class=\"item\"> $url_consigne</td>\n";
            $expout .= "     <td class=\"item\"> $ref_task</td>\n";
            $expout .= "   </tr>\n";
        }
        return $expout;
    }

    /**
     * Turns task into an xml segment
     * @param task object
     * @return string xml segment
     */

    function write_task( $task ) {
    global $CFG;
       // initial string;
        $expout = "";
        // add comment
		if ($task){
            // DEBUG
            // echo "<br />\n";
            // print_r($task);
            $id = $task->id;
            $type_task = trim($task->type_task);
            $description_task = trim($task->description_task);
            $competences_task = trim($task->competences_task);
            $criteres_evaluation = trim($task->criteres_evaluation);
            $ref_instance = $task->ref_instance;
            $ref_referentiel = $task->ref_referentiel;
            $ref_course = $task->ref_course;
            $auteurid = trim($task->auteurid);
            $date_creation = $task->date_creation;
            $date_modif = $task->date_modif;
            $date_debut = $task->date_debut;
            $date_fin = $task->date_fin;

	        $expout .= "\n\n<!-- task: $task->id  -->\n";

            $expout .= "<table class='competence'>\n";
	        $expout .= "<tr>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('id','referentiel')."</b></th>\n";
   	        $expout .= "    <th class=\"competence\"><b>".get_string('type_task','referentiel')."</b></th>\n";
       	    $expout .= "    <th class=\"competence\"><b>".get_string('description','referentiel')."</b></th>\n";
           	$expout .= "    <th class=\"competence\"><b>".get_string('competences','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('criteres_evaluation','referentiel')."</b></th>\n";
            // $expout .= "    <th class=\"competence\"><b>".get_string('instance','referentiel')."</b></th>\n";
            // $expout .= "    <th class=\"competence\"><b>".get_string('referentiel','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('course')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('auteur','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('date_creation','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('date_modif','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('date_debut','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('date_fin','referentiel')."</b></th>\n";
            $expout .= "</tr>\n";
           	$expout .= "<tr>\n";
            $expout .= "<td class=\"competence\"> $id</td>\n";
            $expout .= "<td class=\"competence\"> $type_task</td>\n";
            $expout .= "<td class=\"competence\"> $description_task</td>\n";
            $expout .= "<td class=\"competence\"> $competences_task</td>\n";
            $expout .= "<td class=\"competence\"> $criteres_evaluation</td>\n";
/*
            $expout .= "<td class=\"competence\"> $ref_instance</td>\n";
            $expout .= "<td class=\"competence\"> $ref_referentiel</td>\n";
*/
            $expout .= "<td class=\"competence\">".referentiel_get_course_link($ref_course,true)."</td>\n";
            $expout .= "<td class=\"competence\">".referentiel_get_user_info($auteurid)."</td>\n";
            $expout .= "<td class=\"competence\">".date("Y-m-d H:i:s",$date_creation)."</td>\n";
            $expout .= "<td class=\"competence\">".date("Y-m-d H:i:s",$date_modif)."</td>\n";
            $expout .= "<td class=\"competence\">".date("Y-m-d H:i:s",$date_debut)."</td>\n";
            $expout .= "<td class=\"competence\">".date("Y-m-d H:i:s",$date_fin)."</td>\n";
            $expout .= "</tr>\n";
            $expout .= "</table>\n";
                    
            // consigneS
            $records_consignes = referentiel_get_consignes($task->id);
                    
            if ($records_consignes){
                    	// DEBUG
                    	// echo "<br/>DEBUG :: ITEMS <br />\n";
                    	// print_r($records_consignes);
                       	$expout .= "<table class='item'>\n";
	       	 	$expout .= "   <tr>\n";
                    	$expout .= "     <th class=\"item\"><b>".get_string('type_consigne','referentiel')."</b></th>\n";   
           		$expout .= "     <th class=\"item\"><b>".get_string('description','referentiel')."</b></th>\n";
	    	    $expout .= "     <th class=\"item\"><b>".get_string('url','referentiel')."</b></th>\n";
                           $expout .= "     <th class=\"item\"><b>".get_string('task','referentiel')."</b></th>\n";
                    	$expout .= "   </tr>\n"; 
                    	foreach ($records_consignes as $record_d){
                    		$expout .= $this->write_consigne( $record_d );
                    	}
                    	$expout .= "</table>\n";
            }
		}	

        return $expout;
    }

    /**
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
		if ($item){
                    // DEBUG
                    // echo "<br />\n";
                    // print_r($item);
                    $code = $item->code_item;
                    $description_item = $item->description_item;
                    $ref_referentiel = $item->ref_referentiel;
                    $ref_competence = $item->ref_competence;
                    $type_item = $item->type_item;
                    $poids_item = $item->poids_item;
                    $num_item = $item->num_item;

                    $expout .= "   <tr>\n";
                    $expout .= "     <td class=\"item\"> ".stripslashes($code)."</td>\n";   
                    $expout .= "     <td class=\"item\"> ".stripslashes($description_item)."</td>\n";
                    // $expout .= "   <td class=\"item\"> $ref_referentiel</td>\n";
                    // $expout .= "   <td class=\"item\"> $ref_competence</td>\n";
                    $expout .= "     <td class=\"item\"> ".stripslashes($type_item)."</td>\n";
                    $expout .= "     <td class=\"item\"> $poids_item</td>\n";
                    $expout .= "     <td class=\"item\"> $num_item</td>\n";                    
                    $expout .= "   </tr>\n";   
        }
        return $expout;
    }

	 /**
     * Turns competence into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_competence( $competence ) {
    global $CFG;
        // initial string;
        $expout = "";
		
		if ($competence){
                    $code = $competence->code_competence;
                    $description_competence = $competence->description_competence;
                    $ref_domaine = $competence->ref_domaine;
                    $num_competence = $competence->num_competence;
                    $nb_item_competences = $competence->nb_item_competences;
	    	
                       $expout .= "<table class='competence'>\n";
	        $expout .= "<tr>\n";
                    $expout .= "    <th class=\"competence\"><b>".get_string('code_competence','referentiel')."</b></th>\n";   
   	        $expout .= "    <th class=\"competence\"><b>".get_string('description_competence','referentiel')."</b></th>\n";
       	    // $expout .= "    <th class=\"competence\"><b>".get_string('ref_domaine','referentiel')."</b></th>\n";
           	$expout .= "    <th class=\"competence\"><b>".get_string('num_competence','referentiel')."</b></th>\n";
                    $expout .= "    <th class=\"competence\"><b>".get_string('nb_item_competences','referentiel')."</b></th>\n";
                    $expout .= "</tr>\n";
                    
                    $expout .= "  <tr>\n";
                    $expout .= "    <td class=\"competence\"> ".stripslashes($code)."</td>\n";   
                    $expout .= "    <td class=\"competence\"> ".stripslashes($description_competence)."</td>\n";
                    // $expout .= "  <td class=\"competence\"> $ref_domaine</td>\n";
                    $expout .= "    <td class=\"competence\"> $num_competence</td>\n";
                    $expout .= "    <td class=\"competence\"> $nb_item_competences</td>\n";
                    $expout .= "  </tr>\n";
                    $expout .= "</table>\n";

                    // ITEM
                    $compteur_item=0;
                    $records_items = referentiel_get_item_competences($competence->id);
                    
                    if ($records_items){
                    	// DEBUG
                    	// echo "<br/>DEBUG :: ITEMS <br />\n";
                    	// print_r($records_items);
                       	$expout .= "<table class='item'>\n";
	       	 	$expout .= "   <tr>\n";
                    	$expout .= "     <th class=\"item\"><b>".get_string('code','referentiel')."</b></th>\n";   
           		$expout .= "     <th class=\"item\"><b>".get_string('description_item','referentiel')."</b></th>\n";
		        // $expout .= "     <th class=\"item\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
    	   		// $expout .= "     <th class=\"item\"><b>".get_string('ref_competence','referentiel')."</b></th>\n";
	    	    $expout .= "     <th class=\"item\"><b>".get_string('type_item','referentiel')."</b></th>\n";
                           $expout .= "     <th class=\"item\"><b>".get_string('poids_item','referentiel')."</b></th>\n";
		        $expout .= "     <th class=\"item\"><b>".get_string('num_item','referentiel')."</b></th>\n";
                    	$expout .= "   </tr>\n"; 

                    	foreach ($records_items as $record_i){
                    		// DEBUG
                    		// echo "<br/>DEBUG :: ITEM <br />\n";
                    		// print_r($record_i);
                    		$expout .= $this->write_item( $record_i );
                    	}
                    	$expout .= "</table>\n";
                    }
        }
        return $expout;
    }


	 /**
     * Turns domaine into an xml segment
     * @param domaine object
     * @return string xml segment
     */

    function write_domaine( $domaine ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment		
		
		if ($domaine){
                    $code = $domaine->code_domaine;
                    $description_domaine = $domaine->description_domaine;
                    $ref_referentiel = $domaine->ref_referentiel;
                    $num_domaine = $domaine->num_domaine;
                    $nb_competences = $domaine->nb_competences;

                    $expout .= "<br /><table class='domaine'>\n";
                    $expout .= "<tr>\n";                    
                    $expout .= "   <th class=\"domaine\"><b>".get_string('code_domaine','referentiel')."</b></th>\n";   
		    $expout .= "   <th class=\"domaine\"><b>".get_string('description_domaine','referentiel')."</b></th>\n";
        	// $expout .= "   <th class=\"domaine\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
		    $expout .= "   <th class=\"domaine\"><b>".get_string('num_domaine','referentiel')."</b></th>\n";
	        $expout .= "   <th class=\"domaine\"><b>".get_string('nb_competences','referentiel')."</b></th>\n";
                    $expout .= "</tr>\n";	
                    
                    $expout .= "<tr>\n";                    
                    $expout .= "   <td class=\"domaine\"> ".stripslashes($code)."</td>\n";   
                    $expout .= "   <td class=\"domaine\"> ".stripslashes($description_domaine)."</td>\n";
                    // $expout .= "   </td><td class=\"domaine\"> $ref_referentiel</td>\n";
                    $expout .= "   <td class=\"domaine\"> $num_domaine</td>\n";
                    $expout .= "   <td class=\"domaine\"> $nb_competences</td>\n";
                    $expout .= "</tr>\n";
                    $expout .= "</table>\n";
                    
                    // LISTE DES COMPETENCES DE CE DOMAINE
                    $compteur_competence=0;
                    $records_competences = referentiel_get_competences($domaine->id);
                    if ($records_competences){
                    	// DEBUG
                    	// echo "<br/>DEBUG :: COMPETENCES <br />\n";
                    	// print_r($records_competences);
                    	foreach ($records_competences as $record_c){
                    		$expout .= $this->write_competence( $record_c );
                    	}
                    }
        }
        return $expout;
    }



	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel( $referentiel ) {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $referentiel->id;

    	// add comment and div tags
    	$expout .= "<!-- date: ".date("Y/m/d")." referentiel:  $referentiel->id  name: ".stripslashes($referentiel->name)." -->\n";
    	// add header
    	$expout .= "<h3>".stripslashes($referentiel->name)."</h3>\n";
		
		if ($referentiel){
            $name = $referentiel->name;
            $code_referentiel = $referentiel->code_referentiel;
            $description_referentiel = $referentiel->description_referentiel;
            $url_referentiel = $referentiel->url_referentiel;
            $seuil_certificat = $referentiel->seuil_certificat;
            $timemodified = $referentiel->timemodified;
            $nb_domaines = $referentiel->nb_domaines;
            $liste_codes_competence = $referentiel->liste_codes_competence;
                    $liste_empreintes_competence = $referentiel->liste_empreintes_competence;
                    $local = $referentiel->local;
                    $logo_referentiel = $referentiel->logo_referentiel;
                    
	    $expout .= "<table class=\"referentiel\">\n";
                    $expout .= "<tr>\n";
                    $expout .= " <th class=\"referentiel\"><b>".get_string('name','referentiel')."</b></th>\n";
                    $expout .= " <th class=\"referentiel\"><b>".get_string('code_referentiel','referentiel')."</b></th>\n";   
      $expout .= " <th class=\"referentiel\" colspan=\"4\"><b>".get_string('description_referentiel','referentiel')."</b></th>\n";
                    $expout .= " </tr>\n";
                    $expout .= "<tr>\n";
                    $expout .= " <td class=\"referentiel\"> ".stripslashes($name)."</td>\n";
                    $expout .= " <td class=\"referentiel\"> ".stripslashes($code_referentiel)."</td>\n";   
      $expout .= " <td class=\"referentiel\" colspan=\"4\"> ".stripslashes($description_referentiel)."</td>\n";
                    $expout .= " </tr>\n";                    
                    $expout .= "<tr>\n";
                    $expout .= "<tr>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('url_referentiel','referentiel')."</b></th>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('liste_codes_competence','referentiel')."</b></th>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('liste_empreintes_competence','referentiel')."</b></th>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('seuil_certificat','referentiel')."</b></th>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('nb_domaines','referentiel')."</b></th>\n";
                    // $expout .= " <td class\"referentiel\"><b>".get_string('local','referentiel')."</b></td>\n";
      $expout .= " <th class=\"referentiel\"><b>".get_string('logo','referentiel')."</b></th>\n";

      $expout .= "</tr>\n";                    
      $expout .= " <td class=\"referentiel\"> <a href=\"".$url_referentiel."\" title=\"".$url_referentiel."\" target=\"_blank\">".$url_referentiel."</a></td>\n";
      $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_codes_competence,"/",60)."</td>\n";
      $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_empreintes_competence,"/",60)."</td>\n";
      $expout .= " <td class=\"referentiel\"> $seuil_certificat</td>\n";
      $expout .= " <td class=\"referentiel\"> $nb_domaines</td>\n";
      // $expout .= " <td class\"referentiel\"> $local</td>\n";
                    $expout .= " <td class\"referentiel\">&nbsp; $logo_referentiel</td>\n";
                    $expout .= "</tr>\n";
                    $expout .= "</table>\n\n\n";
                    
                    // DOMAINES
                    if (isset($referentiel->id) && ($referentiel->id>0)){
                    	// LISTE DES DOMAINES
                    	$compteur_domaine=0;
                    	$records_domaine = referentiel_get_domaines($referentiel->id);
		    	if ($records_domaine){
                        	// afficher
                    		// DEBUG
                    		// echo "<br/>DEBUG ::<br />\n";
                    		// print_r($records_domaine);
                    		foreach ($records_domaine as $record_d){
                                        // DEBUG
                                        // echo "<br/>DEBUG ::<br />\n";
                                        // print_r($records_domaine);
                                        $expout .= $this->write_domaine( $record_d );
                    		}
                    	}
                    } 
		}
        return $expout;
    }

	 /**
     * Turns referentiel into an xml segment
     * @param competence object
     * @return string xml segment
     */

    function write_referentiel_reduit() {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $this->rreferentiel->id;

		if ($this->rreferentiel){
            $name = trim($this->rreferentiel->name);
            $code_referentiel =  trim($this->rreferentiel->code_referentiel);
			$mail_auteur_referentiel = trim($this->rreferentiel->mail_auteur_referentiel);
			$cle_referentiel = trim($this->rreferentiel->cle_referentiel);
			$pass_referentiel = trim($this->rreferentiel->pass_referentiel);
            $description_referentiel = trim($this->rreferentiel->description_referentiel);
            $url_referentiel =  trim($this->rreferentiel->url_referentiel);
			$seuil_certificat =trim($this->rreferentiel->seuil_certificat);
			$timemodified = trim($this->rreferentiel->timemodified);
			$nb_domaines = trim($this->rreferentiel->nb_domaines);
			$liste_codes_competence = trim($this->rreferentiel->liste_codes_competence);
			$liste_empreintes_competence = trim($this->rreferentiel->liste_empreintes_competence);
			$local =  trim($this->rreferentiel->local);
			$logo_referentiel =  trim($this->rreferentiel->logo_referentiel);

	    	$expout .= "<!-- referentiel :  ".$id."  name: ".$name." -->\n";
            $expout .= "<h3>".get_string('referentiel','referentiel')."</h3>\n";
            $expout .= "<table class=\"referentiel\">\n";
            $expout .= "<tr>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('name','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('code_referentiel','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\" colspan=\"4\"><b>".get_string('description_referentiel','referentiel')."</b></th>\n";
            $expout .= " <th class=\"referentiel\"><b>".get_string('liste_codes_competence','referentiel')."</b></th>\n";

            $expout .= " </tr>\n";
            $expout .= "<tr>\n";
            $expout .= " <td class=\"referentiel\"> ".stripslashes($name)."</td>\n";
            $expout .= " <td class=\"referentiel\"> ".stripslashes($code_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\" colspan=\"4\"> ".stripslashes($description_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_codes_competence,"/",60)."</td>\n";
            $expout .= "</tr>\n";
            $expout .= "</table>\n";
		}
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_tasks() {
    	global $CFG;
        // initial string;
        $expout = "";

    	// add comment and div tags
		$expout .= "<h1>".get_string('tasks','referentiel')."</h1>\n";
 		// 
		if ($this->rreferentiel){
            $expout .= $this->write_referentiel_reduit();
		}

		if ($this->ireferentiel){
            $id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
                    $description_instance = trim($this->ireferentiel->description_instance);
                    $label_domaine = trim($this->ireferentiel->label_domaine);
                    $label_competence = trim($this->ireferentiel->label_competence);
                    $label_item = trim($this->ireferentiel->label_item);
                    $date_instance = $this->ireferentiel->date_instance;
                    $course = $this->ireferentiel->course;
                    $ref_referentiel = $this->ireferentiel->ref_referentiel;
                    $visible = $this->ireferentiel->visible;

	    	$expout .= "<!-- instance :  ".$this->ireferentiel->id."  name: ".$this->ireferentiel->name." -->\n";
    		$expout .= "<h3>".get_string('instance','referentiel')."</h3>\n";
                    $expout .= "<table class=\"referentiel\">\n";
                    $expout .= "<tr>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('id','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('name','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('description_instance','referentiel')."</b></th>\n";   
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('label_domaine','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('label_competence','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('label_item','referentiel')."</b></th>\n";                    
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('date_instance','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('course')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
                    $expout .= " <th  class=\"referentiel\"><b>".get_string('visible','referentiel')."</b></th>\n";
                    $expout .= "</tr>\n";
                    $expout .= "<tr>\n";
                    $expout .= " <td  class=\"referentiel\"> $id</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $name</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $description_instance</td>\n";   
                    $expout .= " <td  class=\"referentiel\"> $label_domaine</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $label_competence</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $label_item</td>\n";                    
                    $expout .= " <td  class=\"referentiel\">".date("Y-m-d H:i:s",$date_instance)."</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $course</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $ref_referentiel</td>\n";
                    $expout .= " <td  class=\"referentiel\"> $visible</td>\n";
                    $expout .= "</tr>\n";
            $expout .= "</table>\n";
                    // taskS
            if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
                $records_tasks = referentiel_get_tasks_instance($this->ireferentiel->id);
                if ($records_tasks){
                    $expout .= "<h3>".get_string('tasks','referentiel')."</h3>\n";
                    foreach ($records_tasks as $record_a){
                        $expout .= $this->write_task( $record_a );
                    }
                }
            }
        }
        return $expout;
    }
}

// ################################################################################################################
// pedagoS : export des pedagos
class pformat_html extends pformat_default {

    function provide_export() {
      return false;
    }

	function provide_import() {
        return false;
    }
}
// //////////////////////////////////////////////////////////////////////////////////////////////////////

// ################################################################################################################
// archive : export des pedagos
// INCLUSION
require_once "archive_format.php";
// //////////////////////////////////////////////////////////////////////////////////////////////////////

?>
