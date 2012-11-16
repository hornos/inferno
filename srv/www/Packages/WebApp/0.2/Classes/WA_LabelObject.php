<?php

class WA_LabelObject extends WA_WebObject {
  public function __construct( $id = 'WA_LabelObject', $label = 'Label Text' ) {
	parent::__construct( $id, 'WA_LabelObject' );
	$this->set_val( $label );
  }
  
  public function getContent( $i = 0, $j = 0 ) {
	echo '<div class="' . $this->css_class . '">' . $this->get_val() . '</div>';
  }
  
  public function getStaticContent( $i = 0, $j = 0 ) {
	$this->getContent( $i, $j );
  }
  
  public function setLabel( $v ) {
	if( WA_String::nzstr( $v ) ) {
	  $this->set_val( $v );
	}
  }
  
  public function is_empty() {
	return true;
  }
}

?>
