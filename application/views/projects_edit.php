<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function project_edit_script() { ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/plupload/js/browserplus-min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/plupload/js/plupload.full.js'); ?>"></script>
<script type="text/javascript">
    var isSubmit = false,
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
        var uploader = new plupload.Uploader({
            runtimes : 'gears,html5,flash,silverlight,browserplus',
            browse_button : 'pickfiles',
            container : 'uploadcontainer',
            max_file_size : '10mb',
            url : '<?php echo site_url('files/upload'); ?>',
            flash_swf_url : '<?php echo base_url('assets/js/plupload/js/plupload.flash.swf'); ?>',
            silverlight_xap_url : '<?php echo base_url('assets/js/plupload/js/plupload.silverlight.xap'); ?>',
            filters : [
                {title : "Image files", extensions : "jpg,jpeg,gif,png"},
                {title : "Compressed files", extensions : "zip,gz,tar,bz2"},
                {title : "Document files", extensions : "pdf,doc,xls,docx,xlsx,pptx,ppt,odt,txt,rtf"}
            ],
            resize : {width : 320, height : 240, quality : 90}
        });

        uploader.bind('Init', function(up, params) {
            //$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
        });

        $('#uploadfiles').click(function(e) {
            if ( $("#filelist .addedfile").length > 0 )
                uploader.start();
            return false;
        });

        uploader.init();
        
        uploader.bind('FilesAdded', function(up, files) {
            $.each(files, function(i, file) {
                var ext = getExt(file.name);
                $('#filelist').append('<div class="addedfile uploadfile '+ext+'" id="' + file.id + '"><span class="filename">' + file.name + '</span> (' + plupload.formatSize(file.size) + ') <b></b>' + '</div>');
            });
            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('UploadProgress', function(up, file) {
            $('#' + file.id + " b").html(file.percent + "%");
        });

        uploader.bind('Error', function(up, err) {
            $('#filelist').append("<div class='alert alert-error'>Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "</div>");
            up.refresh(); // Reposition Flash/Silverlight
        });

        uploader.bind('FileUploaded', function(up, file, jsonresp) {
            if (window.console)
                console.log(['filesuploaded', file, jsonresp]);
            var json = eval('('+jsonresp.response+')');
            var div = $("#"+file.id);
            div.removeClass('addedfile').addClass('completefile').find('b').html("100%").after('<input type="hidden" name="attachment[]" value="'+json.result+'">');
            if ( $("#filelist .addedfile").length == 0 && isSubmit ) {
                isSubmit = false;
                $("#projectForm").submit();
            }
        });
        
        $("#projectForm").submit(function() {
            if ( !$.trim($("#id_title").val()) ) {
                alert("Please enter title");
                $("#id_title").focus();
                return false;
            }
            
            if ( $("#filelist .addedfile").length > 0 ) {
                isSubmit = true;
                uploader.start();
                return false;
            }
        });
        
        $(".delatt").click(function() {
            if (!confirm("Delete this attachment?"))
                return false;
            $(this).parent().remove();
            return false;
        });
        
        $("input,select,textarea").tooltip({placement:'right'});
        $("label").tooltip({placement:'left'});
    });
</script>
    
<?php }
add_action('page_foot', 'project_edit_script');

if ($this->id) {
    $page_title = 'Edit Project ' . (empty($project->title) ? '' : $project->title);
} else {
    $page_title = 'Create New Project';
}
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
            
            <form id="projectForm" class="form-horizontal" action="<?php echo site_url('projects/edit?id='.$this->id);?>" method="POST" enctype="multipart/form-data">
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="id_title">Title</label>
                        <div class="controls">
                            <input type="text" id="id_title" name="title" placeholder="Project title" class="input-xxlarge"
                                    data-toggle="tooltip" 
                                    title="Project title is required"
                                    value="<?php echo empty($project->title) ? '' : htmlspecialchars($project->title); ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="id_description">Description</label>
                        <div class="controls">
                            <textarea id="id_description" name="description" placeholder="Project description" class="autogrow input-xxlarge"
                                    data-toggle="tooltip" 
                                    title="Please enter project description"><?php echo empty($project->description) ? '' : htmlspecialchars($project->description); ?></textarea>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="id_user_id">Project manager</label>
                        <div class="controls">
                            <select id="id_user_id" name="user_id"
                                    placeholder="Set project manager"
                                    data-toggle="tooltip" 
                                    title="Select Project manager">
                                <option value="">{choose user to be project manager}</option>
                                <?php
                                $where = $this->orca_auth->user->client_id ? "WHERE client_id = {$this->orca_auth->user->client_id}" : "";
                                $users = $this->db->query("SELECT * FROM users $where ORDER BY username")->result(); 
                                foreach($users as $user)
                                    echo '<option value="'.$user->id.'"'.($user->id == $project->user_id? ' selected="selected"':'').'>'.$user->username.'</option>';
                                ?>
                                
                            </select>
                        </div>
                    </div>
                    <?php if ( !$this->orca_auth->user->client_id ): ?>
                    <div class="control-group">
                        <label class="control-label" for="id_client_id">Client</label>
                        <div class="controls">
                            <select id="id_client_id" name="client_id" placeholder="Isikan client dari project" class="input-large"
                                    data-toggle="tooltip" 
                                    title="Client project ini"><?php
                                    $query = $this->db->query("SELECT * FROM clients ORDER BY name");
                                    foreach($query->result() as $row)
                                    {
                                        echo '<option value="'.$row->client_id.'" '.(!empty($project->client_id) && $row->client_id == $project->client_id ? ' selected="selected"':'').'>'.$row->name.'</option>';
                                    }
                                    ?></select>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="control-group">
                        <label class="control-label">Attach Files</label>
                        <div class="controls">
                            <div id="uploadcontainer">
                                <div id="filelist">
                                    <?php if ( !empty($project->attachments) ) { foreach( $project->attachments as $att ):
                                        $ext = get_ext( $att->filename );
                                    ?>
                                    <div class="uploadfile <?php echo $ext; ?> completefile">
                                        <span class="filename"><?php echo basename($att->filename); ?></span> <b></b>
                                        <input type="hidden" name="attachment[]" value="<?php echo $att->filename; ?>">
                                        <a href="#" class="btn btn-danger btn-mini delatt">X</a>
                                    </div>
                                    <?php endforeach; } ?>
                                </div>
                                <a id="pickfiles" class="btn btn-info btn-small" href="#">Select files</a>
                                <a id="uploadfiles" class="btn btn-small" href="#">Upload files</a>
                            </div>
                        </div>
                    </div>
                    
                </fieldset>
                <fieldset>
                    <legend>Timelines</legend>
                    <?php
                    $timelines = false;
                    if (!empty($this->id))
                    {
                        $timelines = $this->db->query("SELECT * FROM timelines WHERE project_id = ?",array($this->id));
                    }
                    ?>
                    <ol class="timeline-list">
                        <?php if ($timelines): foreach($timelines->result() as $timeline): ?>
                        <li class="clearfix">
                            <input type="text" id="id_timeline<?php echo $timeline->id; ?>" name="timeline[<?php echo $timeline->id; ?>]" placeholder="Edit timeline #<?php echo $timeline->id; ?>" class="input-large"
                                    data-toggle="tooltip" 
                                    title="Rename this timeline"
                                    value="<?php echo htmlspecialchars($timeline->title); ?>">
                        </li>
                        <?php endforeach; endif; ?>
                        <li class="clearfix">
                            <label for="id_new_timeline">Add Timeline</label>
                            <input type="text" id="id_new_timeline" name="timeline[new]" placeholder="Add new timeline" class="input-large"
                                    data-toggle="tooltip" 
                                    title="Add new timeline">
                        </li>
                    </ol>
                </fieldset>
                <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a class="btn" href="<?php echo site_url('projects/'); ?>">Cancel</a>
                </div>
            </form>
            
            
        </div>
    </div>
</div>

<?php include 'footer.php';
