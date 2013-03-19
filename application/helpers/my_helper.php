<?php

function h($h)
{
    if ( is_array($h) )
        return array_map('h', $val);
    return htmlspecialchars($h);
}

function p($x)
{
    $ci =& get_instance();
    return $ci->input->post($x);
}

function g($x)
{
    $ci =& get_instance();
    return $ci->input->get($x);
}

function r($x)
{
    $ci =& get_instance();
    return $ci->input->get_post($x);
}

function is_post_request()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function flashmsg_set($msg)
{
    if (!isset($_SESSION))
        @session_start();
    $_SESSION['_flashmsg'] = $msg;
}

function flashmsg_get()
{
    if (!isset($_SESSION))
        @session_start();
    $msg = isset($_SESSION['_flashmsg']) ? $_SESSION['_flashmsg'] : null;
    if (!is_null($msg)) unset($_SESSION['_flashmsg']);
    return $msg;
}

function auto_code($prefix)
{
    $ci  = & get_instance();
    $ci->db->query("INSERT INTO auto_code (prefix, sequence) VALUES ( ?, 1 ) ON DUPLICATE KEY UPDATE sequence  =  sequence + 1", array($prefix));
    $result  =  $ci->db->query("SELECT sequence FROM auto_code WHERE prefix  =  ?", array($prefix));
    $row  =  $result->row();
    $result  =  strtoupper($prefix) . '-' . str_pad($row->sequence, 5, '0', STR_PAD_LEFT);
    return $result;
}

function unique_id($stub)
{
    $ci  = & get_instance();
    $ci->db->query("REPLACE INTO tickets32 (stub) VALUES (?)", array($stub));
    $result  =  $ci->db->insert_id();
    return $result;
}

function secure_seed_rng($count=8)
{
    $output = '';

    // Try the OpenSSL method first. This is the strongest.
    if(function_exists('openssl_random_pseudo_bytes'))
    {
        $output = openssl_random_pseudo_bytes($count, $strong);
        if($strong !== true)
        {
            $output = '';
        }
    }

    if($output == '')
    {
        // Then try the unix/linux method
        if(@is_readable('/dev/puxurandom') && ($handle = @fopen('/dev/urandom', 'rb')))
        {
            $output = @fread($handle, $count);
            @fclose($handle);
        }
		else if(version_compare(PHP_VERSION, '5.0.0', '>=') && class_exists('COM'))
        {
			// Then try the Microsoft method
            try {
                $util = new COM('CAPICOM.Utilities.1');
                $output = base64_decode($util->GetRandom($count, 0));
            }
            catch(Exception $ex) { }
        }
    }

    // Didn't work? Do we still not have enough bytes? Use our own (less secure) rng generator
    if(strlen($output) < $count)
    {
        $output = '';

        // Close to what PHP basically uses internally to seed, but not quite.
        $unique_state = microtime().getmypid();

        for($i = 0; $i < $count; $i += 16)
        {
            $unique_state = md5(microtime().$unique_state);
            $output .= pack('H*', md5($unique_state));
        }
    }

    // /dev/urandom and openssl will always be twice as long as $count. base64_encode will roughly take up 33% more space but crc32 will put it to 32 characters
    $output = hexdec(substr(dechex(crc32(base64_encode($output))), 0, $count));

    return $output;
}

function my_rand($min=null, $max=null, $force_seed=false)
{
    static $seeded = false;
    static $obfuscator = 0;

    if($seeded == false || $force_seed == true)
    {
        mt_srand(secure_seed_rng());
        $seeded = true;

        $obfuscator = abs((int) secure_seed_rng());

        // Ensure that $obfuscator is <= mt_getrandmax() for 64 bit systems.
        if($obfuscator > mt_getrandmax())
        {
            $obfuscator -= mt_getrandmax();
        }
    }

    if($min !== null && $max !== null)
    {
        $distance = $max - $min;
        if ($distance > 0)
        {
            return $min + (int)((float)($distance + 1) * (float)(mt_rand() ^ $obfuscator) / (mt_getrandmax() + 1));
        }
        else
        {
            return mt_rand($min, $max);
        }
    }
    else
    {
        $val = mt_rand() ^ $obfuscator;
        return $val;
    }
}

