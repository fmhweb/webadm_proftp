var imgLoading = new Image(); 
imgLoading.src = "images/loading.gif";
var imgAccept = new Image(); 
imgAccept.src = "images/accept.png";
var imgCross = new Image(); 
imgCross.src = "images/cross.png";
var imgDelete = new Image(); 
imgDelete.src = "images/delete.png";
var imgDisk = new Image(); 
imgDisk.src = "images/disk.png";
var imgFolder = new Image(); 
imgFolder.src = "images/folder.png";
var imgGroup = new Image(); 
imgGroup.src = "images/group.png";
var imgMinus = new Image(); 
imgMinus.src = "images/minus.gif";
var imgPencil = new Image(); 
imgPencil.src = "images/pencil.png";
var imgPlus = new Image(); 
imgPlus.src = "images/plus.gif";
var imgUser = new Image(); 
imgUser.src = "images/user.png";
var imgArrowRefresh = new Image(); 
imgArrowRefresh.src = "images/arrow_refresh.png";

var current_pagename = "home";
var prev_pagename = "home";
var current_details_id = "";
var current_details_pagename = "";
var current_acl_path = "";
var watchTimeout;
var watchCommadCount = 0;
var watchCommadCountMax = 10;

function showTab(pagename,tabname,page,action){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			elResultTab.innerHTML = xmlhttp.responseText;
			scripts = elResultTab.getElementsByTagName("script");
			for(var i=0; i<scripts.length; i++){
				eval(scripts[i].innerHTML);
			}
                }
        }
	elResultTab = document.getElementById('resulttab');
	formFilter = document.forms['formfilter'];
	query = "includes/show_"+pagename+"_"+tabname+".php";
	query_param = "pagename="+pagename+"&tabname="+tabname+"&page="+page+"&action="+action;
	if(formFilter){
		for(i = 0; i < formFilter.length;i++){
			query_param += "&"+formFilter[i].name+"="+formFilter[i].value
		}
	}
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
	return;
}

function watchCommand(id,command_id){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText){
	                        el.innerHTML = xmlhttp.responseText;
			}
			else{
				watchTimeout = setTimeout(function(){watchCommand(id,command_id)}, 2000);
			}
                }
        }
	if(id && command_id){
		watchCommadCount++;
		if(watchCommadCount < watchCommadCountMax){
			clearTimeout(watchTimeout);
			el = document.getElementById(id);
			el.innerHTML = "<img src=\""+imgLoading.src+"\" /> Waiting for command to complete ("+watchCommadCount+"/"+watchCommadCountMax+")";
			query = "includes/submit_command.php";
	                query_param = "action=0&id="+command_id;
                	xmlhttp.open("POST",query,true);
        	        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	                xmlhttp.send(query_param);
		}
		else{
			el.innerHTML = "<div class=\"error\">Command timed out - Is the daemon running?</div>";
			watchCommadCount = 0;
		}
	}
	return;
}

function calcBytes(bytes,id){
	returnval = bytes+"B";
	result = Math.floor(bytes / 1024);
	if(result > 0){
		returnval = result+"KB";
		result = Math.floor(result / 1024);
		if(result > 0){
			returnval = result+"MB";
			result = Math.floor(result / 1024);
			if(result > 0){
				returnval = result+"GB";
			}
		}
	}
	document.getElementById(id).innerHTML = bytes+"B = "+returnval;
	return;
}

function remAcl(userid,acl_path,level){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			elResultSave.innerHTML = xmlhttp.responseText;
			if(level > 1){reloadDetails();}
                }
        }
	if(userid && acl_path){
        	elResultSave = document.getElementById("resultsaveacl");
	        query = "includes/save_acl.php";
        	query_param = "userid="+userid+"&path="+acl_path+"&remove=1&level="+level;
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
	return;
}

function addAcl(userid,usernew,acl_path){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			elResultSave.innerHTML = xmlhttp.responseText;
			if(usernew == 1){reloadDetails();}
                }
        }
	if(usernew == 1){
		read_acl = "false";
		write_acl = "false";
		delete_acl = "false";
		create_acl = "false";
		modify_acl = "false";
		move_acl = "false";
		view_acl = "false";
		navigate_acl = "false";
	}
	else{
		read_acl = document.getElementById("read"+userid).checked;
                write_acl = document.getElementById("write"+userid).checked;
                delete_acl = document.getElementById("delete"+userid).checked;
                create_acl = document.getElementById("create"+userid).checked;
                modify_acl = document.getElementById("modify"+userid).checked;
                move_acl = document.getElementById("move"+userid).checked;
                view_acl = document.getElementById("view"+userid).checked;
                navigate_acl = document.getElementById("navigate"+userid).checked;
	}
	if(userid && acl_path){
        	elResultSave = document.getElementById("resultsaveacl");
	        query = "includes/save_acl.php";
        	query_param = "userid="+userid+"&path="+acl_path+"&read="+read_acl+"&write="+write_acl+"&delete="+delete_acl+"&create="+create_acl+"&modify="+modify_acl+"&move="+move_acl+"&view="+view_acl+"&navigate="+navigate_acl+"&remove=0";
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
	return;
}

