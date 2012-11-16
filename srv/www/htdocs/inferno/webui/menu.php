<?php
  require_once( '/home/inferno/webui/Packages/WebApp/0.3/config.php' );
  if( ! WA_Session::start() ) exit;

  if( ! WA_Session::checklogin() ) exit;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <title>Network Management</title>
<?php
  WA_session::include_css( 'common.css' );
  WA_session::include_css( 'inferno.css' );
  WA_session::include_js( 'jquery-1.3.2.js' );  
?>

</head>
 
<body style="background-color: #eeeeee">

<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="menu_item" style="padding-top: 10px;">
      <a href="#" onclick='parent.location="logout.php"'>Kilépés</a> 
      | 
      <a href="./site.php" target="site"><b>Főoldal</b></a>
    </td>
  </tr>
</table>

<?php
  if( $group == 'redate' || $group == 'admin' ) {
    WA_Session::inc_menu( 'redate_admin_frame' );
  }

  if( $group == 'admin' ) {
    WA_Session::inc_menu( 'admin_admin_frame' );
  }

?>

</body>
</html>

