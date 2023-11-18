<?php
/**
 * Usermanager Functions
 *
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
// Регистрируем базу данных в глобальном списке баз COTONTI
global $db_userman;
$db_userman = isset($db_userman) ? $db_userman : $db_x . 'userman';

// Requirements
require_once cot_incfile('auth');
require_once cot_langfile('users', 'core');
require_once cot_incfile('users', 'module', 'resources');
//Подключаем файл с ресурсами
require_once cot_incfile('userman', 'plug', 'resources');

/**
 * Delete user from temporary access dbase if user inlist
 * 
 * @param $user_id  Id of user to be deleted   Superadmin can't be deleted using this function 
 * @return  1 / 0 
 */
function um_delete_user_access( $user_id )
{
	global $db, $db_userman;

	$sql = $db->query("SELECT * FROM $db_userman WHERE user_id=$user_id LIMIT 1");
	$user_found = $sql->fetch();
	// Проверяем есть ли пользователь в базе с поднятием уровня на время
	if ( $user_found )
	{
		$sql1 = $db->delete($db_userman, 'user_id='.$user_id );
	}
}

/**
 * Silently delete user 
 * 
 * @param $user_id  Id of user to be deleted   Superadmin can't be deleted using this function 
 * @return  1 / 0 
 */
function um_delete_user( $user_id )
{
	global $db, $db_users, $db_groups_users;

	if ( $user_id > 1 )
	{
		$u = $db->query("SELECT * FROM $db_users WHERE user_id=$user_id LIMIT 1")->fetch();
		$deleted_name = $u['user_name'];
		$sql = $db->delete($db_users, "user_id=$user_id");
		$sql = $db->delete($db_groups_users, "gru_userid=$user_id");

		foreach($cot_extrafields[$db_users] as $exfld)
		{
			cot_extrafield_unlinkfiles($urr['user_'.$exfld['field_name']], $exfld);
		}

		if (cot_module_active('pfs') && cot_import('um_edit_userdelpfs','P','BOL'))
		{
			require_once cot_incfile('pfs', 'module');
			cot_pfs_deleteall($user_id);
		}
	}
	else
	{
		return "";
	}
	return $deleted_name;
}

/**
 *Generate massive from string  - groups list 
 * 
 * @param string  string of groups  like 4,5,6 
 * @return massive user groups  [4]=1,[5]=1,[6]=1 
 */
function um_string_to_massive($string){
    $member = explode(',', $string);
    foreach( $member as $key => $value ){
	$value ? $united[$value] = 1 : $united[$value];     
    }
    return($united);
}


/**
 *Cut last symbol in Groups generated string  like 4,5,6,  , - need to be cuted
 *
 * @param string  string of groups  like 4,5,6, 
 * @return string 4,5,6
 */
function um_cut_string($string){
    $length = strlen($string)-1;
    $string = strrev($string);
    $string = substr($string,-$length);
    $string = strrev($string);
    return($string);
}

/**
 *Generate user default groups list from main base
 *
 * @param int  userid - User id for operate
 * @return string default user groups  like 4,5,6
 */
function um_gen_user_defgroups($userid){
    global $db,  $db_groups_users; 
//  Генерируем строку для базы  из групп которые доступны пользователю по умолчанию    
    $memberships = $db->query("SELECT gru_groupid FROM $db_groups_users	WHERE gru_userid = ?", array($userid))->fetchAll();
  if ( !$memberships ){
      return ( null );
  }
  foreach ($memberships as $row)
  {
    $member[$row['gru_groupid']] = TRUE;
  }
    $act_user['groups_default'] = '';
    $gpoups_string = '';    
    foreach($member as $number => $val){
	    $gpoups_string .= $number.',';
    }
    return(um_cut_string($gpoups_string));
}

/**
 *Generate user accessed groups list. Including Time accesssd and Default accessed
 *
 * @param int  userid - User id for operate
 * @return string default user groups  like 4,5,6,0  0 - list end 
 */
function um_gen_user_access_groups($userid){
    global $db,  $db_groups_users, $db_userman; 

  $act_user = $db->query("SELECT * FROM $db_userman WHERE user_id=$userid LIMIT 1")->fetch();
  $access_groups = explode(',', $act_user['groups_access']);
	foreach($access_groups as $n ){
	$member[$n] = TRUE;    
	}
    $gpoups_string = '';    
    foreach($member as $number => $val){
	    $gpoups_string .= $number.',';
    }
    return(um_cut_string($gpoups_string));
}

