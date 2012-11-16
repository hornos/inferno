<?php
  
  $module = 'mfy_user';
  
  if( ! isset( $_SESSION['valid'] ) ) {
	echo "Direct access is not permitted!";
	exit;
  }

  if( ! WA_Session::authorize( $module ) ) {
	exit;
  }  

  // Choose Form Action Type
  $action = 'lst';
  if( WA_Session::is_sended( 'action' ) ) {
	$action = WA_Session::get_sended( 'action' );
	
	$valid_action = array( 'lst' );
	if( ! WA_Session::is_valid_arg( $action, $valid_action ) ) {
	  exit;
	}
  }

  // Arguments
  $ptype = 'student';
  if( WA_Session::is_sended( 'ptype' ) ) {
	$ptype = WA_Session::get_sended( 'ptype' );
	
	$valid_ptype = array( 'student', 'guest', 'wifi' );
	if( ! WA_Session::is_valid_arg( $ptype, $valid_ptype ) ) {
	  exit;
	}
  }

  $opts =  array( 'action' => $action, 'table_ptype' => $ptype );
  $form = new WA_ModifyUserForm( $action.'_user', 'Felhasználók Keresése és Módosítása', $module, $opts );
  $form->setAction( $action );
  $form->Validate();
  // Form Logic Start
  
  // Debug
  WA_Session::print_infobar( $form, $action );

  // work state
  if( $form->isState( 'work' ) && $form->is_valid ) {
	$form->Validate();
	$form->setState( 'work' );
	$form->disableErrors();
	$form->genHTML();
	$form->query( WA_session::init_db() );
	
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

  // init state
  if( $form->isState( 'init' ) ) {
	$form->setState( 'work' );
	$form->disableErrors();
	$form->genHTML();
	WA_Session::finish();
  }
  // Form Logic End
    
?>
