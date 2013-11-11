<?php

	//find out the page
	if ($_GET['page'] == NULL)
		$page = 'home';
	else
		$page = $_GET['page'];
    
  if ($_POST['submit'] && array_key_exists('displayfiletype', $_POST))
		$displayfiletype = $_POST['displayfiletype'];
	else
		$displayfiletype = 0;
	
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
			$queryb = "UPDATE files SET deleted = 1 WHERE file_id = '$id'";
			$result = mysql_query($queryb) or die ('Error in query: $queryb. ' . mysql_error());
			echo '<meta http-equiv="refresh" content="0;url=managefiles.php?c=d">';
		}
			
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<title>Manage Files</title>
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
						if ($_POST['submit'] == 'Upload file')
						{
							$file_filename = $_FILES['file_filename'];
							$file_newname = $_POST['file_newname'];
							$file_link_text = $_POST['file_link_text'];
              
              // replace spaces and remove disallowed characters from filename
              $file_newname = strtolower(preg_replace("/ /", "_", $file_newname));
              $allowed = "/[^a-z\\_]/i";
              $file_newname = preg_replace($allowed,"",$file_newname);
              $file_newname = trim($file_newname, "_");
							
              $errors = null;
							
              // check if there was a file upload error
							$upload_error = $_FILES['file_filename']['error'];
              if ($upload_error == 0) {
                // if no upload error, check file is valid image and if so, get its filetype for the database
                $name = $file_newname;
                $size = $file_filename['size'];
                $type = $file_filename['type'];
                $file_types = array(  
                   'application/pdf' => 'pdf',
                   'application/zip' => 'zip',
                   'application/x-gzip' => 'gz',
                   'text/html' => 'html',
                   'text/csv' => 'csv',
                   'text/plain' => 'txt',
                   'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                   'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                   'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                   'application/msword' => 'doc',
                   'application/vnd.ms-excel' => 'xls',
                   'application/vnd.ms-powerpoint' => 'ppt',
                   'image/pjpeg' => 'jpg', 
                   'image/jpeg' => 'jpg', 
                   'image/gif' => 'gif', 
                   'image/bmp' => 'bmp', 
                   'image/x-png' => 'png'
                );
                foreach ($file_types as $known_type => $known_extension)
                  if ($type == $known_type)
                    $extension = $known_extension;
                if(!array_key_exists($file_filename['type'], $file_types))
                  $errors['filetype'] = 'The file you uploaded is not supported. Please try another format.';
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
              if ($file_newname == NULL || $file_link_text == NULL)
								$errors['required'] = 'The <strong>Name</strong> and <strong>Link text</strong> fields are 
                                       required.';
              // check filename doesn't already exist
              $queryj = "SELECT name FROM files WHERE name = '$file_newname' AND filetype='$extension'";
              $result = mysql_query($queryj) or die ('Error in query: $queryj. ' . mysql_error());
              if (mysql_num_rows($result) > 0)
                $errors['unique'] = 'A file called <strong>' . $file_newname . '.' . $extension .'</strong> 
                                     already exists, please choose a different name.';
              
              // if no errors, try and finalise the upload
              if ($errors == null) {
                // add data to table
                $queryc = "INSERT INTO files (name, filetype, size, link_text) 
                                      VALUES ('$name', '$extension', '$size', '$file_link_text')";
                $result = mysql_query($queryc) or die ("Error in query: $queryc. " . mysql_error());
                // get the new file's id and redirect to its page
                $id = mysql_insert_id();
                $file_location = "../resources/" . $name . "." . $extension;
                if (move_uploaded_file($file_filename['tmp_name'], $file_location) == 1)
                {
                  echo '<meta http-equiv="refresh" content="0;url=managefiles.php?id='.$id.'&a=u">';
                  die();
                }
                else
                  $errors['move_upload'] = 'There was an unknown problem uploading the file. Please try again, or 
                                            contact <a href="mailto:webmaster@mountpleasantchurch.com">
                                            webmaster@mountpleasantchurch.com</a> for help.';
              }
						}
					}
					
				if ($id == NULL) {
				?>
      	<h3><img src="../images/icons/files.png" title="files" alt="files icon"/> Files</h3>
        <? if ($confirm == 'd') echo '<div class="confirm">File deleted</div>'; ?>
        <p>Here you can see files that have been uploaded to the site, and upload new ones. You can create download
           links to uploaded files with short pieces of code, click 'Edit file details' to see how.</p>
        <div class="iconlink" style="width:200px">
          <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
            <img src="../images/icons/addfile.png" title="upload new file" 
                 alt="upload new file icon" border="0" />
          </a>
          <div class="text">
            <a href="#" onclick='document.getElementById("uploadform").style.display="block"'>
              Upload new file
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
      <? if ($_POST['submit'] && !array_key_exists('orderby', $_POST)) echo 'style="display:block"'; ?>
			> 
				<p><div class="label">File:</div><input name="file_filename" type="file" size="30"/></p>
				<p><div class="label">Name:</div><input name="file_newname" size="44" value="<?= $file_newname ?>"/></p>
				<p><div class="label">Link text:</div>
             <input name="file_link_text" size="44" value="<?= $file_link_text ?>"/></p>
        <div class="label"></div>
        <input type="submit" name="submit" value="Upload file" />
        <input type="reset" name="cancel" value="Cancel" 
               onclick='document.getElementById("uploadform").style.display="none"
                        document.getElementById("errors").style.display="none"'/>
			</form>
      
      <form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>" 
            method="post" name="filter" class="right">
          Show:
          <select name="displayfiletype" >
            <option value="0">All files</option>
            <?php
              $querye = "SELECT DISTINCT(filetype) FROM files ORDER BY filetype ASC";
              $result = mysql_query($querye) or die ('Error in query: $querye. ' . mysql_error());
              // check if records were returned
              if (mysql_num_rows($result) > 0)
                while($row = mysql_fetch_object($result)) {
                  echo '<option value="' . $row->filetype . '"';
                  if ($row->filetype === $displayfiletype)
										echo ' selected="selected"';
                  echo '>' . $row->filetype . '</option>';
                }
            ?>
          </select>
          Sort:
          <select name="orderby" >
            <option value="name:asc"
						  <? if ($_POST['orderby'] == 'name:asc') echo 'selected="selected"'; ?>>A - Z</option>
            <option value="name:desc"
						  <? if ($_POST['orderby'] == 'name:desc') echo 'selected="selected"'; ?>>Z - A</option>
            <option value="file_id:asc"
						  <? if ($_POST['orderby'] == 'file_id:asc') echo 'selected="selected"'; ?>>Oldest first</option>
            <option value="file_id:desc"
						  <? if ($_POST['orderby'] == 'file_id:desc') echo 'selected="selected"'; ?>>Newest first</option>
          </select>
          <input type="submit" name="submit" value="OK" />
        </form>
    
    <?php
		
		$queryf = "SELECT file_id, name, filetype FROM files WHERE deleted = 0 ";
		if ($displayfiletype != '0')
			$queryf .= "AND filetype = '".$displayfiletype."' ";
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
				echo '<div class="row"><div class="image" style="padding:25px 15px; width:30px; height:30px;">';
				echo '<img src="../images/icons/' . $row->filetype . '.gif" ';
				echo 'title="'. $row->filetype .' file"/></div>';
				echo '<div class="details">';
				echo '<p><strong>Name:</strong><br/>' . $row->name . '.' . $row->filetype . '</p>';
				echo '<p><a href="'.$_SERVER['PHP_SELF'].'?id='.$row->file_id.'">Edit file details</a><br/>';
				echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$row->file_id.'&a=d">Delete file</a></p>';
				echo '</div></div>';
			}
			echo '</div>';
		}
		else
			echo '<br class="clear" /><br/>
			      <div class="warning"><p>There are no files to display matching your filters.</p></div>
						<br class="clear" />';
		
		
	?>
    
    <div class="iconlink" style="width:200px">
      <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
        <img src="../images/icons/addfile.png" title="upload new file" 
             alt="upload new file icon" border="0"/>
      </a>
      <div class="text">
        <a href="#upload" onclick='document.getElementById("uploadform").style.display="block"'>
          Upload new file
        </a>
      </div>
    </div>
    <br class="clear"/>
    
    <?php
		}
		else {
			if ($_POST['submit']) {
        $name = $_POST['file_name'];
        $filetype = $_POST['file_filetype'];
        $link_text = $_POST['file_link_text'];
        
        if ($link_text == NULL)
          $error = '<div class="error">The <strong>Link text</strong> field is required.</div>';
        else {
          $queryg = "UPDATE files SET link_text='$link_text' WHERE file_id = $id";
          $result = mysql_query($queryg) or die ('Error in query: $queryg. ' . mysql_error());
          $action = 's';
        }
      }
      
      else {
        $queryh = "SELECT name, filetype, size, link_text FROM files WHERE file_id = '".$id."'";
        $result = mysql_query($queryh) or die ('Error in query: $queryh. ' . mysql_error());
        if (mysql_num_rows($result))
          while($row = mysql_fetch_object($result)) {
            $name = $row->name;
            $filetype = $row->filetype;
            $size = $row->size;
            $link_text = $row->link_text;
          }
        else
          $no_record = 1;
      }
      
      // don't display the edit picture page if no record was found
      if (!$no_record) { ?>
        <h3>
          <img src="../images/icons/files.png" title="files" alt="files icon"/>
          Edit file: <em><?= $name . '.' . $filetype ?></em>
        </h3>
        <div class="calendar"><div class="options">
          <div class="selected"><a href="<?= $_SERVER['SCRIPT_NAME'] ?>">Back to index</a></div>
        </div></div>
        <p><a href="<?= $PHP_SELF.'?id='.$id.'&a=d' ?>">Delete the file</a></p>
        <?
          if ($action == 'u') echo '<div class="confirm"><p>File uploaded successfully!</p></div>';
          if ($action == 's') echo '<div class="confirm"><p>Changes saved successfully!</p></div>';
          if ($action == 'd') echo '<div class="warning"><p>Are you sure you want to delete this file?</p>
                                    <p><a href="'.$PHP_SELF.'?id='.$id.'&a=d&c=t">Yes, delete the picture</a> |
                                    <a href="'.$PHP_SELF.'?id='.$id.'">No, cancel!</a></p></div>';
        
          echo '<br/><div class="iconlink">';
          echo '<a href="resources/'.$name.'.'.$filetype.'">';
          echo '<img src="../images/icons/'.$filetype.'_sm.gif" 
                         title="'.$name.'.'.$filetype.' ('.round(($size)/1024).'kb)" /></a>';
          echo '<div class="text">';
          echo '<a href="resources/'.$name.'.'.$filetype.'"
                       title="'.$name.'.'.$filetype.' ('.round(($size)/1024).'kb)">';
          echo $link_text.'</a> ';
          if ($filetype == 'pdf')
            echo '<small>(Requires <a href="http://get.adobe.com/uk/reader/" target="adobereader">Adobe Reader</a>)</small>';
          echo '</div></div>';
          echo '<br class="clear"/>';
          echo '<br class="clear"/>';
          echo '<p><em>To generate the above link in a page, type [[download:'.$name.'.'.$filetype.']]</em></p>';
        
        // don't show the editing form if we're deleting the picture
        if ($action != 'd') {
          if ($error) echo $error;
        ?>
        <form enctype="multipart/form-data" action="<?php $_SERVER['PHP_SELF'] ?>" 
              method="post" name="update" class="uploadform" 
              style="display:block; margin-left:0; width:350px"> 
          <input type="hidden" name="file_name" value="<?= $name ?>" />
          <input type="hidden" name="file_filetype" value="<?= $filetype ?>" />
          <p><div class="label">Name:</div>
            <input name="file_newname" size="30" value="<?= $name . '.' . $filetype ?>" disabled="disabled" /></p>
          <p><div class="label">Link text:</div>
            <input name="file_link_text" size="30" value="<?= $link_text ?>" /></p>
          <div class="label"></div>
            <input type="submit" name="submit" value="Save changes" />
        </form>
	<?php
			  }
      }
      else {
        echo '<h3><img src="../images/icons/files.png" title="files" alt="files icon"/> Files</h3>';
        echo '<p>File not found! <a href="managefiles.php">Back to manage files.</a></p>';
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