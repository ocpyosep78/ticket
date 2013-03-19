<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="<?php echo site_url(); ?>"><?php echo $this->config->item('site_name'); ?></a>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <?php
                    function print_head_menu($perms, $parent_id = 0) {
                        $out = array();
                        foreach($perms as $perm) {
                            if ( $perm->parent_id != $parent_id || !$perm->perm_name)
                                continue;
                            $tmp = print_head_menu($perms, $perm->perm_id);
                            if ($tmp) {
                                $out[] = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="'.htmlspecialchars(site_url($perm->perm_path)).'">'.htmlspecialchars($perm->perm_name).'  <b class="caret"></b></a><ul class="dropdown-menu">'.$tmp.'</ul></li>';
                            } else if ($perm->public != 2) {
                                $out[] = '<li><a href="'.htmlspecialchars(site_url($perm->perm_path)).'">'.htmlspecialchars($perm->perm_name).'</a></li>';
                            }
                        }
                        return implode('', $out);
                    }
                    
                    //$query = $this->db->order_by('parent_id,perm_order')->get('perms');
                    $this->Users->my_perms( $this->orca_auth->user->id );
                    //$perms = array();
                    //foreach($query->result_array() as $row)
                    //    $perms[$row['perm_id']] = $row;
                    $perms = $this->Users->perm_tables[$this->orca_auth->user->id];
                    echo print_head_menu($perms); 
                    ?>
                </ul>

                <ul class="nav pull-right">
                    <?php if ( $this->orca_auth->get_current_user() ): ?>
                    <li class="dropdown">
                        <a id="menuAdmin" href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <span id="username"><?php echo $this->orca_auth->user->username; ?></span> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('auth/profile'); ?>" data-toggle="modal"><i class="icon-pencil"></i> Edit Profil</a></li>
                            <li><a href="<?php echo site_url('auth/logout'); ?>"><i class="icon-off"></i> Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class=""><a href="<?php echo site_url('auth/login'); ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>