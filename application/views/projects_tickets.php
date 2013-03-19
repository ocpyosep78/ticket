<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * ini untuk callback di template footer.php,
 * saya pakai class karena kudu passing variable project di template 
 */
class TicketsScriptHook {
    function __construct($project) {
        $this->project = $project;
    }
    
    function tickets_datatable_script() { 
        require_once APPPATH.'controllers/tickets.php';
        $tickets = new Tickets();
    ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/DT_bootstrap.css'); ?>">
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/DT_bootstrap.js'); ?>"></script>
    <script>
        var STATUS = <?php echo json_encode($tickets->status); ?>,
            TYPES = <?php echo json_encode($tickets->types); ?>,
            SEVERITY = <?php echo json_encode($tickets->severity); ?>;
        
        $(function() {
            $("#dataTable").dataTable({
                bProcessing: true,
                bServerSide: true,
                sDom: "<'row-fluid'<'span9 toolbar'l><'span3'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                sPaginationType: "bootstrap",
                sAjaxSource: "<?php echo site_url('tickets/all'); ?>",
                fnServerParams: function ( aoData ) {
                    aoData.push({name:"project_id", "value": "<?php echo $this->project->id; ?>"});
                    aoData.push({name:"severity", "value": $("#filterSeverity").val()});
                    aoData.push({name:"status", "value": $("#filterStatus").val()});
                    aoData.push({name:"type", "value": $("#filterType").val()});
                },
                aoColumns: [
                    {mData: 'id', sWidth: "50px", bSearchable:false},
                    {mData: 'created_on', bSearchable:false},
                    {mData: 'create_user'},
                    {mData: function(src,t,v) {
                        var descr = src.description;
                        if (descr.length > 140)
                            descr = descr.substring(0,140) + '..';
                            return '<b>'+ src.title + '</b>'+
                                '<div class="description">'+descr.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/(\r\n|\n)/g, '<br>')+'</div>';
                    }, sWidth: "40%"},
                    {mData:function(src,t,v){ 
                            return '<span class="label">'+src.type+'</span>'; 
                    }, bSearchable:false},
                    {mData:'severity', bSearchable:false},
                    {mData:'assigned_username'},
                    {mData:'status', bSearchable:false},
                    {mData: function(src,t,v) {
                            return '<a href="<?php echo site_url('tickets/detail?id='); ?>'+src.id+'" class="btn btn-small btn-details">details</a>';
                    },bSortable:false, bSearchable:false},
                    {mData: function(src,t,v) {
                            return src.project_user_id == window.USER_ID || has_perms('tickets/edit') ? '<a href="<?php echo site_url('tickets/edit?id='); ?>'+src.id+'" class="btn btn-small btn-edit">edit</a>' : '';
                    },bSortable:false, bSearchable:false}
                ],
                fnDrawCallback : function() {
                    //
                },
                fnCreatedRow: function( nRow, aData, iDataIndex ) {
                    $(nRow).addClass(aData.severity).addClass(aData.type).addClass('status-'+aData.status);
                }
            });
            
            var optStatus = '<option value="">[status]</option>', 
                optSeverity='<option value="">[severity]</option>', 
                optType = '<option value="">[type]</option>';
            for( var k=0,len=STATUS.length; k<len; k++ )
                optStatus += '<option value="'+STATUS[k]+'">'+STATUS[k]+'</option>';
            for( var k=0,len=SEVERITY.length; k<len; k++ )
                optSeverity += '<option value="'+SEVERITY[k]+'">'+SEVERITY[k]+'</option>';
            for( var k=0,len=TYPES.length; k<len; k++ )
                optType += '<option value="'+TYPES[k]+'">'+TYPES[k]+'</option>';
            
            //modif dataTables
            $(".dataTables_filter input").prop('placeholder', 'search');
            $('.dataTables_wrapper .toolbar').append('<a href="<?php echo site_url('tickets/edit?project_id='.$this->project->id); ?>" class="btn btn-primary btn-add">Create Ticket</a>' + 
                ' <select class="input-medium" id="filterType">'+optType+'</select>' +
                ' <select class="input-medium" id="filterSeverity">'+optSeverity+'</select>' +
                ' <select class="input-medium" id="filterStatus">'+optStatus+'</select>' +
                ''
            );
            $("input,select,textarea").tooltip({placement:'right'});
            $("label").tooltip({placement:'left'});
            
            $("#filterType,#filterSeverity,#filterStatus").change(function() {
                $("#dataTable").dataTable()._fnReDraw();
            });
        });
        
    </script>
<?php }
}
$ticketsHook = new TicketsScriptHook($project);
add_action('page_foot', array(&$ticketsHook, 'tickets_datatable_script'));

$page_title = $project->title . '\'s Tickets';
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
                    <th>By</th>
                    <th>Summary</th>
                    <th>Type</th>
                    <th>Severity</th>
                    <th>Assigned</th>
                    <th>Status</th>
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
