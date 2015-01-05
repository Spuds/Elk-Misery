<?php

/**
 *
 * @package "Miserable User" addon for ElkArte
 * @author Spuds
 * @copyright (c) 2014 Spuds
 * @license Mozilla Public License version 1.1 http://www.mozilla.org/MPL/1.1/.
 *
 * @version 1.0
 *
 */

if (!defined('ELK'))
	die('No access...');

/**
 * integrate_actions hook
 *
 * - Normally used to add actions, we use it to apply the on every page load
 * misery items as well as redirection.
 *
 * @param mixed[] $actionArray
 * @param string $adminActions
 */
function ia_misery(&$actionArray, &$adminActions)
{
	// These can hit on every page load
	make_miserable_pageload();

	// You might not go where you think
	if (isset($_GET['action']) && isset($actionArray[$_GET['action']]) && !in_array($_GET['action'], $adminActions))
		make_miserable('redirect_page');

	// And a few features may not be available
	if (isset($_GET['action']) && in_array($_GET['action'], array('search', 'unreadreplies', 'show_posts', 'unread', 'recent')))
		make_miserable($_GET['action']);
}

/**
 * ilp_misery()
 *
 * Permissions hook, integrate_load_permissions, called from ManagePermissions.php
 * used to add new permissions
 *
 * @param mixed $permissionGroups
 * @param mixed $permissionList
 * @param mixed $leftPermissionGroups
 * @param mixed $hiddenPermissions
 * @param mixed $relabelPermissions
 */
function ilp_misery(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
	loadLanguage('Misery');

	$permissionList['membergroup']['misery'] = array(false, 'member_admin', 'moderate_general');
}

/**
 * iaa_misery()
 *
 * Admin Hook, integrate_admin_areas, called from Admin.php
 * used to add/modify admin menu areas
 *
 * @param mixed $admin_areas
 */
function iaa_misery(&$admin_areas)
{
	global $txt;

	loadLanguage('Misery');

	$admin_areas['config']['areas']['securitysettings']['subsections']['misery'] = array($txt['misery']);
}

/**
 * integrate_action_post_before hook
 *
 * Called before entry to post controller, used to apply some misery when
 * a member has selected to post
 *
 * @param string $action
 */
function iapb_misery($action)
{
	// Do they deserve a bit of misery when trying to post?
	if ($action !== 'action_post2')
		return;

	// So then maybe they get a post error
	make_miserable('posterror');
}

/**
 * integrate_action_personalmessage_before hook
 *
 * Called before entry to PM controller, used to apply some misery when
 * a member has selected to send a PM
 *
 * @param string $action
 */
function iapmb_misery()
{
	// Do they deserve a bit of misery when trying to PM?
	if (!isset($_GET['sa']) || $_GET['sa'] !== 'send2')
		return;

	// Lets see if we shall generate a PM error
	make_miserable('pmerror');
}

/**
 * integrate_action_moderationcenter_after hook
 *
 * Called after exit of moderation center, used to add a status block to the mod center home page
 *
 * @param string $action
 */
function iamca_misery($action)
{
	global $modSettings, $context;

	// Show the who is miserable block in the moderation center
	if (empty($modSettings['misery_enabled']) || !allowedTo('misery') || isset($_REQUEST['sa']))
		return;

	// Find out who is miserable
	require_once(ADMINDIR . '/ManageMisery.controller.php');
	$misery = new ManageMisery_Controller;
	$context['mod_blocks'][] = $misery->ModBlockMisery();

	loadLanguage('Misery');
	loadTemplate('Misery');
}

/**
 * integrate_action_emailuser_before hook
 *
 * Called before entry to email user controller, used to apply some misery when
 * a member has selected to send an email
 *
 * @param string $action
 */
function iaeub_misery($action)
{
	// Do they deserve a bit of misery when trying to send an email?
	if ($action !== 'action_email' || !isset($_POST['send']))
		return;

	// Maybe they get an email error
	make_miserable('emailerror');
}

