<?php 

	include "../includes/connect.php";
	
	$i = $_GET['i'];
	$m = $_GET['m'];
	
	if($i == NULL) {
		$i = 0;
	}

if ($m == NULL) {
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN">';
}

?>

<html>
<head>
<title>iDentity @ Mount Pleasant Baptist Church, Northampton</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<style type="text/css">
	body {
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size:18px;
		line-height:150%;
	}
	.content {
		width:688px;
		padding:10px;
	}
	h1,h2,h3,h4,h5,h6 {
		font-family:Verdana, Arial, Helvetica, sans-serif;
		text-align:left;
	}
	.column {
		padding-top:26px;
		margin-left:16px;
		float:left;
		display:inline;
		width:210px;
	}
	.box1_top {
		background-image:url(graphics/id_box1_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	.box1_middle {
		background-color:#ffbe69;
		border-left:4px solid #FFF;
		border-right:4px solid #FFF;
		width:188px;
		height:220px;
		padding:5px;
		padding-top:15px;
	}
	.box1_bottom {
		background-image:url(graphics/id_box1_bottom.png);
		background-repeat:no-repeat;
		width:206px;
		height:18px;
	}
	.box2_top {
		background-image:url(graphics/id_box2_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	.box2_middle {
		background-color:#ffa42f;
		border-left:4px solid #FFF;
		border-right:4px solid #FFF;
		width:188px;
		height:220px;
		padding:5px;
		padding-top:15px;
		font-size:12px;
		line-height:150%;
	}
	.box2_middle p {
		margin-top:0px;
		margin-bottom:0px;
	}
	.box2_bottom {
		background-image:url(graphics/id_box2_bottom.png);
		background-repeat:no-repeat;
		width:206px;
		height:18px;
	}
	.box3_top {
		background-image:url(graphics/id_box3_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	.box3_middle {
		background-color:#ffe5bf;
		border-left:4px solid #FFF;
		border-right:4px solid #FFF;
		width:188px;
		height:220px;
		padding:5px;
		padding-top:15px;
	}
	.box3_bottom {
		background-image:url(graphics/id_box3_bottom.png);
		background-repeat:no-repeat;
		width:206px;
		height:18px;
	}
	.box4_top {
		background-image:url(graphics/id_box4_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	.box5_top {
		background-image:url(graphics/id_box5_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	.box6_top {
		background-image:url(graphics/id_box6_top.png);
		background-repeat:no-repeat;
		width:206px;
		height:60px;
	}
	input {
		font-family:Verdana, Arial, Helvetica, sans-serif;
		font-size: 10px;
		color:#333333;
		border: 1px solid #333333;
		padding:2px;
		margin-top:5px;
		margin-bottom:15px;		
	}
	.error {
		font-size:10px;
		background-color:#FF9999;
		color:#990000;
		line-height:100%;
		padding:4px;
		margin-top:5px;
		border:#990000 solid 1px;
		width:177px;
	}
</style>

</head>

<? if ($m == 'p') { ?>

<? 

if (date('H') > 19) {
	echo '<body background="graphics/video2.jpg" topmargin="0" marginheight="0" bottommargin="0" leftmargin="0">';
}
else {
	echo '<body background="graphics/video.jpg" topmargin="0" marginheight="0" bottommargin="0" leftmargin="0">';
}

?>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr valign="middle"><td align="center">
			<?
				$query = "SELECT `name`, `url`, TIME_FORMAT(length, '%s') as seconds, TIME_FORMAT(length, '%i') as minutes, `group` FROM `videos` LIMIT $i, 1";
                $result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
                if(mysql_num_rows($result) == 1) {
					while ($row = mysql_fetch_object($result)) {
						$i++;
						$code =  substr(stristr($row->url, '='), 1);
						$time = $row->minutes * 60 + $row->seconds;
						echo '<object width="638" height="525"><param name="movie" value="http://www.youtube.com/v/'.$code.'&autoplay=1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/'.$code.'&autoplay=1" type="application/x-shockwave-flash" wmode="transparent" width="638" height="525"></embed></object>';
						echo '<meta http-equiv="refresh" content="'.$time.';url=video.php?m=p&i='.$i.'">';
					}
				}
				else {
					echo '<meta http-equiv="refresh" content="0;url=video.php?m=p&i=0">';
				}
            ?>
            <table style="position:relative; top:-36px" height="30px" border="0" width="642"><tr><td bgcolor="#000000"></td></tr></table>
    	</td>
	</tr>

</body>

<? }

else { ?>

<body bgcolor="#CCCCCC" topmargin="30">

<table align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
        <td background="graphics/layout/id_strip_top.jpg" width="722" height="28" colspan="3"></td>
	</tr>
    <tr>
    	<td bgcolor="#FFFFFF" width="7"></td>
        <td bgcolor="#f7961d" background="graphics/layout/id_strip_middle.jpg" valign="top" width="708" height="665">
           	<img src="graphics/iD_banner.png" title="iDentity logo" style="margin-top:8px"/>
            <div class="content">
            	<div class="column">
                    <div class="box5_top"> </div>
                    <div class="box3_middle">
                        <p>Got a favourite YouTube movie?</p>
                        <p>Uploaded your own movies?</p>
                        <p>Found something you like?</p>
                    </div>
                    <div class="box3_bottom"> </div>
                </div>
                <div class="column">
                    <div class="box4_top"> </div>
                    <div class="box1_middle">
                        <p>Tell us where a clip is, and you might<font size="-1"><sup>*</sup></font> see it used at an iD evening soon.</p>
                        <p style="line-height:100%"><font size="-2">* All clips are checked for suitability by iD staff before they are used at an iD event.</font>
                    </div>
                    <div class="box1_bottom"> </div>
                </div>
                <div class="column">
                    <div class="box6_top"> </div>
                    <div class="box2_middle">
                    
						<?
                        
                            if ($_POST['submit'] && $_POST['form'] == 'video'){
                                $url = $_POST['url'];
                                $name = strip_tags($_POST['name']);
                                
                                if ($url == NULL) {
                                    $error = 'Please give us a URL...';
                                }
                                else {
                                	if ($name == NULL) {
                                    	$name = 'Annonymous';
	                                }
									$query = "INSERT INTO videos(url, contributor) VALUES ('$url', '$name')";
                                    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
                                }
                            }
							if ($_POST['submit'] && $_POST['form'] == 'video' && $error == NULL){
								echo '<font size="4"><br><br>Cheers!<br><br><a href="video.php">Got another one?</a></font>';
							}
							else {
                        ?>

                        <form name="comment" method="post" action="<?php echo $PHP_SELF; ?>">
                            <input type="hidden" name="form" value="video">
                            <p>Copy the URL from YouTube and paste it here:</p>
                            <?
								if ($error <> NULL) {
									echo '<div class="error">' . $error . '</div>';
								}
							?>
                            <input name="url" size="34" maxlength="60" value="<?php if ($error <> NULL) { echo $url; } ?>">
                            <p>Tell us your name so we can credit you later. (Optional.)</p>
                            <input name="name" size="20" maxlength="18" value="<?php if ($error <> NULL) { echo $name; } ?>">
                            <p>Click submit:</p>
                            <input type="submit" value=" submit " name="submit" style="padding:0px;">
                        </form>
                        <?
							}
						?>
                    </div>
                    <div class="box2_bottom"> </div>
                </div>
            </div>
            <div style="margin-left:26px; margin-top:20px;">
            	<&nbsp;<a href="index.htm">back to iD</a>
            </div>
        </td>
        <td bgcolor="#FFFFFF" width="7"></td>
    </tr>
    <tr>
        <td background="graphics/layout/id_strip_bottom.jpg" height="28" colspan="3"></td>
	</tr>
</table>

<p align="center">
	<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#333333">
	<a href="index.htm">HOME</a> | iDentity | <a href="house_group.html">House Group</a> | <a href="awe.html">AWE</a> | <a href="rock_solid.html">Rock Solid</a><br /><br />
    iDentity @ <a href="http://www.mountpleasantchurch.com/">Mount Pleasant Baptist Church</a>, Northampton</font>
</p>

</body>
	

<? } ?>
</html>