/**
 * Unite groups membership and update user access to gropus
 *
 * @param int  id - User id for operate
 * @param array member - Array of user membership gropus
 * @param array tempgroups - Array of  groups where user have temporary access
 * @param boolean unite - true if you need to unite user groups access before updating Database
 */
function um_gropus_idu($id, $member, $tempgroups = null, $unite = false){
    global $db, $cot_groups, $db_groups_users;
// создаём объединённый массив групп доступных пользователю для обновления базы данных      
foreach( $member as $key => $value ){
    $value ? $united[$key] = 1 : $united[$key]; 
}
if( $unite ){
    foreach( $tempgroups as $key => $value ){
	$value ? $united[$key] = 1 : $united[$key];
    }   
}
		foreach($cot_groups as $k => $i)
		{
			if (isset($united[$k]))
			{
				if ($db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid=$id AND gru_groupid=$k")->rowCount() == 0
					&& !($id == 1 && in_array($k, array(COT_GROUP_BANNED, COT_GROUP_INACTIVE))))
				{
					$db->insert($db_groups_users, array('gru_userid' => (int)$id, 'gru_groupid' => (int)$k));
				}
			}
			else
			{
				$db->delete($db_groups_users, "gru_userid=$id AND gru_groupid=$k");
			}
		}
}		

		
/**
 * Build text message link string from couple strings
 *
 * @param string prefix string
 * @param string body string
 * @param string postfix string
 * @param string link_string string where you want link to be set
 * @param bool linked - true if generate link
 * @return string
 */
function um_build_string($prefix,$username,$postfix,$linked = false){
    global $db, $db_users;
    
    if($linked){
        $u = $db->query("SELECT * FROM $db_users WHERE user_name = '$username' LIMIT 1")->fetch();
        $userid = $u['user_id'];

	$str = $prefix.cot_rc_link(cot_url('admin', 'm=other&p=userman&a=edit&id='.$userid.'&u='.$username),$username, array('title' => $username)).$postfix;//, 'class' => 'my-class', 'rel' => 'nofolow'		
//        $str = $prefix.'<a href="admin.php?m=other&p=userman&a=edit&id='.$userid.'&u='.$username.'">'.$username.'</a>'.$postfix;   
    }
    else{
        $str = $prefix.$username.$postfix;
    }
    return ( $str );
}


/**
 * Builds list of user's groups, editable or not
 *
 * @param int userid 
 * @param bool $edit Permission
 * @param int $maingrp User default group
 * @return string
 * @global CotDB $db
 */
// Used for generation links from Edit Groups Links
// Set LINK TO cot_url('admin','m=other&p=userman
function cot_build_um2_groupsms($userid, $edit = FALSE, $maingrp = 0)
{
	global $db, $db_groups, $db_groups_users, $db_userman, $cot_groups, $L, $usr, $R;
	$group_array = array();
	
	$memberships = $db->query("SELECT gru_groupid FROM $db_groups_users	WHERE gru_userid = ?", array($userid))->fetchAll();
	foreach ($memberships as $row)
	{
		$member[$row['gru_groupid']] = TRUE;
	}

	$tilltime = $db->query("SELECT groups_access FROM $db_userman WHERE user_id = $userid")->fetch();
		
	$group_array = explode(',',$tilltime['groups_access'] ?? '');
	foreach($group_array as $k =>$v){
	 $tilltime_member[$v] = TRUE;   
	}
	
	$res = $R['users_code_grplist_begin'];
	foreach ($cot_groups as $k => $i)
	{
		if(!isset($member[$k])) $member[$k] = FALSE;
		
		if ($edit)
		{
		    $checked = ($member[$k]) ? ' checked="checked"' : '';
		    $time_checked = isset($tilltime_member[$k]) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $k) ? ' checked="checked"' : '';
			$readonly = ' disabled="disabled"';
			$readonly_acess = ($k == COT_GROUP_GUESTS || $k == COT_GROUP_INACTIVE || $k == COT_GROUP_BANNED
				|| ($k == COT_GROUP_SUPERADMINS && $userid == 1)) ? ' disabled="disabled"' : '';
			$readonly_maingrp = ' disabled="disabled"';
		}
		if ($member[$k] || $edit)
		{
			if (!$cot_groups[$k]['hidden'] || cot_auth('users', 'a', 'A'))
			{
				$item = '';
				if ($edit)
				{
					$item .= cot_rc('users_input_grplist_radio', array(
						'value' => $k,
						'name' => 'um_usermaingrp',
						'checked' => $checked_maingrp,
						'title' => '',
						'attrs' => $readonly_maingrp
					));
					$item .= cot_rc('users_input_grplist_checkbox', array(
						'value' => '1',
						'name' => "um_usergroupsms[$k]",
						'checked' => $checked,
						'title' => '',
						'attrs' => $readonly
					));
					$item .= cot_rc('users_input_grplist_checkbox', array(
						'value' => '1',
						'name' => "um_group_time_accesssms[$k]",
						'checked' => $time_checked,
						'title' => '',
						'attrs' => $readonly_acess
					));
				}
				$item .= ( $k == COT_GROUP_GUESTS) ? $cot_groups[$k]['name'] : cot_rc_link(cot_url('admin','m=other&p=userman&gm=' . $k), $cot_groups[$k]['name']);
				$item .= ( $cot_groups[$k]['hidden']) ? ' (' . $L['Hidden'] . ')' : '';
				$rc = ($maingrp == $k) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc($rc, array('item' => $item));
			}
		}
	}
	$res .= $R['users_code_grplist_end'];
	return $res;
}

