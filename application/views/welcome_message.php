<?php 

function login_css() { ?>
    <link href="<?php echo base_url('assets/css/login.css'); ?>" rel="stylesheet">
<?php }
add_action('page_head', 'login_css');

include 'header.php'; 
?>
<div class="container">

<div class="form-center hero">
    <h2>Hello</h2>
	<p><b>This is SIMETRI Tickets</b>, project management and issue tracking application from PT Sinar Media Tiga</p>
    <p>You can <a href="<?php echo site_url('dashboard/'); ?>">login here</a></p>
</div>

</div> <!-- /container -->

<?php include 'footer.php'; ?>
