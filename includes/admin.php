<?php 

global $page;
global $role;

if (isset($_COOKIE["cookie:mpbcadmin"])) {
  $tool = $_GET['tool'];

  if ($tool == NULL) {
    echo '<h3>Administration</h3>';
    echo 'Select a service from the list below:';
    printadminmenu();
  }
  else {
    echo '<p class="right"><a href="'.$_SERVER['REDIRECT_URL'].'">Administration tools</a></p>';
    include('includes/admin/'.$tool.'.php');
  }
}
else {
	echo '<p>Please <a href="/login?target='.urlencode($_SERVER['REQUEST_URI']).'">login</a> to continue.</p>';
}
?>
