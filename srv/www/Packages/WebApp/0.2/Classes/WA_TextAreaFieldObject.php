<?php

class WA_TextAreaFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_TextAreaFieldObject', $rows = 10, $cols = 30, $value = "" ) {
	parent::__construct( $id, 'WA_TextAreaFieldObject' );
	$this->set_val( $value );
	
	$this->rows = $rows;
	$this->cols = $cols;
  }
  
  public function getContent() {
    WA_String::ncat( '<textarea'  );
    WA_String::cat(  ' id="'.$this->id.'" name="'.$this->id.'"' );
	WA_String::catpr( 'rows', $this->rows );
	WA_String::catpr( 'cols', $this->cols );
	WA_String::catpr( 'class', $this->css_class );
	
	if( $this->is_readonly ) {
	  WA_String::catpr(  ' readonly', 'readonly' );
    }    
  
	if( isset( $this->css_style ) ) {
	  if( WA_String::nzstr( $this->css_style ) ) {
		WA_String::catpr(  ' style', $this->css_style );	  
	  }
	}

    WA_String::cat( '>' );
    WA_String::ncat( $this->get_val() );
    WA_String::ncat( '</textarea>' );
  }

  public function getStaticContent() {
	$this->is_readonly = true;
	$this->getContent();
	$this->is_readonly = false;
  }
}


?>
