<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function ticket_detail_foot() { ?>
<script>
    $(function() {
        $('.toggle-comments').click(function() {
            $('.comments').toggle();
            return false;
        });
        $('.btn-done').on('click', function() {
            if (!confirm("Mark this ticket as closed?"))
                return false;
            
            var link = $(this);
            var ticketId = link.data('ticketId');
            $.post('<?php echo site_url('tickets/ajaxdone'); ?>', {id:ticketId}, function() {
                link.replaceWith($('<span class="label label-info">closed</label>'));
            });
            return false;
        });
   });
</script>
<?php }
add_action('page_foot', 'ticket_detail_foot');

$page_title = 'Ticket Detail #'.$ticket->id;
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
            $uid = $this->orca_auth->user->id;
            
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?",array( $ticket->project_id ))->row();
            
            $btndone = $ticket->status == 'close' 
                ? '<span class="label label-info">closed</span>' 
                : ( $uid == $project->user_id || $uid == $ticket->user_id || $this->Users->has_perms($uid, 'tickets/done') || $this->Users->has_perms($uid, 'tickets/edit') ?  '<a class="btn btn-mini btn-warning btn-done" href="" data-ticket-id="'.$ticket->id.'">close ticket</a>' : '<span class="label label-info">open</span>');
            
            ?>
            
            <div class="ticket-meta">
                <span class="label">Pengirim: <?php echo $ticket->create_user; ?></span> sekitar <span class="label"><?php echo time_since2($ticket->created_on); ?></span>
            </div>
            <h3><?php echo htmlspecialchars($ticket->title); ?> <?php echo $btndone; ?></h3>
            <div class="ticket-tags">
                <span class="label">Petugas: <?php echo $ticket->assigned_user_name ? $ticket->assigned_user_name  : 'belum ditugaskan'; ?></span>
                <span class="label label-info">Tipe: <?php echo $ticket->type ? $ticket->type  : ''; ?></span>
                <span class="label label-success">Prioritas: <?php echo $ticket->severity ? $ticket->severity : ''; ?></span>
            </div>
            <div class="ticket-description">
                <div class="text">
                    <?php echo nl2br(htmlspecialchars($ticket->description)); ?>
                </div>
                <ul class="attachment">
                    <?php
                    $upload_url = base_url('files/attachments');
                    foreach($ticket->attachments as $att): ?>
                    <li><?php echo '<a style="display:block" class="uploadfile '.get_ext($att->filename).'" href="'.$upload_url.'/'.$att->filename.'">'.basename($att->filename).'</a>'; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php include 'history.php'; show_history( 'tickets', $ticket->id ); ?>
            <div style="ticket-footer"><a href="<?php echo site_url('projects/tickets?id='.$ticket->project_id); ?>">back to tickets</a></div>

            <div class="comments">
                <?php 
                $this->id_name = 'ticket_id';
                include 'comments.php'; 
                ?>
            </div>
                
        </div>
    </div>
</div>

<?php include 'footer.php';
