<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
    public function index()
    {
        $this->load->library('orca_auth');
        if ($this->orca_auth->get_current_user())
        {
            redirect(site_url('dashboard/'));
        }
        
        $this->load->view('welcome_message');
    }
    /*
    public function createuser()
    {
        $username = $this->input->get('username');
        $password = $this->input->get('password');
        echo $password . '|' . $this->orca_auth->make_hash( $password, '', TRUE );
    }
    */
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */