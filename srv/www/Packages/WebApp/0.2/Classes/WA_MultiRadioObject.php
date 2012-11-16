<?php

class WA_MultiRadioObject extends WA_ContainerObject {
  private $rkeys = array();
  
  public function __construct( $id = 'WA_MultiRadioObject', $label = 'Label', $values = array( 'pussy' => 'Pussy', 'cat' => 'Cat' ), $selected = 'pussy', $align = 'horizontal' ) {

	parent::__construct( $id, 'vertical' );
	$this->selected = $selected;
	$this->label_text = $label;
		
	$this->label  = new WA_LabelObject( $id."_label", $label );
	$this->descr  = new WA_LabelObject( $id."_descr", 'Description' );

	$this->fields = new WA_ContainerObject( $id."_fields_cont", $align );
	
	$i = 0;
	foreach( $values as $k => $v ) {
	  $this->set_val( $v, $k );
	  $this->rkeys[$k] = $i;
	  
	  $is_checked = false;
	  if( $k == $selected ) {
		$is_checked = true;
	  }
	  $this->radio_fields[$i] = new WA_RadioObject( $id."_radio_".$i, $id, $k, $v, $is_checked );
	  $this->fields->putObject( $this->radio_fields[$i] );
	  $i += 1;
	}
	
	$this->cont = new WA_ContainerObject( $id."_radio_cont", 'horizontal' );
	$this->cont->putObject( $this->label );
	$this->cont->putObject( $this->fields );

	$this->descr->disable();
	
	$this->putObject( $this->descr );	
	$this->putObject( $this->cont );
	
	// style
	//foreach( $this->radio_fields as $o ) {
	//  $o->label->setWidth( "50px" );
	//  $o->label->setAlign( "right" );
	//}

	// style
	//$this->label->setWidth( "150px" );
	//$this->label->setAlign( "right" );
	//$this->fields->setSeparator( "&nbsp;&nbsp;" );
  }

  public function setSelected( $s ) {
	$this->radio_fields[$this->rkeys[$this->selected]]->setChecked( false );
	
	$this->selected = $s;
	foreach( $this->rkeys as $k => $i ) {
	  if( $k == $s ) {
		$rf = $this->radio_fields[$this->rkeys[$this->selected]];
		$rf->setChecked( true );
	  }
	}
  }
  
  public function GETHtmlValues() {
	if( WA_Session::is_sended( $this->id ) ) {
	  
	  $o = $this->radio_fields[$this->rkeys[$this->selected]];
	  $o->setChecked( false );
	
	  $sv = WA_Session::get_sended( $this->id );
	  $this->selected = $sv;
	  $o = $this->radio_fields[$this->rkeys[$this->selected]];
	  $o->setChecked( true );	  
	}
  }


  public function genQueryFooter() {
	return WA_String::sqlfmt( trim( $this->selected ) ); 
  }

  public function genStaticHTML() {
	$value = new WA_LabelObject( $this->id."_value", $this->values[$this->selected]['value'] );	
	$h = new WA_HiddenFieldObject( $this->id, $this->selected );

	$this->cont = new WA_ContainerObject( $this->id."_radio_cont", 'horizontal' );
	$this->cont->putObjectArray( array( $this->label, $value, $h ) );
	parent::setLabelWidth( $this->label_width );
	parent::setLabelAlign( $this->label_align );
	$this->cont->genStaticHTML();
  }


  public function setFieldLabelWidth( $s ) {
	foreach( $this->radio_fields as $f ) {
	  $f->cont->tdwidth[0] = $s;
	}
  }

  public function setFieldLabelAlign( $a ) {
	foreach( $this->radio_fields as $f ) {
	  $f->cont->align[0] = $a;
	}
  }

  public function setFieldLabelClass( $c ) {
	foreach( $this->radio_fields as $f ) {
	  $f->label->css_class = $c;
	}
  }

  public function init_from_sql( $va ) {
	$this->setSelected( $va[0] );
  }

}

?>
