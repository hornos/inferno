<?php

class WA_DelHostForm extends WA_FormObject {
    public function __construct( $id = 'del_host', $title = 'List Host', $module = 'del_host', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 </div>';
  
		// FORM
		$this->setDescription( $descr );

		// QUERY
		if( isset( $this->opts['mid'] ) ) {
		  $mid = $this->opts['mid'];
		}
		else {
		  return;
		}
		
		$ptype = 'pid';
		if( isset( $this->opts['ptype'] ) ) {
		  $ptype = $this->opts['ptype'];
		}
		
		if( $ptype == 'pid' ) {
		  $query  = 'SELECT (name).forname, (name).surname , hostname, ip4 from '.$_SESSION['HOST_TABLE'];
		  $query .= ' INNER JOIN '.$_SESSION['PERSON_TABLE'].' USING(pid)';
		  $query .= ' WHERE mid= '.WA_String::sqlfmt( $mid );
		}
		else {
		  $query  = 'SELECT hostname, rr_hinfo_txt, ip4 from '.$_SESSION['HOST_TABLE'];
		  $query .= ' WHERE mid= '.WA_String::sqlfmt( $mid );		
		}
		
		$db = WA_Session::init_db();
		
		try {
		  $rs = $db->Execute( $query );
		}
		catch( exception $e ) {
		// print_error( 'SQL hiba történt, fordulj a rendszergazdához segítségért!' );
		  echo $query;
		  print_query_error( 'Error: ', $e );
		  return;
		}
		$db->Close();
		$row = $rs->FetchRow();

		if( $ptype == 'pid' ) {
		  $name = new WA_NameInput( 'name', 'Név:', false );
		  $name->set_val( 0, $row['forname'] );
		  $name->set_val( 1, $row['surname'] );
		}
		else {
		  $name = new WA_InputObject( 'name', 'Responsible:', array( 'general' ), array( false ), array( 30 ) );
		  $name->set_val( 0, $row['rr_hinfo_txt'] );		
		}
		
		$hostname = new WA_InputObject( 'hostname', 'Hostname:', array( 'hostname' ), array( false ), array( 30 ) );
		$hostname->set_val( 0, $row['hostname'] );
		$hostname->suffx->setLabel( WA_String::suffx( '' ) );
		$hostname->suffx->enable();
		
		$ip = new WA_IPInput( 'ip', 'IP:', false );
		$ip->set_ip( $row['ip4'] );
		
		$mid= new WA_HiddenFieldObject( 'mid', $mid );

		// OBJECTS
		// sql bind: name		
	
		$button  = new WA_ButtonObject( 'button', 'Törlés', '' );
  
		// BUILD FORM

		
		$objarr = array( $hostname, $ip, $name, $mid, $button );

		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }
  
  public function Delete( $db ) {
	if( isset( $this->opts['mid'] ) ) {
	  $mid = $this->opts['mid'];
	}
	else {
	  return;
	}
	
	$query  = 'DELETE FROM '.$_SESSION['HOST_TABLE'];
	$query .= ' WHERE mid = '.WA_String::sqlfmt( $mid );
	
	echo '<br><span class="info">Törlés: </span>';
	try {
	  $rs = $db->Execute( $query );
// TODO: no delete check
//	  if( $rs->RecordCount() == 0 ) {
//		echo '<span class="info_red">Hiba!</span>';
//	  }
//	  else {
	  echo '<span class="info_green">Sikerült!</span>';
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'DELETE HOST', $query );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'DELETE HOST', $query );

//	  }
	}
	catch( exception $e ) {
	  echo '<span class="info_green">Hiba!</span>';
	  print_query_error( 'Error: ', $e );
	  return;
	}
	
  }
}
?>
