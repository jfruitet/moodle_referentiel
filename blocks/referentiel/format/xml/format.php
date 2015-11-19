<?php // $Id: format.php,v 1.21.2.16 2008/01/15 14:58:10 thepurpleblob Exp $
//
///////////////////////////////////////////////////////////////
// XML import/export
//
//////////////////////////////////////////////////////////////////////////
// Based on default.php, included by ../import.php
/**
 * @package referetielbank
 * @subpackage importexport
 */
require_once( "$CFG->libdir/xmlize.php" );


class rformat_xml extends rformat_default {

    function provide_import() {
        return true;
    }

    function provide_export() {
        return true;
    }


    // EXPORT FUNCTIONS START HERE

    function export_file_extension() {
    // override default type so extension is .xml
        return ".xml";
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

    /**
     * generates <text></text> tags, processing raw text therein
     * @param int ilev the current indent level
     * @param boolean short stick it on one line
     * @return string formatted text
     */

    function writetext( $raw, $ilev=0, $short=true) {
        $indent = str_repeat( "  ",$ilev );

        // encode the text to 'disguise' HTML content
		$raw=preg_replace("/\r/", "", $raw);
		$raw=preg_replace("/\n/", "|||", $raw);

        $raw = htmlspecialchars( $raw );

        if ($short) {
            $xml = "$indent<text>$raw</text>\n";
        }
        else {
            $xml = "$indent<text>\n$raw\n$indent</text>\n";
        }

        return $xml;
    }

    /**
     * generates raw text therein
     * @return string not formatted text
     */

    function writeraw( $raw) {
		$raw=preg_replace("/\r/", "", $raw);
		$raw=preg_replace("/\n/", " ", $raw);
	    return $raw;
    }

    function xmltidy( $content ) {
        // can only do this if tidy is installed
        if (extension_loaded('tidy')) {
            $config = array( 'input-xml'=>true, 'output-xml'=>true, 'indent'=>true, 'wrap'=>0 );
            $tidy = new tidy;
            $tidy->parseString($content, $config, 'utf8');
            $tidy->cleanRepair();
            return $tidy->value;
        }
        else {
            return $content;
        }
    }


    function presave_process( $content ) {
    // override method to allow us to add xml headers and footers

        // add the xml headers and footers
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
                       "<referentiel>\n" .
                       $content .
                       "</referentiel>\n\n";

        // make the xml look nice
        $content = $this->xmltidy( $content );

        return $content;
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
     * Turns item into an xml segment
     * @param item object
     * @return string xml segment
     */

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\n\n<!-- item: $item->id  -->\n";
		//
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
			$id = $this->writeraw( $item->id );
            $code = $this->writeraw( trim($item->code_item));
            $description_item = $this->writetext(trim($item->description_item));
            $ref_referentiel = $this->writeraw( $item->ref_referentiel);
            $ref_competence = $this->writeraw( $item->ref_competence);
			$type_item = $this->writeraw( trim($item->type_item));
			$poids_item = $this->writeraw( $item->poids_item);
			$empreinte_item = $this->writeraw( $item->empreinte_item);
			$num_item = $this->writeraw( $item->num_item);
            $expout .= "   <item>\n";
			// $expout .= "    <id>$id</id>\n";
			$expout .= "    <code>$code</code>\n";
            $expout .= "    <description_item>\n$description_item</description_item>\n";
            // $expout .= "    <ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            // $expout .= "    <ref_competence>$ref_competence</ref_competence>\n";
            $expout .= "    <type_item>$type_item</type_item>\n";
            $expout .= "    <poids_item>$poids_item</poids_item>\n";
            $expout .= "    <empreinte_item>$empreinte_item</empreinte_item>\n";
            $expout .= "    <num_item>$num_item</num_item>\n";
			$expout .= "   </item>\n\n";
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
        // add comment
        // $expout .= "\n\n<!-- competence: $competence->id  -->\n";
		//
		if ($competence){
			$id = $this->writeraw( $competence->id );
            $code = $this->writeraw( trim($competence->code_competence));
            $description_competence = $this->writetext(trim($competence->description_competence));
            $ref_domaine = $this->writeraw( $competence->ref_domaine);
			$num_competence = $this->writeraw( $competence->num_competence);
			$nb_item_competences = $this->writeraw( $competence->nb_item_competences);

			$type_competence = $this->writeraw( trim($competence->type_competence));
			$seuil_competence = $this->writeraw( trim($competence->seuil_competence));

			$minima_competence = $this->writeraw( trim($competence->minima_competence));

            $expout .= "  <competence>\n";
			// $expout .= "<id>$id</id>\n";
			$expout .= "   <code_competence>$code</code_competence>\n";
            $expout .= "   <description_competence>\n$description_competence</description_competence>\n";

            $expout .= "   <type_competence>$type_competence</type_competence>\n";
            $expout .= "   <seuil_competence>$seuil_competence</seuil_competence>\n";

            $expout .= "   <minima_competence>$minima_competence</minima_competence>\n";

            // $expout .= "   <ref_domaine>$ref_domaine</ref_domaine>\n";
            $expout .= "   <num_competence>$num_competence</num_competence>\n";
            $expout .= "   <nb_item_competences>$nb_item_competences</nb_item_competences>\n\n";

			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);

			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
			}
			$expout .= "  </competence>\n\n";
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
        // $expout .= "\n\n<!-- domaine: $domaine->id  -->\n";
		//
		if ($domaine){
			$id = $this->writeraw( $domaine->id );
            $code = $this->writeraw( trim($domaine->code_domaine) );
            $description_domaine = $this->writetext(trim($domaine->description_domaine));
            $ref_referentiel = $this->writeraw( $domaine->ref_referentiel );
			$num_domaine = $this->writeraw( $domaine->num_domaine );
			$nb_competences = $this->writeraw( $domaine->nb_competences );

			$type_domaine = $this->writeraw( trim($domaine->type_domaine));
			$seuil_domaine = $this->writeraw( trim($domaine->seuil_domaine));

			$minima_domaine = $this->writeraw( trim($domaine->minima_domaine));

            $expout .= " <domaine>\n";
			// $expout .= "  <id>$id</id>\n";
			$expout .= "  <code_domaine>$code</code_domaine>\n";
            $expout .= "  <description_domaine>\n$description_domaine</description_domaine>\n";
            $expout .= "  <type_domaine>$type_domaine</type_domaine>\n";
            $expout .= "  <seuil_domaine>$seuil_domaine</seuil_domaine>\n";
            $expout .= "  <minima_domaine>$minima_domaine</minima_domaine>\n";
            // $expout .= " <ref_referentiel>$ref_referentiel</ref_referentiel>\n";
            $expout .= "  <num_domaine>$num_domaine</num_domaine>\n";
            $expout .= "  <nb_competences>$nb_competences</nb_competences>\n\n";

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
			$expout .= " </domaine>\n\n";
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
			$id = $this->writeraw( $protocole->id );
            $ref_occurrence = $this->writeraw($protocole->ref_occurrence);
			$seuil_referentiel = $this->writeraw( $protocole->seuil_referentiel );
			$minima_referentiel = $this->writeraw( $protocole->minima_referentiel );
            $l_domaines_oblig = $this->writetext(trim($protocole->l_domaines_oblig));
            $l_seuils_domaines = $this->writetext(trim($protocole->l_seuils_domaines));
            $l_minimas_domaines = $this->writetext(trim($protocole->l_minimas_domaines));
            $l_domaines_oblig = $this->writetext(trim($protocole->l_domaines_oblig));
            $l_competences_oblig = $this->writetext(trim($protocole->l_competences_oblig));
            $l_seuils_competences = $this->writetext(trim($protocole->l_seuils_competences));
            $l_minimas_competences = $this->writetext(trim($protocole->l_minimas_competences));
            $l_items_oblig = $this->writetext(trim($protocole->l_items_oblig));
            $timemodified = $this->writeraw($protocole->timemodified);
			$actif = $this->writeraw( $protocole->actif );
            $commentaire = $this->writetext(trim($protocole->commentaire));

            $expout .= " <protocole>\n";
			// $expout .= "  <p_id>$id</p_id>\n";
			// $expout .= "  <p_ref_occurrence>$ref_occurrence</p_ref_occurrence>\n";
			$expout .= "  <p_seuil_referentiel>$seuil_referentiel</p_seuil_referentiel>\n";
			$expout .= "  <p_minima_referentiel>$minima_referentiel</p_minima_referentiel>\n";
			$expout .= "  <p_domaines_oblig>\n$l_domaines_oblig</p_domaines_oblig>\n";
			$expout .= "  <p_seuils_domaines>\n$l_seuils_domaines</p_seuils_domaines>\n";
			$expout .= "  <p_minimas_domaines>\n$l_minimas_domaines</p_minimas_domaines>\n";
			$expout .= "  <p_competences_oblig>\n$l_competences_oblig</p_competences_oblig>\n";
			$expout .= "  <p_seuils_competences>\n$l_seuils_competences</p_seuils_competences>\n";
			$expout .= "  <p_minimas_competences>\n$l_minimas_competences</p_minimas_competences>\n";
			$expout .= "  <p_items_oblig>\n$l_items_oblig</p_items_oblig>\n";
			$expout .= "  <p_timemodified>$timemodified</p_timemodified>\n";
			$expout .= "  <p_actif>$actif</p_actif>\n";
            $expout .= "  <p_commentaire>\n$commentaire</p_commentaire>\n\n";
			$expout .= " </protocole>\n\n";
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
        // initial string;
        $expout = "";
        // add comment
		//         $referentiel
		if ($this->referentiel){
			$id = $this->writeraw( $this->referentiel->id );
            $name = $this->writeraw( trim($this->referentiel->name) );
            $code_referentiel = $this->writeraw( trim($this->referentiel->code_referentiel));
            $description_referentiel = $this->writetext(trim($this->referentiel->description_referentiel));
            $url_referentiel = $this->writeraw( trim($this->referentiel->url_referentiel) );
			$seuil_certificat = $this->writeraw( $this->referentiel->seuil_certificat );
			$minima_certificat = $this->writeraw( $this->referentiel->minima_certificat );

			$timemodified = $this->writeraw( $this->referentiel->timemodified );
			$nb_domaines = $this->writeraw( $this->referentiel->nb_domaines );
			$liste_codes_competence = $this->writeraw( trim($this->referentiel->liste_codes_competence) );
			$liste_empreintes_competence = $this->writeraw( trim($this->referentiel->liste_empreintes_competence) );
			$local = $this->writeraw( $this->referentiel->local );
			$logo_referentiel = $this->writeraw( $this->referentiel->logo_referentiel );

			// $expout .= "<id>$id</id>\n";
			$expout .= " <name>$name</name>\n";
			$expout .= " <code_referentiel>$code_referentiel</code_referentiel>\n";
            $expout .= " <description_referentiel>\n$description_referentiel</description_referentiel>\n";
            $expout .= " <url_referentiel>$url_referentiel</url_referentiel>\n";

            $expout .= " <seuil_certificat>$seuil_certificat</seuil_certificat>\n";

            $expout .= " <minima_certificat>$minima_certificat</minima_certificat>\n";
            $expout .= " <timemodified>$timemodified</timemodified>\n";
            $expout .= " <nb_domaines>$nb_domaines</nb_domaines>\n";
            $expout .= " <liste_codes_competence>$liste_codes_competence</liste_codes_competence>\n";
            $expout .= " <liste_empreintes_competence>$liste_empreintes_competence</liste_empreintes_competence>\n";
			// $expout .= " <local>$local</local>\n";
			// PAS DE LOGO ICI
			// $expout .= " <logo_referentiel>$logo_referentiel</logo_referentiel>\n";

			// MODIF JF 2012/03/09
			// PROTOCOLE
            if (!empty($this->referentiel->id)){
                if ($record_protocol=referentiel_get_protocol($this->referentiel->id)){
                    $expout .= $this->write_protocol( $record_protocol );
                }
            }

			// DOMAINES
			if (isset($this->referentiel->id) && ($this->referentiel->id>0)){
				// LISTE DES DOMAINES
				$compteur_domaine=0;
				$records_domaine = referentiel_get_domaines($this->referentiel->id);
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



    // IMPORT FUNCTIONS START HERE

    /**
     * Translate human readable format name
     * into internal Moodle code number
     * @param string name format name from xml file
     * @return int Moodle format code
     */
    function trans_format( $name ) {
        $name = trim($name);

        if ($name=='moodle_auto_format') {
            $id = 0;
        }
        elseif ($name=='html') {
            $id = 1;
        }
        elseif ($name=='plain_text') {
            $id = 2;
        }
        elseif ($name=='wiki_like') {
            $id = 3;
        }
        elseif ($name=='markdown') {
            $id = 4;
        }
        else {
            $id = 0; // or maybe warning required
        }
        return $id;
    }

    /**
     * Translate human readable single answer option
     * to internal code number
     * @param string name true/false
     * @return int internal code number
     */
    function trans_single( $name ) {
        $name = trim($name);
        if ($name == "false" || !$name) {
            return 0;
        }
        else {
            return 1;
        }
    }

    /**
     * process text string from xml file
     * @param array $text bit of xml tree after ['text']
     * @return string processed text
     */
    function import_text( $text ) {
        // quick sanity check
        if (empty($text)) {
            return '';
        }
        $data = $text[0]['#'];
        return addslashes(trim( $data ));
    }

    /**
     * return the value of a node, given a path to the node
     * if it doesn't exist return the default value
     * @param array xml data to read
     * @param array path path to node expressed as array
     * @param mixed default
     * @param bool istext process as text
     * @param string error if set value must exist, return false and issue message if not
     * @return mixed value
     */
    function getpath( $xml, $path, $default, $istext=false, $error='' ) {
        foreach ($path as $index) {
			// echo " $index ";
            if (!isset($xml[$index])) {
                if (!empty($error)) {
                    $this->error( $error );
                    return false;
                }
                else {
					// echo " erreur ";
                    return $default;
                }
            }
            else {
				$xml = $xml[$index];
				// echo " $xml ";
			}
        }
        if ($istext) {
            $xml = addslashes( trim( $xml ) );
        }

        return $xml;
    }


    /**
     * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_referentiel( $xmlreferentiel ) {
	// recupere le fichier xml
	// selon les parametres soit cree une nouvelle occurence
	// soit modifie une occurrence courante de referentiel
	global $SESSION;
	global $USER;
	global $CFG;
	$nbdomaines=0;        // compteur
	$nbcompetences=0;        // compteur
    $nbitems=0;              // compteur
 	if (!$xmlreferentiel){
        $this->error( get_string( 'incompletedata', 'referentiel' ) );
  		return false;
	}

		// print_r($xmlreferentiel);
		if (!isset($this->action) || (isset($this->action) && ($this->action!="selectreferentiel") && ($this->action!="importreferentiel"))){
			if (!(isset($this->course->id) && ($this->course->id>0))
				||
				!(isset($this->referentiel->id) && ($this->referentiel->id>0))
				||
				!(isset($this->coursemodule->id) && ($this->coursemodule->id>0))
				){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="selectreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}
		else if (isset($this->action) && ($this->action=="importreferentiel")){
			if (!(isset($this->course->id) && ($this->course->id>0))){
				$this->error( get_string( 'incompletedata', 'referentiel' ) );
				return false;
			}
		}

		$risque_ecrasement=false;

        // get some error strings
        $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
        $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
		$error_override = get_string( 'overriderisk', 'referentiel' );

        // this routine initialises the import object
        $re = $this->defaultreferentiel();
        //
		// $re->id = $this->getpath( $xmlreferentiel, array('#','id',0,'#'), '', false, '');
        $re->name = $this->getpath( $xmlreferentiel, array('#','name','0','#'), '', true, $error_noname);
        $re->code_referentiel = $this->getpath( $xmlreferentiel, array('#','code_referentiel',0,'#'), '', true, $error_nocode);
        $re->description_referentiel = $this->getpath( $xmlreferentiel, array('#','description_referentiel',0,'#','text',0,'#'), '', true, '');
        $re->url_referentiel = $this->getpath( $xmlreferentiel, array('#','url_referentiel',0,'#'), '', true, '');
		$re->seuil_certificat = $this->getpath( $xmlreferentiel, array('#','seuil_certificat',0,'#'), '', false, '');
		$re->minima_certificat = $this->getpath( $xmlreferentiel, array('#','minima_certificat',0,'#'), '', false, '');
		$re->timemodified = $this->getpath( $xmlreferentiel, array('#','timemodified',0,'#'), '', false, '');
		$re->nb_domaines = $this->getpath( $xmlreferentiel, array('#','nb_domaines',0,'#'), '', false, '');
		$re->liste_codes_competence = $this->getpath( $xmlreferentiel, array('#','liste_codes_competence',0,'#'), '', true, '');
		$re->liste_empreintes_competence = $this->getpath( $xmlreferentiel, array('#','liste_empreintes_competence',0,'#'), '', true, '');
		$re->logo_referentiel = $this->getpath( $xmlreferentiel, array('#','logo_referentiel',0,'#'), '', true, '');
		// $re->local = $this->getpath( $xmlreferentiel, array('#','course',0,'#'), '', false, '');

		/*
		// traitement d'une image associee
		// non implante
        $image = $this->getpath( $xmlreferentiel, array('#','image',0,'#'), $re->image );
        $image_base64 = $this->getpath( $xmlreferentiel, array('#','image_base64','0','#'),'' );
        if (!empty($image_base64)) {
            $re->image = $this->importimagefile( $image, stripslashes($image_base64) );
        }
		*/

		$re->export_process = false;
		$re->import_process = true;

		// le referentiel est toujours place dans le cours local d'appel
		$re->course = $this->course->id;

		$risque_ecrasement=false;
		if (!isset($this->action) || ($this->action!="importreferentiel")){
			// importer dans le cours courant en remplacement du referentiel courant
			// Verifier si ecrasement referentiel local

			if (isset($re->name) && ($re->name!="")
				&&
				isset($re->code_referentiel) && ($re->code_referentiel!="")
				&&
				isset($re->id) && ($re->id>0)
				&&
				isset($re->course) && ($re->course>0)){
				// sauvegarder ?
				if ($this->course->id==$re->course){
					if (
						(isset($this->referentiel->id) && ($this->referentiel->id==$re->id))
						||
						(
							(isset($this->referentiel->name) && ($this->referentiel->name==$re->name))
							&&
							(isset($this->referentiel->code_referentiel) && ($this->referentiel->code_referentiel==$re->code_referentiel))
						)
					)
					{
						$risque_ecrasement=true;
					}
				}
			}
		}

		if (($risque_ecrasement==false) || ($this->newinstance==1)) {
			// Enregistrer dans la base comme un nouveau referentiel_referentiel du cours courant
			$new_referentiel_id=referentiel_add_referentiel($re);
			$this->setReferentielId($new_referentiel_id);
			// DEBUG
			// echo "<br />DEBUG xml/format.php ligne 572<br />NEW REFERENTIEL ID ENREGISTRE : ".$this->new_referentiel_id."\n";
		}
		else if (($risque_ecrasement==true) && ($this->override==1)) {
			// Enregistrer dans la base en remplaçant la version courante (update)
			// NE FAUDRAIT IL PAS SUPPRIMER LE REFERENTIEL AVANT DE LA RECHARGER ?
			$re->instance=$this->referentiel->id;    // en realite instance est ici occurrence
			$re->referentiel_id=$this->referentiel->id;
			$ok=referentiel_update_referentiel($re);
			$new_referentiel_id=$this->referentiel->id;
		}
		else {
			// ni nouvelle instance ni recouvrement
			$this->error( $error_override );
			return false;
		}

    // MODIF JF 2012/03/09
	// importer le protocole
	$pindex=0;
	$nbprotocoles=0;  // compteur
    $re->protocole = array();

	if (!empty($xmlreferentiel['#']['protocole'])) {
        $xmlprotocole = $xmlreferentiel['#']['protocole'];
        foreach ($xmlprotocole as $protocole) {
			// PROTOCOLE
			// print_r($protocole);
			$pindex++;
			$new_protocole = array();
			$new_protocole = $this->defaultprotocole();
			// $new_protocole->id=$this->getpath( $protocole, array('#','p_id',0,'#'), '', false, '');
			// $new_protocole->ref_occurrence=$this->getpath( $protocole, array('#','p_ref_occurrence',0,'#'), '', false, '');
            $new_protocole->seuil_referentiel=$this->getpath( $protocole, array('#','p_seuil_referentiel',0,'#'), '', false, '');
            $new_protocole->minima_referentiel=$this->getpath( $protocole, array('#','p_minima_referentiel',0,'#'), '', false, '');
            // La suite initialise en chargeant les domaines / compétences / items
            // $new_protocole->l_domaines_oblig=$this->getpath( $protocole, array('#','p_domaines_oblig',0,'#','text',0,'#'), '', true, '');
			// $new_protocole->l_seuils_domaines=$this->getpath( $protocole, array('#','p_seuils_domaines',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_competences_oblig=$this->getpath( $protocole, array('#','p_competences_oblig',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_seuils_competences=$this->getpath( $protocole, array('#','p_seuils_competences',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_minimas_competences=$this->getpath( $protocole, array('#','p_minimas_competences',0,'#','text',0,'#'), '', true, '');
            // $new_protocole->l_items_oblig=$this->getpath( $protocole, array('#','p_items_oblig',0,'#','text',0,'#'), '', true, '');
            $new_protocole->timemodified=$this->getpath( $protocole, array('#','p_timemodified',0,'#'), '', false, '');
            $new_protocole->actif=$this->getpath( $protocole, array('#','p_actif',0,'#'), '', false, '');
            $new_protocole->commentaire=$this->getpath( $protocole, array('#','p_commentaire',0,'#','text',0,'#'), '', true, '');
			// enregistrer
			$re->protocoles[$pindex]=$new_protocole;

			// sauvegarder dans la base
			// remplacer l'id du referentiel importe par l'id du referentiel cree
			// trafiquer les donnees pour appeler la fonction ad hoc
			$new_protocole->ref_occurrence=$new_referentiel_id;
			// DEBUG
			// echo "<br />DEBUG ./format/xml/format.php :: 710<br />\n";
			// print_object($new_protocole);

			if (referentiel_add_protocol($new_protocole)){
                $nbprotocoles++;
            }
        }
   }
   else{
        $new_protocole = $this->defaultprotocole();
        $new_protocole->ref_occurrence=$new_referentiel_id;
        $re->protocoles[1]=$new_protocole;
        if (referentiel_add_protocol($new_protocole)){
            $nbprotocoles++;
        }
   }
		// importer les domaines
		$xmldomaines = $xmlreferentiel['#']['domaine'];
		$dindex=0;
        $re->domaines = array();

		$nbdomaines=0;        // compteur
        foreach ($xmldomaines as $domaine) {
			// DOMAINES
			// print_r($domaine);
			$dindex++;
			$new_domaine = $this->defaultdomaine();
			// $new_domaine->id=$this->getpath( $domaine, array('#','id',0,'#'), '', false, '');
			$new_domaine->code_domaine=$this->getpath( $domaine, array('#','code_domaine',0,'#'), '', true, $error_nocode);
			$new_domaine->description_domaine=$this->getpath( $domaine, array('#','description_domaine',0,'#','text',0,'#'), '', true, '');

// retablir des sauts de ligne
            $new_domaine->description_domaine=preg_replace("/\|\|\|/", "\r\n" , $new_domaine->description_domaine);

			$new_domaine->num_domaine=$this->getpath( $domaine, array('#','num_domaine',0,'#'), '', false, '');
			$new_domaine->nb_competences=$this->getpath( $domaine, array('#','nb_competences',0,'#'), '', false, '');
			// $new_domaine->ref_referentiel=$this->getpath( $domaine, array('#','ref_referentiel',0,'#'), '', false, '');

            $new_domaine->type_domaine=$this->getpath( $domaine, array('#','type_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->type_domaine)){
                $new_domaine->type_domaine=0;
            }
            $new_domaine->seuil_domaine=$this->getpath( $domaine, array('#','seuil_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->seuil_domaine)){
                $new_domaine->seuil_domaine='0.0';
            }

            $new_domaine->minima_domaine=$this->getpath( $domaine, array('#','minima_domaine',0,'#'), '', false, '');
            if (empty($new_domaine->minima_domaine)){
                $new_domaine->minima_domaine='0';
            }

			// enregistrer
			$re->domaines[$dindex]=$new_domaine;

			// sauvegarder dans la base
			// remplacer l'id du referentiel importe par l'id du referentiel cree
			// trafiquer les donnees pour appeler la fonction ad hoc
			$new_domaine->ref_referentiel=$new_referentiel_id;
			$new_domaine->instance=$new_referentiel_id; // pour que ca marche
			$new_domaine->new_code_domaine=$new_domaine->code_domaine;
			$new_domaine->new_description_domaine=$new_domaine->description_domaine;
			$new_domaine->new_num_domaine=$new_domaine->num_domaine;
			$new_domaine->new_nb_competences=$new_domaine->num_domaine;

            $new_domaine->new_type_domaine=$new_domaine->type_domaine;
            $new_domaine->new_seuil_domaine=$new_domaine->seuil_domaine;

            $new_domaine->new_minima_domaine=$new_domaine->minima_domaine;

			$new_domaine_id=referentiel_add_domaine($new_domaine);
			if ($new_domaine_id){
                $nbdomaines++;
            }

			// importer les competences
			$xmlcompetences = $domaine['#']['competence'];

			$cindex=0;
			$re->domaines[$dindex]->competences=array();

			$nbcompetences=0;        // compteur
            foreach ($xmlcompetences as $competence) {
				$cindex++;
				$new_competence = array();
				$new_competence = $this->defaultcompetence();
		    	// $new_competence->id = $this->getpath( $competence, array('#','id',0,'#'), '', false, '');
				$new_competence->code_competence=$this->getpath( $competence, array('#','code_competence',0,'#'), '', true, $error_nocode);
				$new_competence->description_competence=$this->getpath( $competence, array('#','description_competence',0,'#','text',0,'#'), '', true, '');

// retablir des sauts de ligne
                $new_competence->description_competence=preg_replace("/\|\|\|/", "\r\n" , $new_competence->description_competence);

				$new_competence->num_competence=$this->getpath( $competence, array('#','num_competence',0,'#'), '', false, '');
				$new_competence->nb_item_competences=$this->getpath( $competence, array('#','nb_item_competences',0,'#'), '', false, '');
				// $new_competence->ref_domaine=$this->getpath( $competence, array('#','ref_domaine',0,'#'), '', false, '');

                $new_competence->type_competence=$this->getpath( $competence, array('#','type_competence',0,'#'), '', false, '');
                $new_competence->seuil_competence=$this->getpath( $competence, array('#','seuil_competence',0,'#'), '', false, '');
                if (empty($new_competence->type_competence)){
                    $new_competence->type_competence=0;
                }
                if (empty($new_competence->seuil_competence)){
                    $new_competence->seuil_competence='0.0';
                }

                $new_competence->minima_competence=$this->getpath( $competence, array('#','minima_competence',0,'#'), '', false, '');
                if (empty($new_competence->minima_competence)){
                    $new_competence->minima_competence=0;
                }

				// enregistrer
				$re->domaines[$dindex]->competences[$cindex]=$new_competence;

				// sauvegarder dans la base
				// remplacer l'id du referentiel importe par l'id du referentiel cree
				$new_competence->ref_domaine=$new_domaine_id;
				// trafiquer les donnees pour appeler la fonction ad hoc
				$new_competence->instance=$new_referentiel_id; // pour que ca marche
				$new_competence->new_code_competence=$new_competence->code_competence;
				$new_competence->new_description_competence=$new_competence->description_competence;
				$new_competence->new_ref_domaine=$new_competence->ref_domaine;
				$new_competence->new_num_competence=$new_competence->num_competence;
				$new_competence->new_nb_item_competences=$new_competence->nb_item_competences;

                $new_competence->new_type_competence=$new_competence->type_competence;
                $new_competence->new_seuil_competence=$new_competence->seuil_competence;


                $new_competence->new_minima_competence=$new_competence->minima_competence;

				// creation
				$new_competence_id=referentiel_add_competence($new_competence);
				if ($new_competence_id){
                    $nbcompetences++;        // compteur
                }

				// importer les items
				$xmlitems = $competence['#']['item'];
				$iindex=0;
				$re->domaines[$dindex]->competences[$cindex]->items=array();

                $nbitems=0; // compteur
		        foreach ($xmlitems as $item) {
					$iindex++;
					$new_item = array();
					$new_item = $this->defaultitem();
					// $new_item->id = $this->getpath( $item, array('#','id',0,'#'), '', false, '');
					$new_item->code_item = $this->getpath( $item, array('#','code',0,'#'), '', true, $error_nocode);
					$new_item->description_item=$this->getpath( $item, array('#','description_item',0,'#','text',0,'#'), '', true, '');

// retablir des sauts de ligne
                    $new_item->description_item=preg_replace("/\|\|\|/", "\r\n" , $new_item->description_item);

					$new_item->num_item=$this->getpath( $item, array('#','num_item',0,'#'), '', false, '');
					$new_item->type_item=$this->getpath( $item, array('#','type_item',0,'#'), '', true, '');
					$new_item->poids_item=$this->getpath( $item, array('#','poids_item',0,'#'), '', false, '');
					// $new_item->ref_competence=$this->getpath( $item, array('#','ref_competence',0,'#'), '', false, '');
					// $new_item->ref_referentiel=$this->getpath( $item, array('#','ref_referentiel',0,'#'), '', false, '');
					$new_item->empreinte_item=$this->getpath( $item, array('#','empreinte_item',0,'#'), '', false, '');
					// enregistrer
					$re->domaines[$dindex]->competences[$cindex]->items[$iindex]=$new_item;

					// sauvegarder dans la base

					// remplacer l'id du referentiel importe par l'id du referentiel cree
					$new_item->ref_referentiel=$new_referentiel_id;
					$new_item->ref_competence=$new_competence_id;
					// trafiquer les donnees pour pouvoir appeler la fonction ad hoc
					$new_item->instance=$new_item->ref_referentiel;
					$new_item->new_ref_competence=$new_item->ref_competence;
					$new_item->new_code_item=$new_item->code_item;
					$new_item->new_description_item=$new_item->description_item;
					$new_item->new_num_item=$new_item->num_item;
					$new_item->new_type_item=$new_item->type_item;
					$new_item->new_poids_item=$new_item->poids_item;
					$new_item->new_empreinte_item=$new_item->empreinte_item;
					// creer
					$new_item_id=referentiel_add_item($new_item);
					if ($new_item_id){
                        $nbitems++;
                    }
                    // that's all folks
				} // items
    			if ($nbitems>0){
                    // mettre a jour
                    referentiel_set_competence_nb_item($new_competence_id, $nbitems);
                }
			} // competences
			if ($nbcompetences>0){
                // mettre a jour
                referentiel_set_domaine_nb_competence($new_domaine_id, $nbcompetences);
            }
        }
        // mettre a jour
        if ($nbdomaines>0){
            // mettre a jour
            referentiel_set_referentiel_nb_domaine($new_referentiel_id, $nbdomaines);
        }
        return $re;
    }



    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function read_import_referentiel($lines) {
        // we just need it as one big string
        $text = implode($lines, " ");
        unset( $lines );

        // this converts xml to big nasty data structure
        // the 0 means keep white space as it is (important for markdown format)
        // print_r it if you want to see what it looks like!
        $xml = xmlize( $text, 0 );

		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 580<br />\n";
		// print_r($xml);
		// echo "<br /><br />\n";
		// print_r($xml['referentiel']['domaine']['competence']);
		// print_r($xml['referentiel']['#']['domaine']['#']);
		// echo "<br /><br />\n";
		// exit;
		$re=$this->import_referentiel($xml['referentiel']);
        // stick the result in the $treferentiel array
 		// DEBUG
		// echo "<br />DEBUG xml/format.php :: ligne 632\n";
		// print_r($re);
        return $re;
    }
}



?>
