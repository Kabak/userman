<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.done
[END_COT_EXT]
==================== */

/* @var $db CotDB */
/* @var $cache Cache */
/* @var $t Xtemplate */

/**
 * @package userman
 * @version 8.1.0
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license 
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userman', 'plug');
//Подключаем чтобы строковые переменные из lang файла были доступны как переменные
require_once cot_langfile('userman', 'plug');

//$id = $ruser['user_id'];
$sql = $db->query("SELECT * FROM $db_userman WHERE user_id=$id LIMIT 1");
$user_found = $sql->fetch();
// Проверяем есть ли пользователь в базе с поднятием уровня на время
if ( $user_found != null ){
        $user_found['groups_default'] = um_gen_user_defgroups($id);	
// Если пользователь был удалён вообще из баз данных, то удаляем его и  из базы временного доступа    
    if ($user_found['groups_default'] == null ){
	$sql1 = $db->delete($db_userman, 'user_id='.$id );
    }
// Если не был удалён, обновляем данные в базе временного доступа
    else{
// Если запись в базе доступа на время активна, то делаем операцию и с вновь установленными группами 
// по умолчанию для пользователя и теми что доступны  на время	
	if( $user_found['active'] == true  ){
	    $user_found['groups_access'] = um_gen_user_access_groups($id);
	    $member = um_string_to_massive($user_found['groups_default']);
	    $tempgroups = um_string_to_massive($user_found['groups_access']);
//	    um_gropus_idu($id, $member, $tempgroups, true );
	  um_gropus_idu($id, $tempgroups);
	$message = $L['userman'];
	$message .= um_build_string($L['access_user_edit'],$urr['user_name'],$L['access_go_to']);
	$message .= cot_rc_link(cot_url('admin', 'm=other&p=userman&a=access&id='.$id),$urr['user_name']);
	cot_message($message,'warning');	    
	}
//	else{
//	}	
	$sql1 = $db->update($db_userman, $user_found, 'user_id='.$id );
    }
//	$message = $L['userman'];
//	$message .= um_build_string($L['user_access'],$urr['user_name'],$L['access_updated']);
//	cot_message($message,'warning');	
}


