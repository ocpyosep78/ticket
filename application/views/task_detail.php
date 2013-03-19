<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function task_detail_foot() { ?>
<script>
    $(function() {
        $('.toggle-descr').click(function() {
            $(this).parent().next().toggle();
            return false;
        });
        $('.toggle-comments').click(function() {
            $('.comments').toggle();
            return false;
        });
        $('.btn-done').on('click', function() {
            if (!confirm("Mark this task as done?"))
                return false;
            
            var link = $(this);
            var taskId = link.data('taskId');
            $.post('<?php echo site_url('tasks/ajaxdone'); ?>', {id:taskId}, function() {
                link.replaceWith($('<span class="label label-info">done</label>'));
            });
            return false;
        });
   });
</script>
<?php }
add_action('page_foot', 'task_detail_foot');

$page_title = 'Task Detail';
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
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?",array( $task->project_id ))->row();
            $btndone = $task->status == 1 
                    ? '<span class="label label-info">complete</span>' 
                    : ( $uid  == $project->user_id || $this->Users->has_perms($uid, 'tasks/done') || $this->Users->has_perms($uid, 'tasks/edit') ? '<a class="btn btn-mini btn-warning btn-done" href="" data-task-id="'.$task->id.'">mark as complete</a>' : '<span class="label label-warning">incomplete</span>');
            
            if ( $task->status ) {
                $t1 = strtotime( $task->due . " 23:59:59" );
                $t2 = strtotime( $task->complete_date );

                $str_due =  'Finish:<span class="due label">'.date('Y-m-d',strtotime($task->complete_date));
                if ( $t2 <= $t1 || $task->due == substr($task->complete_date, 0, 10) ) {
                    $str_due .= '</span> <span class="label label-success">'.  time_since2($t2, $t1) . ' lebih cepat';
                } else {
                    $str_due .= '</span> <span class="label label-warning">'.  time_since2($t1, $t2) . " terlambat";
                }

                $str_due .= '</span>';
            } else {
                $t1 = strtotime( $task->due );
                $t2 = strtotime( date('Y-m-d 23:59:59') );
                if ( $t1 < $t2 ) {
                    $str_due = '<span class="due label label-important">'. time_since2($t1, $t2) .' Terlambat!</span>';
                } else {
                    $str_due = '<span class="due label label-info">'. time_since2($t2, $t1) .'</span>';
                }
            }
            
            ?>
            
            <h3><?php echo htmlspecialchars($task->task); ?> <?php echo $btndone; ?></h3>
            <div class="ticket-description"><div class="text"><?php echo nl2br(htmlspecialchars($task->description)); ?></div></div>
            <p>
                User:<span class="label label-info"><?php echo $task->assigned_user; ?></span>
                <?php echo $str_due; ?>
                <br><a href="<?php echo site_url('projects/tasks?id='.$task->project_id); ?>">back to timelines</a>
            </p>
            
            <?php include 'history.php'; show_history( 'tasks', $task->id ); ?>

            <div class="comments">
                <?php 
                $this->id_name = 'task_id';
                include 'comments.php'; 
                ?>
            </div>
                
        </div>
    </div>
</div>

<?php include 'footer.php';
