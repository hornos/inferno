<?php

class WA_TelInput extends WA_ContainerObject {
  public function __construct( $id = 'WA_TelInput', $labels = array( 'Home:' => 'telhome', 'Work' => 'telwork', 'Mobile' => 'telmobile' ),  $required = array( false, false, false ) ) {

	parent::__construct( $id, 'vertical' );
	
	$this->labels = $labels;
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->error = new WA_LabelObject( $id."_error", 'Error' );

	$this->fields_cont  = new WA_ContainerObject( $id."_fields_cont", 'vertical' );

/*
	for( $i = 0; $i < count( $labels ); ++$i ) {
	  $this->fields[$i] = new WA_InputObject( $id."_field_".$i, $labels[$i], array( 'numeric' ) , array( $required[$i] ), array( 25 ) );
	  $this->fields[$i]->descr->disable();
	  $this->fields[$i]->error->disable();	  
	}
*/

	$i = 0;
	foreach( $labels as $k => $v ) {
	  $this->fields[$i] = new WA_InputObject( $id."_field_".$i, $k, array( 'numeric' ) , array( $required[$i] ), array( 25 ) );
	  $this->fields[$i]->descr->disable();
	  $this->fields[$i]->error->disable();
	  ++$i;	  
	}
	
	$this->fields_cont->putObjectArray( $this->fields );

	$this->descr->disable();
	$this->error->disable();

	$this->putObjectArray( array( $this->descr, $this->fields_cont, $this->error ) );	
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
  
  public function genQueryFooter() {
	$qstr = 'ROW(';

	$first = true;	
	foreach( $this->fields as $o ) {
	  if( ! $first ) {
		$qstr .= ',';
	  }
	  $qstr .= WA_String::sqlfmt( trim( $o->get_val() ) );
	  $first = false;
	}
	
	$qstr .= ')';
	return $qstr;
  }

  public function init_from_sql( $va ) {
	$c = 0;
	foreach( $va as $v ) {
	  $this->fields[$c]->set_val( 0, $v );  
	  ++$c;
	}
  }
}

?>
