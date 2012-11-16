<?php

class WA_Session {

public static function jsredirect( $page ) {
	echo '<script type="text/javascript">';
	echo 'window.location = "'.$page.'"';
	echo '</script>';
}

/*
public static function logout() {
  $db	 = WA_Session::init_db();
  $user	 = $_SERVER['PHP_AUTH_USER'];
  $timenow = WA_String::timenow();
  
  $query  = 'UPDATE '.$_SESSION['USER_TABLE'];
  $query .= ' SET isonline = '.WA_String::sqlfmt( 'f' );
  $query .= ', logouttime = '.WA_String::sqlfmt( $timenow );
  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
  try {
    $rs = $db->Execute( $query );
  }
  catch( exception $e ) {
	print_query_error( 'Error: ', $e );
  	exit;
  }
  
  session_unset();
  session_destroy();
  echo "Close the browser and login again";
  WA_Session::jsredirect( 'logout.html' );
}
*/

public static function sql_logout( $user, $sleep = 0 ) {
  $lsleep = $sleep;
  
  if( WA_String::nzstr( $user ) ) {
	$db	 = WA_Session::init_db();
	$timenow = WA_String::timenow();
  
	$query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	$query .= ' SET isonline = '.WA_String::sqlfmt( 'f' );
	$query .= ', logouttime = '.WA_String::sqlfmt( $timenow );
	$query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
	try {
  	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	}
  }
  
  session_unset();
  session_destroy();
  
  sleep( $lsleep );
  WA_Session::jsredirect( 'login.php' );
  exit;
}



/*
public static function login() {
  $db	 = WA_Session::init_db();
  $user	 = $_SERVER['PHP_AUTH_USER'];
  
  // login handling
  $query  = 'SELECT * FROM '.$_SESSION['USER_TABLE'];
  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );

  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
  	print_query_error( 'Error: ', $e );
  	exit;
  }
  if( $rs->RecordCount() < 1 ) {
	print_info( 'Authorization failed' );
    exit;
  }
  
  $row = $rs->FetchRow();
  if( $row['isonline'] == 't' ) {
	// check grace
	$grace = $row['gracetime'];
	$lact  = strtotime( $row['lastactiontime'] );
	$now   = strtotime( 'now' );
	$delta = $now - $lact;
	if( $delta > $grace ) {
	  echo 'Login grace time has expired<br>';
	  WA_Session::logout();
	  exit;
	}
	else {
	  $timenow = WA_String::timenow();
	  $query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	  $query .= ' SET lastactiontime = '.WA_String::sqlfmt( $timenow );
	  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
	  try {
		$rs = $db->Execute( $query );
	  }
	  catch( exception $e ) {
  		print_query_error( 'Error: ', $e );
  		exit;
	  }
	  echo "Login was successfull!";
	  echo '<script type="text/javascript">';
	  echo 'window.location = "index.php"';
	  echo '</script>';
	}
  }
  else {
	$timenow = WA_String::timenow();
	$query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	$query .= ' SET isonline = '.WA_String::sqlfmt( 't' );
	$query .= ', logintime = '.WA_String::sqlfmt( $timenow );
	$query .= ', lastactiontime = '.WA_String::sqlfmt( $timenow );
	$query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
  	  print_query_error( 'Error: ', $e );
  	  exit;
	}
	
	echo "Login was successfull!";
	WA_Session::jsredirect( 'index.php' );
  }  
}
*/


public static function sql_login( $user, $pass ) {
  $db	 = WA_Session::init_db();
  
  // login handling
  $query  = 'SELECT * FROM '.$_SESSION['USER_TABLE'];
  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );

  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
  	print_query_error( 'Error: ', $e );
  	return false;
  }
  if( $rs->RecordCount() < 1 ) {
	print_info( 'Authorization failed' );
    return false;
  }
  
  $row = $rs->FetchRow();
  
  // password check
  $sqlpass = $row['passwd'];
  if( $sqlpass != $pass ) {
	return false;
  }
  
  if( $row['isonline'] == 't' ) {
	// check grace
	$grace = $row['gracetime'];
	$lact  = strtotime( $row['lastactiontime'] );
	$now   = strtotime( 'now' );
	$delta = $now - $lact;
	if( $delta > $grace ) {
	  // echo 'Login grace time has expired<br>';
	  WA_Session::sql_logout( $user );
	}
	else {
	  $timenow = WA_String::timenow();
	  $query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	  $query .= ' SET lastactiontime = '.WA_String::sqlfmt( $timenow );
	  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
	  try {
		$rs = $db->Execute( $query );
	  }
	  catch( exception $e ) {
  		print_query_error( 'Error: ', $e );
  		return false;
	  }
	  echo "Login was successfull!";
	  return true;
	}
  }
  else {
	$timenow = WA_String::timenow();
	$query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	$query .= ' SET isonline = '.WA_String::sqlfmt( 't' );
	$query .= ', logintime = '.WA_String::sqlfmt( $timenow );
	$query .= ', lastactiontime = '.WA_String::sqlfmt( $timenow );
	$query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
  	  print_query_error( 'Error: ', $e );
  	  return false;
	}
	
	echo "Login was successfull!";
	// WA_Session::jsredirect( 'index.php' );
	return true;
  }  
}



