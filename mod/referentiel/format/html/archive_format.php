<?php

class zformat_html extends zformat_default {

var $tab_users=array(array());  //liste des utilisateurs ayant un certificat affiché sur la page

    function provide_import() {
        return false;
    }

    function provide_export() {
        return true;
    }


    // EXPORT FUNCTIONS START HERE

    function export_file_extension() {
    // override default type so extension is .html
        return ".html";
    }


    /**
     * Convert internal Moodle text format code into
     * human readable form
     * @param int id internal code
     * @return string format text
     */
    function get_format( $id ) {
        switch( $id ) {
        case 0:
            $name = "moodle_auto_format";
            break;
        case 1:
            $name = "html";
            break;
        case 2:
            $name = "plain_text";
            break;
        case 3:
            $name = "wiki_like";
            break;
        case 4:
            $name = "markdown";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

    /**
     * Convert internal single question code into
     * human readable form
     * @param int id single question code
     * @return string single question string
     */
    function get_single( $id ) {
        switch( $id ) {
        case 0:
            $name = "false";
            break;
        case 1:
            $name = "true";
            break;
        default:
            $name = "unknown";
        }
        return $name;
    }

	function repchar( $text ) {
	    // escapes 'reserved' characters # = ~ { ) and removes new lines
    	$reserved = array( '#','=','~','{','}',"\n","\r" );
	    $escaped = array( '\#','\=','\~','\{','\}',' ','' );
		return str_replace( $reserved, $escaped, $text );
	}
/*
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
  		$xp .= "<title>Moodle Referentiel HTML Archive</title>\n";
  		$xp .= $css;

  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= "<table><tr valign='top'><td width='108' rowspan='2'>\n";
        //$xp .= '<SCRIPT LANGUAGE="JavaScript">'."\n";
        //$xp .= $this->affiche_tab_users_js()."\n";
        $xp .= $this->affiche_tab_users()."\n";
        //$xp .= '</SCRIPT>'."\n";
        $xp .= "</td><td><h1 align='center'>".get_string('generatedby', 'referentiel')."</h1>\n";
        $xp .= "<p align='center'>".date("Y-m-d H:g:s")."</p></td>\n";
        $xp .= "</tr>\n";
		$xp .= "<tr><td>$content</td></tr>\n";
		$xp .= "</table></body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}
*/
/* OLD ONE
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
  		$xp .= "<title>Moodle Referentiel HTML Archive</title>\n";
  		$xp .= $css;

  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= "<div id='menuDiv'>\n";
        $xp .= $this->affiche_tab_users()."\n";
        
        $xp .= "</div>\n<div id='dataDiv'><h1 align='center'>".get_string('generatedby', 'referentiel')."</h1>\n";
        $xp .= "<p align='center'>".date("Y-m-d H:g:s")."</p>\n";
		$xp .= "$content\n";
		$xp .= "</div</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

*/

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

            $expout .= "   <tr valign=\"top\">\n";
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
	        $expout .= "<tr valign=\"top\">\n";
			$expout .= "    <th class=\"competence\"><b>".get_string('code_competence','referentiel')."</b></th>\n";
   	        $expout .= "    <th class=\"competence\"><b>".get_string('description_competence','referentiel')."</b></th>\n";
       	    // $expout .= "    <th class=\"competence\"><b>".get_string('ref_domaine','referentiel')."</b></th>\n";
           	$expout .= "    <th class=\"competence\"><b>".get_string('num_competence','referentiel')."</b></th>\n";
            $expout .= "    <th class=\"competence\"><b>".get_string('nb_item_competences','referentiel')."</b></th>\n";
			$expout .= "</tr>\n";

			$expout .= "  <tr valign=\"top\">\n";
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
	       	 	$expout .= "   <tr valign=\"top\">\n";
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
			$expout .= "<tr valign=\"top\">\n";
			$expout .= "   <th class=\"domaine\"><b>".get_string('code_domaine','referentiel')."</b></th>\n";
		    $expout .= "   <th class=\"domaine\"><b>".get_string('description_domaine','referentiel')."</b></th>\n";
        	// $expout .= "   <th class=\"domaine\"><b>".get_string('ref_referentiel','referentiel')."</b></th>\n";
		    $expout .= "   <th class=\"domaine\"><b>".get_string('num_domaine','referentiel')."</b></th>\n";
	        $expout .= "   <th class=\"domaine\"><b>".get_string('nb_competences','referentiel')."</b></th>\n";
			$expout .= "</tr>\n";

			$expout .= "<tr valign=\"top\">\n";
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

    function write_referentiel() {
    	global $CFG;
		global $USER;
        // initial string;
        $expout = "";
	    $id = $this->rreferentiel->id;

    	// add header
    	$expout .= "<h3 align=\"center\">".stripslashes($this->rreferentiel->name)."</h3>\n";

		if ($this->rreferentiel){
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$liste_poids_competence = $this->rreferentiel->liste_poids_competence;
			$local = $this->rreferentiel->local;
			$logo_referentiel = $this->rreferentiel->logo_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
            $minima_certificat = $this->rreferentiel->minima_certificat;

	    	$expout .= "<table class=\"referentiel\">\n";
			$expout .= "<tr valign=\"top\">\n";
			$expout .= " <th class=\"referentiel\">".get_string('name','referentiel')."</th>\n";
			$expout .= " <th class=\"referentiel\">".get_string('code_referentiel','referentiel')."</th>\n";
            $expout .= " <th class=\"referentiel\">".get_string('description_referentiel','referentiel')."</th>\n";
            $expout .= " <th class=\"referentiel\">".get_string('url_referentiel','referentiel')."</th>\n";
			$expout .= " </tr>\n";
			$expout .= "<tr valign=\"top\">\n";
			$expout .= " <td class=\"referentiel\">".stripslashes($name)."</td>\n";
			$expout .= " <td class=\"referentiel\">".stripslashes($code_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\">".stripslashes($description_referentiel)."</td>\n";
            $expout .= " <td class=\"referentiel\"><a href=\"".$url_referentiel."\" title=\"".$url_referentiel."\" target=\"_blank\">".$url_referentiel."</a></td>\n";
        	$expout .= " </tr>\n";
			$expout .= "<tr valign=\"top\">\n";
            $expout .= " <th class=\"referentiel\">".get_string('liste_codes_competence','referentiel')."</th>\n";
            $expout .= " <th class=\"referentiel\">".get_string('liste_empreintes_competence','referentiel')."</th>\n";
            $expout .= " <th class=\"referentiel\">".get_string('liste_poids_competence','referentiel')."</th>\n";
            $expout .= " <th class=\"referentiel\">".get_string('seuil_certificat','referentiel')." ";
            $expout .= get_string('minima_certificat','referentiel')."</th>\n";
//            $expout .= " <th class=\"referentiel\">".get_string('nb_domaines','referentiel')."</th>\n";
// $expout .= " <td class\"referentiel\"><b>".get_string('local','referentiel')."</th>\n";
			$expout .= "</tr>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_codes_competence,"/",60)."</td>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_empreintes_competence,"/",60)."</td>\n";
            $expout .= " <td class=\"referentiel\"> ".$this->write_ligne($liste_poids_competence,"/",60)."</td>\n";
            $expout .= " <td class=\"referentiel\"> $seuil_certificat \n";
            $expout .= $minima_certificat. "</td>\n";

//            $expout .= " <td class=\"referentiel\"> $nb_domaines</td>\n";
// $expout .= " <td class\"referentiel\"> $local</td>\n";
//			$expout .= " <td class\"referentiel\"> $logo_referentiel</td>\n";
			$expout .= "</tr>\n";
			$expout .= "</table>\n\n\n";

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

/// ACTIVITES
    function write_document( $userid, $document ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- document: $document->id  -->\n";
		//
		if ($document){
			$id = $document->id ;
            $type_document = trim($document->type_document);
            $description_document = trim($document->description_document);
			$url_document = $document->url_document;
            $ref_activite = $document->ref_activite;

            if (!empty($url_document) && !preg_match("/http/",$url_document)){
                if ($this->export_documents){
                // le fichier est copié dans le dossier temporaire moodledata/archive/referentiel_id/document_files
                    $new_url=referentiel_copy_document_file ($this->rreferentiel->id, $this->user_creator, $userid, $url_document);
                    if (!empty($new_url)){
                        $url_document='./'.$new_url;
                    }
                }
                else{   // recopie de l'adresse vers le serveur Moodle
                // A MODIFIER
                /*
                // Moodle 1.9
                    if ($CFG->slasharguments) {
                        $url_document = "{$CFG->wwwroot}/file.php/".$url_document."?forcedownload=1";
                    }
                    else {
                        $url_document = "{$CFG->wwwroot}/file.php?file=/".$url_document."&forcedownload=1";
                    }
                */
                // Moodle 2.0
                    $url_document = new moodle_url($CFG->wwwroot.'/pluginfile.php'.$url_document);
                    // DEBUG
                    // echo "<br />DEBUG :: format/html/archive_format.php :: 474\n";
                    // echo "<br />URL : $url_document\n";
                }
            }

            $expout .= "  <tr valign=\"top\">\n";
            $expout .= "     <td class=\"referentiel\"> $type_document</td>\n";
            $expout .= "     <td class=\"referentiel\"> $description_document</td>\n";
            $expout .= "     <td class=\"referentiel\"> <a href=\"".$url_document."\" target=\"_blank\">".$url_document."</a></td>\n";
            $expout .= "     <td class=\"referentiel\"> $ref_activite</td>\n";
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
        // add comment
        // $expout .= "\n\n<!-- activite: $activite->id  -->\n";
		//
		if ($activite){
			// DEBUG
			// echo "<br />DEBUG LIGNE 960<br />\n";
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

            $expout .= "<tr valign=\"top\">\n";
            $expout .= "<td class=\"referentiel\"> $id</td>\n";
			$expout .= "<td class=\"referentiel\"> $type_activite</td>\n";
            $expout .= "<td class=\"referentiel\" colspan=\"2\"> $description_activite</td>\n";
            $expout .= "<td class=\"referentiel\" colspan=\"2\"> $competences_activite</td>\n";
            $expout .= "<td class=\"referentiel\" colspan=\"2\"> $commentaire_activite</td>\n";
 //           $expout .= "<td class=\"referentiel\"> $ref_instance</td>\n";
 //           $expout .= "<td class=\"referentiel\"> $ref_referentiel</td>\n";
//            $expout .= "<td class=\"referentiel\"> $ref_course</td>\n";
            $expout .= "<td class=\"referentiel\"> $userid</td>\n";
            $expout .= "<td class=\"referentiel\"> $lastname</td>\n";
            $expout .= "<td class=\"referentiel\"> $firstname</td>\n";
            $expout .= "<td class=\"referentiel\"> $teacherid</td>\n";
            $expout .= "<td class=\"referentiel\"> $teacher_lastname</td>\n";
            $expout .= "<td class=\"referentiel\"> $teacher_firstname</td>\n";
            $expout .= "<td class=\"referentiel\">".date("Y-m-d H:i:s",$date_creation)."</td>\n";
            $expout .= "<td class=\"referentiel\">".date("Y-m-d H:i:s",$date_modif_student)."</td>\n";
            $expout .= "<td class=\"referentiel\">".date("Y-m-d H:i:s",$date_modif)."</td>\n";
            $expout .= "<td class=\"referentiel\"> $approved</td>\n";
			$expout .= "</tr>\n";
			// DOCUMENTS
			$records_documents = referentiel_get_documents($activite->id);

			if ($records_documents){
                $expout .= "<tr valign=\"top\"><td colspan=\"17\">\n";
                $expout .= "<table class=\"referentiel\">\n<tr valign=\"top\">\n";
                $expout .= "<th class=\"referentiel\">".get_string('type_document','referentiel')."</th>\n";
                $expout .= "<th class=\"referentiel\">".get_string('description','referentiel')."</th>\n";
                $expout .= "<th class=\"referentiel\">".get_string('url','referentiel')."</th>\n";
                $expout .= "<th class=\"referentiel\">".get_string('ref_activite','referentiel')."</th>\n";
                $expout .= "</tr>\n";

				foreach ($records_documents as $record_d){
                    $expout .= $this->write_document($activite->userid, $record_d );
				}
				$expout .= "</table>\n</td></tr>\n";
			}

        }
        return $expout;
    }
    /**
     * Turns activite into an xml segment
     * @param activite object
     * @return string xml segment
     */


    function write_instance($instance_record) {
    	global $CFG;
        // initial string;
        $expout = "";
        // add comment
        //$expout .= "\n\n<!-- instance : ".$instance_record->id."  -->\n";
		//
		if ($instance_record){
			// DEBUG
			//echo "<br />DEBUG :: ./formta/xml/archive_format.php :: LIGNE 455<br />\n";
			//print_r($instance_record);
            // exit;
			$id = $instance_record->id ;
            $name = trim($instance_record->name) ;
            $description_instance = trim($instance_record->description_instance);
            $label_domaine = trim($instance_record->label_domaine) ;
            $label_competence = trim($instance_record->label_competence) ;
            $label_item = trim($instance_record->label_item) ;
            $date_instance = $instance_record->date_instance;
            $course = $instance_record->course;
            $ref_referentiel = $instance_record->ref_referentiel;
			$visible = $instance_record->visible ;

// INSTANCE
            $expout .= "<table class=\"domaine\">\n";
		    $expout .= "<tr valign=\"top\">\n";
			$expout .= " <th class=\"domaine\">".get_string('id','referentiel')."</th>\n";
			$expout .= " <th class=\"domaine\">".get_string('name','referentiel')."</th>\n";
			$expout .= " <th class=\"domaine\">".get_string('description_instance','referentiel')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('label_domaine','referentiel')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('label_competence','referentiel')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('label_item','referentiel')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('date_instance','referentiel')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('course')."</th>\n";
            $expout .= " <th class=\"domaine\">".get_string('ref_referentiel','referentiel')."</th>\n";
            $expout .= "</tr><tr valign=\"top\">\n";

			$expout .= " <td class=\"domaine\"> $id</td>\n";
			$expout .= " <td class=\"domaine\"> $name</td>\n";
			$expout .= " <td class=\"domaine\"> $description_instance</td>\n";
            $expout .= " <td class=\"domaine\"> $label_domaine</td>\n";
            $expout .= " <td class=\"domaine\"> $label_competence</td>\n";
            $expout .= " <td class=\"domaine\"> $label_item</td>\n";
            $expout .= " <td class=\"domaine\">".date("Y-m-d H:i:s",$date_instance)."</td>\n";
            $expout .= " <td class=\"domaine\">".referentiel_get_course_link($course, true)."</td>\n";
            $expout .= " <td class=\"domaine\"> $ref_referentiel</td>\n";
			$expout .= "</table>\n";
			$expout .= "<br />\n";
        }
        return $expout;
    }

    function write_liste_activites($records_activites) {
    	global $CFG;
        // initial string;
        $expout = "";
		if (!empty($records_activites)){
			// ACTIVITES
                    $expout .= "<table class=\"referentiel\"><tr valign=\"top\">\n";
                    $expout .= "<th class=\"referentiel\">".get_string('id','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\">".get_string('type_activite','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\" colspan=\"2\">".get_string('description','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\" colspan=\"2\">".get_string('competences_activite','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\" colspan=\"2\">".get_string('commentaire','referentiel')."</th>\n";
     //               $expout .= "<th class=\"referentiel\">".get_string('ref_course','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\" colspan=\"3\">".get_string('auteur','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\" colspan=\"3\">".get_string('referent','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\">".get_string('date_creation','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\">".get_string('date_modif_student','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\">".get_string('date_modif','referentiel')."</th>\n";
                    $expout .= "<th class=\"referentiel\">".get_string('approved','referentiel')."</th>\n";
                    $expout .= "</tr>\n";

					foreach ($records_activites as $record_a){
						$expout .= $this->write_activite( $record_a );
					}
                    $expout .= "</table>\n";
			        $expout .= "<br />\n";
		}

        return $expout;
    }


    /// ETABLISSEMENT
    function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- etablissement: $record->id  -->\n";
		if ($record){
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = trim( $record->nom_etablissement);
			$adresse_etablissement = trim( $record->adresse_etablissement);
			$logo = trim( $record->logo_etablissement);

            if (!$this->format_condense){
        		// $expout .= "<tr valign=\"top\"><th class=\"referentiel\" colspan='9'>".$expout .= get_string('etablissement','referentiel')."</th></tr>\n";
                $expout .= "<tr valign=\"top\"><th class=\"referentiel\">".get_string('id','referentiel')."</th>
<th class=\"referentiel\">".get_string('num_etablissement','referentiel')."</th>
<th class=\"referentiel\" colspan=\"2\">".get_string('nom_etablissement','referentiel')."</th>
<th class=\"referentiel\" colspan=\"4\">".get_string('adresse_etablissement','referentiel')."</th>
<th class=\"referentiel\" colspan=\"2\">".get_string('logo','referentiel')."</th></tr>\n";

                $expout .= "<tr valign=\"top\">\n<td class=\"referentiel\"> $id</td>
            <td class=\"referentiel\"> $num_etablissement</td>
<td class=\"referentiel\" colspan=\"2\"> $nom_etablissement</td>
<td class=\"referentiel\" colspan=\"4\"> $adresse_etablissement</td>
<td class=\"referentiel\" colspan=\"2\"> $logo</td>\n</tr>\n";
            }
        }
        return $expout;
    }


    /// ETUDIANT
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


            $expout .= "\n\n<!-- record etudiant: $id  -->\n";
            $expout .= "<table class=\"competence\">\n";
//                $expout .= "<tr valign=\"top\"><th class=\"competence\" colspan=\"9\"><b>Etudiant</th></tr>\n";
            $expout .= "<tr valign=\"top\"><th class=\"competence\"><b>".get_string('id','referentiel')."</th>
<th class=\"competence\">".get_string('userid','referentiel')."</th>
<th class=\"competence\">".get_string('nom_prenom','referentiel')."</th>
<th class=\"competence\">".get_string('num_etudiant','referentiel')."</th>
<th class=\"competence\">".get_string('ddn_etudiant','referentiel')."</th>
<th class=\"competence\">".get_string('lieu_naissance','referentiel')."</th>
<th class=\"competence\">".get_string('departement_naissance','referentiel')."</th>
<th class=\"competence\">".get_string('adresse_etudiant','referentiel')."</tH>
<th class=\"competence\">".get_string('ref_etablissement','referentiel')."</th>
</th>\n<tr valign=\"top\">\n";
            $expout .= " <td class=\"competence\"> $id</td><td class=\"competence\"> $userid</td><td class=\"competence\"> ".referentiel_get_user_info($record->userid)."</td>
<td class=\"competence\"> $texte</td><td class=\"competence\"> $ddn_etudiant</td><td class=\"competence\"> $lieu_naissance</td><td class=\"competence\"> $departement_naissance</td>
<td class=\"competence\"> $adresse_etudiant</td><td class=\"competence\"> $ref_etablissement</td>\n";

            // Etablissement
            $record_etablissement=$this->t_etablissements[$record->ref_etablissement];
            if ($record_etablissement){
                $expout .= $this->write_etablissement( $record_etablissement );
            }
            $expout .= "</table>\n\n";
        }
        return $expout;
    }

    // PEDAGOGIES
	function write_pedagogie( $userid) {
    // $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
        $expout="";
        if ($userid){
            $rec_pedago=referentiel_get_pedagogie_user($userid, $this->rreferentiel->id);
            if ($rec_pedago){
                $expout .= "\n<table class=\"item\">\n<tr valign=\"top\">\n
<th class=\"item\">".get_string('promotion','referentiel')."</th>
<th class=\"item\">".get_string('formation','referentiel')."</th>
<th class=\"item\">".get_string('pedagogie','referentiel')."</th>
<th class=\"item\">".get_string('composante','referentiel')."</th>
<th class=\"item\">".get_string('num_groupe','referentiel')."</th>
<th class=\"item\">".get_string('commentaire','referentiel')."</th>
<th class=\"item\">".get_string('date_cloture','referentiel')."</th>
</tr>\n";

                $expout .= "<tr valign=\"top\">\n
<td class=\"item\">".$rec_pedago->promotion."</td>
<td class=\"item\">".stripslashes($rec_pedago->formation)."</td>
<td class=\"item\">".stripslashes($rec_pedago->pedagogie)."</td>
<td class=\"item\">".stripslashes($rec_pedago->composante)."</td>
<td class=\"item\">".stripslashes($rec_pedago->num_groupe)."</td>
<td class=\"item\">".stripslashes($rec_pedago->commentaire)."</td>
<td class=\"item\">".stripslashes($rec_pedago->date_cloture)."</td>\n
</tr></table>\n";
            }
        }
        return $expout;
    }
    
    /// CERTIFICATS
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

    		// empreintes
	   	    //$liste_empreintes=referentiel_purge_dernier_separateur(referentiel_get_liste_empreintes_competence($this->rreferentiel), '/');



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
                
                if ($this->export_pedagos){
                    $expout .= $this->write_pedagogie($record->userid);
                }
                
                $expout .= "<table class=\"certificat\">\n";

                $expout .= "<tr valign=\"top\">\n
<th class=\"certificat\">".get_string('commentaire_certificat','referentiel')."</th>
<th class=\"certificat\">".get_string('synthese_certificat','referentiel')."</th>\n";
                if ($this->format_condense!=1){
                    $expout .= "<th class=\"certificat\">".get_string('competences_certificat','referentiel')."</th>\n";
                }
                $expout .= "<th class=\"certificat\">".get_string('decision_jury','referentiel')."</th>
<th class=\"certificat\">".get_string('date_decision','referentiel')."</th>
<th class=\"certificat\">".get_string('verrou','referentiel')."</th>
<th class=\"certificat\">".get_string('evaluation','referentiel')."</th>
</tr>\n";
                $expout .= "<tr valign=\"top\">\n
<td class=\"certificat\"> $commentaire_certificat</td>
<td class=\"certificat\"> $synthese_certificat</td>\n";
                if ($this->format_condense!=1){
                    $expout.="<td class=\"certificat\">".$this->certificat_items_valides($competences_certificat, $this->rreferentiel->id)."</td>\n";
                }
                $expout.="<td class=\"certificat\"> $decision_jury</td>
<td class=\"certificat\"> $date_decision</td>
<td class=\"certificat\"> $verrou</td>
<td class=\"certificat\"> $evaluation</td>\n</tr>\n";

                if ($this->format_condense==1){
                    // couleur de fond pour le certificat
                    if (isset($verrou) && ($verrou!="")) {
                        if ($verrou!=0){
                            $bgcolor='verrouille';
                        }
                        else{
                            $bgcolor='deverrouille';;
                        }
                    }
                    else{
                        $bgcolor='deverrouille';
                    }

                    $expout .= "<tr><th class=\"certificat\" colspan=\"6\">".get_string('competences_certificat','referentiel')."</th></tr>\n";
                    $expout .= "<tr><td class=\"certificat\" colspan=\"6\">\n";
            		$expout .= referentiel_retourne_certificat_consolide('/',':',$competences_certificat, $this->rreferentiel->id, ' class="'.$bgcolor.'"');
            		$expout .= "</td></tr>\n";
                }


                $expout .= "</table>\n";
            }
		}
        return $expout;
    }


	function presave_process( $content, $userid=0, $filename='' ) {
	  // override method to allow us to add xhtml headers and footers

  		global $CFG;
		global $USER;
        $lastname=referentiel_get_user_nom($userid);
        $firstname=referentiel_get_user_prenom($userid);

  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/html/html.css" );
		$css = implode( ' ',$css_lines );
		$xp = "<html>\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$xp .= "<meta author=\"".referentiel_get_user_info($USER->id)."\">\n";
  		$xp .= "<title>".get_string('archive_file', 'referentiel')."</title>\n";
  		$xp .= $css;

  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= "<div id='menuDiv'>\n";
        $xp .= '<a href="#'.$userid.'">'.$lastname.' '.$firstname."</a>\n";
        $xp .= ' <a href="'.$filename.'">'.get_string('retour', 'referentiel')."</a>\n";
        $xp .= "</div>\n<div id='dataDiv'><h1 align='center'>".get_string('generatedby', 'referentiel')."</h1>\n";
        $xp .= "<p align='center'>".date("Y-m-d H:g:s")."</p>\n";
		$xp .= "$content\n";
		$xp .= "</div>\n</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

    /**
     * @param string processed output text
     */
    function get_url_users(){
        /*
        	echo "<br />DEBUG format.php :: LIGNE 2977<br />\n";
			print_object($this->records_users);
            echo "<br />EXIT\n";
        //    exit;
        */
        $expout ='';
        foreach ($this->records_users as $userid){
            if ($filename=$this->get_filename_by_userid($userid)){
                $expout .= '<a href="./'. $filename . $this->export_file_extension().'">'.$filename.$this->export_file_extension().'</a><br />'."\n";
            }
        }
        return $expout;
    }

	function write_index() {
	  // add xhtml headers and footers

  		global $CFG;
		global $USER;
  		// get css bit
		$css_lines = file( "$CFG->dirroot/mod/referentiel/format/html/html.css" );
		$css = implode( ' ',$css_lines );
		$xp = "<html>\n";
  		$xp .= "<head>\n";
  		$xp .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$xp .= "<meta author=\"".referentiel_get_user_info($USER->id)."\">\n";
	    $xp .= "<title>".get_string('archive_file', 'referentiel')."</title>\n";
  		$xp .= $css;

  		$xp .= "</head>\n";
		$xp .= "<body>\n";
		$xp .= "<div id='dataDiv'><h1 align='center'>".get_string('generatedby', 'referentiel')."</h1>\n";
        $xp .= "<p align='center'>".date("Y-m-d H:g:s")."</p>\n";
		$xp .= "<div align='center'>\n";
        $xp .= $this->get_url_users()."\n";
        $xp .= "</div>\n";
        $xp .= "</body>\n";
		$xp .= "</html>\n";

  		return $xp;
	}

    function write_archive_user($userid) {
    	global $CFG;

        // initial string;
        $expout = "";

        // REFERENTIEL
        if (!empty($this->rreferentiel)){

			$expout .= $this->write_referentiel();

            // recuperer les instances associées à ce référentiel  pour l'affichage ultérieur
            $records_instance=referentiel_referentiel_get_instances($this->rreferentiel->id);
            foreach ($records_instance as $record_i){
                $this->t_instances[$record_i->id]=$record_i;
            }
            // recuperer les etablissement associées à ce référentiel  pour l'affichage ultérieur
            $records_etablissement=referentiel_get_etablissements();
            foreach ($records_etablissement as $record_e){
                $this->t_etablissements[$record_e->id]=$record_e;
            }

            if (!empty($userid)){

				//echo "<br />DEBUG .format/html/archive_format.php :: LIGNE 910<br />\n";
				//print_r($this->records_users);
                //echo "<br />\n";
                //exit;

                    //
                    // $expout .= "<users>\n";

                        // $expout .= "<user>\n";
                        if (is_object($userid)){
                            $userid=$userid->userid;
                        }
                        //echo "<br />\n";
                        //echo($userid);
                        // exit;


                        $lastname=referentiel_get_user_nom($userid);
                        $firstname=referentiel_get_user_prenom($userid);

                        $expout .= '<a name="'.$userid.'"></a>'."\n";
                        $expout .= '<p>&nbsp;</p><h3 align="center">'.mb_strtoupper($lastname,'UTF-8').' '.mb_convert_case($firstname, MB_CASE_TITLE, 'UTF-8').'</h3>'."\n";

                        //
                        $record_certificat = referentiel_get_certificat_user($userid, $this->rreferentiel->id);
						if (!empty($record_certificat)){
                            $expout .= $this->write_certificat($record_certificat);
                        }
						else{
                            $expout .= '<p align="center">'.get_string('nocertificat', 'referentiel').'</p>'."\n";
                        }


                        foreach ($this->t_instances as $record_i){
                            //$expout .= "<activites>\n";
                            $recs=referentiel_get_activites_users_from_instance($record_i->id, $userid, '', 'userid ASC, date_creation ASC ');
				            //echo "<br />DEBUG .format/html/archive_format.php :: LIGNE 936<br />\n";
				            //print_r($recs);
                            //echo "<br />\n";
                            //exit;

                            if (!empty($recs)){
                                $expout .= $this->write_instance( $record_i);
                                $expout .= $this->write_liste_activites( $recs );
                            }
                            //$expout .= "</activites>\n";
                        }
                        //$expout .= "</user>\n";

					//$expout .= "</users>\n\n";
            }
		}


        return $expout;
    }


	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_archive() {
    	global $CFG;

        // initial string;
        $expout = "";

        // REFERENTIEL
        if (!empty($this->rreferentiel)){

			$expout .= $this->write_referentiel();

            // recuperer les instances associées à ce référentiel  pour l'affichage ultérieur
            $records_instance=referentiel_referentiel_get_instances($this->rreferentiel->id);
            foreach ($records_instance as $record_i){
                $this->t_instances[$record_i->id]=$record_i;
            }
            // recuperer les etablissement associées à ce référentiel  pour l'affichage ultérieur
            $records_etablissement=referentiel_get_etablissements();
            foreach ($records_etablissement as $record_e){
                $this->t_etablissements[$record_e->id]=$record_e;
            }

            if (!empty($this->records_users)){

				//echo "<br />DEBUG .format/html/archive_format.php :: LIGNE 910<br />\n";
				//print_r($this->records_users);
                //echo "<br />\n";
                //exit;

                    //
                    // $expout .= "<users>\n";
                    $u=0;
					foreach ($this->records_users as $userid){
                        // $expout .= "<user>\n";
                        if (is_object($userid)){
                            $userid=$userid->userid;
                        }
                        //echo "<br />\n";
                        //echo($userid);
                        // exit;
                        
                        $this->tab_users[$u][0]=$userid; // id certificat
                        $lastname=referentiel_get_user_nom($userid);
                        $firstname=referentiel_get_user_prenom($userid);
//                        $this->tab_users[$u][1]=mb_strtoupper($lastname,'UTF-8').' '.mb_convert_case($firstname, MB_CASE_TITLE, 'UTF-8');
                        $this->tab_users[$u][1]=$this->m_special_case($lastname, true).' '.$this->m_special_case($firstname, false);
                        $u++;
                        
                        $expout .= '<a name="'.$userid.'"></a>'."\n";
                        $expout .= '<p>&nbsp;</p><h3 align="center">'.mb_strtoupper($lastname,'UTF-8').' '.mb_convert_case($firstname, MB_CASE_TITLE, 'UTF-8').'</h3>'."\n";

                        //
                        $record_certificat = referentiel_get_certificat_user($userid, $this->rreferentiel->id);
						if (!empty($record_certificat)){
                            $expout .= $this->write_certificat($record_certificat);
                        }
						else{
                            $expout .= '<p align="center">'.get_string('nocertificat', 'referentiel').'</p>'."\n";
                        }
						

                        foreach ($this->t_instances as $record_i){
                            //$expout .= "<activites>\n";
                            $recs=referentiel_get_activites_users_from_instance($record_i->id, $userid, '', 'userid ASC, date_creation ASC ');
				            //echo "<br />DEBUG .format/html/archive_format.php :: LIGNE 936<br />\n";
				            //print_r($recs);
                            //echo "<br />\n";
                            //exit;

                            if (!empty($recs)){
                                $expout .= $this->write_instance( $record_i);
                                $expout .= $this->write_liste_activites( $recs );
                            }
                            //$expout .= "</activites>\n";
                        }
                        //$expout .= "</user>\n";
					}
					//$expout .= "</users>\n\n";
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
                    		$s.='<th class=\"referentiel\">'.$t_competence[$i].'</th>';
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
                    		$s.='<th class=\"referentiel\">'.$t_item_code[$i].'</th>';
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

                    $s.='<table><tr>';
                    for ($i=0; $i<count($t_domaine_coeff); $i++){
                    	if ($t_domaine_coeff[$i]){
                    		$s.='<td>'.$t_domaine[$i].'</td>';
                    	}
                    	else{
                    		$s.='<td>'.$t_domaine[$i].'</td>';
                    	}
                    }
                    $s.="</tr><tr>\n";

                    for ($i=0; $i<count($t_domaine_coeff); $i++){
                    	if ($t_domaine_coeff[$i]){
                    		$s.='<td>'.referentiel_pourcentage($t_certif_domaine_poids[$i], $t_domaine_coeff[$i]).'%</td>';
                    	}
                    	else{
                    		$s.='<td>0%</td>';
                    	}
                    }
                    $s.="</tr><tr>\n";

                    for ($i=0; $i<count($t_competence); $i++){
                    		$s.='<td>'.$t_competence[$i].'</td>';
                    }
                    $s.="</tr><tr>\n";

                    for ($i=0; $i<count($t_competence); $i++){
                    		$s.='<td>'.referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%</td>';
                    }
                    $s.="</tr><tr>\n";

                    // ITEMS

                    for ($i=0; $i<count($t_item_code); $i++){
                    	if ($t_item_empreinte[$i]){
                    		if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i])
                                        $s.='<td>'.$t_item_code[$i].'</td>';
                    		else
                                        $s.='<td>'.$t_item_code[$i].'</td>';
                    	}
                    	else{
                    		$s.='<td>&nbsp;</td>';
                    	}
                    }
                    $s.="</tr><tr>\n";

                    for ($i=0; $i<count($t_item_coeff); $i++){
                    	if ($t_item_empreinte[$i]){
                    		if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
                                        $s.='<td>100%</td>';
                    		}
                    		else{
                                        $s.='<td>'.referentiel_pourcentage($t_certif_item_valeur[$i], $t_item_empreinte[$i]).'%</td>';
                    		}
                    	}
                    	else {
                            $s.='<td>&nbsp;</td>';
                    	}
                    }
                    $s.="</tr></table>\n";

		}
	}
	}

	return $s;
    }


    // -------------------
    function certificat_items_valides($liste_code, $ref_referentiel){
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
                    //$s.='<td class=\"referentiel\">'.$t_item_code[$i].': ';
                    // $s.='<td class=\"referentiel\">';
    				if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.=$t_item_code[$i].' ';
					}
					else{
                        // $s.='0 ';
					}
                    // $s.='</td>';
				}
    		}
            $s.="\n";
		}
	}
	}

	return $s;
    }

    function affiche_tab_users(){
    // affiche une table de selection des utilisateurs
    //print_r($this->tab_users) ;
    //exit;
        $menu='<b>Sélectionnez un utilisateur</b>';

        for ($i=0; $i<count($this->tab_users); $i++){
            $menu.='<br /><A HREF="#'.$this->tab_users[$i][0].'" class=small>'.$this->tab_users[$i][1].'</a>'."\n";
        }
        return $menu;
    }


    function affiche_tab_users_js(){
    // affiche une table de selection des utlisateurs
    //print_r($this->tab_users) ;
    //exit;
/*
SCRIPT EDITE SUR L\'EDITEUR JAVACSRIPT
http://www.editeurjavascript.com
*/
        $menu='// JavaScript Document
bgcolor=\'#ccccaa\';
bgcolor2=\'#ffffcc\';
document.write(\'<style type="text/css">\');
document.write(\'.popper { POSITION: absolute; VISIBILITY: hidden; z-index:15; left:99px \')
document.write(\'#topgauche { position:absolute;  z-index:10; }\')
document.write(\'A:hover.ejsmenu {color:#FFFFFF; text-decoration:none;}\')
document.write(\'.ejsmenu {color:#FFFFFF; text-decoration:none;}\')
document.write(\'</style>\')
document.write(\'<div style="position:relative;height:25"><DIV class=popper id=topdeck></DIV>\');


// LIENS

zlien = new Array;
zlien[0] = new Array;
';
// php
        for ($i=0; $i<count($this->tab_users); $i++){
            $menu.='zlien[0]['.$i.'] = \'<A HREF="#'.$this->tab_users[$i][0].'" CLASS=ejsmenu>'.$this->tab_users[$i][1].'</A>\';'."\n";
        }

        $menu.='
if(document.getElementById)
	{
	skn = document.getElementById("topdeck").style
	// skn.left = 99;
	skn.left = 0;
	}

function pop(msg,pos)
{
skn.visibility = "hidden";
a=true
// skn.top = pos;
skn.top = pos+30;
var content ="<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#000000 WIDTH=100><TR><TD><TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=1>";
pass = 0
while (pass < msg.length)
	{
	content += "<TR><TD BGCOLOR="+bgcolor+" onMouseOver=\"this.style.background=\'"+bgcolor2+"\'\" onMouseOut=\"this.style.background=\'"+bgcolor+"\'\" HEIGHT=20><FONT SIZE=1 FACE=\"Verdana\">&nbsp;&nbsp;"+msg[pass]+"</FONT></TD></TR>";
	pass++;
	}
content += "</TABLE></TD></TR></TABLE>";
document.getElementById("topdeck").innerHTML = content;
skn.visibility = "visible";
}
function kill()
{
	if(document.getElementById)
		skn.visibility = "hidden";
}
document.onclick = kill;
'."\n";

    $menu.='
if(document.getElementById)
	{
	document.write(\'<DIV ID=topgauche><TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=#000000 WIDTH=100 HEIGHT=20><TR><TD><TABLE CELLPADING=0 CELLSPACING=1 BORDER=0 WIDTH=100% HEIGHT=20>\')
document.write(\'<tr><TD WIDTH=100 ALIGN=center BGCOLOR=\'+bgcolor+\' onMouseOver="this.style.background=\\\'\'+bgcolor2+\'\\\';pop(zlien[0],0)" onMouseOut="this.style.background=\\\'\'+bgcolor+\'\\\'" CLASS=ejsmenu><FONT SIZE=1 FACE="Verdana">Sélectionnez un utilisateur</FONT></TD></tr>\')
	document.write(\'</TABLE></TD></TR></TABLE></DIV>\')
	}
document.write(\'</div>\');
    '."\n";
        return $menu;
    }
}


?>
