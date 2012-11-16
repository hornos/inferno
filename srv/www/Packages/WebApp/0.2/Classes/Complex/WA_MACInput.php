<?php

class WA_MACInput extends WA_InputObject {
  public function __construct( $id = 'WA_MACInput', $label = 'MAC:',  $required = true ) {

	parent::__construct( $id, $label = $label, 
							   $validate_type = array( 'hex', 'hex', 'hex', 'hex', 'hex', 'hex' ), 
							   $required = array( $required, $required, $required, $required, $required, $required ),
							   $length = array( 2, 2, 2, 2, 2, 2 ) );
	
	$this->fields_cont->sepstr = '&nbsp;:&nbsp;';
  }
  
  public function set_addr( $mac ) {
	// echo $mac;
	$macarr = explode( ':', $mac );
	$i = 0;
	foreach( $macarr as $maf ) {
	  $this->fields[$i]->set_val( $maf );
	  ++$i;
	}
  }
  
  public function set_init_val( $mac ) {
	// echo $mac;
	$macarr = explode( ':', $mac );
	$i = 0;
	foreach( $macarr as $maf ) {
	  $this->fields[$i]->set_init_val( $maf );
	  ++$i;
	}
  }
  
}

?>
