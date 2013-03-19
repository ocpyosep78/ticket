<?php 

function login_css() { ?>
    <link href="<?php echo base_url('assets/css/login.css'); ?>" rel="stylesheet">
<?php }
add_action('page_head', 'login_css');

include 'header.php'; 
?>
<div class="container">

<form class="form-signin" action="<?php echo site_url('auth/profile'); ?>" method="post">
    <h2 class="form-signin-heading"><?php echo $this->orca_auth->user->username; ?></h2>
    
    <?php
    $msg = flashmsg_get();
    if ($msg) echo '<div class="alert alert-error">'.  htmlspecialchars($msg).'</div>';
    ?>

    <fieldset>
        <legend>Ganti Password</legend>
        <p>Masukkan password baru anda, ulangi 2x</p>
        <input id="id_password" type="password" class="input-block-level" placeholder="Password" name="password">
        <input id="id_password2" type="password" class="input-block-level" placeholder="Ulangi Password" name="password2">
    </fieldset>
    
    <button class="btn btn-large btn-primary" type="submit">Save</button>
    <a class="btn btn-large" href="<?php echo site_url('dashboard'); ?>">Cancel</a>
    
</form>

</div> <!-- /container -->

<?php include 'footer.php'; ?>
