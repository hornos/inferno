<?php

class WA_UpdateInfoForm extends WA_FormObject {
    public function __construct( $id = 'lst_updt', $title = 'List User', $module = 'lst_updt', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat.
				 </div>';
  
		// FORM
		// $this->setDescription( $descr );

  }

  public function genHTML() {
	parent::genHTML();

	echo '<br>';
	echo '<a href="index.php?m=lst_updt&action=last">Utolsó frissítés</a>';
	echo ' | ';
	echo '<a href="index.php?m=lst_updt&action=dhcpd">dhcpd.conf</a>';
	echo ' | ';
	echo '<a href="index.php?m=lst_updt&action="></a>';

	
	if( isset( $this->opts['action'] ) ) {
	  $action = $this->opts['action'];
	}

	if( $action == 'dhcpd' ) {
	  $this->dhcpd_update();
	}
	else if( $action == '' ) {
	  $this->dns_update( ''.$action );
	}
	else if( $action == '' ) {
	  $this->dns_update( '' );
	}
	else {
	  $this->last_update();
	}
  }
  
  public function dns_update( $ip ) {
	$dnscfg = $_SESSION['INFERNO_ROOT'].'/dns/'.$ip;
	if( is_readable( $dnscfg ) ) {
	  echo "\n<div class=\"ttext\">";

	  //$lines = file( $dnscfg );
	  // foreach( $lines as $ln => $line ) {
	  //	echo "\n<br>".$line;
	  // }
	  
	  $lines = file_get_contents( $dnscfg );
	  echo WA_String::txt2html( $lines );
	  
	  echo "\n</div>";
	}
	else {
	  WA_String::print_error( $ip.' error' );	
	}  	
  }
  
  public function dhcpd_update() {
	$dhcpdcfg = $_SESSION['INFERNO_ROOT'].'/dhcp/dhcpd.conf';
	if( is_readable( $dhcpdcfg ) ) {
	  $lines = file( $dhcpdcfg );
	  echo "\n<div class=\"ttext\">";

	  foreach( $lines as $ln => $line ) {
		echo "\n<br>".$line;
	  }
	  echo "\n</div>";
	}
	else {
	  WA_String::print_error( 'dhcpd.conf error' );	
	}  
  }


  public function last_update() {
	$logfile = $_SESSION['INFERNO_LOGS'].'/update_daemon.lastlog';
	
	if( is_readable( $logfile ) ) {
	  $lines = file( $logfile );
	  echo "\n<div class=\"ttext\">";
	  foreach( $lines as $ln => $line ) {
		if( strlen( trim( $line ) ) > 0 ) {
		  if( preg_match( "/ OK/", $line ) ) {
			echo "\n<br><span class=\"bgreen\">".$line."</span>";
		  }
		  else if( preg_match( "/ FAILED/", $line )   or 
				   preg_match( "/NOT VALID/", $line ) or 
				   preg_match( "/EXPIRED/", $line )   or
				   preg_match( "/NO DNS/", $line )    or
				   preg_match( "/NOT DHCP/", $line ) ) {
			echo "\n<br><span class=\"bred\">".$line."</span>";		  
		  }
		  else if( preg_match( "/ REQUEST/", $line )  or
				   preg_match( "/STAGE/", $line ) ) {
			echo "\n<br><span class=\"bold\">".$line."</span>";
		  }
		  else {
			echo "\n<br>".$line;
		  }
		}
	  }
	  echo "\n</div>";
	}
	else {
	  WA_String::print_error( 'Lastlog error' );
	}
  }
}
?>
