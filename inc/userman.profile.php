<?php

/* @var $db CotDB */
/* @var $cache Cache */
/* @var $t Xtemplate */

/**
 * @package userman
 * @version 8.1.1
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'userman');
cot_block($usr['auth_read']);

global $temp;

$sql = $db->query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();

if($update == 'true')
{
	cot_check_xg();

	$ruser['user_text'] = cot_import('rusertext','P','HTM', $cfg['users']['usertextmax']);
	$ruser['user_country'] = cot_import('rusercountry','P','ALP');
	$rtheme = explode(':', cot_import('rusertheme','P','TXT'));
	$ruser['user_theme'] = $rtheme[0];
	$ruser['user_scheme'] = $rtheme[1];
	$ruser['user_lang'] = cot_import('ruserlang','P','ALP');
	$ruser['user_gender'] = cot_import('rusergender','P','ALP');
	$ruser['user_timezone'] = cot_import('rusertimezone','P','TXT');
	$ruser['user_hideemail'] = cot_import('ruserhideemail','P','BOL');
// Пришлось вырезать из модуля PM
if (cot_module_active('pm'))	
	$ruser['user_pmnotify'] = (int)cot_import('um_profilepmnotify','P','BOL');
	// Extra fields
	foreach($cot_extrafields[$db_users] as $exfld)
	{
		$ruser['user_'.$exfld['field_name']] = cot_import_extrafields('ruser'.$exfld['field_name'], $exfld, 'P', $urr['user_'.$exfld['field_name']]);
	}
	$ruser['user_birthdate'] = cot_import_date('ruserbirthdate', false);
	if (!is_null($ruser['user_birthdate']) && $ruser['user_birthdate'] > $sys['now'])
	{
		cot_error('pro_invalidbirthdate', 'ruserbirthdate');
	}

	$roldpass = cot_import('roldpass','P','HTM');
	$rnewpass1 = cot_import('rnewpass1','P','HTM', 32);
	$rnewpass2 = cot_import('rnewpass2','P','HTM', 32);
	$rmailpass = cot_import('rmailpass','P','HTM');
	$ruseremail = cot_import('ruseremail','P','TXT');

	if (!empty($rnewpass1) && !empty($rnewpass2) && !empty($roldpass))
	{
		if ($rnewpass1 != $rnewpass2) cot_error('pro_passdiffer', 'rnewpass2');
		if (mb_strlen($rnewpass1) < 4) cot_error('pro_passtoshort', 'rnewpass1');
		if (cot_hash($roldpass, $urr['user_passsalt'], $urr['user_passfunc']) != $urr['user_password']) cot_error('pro_wrongpass', 'roldpass');

		if (!empty($ruseremail) && !empty($rmailpass) && $cfg['users']['useremailchange'] && $ruseremail != $urr['user_email'])
		{
			cot_error('pro_emailandpass', 'ruseremail');
		}
		if (!cot_error_found())
		{
			$ruserpass = array();
			$ruserpass['user_passsalt'] = cot_unique(16);
			$ruserpass['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
			$ruserpass['user_password'] = cot_hash($rnewpass1, $ruserpass['user_passsalt'], $ruserpass['user_passfunc']);
			$db->update($db_users, $ruserpass, "user_id={$usr['id']}");
			unset($ruserpass);
			cot_message('Updated');
		}
	}
	if (!empty($ruseremail) && (!empty($rmailpass) || $cfg['users']['user_email_noprotection']) && $cfg['users']['useremailchange'] && $ruseremail != $urr['user_email'])
	{
		$sqltmp = $db->query("SELECT COUNT(*) FROM $db_users WHERE user_email='".$db->prep($ruseremail)."'");
		$res = $sqltmp->fetchColumn();

		if (!$cfg['users']['user_email_noprotection'])
		{
			$rmailpass = cot_hash($rmailpass, $urr['user_passsalt'], $urr['user_passfunc']);
			if ($rmailpass != $urr['user_password']) cot_error('pro_wrongpass', 'rmailpass');
		}

		if (!cot_check_email($ruseremail))
			cot_error('aut_emailtooshort', 'ruseremail');
		if ($res > 0) cot_error('aut_emailalreadyindb', 'ruseremail');

		if (!cot_error_found())
		{
			if (!$cfg['users']['user_email_noprotection'])
			{
				$validationkey = md5(microtime());
				$db->update($db_users, array('user_email' => $ruseremail, 'user_lostpass' => $validationkey, 'user_maingrp' => '-1', 'user_sid' => $urr['user_maingrp']), "user_id='".$usr['id']."'");

				$rsubject = $L['aut_mailnoticetitle'];
				$ractivate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$validationkey, '', true);
				$rbody = sprintf($L['aut_emailchange'], $usr['name'], $ractivate);
				$rbody .= "\n\n".$L['aut_contactadmin'];
				cot_mail($ruseremail, $rsubject, $rbody);

				if(!empty($_COOKIE[$sys['site_id']]))
				{
					cot_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
				}

				if (!empty($_SESSION[$sys['site_id']]))
				{
					session_unset();
					session_destroy();
				}
				if (cot_plugin_active('whosonline'))
				{
					$db->delete($db_online, "online_ip='{$usr['ip']}'");
				}
				cot_redirect(cot_url('message', 'msg=102', '', true));
			}
			else
			{
				$db->update($db_users, array('user_email' => $ruseremail), "user_id='".$usr['id']."'");
			}
		}
	}
	if (!cot_error_found())
	{
		$ruser['user_birthdate'] = (is_null($ruser['user_birthdate'])) ? '0000-00-00' : cot_stamp2date($ruser['user_birthdate']);
		$ruser['user_auth'] = '';
		$db->update($db_users, $ruser, "user_id='".$usr['id']."'");
		cot_extrafield_movefiles();
                
                cot_message($L['successupdprof'],'ok');
		cot_redirect(cot_url('admin','m=other&p=userman&a=profile', '', true));
	}
}

$sql = $db->query("SELECT * FROM $db_users WHERE user_id='".$usr['id']."' LIMIT 1");
$urr = $sql->fetch();

$out['subtitle'] = $L['Profile'];
$out['head'] .= $R['code_noindex'];

require_once $cfg['system_dir'] . '/header.php';
require_once cot_incfile('forms');

$protected = !$cfg['users']['useremailchange'] ? array('disabled' => 'disabled') : array();
$profile_form_email = cot_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)
	+ $protected);

$editor_class = $cfg['users']['usertextimg'] ? 'minieditor' : '';

$temp->assign(array(
        'UM_PROFILE_TITLE' => $L['title'],
	'UM_PROFILE_SUBTITLE' => $L['pro_subtitle'],
	'UM_PROFILE_DETAILSLINK' => cot_url('admin','m=other&p=userman&a=edit&id='.$urr['user_id']),
	'UM_PROFILE_EDITLINK' => cot_url('admin','m=other&p=userman&a=edit&id='.$urr['user_id']),
	'UM_PROFILE_FORM_SEND' => cot_url('admin','m=other&p=userman&a=profile&update=true&'.cot_xg()),
	'UM_PROFILE_ID' => $urr['user_id'],
	'UM_PROFILE_NAME' => htmlspecialchars($urr['user_name']),
	'UM_PROFILE_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'UM_PROFILE_GROUPS' => cot_build_um1_groupsms($urr['user_id'], FALSE, $urr['user_maingrp']),
	'UM_PROFILE_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'rusercountry'),
	'UM_PROFILE_TEXT' => cot_textarea('rusertext', $urr['user_text'], 8, 56, array('class' => $editor_class)),
	'UM_PROFILE_EMAIL' => $profile_form_email,
	'UM_PROFILE_EMAILPASS' => cot_inputbox('password', 'rmailpass', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
	'UM_PROFILE_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	'UM_PROFILE_THEME' => cot_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	'UM_PROFILE_LANG' => cot_selectbox_lang($urr['user_lang'], 'ruserlang'),
	'UM_PROFILE_GENDER' => cot_selectbox_gender($urr['user_gender'] ,'rusergender'),
	'UM_PROFILE_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', cot_date('Y', $sys['now']), cot_date('Y', $sys['now']) - 100, false),
	'UM_PROFILE_TIMEZONE' => cot_selectbox_timezone($urr['user_timezone'], 'rusertimezone'),
	'UM_PROFILE_REGDATE' => cot_date('datetime_medium', $urr['user_regdate']),
	'UM_PROFILE_REGDATE_STAMP' => $urr['user_regdate'],
	'UM_PROFILE_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog']),
	'UM_PROFILE_LASTLOG_STAMP' => $urr['user_lastlog'],
	'UM_PROFILE_LOGCOUNT' => $urr['user_logcount'],
	'UM_PROFILE_ADMINRIGHTS' => '',
	'UM_PROFILE_OLDPASS' => cot_inputbox('password', 'roldpass', '', array('size' => 12, 'maxlength' => 32)),
	'UM_PROFILE_NEWPASS1' => cot_inputbox('password', 'rnewpass1', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
	'UM_PROFILE_NEWPASS2' => cot_inputbox('password', 'rnewpass2', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off')),
	'UM_PROFILE_GOBACK' => cot_url('admin','m=other&p=userman'),
	'UM_PROFILE_GOBACK_TEXT' => $L['GoBack']    
));
// Extra fields
foreach($cot_extrafields[$db_users] as $exfld)
{
	$tag = strtoupper($exfld['field_name']);
	$temp->assign(array(
		'UM_PROFILE_'.$tag => cot_build_extrafields('ruser'.$exfld['field_name'], $exfld, $urr['user_'.$exfld['field_name']]),
		'UM_PROFILE_'.$tag.'_TITLE' => isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description']
	));
}
    if(defined('COT_PM')){
    $temp->assign(array(
	'UM_PROFILE_PMNOTIFY' => cot_radiobox($urr['user_pmnotify'], 'um_profilepmnotify', array(1, 0), array($L['Yes'], $L['No'])),
));
    }
// Extra fields
foreach($cot_extrafields[$db_users] as $exfld)
{
	$tag = strtoupper($exfld['field_name']);
	$temp->assign(array(
		'USERS_PROFILE_'.$tag => cot_build_extrafields('ruser'.$exfld['field_name'], $exfld, $urr['user_'.$exfld['field_name']]),
		'USERS_PROFILE_'.$tag.'_TITLE' => isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description']
	));
}

if ($cfg['users']['useremailchange'])
{
	if (!$cfg['users']['user_email_noprotection'])
	{
		$temp->parse('MAIN.USERS_PROFILE_EMAILCHANGE.USERS_PROFILE_EMAILPROTECTION');
	}
	$temp->parse('MAIN.USERS_PROFILE_EMAILCHANGE');
}

$temp->parse('MAIN.UM_PROFILE');

