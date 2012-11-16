<?php

class WA_HashComboFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_HashComboFieldObject', $values = array( 'pussy' => 'Cica', 'cat' => 'Mica' ), $selected = 'pussy' ) {
	parent::__construct( $id, 'WA_HashComboFieldObject' );
	$this->selected = $selected;
	
	$this->values = array();
	foreach( $values as $k => $v ) {
	  $this->set_val( $v, $k );
	}
  }

  public function setOnChange( $onc = '' ) {
	$this->onc = $onc;
  }

  public function is_empty() {
	return false;
  }

  public function get_val() {  
	foreach( $this->values as $k => $v ) {
	  if( $k == $this->selected ) {
		return $v['value'];
	  }
	}
  }

  public function getContent() {
    WA_String::ncat( '<select'  );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->id );

    if( $this->is_disabled ) {
	  WA_String::catpr(  ' disabled', 'disabled' );
    }
    
    if( $this->is_readonly ) {
	  WA_String::catpr(  ' readonly', 'readonly' );
    }    

	if( isset( $this->onc ) ) {    
	  if( WA_String::nzstr( $this->onc ) ) {
		WA_String::catpr( ' onchange', $this->onc );
	  }
	}
	
    WA_String::catpr( 'class', $this->css_class );
    WA_String::catn( '/>' );
	
	foreach( $this->values as $k => $v ) {
	  if( $k == $this->selected ) {
		WA_String::ncat( '<option value="'.$k.'" selected="selected">'.$v['value'] );
	  }
	  else {
		WA_String::ncat( '<option value="'.$k.'">'.$v['value'] );	  
	  }
	}
    WA_String::ncat( '</select>'  );
  }


  public function getStaticContent() {
    WA_String::ncat( '<div class="combostat"'  );
    WA_String::cat(  ' id="'.$this->id.'" name="'.$this->id.'">' );
	
	foreach( $this->values as $k => $v ) {
	  if( $k == $this->selected ) {
		WA_String::cat( $v['value'] );
	  }
	}
    WA_String::cat( '</div>' );

	$h = new WA_HiddenFieldObject( $this->id, $this->selected );
	$h->getContent();
  }

  public function GETHtmlValues() {
  	if( WA_Session::is_sended( $this->id ) ) {
	  $this->selected = trim( WA_Session::get_sended( $this->id ) );
	}
  }
}

?>
