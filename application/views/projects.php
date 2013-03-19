<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function datatable_script() { 
    ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/DT_bootstrap.css'); ?>">
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/DT_bootstrap.js'); ?>"></script>
    <script>
        $(function() {
            $("#dataTable").dataTable({
                bProcessing: true,
                bServerSide: true,
                sPaginationType: "bootstrap",
                sAjaxSource: "<?php echo site_url('projects/all'); ?>",
                aoColumns: [
                    {mData: 'id', sWidth: "60px"},
                    {mData: 'created_on', sWidth: "100px"},
                    {mData:'user_create', sWidth:'80px'},
                    {mData: function(src, type, val) {
                        var descr = src.description;
                        if (descr.length > 140)
                            descr = descr.substring(0,140) + '..';
                        descr = descr.replace(/\r\n|\n/g, '<br>');
                        return '<h2 class="content-title">'+src.title+'</h2>'+
                            '<div class="content-descr"><p>'+descr+'</p></div>'+
                            '<div class="content-meta">'+
                                '<span class="btn-group">'+
                                    (has_perms('projects/tasks')?'<a class="btn btn-small btn-tasks" href="<?php echo site_url('projects/tasks?id='); ?>'+src.id+'">tasks</a>':'')+
                                    (has_perms('tickets/') ? '<a class="btn btn-small btn-tickets" href="<?php echo site_url('projects/tickets?id='); ?>'+src.id+'">tickets</a>':'')+
                                    (has_perms('projects/edit') ?'<a class="btn btn-small btn-edit" href="<?php echo site_url('projects/edit?id='); ?>'+src.id+'">edit</a>':'') +
                                '</span>';
                    }, bSortable:false}
                ],
                fnDrawCallback : function() {
                }
            });
            //modif dataTables
            if ( has_perms( 'projects/add' ) )
                $('.dataTables_wrapper .toolbar').append('<a href="<?php echo site_url('projects/edit'); ?>" class="btn btn-primary btn-add">Create Project</a>');
            $(".dataTables_filter input").prop('placeholder', 'search');
            $("input,select,textarea").tooltip({placement:'right'});
            $("label").tooltip({placement:'left'});
        });
        
    </script>
<?php }
add_action('page_foot', 'datatable_script');

$page_title = 'Projects';
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
            
            <table id="dataTable" class="table table-bordered table-stripped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Created</th>
                    <th>Owner</th>
                    <th>Project</th>
                </tr>
            </thead>
            <tbody></tbody>
            </table>
            
        </div>
    </div>
</div>

<?php include 'footer.php';
