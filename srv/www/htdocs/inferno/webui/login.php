<?php
  require_once( '/home/inferno/webui/Packages/WebApp/0.3/config.php' );

  if( empty( $_POST['user'] ) || empty( $_POST['pass'] ) ) {
    WA_Session::jsredirect( 'index.php' );
  }

  $user = coString::subalpha( $_POST['user'] );
  $pass = md5( coString::trunc( $_POST['pass'] ) );

  unset( $_POST['user'] );
  unset( $_POST['pass'] );
  
  if( ! WA_Session::start() ) {
    WA_Session::jsredirect( 'index.php' );  
  }

  if( WA_Session::login( $user, $pass ) ) {
    WA_Session::jsredirect( 'frame.php' );
  }

  WA_Session::stop( false );
  WA_Session::jsredirect( 'index.php', 3 );
 
?>
