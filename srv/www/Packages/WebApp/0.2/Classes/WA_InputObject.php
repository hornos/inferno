<?php

class WA_InputObject extends WA_ContainerObject {
  public function __construct( $id = 'WA_InputObject', 
							   $label = 'Input', 
							   $validate_type = array( 'alnum' ), 
							   $required = array( true ), 
							   $length = array( 5 ) ) {

	parent::__construct( $id, 'vertical' );
		
	$this->label = new WA_LabelObject( $id."_label", $label );
	$this->suffx = new WA_LabelObject( $id."_suffx", '' );
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->error = new WA_LabelObject( $id."_error", 'Error' );

	$this->fields_cont  = new WA_ContainerObject( $id."_fields_cont", 'horizontal' );

	for( $i = 0; $i < count( $validate_type ); ++$i ) {
	  $this->fields[$i] = new WA_InputFieldObject( $id."_field_".$i, $validate_type[$i], $required[$i], $length[$i] );
	}
	$this->fields_cont->putObjectArray( $this->fields );

	$this->cont = new WA_ContainerObject( $id."_cont", 'horizontal' );
	$this->cont->putObjectArray( array( $this->label, $this->fields_cont, $this->suffx ) );

	$this->suffx->disable();
	$this->descr->disable();
	$this->error->disable();

	$this->putObjectArray( array( $this->descr, $this->cont, $this->error ) );	
	
	// style
	$this->fields_cont->sepstr = "&nbsp;&nbsp;";
  }


  // TODO: foreach   
  public function GETHtmlValues() {
	foreach( $this->fields as $o ) {
	  if( WA_String::zstr( $o->get_val() ) ) {
		$o->GETHtmlValues();
	  }
	}  
  }

  public function get_val( $i = 0 ) {
	return $this->fields[$i]->get_val();	
  } 

  public function set_val( $i = 0, $v = 0 ) {
	$this->fields[$i]->set_val( $v );	
  } 

  public function set_init_val( $i = 0, $v = 0 ) {
	$this->fields[$i]->set_init_val( $v );	
  } 

  public function set_conv( $i = 0, $ca = array() ) {
	$this->fields[$i]->set_conv( $ca );		
  }
  
  public function init() {
	foreach( $this->fields as $f ) {
	  $f->init();
	}
  }

  public function set_valarr( $va = array( 0 ) ) {
	foreach( $va as $v ) {
	  $this->fields[$i]->set_val( $v );
	}
  } 


  public function genHTML() {
	$this->error->disable();
/*
	if( $this->is_valid ) {
	  $this->error->disable();
	}
	else {
	  $this->error->enable();	
	}
*/
	parent::genHTML();	
  }


  
  public function genQueryFooter() {
	if( $this->sql_merge ) {
	  $qstr = '';
	  if( isset( $this->sql_prefix ) ) {
		$qstr = $this->sql_prefix;
	  }

	  $first = true;

	  foreach( $this->fields as $o ) {
		if( isset( $this->sql_merge_sep ) ) {
		  if( ! $first ) {
			$qstr .= $this->sql_merge_sep;
		  }
		}
		$qstr .= trim( $o->get_val() );		
		$first = false;
	  }
	  
	  if( isset( $this->sql_postfix ) ) {
		$qstr .= $this->sql_postfix;
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
	  $qstr = '';
	  if( isset( $this->sql_prefix ) ) {
		$qstr .= $this->sql_prefix;
	  }
	  $qstr .= trim( $this->fields[0]->get_val() );
	  if( isset( $this->sql_postfix ) ) {
		$qstr .= $this->sql_postfix;
	  }
	  return WA_String::sqlfmt( $qstr );
	}
  }

  public function init_from_sql( $va ) {
	$c = 0;
	foreach( $va as $v ) {
	  $this->set_val( $c, $v );  
	  ++$c;
	}
  
  }
}

?>
