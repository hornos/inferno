<?php

class WA_WebObject {
  // Private Variables
  
  // Public Variables
  public $id;
  public $type;
  public $css_class;
  public $ca;
  
  // Flags
  public $is_disabled;
  public $is_valid;
  public $is_required;
  public $is_readonly;
  public $is_empty;
  
  // Values
  public $values;		// array

  // Function Callbacks
  public $validate_type;
  public $f_validate;
  public $f_getvalues;
  public $f_genhtml;


  // Constructor
  public function __construct( $id = 'WA_WebObject', $type ='WA_WebObject' ) {
	$this->id   = $id;
    $this->type = $type;
	$this->css_class = $type;
	$this->ca = array( 'trim' );
	
	// Variables init
	// Flags
	$this->is_disabled	= false;
	$this->is_valid		= true;
	$this->is_required	= false;
	$this->is_readonly	= false;
	
	// Values
	$this->values		= array( 0 => array( 'value' => '', 'sqlbind' => false, 'sqlfmt' => true, 'sqlid' => '' ) );		// array: sqlfield => val, sqlbind
	$this->is_valsqlrow = false;
	// Function Callbacks
	$this->validate_type = '';
	$this->f_validate	 = 'validator';
	$this->f_getvalues	 = 'GetHtmlValues';
	$this->f_genhtml	 = '';
	
  }	// End Constructor


  // Public Functions
  public function sqlid() {
	if( isset( $this->id_sql ) ) {
	  return $this->id_sql;
	}
	return $this->id;
  }

  public function set_sqlid( $id ) {
	$this->id_sql = $id;
  }

  public function enable() {
    $this->is_disabled = false;
  }

  public function disable() {
    $this->is_disabled = true;
  }

  public function is_empty() {
	$is_empty = true;
	
	foreach( $this->values as $k => $v ) {
	  $a = $this->values[$k];
	  $is_empty = $is_empty && WA_String::zstr( $a['value'] );
	}
	
	return $is_empty;
  }

  public function set_conv( $ca = array() ) {
	$this->ca = array_merge( array( 'trim' ), $ca );
  }

  public function set_val( $v, $id = 0, $sb = '', $sf = false ) {
	$this->values[$id] = array( 'value' => $v, 'sqlbind' => $sb, 'sqlfmt' => $sf );
  }

  public function set_init_val( $v, $id = 'init', $sb = '', $sf = false ) {
	$this->values[$id] = array( 'value' => $v, 'sqlbind' => $sb, 'sqlfmt' => $sf );
  }

  public function set_default_val( $v, $id = 'default', $sb = '', $sf = false ) {
	$this->values[$id] = array( 'value' => $v, 'sqlbind' => $sb, 'sqlfmt' => $sf );
  }

  public function init() {
	if( isset( $this->values['init'] ) ) {
	  // echo "<br>id: " . $this->id . "  init: " . $this->get_val( 'init' );
	  $this->set_val( $this->get_val( 'init' ) );
	}
  }

  public function get_val( $id = 0 ) {
	if( isset( $this->values[$id] ) ) {
	  $a = $this->values[$id];
	  $v = $a['value'];
	  $v = str_replace( '\'', '', $v );

	  foreach( $this->ca as $conv ) {
		if( function_exists( $conv ) ) {
		  $v = $conv( $v );
		}
	  }
	  return $v;
	}
	throw new Exception( 'Index out of range in ' . $this->id . '->get_val at ' . $id . ' !' );
  }

  // SQL handling
  public function is_sqlbind( $id ) {
	if( isset( $this->values[$id] ) ) {
  	  $a = $this->values[$id];
	  return $a['sqlbind'];
	}
	throw new Exception( 'Index out of range in ' . $this->id . '->is_sqlbind at ' . $id . ' !' );
  }

  public function set_sqlbind( $id, $v ) {
	if( isset( $this->values[$id] ) ) {
  	  $a = &$this->values[$id];
	  $a['sqlbind'] = $v;
	}
	throw new Exception( 'Index out of range in ' . $this->id . '->set_sqlbind at ' . $id . ' !' );
  }
  
  public function and_valid( $v ) {
    $this->is_valid = $this->is_valid && $v;
  }

  
  public function setValidateFunc( $f ) {
	$this->f_validate = $f;
  }

  public function setGetvaluesFunc( $f ) {
	$this->f_getvalues = $f;
  }

  public function setGenhtmlFunc( $f ) {
	$this->f_genhtml = $f;
  }


  public function genQueryHeader() {
	$header = '';
	$first  = true;
	
	foreach( $this->values as $k => $v ) {
	  $a = $this->values[$k];
	  if( $a['sqlbind'] ) {
		if( $first ) {
		  $header = $k;
		  $first = false;
		}
		else {
		  $header .= ', '.$k;
		}
	  }
	}
	
	return $header;
  }

