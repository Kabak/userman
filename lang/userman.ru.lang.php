<?php

/**
 * Russian Language File for User Manager plugin
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/*
 * Config
 */
  $L['cfg_options'] = 'Настройки';
  $L['cfg_defaultlevel'] = 'Уровень по умолчанию';
  $L['cfg_defaultemail'] = 'E-mail по умолчанию';
  $L['cfg_defaultname'] = 'Имя пользователя по умолчанию';
  $L['cfg_defaultpass'] = 'Пароль по умолчанию';
  $L['cfg_defaultsign'] = "Подпись";
  $L['cfg_defaultsigntext'] = "С уважением, \r\n Администрация сайта";
  $L['cfg_pass'] = '1111';
/*
 * Access till time config
 */  
  $L['cfg_tilltimeoptions'] = 'Настройки';
  $L['cfg_tilltime_defaultlgroup'] = 'Группа по умолчанию';
  $L['cfg_tilltime_accessgroup'] = 'Группы открытые для доступа';
  
  $L['info_desc'] = 'Управление пользователями в Админ панели';
  $L['title'] = 'Менеджер пользователей';
  $L['subtitle'] = 'Мы все чему нибудь учились ...';
  $L['userman'] = 'Userman plugin <br />';
	
  $L['um_absent'] = 'отсутствуют';
  $L['um_req_extra'] = 'Обязательные экстрaполя';
  $L['um_delete_sel'] = 'Удалить выбранных ( без подтверждения! )';
  $L['um_createuser'] = 'Создать пользователя';
  $L['um_defaultpass'] = '( пароль по умолчанию: ';
  $L['um_found'] = 'Найдено ';
  $L['um_users'] = ' пользователей';
  $L['um_fpc'] = 'Форум / Страницы / Коментарии';

  $L['um_request_error'] = 'Ошибка в параметрах запроса';

  $L['users_list'] = 'Список пользователей';
  $L['delete'] = 'Удалить пользователя';
  $L['deldenied'] = ' Вы не можете удалить главного Админа ';
  $L['GoBack'] = 'Вернуться';
  $L['GoBackEdit'] = 'Вернуться к профилю пользователя';
  $L['yourprofile'] = 'Зайти в ваш профиль';
  $L['site_config'] = 'Конфигурация сайта';
  $L['user_rights'] = 'Права пользователей';
  $L['extra_fields'] = 'Дополнительные поля';
  $L['user'] = 'Пользователь : ';
  $L['successcreation'] = ' успешно создан.';
  $L['deleted'] = ' удалён.';
  $L['successupdprof'] = 'Профиль успешно обновён';
  $L['access_user_edit'] = 'Для изменения групп доступа пользователю : ';
  $L['access_go_to'] = ' , пройдите в редактор групп временного доступа -> ';
  /*
   * Access till time
   */
  $L['accesstilltime_text'] = 'Изменить временный доступ к группам';
  $L['accesstilltime_title'] = 'Управление временным доступом пользователя к группам';
  $L['access_start'] = 'Дата начала доступа';
  $L['access_stop'] = 'Дата окончания доступа';
  $L['access_groups'] = 'Доступ в следующие группы :';
  $L['access_default'] = 'Доступ по умолчанию :';
  $L['active'] = 'Профиль активен';
  $L['access_start_reason_text'] = 'Причина установки доступа к группам';
  $L['access_stop_reason_text'] = "Причина снятия доступа к группам";
  $L['user_access'] = 'Временный доступ к дополнительным группам для пользователя : ';
  $L['access_set_success'] = ' установлен.';
  $L['access_updated'] = ' обновлён.';
  $L['access_set'] = 'Редактировать временный доступ пользователя к группам';
  $L['access_title'] = 'TGA';
 /*
   * Help
   */
$L['userman_help_main'] = 'Для редактирования профиля пользователя нажмите на его имя в списке имён.<br /><br /> Для поиска пользователя по E-mail или имени введите E-mail или имя в поле <b>флильтра</b> для поиска.<br><br>Для поиска пользователей заходивших на сайт до какой-нибудь даты введите слово <b>before</b> / <b>after</b> и через пробел дату например : <b>12.10.2017</b> в поле <b>фильтра</b> для поиска. 
Получите список пользователей которые зарегистрировались до указанной вами даты и ничего не писали на сайте.<br> После даты через пробел можно указывать <b>neverlogin</b> или <b>all</b>.<br>
<b>neverlogin</b> - для отображения только тех, кто создал профиль, но никогда не проходил авторизацию на сайте. <br>
<b>all</b> - всех , включая тех кто что-то писал или лайкал на сайте.<br>
Без указания <b>neverlogin</b> или <b>all</b> после даты плагин не включает в список тех, кто что-то писал на сайте и проходил авторизацию.<br><br>
Пример фильтров : <b>before 01.01.2017</b> ;  <b>after 01.01.2017</b> ; <b>before 01.01.2017 neverlogin</b> ; <b>before 01.01.2017 all</b>
<hr><b>TGA</b> - Временный доступ к группам ( Temporary groups access )<br /><br /> Если поле <b>TGA</b> пустое, то временный доступ пользователю не установлен, и в БД нет записи для пользователя. Если в поле есть иконка <img class="icon" src="plugins/userman/img/access.png" alt="запись активна" title="запись активна" />, то пользователь есть в базе для временного доступа к группам и запись активна.
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

  

