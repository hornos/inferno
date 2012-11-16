<?php

class WA_ComboFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_ComboFieldObject', $values = array( 'pussy', 'cat' ), $selected = 1 ) {
	parent::__construct( $id, 'WA_ComboFieldObject' );
	$this->selected = $selected;
	
	$i = 0;
	foreach( $values as $v ) {
	  $this->set_val( $v, $i );
	  ++$i;
	}
  }

  public function is_empty() {
	return false;
  }

  public function get_val() {
	$i = 1;
	foreach( $this->values as $v ) {
	  if( $i == $this->selected ) {
		return $v['value'];
	  }
	  ++$i;
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
    
    WA_String::catpr( 'class', $this->css_class );
    WA_String::catn( '/>' );

	$i = 1;
	foreach( $this->values as $v ) {
	  if( $i == $this->selected ) {
		WA_String::ncat( '<option value="'.$i.'" selected="selected">'.$v['value'] );
	  }
	  else {
		WA_String::ncat( '<option value="'.$i.'">'.$v['value'] );	  
	  }
	  ++$i;
	}
    WA_String::ncat( '</select>'  );
  }


  public function getStaticContent() {
    WA_String::ncat( '<div class="combostat"'  );
    WA_String::cat(  ' id="'.$this->id.'" name="'.$this->id.'">' );

	$i = 1;	
	foreach( $this->values as $v ) {
	  if( $i == $this->selected ) {
		WA_String::cat( $v['value'] );
	  }
	  ++$i;
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
