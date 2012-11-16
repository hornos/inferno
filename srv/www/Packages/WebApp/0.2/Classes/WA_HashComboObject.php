<?php

class WA_HashComboObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_HashComboObject', $label = 'Combo', $values = array( array( 'pussy' => 'Cica', 'cat' => 'Mica') ), $selected = array( 'pussy' ) ) {

	parent::__construct( $id, 'vertical' );
	
	$this->combo_values = $values;
	
	$this->label  = new WA_LabelObject( $id."_label", $label );
	$this->suffx  = new WA_LabelObject( $id."_suffx", '' );
	$this->descr  = new WA_LabelObject( $id."_descr", 'Description' );

	$this->fields_cont = new WA_ContainerObject( $id."_fields_cont", 'horizontal' );

	for( $i = 0; $i < count( $selected ); ++$i ) {
	  $this->fields[$i] = new WA_HashComboFieldObject( $id."_field_".$i, $values[$i], $selected[$i] );
	}
	$this->fields_cont->putObjectArray( $this->fields );

	
	$this->cont = new WA_ContainerObject( $id."_cont", 'horizontal' );
	$this->cont->putObjectArray( array( $this->label, $this->fields_cont, $this->suffx ) );

	$this->suffx->disable();
	$this->descr->disable();
	
	$this->putObjectArray( array( $this->descr, $this->cont ) );
	
	// style
	// $this->label->setWidth( "150px" );
	// $this->label->setAlign( "right" );
	$this->fields_cont->sepstr = "&nbsp;&nbsp;";
  }

  public function setOnChange( $i = 0, $onc = '' ) {
	$this->fields[$i]->setOnChange( $onc );
  }

  public function get_val( $i = 0) {
	return $this->fields[$i]->get_val();
  }

  public function get_selected( $i = 0) {
	return $this->fields[$i]->selected;
  }

  public function genQueryFooter() {
	if( $this->sql_merge ) {
	  $qstr = '';
	  foreach( $this->fields as $o ) {
		$qstr .= trim( $o->get_val() );
	  }
	  return WA_String::sqlfmt( $qstr );	  
	}
	
  	if( count( $this->fields ) > 1 ) {
	  $isfirst = true;
	  
	  $qstr = 'ROW(';
	  foreach( $this->fields as $o ) {
		if( ! $isfirst ) { $qstr .= ','; }
		$qstr .= WA_String::sqlfmt( trim( $o->get_val() ) );
		$isfirst = false;
	  }
	  $qstr .= ')';
	  return $qstr;
	}
	else {
	  return WA_String::sqlfmt( trim( $this->fields[0]->get_val() ) );
	}
  }

  public function setSelected( $i = 0, $s = 1 ) {
	$values = $this->combo_values[$i];
	
	foreach( $values as $k => $v ) {
	  if( $k == $s ) {
		break;
	  } 
	}
	
	$this->fields[$i]->selected = $k;
  }

  public function init_from_sql( $va ) {
	$c = 0;
	foreach( $va as $v ) {
	  $this->setSelected( $c, $v );
	  ++$c;
	}
  }
  
}

?>
