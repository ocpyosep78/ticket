<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

global $clients,$groups;

$query = $this->db->query("SELECT * FROM clients");
$clients=array();
$clients[0]='admin';
foreach($query->result() as $row)
    $clients[$row->client_id]=$row->name;

$query = $this->db->query("SELECT * FROM groups");
$groups=array();
$groups[0]='no group';
foreach($query->result() as $row)
    $groups[$row->group_id]=$row->group_name;


function datatable_script() { 
    global $clients, $groups;
    ?>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/DT_bootstrap.css'); ?>">
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.dataTables.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/DT_bootstrap.js'); ?>"></script>
    <script>
        <?php
        echo 'var clients = (' . json_encode($clients) . ');';
        echo 'var groups = (' . json_encode($groups) . ');';
        ?>
            
        function loadGroups(object) {
            var groups = object.groups;
            var selected = object.selected;

            var modalBody = $("#groupModal .modal-body");
            modalBody.empty();
            var str = '';
            for(var i=0,len=groups.length; i<len; i++) {
                var checked = typeof selected[ groups[i].group_id ] != 'undefined' && selected[ groups[i].group_id ] ? 'checked' : ''; 
                str += '<label class="checkbox"><input type="checkbox" '+checked+' name="selected['+groups[i].group_id+']" value="'+groups[i].group_id+'"> '+groups[i].group_name+'</label><br>';
            }
            modalBody.append(str);
            
            $("#groupModal input[name=id]").val(object.user_id);
            $("#groupModal").modal('show');
        }
            
        $(function() {
            $("#dataTable").dataTable({
                bProcessing: true,
                bServerSide: true,
                sPaginationType: "bootstrap",
                sAjaxSource: "<?php echo site_url('users/all'); ?>",
                aoColumns: [
                    {mData: 'id'},
                    {mData: 'username'},
                    {mData: 'email'},
                    {mData: 'name'},
                    {mData: function(source,type,val) {
                        return typeof clients[source.client_id] != 'undefined' ? clients[source.client_id] : 'N/A';
                    }},
                    {mData: function(src, type, val) {
                        return '<a class="btn btn-primary btn-groups" href="<?php echo site_url('users/groups?id='); ?>'+src.id+'">Groups</a>';
                    }, bSortable:false},
                    {mData: function(src, type, val) {
                        return '<a class="btn btn-success btn-edit" href=""><i class="icon-edit"></i></a>';
                    }, bSortable:false},
                    {mData: function(src, type, val) {
                        if (!has_perms( 'users/delete' ))
                            return '';
                        return '<a class="btn btn-danger btn-delete" href="<?php echo site_url('users/delete?id='); ?>'+src.id+'"><i class="icon-trash"></i></a>';
                    }, bSortable:false}
                ],
                fnDrawCallback : function() {
                    $(".btn-edit").on('click',function() {
                        var dt = $('#dataTable').dataTable();
                        var pos = dt.fnGetPosition(this.parentNode);
                        var data = dt.fnGetData( pos[0] );
                        for(var k in data) {
                            var el = $("#userModal [name='"+k+"']");
                            if (el.prop('type') == 'checkbox') {
                                if ( data[k] ) el.prop('checked', 'checked');
                                else el.removeProp('checked');
                            } else {
                                el.val( data[k] );
                            }
                        }
                        $("#userModal").modal('show');
                        return false;
                    });
                    $('.btn-delete').on('click', function() {
                        var link = $(this).prop('href');
                        if (confirm("Delete this record?")) {
                            $.post( link, {}, function() {
                                $('#dataTable').dataTable()._fnReDraw();
                            });
                        }
                        return false;
                    });
                    $('.btn-groups').on('click',function() {
                        var link = $(this).prop('href');
                        $.get( link, function(resp) {
                            var object = eval('('+ resp +')');
                            loadGroups(object);
                        });
                        return false;
                    });
                }
            });
            
            $("#userForm").on('submit', function() {
                var f = $(this);
                $.ajax({
                    type:'POST',
                    url:f.prop('action'),
                    data:f.serialize(),
                    success:function(resp) {
                        var result = eval('('+resp+')');
                        if (result.success) { 
                            $('#dataTable').dataTable()._fnReDraw();
                            $("#userModal").modal('hide');
                        } else {
                            alert(result.msg);
                        }
                    },error: function(xhr, textStatus, error) {
                        alert("Error while saving (" + xhr.status + ") " + error);
                    }
                });
                return false;
            });
            
            $("#groupForm").on('submit',function() {
                var f = $(this);
                $.ajax({
                    type:'POST',
                    url:f.prop('action'),
                    data:f.serialize(),
                    success:function(resp) {
                        var result = eval('('+resp+')');
                        if (result.success) { 
                            loadGroups(result);
                        } else {
                            alert(result.msg);
                        }
                    },error: function(xhr, textStatus, error) {
                        alert("Error while saving (" + xhr.status + ") " + error);
                    }
                });
                return false;
            });
            
            //modif dataTables
            $(".dataTables_filter input").prop('placeholder', 'search');
            $('.dataTables_wrapper .toolbar').append('<button class="btn btn-primary btn-add">Add User</button>');
            $(".btn-add").on('click', function() {
                $("#userForm").find('select,textarea,input').each(function() {
                    var i = $(this);
                    if (i.prop('type') == 'button'||i.prop('type') == 'submit')
                        return;
                    else if (i.prop('type') == 'checkbox' || i.prop('type') == 'radio')
                        i.removeProp('checked');
                    else i.val('');
                });
                $("#userModal").modal('show');
                return false;
            });
            
            //tooltips
            $("input,select,textarea").tooltip({placement:'right'});
            $("label").tooltip({placement:'left'});
        });
        
    </script>
<?php }
add_action('page_foot', 'datatable_script');

