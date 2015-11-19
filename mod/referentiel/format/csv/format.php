<?php 
// Based on default.php, included by ../import.php

class rformat_csv extends rformat_default {

	var $sep = ";";

	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1



	// ----------------
	function guillemets($texte){
		return '"'.trim($texte).'"';
	}

	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return $this->guillemets(str_replace($cherche, $remplace, $texte));
	}

	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	 /**
     * @param
     * @return string recode latin1
	 *
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}

		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}

	 /**
     * @param
     * @return string recode utf8
	 *
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}

		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}

    function provide_export() {
      return true;
    }

    function provide_import() {
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
		$xp =  "#Moodle Referentiel CSV Export;latin1;".referentiel_get_user_info($USER->id)."\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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
		//
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
            $code = $item->code_item;
            $description_item = $this->purge_sep($item->description_item);
            $ref_referentiel = $item->ref_referentiel;
            $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$num_item = $item->num_item;
			$empreinte_item = $item->empreinte_item;
            $expout .= stripslashes($this->output_codage_caractere($code)).";".stripslashes($this->output_codage_caractere($description_item)).";".$this->output_codage_caractere($type_item).";$poids_item;$num_item;$empreinte_item\n";
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
		if ($competence){
            $code_competence = $competence->code_competence;
            $description_competence = $this->purge_sep($competence->description_competence);
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;

// MODIF 2012/03/08
			$type_competence = trim($competence->type_competence);
			$seuil_competence = trim($competence->seuil_competence);
// MODIF 2012/03/26
            $minima_competence = $competence->minima_competence;
			$expout .= stripslashes($this->output_codage_caractere($code_competence)).";".stripslashes($this->output_codage_caractere($description_competence)).";$num_competence;$nb_item_competences;$type_competence;$seuil_competence;$minima_competence\n";

			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);

			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				$expout .= "#code_item;description_item;type_item;poids_item;num_item;empreinte_item\n";
				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
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
            $code_domaine = $domaine->code_domaine;
            $description_domaine = $this->purge_sep($domaine->description_domaine);
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;
// MODIF 2012/03/08
			$type_domaine = $domaine->type_domaine;
			$seuil_domaine = $domaine->seuil_domaine;
// MODIF 2012/03/26
            $minima_domaine = $domaine->minima_domaine;

			$expout .= stripslashes($this->output_codage_caractere($code_domaine)).";".stripslashes($this->output_codage_caractere($description_domaine)).";$num_domaine;$nb_competences;$type_domaine;$seuil_domaine;$minima_domaine\n";

			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				// DEBUG
				// echo "<br/>DEBUG :: COMPETENCES <br />\n";
				// print_r($records_competences);
				foreach ($records_competences as $record_c){
					$expout .= "#code_competence;description_competence;num_competence;nb_item_competences;type_competence;seuil_competence;minima_competence\n";
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
			$id =  $protocole->id;
            $ref_occurrence = $protocole->ref_occurrence;
			$seuil_referentiel =  $protocole->seuil_referentiel;
			$minima_referentiel =  $protocole->minima_referentiel;
            $l_domaines_oblig = $this->purge_sep(trim($protocole->l_domaines_oblig));
            $l_seuils_domaines = $this->purge_sep(trim($protocole->l_seuils_domaines));
            $l_minimas_domaines = $this->purge_sep(trim($protocole->l_minimas_domaines));

            $l_competences_oblig = $this->purge_sep(trim($protocole->l_competences_oblig));
            $l_seuils_competences = $this->purge_sep(trim($protocole->l_seuils_competences));
            $l_minimas_competences = $this->purge_sep(trim($protocole->l_minimas_competences));

            $l_items_oblig = $this->purge_sep(trim($protocole->l_items_oblig));
            $timemodified = $protocole->timemodified;
			$actif = $protocole->actif;
            $commentaire = $this->purge_sep(trim($protocole->commentaire));

$expout .= $seuil_referentiel.";".$minima_referentiel
.";".stripslashes($this->output_codage_caractere($l_domaines_oblig))
.";".stripslashes($this->output_codage_caractere($l_seuils_domaines))
.";".stripslashes($this->output_codage_caractere($l_minimas_domaines))
.";".stripslashes($this->output_codage_caractere($l_competences_oblig))
.";".stripslashes($this->output_codage_caractere($l_seuils_competences))
.";".stripslashes($this->output_codage_caractere($l_minimas_competences))
.";".stripslashes($this->output_codage_caractere($l_items_oblig))
.";".$timemodified
.";".$actif
.";".stripslashes($this->output_codage_caractere($commentaire))."\n";
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
    	// add header
		$expout .= "#code_referentiel;nom_referentiel;description_referentiel;url_referentiel;date_creation;nb_domaines;seuil_certificat;liste_codes_competences;liste_empreintes;logo_referentiel;minima_certificat\n";

		if ($this->rreferentiel){
		    $id = $this->rreferentiel->id;
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->purge_sep($this->rreferentiel->description_referentiel);
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$minima_certificat = $this->rreferentiel->minima_certificat;
			$timemodified = $this->rreferentiel->timemodified;
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$local = $this->rreferentiel->local;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$logo_referentiel = $this->rreferentiel->logo_referentiel;

			// PAS DE LOGO ICI
			// $expout .= stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($name)).";".$this->output_codage_caractere($description_referentiel).";$url_referentiel;$timemodified;$nb_domaines;$seuil_certificat;".stripslashes($this->output_codage_caractere($liste_codes_competence)).";".stripslashes($this->output_codage_caractere($liste_empreintes_competence)).";".$logo_referentiel."\n";
			$expout .= stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($name)).";".$this->output_codage_caractere($description_referentiel).";$url_referentiel;".referentiel_timestamp_date_special($timemodified).";$nb_domaines;$seuil_certificat;".stripslashes($this->output_codage_caractere($liste_codes_competence)).";".stripslashes($this->output_codage_caractere($liste_empreintes_competence)).";$minima_certificat\n";
            // DOMAINES
			if (isset($this->rreferentiel->id) && ($this->rreferentiel->id>0)){

                // MODIF JF 2012/03/09
	       		// PROTOCOLE
                if (!empty($this->rreferentiel->id)){
                    if ($record_protocol=referentiel_get_protocol($this->rreferentiel->id)){
                        $expout .= "#protocole_seuil;protocole_minima;l_domaines_oblig;l_seuils_domaines;l_minimas_domaines;l_competences_oblig;l_seuils_competences;l_minimas_competences;l_items_oblig;timemodified;actif;commentaire\n";
                        $expout .= $this->write_protocol( $record_protocol );
                    }
                }


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
						$expout .= "#code_domaine;description_domaine;num_domaine;nb_competences;type_domaine;seuil_domaine;minima_domaine\n";
						$expout .= $this->write_domaine( $record_d );
					}
				}
            }
        }
        return $expout;
    }

	/***************************************************************************

	// IMPORT FUNCTIONS START HERE

	***************************************************************************/
     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function importation_referentiel_possible(){
	// selon les parametres soit cree une nouvelle instance
	// soit modifie une instance courante de referentiel
	global $CFG;

		if (!isset($this->action) || (isset($this->action) && ($this->action!="selectreferentiel") && ($this->action!="importreferentiel"))){
			if (!(isset($this->course->id) && ($this->course->id>0))
				||
				!(isset($this->rreferentiel->id) && ($this->rreferentiel->id>0))
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
		return true;
	}


     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_referentiel( $lines ) {
	// recupere le tableau de lignes
	// selon les parametres soit cree une nouvelle instance
	// soit modifie une instance courante de referentiel
	global $SESSION;
	global $USER;
	global $CFG;

	if (!$this->importation_referentiel_possible()){
		exit;
	}

	// initialiser les variables
	// id du nouveau referentiel si celui ci doit être cree
	$new_referentiel_id=0;
	$auteur="";
	$l_id_referentiel = "id_referentiel";
   	$l_code_referentiel = "code_referentiel";
    $l_description_referentiel = "description_referentiel";
	$l_date_creation = "date_creation";
	$l_nb_domaine = "nb_domaine";
	$l_seuil_certification = "seuil_certificat";
	$l_local = "local";
	$l_name = "name";
	$l_url_referentiel = "url_referentiel";
	$l_liste_competences= "liste_competences";
	$l_liste_empreintes= "liste_empreintes";
	$l_logo= "logo_referentiel";

	$ok_referentiel_charge=false;

// MODIF JF 2012/03/09
    $ok_protocole_charge=false;

    $ok_domaine_charge=false;
	$ok_competence_charge=false;
	$ok_item_charge=false;

	$risque_ecrasement=false;

    // get some error strings
    $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
    $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
	$error_override = get_string( 'overriderisk', 'referentiel' );

	// DEBUT
	// Decodage
	$line = 0;
	// TRAITER LA LIGNE D'ENTETE
	$nbl=count($lines);
	if ($nbl>0){ // premiere ligne entete fichier csv
        // echo "<br />DEBUG :: forme/csv/format.php :: 424\n";
        // echo "<br />LIGNE $line --------------<br />\n".$lines[$line]."<br />\n";

		// "#Moodle Referentiel CSV Export;latin1;Prénom NOM\n";

        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
	  	$line++;
		if (substr($lines[$line],0,1)=='#'){
			// labels
	        /// If a line is incorrectly formatted
            if (count($fields) < 3 ) {
	           	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted - ignoring\n");
				}
           	}
			if (isset($fields[1]) && ($fields[1]!="")){
		        $this->table_caractere_input=trim($fields[1]);
			}
			$auteur=trim($fields[2]);
		}
	}
	else{
		$this->error("ERROR : CSV File incorrect\n");
	}

	// echo "<br />DEBUG :: 991 : $this->table_caractere_input\n";


	if ($nbl>1){ // deuxieme ligne : entete referentiel
		// echo "<br />$line : ".$lines[$line]."\n";
		// #code_referentiel;name;description_referentiel;url_referentiel;date_creation;
		// nb_domaines;seuil_certification;liste_competences
        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        /// If a line is incorrectly formatted
        if (count($fields) < 3 ) {
           	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
				$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    		}
		}
		if (substr($lines[$line],0,1)=='#'){
			// labels
    	    $l_code_referentiel = trim($fields[0]);
			$l_name = trim($fields[1]);
	        $l_description_referentiel = trim($fields[2]);
			if (isset($fields[3]))
				$l_url_referentiel = trim($fields[3]);
			else
				$l_url_referentiel = "";
			if (isset($fields[4]))
			    $l_date_creation = trim($fields[4]);
			else
				$l_date_creation = "";
			if (isset($fields[5]))
				$l_nb_domaines = trim($fields[5]);
			else
				$l_nb_domaines = "";
			if (isset($fields[6]))
				$l_seuil_certificat = trim($fields[6]);
			else
				$l_seuil_certificat = "";
			if (isset($fields[7]))
				$l_liste_competences = trim($fields[7]);
			else
				$l_liste_competences = "";
			if (isset($fields[8]))
				$l_liste_empreintes = trim($fields[8]);
			else
				$l_liste_empreintes = "";
			if (isset($fields[9]))
				$l_logo = trim($fields[9]);
			else
				$l_logo = "";
// MODIF JF 2012/03/26
			if (isset($fields[10]))
				$l_minima_certificat = trim($fields[10]);
			else
				$l_minima_certificat = "";

		}
		else{
			// data  : referentiel
    		$code_referentiel = $this->input_codage_caractere(trim($fields[0]));
	    	$name = $this->input_codage_caractere(trim($fields[1]));
			$description_referentiel = $this->input_codage_caractere(trim($fields[2]));
			if (isset($fields[3]))
				$url_referentiel = trim($fields[3]);
			else
				$url_referentiel = "";
			if (isset($fields[4]))
				$date_creation = trim($fields[4]);
			else
				$date_creation = "";
			if (isset($fields[5]))
				$nb_domaines = trim($fields[5]);
			else
				$nb_domaines = "";
			if (isset($fields[6]))
				$seuil_certificat = trim($fields[6]);
			else
				$seuil_certificat = 0.0;
			if (isset($fields[7]))
				$liste_competences = $this->input_codage_caractere(trim($fields[7]));
			else
				$liste_competences = "";
			if (isset($fields[8]))
				$liste_empreintes = $this->input_codage_caractere(trim($fields[8]));
			else
				$liste_empreintes = "";
			if (isset($fields[9]))
				$logo_referentiel = trim($fields[9]);
			else
				$logo_referentiel = "";
// MODIF JF 2012/03/26
			if (isset($fields[10]))
				$minima_certificat = trim($fields[10]);
			else
				$minima_certificat = 0;

			$ok_referentiel_charge=true;
		}
		$line++;
	}

	// maintenant les données indispensables
	while (($line<$nbl) && ($ok_referentiel_charge==false)){ // data : referentiel
		// echo "<br />$line : ".$lines[$line]."\n";
		// #referentiel_id;code_referentiel;description_referentiel;date_creation;
		// nb_domaines;seuil_certificat;local;name;url_referentiel;liste_competences
        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        /// If a line is incorrectly formatted
        if (count($fields) < 3 ) {
          	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
				$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    		}
			continue;
		}
		// DEBUG
        // echo "<br />DEBUG : ./mod/refrentiel/format/csv/format.php :: 560<br >\n";
        // print_r($fields);
		// data  : referentiel
    	$code_referentiel = $this->input_codage_caractere(trim($fields[0]));
	    $name = $this->input_codage_caractere(trim($fields[1]));
		$description_referentiel = $this->input_codage_caractere(trim($fields[2]));
		if (isset($fields[3]))
			$url_referentiel = trim($fields[3]);
		else
			$url_referentiel = "";
		if (isset($fields[4]))
			$date_creation = trim($fields[4]);
		else
			$date_creation = "";
		if (isset($fields[5]))
			$nb_domaines = trim($fields[5]);
		else
			$nb_domaines = "";
		if (isset($fields[6]))
			$seuil_certificat = trim($fields[6]);
		else
			$seuil_certificat = 0.0;
		if (isset($fields[7]))
			$liste_competences= $this->input_codage_caractere(trim($fields[7]));
		else
			$liste_competences= "";
		if (isset($fields[8]))
			$liste_empreintes = $this->input_codage_caractere(trim($fields[8]));
		else
			$liste_empreintes = "";
		if (isset($fields[9]))
			$logo_referentiel = trim($fields[9]);
		else
			$logo_referentiel = "";
// MODIF JF 2012/03/26
		if (isset($fields[10]))
				$minima_certificat = trim($fields[10]);
			else
				$minima_certificat = 0;

		$ok_referentiel_charge=true;
	  	$line++;
	}

	if (!$ok_referentiel_charge){
		$this->error( get_string( 'incompletedata', 'referentiel' ) );
	}
	// this routine initialises the import object
    $re = $this->defaultreferentiel();

	$re->name=str_replace("'", " ",$name);
	// $re->name=addslashes($name);
	$re->code_referentiel=$code_referentiel;
	$re->description_referentiel=str_replace("'", "`",$description_referentiel);
	// $re->description_referentiel=addslashes($description_referentiel);
	$re->url_referentiel=$url_referentiel;
	$re->seuil_certificat=$seuil_certificat;
	$re->minima_certificat=$minima_certificat;
	$re->timemodified = $date_creation;
	$re->nb_domaines=$nb_domaines;
	$re->liste_codes_competence=$liste_competences;
	$re->liste_empreintes_competence=$liste_empreintes;
	$re->logo_referentiel=$logo_referentiel;

	/*
	// GROS BUG
	if ($id_referentiel!=""){
		$re->id=$id_referentiel;
	}
	*/
	$re->id=0;

	// DEBUG
	// print_r($re);

	// RISQUE ECRASEMENT ?
	$risque_ecrasement = false; //
	if (($this->rreferentiel) && ($this->rreferentiel->id>0)){ // charger le referentiel associé à l'instance
		$this->rreferentiel = referentiel_get_referentiel_referentiel($this->rreferentiel->id);
		if ($this->rreferentiel){
			$risque_ecrasement = (($this->rreferentiel->name==$re->name) && ($this->rreferentiel->code_referentiel==$re->code_referentiel));
		}
	}

	// SI OUI arrêter
	if ($risque_ecrasement==true){
		if ($this->override!=1){
			$this->error($error_override);
		}
		else {
			// le referentiel courant est remplace
			$new_referentiel_id=$this->rreferentiel->id;
			$re->id=$new_referentiel_id;
		}
	}

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
			isset($re->course) && ($re->course>0))
		{
			// sauvegarder ?
			if ($this->course->id==$re->course){
				if 	(
						(isset($this->rreferentiel->id) && ($this->rreferentiel->id==$re->id))
						||
						(
							(isset($this->rreferentiel->name) && ($this->rreferentiel->name==$re->name))
							&&
							(isset($this->rreferentiel->code_referentiel) && ($this->rreferentiel->code_referentiel==$re->code_referentiel))
						)
					)
				{
					$risque_ecrasement=true;
				}
			}
		}
	}

	// DEBUG
	/*
	if ($risque_ecrasement)
		echo "<br />DEBUG : 607 : Risque d'ecrasement N:$this->newinstance O:$this->override\n";
	else
		echo "<br />DEBUG : 607 : Pas de risque d'ecrasement N:$this->newinstance O:$this->override\n";
	*/

	if (($risque_ecrasement==false) || ($this->newinstance==1)) {
		// Enregistrer dans la base comme un nouveau referentiel du cours courant
		// DEBUG
		// echo "<br />DEBUG csv/format.php ligne 704<br />\n";
		// print_object($re);
        // exit;
		$new_referentiel_id=referentiel_add_referentiel($re); // retourne un id de la table refrentiel_referentiel
		$this->setReferentielId($new_referentiel_id);
	}
	else if (($risque_ecrasement==true) && ($this->override==1)) {
		// Enregistrer dans la base en remplaçant la version courante (update)
		// NE FAUDRAIT IL PAS SUPPRIMER LE REFERENTIEL AVANT DE LA RECHARGER ?
		$re->instance=$this->rreferentiel->id;
		$re->referentiel_id=$this->rreferentiel->id;
		// DEBUG
		// echo "<br />DEBUG csv/format.php ligne 638<br />MISE A JOUR  : ".$r->rreferentiel_id."\n";

		$ok=referentiel_update_referentiel($re); // retourne un id de la table referentiel_referentiel
		$new_referentiel_id=$this->rreferentiel->id;
	}
	else {
		// ni nouvelle instance ni recouvrement
		$this->error("ERREUR 2 ".$error_override );
		return false;
	}

	if (isset($new_referentiel_id) && ($new_referentiel_id>0)){
		// IMPORTER LE RESTE DU REFERENTIEL
		$dindex=0;
		$cindex=0;
		$iindex=0;

        $re->domaines = array();
		$new_domaine_id=0;
		$new_competence_id=0;
		$numero_domaine=0; // compteur pour suppleer le numero si non importe
		$numero_competence=0;
		$numero_item=0;
		$num_domaine=0;
		$num_competence=0;
		$type_domaine=0;
		$seuil_domaine=0.0;
		$type_competence=0;
		$seuil_competence=0.0;
		$minima_domaine=0.0;
		$minima_competence=0;

		$num_item=0;

		$is="";

        $pindex=0;
        $is_protocole = false;
		$ok_protocole_charge=false;
        $nbprotocoles=0;

		$is_domaine=false;
		$is_competence=false;
		$is_item=false;

		$mode="add";

		while ($line<$nbl) {
			// echo "<br />DEBUG 652 :: <br />".$lines[$line]."\n";
           	$fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
	        if (count($fields) < 2 ) {
    	      	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    			}
				continue;
			}

			// print_r($fields);
			// Label ou data ?
			// echo "<br />".substr($fields[0],0,1)."\n";

			if (substr($fields[0],0,1)=='#'){
				// labels
				// on s'en sert pour construire l'arbre
				$is=trim($fields[0]);

                $is_protocole=false;

				$is_domaine=false;
				$is_competence=false;
				$is_item=false;

				switch($is){

					case '#protocole_seuil' :
                        // #protocole_seuil;l_domaines_oblig;l_seuils_domaines;l_competences_oblig;l_seuils_competences;l_items_oblig;timemodified;actif;commentaire
						$is_protocole=true;
					break;
					case '#code_domaine' :
						// #code_domaine;description_domaine;num_domaine;nb_competences;type_domaine;seuil_domaine
						$is_domaine=true;
					break;
					case '#code_competence' :
						// #code_competence;description_competence;num_competence;nb_item_competences;type_competence;seuil_competence
						$is_competence=true;
					break;
					case '#code_item' :
						// #code_item;description_item;type_item;poids_item;num_item
						$is_item=true;
					break;
					default :
						$this->error("ERROR : CSV File incorrect line number:".$line."\n");
					break;
				}
			}
			else if (isset($is) && ($is!="")){
				// data
				switch($is){
// MODIF JF 2012/03/09
					case '#protocole_seuil' :
						// Protocole
						// data
						$seuil_referentiel = trim($fields[0]);
						$minima_referentiel = trim($fields[1]);
$l_domaines_oblig=addslashes($this->input_codage_caractere(trim($fields[2])));
$l_seuils_domaines=addslashes($this->input_codage_caractere(trim($fields[3])));
$l_minimas_domaines=addslashes($this->input_codage_caractere(trim($fields[4])));
$l_competences_oblig=addslashes($this->input_codage_caractere(trim($fields[5])));
$l_seuils_competences=addslashes($this->input_codage_caractere(trim($fields[6])));
$l_minimas_competences=addslashes($this->input_codage_caractere(trim($fields[7])));
$l_items_oblig=addslashes($this->input_codage_caractere(trim($fields[8])));
$timemodified = trim($fields[9]);
$actif = trim($fields[10]);
$commentaire=addslashes($this->input_codage_caractere(trim($fields[11])));

                        // Creer un enregistrement
						$new_protocole = $this->defaultprotocole();

                        // sauvegarder dans la base
			            // remplacer l'id du referentiel importe par l'id du referentiel cree
                        // trafiquer les donnees pour appeler la fonction ad hoc
                        $new_protocole->ref_occurrence=$new_referentiel_id;
			            // $new_protocole->id=$this->getpath( $protocole, array('#','p_id',0,'#'), '', false, '');
            			// $new_protocole->ref_occurrence;
                        $new_protocole->seuil_referentiel=$seuil_referentiel;
                        $new_protocole->minima_referentiel=$minima_referentiel;

// A recreer a partir des domaines et competences pourr eviter incoherences
                        // La suite initialise en chargeant les domaines / compétences / items
                        // $new_protocole->l_domaines_oblig=$l_competences_oblig;
                        // $new_protocole->l_seuils_domaines= $l_seuils_domaines;
                        // $new_protocole->l_competences_oblig=$l_competences_oblig;
                        // $new_protocole->l_seuils_competences=$l_seuils_competences;
                        // $new_protocole->l_minimas_competences=$l_minimas_competences;
                        // $new_protocole->l_items_oblig= $l_items_oblig;

                        $new_protocole->timemodified=$timemodified;
                        $new_protocole->actif=$actif;
                        $new_protocole->commentaire=$commentaire;

			            // sauvegarder dans la base
			            // remplacer l'id du referentiel importe par l'id du referentiel cree
			            // trafiquer les donnees pour appeler la fonction ad hoc
			            $new_protocole->ref_occurrence=$new_referentiel_id;
			            // DEBUG
			            // echo "<br />DEBUG ./format/csv/format.php :: 710<br />\n";
			            // print_object($new_protocole);

			            if (referentiel_add_protocol($new_protocole)){
                            $nbprotocoles++;
                        }
						// enregistrer
						$pindex++;
						$re->protocoles[$pindex]=$new_protocole;
						$ok_protocole_charge=true;

					break;

					case '#code_domaine' :
						// $code_domaine;$description_domaine;$num_domaine;$nb_competences
						// Domaines
						// data
						$code_domaine = addslashes($this->input_codage_caractere(trim($fields[0])));
						$description_domaine = addslashes($this->input_codage_caractere(trim($fields[1])));
						$num_domaine = trim($fields[2]);
						$nb_competences = trim($fields[3]);
						if (isset($fields[4])){
                            $type_domaine = trim($fields[4]);
                        }
                        else{
                            $type_domaine = 0;
                        }
						if (isset($fields[5])){
                            $seuil_domaine = trim($fields[5]);
                        }
                        else{
                            $seuil_domaine = 0;
                        }
						if (isset($fields[6])){
                            $minima_domaine = trim($fields[6]);
                        }
                        else{
                            $minima_domaine = 0;
                        }



						if ($code_domaine!=""){
							// Creer un domaine
							$numero_domaine++;
							$new_domaine = array();
							$new_domaine = $this->defaultdomaine();

							$new_domaine->code_domaine=$code_domaine;
							if ($description_domaine!="")
								$new_domaine->description_domaine = $description_domaine;

							if ($num_domaine!="")
								$new_domaine->num_domaine=$num_domaine;
							else
								$new_domaine->num_domaine=$numero_domaine;
							if ($nb_competences!="")
								$new_domaine->nb_competences=$nb_competences;
							else
								$new_domaine->nb_competences=0;

							if ($type_domaine!="")
								$new_domaine->type_domaine=$type_domaine;
							else
								$new_domaine->type_domaine=0;

							if ($seuil_domaine!="")
								$new_domaine->seuil_domaine=$seuil_domaine;
							else
								$new_domaine->seuil_domaine=0.0;

							if ($minima_domaine!="")
								$new_domaine->minima_domaine=$minima_domaine;
							else
								$new_domaine->minima_domaine=0;

							$new_domaine->ref_referentiel=$new_referentiel_id;

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

							if (isset($new_domaine_id) && ($new_domaine_id>0)){
								$new_domaine->id=$new_domaine_id;
							}
							else{
								$new_domaine->id=0;
							}

							// enregistrer
							$dindex++;
							$re->domaines[$dindex]=$new_domaine;
							$cindex=0;
							$re->domaines[$dindex]->competences=array();
							$numero_competence=0;
							$ok_domaine_charge=true;
						}
						else{
							$ok_domaine_charge=false;
						}
					break;
					case '#code_competence' :
						// $competence_id;$code_competence;$description_competence;$ref_domaine;
						// $num_competence;$nb_item_competences
						$code_competence = addslashes($this->input_codage_caractere(trim($fields[0])));
						$description_competence = addslashes($this->input_codage_caractere(trim($fields[1])));
						$num_competence = trim($fields[2]);
						$nb_item_competences = trim($fields[3]);
						if (isset($fields[4])){
                            $type_competence = trim($fields[4]);
                        }
                        else{
                            $type_competence = 0;
                        }
						
						if (isset($fields[5])){
                            $seuil_competence = trim($fields[5]);
                        }
                        else{
                            $seuil_competence = 0;
                        }
						if (isset($fields[6])){
                            $minima_competence = trim($fields[6]);
                        }
                        else{
                            $minima_competence = 0;
                        }
						if (($code_competence!="") && ($ok_domaine_charge) && ($new_domaine_id>0)){
							// Creer une competence
							$new_competence_id=0;
							$numero_competence++;
							$new_competence = array();
							$new_competence = $this->defaultcompetence();

							$new_competence->id=0;
							$new_competence->code_competence=$code_competence;
							if ($description_competence!="")
								$new_competence->description_competence = $description_competence;
							if ($num_competence!="")
								$new_competence->num_competence=$num_competence;
							else
								$new_competence->num_competence=$numero_competence;
							if ($nb_item_competences!="")
								$new_competence->nb_item_competences=$nb_item_competences;
							else
								$new_competence->nb_item_competences=0;

							if ($type_competence!="")
								$new_competence->type_competence=$type_competence;
							else
								$new_competence->type_competence=0;

							if ($seuil_competence!="")
								$new_competence->seuil_competence=$seuil_competence;
							else
								$new_competence->seuil_competence=0.0;

							if ($minima_competence!="")
								$new_competence->minima_competence=$minima_competence;
							else
								$new_competence->minima_competence=0;

							if (isset($new_domaine_id) && ($new_domaine_id>0)){
								$new_competence->ref_domaine=$new_domaine_id;
							}
							else{
								$new_competence->ref_domaine=0;
							}

							// sauvegarder dans la base
							// remplacer l'id du referentiel importe par l'id du referentiel cree
							$new_competence->ref_domaine=$new_domaine_id;
							// trafiquer les donnees pour appeler la fonction ad hoc
							$new_competence->instance=$new_referentiel_id; // pour que ca marche
							$new_competence->new_code_competence=$new_competence->code_competence;
							$new_competence->new_description_competence=$new_competence->description_competence;
							$new_competence->new_ref_domaine=$new_domaine_id;
							$new_competence->new_num_competence=$new_competence->num_competence;
							$new_competence->new_nb_item_competences=$new_competence->nb_item_competences;

							$new_competence->new_type_competence=$new_competence->type_competence;
							$new_competence->new_seuil_competence=$new_competence->seuil_competence;

							$new_competence->new_minima_competence=$new_competence->minima_competence;

							// creation
							$new_competence_id=referentiel_add_competence($new_competence);
							$new_competence->id=$new_competence_id;

							// enregistrer
							$cindex++;
							$re->domaines[$dindex]->competences[$cindex]=$new_competence;
							$iindex=0; // nouveaux items à suivre
							$re->domaines[$dindex]->competences[$cindex]->items=array();

							$numero_item=0;
							$ok_competence_charge=true;
						}
						else{
							$ok_competence_charge=false;
						}
					break;
					case '#code_item' :
						// $code_item;$description_item;$type_item;$poids_item;$num_item;$empreinte_item
						$code_item = $this->input_codage_caractere(addslashes(trim($fields[0])));
						$description_item = $this->input_codage_caractere(addslashes(trim($fields[1])));
						$type_item = $this->input_codage_caractere(addslashes(trim($fields[2])));
						$poids_item = trim($fields[3]);
						$num_item = trim($fields[4]);
						if (isset($fields[5]) && (trim($fields[5])!="")){
							$empreinte_item = trim($fields[5]);
						}
						else{
							$empreinte_item = "1";
						}
						if (($code_item!="") && ($ok_competence_charge) && ($new_competence_id>0)){
							// Creer un domaine
							$numero_item++;
							$new_item = array();
							$new_item = $this->defaultitem();
							$new_item->code_item=$code_item;
							if ($description_item!="")
								$new_item->description_item = $description_item;
							$new_item->ref_referentiel=$new_referentiel_id;
							$new_item->ref_competence=$new_competence_id;
							$new_item->type_item=$type_item;
							$new_item->poids_item=$poids_item;
							if ($num_item!="")
								$new_item->num_item=$num_item;
							else
								$new_item->num_item=$numero_item;
							$new_item->empreinte_item=$empreinte_item;

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
							$new_item->id=$new_item_id;

							$iindex++;
							$re->domaines[$dindex]->competences[$cindex]->items[$iindex]=$new_item;
							$ok_item_charge=true;
						}
						else{
							$ok_item_charge=false;
						}
					break;
					default :
						$this->error("ERROR : CSV File incorrect line number:".$line."\n");
					break;
				}
			}
			// that's all folks
			$line++;
        } // end of while loop
		if ($mode=="add"){
			// rien de special ici ?
		}
	    return $re;
	}
	return false;
}


    /**
     * parse the array of lines into an array of questions
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array (of objects) question objects
     */
    function read_import_referentiel($lines) {
        // we just need it as one array
		$re=$this->import_referentiel($lines);
        return $re;
    }
}


