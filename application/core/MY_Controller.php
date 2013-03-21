<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        add_action('after_login', array($this, '_after_login'));
        add_action('after_logout', array($this, '_after_logout'));
        add_action('after_save', array($this, '_after_save'));
        
        add_action( 'after_user_save', array( $this, '_after_user_save' ) );
        
        add_action( 'before_project_save', array( $this, '_before_project_save' ) );
        
        add_action( 'before_task_edit', array( $this, '_before_task_edit' ) );
        add_action( 'after_task_edit', array( $this, '_after_task_edit' ) );
        
        add_action( 'before_task_done', array( $this, '_before_task_done' ) );
        add_action( 'after_task_done', array( $this, '_after_task_done' ) );
        
        add_action( 'before_ticket_done', array( $this, '_before_ticket_done' ) );
        add_action( 'after_ticket_done', array( $this, '_after_ticket_done' ) );
        
        add_action( 'before_ticket_edit', array( $this, '_before_ticket_edit' ) );
        add_action( 'after_ticket_edit', array( $this, '_after_ticket_edit' ) );
        
        add_action( 'after_comment_add', array( $this, '_after_comment_add' ) );
        
        $this->orca_auth->login_required();
    }
    
    public function _after_comment_add($controller)
    {
        $users = array();
        $title = '';
        $url = '';
        if ( isset($controller->ticket) && $controller->ticket )
        {
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?", array( $controller->ticket->project_id ))->row();
            $users[] = $project->user_id;
            $users[] = $controller->ticket->user_id;
            if ($controller->ticket->assigned_user)
                $users[] = $controller->ticket->assigned_user;
            $title = "Ticket {$controller->ticket->title}";
            $url = site_url("tickets/detail?id={$controller->ticket->id}");
        }
        
        if ( isset($controller->task) && $controller->task )
        {
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?", array( $controller->task->project_id ))->row();
            $users[] = $project->user_id;
            if ($controller->task->user_id)
                $users[] = $controller->task->user_id;
            $title = "Task {$controller->task->task}";
            $url = site_url("tasks/detail?id={$controller->task->id}");
        }
        
        if ($users)
        {
            $comment_text = empty($_POST['comment_text']) ? '' : trim($_POST['comment_text']);
            
            $users = $this->db->query("SELECT * FROM users WHERE id IN (".implode($users).")")->result();
            foreach($users as $user)
            {
                @mail( $user->email, "Comment Added on {$title}", "Halo\n".
                        "{$this->orca_auth->user->username} menambahkan komentar di {$title}\n".
                        "$comment_text\n\nCek di $url\n--\nTerima kasih");
            }
        }
    }
    
    public function _before_ticket_edit($controller)
    {
        $project = isset($controller->project)?$controller->project:false;
        $ticket = isset($controller->ticket)?$controller->ticket:false;
        if ($project) {
            //project owner always true
            if ( $this->orca_auth->user->id == $project->user_id )
                return true;
            
            if (!$ticket) {
                //any user with add privileges can add
                if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tickets/add' ) )
                    return true;
            } else {
                // any user with edit privilege can edit
                if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tickets/edit' ) )
                    return true;
            }
            
            show_error("Permission required", 403);
            exit;
        }
    }
    
    public function _after_ticket_edit($controller)
    {
        $ticket = $this->db->query("SELECT * FROM tickets WHERE id = ?", array( $controller->id ))->row();
        if ($ticket) {
            $project = $this->db->query("SELECT * FROM projects WHERE id = ?", array( $ticket->project_id ))->row();
            $subject = $ticket->status == 'close' ? "Ticket Ditutup" : "Ticket Anda";
            $body = "Halo\n\n".
                        "Ticket diupdate:\n\n".
                        "{$ticket->title}\n$ticket->description\n".
                        "Project: {$project->title}\n".
                        "Detail kunjungi\n".site_url("tickets/detail?id=$controller->id")."\n\n".
                        "--\nTerima Kasih";
            if ($ticket->assigned_user)
            {
                $user = $this->db->query("SELECT * FROM users WHERE id = ?",array($ticket->assigned_user))->row();
                @mail( "$user->email", $subject, $body);
            }
            
            if ($project->user_id)
            {
                $user = $this->db->query("SELECT * FROM users WHERE id = ?",array($project->user_id))->row();
                @mail( "$user->email", $subject, $body);
            }
        }
    }
    
    public function _before_ticket_done($controller)
    {
        $ticket = $controller->ticket;
        if ($this->orca_auth->user->id != $ticket->project_user_id && $this->orca_auth->user->id != $ticket->user_id && (!$this->Users->has_perms( $this->orca_auth->user->id, 'tickets/done' )))
        {
            show_error("Permission required", 403);
            exit;
        }
    }
    
    public function _after_ticket_done($controller)
    {
        $ticket = $controller->ticket;
        if ($ticket->assigned_user)
        {
            $user = $this->db->query("SELECT * FROM users WHERE id = ?",array($ticket->assigned_user))->row();
            @mail( "$user->email", "Ticket Ditutup", "Halo\n\n".
                    "Ticket ditutup:\n\n".
                    "{$ticket->title}\n$ticket->description\n".
                    "Project: {$ticket->project_title}\n".
                    "Detail kunjungi\n".site_url("tickets/detail?id=$this->id")."\n\n".
                    "--\nTerima Kasih");
        }
    }
    
    public function _before_task_done($controller)
    {
        $task = $this->db->query("SELECT tasks.*, projects.user_id as project_user_id, users.username as assigned_user 
            FROM tasks LEFT JOIN users ON tasks.user_id = users.id 
            LEFT JOIN projects ON tasks.project_id = projects.id
            WHERE tasks.id = ?", array( $this->id ))->row();
        if ($task && $this->orca_auth->user->id != $task->project_user_id && (!$this->Users->has_perms( $this->orca_auth->user->id, 'tasks/done' )))
        {
            show_error("Permission required", 403);
            exit;
        }
    }
    
    public function _after_task_done($controller)
    {
        $task = $controller->task;
        if ($task->user_id)
        {
            $user = $this->db->query("SELECT * FROM users WHERE id = ?",array($task->user_id))->row();
            @mail( "$user->email", "Task Selesai", "Halo\n\n".
                    "Task anda telah selesai:\n\n".
                    "Task: {$task->task}\n".
                    "Deadline: ".time_since2( date("Y-m-d"), $task->due )."\n".
                    "Detail task silahkan kunjungi\n".site_url("tasks/detail?id=$k")."\n\n".
                    "--\nTerima Kasih");
        }
    }
    
    public function _before_task_edit($controller)
    {
        $project = $this->db->query("SELECT * FROM projects WHERE id = ?", array($controller->id))->row();
        if ($this->orca_auth->user->id != $project->user_id && (!$this->Users->has_perms( $this->orca_auth->user->id, 'tasks/add' ) || !$this->Users->has_perms( $this->orca_auth->user->id, 'tasks/edit' )) )
        {
            show_error("Permission required", 403);
            exit;
        }
    }
    
    public function _after_task_edit($controller)
    {
		$strClient = (isset($strClient)) ? $strClient : '';
        $project = $this->db->query("SELECT * FROM projects WHERE id = ? $strClient", array($controller->id))->row();
        
        $fields = array( 'task', 'description', 'user_id', 'due', 'timeline_id', 'parent_id' );
        foreach($fields as $field)
            $$field = !empty($_POST[$field]) ? $_POST[$field] : array();
            
        foreach($task as $k => $tasks)
        {
            foreach ($tasks as $i => $t)
            {
                $t = trim($t);
                if (!$t) continue;

                $descr = empty($description[$k][$i]) ? '' : $description[$k][$i];
                $user_id = @empty($user_id[$k][$i]) ? 0 : $user_id[$k][$i];
                $due = @empty($due[$k][$i]) ? date('d/m/Y', strtotime('+1 day')) : $due[$k][$i];
                $parent_id = @empty($parent_id[$k][$i]) ? 0 : $parent_id[$k][$i];
                $task_id = empty($task_id[$k][$i]) ? 0 : $task_id[$k][$i];
                $tl_id = empty($timeline_id[$k][$i]) ? 0 : $timeline_id[$k][$i];
				
                if ($user_id) {
                    $user = $this->db->query("SELECT * FROM users WHERE id = ?",array($user_id))->row();
                    if ($k == 'new') {
                        $k = $this->db->insert_id();
                        $subject = "Task Baru Anda";
                    } else {
                        $subject = "Update Task Anda";
                    }
                    
                    @mail( "$user->email", $subject, "Halo\n\n".
                            "Anda mendapat task baru:\n\n".
                            "Task: {$t}\n$descr\n".
                            "Project: {$project->title}\n".
                            "Deadline: ".time_since2( date("Y-m-d"), $due )."\n".
                            "Detail task silahkan kunjungi\n".site_url("tasks/detail?id=$k")."\n\n".
                            "--\nTerima Kasih");
                }
            }
        }
    }
    
    public function _after_user_save( $controller )
    {
        $userid = isset($_POST['id']) ? intval($_POST['id']) : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $user = $this->db->query("SELECT users.*, clients.name AS client_name FROM users LEFT JOIN clients ON users.client_id = clients.client_id WHERE users.username = ?", array( $username ))->row();
        if ($user)
        {
            if (!$user->client_name) $user->client_name = 'SIMETRI';
            
            if ( !$userid )
            {
                @mail( "$user->email", "Selamat datang di {$user->client_name} Tickets", 
                        "Halo\n\n".
                        "Anda ditambahkan sebagai pengguna di {$user->client_name} Tickets\n".
                        "Akun anda:\n".
                        "Username: {$user->username}\n".
                        "Password: $password\n\n".
                        "Untuk login, klik ".site_url('auth/login')."\n\n".
                        "--\nTerima Kasih");
            }
            else if ($password)
            {
                @mail( "$user->email", "Password Diganti", 
                        "Halo\n\n".
                        "Password anda di {$user->client_name} Tickets telah diganti\n".
                        "Akun anda:\n".
                        "Username: {$user->username}\n".
                        "Password: $password\n\n".
                        "Untuk login, klik ".site_url('auth/login')."\n\n".
                        "--\nTerima Kasih");
            }
        }
    }
    
    public function _before_project_save( $controller )
    {
        $project_id = $controller->id;
        if ( $project_id )
        {
            $the_project = $this->db->query("SELECT * FROM projects WHERE id = ?", array($project_id))->row();
            if ($this->orca_auth->user->id != $the_project->user_id && (!$this->Users->has_perms( $this->orca_auth->user->id, 'projects/edit' )))
            {
                show_error("Permission required", 403);
                exit;
            }
        }
        else
        {
            if ($this->Users->has_perms( 'projects/add' ))
            {
                show_error("Permission required", 403);
                exit;
            }
        }
    }
    
    public function _after_login($user) {
        $this->db->query("INSERT INTO change_history (user_id, object_type, object_id, client_id, message) VALUES (?,?,?,?,?)", 
                array( $user->id, 'auth', $user->id, $user->client_id, 
                    sprintf('User %s is logged in from %s', $user->username, $this->orca_auth->get_ip()) ));
    }
    
    public function _after_logout($user) {
        $this->db->query("INSERT INTO change_history (user_id, object_type, object_id, client_id, message) VALUES (?,?,?,?,?)", 
                array( $user->id, 'auth', $user->id, $user->client_id, 
                    sprintf('User %s is logged out from %s', $user->username, $this->orca_auth->get_ip()) ));
    }
    
    public function _after_save($data) {
        $user = array_shift($data);
        $type = array_shift($data);
        $id = array_shift($data);
        $description = array_shift($data);
        $this->db->query("INSERT INTO change_history (user_id, object_type, object_id, client_id, message) VALUES (?,?,?,?,?)", 
                array( $user->id, $type, $id, $user->client_id, $description ));
    }
}