public static function checklogin() {
  if( ! isset( $_SESSION['LOGIN_USER'] ) ) {
	WA_Session::sql_logout( '' );
  }
  if( WA_String::zstr( $_SESSION['LOGIN_USER'] ) ) {
	WA_Session::sql_logout( '' );  
  }
  
  $user = $_SESSION['LOGIN_USER'];
  $db	 = WA_Session::init_db();
  // $user	 = $_SERVER['PHP_AUTH_USER'];
  
  // login handling
  $query  = 'SELECT * FROM '.$_SESSION['USER_TABLE'];
  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );

  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
  	print_query_error( 'Error: ', $e );
	WA_Session::sql_logout( '' );  
  }
  if( $rs->RecordCount() < 1 ) {
	print_info( 'Authorization failed' );
	WA_Session::sql_logout( '' );
  }
  
  $row = $rs->FetchRow();
  if( $row['isonline'] == 't' ) {
	// check grace
	$grace = $row['gracetime'];
	$lact  = strtotime( $row['lastactiontime'] );
	$now   = strtotime( 'now' );
	$delta = $now - $lact;
	if( $delta > $grace ) {
	  // echo 'Login grace time has expired';
	  WA_Session::sql_logout( $user );
	}
  }
  else {
	WA_Session::sql_logout( $user );
  }
}


public static function authorize( $module ='', $opts = array() ) {
  if( $module == '' ) {
	// exit;
	WA_Session::sql_logout( '' );
  }

  if( $_SESSION['LOCKED'] ) {
	// exit;
	WA_Session::sql_logout( '' );
  }

  if( is_readable( $_SESSION['LOCKFILE'] ) ) {
	$lckstr = file_get_contents( $_SESSION['LOCKFILE'] );
	echo $lckstr;
	WA_Session::sql_logout( '' );	
	// exit;
  }

  $db	 = WA_Session::init_db();
  // $user	 = $_SERVER['PHP_AUTH_USER'];
  if( ! isset( $_SESSION['LOGIN_USER'] ) ) {
	WA_Session::sql_logout( '' );
  }
  if( WA_String::zstr( $_SESSION['LOGIN_USER'] ) ) {
	WA_Session::sql_logout( '' );  
  }

  $user = $_SESSION['LOGIN_USER'];
  
  // login handling
  $query  = 'SELECT * FROM '.$_SESSION['USER_TABLE'];
  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );

  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
  	print_query_error( 'Error: ', $e );
	WA_Session::sql_logout( '' );  
  }
  if( $rs->RecordCount() < 1 ) {
	print_info( 'Authorization failed' );
	WA_Session::sql_logout( '' );  
  }
  
  $row = $rs->FetchRow();
  if( $row['isonline'] == 't' ) {
	// check grace
	$grace = $row['gracetime'];
	$lact  = strtotime( $row['lastactiontime'] );
	$now   = strtotime( 'now' );
	$delta = $now - $lact;
	if( $delta > $grace ) {
	  // echo 'Login grace time has expired';
	  WA_Session::sql_logout( $user );
	}
	else {
	  $query  = 'UPDATE '.$_SESSION['USER_TABLE'];
	  $query .= ' SET lastactiontime = '.WA_String::sqlfmt( WA_String::timenow() );
	  $query .= ' WHERE userid = '.WA_String::sqlfmt( $user );
	  try {
		$rs = $db->Execute( $query );
	  }
	  catch( exception $e ) {
  		print_query_error( 'Error: ', $e );
  		// exit;
		WA_Session::sql_logout( '' );
	  }
	}
  }
  
  // exit;
  
  // module permissions
  if( isset( $opts['vname'] ) and isset( $opts['vvalue'] ) ) {
	$vname  = $opts['vname'];
	$vvalue = $opts['vvalue'];

	$query  = 'SELECT userid,groupid,moduleid,vname,vvalue FROM '.$_SESSION['USER_TABLE'];
	$query .= ' INNER JOIN '.$_SESSION['AUTH_TABLE'].' USING(groupid) WHERE userid = '.WA_String::sqlfmt( $user );
	$query .= ' AND moduleid = '.WA_String::sqlfmt( $module );
	$query .= ' AND vname = '.WA_String::sqlfmt( $vname );
	$query .= ' AND vvalue = '.WA_String::sqlfmt( $vvalue );
	
	// echo $query;
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
  	  print_query_error( 'Error: ', $e );
	  return false;
	}
	if( $rs->RecordCount() > 0 ) {
	  return true;
	}
  }
  
  $query  = 'SELECT userid,groupid,moduleid FROM '.$_SESSION['USER_TABLE'];
  $query .= ' INNER JOIN '.$_SESSION['AUTH_TABLE'].' USING(groupid) WHERE userid = '.WA_String::sqlfmt( $user );
  $query .= ' AND moduleid = '.WA_String::sqlfmt( $module );
  $query .= ' AND vname = '.WA_String::sqlfmt( 'all' );
  $query .= ' AND vvalue = '.WA_String::sqlfmt( 'all' );
  
  // echo '<br><br>'.$query;
  
  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
    // print_query_error( 'Error: ', $e );
  	return false;
  }
  
  $db->close();
  
  if( $rs->RecordCount() < 1 ) {
    print_info( 'Authorization failed' );
    return false;
  }
  else {
	return true;
  }
}


