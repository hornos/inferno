<?php

  // Just for fun
  $module = 'add_user';
  
  // Direct Access Control
  if( ! isset( $_SESSION['valid'] ) ) {
	echo "Direct access is not permitted!";
	exit;
  }

  // Arguments and argument check
  $ptype = 'student';
  if( WA_Session::is_sended( 'ptype' ) ) {
	$ptype = WA_Session::get_sended( 'ptype' );

    $valid_ptype = array( 'student', 'guest', 'wifi' );
	if( ! WA_Session::is_valid_arg( $ptype, $valid_ptype ) ) {
	  exit;
	}
  }

  // Authorization
  if( ! WA_Session::authorize( $module, $opts = array( 'vname' => 'ptype', 'vvalue' => $ptype ) ) ) {
	exit;
  }

  // Action setting
  $action = 'add'; // upd
  if( WA_Session::is_sended( 'action' ) ) {
	$action = WA_Session::get_sended( 'action' );
  
	$valid_action = array( 'add', 'upd', 'del' );
	if( ! WA_Session::is_valid_arg( $action, $valid_action ) ) {
	  exit;
	}
  }

  // Init form variables
  $title_array  = array( 'student' => 'Hallgató',
						 'guest'   => 'Vendég',
						 'wifi'	   => 'Wifi felhasználó' );
    
  $action_array = array( 'upd' => 'módosítás',
						 'del' => 'törlés',
						 'add' => 'felvétel' );
						 

  $title = $title_array[$ptype].' '.$action_array[$action];

  // Options
  $opts  = array( 'ptype' => $ptype, 'action' => $action );
  
  // Create form
  $form = new WA_AddUserForm( $action.'_user', $title, $module, $opts );
  $form->setAction( $action );
  
  // Initial Validation
  $form->Validate();
  
  if( $action == 'upd' ) {
	$pid = WA_Session::get_sended( 'pid' );
	if( is_numeric( $pid ) ) {
	  $form->putObject( new WA_HiddenFieldObject( 'pid', $pid ) );
	
	  if( $form->isState( 'init' ) ) {
		$form->sqlinit( WA_Session::init_db() );
		$form->setState( 'edit' );
	  }
	}
  }
  else if( $action == 'del' ) {
	$pid = WA_Session::get_sended( 'pid' );
	if( is_numeric( $pid ) ) {
	  $form->putObject( new WA_HiddenFieldObject( 'pid', $pid ) );
	
	  if( $form->isState( 'init' ) ) {
		$form->sqlinit( WA_Session::init_db() );
		$form->setState( 'check' );
	  }
	}
  }


  // Form Logic Start
  // Mind the order: specific -> more general case
  
  $form->Validate();
  
  // Debug
  WA_Session::print_infobar( $form, $action );

  // work state
  if( $form->isState( 'work' ) && $form->is_valid ) {
	$form->setState( 'end' );
	
	// TODO: if action
	if( strcmp( $form->getAction(), 'upd' ) == 0 ) {
	  $form->Update( WA_Session::init_db() );
	}
	else if( strcmp( $form->getAction(), 'del' ) == 0 ) {
	  $form->Delete( WA_Session::init_db() );
	}
	else {  
	  $form->Record( WA_Session::init_db() );
	}
	WA_Session::finish();
  }

  // check state
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
	$form->genHTML();
	WA_Session::finish();
  }

  // init state
  if( $form->isState( 'init' ) ) {
	$form->setState( 'check' );
	$form->disableErrors();
	$form->genHTML();
	WA_Session::finish();
  }
  // Form Logic End

?>
