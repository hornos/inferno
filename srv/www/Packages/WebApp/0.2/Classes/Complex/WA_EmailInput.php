<?php

class WA_EmailInput extends WA_ContainerObject {
  public function __construct( $id = 'WA_EmailInput', $label = 'Email:', $required = false, $ispub = false ) {

	parent::__construct( $id, 'vertical' );
	
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->error = new WA_LabelObject( $id."_error", 'Error' );

	$this->fields_cont  = new WA_ContainerObject( $id."_fields_cont", 'horizontal' );
	$this->fields[0]    = new WA_InputObject( $id."_field_0", $label, array( 'email' ) , array( $required ), array( 35 ) );
	$this->fields[1]    = new WA_CheckboxObject( $id."_field_1", "Publikus", $ispub );
	
	$this->fields_cont->putObjectArray( $this->fields );

	$this->descr->disable();
	$this->error->disable();

	$this->putObjectArray( array( $this->descr, $this->fields_cont, $this->error ) );	
  }

  public function is_empty() {
	return $this->fields[0]->is_empty();
  }

  public function Validate() {
	return $this->fields[0]->Validate();
  }

  public function genQueryFooter() {
	$qstr = 'ROW(';
	$qstr .= WA_String::sqlfmt( trim( $this->fields[0]->get_val() ) );
	$qstr .= ',';
	$qstr .= $this->fields[1]->genQueryFooter();
	$qstr .= ')';
	return $qstr;
  }

  public function setLabelWidth( $s ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelWidth( $s );
	}
  }

  public function setLabelAlign( $a ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelAlign( $a );
	}
  }

  public function setLabelClass( $c ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelClass( $c );
	}
  }

  public function init_from_sql( $va ) {
	$v = $va[0];
	
	$vva = split( ',', rtrim( ltrim( $v, '(' ), ')' ) );
	if( isset( $vva[0] ) )
	  $this->fields[0]->set_val( 0, $vva[0] );
	if( isset( $vva[1] ) )
	  $this->fields[1]->setInitChecked( $vva[1] );
  }
}

?>
