<?php

class WA_ListUserForm extends WA_FormObject {
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
		$this->setDescription( $descr );
		if( ! isset( $this->faculty_arr ) ) {
		  $this->faculty_arr = array( array( '.*', 'GTK', 'TTK', 'VIK', 'ÉPK', 'VBK', 'GPK', 'ÉMK', 'KSK' ) );
		}
		// OBJECTS
		// sql bind: name
		
		$name = new WA_NameInput( 'name', 'Név:', false );
		// $name->set_init_val( 0, '.*');
		// $name->set_init_val( 1, '.*');
		$name->set_init_val( 0, '');
		$name->set_init_val( 1, '');
		
		$name->fields[0]->validate_type = 'general';
		$name->fields[1]->validate_type = 'general';
		$name->setLabelWidth( '150px' );
		$name->setLabelAlign( 'right' );
		$name->setLabelClass( 'item_label' );		

		$sort_name = new WA_CheckboxFieldObject( 'sort_name', true );
		$name_cont = new WA_ContainerObject( 'name_cont', 'horizontal' );
		$name_cont->putObjectArray( array( $sort_name, $name ) );

				
		$sex = new WA_GenderSelect( 'sex', 'M' );
		$sex->setLabelWidth( '150px' );
		$sex->setLabelAlign( 'right' );
		$sex->setLabelClass( 'item_label' );		
		$sex->setFieldLabelAlign( 'right' );
		$sex->setFieldLabelClass( 'radio' );	

		$sort_sex = new WA_CheckboxFieldObject( 'sort_sex', false );
		$sex_cont = new WA_ContainerObject( 'sex_cont', 'horizontal' );
		$sex_cont->putObjectArray( array( $sort_sex, $sex ) );


		$faculty = new WA_ComboObject( 'faculty', 'Kar:', $this->faculty_arr , array( 1 ) );
		$faculty->setLabelWidth( '150px' );
		$faculty->setLabelAlign( 'right' );
		$faculty->setLabelClass( 'item_label' );		
		$sort_faculty = new WA_CheckboxFieldObject( 'sort_faculty', false );
		$faculty_cont = new WA_ContainerObject( 'faculty_cont', 'horizontal' );
		$faculty_cont->putObjectArray( array( $sort_faculty, $faculty ) );


		$class   = new WA_ComboObject( 'class', 'Évfolyam:', array( array( '.*', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 'Phd 1', 'Phd 2', 'Phd 3' ) ), array( 1 ) );
		$class->setLabelWidth( '150px' );
		$class->setLabelAlign( 'right' );
		$class->setLabelClass( 'item_label' );		

		$sort_class = new WA_CheckboxFieldObject( 'sort_class', false );
		$class_cont = new WA_ContainerObject( 'class_cont', 'horizontal' );
		$class_cont->putObjectArray( array( $sort_class, $class ) );


		$roomid = new WA_ComboObject( 'roomid', 'Szobaszám:', array( array( '.', 'A', 'B', 'C' ), 
			  array( '.', 0, 1, 2, 3, 4 ), array( '..', '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14' ) ), array( 1, 1, 1 ) );
		$roomid->setLabelWidth( '150px' );
		$roomid->setLabelAlign( 'right' );
		$roomid->setLabelClass( 'item_label' );		
		$roomid->sql_merge = true;
		$sort_roomid = new WA_CheckboxFieldObject( 'sort_roomid', false );
		$roomid_cont = new WA_ContainerObject( 'roomid_cont', 'horizontal' );
		$roomid_cont->putObjectArray( array( $sort_roomid, $roomid ) );


		$opts_colors  = new WA_CheckboxObject( 'opts_colors', 'Színek', true );
		$opts_colors->setLabelWidth( '100px' );
		$opts_colors->setLabelAlign( 'right' );
		$opts_colors->setLabelClass( 'item_label' );		

		$opts_details = new WA_CheckboxObject( 'opts_details', 'Részletes', false );
		$opts_details->setLabelWidth( '100px' );
		$opts_details->setLabelAlign( 'right' );
		$opts_details->setLabelClass( 'item_label' );		

		$opts_email = new WA_CheckboxObject( 'opts_email', 'Email', false );
		$opts_email->setLabelWidth( '100px' );
		$opts_email->setLabelAlign( 'right' );
		$opts_email->setLabelClass( 'item_label' );		
		$opts_email->disable();

		$opts_all = new WA_CheckboxObject( 'opts_all', 'Összes (TODO)', false );
		$opts_all->setLabelWidth( '100px' );
		$opts_all->setLabelAlign( 'right' );
		$opts_all->setLabelClass( 'item_label' );		
		$opts_all->disable();

		
		$opts_cont   = new WA_ContainerObject( 'opts_cont', 'horizontal' );
		$opts_cont->putObjectArray( array( $opts_colors, $opts_details, $opts_email, $opts_all ) );
	
		$button  = new WA_ButtonObject( 'button', 'Keres', '' );
  