/**
 * integrate_action_messageindex_before before
 *
 * Called from the dispatcher when viewing the message index
 */
function iamb_misery()
{
	// These can hit on every page load
	make_miserable_pageload();

	// Message index, maybe
	make_miserable('redirect_page');
}

/**
 * integrate_action_display_before hook
 *
 * Called from the dispatcher when viewing a topic thread
 */
function iadb_misery()
{
	// These can hit on every page load
	make_miserable_pageload();

	// Maybe we will let you see that message
	make_miserable('redirect_page');
}

// integrate_account_profile_fields
function iapf_misery(&$fields)
{
	global $modSettings;

	// Can they impart misery?
	if (!empty($modSettings['misery_enabled']) && allowedTo('misery'))
		$fields = elk_array_insert($fields, 'id_group', array('misery'), 'after', false);
}

/**
 * integrate_load_profile_fields
 *
 * - Called from profile.subs
 * - Used to add additional fields to the profile createlist
 *
 * @param mixed[] $profile_fields
 */
function ilpf_misery(&$profile_fields)
{
	global $txt, $cur_profile, $modSettings;

	// Can you see this or use this?
	if (empty($modSettings['misery_enabled']) || !allowedTo('misery'))
		return;

	loadLanguage('Misery');

	$profile_fields['misery'] = array(
		'type' => 'check',
		'value' => empty($cur_profile['misery']) ? false : true,
		'label' => $txt['add_to_misery'],
		'permission' => 'misery',
		'input_validate' => create_function('&$value', '
			$value = $value == 0 ? 0 : 1;

			return true;
		'),
	);
}

/**
 * integrate_member_context hook
 *
 * - Called from load.php
 * - Used to add items to the $memberContext array
 *
 * @param int $user
 * @param mixed $display_custom_fields
 */
function imc_misery($user)
{
	global $memberContext, $user_profile;

	$memberContext[$user] += array(
		'misery' => !isset($user_profile[$user]['misery']) ? 0 : (int) $user_profile[$user]['misery']
	);
}

/**
 * integrate_load_member_data
 *
 * - Called from load.php
 * - Used to add columns / tables to the query so additional data can be loaded for a set
 *
 * @param string $select_columns
 * @param mixed[] $select_tables
 * @param string $set
 */
function ilmd_misery(&$select_columns, &$select_tables, $set)
{
	if ($set == 'profile' || $set == 'normal')
		$select_columns .= ',mem.misery';
}

/**
 * Load User Info hook, integrate_user_info
 *
 * - Called from load.php
 * - Used to add items to the $user_info array
 */
function iui_misery()
{
	global $user_info, $user_settings;

	// The the users misery flag
	$user_info['misery'] = empty($user_settings['misery']) ? 0 : 1;
}

/**
 * integrate_sa_modify_security
 *
 * - Add our misery tab to the list of security options
 *
 * @param mixed[] $subactions
 */
function iams_misery(&$subactions)
{
	global $context, $txt;

	$subactions['misery'] = array(
		'dir' => ADMINDIR,
		'file' => 'ManageMisery.controller.php',
		'controller' => 'ManageMisery_Controller',
		'function' => 'action_index'
	);

	$context[$context['admin_menu_name']]['tab_data']['tabs']['misery'] = array('description' => $txt['misery_desc']);
}

/**
 * integrate_admin_search
 *
 * - Used to add config var settings to the quick admin search area
 *
 * @param string[] $language_files
 * @param string[] $include_files
 * @param string[] $settings_search
 */
function ias_misery(&$language_files, &$include_files, &$settings_search)
{
	$language_files[] = 'Misery';
	$include_files[] = 'ManageMisery.controller';
	$settings_search[] = array('miserySettings_search', 'area=securitysettings;sa=misery', 'ManageMisery_Controller');
}

/**
 * integrate_profile_save
 *
 * - Profile save fields hook, called from Profile.controller.php
 * - used to prep and check variables before a profile update is saved
 *
 * @param mixed[] $profile_vars
 * @param mixed[] $post_errors
 * @param int $memID
 */
function ips_misery(&$profile_vars, &$post_errors, $memID)
{
	if (isset($_POST['misery']))
		$profile_vars['misery'] = !empty($_POST['misery']) ? 1 : 0;
}

/**
 * A few things that can cause misery on every page load
 */
function make_miserable_pageload()
{
	global $modSettings, $user_info;

	// Do they deserve a bit of misery?
	if (empty($modSettings['misery_enabled']) || empty($user_info['misery']) || $user_info['is_admin'])
		return;

	// These can hit on every page load
	make_miserable('randomdelay');
	make_miserable('serverbusy');
	make_miserable('dbbusy');
	make_miserable('blankscreen');
	make_miserable('serverhttperror');
	make_miserable('popup');
	make_miserable('logout');
}

/**
 * Apply's the misery based on the $type provided
 *
 * @param string $type the type of misery for them
 */
function make_miserable($type = '')
{
	global $user_info, $txt, $modSettings, $boardurl, $context, $board, $topic;

	// Make sure they deserve it, most do :P
	if (empty($modSettings['misery_enabled']) || empty($user_info['misery']) || $user_info['is_admin'])
		return;

	// If they have enabled this misery
	$setting = 'misery_' . $type;

	if (!empty($modSettings[$setting]))
	{
		// Each misery has its own chance to hit
		$chance = (int) $modSettings[$setting];
		$lotto = mt_rand(0, 100);

		// Buy a lotto ticket, you just got lucky
		if ($lotto > $chance)
			return;

		// To bad for you, time for some misery
		switch ($type)
		{
			// Time to be a bit unresponsive, just for them
			case 'randomdelay':
				if ($modSettings['misery_randomdelay_min'] <= $modSettings['misery_randomdelay_max'] && ($modSettings['misery_randomdelay_min'] > 0 || $modSettings['misery_randomdelay_max'] > 0))
				{
					if (function_exists('apache_reset_timeout'))
						@apache_reset_timeout();

					// Pick a wait time based on the limits
					$wait_min = $modSettings['misery_randomdelay_min'];
					$wait_max = $modSettings['misery_randomdelay_max'];
					$wait_time = mt_rand($wait_min, $wait_max);

					// Zzzzz
					@set_time_limit($wait_time + 30);
					sleep($wait_time);
				}

				break;
			// Log them out since we want to mess with you
			case 'logout':
				if (!$user_info['is_guest'])
				{
					require_once(CONTROLLERDIR . '/Auth.controller.php');
					$controller = new Auth_Controller();
					$controller->action_logout(true);
				}

				break;
			// Let them think the server is overloaded
			case 'serverbusy':
				require_once(SOURCEDIR . '/Errors.php');
				display_loadavg_error();

				break;
			// Let them think the database is not available
			case 'dbbusy':
				global $db_error_send;

				unset($db_error_send);
				require_once(SOURCEDIR . '/Errors.php');
				display_db_error();

				break;
			// Nothing like a white screen to add to their misery
			case 'blankscreen':
				die();

				break;
			// HTTP headers can be fun as well, makes them think something is broke
			case 'serverhttperror':
				$status_codes = array(
					'400~Bad Request~<html><head><title>400 Bad Request</title></head><body><h1>Bad Request</h1><p>Your browser sent a request that this server could not understand.</p></body></html>',
					'401~Unauthorized~<html><head><title>401 Authorization Required</title></head><body><h1>Authorization Required</h1><p>This server could not verify that you are authorized to access the document requested.  Either you supplied the wrong credentials (e.g., bad password), or your browser does not understand how to supply the credentials required.</p><p>Additionally, a 404 Not Found error was encountered while trying to use an ErrorDocument to handle the request.</p></body></html>',
					'403~Forbidden~<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You do not have permission to access / on this server.</p></body></html>',
					'404~Not Found~<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>',
					'405~Method Not Allowed~<html><head><title>405 Method Not Allowed</title></head><body><h1>Method Not Allowed</h1><p>The requested method GET is not allowed for the URL.</p></body></html>',
					'408~Request Timeout~<html><head><title>408 Request Time-out</title></head><body><h1>Request Time-out</h1><p>Server timeout waiting for the HTTP request from the client.</p></body></html>',
					'410~Gone~<html><head><title>410 Gone</title></head><body><h1>Gone</h1><p>The requested resource is no longer available on this server and there is no forwarding address. Please remove all references to this resource.</p></body></html>',
					'413~Request Entity Too Large~<html><head><title>413 Request Entity Too Large</title></head><body><h1>Request Entity Too Large</h1>The requested resource does not allow request data with GET requests, or the amount of data provided in the request exceeds the capacity limit.</body></html>',
					'414~Request-URI Too Long~<html><head><title>414 Request-URI Too Large</title></head><body><h1>Request-URI Too Large</h1><p>The requested URLs length exceeds the capacity limit for this server.</p></body></html>',
					'423~Locked~<html><head><title>423 Locked</title></head><body><h1>Locked</h1><p>The requested resource is currently locked.    The lock must be released or proper identification given before the method can be applied.</p></body></html>',
					'424~Failed Dependency~<html><head><title>424 Failed Dependency</title></head><body><h1>Failed Dependency</h1><p>The method could not be performed on the resource because the requested action depended on another action and that other action failed.</p></body></html>',
					'426~Upgrade Required~<html><head><title>426 Upgrade Required</title></head><body><h1>Upgrade Required</h1><p>The requested resource can only be retrieved using SSL.  The server is willing to upgrade the current connection to SSL, but your client does not support it.  Either upgrade your client, or try requesting the page using https:// </p></body></html>',
					'503~Service Temporarily Unavailable~<html><head><title>503 Service Temporarily Unavailable</title></head><body><h1>Service Temporarily Unavailable</h1><p>The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.</p></body></html>',
					'504~Gateway Timeout~<html><head><title>504 Gateway Time-out</title></head><body><h1>Gateway Time-out</h1><p>The proxy server did not receive a timely response from the upstream server.</p></body></html>',
				);

				// Pick a random header
				$status_error = $status_codes[mt_rand(0, count($status_codes) - 1)];
				list($status_code, $status_string, $status_html) = explode('~', $status_error);

				// Send it and bow out
				ob_end_clean();
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: no-cache');
				header((preg_match('~HTTP/1\.[01]~i', $_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') . ' ' . $status_code . ' ' . $status_string, true, $status_code);

				echo $status_html;
				die();

				break;
			// Redirect them to a new place, makes them mad
			case 'redirect_page':
				// Specific location given or just the front page.
				if (empty($modSettings['misery_redirect']))
					$modSettings['misery_redirect'] = $boardurl;

				redirectexit($modSettings['misery_redirect']);

				break;
			// I love it, I love my little naughty post, you're naughty! And then I take my naughty post and ...
			case 'posterror':
				$seed = mt_rand(0, 1000) / 10;
				$post_errors = Error_Context::context('post', 1);

				// Maybe they get an error, or maybe just pretend they posted but its lost :'(
				switch ($seed)
				{
					// 30% session timeout, try again
					case ($seed <= 30):
						$post_errors->addError('session_timeout');

						break;
					// 10% you submitted already, try again
					case ($seed <= 40):
						$post_errors->addError('form_already_submitted');

						break;
					// 10% you loose the subject
					case ($seed <= 50):
						$post_errors->addError('no_subject');
						unset($_REQUEST['subject']);
						unset($_POST['subject']);

						break;
					// 10% you loose the message
					case ($seed <= 60):
						$post_errors->addError('no_message');
						unset($_REQUEST['message']);
						unset($_POST['message']);

						break;
					// 5% to long
					case ($seed <= 65):
						$post_errors->addError(array('long_message', array($modSettings['max_messageLength'])));

						break;
					// 35% it acts like it posted but it goes nowhere.
					default:
						// Lets act like we posted, but we have not
						if (isset($_REQUEST['msg']) && !empty($_REQUEST['goback']) && !empty($topic))
							redirectexit('topic=' . $topic . '.msg' . $_REQUEST['msg'] . '#msg' . $_REQUEST['msg'], isBrowser('ie'));
						elseif (!empty($_REQUEST['goback']) && !empty($topic))
							redirectexit('topic=' . $topic . '.new#new', isBrowser('ie'));
						else
							redirectexit('board=' . $board . '.0');

						break;
				}

				return $post_errors;
			// PM ... Lets generate an error and blank out their work, or just pretend it sent
			case 'pmerror':
				$post_errors = Error_Context::context('pm', 1);
				$seed = mt_rand(0, 1000) / 10;

				// What went wrong?
				switch ($seed)
				{
					// 40% chance for session error, try again
					case ($seed <= 40):
						$post_errors->addError('session_timeout');

						break;
					// 10% you loose the subject
					case ($seed <= 50):
						$post_errors->addError('no_subject');
						$_REQUEST['subject'] = '';
						$_POST['subject'] = '';

						break;
					// 10% you loose the message
					case ($seed <= 100):
						$post_errors->addError('no_message');
						$_REQUEST['message'] = '';
						$_POST['message'] = '';

						break;
					// 5% its to darn long
					case ($seed <= 65):
						$post_errors->addError('long_message');

						break;
					// 35% it says it sent, but nothing happens
					default:
						redirectexit((isset($context['current_label_redirect']) ? $context['current_label_redirect'] : 'action=pm'));
				}

				return $post_errors;
			// Let them think they sent and email, but they did not
			case 'emailerror':
				$email_errors = '';
				$seed = mt_rand(0, 100);

				// What went wrong?
				switch ($seed)
				{
					// 30% session timeout
					case ($seed <= 30):
						$email_errors = 'session_timeout';

						break;
					// 10% cant find the email
					case ($seed <= 40):
						$email_errors = 'cant_find_user_email';

						break;
					// 10% sent to many
					case ($seed <= 50):
						$email_errors = 'sendmail_WaitTime_broken';

						break;
					// 10% you get to enter the subject again
					case ($seed <= 60):
						$_REQUEST['email_subject'] = '';
						$_POST['email_subject'] = '';
					// 10% you get to enter the message again
					case ($seed <= 70):
						$_REQUEST['email_body'] = '';
						$_POST['email_body'] = '';

						break;
					// 30% it goes notwhere, but acts like it did
					default:
						if (isset($_REQUEST['uid']))
							redirectexit('action=profile;u=' . (int) $_REQUEST['uid']);
						elseif (isset($_REQUEST['msg']))
							redirectexit('msg=' . (int) $_REQUEST['msg']);
						else
							redirectexit();
				}

				if (!empty($email_errors))
					fatal_lang_error($email_errors, false, array(300));

				break;
			// Popup messages are a pain to their existence
			case 'popup':
				loadLanguage('Misery');

				$message = !empty($modSettings['misery_popup_message']) ? $modSettings['misery_popup_message'] : $txt['misery_popup_message_default'];
				$message = str_replace('{membername}', $user_info['name'], $message);
				$message = str_replace('"', '&quot;', $message);
				$message = str_replace("\n", '\n', $message);

				addInlineJavascript('alert("' . $message . '");');

				break;

			// Some catchall load average problems
			case 'search':
			case 'unreadreplies':
			case 'show_posts':
			case 'unread':
			case 'recent':
				if (!in_array($type, array('search', 'unreadreplies', 'show_posts', 'unread', 'allunread')))
					$type = 'generic';

				fatal_lang_error('loadavg_' . $type . '_disabled', false);

				break;
		}
	}
}