$page_title = 'User Management';
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
                    <th>Username</th>
                    <th>E-Mail</th>
                    <th>Name</th>
                    <th>Client</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody></tbody>
            </table>
            
            <!-- User Form -->
            <div id="userModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h3 id="modalTitle">User Details</h3>
                </div>
                <form method="post" action="<?php echo site_url('users/save'); ?>" class="form-horizontal" id="userForm">
                <div class="modal-body">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="id_username">Username</label>
                            <div class="controls">
                                <input type="text" id="id_username" name="username" placeholder="Username" 
                                       data-toggle="tooltip" 
                                       title="Username is required, must be no space and in lower case">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="id_email">E-Mail</label>
                            <div class="controls">
                                <input type="text" id="id_email" name="email" placeholder="E-Mail" 
                                       data-toggle="tooltip" 
                                       title="E-Mail is required, must be in valid e-mail format">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="id_name">Display name</label>
                            <div class="controls">
                                <input type="text" id="id_name" name="name" placeholder="Display name"
                                       data-toggle="tooltip" 
                                       title="Enter display name or full name here">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="id_group">Group</label>
                            <div class="controls">
                                <select name="group_id" id="id_group"
                                       data-toggle="tooltip" 
                                       title="Select group permission for this user">
                                    <?php foreach($groups as $group_id => $group_name): ?>
                                    <option value="<?php echo $group_id; ?>"><?php echo htmlspecialchars($group_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="id_client">Client</label>
                            <div class="controls">
                                <select name="client_id" id="id_group"
                                       data-toggle="tooltip" 
                                       title="Select user client ID">
                                    <?php foreach($clients as $client_id => $client_name): ?>
                                    <option value="<?php echo $client_id; ?>"><?php echo htmlspecialchars($client_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Change password</legend>
                        <div class="control-group">
                            <label class="control-label" for="id_password">Password</label>
                            <div class="controls">
                                <input type="text" id="id_password" name="password" placeholder="Enter new password"
                                       data-toggle="tooltip" 
                                       title="If you want to change or set the password, otherwise set to blank">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button id="saveUser" class="btn btn-primary">Save changes</button>
                    <input type="hidden" name="id" id="id" value="">
                </div>
                </form>
            </div>
            
            <div id="groupModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalTitle1" aria-hidden="true">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                    <h3 id="modalTitle1">User Groups</h3>
                </div>
                <form method="post" action="<?php echo site_url('users/groups'); ?>" class="form-horizontal" id="groupForm">
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button id="saveGroup" class="btn btn-primary">Save changes</button>
                    <input type="hidden" name="id" id="id" value="">
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php';
