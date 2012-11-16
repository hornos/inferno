<?php
  require_once( '/home/inferno/webui/Packages/WebApp/0.3/config.php' );

  if( ! WA_Session::start() ) {
    WA_Session::jsredirect( 'index.php' );  
  }

  WA_Session::logout();
  WA_Session::stop( false ); 
  WA_Session::jsredirect( 'index.php' );
?>
