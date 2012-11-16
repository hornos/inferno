<?php

class WA_NameInput extends WA_InputObject {
  public function __construct( $id = 'WA_NameInput', $label = 'Név:',  $required = true ) {

	parent::__construct( $id, $label = $label, 
							   $validate_type = array( 'alpha', 'alpha', 'alpha' ), 
							   $required = array( false, $required, $required ), 
							   $length = array( 5, 25, 30 ) );
  }
  
}

?>
