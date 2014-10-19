var prvFolderVal = new Array();

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

function showSubFolder(id,name,action){
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
	showDebug("OPEN"+name);
	if(elIl.title == "Expand"){
		elFolderImg.src = imgMinus.src;
		elIl.title = "Close";
        	query = "includes/show_folder_tree.php";
		query_param = "id="+id+"&action="+action;
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
