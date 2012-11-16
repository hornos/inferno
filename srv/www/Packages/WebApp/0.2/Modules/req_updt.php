<?php
  $module = 'req_updt';
  
  if( ! isset( $_SESSION['valid'] ) ) {
	echo "Direct access is not permitted!";
	exit;
  }

  if( ! WA_Session::authorize( $module ) ) {
	exit;
  }  

  // Choose Form Action Type
  $action = 'req';
  if( WA_Session::is_sended( 'action' ) ) {
	$action = WA_Session::get_sended( 'action' );
	
	$valid_action = array( 'req' );
	if( ! WA_Session::is_valid_arg( $action, $valid_action ) ) {
	  exit;
	}
  }

  // Arguments
  $opts =  array( 'action' => $action );

  $form = new WA_RequestUpdateForm( $action.'_updt', 'Szerver Frissítés Kérése', $module, $opts );
  $form->setAction( $action );
  $form->Validate();
  // Form Logic Start
  
  // Debug
  WA_Session::print_infobar( $form, $action );

  // work state
  if( $form->isState( 'work' ) && $form->is_valid ) {
	$form->Validate();
	$form->setState( 'check' );
	$form->disableErrors();
	$form->genHTML();
	$form->request();
	
	WA_session::finish();
  }

  // check state (skipped)
  if( $form->isState( 'check' ) && $form->is_valid ) {
	$form->setState( 'work' );
	$form->genStaticHTML();
	WA_session::finish();
  }

  // start state
  if( $form->isState( 'check' ) ) {
	// TODO: implement correct errors
	$form->disableErrors();
	$form->genHTML();
	WA_session::finish();
  }

  // init state
  if( $form->isState( 'init' ) ) {
	$form->setState( 'check' );
	$form->disableErrors();
	$form->genHTML();
	WA_session::finish();
  }
  // Form Logic End
    
?>
