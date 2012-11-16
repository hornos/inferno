<?php

// TODO: first time set checked! 
class WA_CheckboxFieldObject extends WA_WebObject {

  // type = empty no js syntax validation
  public function __construct( $id = 'WA_CheckboxFieldObject', $checked = true ) {
	parent::__construct( $id, 'WA_CheckboxFieldObject' );
	$this->checked = $checked;
  }

  public function is_empty() {
	return false;
  }

  public function getContent() {
    WA_String::ncat(  '<input type="checkbox"'  );
    WA_String::catpr( 'id', $this->id );
	WA_String::catpr( 'name', $this->id );
    WA_String::catpr( 'class', $this->css_class );
    // WA_String::catpr( 'dojoType', 'Checkbox' );
    
    if( $this->checked ) {
	  WA_String::cat(  ' checked' );
	  // WA_String::catpr(  'checked', 'true' );  
    }

    if( $this->is_readonly ) {
	  WA_String::catpr(  ' readonly', 'readonly' );
	  // WA_String::catpr(  ' disabled', 'disabled' );
    }
    WA_String::cat( ' />' );
  }
  
  public function getStaticContent() {
	if( isset( $this->not_static ) and $this->not_static ) {
	  $this->getContent();
	  return;
	}
    WA_String::ncat( "<span"  );
    WA_String::cat(  " id=\"".$this->id."\" name=\"".$this->id."\"" );
    WA_String::cat(  " class=\"checkbox\">" );
    
    if( $this->checked ) {
	  WA_String::cat(  "<b>Igen</b>" );
	  $val = new WA_HiddenFieldObject( $this->id, 'on' );
	  $val->getContent();
    }
	else {
	  WA_String::cat(  "<b>Nem</b>" );	
	}
    WA_String::cat( "</span>" );
  }
  
  public function GETHtmlValues() {
	if( isset( $this->init_checked ) ) {
	  $this->checked = $this->init_checked;
	}
	else {
  	  if( WA_Session::is_sended( $this->id ) ) {
		$this->checked = true;
	  }
	  else {
		$this->checked = false;
	  }
	}
  }

}

?>
