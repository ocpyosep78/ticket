<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

global $current_user_id;
$current_user_id = $this->orca_auth->user->id;

 function tickets_datatable_script() { 
     global $current_user_id; ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/DT_bootstrap.css'); ?>">
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/DT_bootstrap.js'); ?>"></script>
    <script>
        $(function() {
            $("#dataTable").dataTable({
                bProcessing: true,
                bServerSide: true,
                sDom: "<'row-fluid'<'span9 toolbar'l><'span3'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                sPaginationType: "bootstrap",
                sAjaxSource: "<?php echo site_url('tasks/all'); ?>",
                fnServerParams: function ( aoData ) {
                    aoData.push({name:"user_id", "value": "<?php echo  $current_user_id; ?>"});
                    aoData.push({name:"status", "value": $("#filterStatus").val()});
                },
                aoColumns: [
                    {mData: 'id', sWidth: "50px", bSearchable:false},
                    {mData: function(src,t,v) {
                       return '<a href="<?php echo site_url('projects/tasks?id='); ?>'+src.id+'">'+src.project_title+'</a>';
                    }},
                    {mData: function(src,t,v) {
                       return '<a href="<?php echo site_url('projects/tasks?id='); ?>'+src.id+'">'+src.timeline_title+'</a>';
                    }},
                    {mData: function(src,t,v) {
                        var descr = src.description;
                        if (descr.length > 140)
                            descr = descr.substring(0,140) + '..';
                            return '<b>'+ src.task + '</b>'+
                                '<div class="description">'+descr.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/(\r\n|\n)/g, '<br>')+'</div>';
                    }, sWidth: "40%"},
                    {mData: 'assigned_username'},
                    {mData:function(src,t,v){
                            return src.status == 0 ? '<span class="label label-info">incomplete</span>' : '<span class="label label-warning">complete</span>';
                    }, bSearchable:false},
                    {mData: function(src,t,v) {
                            var due = parseDate( src.due, 'yyyy-mm-dd' );
                            if (src.status == 1) {
                                var complete = parseDate( src.complete_date, 'yyyy-mm-dd hh:ii:ss' );
                                if ( complete.getTime() < due.getTime()  ) {
                                    return '<span class="label label-success">' + timeSince( complete.getTime(), due.getTime() ) + ' lebih cepat</span>';
                                } else {
                                    return '<span class="label label-warning">' + timeSince( due.getTime(), complete.getTime() ) + ' terlambat</span>';
                                }
                            } else {
                                var d = new Date(), now = new Date( d.getTime()+d.getTimezoneOffset() );
                                if ( now.getTime() > due.getTime() ) {
                                    return '<span class="label label-info">sisa ' + timeSince( due.getTime(), now.getTime() ) + '</span>';
                                } else {
                                    return '<span class="label label-important">terlambat ' + timeSince( now.getTime(), due.getTime() ) + '</span>';
                                }
                            }
                    }},
                    {mData: function(src,t,v) {
                            return '<a href="<?php echo site_url('tasks/detail?id='); ?>'+src.id+'" class="btn btn-small btn-details">details</a>';
                    },bSortable:false, bSearchable:false},
                    {mData: function(src,t,v) {
                            return src.project_user_id == window.USER_ID || has_perms('tasks/edit') ?'<a href="<?php echo site_url('projects/tasks?id='); ?>'+src.project_id+'" class="btn btn-small btn-edit">edit</a>' : '';
                    },bSortable:false, bSearchable:false}
                ],
                fnDrawCallback : function() {
                    //
                },
                fnCreatedRow: function( nRow, aData, iDataIndex ) {
                    var status = ['incomplete','complete'];
                    $(nRow).addClass('status-'+status[aData.status]);
                }
            });
            
            var optStatus = '<option value="">[any]</option><option value="0">incomplete</option><option value="1">complete</option>'; 
            $(".dataTables_filter input").prop('placeholder', 'search');
            $('.dataTables_wrapper .toolbar').append(' <select class="input-medium" id="filterStatus">'+optStatus+'</select>');
            $("input,select,textarea").tooltip({placement:'right'});
            $("label").tooltip({placement:'left'});
            
            $("#filterStatus").change(function() {
                $("#dataTable").dataTable()._fnReDraw();
            });
        });
        
    </script>
<?php }

add_action('page_foot', 'tickets_datatable_script');

$page_title = $this->orca_auth->user->username . '\'s Tasks';
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
                    <th>Project</th>
                    <th>Timeline</th>
                    <th>Summary</th>
                    <th>Assigned</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
            </table>
            
        </div>
    </div>
</div>

<?php include 'footer.php';
