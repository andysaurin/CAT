<?php

/**
* NQ_User
*
* @package Framework
* @filesource
*/

/**
* NQ_User
*
* Base user object.
*
* @package Framework
*/
class NQ_User extends NQ_Object_DB
{
	public $id;
	public $username;
//	public $password;
	public $created;
	public $modified;
	public $locked;
	public $type;
	public $module_permissions;
	public $message;
	public $module_permission_level;
	public $module_section_permissions;
	public $module_section_permission_level;
	public $iplock;
	public $owner;


	public function __construct($id=null)
	{
		parent::__construct();

		if ($id === null) {
			$session = NQ_Session::singleton();
			if (!is_numeric($session->id)) {
				$id = 0;
			} else {
				$id = $session->id;
				$username = $session->username;
				$password = $session->password;
			}
		}

		if ($id > 0) {
			$sql = "SELECT *
					FROM admin_users
					WHERE username = '".$username."' AND password = '".$password."' LIMIT 1";

			$user = $this->db_nuqem->get_row($sql);
			if ($user->id < 1) {
				header("Location: /logout/");
			}
			NQ_Module::logQuery('db_nuqem');
		}

		if ( isset($user->id) && is_numeric($user->id) && $user->id > 0 ) {

			//are we restricting sessions to single IPs?
			if ( SINGLE_IP_LOGON === true ) {
				//check that their last seen IP equals their current IP
				if ( $_SERVER['REMOTE_ADDR'] != $user->last_seen_ip ) {
					$user->locked = 'Session/User IP mismatch. You have been logged out for security reasons.';
				}
			}
			//check to make sure the session hasn't been hijacked - if so, log out everyone
			if ( $session->ip != $_SERVER['REMOTE_ADDR'] ) {
				if ( !$_SERVER['HTTP_X_FORWARDED_FOR']
					&& !$_SERVER['HTTP_X_FORWARDED']
					&& !$_SERVER['HTTP_FORWARDED_FOR']
					&& !$_SERVER['HTTP_CLIENT_IP']
                                        && !$_SERVER['HTTP_VIA']
                                        && !$_SERVER['HTTP_X_WISP']
				) { //user is not on a proxy
					$user->locked = 'Session IP / User IP mismatch. You have been logged out for security reasons.';
				}

			}

			// is the user on an authorised IP?
			if ( $user->iplock == 1) {
				$sql = "SELECT `user_id` FROM `admin_users_iplocks` WHERE `user_id`='{$user->id}' AND `ip`='".$_SERVER['REMOTE_ADDR']."' LIMIT 1";
				$user_id = $this->db_nuqem->get_var($sql);
				NQ_Module::logQuery('db_nuqem');
				if ( $user_id < 1 ) { //lock them out
					$user->locked = 'Unauthorized access from this location.';
				}
			}


			if ( $user->locked ) { //the user has been locked out, so log them out

				NQ_Session::destroy();
				unset($_SERVER['PHP_AUTH_USER']);
				if ( is_array($_SESSION) )
					unset($_SESSION);

				if (is_array($_COOKIE)) {
					foreach ($_COOKIE as $key => $val) {
               			NQ_Cookie::delete($key);
					}
				}
				setcookie("user", '', time()-31536000, '/', $_SERVER['HTTP_HOST']);

				//now set the message
				$message['text'] = $user->locked;
				$message['type'] = 'error';
				$message['delay'] = '8000';
				session_start();
				$_SESSION['message'] = $message;

				header("Location: /login/?ret=".NQ_Module::nq_encrypt(SECRET_KEY, $_SERVER['REQUEST_URI']));
				exit();

			} else {

				//verify the user's permissions for this module
				$user->module_permissions = $this->module_permissions($user->id, $user->type);
				$requested_module = mysql_real_escape_string($_GET['module']);
				$user->module_permission_level = $user->module_permissions[$requested_module];

				if ($user->module_permission_level < 1 && ($_GET['module'] != 'api' && $_GET['class'] != 'login' && $_GET['class'] != 'logout')){

					$user->access_denied = 1;
					$user->message->text = 'You do not have permission to view this page.';
					$user->message->type = 'error';

				}

				//this has to go before the verification of module section permissions, or else no section subheadings would show for the main module index page.
				$user->module_section_permissions = $this->section_permissions(mysql_real_escape_string($_GET['module']), $user);

				if ( !isset($_GET['class']) )
					$_GET['class'] = 'index'; // for module index pages, _GET[index] isn't set, so we need to specify it here to force permission checking

				if ( $_GET['class'] != 'login' && $_GET['class'] != 'logout' ) {

					//verify the user's permissions for this module section
					$requested_section = mysql_real_escape_string($_GET['class']);
// swap this out after testing module index page permissions
					$user->module_section_permission_level = ($user->type=='superuser' ? 100 : $user->module_section_permissions[$requested_section]);
//					$user->module_section_permission_level = $user->module_section_permissions[$requested_section];
					if ( $user->module_section_permission_level < 1 && isset($_GET['class']) && $_GET['module'] != 'api' && $_GET['class'] != 'login' && $_GET['class'] != 'logout' ){

						$user->access_denied = 1;
						$user->message->text = 'You do not have permission to view this page.';
						$user->message->type = 'error';

					}
				} else {
					//the section is default module index file, so set their usage level to that of the main module
					$user->module_section_permission_level = $user->module_permission_level;
				}

				//$user->geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
				$user->last_login_formatted = NQ_Module::localiseDate($user->last_login);

				//initiate the session class if not initiated
				if( !is_object( $this->session ) ) {
    				$this->session = new StdClass;
				}

				foreach ($user as $key=>$val) { //session vars
					$this->session->$key = $val;
				}
				$this->setFrom($user); //individual $this->user vars

				$this->db_nuqem->query("UPDATE admin_users SET last_seen='".time()."', last_seen_ip='".$_SERVER['REMOTE_ADDR']."' WHERE id='".$user->id."' LIMIT 1");

				$test['key1'] = 'val1';
				$this->setFrom($test);

			}
		}

	}

