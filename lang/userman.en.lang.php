<?php

/**
 * English Language File for User Manager plugin
 * @package userman
 * @version 8.1.0
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
  
  $L['users_list'] = 'Users list';
  $L['createuser'] = 'Create User';
  $L['um_defaultpass'] = '( Default password: ';
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
  $L['userman_help_main'] = 'Warning! Be careful. <br />If you press red icon - Delete behind the user, user will be deleted without additional questions.<br />';
  $L['userman_help_access'] = 'Доступ к группам на время удобен, когда, например, у вас на сайте орагнизован платный доступ к неторорым разделам и вы открываете доступ пользователям на время, после оплаты.<br />
       Вы можете установить временный доступ сразу в несколько групп. Установив чекбоксы в группах где пользователь уже состоит,<br />добавив группы куда вы расширяете доступ пользователю. Основные группы в этом разделе недоступны для изменения.<br /> 
      <br /><b>Например:</b> Пользователь состоит в группе members и вы хотите дать ему доступ на время в группу moderators. Вы устанавливаете на этой странице чекбоксы members и moderators. Устанавите дату, когда вы хотите чтобы плагин отключил доступ пользователя в
      дополнительную группу - moderatos. Можете написать причину подключения в группу moderators ( это для админа просто справка ).<br /><br /> Для активации профиля необходимо установить профиль в активное состояние чекбоксом <b><i>Профиль активен</i></b>.<br />
      <br />Для удаления профиля временного доступа просто очистите все чекбоксы групп и нажмите обновить. Пользователю будут возвращены привелегии в соответствии с установленными на вашем сайте и данные с временным доступом будут удалены из базы данных.
      ( плагин хранит их отдельно и возвращает после удаления доступа к группам на время ).<br />
      <br />Если вы просто хотите отключить доступ, но не удалять профиль пользователя из базы, то установите чекбокс <b><i>Профиль активен</i></b> в состояние <b><i>НЕТ</i></b>.<br /><br /> Если доступ был автоматически отключён плагином по истечении установленного времени,
      то запись из базы не удаляется, а просто отключается переводом <b><i>Профиль активен</i></b> в состояние <b><i>НЕТ</i></b>.';

 
