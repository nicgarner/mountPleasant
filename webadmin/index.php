<?php

	include '../includes/connect.php'; 
	include '../includes/functions.php';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Mount Pleasant Baptist Church, Northampton</title>
            <meta name="Author" content="Mount Pleasant Baptist Church" />
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Jeff, Taylor, Paul, Lavender, service, services, Mount, Plesant, town, centre" />
            <meta name="Description" content="Mount Plesant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" />
		</head>

		<body>
        	
            <div class="site">
            
                <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
                
                <div class="menu"> <?php draw_menu(0); ?> </div>
            <div class="page">
            <p><br />This page has moved. Click <a href="../index.php?page=login">here</a> to go to the new page, or wait to be redirected.</p></div>
            
			<?php
echo '<meta http-equiv="refresh" content="3;url=../index.php?page=login">';

               	include('../includes/footer.php'); 
			?>

        
			</div>

</body>
</html>

<div id="addpagepopup" name="addpagepopup" class="popup">
								<h4>Help</h4><br />
                                <p><b>Page name</b><br>The name of the page as it should appear in links and the menu.</p>
                                <p><b>Section</b><br>The section of the site you want the new page to be included in.</p>
                                <p><b>Include in menu</b><br>Check this box if you want the page to have a link in the site's menu system.</p>
                                <p><b>Weight</b><br>The priority of the page in the menu. The higher the number, the higher the item will appear in the menu.</p>
                                <p><a href="#" onClick='document.getElementById("addpagepopup").style.display = "none"'>Close</a></p>
							</div>