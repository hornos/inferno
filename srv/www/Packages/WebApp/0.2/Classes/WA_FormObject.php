<?php

class WA_FormObject extends WA_ContainerObject {  
  public $qarray = array();
  
  private $states_arr = array( 'init' => 0, 'edit' => 1, 'check' => 2, 'work' => 3, 'end' => 4 );
  
  public function __construct( $id = 'WA_FormObject', $title = 'Form', $module='none', $opts = array() ) { 
	parent::__construct( $id );
	$this->title = $title;
	$this->action = "";
	$this->opts = $opts;

	$this->state  = new WA_HiddenFieldObject( 'state', $this->states_arr['init'] );
	$this->putObject( $this->state );

	$this->module = new WA_HiddenFieldObject( 'module', $module );
	$this->putObject( $this->module );

	$this->action = new WA_HiddenFieldObject( 'action', '' );
	$this->putObject( $this->action );

	$this->title  = new WA_LabelObject( $this->id.'_title', $title );
	$this->title->css_class = 'form_title';
	
	$this->putObject( $this->title );

	$this->descr  = new WA_LabelObject( $this->id.'_descr', "Description" );
	$this->descr->disable();
	$this->putObject( $this->descr );
	
	$this->cont   = new WA_ContainerObject( $this->id.'_cont', 'vertical' );
	$this->putObject( $this->cont );
	
	$this->is_valid = true;	
	// session handling
  }
  
  public function putContentObject( $o ) {
	$this->cont->putObject( $o );
  }
  
  public function delContentObject( $id ) {
	$this->cont->delObject( $id );
  }
  
  public function getContentObject( $id ) {
	return $this->cont->getObject( $id );
  }
  
  public function getChildObject( $parent, $child ) {
	$o = $this->getContentObject( $parent );
	return $o->getObject( $child );
  }
  
  public function putContentObjectArray( $a ) {
	foreach( $a as $o ) {
	  $this->putContentObject( $o );
	}
  }
  
  public function setState( $v ) {
	if( array_key_exists( $v, $this->states_arr ) ) {
	  $this->state->set_val( $this->states_arr[$v] );
	}
	else {
	  $this->state->set_val( $v );	
	}
  }

  public function getState() {
	return $this->state->get_val();
  }

  public function isState( $id ) {
	if( $this->getState() == $this->states_arr[$id] ) {
	  return true;
	}
	return false;
  }

  public function isopt( $k, $v ) {
	if( isset( $this->opts[$k] ) ) {
	  if( $this->opts[$k] == $v ) {
		return true;
	  }
	  return false;
	}
	return false;
  }

  public function opt( $k ) {
	if( isset( $this->opts[$k] ) ) {
	  return $this->opts[$k];
	}
  }
  
  public function Validate() {
	$this->state->GETHtmlValues();

	if( $this->isState( 'init' ) ) {
	  $this->init();
	}

	if( ! $this->isState( 'init' ) ) {
	  $this->GETHtmlValues();
	}
	parent::Validate();
  }

  public function pValidate() {
	parent::Validate();
  }

  public function setAction( $s ) {
	$this->action->set_val( $s );
  }

  public function getAction() {
	return $this->action->get_val();
  }


  public function setDescription( $str ) {
	// TODO: zstr check
	$this->descr->setLabel( $str );
	$this->descr->enable();
  }
  
  public function preHTML() {
    WA_String::ncat( '<form name="'.$this->id.'" method="post" action="">' );
  }
  
  public function postHTML() {
    WA_String::ncat( '</form>' );
  }

  public function enableErrors() {
	$this->cont->enableErrors();
  }

  public function disableErrors() {
	$this->cont->disableErrors();
  }


  public function setLabelWidth( $s ) {
	foreach( $this->cont->objects as $k => $o ) {
	  if( is_callable( array( $o, 'setLabelWidth' ) ) ) {
		$o->setLabelWidth( $s );
	  }
	}
  }

  public function setLabelAlign( $a ) {
	foreach( $this->cont->objects as $k => $o ) {
	  if( is_callable( array( $o, 'setLabelAlign' ) ) ) {
		$o->setLabelAlign( $a );
	  }
	}
  }

  public function setLabelClass( $c ) {
	foreach( $this->cont->objects as $k => $o ) {
	  if( is_callable( array( $o, 'setLabelClass' ) ) ) {
		$o->setLabelClass( $c );
	  }
	}
  }


  // SQL binding
  public function genQueryArray( $exlist = array(), $isempty = false ) {
	$this->qarray = array();

	foreach( $this->cont->objects as $k => $o ) {
	  // echo '<br>id: ' . $o->id . ' ';
	  // if( $o->is_empty() ) { echo 'empty'; } else { echo 'full'; }
	  if( $isempty ) {
		if( ! in_array( $k, $exlist ) ) {
		  //echo '<br>id: '.$o->id.'   '.$o->genQueryHeader();
		  if( $o->genQueryHeader() != "" ) {
			$this->qarray[$o->genQueryHeader()] = $o->genQueryFooter();
		  }
		}
	  }
	  else {
		if( ! in_array( $k, $exlist ) && ! $o->is_empty() ) {
		  $this->qarray[$o->genQueryHeader()] = $o->genQueryFooter();
		}	  
	  }
	}	
  }


  public function putQueryArray( $a = array() ) {
	$this->qarray = array_merge( $this->qarray, $a );
  }

  public function genStoreQueryString( $table ) {
	$qheader = 'INSERT INTO '.$table.' (';
	$qfooter = ' VALUES (';
	$i = 0;

	foreach( $this->qarray as $k => $v ) {
	  if( $i > 0 ) {
		$qheader .= ', ';
		$qfooter .= ', ';
	  }

	  $qheader .= $k;
	  $qfooter .= $v;
		  
	  $i += 1;
	}
	
	$qheader .= ')';
	$qfooter .= ')';
	
	return $qheader.$qfooter;
  }

  public function genUpdateQueryString( $table, $pid, $piid = 'pid' ) {
	$qheader = 'UPDATE '.$table;
	$qfooter = ' SET ';
	$i = 0;

	foreach( $this->qarray as $k => $v ) {
	  if( $i > 0 ) {
		$qfooter .= ', ';
	  }

	  $qfooter .= $k.' = '.$v;
		  
	  $i += 1;
	}
	
	$qfooter .= ' WHERE '.$piid.' = '.WA_String::sqlfmt( $pid );
	
	return $qheader.$qfooter;
  }

  
  public function sqlinit() {
	WA_String::print_info( 'SQL init' );
  }

}

?>
