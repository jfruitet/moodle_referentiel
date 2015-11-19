<?php
/* ************************  RECODAGES de tables de caracteres *************************/
// require_once($CFG->libdir .'/core_text.class.php'); // pour utiliser $core_text

// FONCTIONS ===================================================================

// -------------------------
function CleanFiles($dir, $ext)
{
	// $ext ='.doc'
    //Efface les fichiers temporaires de plus de $delai secondes dont le nom contient tmp
    $t=time();
    $h=opendir($dir);
    while($file=readdir($h))
    {
        if(substr($file,0,3)=='tmp' and substr($file,-4)==$ext)
        {
            $path=$dir.'/'.$file;
			// DEBUG
			// echo "<br /> $path";			
            if ($t-filemtime($path)>120)
                @unlink($path);
        }
    }
    closedir($h);
}  




/// Select encoding
if (function_exists('current_charset')){
    $encoding = current_charset();
}

/// Select direction
/*
if ( get_string('thisdirection') == 'rtl' ) {
	$direction = ' dir="rtl"';
}
else {
	$direction = ' dir="ltr"';
}
*/
/// Loading the core_text singleton instance. We are going to need it.
/// pour les fonctions strpos(), substr() et autres conversions de chaines UTF8
// $core_text = core_text_get_instance();


/* ************************  RECODAGES de tables de caracteres ************************/
// seule table de caracteres acceptee par fpdf.php est le latin1 : ISO-8859-1


// ----------------
function recode_latin1_vers_utf8($string) {
     return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}


// ----------------
function recode_utf8_vers_latin1($string) {
     return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}

// ----------------
function recode_chaine_vers_html($string){
	return mb_convert_encoding($string, 'HTML-ENTITIES', mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
}

// ----------------
function recode_nom_fichier_latin1($fileName){
	return strtr(mb_convert_encoding($fileName,'ASCII', mb_detect_encoding($fileName, "UTF-8, ISO-8859-1, ISO-8859-15", true)),
    ' ,;:?*#!�$%&/(){}<>=`�|\\\'"',
    '____________________________');
}



// ----------------
function recode_html_vers_latin1($s0){
// retourne un nom recode
$s="";
    if (is_string($s0) && preg("/&/", $s0)){
    // �����
    // ëçöùñ
                    $s0=preg_replace("/&eacute;/", "�", $s0 );
                    $s0=preg_replace("/&egrave;/", "�", $s0 );
                    $s0=preg_replace("/&ecirc;/", "�", $s0 );
                    $s0=preg_replace("/&euml;/", "�", $s0 );
                    $s0=preg_replace("/&iuml;/", "�", $s0 );
                    $s0=preg_replace("/&icirc;/", "�", $s0 );
                    $s0=preg_replace("/&agrave;/", "�", $s0);
					$s0=preg_replace("/&acirc;/", "�", $s0);
                    $s0=preg_replace("/&ocirc;/", "�", $s0);
                    $s0=preg_replace("/&oulm;/", "�", $s0);
                    $s0=preg_replace("/&acirc;/", "�", $s0);
                    $s0=preg_replace("/&ccedil;/", "�", $s0);
                    $s0=preg_replace("/&ugrave;/", "�", $s0);
                    $s0=preg_replace("/&ntilde;/", "�", $s0);
                    $s0=preg_replace("/&deg;/","�",  $s0);
					$s0=preg_replace("/&oelig;/", "�", $s0);
					$s0=preg_replace("/&Ecirc;/", "�", $s0);
    }
return $s0;
}

// -----------------
function recode_nom_latin1_html($s0){
// retourne un nom d'url acceptable non accentue
// input : latin1
// output : html 
$s="";
    if (is_string($s0)){
	    for ($i=0; $i<strlen($s0); $i++){
            if (isset($s0[$i])){
                $c=$s0[$i];
                if (  ($c=="'") || ($c=="\\") || ($c=="\r") || ($c=="=")  || ($c=="{")  || ($c=="}")  || ($c=="[")  
                    || ($c=="]")  || ($c=="(")  || ($c==")")  ||  ($c=="'") || ($c==",")  || ($c==":")  || ($c=="!")  || ($c=="?")  || ($c==";")  || ($c==".")  ||  ($c=="'")  || ($c=='-')    || ($c=='_')   || ($c=='/')   || ($c=='+')   || ($c=='*') || ($c=='"') || ($c==' ') 
                    || (($c>='0') && ($c<='9')) || (($c>='A') && ($c<='Z'))  || (($c>='a') && ($c<='z'))){
                    $s.=$c;
                }
                else {
                    switch($c) {
                    case '�' :  $s.='&agrave;'; break;
					case '�' :  $s.='&acirc;'; break;
                    case '�' :  $s.='&auml;'; break; 
                    case '�' :  $s.='&acirc;'; break; 
                    case '�' :  $s.='&eacute;'; break; 
                    case '�' :  $s.='&egrave;'; break; 
                    case '�' :  $s.='&ecirc;'; break; 
                    case '�' :  $s.='&euml;'; break; 
                    case '�' :  $s.='&iuml;'; break; 
                    case '�' :  $s.='&icirc;'; break; 
                    case '�' :  $s.='&ouml;'; break; 
                    case '�' :  $s.='&ocirc;'; break; 
                    case '�' :  $s.='&otilde';  break;
                    case '�' :  $s.='&uuml;'; break; 
                    case '�' :  $s.='&ucirc;'; break; 
                    case '�' :  $s.='&ugrave;'; break; 
                    case '�' :  $s.='&ccedil;'; break; 
                    case '�' :  $s.='&ntilde;'; break;
					case '�' :  $s.='&oelig;'; break;
					case '�' :  $s.='&Ecirc;'; break;
                      default :
                    $s.='_';
                    break;   
                    } 
                }
            }
        }
    }    
return $s;
}



?>