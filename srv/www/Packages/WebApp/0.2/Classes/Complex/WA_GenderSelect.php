<?php

class WA_GenderSelect extends WA_MultiRadioObject {
  public function __construct( $id = 'WA_GenderSelect', $selected = 'M' ) {
	parent::__construct(  $id, 'Neme:', array( 'M' => 'Férfi', 'F' => 'Nő' ), $selected, 'horizontal' );
  }
  
}

?>
