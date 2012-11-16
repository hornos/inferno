<?php

class WA_InputFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_InputFieldObject', $validate_type = 'alnum', $required = true, $length = 20 ) {
	parent::__construct( $id, 'WA_InputFieldObject' );
	
	$this->dojo_type	 = 'Textbox';
	$this->validate_type = $validate_type;
	$this->is_required	 = $required;
	$this->length		 = $length;
  }
  
  public function getContent() {
    WA_String::ncat(  '<input type="text"'  );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->id );

    if( $this->is_disabled ) {
	  WA_String::cat(  ' disabled' );
	  // WA_String::catpr(  'disabled', 'disabled' );
    }
    
    if( $this->is_readonly ) {
	  WA_String::catpr(  ' readonly', 'readonly' );
    }    
	
    if( ! $this->is_empty() ) {
	  WA_String::catpr( 'value', $this->get_val() );    
    }
    
    WA_String::catpr( 'class', $this->css_class );
    WA_String::catpr( 'maxlength', $this->length );
    WA_String::catpr( 'size', $this->length + 2 );

	$this->css_style = '';	
	
	if( $this->is_required ) {
	  $this->css_style .= 'background-color:yellow;';
	}

	if( $this->is_valid ) {
	  $this->css_style .= 'border:1px solid green;';
	}
	else {
	  $this->css_style .= 'border:1px solid red;';	
	}
	
	
	if( isset( $this->css_style ) ) {
	  if( WA_String::nzstr( $this->css_style ) ) {
		WA_String::catpr( 'style', $this->css_style );
	  }
	}
	
	// Dojo Part
    // WA_String::catpr( 'dojoType', $this->dojo_type );
    // WA_String::catpr( 'trim', 'true' );
    // WA_String::catpr( 'ucfirst', 'true' );
	
	WA_String::catn( ' />' );
  }

  public function getStaticContent() {
	$this->is_readonly = true;
	$this->getContent();
	$this->is_readonly = false;
  }

  public function is_empty() {
	$v = $this->values[0];
	return WA_String::zstr( $v['value'] );
  }  
}


?>