/** ******************************************

EXPORT ACTIVITES

*/

// ACTIVITES : export des activites
class aformat_csv extends aformat_default {
// NON SUPPORTE POUR LE FORMAT CSV
	var $sep = ";";
	
	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1


	// ----------------
	function guillemets($texte){
		return '"'.trim($texte).'"';
	}

	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return $this->guillemets(str_replace($cherche, $remplace, $texte));
	}


	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}



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
		$xp =  "Moodle Referentiel CSV Export\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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
        // insere un saut de ligne apres le 80 caractere 
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
     * Turns document into an xml segment
     * @param document object
     * @return string xml segment
     */

    function write_document( $document ) {
    global $CFG;
       // initial string;
        $expout = "";
		if ($document){
			$id_document = $document->id ;		
            $type_document = trim($document->type_document);
            $description_document = $this->purge_sep($document->description_document);
			$url_document = $document->url_document;
            $ref_activite = $document->ref_activite;
            $timestamp = $document->timestamp;
            $expout .= "$id_document;".stripslashes($this->output_codage_caractere($type_document)).";".stripslashes($this->output_codage_caractere($description_document)).";$url_document;$ref_activite;$timestamp\n";
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
		if ($activite){
			// DEBUG
			// echo "<br />\n";
			// print_r($activite);
			$id_activite = $activite->id;
            $type_activite = $this->purge_sep(strip_tags($activite->type_activite));
			$description_activite = $this->purge_sep(strip_tags($activite->description_activite));
            $competences_activite = trim($activite->competences_activite);
            $commentaire_activite = $this->purge_sep($activite->commentaire_activite);
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
            
            $expout .= "#id_activite;type_activite;description_activite;competences_activite;commentaire_activite;ref_instance;ref_referentiel;ref_course;userid;lastname;firstname;teacherid;teacher_lastname;teacher_firstname;date_creation;date_modif_student;date_modif;approved\n";
			$expout .= "$id_activite;".stripslashes($this->output_codage_caractere($type_activite)).";".stripslashes($this->output_codage_caractere($description_activite)).";".stripslashes($this->output_codage_caractere($competences_activite)).";".stripslashes($this->output_codage_caractere($commentaire_activite)).";$ref_instance;$ref_referentiel;$ref_course;$userid;".stripslashes($this->output_codage_caractere($lastname)).";".stripslashes($this->output_codage_caractere($firstname)).";".$teacherid;"".stripslashes($this->output_codage_caractere($lastname)).";".stripslashes($this->output_codage_caractere($firstname)).";".referentiel_timestamp_date_special($date_creation).";".referentiel_timestamp_date_special($date_modif_student).";".referentiel_timestamp_date_special($date_modif).";$approved\n";
			
			// DOCUMENTS
			$records_documents = referentiel_get_documents($activite->id);
			
			if ($records_documents){
				$expout .= "#id_document;type_document;description_document;url_document;ref_activite;timestamp\n";
				foreach ($records_documents as $record_d){
					$expout .= $this->write_document( $record_d );
				}
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
		// 
		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = $this->purge_sep($this->ireferentiel->name);
            $description_instance = $this->purge_sep($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;
			
			// $expout .= "#Instance de referentiel : $this->ireferentiel->name\n";
			$expout .= "#id_instance;name;description_instance;label_domaine;label_competence;label_item;date_instance;course;ref_referentiel;visible\n";
			$expout .= "$id;".stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($description_instance)).";".stripslashes($this->output_codage_caractere($label_domaine)).";".stripslashes($this->output_codage_caractere($label_competence)).";".stripslashes($this->output_codage_caractere($label_item)).";".referentiel_timestamp_date_special($date_instance).";$course;$ref_referentiel;$visible\n";
			
			// ACTIVITES
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
				$records_activites = referentiel_get_activites_instance($this->ireferentiel->id);
		    	if ($records_activites){
					foreach ($records_activites as $record_a){
						// DEBUG
						// print_r($record_a);
						// echo "<br />\n";
						$expout .= $this->write_activite( $record_a );
					}
				}
			}
		}
        return $expout;
    }
}

