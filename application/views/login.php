<?php 

function login_css() { ?>
    <link href="<?php echo base_url('assets/css/login.css'); ?>" rel="stylesheet">
<?php }
add_action('page_head', 'login_css');

include 'header.php'; 
?>
<div class="container">

<form class="form-signin" action="<?php echo site_url('auth/login'); ?>" method="post">
    <h2 class="form-signin-heading">Please sign in</h2>

    <?php
    $msg = flashmsg_get();
    if ($msg) echo '<div class="alert alert-error">'.  htmlspecialchars($msg).'</div>';
    $next = $this->input->get_post('next'); 
    ?>

    <input id="id_username" type="text" class="input-block-level" placeholder="Username" name="username">
    <input id="id_password" type="password" class="input-block-level" placeholder="Password" name="password">
    <button class="btn btn-large btn-primary" type="submit">Sign in</button>
    <input type="hidden" name="next" value="<?php echo htmlentities($next); ?>" />
</form>

</div> <!-- /container -->

<?php include 'footer.php'; ?>
