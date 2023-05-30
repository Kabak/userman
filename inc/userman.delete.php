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

// Проверяем чтобы не удалили самого главного админа   
if ($id != 1){
// Получаем имя удаляемого для сообщения  Пользователь с именем - ххх   удалён.    
		$u = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1")->fetch();
		$name = $u['user_name'];
		$sql = $db->delete($db_users, "user_id=$id");
		$sql = $db->delete($db_groups_users, "gru_userid=$id");
		
		foreach($cot_extrafields[$db_users] as $exfld)
		{
			cot_extrafield_unlinkfiles($u['user_'.$exfld['field_name']], $exfld);
		}

		if (cot_module_active('pfs') && cot_import('ruserdelpfs','P','BOL'))
		{
			require_once cot_incfile('pfs', 'module');
			cot_pfs_deleteall($id);
		}
// Update user temporary access DB		
		include ('userman.users.edit.update.delete.php');		
		
		cot_message(um_build_string($L['user'],$name,$L['deleted']),'warning'); 		
}
else{
                cot_error($L['deldenied']);   
}
		cot_redirect(cot_url('admin', 'm=other&p=userman', '', true));
