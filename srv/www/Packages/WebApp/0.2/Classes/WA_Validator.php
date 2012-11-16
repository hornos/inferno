<?php

class WA_Validator {
  public static $hunw = "([a-z]|(\xC3[\x9F-\xB6\xB8-\xBF])|(\xC5[\xB1\x91]))+";
  public static $HUNw = "([A-Z]|(\xC3[\x81-\x96\x98-\x9E])|(\xC5[\xB0\x90]))+([a-z]|(\xC3[\x9F-\xB6\xB8-\xBF])|(\xC5[\xB1\x91]))*";
  public static $Hunw = "([A-Z]|(\xC3[\x81-\x96\x98-\x9E])|(\xC5[\xB0\x90]))([a-z]|(\xC3[\x9F-\xB6\xB8-\xBF])|(\xC5[\xB1\x91]))+";
  public static $HUNw123 = "(([A-Z0-9\-\.\/]|(\xC3[\x81-\x96\x98-\x9E])|(\xC5[\xB0\x90]))|([a-z0-9\-\.\/]|(\xC3[\x9F-\xB6\xB8-\xBF])|(\xC5[\xB1\x91])))+";

  public function __construct() {
	;
  }

  public static function isHun( $str ) {
	return preg_match( "/^".self::$Hunw."$/", $str );
  }

  public static function alpha( $str ) {
	//return WA_String::nzstr( $str );
	return preg_match( "/^".self::$Hunw."[\s-]*(".self::$Hunw.")*$/", $str );
  }

  public static function genalpha( $str ) {
	$Huns = "(".self::$HUNw."\s*)+";  
	return preg_match( "/^(".$Huns."(\s*,\s*".$Huns.")*)+$/", $str );	  
  }

  public static function isHuns( $str ) {
	$Huns = "(".self::$Hunw."\s*)+";
	return preg_match( "/^".$Huns."$/", $str );	
  }

  public static function alnum( $str ) {
	$Huns = "(".self::$HUNw123."\s*)+";  
	return preg_match( "/^(".$Huns."(\s*,\s*".$Huns.")*)+$/", $str );	  
  }
  
  public static function alnum_en( $str ) {
	return preg_match( "/^[a-zA-Z0-9]+$/", $str );
  }
  
  public static function numeric( $str ) {
	return preg_match( "/^[0-9]+$/", $str );
  }

  public static function ipf( $str ) {
	if( WA_Validator::numeric( $str ) ) {
	  $v = (int)$str;
	  if( $v >= 0 and $v <= 255 ) {
		return true;
	  }
	}
	return false;
  }

  public static function hostname( $str ) {
	return preg_match( "/^[a-z0-9]+([0-9]*[a-z\-]*)*$/", $str );
  }

  public static function cname( $str ) {
	return preg_match( "/^[a-z0-9]+([0-9]*[a-z\-]*\.[0-9]*[a-z\-]*)*$/", $str );
  }


  public static function hex( $str ) {
	return preg_match( "/^[0-9abcdefABCDEF][0-9abcdefABCDEF]$/", $str );
  }

  public static function mac1( $str ) {
	return preg_match( "/^[0-9a-fA-F][0-9a-fA-F]\:[0-9a-fA-F][0-9a-fA-F]\:[0-9a-fA-F][0-9a-fA-F]\:[0-9a-fA-F][0-9a-fA-F]\:[0-9a-fA-F][0-9a-fA-F]\:[0-9a-fA-F][0-9a-fA-F]$/", $str );
  }

  public static function email( $str ) { 
	//$ch = "[-!#$%&'*+/0-9=?A-Z^_`a-z{|}~]"; 
	//return ereg('^'.$ch.'+(('.$ch.'+.+'.$ch.'+$', $address);
	return preg_match( "/^([A-Za-z0-9_]+(\.){0,1})+\@([A-Za-z0-9_]+(\.){0,1})+[A-Za-z0-9_]+$/", $str );
  }
  
  public static function year( $str ) {
	if( preg_match( "/^[0-9]+$/", $str ) ) {
	  return checkdate( 1, 1, $str );
	}
	return false;
  }

  public static function dateymd( $y, $m, $d ) {
	$m = $m + 0;
	$d = $d + 0;
	$y = $y + 0;	
	return checkdate( $m, $d, $y );
  }

  public static function general( $str ) {
	return true;
  }
  
  public static function is_roomid( $str ) {
	return preg_match( "/^[A-Z0-9]*$/", $str );	
  }
  
  public static function is_port( $str ) {
	return preg_match( "/^[A-Z0-9]*$/", $str );	
  }
  
}

?>
