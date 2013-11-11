<?php

  // test server sessions
  // session_save_path('C:\xampp\tmp');
  
  // live server sessions
  // session_save_path('/var/lib/php5');
  
  //  session_start();
  
  include 'includes/connect.php'; 
  include 'includes/functions.php';
  include 'includes/ckeditor/ckeditor.php';
  
  //find out the mode
  $mode = $_GET['mode'];
  
  // if a cookie is set, get corresponding user's details 
  if (isset($_COOKIE["cookie:mpbcadmin"])) {
    $uid = $_COOKIE['cookie:mpbcadmin'];
    $query = "SELECT name, surname, role, confirmed, user_id FROM users 
              WHERE user_id = $uid";
    $result = mysql_query($query) or die ("Error: $query. ".mysql_error());
    
    while ($row = mysql_fetch_assoc($result)) {
      $fullname = $row['name'].' '.$row['surname'];
      $role = $row['role'];
      $user_id = $row['user_id'];
      $confirmed = $row['confirmed'];
    }
  }

  // if the server requested has youth. but the URI does not, redirect to   
  // the file they wanted.
  if (strpos($_SERVER['HTTP_HOST'], 'youth.') !== false && 
      strpos($_SERVER['REQUEST_URI'], '/youth') === false) {
        header("Location: /youth/".$_SERVER['REQUEST_URI']);
  }

  // try to find a page name based on the main part of the URI.
  $uriParts = explode('/', $_SERVER['REQUEST_URI']);
  foreach($uriParts as $uriPart)
  {
    $positionOfQM = strpos($uriPart, '?');
    if ($positionOfQM !== false)
      $uriPart = substr($uriPart, 0, $positionOfQM);
    if (!empty($uriPart))
      $pageFromURI = $uriPart;
  }

  // try to find a page name using a blank url variable
  // (like index.php?pagename)
  foreach($_GET as $possiblePagename => $shouldBeBlank)
    if ($shouldBeBlank == '' && $possiblePagename != 'page')
    {
      $page = $possiblePagename;
      break;
    }

  // if we can't find a page name using the above, try using page url 
  // variable (like index.php?page=pagename)
  if (empty($page))
    if (!empty($_GET['page']))
      $page = $_GET['page'];

  if (empty($page) || $page == 'loggedin')
    $page = $pageFromURI;
  else if (!empty($pageFromURI) && $page != $pageFromURI && 
           $page != 'loggedin')
  {
    // we have a page name from the URI and ALSO a different page name 
    // from the query string. Redirect to use the one in the query string 
    // as a URI.
    header("Location: /{$page}");
    die;
  }

  // If there is no page defined by any of the methods above and we're not 
  // trying to create a new page, use home.
  if (empty($page) || $page == 'index.php')
      if ($mode != 'edit')
        $page = 'home';

  if ($page == 'login') {
	  if (!$_SERVER['HTTPS'] && $_SERVER['SERVER_NAME'] != 'mountpleasantchurch.local')
	    header('Location: https://'.$_SERVER['SERVER_NAME'].'/login');
  }
  else if ($page == 'file_download')
  {
    include_once('includes/download.php');
    die;
  }
	
  //find out the page's content_id
  $query = "SELECT content_id FROM content WHERE shortname = '$page'";
  $result = mysql_query($query);
  while($row = mysql_fetch_object($result))
    $content_id = $row->content_id;
  
  //find out the page's parent
  $query = "SELECT parent FROM content WHERE shortname = '$page'";
  $result = mysql_query($query);
  while($row = mysql_fetch_object($result))
    $page_parent = $row->parent;
    
  // working code for translations, not implemented yet
  $lan = 2;
                
  $querya = "SELECT content_id FROM content WHERE shortname = '$page'";
  $result = mysql_query($querya) or die ('Error: $querya. '.mysql_error());
  while($row = mysql_fetch_object($result))
    $content_id = $row->content_id;
    
  $queryb = "SELECT content, title FROM translations 
             WHERE content_id = '$content_id' AND language_id = '$lan'";
  $result = mysql_query($queryb) or die ('Error: $queryb. ' . mysql_error());
  if (mysql_num_rows($result) > 0)
    while($row = mysql_fetch_object($result)) {
      $content = $row->content;
      $name   = $row->title;
    }
  else {
    $queryc = "SELECT content, editor, name FROM content 
               WHERE shortname = '$page'";
    $result = mysql_query($queryc) or die ('Error: $queryc. '.mysql_error());
    if (mysql_num_rows($result) > 0) {
      if ($lan <> 2)
        $notranslation = 1;
      while($row = mysql_fetch_object($result)) {
        $content = $row->content;
        $editor  = $row->editor;
        if ($content_id <> 1)
          $name = $row->name . ' - ';
      } // while
    } // if
    else
      if ($mode <> 'edit') {
        $error = 404;
        header("HTTP/1.0 404 Not Found");
        $name  = 'Page not found - ';
      } // if
  } //else
      
