<?php


class WA_Language {
  
  public function __construct() {
	;
  }

  public static function trans( $id ) {
	global $dict_hun;
	return $dict_hun[$id];
  }
}

?>
