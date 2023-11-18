<?php

/**
 * Usermanager Resources
 * 
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Requirements
require_once cot_langfile('users', 'core');
require_once cot_incfile('users', 'module', 'resources');

$R['access'] = '<img class="icon" src="plugins/userman/img/access.png" alt="{$alt}" title="{$title}" />';
$R['access_off'] ='<img class="icon" src="plugins/userman/img/access_off.png" alt="{$alt}" title="{$title}" />';
$R['del_icon'] = '<img class="icon" src="plugins/userman/img/delete.png" alt="{$alt}" title="{$title}" />';
