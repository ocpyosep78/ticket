<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo empty($page_title) ? '' : htmlspecialchars($page_title) . ' &middot; '; ?><?php echo $this->config->item('site_name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Herdian Ferdianto, PT Sinar Media Tiga">
    <link href="<?php echo base_url('assets/css/bootstrap.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/js/datepicker/css/datepicker.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/js/calender/fullcalendar_gebo.css'); ?>" />
    <link href="<?php echo base_url('assets/css/hehe.css'); ?>" rel="stylesheet">
    
    <script type="text/javascript">var web = { host: '<?php echo base_url(); ?>' }</script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-1.7.2.min.js'); ?>"></script>
    <!--[if lt IE 9]>
        <script src="<?php echo base_url('assets/js/html5shiv.js'); ?>"></script>
    <![endif]-->
    <?php do_action('page_head'); ?>
</head>
<body>
