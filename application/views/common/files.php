<?php
	$Param = array();
	if (!empty($task_id))
		$Param['filter'] = '[{"type":"numeric","comparison":"eq","value":"'.$task_id.'","field":"task_id"}]';
	if (!empty($project_id))
		$Param['filter'] = '[{"type":"numeric","comparison":"eq","value":"'.$project_id.'","field":"project_id"}]';
	$ArrayFile = $this->File_model->GetArray($Param);
?>

<?php if (count($ArrayFile) > 0) { ?>
	<div class="history-div">
		<h4>Attachment</h4>
		<ul class="history-list">
			<?php foreach($ArrayFile as $file) : ?>
				<li><a href="<?php echo $file['file_link']; ?>"><?php echo basename($file['filename']); ?></a></li>
			<?php endforeach; ?> 
		</ul>
	</div>
<?php } ?>