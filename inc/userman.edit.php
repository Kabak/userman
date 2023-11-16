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

// Подключаем чтобы cot_generate_usertags корректно отрабатывал
require_once cot_incfile('users', 'module');
require_once cot_incfile('pm', 'module');
//Подключаем чтобы строковые переменные из lang файла были доступны как переменные
require_once cot_langfile('userman', 'plug');
// Подключаем, если есть файл  в подкаталоге плагина с функциями вынесеными в отдельный файл functions.имяплагина.php
require_once cot_incfile('userman', 'plug');
//Подключаем файл с ресурсами
require_once cot_incfile('userman', 'plug', 'resources');

global $temp;

$sql = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();
$sql1 = $db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$id and gru_groupid=".COT_GROUP_SUPERADMINS);
$sys['edited_istopadmin'] = ($sql1->rowCount()>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = cot_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];    

if( $update == 'true')
{
   	cot_check_xg();

	$userdelete = cot_import('um_edit_userdelete','P','BOL');
	// Чтобы нельзя было удалить главного админа
	if ($userdelete && $id != 1)
	{
		// Avatar & photo delete
		if ( cot_plugin_active('userimages') )
		{
			$userimages = cot_userimages_config_get();
			foreach ($userimages as $code => $settings)
			{
				$sql = Cot::$db->query("SELECT user_" . Cot::$db->prep($code) . " FROM ".Cot::$db->users." WHERE user_id=" . $id);
				if ($image = $sql->fetchColumn())
				{
					if (file_exists($image))
					{
						unlink($image);
					}
				}
			}			
		}

		// Получаем имя удаляемого для сообщения  Пользователь с именем - ххх   удалён. 	    
		$u = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1")->fetch();
		$name = $u['user_name'];
		
		$sql = $db->delete($db_users, "user_id=$id");
		$sql = $db->delete($db_groups_users, "gru_userid=$id");

		foreach($cot_extrafields[$db_users] as $exfld)
		{
			cot_extrafield_unlinkfiles($urr['user_'.$exfld['field_name']], $exfld);
		}

		if (cot_module_active('pfs') && cot_import('um_edit_userdelpfs','P','BOL'))
		{
			require_once cot_incfile('pfs', 'module');
			cot_pfs_deleteall($id);
		}

		// Update user temporary access DB		
		include cot_incfile('userman', 'plug','users.edit.update.delete');
	
		
		cot_log("Deleted user #".$id,'adm');
                cot_message(um_build_string($L['user'],$name,$L['deleted']),'warning');
                cot_redirect(cot_url('admin', 'm=other&p=userman','', true));
	}
	else if ($userdelete && $id == 1)
	{
	    cot_error($L['deldenied']);   
	}
	  
	if ( cot_plugin_active('userimages') )
	{
		require_once cot_incfile('userimages', 'plug');
		require_once cot_incfile('uploads');
		cot_userimages_process_uploads($id);
	}

	$euser['user_name'] = cot_import('um_edit_username','P','TXT');
	$euser['user_maingrp'] = cot_import('um_usermaingrp','P','INT');
	$euser['user_banexpire'] = cot_import('um_edit_userbanexpire','P','INT');
	$euser['user_country'] = cot_import('um_edit_usercountry','P','ALP');
	$euser['user_text'] = cot_import('um_edit_sign','P','HTM');
	$euser['user_email'] = cot_import('um_edit_useremail','P','TXT');
	$euser['user_hideemail'] = cot_import('um_edit_userhideemail','P','INT');
	$euser['user_theme'] = cot_import('um_edit_usertheme','P','TXT');
	$euser['user_lang'] = cot_import('um_edit_userlang','P','ALP');
	$euser['user_gender'] = cot_import('um_edit_usergender','P','TXT');
	
	// Пришлось вырезать из модуля PM
	if (cot_module_active('pm'))
	$euser['user_pmnotify'] = (int)cot_import('um_userpmnotify','P','BOL');

	$euser['user_birthdate'] = cot_import_date('um_edit_userbirthdate', false);		
	
	if (!is_null($euser['user_birthdate']) && $euser['user_birthdate'] > $sys['now'])
	{
		cot_error('pro_invalidbirthdate', 'um_edit_userbirthdate');
	}

	$euser['user_timezone'] = cot_import('um_edit_usertimezone','P','TXT');
	$eusernewpass = cot_import('um_edit_usernewpass','P','HTM', 32);

	// Extra fields
	foreach($cot_extrafields[$db_users] as $exfld)
	{
		$euser['user_'.$exfld['field_name']] = cot_import_extrafields('ruser'.$exfld['field_name'], $exfld, 'P', $urr['user_'.$exfld['field_name']]);
	}

	$eusergroupsms = cot_import('um_usergroupsms', 'P', 'ARR');

	if (mb_strlen($euser['user_name']) < 2 || mb_strpos($euser['user_name'], ',') !== false || mb_strpos($euser['user_name'], "'") !== false)
	{
		cot_error('aut_usernametooshort', 'um_edit_username');
	}
	if ($euser['user_name'] != $urr['user_name'] && $db->query("SELECT COUNT(*) FROM $db_users WHERE user_name = ?", array($euser['user_name']))->fetchColumn() > 0)
	{
		cot_error('aut_usernamealreadyindb', 'um_edit_username');
	}
	if (!cot_check_email($euser['user_email']))
	{
		cot_error('aut_emailtooshort', 'um_edit_useremail');
	}
	if ($euser['user_email'] != $urr['user_email'] && $db->query("SELECT COUNT(*) FROM $db_users WHERE user_email = ?", array($euser['user_email']))->fetchColumn() > 0)
	{
		cot_error('aut_emailalreadyindb', 'um_edit_useremail');
	}
	if (!empty($eusernewpass) && mb_strlen($eusernewpass) < 4)
	{
		cot_error('aut_passwordtooshort', 'um_edit_usernewpass');
	}

	if (!cot_error_found())
	{
		if (!empty($eusernewpass))
		{
			$euser['user_passsalt'] = cot_unique(16);
			$euser['user_passfunc'] = empty($cfg['hashfunc']) ? 'sha256' : $cfg['hashfunc'];
			$euser['user_password'] = cot_hash($eusernewpass, $euser['user_passsalt'], $euser['user_passfunc']);
		}

		$euser['user_name'] = ($euser['user_name']=='') ? $urr['user_name'] : $euser['user_name'];

		if ($euser['user_name'] != $urr['user_name'])
		{
			$newname = $euser['user_name'];
			$oldname = $urr['user_name'];
			if (cot_module_active('forums'))
			{
				require_once cot_incfile('forums', 'module');
				$db->update($db_forum_topics, array('ft_lastpostername' => $newname), 'ft_lastpostername = ?', array($oldname));
				$db->update($db_forum_topics, array('ft_firstpostername' => $newname), 'ft_firstpostername = ?', array($oldname));
				$db->update($db_forum_posts, array('fp_postername' => $newname), 'fp_postername = ?', array($oldname));
				$db->update($db_forum_stats, array('fs_lt_postername' => $newname), 'fs_lt_postername = ?', array($oldname));
			}
			if (cot_module_active('page'))
			{
				require_once cot_incfile('page', 'module');
				$db->update($db_pages, array('page_author' => $newname), 'page_author = ?', array($oldname));
			}
			if (cot_plugin_active('comments'))
			{
				require_once cot_incfile('comments', 'plug');
				$db->update($db_com, array('com_author' => $newname), 'com_author = ?', array($oldname));
			}
			if (cot_module_active('pm'))
			{
				require_once cot_incfile('pm', 'module');
				$db->update($db_pm, array('pm_fromuser' => $newname), 'pm_fromuser = ?', array($oldname));
			}
			if (cot_plugin_active('whosonline'))
			{
				$db->update($db_online, array('online_name' => $newname), 'online_name = ?', array($oldname));
			}
		}

		$euser['user_auth'] = '';

		$sql = $db->update($db_users, $euser, 'user_id='.$id);
		cot_extrafield_movefiles();

		$euser['user_maingrp'] = ($euser['user_maingrp'] < COT_GROUP_MEMBERS && $id==1) ? COT_GROUP_SUPERADMINS : $euser['user_maingrp'];

		if (!$eusergroupsms[$euser['user_maingrp']])
		{
			$eusergroupsms[$euser['user_maingrp']] = 1;
		}
		$db->update($db_users, array('user_maingrp' => $euser['user_maingrp']), 'user_id='.$id);

		foreach($cot_groups as $k => $i)
		{
			if (isset($eusergroupsms[$k]))
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

		if ($euser['user_maingrp'] == COT_GROUP_MEMBERS && $urr['user_maingrp'] == COT_GROUP_INACTIVE)
		{
			$rsubject = $L['useed_accountactivated'];
			$rbody = $L['Hi']." ".$urr['user_name'].",\n\n";
			$rbody .= $L['useed_email'];
			$rbody .= $L['auth_contactadmin'];
			cot_mail($urr['user_email'], $rsubject, $rbody);
		}

		// Update user temporary access DB		
		include cot_incfile('userman', 'plug','users.edit.update.done');
		// Если пользователь имеет временны доступ то не выводить сообщение что данные пользователя успешно обновлены		
		if( $user_found['active'] != true  )
		{		
    		cot_auth_clear($id);
			cot_log("Edited user #".$id,'adm');
			cot_message($L['successupdprof'],'ok');
		}
		cot_redirect(cot_url('admin','m=other&p=userman&a=edit&id='.$id, '', true));
	}
	else
	{
		cot_redirect(cot_url('admin','m=other&p=userman&a=edit&id='.$id, '', true));
	}
}

$delete_pfs = cot_module_active('pfs') ? cot_checkbox(false, 'um_edit_userdelpfs', $L['PFS']) : '';
	
if ( cot_plugin_active('userimages') )
{
	require_once cot_incfile('userimages', 'plug');
	require_once cot_incfile('userimages', 'plug', 'resources');
	$temp->assign( cot_um_userimages_tags($urr, 'UM_EDIT_'));
}

    $temp->assign(array(
    'UM_EDIT_TITLE' => $L['title'],
//    'UM_EDIT_DETAILSLINK' => cot_url('admin','m=other&p=userman&a=edit&id='.$urr['user_id']),
//	'UM_EDIT_EDITLINK' => cot_url('admin','m=other&p=userman&a=edit&id='.$urr['user_id']),
//	'UM_EDIT_SUBTITLE' => $L['useed_subtitle'],
	'UM_EDIT_SEND' => cot_url('admin','m=other&p=userman&a=edit&update=true&'.cot_xg().'&id='.$urr['user_id']),
	'UM_EDIT_ID' => $urr['user_id'],
	'UM_EDIT_NAME' => cot_inputbox('text', 'um_edit_username', $urr['user_name'], array('size' => 32, 'maxlength' => 100) + $protected),
//	'UM_EDIT_ACTIVE' => $user_form_active,
//	'UM_EDIT_BANNED' => $user_form_banned,
	'UM_EDIT_THEME' => cot_selectbox_theme($urr['user_theme'], $urr['user_scheme'], 'rusertheme'),
	'UM_EDIT_LANG' => cot_selectbox_lang($urr['user_lang'], 'ruserlang'),
	'UM_EDIT_NEWPASS' => cot_inputbox('password', 'um_edit_usernewpass', '', array('size' => 12, 'maxlength' => 32, 'autocomplete' => 'off') + $protected),
	'UM_EDIT_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'UM_EDIT_GROUPS' => cot_build_um1_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	'UM_EDIT_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'um_edit_usercountry'),
	'UM_EDIT_EMAIL' => cot_inputbox('text', 'um_edit_useremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)),
	'UM_EDIT_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'um_edit_userhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	'UM_EDIT_TEXT' => cot_textarea('um_edit_sign', $urr['user_text'], 4, 56, array('class' => $editor_class)),
	'UM_EDIT_GENDER' => cot_selectbox_gender($urr['user_gender'], 'um_edit_usergender'),
	'UM_EDIT_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'um_edit_userbirthdate', cot_date('Y', $sys['now']), cot_date('Y', $sys['now']) - 100, false),
	'UM_EDIT_TIMEZONE' => cot_selectbox_timezone($urr['user_timezone'], 'um_edit_usertimezone'),
	'UM_EDIT_REGDATE' => cot_date('datetime_medium', $urr['user_regdate']),
//	'UM_EDIT_REGDATE_STAMP' => $urr['user_regdate'],
	'UM_EDIT_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog']),
//	'UM_EDIT_LASTLOG_STAMP' => $urr['user_lastlog'],
	'UM_EDIT_LOGCOUNT' => $urr['user_logcount'],
	'UM_EDIT_LASTIP' => cot_build_ipsearch($urr['user_lastip']),
	'UM_EDIT_DELETE' => ($sys['user_istopadmin']) ? cot_radiobox(0, 'um_edit_userdelete', array(1, 0), array($L['Yes'], $L['No'])) . $delete_pfs : $L['na'],
	'UM_EDIT_GOBACK' => cot_url('admin','m=other&p=userman'),
	'UM_EDIT_GOBACK_TEXT' => $L['GoBack'],
	'UM_EDIT_ACCESS' => cot_url('admin','m=other&p=userman&a=access&id='.$urr['user_id']),
	'UM_EDIT_ACCESS_TEXT' => $L['accesstilltime_text'],
	));
    
// Extra fields
foreach($cot_extrafields[$db_users] as $exfld)
{
	$tag = strtoupper($exfld['field_name']);
	$temp->assign(array(
		'UM_EDIT_EXTRAFLD' => cot_build_extrafields('ruser'.$exfld['field_name'],  $exfld, $urr['user_'.$exfld['field_name']]),
		'UM_EDIT_EXTRAFLD_TITLE' => isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description']
	));
	$temp->parse('MAIN.UM_EDIT.EXTRAFLD');
}

if (cot_module_active('pm'))
{
    $temp->assign(array(
	'UM_EDIT_PMNOTIFY' => cot_radiobox($urr['user_pmnotify'], 'um_userpmnotify', array(1, 0), array($L['Yes'], $L['No'])),
	));	
}

    $temp->parse('MAIN.UM_EDIT');