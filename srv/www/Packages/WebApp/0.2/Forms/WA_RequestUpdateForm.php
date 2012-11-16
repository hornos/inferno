<?php

class WA_RequestUpdateForm extends WA_FormObject {
    public function __construct( $id = 'req_updt', $title = 'List Host', $module = 'req_updt', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 </div>';
  
		// FORM
		// $this->setDescription( $descr );

		// OBJECTS
		$dcf = $_SESSION['INFERNO_ROOT'].'/dns/dns_serial';
		$dcc = file_get_contents( $dcf );
		
		$dns_counter = new WA_labelObject( 'dns_counter', 'DNS Serial Számláló: '.$dcc );
		$dns_counter->css_class = 'dns_counter';
		$upd_dhcp	 = new WA_CheckboxObject( 'upd_dhcp', 'DHCP Frissítés', true );
		$upd_dns	 = new WA_CheckboxObject( 'upd_dns', 'DNS Frissítés', true );
		$upd_radius  = new WA_CheckboxObject( 'upd_radius', 'Radius Frissítés', true );
		$upd_c3550	 = new WA_CheckboxObject( 'upd_c3550', 'Catalyst 3550 Frissítés', false );
		$button  	 = new WA_ButtonObject( 'button', 'Elküldése', 'Kérés' );
  
		// BUILD FORM		
		$objarr = array( $dns_counter, $upd_dhcp, $upd_dns, $upd_radius, $upd_c3550, $button );
		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }
  
  public function request() {
	$updr = $_SESSION['INFERNO_LOCK'].'/update_request';
  
	if( is_readable( $updr ) ) {
	  WA_String::print_info( 'Egy frissítési kérelem már el van helyezve, próbálkozz 1 perc után újra!' );
	  return 0;
	}
	
	$updlcks = array( 'upd_dhcp'   => 'update_request_dhcp',
						   'upd_dns'    => 'update_request_dns',
						   'upd_radius' => 'update_request_radius',
						   'upd_c3550'  => 'update_request_c3550' );
	
	$isupd = false;
	foreach( $updlcks as $k => $v ) {
	  $o = $this->getContentObject( $k );
	  if( $o->isChecked() ) {
		// $updmsg = date( "%Y-%m-%d-%H:%i:%s").' '.$_SERVER['PHP_AUTH_USER'];
		$updmsg = date( "%Y-%m-%d-%H:%i:%s").' '.$_SESSION['LOGIN_USER'];

		$updlck = $_SESSION['INFERNO_LOCK'].'/'.$v;

		if( ($fp = fopen( $updlck, 'w+' )) === FALSE ) {
		  WA_String::print_error( 'Cannot create lock: '.$v );
		  continue;
		}
	
		fwrite( $fp, $updmsg );
		fclose( $fp );
		$isupd = true;
	  }
	}
	
	if( $isupd ) {
	  // $updmsg = date( "Y-m-d-H:i:s").' '.$_SERVER['PHP_AUTH_USER'].' REQUEST';
	  $updmsg = date( "Y-m-d-H:i:s").' '.$_SESSION['LOGIN_USER'].' REQUEST';

	  $updlck = $_SESSION['INFERNO_LOCK'].'/'.'update_request';

	  if( ($fp = fopen( $updlck, 'w+' )) === FALSE ) {
		WA_String::print_error( 'Cannot create lock: update_request' );
		return 1;
	  }
		
	  fwrite( $fp, $updmsg );
	  fclose( $fp );
	  WA_String::print_info( 'Request was placed. Update occures in every minute.' );
	  // WA_Session::log_this( $_SERVER['PHP_AUTH_USER'], 'UPDATE REQUEST', '' );
	  WA_Session::log_this( $_SESSION['LOGIN_USER'], 'UPDATE REQUEST', '' );
	}
	else {
	  WA_String::print_error( 'Please select at least one!' );
	}	
  }
  
}
?>
