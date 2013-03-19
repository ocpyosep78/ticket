    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.autogrow-textarea.js'); ?>"></script>
    <script type="text/javascript">
        var has_perms = function(uri) {
            return typeof window._PERMS[ uri ] != 'undefined' ? true : false;
        },
        parseDate = function(date, format) {
            var parts = date.split(/[^\d]/g),
                format = format.split(/\W+/),
                date = new Date(1970, 0, 1, 0, 0, 0),
                val;
            for (var i=0, cnt = format.length; i < cnt; i++) {
                val = parseInt(parts[i], 10)||1;
                switch(format[i]) {
                    case 'dd':
                    case 'd':
                        date.setDate(val);
                        break;
                    case 'mm':
                    case 'm':
                        date.setMonth(val - 1);
                        break;
                    case 'yy':
                        date.setFullYear(2000 + val);
                        break;
                    case 'yyyy':
                        date.setFullYear(val);
                        break;
                    case 'hh':
                    case 'h':
                        date.setHours(val);
                        break;
                    case 'ii':
                    case 'i':
                        date.setMinutes(val);
                        break;
                    case 'ss':
                    case 's':
                        date.setSeconds(val);
                        break;
                }
            }
            return date;
        },
        timeSince = function($older_date, $newer_date) {
            if (typeof $newer_date == 'undefined')
                $newer_date = new Date().getTime();
            
            var $minus = false;
            if ($newer_date < $older_date) {
                $minus = true;
                var tmp = $newer_date;
                $newer_date = $older_date;
                $older_date = tmp;
            }
            
            var $since = ($newer_date/1000) - ($older_date/1000);
            
            var $chunks = [
                [60 * 60 * 24 * 365 , 'tahun'],
                [60 * 60 * 24 * 30 , 'bulan'],
                [60 * 60 * 24 * 7, 'minggu'],
                [60 * 60 * 24 , 'hari'],
                [60 * 60 , 'jam'],
                [60 , 'menit']
            ];
            
            var $i, $j, $count, $count2, $seconds, $name, $seconds2, $name2;
            for ($i = 0, $j = $chunks.length; $i < $j; $i++) {
                $seconds = $chunks[$i][0];
                $name = $chunks[$i][1];
                $count = Math.floor($since / $seconds);
                if ($count != 0) {
                    break;
                }
            }
            var $output = $count + ' ' + $name;
            if ($i + 1 < $j) {
                $seconds2 = $chunks[$i + 1][0];
                $name2 = $chunks[$i + 1][1];
                $count2 = Math.floor(($since - ($seconds * $count)) / $seconds2);
                if ($count2 != 0) {
                    $output += ", " + $count2 + " " + $name2;
                }
            }

            return ($minus ? '-' : '') + $output;
        };
        
        $(function() {
            $('.autogrow').autogrow();
            window._PERMS = {}, window.USER_ID = 0;
            <?php if ( $this->orca_auth->get_current_user() ): ?>
                    <?php 
                    $this->Users->my_perms( $this->orca_auth->user->id );
                    $perms = array();
                    foreach($this->Users->perm_tables[ $this->orca_auth->user->id ] as $perm) {
                        $perms[ $perm->perm_path ] = $perm;
                    }
                    ?>
                    window._PERMS = ( <?php echo json_encode( $perms ); ?> );
                    window.USER_ID = ( <?php echo json_encode( $this->orca_auth->user->id ); ?> );
            <?php endif; ?>
        });
    </script>
    <?php do_action('page_foot'); ?>
  </body>
</html>
