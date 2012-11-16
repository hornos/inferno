<?php

$_SESSION['APP'] = "WebApp";
$_SESSION['VER'] = "0.2";

$_SESSION['WAROOT'] = '/home/srv/www/Packages/' . $_SESSION['APP'] . '/' . $_SESSION['VER'];
$_SESSION['INFERNO_ROOT'] = '/home/inferno';
$_SESSION['INFERNO_LOCK'] = '/home/inferno/lock';
$_SESSION['INFERNO_LOGS'] = '/home/inferno/logs';
$_SESSION['INFERNO_CERTS'] = '/home/inferno/certs';

$_SESSION['DEBUG']    = false;
$_SESSION['LOCKED']   = false;
$_SESSION['LOCKFILE'] = $_SESSION['INFERNO_LOCK'].'/webapp.lock';

$_SESSION['DBHOST'] = 'localhost';
$_SESSION['DBNAME'] = 'inferno';
$_SESSION['DBUSER'] = 'inferno';
$_SESSION['DBPORT'] = 5432;
$_SESSION['DBPASS'] = 'inferno';
$_SESSION['DBTYPE'] = 'postgres';
$_SESSION['QUERY_LIMIT'] = 1000;

$_SESSION['STUDENT_TABLE'] = 'student_tbl';
$_SESSION['PERSON_TABLE']  = 'person_tbl';
$_SESSION['GUEST_TABLE']   = 'guest_tbl';
$_SESSION['ADDRESS_TABLE'] = 'address_tbl';
$_SESSION['HOST_TABLE']    = 'host_tbl';
$_SESSION['USER_TABLE']    = 'user_tbl';
$_SESSION['WID_TABLE']= 'wid_tbl';
$_SESSION['ROOM_TABLE']    = 'room_tbl';
$_SESSION['VLAN_TABLE']    = 'vlan_tbl';
$_SESSION['AUTH_TABLE']    = 'mauth_tbl';
$_SESSION['DEVICE_TABLE']  = 'ndev_tbl';
$_SESSION['PORT_TABLE']    = 'port_tbl';
$_SESSION['RECORD_TABLE']  = 'record_tbl';
$_SESSION['LOG_TABLE']     = 'log_tbl';


$_SESSION['INFOBAR']	   = false;

$_SESSION['OPENAP'] = 'ap';
$_SESSION['APUSER'] = 'root';
$_SESSION['APPASS'] = 'root';
$_SESSION['APCMD']  = '/usr/sbin/nvram show | grep dnsmasq_lease';

$_SESSION['INDEX'] = 'index.php';

include_once( "/home/srv/www/Packages/adodb/adodb-exceptions.inc.php" );
include_once( "/home/srv/www/Packages/adodb/adodb.inc.php" );

function __autoload( $class ) {
  if( file_exists( $_SESSION['WAROOT']."/Classes/".$class.".php" ) ) {
	require_once( $_SESSION['WAROOT']."/Classes/".$class.".php" );
  }
  else if( file_exists( $_SESSION['WAROOT']."/Classes/Complex/".$class.".php" ) ) {
	require_once( $_SESSION['WAROOT']."/Classes/Complex/".$class.".php" );
  }
  else if( file_exists( $_SESSION['WAROOT']."/Forms/".$class.".php" ) ) {
	require_once( $_SESSION['WAROOT']."/Forms/".$class.".php" );  
  }
  else {
	echo "<br>Class Autoload Error: ".$class;
  }
  
}

function getPath( $in, $f ) {
  return $_SESSION['WAROOT'].'/'.$in.'/'.$f;
}

function includeModule( $module ) {
  $module_page = get_path( 'Modules', $module );

  if( is_readable( $module_page ) ) {
	include_once( $module_page );
  }
  else {
	echo "Error loading module: ".$module;
  }
  // exception
}

function getModule() {
  if( WA_Session::is_sended( 'm' ) ) {
	includeModule( WA_Session::get_module( 'm' ) );
  }

}

function valid_session() {  
  $_SESSION['valid'] = true;
}


$dict_hun = array(
  'YES' => 'Igen',
  'NO'  => 'Nem',
  'MAN' => 'Férfi',
  'WOMAN' => 'Nő',
  'SEX' => 'Nem');


function timenow() {
  return date( 'Y-m-d H:i:s T' );
}

function print_div( $s, $d ) {
  echo '<div class="'.$d.'">'.$s.'</div>';
}

function print_error( $s ) {
  print_div( $s, 'error' );
}

function print_info( $s ) {
  print_div( $s, 'info' );
}

function print_query_error( $msg, $e ) {
  print_error( $msg );
  print_error( $e->msg );
}
?>
öOD