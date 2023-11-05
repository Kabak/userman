<?php

/**
 * English Language File for User Manager plugin
 * @package userman
 * @version 8.1.1
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/*
 * Config
 */
  $L['cfg_options'] = 'Options';
  $L['cfg_defaultlevel'] = 'Default level';
  $L['cfg_defaultemail'] = 'Default E-mail';
  $L['cfg_defaultname'] = 'Default Username';
  $L['cfg_defaultpass'] = 'Default Password';
  $L['cfg_defaultsign'] = "Sign";
  $L['cfg_defaultsigntext'] = "Regards, \r\n Site admin.";
  $L['cfg_pass'] = '1111';
/*
 * Access till time config
 */
  $L['cfg_tilltimeoptions'] = 'Access for some time options';
  $L['cfg_tilltime_defaultlgroup'] = 'Default Group';
  $L['cfg_tilltime_accessgroup'] = 'Groups for access';
  
  $L['info_desc'] = 'User manager in Admin panel';
  $L['title'] = 'User Manager';
  $L['subtitle'] = 'Everybody learn for something ...';
  $L['userman'] = 'Userman plugin <br />';
  
  $L['um_absent'] = 'absent';
  $L['um_req_extra'] = 'Required extrafields';
  $L['um_delete_sel'] = 'Delete selected ( without confirmation! ) ';
  $L['um_createuser'] = 'Create User';
  $L['um_defaultpass'] = '( Default password: ';

  $L['users_list'] = 'Users list';
  $L['delete'] = 'Delete user';
  $L['deldenied'] = ' You can not delete Superadmin ';
  $L['GoBack'] = 'Go Back';
  $L['GoBackEdit'] = 'Back to user profile';
  $L['yourprofile'] = 'Goto Your Profile';
  $L['site_config'] = 'Goto Site config.';
  $L['user_rights'] = 'Users Rights';
  $L['extra_fields'] = 'Extrafields';
  $L['user'] = 'User : ';
  $L['successcreation'] = ' created successfuly.';
  $L['deleted'] = ' deleted.';
  $L['successupdprof'] = 'Profile updated successfully';
  $L['access_user_edit'] = 'To change groups access for user : ';
  $L['access_go_to'] = ' , goto Userman temporary access editor -> ';  
  /*
   * Access till time
   */
  $L['accesstilltime_text'] = 'Edit user temporary groups access';
  $L['accesstilltime_title'] = 'Manage user groups access for a period of time';
  $L['access_start'] = 'Start access date';
  $L['access_stop'] = 'End access date';
  $L['access_groups'] = 'Set access to groups :';
  $L['access_default'] = 'Default access to groups :';
  $L['active'] = 'Access active';
  $L['access_start_reason_text'] = 'Reason user get access';
  $L['access_stop_reason_text'] = "Reason user lost access";
  $L['user_access'] = 'Temporary access to additional groups for user : ';  
  $L['access_set_success'] = ' was set.';
  $L['access_updated'] = ' has been updated.';
  $L['access_set'] = 'Edit user temporary groups access';
  $L['access_title'] = 'TGA';
  /*
   * Help
   */
  $L['userman_help_main'] = 'Для редактирования профиля пользователя нажмите на его имя в списке имён.<br /><br /> Для поиска пользователя по E-mail введите E-mail в поле <b>флильтра</b> для поиска.<br><br><b>TGA</b> - Временный доступ к группам ( Temporary groups access )<br /><br /> Если поле <b>TGA</b> пустое, то временный доступ пользователю не установлен, и в БД нет записи для пользователя. Если в поле есть иконка <img class="icon" src="plugins/userman/img/access.png" alt="запись активна" title="запись активна" />, то пользователь есть в базе для временного доступа к группам и запись активна.
  Если в поле есть иконка <img class="icon" src="plugins/userman/img/access_off.png" alt="запись отключена" title="запись отключена" />, то пользователь есть в базе для временного доступа к группам, но запись не активна - временный доступ пользователю отключён.<br /><br /> Для редактирования доступа пользователя к дополнительным группам нажмите на иконку в поле <b>TGA</b>.<br /><br />
  Для создания пользователю временного доступа к группам, зайдите в профиль пользователя кликнув по имени пользователя в списке, и там воспользуйтесь кнопкой - "Изменить временный доступ к группам".';
  
  $L['userman_help_access'] = 'Временный доступ к группам удобен, когда, например, у вас на сайте орагнизован платный доступ к неторорым разделам и вы открываете доступ пользователям на время, после оплаты.<br />
  Вы можете установить временный доступ сразу в несколько групп. Установив чекбоксы в группах где пользователь уже состоит,<br />добавив группы куда вы расширяете доступ пользователю. Основные группы на этой странице недоступны для изменения.<br /> 
 <br /><b>Например:</b> Пользователь состоит в группе members и вы хотите дать ему доступ на время в группу moderators. Вы устанавливаете на этой странице чекбокс <b>moderators</b>. Устанавите дату, когда вы хотите чтобы плагин отключил доступ пользователя в
 группу - moderatos. Можете написать причину подключения в группу moderators, - это просто справка для админа.<br /><br /> Для активации профиля необходимо установить профиль в активное состояние чекбоксом <b><i>Профиль активен</i></b>.<br />
 <br />Для удаления профиля временного доступа просто очистите все чекбоксы групп и нажмите обновить. Пользователю будут возвращены привелегии в соответствии с установленными на вашем сайте и данные с временным доступом будут удалены из базы данных.
 ( плагин хранит доступные пользователю группы по умолчанию и возвращает их после удаления временного доступа к группам или удалении профиля для временного дуступа ).<br />
 <br />Если вы просто хотите отключить доступ, но не удалять профиль пользователя из базы, то установите чекбокс <b><i>Профиль активен</i></b> в состояние <b><i>НЕТ</i></b>.<br /><br /> Если доступ был автоматически отключён плагином по истечении установленного времени,
 то запись из базы не удаляется, а просто отключается переводом <b><i>Профиль активен</i></b> в состояние <b><i>НЕТ</i></b>.<br /><br /><b>Внимание !</b><br /> Если временный досту пользователя к группам активен - <img class="icon" src="plugins/userman/img/access.png" alt="запись активна" title="запись активна" />, редактирование доступа к группам нужно изменять с этой страницы
 - страницы временного доступа к группам. Вы не сможете изменить доступ пользователя к группам из других страниц.<br />Если профиль не активен - <img class="icon" src="plugins/userman/img/access_off.png" alt="запись отключена" title="запись отключена" /> или отсутствует в базе временного доступа, вы можете изменять права доступа к группам с любой страницы сайта, позволяющей редактровать доступ к группам.';

 
