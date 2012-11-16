<?php

class WA_ButtonObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_ButtonObject', $title = 'Submit', $label = 'Input' ) {

	parent::__construct( $id, 'v' );
	
	$this->label = new WA_LabelObject( $id."_label", $label );
	$this->suffx = new WA_HrefObject( $id."_suffx", '', '' );
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->buttn = new WA_ButtonFieldObject( $id."_buttn", $title );
  	
	$this->cont = new WA_ContainerObject( $id."_button_cont", 'horizontal' );
	$this->cont->putObject( $this->label );
	$this->cont->putObject( $this->buttn );
	$this->cont->putObject( $this->suffx );

	$this->suffx->disable();
	$this->descr->disable();
	
	$this->putObject( $this->descr );	
	$this->putObject( $this->cont );
	
	// style
	// $this->label->setWidth( "150px" );
	// $this->label->setAlign( "right" );
  }

/* WebObject  
  public function setLabelWidth( $s ) {
	$this->cont->tdwidth[0] = $s;
  }
*/

  public function is_empty() {
	return true;
  }
}

?>
