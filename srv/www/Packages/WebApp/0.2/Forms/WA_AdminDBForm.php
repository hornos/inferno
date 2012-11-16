<?php

class WA_AdminDBForm extends WA_FormObject {
    public function __construct( $id = 'admin_db', $title = 'List User', $module = 'admin_db', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 </div>';
  
		// FORM
		// $this->setDescription( $descr );

		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
 		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }

  public function genHTML() {
	parent::genHTML();
	$db = WA_Session::init_db();

	// Person table status
	$query  = 'SELECT count(*) FROM '.$_SESSION['PERSON_TABLE'];
	$query .= ' WHERE ptype = '.WA_String::sqlfmt('student');
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row   = $rs->FetchRow();
	  $stuno = $row['count'];
	}


	$query  = 'SELECT count(*) FROM '.$_SESSION['PERSON_TABLE'];
	$query .= ' WHERE ptype = '.WA_String::sqlfmt('student');
	$query .= ' AND sex = '.WA_String::sqlfmt('F');
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row   = $rs->FetchRow();
	  $stugno = $row['count'];
	}

	
	// Host table status
	$query = 'SELECT count(*) FROM '.$_SESSION['HOST_TABLE'];
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row   = $rs->FetchRow();
	  $totno = $row['count'];
	}

	
	$query  = 'SELECT count(*) FROM '.$_SESSION['HOST_TABLE'];
	$query .= ' WHERE valid = '.WA_String::sqlfmt( 't' );
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row = $rs->FetchRow();
	  $vno = $row['count'];
	}
	
	
	$query  = 'SELECT count(*) FROM '.$_SESSION['HOST_TABLE'];
	$query .= ' WHERE wifi = '.WA_String::sqlfmt( 't' );
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row = $rs->FetchRow();
	  $wno = $row['count'];
	}

	
	$query  = 'SELECT count(*) FROM '.$_SESSION['HOST_TABLE'];
	$query .= ' WHERE vl_id = '.WA_String::sqlfmt( '112' );
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  print_query_error( 'Error: ', $e );
	  $db->close();
	  return;
	}
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  $db->close();
	  return;
	}
	else {
	  $row = $rs->FetchRow();
	  $vl112 = $row['count'];
	}

	
	echo '<h3>Regisztrált hallgatók száma: '.$stuno.'</h3>';
	echo '<table>';
	echo '<tr>';
	echo '<td>Lányok:</td><td>'.$stugno.'</td>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>Fiúk:</td><td>'.($stuno - $stugno).'</td>';	
	echo '</tr>';

	echo '</table>';
		

	echo '<h3>Regisztrált gépek száma: '.$totno.'</h3>';

	echo '<table>';
	echo '<tr>';
	echo '<td>Engedélyezett gépek:</td><td>'.$vno.'</td>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>Leiltott gépek:</td><td>'.($totno - $vno).'</td>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>Wifit használó gépek:</td><td>'.$wno.'</td>';	
	echo '</tr>';

	echo '<tr>';
	echo '<td>Gépek a 112-es Vlanban:</td><td>'.$vl112.'</td>';	
	echo '</tr>';
	
	$mph = sprintf( "%2.2lf", $vl112/$stuno );
	echo '<tr>';
	echo '<td>Egy főre jutó gépek:</td><td>'.$mph.'</td>';	
	echo '</tr>';
	
	
	echo '</table>';


	
	$db->close();
  }

}
?>
