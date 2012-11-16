<?php

class WA_ComboObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_ComboObject', $label = 'Combo', $values = array( array( 'pussy', 'cat' ) ), $selected = array( 1 ) ) {

	parent::__construct( $id, 'vertical' );
	
	$this->combo_values = $values;
	
	$this->label  = new WA_LabelObject( $id."_label", $label );
	$this->suffx  = new WA_LabelObject( $id."_suffx", '' );
	$this->descr  = new WA_LabelObject( $id."_descr", 'Description' );

	$this->fields_cont = new WA_ContainerObject( $id."_fields_cont", 'horizontal' );

	for( $i = 0; $i < count( $selected ); ++$i ) {
	  $this->fields[$i] = new WA_ComboFieldObject( $id."_field_".$i, $values[$i], $selected[$i] );
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

  public function get_val( $i = 0) {
	return $this->fields[$i]->get_val();
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
	
	$j = 1;
	foreach( $values as $v ) {
	  if( $v == $s ) {
		break;
	  }
	  ++$j;  
	}
	
	$this->fields[$i]->selected = $j;
  }

  public function init_from_sql( $va ) {
	$c = 0;
	foreach( $va as $v ) {
	  $this->setSelected( $c, $v );
	  ++$c;
	}
  }

/*  
  public function setSuffix( $str ) {
	$this->suffx->setLabel( $str );
	$this->suffx->enable();
  }

  public function setSelectedValue( $i, $v ) {
	return $this->combo_fields[$i]->setSelectedValue( $v );
  }
  
  
  public function setFromQuery( $v ) {
	$func = $this->get( 'fromquery_func' );
	if( is_callable( array( $this, $func ) ) ) {
	  $this->$func( $v );	    
	}
  }
  
  public function setRoomNumber( $v ) {
	$roomid = trim( $v );
	$this->setSelectedValue( 0, $roomid{0} );
	$this->setSelectedValue( 1, $roomid{1} );
	$this->setSelectedValue( 2, $roomid{2}.$roomid{3} );
  }
*/  
}

?>
