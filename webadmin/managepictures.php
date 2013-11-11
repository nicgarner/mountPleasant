<?php

	//find out the page
	if ($_GET['page'] == NULL)
		$page = 'home';
	else
		$page = $_GET['page'];
	
	if ($_POST['submit'] && array_key_exists('displaycategory', $_POST))
		$displaycategory = $_POST['displaycategory'];
	else
		$displaycategory = 0;
			
	$id      = $_GET['id'];
	$action  = $_GET['a'];
	$confirm = $_GET['c'];
	
	include '../includes/connect.php'; 
	include '../includes/functions.php'; 
	
	if (isset($_COOKIE["cookie:mpbcadmin"])) {
		$uid = $_COOKIE['cookie:mpbcadmin'];
		$querya = "SELECT name FROM users WHERE user_id = $uid";
		$result = mysql_query($querya) or die ("Error in query: $querya. " . mysql_error());
		
		while ($row = mysql_fetch_assoc($result))
			$username = $row['name'];
			
		if ($action == 'd' && $confirm == 't') {
			$queryb = "UPDATE graphics SET deleted = 1 WHERE graphic_id = '$id'";
			$result = mysql_query($queryb) or die ('Error in query: $queryb. ' . mysql_error());
			echo '<meta http-equiv="refresh" content="0;url=managepictures.php?c=d">';
		}
			
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Manage Pictures</title>
      <meta name="Author" content="Mount Pleasant Baptist Church" />
      <link rel="shortcut icon" href="../admin/images/favicon.ico" type="image/x-icon" />
      <link rel="icon" href="../admin/images/favicon.ico" type="image/ico" />
      <link rel="stylesheet" media="all" type="text/css" href="../site.css" />
		</head>

		<body>
    	<div class="site">
      	<a href="http://mountpleasantchurch.com/index.php"><img src="../images/layout/banner.jpg" border="0" /></a>
        <div class="menu"><a href="../index.php">Home</a></div>
        <?php
				if (isset($_COOKIE["cookie:mpbcadmin"])) {
					echo '<div class="content">';
					if ($_POST['submit']) {
						if ($_POST['submit'] == 'Upload photo')
						{
							$photo_filename = $_FILES['photo_filename'];
							$photo_newname = $_POST['photo_newname'];
							$photo_caption = $_POST['photo_caption'];
							$photo_copyright = $_POST['photo_copyright'];
							$photo_title = $_POST['photo_title'];
							$photo_category = $_POST['category'];
              
              // replace spaces and remove disallowed characters from filename
              $photo_newname = strtolower(preg_replace("/ /", "_", $photo_newname));
              $allowed = "/[^a-z\\_]/i";
              $photo_newname = preg_replace($allowed,"",$photo_newname);
              $photo_newname = trim($photo_newname, "_");
							
              $errors = null;
							
              // check if there was a file upload error
							$upload_error = $_FILES['photo_filename']['error'];
              if ($upload_error == 0) {
                // if no upload error, check file is valid image and if so, get its filetype for the database
                $name = $photo_newname;
                $size = $photo_filename['size'];
                $type = $photo_filename['type'];
                $photo_types = array(
                   'image/pjpeg' => 'jpg', 
                   'image/jpeg' => 'jpg', 
                   'image/gif' => 'gif', 
                   'image/bmp' => 'bmp', 
                   'image/x-png' => 'png'
                );
                foreach ($photo_types as $known_type => $known_extension)
                  if ($type == $known_type)
                    $extension = $known_extension;
                if(!array_key_exists($photo_filename['type'], $photo_types))
                  $errors['filetype'] = 'The file you uploaded is not a valid image. Please try again.';
              }
              else {
								$errors['upload'] = 'File upload was unsuccessful because ';
								switch ($upload_error) {
									case 1: $errors['upload'] .= ' files must be less than '.ini_get('upload_max_filesize').'. '; break;
									case 2: $errors['upload'] .= ' files must be less than '.ini_get('upload_max_filesize').'. '; break;
									case 3: $errors['upload'] .= ' the upload was interrupted: please try again. '; break;
									case 4: $errors['upload'] .= ' no file was selected: please try again. '; break;
									case 6: $errors['upload'] .= ' of server error 6. '; break;
									case 7: $errors['upload'] .= ' of server error 7. '; break;
									case 8: $errors['upload'] .= ' of server error 8. '; break;
									default: $errors['upload'] .= ' there was an unknown problem uploading the file. ';
								}
								$errors['upload'] .= 'Contact <a href="mailto:webmaster@mountpleasantchurch.com">
								                      webmaster@mountpleasantchurch.com</a> for help.';
              }
							// check required fields have been supplied
              if ($photo_newname == NULL || $photo_caption == NULL)
								$errors['required'] = 'The <strong>Name</strong> and <strong>Caption</strong> fields are required.';
              // check filename doesn't already exist
              $queryj = "SELECT name FROM graphics WHERE name = '$photo_newname'";
              $result = mysql_query($queryj) or die ('Error in query: $queryj. ' . mysql_error());
              if (mysql_num_rows($result) > 0)
                $errors['unique'] = 'A picture called <strong>' . $photo_newname . '</strong> already exists, 
                                     please choose another name.';
              
              // if no errors, try and finalise the upload
              if ($errors == null) {
                // add data to table
                $queryc = "INSERT INTO graphics(name, filetype, size, caption, copyright, category) 
                                      VALUES('$name', '$extension', '$size', '$photo_caption', 
                                             '$photo_copyright', '$photo_category')";
                $result = mysql_query($queryc) or die ("Error in query: $queryc. " . mysql_error());
                // get the new file's id and redirect to its page
                $id = mysql_insert_id();
                $photo_location = "../images/graphics/" . $name . "." . $extension;
                if (move_uploaded_file($photo_filename['tmp_name'], $photo_location) == 1)
                {
                  echo '<meta http-equiv="refresh" content="0;url=managepictures.php?id='.$id.'&a=u">';
                  die();
                }
                else
                  $errors['move_upload'] = 'There was an unknown problem uploading the picture. Please try again, or 
                                            contact <a href="mailto:webmaster@mountpleasantchurch.com">
                                            webmaster@mountpleasantchurch.com</a> for help.';
              }
						}
					}
					
				if ($id == NULL) {
				?>
      	<h3><img src="../images/icons/pictures.png" title="pictures" alt="pictures icon"/> Pictures</h3>
        <? if ($confirm == 'd') echo '<div class="confirm">Picture deleted</div>'; ?>
        <p>Here you can see pictures currently uploaded to the site, and upload new ones.</p>
        <div class="iconlink" style="width:200px">
          <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
            <img src="../images/icons/addpicture.png" title="upload new picture" 
                 alt="upload new picture icon" border="0" />
          </a>
          <div class="text">
            <a href="#" onclick='document.getElementById("uploadform").style.display="block"'>
              Upload new picture
            </a>
          </div>
        </div>
        <br class="clear" />
       
			<a name="upload"></a>
      <? if ($errors) {
           echo '<div class="error" id="errors">';
           foreach ($errors as $error)
             echo '<p>' . $error . '</p>';
           echo '</div>'; 
         }
      ?>
			<form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" 
						name="uploadform" id="uploadform" class="uploadform"
      <? if ($_POST['submit'] && !array_key_exists('displaycategory', $_POST)) echo 'style="display:block"'; ?>
			> 
				<p><div class="label">Photo:</div><input name="photo_filename" type="file" size="30"/></p>
				<p><div class="label">Name:</div><input name="photo_newname" size="44" value="<?= $photo_newname ?>"/></p>
				<p><div class="label">Caption:</div><input name="photo_caption" size="44" value="<?= $photo_caption ?>"/></p>
				<p><div class="label">Copyright:</div>
             <input name="photo_copyright" size="44" value="<?= $photo_copyright ?>" /></p>
				<p><div class="label">Category:</div>
				<select name="category">
				<?php
          $queryd = "SELECT category_id, name FROM graphic_categories ";
          $result = mysql_query($queryd) or die ('Error in query: $queryd. ' . mysql_error());
          // check if records were returned
          if (mysql_num_rows($result) > 0) {
            while($row = mysql_fetch_object($result)) {
              echo '<option value="' . $row->category_id . '"';
              if ($photo_category) {
                if ($row->category_id == $photo_category)
                  echo ' selected="selected"';
              }
              else
                if ($row->category_id == $displaycategory)
                  echo ' selected="selected"';
              echo '>' . $row->name . '</option>';
            }
          }
        ?>
        </select></p>
        <div class="label"></div>
        <input type="submit" name="submit" value="Upload photo" />
        <input type="reset" name="cancel" value="Cancel" 
               onclick='document.getElementById("uploadform").style.display="none"
                        document.getElementById("errors").style.display="none"'/>
			</form>
      
      <form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>" 
              method="post" name="category" class="right">
          Show:
          <select name="displaycategory" >
            <option value="0">All pictures</option>
            <?php
              $querye = "SELECT category_id, name FROM graphic_categories";
              $result = mysql_query($querye) or die ('Error in query: $querye. ' . mysql_error());
              // check if records were returned
              if (mysql_num_rows($result) > 0)
                while($row = mysql_fetch_object($result)) {
                  echo '<option value="' . $row->category_id . '"';
                  if ($row->category_id == $displaycategory)
										echo ' selected="selected"';
                  echo '>' . $row->name . '</option>';
                }
            ?>
          </select>
          Sort:
          <select name="orderby" >
            <option value="name:asc"
						  <? if ($_POST['orderby'] == 'name:asc') echo 'selected="selected"'; ?>>A - Z</option>
            <option value="name:desc"
						  <? if ($_POST['orderby'] == 'name:desc') echo 'selected="selected"'; ?>>Z - A</option>
            <option value="graphic_id:asc"
						  <? if ($_POST['orderby'] == 'graphic_id:asc') echo 'selected="selected"'; ?>>Oldest first</option>
            <option value="graphic_id:desc"
						  <? if ($_POST['orderby'] == 'graphic_id:desc') echo 'selected="selected"'; ?>>Newest first</option>
          </select>
          <input type="submit" name="submit" value="OK" />
        </form>
    
    <?php
		
		$queryf = "SELECT graphic_id, name, filetype, size, caption, copyright FROM graphics WHERE deleted = 0 ";
		if ($displaycategory != '0')
			$queryf .= "AND category = '".$displaycategory."' ";
		if (array_key_exists('orderby', $_POST)) {
			$orderby = explode(':',$_POST['orderby']);
			$queryf .= "ORDER BY " . $orderby[0] . ' ' . $orderby[1];
		}
		else
			$queryf .= "ORDER BY name asc";
		$result = mysql_query($queryf) or die ('Error in query: $queryf. ' . mysql_error());
		
		if (mysql_num_rows($result) > 0) {
			echo '<div class="manage_pictures">';
			while($row = mysql_fetch_object($result)) {
				echo '<div class="row"><div class="image">';
				echo '<img width="100" src="../images/graphics/' . $row->name . '.' . $row->filetype . '" ';
				echo 'title="'. $row->caption .'"/></div>';
				echo '<div class="details">';
				echo '<p><strong>Name:</strong><br/>' . $row->name . '</p>';
				echo '<p><a href="'.$_SERVER['PHP_SELF'].'?id='.$row->graphic_id.'">Edit picture details</a><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row->graphic_id.'&a=d">Delete picture</a></p>';
				echo '</div></div>';
			}
			echo '</div>';
		}
		else
			echo '<br class="clear" /><br/>
			      <div class="warning"><p>There are no images to display in this category.</p></div>
						<br class="clear" />';
		
		
	?>
    
    <div class="iconlink" style="width:200px">
      <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
        <img src="../images/icons/addpicture.png" title="upload new picture" 
             alt="upload new picture icon" border="0"/>
      </a>
      <div class="text">
        <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
          Upload new picture
        </a>
      </div>
    </div>
    <br class="clear"/>
    
    <?php
		}
		else {
			if ($_POST['submit']) {
        $name = $_POST['photo_name'];
        $filetype = $_POST['photo_filetype'];
        $caption = $_POST['photo_caption'];
        $copyright = $_POST['photo_copyright'];
        $category = $_POST['photo_category'];
        
        if ($caption == NULL)
          $error = '<div class="error">The <strong>Caption</strong> field is required.</div>';
        else {
          $queryg = "UPDATE graphics SET caption='$caption', copyright='$copyright', category=$category
                    WHERE graphic_id = $id";
          $result = mysql_query($queryg) or die ('Error in query: $queryg. ' . mysql_error());
          $action = 's';
        }
      }
      
      else {
        $queryh = "SELECT name, filetype, size, caption, copyright, category 
                  FROM graphics WHERE graphic_id = '".$id."'";
        $result = mysql_query($queryh) or die ('Error in query: $queryh. ' . mysql_error());
        if (mysql_num_rows($result))
          while($row = mysql_fetch_object($result)) {
            $name = $row->name;
            $filetype = $row->filetype;
            $caption = $row->caption;
            $copyright = $row->copyright;
            $category = $row->category;
          }
        else
          $no_record = 1;
      }
      
      // don't display the edit picture page if no record was found
      if (!$no_record) { ?>
        <h3>
          <img src="../images/icons/pictures.png" title="pictures" alt="pictures icon"/>
          Edit picture: <em><?= $name ?></em>
        </h3>
        <div class="calendar"><div class="options">
          <div class="selected"><a href="<?= $_SERVER['SCRIPT_NAME'] ?>">Back to index</a></div>
        </div></div>
        <p><a href="<?= $PHP_SELF.'?id='.$id.'&a=d' ?>">Delete the picture</a></p>
        <?
          if ($action == 'u') echo '<div class="confirm"><p>Picture uploaded successfully!</p></div>';
          if ($action == 's') echo '<div class="confirm"><p>Changes saved successfully!</p></div>';
          if ($action == 'd') echo '<div class="warning"><p>Are you sure you want to delete this picture?</p>
                                    <p><a href="'.$PHP_SELF.'?id='.$id.'&a=d&c=t">Yes, delete the picture</a> |
                                    <a href="'.$PHP_SELF.'?id='.$id.'">No, cancel!</a></p></div>';
        
          echo '<img src="../images/graphics/' . $name . '.' . $filetype .'"
                     title="' . $caption . '" />';
        
        // don't show the editing form if we're deleting the picture
        if ($action != 'd') {
          if ($error) echo $error;
        ?>
        <form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>" 
              method="post" name="update" class="uploadform" 
              style="display:block; margin-left:0; width:350px"> 
          <input type="hidden" name="photo_name" value="<?= $name ?>" />
          <input type="hidden" name="photo_filetype" value="<?= $filetype ?>" />
          <p><div class="label">Name:</div>
            <input name="photo_newname" size="30" value="<?= $name ?>" disabled="disabled" /></p>
          <p><div class="label">Caption:</div>
            <input name="photo_caption" size="30" value="<?= $caption ?>" /></p>
          <p><div class="label">Copyright:</div>
            <input name="photo_copyright" size="30" value="<?= $copyright ?>" /></p>
          <p><div class="label">Category:</div>
            <select name="photo_category">
            <?php
              $queryi = "SELECT category_id, name FROM graphic_categories";
              $result = mysql_query($queryi) or die ('Error in query: $queryi. ' . mysql_error());
              // check if records were returned
              if (mysql_num_rows($result) > 0)
                while($category_row = mysql_fetch_object($result)) {
                  echo '<option value="' . $category_row->category_id . '"';
                  if ($category == $category_row->category_id)
                    echo ' selected="selected"';
                  echo '>' . $category_row->name . '</option>';
                }
            ?>
            </select></p>
          <div class="label"></div>
            <input type="submit" name="submit" value="Save changes" />
        </form>
	<?php
			  }
      }
      else {
        echo '<h3><img src="../images/icons/pictures.png" title="pictures" alt="pictures icon"/> Pictures</h3>';
        echo '<p>Picture not found! <a href="managepictures.php">Back to manage pictures.</a></p>';
      }
		}
	?>
</div>

<div class="whiteside">
	<a href="../index.php?page=login"><h4>Admin Home</h4></a>
	<? printadminmenu(); ?>
</div>
<br class="clear" />

<?php
}
else {
	echo '<div class="page"><p>Please <a href="/login?target='.urlencode($_SERVER['PHP_SELF']).'">login</a> to continue.</p></div>';
}
?>
<?php include('../includes/footer.php'); ?>
</div>
</body> 
</html>