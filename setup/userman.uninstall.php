<?php

/**
 * Set all extended users rights to default state before delete plugin
 *
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Подключаем, если есть файл  в подкаталоге плагина с функциями вынесеными в отдельный файл functions.имяплагина.php
require_once cot_incfile('userman', 'plug');

global $db, $db_config, $db_userman;

$u_list = $db->query("SELECT * FROM $db_userman ")->fetchAll();

foreach ($u_list as $u_data )
{
    // Устанавливаем пользователям уровень по умолчанию для этого пользователя в основных базах групп.
    um_gropus_idu($u_data['user_id'], um_string_to_massive($u_data['groups_default']));
}

$db->query("DROP TABLE IF EXISTS $db_userman");