/**
 * Builds list of user's groups, editable or not
 *
 * @param int userid 
 * @param bool $edit Permission
 * @param int $maingrp User default group
 * @return string
 * @global CotDB $db
 */
// Used for generation links from Edit Groups Links
// Set LINK TO cot_url('admin','m=other&p=userman
function cot_build_um1_groupsms($userid, $edit = FALSE, $maingrp = 0)
{
	global $db, $db_groups, $db_groups_users, $cot_groups, $L, $usr, $R;

	$memberships = $db->query("SELECT gru_groupid FROM $db_groups_users	WHERE gru_userid = ?", array($userid))->fetchAll();
	foreach ($memberships as $row)
	{
		$member[$row['gru_groupid']] = TRUE;
	}

	$res = $R['users_code_grplist_begin'];
	foreach ($cot_groups as $k => $i)
	{
		if ($edit)
		{
		    $checked = (isset($member[$k])) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $k) ? ' checked="checked"' : '';
			$readonly = ($k == COT_GROUP_GUESTS || $k == COT_GROUP_INACTIVE || $k == COT_GROUP_BANNED
				|| ($k == COT_GROUP_SUPERADMINS && $userid == 1)) ? ' disabled="disabled"' : '';
			$readonly_maingrp = ( $k == COT_GROUP_GUESTS || ($k == COT_GROUP_INACTIVE && $userid == 1)
				|| ($k == COT_GROUP_BANNED && $userid == 1)) ? ' disabled="disabled"' : '';
		}
		if (isset($member[$k]) || $edit)
		{
			if (!$cot_groups[$k]['hidden'] || cot_auth('users', 'a', 'A'))
			{
				$item = '';
				if ($edit)
				{
					$item .= cot_rc('users_input_grplist_radio', array(
						'value' => $k,
						'name' => 'um_usermaingrp',
						'checked' => $checked_maingrp,
						'title' => '',
						'attrs' => $readonly_maingrp
					));
					$item .= cot_rc('users_input_grplist_checkbox', array(
						'value' => '1',
						'name' => "um_usergroupsms[$k]",
						'checked' => $checked,
						'title' => '',
						'attrs' => $readonly
					));
				}
				$item .= ( $k == COT_GROUP_GUESTS) ? $cot_groups[$k]['name'] : cot_rc_link(cot_url('admin','m=other&p=userman&gm=' . $k), $cot_groups[$k]['name']);
				$item .= ( $cot_groups[$k]['hidden']) ? ' (' . $L['Hidden'] . ')' : '';
				$rc = ($maingrp == $k) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc($rc, array('item' => $item));
			}
		}
	}
	$res .= $R['users_code_grplist_end'];
	return $res;
}
/**
 * Builds list of user's groups, editable or not
 *
 * @param array [0]['gru_groupid'] - default group to display
 * @param bool $edit Permission
 * @param int $maingrp User  default group
 * @return string
 * @global CotDB $db
 */
