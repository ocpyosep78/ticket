<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function tasks_script() { ?>
<script>
    var TASKID = 0;
    $(function() {
        $(".btn-add-task").click(function() {
            var $button = $(this);
            
            //copy template form
            var $form = $(".addTaskForm").clone();
            
            //set form name
            TASKID++;
            $form.find('[name]').each(function() {
                var el = $(this);
                el.prop('name', el.prop('name') + '['+TASKID+']');
            });
            //set timeline ID field
            $form.find('[name^=timeline_id]').val( $button.data('timelineId') );
            $form.find('[name^=parent_id]').val( $button.data('parentId') );
            
            //add to page and display
            $button.parent().after($form);
            $form.removeClass('hide').removeClass('addTaskForm').addClass('taskForm').show('slow');
            
            //prepare datepicker & tooltip
            var date = new Date(), val = {
                d: date.getDate(),
                m: date.getMonth() + 1,
                yy: date.getFullYear().toString().substring(2),
                yyyy: date.getFullYear()
            }
            $form.find('.datepicker').val(val.d+'/'+val.m+'/'+val.yyyy).datepicker().on('changeDate', function() { $(this).datepicker('hide'); });
            $form.find("input,select,textarea").tooltip({placement:'right'});
            $form.find("label").tooltip({placement:'left'});
            
            $form.find('.autogrow').autogrow();
                    
            $form.find('.btn-cancel-form').click(function() {
                $(this).parents('.taskForm').remove();
                return false;
            });
            
            return false;
        });
        
        $('.btn-edit-task').click(function() {
            var $button = $(this);
            var $form = $(".addTaskForm").clone();
            $form.find('[name]').each(function() {
                var el = $(this);
                el.prop('name', el.prop('name').replace(/new/, $button.data('taskId')) + '[0]');
            });
            
            $.get('<?php echo site_url('tasks/ajaxdetail?id='); ?>'+$button.data('taskId'), function(r) {
                var task = eval('('+r+')');
                if (task.task) {
                    $form.find('[name^=task]').val( task.task.task );
                    $form.find('[name^=description]').val( task.task.description );
                    var split = task.task.due.split(/-/);
                    $form.find('[name^=due]').val( split[2]+'/'+split[1]+'/'+split[0] );
                    
                    //set timeline ID field
                    $form.find('[name^=timeline_id]').val( $button.data('timelineId') );
                    $form.find('[name^=parent_id]').val( $button.data('parentId') );

                    //add to page and display
                    $button.parents('li').find('.task-description[data-task-id='+$button.data('taskId')+']').after($form);
                    $form.removeClass('hide').removeClass('addTaskForm').addClass('taskForm').show('slow');

                    //prepare datepicker & tooltip
                    var date = new Date(), val = {
                        d: date.getDate(),
                        m: date.getMonth() + 1,
                        yy: date.getFullYear().toString().substring(2),
                        yyyy: date.getFullYear()
                    }
                    $form.find('.datepicker').val(val.d+'/'+val.m+'/'+val.yyyy).datepicker().on('changeDate', function() { $(this).datepicker('hide'); });
                    $form.find("input,select,textarea").tooltip({placement:'right'});
                    $form.find("label").tooltip({placement:'left'});
                    $form.find('.btn-cancel-form').click(function() {
                        $(this).parents('.taskForm').remove();
                        return false;
                    });
                    
                    $form.find('.autogrow').autogrow();
                }
            });
            
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
        
        $('.btn-delete-task').on('click', function() {
            if (!confirm("Delete this task?"))
                return false;
            var link = $(this);
            var taskId = link.data('taskId');
            $.post('<?php echo site_url('tasks/ajaxdelete'); ?>', {id:taskId}, function() {
                $("#task-"+taskId).remove();
            });
            return false;
        });
        
        $('.toggle-description').click(function() {
            var taskid = $(this).data('taskId');
            $('.task-description[data-task-id='+taskid+']').toggle();
            return false;
        });
        
        $("#tzoffset").val(new Date().getTimezoneOffset());
    });
</script>
<?php } 
add_action('page_foot', 'tasks_script');

$page_title = 'Project ' . (empty($project->title) ? '' : $project->title);
include 'header.php';
include 'pagetop.php';
?>

<div class="container-fluid" id="maincontent">
    <div class="row-fluid">
        <div class="span12" id="content">
            <h1><?php echo $page_title; ?></h1>
            <div class="content-descr">
                <?php echo nl2br(htmlspecialchars($project->description)); ?>
            </div>
            <ul class="attachment">
                <?php
                $upload_url = base_url('files/attachments');
                foreach($project->attachments as $att): ?>
                <li><?php echo '<a style="display:block" class="uploadfile '.get_ext($att->filename).'" href="'.$upload_url.'/'.$att->filename.'">'.basename($att->filename).'</a>'; ?></li>
                <?php endforeach; ?>
            </ul>
            <?php include 'history.php'; show_history( 'projects', $project->id ); ?>
            
            <?php

            $msg = flashmsg_get();
            if ($msg)
                echo '<div class="alert alert-error">'.htmlentities($msg).'</div>';
            
            global $_user_model, $user_id, $_project;
            $_user_model = $this->Users;
            $user_id = $this->orca_auth->user->id;
            $_project = $project;
            
            function print_task_form($tasks) {
                global $_user_model, $user_id,$_project;
                
                if (!$tasks) return;
                
                foreach($tasks as $task) {
                    $btndone = $task->status == 1 ? '<span class="label label-info">complete</span>' : '<a class="btn btn-mini btn-primary btn-done" href="" data-task-id="'.$task->id.'">mark as complete</a>';
                    $btnaddtask = '<a class="btn btn-mini btn-inverse btn-add-task" data-timeline-id="'.$task->timeline_id.'" data-parent-id="'.$task->id.'" href="">add task</a>';
                    $btnedittask = '<a class="btn btn-mini btn-inverse btn-edit-task" href="" data-timeline-id="'.$task->timeline_id.'" data-parent-id="'.$task->parent_id.'" data-task-id="'.$task->id.'">edit</a>';
                    $btnviewtask = '<a class="btn btn-mini btn-inverse btn-view-task" href="'.site_url('tasks/detail?id='.$task->id).'" data-task-id="'.$task->id.'">details</a>';
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
                    $btndeltask = '<a class="btn btn-danger btn-mini btn-delete-task" href="" data-task-id="'.$task->id.'">X</a>';
                    
                    if ( $user_id != $_project->user_id )
                    {
                        if (!$_user_model->has_perms($user_id, 'tasks/add'))
                            $btnaddtask = '';
                        if (!$_user_model->has_perms($user_id, 'tasks/edit'))
                            $btnedittask = '';
                        if (!$_user_model->has_perms($user_id, 'tasks/delete'))
                            $btndeltask = '';
                        if ($task->status == 0 && !$_user_model->has_perms($user_id, 'tasks/done'))
                            $btndone = '';
                    }
                    
                    echo '<li id="task-'.$task->id.'" class="task">
                            <h4>
                                <a class="toggle-description" href="#" data-task-id="'.$task->id.'">'.$task->task.'</a>
                                '.$btndone.'
                                <div class="task-meta">
                                    User:<span class="assigned label label-info">'.($task->assigned_user?$task->assigned_user:'None').'</span>
                                    '.$str_due.'
                                    '.$btndeltask.'
                                </div>
                                <span class="btn-group">
                                    '.$btnaddtask.'
                                    '.$btnedittask.'
                                    '.$btnviewtask.'
                                </span>
                            </h4>
                            <div class="task-description" data-task-id="'.$task->id.'">'.nl2br(htmlspecialchars( $task->description )).'</div>
                            ';
                    echo '<ul class="task-list">';
                    if (!empty($task->tasks[$task->timeline_id]))
                        print_task_form($task->tasks[$task->timeline_id]);
                    echo '</ul>';
                    echo '</li>';
                }
            }

            ?>
            
            <form id="tasksForm" class="form-horizontal" action="<?php echo site_url('projects/tasks?id='.$this->id);?>" method="POST">
                <h3>Timelines &amp; Tasks</h3>
                <ul class="timelines">
                    <?php foreach($timelines as $tl): ?>
                    <li>
                        <h4>
                            <?php echo $tl->title; ?> 
                            <?php if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tasks/add' ) ): ?>
                            <a class="btn btn-primary btn-add-task btn-mini" data-parent-id="0" data-timeline-id="<?php echo $tl->id; ?>" href="#">Add Task</a>
                            <?php endif; ?>
                        </h4>
                        <ul class="task-list">
                            <?php print_task_form( empty($tasks[$tl->id])?array():$tasks[$tl->id] ); ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="form-actions">
                    <a class="btn" href="<?php echo site_url('projects/'); ?>">Back to Projects</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="hide addTaskForm">
    <fieldset>
    <div class="control-group">
        <input type="text" name="task[new]" placeholder="Task title" class="input-xlarge"
                data-toggle="tooltip" 
                title="Task title">
    </div>
    <div class="control-group">
        <textarea name="description[new]" placeholder="Task description" class="autogrow input-xlarge"
                data-toggle="tooltip" 
                title="Task description"></textarea>
    </div>
    <div class="control-group">
        <label>Assign to:
        <select name="user_id[new]" placeholder="Assigned to"
                data-toggle="tooltip" 
                title="Assign this task to selected user">
            <?php 
                $users = $this->db->query("SELECT * FROM users " . ( $this->orca_auth->user->client_id ? "WHERE client_id = {$this->orca_auth->user->client_id}" : "" ). " ORDER BY username")->result(); 
                foreach($users as $u) {
                    echo '<option value="'.$u->id.'">'.$u->username.'</option>';
                }
            ?>
        </select></label>
    </div>
    <div class="control-group">
        <label>Due date:
            <span class="input-append">
                <input type="text" name="due[new]" class="input-small datepicker" data-date-format="dd/mm/yyyy" value="">
                <span class="add-on"><i class="icon-th"></i></span>
            </span>
        </label>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a class="btn btn-cancel-form" href="#">Cancel</a>
    </div>
    <input type="hidden" name="tzoffset" id="tzoffset" value="">
    <input type="hidden" name="task_id[new]" value="">
    <input type="hidden" name="timeline_id[new]" value="">
    <input type="hidden" name="parent_id[new]" value="">
    </fieldset>
</div>

<?php include 'footer.php';