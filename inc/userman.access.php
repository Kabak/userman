<?php

/* @var $db CotDB */
/* @var $cache Cache */
/* @var $t Xtemplate */

/**
 * @package userman
 * @version 8.1.0
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'userman');
cot_block($usr['auth_read']);

global $temp;

    $member = array();

    $sql = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1");
    cot_die($sql->rowCount() == 0);
    $a_user = $sql->fetch();
    $userid = $a_user['user_id'];
// Проверяем есть ли пользователь в нашей базе с расширеными полномочиями по доступу
    $sql1 = $db->query("SELECT * FROM $db_userman WHERE user_id=$id LIMIT 1");
// Если есть, то читаем куда именно ему расширены полномочия
    if( $sql1->rowCount() !=0 ){
	$act_user = $sql1->fetch();
// Конвертируем из строки 4,5,6,0 в массив групп  последний ноль не участвует в конвертации
	$access_groups = explode(',', $act_user['groups_access']);
	foreach($access_groups as $n ){
	$um_usergroupsms[$n] = TRUE;
// Если пользователь в базе временного доступа, то генерируем список групп куда ему есть доступ
	$member[$n] = TRUE;
	}
	$no_access_record = FALSE;
    }
    else{
//  Генерируем строку для базы  из групп которые доступны пользователю по умолчанию    
    $act_user['groups_default'] = um_gen_user_defgroups($userid);
// Устанавливаем признак того, что записи по текущему ID потзователя в базе данных доступа на время нет
    $no_access_record = TRUE;
    }
if( $update == 'true'){
    $act_user['active'] = cot_import('um_access_active','P','BOL');
    $act_user['start_date'] = cot_import_date('um_access_start', false);
    $act_user['start_date'] = (is_null($act_user['start_date'])) ? '0000-00-00' : cot_stamp2date($act_user['start_date']);  
    $act_user['start_reason'] = cot_import('um_access_start_reason','P','TXT'); 
    $act_user['stop_date'] = cot_import_date('um_access_stop', false);
    $act_user['stop_date'] = (is_null($act_user['stop_date'])) ? '0000-00-00' : cot_stamp2date($act_user['stop_date']);
    $act_user['stop_reason'] = cot_import('um_access_stop_reason','P','TXT'); 
    $act_user['user_id'] = $a_user['user_id'];
    $act_user['groups_access'] = '';
    $gpoups_string = '';
    $tempgroups = cot_import('um_group_time_accesssms', 'P', 'ARR');
    $member = cot_import('um_group_time_accesssms', 'P', 'ARR');
    
// Удаляем запись в бызе, если есть запись для конкретного ID
    if( $tempgroups == null && $no_access_record == FALSE){
	$access_groups = explode(',', $act_user['groups_default']);
	foreach($access_groups as $n ){
	    $um_usergroupsms[$n] = TRUE;	
	    $member[$n] = TRUE;
	}
	um_gropus_idu($id, $member, $tempgroups);	
	$sql2 = $db->delete($db_userman, 'user_id='.$userid );
	
        cot_message(um_build_string($L['user_access'],$a_user['user_name'],$L['deleted']),'warning'); 
	cot_redirect(cot_url('admin','m=other&p=userman&a=edit&id='.$userid, '', true));	
    }
// Если админ не установил ни одной группы пользователю + пользователя нет в базе доступа на время 
// и админ нажал кнопку обновить
    else if( $tempgroups != null){
    foreach($tempgroups as $number => $val){
	$gpoups_string .= $number.',';
    }
    $act_user['groups_access'] = um_cut_string($gpoups_string);
    }
    
    $sql2 = $db->query("SELECT * FROM $db_userman WHERE user_id=$userid LIMIT 1");
// Вставляем запись в базу, если хоть одна группа выбрана как устанавливаемая на время
    if($sql2->rowCount() == 0 && $gpoups_string != null){
 		$sql2 = $db->insert($db_userman, $act_user);
		um_gropus_idu($id, $member, $tempgroups, true);
	cot_message(um_build_string($L['user_access'],$a_user['user_name'],$L['access_set_success']),'ok');		
    }
    else{
 		$sql2 = $db->update($db_userman, $act_user, 'user_id='.$userid );
		um_gropus_idu($id, $member, $tempgroups, true);
        cot_message(um_build_string($L['user_access'],$a_user['user_name'],$L['access_updated']),'ok');		
    }
}
// Устанавливаем даты в шаблоне в соответствии с БД временного доступа
	if($no_access_record == FALSE ){
	    $start_date = cot_selectbox_date(cot_date2stamp($act_user['start_date']), 'short', 'um_access_start', cot_date('Y', $sys['now']) +10, cot_date('Y', $sys['now']) - 5, false);
	    $stop_date =  cot_selectbox_date(cot_date2stamp($act_user['stop_date']), 'short', 'um_access_stop', cot_date('Y', $sys['now']) + 10, cot_date('Y', $sys['now']) - 5, false);	    
	}
// Устанавливаем даты в шаблоне в соответствии с текущей датой (  для удобства )
	else{
	    $start_date = cot_selectbox_date(cot_date2stamp($sys['day']), 'short', 'um_access_start', cot_date('Y', $sys['now']) + 10, cot_date('Y', $sys['now']) - 5, false);
	    $stop_date =  cot_selectbox_date(cot_date2stamp($sys['day']), 'short', 'um_access_stop', cot_date('Y', $sys['now']) + 10, cot_date('Y', $sys['now']) - 5, false);   
	}

	isset($act_user['start_reason']) ? $act_user['start_reason'] : $act_user['start_reason'] = "";
	isset($act_user['stop_reason']) ? $act_user['stop_reason'] : $act_user['stop_reason'] = "";
	isset($act_user['active']) ? $act_user['active'] : $act_user['active'] = false;

    $temp->assign(array(
	'UM_TIME_ACCESS_TITLE' => $L['accesstilltime_title'],
	'UM_TIME_ACCESS_SEND' => cot_url('admin','m=other&p=userman&a=access&update=true&id='.$a_user['user_id']),
	'UM_ACCESS_ID' => $a_user['user_id'], 
	'UM_ACCESS_NAME' => $a_user['user_name'],
	'UM_ACCESS_EMAIL' => $a_user['user_email'],
	'UM_ACCESS_GROUPS' => cot_build_um2_groupsms($a_user['user_id'], $usr['isadmin'], $a_user['user_maingrp']),
	'UM_ACCESS_START_TEXT' => $L['access_start'],
	'UM_ACCESS_START' => $start_date,
	'UM_ACCESS_START_REASON_TEXT' => $L['access_start_reason_text'],
	'UM_ACCESS_START_REASON' => cot_textarea('um_access_start_reason', $act_user['start_reason'], 4, 56, array('class' => $editor_class)),
	'UM_ACCESS_STOP_TEXT' => $L['access_stop'],
	'UM_ACCESS_STOP' => $stop_date,
	'UM_ACCESS_STOP_REASON_TEXT' => $L['access_stop_reason_text'],	
	'UM_ACCESS_STOP_REASON' => cot_textarea('um_access_stop_reason', $act_user['stop_reason'], 4, 56, array('class' => $editor_class)),
	'UM_ACCESS_ACTIVE_TEXT' => $L['active'],
	'UM_ACCESS_ACTIVE' => cot_radiobox($act_user['active'], 'um_access_active', array(1, 0), array($L['Yes'], $L['No'])),
	'UM_ACCESS_LASTDATE' => cot_date('datetime_medium', $a_user['user_lastlog']),
	'UM_ACCESS_GOBACK' => cot_url('admin','m=other&p=userman&a=edit&id='.$a_user['user_id'].'&u='.$a_user['user_name']),
	'UM_ACCESS_GOBACK_TEXT' => $L['GoBackEdit'],
	'UM_ACCESS_GOBACK_UMTOP' => cot_url('admin','m=other&p=userman'),
	'UM_ACCESS_GOBACK_UMTOP_TEXT' => $L['GoBack'],
    ));
$L['userman_help'] =  $L['userman_help_access'];
    $temp->parse('MAIN.UM_TIME_ACCESS'); 