<?php

class WA_ContainerObject extends WA_WebObject {
  public $objects  = array();  

  public function __construct( $id = 'WA_ContainerObject', $layout = 'vertical' ) { 
	parent::__construct( $id, 'WA_ContainerObject' );

	$this->sql_merge = false;
		
	$this->rows = 0;
	$this->cols = 0;

	$this->layout = $layout;


	if( $this->layout == 'vertical' ) {;
	  $this->cols = 1; 
	}
	else {
	  $this->rows = 1;
	}
  }


  // Public Functions
  public function is_empty() {
	$is_empty = parent::is_empty();

	foreach( $this->objects as $k => $o ) {
	  $is_empty = $is_empty && $o->is_empty();
	}
	return $is_empty;
  }

  public function init() {
	parent::init();
	
	foreach( $this->objects as $k => $o ) {
	  $o->init();
	}	
  }


  public function is_vertical() {
	if( $this->layout == 'vertical' ) {
	  return true;
	}
	return false;
  }

  public function is_horizontal() {
	return ! $this->is_vertical();
  }


  public function putObject( $o ) {
	// echo '<br>id: '.$o->id;
	$this->objects[$o->id] = $o;
	
	if( $this->is_vertical() ) {
  	  ++$this->rows;
	}
	else {
	  ++$this->cols;
	}
  }

  public function putObjectArray( $a ) {
	foreach( $a as $o ) {
	  $this->putObject( $o );
	}
  }

  public function delObject( $id ) {
	if( in_array( $id, $this->objects ) ) {
	  unset( $this->objects[$id] );
	}

	if( $this->is_vertical() ) {
  	  --$this->rows;
	}
	else {
	  --$this->cols;
	}
  }
  
  public function getObject( $id ) {
	if( isset( $this->objects[$id] ) ) {
	  return $this->objects[$id];
	}
	throw new Exception( 'Undefined object id in ' . $this->id . ' ->getObject at ' . $id . ' !' );
  }


  public function setLabelWidth( $s ) {
	parent::setLabelWidth( $s );
	
	foreach( $this->objects as $k => $o ) {
	  $o->setLabelWidth( $s );
	}
  }

  
  public function getContent( $i, $j ) {
	$keys = array_keys( $this->objects );
  
	if( $this->is_vertical() ) {
	  $o = $this->objects[$keys[$i]];
	}
	else {
	  $o = $this->objects[$keys[$j]];	  
	}
	
    $o->genHTML();
  }

  public function getStaticContent( $i, $j ) {
	$keys = array_keys( $this->objects );
  
	if( $this->is_vertical() ) {
	  $o = $this->objects[$keys[$i]];
	}
	else {
	  $o = $this->objects[$keys[$j]];	  
	}

    $o->genStaticHTML();
  }


  public function GETHtmlValues() {
	foreach( $this->objects as $k => $o ) {
	  $o->GETHtmlValues();
	}
  }

  public function Validate() {
	parent::Validate();
	
	foreach( $this->objects as $k => $o ) {
	  $o->Validate();
	  
	  $this->and_valid( $o->is_valid );
	}
	
	return $this->is_valid;	
  }

  public function enableErrors() {
	foreach( $this->objects as $k => $o ) {
	  $o->enableErrors();
	}
	parent::enableErrors();	
  }

  public function disableErrors() {
	foreach( $this->objects as $k => $o ) {
	  $o->disableErrors();
	}
	parent::disableErrors();	
  }

  public function genHTML() {
	$this->_genHTML( false );
  }
  
  public function genStaticHTML() {
	$this->_genHTML( true );
  }
  
  
  // PRIVATE
  private function _genHTML( $static ) {
	if( $this->is_disabled ) {
	  return;
	}
	
    $this->preHTML();

    $i = 0;	$j = 0;
    
    $r = $this->rows;
    $c = $this->cols;
        
    WA_String::ncat(  '<table' );
    WA_String::catpr( 'id', $this->id );    
    WA_String::catpr( 'border', 0 );
    WA_String::catpr( 'cellpadding', 0 );
    WA_String::catpr( 'cellspacing', 0 );
    // WA_String::catpr( 'width', 0 );
    WA_String::catpr( 'class', $this->css_class );
    WA_String::cat( ' >');
    
    
    for( $i = 0; $i < $r; ++$i ) {
	  WA_String::ncat(  '<tr' );
	  WA_String::catpr( 'id', 'tr_' . $this->id );
	  WA_String::catpr( 'class', 'tr_' . $this->css_class );
	  WA_String::cat( ' >');

	  for( $j = 0; $j < $c; ++$j ) {
		
		// separator
		if( isset( $this->sepstr ) && $this->is_horizontal() && WA_String::nzstr( $this->sepstr ) ) {
		  if( $j > 0 && $j < $c ) {
			WA_String::ncat(  '<td' );
			WA_String::catpr( 'align', 'center' );
			if( isset( $this->tdclass[$j] ) ) {
	  		  WA_String::catpr( 'class', $this->tdclass[$j] );
			}
			else {
			  WA_String::catpr( 'class', 'sep_' . $this->css_class );
	  		}
			WA_String::catn( ' >' );  
	  		WA_String::cat( $this->sepstr );
	  		WA_String::ncat( '</td>' );
		  }
		}
		
		// field
	    WA_String::ncat( '<td' );
	    
		if( isset( $this->align[$j] ) ) {
		  WA_String::catpr( 'align', $this->align[$j] );
	    }
	    else {
		  WA_String::catpr( 'align', 'left' );		
	    }

		if( isset( $this->valign[$j] ) ) {
		  WA_String::catpr( 'valign', $this->valign[$j] );
	    }
	    else {
		  WA_String::catpr( 'valign', 'middle' );		
	    }
	    
		WA_String::catpr( 'id', 'td_' . $this->id . "_td" );
	    WA_String::catpr( 'class', 'td_' . $this->css_class );
	    
		if( isset( $this->tdwidth[$j] ) ) {
		  WA_String::catpr( 'width', $this->tdwidth[$j] );
	    }

		if( isset( $this->tdheight[$j] ) ) {
		  WA_String::catpr( 'height', $this->tdwidth[$j] );
	    }
		
	    WA_String::catn( '>');
		
		if( $static ) {
		  $this->getStaticContent( $i, $j );
		}
		else {
	  	  $this->getContent( $i, $j );
		}
	    WA_String::ncat( '</td>' );
	  }
	  // end for
	  WA_String::ncat( '</tr>' );
    }
    WA_String::ncatn( '</table>' );
    
    $this->postHTML();
  }
  // genHTML end


  // SQL binding
  public function genQueryHeader() {
	return $this->sqlid();
  }
  
  public function genStoreQueryString( $table ) {
	$qheader = 'INSERT INTO '.$table.' (';
	$qfooter = ' VALUES (';
	$i = 0;

	foreach( $this->objects as $k => $o ) {
	  if( $i > 0 ) {
		$qheader .= ', ';
		$qfooter .= ', ';
	  }

	  if( ! $o->is_disabled && ! $o->is_empty() ) {
		$qheader .= $o->sqlid();
		$qfooter .= $o->genQueryFooter();
		  
		$i += 1;
	  }
	}
	
	$qheader .= ')';
	$qfooter .= ')';
	
	return $qheader.$qfooter;
  }
}

?>
