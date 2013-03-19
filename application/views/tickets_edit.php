<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

class TicketsEditHeaderScript {
    function __construct($project, $ticket) {
        $this->project = $project;
        $this->ticket = $ticket;
    }
    
    function add_head() { ?>
    <?php }
    
    function add_script() { ?>
    <script type="text/javascript" src="<?php echo base_url('assets/js/plupload/js/browserplus-min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/plupload/js/plupload.full.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/bootstrap-typeahead.js'); ?>"></script>
    
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
                $("#ticketForm").submit();
            }
        });
        
        $("#ticketForm").submit(function() {
            if ( !$.trim($("#id_type").val()) ) {
                alert("Please select ticket type");
                $("#id_type").focus();
                return false;
            }
            
            if ( !$.trim($("#id_title").val()) ) {
                alert("Ticket title is empty");
                $("#id_title").focus();
                return false;
            }
            
            if ( !$.trim($("#id_description").val()) ) {
                alert("Ticket description is empty");
                $("#id_description").focus();
                return false;
            }
            
            if ( $("#filelist .addedfile").length > 0 ) {
                isSubmit = true;
                uploader.start();
                return false;
            }
        });
        
        $("input,select,textarea").tooltip({placement:'right'});
        $("label").tooltip({placement:'left'});
        
       $('#id_assigned_user').typeahead({
           source: function(th, query) {
               $.ajax({
                   url: '<?php echo site_url('tickets/users?project_id='.$this->project->id); ?>',
                   data: {q:query},
                   dataType:'json',
                   success: function(data) {
                       if (window.console) console.log(['data',data]);
                       th.process(data.users);
                   }
               });
           },
           property: 'username'
       });
       
       $(".delatt").click(function() {
            if (!confirm("Delete this attachment?"))
                return false;
            $(this).parent().remove();
            return false;
       });
    });
    </script>

    <?php }
}
$hook = new TicketsEditHeaderScript($project,$ticket);
add_action('page_head', array(&$hook, 'add_head'));
add_action('page_foot', array(&$hook, 'add_script'));

$page_title = $ticket ? "Edit Ticket #{$ticket->id}" : "Create Ticket";
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
            
            <form enctype="multipart/form-data" id="ticketForm" class="form-horizontal" action="<?php echo site_url('tickets/edit?id='.$this->id.'&project_id='.$project->id);?>" method="POST">
                <div class="control-group">
                    <label class="control-label" for="id_title">Title</label>
                    <div class="controls">
                        <input type="text" id="id_title" name="title" placeholder="Ticket title" class="input-xlarge"
                                data-toggle="tooltip" 
                                title="Please enter your ticket title"
                                value="<?php echo empty($ticket->title) ? '' : htmlspecialchars($ticket->title); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="id_description">Description</label>
                    <div class="controls">
                        <textarea id="id_description" name="description" placeholder="Ticket description" class="autogrow input-xxlarge"
                                data-toggle="tooltip" 
                                title="Please enter ticket description"><?php echo empty($ticket->description) ? '' : htmlspecialchars($ticket->description); ?></textarea>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Attach Files</label>
                    <div class="controls">
                        <div id="uploadcontainer">
                            <div id="filelist">
                                <?php if ( !empty($ticket->attachments) ) { foreach( $ticket->attachments as $att ):
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
                <div class="control-group">
                    <label class="control-label" for="id_type">Type</label>
                    <div class="controls">
                        <select name="type" id="id_type" class="combobox input-medium" data-toggle="tooltip" title="Please choose ticket type" placeholder="Choose type">
                            <option value="">[pick a type]</option>
                            <?php sort($this->types); foreach($this->types as $type): ?>
                            <option value="<?php echo $type; ?>"<?php echo !empty($ticket->type) && $type == $ticket->type ? ' selected="selected"':''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="id_severity">Severity</label>
                    <div class="controls">
                        <select name="severity" id="id_severity" class="combobox input-large" data-toggle="tooltip" title="Ticket severity" placeholder="Choose severity">
                            <option value="">[pick a severity level]</option>
                            <?php sort($this->types); foreach($this->severity as $type): ?>
                            <option value="<?php echo $type; ?>"<?php echo !empty($ticket->severity) && $type == $ticket->severity ? ' selected="selected"':''; ?>><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tickets/assign' ) ||  $this->orca_auth->user->id == $project->user_id ): ?>
                <div class="control-group">
                    <label class="control-label" for="id_assigned_user">Assign To</label>
                    <div class="controls">
                        <input type="text" id="id_assigned_user" name="assign_user" placeholder="Assigned to" class="input-xxlarge" style="width:300px;"
                                value="<?php echo empty($ticket->assigned_user_name) ? '' : htmlspecialchars($ticket->assigned_user_name); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="id_status">Status</label>
                    <div class="controls">
                        <select name="status" id="id_status" data-toggle="tooltip" title="Ticket status" placeholder="Choose status">
                            <option value="">[ticket status]</option>
                            <option value="close"<?php echo !empty($ticket->status) && 'close' == $ticket->status ? ' selected="selected"':''; ?>>close</option>
                            <option value="open"<?php echo !empty($ticket->status) && 'open' == $ticket->status ? ' selected="selected"':''; ?>>open</option>
                        </select>
                    </div>
                </div>
                <?php else: ?>
                <input type="hidden" name="status" value="open">
                <input type="hidden" name="assign_user" value="0">
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a class="btn" href="<?php echo site_url('projects/tickets?id='.$project->id); ?>">Cancel</a>
                </div>
            </form>
            
        </div>
    </div>
</div>

<?php include 'footer.php';
