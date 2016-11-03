<?php

/**
* login
*
* 
* 
* @license http://www.opensource.org/licenses/gpl-license.php 
* @package Modules
* @filesource
*/

//require_once('Validate.php');

/**
* login
*
* 
* @package Modules
*/
class login extends NQ_Auth_No
{
	public function __construct()
	{
		parent::__construct();
	}

	public function __default()
	{
		$this->log->log('include modules/admin/login.php', PEAR_LOG_DEBUG);

		if ($_POST['username'] && $_POST['password']) {
			
/*
			$username = $_POST['username'];
			$password = $_POST['password'];
*/
			if (preg_match('/^[0-9A-Za-z_\-]+$/', $_POST['username'])) {
				$username = $_POST['username'];
			} 
			if (preg_match('/^[0-9A-Za-z_\-]+$/', $_POST['password'])) {
				$password = $this->nq_encrypt(PASSWORD_KEY, $_POST['password']);
			} 
			
			if ( isset($username) && isset($password) ) {
			
				//turn off mysql disk caching if it's on for all the function
				if (DB_USECACHE == true) {
					$this->db_nuqem->cache_queries = false;
					$this->db_nuqem->use_disk_cache = false;
				}
			
				$sql = "SELECT *
					FROM admin_users
					WHERE username = '".$username."' AND password = '".$password."' LIMIT 1";

				$user = $this->db_nuqem->get_row($sql);
				$this->logQuery('db_nuqem');

				if ( $user->iplock == 1) { // is the user on an authorised IP?
				
					$sql = "SELECT `user_id` FROM `admin_users_iplocks` WHERE `user_id`='{$user->id}' AND `ip`='".$_SERVER['REMOTE_ADDR']."' LIMIT 1";
					$user_id = $this->db_nuqem->get_var($sql);
					NQ_Module::logQuery('db_nuqem');
				
					if ( $user_id < 1 ) { //don't let them in
						$message['text'] = 'Unauthorized access from this location.';
						$message['type'] = 'error';
						$message['delay'] = '5000';
						unset($user);
					}
				
				}
				if ( $user->locked > 0 ) {
			
					$message['text'] = "This account has been disabled.";
					$message['type'] = 'error';
					unset($user);
				
			
				}elseif ( is_numeric($user->id) && $user->id > 0 ) {
					$session = NQ_Session::singleton(); 
					
					//store when they had previously last logged in.
					$session->prev_login_time = $user->last_login;
					$session->prev_login_ip = $user->last_login_ip;
					
					//store their current session IP to prevent session hijacking;
					$session->ip = $_SERVER['REMOTE_ADDR'];
					
					//now update the time they logged in.
					$this->db_nuqem->query("UPDATE admin_users SET last_login='".time()."', last_login_ip='".$_SERVER['REMOTE_ADDR']."', last_seen='".time()."', last_seen_ip='".$_SERVER['REMOTE_ADDR']."' WHERE id='".$user->id."' LIMIT 1");
							
					if ( isset($_GET['ret']) ) {
						$go = $this->nq_decrypt( SECRET_KEY, $_GET['ret']);
					} else {
						$go = '/';
					}
				
					foreach ($user as $key=>$val) { //session vars
						$session->$key = $val;
					}
				
					//turn disk caching back on if default is to cache
					if (DB_USECACHE == true) {
						$this->db_nuqem->cache_queries = true;
						$this->db_nuqem->use_disk_cache = true;
					}
				
					$message['text'] = 'You are logged in as <i>'.$_POST['username'].'</i>';
					$message['type'] = '';
					
					$session->message = $message;
					header("Location: $go");
					exit();

				} else { //invalid user/pass
				
					$message['text'] = 'Invalid username and/or password.';
					$message['type'] = 'error';
					$message['delay'] = '3000';

				}
			
				//turn disk caching back on if default is to cache
				if (DB_USECACHE == true) {
					$this->db_nuqem->cache_queries = true;
					$this->db_nuqem->use_disk_cache = true;
				}
			} else {
			
				$message['text'] = "Invalid username and/or password.";
				$message['type'] = 'error';
				$message['delay'] = '3000';
			
			}

		}elseif ( $_POST && ( !$_POST['username'] || !$_POST['password'] )) {
			$message['text'] = 'You must provide a login and password.';
			$message['type'] = 'warning';		
		}
		if (is_array($message)) {
			$this->set('message', $message);
		}
	}

	public function __destruct()
	{
		parent::__destruct();
	}
}


?>