function fix_float( $array )
{
    if ( is_array( $array ) )
    {
        foreach( $array as $k => $v )
        {
            $array[$k] = fix_float($v);
        }
        return $array;
    }
    else
    {
        return is_float($array) ? round($array,2) : $array;
    }
}

function html2plain($html)
{
    $html = preg_replace('~/\s*>~', '>', $html);
    $html = preg_replace('~<br[^/>]+/?>~i', "\n", $html);
    $html = preg_replace('~<p[^>]+>~i', "\n\n", $html);
    $html = preg_replace('~<h[1-6][^>]+>~i', "\n", $html);
    $html = preg_replace('~<div[^>]+>~i', "\n", $html);
    $html = preg_replace('~<t[hd][^>]+>~i', " ", $html);
    $html = preg_replace('~<tr[^>]+>~i', "\n\n", $html);
    //$html = preg_replace('~<a[^h]+href\s*=\s*\"([^"]+)\">[^<]+</a>~i', "\1", $html);
    $html = strip_tags($html);
    $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
    return trim($html);
}

function time_since2($older_date, $newer_date = false, $format='')
{
    if ( !is_numeric($older_date) )
        $older_date = strtotime($older_date);

    if ( !is_numeric($newer_date) && $newer_date )
        $newer_date = strtotime($newer_date);

    // $newer_date will equal false if we want to know the time elapsed between a date and the current time
    // $newer_date will have a value if we want to work out time elapsed between two known dates
    $newer_date = ($newer_date == false) ? time() : $newer_date;
    
    $minus = false;
    
    if ($newer_date < $older_date)
    {
        $minus = true;
        list($newer_date, $older_date) = array($older_date, $newer_date);
    }

    // difference in seconds
    $since = $newer_date - $older_date;

    $max = 86400 * 30 * 6;
    if ( $since > $max )
    {
        return date( 'l, d/M/Y H:i', $older_date );
    }

    // array of time period chunks
    global $time_chunks;
    if (!isset($time_chunks))
    {
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'tahun'),
            array(60 * 60 * 24 * 30 , 'bulan'),
            array(60 * 60 * 24 * 7, 'minggu'),
            array(60 * 60 * 24 , 'hari'),
            array(60 * 60 , 'jam'),
            array(60 , 'menit'),
        );
        $time_chunks = $chunks;
    }
    else
    {
        $chunks = $time_chunks;
    }

    // we only want to output two chunks of time here, eg:
    // x years, xx months
    // x days, xx hours
    // so there's only two bits of calculation below:

    // step one: the first chunk
    for ($i = 0, $j = count($chunks); $i < $j; $i++)
    {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0)
        {
            break;
        }
    }

    // set output var
    $output = "$count {$name}";

    // step two: the second chunk
    if ($i + 1 < $j)
    {
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];

        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0)
        {
            // add to output var
            $output .= ", $count2 {$name2}";
        }
    }

    return ($minus ? '-' : '') . "$output";
}

/** WORDPRESS PLUGIN IS KEREN! **/

function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	global $wp_filter, $merged_filters;

	$idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
	$wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
	unset( $merged_filters[ $tag ] );
	return true;
}

function remove_filter($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	$function_to_remove = _wp_filter_build_unique_id($tag, $function_to_remove, $priority);

	$r = isset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);

	if ( true === $r) {
		unset($GLOBALS['wp_filter'][$tag][$priority][$function_to_remove]);
		if ( empty($GLOBALS['wp_filter'][$tag][$priority]) )
			unset($GLOBALS['wp_filter'][$tag][$priority]);
		unset($GLOBALS['merged_filters'][$tag]);
	}

	return $r;
}

