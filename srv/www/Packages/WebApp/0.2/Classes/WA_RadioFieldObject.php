<?php

// TODO: first time set checked! 
class WA_RadioFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_RadioFieldObject', $name = 'WA_RadioFieldObject', $value = 'pussy', $is_checked = false ) {
	parent::__construct( $id, 'WA_CheckboxFieldObject' );
	$this->set_val( $value );
	$this->is_checked = $is_checked;
	$this->name = $name;
  }

  public function getContent() {
    WA_String::ncat( '<input type="radio"'  );
    WA_String::catpr( 'id',  $this->id );
	WA_String::catpr( 'name', $this->name );
    WA_String::catpr( 'class', $this->css_class );
	
	WA_String::catpr( 'value', $this->get_val() );
  	  
	if( $this->is_checked ) {
	  WA_String::cat(  ' checked ' );
	}

    if( $this->is_readonly ) {
	  WA_String::cat(  ' readonly' );
    }    
	
    WA_String::cat( '>' );
  }  
  
  public function getStaticContent() {
    WA_String::ncat( '<div'  );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->name );
    WA_String::catpr( 'class', $this->css_class );
    WA_String::cat( '>' );
    
    if( $this->is_checked ) {
	  WA_String::cat( WA_Language::trans( 'YES' ) );
  	  WA_String::cat( '</span>' );
	  
	  $h = new WA_HiddenFieldObject( $this->id, 'on' );
	  $h->getContent();
    }
	else {
	  WA_String::cat( WA_Language::trans( 'NO' ) );	
  	  WA_String::cat( '</div>' );
	}
  }
}

?>
