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
 * Control project creation and project management, including adding user into project.
 *
 * @author ferdhie
 */
class Projects extends MY_Controller {
    
    public function index() {
        $this->load->view('projects');
    }
    
    protected function update_tasks($tasks, $parent_id=0) {
        $result = array();
        foreach($tasks as $t) {
            if ($t->parent_id == $parent_id) {
                $t->tasks = $this->update_tasks( $tasks, $t->id );
                $result[$t->timeline_id][$t->id] = $t;
            }
        }
        return $result;
    }
    
    public function tasks() {
        $this->id = intval($this->input->get_post('id'));
        if (!$this->id) show_404();
        
        //batasi akses data untuk client jika user adalah user client
        $strClient = $this->orca_auth->user->client_id ? " AND client_id = {$this->orca_auth->user->client_id}" : "";
        $project = $this->db->query("SELECT * FROM projects WHERE id = ? $strClient", array($this->id))->row();
        if (!$project) show_404();
        
        $project->attachments = $this->db->query("SELECT * FROM files WHERE project_id = ?", array($this->id))->result();
        
        if (is_post_request())
        {
            do_action( 'before_task_edit', array($this) );
            
            $success = false;
            $fields = array( 'task', 'description', 'user_id', 'due', 'timeline_id', 'parent_id' );
            foreach($fields as $field)
                $$field = !empty($_POST[$field]) ? $_POST[$field] : array();
            
            $timeline = empty($_POST['timeline'])?array():$_POST['timeline'];
            $tzoffset = empty($_POST['tzoffset'])?0:intval($_POST['tzoffset'])*60;
            
            if ($timeline)
            {
                foreach($timeline as $id => $tl)
                {
                    $tl = trim($tl);
                    if (!$tl) continue;
                    
                    if ($id == 'new')
                    {
                        $this->db->query("INSERT INTO timelines (client_id, project_id, title) VALUES (?,?,?)",array( $project->client_id, $project->id, $tl ));
                        $id = $this->db->insert_id();
                    } 
                    else
                    {
                        $this->db->query("UPDATE timelines SET title = ? WHERE id = ?",array( $tl, $id ));
                    }
                    
                    $success = true;
                    do_action( 'after_save', array( $this->orca_auth->user, 'timelines', $id, "project timeline $tl" ) );
                }
            }
            
            if ($task)
            {
                //new task
                foreach($task as $k => $tasks)
                {
                    foreach ($tasks as $i => $t)
                    {
                        $t = trim($t);
                        if (!$t) continue;
                        
                        $descr = empty($description[$k][$i]) ? '' : $description[$k][$i];
                        $user_id = empty($user_id[$k][$i]) ? 0 : $user_id[$k][$i];
                        $due = empty($due[$k][$i]) ? date('d/m/Y', strtotime('+1 day')) : $due[$k][$i];
                        $parent_id = empty($parent_id[$k][$i]) ? 0 : $parent_id[$k][$i];
                        $task_id = empty($task_id[$k][$i]) ? 0 : $task_id[$k][$i];
                        $tl_id = empty($timeline_id[$k][$i]) ? 0 : $timeline_id[$k][$i];
                        
                        $array = array_map('intval',explode('/', $due));
                        $due = date('Y-m-d', strtotime("$array[2]-$array[1]-$array[0]") + $tzoffset);
                        $client_id =  $project->client_id;
                        
                        if ($k == 'new') {
                            $this->db->query("INSERT INTO tasks (task,description,user_id,due,client_id,parent_id,project_id,timeline_id) 
                                VALUES (?,?,?,?,?,?,?,?)",
                                    array( $t, $descr, $user_id, $due, $client_id, $parent_id, $project->id, $tl_id ));
                            $k = $this->db->insert_id();
                        } else {
                            $this->db->query("UPDATE tasks SET task = ?, description = ?, user_id = ?, due = ? WHERE id = ?",
                                    array( $t, $descr, $user_id, $due, $k ));
                        }
                        
                        do_action( 'after_save', array( $this->orca_auth->user, 'tasks', $k, "edit task $task" ) );
                        $success = true;
                    }
                }
                
                do_action( 'after_task_edit', array( $this ) );
            }
            
            if ($success)
            {
                flashmsg_set("Task data has been updated");
            }
            
            redirect(site_url('projects/tasks?id='.$project->id));
        }
        
        
        $tl = $this->db->query("SELECT * FROM timelines WHERE project_id = ? $strClient", array( $project->id ))->result();
        $timelines=array();
        foreach($tl as $timeline) {
            $timelines[$timeline->id]=$timeline;
        }
        
        $strClient = $this->orca_auth->user->client_id ? " AND tasks.client_id = {$this->orca_auth->user->client_id}" : "";
        $tsks = $this->db->query("SELECT tasks.*, users.username as assigned_user FROM tasks LEFT JOIN users ON tasks.user_id = users.id WHERE tasks.project_id = ? $strClient ORDER BY parent_id, id", array( $project->id ))->result();
        $tasks = $this->update_tasks($tsks);
        
        $this->load->view('projects_tasks', array('project' => $project, 'timelines' => $timelines, 'tasks' => $tasks));
    }
    
