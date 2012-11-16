<?php

class WA_ChangelogForm extends WA_FormObject {
    public function __construct( $id = 'lst_clog', $title = 'Changelog', $module = 'lst_clog', $opts = array() ) {
	  parent::__construct( $id, $title, $module, $opts );

		// OBJECTS
		// sql bind: name
		
		$user = new WA_InputObject( 'user', 'User:', array( 'general' ), array( false ), array( 25 )  );
		$user->set_init_val( 0, '.*');
		$user->setLabelWidth( '150px' );
		$user->setLabelAlign( 'right' );
		$user->setLabelClass( 'item_label' );		

		$startdate = new WA_DateInput( 'startdate', 'Kezdő dátum:', true );
		$startdate->set_init_val( time() );

		$enddate = new WA_DateInput( 'enddate', 'Befejező dátum:', true );
		$enddate->set_init_val( time() );
	
		$button  = new WA_ButtonObject( 'button', 'Keres', '' );
  
		// BUILD FORM

		
		$objarr = array( $user, $startdate, $enddate, new WA_LabelObject( 'gap1', '&nbsp;' ),
						 new WA_LabelObject( 'gap2', '&nbsp;' ), $button );

		$this->putContentObjectArray( $objarr );


		// SET GLOBAL STYLE
		$this->setLabelWidth( '150px' );
		$this->setLabelAlign( 'right' );
		$this->setLabelClass( 'item_label' );		
		$this->cont->css_class = 'form_cont';
  }



  public function showresults( $rs ) {
	foreach( $rs as $rsl ) {
	  echo '<div class="logtime">'.$rsl['logtime'].'</div>';
	  echo '<div class="user">User: '.$rsl['userid'].' ';
	  echo '<span class="action">Action: '.$rsl['logtxt'].'</span></div>';
	  if( WA_String::nzstr( $rsl['query'] ) ) {
		echo '<div class="query">'.$rsl['query'].'</div>';
	  }
	  echo '<br>';
	}

  }



  public function query( $db ) {
	$o = $this->getContentObject( 'user' );
	$user = $o->get_val();
	
	$o = $this->getContentObject( 'startdate' );	
	$sdate = $o->get_date().' 00:00:00';
	
	$o = $this->getContentObject( 'enddate' );		
	$edate = $o->get_date().' 24:00:00';

	$query  = 'SELECT * FROM '.$_SESSION['LOG_TABLE'];
	$query .= ' WHERE userid ~ '.WA_String::sqlfmt( $user );
	$query .= ' AND logtime >= '.WA_String::sqlfmt( $sdate );
	$query .= ' AND logtime <= '.WA_String::sqlfmt( $edate );
	$query .= ' ORDER BY logtime DESC LIMIT '.$_SESSION['QUERY_LIMIT'];

	// echo $query;
	// User Check
	try {
	  $rs = $db->Execute( $query );
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