	public function module_permissions($user_id, $type=false)
	{
		if ($type == 'superuser') { // superusers have access to all modules
			$sql = "SELECT * FROM admin_modules";
			$modules = $this->db_nuqem->get_results($sql);
			NQ_Module::logQuery('db_nuqem');
			if (is_array($modules) && count($modules)) {
				$user_permissions = array();
				foreach($modules as $obj) {
					$user_permissions[$obj->name] = 100; // 100 is superuser
				}
			}
		} else {
			$sql = "SELECT am.name as module_name, aup.* FROM admin_modules am, admin_users_permissions aup WHERE aup.user_id={$user_id} AND aup.module_id=am.id";
			$permissions = $this->db_nuqem->get_results($sql);
			NQ_Module::logQuery('db_nuqem');

			if (is_array($permissions) && count($permissions)) {
				$user_permissions = array();
				foreach($permissions as $obj) {
					if ($obj->module_section_id < 1) {
						$user_permissions[$obj->module_name] = $obj->group_id;
					}
				}
			}
		}
		return $user_permissions;
	}

	public function section_permissions($module_name, $user)
	{

		if ($user->type == 'superuser') { // superusers have access to all module sections

			$sql = "SELECT ams.name as section_name FROM admin_modules_sections ams, admin_modules am WHERE am.name='{$module_name}' AND am.id=ams.module_id";
			$sections = $this->db_nuqem->get_results($sql);
			NQ_Module::logQuery('db_nuqem');
			if (is_array($sections) && count($sections)) {
				$user_permissions = array();
				foreach($sections as $obj) {
					$user_permissions[$obj->section_name] = 100; // 100 is superuser
				}
			}

		} else {

			$sql = "
				SELECT
					ams.name as section_name, ams.min_permission_level, aup.*
				FROM
					admin_modules_sections ams, admin_modules am, admin_users_permissions aup
				WHERE
					am.name='{$module_name}'
					AND
					am.id=aup.module_id
					AND
					ams.id=aup.module_section_id
					AND
					aup.module_section_id>0
					AND
					aup.user_id={$user->id}";
			$permissions = $this->db_nuqem->get_results($sql);
			NQ_Module::logQuery('db_nuqem');

			if (is_array($permissions) && count($permissions)) {
				$user_permissions = array();
				foreach($permissions as $obj) {
					if ($obj->module_section_id > 0) {
						//does the user have the required minimum permission level for this section?
						$sql = "SELECT ams.min_permission_level FROM admin_modules_sections ams, admin_modules am WHERE am.name='{$module_name}' AND am.id=ams.module_id AND ams.name='{$obj->section_name}' LIMIT 1";
						$min_permission_level = $this->db_nuqem->get_var($sql);
						NQ_Module::logQuery('db_nuqem');
						if ( $obj->group_id >= $min_permission_level ) { //yup, they have ath least the minimum required permission
							$user_permissions[$obj->section_name] = $obj->group_id;
						}
					}
				}
			}
		}
		return $user_permissions;
	}

	public function permission_groups($id=false) {
		if (!$id) {
			$sql = "SELECT * FROM `admin_groups` ORDER BY `id`";
			$groups = $this->db_nuqem->get_results($sql);
			NQ_Module::logQuery('db_nuqem');
			return $groups;

		} else {
			$sql = "SELECT * FROM `admin_groups` WHERE `id` = ".(int)$id." LIMIT 1";
			$group = $this->db_nuqem->get_row($sql);
			NQ_Module::logQuery('db_nuqem');
			return $group;
		}
	}
	public function __destruct()
	{
		parent::__destruct();
	}

}

?>
