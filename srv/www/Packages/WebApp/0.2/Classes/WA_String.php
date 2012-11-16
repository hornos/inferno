<?php

class WA_String {

  public function __construct( $str="string" ) {
	self::setbuf( $str );
  }

  public static function zstr( $str ) {
	if( strcmp( $str, "" ) == 0 ) {
	  return True;
	}
	return False;
  }

  public static function nzstr( $str ) {
	return ! self::zstr( $str );
  }
  
  public static function nft( $bool ) {
	if( $bool ) return 1;
	return 0;
  }
  
  
  public static function cat( $str ) {
	echo $str;
  }
  
  public static function ncat( $str ) {
	self::cat( "\n" . $str );
  }

  public static function catn( $str ) {
	self::cat( $str . "\n" );
  }

  public static function ncatn( $str ) {
	self::cat( "\n" . $str . "\n" );
  }


  public static function catpr( $pr, $v ) {
	if( self::nzstr( $v ) ) {
	    self::cat( ' '. $pr . '="' . $v . '"' );
	}
  }

  public static function catjs( $pr, $v ) {
	if( self::nzstr( $v ) ) {	
	  self::cat( $pr . '=\'' . $v . '\' ' );
	}
  }

  public static function sqlfmt( $str ) {
	return '\''.$str.'\'';
  }

  public static function sqlfmt2( $str ) {
	return '\''.str_replace( "'", "\'", $str).'\'';
  }  

  public static function suffx( $str ) {
	return '<span class="suffx">'.$str.'</span>';
  }

  public static function zpad( $str ) {
	if( strlen( $str ) == 1 ) {
	  return '0'.$str;
	}
	return $str;
  }

  public static function geneap() {
	$rnd = substr( md5( (double)microtime() * 1000000 ), 0, 8);
	return $rnd;
  }
  
  public static function timenow() {
	return date( 'Y-m-d H:i:s T' );
  }

  public static function print_div( $s, $d ) {
	echo '<div class="'.$d.'">'.$s.'</div>';
  }

  public static function print_error( $s ) {
	WA_String::print_div( $s, 'error' );
  }

  public static function print_info( $s ) {
	WA_String::print_div( $s, 'info' );
  }

  public static function print_spaninfo( $s ) {
	echo '<br><span class="info">'.$s.'</span>';
  }

  public static function stri_replace( $find, $replace, $string ) {
	// Case-insensitive str_replace()
	$parts = explode( strtolower($find), strtolower($string) );

	$pos = 0;

	foreach( $parts as $key=>$part ){
  	  $parts[ $key ] = substr($string, $pos, strlen($part));
  	  $pos += strlen($part) + strlen($find);
	}

	return( join( $replace, $parts ) );
  }


  public static function txt2html($txt) {
	// Transforms txt in html

	//Kills double spaces and spaces inside tags.
	// while( !( strpos($txt,'  ') === FALSE ) ) $txt = str_replace('  ',' ',$txt);
    if( !( strpos($txt,'  ') === FALSE ) ) $txt = str_replace('  ',' ',$txt);

	$txt = str_replace(' >','>',$txt);
	$txt = str_replace('< ','<',$txt);

	//Transforms accents in html entities.
	$txt = utf8_decode($txt);
	$txt = htmlentities($txt);

	//We need some HTML entities back!
	$txt = str_replace('&quot;','"',$txt);
	$txt = str_replace('&lt;','<',$txt);
	$txt = str_replace('&gt;','>',$txt);
	$txt = str_replace('&amp;','&',$txt);
	
	//Ajdusts links - anything starting with HTTP opens in a new window
	$txt = WA_String::stri_replace("<a href=\"http://","<a target=\"_blank\" href=\"http://",$txt);
	$txt = WA_String::stri_replace("<a href=http://","<a target=\"_blank\" href=http://",$txt);

	//Basic formatting
	$eol = ( strpos($txt,"\r") === FALSE ) ? "\n" : "\r\n";
	$html = '<p>'.str_replace("$eol$eol","</p><p>",$txt).'</p>';
    $html = str_replace("$eol","<br />\n",$html);
	$html = str_replace("</p>","</p>\n\n",$html);
	$html = str_replace("<p></p>","<p>&nbsp;</p>",$html);

	//Wipes <br> after block tags (for when the user includes some html in the text).
	$wipebr = Array("table","tr","td","blockquote","ul","ol","li");

	for($x = 0; $x < count($wipebr); $x++) {
  	  $tag = $wipebr[$x];
  	  $html = WA_String::stri_replace("<$tag><br />","<$tag>",$html);
  	  $html = WA_String::stri_replace("</$tag><br />","</$tag>",$html);
	}

	return $html;
  }

  
}

?>
