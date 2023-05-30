<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
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
if ( $user_found ){
	$sql1 = $db->delete($db_userman, 'user_id='.$id );
}    


