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
 * Description of tickets
 *
 * @author ferdhie
 */
class Tickets extends MY_Controller {
    
    var $status = array('open', 'close');
    var $types = array('bug', 'enhancement', 'request', 'duplicate', 'invalid', 'question', 'wontfix');
    var $severity = array('minor','normal','major', 'critical');
    
    public function index() {
        $this->load->view('my_tickets');
    }
    
    public function ajaxdone() {
        $strClient = $this->orca_auth->user->client_id ? " AND tickets.client_id = {$this->orca_auth->user->client_id}" : "";
        $this->id = intval($this->input->get_post('id'));
        if (!$this->id) show_404 ();
        
        $this->ticket = $ticket = $this->db->query("SELECT tickets.*, 
                user1.username AS create_user, user2.username AS assigned_user_name,
                projects.title AS project_title, projects.description AS project_description, projects.user_id AS project_user_id
            FROM tickets 
            LEFT JOIN users user1 ON tickets.user_id = user1.id
            LEFT JOIN users user2 ON tickets.assigned_user = user2.id 
            LEFT JOIN projects ON tickets.project_id = projects.id 
            WHERE tickets.id = ? $strClient",array($this->id))->row();
        if (!$ticket) show_404 ();
        
        do_action( 'before_ticket_done',array($this) );
        
        $sql = "UPDATE tickets SET complete_date = NOW(), status = 'close' WHERE id = ?";
        $this->db->query($sql, array( $this->id ));
        
        do_action( 'after_ticket_done',array($this) );
        do_action( 'after_save', array( $this->orca_auth->user, 'tickets', $ticket->id, "mark {$ticket->title} as done" ) );
        
        echo json_encode(array('success' => true, 'ticket'=>$ticket));
    }
    
    public function users() {
        $strClient = $this->orca_auth->user->client_id ? " AND client_id = {$this->orca_auth->user->client_id}" : "";
        $this->project_id = intval($this->input->get_post('project_id'));
        
        $project = $this->db->query("SELECT * FROM projects WHERE id = ? $strClient", array($this->project_id))->row();
        if (!$project) show_404();
        
        $users=array();
        
        $page = intval($this->input->get_post('page'));
        $page--;
        $page = abs($page);
        $q = trim($this->input->get_post('q'));
        if ($q)
        {
            $q = mysql_real_escape_string($q);
            $limit = intval($this->input->get_post('page_limit'));
            if (!$limit) $limit=10;
            $start = $page * $limit;

            $sql = "SELECT username FROM users WHERE username LIKE '$q%' $strClient LIMIT $limit";
            $users = $this->db->query($sql)->result();
        }
        
        echo json_encode( array('users' => $users) );
    }
    
    public function detail() {
        $this->id = intval($this->input->get_post('id'));
        if (!$this->id) show_404 ();
        
        $strClient = $this->orca_auth->user->client_id ? " AND tickets.client_id = {$this->orca_auth->user->client_id}" : "";
        $ticket = $this->db->query("SELECT tickets.*, 
                user1.username AS create_user, user2.username AS assigned_user_name,
                projects.title AS project_title, projects.description AS project_description
            FROM tickets 
            LEFT JOIN users user1 ON tickets.user_id = user1.id
            LEFT JOIN users user2 ON tickets.assigned_user = user2.id 
            LEFT JOIN projects ON tickets.project_id = projects.id 
            WHERE tickets.id = ? $strClient",array($this->id))->row();
        if (!$ticket) show_404 ();

        $sql = "SELECT * FROM files WHERE ticket_id = {$ticket->id}";
        $ticket->attachments = $this->db->query($sql)->result();
        
        $strClient = $this->orca_auth->user->client_id ? " AND comments.client_id = {$this->orca_auth->user->client_id}" : "";
        $comments = $this->db->query("SELECT comments.*, users.username AS comment_user FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE ticket_id = ? $strClient", $ticket->id)->result();
        
        $ids=array();
        foreach($comments as $c) {
            $ids[]=$c->id;
        }
        $files=array();
        $query = $this->db->query("SELECT * FROM files WHERE comment_id IN ('".implode("','", $ids)."')");
        foreach($query->result() as $file) {
            $files[ $file->comment_id ][] = $file;
        }
        foreach($comments as $i=>$c) {
            $comments[$i]->attachments = isset($files[$c->id])?$files[$c->id]:array();
        }
        
        $this->load->view('tickets_detail',array( 'ticket' => $ticket, 'comments' => $comments ));
    }
    
    public function edit() {
        $this->id = intval($this->input->get_post('id'));
        $this->project_id = intval($this->input->get_post('project_id'));
        
        $ticket = false;
        $project = false;
        
        if ($this->id)
        {
            $strClient = $this->orca_auth->user->client_id ? " AND tickets.client_id = {$this->orca_auth->user->client_id}" : "";
            $strProjects = $this->project_id ? " AND tickets.project_id = {$this->project_id}" : "";
            $this->ticket = $ticket = $this->db->query("SELECT tickets.*, user1.username AS create_user, user2.username AS assigned_user_name 
                FROM tickets LEFT JOIN users user1 ON tickets.user_id = user1.id
                LEFT JOIN users user2 ON tickets.assigned_user = user2.id 
                WHERE tickets.id = ? $strClient $strProjects",array($this->id))->row();
            if (!$ticket) show_404 ();
            
            $sql = "SELECT * FROM files WHERE ticket_id = {$ticket->id}";
            $ticket->attachments = $this->db->query($sql)->result();
            if (!$this->project_id) $this->project_id  = $ticket->project_id;
        }
        
        if ($this->project_id)
        {
            $strClient = $this->orca_auth->user->client_id ? " AND projects.client_id = {$this->orca_auth->user->client_id}" : "";
            $this->project = $project = $this->db->query("SELECT * FROM projects WHERE id = ? $strClient", array($this->project_id))->row();
            if (!$project) show_404();
        }
        
        if (is_post_request() )
        {
            do_action( 'before_ticket_edit', array( $this ) );
                
            $fields = array('title', 'description', 'severity', 'type', 'attachment', 'status', 'assign_user');
            foreach($fields as $field)
                $$field = isset($_POST[$field])?$_POST[$field]:'';
            do
            {
                if (!$ticket)
                    $ticket = new stdClass ();
                
                $ticket->severity = trim($severity);
                if (!$ticket->severity) $ticket->severity = 'minor';
                
                $ticket->status = trim($status);
                if (!$ticket->status) $ticket->status = 'open';
                
                $ticket->title = trim($title);
                if (!$ticket->title)
                {
                    flashmsg_set("Ticket title is required");
                    break;
                }
                
                $ticket->description = trim($description);
                if (!$ticket->description)
                {
                    flashmsg_set("Ticket description is required");
                    break;
                }
                
                $ticket->type = trim($type);
                if (!$ticket->type)
                {
                    flashmsg_set("Ticket type is required");
                    break;
                }
                
                if (empty($ticket->assigned_user)) $ticket->assigned_user=0;
                $ticket->assigned_user_name = strtolower(trim($assign_user));
                if ($ticket->assigned_user_name)
                {
                    $strClient = $this->orca_auth->user->client_id ? " AND client_id = {$this->orca_auth->user->client_id}" : "";
                    $row = $this->db->query("SELECT * FROM users WHERE username = ? $strClient", array($ticket->assigned_user_name))->row();
                    if (!$row)
                    {
                        flashmsg_set("User not exists, please choose another");
                        break;
                    }
                    $ticket->assigned_user = $row->id;
                }
                
                $ticket->project_id = $project->id;
                $ticket->client_id = $project->client_id;
                
                $strComplete = "";
                if ($ticket->status == 'close' && ( empty( $ticket->complete_date ) || $ticket->complete_date == '0000-00-00 00:00:00' ))
                {
                    $strComplete = "complete_date = NOW()";
                }
                
                if ($this->id)
                {
                    $sql = "UPDATE tickets SET title = ?, description = ?, type = ?, severity = ?, status = ?, assigned_user = ? $strComplete WHERE id = ?";
                    $this->db->query($sql,array( $ticket->title, $ticket->description, $ticket->type, $ticket->severity, $ticket->status, $ticket->assigned_user, $this->id ));
                }
                else
                {
                    $sql = "INSERT INTO tickets (title,description,user_id,project_id,client_id,status,severity,type,assigned_user) 
                            VALUES (?,?,?,?,?,?,?,?,?)";
                    $this->db->query($sql,array( $ticket->title, $ticket->description,
                        $this->orca_auth->user->id,
                        $project->id,
                        $project->client_id,
                        $ticket->status, $ticket->severity, $ticket->type, $ticket->assigned_user ));
                    $this->id = $this->db->insert_id();
                }
                
                $upload_tmp_dir = str_replace('\\', '/', FCPATH) . '/files/plupload';
                $upload_dir = str_replace('\\', '/', FCPATH) . '/files/attachments';
                if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
                $upload_dir .= "/{$project->client_id}";
                if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
                $upload_dir .= "/{$this->orca_auth->user->id}";
                if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
                
                if ($attachment)
                {
                    $this->db->query("DELETE FROM files WHERE ticket_id = {$this->id}");
                    foreach($attachment as $att)
                    {
                        if ( is_file("$upload_tmp_dir/$att") )
                        {
                            rename("$upload_tmp_dir/$att", "$upload_dir/$att");
                            $this->db->query("INSERT INTO files ( ticket_id, client_id, filename, user_id  )
                                            VALUES ( ?, ?, ?, ? )",
                                    array( $this->id, $project->client_id, "{$project->client_id}/{$this->orca_auth->user->id}/$att", $this->orca_auth->user->id ));
                        } 
                        else 
                        {
                            $this->db->query("INSERT INTO files ( ticket_id, client_id, filename, user_id  )
                                            VALUES ( ?, ?, ?, ? )",
                                    array( $this->id, $project->client_id, "$att", $this->orca_auth->user->id ));
                        }
                    }
                }
                
                do_action( 'before_ticket_edit', array( $this ) );
                do_action( 'after_save', array( $this->orca_auth->user, 'tickets', $this->id, "mark {$ticket->title} as done" ) );
                
                flashmsg_set("Ticket updated");
                redirect(site_url('projects/tickets?id='.$project->id));
            }
            while(0);
        }
        
        $this->load->view('tickets_edit', array('ticket' => $ticket, 'project' => $project));
    }
    
    public function all() {
        $aColumns = array('tickets.id', 'tickets.created_on', 'tickets.user_id', 
            'tickets.project_id', 'tickets.client_id', 'tickets.title',
            'tickets.description', 'tickets.status', 'tickets.severity',
            'tickets.type', 'tickets.assigned_user', 'tickets.complete_date',
            'user1.username AS create_user',
            'clients.name AS client_name',
            'projects.title as project_title',
            'user2.username AS assigned_username',
            'projects.user_id AS project_user_id');

        //LIMIT
        $sLimit = "";
        if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
        {
            $start = intval($_REQUEST['iDisplayStart']);
            $length = empty($_REQUEST['iDisplayLength'])?10:$_REQUEST['iDisplayLength'];
            if (!$length) $length = 10;
            $sLimit = "LIMIT $start,$length";
        }

        //ORDER BY
        $sOrder = array();
        if ( isset( $_REQUEST['iSortCol_0'] ) && isset($_REQUEST['iSortingCols']) )
        {
            $length = intval($_REQUEST['iSortingCols']);
            for ( $i=0 ; $i<$length ; $i++ )
            {
                $colindex = isset($_REQUEST['iSortCol_'.$i])?intval($_REQUEST['iSortCol_'.$i]):FALSE;
                $sortdir = empty($_REQUEST['sSortDir_'.$i]) ? 'ASC' : strtoupper($_REQUEST['sSortDir_'.$i]);
                if ( $sortdir != 'ASC' || $sortdir != 'DESC' ) $sortdir = 'ASC';

                if ( $colindex  !== false && !empty($_REQUEST[ "bSortable_$colindex" ]) && $_REQUEST[ "bSortable_$colindex" ] == "true" )
                {
                    $colname = $colindex + 1;
                    $sOrder[] = "$colname $sortdir";
                }
            }
        }
        $sOrder = empty($sOrder) ? "" : "ORDER BY ".implode(",", $sOrder);

        $sWhere1 = array();
        if ( !empty($_REQUEST['sSearch']) )
        {
            $search = mysql_real_escape_string( $_REQUEST['sSearch'] );
            for ( $i=0 ; $i<count($aColumns)-2 ; $i++ )
            {
                $colname = get_column_name($aColumns[$i]);
                $sWhere1[] = "$colname LIKE '%$search%'";
            }
        }

        $sWhere2 = array();
        if ( $this->orca_auth->user->client_id )
            $sWhere2[] = "tickets.client_id = {$this->orca_auth->user->client_id}";
            
        $project_id = intval($this->input->get_post('project_id'));
        if ($project_id)
            $sWhere2[] = "tickets.project_id = $project_id";
        
        $type = trim(strtolower($this->input->get_post('type')));
        if ($type)
            $sWhere2[] = "tickets.type = '".  mysql_real_escape_string($type)."'";
        
        $severity = trim(strtolower($this->input->get_post('severity')));
        if ($severity)
            $sWhere2[] = "tickets.severity = '".  mysql_real_escape_string($severity)."'";

        $status = trim(strtolower($this->input->get_post('status')));
        if ($status)
            $sWhere2[] = "tickets.status = '".  mysql_real_escape_string($status)."'";
        
        $user_id = intval($this->input->get_post('user_id'));
        if ($user_id)
            $sWhere2[] = "(tickets.user_id = '$user_id' OR tickets.assigned_user = '$user_id')";
        
        for ( $i=0 ; $i<count($aColumns)-2 ; $i++ )
        {
            if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && isset($_REQUEST['sSearch_'.$i]) && $_REQUEST['sSearch_'.$i] != '' )
            {
                $colname= get_column_name($aColumns[$i]);
                $sWhere2[] = "$colname LIKE '%".mysql_real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
            }
        }

        $sWhere1 = empty( $sWhere1 ) ? "" : "WHERE (".implode(" OR ", $sWhere1).")";
        $sWhere = $sWhere1 . (empty( $sWhere2 ) ? "" : ( empty($sWhere1) ? "WHERE " : " AND " ) . implode( " AND ", $sWhere2 ));
        

        $sql = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
                FROM tickets
                LEFT JOIN projects ON tickets.project_id = projects.id
                LEFT JOIN users user1  ON tickets.user_id = user1.id
                LEFT JOIN users user2  ON tickets.assigned_user = user2.id
                LEFT JOIN clients ON tickets.client_id = clients.client_id
                $sWhere
                $sOrder
                $sLimit";
        //header("Debug-SQL: $sql");
        
        $query1 = $this->db->query($sql);
        $query2 = $this->db->query("SELECT FOUND_ROWS() AS cnt");
        $totalrow = $query2->row()->cnt;

        $sEcho = empty($_GET['sEcho'])?0:intval($_GET['sEcho']);
        $output = array(
                "sEcho" => $sEcho,
                "iTotalRecords" => $totalrow,
                "iTotalDisplayRecords" => 0,
                "aaData" => $query1->result()
        );

        $output["iTotalDisplayRecords"] = count($output['aaData']);
        echo json_encode( $output );
    }
    
}