// Used for generation links from Create Groups
function cot_build_um_groupsms($usergroup, $edit = FALSE, $maingrp = 0)
{
	global $db, $db_groups, $db_groups_users, $cot_groups, $L, $usr, $R;

	//$memberships = $db->query("SELECT gru_groupid FROM $db_groups_users	WHERE gru_userid = ?", array($userid))->fetchAll();
	foreach ($usergroup as $row)
	{
		$member[$row['gru_groupid']] = TRUE;
	}
	$userid = $usr["id"];
	$res = $R['users_code_grplist_begin'];
	foreach ($cot_groups as $k => $i)
	{
		if(!isset($member[$k])) $member[$k] = FALSE;

		if ($edit)
		{
			$checked = ($member[$k]) ? ' checked="checked"' : '';
			$checked_maingrp = ($maingrp == $k) ? ' checked="checked"' : '';
			$readonly = ($k == COT_GROUP_GUESTS || $k == COT_GROUP_INACTIVE || $k == COT_GROUP_BANNED
				|| ($k == COT_GROUP_SUPERADMINS && $userid == 1)) ? ' disabled="disabled"' : '';
			$readonly_maingrp = ( $k == COT_GROUP_GUESTS || ($k == COT_GROUP_INACTIVE && $userid == 1)
				|| ($k == COT_GROUP_BANNED && $userid == 1)) ? ' disabled="disabled"' : '';
		}
		if ($member[$k] || $edit)
		{
			if (!$cot_groups[$k]['hidden'] || cot_auth('users', 'a', 'A'))
			{
				$item = '';
				if ($edit)
				{
					$item .= cot_rc('users_input_grplist_radio', array(
						'value' => $k,
						'name' => 'um_usermaingrp',
						'checked' => $checked_maingrp,
						'title' => '',
						'attrs' => $readonly_maingrp
					));
					$item .= cot_rc('users_input_grplist_checkbox', array(
						'value' => '1',
						'name' => "um_usergroupsms[$k]",
						'checked' => $checked,
						'title' => '',
						'attrs' => $readonly
					));
				}
				$item .= ( $k == COT_GROUP_GUESTS) ? $cot_groups[$k]['name'] : cot_rc_link(cot_url('admin','m=other&p=userman&gm=' . $k), $cot_groups[$k]['name']);
				$item .= ( $cot_groups[$k]['hidden']) ? ' (' . $L['Hidden'] . ')' : '';
				$rc = ($maingrp == $k) ? 'users_code_grplist_item_main' : 'users_code_grplist_item';
				$res .= cot_rc($rc, array('item' => $item));
			}
		}
	}
	$res .= $R['users_code_grplist_end'];
	return $res;
}


/**
 * Returns all user tags for XTemplate
 *
 * @param mixed $user_data User Info Array
 * @param string $tag_prefix Prefix for tags
 * @param string $emptyname Name text if user is not exist
 * @param bool $allgroups Build info about all user groups
 * @param bool $cacheitem Cache tags
 * @return array
 * @global CotDB $db
 */
