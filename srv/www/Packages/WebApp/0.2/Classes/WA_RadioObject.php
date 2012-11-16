<?php

class WA_RadioObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_RadioObject', $name = 'WA_RadioObject', $value = 'pussy', $label = 'Radio', $is_checked = false ) {

	parent::__construct( $id, 'v' );
	
	$this->label = new WA_LabelObject( $id."_label", $label );
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->radio = new WA_RadioFieldObject( $id."_radio", $name, $value, $is_checked );
	
	$this->cont = new WA_ContainerObject( $id."_radio_cont", 'h' );
	$this->cont->putObject( $this->label );
	$this->cont->putObject( $this->radio );

	$this->descr->disable();
	
	$this->putObject( $this->descr );	
	$this->putObject( $this->cont );
	
	// style
	//$this->label->setWidth( "150px" );
	//$this->label->setAlign( "right" );
  }
  
  public function isChecked() {
	return $this->radio->is_checked;
  }
  
  public function setChecked( $v ) {
	$this->radio->is_checked = $v;
  }
}

?>
