<?php

class WA_CheckboxObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_CheckboxObject', $label = 'Checkbox', $checked = true ) {

	parent::__construct( $id, 'v' );
	
	$this->label = new WA_LabelObject( $id."_label", $label );
	$this->suffx = new WA_LabelObject( $id."_suffx", $label );	
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->chbox = new WA_CheckboxFieldObject( $id."_checkbox", $checked );
	
	$this->cont = new WA_ContainerObject( $id."_checkbox_cont", 'horizontal' );
	$this->cont->putObject( $this->label );
	$this->cont->putObject( $this->chbox );
	$this->cont->putObject( $this->suffx );

	$this->suffx->disable();
	$this->descr->disable();
	
	$this->putObject( $this->descr );	
	$this->putObject( $this->cont );
	
	// style
	// $this->label->setWidth( "150px" );
	// $this->label->setAlign( "right" );
  }
  
  public function isChecked() {
	return $this->chbox->checked;
  }
  
  public function setChecked( $v ) {
	$this->chbox->checked = $v;
  }

  public function setInitChecked( $v ) {
	if( $v == "t")	{
	  $this->chbox->init_checked = true;
	}
	else {
	  $this->chbox->init_checked = false;	
	}
  }

  public function init_from_sql( $v ) {
	$this->setInitChecked( $v[0] );
  }

  public function setReadonly( $v ) {
	$this->chbox->is_readonly = $v;
  }
  
  public function genQueryFooter() {
	if( $this->chbox->checked ) {
	  return WA_String::sqlfmt( 'true' );
	}
	else {
	  return WA_String::sqlfmt( 'false' );
	}
  }
}

?>
