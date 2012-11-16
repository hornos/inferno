<?php
  $module = 'welcome';
  
  if( ! isset( $_SESSION['valid'] ) ) {
	echo "Direct access is not permitted!";
	exit;
  }

  if( ! WA_Session::authorize( $module ) ) {
	exit;
  }  

  // Choose Form Action Type
//  $action = 'admin';
//  if( WA_Session::is_sended( 'action' ) ) {
//	$action = WA_Session::get_sended( 'action' );
	
//	$valid_action = array( 'admin' );
//	if( ! WA_Session::is_valid_arg( $action, $valid_action ) ) {
//	  exit;
//	}
//  }


  // Arguments
//  $opts =  array( 'action' => $action );
//  $form = new WA_AdminDBForm( $action.'_db', 'Adatbázis Karbantartás', $module, $opts );
//  $form->setAction( $action );
//  $form->Validate();
  // Form Logic Start
  
  // Debug
  // WA_Session::print_infobar( $form, $action );

  $db	   = WA_Session::init_db();
  $timenow = WA_String::timenow();

  $query  = 'SELECT userid, isonline, logintime, lastactiontime, gracetime FROM '.$_SESSION['USER_TABLE'];
  $query .= ' WHERE isonline = '.WA_String::sqlfmt( 't' );
	
  try {
	$rs = $db->Execute( $query );
  }
  catch( exception $e ) {
	print_query_error( 'Error: ', $e );
  }

  if( $rs->RecordCount() > 0 ) {
	echo '<div class="text">';
	echo '<div class="page_subtitle">Online felhasználók</div>';
	echo '<table cellpadding="3">';
	echo '<tr>';
	echo '<td class="table_title">Felhasználó</td>';
	echo '<td class="table_title">Belépés ideje</td>';
	echo '<td class="table_title">Utolsó aktivitás ideje</td>';
	echo '</tr>';	
	
	foreach( $rs as $row ) {
	  $usrid = $row['userid'];
	  $lgint = $row['logintime'];
	  $grace = $row['gracetime'];	  
	  
	  $lact  = strtotime( $row['lastactiontime'] );
	  $now   = strtotime( 'now' );
	  $delta = $now - $lact;
	  $css_class = 'green';
	  if( $delta > $grace ) {
		// echo 'Login grace time has expired';
		$css_class = 'red';
	  }
	  
	  echo '<tr>';
	  echo '<td class="'.$css_class.'">'.$row['userid'].'</td>';
	  echo '<td class="'.$css_class.'">'.$row['logintime'].'</td>';
	  echo '<td class="'.$css_class.'">'.$row['lastactiontime'].'</td>';
	  echo '</tr>';
	}
	
	echo '</table><br><br>';
	echo '</div>';
  }

?>

<div class="text">
<!--
<div class="page_title">üzenőfal</div>
-->

<div class="page_subtitle">szűrések</div>
<?php

  $url = "http://filter";
  $ctx = stream_context_create( array( 'http' => array( 'timeout' => 5 ) ) ); 
  
  $filter   = file_get_contents( $url, 0, $ctx );
  if( ! $filter ) {
    echo $url . " nem elérhető";
  }
  else {
    $denylist = preg_split( "/\n/", html_entity_decode( $filter ) );
  
    if( $denylist != FALSE ) {
	echo '<table cellpadding="3" cellspacing="3">';
	echo '<tr>';
	echo '<td class="table_title">IP</td>';
	echo '<td class="table_title">Hostname</td>';
	echo '<td class="table_title">Tiltás oka</td>';
	echo '<td class="table_title">Tiltás kezdete</td>';
	echo '<td class="table_title">Tulajdonos</td>';
	echo '</tr>';

	foreach( $denylist as $line ) {
	  if( preg_match( '//', $line ) or preg_match( '/[6-7]\..*/', $line ) ) {
		// echo $line;
		echo '<tr>';
		$line = preg_replace( '/:/', '', $line );		
		$line = preg_replace( '/^<tr>\s*<td>/', '', $line );
		// echo $line;
		$line = preg_replace( '/s*<td>s*/', '|', $line );
		// echo $line;
		$larr = preg_split( '/s*\|s*/', $line );
		// print_r( $larr );
		// $ip = preg_replace( '/:/', '', trim( $larr[0] ) );
		$iparr = preg_split( '/\ +/', $larr[0] );
		
		// return;
		$ip = trim( $iparr[0] );
		// echo $ip;
	
		// try to backsearch
		$query  = 'SELECT (name).forname AS forname, (name).surname AS surname, roomid FROM '.$_SESSION['HOST_TABLE'];
		$query .= ' INNER JOIN '.$_SESSION['PERSON_TABLE'].' USING(pid)';
		$query .= ' WHERE mtype = '.WA_String::sqlfmt( 'pid' );
		$query .= ' AND ip4 = '.WA_String::sqlfmt( $ip );
		
		$db = WA_Session::init_db();
		
		$rs = $db->Execute( $query );
		$row = $rs->FetchRow();
		$resp = $row['forname'].' '.$row['surname'].' ('.$row['roomid'].')';
	
		if( $rs->RecordCount() < 1 ) {
		  $query  = 'SELECT rr_hinfo_txt FROM '.$_SESSION['HOST_TABLE'];
		  $query .= ' WHERE mtype = '.WA_String::sqlfmt( 'nopid' );
		  $query .= ' AND ip4 = '.WA_String::sqlfmt( $ip );
		  try {
			$rs = $db->Execute( $query );
			$row = $rs->FetchRow();
			$resp = $row['rr_hinfo_txt'];
		  }
		  catch( exception $e ) {
			$resp = 'ismeretlen';
		  }
		}
/*	
		print_info( $desc );
		if( $rs->RecordCount() < 1 ) {
		  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
		}
		else {
		  $this->showresults( $rs );
		}
*/


		echo '<td>'.$ip.'</td>';
		echo '<td>'.preg_replace( '/[()]/', '' ,$iparr[1] ).'</td>';
		echo '<td>';
		if( isset( $larr[2] ) ) {
		  echo $larr[2];
		}
		echo '</td>';		
		echo '<td>'.$larr[1].'</td>';
		echo '<td>'.$resp.'</td>';		
		// echo html_entity_decode( $line ) . "<br>";
		// echo $line;
		echo '</tr>';
	  }
	}
	echo '</table>';
    }
  }
?>

</div>


