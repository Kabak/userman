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

um_delete_user_access( $id );
