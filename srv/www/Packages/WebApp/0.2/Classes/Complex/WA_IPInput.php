<?php

class WA_IPInput extends WA_InputObject {
  public function __construct( $id = 'WA_IPInput', $label = 'IP:',  $required = true ) {

	parent::__construct( $id, $label = $label, 
							   $validate_type = array( 'ipf', 'ipf', 'ipf', 'ipf' ), 
							   $required = $required, 
							   $length = array( 3, 3, 3, 3 ) );

	$this->fields_cont->sepstr = '&nbsp;.&nbsp;';
  }
  
  public function set_ip( $ipstr = '127.0.0.1' ) {
	$ipa = explode( '.', trim( $ipstr ) );
	$i = 0;
	foreach( $ipa as $ip ) {
	  $this->set_val( $i, $ip );
	  ++$i;
	}
  }
}

?>
