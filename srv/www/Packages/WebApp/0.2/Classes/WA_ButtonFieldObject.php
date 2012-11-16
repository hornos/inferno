<?php

class WA_ButtonFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_ButtonFieldObject', $value = "Submit" ) {
	parent::__construct( $id, 'WA_ButtonFieldObject' );
	$this->set_val( $value );
  }
  
  public function getContent() {
    WA_String::ncat( '<input type="submit"'  );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->id );
    WA_String::catpr( 'class', $this->css_class );
    WA_String::catpr( 'value', $this->get_val() );
    WA_String::cat( ' />' );
  }
  
  public function getStaticContent() {
	$this->getContent();
  }
}

?>
