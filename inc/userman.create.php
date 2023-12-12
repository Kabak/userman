<?php

/* @var $db CotDB */
/* @var $cache Cache */
/* @var $t Xtemplate */

/**
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'userman');
cot_block($usr['auth_read']);

global $temp;

	$umuser['user_name'] = cot_import('um_username','P','TXT', 100, TRUE);
	$umuser['user_email'] = cot_import('um_useremail','P','TXT',64, TRUE);
	$rpassword1 = cot_import('um_password1','P','HTM',32);
	$rpassword2 = cot_import('um_password2','P','HTM',32);
	$umuser['user_country'] = '00';
	$umuser['user_timezone'] = 'GMT';
	$umuser['user_email'] = mb_strtolower($umuser['user_email']);
	$umuser['user_text'] = cot_import('um_sign','P','HTM');
	$umuser['user_maingrp'] = cot_import('um_usermaingrp','P','INT');
	

	// Extra fields
	foreach($cot_extrafields[$db_users] as $exfld)
	{
		$umuser['user_'.$exfld['field_name']] = cot_import_extrafields($exfld['field_name'], $exfld);
	}

	$umuser['user_text'] == null ? $umuser['user_text']  = '' : $umuser['user_text'];

	$user_exists = (bool)$db->query("SELECT user_id FROM $db_users WHERE user_name = ? LIMIT 1", array($umuser['user_name']))->fetch();
	$email_exists = (bool)$db->query("SELECT user_id FROM $db_users WHERE user_email = ? LIMIT 1", array($umuser['user_email']))->fetch();

	if (preg_match('/&#\d+;/', $umuser['user_name']) || preg_match('/[<>#\'"\/]/', $umuser['user_name'])) 
		cot_error('aut_invalidloginchars', 'um_username');
	if (mb_strlen($umuser['user_name']) < 2) 
	    cot_error('aut_usernametooshort', 'um_username');
	if (mb_strlen($rpassword1) < 4) 
	    cot_error('aut_passwordtooshort', 'um_password1');
	if (!cot_check_email($umuser['user_email']))	
	    cot_error('aut_emailtooshort', 'um_useremail');
	if ($user_exists) 
	    cot_error('aut_usernamealreadyindb', 'um_username');
	if ($email_exists && !$cfg['useremailduplicate']) 
	    cot_error('aut_emailalreadyindb', 'um_useremail');
	if ($rpassword1 != $rpassword2) 
	    cot_error('aut_passwordmismatch', 'um_password2');
	
if (!cot_error_found())
{
	
	$umuser['user_passsalt'] = cot_unique(16);
	$umuser['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
	$umuser['user_password'] = cot_hash($rpassword1, $umuser['user_passsalt'], $umuser['user_passfunc']);

	$userid = cot_add_user($umuser,$umuser['user_email'],$umuser['user_name'],$rpassword1,$umuser['user_maingrp'],$sendemail =false );
	// Вносим в базу все данные о новом пользователе.
	$db->update($db_users, $umuser, 'user_id='.$userid);

	if ( $db->countRows($db_users) == 1 )
	{
		cot_redirect(cot_url('userman', 'msg=106', '', true));
	}
	elseif ($cfg['users']['regrequireadmin'])
	{
		cot_redirect(cot_url('userman', 'msg=118', '', true));
	}
	else
	{
		cot_message(um_build_string($L['user'],$umuser['user_name'],$L['successcreation'],true));
	}
}

cot_redirect(cot_url('admin', 'm=other&p=userman','', true));
