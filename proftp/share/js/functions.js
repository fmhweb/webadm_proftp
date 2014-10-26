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
var imgArrowOut = new Image(); 
imgArrowOut.src = "images/arrow_out.png";

var dropDownOffsetTop = 12;
var dropDownOffsetLeft = 0;
var current_pagename = "home";
var prev_pagename = "home";
var current_details_id = "";
var current_details_pagename = "";
var current_details_tabname = "";
var currentTabPagename = "home";
var currentTabTabname = "";
var currentTabPage = 1;
var currentTabAction = 0;
var currentTabParams = 0;
var prevTabPagename = "home";
var prevTabTabname = "";
var prevTabPage = 1;
var prevTabAction = 0;
var prevTabParams = 0;
var watchTimeout;
var watchCommadCount = 0;
var watchCommadCountMax = 10;

function alterDateFilter(){
	var t = $('#dateend').val().split(/[- :]/);
	var dateBegin = new Date(t[0],t[1]-1,t[2],t[3],t[4],t[5]);
	var dateManip = $('#datemanip').val().split(" ");
	if(dateManip[1] == "hour" || dateManip[1] == "hours"){
		dateBegin.setHours(dateBegin.getHours() - dateManip[0]);
	}
	else if(dateManip[1] == "day" || dateManip[1] == "days"){
		dateBegin.setHours(dateBegin.getHours() - (dateManip[0] * 24));
	}
	else if(dateManip[1] == "week" || dateManip[1] == "weeks"){
		dateBegin.setHours(dateBegin.getHours() - (dateManip[0] * 24 * 7));
	}
	else if(dateManip[1] == "month" || dateManip[1] == "months"){
		dateBegin.setMonth(dateBegin.getMonth() - dateManip[0]);
	}
	else if(dateManip[1] == "year" || dateManip[1] == "years"){
		dateBegin.setYear(dateBegin.getYear() - dateManip[0] + 1900);
	}
	else{
		dateBegin.setHours(dateBegin.getHours() - 24);
	}

	var YYYY = dateBegin.getFullYear();
	var MM = ((dateBegin.getMonth() + 1 < 10) ? '0' : '') + (dateBegin.getMonth() + 1);
	var DD = ((dateBegin.getDate() < 10) ? '0' : '') + dateBegin.getDate();
	var HH = ((dateBegin.getHours() < 10) ? '0' : '') + dateBegin.getHours();
	var mm = ((dateBegin.getMinutes() < 10) ? '0' : '') + dateBegin.getMinutes();
	var ss = ((dateBegin.getSeconds() < 10) ? '0' : '') + dateBegin.getSeconds();

	$('#datebegin').val(YYYY+'-'+MM+'-'+DD+' '+HH+':'+mm+':'+ss);
	return;
}

function setDropDown(id,value,action){
	$('#'+id).val(value);
	hideDropDown();
	if(action == 1){
		alterDateFilter();
		$('#filtergo').click();
	}
	return;
}

function hideDropDown(){
	$('#drop').css({'display':'none','visibility':'hidden'});
	return;
}

function showDropDown(id){
	var elDrop = $('#drop');
	var elFilter = $('#'+id);
	var elFilterVals = $('#'+id+'vals');
	if(elDrop.css('display') == "none" && elFilterVals.html()){
		elDrop.html(elFilterVals.html());
		elDrop.width(elFilter.width());
		var offset = elFilter.offset();
		var topval = offset.top + elFilter.height() + dropDownOffsetTop - $(document).scrollTop();
		var leftval = offset.left;
		elDrop.css({'top':topval,'left':leftval});
		elDrop.css({'display':'block','visibility':'visible'});
	}
	else{
		elDrop.css({'display':'none','visibility':'hidden'});
	}
	return;
}

function evalFilter(e,pagename,tabname,page,action,params){
	if(e.keyCode == 13){
		showTab(pagename,tabname,page,action,params);
	}
	return;
}

function reloadTab(){
	showTab(currentTabPagename,currentTabTabname,currentTabPage,-1,currentTabParams);
	return;
}