public static function log_this( $usr, $desc, $querytxt ) {
  $time = WA_String::timenow();
  
  $query  = 'INSERT INTO '.$_SESSION['LOG_TABLE'];
  $query .= ' (userid, logtxt, query, logtime )';
  $query .= ' VALUES ('.WA_String::sqlfmt($usr).',';
  $query .= WA_String::sqlfmt($desc).',';
  $query .= WA_String::sqlfmt2($querytxt).',';
  $query .= WA_String::sqlfmt($time).')';  

  // echo $query;
  $db = WA_Session::init_db();
  
  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
    print_query_error( 'Error: ', $e );
    exit;
  }
}


public static function is_valid_arg( $arg, $argarr ) {
  $ok = false;  

  foreach( $argarr as $varg ) {
	if( $varg == $arg ) {
	  $ok = true;
	}
  }
  return $ok;
}


public static function is_sended( $id ) {
	if( isset( $_POST[$id] ) ) {
	  return true;
	}
	else if( isset( $_GET[$id] ) ) {
	  return true;
	}
	return false;
  }


  public static function get_sended( $id ) {
	if( isset( $_POST[$id] ) ) {
	  return strip_tags( $_POST[$id] );
	}
	else if( isset( $_GET[$id] ) ) {
	  return strip_tags( $_GET[$id] );
	}
	return '';
  }

  public static function get_path( $in, $f ) {
	return $_SESSION['WAROOT'].'/'.$in.'/'.$f;
  }

  public static function module_path( $m ) {
	return WA_Session::get_path( 'Modules', $m.'.php' );
  }
  
  public static function inc_module( $m ) {
	$mp = WA_Session::module_path( $m );

	if( is_readable( $mp ) ) {
	  include_once( $mp );
	}
	else {
	  echo "<br>Error loading: ".$m;
	  echo "<br>Path: ".$mp;
	}
  }
  
  public static function get_module() {
	if( WA_Session::is_sended( 'm' ) ) {
	  WA_Session::inc_module( WA_Session::get_sended( 'm' ) );
	}
	else {
	  WA_Session::inc_module( 'welcome' );	
	}
  }
  
  public static function include_js( $js ) {
	$path = './' . $_SESSION['APP'] . '/' . $_SESSION['VER'] . '/js/'.$js;
	echo '<script src="' . $path  . '" type="text/javascript" language="Javascript"></script>'."\n";
  }

  public static function include_css( $css ) {
	$path = './' . $_SESSION['APP'] . '/' . $_SESSION['VER'] .'/css/'.$css;
    echo '<link rel="stylesheet" type="text/css" href="' . $path  . '" >'."\n";
  }

  public static function include_php( $php ) {
	$path = './' . $_SESSION['APP'] . '/' . $_SESSION['VER'] . '/php/' . $php;
	include_once( $path );
  }

  public static function path_img( $img ) {
	$path = './' . $_SESSION['APP'] . '/' . $_SESSION['VER'] . '/img/' . $img;
	return '<img src="' . $path  . '">';
  }
  
  public static function include_img( $img ) {
	// $path = './' . $_SESSION['APP'] . '/' . $_SESSION['VER'] . '/img/' . $img;
	// echo '<img src="' . $path  . '">';
	echo WA_Session::path_img( $img );
  }
  
  public static function valid_session() {  
	$_SESSION['valid'] = true;
  }


  public static function print_info() {
	// $infstr  = "User: <b>" . $_SERVER['PHP_AUTH_USER'] . "</b>&nbsp;&nbsp;Server: "  . $_SERVER['SERVER_NAME'];
	$infstr  = "User: <b>" . $_SESSION['LOGIN_USER'] . "</b>&nbsp;&nbsp;Server: "  . $_SERVER['SERVER_NAME'];

	$data = shell_exec( 'uptime' );
	$infstr .= "&nbsp;&nbsp;".$data;
	echo $infstr;
  }
  
  public static function init_db() {
	$db = NewADOConnection( $_SESSION['DBTYPE'] );  
    $db->Connect( $_SESSION['DBHOST'], $_SESSION['DBUSER'], $_SESSION['DBPASS'], $_SESSION['DBNAME'] );
    $db->SetFetchMode( ADODB_FETCH_ASSOC );
	return $db;
  }
  
  public static function finish() {
	$contactbar  = 'This page was generated by WebApp 2 &copy; PHP framework.';
	$contactbar .= '<br><span style="color: grey;">More information:</span>';
	WA_String::print_info( '<div class="contactbar">' . $contactbar . '</div>' );
	echo "\n</body></html>";
	exit;
  }

  public static function print_infobar( $form, $action ) {
	if( $_SESSION['INFOBAR'] ) {
	  $infobar = 'Az Ürlap állapotkódja: '.$form->getState().'&nbsp;&nbsp;Művelet: '.$action;
	  WA_String::print_spaninfo( '<div class="infobar">' . $infobar . '</div>' );
	}
  }


  public static function select_string( $a, $t ) {
	$str = 'SELECT ';
	$first = true;
	foreach( $a as $i ) {
	  if( ! $first ) {
		$str .= ', ';
	  }
	  if( is_array( $i ) ) {
		$subfirst = true;
		foreach( $i as $k => $v ) {
		  if( ! $subfirst ) {
			$str .= ', ';
		  }
		  $str .= $k . ' AS ' .$v;
		  $subfirst = false;
		}
	  }
	  else {
		$str .= $i;
	  }
	  $first = false;
	}
	$str .= ' FROM ' . $t;
	return $str;
  }


  public static function get_tops( $f ) {
	$tops = array( 'table' => $_SESSION['PERSON_TABLE'],
				 'table_ptype' => 'person',
				 'ptype' => $f->opt( 'ptype' ) );

	if( $f->isopt( 'ptype', 'student' ) ) {
#    $tops['table'] = $_SESSION['STUDENT_TABLE'];
  	  $tops['table'] = $_SESSION['PERSON_TABLE'];
  	  $tops['table_ptype'] = 'student';
  	  $tops['ptype'] = 'student';
	}
	else if ( $f->isopt( 'ptype', 'guest' ) ) {
#    $tops['table'] = $_SESSION['GUEST_TABLE'];
  	  $tops['table'] = $_SESSION['PERSON_TABLE'];
  	  $tops['table_ptype'] = 'guest';
  	  $tops['ptype'] = 'guest';
	}
  
	return $tops;
  }

  public static function gen_ipchkl( $fr, $to, $ex = array() ) {
	$ret = array();
	$fra = explode( ".", $fr );
	$toa = explode( ".", $to );
  
	$isex = false;
	if( sizeof( $ex ) ) {
	  $isex = true;
	}

	// print $fr." ".print_r($fra)." ";
	// print $to." ".print_r($toa)." ";
	
	foreach( range( $fra[2], $toa[2] ) as $mid ) {
	  if( $fra[2] == $toa[2] ) {
	    $ipr = range( $fra[3], $toa[3] );
	  }
	  else {
	    if( $mid == $fra[2] ) {
		$ipr = range( $fra[3], 254 );
	    }
	    elseif( $mid == $toa[2] ) {
		$ipr = range( 1, $toa[3] );
	    }
	    else {
		$ipr = range( 1, 254 );
	     }
	  }
	  
	// foreach( range( $fra[3], $toa[3] ) as $end ) {
	  foreach( $ipr as $end ) {
		$addr = $fra[0].'.'.$fra[1].'.'.$mid.'.'.$end;
		if( $isex ) {
		  $exists = false;
		  foreach( $ex as $exaddr ) {
			if( $addr == $exaddr ) {
			  $exists = true;
			}
		  }
	  
		  if( ! $exists ) {
			array_push( $ret, $addr );		
		  }
		}  
	    else {	
		  array_push( $ret, $addr );
		}
	  }	
	}	
	
	return $ret;
  }

}

?>
