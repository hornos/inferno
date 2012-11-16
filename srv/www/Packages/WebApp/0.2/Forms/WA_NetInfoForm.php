<?php

class WA_NetInfoForm extends WA_FormObject {
    public function __construct( $id = 'net_info', $title = 'List User', $module = 'net_info', $opts = array() ) {
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
	$db = NewADOConnection( $_SESSION['DBTYPE'] );  
  	$db->Connect( $_SESSION['DBHOST'], $_SESSION['DBUSER'], $_SESSION['DBPASS'], $_SESSION['DBNAME'] );
	$db->SetFetchMode( ADODB_FETCH_ASSOC );

	
	$this->query_vlan( $db );
	$this->query_device( $db );
	$this->query_portmap( $db );
	
	$db->Close();
  }

  public function query_device( $db ) {
	$query = 'SELECT * FROM '.$_SESSION['DEVICE_TABLE'];
	$this->ndev = array();
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  echo $this->query;
	  print_query_error( 'Error: ', $e );
	  return;
	}
	
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	}
	else {
	  foreach( $rs as $row ) {
		array_push( $this->ndev, $row['name'] );
	  }
	  
	  $titles = array( 'Device Name' => 'name', 'Uplink Port' => 'uport',
					   'Hostname' => 'hostname', 'Description' => 'descr' );
	  $sizes  = array( '180px' , '200px', '160px', '300px' );

	  $rstable = new WA_ResultsTable( 'results', $titles, $sizes , $rs, array( 'div' => false ) );
	  print_info( 'Device Mapping' );
	  $rstable->genHTML();
	}
  }

  public function query_portmap( $db ) {
	print_info( 'Port Mapping' );
	
	foreach( $this->ndev as $ndev ) {
	  $query  = 'SELECT * FROM '.$_SESSION['PORT_TABLE'].' WHERE ndev = '.WA_String::sqlfmt( $ndev );
	  $query .= ' ORDER BY id';
	  // echo '<br>'.$query;
	  try {
		$rs = $db->Execute( $query );
	  }
	  catch( exception $e ) {
		echo $this->query;
		print_query_error( 'Error: ', $e );
		return;
	  }
	
	  if( $rs->RecordCount() < 1 ) {
		// print_info( 'No record for: '.$ndev );
		echo "\n".'<div class="device">'.$ndev.'</div>';
		echo "\n".'<span class="port">No port</span>';
	  }
	  else {
		echo "\n".'<div class="device">'.$ndev.'</div>';
		$i = 1;
		foreach( $rs as $row ) {
		  $pn = sprintf( "%02d", $i );
		  echo "\n".'<span class="port"><span style="color: grey; font-size: 11px;">['.$pn.']</span> '.$row['port'].'&nbsp;&nbsp;</span>';
		  if( $i % 8 == 0 ) {
			echo "<br>";
		  }
		  ++$i;
		}
	  }
	}
  }

  public function query_vlan( $db ) {
	$query = 'SELECT * FROM '.$_SESSION['VLAN_TABLE'];
	
	try {
	  $rs = $db->Execute( $query );
	}
	catch( exception $e ) {
	  echo $this->query;
	  print_query_error( 'Error: ', $e );
	  return;
	}
	
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	}
	else {
	  $titles = array( 'VLAN' => 'vl_id', 'Name' => 'vl_name',
					   'Network' => 'vl_net', 'Netmask' => 'vl_mask', 
					   'Broadcast' => 'vl_bcast', 'Gateway' => 'vl_gw',
					   'Host from' => 'vl_hfrom', 'Host to' => 'vl_hto');
	  $sizes  = array( '40px' , '180px', '160px', '140px', '130px','130px','130px', '130px' );

	  $rstable = new WA_ResultsTable( 'results', $titles, $sizes , $rs, array( 'div' => false ) );
	  print_info( 'VLAN Address Mapping' );
	  $rstable->genHTML();
	  // print_info( 'VLAN Host Details' );	  
	}
  }
}
?>
