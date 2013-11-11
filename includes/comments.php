<?php


if ($_POST['submit'] && $_POST['form'] == 'comment'){
	$name = $_POST['name'];
	$shout = strip_tags($_POST['comment'], '<b><i><br><u>');
	
	if ($comment == NULL && $name == NULL) {
		$error = 'Please enter a name and a comment.';
	}
	elseif ($name == NULL) {
		$error = 'Please enter a name.';
	}
	elseif ($comment == NULL) {
		$error = 'Please enter a comment.';
	}
	else {
		$query = "INSERT INTO development_comments(name, comment, content_id) VALUES ('$name', '$comment', '$content_id')";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	}
}

?>

<div class="comments" id="comments">
	<?php
		echo '<font color="#00584c"><b>Comments</b></font> <img src="images/buttons/green_up.jpg">';
		echo '<p>Please post your comments on the site below.<br>';
		echo 'Each page has its own comments.</p>';
	
		if ($error <> NULL) {
			echo '<div class="error">' . $error . '</div>';
		}
	?>
    <div align="center">
        <form name="comment" method="post" action="<?php echo $PHP_SELF; ?>">
            <input type="hidden" name="form" value="comment">
            <table border="0" cellpadding="3" cellspacing="0">
                <tr valign="center">
                    <td width="35">name:</td>
                    <td><input name="name" size="39" maxlength="60" value="<?php if ($error <> NULL) { echo $name; } ?>"></td>
                </tr>
                <tr valign="top">
                    <td colspan="2" align="right"><textarea name="comment" cols="37" rows="3"><?php if ($error <> NULL) { echo stripslashes($comment); } ?></textarea><font size="1"><br />allowed HTML: &lt;b&gt;&lt;i&gt;&lt;u&gt;</font></td>
                </tr>
                <tr valign="top">
                    <td colspan="2" align="center"><input type="submit" value=" Submit " class="submit" name="submit"></td>
                </tr>
            </table>
        </form>
    </div>


<?php

$query = "SELECT name, comment, DATE_FORMAT(datetime, '%d/%m/%y %H:%i') as timef FROM development_comments WHERE content_id = 1 ORDER BY datetime DESC";
$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
while ($row = mysql_fetch_assoc($result)) {
	echo '<div class="comment">';
		echo '<p><b>'.$row['name'].'</b> - <font size="1">' . $row['timef'] . '</font><br />';
		echo $row['comment'].'</p>';
	echo '</div>';
}

?>