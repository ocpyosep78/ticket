<?php
	$project_id = (isset($_GET['id'])) ? $_GET['id'] : 0;
	$project = $this->Project_model->GetByID(array('id' => $project_id));
	$page_title = 'Project ' . (empty($project['title']) ? '' : $project['title']);
	$array_calender = $this->Task_model->GetArrayCalender(array('project_id' => $project_id));
?>

<?php include 'header.php'; ?>
<?php include 'pagetop.php'; ?>

<div id="array_calender" class="hidden"><?php echo json_encode($array_calender); ?></div>

<div class="container-fluid" id="maincontent">
    <div class="row-fluid">
        <div class="span12" id="content">
            <h1><?php echo $page_title; ?></h1>
            <div class="content-descr"><?php echo nl2br(htmlspecialchars($project['description'])); ?></div>
			
			<?php $this->load->view('common/files', array( 'project_id' => $project['id'] )); ?>
            <?php include 'history.php'; show_history( 'projects', $project['id'] ); ?>
            
			<h3>Timelines &amp; Tasks</h3>
			<div id="calendar"></div>
			
			<div class="form-actions">
				<a class="btn" href="<?php echo site_url('projects/'); ?>">Back to Projects</a>
				<a class="btn" href="<?php echo site_url('projects/tasks?id='.$project['id']); ?>">Tree</a>
			</div>
        </div>
    </div>
</div>

<div id="window-calender" class="modal modal-big hide fade big-popup" style="" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
	<div class="modal-header">
		<a href="#" class="close" data-dismiss="modal">&times;</a>
		<h3>Task Detail</h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label">Task</label>
				<div class="controls"><input type="text" name="task" readonly="readonly" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Description</label>
				<div class="controls"><textarea name="description" readonly="readonly" style="height: 80px;" ></textarea></div>
			</div>
			<div class="control-group">
				<label class="control-label">User</label>
				<div class="controls"><input type="text" name="user_name" readonly="readonly" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Due Date</label>
				<div class="controls"><input type="text" name="due" readonly="readonly" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Complete Date</label>
				<div class="controls"><input type="text" name="complete_date" readonly="readonly" /></div>
			</div>
			<div class="control-group">
				<label class="control-label">Status</label>
				<div class="controls"><input type="text" name="status" readonly="readonly" /></div>
			</div>
		</form>
	</div>
</div>

<script>
	$(function () {
		var array_calender_raw = $('#array_calender').text();
		eval('array_calender = ' + array_calender_raw);
		
		var Local = {
			Calender: function() {
				for (var i = 0; i < array_calender.length; i++) {
					array_calender[i].start = eval(array_calender[i].start_text);
				}
				
				var calendar = $('#calendar').fullCalendar({
					aspectRatio: 2, selectable: true, selectHelper: true,
					editable: false, theme: false, eventColor: '#bcdeee',
					header: { left: 'prev next', center: 'title,today', right: 'month' },
					buttonText: { prev: '<i class="icon-chevron-left cal_prev" />', next: '<i class="icon-chevron-right cal_next" />' },
					events: array_calender,
					eventClick: function(event) {
						eval('var record = ' + event.desc);
						$('#window-calender input[name="task"]').val(record.task);
						$('#window-calender textarea[name="description"]').val(record.description);
						$('#window-calender input[name="user_name"]').val(record.user_name);
						$('#window-calender input[name="due"]').val(Func.SwapDate(record.due));
						$('#window-calender input[name="complete_date"]').val(Func.SwapDate(record.complete_date));
						$('#window-calender input[name="status"]').val((record.status == 1) ? 'complete' : 'incomplete');
						$('#window-calender').modal();
						return false;
					}
				})
			}
		}
		Local.Calender();
	})
</script>


<?php include 'footer.php';