    public function handle_attachments($client_id, $var_name, $id, $attachment) {
        $upload_tmp_dir = str_replace('\\', '/', FCPATH) . '/files/plupload';
        $upload_dir = str_replace('\\', '/', FCPATH) . '/files/attachments';
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        $upload_dir .= "/{$client_id}";
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        $upload_dir .= "/{$this->orca_auth->user->id}";
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        if ($attachment)
        {
            $this->db->query("DELETE FROM files WHERE $var_name = {$id}");
            foreach($attachment as $att)
            {
                if ( is_file("$upload_tmp_dir/$att") )
                {
                    rename("$upload_tmp_dir/$att", "$upload_dir/$att");
                    $this->db->query("INSERT INTO files ( $var_name, client_id, filename, user_id  )
                                    VALUES ( ?, ?, ?, ? )",
                            array( $id, $client_id, "{$client_id}/{$this->orca_auth->user->id}/$att", $this->orca_auth->user->id ));
                } 
                else 
                {
                    $this->db->query("INSERT INTO files ( $var_name, client_id, filename, user_id  )
                                    VALUES ( ?, ?, ?, ? )",
                            array( $id, $client_id, "$att", $this->orca_auth->user->id ));
                }
            }
        }
    }
    
    public function edit() {
        $this->id = intval($this->input->get_post('id'));
        $project=false;
        
        if (is_post_request() )
        {
            $fields = array('title', 'description', 'client_id', 'timeline', 'user_id');
            foreach($fields as $field)
                $$field = isset($_POST[$field]) ? $_POST[$field] : '';
            $project = new stdClass;
            do
            {
                if (!$client_id)
                    $client_id = $this->orca_auth->user->client_id;
                
                $project->title = $title;
                $project->description = $description;
                $project->client_id = $client_id;
				
                $attachment = isset($_POST['attachment']) ? $_POST['attachment'] : array();
                $project->attachments = array();
                foreach($attachment as $att)
                {
                    $a = new stdClass();
                    $a->filename = basename($att);
                    $project->attachments[] = $a;
                }
                
                if (!$title)
                {
                    flashmsg_set("Judul project kosong, mohon isikan");
                    break;
                }
                
                do_action( 'before_project_save', array( $this ) );
                
                if ($this->id)
                {
                    $this->db->where('id', $this->id)->update('projects', array('title' => $title, 'description' => $description, 'client_id' => $client_id, 'user_id'=>$user_id));
                }
                else
                {
                    $this->db->insert('projects', array('title' => $title, 'description' => $description, 'client_id' => $client_id, 'user_id'=>$user_id));
                    $this->id = $this->db->insert_id();
                }
                $project->id = $this->id;
                
                if (!$timeline)
                    $timeline['new'] = 'Default timeline';
                
                foreach($timeline as $id => $tl)
                {
                    $tl = trim($tl);
                    if (!$tl) continue;
                    
                    if ($id == 'new')
                    {
                        $this->db->query("INSERT INTO timelines (client_id, project_id, title) VALUES (?,?,?)",array( $project->client_id, $project->id, $tl ));
                    } 
                    else
                    {
                        $this->db->query("UPDATE timelines SET title = ? WHERE id = ?",array( $tl, $id ));
                    }
                }
                
                $this->handle_attachments( $project->client_id, 'project_id', $this->id, $attachment );
               
                do_action( 'after_project_save', array( $this ) );
                do_action( 'after_save', array( $this->orca_auth->user, 'projects', $this->id, "Edit project {$project->id} {$project->title}" ) );
                
                flashmsg_set("Project $title saved");
                redirect( site_url('projects/edit?id='.$this->id) );
                
            }
            while(0);
        }
        else if ($this->id)
        {
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?", array($this->id))->row();
            $project->attachments = $this->db->query("SELECT * FROM files WHERE project_id = ?", array( $this->id ))->result();
        }
        
        $this->load->view('projects_edit',array('project' => $project));
    }
    
    public function tickets() {
        $this->id = intval($this->input->get_post('id'));
        if (!$this->id) show_404();
        
        $strClient = $this->orca_auth->user->client_id ? " AND client_id = {$this->orca_auth->user->client_id}" : "";
        $project = $this->db->query("SELECT * FROM projects WHERE id = ? $strClient", array($this->id))->row();
        if (!$project) show_404 ();
        
        $this->load->view('projects_tickets',array( 'project' => $project ));
    }
            
    
    public function all() {
        $aColumns = array('projects.id', 'projects.title', 'projects.description', 'users.username AS user_create',
            'clients.name AS client_name', 'projects.created_on');

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
            $sWhere2[] = "projects.client_id = {$this->orca_auth->user->client_id}";

        for ( $i=0 ; $i<count($aColumns)-2 ; $i++ )
        {
            if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && isset($_REQUEST['sSearch_'.$i]) && $_REQUEST['sSearch_'.$i] != '' )
            {
                $colname= get_column_name($aColumns[$i]);
                $sWhere2[] = "$colname LIKE '%".mysql_real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
            }
        }

        $sWhere1 = empty( $sWhere1 ) ? "" : " WHERE (".implode(" OR ", $sWhere1).")";
        $sWhere = $sWhere1 . (empty( $sWhere2 ) ? "" : ( empty($sWhere1) ? "WHERE " : " AND " ) . implode( " AND ", $sWhere2 ));

        $sql = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
                FROM projects
                LEFT JOIN clients ON projects.client_id = clients.client_id
                LEFT JOIN users ON projects.user_id = users.id
                $sWhere
                $sOrder
                $sLimit";
        header("Debug-SQL: $sql");
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
        
        foreach( $output['aaData'] as $i=>$row )
            $output['aaData'][$i]->description = apply_filters('project_description', $output['aaData'][$i]->description);

        $output["iTotalDisplayRecords"] = count($output['aaData']);
        echo json_encode( $output );
    }
    
}

