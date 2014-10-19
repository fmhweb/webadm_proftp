<?php
	function showPageIndex($pagename,$tabname,$page,$pages,$action){
		if($pages > 1){
			echo "<div align=\"center\">\n";
			if($page == 1){$class = "pageindexactive";}
			else{$class = "pageindex";}
			echo "<span class=\"$class\" onclick=\"showTab('$pagename','$tabname','1','$action');\">1</span>\n";
			$start_page = $page - 2;
			$max_page = $page + 2;
			if($start_page > $pages - 3){$start_page = $pages - 4;}
			elseif($page == 1){$start_page = $page + 1;}
			elseif($page == 2){$start_page = $page;}
			elseif($page == 3){$start_page = $page - 1;}
			if($max_page > $pages){$max_page = $pages - 1;}
			if($page > 2){echo "...";}
	                for($i = $start_page; $i <= $max_page; $i++){
				if($i == $page){$class = "pageindexactive";}
				else{$class = "pageindex";}
                	        echo "<span class=\"$class\" onclick=\"showTab('$pagename','$tabname','$i','$action');\">$i</span>\n";
        	        }
			if($max_page < ($pages - 1)){echo "...";}
			if($page == $pages){$class = "pageindexactive";}
			else{$class = "pageindex";}
			echo "<span class=\"$class\" onclick=\"showTab('$pagename','$tabname','$pages','$action');\">$pages</span>\n";
	                echo "</div>\n";
		}
        }
?>
