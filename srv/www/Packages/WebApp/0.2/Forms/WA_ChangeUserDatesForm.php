<?php

class WA_ChangeUserDatesForm extends WA_FormObject {
    public function __construct( $id = 'lst_user', $title = 'List User', $module = 'lst_user', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );
  
		$descr = '<div class="help_title">Segítség</div>
				 <div class="help_text">
				 Ez az elektronikus űrlap a Kollégium adminisztrációját kívánja segíteni. 
				 Értelem szerűen, a magyar helyesírásnak megfelelően kell kitölteni az adatokat. 
				 A név mező <a href="http://www.regular-expressions.info/reference.html">szabályos kifejezéseket</a> (regular expressions) is elfogad. A bal oldali 
				 mezőkkel a találatok rendezésének módját határozhatod meg. 
				 </div>';
  
		// FORM
		// $this->setDescription( $descr );
		// OBJECTS
		// sql bind: name

		$cont_occ = new WA_ContainerObject( 'cont_occ', 'horizontal' );
		$cont_lea = new WA_ContainerObject( 'cont_lea', 'horizontal' );

		$is_chg_occ   = new WA_CheckboxObject( 'is_chg_occ', 'Beköltözés:', false );
		$is_chg_occ->setLabelWidth( '150px' );
		$is_chg_occ->setLabelAlign( 'right' );
		$is_chg_occ->setLabelClass( 'item_label' );		

		$date_chg_occ = new WA_DateInput( 'date_chg_occ', 'Beköltözés Dátuma:', true );
		$date_chg_occ->set_init_date( date( 'Y-m-d' ) );
		
		$date_chg_occ->setLabelWidth( '150px' );
		$date_chg_occ->setLabelAlign( 'right' );
		$date_chg_occ->setLabelClass( 'item_label' );		

		$cont_occ->putObjectArray( array( $is_chg_occ, $date_chg_occ ) );


		$is_chg_lea   = new WA_CheckboxObject( 'is_chg_lea', 'Kiköltözés:', false );
		$is_chg_lea->setLabelWidth( '150px' );
		$is_chg_lea->setLabelAlign( 'right' );
		$is_chg_lea->setLabelClass( 'item_label' );		

		$date_chg_lea = new WA_DateInput( 'date_chg_lea', 'Kiköltözés Dátuma:', true );
		$date_chg_lea->set_init_date( date( 'Y-m-d' ) );

		$date_chg_lea->setLabelWidth( '150px' );
		$date_chg_lea->setLabelAlign( 'right' );
		$date_chg_lea->setLabelClass( 'item_label' );		

		$cont_lea->putObjectArray( array( $is_chg_lea, $date_chg_lea ) );


		$list_chg = new WA_TextAreaObject( 'list_chg', 'Lista:', '', 25, 45 );	  
		$list_chg->setLabelWidth( '150px' );
		$list_chg->setLabelAlign( 'right' );
		$list_chg->setLabelClass( 'item_label' );		

		$button  = new WA_ButtonObject( 'button', 'Módosítás Elküldése', '&nbsp;' );
		$button->setLabelWidth( '150px' );
  
		// BUILD FORM

		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';

		$objarr = array( $cont_occ, $cont_lea, $list_chg, new WA_LabelObject( 'gap1', '&nbsp;' ), $button );

		$this->putContentObjectArray( $objarr );
		
  }


  public function Update( $db ) {
	$ptype = 'guest';
	if( isset( $this->opts['ptype'] ) ) {
	  $ptype = $this->opts['ptype'];
	}
	$table = $_SESSION['PERSON_TABLE'];

	$this->limit = 1000;
	if( isset( $this->opts['limit'] ) ) {
	  $this->limit = $this->opts['limit'];
	}


	$o = $this->getChildObject( 'cont_occ', 'is_chg_occ' );
	$is_chg_occ = $o->isChecked();
	
	$o = $this->getChildObject( 'cont_lea', 'is_chg_lea' );
	$is_chg_lea = $o->isChecked();
	
	$o = $this->getChildObject( 'cont_occ', 'date_chg_occ' );
	$occ_date = $o->get_date();

	$o = $this->getChildObject( 'cont_lea', 'date_chg_lea' );
	$lea_date = $o->get_date();

	$o = $this->getContentObject( 'list_chg' );
	$list = $o->get_val();

	if( WA_String::zstr( $list ) ) {
	  print '<br>Nincs megadva lista';
	  return;	  
	}

	foreach( split( ',' , $list ) as $l ) {
	  $l = trim($l);
	  $etrid = '';
	  if( preg_match( '/^[a-zA-Z0-9]+$/',$l ) ) {
		$etrid = strtoupper( $l );

		if( $is_chg_occ ) {
		  $query  = 'UPDATE '.$table.' SET ';
		  $query .= 'occupdate = '.WA_String::sqlfmt($occ_date);
		  $query .= ' WHERE etrid = '.WA_String::sqlfmt( $etrid );

		  print '<br>'.$etrid.' Beköltözés dátuma: '.$occ_date;

		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print '<br><span style="color: red; font-weight: bold;">Nincs '.$etrid.' felhasználó</span>';
		  }
		}

		if( $is_chg_lea ) {
		  $query  = 'UPDATE '.$table.' SET ';
		  $query .= 'leavedate = '.WA_String::sqlfmt($lea_date);
		  $query .= ' WHERE etrid = '.WA_String::sqlfmt( $etrid );

		  print '<br>'.$etrid.' Kiköltözés dátuma: '.$lea_date;

		  try {
			$rs = $db->Execute( $query );
		  }
		  catch( exception $e ) {
			print '<br><span style="color: red; font-weight: bold;">Nincs '.$etrid.' felhasználó</span>';
		  }
		}	
	  }
	}

	return;
  }


}
?>
