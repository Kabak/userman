<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.auth.check.done
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

$id = $row['user_id'];
$sql = $db->query("SELECT * FROM $db_userman WHERE user_id=$id LIMIT 1");
$user_found = $sql->fetch();
// Проверяем есть ли пользователь в базе с поднятием уровня на время
if ( $user_found != NULL ){

$now = time();
$expired = cot_date2stamp($user_found['stop_date']);
//$expired = 0;
// Если время активации пользователя истекло
    if( $now >= $expired ){
// Если активация пользователя ещё не отключёна
// Выключаем пользователю доступ , потому что его время истекло
        if( $user_found['active'] == true  ){
            $user_found['active'] = false;
            $db->update($db_userman, $user_found, 'user_id='.$id);
// Устанавливаем пользователю уровень по умолчанию для этого пользователя в основных базах групп.
	    $default_groups = um_string_to_massive($user_found['groups_default']);
	    um_gropus_idu($id, $default_groups);
        }
// Если активация ползователя уже отключена
//        else{
//        }
    }
// Если время активации не истекло    
//    else{
//    }
}
// Пользователь не найден в списках с открытым доступом на время
//else{
//}


