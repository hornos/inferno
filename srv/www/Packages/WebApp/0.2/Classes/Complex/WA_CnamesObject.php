<?php

class WA_CnamesObject extends WA_TextAreaObject {
  public function __construct( $id = 'WA_CnamesObject', $label = 'Text Area', $value = "", $rows = 3, $cols = 50 ) {
	parent::__construct( $id, $label, $value, $rows, $cols );
  }

  public function Validate() {
	$this->is_valid = parent::Validate();

	if( WA_String::zstr( trim( $this->tarea->get_val() ) ) ) {
	  $this->is_empty = true;
	  $this->is_valid = true;
	  return;
	}

	$val = trim( $this->tarea->get_val() );
	
	$arr = explode( ",", $val );
	
	$valid = true;
	foreach( $arr as $v ) {
	  // echo $v;
	  $valid = $valid && WA_Validator::cname( trim( $v ) );
	}

	if( ! $valid ) {
	  $this->tarea->css_style="border: 1px solid red;";
	}
	
	$this->is_valid = $valid;
	return $this->is_valid;
  }

  public function genQueryFooter() {
	return WA_String::sqlfmt( trim( $this->tarea->get_val() ) );	
  }
}


?>