// ##########################################################################################################
// *************************************
// CERTIFICATS : export des certificats
// *************************************
class cformat_csv extends cformat_default {
	var $sep = ";";
	
	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1

	// ----------------
	function guillemets($texte){
		return '"'.trim($texte).'"';
	}


	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return $this->guillemets(str_replace($cherche, $remplace, $texte));
	}


	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}

    function provide_export() {
      return true;
    }

	function provide_import() {
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
		$xp =  "#Moodle Certification CSV Export;latin1;\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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

    function write_item( $item ) {
    global $CFG;
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\nitem: $item->id\n";
		// $expout .= "id;code_item;description_item;ref_referentiel;ref_competence;type_item;poids_item;num_item\n";
		// 
		if ($item){
			// DEBUG
			// echo "<br />\n";
			// print_r($item);
			$id_item = $item->id;
            $code = $item->code_item;
            $description_item = $this->purge_sep($item->description_item);
            $ref_referentiel = $item->ref_referentiel;
            $ref_competence = $item->ref_competence;
			$type_item = $item->type_item;
			$poids_item = $item->poids_item;
			$num_item = $item->num_item;
			$empreinte_item = $item->empreinte_item;
            $expout .= "$id_item;".stripslashes($this->output_codage_caractere($code)).";".stripslashes($this->output_codage_caractere($description_item)).";".$this->output_codage_caractere($type_item).";$poids_item;$num_item;$empreinte_item\n";
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
        // $expout .= "\ncompetence: $competence->id\n";
		if ($competence){
			$id_competence = $competence->id;
            $code = $competence->code_competence;
            $description_competence = $this->purge_sep($competence->description_competence);
            $ref_domaine = $competence->ref_domaine;
			$num_competence = $competence->num_competence;
			$nb_item_competences = $competence->nb_item_competences;
			$expout .= "$id_competence;".stripslashes($this->output_codage_caractere($code)).";".stripslashes($this->output_codage_caractere($description_competence)).";$ref_domaine;$num_competence;$nb_item_competences\n";
			
			// ITEM
			$compteur_item=0;
			$records_items = referentiel_get_item_competences($competence->id);
			
			if ($records_items){
				// DEBUG
				// echo "<br/>DEBUG :: ITEMS <br />\n";
				// print_r($records_items);
				$expout .= "#id_item;code_item;description_item;ref_referentiel;ref_competence;type_item;poids_item;num_item\n";				
				foreach ($records_items as $record_i){
					// DEBUG
					// echo "<br/>DEBUG :: ITEM <br />\n";
					// print_r($record_i);
					$expout .= $this->write_item( $record_i );
				}
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
        $expout = "#domaine_id;code;description;ref_referentiel;num_domaine;nb_competences\n";
        // add comment		
		if ($domaine){
            $code = $domaine->code_domaine;
            $description_domaine = $this->purge_sep($domaine->description_domaine);
            $ref_referentiel = $domaine->ref_referentiel;
			$num_domaine = $domaine->num_domaine;
			$nb_competences = $domaine->nb_competences;			
			$expout .= stripslashes($this->output_codage_caractere($code)).";".stripslashes($this->output_codage_caractere($description_domaine)).";$ref_referentiel;$num_domaine;$nb_competences\n";
			
			// LISTE DES COMPETENCES DE CE DOMAINE
			$compteur_competence=0;
			$records_competences = referentiel_get_competences($domaine->id);
			if ($records_competences){
				// DEBUG
				// echo "<br/>DEBUG :: COMPETENCES <br />\n";
				// print_r($records_competences);
				foreach ($records_competences as $record_c){
					$expout .= "#id_competence;code_competence;description_competence;num_competence;nb_item_competences\n";								
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
        // initial string;
		$expout ="";
		if ($this->rreferentiel){
			$id = $this->rreferentiel->id;
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
            $description_referentiel = $this->purge_sep($this->rreferentiel->description_referentiel);
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$timemodified = $this->rreferentiel->timemodified;			
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;
        	
			// $expout = "#Referentiel : ".$this->rreferentiel->id." : ".stripslashes($this->output_codage_caractere($this->rreferentiel->name))."\n";
            // add header
            if ($this->format_condense==1){
                $expout .= "#name;code_referentiel;description_referentiel;\n";
                $expout .= stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($description_referentiel))."\n";

                $expout .= "#user_id;login;num_etudiant;NOM;Prenom;";
                $expout .= $this->liste_codes_competences($this->rreferentiel->id);
                if ($this->export_pedagos){
                    $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
                }
                $expout .= "\n";
            }
            else if ($this->format_condense==2){
                $expout .= "#name;code_referentiel;description_referentiel;\n";
                $expout .= stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($description_referentiel))."\n";
            }
            else{
                $expout .= "#id_referentiel;name;code_referentiel;description_referentiel;url_referentiel;seuil_certificat;timemodified;nb_domaines;liste_codes_competences;liste_empreintes_competences;local\n";
                $expout .= "$id;".stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($description_referentiel)).";$url_referentiel;$seuil_certificat;".referentiel_timestamp_date_special($timemodified).";$nb_domaines;".stripslashes($this->output_codage_caractere($liste_codes_competence)).";".stripslashes($this->output_codage_caractere($liste_empreintes_competence)).";$local\n";

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
        }
        return $expout;
    }

	function write_etablissement( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\netablissement: $record->id\n";
		if ($record){
			// $expout .= "#id_etablissement;num_etablissement;nom_etablissement;dresse_etablissement\n";
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = $this->purge_sep($record->nom_etablissement);
			$adresse_etablissement = $this->purge_sep($record->adresse_etablissement);
			
			$expout .= "$id;".stripslashes($this->output_codage_caractere($num_etablissement)).";".stripslashes($this->output_codage_caractere($nom_etablissement)).";".stripslashes($this->output_codage_caractere($adresse_etablissement))."\n";
        }
        return $expout;
    }

    function write_liste_etablissements() {
    	global $CFG;
        // initial string;
        $expout = ""; 
		// ETABLISSEMENTS
		$records_all_etablissements = referentiel_get_etablissements();
		if ($records_all_etablissements){
			$expout .= "#id_etablissement;num_etablissement;nom_etablissement;adresse_etablissement\n";		
			foreach ($records_all_etablissements as $record){
				if ($record){
					$expout.=$this->write_etablissement($record);
				}
			}
        }
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */
	 /**
     *
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_certificat( $record ) {
    	global $CFG;
        // initial string;
        $expout = "";
    	// $expout .= "\ncertificat : $record->id\n";
		// USER
        if ($record){
			//$expout .= "#id_etudiant;user_id;login;num_etudiant;NOM;Prenom;ddn_etudiant;lieu_naissance;departement_naissance;adresse_etudiant;ref_etablissement;id_certificat;commentaire_certificat;competences_certificat;decision_jury;date_decision;ref_referentiel;verrou;valide;evaluation\n";
			$ok_etudiant=false;

			$record_etudiant = referentiel_get_etudiant_user($record->userid);
			if (!$record_etudiant){
                // creer l'enregistrement car on en a besoin immediatement
                if (referentiel_add_etudiant_user($record->userid)){
                    $record_etudiant = referentiel_get_etudiant_user($record->userid);
                }
            }
            if ($record_etudiant){
                $id_etudiant = trim($record_etudiant->id );
                $ref_etablissement = trim($record_etudiant->ref_etablissement);
                $num_etudiant = trim($record_etudiant->num_etudiant);
                $ddn_etudiant = trim($record_etudiant->ddn_etudiant);
                $lieu_naissance = $this->purge_sep($record_etudiant->lieu_naissance);
                $departement_naissance = $this->purge_sep($record_etudiant->departement_naissance);
                $adresse_etudiant = $this->purge_sep($record_etudiant->adresse_etudiant);

                $login = trim(referentiel_get_user_login($record->userid));
/*
                if ($num_etudiant==$login){
                    $texte=$num_etudiant;
                }
                elseif ($num_etudiant==''){
                    $texte=$login;
                }
                else{
                    $texte=$num_etudiant." (".$login.")";
                }
*/

                if (!$this->format_condense){
                    $expout .= "$id_etudiant;".$record->userid.";".$this->output_codage_caractere($login).";".$this->output_codage_caractere($num_etudiant).";".stripslashes($this->output_codage_caractere(referentiel_get_user_nom($record->userid))).";".stripslashes($this->output_codage_caractere(referentiel_get_user_prenom($record->userid))).";$ddn_etudiant;".stripslashes($this->output_codage_caractere($lieu_naissance)).";".stripslashes($this->output_codage_caractere($departement_naissance)).";".stripslashes($this->output_codage_caractere($adresse_etudiant)).";$ref_etablissement;";
                }
                elseif ($this->format_condense==1){
                    $expout .= $record->userid.";".$this->output_codage_caractere($login).";".$this->output_codage_caractere($num_etudiant).";".stripslashes($this->output_codage_caractere(referentiel_get_user_nom($record->userid))).";".stripslashes($this->output_codage_caractere(referentiel_get_user_prenom($record->userid))).";";
                }
                elseif ($this->format_condense==2){
                    $expout .= $this->output_codage_caractere($login).";".$this->output_codage_caractere($num_etudiant).";".stripslashes($this->output_codage_caractere(referentiel_get_user_nom($record->userid))).";".stripslashes($this->output_codage_caractere(referentiel_get_user_prenom($record->userid))).";";
                }
                $ok_etudiant=true;
			}

			if ($ok_etudiant==false){
                if (!$this->format_condense){
                    $expout .= ";".$record->userid.";;;;;;;;;;;";
                }
                else if ($this->format_condense==1){
                    $expout .= $record->userid.";;;;;";
                }
			}

			// DEBUG
			// echo "<br />DEBUG LIGNE 1021<br />\n";
			// print_r($this->ireferentiel);
			$id = trim( $record->id );
            $commentaire_certificat = $this->purge_sep($record->commentaire_certificat);
            $synthese_certificat = $this->purge_sep($record->synthese_certificat);
            $competences_certificat =  trim($record->competences_certificat) ;
            $decision_jury = $this->purge_sep($record->decision_jury);
            $date_decision = trim($record->date_decision);
            $userid = trim( $record->userid);
            $teacherid = trim( $record->teacherid);
            $ref_referentiel = trim( $record->ref_referentiel);
			$verrou = trim( $record->verrou );
			$valide = trim( $record->valide );
			$evaluation = trim( $record->evaluation );
            $synthese_certificat = $this->purge_sep($record->synthese_certificat);

			if (!$this->format_condense){
                $expout .= "$id;".stripslashes($this->output_codage_caractere($commentaire_certificat)).";".stripslashes($this->output_codage_caractere($synthese_certificat)).";".stripslashes($this->output_codage_caractere($competences_certificat)).";".stripslashes($this->output_codage_caractere($decision_jury)).";".referentiel_timestamp_date_special($date_decision).";$ref_referentiel;$verrou;$valide;$evaluation;$synthese_certificat;";
            }
            elseif ($this->format_condense==1){
                $expout .= $this->certificat_pourcentage($competences_certificat, $this->ref_referentiel);
            }
            else{
                $expout .= stripslashes($this->output_codage_caractere($decision_jury)).";".$this->certificat_items_binaire($competences_certificat, $this->ref_referentiel);
            }
            
            // PEDAGOGIES
            if ($this->export_pedagos){
                // $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
                $rec_pedago=referentiel_get_pedagogie_user($userid, $ref_referentiel);
                if ($rec_pedago){
                    $expout .= stripslashes($this->output_codage_caractere($rec_pedago->promotion)).";".stripslashes($this->output_codage_caractere($rec_pedago->formation)).";".stripslashes($this->output_codage_caractere($rec_pedago->pedagogie)).";".stripslashes($this->output_codage_caractere($rec_pedago->composante)).";".stripslashes($this->output_codage_caractere($rec_pedago->num_groupe)).";".stripslashes($this->output_codage_caractere($rec_pedago->commentaire)).";".stripslashes($this->output_codage_caractere($rec_pedago->date_cloture));
                }
            }

            $expout .= "\n";

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
		// 
		if ($this->ireferentiel){
            $id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description_instance = $this->purge_sep($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
            $visible = $this->ireferentiel->visible;
			if ($this->format_condense==0){
                // $expout .= "#Instance de referentiel : $this->ireferentiel->name\n";
                $expout .= "#id_instance;name;description_instance;label_domaine;label_competence;label_item;date_instance;course;ref_referentiel;visible\n";
                $expout .= "$id;".stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($description_instance)).";".stripslashes($this->output_codage_caractere($label_domaine)).";".stripslashes($this->output_codage_caractere($label_competence)).";".stripslashes($this->output_codage_caractere($label_item)).";".referentiel_timestamp_date_special($date_instance).";$course;$ref_referentiel;$visible\n";
            }
        }

        if (empty($this->rreferentiel) && (!empty($this->ireferentiel->ref_referentiel) && ($this->ireferentiel->ref_referentiel>0))){
            $this->rreferentiel = referentiel_get_referentiel_referentiel($this->ireferentiel->ref_referentiel);
        }
    
        if (!empty($this->rreferentiel)){
                $expout .= $this->write_referentiel();

                if (!$this->records_certificats){
                    $this->records_certificats = referentiel_get_certificats($this->rreferentiel->id);
                }

                if ($this->records_certificats){
                    if (!$this->format_condense){
                        $expout .= "#id_etudiant;user_id;login;num_etudiant;NOM;Prenom;ddn_etudiant;lieu_naissance;departement_naissance;adresse_etudiant;ref_etablissement;id_certificat;commentaire_certificat;synthese_certificat;competences_certificat;decision_jury;date_decision;ref_referentiel;verrou;valide;evaluation;";
                        if ($this->export_pedagos){
                            $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
                        }
                        $expout .= "\n";
                    }
                    else if ($this->format_condense==1){
                        // $expout .= $this->write_liste_etablissements($this->rreferentiel);
                        // $expout .= "#user_id;login;num_etudiant;NOM;Prenom;\n";
                        // la suite de l'entete est reportée dans l'affichage du referentiel car il faut aussi afficher les codes des items...
				    }
                    else if ($this->format_condense==2){
                        $expout .= "#login;num_etudiant;NOM;Prenom;decision_jury;";
                        $expout .= $this->liste_codes_items($this->ref_referentiel);
                        if ($this->export_pedagos){
                            $expout .= "promotion;formation;pedagogie;composante;num_groupe;commentaire;date_cloture";
                        }
                        $expout .= "\n";
                    }

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
					$s.=$t_competence[$i].';';
                }
            }
        }
        return $s;

    }

    // -------------------
    function liste_codes_items($ref_referentiel){
    // retourne la liste des items
    // ITEMS
    global $t_item_code;

	// affichage
	$s='';
	// donnees globales du referentiel
	if ($ref_referentiel){
		if (!isset($OK_REFERENTIEL_DATA) || ($OK_REFERENTIEL_DATA==false) ){
			$OK_REFERENTIEL_DATA=referentiel_initialise_data_referentiel($ref_referentiel);
		}

		if (isset($OK_REFERENTIEL_DATA) && ($OK_REFERENTIEL_DATA==true)){

			for ($i=0; $i<count($t_item_code); $i++){
                $s.=$t_item_code[$i].';';
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
					if ($t_certif_item_valeur[$i]>=$t_item_empreinte[$i]){
						$s.='1;';
					}
					else{
                        $s.='0;';
					}
				}
				else {
					$s.=';';
				}
			}
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
				if ($t_competence_coeff[$i]){
					$s.=referentiel_pourcentage($t_certif_competence_poids[$i], $t_competence_coeff[$i]).'%;';
				}
				else{
					$s.='0%;';
				}
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
    
	/***************************************************************************

	// IMPORT FUNCTIONS START HERE

	***************************************************************************/

     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees
     */
    function import_certifs( $lines ) {
	// recupere le tableau de lignes
	// selon les parametres soit cree une nouvelle instance
	// soit modifie une instance courante de students
	global $SESSION;
	global $USER;
	global $CFG;
	global $DB;

    // DEBUG
    // print_r($lines);
    
    $code_ref ='';
    $t_items=array(); // les codes d'items du referentiel
	$in_referentiel=false;
	$in_certif=false;
	// initialiser les variables
	$date_creation="";

    // get some error strings
    $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
    $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
	$error_override = get_string( 'overriderisk', 'referentiel' );

	// DEBUT
	// Decodage
	$line = 0;
	// TRAITER LA LIGNE D'ENTETE
	$nbl=count($lines);
	if ($nbl>0){ // premiere ligne entete fichier csv
		// echo "<br />2378:: format/csv :: $line : ".$lines[$line]."\n";
		//"#Moodle Referentiel Students CSV Export;latin1;Y:2009m:06d:11\n"

        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        // DEBUG
        // echo "<br /> DEBUG :: 2325 <br />\n";
        // print_r($fields);

	  	$line++;
		if (substr($lines[$line],0,1)=='#'){
			// labels
	        /// If a line is incorrectly formatted
            if (count($fields) < 2 ) {
	           	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted - ignoring\n");
				}
           	}
			if (isset($fields[1]) && ($fields[1]!="")){
		        $this->table_caractere_input=trim($fields[1]);
			}
			if (isset($fields[2]) && ($fields[2]!="")){
                $date_creation=trim($fields[2]);
            }
		}
	}
	else{
		$this->error("ERROR : CSV File incorrect\n");
	}
	// echo "<br />DEBUG :: 2348 : $this->table_caractere_input\n";

	if ($nbl>1){ // deuxieme ligne : entete certificat
		// echo "<br />$line : ".$lines[$line]."\n";
		while ($line<$nbl){ // data : referentiel
			// #name;code_referentiel;description_referentiel;
			//
        	$fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        	/// If a line is incorrectly formatted
        	$nbfields= count($fields);
        	if ($nbfields < 3 ) {
           		if ( $nbfields > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    			}
			}
			else{
				if (substr($lines[$line],0,1)=='#'){
					// labels
    		    	$l_s0 = trim($fields[0]);
					if ($l_s0=="#name"){
						$l_name_ref = trim($fields[0]);
						$l_code_ref = trim($fields[1]);
		        		$l_desc_ref = trim($fields[2]);

						$in_referentiel=true;
						$in_certif=false;
					}
					else if ($l_s0=="#login"){
						// #name;code_referentiel;description_referentiel;
						$l_login = trim($fields[0]);
						$l_num_etudiant = trim($fields[1]);
						$l_nom = trim($fields[2]);
						$l_prenom = trim($fields[3]);
				        $l_decidion_jury = trim($fields[4]);


                        $i=5;
                        // On peut ameliorer la robustesse en recherchant si le code appartient au referentiel
                        while (($i<$nbfields) && (trim($fields[$i]) != 'promotion')){ // items
                            $t_items[]=trim($fields[$i]);
                            $i++;
                        }

                        $liste_codes_competences='';
                        for ($j=0; $j<count($t_items); $j++) {
                            if ($t_items[$j]){
                                $liste_codes_competences.=$t_items[$j].'/';
                            }
                        }
                        // DEBUG
                        // echo "<br />DEBUG :: 2395 Liste codes : '$liste_codes_competences'<br />\n";

						$in_referentiel=false;
						$in_certif=true;
					}
				}
				else{
					// data  :
		    		if ($in_referentiel==true){ // referentiel
						$name_ref = $this->input_codage_caractere(trim($fields[0]));
						$code_ref = $this->input_codage_caractere(trim($fields[1]));
				        $desc_ref = $this->input_codage_caractere(trim($fields[2]));

						// DEBUG
                        // echo "<br />DEBUG :: 2427 :: format/csv ::<br />\n";
                        // echo ($name_ref, $code_ref, $desc_ref);
					}
					elseif ($in_certif==true){ // etudiant
						$login = trim($fields[0]);
						$num_etudiant = $this->input_codage_caractere(trim($fields[1]));
						$nom = $this->input_codage_caractere(trim($fields[2]));
			        	$prenom = $this->input_codage_caractere(trim($fields[3]));
						$decision = $this->input_codage_caractere(trim($fields[4]));

                        $t_items_values=array(); // 0 : non valide / 1 : valide                        $i=5;
                        $i=5;
                        while (($i<$nbfields) && (($fields[$i]=='0') || ($fields[$i]=='1'))) { // items
                            if (($fields[$i]=='0') || ($fields[$i]=='1')){
                                $t_items_values[]=trim($fields[$i]);
                            }
                            $i++;
                        }
                        // echo "<br />T_ITEMS_VALUES<br />\n";
                        // print_r($t_items_values);
                        
                        $liste_competences='';
                        for ($i=0; $i<count($t_items_values); $i++) {
                            $liste_competences.=$t_items[$i].':'.$t_items_values[$i].'/';
                        }

						// rechercher l'id
						if ($login!=''){
                            $user_id=referentiel_get_userid_by_login($login);
                        }
                        if (!empty($user_id)){  // utilisateur inscrit

                            // Verifier referentiel
                            // DEBUG
                            // echo "<br />DEBUG :: 2432 CODE_REF : $code_ref<br />Liste competences : '$liste_competences'<br />\n";
                            // print_object($this->rreferentiel);

                            if (!empty($this->rreferentiel)
                                && ($this->rreferentiel->code_referentiel==$code_ref)
                                && ($this->rreferentiel->liste_codes_competence==$liste_codes_competences)){

                                /*
                                // this routine initialises the import object
				                $import_certif = new stdClass();
						        $import_certif->login=$login;
						        $import_certif->nom=$nom;
                                $import_certif->prenom=$prenom;
						        $import_certif->userid=$user_id;
						        $import_certif->decision=$decision;
                                $import_certif->competences_certificat=$liste_competences;
                                $import_certif->ref_referentiel = $this->rreferentiel->id;

                                // DEBUG
                                //echo "<br /> DEBUG ./format/csv :: 2478\n";
                                //print_object($import_certif);
                                */

                                // sauvegarde dans la base uniquement pour les certificats existants
                                if ($certificat=$DB->get_record('referentiel_certificat', array("userid"=>$user_id, "ref_referentiel"=>$this->rreferentiel->id))){
                                    $certificat->decision_jury=$decision;
                                    $certificat->verrou=1;
                                    $certificat->valide=1;
                                    // mise à jour simple de la table certificat
                                    $certificat->competences_certificat=$liste_competences;
                                    if (!$DB->update_record("referentiel_certificat", $certificat)){
                    					$this->error("ERROR update certificate");
                                    }
                                    // Creation d'une activité pour les nouvelles compétences
                                    if (!empty($this->import_activity)){
                                        $this->creer_activite_supplementaire($certificat, $liste_competences);
                                    }
                                }
						    }
					    }
					}
				}
			}
			$line++;
		}
	}
	return true;
}


    /**
     * parse the array of lines into an array
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array of student object
     */
	function read_import_certifs($lines) {
        // we just need it as one array
		return $this->import_certifs($lines);
    }
    
    
    // Creation d'une activité pour les nouvelles compétences
    function  creer_activite_supplementaire($certificat, $liste_competences){
    global $DB;
    global $USER;
    
        // calculer le delta
        $liste_competences_acquises='';
        // $t_values_old=array();
        $t_codes_new=array();
        $t_values_new=array();
/*
        $t_items_old=explode('/', $certificat->competences_certificat);
        for ($i=0; $i<count($t_items_old); $i++){
            if (!empty($t_items_old[$i])){
                // comparer
                $value_old=explode(':', $t_items_old[$i]);
                if (isset($value_old[1])){
                    $t_values_old[]=trim($value_old[1]);
                }
            }
        }
*/
        $t_items_new=explode('/', $liste_competences);
        for ($i=0; $i<count($t_items_new); $i++){
            if (!empty($t_items_new[$i])){
                // comparer
                $value_new=explode(':', $t_items_new[$i]);
                if (isset($value_new[0]) && isset($value_new[1])){
                    $t_codes_new[]=trim($value_new[0]);
                    $t_values_new[]=trim($value_new[1]);
                }
            }
        }
        for ($i=0;  $i<count($t_values_new); $i++){
            if ($t_values_new[$i]!='0'){
                $liste_competences_acquises.= $t_codes_new[$i].'/';
            }
        }

/*
        // comparer
        $ok_modif=false;
        if (count($t_values_new)==count($t_values_old)){
            for ($i=0;  $i<count($t_values_new); $i++){
                if ($t_values_new[$i]!='0'){
                    $liste_competences_acquises.=
                if ($t_values_old[$i]!=$t_values_new[$i]){
                    //
                    if ($t_values_new[$i]

                    $ok_modif=true;
                }
            }
        }
*/

        // creer une activité
        $import_activity = new stdClass();
        $import_activity->type_activite=get_string('imported_activity_type','referentiel');
        $import_activity->description_activite=get_string('imported_activity_description','referentiel');
        $import_activity->competences_activite=$liste_competences_acquises;
        $import_activity->commentaire_activite=get_string('imported_activity_comment','referentiel', date("Y/m/d H:i:s",time()));
        $import_activity->ref_instance=$this->ireferentiel->id;
        $import_activity->ref_referentiel=$this->ireferentiel->ref_referentiel;
        $import_activity->ref_course=$this->ireferentiel->course;
        $import_activity->userid=$certificat->userid;
        $import_activity->teacherid=$USER->id;
        $import_activity->date_creation=time();
        $import_activity->date_modif_student=time();
        $import_activity->date_modif=time();
        $import_activity->approved=1;
        $import_activity->ref_task=0;
        $import_activity->mailed=1;
        $import_activity->mailnow=0;

        // DEBUG
        // echo "<br /> DEBUG ./format/csv :: 2583\n";
        // print_object($import_activity);
    	$activite_id= $DB->insert_record("referentiel_activite", $import_activity);
        // DEBUG
    	// echo "ACTIVITE ID / $activite_id<br />";
        /*
        if 	(($activite_id>0) && ($import_activity->competences_activite!='')){
            // mise a jour du certificat
            // referentiel_mise_a_jour_competences_certificat_user('', $import_activity->competences_activite, $import_activity->userid, $activite->ref_referentiel, $import_activity->approved, true, false);
        }
        */
   }

}  // fin de la classe cformat


// ################################################################################################################
// ETUDIANTS : export des etudiants
class eformat_csv extends eformat_default {
	var $sep = ";";

	// ----------------
	function guillemets($texte){
		return '"'.trim($texte).'"';
	}


	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return $this->guillemets(str_replace($cherche, $remplace, $texte));
	}


	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1
	
	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}



    function provide_export() {
      return true;
    }

	function provide_import() {
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
		$xp =  "#Moodle Referentiel Students CSV Export;latin1;".referentiel_timestamp_date_special(time())."\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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


	function write_etablissement($record) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\netablissement: $record->id\n";
		if ($record){
			//$expout .= "#id_etablissement;num_etablissement;nom_etablissement;adresse_etablissement\n";
			$id = trim( $record->id );
			$num_etablissement = trim( $record->num_etablissement);
			$nom_etablissement = $this->output_codage_caractere($this->purge_sep($record->nom_etablissement));
			$adresse_etablissement = $this->output_codage_caractere($this->purge_sep($record->adresse_etablissement));
			$logo = trim( $record->logo_etablissement);
			
			$expout .= "$id;".stripslashes($this->output_codage_caractere($num_etablissement)).";$nom_etablissement;$adresse_etablissement\n";
        }
        return $expout;
    }


	function write_etudiant( $record ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\netudiant: $record->id  -->\n";
		if ($record){
			$id = trim( $record->id );
			$userid = trim( $record->userid );

            $ref_etablissement = trim( $record->ref_etablissement);
			$num_etudiant = trim( $record->num_etudiant);
			$ddn_etudiant = trim( $record->ddn_etudiant);
			$lieu_naissance = $this->output_codage_caractere($this->purge_sep($record->lieu_naissance));
			$departement_naissance = $this->output_codage_caractere($this->purge_sep($record->departement_naissance));
			$adresse_etudiant = $this->output_codage_caractere($this->purge_sep($record->adresse_etudiant));

			$login=trim(referentiel_get_user_login($record->userid ));

            if ($num_etudiant==$login){
                    $texte=$num_etudiant;
            }
            elseif ($num_etudiant==''){
                    $texte=$login;
            }
            else{
                    $texte=$num_etudiant;
            }

    		$expout .= "$id;$userid;$login;".$this->output_codage_caractere(referentiel_get_user_prenom($record->userid)).";".$this->output_codage_caractere(referentiel_get_user_nom($record->userid)).";".$this->output_codage_caractere($texte).";$ddn_etudiant;$lieu_naissance;$departement_naissance;$adresse_etudiant;$ref_etablissement\n";
/*
			// Etablissement
			$record_etablissement=referentiel_get_etablissement($record->ref_etablissement);
	    	if ($record_etablissement){
				$expout .= $this->write_etablissement( $record_etablissement );
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

    function write_liste_etudiants() {
    	global $CFG;
        // initial string;
        $expout = ""; 

		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = $this->output_codage_caractere(trim($this->ireferentiel->name));
            $description_instance = $this->output_codage_caractere($this->purge_sep($this->ireferentiel->description_instance));
            $label_domaine = $this->output_codage_caractere(trim($this->ireferentiel->label_domaine));
            $label_competence = $this->output_codage_caractere(trim($this->ireferentiel->label_competence));
            $label_item = $this->output_codage_caractere(trim($this->ireferentiel->label_item));
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;
			
//			$expout .= "Instance de referentiel : $this->ireferentiel->name\n";
//			$expout .= "id;name;description_instance;label_domaine;label_competence;label_item;date_instance;course;ref_referentiel;visible\n";
//			$expout .= "$id;$name;$description_instance;$label_domaine;$label_competence;$label_item;$date_instance;$course;$ref_referentiel;$visible\n";
			
			if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
				// ETUDIANTS
				$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
				if ($records_all_students){
				    $expout .= "#id_etudiant;user_id;login;Prenom;NOM;num_etudiant;ddn_etudiant;lieu_naissance;departement_naissance;adresse_etudiant;ref_etablissement\n";
				    foreach ($records_all_students as $record){
						  // USER
						  if (isset($record->userid) && ($record->userid>0)){
							  $record_etudiant = referentiel_get_etudiant_user($record->userid);
		    				if ($record_etudiant){
								  $expout .= $this->write_etudiant( $record_etudiant );
							  }
						  }
					  }
				}
			}
        }
        return $expout;
    }

    function write_liste_etablissements() {
    	global $CFG;
        // initial string;
        $expout = ""; 
		// ETABLISSEMENTS
		$records_all_etablissements = referentiel_get_etablissements();
		if ($records_all_etablissements){
			$expout .= "#id_etablissement;num_etablissement;nom_etablissement;adresse_etablissement\n";		
			foreach ($records_all_etablissements as $record){
				if ($record){
					$expout.=$this->write_etablissement($record);
				}
			}
        }
        return $expout;
    }

	// IMPORTATION
	/***************************************************************************
		
	// IMPORT FUNCTIONS START HERE
	
	***************************************************************************/

	
     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees 
     */
    function import_etablissements_etudiants( $lines ) {
	// recupere le tableau de lignes 
	// selon les parametres soit cree une nouvelle instance 
	// soit modifie une instance courante de students
	global $SESSION;
	global $USER;
	global $CFG;
	global $DB;
	
	// initialiser les variables	
	$date_creation="";
	$in_etablissement=false; // drapeau
	$in_etudiant=false;		// drapeau

    $t_id_etablissements=array(); // table de reaffectation des id d'etablissement

    // get some error strings
    $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
    $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
	$error_override = get_string( 'overriderisk', 'referentiel' );
	
	// DEBUT	
	// Decodage 
	$line = 0;
	// TRAITER LA LIGNE D'ENTETE
	$nbl=count($lines);
	if ($nbl>0){ // premiere ligne entete fichier csv
		// echo "<br />2378:: format/csv :: $line : ".$lines[$line]."\n";
		//"#Moodle Referentiel Students CSV Export;latin1;Y:2009m:06d:11\n"
		
        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
	  	$line++;
		if (substr($lines[$line],0,1)=='#'){
			// labels			
	        /// If a line is incorrectly formatted 
            if (count($fields) < 3 ) {
	           	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted - ignoring\n");
				}
           	}
			if (isset($fields[1]) && ($fields[1]!="")){
		        $this->table_caractere_input=trim($fields[1]);
			}
			$date_creation=trim($fields[2]);
		}
	}
	else{
		$this->error("ERROR : CSV File incorrect\n");
	}
	// echo "<br />DEBUG :: 2073 : $this->table_caractere_input\n";
	
	if ($nbl>1){ // deuxieme ligne : entete etablissment
		// echo "<br />$line : ".$lines[$line]."\n";
		while ($line<$nbl){ // data : referentiel		
			// #id_etablissement;num_etablissement;nom_etablissement;adresse_etablissement
			// 
        	$fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        	/// If a line is incorrectly formatted 
        	if (count($fields) < 3 ) {
           		if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    			}
			}
			else{
				if (substr($lines[$line],0,1)=='#'){
					// labels
    		    	$l_id = trim($fields[0]);
					if ($l_id=="#id_etablissement"){
						$l_id_etablissement = trim($fields[0]);
						$l_num_etablissement = trim($fields[1]);
		        		$l_nom_etablissement = trim($fields[2]);
						$l_adresse_etablissement = trim($fields[3]);
						if (isset($fields[4]))
							$l_logo_etablissement = trim($fields[4]);
						else
							$l_logo_etablissement = "";
						$in_etablissement=true;
						$in_etudiant=false;
					}
					else if ($l_id=="#id_etudiant"){
						// #id_etudiant;user_id;login;NOM_Prenom;num_etudiant;ddn_etudiant;lieu_naissance;departement_naissance;adresse_etudiant;ref_etablissement
						$l_id_etudiant = trim($fields[0]);
						$l_user_id = trim($fields[1]);
						$l_login = trim($fields[2]);
						$l_Prenom = trim($fields[3]);
				        $l_NOM = trim($fields[4]);
						$l_num_etudiant = trim($fields[5]);
						$l_ddn_etudiant = trim($fields[6]);
						$l_lieu_naissance = trim($fields[7]);
						$l_departement_naissance = trim($fields[8]);
						$l_adresse_etudiant = trim($fields[9]);
						$l_ref_etablissement = trim($fields[10]);
						
						$in_etablissement=false;
						$in_etudiant=true;
					}
				}
				else{
					// data  : 
		    		if ($in_etablissement==true){ // etablissement
						$id_etablissement = trim($fields[0]);
						$num_etablissement = $this->input_codage_caractere(trim($fields[1]));
				        $nom_etablissement = $this->input_codage_caractere(trim($fields[2]));
						$adresse_etablissement = $this->input_codage_caractere(trim($fields[3]));
						if (isset($fields[4]))
							$logo_etablissement = trim($fields[4]);
						else
							$logo_etablissement = "";
						
						// this routine initialises the import object
				        $import_etablissement = new stdClass();
						$import_etablissement->id=0;
						$import_etablissement->num_etablissement=$num_etablissement;
						$import_etablissement->nom_etablissement=str_replace("'", " ",$nom_etablissement);
						$import_etablissement->adresse_etablissement=str_replace("'", " ",$adresse_etablissement);
						$import_etablissement->logo_etablissement=$logo_etablissement;
						// sauvegarde dans la base
						
						if (!empty($num_etablissement)){
                            // rechercher
                            $etablissement_id=referentiel_get_id_etablissement($num_etablissement);
                        }
                        if (!empty($etablissement_id)){    // reindexer
                             $t_id_etablissements[$id_etablissement]=$etablissement_id;
                             $id_etablissement=$etablissement_id;
                        }
                        else{  // etablissement inconnu
                            $new_id_etablissement=$DB->insert_record("referentiel_etablissement", $import_etablissement);
                            $t_id_etablissements[$id_etablissement]=$new_id_etablissement;
                            $id_etablissement=$new_id_etablissement;
                        }

						if ($id_etablissement!=0){
							$import_etablissement->id=$id_etablissement;
							if (!$DB->update_record("referentiel_etablissement", $import_etablissement)){
								// DEBUG
								// echo "<br /> ERREUR UPDATE etablissement\n";
							}
						}
						// DEBUG
                        // echo "<br />DEBUG :: 2479 :: format/csv ::<br />\n";
                        // print_object($import_etablissement);
					}
					elseif ($in_etudiant==true){ // etudiant
						$id_etudiant = trim($fields[0]);
						$user_id = $this->input_codage_caractere(trim($fields[1]));
						$login = $this->input_codage_caractere(trim($fields[2]));
			        	$Prenom = $this->input_codage_caractere(trim($fields[3]));
						$NOM = $this->input_codage_caractere(trim($fields[4]));
						$num_etudiant = $this->input_codage_caractere(trim($fields[5]));
						$ddn_etudiant = trim($fields[6]);
						$lieu_naissance = $this->input_codage_caractere(trim($fields[7]));
						$departement_naissance = $this->input_codage_caractere(trim($fields[8]));
						$adresse_etudiant = $this->input_codage_caractere(trim($fields[9]));
						$ref_etablissement = trim($fields[10]);

						// rechercher l'id
						if ($login!=''){
                            $user_id=referentiel_get_userid_by_login($login);
                            if (!empty($user_id)){
                                $id_etudiant=referentiel_get_etudiant_id_by_userid($user_id);
                            }
                        }
                        else if (($user_id!='') && ($user_id>0)){
                            // rechercher l'id s'il existe
                            $id_etudiant=referentiel_get_etudiant_id_by_userid($user_id);
						}
                        if (!empty($user_id)){  // utilisateur inscrit

						  // this routine initialises the import object
				            $import_etudiant = new stdClass();
						    $import_etudiant->id=0;
						    $import_etudiant->num_etudiant=$num_etudiant;
						    $import_etudiant->adresse_etudiant=str_replace("'", " ",$adresse_etudiant);
						    $import_etudiant->ddn_etudiant = $ddn_etudiant ;
						    $import_etudiant->lieu_naissance =$lieu_naissance;
						    $import_etudiant->departement_naissance = $departement_naissance;
						    $import_etudiant->ref_etablissement = $t_id_etablissements[$ref_etablissement];
						    $import_etudiant->userid = $user_id;
						
						    // DEBUG
                            // echo "<br />DEBUG :: 2513 :: format/csv ::<br />\n";
                            // print_object($import_etudiant);
                        
						    // sauvegarde dans la base
						    if ($id_etudiant==0){
							    $new_etudiant_id=$DB->insert_record("referentiel_etudiant", $import_etudiant);
						    }
						    else{
							    $import_etudiant->id=$id_etudiant;
							    if (!$DB->update_record("referentiel_etudiant", $import_etudiant)){
								// DEBUG
								// echo "<br /> ERREUR UPDATE etudiant\n";
							    }
						    }
					    }
					}
				}
			}
			$line++;
		}
	}
	return true;
}


    /**
     * parse the array of lines into an array 
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array of student object
     */
	function read_import_students($lines) {
        // we just need it as one array
		return $this->import_etablissements_etudiants($lines);
    }

}

/** ******************************************

EXPORT TASKS

*/

// TASKS : export des taches
class tformat_csv extends tformat_default {

	var $sep = ";";
	
	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1

	// ----------------
	function guillemets($texte){
		return '"'.trim($texte).'"';
	}


	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return $this->guillemets(str_replace($cherche, $remplace, $texte));
	}

	
	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}



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
		$xp =  "Moodle Referentiel CSV Export\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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
        // insere un saut de ligne apres le 80 caractere 
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
     * Turns consigne into an xml segment
     * @param consigne object
     * @return string xml segment
     */

    function write_consigne( $consigne ) {
    global $CFG;
       // initial string;
        $expout = "";
		if ($consigne){
			$id_consigne = $consigne->id ;		
            $type_consigne = trim(substr($consigne->type_consigne,0, 20));
            $description_consigne = $this->purge_sep($consigne->description_consigne);
			$url_consigne = $consigne->url_consigne;
            $ref_task = $consigne->ref_task;
            $tiemstamp= $consigne->timestamp;
            $expout .= "$id_consigne;".stripslashes($this->output_codage_caractere($type_consigne)).";".stripslashes($this->output_codage_caractere($description_consigne)).";$url_consigne;$ref_task;$timestamp\n";
        }
        return $expout;
    }

    /**
     * Turns task into an csv segment
     * @param task object
     * @return string csv segment
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
			$id_task = $task->id;
            $type_task = trim($task->type_task);
			$description_task = $this->purge_sep($task->description_task);
            $competences_task = trim($task->competences_task);
            $criteres_evaluation = $this->purge_sep($task->criteres_evaluation);
            $ref_instance = $task->ref_instance;
            $ref_referentiel = $task->ref_referentiel;
            $ref_course = $task->ref_course;
			$auteurid = trim($task->auteurid);
			$date_creation = $task->date_creation;
			$date_modif = $task->date_modif;
			$date_debut = $task->date_debut;
			$date_fin = $task->date_fin;
			
			
			$expout .= "#id_task;type_task;description_task;competences_task;criteres_evaluation;ref_instance;ref_referentiel;ref_course;auteurid;date_creation;date_modif;date_debut;date_fin\n";
			$expout .= "$id_task;".stripslashes($this->output_codage_caractere($type_task)).";".stripslashes($this->output_codage_caractere($description_task)).";".stripslashes($this->output_codage_caractere($competences_task)).";".stripslashes($this->output_codage_caractere($criteres_evaluation)).";$ref_instance;$ref_referentiel;$ref_course;$auteurid;".referentiel_timestamp_date_special($date_creation).";".referentiel_timestamp_date_special($date_modif).";".referentiel_timestamp_date_special($date_debut).";".referentiel_timestamp_date_special($date_fin)."\n";
			
			// consigneS
			$records_consignes = referentiel_get_consignes($task->id);
			
			if ($records_consignes){
				$expout .= "#id_consigne;type_consigne;description_consigne;url_consigne;ref_task;timestamp\n";
				foreach ($records_consignes as $record_d){
					$expout .= $this->write_consigne( $record_d );
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
        // initial string;
        $expout = "";
    	// add header
		if ($this->rreferentiel){
            $name = $this->rreferentiel->name;
            $code_referentiel = $this->rreferentiel->code_referentiel;
			$mail_auteur_referentiel = $this->rreferentiel->mail_auteur_referentiel;
			$cle_referentiel = $this->rreferentiel->cle_referentiel;
			$pass_referentiel = $this->rreferentiel->pass_referentiel;
            $description_referentiel = $this->rreferentiel->description_referentiel;
            $url_referentiel = $this->rreferentiel->url_referentiel;
			$seuil_certificat = $this->rreferentiel->seuil_certificat;
			$timemodified = $this->rreferentiel->timemodified;			
			$nb_domaines = $this->rreferentiel->nb_domaines;
			$liste_codes_competence = $this->rreferentiel->liste_codes_competence;
			$liste_empreintes_competence = $this->rreferentiel->liste_empreintes_competence;
			$local = $this->rreferentiel->local;
			$logo_referentiel = $this->rreferentiel->logo_referentiel;

			// INFORMATION REDUITE
			$expout .= "#code_referentiel;nom_referentiel;description_referentiel;cle_referentiel;liste_codes_competences\n";
			$expout .= stripslashes($this->output_codage_caractere($code_referentiel)).";".stripslashes($this->output_codage_caractere($name)).";".$this->output_codage_caractere($description_referentiel).";$cle_referentiel;".stripslashes($this->output_codage_caractere($liste_codes_competence))."\n";			
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
		if ($this->rreferentiel){
			$expout .= $this->write_referentiel_reduit();
		}

		// 
		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = trim($this->ireferentiel->name);
            $description_instance = $this->purge_sep($this->ireferentiel->description_instance);
            $label_domaine = trim($this->ireferentiel->label_domaine);
            $label_competence = trim($this->ireferentiel->label_competence);
            $label_item = trim($this->ireferentiel->label_item);
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;
			
			/* INUTILE ICI
			// $expout .= "#Instance de referentiel : $this->ireferentiel->name\n";
			$expout .= "#id_instance;name;description_instance;label_domaine;label_competence;label_item;date_instance;course;ref_referentiel;visible\n";
			$expout .= "$id;".stripslashes($this->output_codage_caractere($name)).";".stripslashes($this->output_codage_caractere($description_instance)).";".stripslashes($this->output_codage_caractere($label_domaine)).";".stripslashes($this->output_codage_caractere($label_competence)).";".stripslashes($this->output_codage_caractere($label_item)).";".referentiel_timestamp_date_special($date_instance).";$course;$ref_referentiel;$visible\n";
			*/
			// tasks
			if (isset($this->ireferentiel->id) && ($this->ireferentiel->id>0)){
			  	$records_tasks = referentiel_get_tasks_instance($this->ireferentiel->id);
		    	if ($records_tasks){
					foreach ($records_tasks as $record_a){
						// DEBUG
						// print_r($record_a);
						// echo "<br />\n";
						$expout .= $this->write_task( $record_a );
					}
				}
			}
		}
    return $expout;
  }
  
