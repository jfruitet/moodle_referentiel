// // JavaScript Document

// Pour tester ajax et pagination
// Inspiré de la classe  ajaxPagingManager.class.php de CK sur developper.com
// Architecture
// Le script principal installe des appels Ajax à un script secondaire
// qui fait des requêtes limitées sur la base de données
// Utilise une classe ajax.js
// Attention de bien respecter l'id des différents balises <div>
// $pagename     The secondary php file path called by Ajax call
// $div          The div name where result will be displayed
// $pageNo Current Pageno
// $instanceid   referentiel instance id
// $sql          SQL request called by Ajax call
// $totalPage   The number of pages
// $perPage      The number of records per page
// selacc        accompagnement (0: no, 1: yes)
// $modeaff      mode to display (listactivityall==1)

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

    function urldecode(str) {
		if (typeof str != "string") {
			return str;
		}
		return decodeURIComponent(str.replace(/\+/g, ' '));
	}



    // dessine la barre de selection des pages
	function redraw(pagename, pageNo, instanceid, sql, lparams, div, totalPage, perPage, selacc, modeaff, userid, order, pagination)
	{
        var i;
        var pagingstr;

        pagingstr='';
        sql=addslashes(sql);

        for (i = 1; i <= totalPage; i++)
		{
            if (i != pageNo)
			{
                pagingstr = pagingstr + ' <a href="javascript:ajaxPaging(\''+pagename+'\',pageNo=\''+i+'\',instanceid=\''+instanceid+'\',sql=\''+sql+'\',lparams=\''+lparams+'\',div=\''+div+'\',totalPage=\''+totalPage+'\',perPage=\''+perPage+'\',selacc=\''+selacc+'\',modeaff=\''+modeaff+'\',userid=\''+userid+'\',order=\''+order+'\',pagination=\''+pagination+'\')">'+i+'</a> ';
                //alert(i);
			}
			else
			{
                pagingstr = pagingstr + ' <span class="current">'+i+'</span>';
			}
		}
        var elem = document.getElementById('loadin');
		if(typeof elem   !== 'undefined' && elem !== null) {
			document.getElementById('loadin').innerHTML='';
		}
        document.getElementById('pagin').innerHTML=pagingstr;
    }

    // Ajax call  ajax.js
    function createXHR()
    {
		var request = false;
        try {
            request = new ActiveXObject('Msxml2.XMLHTTP');
        }
        catch (err2) {
            try {
                request = new ActiveXObject('Microsoft.XMLHTTP');
            }
            catch (err3) {
				try {
					request = new XMLHttpRequest();
				}
				catch (err1){
					request = false;
				}
            }
        }
        return request;
    }

    // lance l'appel au script des requetes par Ajax
	function ajaxPaging(pagename, pageNo, instanceid, sql, lparams, div, totalPage, perPage, selacc, modeaff, userid, order, pagination)
	{
	// alert(sql);
        var url= urldecode(pagename);
        var xhr=createXHR();
        //xhr.open("GET",pagename+'?pageNo='+pageNo+'&perPage='+perPage+'&instanceid='+instanceid+'&sql='+sql+'&lparams='+lparams+'&selacc='+selacc+'&modeaff='+modeaff, true);
        xhr.open("GET",url+'?pageNo='+pageNo+'&perPage='+perPage+'&instanceid='+instanceid+'&sql='+sql+'&lparams='+lparams+'&selacc='+selacc+'&modeaff='+modeaff+'&userid='+userid+'&order='+order+'&pagination='+pagination, true);

		//document.getElementById(div).innerHTML="Started...";
        document.getElementById(div).innerHTML='Started... <img src="ajax-loader.gif" id="loader">';

		xhr.onreadystatechange=function()
		{
			document.getElementById(div).innerHTML='Wait server... <img src="ajax-loader.gif" id="loader">';

			if(xhr.readyState == 4)
			{
				document.getElementById(div).innerHTML= xhr.responseText;
			}
		};

		xhr.send(null);

		redraw(pagename, pageNo, instanceid, sql, lparams, div, totalPage, perPage, selacc, modeaff, userid, order, pagination);

	}
