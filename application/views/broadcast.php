<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

global $_users;
$strClient = $this->orca_auth->user->client_id?" WHERE client_id = {$this->orca_auth->user->client_id}" : "";
$rr = $this->db->query("SELECT * FROM users $strClient ORDER BY username")->result();
foreach($rr as $r)
    $_users[] = $r->username;

function add_broadcast_css() { ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/js/select2/select2.css'); ?>">
<?php }
add_action('page_head', 'add_broadcast_css');

function add_broadcast_script() { 
    global $_users; ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/select2/select2.min.js'); ?>"></script>
<script>
    $(function() {
        $("#usernames").select2({
            tags:(<?php echo json_encode($_users); ?>),
            tokenSeparators: [",", " "]
        });
    });
</script>
<?php }
add_action('page_foot', 'add_broadcast_script');

$page_title = "Broadcast E-Mail";
include 'header.php';
include 'pagetop.php';
?>
<div class="container-fluid" id="maincontent">
    <div class="row-fluid">
        <div class="span12" id="content">
            <h1><?php echo $page_title; ?></h1>
            
            <?php

            $msg = flashmsg_get();
            if ($msg)
                echo '<div class="alert alert-error">'.htmlentities($msg).'</div>';

            ?>
            
            <form class="form-horizontal" action="<?php echo site_url('dashboard/broadcast');?>" method="POST">
                <div class="control-group">
                    <label class="control-label" for="id_subject">Subject</label>
                    <div class="controls">
                        <input type="text" id="id_subject" name="subject" placeholder="message subject" class="input-xxlarge"
                                data-toggle="tooltip" 
                                title="Message Subject"
                                value="<?php echo empty($_POST['subject']) ? '' : htmlspecialchars($_POST['subject']); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="usernames">To</label>
                    <div class="controls">
                        <input type="text" id="usernames" name="usernames" placeholder="send message to" class="input-xxlarge"
                                value="<?php echo empty($_POST['usernames']) ? '' : htmlspecialchars($_POST['usernames']); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="id_message">Message</label>
                    <div class="controls">
                        <textarea id="id_message" name="message" placeholder="Your message" class="autogrow input-xxlarge"
                                data-toggle="tooltip" 
                                title="Your message please"><?php echo empty($_POST['message']) ? '' : htmlspecialchars($_POST['message']); ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Send Message</button>
                    <a class="btn" href="<?php echo site_url('dashboard/'); ?>">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<?php include 'footer.php';