function showPrevTab(){
	showTab(prevTabPagename,prevTabTabname,prevTabPage,-1,prevTabParams);
	return;
}

function showTab(pagename,tabname,page,action,params){
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
	if(pagename && tabname && page){
		if(action == 0){expandResult(0);}
		else if(action == -1){action = 0;}
		if(action == 0){
			prevTabPagename = currentTabPagename;
			prevTabTabname = currentTabTabname;
			prevTabPage = currentTabPage;
			prevTabAction = currentTabAction;
			prevTabParams = currentTabParams;

			currentTabPagename = pagename;
			currentTabTabname = tabname;
			currentTabPage = page;
			currentTabAction = action;
			currentTabParams = params;
		}

		elResultTab = document.getElementById('resulttab');
		formFilter = document.forms['formfilter'];
		query = "includes/show_"+pagename+"_"+tabname+".php";
		query_param = "pagename="+pagename+"&tabname="+tabname+"&page="+page+"&action="+action+"&params="+params;
		if(formFilter){
			for(i = 0; i < formFilter.length;i++){
				query_param += "&"+formFilter[i].name+"="+formFilter[i].value
			}
		}
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
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
				watchCommadCount = 0;
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

function remAcl(nameid,type,acl_path,level){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			response = xmlhttp.responseText;
                        setTimeout(function(){document.getElementById("resultsaveacl").innerHTML = response;},200);
			if(level > 1){reloadDetails('detailsacl');}
                }
        }
	if(nameid && acl_path){
	        query = "includes/save_acl.php";
        	query_param = "nameid="+nameid+"&type="+type+"&path="+acl_path+"&remove=1&level="+level;
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
	return;
}

function addKey(keyid,userid,remove,level){
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        response = xmlhttp.responseText;
                        setTimeout(function(){document.getElementById("resultsavekey").innerHTML = response;},200);
                        reloadDetails('detailskey');
                }
        }
        query = "includes/add_user_key.php";
        query_param = "keyid="+keyid+"&userid="+userid+"&level="+level+"&remove="+remove;
        xmlhttp.open("POST",query,true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(query_param);
        return;
}

function addGroupMember(groupid,userid,remove,level){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			response = xmlhttp.responseText;
                        setTimeout(function(){document.getElementById("resultsavemembers").innerHTML = response;},200);
                        reloadDetails('detailsmembers');
                }
        }
	query = "includes/add_group_member.php";
	query_param = "groupid="+groupid+"&userid="+userid+"&level="+level+"&remove="+remove;
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
        return;
}

function addAcl(id,nameid,namenew,type,acl_path){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			response = xmlhttp.responseText;
                        setTimeout(function(){document.getElementById("resultsaveacl").innerHTML = response;},200);
			if(namenew == 1){reloadDetails('detailsacl');}
                }
        }
	if(namenew == 1){
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
		read_acl = document.getElementById("read"+id).checked;
                write_acl = document.getElementById("write"+id).checked;
                delete_acl = document.getElementById("delete"+id).checked;
                create_acl = document.getElementById("create"+id).checked;
                modify_acl = document.getElementById("modify"+id).checked;
                move_acl = document.getElementById("move"+id).checked;
                view_acl = document.getElementById("view"+id).checked;
                navigate_acl = document.getElementById("navigate"+id).checked;
	}
	if(nameid && acl_path){
	        query = "includes/save_acl.php";
        	query_param = "nameid="+nameid+"&type="+type+"&path="+acl_path+"&read="+read_acl+"&write="+write_acl+"&delete="+delete_acl+"&create="+create_acl+"&modify="+modify_acl+"&move="+move_acl+"&view="+view_acl+"&navigate="+navigate_acl+"&remove=0&namenew="+namenew;
		xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send(query_param);
	}
	return;
}

