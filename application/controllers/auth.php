<?php

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function profile()
    {
        if (is_post_request())
        {
            $password = $this->input->get_post('password');
            $password2 = $this->input->get_post('password2');
            
            if ($password && $password == $password2)
            {
                $password = $this->orca_auth->make_hash($password, '', true);
                $this->db->query("UPDATE users SET password = ? WHERE id = ?", array( $password, $this->orca_auth->user->id ));
                flashmsg_set("Password telah diganti");
                redirect( site_url('auth/profile?save='.rand()) );
            }
            else flashmsg_set("Maaf, password tidak cocok atau kosong");
        }
        
        $this->load->view('profile');
    }
    
    public function login()
    {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $next = $this->input->get_post('next');
            if ($this->orca_auth->login())
            {
                do_action( 'after_login', $this->orca_auth->user );
                if ($next)
                {
                    redirect( $next );
                }
                else
                {
                    redirect( site_url() );
                }
            }
            else
            {
                flashmsg_set('Username atau password anda kurang tepat. Mohon ulangi kembali');
            }
        }
        
        $this->load->view('login');
    }
    
    public function logout()
    {
        do_action( 'after_logout', $this->orca_auth->user );
        $this->orca_auth->logout();
        redirect( site_url() );
    }
}
