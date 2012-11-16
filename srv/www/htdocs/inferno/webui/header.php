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
?>

</head>
 
<body style="background-color: #eeeeee;" >
<table cellpadding="0" cellspacing="0" border="0" width='100%' height="100%">
<tr>
  <td style="background-color: #eeeeee;">
  <img src="img/inferno.png">
  </td>
</tr>

<tr>
  <td style="background-color: #cccccc; font-size: 11px; padding-left: 5px; padding-bottom: 10px;">
<?php WA_session::print_info();?>
  </td>
</tr>
</table>
</body>