		// BUILD FORM

		
		$objarr = array( $name_cont, $sex_cont, $faculty_cont, 
						 $class_cont, $roomid_cont, new WA_LabelObject( 'gap1', '&nbsp;' ),
						 $opts_cont, new WA_LabelObject( 'gap2', '&nbsp;' ), $button );

		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$sex->setFieldLabelWidth( '50px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }


  public function genQuery() {
	$ptype = 'guest';
	if( isset( $this->opts['ptype'] ) ) {
	  $ptype = $this->opts['ptype'];
	}
	$table = $_SESSION['PERSON_TABLE'];

	$this->limit = 1000;
	if( isset( $this->opts['limit'] ) ) {
	  $this->limit = $this->opts['limit'];
	}

	// construct query
	$first = false;
	$first_order = true;
	$query_header = WA_Session::select_string( array( 'pid', '(name).forname', 
							'(name).surname', 'sex', 'ptype', 'isvalid', 'exprydate',
							'roomid', 'occupdate', 'leavedate', 'faculty', 'wing', 'floor', 'number', 
							'class', 'enrollyear', 'email', 'msn', 'skype'), $table );

	$query_footer = ' INNER JOIN '.$_SESSION['ROOM_TABLE'].' USING (roomid) WHERE ptype ~ '.WA_String::sqlfmt( $ptype );

	// name	
	$o = $this->getChildObject( 'name_cont', 'name' );
	if( WA_String::nzstr( $o->getForname() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " (name).forname ~* ".WA_String::sqlfmt( $o->getForname() );
	}

	if( WA_String::nzstr( $o->getSurname() ) ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;
	  $query_footer .= " (name).surname ~* ".WA_String::sqlfmt( $o->getSurname() );	  
	}
	// name end

	$o = $this->getChildObject( 'sex_cont', 'sort_sex' );
	if( $o->checked ) {
	  if( ! $first ) { $query_footer .= " AND"; }
	  $first = false;

	  $o = $this->getChildObject( 'sex_cont', 'sex' );
	  $query_footer .= " sex ~ ".WA_String::sqlfmt( $o->selected );	  
	}

	if( ! $first ) { $query_footer .= " AND"; }
	$first = false;
	
	$o = $this->getChildObject( 'faculty_cont', 'faculty' );
	$query_footer .= " faculty ~ ".WA_String::sqlfmt( $o->get_val() );	  
	
	$o = $this->getChildObject( 'class_cont', 'class' );
	$query_footer .= " AND class ~ ".WA_String::sqlfmt( $o->get_val() );	  

	$o = $this->getChildObject( 'roomid_cont', 'roomid' );
	// $query_footer .= " AND roomid ~ ".WA_String::sqlfmt( $o->get_val() );	  
	$query_footer .= " AND roomid ~ ".$o->genQueryFooter();	  

	$query_footer .= " AND isvalid = ".WA_String::sqlfmt( 't' );
	
	$o = $this->getChildObject( 'faculty_cont', 'sort_faculty' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " faculty";
	}

	$o = $this->getChildObject( 'class_cont', 'sort_class' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " class";
	}

	$o = $this->getChildObject( 'roomid_cont', 'sort_roomid' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " roomid";
	}

	$o = $this->getChildObject( 'name_cont', 'sort_name' );
	if( $o->checked ) {
	  if( $first_order ) {
		$query_footer .= " ORDER BY";
		$first_order = false;
	  }
	  else {
		$query_footer .= ",";
	  }
	  $query_footer .= " (name).forname, (name).surname";
	}

	if( isset( $this->limit ) ) {
	  $query_footer .= ' LIMIT '.$this->limit;
	}
	else {
	  $query_footer .= ' LIMIT '.$_SESSION['QUERY_LIMIT'];	
	}
	
	$query = $query_header.$query_footer;

	$this->query_footer = $query_footer;
	$this->query_header = $query_header;
	$this->query = $query;
	
	// echo "<br>".$query;
  }



  public function showresults( $rs ) {
	$isdet = $this->getChildObject( 'opts_cont', 'opts_details' );
	$iscol = $this->getChildObject( 'opts_cont', 'opts_colors' );

	  
	if( ! $isdet->isChecked() ) {
	  $rstable = new WA_ResultsTable( 'results', array( 'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 'Kar' => 'faculty', 
														  'Évf.' => 'class', 'Szobaszám' => 'roomid'), 
												   array( '100px', '100px', '40px','45px','80px' ), $rs,
												   array( 'colors' => $iscol->isChecked() ) );	  
	}
	else {
	  $rstable = new WA_ResultsTable( 'results', array( 'Vezetéknév' => 'forname', 'Keresztnév' => 'surname', 
														  'Kar' => 'faculty', 'Évf.' => 'class', 
														  'Szárny' => 'wing', 'Em.' => 'floor', 
														  'Szoba' => 'number', 'Beiratkozás' => 'enrollyear',
														  'Beköltözés' => 'occupdate', 'Kiköltözés' => 'leavedate' ), 
												   array( '100px', '100px', '40px',
												  		  '40px','45px', '40px', 
														  '40px', '80px', '90px', '90px' ), $rs,
												   array( 'colors' => $iscol->isChecked() ) );	  
	}
	  
	$rstable->genHTML();
  }


  public function query( $db ) {
	$this->genQuery();
	
	// User Check
	try {
	  $rs = $db->Execute( $this->query );
	}
	catch( exception $e ) {
	  // print_error( 'SQL hiba történt, fordulj a rendszergazdához segítségért!' );
	  print_query_error( 'Error: ', $e );
	  return;
	}
	
	if( $rs->RecordCount() < 1 ) {
	  print_info( 'Nincs a keresésnek megfelelő találat az adatbázisban!' );
	  return;
	}
	else {
	  $this->showresults( $rs );
	}
  }
}
?>