?>
        
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $name ?> Mount Pleasant Baptist Church, Northampton</title>
<meta name="Author" content="Mount Pleasant Baptist Church" />
<meta name="Keywords" content="Church, Northampton, Baptist, alpha, God, 
Jesus, Holy, Spirit, Kettering Road, Paul, Lavender, service, services, Mount, 
Pleasant, town, centre, Open, Door, Centre, Micky, Munroe" />
<meta name="Description" content="Mount Pleasant is a large and friendly 
Baptist church, situated in a busy downtown area of Northampton, UK." />
<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="icon" href="images/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" media="all" type="text/css" href="/site.css" />
<script src="includes/functions.js" type="text/javascript" ></script>
<script src="includes/jquery-1.6.1.min.js" type="text/javascript" ></script>
<?php 
  if ($page == 'link_up')
    echo '<link rel="alternate" type="application/rss+xml" 
                href="feeds/linkup.php" 
                title="Mount Pleasant Baptist Church Link-Up Magazine" />';
  elseif ($page == 'sunday_messages')
    echo '<link rel="alternate" type="application/rss+xml" 
                href="feeds/sunday_messages.php" 
                title="Mount Pleasant Baptist Church Sunday Messages" />';

  // if on live server, include Google Analytics code insert
  if ($_SERVER['SERVER_NAME'] == 'mountpleasantchurch.com' || 
      $_SERVER['SERVER_NAME'] == 'www.mountpleasantchurch.com') {
  ?>
  <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-6015265-1']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
  <?php
  } // if (live server)                
  ?>
</head>

