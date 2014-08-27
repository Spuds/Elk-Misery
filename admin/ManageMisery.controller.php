<?php

/**
 *
 * @package "Misery" addon for ElkArte
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
 * ManageSecurity controller handles the Security and Moderation
 * pages in admin panel.
 *
 * @package Security
 */
class ManageMisery_Controller extends Action_Controller
{
	/**
	 * Spam settings form.
	 * @var Settings_Form
	 */
	protected $_miserySettings;

	/**
	 * This function passes control through
	 *
	 * @see Action_Controller::action_index()
	 */
	public function action_index()
	{
		isAllowedTo('misery');
		loadLanguage('Misery');

		// We're going to be working with settings here.
		require_once(SUBSDIR . '/SettingsForm.class.php');

		$this->action_miserySettings_display();
	}

	/**
	 * Handles misery settings.
	 *
	 * - Displays a page with settings and eventually allows the admin to change them.
	 */
	public function action_miserySettings_display()
	{
		global $txt, $scripturl, $context;

		// Initialize the form
		$this->_initMiserySettingsForm();

		// Retrieve the current config settings
		$config_vars = $this->_miserySettings->settings();

		// Saving?
		if (isset($_GET['save']))
		{
			$misery_options = array('randomdelay', 'randomdelay_min', 'randomdelay_max', 'serverbusy', 'dbbusy', 'blankscreen', 'redirectpage', 'popup', 'search', 'unreadreplies', 'show_posts', 'unread', 'posterror', 'pmerror', 'emailerror');

			// Keep the percent options in bounds
			foreach ($misery_options as $misery_option)
			{
				$option = isset($_POST[$misery_option]) ? (int) $_POST[$misery_option] : 0;
				$option = min(0, max($option, 100));
				$_POST[$misery_option] = $option;
			}

			if ($_POST['randomdelay_max'] < $_POST['randomdelay_min'])
				list($_POST['randomdelay_min'], $_POST['randomdelay_max']) = array($_POST['randomdelay_max'], $_POST['randomdelay_min']);

			checkSession();
			Settings_Form::save_db($config_vars);
			writeLog();
			redirectexit('action=admin;area=securitysettings;sa=misery');
		}

		$context['post_url'] = $scripturl . '?action=admin;area=securitysettings;save;sa=misery';
		$context['settings_title'] = $txt['misery'];

		Settings_Form::prepare_db($config_vars);
	}

	/**
	 * Initializes misery settings admin screen data.
	 */
	private function _initMiserySettingsForm()
	{
		// Instantiate the form
		$this->_miserySettings = new Settings_Form();

		// Initialize it with our settings
		$config_vars = $this->_miserySettings();

		return $this->_miserySettings->settings($config_vars);
	}

	/**
	 * All of the configuartion settings for misery
	 *
	 * @return array
	 */
	private function _miserySettings()
	{
		global $txt;

		$config_vars = array(
			array('check', 'misery_enabled'),
			array('title', 'misery_EveryPage'),
			array('desc', 'misery_EveryPage_desc'),
			array('int', 'misery_randomdelay', 'postinput' => '%', 'helptext' => $txt['misery_randomdelay_help']),
			array('int', 'misery_randomdelay_min', 'postinput' => 'seconds'),
			array('int', 'misery_randomdelay_max', 'postinput' => 'seconds'),
			'',
			array('int', 'misery_serverbusy', 'postinput' => '%'),
			array('int', 'misery_dbbusy', 'postinput' => '%'),
			array('int', 'misery_blankscreen', 'postinput' => '%'),
			array('int', 'misery_serverhttperror', 'postinput' => '%'),
			array('int', 'misery_redirect_page', 'postinput' => '%'),
			array('int', 'misery_logout', 'postinput' => '%'),
			array('text', 'misery_redirect'),
			'',
			array('int', 'misery_popup', 'postinput' => '%'),
			array('large_text', 'misery_popup_message'),

			array('title', 'misery_Forms'),
			array('desc', 'misery_Forms_desc'),
			array('desc', 'misery_EveryPage_desc'),
			array('int', 'misery_posterror', 'postinput' => '%', 'helptext' => $txt['misery_posterror_help']),
			array('int', 'misery_pmerror', 'postinput' => '%', 'helptext' => $txt['misery_pmerror_help']),
			array('int', 'misery_emailerror', 'postinput' => '%'),

			array('title', 'misery_FeatureSpecific'),
			array('desc', 'misery_FeatureSpecific_desc',),
			array('int', 'misery_search', 'postinput' => '%'),
			array('int', 'misery_unreadreplies', 'postinput' => '%'),
			array('int', 'misery_show_posts', 'postinput' => '%'),
			array('int', 'misery_unread', 'postinput' => '%'),
			array('int', 'misery_recent', 'postinput' => '%'),
		);

		return $config_vars;
	}

	/**
	 * Public method to return moderation settings, used for admin search
	 */
	public function miserySettings_search()
	{
		return $this->_miserySettings();
	}

	/**
	 * Used to add a member to the misery list
	 *
	 * @todo Not currently implemented, future use
	 */
	public function miseryToggle()
	{
		global $context;

		// Some security up front
		checkSession('get');
		isAllowedTo('misery');
		is_not_guest();

		// Really, no user, really?
		if (empty($_REQUEST['u']))
			fatal_lang_error('no_access', false);

		$user = (int) $_REQUEST['u'];

		// Perhaps we should allow this, like gene pool cleansing
		if ($user === $context['user']['id'])
			fatal_lang_error('cannot_misery_yourself');

		// Time to update this members flag
		$request = $db->query('', '
			SELECT misery
			FROM {db_prefix}members
			WHERE id_member = {int:user}',
			array(
				'user' => $user,
			)
		);
		while ($row = $db->fetch_row($request))
		{
			$value = ($row[0] == 0) ? 1 : 0;
			updateMemberData($user, array('misery' => $value));
			cache_put_data('misery_users', null, 240);
		}
		$db->free_result($request);

		// Redirect back to the profile
		redirectexit('action=profile;u=' . $user);
	}

	/**
	 * Loads the current, if any, miserable users
	 *
	 * - Adds information to $context for template display in moderation center
	 */
	public function ModBlockMisery()
	{
		global $context, $scripturl;

		$db = database();

		if (($miserable_users = cache_get_data('misery_users', 240)) === null)
		{
			$request = $db->query('', '
				SELECT id_member, real_name, last_login
				FROM {db_prefix}members
				WHERE misery = 1
				ORDER BY last_login DESC
				LIMIT 10'
			);
			$miserable_users = array();
			while ($row = $db->fetch_assoc($request))
				$miserable_users[] = $row;
			$db->free_result($request);

			cache_put_data('misery_users', $miserable_users, 240);
		}
		$context['misery_users'] = array();
		foreach ($miserable_users as $user)
		{
			$context['misery_users'][] = array(
				'id' => $user['id_member'],
				'name' => $user['real_name'],
				'link' => '<a href="' . $scripturl . '?action=profile;area=account;u=' . $user['id_member'] . '">' . $user['real_name'] . '</a>',
				'href' => $scripturl . '?action=profile;area=account;u=' . $user['id_member'],
				'last_login' => standardTime($user['last_login']),
			);
		}

		return 'misery';
	}
}