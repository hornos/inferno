<?php

class WA_HiddenFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_HiddenFieldObject', $value = 0 ) {
	parent::__construct( $id, 'WA_HiddenFieldObject' );
	$this->set_val( $value );
  }
  
  public function getContent() {
    WA_String::ncat(  '<input type="hidden"' );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->id );
    WA_String::catpr( 'value', $this->get_val() );
    WA_String::cat( ' />' );
  }

  public function getStaticContent() {
	$this->getContent();
  }

}

?>