<body bgcolor="#CCCCCC">
  <div class="site">
    <a href="/">
      <img src="/images/layout/banner<? echo rand(1, 6); ?>_2012.jpg" 
           border="0" width="738" height="200" />
    </a>
        <?php
          if ($confirmed == '3') {
            // user needs to reset temporary password
            echo '<div class="menu">  </div>';
            echo '<div class="page">';
            echo '<h3><img src="images/icons/key.png" title="login" 
                       alt="key icon" /> Change password</h3>';
            echo '<p>The password you used to log in is a temporary one. You
                  must now change the password before you can continue using 
				  the site. Please enter your new password below.</p>';
            if ($_POST['submit']) {
              // they've submitted the form so check the new password
              $password = $_POST['password'];
              $confirm  = $_POST['confirm'];
                if ($password == NULL) {
                  $error1  = 1;
                  $error   = 1;
                  $confirm = NULL;
                } // if
                if ($confirm == NULL) {
                  $error1   = 1;
                  $error    = 1;
                  $password = NULL;
                } // if
                if ($password <> NULL && $confirm <> NULL)
                  if ($password <> $confirm) {
                    $error1   = 1;
                    $password = NULL;
                    $confirm  = NULL;
                    $error2   = 1;
                    $error    = 1;
                  } // if
                // report error if there was one
                if ($error == 1) 
                  if ($error2 == 1)
                    echo '<div class="error">The passwords you gave do not 
                          match. Please try again.</div><br/>';
                  elseif ($error1 == 1)
                    echo '<div class="error">Please enter your new password 
                          and then press submit.</div><br/>';
                // otherwise, update the database
                else {
                  $query = "UPDATE users SET confirmed='1',
                            password='$password' WHERE user_id='$user_id'";
                  $result = mysql_query($query) or die 
                                            ("Error: $query. " . mysql_error());
              
                  echo '<br /><div class="confirm"><p>Thank you, your password 
                        has been changed. <a href="'.$_SERVER['PHP_SELF'].'">
                        Click here to continue using the site.</a> (This will 
						happen automatically in a few seconds.)</p>';
                  echo '<meta http-equiv="refresh" content="5;
                              url='.$_SERVER['PHP_SELF'].'"></div><br />'; 
                } // else
              } // if
              else
                $error = 1;
              
              // othewise, show the change password form
              if ($error <> NULL) {
                echo '<form method="post" action="' . $_SERVER['PHP_SELF'].'">';
                echo '<div class="label">Password:</div>
                      <input type="password" name="password" size=30" 
                             value="'.$password.'"';
                if ($error1 == 1) echo ' style="background-color:#FFAFA4"'; 
				echo '><br class="clear" />';
                echo '<div class="label">Confirm password:</div>
                      <input type="password" name="confirm" size=30" 
                             value="'.$confirm.'"';
                if ($error1 == 1) echo ' style="background-color:#FFAFA4"';
                echo '><br class="clear" /><br />';
                echo '<p class="submit">
                      <input type="submit" name="submit" value="submit"> 
                      <input type="reset" name="reset" value="reset"></p>';
                echo '</form>';
          } // if
          echo '</div>';
        } // if (password needs resetting)
        else {
?>
    <div class="menu"> <?php draw_menu(0); ?> </div>
<?php
          if ($mode == 'delete') {
            // user (probably) wants to delete the page
            if ($_GET['confirm'] == 'true') {
              // we've confirmed, so delete
              echo '<div class="page">Deleting page...</div>';
              $query = "DELETE FROM content WHERE shortname='$page'";
              $result = mysql_query($query) or die 
                                              ("Error: $query. ".mysql_error());
              echo '<meta http-equiv="refresh" content="0;
                     url='. $_SERVER['PHP_SELF'] .'">';
            } // if
            else {
              // we haven't confirmed yet, so check
              echo '<div class="page">';
              echo '<div class="warning">';
              echo '<p>Are you sure you want to delete this page from the 
                    site? This action cannot be undone.</p>';
              echo '<p align="center"><a href="'.$page.
                   '?mode=delete&confirm=true">Delete page</a> | ';
              echo '<a href="'.$page.'">Cancel</a></p></div>';
              
              // identify all the [[custom codes]] used in the content
              preg_match_all("@[\[]{2}.+[\]]{2}@", 
                             $content, $codes);
              // identify what type of split the page has, if any
              foreach ($codes as $link)
                foreach ($link as $alink)
                  if ($alink == '[[side]]')
                    $divide = 1;
                  elseif ($alink == '[[whiteside]]')
                    $divide = 2;
              // render the appropriate divs, adding content as we go
              if ($divide == 1) {
                $content = explode("[[side]]", $content);
                echo '<div class="content">';
                  render_content($content[0]);
                echo '</div>';
                echo '<div class="side">';
                  render_content($content[1]);
                echo '</div>';
                if ($content[2] <> NULL) {
                  echo '<div class="side">';
                    render_content($content[2]);
                    echo '</div><br class="clear" />';
                } // if
                else
                  echo '<br class="clear" />';
              }
              elseif ($divide == 2) {
                $content = explode("[[whiteside]]", $content);
                echo '<div class="content">';
                  render_content($content[0]);
                echo '</div>';
                echo '<div class="whiteside">';
                  render_content($content[1]);
                echo '</div>';
                if ($content[2] <> NULL) {
                  echo '<div class="whiteside">';
                    render_content($content[2]);
                  echo '</div>';
                } // if
                else
                  echo '<br class="clear" />'; 
                if ($content[3] <> NULL) {
                  echo '<div class="whiteside">';
                    render_content($content[3]);
                  echo '</div>';
                } // if
                else
                  echo '<br class="clear" />'; 
                if ($content[4] <> NULL) {
                  echo '<div class="whiteside">';
                    render_content($content[4]);
                  echo '</div><br class="clear" />';
                } // if
                else
                  echo '<br class="clear" />'; 
              } // if
              else {  
                echo '<div class="page">';          
                  render_content($content);
                echo '</div>';
              } // else
              echo '</div>';
            } // else
          } // if (deleting page)
          
          elseif ($mode == 'edit') {
            // user wants to edit the page
            if ($_POST['submit']) {
              // form submitted so get the details
              $content = $_POST['content'];
              $parent = $_POST['parent'];
              $pagename = $_POST['pagename'];
              $weight = $_POST['weight'];
              $editinguser = $_POST['editinguser'];
              if ($_POST['in_nav'] == on)
                $in_nav = 1;
              else
                $in_nav = 0;
              // create shortname from the page title
              $shortname = strtolower(preg_replace("/ /", "_", $pagename));
              $allowed = "/[^a-z\\_]/i";
              $shortname = preg_replace($allowed,"",$shortname);
              
              if ($page == NULL) {
                // new page, so insert into database
                echo '<div class="page">Creating page...</div>';
                $query = "INSERT INTO content(name, shortname, parent, in_nav, 
                                              weight, content, editor)
                          VALUES('$pagename', '$shortname', '$parent', 
                                 '$in_nav', '$weight', '$content', 
                                 '$editinguser')";
              } // if
              else {
                // existing page, so update database record
                echo '<div class="page">Updating page...</div>';
                if ($role == 1) {
                  // if admin, can update more fields
                  $query = "UPDATE content SET content='$content', 
                            parent='$parent', in_nav='$in_nav', 
                            weight='$weight', editor='$editinguser' 
                            WHERE shortname='$page'";
                }
                else {
                  $query = "UPDATE content SET content='$content' 
                            WHERE shortname='$page'";
                }
              } // else
              $result = mysql_query($query) or die 
                                            ("Error: $query. " . mysql_error());
              // go to the page (if just created a new page, get the new 
              // shortname and put it into $page first
              if ($page == NULL)
                $page = $shortname;
              echo '<meta http-equiv="refresh" content="0;url='.$page.'">';
            } // if (edit page form submitted)
            else {
              // show edit page form
              echo '<div class="page">';
              echo '<form name="editpage" method="post" action="';
              if ($page == NULL) # creating new page
                echo '?mode=edit';
              else # editing existing page
                echo $page.'?mode=edit';
              echo '">';
              echo '<h3>';
            if ($page == NULL) # creating new page
              echo 'Add ';
            else  # editing existing page
              echo 'Edit ';
            echo 'page ';
            if ($role == 1) {
              // administrators have more complex form so show help button
              echo '<img src="images/icons/help.png" alt="help icon" 
                         title="help" border="0" 
						 onClick=\'document.getElementById("addpagepopup").style
                         .display="block"\'/></h3>';
              // get current page details to display
              $query = "SELECT content_id, name, parent, in_nav, weight
                        FROM content WHERE shortname='$page'";
              $result = mysql_query($query) or die
                                            ('Error: $query. ' . mysql_error());
              while($row = mysql_fetch_object($result)) {
                $name = $row->name;
                $pageparent = $row->parent;
                $in_nav = $row->in_nav;
                $weight = $row->weight;
              } // while
              
              // page name input, disabled if editing exiting page
              echo '<div class="label">Page name:</div>
                    <input name="pagename" maxlength="30" width="30" 
                           value="'.$name.'" ';
              if ($page <> NULL)
                echo 'disabled="DISABLED" />
                      <input name="pagename" maxlength="30" width="30" 
                             value="'.$name.'" type="hidden" />
                      <br class="clear" />';
              else
                echo ' /><br class="clear" />';   
                
              // page parent selection box
              $query = "SELECT content_id, name FROM content 
                        WHERE parent=0 ORDER BY weight DESC";
              $result = mysql_query($query) or die 
			                                ('Error: $query. ' . mysql_error());
              // check if records were returned
              if (mysql_num_rows($result) > 0) {
                echo '<div class="label">Section:</div> <select name="parent">';
                echo '<option value="0" ';
                  if ($pageparent == 0)
                    echo ' SELECTED';
                echo '>Top level</option>';
                while($row = mysql_fetch_object($result)) {
                  echo '<option value="' . $row->content_id . '"';
                  if ($pageparent == $row->content_id)
                    echo ' SELECTED';
                  echo '>' . $row->name . '</option>';
                } // while
                echo '</select><br class="clear" />';
              } // if (some paage parents exist in database
              else
                echo '<p>There are no categories in the database!</p>
                      <br class="clear" />';
                      
              // include in menu checkbox
              echo '<div class="label">Include in menu:</div>
                    <input type="checkbox" name="in_nav" ';
              if ($in_nav == 1)
                echo ' checked="CHECKED" ';
              echo '/><br class="clear" />';
              
              // list of existing page weights to guide user
              echo '<div id="weightspopup" name="weightspopup" class="popup">';
              echo '<h4>Page weights</h4>';
              echo '<p><i>This list shows the weight of the existing menu 
                    items. Use it to help work out the desired weight for your 
					new page.</i></p>';
              list_menu(0);
              // link to close popup
              echo '<a href="#"
                       onClick=\'document.getElementById("weightspopup")
                                 .style.display = "none"\'>Close</a>';
              echo '</div>';
              
              // weight input field
              echo '<div class="label">Weight:</div>
                    <input name="weight" width="4" maxlength="3" 
                     value="'.$weight.'"/> ';
              // link to open popup
              echo '<a href="#" 
                       onClick=\'document.getElementById("weightspopup")
                                 .style.display="block"\'>';
              echo 'Show existing page weights</a><br class="clear" />';
              
              // user with permission to edit page content selection box
              echo '<div class="label">Editing user:</div>';
              // get non-admin users from database
              $query  = "SELECT user_id, name, surname FROM users
                         WHERE role=2 OR role=3 OR role=4 AND confirmed=1 
                         ORDER BY surname ASC";
              $result = mysql_query($query) or die
                                            ('Error: $query. ' . mysql_error());
              if (mysql_num_rows($result) > 0) {
                echo '<select name="editinguser">';
                echo '<option value="#">None</option>';
                while($row = mysql_fetch_object($result)) {
                  echo '<option value="' . $row->user_id . '"';
                  if ($editor == $row->user_id)
                    echo ' SELECTED';
                  echo '>' . $row->name . ' ' . $row->surname . '</option>';
                } // while
                echo '</select> <font size="1">Choose a user who will have 
                      permission to edit this page.</font><br class="clear" />';
              } // if (users in database)
              else
                echo 'There are no users in the database!<br />';
            } // if (role is administrator)
            else
              echo '</h3>';
              
            // show CKEditor with content
						$CKEditor = new CKEditor();
						$CKEditor->basePath = '/includes/ckeditor/';
						$CKEditor->editor('content', stripslashes($content));
            
            echo '</div>';
          } // else (show edit page form)
        } // if (mode == edit)

        else {
          // just show the page!
          if ($error == 404) {
            echo '<div class="page">';
            echo '<h3>Page not found</h3>';
            echo '<p>Unfortunatly, the page you requested hasn\'t been found 
                     on the site. If you followed a link to this page from
                     somewhere else on the site, please let us know by 
                     emailing the webmaster at: <a 
                     href="mailto:webmaster@mountpleasantchurch.com">
                     webmaster@mountpleasantchurch.com</a>.</p>';
            echo '<p>To go back to where you came from, click <a 
                  href="javascript:history.go(-1)">here</a>. To go to the home 
                  page, click <a href="/">here</a>.</p>';
            echo '</div>';
          } // if (page not found)
          else {
            // identify all the [[custom codes]] used in the content
            preg_match_all("@[\[]{2}.+[\]]{2}@",
                           $content, $codes);
            // identify what type of split the page has, if any
            foreach ($codes as $link)
              foreach ($link as $alink)
                if ($alink == '[[side]]')
                  $divide = 1;
                elseif ($alink == '[[whiteside]]')				
                  $divide = 2;

            // render the appropriate divs, adding content as we go
            if ($divide == 1) {
              $content = explode("[[side]]", $content);
              echo '<div class="content">';
                render_content($content[0]);
              echo '</div>';
              echo '<div class="side">';
                render_content($content[1]);
              echo '</div>';
              if ($content[2] <> NULL) {
                echo '<div class="side">';
                  render_content($content[2]);
                  echo '</div><br class="clear" />';
              } // if
              else
                echo '<br class="clear" />';
            }
            elseif ($divide == 2) {
              $content = explode("[[whiteside]]", $content);
              echo '<div class="content">';
                render_content($content[0]);
              echo '</div>';
              echo '<div class="whiteside">';
                render_content($content[1]);
              echo '</div>';
              if ($content[2] <> NULL) {
                echo '<div class="whiteside">';
                  render_content($content[2]);
                echo '</div>';
              } // if
              else
                echo '<br class="clear" />'; 
              if ($content[3] <> NULL) {
                echo '<div class="whiteside">';
                  render_content($content[3]);
                echo '</div>';
              } // if
              else
                echo '<br class="clear" />'; 
              if ($content[4] <> NULL) {
                echo '<div class="whiteside">';
                  render_content($content[4]);
                echo '</div><br class="clear" />';
              } // if
              else
                echo '<br class="clear" />'; 
            } // if
            else {  
              echo '<div class="page">';          
                render_content($content);
              echo '</div>';
            } // else
          } // else (page found)
        } // else (no mode)
      } // else (password doesn't need resetting)
           
      include('includes/footer.php'); 
      echo '</div>';

      // show page administration tools to relevent users
      if (isset($_COOKIE["cookie:mpbcadmin"]) && $role == 1 
          && $mode <> 'delete' || $editor == $user_id && $user_id <> NULL) {
?>
        <div class="controlpanel">
          <h4>Administration Tools</h4>
            <div class="buttons">
              <?php
                if ($mode == 'edit') {
              ?>
              <div class="iconlink">
                <img src="/images/icons/save.png" title="save changes" 
                     alt="save changes icon" border="0" />
                <div class="text">
                  <input type="submit" name="submit" value="Save changes" 
                         class="admin_button">
                </div>
              </div><br class="clear" />
              <div class="iconlink">
                <a href="/<?=(($page == NULL)?'':$page)?>">
                  <img src="/images/icons/cancel.png" title="cancel changes" 
				       alt="cancel changes icon" border="0" />
                </a>
                <div class="text">
                  <a href="/<?=(($page == NULL)?'.':$page)?>">Cancel changes</a>
                </div>
              </div><br class="clear" />
              <?php
                } // if (editing the page)
                else {
              ?>
              <div class="iconlink">
                <a href="/<?=$page?>?mode=edit">
                  <img class="img_button" src="/images/icons/edit.png" 
                       title="edit page" alt="edit icon" border="0" />
                </a>
                <div class="text">
                  <a href="/<?=$page?>?mode=edit">Edit this page</a>
                </div>
              </div><br class="clear" />
              <?php
                if ($role == 1) {
                  // administrators get more options
              ?>
              <div class="iconlink">
                <a href="/<?=$page?>?mode=delete">
                  <img class="img_button" src="/images/icons/deletepage.png" 
				       title="delete page" alt="delete page icon" border="0" />
                </a>
                <div class="text">
                  <a href="/<?=$page?>?mode=delete">Delete this page</a>
                </div>
              </div><br class="clear" />
              <div class="iconlink">
                <a href="/?mode=edit">
                  <img class="img_button" src="/images/icons/addpage.png" 
                       title="add page" alt="add page icon" border="0" />
                </a>
              <div class="text">
                <a href="/?mode=edit">Add a new page</a>
              </div>
            </div><br class="clear" />
            <?php
              } // if (administrator)
            } // else (not editing page)
            echo '</div>';
            if ($mode == 'edit')
              echo '</form>';
            echo '</div>';
      } // if (showing admin tools)
?>
</body>
</html>

<?php 
// output server information for debugging
#  echo $_SERVER['HTTP_HOST'].'<br/>'.
#       $_SERVER['PHP_SELF'].'<br/>'.
#       $_SERVER['SERVER_NAME'];

// hidden help information for administrators editing page
if ($role == 1) {
?>
<div id="addpagepopup" name="addpagepopup" class="popup">
  <h4>Help</h4><br />
  <p><b>Page name</b><br>The name of the page as it should appear in links and 
  the menu.</p>
  <p><b>Section</b><br>The section of the site you want the new page to be 
  included in.</p>
  <p><b>Include in menu</b><br>Check this box if you want the page to have a 
  link in the site's menu system.</p>
  <p><b>Weight</b><br>The priority of the page in the menu. The higher the 
  number, the higher the item will appear in the menu. Click 'Show existing 
  page weights' to help you pick the right number.</p>
  <p><a href="#" onClick=
  'document.getElementById("addpagepopup").style.display = "none"'>Close</a></p>
</div>
<? } ?>
