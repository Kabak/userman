<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/* @var $db CotDB */
/* @var $cache Cache */
/* @var $t Xtemplate */

/**
 * @package userman
 * @version 8.1.2
 * @author Aliaksei Kobak
 * @copyright Copyright (c) Aliaksei Kobak 2013 - 2023
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'userman');
cot_block($usr['isadmin']);

// Countries list
require_once cot_langfile('countries', 'core');
//
require_once cot_incfile('forms');

require_once cot_incfile('users', 'module');

if ( cot_module_active('pm') )
	require_once cot_incfile('pm', 'module');

//Подключаем чтобы строковые переменные из lang файла были доступны как переменные
require_once cot_langfile('userman', 'plug');
// Подключаем, если есть файл в подкаталоге плагина с функциями вынесеными в отдельный файл functions.имяплагина.php
require_once cot_incfile('userman', 'plug');
//Подключаем файл с ресурсами
require_once cot_incfile('userman', 'plug', 'resources');

$temp = new XTemplate(cot_tplfile('userman.admin', 'plug', true));

// Edit name of user in userman form available if logпed user is admin
$protected = !$usr['isadmin'] ? array('disabled' => 'disabled') : array();

$editor_class = Cot::$cfg['users']['usertextimg'] ? 'minieditor' : '';

$id = cot_import('id', 'G', 'INT');
$sort = cot_import('sort', 'G', 'ALP', 16);
$w = cot_import('w', 'G', 'ALP', 4);

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['users']['maxusersperpage']);
$f = cot_import('f', 'G', 'ALP', 16);
$g = cot_import('g', 'G', 'INT');
$gm = cot_import('gm', 'G', 'INT');
$y = cot_import('y', 'P', 'TXT', 30);
$sq = cot_import('sq', 'G', 'TXT', 8);
$update = cot_import('update','G', 'TXT',8);

if ($a == 'edit'){ include_once cot_incfile('userman', 'plug','edit'); }
else if( $a == 'access'){ include_once cot_incfile('userman', 'plug','access'); } // Temporary access user to groups
else if( $a == 'profile'){ include_once cot_incfile('userman', 'plug','profile'); }
else 
{

$users_sort_tags = array(
	// columns in $db_users table
//	'access' => array('UM_TOP_ACCESS', &$L['Access'],),
	'email' => array('UM_TOP_EMAIL', &$L['Email'],),
	'id' => array('UM_TOP_USERID', &$L['Userid'],),
	'name' => array('UM_TOP_NAME', &$L['Username'],),
	'maingrp' => array('UM_TOP_MAINGRP', &$L['Maingroup'],),
	'country' => array('UM_TOP_COUNTRY', &$L['Country'],),
	'occupation' => array('UM_TOP_OCCUPATION', &$L['Occupation'],),
	'location' => array('UM_TOP_LOCATION', &$L['Location'],),
	'timezone' => array('UM_TOP_TIMEZONE', &$L['Timezone'],),
	'birthdate' => array('UM_TOP_BIRTHDATE', &$L['Birthdate'],),
	'gender' => array('UM_TOP_GENDER', &$L['Gender'],),
	'regdate' => array('UM_TOP_REGDATE', &$L['Registered'],),
	'lastlog' => array('UM_TOP_LASTLOGGED', &$L['Lastlogged'],),
	'logcount' => array('UM_TOP_LOGCOUNT', &$L['Count'],),
	'postcount' => array('UM_TOP_POSTCOUNT', &$L['Posts'],),
	// like columns in $db_groups table
	'grplevel' => array('UM_TOP_GRPLEVEL', &$L['Level'],),
	'grpname' => array('UM_TOP_GRPTITLE', &$L['Maingroup'],),
);

$users_sort_blacklist = array('email', 'lastip', 'password', 'sid', 'sidtime', 'lostpass', 'auth', 'token');
$users_sort_whitelist = array('id', 'name', 'maingrp', 'country', 'timezone', 'birthdate', 'gender', 'lang', 'regdate');

if (empty($sort) || in_array(mb_strtolower($sort), $users_sort_blacklist) || !in_array($sort, $users_sort_whitelist) && !Cot::$db->fieldExists($db_users, "user_$sort"))
	$sort = 'regdate'; //name

if (!in_array($w, array('asc', 'desc')))	$w = 'desc';//asc

if (empty($f))	$f = 'all';

if (empty($d))	$d = 0;


$title[] = array(cot_url('admin'), $L['Users']);

if(!empty($sq))	$y = $sq;

if ($sort == 'grplevel' || $sort == 'grpname' || $gm > 1)
{
	$join_condition = "LEFT JOIN $db_groups as g ON g.grp_id=u.user_maingrp";
}

if( $f == 'search' && mb_strlen( is_null($y) ? "" : $y ) > 1)
{
	// search users by param
	if ( !empty($y) )
	{
		
		$request_array = explode(' ', $y);

		$size = sizeof( $request_array);

		if ( $size < 2 ) goto skeep_uf;

		$search_keys = array( "passive", "before", "after", "neverlogin", "all" );

		$user_active = "";

		for ( $index = 0; $index < $size; ++$index )
		{
			$val = array_search ( $request_array[$index], $search_keys );

			if ( $val !== false )
			{
				if ( $val == "0" )
				{
					$user_active = " AND user_postcount = 0";
				} 
				elseif ( $val == "1" )
				{
					$where['regdate'] = "user_lastlog < ";
					$regdate = " AND user_regdate < ";
				} 
				elseif ( $val == "2" )
				{
					$where['regdate'] = "user_lastlog > ";
					$regdate = " AND user_regdate > ";
				}
				elseif ( $val == "3" )
				{
					$time = strtotime('01.01.1970 05:00');
					$where['regdate'] = "user_lastlog < ". $time;
					$regdate1 = "";
				}
				elseif ( $val == "4" )
				{
					$where['regdate'] = "";
				}

			}

			if ( strtotime($request_array[$index]) != false )
			{
				$date = strtotime($request_array[$index]);
			}			
		}

		if ( isset($date) && $date != false && isset($where['regdate']) )
		{
			if ( isset($regdate1) )
			{
				$where['regdate'] .= $regdate . $date . $user_active;
			}
			else
			{
				$where['regdate'] .= $date . $regdate . $date . $user_active;
			}
			
			goto skeep_ENamme;
		}
		else
		{	
			cot_message($L['um_request_error'], 'warning');

			if ( isset ($where['regdate'] ) )
			{
				unset ( $where['regdate'] );
				$y = "";
			}
			else
			{
				$y = "";
				$where['1'] = "1";
				goto skeep_ENamme;
			}
		}

skeep_uf:
		unset ( $search_keys );
	}
	
	// Search by Email
    if( preg_match('|.*@.*\..*|',$y) )
	{
		$sq = $y;
		$title[] = $L['Search']." '".htmlspecialchars($y)."'";
		$where['email'] = "user_email LIKE '%".Cot::$db->prep($y)."%'";

	// Search by Name if no @ symbol was not entered
    } else
	{
		$sq = $y;
		$title[] = $L['Search']." '".htmlspecialchars($y)."'";
		$where['namelike'] = "user_name LIKE '%".Cot::$db->prep($y)."%'";
    }

skeep_ENamme:
}
elseif($g > 1)
{
	$title[] = $L['Maingroup']." = ".cot_build_group($g);
	$where['maingrp'] = "user_maingrp=$g";
}
elseif($gm > 1)
{
	$title[] = $L['Group']." = ".cot_build_group($gm);
	$join_condition .= " LEFT JOIN $db_groups_users as m ON m.gru_userid=u.user_id";
	$where['maingrp'] = "m.gru_groupid=".$gm;
}
elseif(mb_substr($f, 0, 8) == 'country_')
{
	$cn = mb_strtolower(mb_substr($f, 8, 2));
	$title[] = $L['Country']." '" . (($cn == '00') ? $L['None']."'" : $cot_countries[$cn]."'");
	$where['country'] = "user_country='$cn'";
}
else//if($f == 'all')
{
	$where['1'] = "1";
}

switch ($sort)
{
	case 'grplevel':
		$sqlorder = "g.grp_level $w";
	break;
	case 'grpname':
		$sqlorder = "g.grp_name $w";
	break;
	default:
		$sqlorder = "user_$sort $w";
	break;
}

$users_url_path = array('m' => 'other','p' => 'userman','f' => $f, 'g' => $g, 'gm' => $gm, 'sort' => $sort, 'w' => $w, 'sq' => $sq);
isset ($join_condition) ? $join_condition : $join_condition ="";
$totalusers = Cot::$db->query(
	"SELECT COUNT(*) FROM $db_users AS u $join_condition WHERE ".implode(" AND ", $where)
)->fetchColumn();

cot_message($L['um_found'] . (string)$totalusers . $L['um_users']);

// Disallow accessing non-existent pages
if ($totalusers > 0 && $d > $totalusers)
	cot_die_message(404);

isset ($join_columns) ? $join_columns : $join_columns ="";
$sqlusers = Cot::$db->query(
	"SELECT u.* $join_columns FROM $db_users AS u $join_condition
	WHERE ".implode(" AND ", $where)." ORDER BY $sqlorder LIMIT $d,{$cfg['users']['maxusersperpage']}"
)->fetchAll();

$totalpage = ceil($totalusers / $cfg['users']['maxusersperpage']);
$currentpage = ceil($d / $cfg['users']['maxusersperpage']) + 1;
$pagenav = cot_pagenav('admin', $users_url_path, $d, $totalusers, $cfg['users']['maxusersperpage'], 'd', '', $ajax = true, $target_div ='MyajaxBlock');

$out['subtitle'] = $L['Users'];

$countryfilters_titles = [];
$countryfilters_values = [];
$countryfilters_titles[] = $R['users_sel_def_l'].$L['Country'].$R['users_sel_def_r'];
$countryfilters_values[] = cot_url('admin','m=other&p=userman');
$countryfilters_titles[] = $L['None'];
$countryfilters_values[] = cot_url('admin', 'm=other&p=userman&f=country_00');

foreach ($cot_countries as $i => $x) 
{
	$countryfilters_titles[] = cot_cutstring($x, 23);
	$countryfilters_values[] = cot_url('admin', 'm=other&p=userman&f=country_'.$i);
}

$countryfilters = cot_selectbox(cot_url('admin', 'm=other&p=userman&f='.$f), 'bycountry', $countryfilters_values, $countryfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);
// $countryfilters = cot_selectbox(cot_url('admin', array('m' => 'other', 'p' => 'userman','f'=> $f)), 'bycountry', $countryfilters_values, $countryfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);


$grpfilters_titles = array($L['Maingroup']);
$grpfilters_group_values = array(cot_url('admin','m=other&p=userman'));
$grpfilters_maingrp_values = array(cot_url('admin','m=other&p=userman'));

foreach($cot_groups as $k => $i)
{
	$grpfilters_titles[] = $cot_groups[$k]['name'];
	$grpfilters_maingrp_values[] = cot_url('admin', 'm=other&p=userman&g='.$k, '', true);
	$grpfilters_group_values[] = cot_url('admin', 'm=other&p=userman&gm='.$k, '', true);
}

$maingrpfilters = cot_selectbox(cot_url('admin', array('m' => 'other', 'p' => 'userman', 'g' => $g) , '', true), 'bymaingroup', $grpfilters_maingrp_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

$grpfilters_titles[0] = $L['Group'];
$grpfilters = cot_selectbox(cot_url('admin', array('m' => 'other', 'p' => 'userman', 'gm' => $gm) , '', true), 'bygroupms', $grpfilters_group_values, $grpfilters_titles, false, array('onchange' => 'redirect(this)'), '', true);

$temp->assign(array(
	'UM_TITLE' => cot_breadcrumbs($title, $cfg['homebreadcrumb']),
	'UM_SUBTITLE' => $L['use_subtitle'],
	'UM_CURRENTFILTER' => $f,
	'UM_TOP_CURRENTPAGE' => $currentpage,
	'UM_TOP_TOTALPAGE' => $totalpage,
	'UM_TOP_MAXPERPAGE' => $cfg['users']['maxusersperpage'],
	'UM_TOP_TOTALUSERS' => $totalusers,
	'UM_TOP_FILTER_ACTION' => cot_url('admin', 'm=other&p=userman&f=search'),
	'UM_TOP_FILTERS_COUNTRY' => $countryfilters,
	'UM_TOP_FILTERS_MAINGROUP' => $maingrpfilters,
	'UM_TOP_FILTERS_GROUP' => $grpfilters,
	'UM_TOP_FPC' => $L['um_fpc'],
	'UM_TOP_FILTERS_SEARCH' => cot_inputbox('text', 'y', $y, array('size' => 30, 'maxlength' => 30)),
	'UM_TOP_FILTERS_SUBMIT' => cot_inputbox('submit', 'submit', $L['Search']),
));

$k = '_.__._';
$asc = explode($k, cot_url('admin', array('m' => 'other', 'p' => 'userman','sort' => $k, 'w'=> 'asc') + $users_url_path));
$desc = explode($k, cot_url('admin', array('m' => 'other', 'p' => 'userman','sort' => $k, 'w'=> 'desc') + $users_url_path));

foreach ($users_sort_tags as $k => $x)
{
	$temp->assign($x[0], cot_rc('users_link_sort', array(
		'asc_url' => implode($k, $asc),
		'desc_url' => implode($k, $desc),
		'text' => $x[1],
		'icon_down' => $k == $s && $w == 'asc' ? $R['icon_vert_active']['asc'] : $R['icon_down'],
		'icon_up' => $k == $s && $w == 'desc' ? $R['icon_vert_active']['desc'] : $R['icon_up']
	)));
}

// Extra fields for users
foreach($cot_extrafields[$db_users] as $exfld)
{
	$uname = strtoupper($exfld['field_name']);
	$fieldtext = isset($L['user_'.$exfld['field_name'].'_title']) ? $L['user_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
	$temp->assign('UM_TOP_'.$uname, cot_rc('users_link_sort', array(
		'asc_url' => cot_url('users', array('m' => 'other', 'p' => 'userman','s' => $exfld['field_name'], 'w'=> 'asc') + $users_url_path),
		'desc_url' => cot_url('users', array('m' => 'other', 'p' => 'userman','s' => $exfld['field_name'], 'w'=> 'desc') + $users_url_path),
		'text' => $fieldtext
	)));
}

if		($a == 'create'){  include_once cot_incfile('userman', 'plug','create'); } // Create new user
else if ($a == 'delete'){  include_once cot_incfile('userman', 'plug','delete'); } // Delete user
else if ($a == 'silent_delete') // Silent delete user
{ 
	include_once cot_incfile('userman', 'plug','delete');
	cot_redirect(cot_url('admin', 'm=other&p=userman','', true));
}
else if ($a == 'delete_selected') // Delete selected group
{
	if ( is_array($_POST['s'] ) )
	{
		cot_check_xp();
		$s = cot_import('s', 'P', 'ARR');
		
		foreach ($s as $id => $v)
		{
			include_once cot_incfile('userman', 'plug','delete');
		}
	}

	cot_redirect(cot_url('admin', 'm=other&p=userman','', true));
}

$jj = 0;
foreach ($sqlusers as $urr)
{
	$jj++;




	// Генерируем вопрос по удалению пользователя
	$url_del = cot_confirm_url('admin.php?m=other&p=userman&a=delete&id='.$urr['user_id']);
	$url_del_noconf = cot_url('admin','m=other&p=userman&a=silent_delete&id='.$urr['user_id']);
	$temp->assign('UM_ROW_DELETE', cot_rc_link($url_del, cot_rc('del_icon', array('alt' => $L['delete'], 'title' => $L['delete'])), array('class' => 'confirmLink')));
	$temp->assign('UM_ROW_DELETE_NOCONF', cot_rc_link($url_del_noconf, cot_rc('del_icon', array('alt' => $L['delete'], 'title' => $L['delete']))));
	$id = $urr['user_id'];
	$sql_access = Cot::$db->query("SELECT * FROM $db_userman WHERE user_id=$id LIMIT 1");
	if ( $sql_access->rowCount() != 0 )
	{
	    $us_er = $sql_access->fetch();
	    if( $us_er['active'] )
		$temp->assign(array('UM_ROW_ACCESS' => cot_rc_link(cot_url('admin', 'm=other&p=userman&a=access&id='.$urr['user_id']),$R['access'])));	
	    else
		$temp->assign(array('UM_ROW_ACCESS' => cot_rc_link(cot_url('admin', 'm=other&p=userman&a=access&id='.$urr['user_id']),$R['access_off'])));	
    }
	else
	{
	    	$temp->assign(array('UM_ROW_ACCESS' => ''));   
	}
	
	// Users pages & comments
	if ( cot_module_active('page') )
	{
		$use_page_amount = Cot::$db->query("SELECT COUNT(*) FROM ". Cot::$db->pages ." WHERE page_ownerid = " . $urr['user_id'] )->fetchColumn();
	}
	else
	{
		$use_page_amount = "X"; 
	}

	if ( cot_plugin_active('comments') )
	{
		$use_comments_amount = Cot::$db->query("SELECT COUNT(*) FROM ". Cot::$db->com ." WHERE com_authorid = " . $urr['user_id'] )->fetchColumn();
	}
	else
	{
		$use_comments_amount = "X"; 
	}

	// No Forum module
	!isset($urr['user_postcount']) ? $urr['user_postcount'] = "X" : $urr['user_postcount'];

	$temp->assign(array(
		'UM_ROW_NUM' => $jj,
		'UM_ROW' => $urr,
		'UM_ROW_UFORUM' => $urr['user_postcount'],
		'UM_ROW_UPAGES' => $use_page_amount,
		'UM_ROW_UCOMMENTS' => $use_comments_amount,
		));
	$temp->assign(cot_generate_um_usertags($urr, 'UM_ROW_'));


	$temp->assign(array( 'UM_USER_ID' => $urr['user_id'] ));

	$temp->parse('MAIN.CREATE.UM_ROW');
       
}

// Extra fields for create new user  ++++++++++++++++++
if (!empty(Cot::$extrafields[Cot::$db->users])) {
    foreach (Cot::$extrafields[Cot::$db->users] as $exfld) 
	{
        $uname = strtoupper($exfld['field_name']);
        $exfld_val = cot_build_extrafields( $exfld['field_name'], $exfld, '');
        $exfld_title = cot_extrafield_title($exfld, 'user_');

		if ( $exfld['field_required'] )
		{
			$temp->assign(array(
				'UM_USERS_EDIT_EXTRAFLD' => $exfld_val,
				'UM_USERS_EDIT_EXTRAFLD_TITLE' => $exfld_title
			));
			$temp->parse('MAIN.CREATE.EXTRAFLD');
		}
    }
}
// Extra fields ---------------------------------------

// Create block +++++++++++++++++++++++++++++++++++++++
// Reading default level for user creation
$um_maingrp = $cfg['plugin']['userman']['defaultlevel'];
$um_defaultgrp[0]['gru_groupid'] = $um_maingrp;

// If no user in list
 !isset($urr['user_id']) ? $urr['user_id'] = "" : $urr['user_id'];

$temp->assign(array(
	'UM_TITLE' => $L['title'],
	'UM_SUBTITLE' => $L['subtitle'],
	'UM_CREATE_USER_CREATE' => cot_url('admin', 'm=other&p=userman&a=create'),
	'UM_CREATE_USER_DELETE_SEL' => cot_url('admin', 'm=other&p=userman&a=delete_selected'),
	'UM_CREATE_USER_ID' => $urr['user_id'], 
	'UM_CREATE_USER_NAME' => cot_inputbox('text', 'um_username', $cfg['plugin']['userman']['defaultname'], array('size' => 32, 'maxlength' => 100) + $protected),
	'UM_CREATE_USER_EMAIL' => cot_inputbox('text', 'um_useremail', $cfg['plugin']['userman']['defaultemail'], array('size' => 32, 'maxlength' => 64)),
	'UM_CREATE_USER_DEFAULT_PASS' => cot_inputbox('password', 'um_password1', $cfg['plugin']['userman']['defaultpass'], array('size' => 12, 'maxlength' => 32,'autocomplete' => 'off') + $protected),
	'UM_CREATE_USER_PASSWORDREPEAT' => cot_inputbox('password', 'um_password2', $cfg['plugin']['userman']['defaultpass'], array('size' => 12, 'maxlength' => 32)),
	'UM_CREATE_USER_MAINGRP' => cot_build_group($um_maingrp),
	'UM_CREATE_USER_GROUPS' => cot_build_um_groupsms($um_defaultgrp, $usr['isadmin'], $um_maingrp),
	'UM_CREATE_USER_SIGNATURE' => cot_textarea('um_sign',$cfg['plugin']['userman']['defaultsign'], 1, 2, array('class' => $editor_class)),	
));

if( $cfg['plugin']['userman']['defaultpass'] !='' )
$temp->assign(array('UM_CREATE_USER_HELPPASS' => $L['um_defaultpass'].$cfg['plugin']['userman']['defaultpass'].' )'));

// Buttons
$temp->assign(array(
	'UM_CREATE_USER' => $L['um_createuser'],
	'UM_YOURPROFILE' => cot_url('admin','m=other&p=userman&a=profile'),
	'UM_YOURPROFILE_TEXT' => $L['yourprofile'],
	'UM_SITECONFIG' => cot_url('admin','m=config&n=edit&o=module&p=users'),
	'UM_SITECONFIG_TEXT' => $L['site_config'],
	'UM_USER_RIGHTS' => cot_url('admin','m=rightsbyitem&ic=users&io=a'),
	'UM_USER_RIGHTS_TEXT' => $L['user_rights'],
	'UM_EXTRA_FIELDS' => cot_url('admin','m=extrafields&n='.$db_users),
	'UM_EXTRA_FIELDS_TEXT' => $L['extra_fields']
));
	
$temp->assign(array(         
	'UM_LIST_TITLE' => $L['users_list'],
	'UM_TOP_DELETE' => $L['Delete'],
	'UM_TOP_ACCESS' => $L['access_title'],  
	'UM_TOP_ID' => 'User ID',
));

// If no PM module
if ( cot_module_active('pm') )
$temp->assign(array('UM_TOP_PM' => 'PM'));


$temp->assign(array(
	'UM_TOP_PAGNAV' => $pagenav['main'],
	'UM_TOP_PAGEPREV' => $pagenav['prev'],
	'UM_TOP_PAGENEXT' => $pagenav['next'],
));
$temp->parse('MAIN.CREATE.UM_AJAXBLOCK.UM_PAGENAV');

$temp->parse('MAIN.CREATE.UM_AJAXBLOCK');

// Create block ---------------------------------------

$temp->parse('MAIN.CREATE');


if ( COT_AJAX ){
	$temp->out('MAIN.CREATE.UM_AJAXBLOCK.UM_ROW');
	$temp->out('MAIN.CREATE.UM_AJAXBLOCK');        
	$temp->out('MAIN.CREATE.UM_AJAXBLOCK.UM_PAGENAV');
goto END;    
}
// ---------------------------------------------------------------------------------------------------------------------------------------

}
$adminhelp = $L['userman_help_main'];
cot_display_messages($temp);

    $temp->parse('MAIN');

        $plugin_body = $temp->text('MAIN');
END: