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

// Проверяем чтобы не удалили самого главного админа   
if ($id > 1)
{
	// Получаем имя удаляемого для сообщения  Пользователь с именем - ххх   удалён.    
	$name = um_delete_user( $id );
	// Update user temporary access DB		
	um_delete_user_access( $id );

	cot_message(um_build_string($L['user'],$name,$L['deleted']),'warning'); 		
}
else
{
    cot_error($L['deldenied']);   
}
	
cot_redirect(cot_url('admin', 'm=other&p=userman', '', true));
