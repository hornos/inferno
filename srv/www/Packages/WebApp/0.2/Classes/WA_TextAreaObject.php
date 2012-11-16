<?php

class WA_TextAreaObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_TextAreaObject', $label = 'Text Area', $value = "", $rows = 3, $cols = 50 ) {

	parent::__construct( $id, 'v' );
	
	$this->label = new WA_LabelObject( $id."_label", $label );
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->tarea = new WA_TextAreaFieldObject( $id."_tarea", $rows, $cols, $value );
	
	$this->cont = new WA_ContainerObject( $id."_tarea_cont", 'h' );
	$this->cont->putObject( $this->label );
	$this->cont->putObject( $this->tarea );

	$this->descr->disable();
	
	$this->putObject( $this->descr );	
	$this->putObject( $this->cont );
  }

  public function get_val( $i = 0 ) {
	return $this->tarea->get_val();	
  } 

  public function set_val( $i = 0, $v = 0 ) {
	$this->tarea->set_val( $v );	
  } 

  public function set_init_val( $i = 0, $v = 0 ) {
	$this->tarea->set_init_val( $v );	
  } 

  public function set_conv( $i = 0, $ca = array() ) {
	$this->tarea->set_conv( $ca );		
  }

  public function genQueryFooter() {
	return WA_String::sqlfmt( trim( $this->tarea->get_val() ) );	
  }
}


?>
