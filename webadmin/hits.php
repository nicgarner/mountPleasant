<?php
	
	$id = $_GET['id'];
	
	include '../includes/connect.php'; 
	include '../includes/functions.php'; 
	
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		$uid = $_COOKIE['cookie:mpbcadmin'];
		$query = "SELECT name FROM users WHERE user_id = $uid";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		
		while ($row = mysql_fetch_assoc($result)) {
			$username = $row['name'];
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Mount Pleasant Baptist Church, Northampton</title>
            <meta name="Author" content="Mount Pleasant Baptist Church" />
            <meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, Jesus, Holy, Spirit, Kettering Road, Jeff, Taylor, Paul, Lavender, service, services, Mount, Plesant, town, centre" />
            <meta name="Description" content="Mount Plesant Baptist Church, 147 Kettering Road, Northampton" />
            <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
            <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
            <link rel="stylesheet" media="all" type="text/css" href="../site.css" />

		</head>

		<body>
        
        <div class="site">
            
            <a href="/"><img src="../images/layout/banner.jpg" border="0" /></a>
            
            <div class="menu"><a href="../index.php">Home</a></div>
            
            
                
                <div class="content">
                
                <?php
				
                if (isset($_COOKIE["cookie:mpbcadmin"])) {
				
				$selectHits = mysql_query("SELECT * FROM `hits` WHERE `date` = '" . $time . "' GROUP BY `ip`") or die(mysql_error()); // Select stats from today
				  
				$uniqueToday = mysql_num_rows($selectHits); // Count uniques today
				$hitsToday = mysql_result(mysql_query("SELECT SUM(`hits`) as total FROM `hits` WHERE `date` = '" . $time . "' GROUP BY `date`"), 0, "total"); // Count total hits today
				 
				$totalUHits = mysql_result(mysql_query("SELECT COUNT(`hits`) FROM `hits`"), 0); // Count total unique hits
				$totalHits = mysql_result(mysql_query("SELECT SUM(`hits`) as total FROM `hits`"), 0, "total"); // Count total hits
				 
				$diff = time() - 300;
				$countOnline = mysql_query("SELECT * FROM `hits` WHERE `online` > '" . $diff . "'") or die(mysql_error());
				$countOnline = mysql_num_rows($countOnline);
				
				
?>
    
Unique Visits Today: <?php echo $uniqueToday ?><br />
Hits Today: <?php echo $hitsToday ?><br />
<br />
Total Unique Visits: <?php echo $totalUHits ?><br />
Total Hits: <?php echo $totalHits ?><br />
<br />
Guests Online: <?php echo $countOnline ?><br />
<br />
    
</div>

<div class="whiteside">
    <a href="../index.php?page=login"><h4>Admin Home</h4></a>
    
    <? printadminmenu(); ?>


<?php
}
?>
</div>
<?php include('../includes/footer.php'); ?>
</div>
</body> 
</html>