function showDebug(str){
	elDebug = document.getElementById("debug");
	//elDebug.innerHTML = str+"<br>"+elDebug.innerHTML;
	return;
}

function remUser(userid,level){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        if(xmlhttp.responseText == "1"){reloadPage();}
                        else{elResultSave.innerHTML = xmlhttp.responseText;}
                }
        }
        elResultSave = document.getElementById("resultremove");
	query = "includes/save_user.php";
        query_param = "user[userid]="+userid+"&remove=1&level="+level;
	xmlhttp.open("POST",query,true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(query_param);
        return;
}

function saveItem(pagename){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText == "1"){reloadPage();}
			else{elResultSave.innerHTML = xmlhttp.responseText;}
		}
	}
	elResultSave = document.getElementById("resultsave"+pagename);
	query = "includes/save_"+pagename+".php";
	query_param = "remove=0";
	formElement = document.forms["form"+pagename];
	for(i = 0; i < formElement.length;i++){
		query_param += "&"+formElement[i].name+"="+formElement[i].value
	}
	showDebug("Query: "+query+" - Params: "+query_param);
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
	return;
}

function showPrevPage(){
	showPage(prev_pagename);
	return;
}

function reloadPage(){
	showPage(current_pagename);
	return;
}

function showPage(pagename){
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        elResultLeft.innerHTML = xmlhttp.responseText;
			scripts = elResultLeft.getElementsByTagName("script");
			for(var i=0; i<scripts.length; i++){
				eval(scripts[i].innerHTML);
			}
			//showHelp(pagename);
                }
        }
	if(current_pagename != pagename){
		expandResult(0);
	}
	prev_pagename = current_pagename;
	current_pagename = pagename;
	elResultLeft = document.getElementById("resultleft");
	query = "includes/show_"+pagename+".php";
	query_param = "";
	showDebug("Query: "+query+" - Params: "+query_param);
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
	return;
}

function showHelp(pagename){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        elResultRight.innerHTML = xmlhttp.responseText;
                }
        }
	elResultRight = document.getElementById("resultright");
        query = "includes/help.php";
	query_param = "pagename="+pagename;
	showDebug("Query: "+query+" - Params: "+query_param);
        xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(query_param);
        return;
}

function reloadDetails(){
	showDetails(current_details_id,current_details_pagename);
	return;
}

function showDetails(id,pagename){
	expandResult(1);
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			showDebug(xmlhttp.responseText);
                        elResultRight.innerHTML = xmlhttp.responseText;
			scripts = elResultRight.getElementsByTagName("script");
			for(var i=0; i<scripts.length; i++){
				eval(scripts[i].innerHTML);
			}
                }
        }
	if(id && pagename){
		current_details_id = id;
		current_details_pagename = pagename;
		elResultRight = document.getElementById("resultright");
		query = "includes/details_"+pagename+".php";
		query_param = "id="+id;
		showDebug("Query: "+query+" - Params: "+query_param);
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
	return;
}

function expandResult(direction){
	elResultLeft = document.getElementById("resultleft");
	elResultRight = document.getElementById("resultright");
	if(direction == 1){
		elResultLeft.style.width = "50%";
		elResultRight.style.width = "50%";
		elResultRight.style.display = "table-cell";
		elResultRight.style.visibility = "visible";
	}
	else if(direction == 0){
		elResultRight.innerHTML = "";
		elResultLeft.style.width = "100%";
		elResultRight.style.width = "0%";
		elResultRight.style.display = "none";
		elResultRight.style.visibility = "hidden";
	}
	return;
}

function showElement(id){
	el = document.getElementById(id);
	if(el.style.visibility == "visible" || !el.style.visibility){
		el.style.display = "none";
		el.style.visibility = "hidden";
	}
	else{
		el.style.display = "block";
		el.style.visibility = "visible";
	}
}