// fin de la classe  
}


// ################################################################################################################
// pedagos : export des pedagos
class pformat_csv extends pformat_default {
	var $sep = ";";

	// ----------------
	function guillemets($texte){
        return '"'.trim($texte).'"';
        return trim($texte);
	}


	// ----------------
	function purge_sep($texte){
		$cherche= array('"',$this->sep,"\r\n", "\n", "\r");
		$remplace= array("''",",", " ", " ", " ");
		return str_replace($cherche, $remplace, $texte);
	}


	var $table_caractere_input='latin1'; // par defaut import latin1
	var $table_caractere_output='latin1'; // par defaut export latin1
	
	// ----------------
	function recode_latin1_vers_utf8($string) {
		return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}


	// ----------------
	function recode_utf8_vers_latin1($string) {
		return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
	}
	

	 /**
     * @param 
     * @return string recode latin1
	 * 
     */
    function input_codage_caractere($s){
		if (!isset($this->table_caractere_input) || ($this->table_caractere_input=="")){
			$this->table_caractere_input='latin1';
		}
		
		if ($this->table_caractere_input=='latin1'){
			$s=$this->recode_latin1_vers_utf8($s);
		}
		return $s;
	}
	
	 /**
     * @param 
     * @return string recode utf8
	 * 
     */
    function output_codage_caractere($s){
		if (!isset($this->table_caractere_output) || ($this->table_caractere_output=="")){
			$this->table_caractere_output='latin1';
		}
		
		if ($this->table_caractere_output=='latin1'){
			$s=$this->recode_utf8_vers_latin1($s);
		}
		return $s;
	}