function remove_all_filters($tag, $priority = false) {
	global $wp_filter, $merged_filters;

	if( isset($wp_filter[$tag]) ) {
		if( false !== $priority && isset($wp_filter[$tag][$priority]) )
			unset($wp_filter[$tag][$priority]);
		else
			unset($wp_filter[$tag]);
	}

	if( isset($merged_filters[$tag]) )
		unset($merged_filters[$tag]);

	return true;
}

function current_filter() {
	global $wp_current_filter;
	return end( $wp_current_filter );
}

function has_filter($tag, $function_to_check = false) {
	global $wp_filter;

	$has = !empty($wp_filter[$tag]);
	if ( false === $function_to_check || false == $has )
		return $has;

	if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
		return false;

	foreach ( (array) array_keys($wp_filter[$tag]) as $priority ) {
		if ( isset($wp_filter[$tag][$priority][$idx]) )
			return $priority;
	}

	return false;
}

function apply_filters($tag, $value) {
	global $wp_filter, $merged_filters, $wp_current_filter;

	$args = array();

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$wp_current_filter[] = $tag;
		$args = func_get_args();
		_wp_call_all_hook($args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		if ( isset($wp_filter['all']) )
			array_pop($wp_current_filter);
		return $value;
	}

	if ( !isset($wp_filter['all']) )
		$wp_current_filter[] = $tag;

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	if ( empty($args) )
		$args = func_get_args();

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) ){
				$args[1] = $value;
				$value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			}

	} while ( next($wp_filter[$tag]) !== false );

	array_pop( $wp_current_filter );

	return $value;
}

function apply_filters_ref_array($tag, $args) {
	global $wp_filter, $merged_filters, $wp_current_filter;

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$wp_current_filter[] = $tag;
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		if ( isset($wp_filter['all']) )
			array_pop($wp_current_filter);
		return $args[0];
	}

	if ( !isset($wp_filter['all']) )
		$wp_current_filter[] = $tag;

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				$args[0] = call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop( $wp_current_filter );

	return $args[0];
}

function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
	return add_filter($tag, $function_to_add, $priority, $accepted_args);
}

function do_action($tag, $arg = '') {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( ! isset($wp_actions) )
		$wp_actions = array();

	if ( ! isset($wp_actions[$tag]) )
		$wp_actions[$tag] = 1;
	else
		++$wp_actions[$tag];

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$wp_current_filter[] = $tag;
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		if ( isset($wp_filter['all']) )
			array_pop($wp_current_filter);
		return;
	}
	
	if ( !isset($wp_filter['all']) )
		$wp_current_filter[] = $tag;

	$args = array();
	if ( is_array($arg) && 1 == count($arg) && isset($arg[0]) && is_object($arg[0]) ) // array(&$this)
		$args[] =& $arg[0];
	else
		$args[] = $arg;
	for ( $a = 2; $a < func_num_args(); $a++ )
		$args[] = func_get_arg($a);

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}
	
	reset( $wp_filter[ $tag ] );
	
	do {
		foreach ( (array) current($wp_filter[$tag]) as $the_ ) {
			if ( !is_null($the_['function']) ) {
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));
			}
		}
	} while ( next($wp_filter[$tag]) !== false );
	
	array_pop($wp_current_filter);
}

function did_action($tag) {
	global $wp_actions;

	if ( ! isset( $wp_actions ) || ! isset( $wp_actions[$tag] ) )
		return 0;

	return $wp_actions[$tag];
}

