<?php

class WA_DateInput extends WA_InputObject {
  public function __construct( $id = 'WA_DateInput', $label = 'Dátum:',  $required = true ) {

	parent::__construct( $id, $label = $label, 
							   $validate_type = array( 'numeric', 'numeric', 'numeric' ), 
							   $required = array( $required, $required, $required ), 
							   $length = array( 4, 2, 2 ) );

	$this->fields_cont->sepstr = '&nbsp;/&nbsp;';
  }

  public function set_val( $date ) {
	parent::set_val( 0, date( "Y", $date ) );
	parent::set_val( 1, date( "n", $date ) );
	parent::set_val( 2, date( "j", $date ) );

//	$this->doValidate();
//	for( $i = 0; $i < 3; ++$i ) {
//	  $this->input_fields[0]->setValid( $this->isValid() );
//	}
  }
  

  public function set_date( $date ) {
	$this->set_val( strtotime( $date ) );
  }

  public function set_init_val( $date ) {
	parent::set_init_val( 0, date( "Y", $date ) );
	parent::set_init_val( 1, date( "n", $date ) );
	parent::set_init_val( 2, date( "j", $date ) );
  }

  public function set_init_date( $date ) {
	$this->set_init_val( strtotime( $date ) );
  }

  public function getYear() {
	return trim( $this->get_val( 0 ) );
  }

  public function getMonth() {
	return trim( $this->get_val( 1 ) );
  }

  public function getDay() {
	return trim( $this->get_val( 2 ) );
  }

  public function get_date() {
	return $this->getYear().'-'.WA_String::zpad($this->getMonth()).'-'.WA_String::zpad($this->getDay());
  }

  public function genQueryFooter() {
	$y = trim( $this->get_val( 0 ) );
	$m = WA_String::zpad( trim( $this->get_val(1) ) );
	$d = WA_String::zpad( trim( $this->get_val(2) ) );
	return WA_String::sqlfmt( $y.'-'.$m.'-'.$d ) ;
  }

  public function Validate() {
	if( parent::Validate() ) {
	  $this->is_valid = WA_Validator::dateymd( $this->getYear(), $this->getMonth(), $this->getDay() );
	  if( ! $this->is_valid ) {
		$this->fields[0]->is_valid = $this->is_valid;
		$this->fields[1]->is_valid = $this->is_valid;
		$this->fields[2]->is_valid = $this->is_valid;
	  }
	}
//	else {
//	  echo '<br>nem valid<br>';
//	}
  }
  
  public function init_from_sql( $va ) {
	$this->set_date( $va[0] );
  }
}

?>