    function provide_export() {
      return true;
    }

	function provide_import() {
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
		$xp =  "#Moodle Referentiel pedagos CSV Export;latin1;".referentiel_timestamp_date_special(time())."\n";
		$xp .= $content;
  		return $xp;
	}

	function export_file_extension() {
  		return ".csv";
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


	function write_pedago($record_asso, $record_pedago ) {
        // initial string;
        $expout = "";
        // add comment
        // $expout .= "\npedago: $record->id  -->\n";
		if ($record_asso && $record_pedago){
			$id = trim( $record_pedago->id );
			$userid = trim( $record_asso->userid );
			$username=referentiel_get_user_login($userid);
            $refrefid = trim( $record_asso->refrefid);
			$date_cloture = trim( $record_pedago->date_cloture);
			$promotion = $this->output_codage_caractere($this->purge_sep($record_pedago->promotion));
			$formation = $this->output_codage_caractere($this->purge_sep($record_pedago->formation));
			$pedagogie = $this->output_codage_caractere($this->purge_sep($record_pedago->pedagogie));
            $composante = $this->output_codage_caractere($this->purge_sep($record_pedago->composante));
			$num_groupe= $this->output_codage_caractere($this->purge_sep($record_pedago->num_groupe));

            $commentaire = $this->output_codage_caractere($this->purge_sep($record_pedago->commentaire));
    		$expout .= "$username;".$this->output_codage_caractere(referentiel_get_user_prenom($record_asso->userid)).";".$this->output_codage_caractere(referentiel_get_user_nom($record_asso->userid)).";$date_cloture;$promotion;$formation;$pedagogie;$composante;$num_groupe;$commentaire;".$this->output_codage_caractere($this->rreferentiel->code_referentiel).";\n";
        }
        return $expout;
    }

	 /**
     * Turns referentiel instance into an xml segment
     * @param referentiel instanceobject
     * @return string xml segment
     */

    function write_liste_pedagos() {
    	global $CFG;
        // initial string;
        $expout = ""; 

		if ($this->ireferentiel){
			$id = $this->ireferentiel->id;
            $name = $this->output_codage_caractere(trim($this->ireferentiel->name));
            $description_instance = $this->output_codage_caractere($this->purge_sep($this->ireferentiel->description_instance));
            $label_domaine = $this->output_codage_caractere(trim($this->ireferentiel->label_domaine));
            $label_competence = $this->output_codage_caractere(trim($this->ireferentiel->label_competence));
            $label_item = $this->output_codage_caractere(trim($this->ireferentiel->label_item));
            $date_instance = $this->ireferentiel->date_instance;
            $course = $this->ireferentiel->course;
            $ref_referentiel = $this->ireferentiel->ref_referentiel;
			$visible = $this->ireferentiel->visible;

			if (isset($this->ireferentiel->course) && ($this->ireferentiel->course>0)){
				// ETUDIANTS
				$records_all_students = referentiel_get_students_course($this->ireferentiel->course);
				if ($records_all_students){
				    $expout .= "#username;firstname;lastname;date_cloture;promotion;formation;pedagogie;composante;num_groupe;commentaire;referentiel\n";
				    foreach ($records_all_students as $record_user){
                        // USER
                        if (isset($record_user->userid) && ($record_user->userid>0)){
                            $record_assos=referentiel_get_a_user_pedago($record_user->userid, $this->ireferentiel->ref_referentiel);
                            if ($record_assos){
                                foreach($record_assos as $record_asso){
                                    $record_pedago = referentiel_get_pedagogie($record_asso->refpedago);
                                    if ($record_pedago){
                                        $expout .= $this->write_pedago($record_asso, $record_pedago);
                                    }
                                }
                            }
                        }
                    }
				}
			}
        }
        return $expout;
    }


	// IMPORTATION
	/***************************************************************************
		
	// IMPORT FUNCTIONS START HERE
	
	***************************************************************************/

     /**
	 * @param array referentiel array from xml tree
     * @return object import_referentiel object
	 * modifie la base de donnees 
     */
    function import_pedagogies( $lines ) {
	// recupere le tableau de lignes 
	// selon les parametres soit cree une nouvelle instance 
	// soit modifie une instance courante de la table referentiel_a_user_scol
	global $SESSION;
	global $USER;
	global $CFG;
	global $DB;
	
	// initialiser les variables	
	$date_creation="";
	$in_pedago=false;		// drapeau
		
    // get some error strings
    $error_noname = get_string( 'xmlimportnoname', 'referentiel' );
    $error_nocode = get_string( 'xmlimportnocode', 'referentiel' );
	$error_override = get_string( 'overriderisk', 'referentiel' );
	$error_message='';
	// DEBUT	
	// Decodage 
	$line = 0;
	// TRAITER LA LIGNE D'ENTETE
	$nbl=count($lines);
	if ($nbl>0){ // premiere ligne entete fichier csv
		// echo "<br />$line : ".$lines[$line]."\n";
		//"#Moodle Referentiel pedagos CSV Export;latin1;Y:2009m:06d:11\n"
		
        $fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
	  	$line++;
		if (substr($lines[$line],0,1)=='#'){
			// labels			
	        /// If a line is incorrectly formatted 
            if (count($fields) < 3 ) {
	           	if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted - ignoring\n");
				}
           	}
			if (isset($fields[1]) && ($fields[1]!="")){
		        $this->table_caractere_input=trim($fields[1]);
			}
			$date_creation=trim($fields[2]);
		}
	}
	else{
		$this->error("ERROR : CSV File incorrect\n");
	}
	
	if ($nbl>1){ // deuxieme ligne 
		// echo "<br />$line : ".$lines[$line]."\n";
		while ($line<$nbl){ // data : referentiel		
        	$fields = explode($this->sep, str_replace( "\r", "", $lines[$line] ) );
        	/// If a line is incorrectly formatted 
        	if (count($fields) < 3 ) {
           		if ( count($fields) > 1 or strlen($fields[0]) > 1) { // no error for blank lines
					$this->error("ERROR ".$lines[$line].": Line ".$line."incorrectly formatted");
    			}
			}
			else{
				if (substr($lines[$line],0,1)=='#'){
					// labels
                    // $id;$username;".$this->output_codage_caractere(referentiel_get_user_prenom($record->userid)).";".$this->output_codage_caractere(referentiel_get_user_nom($record->userid)).";$num_groupe;$date_cloture;$promotion;$formation;$pedagogie;$composante;$refrefid\n";
					
			        // #username;firstname;lastname;num_groupe;date_cloture;promotion;formation;pedagogie;composante;referentiel;
					 
                    $l_username = trim($fields[0]); 
                    $l_firstname= trim($fields[1]);
				    $l_lastname= trim($fields[2]);
					$l_date_cloture = trim($fields[3]);
                    $l_promotion = trim($fields[4]);
                    $l_formation = trim($fields[5]);
                    $l_pedagogie = trim($fields[6]);
                    $l_composante = trim($fields[7]);
                    if (!empty($fields[8])){
		        	    $l_num_groupe = trim($fields[8]);
		        	}
		        	else{
                        $l_num_groupe = '';
                    }
                    if (!empty($fields[9])){
                        $l_commentaire = trim($fields[9]);
                    }
                    else{
                        $l_commentaire = '';
                    }

                    if (!empty($fields[10])){
                        $l_referentiel = trim($fields[10]);
                    }
                    else{
                            $l_referentiel = '';
                    }
    			}
				else{
                    $login = $this->input_codage_caractere(trim($fields[0]));     // username
  			        $firstname= $this->input_codage_caractere(trim($fields[1]));
					$lastname = $this->input_codage_caractere(trim($fields[2]));
					if (!empty($fields[3])){
                        $date_cloture = $this->input_codage_caractere(trim($fields[3]));
                    }
                    else{
                        $date_cloture = '';
                    }
                    if (!empty($fields[4])){
                        $promotion = $this->input_codage_caractere(trim($fields[4]));
                    }
                    else{
                        $promotion = '';
                    }
                    if (!empty($fields[5])){
                        $formation = $this->input_codage_caractere(trim($fields[5]));
                    }
                    else{
                        $formation = '';
                    }
                    if (!empty($fields[6])){
                        $pedagogie = $this->input_codage_caractere(trim($fields[6]));
                    }
                    else{
                        $pedagogie = '';
                    }
                    if (!empty($fields[7])){
                        $composante = $this->input_codage_caractere(trim($fields[7]));
                    }
                    else{
                        $composante = '';
                    }
                    if (!empty($fields[8])){
                        $num_groupe = $this->input_codage_caractere(trim($fields[8]));
                    }
                    else{
                        $num_groupe ='';
                    }
                    if (!empty($fields[9])){
                        $commentaire = $this->input_codage_caractere(trim($fields[9]));
                    }
                    else{
                        $commentaire = '';
                    }
                    if (!empty($fields[10])){
                        $code_referentiel = $this->input_codage_caractere(trim($fields[10]));
                    }
                    else{
                        $code_referentiel ='';
                    }
                    // rechercher la formation
					if (!empty($formation) && !empty($pedagogie)  && !empty($composante)){
                        $import_pedago = new stdClass();
                        $import_pedago->promotion =addslashes($promotion);
                        $import_pedago->num_groupe = addslashes($num_groupe);
                        $import_pedago->date_cloture = $date_cloture;
						$import_pedago->formation =addslashes($formation);
						$import_pedago->pedagogie =addslashes($pedagogie);
						$import_pedago->composante =addslashes($composante);
                        $import_pedago->commentaire =addslashes($commentaire);

                        $userid=0;
                        if ($login!=''){
                            $userid=referentiel_get_userid_by_login($login);
                        }
                        if ($userid){ // this routine initialises the import object
                            $import_association = new stdClass();
                            $import_association->userid=$userid;
						    // $tref=referentiel_get_infos_from_code_ref($code_referentiel);     // probablement vide
						    $import_association->refrefid = $this->ref_referentiel;
                        }

                        // verification dans la base
                        $trouve_pedago=0;
                        $creerasso=0; // insertion association necessaire

                        if ($userid){
                            $rec_assos=referentiel_get_a_user_pedago($userid, $this->ref_referentiel);
                            if ($rec_assos){
                                // un enregistrement existe
                                $creerasso=1; // un enregistrement existe : update necessaire

                                foreach($rec_assos as $rec_asso){
                                    if ($rec_asso){
                                        $import_association->id=$rec_asso->id;
                                        $rec_pedago=referentiel_get_pedagogie($rec_asso->refpedago);
    						            if ($rec_pedago && !$trouve_pedago){    // sinon pas utile de chercher plus loin
                                            if (($rec_pedago->num_groupe == $num_groupe)
                                                //&&
                                                //($rec_pedago->date_cloture == $date_cloture)
                                                &&
                                                ($rec_pedago->promotion == $promotion)
                                                &&
                                                ($rec_pedago->formation == $formation)
                                                &&
                                                ($rec_pedago->pedagogie == $pedagogie)
                                                &&
                                                ($rec_pedago->composante == $composante)){ // deja connu
                                                $trouve_pedago=$rec_pedago->id;

                                                $creerasso=2;   // rien a faire pour l'association
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        else{
                             // MODIF JF 2013/05/23
                             // il faut probablement ajouter cet utilisateur à Moodle, mais comment faire ?
                             // A tout le moins signaler la difficulté
                             $error_message.='<br/>'.get_string('user_unknown','referentiel',$login.' '.$firstname.' '.$lastname);                      
                        }                        
                        
        				if (!$trouve_pedago){  // verifier si une pedagogie identique existe
                            $rec_pedago=referentiel_get_id_pedago_from_data($num_groupe, $date_cloture, $promotion, $formation, $pedagogie, $composante);
                            if ($rec_pedago){
                                $trouve_pedago=$rec_pedago->id;
                            }
						}
        				if (!$trouve_pedago){   // enregistrer  pedagogie
                            $trouve_pedago=$DB->insert_record("referentiel_pedagogie", $import_pedago);
                        }
                        if ($trouve_pedago){    // mettre a jour association si necessaire
                            // faut-il mettre quelque chose à jour ?
                            $rec_pedago=referentiel_get_pedagogie($trouve_pedago);

                            if (!empty($commentaire) && ($commentaire!=$rec_pedago->commentaire)){
                                    $rec_pedago->commentaire=$commentaire;
                                    referentiel_update_pedagogie_record($rec_pedago);
                            }
                            if (!empty($date_cloture) && ($date_cloture!=$rec_pedago->date_cloture)){
                                    $rec_pedago->date_cloture=$date_cloture;
                                    referentiel_update_pedagogie_record($rec_pedago);
                            }

                            if ($userid){
                                $import_association->refpedago = $trouve_pedago;
                                if ($creerasso==1){  // update
                                    $DB->update_record("referentiel_a_user_pedagogie", $import_association);
                                }
                                elseif ($creerasso==0){ // insertion
                                    $id_asso=$DB->insert_record("referentiel_a_user_pedagogie", $import_association);
                                }
                            }
                        }
					}
				}
			}
			$line++;
		}
    }
    if (!empty($error_message)){
       echo '<div class="error">';
       echo get_string('import_pedagogie_error','referentiel')."\n";
       echo $error_message."\n";
       echo '</div>'."\n";
    }
    return true;
    }

    /**
     * parse the array of lines into an array 
     * this *could* burn memory - but it won't happen that much
     * so fingers crossed!
     * @param array lines array of lines from the input file
     * @return array of pedago object
     */
	function read_import_pedagos($lines) {
        // we just need it as one array
		return $this->import_pedagogies($lines);
    }
    // ###############"" Fin de la classe pformat
}

// ##########################################################################################################

// ################################################################################################################
// archive : export des pedagos
class zformat_csv extends zformat_default {

    function provide_export() {
      return false;
    }

	function provide_import() {
        return false;
    }
}
// //////////////////////////////////////////////////////////////////////////////////////////////////////

?>