function do_action_ref_array($tag, $args) {
	global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;

	if ( ! isset($wp_actions) )
		$wp_actions = array();

	if ( ! isset($wp_actions[$tag]) )
		$wp_actions[$tag] = 1;
	else
		++$wp_actions[$tag];

	// Do 'all' actions first
	if ( isset($wp_filter['all']) ) {
		$wp_current_filter[] = $tag;
		$all_args = func_get_args();
		_wp_call_all_hook($all_args);
	}

	if ( !isset($wp_filter[$tag]) ) {
		if ( isset($wp_filter['all']) )
			array_pop($wp_current_filter);
		return;
	}

	if ( !isset($wp_filter['all']) )
		$wp_current_filter[] = $tag;

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($wp_filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $wp_filter[ $tag ] );

	do {
		foreach( (array) current($wp_filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));

	} while ( next($wp_filter[$tag]) !== false );

	array_pop($wp_current_filter);
}

function has_action($tag, $function_to_check = false) {
	return has_filter($tag, $function_to_check);
}

function remove_action($tag, $function_to_remove, $priority = 10, $accepted_args = 1) {
	return remove_filter($tag, $function_to_remove, $priority, $accepted_args);
}

function remove_all_actions($tag, $priority = false) {
	return remove_all_filters($tag, $priority);
}

function _wp_call_all_hook($args) {
	global $wp_filter;

	reset( $wp_filter['all'] );
	do {
		foreach( (array) current($wp_filter['all']) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], $args);

	} while ( next($wp_filter['all']) !== false );
}

function _wp_filter_build_unique_id($tag, $function, $priority) {
	global $wp_filter;
	static $filter_id_count = 0;

	if ( is_string($function) )
		return $function;

	if ( is_object($function) ) {
		// Closures are currently implemented as objects
		$function = array( $function, '' );
	} else {
		$function = (array) $function;
	}

	if (is_object($function[0]) ) {
		// Object Class Calling
		if ( function_exists('spl_object_hash') ) {
			return spl_object_hash($function[0]) . $function[1];
		} else {
			$obj_idx = get_class($function[0]).$function[1];
			if ( !isset($function[0]->wp_filter_id) ) {
				if ( false === $priority )
					return false;
				$obj_idx .= isset($wp_filter[$tag][$priority]) ? count((array)$wp_filter[$tag][$priority]) : $filter_id_count;
				$function[0]->wp_filter_id = $filter_id_count;
				++$filter_id_count;
			} else {
				$obj_idx .= $function[0]->wp_filter_id;
			}

			return $obj_idx;
		}
	} else if ( is_string($function[0]) ) {
		// Static Calling
		return $function[0].$function[1];
	}
}

function get_column_alias($s) 
{
    if (preg_match('~^(.*?)\s+(AS\s+)?(.*)$~i', $s, $m))
        return $m[3];
    return $s;
}

function get_column_name($s)
{
    if (preg_match('~^(.*?)\s+(AS\s+)?(.*)$~i', $s, $m))
        return $m[1];
    return $s;
}

function get_ext($filename) 
{
    $pos = strrpos($filename,'.');
    $ext = false;
    if ($pos !== false) $ext = strtolower(substr($filename, $pos+1));
    if (!$ext) {
        $ext = 'file';
    } else if (preg_match('/txt|doc|ppt|xls|pdf/i', $ext)) {
        $ext = 'document ' . $ext;
    } else if (preg_match('/png|gif|jpg|jpeg|tiff|bmp/i', $ext)) {
        $ext = 'image ' . $ext;
    } else if (preg_match('/mp3|aac|wav|au|ogg|wma/i', $ext)) {
        $ext = 'audio ' . $ext;
    } else if (preg_match('/mpg|wmv|mov|flv/i', $ext)) {
        $ext = 'video ' . $ext;
    }
    return $ext;
}

function has_perms( $uri, $user_id=0 )
{
    $ci =& get_instance();
    if (!$user_id)
        $user_id = isset($ci->orca_auth->user->id) ? $ci->orca_auth->user->id : 0;
    if (!$user_id)
        return false;
    if (!isset( $ci->Users ))
        $ci->load->model('User_model', 'Users');
    return $ci->Users->has_perms( $uri, $user_id );
}
