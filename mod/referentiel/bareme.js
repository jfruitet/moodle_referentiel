// // JavaScript Document



    // ajoute '\' à l'expression
    function addslashes(str) {
        str=str.replace(/\\/g,'\\\\');
        str=str.replace(/\'/g,'\\\'');
        str=str.replace(/\"/g,'\\"');
        str=str.replace(/\0/g,'\\0');
        return str;
    }

    // retire '\'' à l'expression
    function stripslashes(str) {
        str=str.replace(/\\'/g,'\'');
        str=str.replace(/\\"/g,'"');
        str=str.replace(/\\0/g,'\0');
        str=str.replace(/\\\\/g,'\\');
        return str;
    }

    // retire '\'' à l'expression
    function sdecode(str) {
        str=str.replace(/!/g,'"');
        str=str.replace(/#/g,"'");
        return str;
    }

    // affiche le code sbareme
	function activerBareme(str, div)
	{
        // sbareme  = stripslashes(sbareme);
        //alert (sbareme );
        str  = sdecode(str);
		//alert (sbareme );
        var elem = document.getElementById(div);
		if(typeof elem   !== 'undefined' && elem !== null) {
			document.getElementById(div).innerHTML=str;
		}
    }
