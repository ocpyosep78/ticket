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
 * Description of tasks
 *
 * @author ferdhie
 */
class Tasks extends MY_Controller {
    
    public function index() {
        $this->load->view('my_tasks');
    }
    
    public function all() {
        $aColumns = array(
            'tasks.id', 
            'projects.title as project_title',
            'timelines.title as timeline_title',
            'tasks.task',
            'tasks.description', 
            'tasks.status', 
            'tasks.due', 
            'tasks.complete_date',
            'user1.username AS assigned_username',
            'clients.name AS client_name',
            'tasks.user_id', 
            'tasks.timeline_id', 
            'tasks.project_id', 
            'projects.user_id AS project_user_id', 
            );

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
            $sWhere2[] = "tasks.client_id = {$this->orca_auth->user->client_id}";
            
        $status = intval($this->input->get_post('status'));
        if (isset($_REQUEST['status']) && $_REQUEST['status'] !== '')
            $sWhere2[] = "tasks.status = '$status'";
        
        $user_id = intval($this->input->get_post('user_id'));
        if ($user_id)
            $sWhere2[] = "(tasks.user_id = '$user_id' OR projects.user_id = '$user_id')";
        
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
                FROM tasks
                LEFT JOIN projects ON tasks.project_id = projects.id
                LEFT JOIN users user1  ON tasks.user_id = user1.id
                LEFT JOIN clients ON tasks.client_id = clients.client_id
                LEFT JOIN timelines ON tasks.timeline_id = timelines.id
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
    
    protected function get_task_children($task) {
        $strClient = $this->orca_auth->user->client_id ? " AND tasks.client_id = {$this->orca_auth->user->client_id}" : "";
        $rows = $this->db->query("SELECT tasks.*, 
                timelines.title as timeline, 
                projects.title AS project_title, 
                projects.description as project_description, 
                users.username as assigned_user 
            FROM tasks LEFT JOIN users ON tasks.user_id = users.id 
            LEFT JOIN timelines ON tasks.timeline_id = timelines.id
            LEFT JOIN projects ON tasks.project_id = projects.id
            WHERE tasks.parent_id = ? $strClient", array( $task->id ))->result();
        foreach($rows as $row) {
            $row = $this->get_task_children($row);
            $task->tasks[$task->timeline_id][$row->id]=$row;
        }
        return $task;
    }
    
    public function detail() {
        $this->id = intval($this->input->get_post('id'));
        if (!$this->id) show_404();
        
        $strClient = $this->orca_auth->user->client_id ? " AND tasks.client_id = {$this->orca_auth->user->client_id}" : "";
        $task = $this->db->query("SELECT tasks.*, 
                    timelines.title as timeline, 
                    projects.title AS project_title, 
                    projects.description as project_description, 
                    users.username as assigned_user 
                FROM tasks LEFT JOIN users ON tasks.user_id = users.id 
                LEFT JOIN timelines ON tasks.timeline_id = timelines.id
                LEFT JOIN projects ON tasks.project_id = projects.id
                WHERE tasks.id = ? $strClient", array( $this->id ))->row();
        if (!$task) show_404();
        $task = $this->get_task_children($task);
        
        $strClient = $this->orca_auth->user->client_id ? " AND comments.client_id = {$this->orca_auth->user->client_id}" : "";
        $comments = $this->db->query("SELECT comments.*, users.username AS comment_user FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE task_id = ? $strClient", $task->id)->result();
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
        
        $this->load->view('task_detail',array( 'task' => $task, 'comments' => $comments ));
    }

    public function ajaxdetail() {
        $id = intval($this->input->get_post('id'));
        if ($id) {
            $strClient = $this->orca_auth->user->client_id ? " AND tasks.client_id = {$this->orca_auth->user->client_id}" : "";
            $task = $this->db->query("SELECT tasks.*, users.username as assigned_user FROM tasks LEFT JOIN users ON tasks.user_id = users.id WHERE tasks.id = ? $strClient", array( $id ))->row();
            if ($task) {
                echo json_encode(array('success' => true, 'task' => $task));
                return;
            }
        }
        show_404();
    }
    
    public function ajaxdone() {
        $this->id = $id = intval($this->input->get_post('id'));
        if ($id) {
            do_action('before_task_done', array( $this ));
            
            $strClient = $this->orca_auth->user->client_id ? " AND tasks.client_id = {$this->orca_auth->user->client_id}" : "";
            $this->db->query("UPDATE tasks SET status = 1, complete_date = NOW() WHERE id = ?",array($id));
            $this->task = $task = $this->db->query("SELECT tasks.*, users.username as assigned_user FROM tasks LEFT JOIN users ON tasks.user_id = users.id WHERE tasks.id = ? $strClient", array( $id ))->row();
            if ($task && $task->user_id) {
                
                do_action('after_task_done', array( $this ));
                do_action( 'after_save', array( $this->orca_auth->user, 'tasks', $task->id, "mark {$task->task} as done" ) );
                
                echo json_encode(array('success' => true, 'task' => $task));
                return;
            }
        }
        show_404();
    }
    
	public function ajaxdelete() {
		$result = $this->Task_model->Delete(array('id' => $_POST['id']));
		echo json_encode($result);
	}
}

