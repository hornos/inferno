<?php

class WA_NameInput extends WA_InputObject {
  public function __construct( $id = 'WA_NameInput', $label = 'Név:',  $required = true ) {

	parent::__construct( $id, $label = $label, 
							   $validate_type = array( 'alpha', 'alpha' ), 
							   $required = array( $required, $required ), 
							   $length = array( 25, 30 ) );
							   
	$this->is_valsqlrow = true;
  }

  public function getForname() {
	return trim( $this->fields[0]->get_val() );
  }

  public function getSurname() {
	return trim( $this->fields[1]->get_val() );
  }
  
/*  public function init_from_sql( $fn, $sn ) {
	$this->set_val( 0, $fn );
	$this->set_val( 1, $sn );
  } */
}

?>
