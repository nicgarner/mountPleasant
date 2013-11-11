<?php 

# global $page;
global $role;
global $user_id;
global $editor;
global $fullname;

include 'includes/recaptchalib.php';

$blog    = $_GET['blog'];
$post    = $_GET['post'];
$comment = $_GET['comment'];
$month   = $_GET['month'];
$action  = $_GET['action'];
$confirm = $_GET['confirm'];

	if ($action == 'delete') {
		if ($confirm == 'true') {
			if ($comment == NULL)
				$query = "UPDATE blog_posts SET deleted='1' WHERE blog_post_id = '$post'";
			else
				$query = "UPDATE blog_posts SET deleted='1' WHERE blog_post_id = '$comment'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			
			$action = NULL;
			$confirm = NULL;
			
			if ($comment == NULL) {
				$query = "UPDATE blog_posts SET deleted='1' WHERE parent = '$post'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$post = NULL;
				$postMessage = 'deleted';
			}
			else {
				$query  = "UPDATE blog_posts SET comments=comments-1 WHERE blog_post_id = '$post'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				$comment = NULL;
				$message = 'deleted';
			}
		}
	}
	
	if ($_POST['submit']) {
		if ($action == 'addcomment') {
			if (! isset($_COOKIE["cookie:mpbcadmin"])) {
				$commentName = $_POST['commentName'];
				if ($commentName == NULL)
					$errors = 1;
			}
			$commentText = $_POST['commentText'];
			if ($commentText == NULL)
				$errors = 1;
			
			if (! isset($_COOKIE["cookie:mpbcadmin"])) {
				$privatekey = "6LcuLQwAAAAAAOnPdFbePk2ucZuWM6WK3d0QTNRX";
				$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"],
					$_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
					$errors = 1;
				}
			}
			
			if ($errors == 0) {
				$commentText = nl2br($commentText);
				$query  = "INSERT INTO blog_posts (blog_id, parent, content, type, author_id, author) ";
				$query .= "VALUES ('$blog', '$post', '$commentText', '1', ";
					if (isset($_COOKIE["cookie:mpbcadmin"]))
						$query .= "'$user_id', '')";
					else
						$query .= "'0', '$commentName')";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
				$query  = "UPDATE blog_posts SET comments=comments+1 WHERE blog_post_id = '$post'";
				$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
				
				// email post author
				$query = "SELECT name, author_ids, email_author FROM blogs WHERE blog_id = '$blog'";
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				while($row = mysql_fetch_object($result)) {
					$blog_name = $row->name;
					$email_author = $row->email_author;
				}
				
				
				if ($email_author = 1) {
					$query = "SELECT title, blog_id, author_id FROM blog_posts WHERE blog_post_id = '$post'";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					while($row = mysql_fetch_object($result)) {
						$blog_title = $row->title;
						$blog_id = $row->blog_id;
						$author_id = $row->author_id;
					}
					// if the author of the comment is the same as the author of the comment, don't send email
					if ($user_id <> $author_id) {
						if (isset($_COOKIE["cookie:mpbcadmin"])) {
							$query = "SELECT name, surname FROM users WHERE user_id = '$user_id'";
							$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
							while($row = mysql_fetch_object($result))
								$poster = $row->name.' '.$row->surname;
						}
						else
							$poster = $commentName;
					
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
					
						$subject .= stripslashes($poster) . ' has added a comment to your blog post: ' . stripslashes($blog_title);
						
						$query = "SELECT name, email FROM users WHERE user_id = '$author_id'";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						while($row = mysql_fetch_object($result)) {
							$email  = '<p>Dear ' . $row->name . ',</p>';
							$email .= '<p>'.stripslashes($poster) . ' has added a comment to your blog post: '.stripslashes($blog_title).'</p>';
							$email .= '<p>"'.stripslashes($commentText).'"</p>';
							$email .= '<p>To view the post and reply to or remove the comment, follow the link below:<br />';
							$email .= '<a href="http://www.mountpleasantchurch.com/blogs?blog='.$blog_id.'&post='.$post.'#comments">';
							$email .= 'http://www.mountpleasantchurch.com/blogs?blog='.$blog_id.'&post='.$post.'#comments</a></p>';
							$email .= '<p>Thanks</p><p>The Mount Pleasant Website Team</p>';
							$email .= '<p><font size="1">You are receiving this email because your blog is set up to notify you of ';
							$email .= 'new comments. To stop receiving these emails, ';
							$email .= '<a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change the ';
							$email .= 'option on the Blogs page.</font></p>';
							mail($row->email, $subject, $email, $headers);
						}
					}
				}
				
				$message = 'added';
				$commentName = NULL; $commentText = NULL;
			}
		}
		elseif ($action == 'manage') {
			$blogname         	= $_POST['blogname'];
			$description  		= $_POST['description'];
			$image_caption		= $_POST['image_caption'];
			$comments_allowed   = $_POST['comments_allowed'];
			$author_ids	        = $_POST['author_ids'];
			$active  			= $_POST['active'];
			$remove 			= $_POST['remove'];
			$image 				= $_FILES['image'];
			$image2				= $_POST['image2'];
			
			if (! $author_ids == NULL) {
				foreach ($author_ids as $author_id)
					$authors = $authors . $author_id . ',';
				$authors = substr($authors,0,-1);
			}
			
			if ($remove == 'on') { $remove = 1; $image = NULL; $image2 = NULL;} else $remove = 0;	
			if ($comments_allowed == 'on') $comments_allowed = 1; else $comments_allowed = 0;
			if ($active == 'on') $active = 1; else $active = 0;
							
			if ($blogname == NULL) {
				$errors = 1;
				$error1 = 1;
			}
			if ($author_ids == NULL) {
				$errors = 1;
				$error2 = 1;
			}
			
			if ($image['error'] == 4) $image = NULL;
					
			if (!$blogname == NULL) {
			
				if ($image == NULL) {
					if (!$image2 == NULL)
						$image = $image2;
				}
				else {
					$type = $image['type'];
					$photo_types = array(
						'image/pjpeg' => 'jpg', 
						'image/jpeg' => 'jpg', 
						'image/gif' => 'gif', 
						'image/bmp' => 'bmp', 
						'image/x-png' => 'png', 
						'image/png' => 'png',
					);
					$photo_name = strtolower(preg_replace("/ /", "_", $blogname));
					$allowed = "/[^a-z\\_]/i";
					$photo_name = preg_replace($allowed,"",$photo_name);
					
					$size  = $image['size'];
					$type  = $image['type'];
					
					foreach ($photo_types as $known_type => $known_extension) {
						if ($type == $known_type) {
							$extension = $known_extension;
						}
					}
					
					if(!array_key_exists($image['type'], $photo_types)) {				
						$errors = 1;
						$error4 = 1;
						$image = NULL;
#						echo 'must be jpeg, go get a jpeg<br />';
					}
					else {
						$photo_location = "images/graphics/" . $photo_name . "." . $extension;
						if (!move_uploaded_file($image['tmp_name'], $photo_location) == 1) {
							$errors = 1;
							$error5 = 1;
							$image = NULL;
#							echo 'problem copying file';
						}
						else {
							$image = $photo_name . '.' . $extension;
						}
					}
				}
			}
			else
				$image = NULL;
			
/*			echo '<p>blog: '.$blog.'<br/>';
			echo 'name: ' . $blogname . '<br/>';
			echo 'description: ' . nl2br($description) . '<br/>';
			echo 'image: '.$image.'<br/>';
			echo 'remove: ' . $remove . '<br/>';
			echo 'image_caption: ' . $image_caption . '<br/>';
			echo 'comments_allowed: ' . $comments_allowed . '<br/>';
			echo 'author_ids: '.$authors.'<br/>';
			echo 'active: ' . $active . '<br/></p>';
*/			
			if (!$errors == 1) {
				if ($blog == 'new') {
					$query  = "INSERT INTO blogs ";
					$query .= "(name, description, image, image_caption, text, comments_allowed, author_ids) ";
					$query .= "VALUES ('$blogname', '".$description."', '$image', '$image_caption', '$text', '$comments_allowed', ";
					$query .= "'$authors')";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$blogid = mysql_insert_id();
					$message = 'added';
				}
				else {
					$query  = "UPDATE blogs SET name='$blogname', description='".$description."', ";
					$query .= "image_caption='$image_caption', text='$text', comments_allowed='$comments_allowed', ";
					$query .= "author_ids='$authors', active='$active', ";
					if ($image <> NULL)
						$query .= "image='$image' ";
					else
						$query .= "image=NULL ";
					$query .= "WHERE blog_id = '$blog'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					if ($active == 0)
						$message = 'deactivated';
					else
						$message = 'saved';
				}
				$blog = NULL;
			}
		}
		else {
			$title    = $_POST['title'];
			$entry    = $_POST['entry'];
			$now      = date('Y-m-d H:i:s');
		
			if ($title == NULL) {
				$errors = 1;
				$error1 = 1;
			}
			if ($entry == NULL) {
				$errors = 1;
				$error2 = 1;
			}
			
			if (!$errors == 1) {
				if (!$post == NULL) {
					$query  = "UPDATE blog_posts SET title='$title', content='$entry', updated='$now' ";
					$query .= "WHERE blog_post_id = '$post'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$postMessage = 'saved';
				}
				else {
					$query  = "INSERT INTO blog_posts (blog_id, title, content, author_id) ";
					$query .= "VALUES ('$blog', '$title', '$entry', '$user_id')";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
					$post = mysql_insert_id();
					
					// if this is the first post, email users subscribed to receive new blog notifications
					$query = "SELECT name FROM blogs WHERE blog_id = '$blog'";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					while($row = mysql_fetch_object($result))
						$blog_name = $row->name;

					$query  = "SELECT blog_post_id FROM blog_posts WHERE blog_id = '$blog'";
					$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

					if (mysql_num_rows($result) == 1) {
						$query  = "SELECT name, email FROM users WHERE emailBlog = 1 AND (confirmed = 1 OR confirmed = 3)";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						if (mysql_num_rows($result) > 0) {
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
							$subject  = $blog_name.' blog has been created on the Mount Pleasant website';
							
							while($row = mysql_fetch_object($result)) {
								$email = '<p>Dear ' . $row->name . ',</p><p>A new blog called <b>'.$blog_name.'</b> has been created on the Mount Pleasant website. You can read it on the <a href="http://www.mountpleasantchurch.com/blogs">Blogs</a> page.</p><p>If you would like to continue to receive emails when this blog is updated, please <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account, go to the Blogs page, and then check the \'Email me when this blog is updated\' box.</p><p>Thanks</p><p>The Mount Pleasant Website Team</p><p><font size="1">You are receiving this email as a member of the Mount Pleasant Baptist Church website. To stop receiving these emails, <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change your preferences.</font></p>';
								mail($row->email, $subject, $email, $headers);
/*								echo '<p>'.$row->email.'</p>';
								echo '<p>'.$subject.'</p>';
								echo '<p>'.$email.'</p>';
								echo '<hr>';
*/							}
						}
					}
					
					
					// otherwise, email users subscribed to this blog
					else {
						$query  = "SELECT users.name, users.email FROM users, blog_subscriptions ";
						$query .= "WHERE users.user_id = blog_subscriptions.user_id AND blog_subscriptions.blog_id = '$blog' ";
						$query .= "AND (confirmed = 1 OR confirmed = 3)";
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						if (mysql_num_rows($result) > 0) {
							
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: Mount Pleasant Baptist Church <webmaster@mountpleasantchurch.com>' . "\r\n";
							
							$subject .= $blog_name . ' has posted a new blog post';
							
							while($row = mysql_fetch_object($result)) {
								$email = '<p>Dear ' . $row->name . ',</p><p>'.$blog_name . ' has posted a new blog post called <b>'.stripslashes(stripslashes($title)).'</b> on the Mount Pleasant website. You can read it on the <a href="http://www.mountpleasantchurch.com/blogs?blog='.$blog.'">Blogs</a> page.</p><p>Thanks</p><p>The Mount Pleasant Website Team</p><p><font size="1">You are receiving this email as a member of the Mount Pleasant Baptist Church website. To stop receiving these emails, <a href="https://www.mountpleasantchurch.com/login">log in</a> to your account and change your preferences.</font></p>';
								mail($row->email, $subject, $email, $headers);
/*								echo '<p>'.$row->email.'</p>';
								echo '<p>'.$subject.'</p>';
								echo '<p>'.$email.'</p>';
								echo '<hr>';
*/							}
						}
					}
					
					$postMessage = 'added';
				}
			}
			else
				$action = 'edit';
		}
	}
	
	if ($action == 'manage') {
		if (! $role == 1)
			$action = NULL;
	}
	
	if ($action == 'manage') {
	echo '<h3>Manage Blogs</h3>';
		// if a blog id is supplied, show details
		if (! $blog == NULL) {
		
			// if submitted with an error, show an error message
			if ($error1 == 1 && $error2 == 1)
				echo '<div class="error"><p>You must enter a name and select at least one blog author before saving.</p></div>';
			elseif ($error1 == 1)
				echo '<div class="error"><p>You must enter a name before saving.</p></div>';
			elseif ($error2 == 1)
				echo '<div class="error"><p>You must select at least one blog author before saving.</p></div>';
				
			if (! $_POST['submit']) {	
				// try and find a blog with the blog id
				$query = "SELECT name, description, image, image_caption, comments_allowed, author_ids, active ";
				$query .= "FROM blogs WHERE blog_id='$blog'; ";		
				$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				if (mysql_num_rows($result) > 0) {
					while($row = mysql_fetch_object($result)) {
						$blogname         	= $row->name;
						$description  		= $row->description;
						$image        		= $row->image;
						$image_caption      = $row->image_caption;
						$comments_allowed   = $row->comments_allowed;
						$author_ids         = explode(',',$row->author_ids);
						$active  			= $row->active;
					}
				echo '<h4>Edit blog properties</h4>';
				}
				else
					echo '<h4>Create blog</h4>';
			}
			else {
				if ($blog == 'new')
					echo '<h4>Create blog</h4>';
				else
					echo '<h4>Edit blog properties</h4>';
			}
			
			// if deleting post, get confimation
			if ($action == 'delete' && $comment == NULL) {
				echo '<div class="warning">';
				echo '<p>Are you sure you want to remove this post from the blog?</p><p align="center">';
				echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'&action=delete&confirm=true">Delete ';
				echo 'blog post</a> | <a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'">Cancel</a></p></div>';
			}
			

			echo '<form enctype="multipart/form-data" name="manageBlogs" method="post" ';
			echo 'action="'. $PHP_SELF.'?action=manage&blog=';
				if ($blog == 0) echo 'new'; else echo $blog;
			echo '">';
				
			echo '<div class="label">Blog name:</div>';
			echo '<input name="blogname" maxlength="60" size="58" value="'.$blogname.'" />';
			echo '<br class="clear" />';
			
			echo '<div class="label">Description:</div>';
			echo '<textarea name="description" cols="60" rows="7">'.$description.'</textarea>';
			echo '<br class="clear" /><br />';
			
			
			if (! $image == NULL && $error6 == NULL) {
				echo '<div class="label">Image:</div>';
				echo '<img class="blog_adminimg" src="images/graphics/'.$image.'?rand='.rand(1,1000).'" alt="';
				if ($image_caption == NULL) echo $blogname;	else echo $image_caption; echo '"><br class="clear" />';
				echo '<div class="label"></div><input type="checkbox" name="remove" />Remove image<br class="clear" />';
				echo '<input name="image2" value="'.$image.'" type="hidden" />';
			}
			echo '<div class="label">'; if ($image == NULL) echo 'Image: '; else echo 'Change image:'; echo '</div>';
			echo '<input type="file" name="image" size="40" /><br class="clear" />';
			echo '<div class="label">Image caption:</div>';
			echo '<input name="image_caption" maxlength="60" size="58" value="'.$image_caption.'" />';
			echo '<br class="clear" /><br />';
			
			echo '<div class="label">Allow comments:</div><input type="checkbox" name="comments_allowed"';
			if ($comments_allowed == '1') echo ' checked="checked"'; echo '><br class="clear" />';
			
			if ($blog <> 'new') {
				echo '<div class="label">Active:</div><input type="checkbox" name="active"';
				if ($active == '1') echo ' checked="checked"'; echo '><br class="clear" />';
			}
			
			echo '<br /><div class="label">Authors:</div>';
			
			$query  = "SELECT user_id, name, surname FROM users WHERE confirmed = 1 ORDER BY surname ASC";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<select multiple="multiple" size="10" name="author_ids[]">';
				while ($row = mysql_fetch_assoc($result)) {
					if (! $row['user_id'] == 0) {
						echo '<option value="'.$row['user_id'].'"';
						if ($author_ids == NULL || $blog == 'new' && ! $_POST['submit'])
							echo '';
						else {
							if (in_array($row['user_id'], $author_ids))
								echo ' selected="selected"';
						}
						echo '>'.$row['name'].' '.$row['surname'].'</option>';
					}
				}
				echo '</select>';
			}
			else
				echo 'There are no registered users.';
			
			echo '<br class="clear" /><br/>';
			echo '<input type="submit" name="submit" value="Save changes" /> ';
			if ($blog == 0)
				echo '<a class="button" href="'.$PHP_SELF.'?action=manage" />Cancel</a> ';
			else
				echo '<a class="button" href="'.$PHP_SELF.'?action=manage" />Discard changes</a> ';
			echo '</form>';
		}
		else {		
			echo '<div class="calendar"><div class="options">';
				echo '<div class="selected"><a href="'.$PHP_SELF.'">Back to blogs</a></div>';
			echo '</div></div>';
			if (!$message == NULL) {
				echo '<div class="confirm"><p>';
				if ($message == 'saved')
					echo 'Your changes have been saved.';
				elseif ($message == 'added') {
					echo 'The blog <b>'.$blogname.'</b> has been created. ';
					echo '<a href="'.$PHP_SELF.'?blog='.$blogid.'&action=edit">Add the first post!</a>';
				}
				elseif ($message == 'deactivated')
					echo 'The blog <b>'.$blogname.'</b> has been deactivated. ';
				echo '</p></div><br />';
			}
			$query  = "SELECT blog_id, name, description, image, image_caption FROM blogs WHERE active='1' AND deleted='0' ";
			$query .= "ORDER BY active DESC, created DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {	
				echo '<p>Click on the name of a blog to edit its properties, or ';
				echo '<a href="'.$PHP_SELF.'?action=manage&blog=new">create a new blog</a>.</p>';
				echo '<h4>Active blogs:</h4>';
				echo '<ul class="wide">';
				while($row = mysql_fetch_object($result)) {
					echo '<li><a href="'.$PHP_SELF.'?action=manage&blog='.$row->blog_id.'">'.$row->name.'</a></li>';
				}
				echo '</ul>';
			}
			else
				echo '<p>No blogs have been created yet.</p>';
			
			$query  = "SELECT blog_id, name, description, image, image_caption FROM blogs WHERE active='0' AND deleted='0' ";
			$query .= "ORDER BY active DESC, created DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<h4>Inactive blogs:</h4>';
				echo '<ul class="wide">';
				while($row = mysql_fetch_object($result)) {
					echo '<li><a href="'.$PHP_SELF.'?action=manage&blog='.$row->blog_id.'">'.$row->name.'</a></li>';
				}
				echo '</ul>';
			}
		}
	}
	else {
	
		// if a post id is supplied, ensure the blog id is correct
		$query = "SELECT blog_id FROM blog_posts WHERE blog_post_id='$post'";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result)) {
				$blog = $row->blog_id;
			}
		}
		else
			$post = NULL;
	
		// try and find a blog with the blog id
		$query  = "SELECT name, description, image, image_caption, text, comments_allowed, author_ids, active FROM blogs ";
		$query .= "WHERE blog_id='$blog' AND deleted='0' ";
		$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
		
		// if a blog is found, display the title and put the other info into variables to use later
		if (mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_object($result)) {
				echo '<h3><a href="'.$PHP_SELF.'?blog='.$blog.'">Blog: '.$row->name.'</a></h3>';
				echo '<div class="options"><div class="selected" style="width:120px">
				     <a href="'.$PHP_SELF.'">Blogs homepage</a>
					 </div></div>';
				
				// if the blog is no longer being actively updated, display a message to say so
				if ($row->active == 0)
					echo '<div class="warning"><p>Please note that this blog is no longer being updated.</p></div>';
				$active = $row->active;
				$blogname = $row->name;
				$description = $row->description;
				$image = $row->image;
				$image_caption = $row->image_caption;
				$sidetext = $row->text;
				$comments_allowed = $row->comments_allowed;
				$author_ids = explode(',',$row->author_ids);
			}
			
			if ($postMessage == 'deleted') {
				echo '<div class="confirm"><p>The blog post has been deleted.</p></div><br />';
			}
			
			// try and find a post with the post id
			$query  = "SELECT title, content, users.name, comments, ";
			$query .= "DATE_FORMAT(date, '%W %M %D %Y') as date, LOWER(DATE_FORMAT(date, '%l:%i %p')) as time, ";
			$query .= "DATE_FORMAT(updated, '%d %b') as updated_date, LOWER(DATE_FORMAT(updated, '%l:%i %p')) as updated_time, ";
			$query .= "DATE_FORMAT(updated, '%W %M %D %Y') as updated_datef ";
			$query .= "FROM blog_posts, users WHERE blog_post_id='$post' AND blog_posts.author_id = users.user_id ";
			$query .= "AND blog_posts.deleted='0'";
				
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0 || $action == 'edit') {
				while($row = mysql_fetch_object($result)) {
					$title         = $row->title;
					$entry         = $row->content;
					$date          =  $row->date;
					$comments      = $row->comments;
					$author        = $row->name;
					$time          = $row->time;
					$updated_date  = $row->updated_date;
					$updated_datef = $row->updated_datef;
					$updated_time  = $row->updated_time;
				}		
				
				// display any messages from the results of submitting the page
				if (! $postMessage == NULL) {
					echo '<div class="confirm"><p>';
					if ($postMessage == 'saved')
						echo 'Your changes have been saved.';
					if ($postMessage == 'added')
						echo 'Your blog post has been added.';
					echo '</p></div><br />';
				}
				
				// if deleting post, get confimation
				if ($action == 'delete' && $comment == NULL) {
					echo '<div class="warning">';
					echo '<p>Are you sure you want to remove this post from the blog?</p><p align="center">';
					echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'&action=delete&confirm=true">Delete ';
					echo 'blog post</a> | <a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'">Cancel</a></p></div>';
				}
				
				// if editing the page, say so
				if ($action == 'edit') {
					echo '<h4>';
					if ($post == 0)
						echo 'Add blog post';
					else
						echo 'Edit blog post';
					echo '</h4>';
					echo '<form name="editpage" method="post" action="'. $PHP_SELF.'?blog='.$blog;
						if ($post == 0) echo ''; else echo '&post='.$post;
					echo '" />';
	//				echo '<input name="date" value="'.$date.'" type="hidden" /> ';
	//				echo '<input name="updated" value="'.$updated.'" type="hidden" />';
				}
				else {
					echo '<div class="blog_page">';
					echo '<div class="date">'.$date.'</div>';
				}
				
				// if editing the post...
				if ($action == 'edit') {
					
					// ... if submitted with an error, show an error message ...
					if ($error1 == 1 && $error2 == 1)
						echo '<div class="error"><p>You must enter a title and some content for the blog post before saving.</p></div>';
					elseif ($error1 == 1)
						echo '<div class="error"><p>You must enter a title for the blog post before saving.</p></div>';
					elseif ($error2 == 1)
						echo '<div class="error"><p>You must enter some content for the blog post before saving.</p></div>';
					
					// ... display a field to edit the title of the post
					echo '<br><div class="label" style="width:70px;">Post title:</div>';
					echo '<input name="title" maxlength="60" size="53" value="'.$title.'" class="blog_title_input" />';
					echo '<br class="clear" />';
				}
				else
					// if not editing, just show the title
					echo '<div class="title">'.stripslashes($title).'</div>';
					
				// if editing, show the post in the editor ...
				if ($action == 'edit') {
					$oFCKeditor = new FCKeditor('entry') ; 
					$oFCKeditor->BasePath	= 'includes/FCKeditor/';
					$oFCKeditor->ToolbarSet = 'Basic';
					$oFCKeditor->Value = stripslashes($entry);
					$oFCKeditor->Height = 600;
					$oFCKeditor->Create();
				}
				
				// ... otherwise, just show the post
				else
					echo '<p>' . stripslashes($entry) . '</p>';
					
				// if editing, show save and cancel buttons ...
				if ($action == 'edit') {
					echo '<br class="clear" /><br/>';
					echo '<input type="submit" name="submit" value="Save changes" /> ';
					if ($blog == 0)
						echo '<a class="button" href="'.$PHP_SELF.'?blog='.$blog.'" />Cancel</a> ';
					else
						echo '<a class="button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'" />Discard changes</a> ';
					echo '</form>';
				}
				
				// ... otherwise, show date and comments
				else {
					echo '<div class="blog_posted">This post was made by '.$author.' at '.$time;
					if ($updated_date <> NULL) {
						echo ' and updated ';
						if ($updated_datef <> $date)
							echo 'on '.$updated_date.' ';
						echo 'at '.$updated_time;
					}
					echo '.</div>';
					
					// show comments
					echo '<a name="comments" />';
					
					if ($active == 1) {
						echo "<div style=\"top:19px; position:relative; float:right;\">";				
						echo "<a href='javascript:' onclick='document.getElementById(\"addcomment\").style.display=\"block\"'>";
						echo "+ Add comment</a></div>";
					}
					if ($comments == 1) echo '<h4>There is 1 comment';
					else echo '<h4>There are '.$comments.' comment'; if ($comments <> 1) echo 's';
					echo ' on this post'; if ($comments == 0) echo ' yet.'; else echo ':'; echo '</h4>';
					
					if (! $message == NULL) {
						echo '<br /><div class="confirm"><p>';
						if ($message == 'added')
							echo 'Your comment has been added.';
						if ($message == 'deleted')
							echo 'The comment has been deleted.';	
						echo '</p></div><br />';
					}
					
					// if deleting a comment, get confirmation
					if ($action == 'delete' && ! $comment == NULL) {
						echo '<br /><div class="warning">';
						echo '<p>Are you sure you want to delete the highlighted comment?</p><p align="center">';
						echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'&comment='.$comment.'&action=delete&';
						echo 'confirm=true#comments">Delete comment</a> | <a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'">';
						echo 'Cancel</a></p></div>';
					}
					
					// form for adding comment
					echo '<form enctype="multipart/form-data" action="'.$PHP_SELF.'?blog='.$blog.'&post='.$post;
					echo '&action=addcomment#comments" method="post" name="addcomment" id="addcomment" class="blog_form"';
					
					// if there are no errors to display, hide the form
					if ($errors == NULL)
						echo ' style="display:none;"';
					echo '>';
					
					if (! $errors == NULL) {
						if (isset($_COOKIE["cookie:mpbcadmin"]))
							echo '<div class="error"><p>Please complete both fields and try again.</p></div>';
						else
							echo '<div class="error"><p>Please complete all fields and try again.</p></div>';
					}
					
					// if logged in, add users name to name field and disable it so they can't change it
					if (isset($_COOKIE["cookie:mpbcadmin"]) && $commentName == NULL)
						$commentName = $fullname;
					echo '<div class="label">Name:</div>';
					echo '<input name="commentName" size="48" value="'.stripslashes($commentName).'" ';
					if (isset($_COOKIE["cookie:mpbcadmin"]) && ! commentName == NULL)
						echo ' disabled="DISABLED" ';
					echo '/><br class="clear" />';
					
					echo '<div class="label">Comment:</div>';
					echo '<textarea name="commentText" cols="50" rows="6">'.stripslashes($commentText).'</textarea>';
					echo '<br class="clear" />';
					
					if (! isset($_COOKIE["cookie:mpbcadmin"])) {
						$publickey = "6LcuLQwAAAAAAGahrjcGmrOvVLyp7jHRFKEJegtK";
						echo '<div style="margin-left:79px">';
							echo recaptcha_get_html($publickey, null, !empty($_SERVER['HTTPS']));
						echo '</div><br />';
					}
					
					echo '<div class="label"> </div>';
						echo '<input type="submit" name="submit" value="Add comment" /> ';
						echo '<input type="reset" name="cancel" value="Cancel" ';
						echo 'onclick=\'document.getElementById("addcomment").style.display="none"\'/><br class="clear" />';
					echo '</form>';
					
					$query  = "SELECT users.name, users.surname, content, DATE_FORMAT(date, '%d %b, %Y %l:%i') as datef, author_id, ";
					$query .= "LOWER(DATE_FORMAT(date, '%p')) as meridiem, author, blog_post_id FROM users, blog_posts ";
					$query .= "WHERE users.user_id = blog_posts.author_id AND parent='$post' AND type='1' AND deleted='0' ";
					$query .= "ORDER BY date ASC";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_object($result)) {
							echo '<div class="comment"';
							if ($comment == $row->blog_post_id)
								echo ' style="background-color:#FFFFAA"';
							echo '><p>';
							if (in_array($user_id, $author_ids) || $role == 1) {
								echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post;
								echo '&comment='.$row->blog_post_id.'&action=delete#comments">';
								echo '<img src="images/icons/error.png" title="delete comment" alt="delete comment" border="0" /></a> ';
							}
							echo '<b>';
							if ($row->author_id == 0)
								echo $row->author;
							else
								echo $row->name.' '.$row->surname;
							echo '</b> <font color="gray" size="1"> - '.$row->datef.' '.$row->meridiem.'</font><br/>';
							echo $row->content.'</p></div>';
						}
						
					}
					echo '</div>';
				}
			}
			
			// if no post is specified or found, display some posts
			else {
				echo '<div class="blog_page">';
				
				// if no month is specified, display 6 most recent entries ...
				if ($month == NULL) {
					$query  = "SELECT blog_post_id, DATE_FORMAT(date, '%W %M %D %Y') as datef, title, content, comments ";
					$query .= "FROM blog_posts WHERE deleted='0' AND type='0' AND blog_id ='$blog' ORDER BY date DESC LIMIT 0, 6";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_object($result)) {
							echo '<div class="date">' . $row->datef . '</div>';
							echo '<div class="title">';
							echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'">'.$row->title . '</a>';
								if ($active == 1 && (in_array($user_id, $author_ids) || $role == 1)) {
									echo ' <a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'&action=edit">';
									echo '<img src="images/icons/edit.png" title="edit post" alt="edit post" border="0" />';
									echo '</a> ';
									echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'&action=delete">';
									echo '<img src="images/icons/deletepage.png" title="delete post" alt="delete post" border="0" />';
									echo '</a>';
								}
							echo '</div>';
							echo '<p>' . stripslashes($row->content) . '</p>';
							echo '<p><a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'#comments">';
								if ($row->comments == 1) echo 'There is 1 comment';
								else echo 'There are '.$row->comments.' comment'; if ($row->comments <> 1) echo 's';
								echo ' on this post'; if ($row->comments == 0) echo ' yet'; echo '.';
							echo '</a></p><br />';
						}
					}
					else {
						echo 'This blog doesn\'t contain any posts yet.';
						if ($active == 1 && (in_array($user_id, $author_ids) || $role == 1)) {
							echo ' <a href="'.$PHP_SELF.'?blog='.$blog.'&action=edit">You can add the first post.</a> ';
						}
					}
				}
				
				// if a month is specified, display all entries from that month
				else {
					$query  = "SELECT blog_post_id, DATE_FORMAT(date, '%W %M %D %Y') as datef, title, content, comments ";
					$query .= "FROM blog_posts ";
					$query .= "WHERE type='0' AND deleted='0' AND blog_id='$blog' AND DATE_FORMAT(date, '%m-%Y') = '$month' ORDER BY date DESC";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						while($row = mysql_fetch_object($result)) {
							echo '<div class="date">' . $row->datef . '</div>';
							echo '<div class="title">';
							echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'">'.$row->title . '</a>';
								if ($active == 1 && (in_array($user_id, $author_ids) || $role == 1)) {
									echo ' <a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'&action=edit">';
									echo '<img src="images/icons/edit.png" title="edit post" alt="edit post" border="0" />';
									echo '</a> ';
									echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'&action=delete">';
									echo '<img src="images/icons/deletepage.png" title="delete post" alt="delete post" border="0" />';
									echo '</a>';
								}
							echo '</div>';
							echo '<p>' . stripslashes($row->content) . '</p>';
							echo '<p><a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'#comments">';
								if ($row->comments == 1) echo 'There is 1 comment';
								else echo 'There are '.$row->comments.' comment'; if ($row->comments <> 1) echo 's';
								echo ' on this post'; if ($row->comments == 0) echo ' yet'; echo '.';
							echo '</a></p><br />';
						}
					}
					else
						echo 'This blog doesn\'t contain any posts yet.';
				}
				echo '</div>';
			}
			
			// if not editing the page, show the sidebar
				if ($action <> "edit") {
					echo '<div class="blog_side">';
					
					// if the user has permission to edit the page or is a site admin, show the admin tools:
					if ($active == 1 && (in_array($user_id, $author_ids) || $role == 1)) {
						echo '<div class="label" style="width:85px;"><b>Admin tools:</b></div>';
						echo '<p>';
						echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&action=edit">';
						echo '<img src="images/icons/addpage.png" title="create new blog post" alt="create new blog post" border="0" />';
						echo '</a> ';
						if (! $post == NULL) { // && $action == NULL
							echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'&action=edit">';
							echo '<img src="images/icons/edit.png" title="edit blog post" alt="edit blog post" border="0" />';
							echo '</a> ';
							echo '<a class="img_button" href="'.$PHP_SELF.'?blog='.$blog.'&post='.$post.'&action=delete">';
							echo '<img src="images/icons/deletepage.png" title="delete blog post" alt="delete blog post" border="0" />';
							echo '</a>';
						}
						
						// report number of subscribed users
						$query = "SELECT COUNT(*) FROM blog_subscriptions, users WHERE blog_subscriptions.user_id = users.user_id ";
						$query .= "AND blog_subscriptions.blog_id = '$blog' AND (users.confirmed = 1 OR users.confirmed = 3)";
						$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
						$row = mysql_fetch_row($result);
						echo '<br /><font size="1"><b>' . $row[0] . ' users subscribe to this blog</b></font>';
						echo '</p>';
					}
					
					if (! $image == NULL) {
						echo '<img class="blog_img" src="images/graphics/'.$image.'"';
						if (! $image_caption == NULL)
							echo ' alt="'.$image_caption.'" title="'.$image_caption.'" ';
						else
							echo ' alt="'.$blogname.'" title="'.$blogname.'" ';
						echo ' />';
					}
					
					echo '<div class="blog_description">';						
						if (! $description == NULL)
							echo nl2br($description);
					echo '</div>';
					
					// if we're looking at a particular post, display next and previous links to next posts
					if (! $post == NULL) {
						$query  = "SELECT blog_post_id, title, DATE_FORMAT(date, '%M %D %Y') as datef FROM blog_posts ";
						$query .= "WHERE type='0' AND deleted='0' AND blog_id='$blog' ORDER BY date DESC";
						
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						$n = 0;
						while($row = mysql_fetch_object($result)) {
							$n++;
							if ($post == $row->blog_post_id) {
								$a = $n;
							}
						}
						
						$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
						$n = 1;
						if (mysql_num_rows($result) > 0) {
							echo '<div class="blog_arrows">';
							while($row = mysql_fetch_object($result)) {
								if ($n == $a-1) {
									echo '<div class="blog_arrow">';
									echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'">';
									echo '<img src="images/layout/next.jpg" style="float:right;" border="0">Next post';
									echo '<div class="date">'.$row->datef.'</div></a></div>';
								}
								elseif ($a == 1 && $n == 1) {
									echo '<div class="blog_arrow_disabled">';
									echo '<img src="images/layout/next_disabled.jpg" style="float:right;" border="0">';
									echo 'You are at the most recent post in this blog.</div>';
								}
								elseif ($n == $a+1) {
									echo '<div class="blog_arrow">';
									echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&post='.$row->blog_post_id.'">';
									echo '<img src="images/layout/previous.jpg" border="0">Previous post';
									echo '<div class="date">'.$row->datef.'</div></a></div>';
									$l = 1;
								}
								$n++;
							}
							if ($l == NULL) {
								echo '<div class="blog_arrow_disabled">';
								echo '<img src="images/layout/previous_disabled.jpg" border="0" />';
								echo 'You are at the first <br>post in this blog.</div>';
							}
							echo '</div>';
						}
					}
					
					// display blog archive
					$query  = "SELECT DISTINCT DATE_FORMAT(date, '%M %Y') as display_month, DATE_FORMAT(date, '%m-%Y') as link_month, ";
					$query .= "COUNT(date) as number FROM blog_posts ";
					$query .= "WHERE type='0' AND deleted='0' AND blog_id='$blog' ";
					$query .= "GROUP BY DATE_FORMAT(date, '%M %Y') ORDER BY date DESC";
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
				
					$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
					if (mysql_num_rows($result) > 0) {
						echo '<h4>Archive:</h4>';
						while($row = mysql_fetch_object($result)) {
							if ($row->link_month == $month)
								echo '<div class="linkup_menu_on">';
							else
								echo '<div class="linkup_menu_off">';
							echo '<a href="'.$PHP_SELF.'?blog='.$blog.'&month='.strtolower($row->link_month).'">';
							echo $row->display_month.'</a> ('.$row->number.')</div>';
						}
					}
					echo '<br />'.$sidetext;
				echo '</div>';				
				}
		}
		
		// if there is no blog to display, display a list of active and inactive blogs 
		else {
			echo '<h3>Blogs</h3>';
			
			//if admin, show manage blogs link:
			if ($role == 1) {
				echo '<div class="calendar"><div class="options">';
					echo '<div class="selected"><a href="'.$PHP_SELF.'?action=manage">Manage blogs</a></div>';
				echo '</div></div>';
			}
			
			// if a blog id was supplied but no matching blog was found, say so
			if (! $blog == NULL)
				echo '<div class="error"><p>The blog you are looking for cannot be found. It may have been deleted.</p></div>';
			echo '<div class="blog_page">';
			
			$query  = "SELECT blog_id, name, description, image, image_thumb, image_caption FROM blogs ";
			$query .= "WHERE active='1' AND deleted='0' ";
			$query .= "ORDER BY active DESC, created DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<p>Click on the name of a blog to start reading:</p>';
				while($row = mysql_fetch_object($result)) {
					echo '<a href="'.$PHP_SELF.'?blog='.$row->blog_id.'">';
					echo '<img class="blog_thumbimg" src="images/graphics/';
					if ($row->image == NULL && $row->image_thumb == NULL)
						echo 'genericblog.jpg';
					elseif ($row->image_thumb == NULL)
						echo $row->image;
					else
						echo $row->image_thumb;
					echo '"';
					if (! $row->image_caption == NULL)
						echo ' alt="'.$row->image_caption.'" title="'.$row->image_caption.'" ';
					else
						echo ' alt="'.$row->name.'" title="'.$row->name.'" ';
					echo ' /></a>';
					echo '<div class="blog_thumbdescription">';
					echo '<h4><a href="'.$PHP_SELF.'?blog='.$row->blog_id.'">'.$row->name.'</a></h4>';
					echo '<p>'.nl2br($row->description).'</p>';
					
					if (isset($_COOKIE["cookie:mpbcadmin"]))
						showBlogSubscriptionOption($row->blog_id);
					
					echo '</div>';
				}
			}
			else
				echo '<p>No blogs have been created yet.</p>';
			
			echo '<br class="clear" />';
			
			$query  = "SELECT blog_id, name, description, image, image_caption FROM blogs WHERE active='0' AND deleted='0' ";
			$query .= "ORDER BY active DESC, created DESC";
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<p>You can also read these blogs which are no longer being updated:</p>';
				while($row = mysql_fetch_object($result)) {
					echo '<h4><a href="'.$PHP_SELF.'?blog='.$row->blog_id.'">'.$row->name.'</a></h4>';
				}
				
			}
			echo '</div>';
			echo '<div class="blog_side">';
			
			//show most recent blog posts
			$query  = "SELECT blogs.blog_id, blogs.name AS blogname, blog_post_id, title, DATE_FORMAT(date, '%d %b %y') as datef ";
			$query .= "FROM blogs, blog_posts ";
			$query .= "WHERE type='0' AND blogs.blog_id = blog_posts.blog_id ";
			$query .= "AND blog_posts.deleted='0' AND blogs.deleted='0' ";
			$query .= "ORDER BY date DESC LIMIT 0, 6";
			
			$result = mysql_query($query) or die ('Error in query: $query. ' . mysql_error());
			if (mysql_num_rows($result) > 0) {
				echo '<h4>Latest blog posts</h4>';
				while($row = mysql_fetch_object($result)) {
					echo '<p class="blog_recent"><font size="1px"><b>'.$row->blogname.'</b></font><br/>';
					echo '<a href="'.$PHP_SELF.'?blog='.$row->blog_id.'&post='.$row->blog_post_id.'">'.$row->title.'</a>';
//					echo ', <font size="1px">'.$row->datef.'</font>';
//					echo '<font size="1px"> &nbsp; </font>';
					echo '</p>';
				}
				echo '<br />';
			}
			echo '</div>';
		}
	}

	
	

?>
