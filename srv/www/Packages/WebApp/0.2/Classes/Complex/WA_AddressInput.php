<?php

class WA_AddressInput extends WA_ContainerObject {

  public function __construct( $id = 'WA_AddressInput', $title = "Address", $required = true ) {
	parent::__construct( $id, 'vertical' );
	
	$this->is_required = $required;
	
	$this->descr = new WA_LabelObject( $id."_descr", 'Description' );
	$this->error = new WA_LabelObject( $id."_error", 'Error' );

	$this->fields_cont  = new WA_ContainerObject( $id."_fields_cont", 'vertical' );
	
	$this->title = new WA_LabelObject( $id.'_title', $title );	
	$this->title->css_class = 'cat_label';
	
	$this->street = new WA_InputObject( $id.'_street', 'Utca, Házszám:', array( 'general' ), array( $required ), array( 35 ) );
	$this->street->set_sqlid( 'street' );
	
	$this->city    = new WA_InputObject( $id.'_city', 'Város:', array( 'general' ), array( $required ), array( 35 ) );
	$this->city->set_sqlid( 'city' );

	$this->state   = new WA_InputObject( $id.'_state', 'Állam/Megye:', array( 'general' ), array( $required ), array( 35 ) );
	$this->state->set_sqlid( 'state' );

	$this->zip     = new WA_InputObject( $id.'_zip', 'Irányítószám:', array( 'general' ), array( $required ), array( 35 ) );
	$this->zip->set_sqlid( 'zip' );

	$this->country = new WA_InputObject( $id.'_country', 'Ország:', array( 'general' ), array( $required ), array( 35 ) );
	$this->country->set_sqlid( 'country' );
	$this->country->set_init_val( 0, 'Magyarország' );

	$this->islett  = new WA_CheckboxObject( $id.'_islett', 'Levelezési cím is?', true );
	$this->islett->set_sqlid( 'islett' );


	$this->fields_cont->putObjectArray( array( $this->title, $this->street,
											   $this->city, $this->state,
											   $this->zip, $this->country, $this->islett ) );

	$this->descr->disable();
	$this->error->disable();
	
	$this->putObjectArray( array( $this->descr, $this->fields_cont, $this->error ) );	
  }

  // PRIVATE
  
  // PROTECTED

  // PUBLIC


  public function checksum( $table ) {
	return md5( $this->fields_cont->genStoreQueryString( $table ) );
  }

  public function genStoreQueryString( $table ) {
	$qheader = 'INSERT INTO '.$table.' (addrid';
	$qfooter = ' VALUES (' . WA_String::sqlfmt( $this->checksum( $table ) );
	$i = 1;

	foreach( $this->fields_cont->objects as $k => $o ) {

	  if( ! $o->is_disabled && ! $o->is_empty() ) {
		if( $i > 0 ) {
		  $qheader .= ', ';
		  $qfooter .= ', ';
		}

		$qheader .= $o->sqlid();
		$qfooter .= $o->genQueryFooter();
		  
		$i += 1;
	  }
	}
	
	$qheader .= ')';
	$qfooter .= ')';
	
	return $qheader.$qfooter;
  }

  public function Validate() {
	$this->is_valid = parent::Validate();

	if( $this->is_required ) {
	  $oa = array( $this->street,
				   $this->city, $this->state,
				   $this->zip, $this->country );
				   
	  foreach( $oa as $o ) {
		if( $o->is_empty() ) {
		  $this->is_valid = false;
		  return false;
		}
	  }
	}
	return $this->is_valid;
  }


  public function setLabelWidth( $s ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelWidth( $s );
	}
  }

  public function setLabelAlign( $a ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelAlign( $a );
	}
  }

  public function setLabelClass( $c ) {
	foreach( $this->fields_cont->objects as $o ) {
	  $o->setLabelClass( $c );
	}
  }

  public function init_from_sql( $va ) {
	$this->street->init_from_sql( array( $va[0] ) );
	$this->city->init_from_sql( array( $va[1] ) );
	$this->state->init_from_sql( array( $va[2] ) );
	$this->zip->init_from_sql( array( $va[3] ) );
	$this->country->init_from_sql( array( $va[4] ) );
	$this->islett->setInitChecked( $va[5] );
  }

/*
  public function doValidate() {
	parent::doValidate();

	if( isset( $this->parentaddr ) ) {
	  if( $this->parentaddr->isValid() && $this->parentaddr->isLettAddr() ) {
		$this->setValid( true );
		$this->setAllValid( true );
	  }
	}
  }
  
  public function setParentAddr( $o ) {
	$this->parentaddr = $o;
  }
  
  // SQL INTERFACE
  
  public function genInsert( $table ) {
	$qheader = 'INSERT INTO '.$table.' (';
	$qfooter = ' VALUES (';
	$i = 0;
	
	foreach( $this->objects as $k => $o ) {
	  if( WA_String::nzstr( $o->getSQLField() ) ) {
		if( $i > 0 ) {
		  $qheader .= ',';
		  $qfooter .= ',';
		}

		$qheader .= $o->getSQLField();
		$qfooter .= sqlfmt( $o->getValue( 0 ) );
		$i += 1;
	  }
	}
	
	$qheader .= ')';
	$qfooter .= ')';
	
	return $qheader.$qfooter;
  }
  
  public function genSelect( $table ) {
	$qheader = 'Select aid FROM '.$table;
	$qfooter = ' WHERE ';
	$i = 0;
	
	foreach( $this->objects as $k => $o ) {
	  if( WA_String::nzstr( $o->getSQLField() ) ) {
		if( $i > 0 ) {
		  $qfooter .= ' and ';
		}

		$qfooter .= $o->getSQLField()." = ".sqlfmt( $o->getValue( 0 ) );
		$i += 1;
	  }
	}
	
	return $qheader.$qfooter;
  }

  public function isLettAddr() {
	return $this->islettaddr->isChecked();
  }
  
  public function setAddress( $va ) {
	foreach( $this->objects as $k => $o ) {
	  if( is_callable( array( $o, "setFromQuery" ) ) ) {
		if( isset( $va[$o->genQueryHeader()] ) ) {
		  $o->setFromQuery( $va[$o->genQueryHeader()] );
		}
	  }
	}
  }
*/  
}

?>