  public function genQueryFooter() {
	$footer = '';
	$first  = true;
	
	foreach( $this->values as $k => $v ) {
	  $a = $this->values[$k];
	  if( $a['sqlbind'] ) {
		if( $first ) {
		  if( $a['sqlfmt'] ) {
			$footer = WA_String::sqlfmt( $a['value'] );
		  }
		  else {
			$footer = $a['value'];			
		  }
		  $first = false;
		}
		else {
		  if( $a['sqlfmt'] ) {
			$footer = ', ' . WA_String::sqlfmt( $a['value'] );
		  }
		  else {
			$footer = ', ' . $a['value'];			
		  }
		}
	  }
	}
	
	return $footer;
  }


  public function Validate() {
	if( $this->is_disabled ) {
	  return false;
	}
	
	if( ! $this->is_required && $this->is_empty() ) {
	  $this->is_valid = true;

	  // echo "<br>Validation: " . $this->id . "  value: " . WA_String::nft( $this->is_valid );
	  return $this->is_valid;
	}
	
	$f = $this->f_validate;
	if( is_callable( array( $this, $f ) ) ) {
	  $this->is_valid = $this->$f();

	  // echo "<br>Validation: " . $this->id . "  value: " . WA_String::nft( $this->is_valid );
	  return $this->is_valid;
	}
	else {
	  $this->is_valid = true;
	  
	  // echo "<br>Validation: " . $this->id . "  value: " . WA_String::nft( $this->is_valid );	  
	  return $this->is_valid;
	}
  }


  public function GETValues() {
	if( $this->is_disabled ) {
	  return;
	}
	
	$f = $this->f_getvalues;
	if( is_callable( array( $this, $f ) ) ) {
	  $this->$f();
	}
  }
  

  public function GETHtmlValues() {
	// echo "<br>id: " . $this->id . " gethml";

  	if( WA_Session::is_sended( $this->id ) ) {
	  // echo "<br>id: " . $this->id . "  value: " . trim( WA_Session::get_sended( $this->id ) );
	  $this->set_val( trim( WA_Session::get_sended( $this->id ) ) );
	}
  }


  // HTML interface
  public function getContent( $i = 0, $j = 0 ) {
	echo "Empty Object: " . $this->id;
  }

  public function getStaticContent( $i = 0, $j = 0 ) {
	echo "Empty Static Object: " . $this->id;
  }
  
  public function preHTML() { ; }
  public function postHTML() { ; }
 
  public function genHTML( $s = false ) {
	if( $this->is_disabled ) {
	  return;
	}
	if( $s ) {
	  $this->getStaticContent();
	}
	else {
	  $this->getContent();
	}
  }
  
  public function genStaticHTML() {
	$this->genHTML( true );
  }

  public function enableErrors() {
	if( array_key_exists( 'error', get_object_vars( $this ) ) ) {
	  $this->error->enable();
	}
  }

  public function disableErrors() {
	if( array_key_exists( 'error', get_object_vars( $this ) ) ) {
	  $this->error->disable();
	}
  }

  public function validator() {
	if( WA_String::nzstr( $this->validate_type ) ) {
	  $f = $this->validate_type;
	  // echo "  [".$f."]";

	  return WA_Validator::$f( $this->get_val() );
	}
	else {
	  return true;
	}
  }


//  FIXME: sentenced to remove
//  TODO: Better & Clearer validator integration  
//  public function Validator_alpha() {
  
	// debug
/*	if( is_debug() ) {
	  echo "<br>value: ".$this->getValue();
	  if( Validator::isGenHuns( $this->getValue() ) ) {
		echo "   ok";
	  }
	  else {
		echo "   err";
	  }
	}
*/	// debug end
/*	
	$this->setValid( WA_Validator::isGenHuns( $this->getValue() ) );
  }


  public function Validator_general() {
	$this->setValid( WA_Validator::isGen( $this->getValue() ) );
  }


  public function Validator_ALNUM() {
	$this->setValid( WA_Validator::isALNUM( $this->getValue() ) );
  }

  public function Validate_email() {
	$this->setValid( WA_Validator::isEmail( $this->getValue() ) );
  }

  public function Validator_numeric() {
	$this->setValid( WA_Validator::isNum( $this->getValue() ) );
  }

  public function Validator_year() {
	$this->setValid( checkdate( 1, 1, $this->getValue() ) );
  }

  public function Validator_date() {
	if( is_debug() ) {
	  echo "<br>DATE VALIDATOR";
	}
	$this->setValid( checkdate( (int)$this->getMonth(), (int)$this->getDay(), (int)$this->getYear() ) );
  }
*/


  // Cheap hack
  public function setLabelWidth( $s ) {
	if( isset( $this->cont ) ) {
	  $this->cont->tdwidth[0] = $s;
	  $this->label_width = $s;
	}
  }

  public function setLabelAlign( $a ) {
	if( isset( $this->cont ) ) {
	  $this->cont->align[0] = $a;
	  $this->label_align = $a;	  
	}
  }

  public function setLabelClass( $a ) {
	if( isset( $this->label ) ) {
	  $this->label->css_class = $a;
	}
  }

}

?>
