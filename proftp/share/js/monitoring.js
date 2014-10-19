function getPieChart(id,action,value){
        if (window.XMLHttpRequest){
                xmlhttp = new XMLHttpRequest();
        }
        else{
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function(){
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			result = JSON.parse(xmlhttp.responseText);
			myPieChart = new Chart(ctx).Pie(result,{
				legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
			});
			//document.getElementById(id+"legend").innerHTML = myPieChart.generateLegend();
                }
        }
	ctx = document.getElementById(id).getContext("2d");
	query = "includes/chart_values.php";
	query_param = "action="+action+"&value="+value;
	xmlhttp.open("POST",query,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(query_param);
        return;
}