function remItem(e,pagename,nameid,level){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(level > 1){reloadTab();}
                        elResultSave.innerHTML = xmlhttp.responseText;
                }
        }
	e.stopPropagation();
        elResultSave = document.getElementById("resultremove");
	query = "includes/save_"+pagename+".php";
        query_param = pagename+"["+pagename+"id]="+nameid+"&remove=1&level="+level;
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
			elResultSave.innerHTML = xmlhttp.responseText;
			reloadTab();
		}
	}
	elResultSave = document.getElementById("resultsave"+pagename);
	query = "includes/save_"+pagename+".php";
	query_param = "remove=0";
	formElement = document.forms["form"+pagename];
	for(i = 0; i < formElement.length;i++){
		query_param += "&"+formElement[i].name+"="+formElement[i].value
	}
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
        xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(query_param);
        return;
}

function reloadDetails(tabname){
	showDetails(current_details_id,current_details_pagename,tabname);
	return;
}

function showDetails(id,pagename,tabname){
	expandResult(1);
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
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
		current_details_tabname = tabname;
		elResultRight = document.getElementById("resultright");
		query = "includes/details_"+pagename+".php";
		query_param = "id="+id+"&tabname="+tabname;
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

function showFolderElement(id){
	$('#detailsfolder').css({'display':'none','visibility':'hidden'});
	$('#detailsacl').css({'display':'none','visibility':'hidden'});
	$('#'+id).css({'display':'block','visibility':'visible'});
}

function showUserElement(id){
	$('#detailsuser').css({'display':'none','visibility':'hidden'});
	$('#detailsquota').css({'display':'none','visibility':'hidden'});
	$('#detailsacl').css({'display':'none','visibility':'hidden'});
	$('#detailsmembers').css({'display':'none','visibility':'hidden'});
	$('#detailskey').css({'display':'none','visibility':'hidden'});
	$('#'+id).css({'display':'block','visibility':'visible'});
}

function showGroupElement(id){
	$('#detailsgroup').css({'display':'none','visibility':'hidden'});
	$('#detailsmembers').css({'display':'none','visibility':'hidden'});
	$('#detailsacl').css({'display':'none','visibility':'hidden'});
	$('#'+id).css({'display':'block','visibility':'visible'});
}

var prvFolderVal = new Array();

function rescanFolder(id){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        if(xmlhttp.responseText > 0){
                                watchCommand(id,xmlhttp.responseText);
                        }
                }
        }
	query = "includes/submit_command.php";
	query_param = "action=3";
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
	return;
}

function addFolder(e,id,parent_path,name){
	if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
	xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText > 0){
				watchCommand(id,xmlhttp.responseText);
			}
                }
        }
	if(e.keyCode == 13){
		if(parent_path && name){
			query = "includes/submit_command.php";
			query_param = "action=1&path="+parent_path+"/"+name;
			xmlhttp.open("POST",query,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(query_param);
		}
	}
        return;
}

function remFolder(id,path,layer){
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
                        if(xmlhttp.responseText > 0){
                                watchCommand(id,xmlhttp.responseText);
                        }
                }
        }
	if(id,path){
		if(layer == 1){
			$('#'+id).html("Delete folder: <img src=\"images/accept.png\" onclick=\"remFolder('"+id+"','"+path+"',2);\" /><img src=\"images/cross.png\" onclick=\"remFolder('"+id+"','"+path+"',3);\" />");
		}
		else if(layer == 2){
			query = "includes/submit_command.php";
			query_param = "action=2&path="+path;
			xmlhttp.open("POST",query,true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(query_param);
		}
		else if(layer == 3){
			$('#'+id).html("<img src=\"images/cross.png\" />Operation cancelled");
		}
	}
        return;
}

function showSubFolder(id,name,action,params){
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			prvFolderVal[id] = elIl.innerHTML;
                        elIl.innerHTML += xmlhttp.responseText;
                }
        }
        elIl = document.getElementById("folder"+id);
        elFolderImg = document.getElementById("folderimg"+id);
	if(elIl.title == "Expand"){
		elFolderImg.src = imgMinus.src;
		elIl.title = "Close";
        	query = "includes/show_folder_tree.php";
		query_param = "id="+id+"&action="+action+"&params="+params;
	        xmlhttp.open("POST",query,true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	        xmlhttp.send(query_param);
	}
	else{
		elFolderImg.src = imgPlus.src;
		elIl.title = "Expand";
		elIl.innerHTML = prvFolderVal[id];
	}
        return;
}
