<?php

function comment_upload_script() { ?>
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
                $("#commentForm").submit();
            }
        });
        
        $("#commentForm").submit(function() {
            if ( !$.trim($("#comment_text").val()) ) {
                alert("Please enter comment");
                $("#comment_text").focus();
                return false;
            }
            
            if ( $("#filelist .addedfile").length > 0 ) {
                isSubmit = true;
                uploader.start();
                return false;
            }
        });
    });
</script>
<?php }
add_action('page_foot', 'comment_upload_script');

?>
                <h3>Comments</h3>
                <ol id="comment-list">
                    <?php foreach($comments as $comment): ?>
                    <li>
                        <span class="label label-user label-info"><?php echo $comment->comment_user; ?></span> <?php echo nl2br(htmlspecialchars($comment->comment_text)); ?>
                        <span class="label label-date"><?php echo time_since2($comment->comment_date); ?></span>
                        <?php if ($comment->attachments): ?>
                        <ul class="attachment">
                            <?php
                            $upload_url = base_url('files/attachments');
                            foreach($comment->attachments as $att): ?>
                            <li><?php echo '<a style="display:block" class="uploadfile '.get_ext($att->filename).'" href="'.$upload_url.'/'.$att->filename.'">'.basename($att->filename).'</a>'; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <form id="commentForm" class="form-horizontal" action="<?php echo site_url('comments/add?'.$this->id_name.'='.$this->id);?>" method="POST" enctype="multipart/form-data">
                    <div class="control-group">
                        <textarea id="comment_text" name="comment_text" placeholder="Add comment" class="autogrow input-xxlarge"
                                data-toggle="tooltip" 
                                title="Please enter your comment here"></textarea>
                    </div>
                    <div class="control-group">
                        <label>Attach Files</label>
                        <div id="uploadcontainer">
                            <div id="filelist"></div>
                            <a id="pickfiles" class="btn btn-info btn-small" href="#">Select files</a>
                            <a id="uploadfiles" class="btn btn-small" href="#">Upload files</a>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
