<?php

class WA_HrefObject extends WA_WebObject {
  public function __construct( $id = 'WA_HrefObject', $label = 'Link', $url = '' ) {
	parent::__construct( $id, 'WA_HrefObject' );
	$this->set_val( $label, 0 );
	$this->set_val( $url, 1 );
  }
  
  public function getContent( $i = 0, $j = 0 ) {
	echo '<a class="' . $this->css_class . '" href="'.$this->get_val( 1 ).'">' . $this->get_val( 0 ) . '</a>';
  }
  
  public function getStaticContent( $i = 0, $j = 0 ) {
	$this->getContent( $i, $j );
  }
  
  public function setHref( $v ) {
	$this->set_val( $v, 1 );
  }
  
  public function is_empty() {
	return true;
  }
  
  public function setLink( $l, $u ) {
	$this->set_val( $l, 0 );
	$this->set_val( $u, 1 );	
  }
}

?>
