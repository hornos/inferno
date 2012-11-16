<?php
  
  $module = 'sel_vlan';
  
  if( ! isset( $_SESSION['valid'] ) ) {
	echo "Direct access is not permitted!";
	exit;
  }

  if( ! WA_Session::authorize( $module ) ) {
	exit;
  }  

  // Choose Form Action Type
  $action = 'sel_vlan';
  if( WA_Session::is_sended( 'action' ) ) {
	$action = WA_Session::get_sended( 'action' );
	
	$valid_action = array( 'sel_ip', 'sel_vlan' );
	if( ! WA_Session::is_valid_arg( $action, $valid_action ) ) {
	  exit;
	}
  }

  // Arguments
  $ptype = 'student';
  if( WA_Session::is_sended( 'ptype' ) ) {
	$ptype = WA_Session::get_sended( 'ptype' );
	
	$valid_ptype = array( 'student', 'guest', 'wifi', 'pid', 'nopid' );
	if( ! WA_Session::is_valid_arg( $ptype, $valid_ptype ) ) {
	  exit;
	}
  }

  if( $ptype != 'nopid' ) {
	if( WA_Session::is_sended( 'pid' ) ) {
	  $pid = WA_Session::get_sended( 'pid' );
	  if( ! is_numeric( $pid ) ) {
	   exit;
	  }
	}
	else {
	  return;
	}

	if( WA_Session::is_sended( 'roomid' ) ) {
	  $roomid = WA_Session::get_sended( 'roomid' );
	  if( ! WA_Validator::is_roomid( $roomid ) ) {
		return;
	  }
	  // echo '<br>Selected roomid: '.$roomid;
	}
	else {
	  return;
	}
  }

  if( WA_Session::is_sended( 'vlan' ) ) {
    $vlan = WA_Session::get_sended( 'vlan' );
    if( ! is_numeric( $vlan ) ) {
  	  return;
	}
  }

  if( WA_Session::is_sended( 'mid' ) ) {
    $mid = WA_Session::get_sended( 'mid' );
    if( ! is_numeric( $mid ) ) {
  	  return;
	}
  }

  if( $action == 'sel_ip' ) {
	if( WA_Session::is_sended( 'vlan_field_0' ) ) {
	  $vlan = WA_Session::get_sended( 'vlan_field_0' );
	  if( ! is_numeric( $vlan ) ) {
		return;
	  }
	  // echo "<br>Selected vlan: ".$vlan;
	}

	if( WA_Session::is_sended( 'port_field_0' ) ) {
	  $port = WA_Session::get_sended( 'port_field_0' );
	  if( ! WA_Validator::is_port( $port ) ) {
		return;
	  }
	  // echo "<br>Selected port: ".$port;
	}
  }

  $db = NewADOConnection( $_SESSION['DBTYPE'] );  
  $db->Connect( $_SESSION['DBHOST'], $_SESSION['DBUSER'], $_SESSION['DBPASS'], $_SESSION['DBNAME'] );
  $db->SetFetchMode( ADODB_FETCH_ASSOC );

  if( $ptype != 'nopid' ) {
	$opts =  array( 'ptype' => $ptype, 'db' => $db, 'pid' => $pid, 
				    'roomid' => $roomid, 'action' => $action );				  
  }
  else {
	$opts =  array( 'ptype' => $ptype, 'db' => $db, 'pid' => 'nopid', 'action' => $action,  );  
  }

  if( isset( $mid ) ) {
	$opts = array_merge( $opts, array( 'mid' => $mid ) );
  }

  $title = '3/2 VLAN kiválasztása';
  if( $action == 'sel_ip' ) {
	if( ! isset( $vlan ) ) {
	  $vlan = 112;
	}
	$opts = array_merge( $opts, array( 'vlan' => $vlan ) );
	if( isset( $port ) ) {
	  $opts = array_merge( $opts, array( 'port' => $port ) );
	}
	$title = '3/3 Regisztráció';
  }
  else {
	if( ! isset( $vlan ) ) {
	  $vlan = 112;
	}
	$opts = array_merge( $opts, array( 'vlan' => $vlan ) );  
  }


  $form = new WA_SelectVlanForm( $action, $title, $module, $opts );
  if( isset( $mid ) ) {
	$form->putObject( new WA_HiddenFieldObject( 'mid', $mid ) );
  }
  $form->Validate();

  // Form Logic Start
  
  // Debug
  WA_Session::print_infobar( $form, $action );

  // work state
  if( $form->isState( 'work' ) && $form->is_valid ) {
	$form->Validate();
	$form->setState( 'work' );
	$form->disableErrors();
	// echo "SQL save";
	// $form->genHTML();
	if( isset( $mid ) ) {
	  $form->Update( WA_Session::init_db() );
	}
	else {
  	  $form->Record( WA_Session::init_db() );
	}
	WA_Session::finish();
  }

  // check state (skipped)
  if( $form->isState( 'check' ) && $form->is_valid ) {
	$form->setState( 'work' );
	$form->genStaticHTML();
	WA_Session::finish();
  }

  // start state
  if( $form->isState( 'check' ) ) {
	// TODO: implement correct errors
	$form->disableErrors();
	$form->genHTML();
	WA_Session::finish();
  }

  if( $form->isState( 'edit' ) ) {
	$form->setState( 'check' );
	$form->disableErrors();
	$form->init();
	$form->pValidate();
	$form->genHTML();
	WA_Session::finish();
  }

  // init state
  if( $form->isState( 'init' ) ) {
	$form->setAction( 'sel_ip' );
	$form->setState( 'edit' );
	$form->disableErrors();
	$form->genHTML();
	WA_Session::finish();
  }
  
  $db->Close();
  // Form Logic End
    
?>
