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
    var auto_no = 0,
		isSubmit = false,
        getExt = function(filename) {
            var index = filename.lastIndexOf('.'), ext = '';
            if (index > 0) ext = filename.toLowerCase().substring( index+1 );
            if (!ext) {
                ext = 'file';
            } if (/txt|doc|ppt|xls|pdf/i.test( ext )) {
                ext = 'document ' + ext;
            } else if (/png|gif|jpg|jpeg|tiff|bmp/i.test( ext )) {
                ext = 'image ' + ext;
            } else if (/mp3|aac|wav|au|ogg|wma/i.test( ext )) {
                ext = 'audio ' + ext;
            } else if (/mpg|wmv|mov|flv/i.test( ext )) {
                ext = 'video ' + ext;
            }
            return ext;
        };
	
    $(function() {
        $(".btn-add-task").click(function() {
			// local instance
			auto_no++;
            var $button = $(this);
			var form_id = new Date().getTime();
            
            //copy template form
            var $form = $(".addTaskForm").clone();
            
            //set default name & value
            $form.find('[name]').each(function() {
                var el = $(this);
                el.prop('name', el.prop('name') + '[' + auto_no + ']');
            });
            $form.find('[name^=timeline_id]').val( $button.data('timelineId') );
            $form.find('[name^=parent_id]').val( $button.data('parentId') );
            
            //add to page and display
            $button.parent().after($form);
			$form.attr('id', form_id);
            $form.removeClass('addTaskForm').addClass('taskForm').slideDown('slow');
            
            //init form & upload
			$('.taskForm .swap-id').each(function() {
				$(this).removeClass('swap-id');
				$(this).attr('id', $(this).data('id') + auto_no);
			});
			Func.InitForm({ Container: '#' + form_id });
			Func.InitUpload({ container: 'uploadcontainer' + auto_no, browse_button: 'pickfiles' + auto_no, attachment: 'attachment[new][' + auto_no + '][]' });
			$('#' + form_id + ' .datepicker').val(Func.GetStringFromDate(new Date()));
			
            $form.find('.btn-cancel-form').click(function() {
                $(this).parents('.taskForm').remove();
                return false;
            });
            
            return false;
        });
        
        $('.btn-edit-task').click(function() {
            var $button = $(this);
            var $form = $(".addTaskForm").clone();
			var form_id = new Date().getTime();
			var task_id = $button.data('taskId');
			
            $form.find('[name]').each(function() {
                var el = $(this);
                el.prop('name', el.prop('name').replace(/new/, $button.data('taskId')) + '[0]');
            });
            
            $.get(web.host + 'index.php/tasks/ajaxdetail?id=' + $button.data('taskId'), function(r) {
                var task = eval('('+r+')');
                if (task.task) {
                    $form.find('[name^=task]').val( task.task.task );
                    $form.find('[name^=description]').val( task.task.description );
                    $form.find('[name^=due]').val( Func.SwapDate(task.task.due) );
                    $form.find('[name^=timeline_id]').val( $button.data('timelineId') );
                    $form.find('[name^=parent_id]').val( $button.data('parentId') );

                    //add to page and display
                    $button.parents('li').find('.task-description[data-task-id='+$button.data('taskId')+']').after($form);
                    $form.removeClass('addTaskForm').addClass('taskForm').slideDown('slow');
					
					//init form & upload
					$('.taskForm .swap-id').each(function() {
						$(this).removeClass('swap-id');
						$(this).attr('id', $(this).data('id') + task_id);
					});
					Func.InitForm({ Container: '#' + form_id });
					Func.InitUpload({ container: 'uploadcontainer' + task_id, browse_button: 'pickfiles' + task_id, attachment: 'attachment[' + task_id + '][0][]' });
					
                    $form.find('.btn-cancel-form').click(function() {
                        $(this).parents('.taskForm').remove();
                        return false;
                    });
                }
            });
            
            return false;
        });
        
        $('.btn-done').on('click', function() {
            if (!confirm("Mark this task as done?"))
                return false;
            var link = $(this);
            var taskId = link.data('taskId');
            $.post(web.host + 'index.php/tasks/ajaxdone', {id:taskId}, function() {
                link.replaceWith($('<span class="label label-info">done</label>'));
            });
            return false;
        });
        
        $('.btn-delete-task').on('click', function() {
            if (!confirm("Delete this task?"))
                return false;
            var link = $(this);
            var taskId = link.data('taskId');
            $.post(web.host + 'index.php/tasks/ajaxdelete', {id:taskId}, function(raw) {
				eval('var result = ' + raw);
				if (result.success)
					$("#task-"+taskId).remove();
            });
            return false;
        });
        
        $('.toggle-description').click(function() {
            var taskid = $(this).data('taskId');
            $('.task-description[data-task-id='+taskid+']').toggle();
            return false;
        });
        
		$('#tasksForm').submit(function() {
			$('.datepicker').each(function() {
				$(this).val(Func.SwapDate($(this).val()));
			});
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
            <div class="content-descr"><?php echo nl2br(htmlspecialchars($project->description)); ?></div>
			
            <?php $this->load->view('common/files', array( 'project_id' => $project->id )); ?>
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
                    
                    if ( $user_id != $_project->user_id ) {
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
                                    User: <span class="assigned label label-info">'.($task->assigned_user?$task->assigned_user:'None').'</span>
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
            </form>
			<div class="form-actions">
				<a class="btn" href="<?php echo site_url('projects/'); ?>">Back to Projects</a>
				<a class="btn" href="<?php echo site_url('projects/calender?id='.$project->id); ?>">Calender</a>
			</div>
        </div>
    </div>
</div>

<div class="hide addTaskForm">
    <fieldset>
    <div class="control-group">
        <input type="text" name="task[new]" placeholder="Task title" class="input-xlarge" data-toggle="tooltip" title="Task title" />
    </div>
    <div class="control-group">
        <textarea name="description[new]" placeholder="Task description" class="autogrow input-xlarge" data-toggle="tooltip" title="Task description"></textarea>
    </div>
    <div class="control-group">
        <label>
			Assign to:
			<select name="user_id[new]" placeholder="Assigned to" data-toggle="tooltip" title="Assign this task to selected user">
				<?php
					$users = $this->db->query("SELECT * FROM users " . ( $this->orca_auth->user->client_id ? "WHERE client_id = {$this->orca_auth->user->client_id}" : "" ). " ORDER BY username")->result(); 
					foreach($users as $u) { echo '<option value="'.$u->id.'">'.$u->username.'</option>'; }
				?>
			</select>
		</label>
    </div>
    <div class="control-group">
        <label>
			Due date:
            <span class="input-append">
                <input type="text" name="due[new]" class="input-small datepicker" value="">
                <span class="add-on"><i class="icon-th"></i></span>
            </span>
        </label>
    </div>
	<div class="control-group">
		<label>Attach Files</label>
		<div data-id="uploadcontainer" class="swap-id">
			<div class="filelist"></div>
			<a data-id="pickfiles" class="btn btn-info btn-small swap-id" href="#">Select files</a>
			<a class="btn btn-small uploadfiles" href="#">Upload files</a>
		</div>
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