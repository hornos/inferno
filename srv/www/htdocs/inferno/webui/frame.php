<?php
  require_once( '/home/inferno/webui/Packages/WebApp/0.3/config.php' );
  if( ! WA_Session::start() ) {
    WA_Session::jsredirect( 'index.php' );  
  }

  if( ! WA_Session::checklogin() ) {
    WA_Session::logout();
    WA_Session::stop();
    WA_Session::jsredirect( 'index.php' );
  }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>

<head>
  <title>Inferno</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF8">
</head>

<frameset rows="85, *" marginwidth="0" marginheight="0" frameborder="0">
  <frame src="header.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no">
  <frameset cols="150, *" marginwidth="0" marginheight="0" frameborder="0">
    <frame src="menu.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="no">
    <frame src="site.php" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" name="site">  
  </frameset>
</frameset>
	       
</html>