function cot_generate_um_usertags($user_data, $tag_prefix = '', $emptyname='', $allgroups = false, $cacheitem = true)
{
	global $db, $cot_extrafields, $cot_groups, $cfg, $L, $user_cache, $db_users;

	static $extp_first = null, $extp_main = null;

	$return_array = array();

	if (is_null($extp_first))
	{
		$extp_first = cot_getextplugins('usertags.first');
		$extp_main = cot_getextplugins('usertags.main');
	}
	
	/* === Hook === */
	foreach ($extp_first as $pl)
	{
		include $pl;
	}
	/* ===== */

	$user_id = is_array($user_data) ? (int)$user_data['user_id'] : (is_numeric($user_data) ? (int)$user_data : 0);
	if (isset($user_cache[$user_id]))
	{
		$temp_array = $user_cache[$user_id];
	}
	else
	{
		if (!is_array($user_data) && $user_id > 0)
		{
			$sql = $db->query("SELECT * FROM $db_users WHERE user_id = $user_id LIMIT 1");
			$user_data = $sql->fetch();
		}

		if (is_array($user_data) && $user_data['user_id'] > 0 && !empty($user_data['user_name']))
		{
			$user_data['user_birthdate'] = cot_date2stamp($user_data['user_birthdate']);
			$user_data['user_text'] = cot_parse($user_data['user_text'], $cfg['users']['usertextimg']);

			$temp_array = array(
				'ID' => $user_data['user_id'],
				'NAME' => cot_build_um_user($user_data['user_id'], htmlspecialchars($user_data['user_name'])),
				'NICKNAME' => htmlspecialchars($user_data['user_name']),
				'DETAILSLINK' => cot_url('admin','m=other&p=userman&a=edit&id=' . $user_data['user_id'].'&u='.htmlspecialchars($user_data['user_name'])),
				'DETAILSLINKSHORT' => cot_url('admin','m=other&p=userman&a=edit&id=' . $user_data['user_id']),
				'TITLE' => $cot_groups[$user_data['user_maingrp']]['title'],
				'MAINGRP' => cot_build_um_group($user_data['user_maingrp']),
				'MAINGRPID' => $user_data['user_maingrp'],
				'MAINGRPNAME' => $cot_groups[$user_data['user_maingrp']]['name'],
				'MAINGRPTITLE' => cot_build_um_group($user_data['user_maingrp'], true),
				'MAINGRPSTARS' => cot_build_stars($cot_groups[$user_data['user_maingrp']]['level']),
				'MAINGRPICON' => cot_build_groupicon($cot_groups[$user_data['user_maingrp']]['icon']),
				'COUNTRY' => cot_build_um_country($user_data['user_country']),
				'COUNTRYFLAG' => cot_build_um_flag($user_data['user_country']),
				'TEXT' => $user_data['user_text'],
				'EMAIL' => cot_build_email($user_data['user_email'],false),// $user_data['user_hideemail']
				'THEME' => $user_data['user_theme'],
				'SCHEME' => $user_data['user_scheme'],
				'GENDER' => ($user_data['user_gender'] == '' || $user_data['user_gender'] == 'U') ? '' : $L['Gender_' . $user_data['user_gender']],
				'BIRTHDATE' => (is_null($user_data['user_birthdate'])) ? '' : cot_date('date_full', $user_data['user_birthdate']),
				'BIRTHDATE_STAMP' => (is_null($user_data['user_birthdate'])) ? '' : $user_data['user_birthdate'],
				'AGE' => (is_null($user_data['user_birthdate'])) ? '' : cot_build_age($user_data['user_birthdate']),
				'TIMEZONE' => cot_build_timezone(cot_timezone_offset($user_data['user_timezone'], false, false)) . ' ' .str_replace('_', ' ', $user_data['user_timezone']),
				'REGDATE' => cot_date('datetime_medium', $user_data['user_regdate']),
				'REGDATE_STAMP' => $user_data['user_regdate'],
				'LASTLOG' => cot_date('datetime_medium', $user_data['user_lastlog']),
				'LASTLOG_STAMP' => $user_data['user_lastlog'],
				'LOGCOUNT' => $user_data['user_logcount'],
				'POSTCOUNT' => $user_data['user_postcount'],
				'LASTIP' => $user_data['user_lastip']
			);

			if ($allgroups)
			{
				$temp_array['GROUPS'] = cot_build_groupsms($user_data['user_id'], FALSE, $user_data['user_maingrp']);
			}
			// Extra fields
			if (isset($cot_extrafields[$db_users]))
			{
				foreach ($cot_extrafields[$db_users] as $exfld)
				{
					$temp_array[strtoupper($exfld['field_name'])] = cot_build_extrafields_data('user', $exfld, $user_data['user_' . $exfld['field_name']]);
					$temp_array[strtoupper($exfld['field_name']) . '_TITLE'] = isset($L['user_' . $exfld['field_name'] . '_title']) ? $L['user_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
					$temp_array[strtoupper($exfld['field_name']) . '_VALUE'] = $user_data['user_' . $exfld['field_name']];
				}
			}
		}
		else
		{
			$temp_array = array(
				'ID' => 0,
				'NAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'NICKNAME' => (!empty($emptyname)) ? $emptyname : $L['Deleted'],
				'MAINGRP' => cot_build_group(1),
				'MAINGRPID' => 1,
				'MAINGRPSTARS' => '',
				'MAINGRPICON' => cot_build_groupicon($cot_groups[1]['icon']),
				'COUNTRY' => cot_build_country(''),
				'COUNTRYFLAG' => cot_build_flag(''),
				'TEXT' => '',
				'EMAIL' => '',
				'GENDER' => '',
				'BIRTHDATE' => '',
				'BIRTHDATE_STAMP' => '',
				'AGE' => '',
				'REGDATE' => '',
				'REGDATE_STAMP' => '',
				'POSTCOUNT' => '',
				'LASTIP' => ''
			);
		}
		
		/* === Hook === */
		foreach ($extp_main as $pl)
		{
			include $pl;
		}
		/* ===== */

		$cacheitem && $user_cache[$user_data['user_id']] = $temp_array;
	}
	foreach ($temp_array as $key => $val)
	{
		$return_array[$tag_prefix . $key] = $val;
	}
	return $return_array;
}

/**
 * Returns link to user profile
 *
 * @param int $id User ID
 * @param string $user User name
 * @param mixed $extra_attrs Extra link tag attributes as a string or associative array,
 *		e.g. array('class' => 'usergrp_admin')
 * @return string
 */
function cot_build_um_user($id, $user, $extra_attrs = '')
{
	if (!$id)
	{
		return empty($user) ? '' : $user;
	}
	else
	{
		return empty($user) ? '?' : cot_rc_link(cot_url('admin','m=other&p=userman&a=edit&id='.$id.'&u='.$user), $user, $extra_attrs);
	}
}

/**
 * Returns group link (button)
 *
 * @param int $grpid Group ID
 * @param bool $title Return group title instead of name
 * @return string
 */
function cot_build_um_group($grpid, $title = false)
{
	if (empty($grpid))
		return '';
	global $cot_groups, $L;

	$type = ($title) ? 'title' : 'name';
	if ($cot_groups[$grpid]['hidden'])
	{
		if (cot_auth('users', 'a', 'A'))
		{
			return cot_rc_link(cot_url('admin', 'm=other&p=userman&sort=grpname&gm=' . $grpid), $cot_groups[$grpid][$type] . ' (' . $L['Hidden'] . ')');
		}
		else
		{
			return $L['Hidden'];
		}
	}
	else
	{
		if ($type == 'title' && isset($L['users_grp_' . $grpid . '_title']))
		{
			return cot_rc_link(cot_url('admin', 'm=other&p=userman&sort=grpname&gm=' . $grpid), $L['users_grp_' . $grpid . '_title']);
		}
		return cot_rc_link(cot_url('admin', 'm=other&p=userman&sort=grpname&gm=' . $grpid), $cot_groups[$grpid][$type]);
	}
}

/**
 * Returns country text button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_um_country($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	$country = isset($cot_countries[$flag]) ? $cot_countries[$flag] : cot::$R['code_option_empty'];
	return cot_rc_link(cot_url('admin', 'm=other&p=userman&sort=country&f=country_'.$flag), $country, array(
		'title' => $country
	));
}

/**
 * Returns country flag button
 *
 * @param string $flag Country code
 * @return string
 */
function cot_build_um_flag($flag)
{
	global $cot_countries;
	if (!$cot_countries) include_once cot_langfile('countries', 'core');
	$flag = (empty($flag)) ? '00' : $flag;
	$country = isset($cot_countries[$flag]) ? $cot_countries[$flag] : cot::$R['code_option_empty'];
	return cot_rc_link(cot_url('admin', 'm=other&p=userman&sort=country&f=country_'.$flag),
		cot_rc('icon_flag', array('code' => $flag, 'alt' => $flag)),
		array('title' => $country)
	);
}


/**
 * Returns UserImages tags for coTemplate
 *
 * @param array $user_data User info array
 * @param string $tag_prefix Prefix for tags
 * @return array
 */
function cot_um_userimages_tags($user_data, $tag_prefix='')
{
	global $m;

	$temp_array = array();
	$userimages = cot_userimages_config_get();
	$uid = $user_data['user_id'];
	$usermode = $m == 'edit' || ($uid != Cot::$usr['id']);

	foreach($userimages as $code => $settings)
	{
		if (!empty($user_data['user_'.$code]))
		{
			$delete_params = 'r=userimages'
				.'&a=delete'
				.'&uid='.($usermode ? $uid : '')
				.'&m='.$m
				.'&code='.$code
				.'&'.cot_xg();
			$userimg_existing = cot_rc('userimg_existing', array(
				'url_file' => $user_data['user_'.$code],
				'url_delete' => cot_url('plug', $delete_params)
			));
		}
		else
		{
			$userimg_existing = '';
		}
		$userimg_selectfile = cot_rc('userimg_selectfile', array(
			'form_input' => cot_inputbox('file', $usermode ? $code.':'.$uid : $code, '', array('size' => 24))
		));
		$userimg_html = cot_rc('userimg_html', array(
			'code' => $usermode ? $code.' uid_'.$uid: $code,
			'existing' => $userimg_existing,
			'selectfile' => $userimg_selectfile
		));

		$temp_array[$tag_prefix . strtoupper($code)] = $userimg_html;
		$temp_array[$tag_prefix . strtoupper($code) . '_SELECT'] = $userimg_selectfile;
	}

	return $temp_array;
}