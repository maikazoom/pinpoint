 <?php header("Expires: 0"); header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); header("cache-control: no-store, no-cache, must-revalidate"); header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $title; ?></title>
<base href = '<?php echo base_url(); ?>' />
<link type = 'text/css' rel = 'stylesheet' media = 'screen' href = 'css/backend/default.css' />



	

<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="expires" content="FRI, 13 APR 1999 01:00:00 GMT">


</head>
<body>
<div style = 'margin-bottom:10px'><?php echo anchor(site_url("backend/logout"), 'Logout'); ?>&nbsp; &nbsp; <?php echo anchor(site_url("backend"), 'Dashboard'); ?>&nbsp; &nbsp; &nbsp; &nbsp; <!--<a rel ="facebox" href = 'action/load_tip_search' >Search Tips</a>--></div>

<div style = "width: 1000px; margin: 0px auto; float: left;">

	