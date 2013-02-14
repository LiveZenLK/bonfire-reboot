<?php

/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2012, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

//--------------------------------------------------------------------

/**
 * Notices - Session based notifications
 *
 * Provides a vast upgrade to CodeIgniter's flashmessages that works for
 * future requests as well as same-page-load messages. Can also maintain
 * a larger amount of
 */
class BF_Notices {

	protected static $groups;

	protected static $notices;

	protected static $sort_by 	= 'time';
	protected static $sort_dir	= NOTICE_SORT_DESC;

	/**
	 * If TRUE, will use the current module's name as the group,
	 * if no group is specified.
	 *
	 * @var boolean
	 */
	protected static $use_current_module = FALSE;

	protected static $ci;

	//--------------------------------------------------------------------

	/**
	 * Primarily for CI's loader.
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

		self::init();
	}

	//--------------------------------------------------------------------

	/**
	 * Populates the current data with information from the session so
	 * that it's always available to the user.
	 *
	 * @return void
	 */
	public static function init()
	{
		self::$groups = array();
		self::$notices = array();

		// Load any items from the session.
		self::import_session();
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Getters
	//--------------------------------------------------------------------

	/**
	 * Retrieves all notices that belong to a specific group.
	 *
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public static function group($group_name)
	{
		if (!isset(self::$groups[$group_name]))
		{
			return NULL;
		}

		$notices = array();
		foreach (self::$notices as $notice)
		{
			if (strtolower($notice['group']) == strtolower($group_name))
			{
				$notices[] = $notice;
			}
		}

		return self::do_sort($notices);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves a list of all current groups. Does not include their
	 * notices, only their names and a count of items in that group.
	 *
	 * @return array 	The names of the groups with their message count.
	 */
	public static function groups()
	{
		if (is_array(self::$groups))
		{
			$notices = self::$groups;

			return $notices;
		}

		return NULL;
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves notices based on their status. The results are then
	 * grouped by group (if any).
	 *
	 * @param  [type] $status [description]
	 * @return [type]         [description]
	 */
	public static function status($status)
	{
		$notices = array();
		foreach (self::$notices as $notice)
		{
			if (strtolower($notice['status']) == strtolower($status))
			{
				$notices[] = $notice;
			}
		}

		return self::do_sort($notices);
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves all notices currently stored.
	 *
	 * @return array/NULL All messages, sorted by the current sort setting.
	 */
	public static function all()
	{
		return self::do_sort(self::$notices);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Setters
	//--------------------------------------------------------------------

	/**
	 * Stores a single notification message. Can add it to a group and set
	 * the status of the message.
	 *
	 * There are no preset status messages so they can be customized for your
	 * needs.
	 *
	 * If no group is passed, it will be stored in a 'general' group, unless
	 * the 'use_current_module' setting is set to TRUE. Then it will use the
	 * lowercase module name for the group name.
	 *
	 * @param [type] $message [description]
	 * @param [type] $status  [description]
	 * @param [type] $group   [description]
	 */
	public static function set($message, $status, $group=null)
	{
		// Create the group if not existing
		if (empty($group) && self::$use_current_module === TRUE)
		{
			$group = strtolower(self::$ci->router->fetch_module());
		}
		// No group is set, so call it general
		else if (empty($group))
		{
			$group = 'general';
		}

		// Make sure the group exists
		if (!isset(self::$groups[$group]))
		{
			self::$groups[$group] = 0;
		}

		// Increment the count for the group
		self::$groups[$group]++;

		self::$notices[] = array(
			'time'		=> time(),
			'msg'		=> $message,
			'status'	=> $status,
			'group'		=> $group
		);

		// Make sure we always keep the session updated for future calls.
		if (class_exists('CI_Session'))
		{
			@self::$ci->session->set_userdata('bf_notice_groups', self::$groups);
			@self::$ci->session->set_userdata('bf_notices', self::$notices);
		}
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Utility Methods
	//--------------------------------------------------------------------

	/**
	 * Clears all notices currently being held.
	 *
	 * @return void
	 */
	public static function clear_all()
	{
		self::$groups = array();
		self::$notices = array();
	}

	//--------------------------------------------------------------------

	/**
	 * A simple way to set the self::$use_current_module setting. If true,
	 * will use the current module's name as the group name, if none is
	 * specified.
	 *
	 * @param  boolean $use [description]
	 * @return [type]       [description]
	 */
	public function use_module_as_group($use=false)
	{
		self::$use_current_module = (boolean)$use;
	}

	//--------------------------------------------------------------------

	/**
	 * Sorts the notices by time saved, group, status or name. The exact
	 * results will vary slightly based on the getter used to retrieve the notices.
	 *
	 * @param  string $sort_by [description]
	 * @param  [type] $dir     [description]
	 * @return [type]          [description]
	 */
	public static function sort($sort_by='time', $dir='asc')
	{
		self::$sort_by	= $sort_by;
		self::$sort_dir	= $dir;
	}

	//--------------------------------------------------------------------

	protected static function do_sort($notices=array())
	{
		$sort_by 	= self::$sort_by ? self::$sort_by : 'time';
		$sort_dir 	= self::$sort_dir && self::$sort_dir == 'asc' ? SORT_ASC : SORT_DESC;

		$messages = new stdClass();

		$rows = array();
		foreach ($notices as $key => $row)
		{
			$rows[$key] = $row[$sort_by];
		}
		array_multisort($rows, $sort_dir, $notices);

		return $notices;
	}

	//--------------------------------------------------------------------


	/**
	 * Retrieves all messages from the session and populates the $groups
	 * and $notices arrays.
	 *
	 * @return void
	 */
	protected static function import_session()
	{
		// If there's no session class loaded bail.
		// We check this way so that the exact name of the
		// session class doesn't matter (CI_Session, BF_Session, etc).
		if (!isset(self::$ci->session))
		{
			return;
		}

		$groups = self::$ci->session->userdata('bf_notice_groups');

		if (!empty($groups) && is_array($groups))
		{
			self::$groups = $groups;
			unset($groups);
		}

		$notices = self::$ci->session->userdata('br_notices');

		if (!empty($notices) && is_array($notices))
		{
			self::$notices = $notices;
			unset($notices);
		}
	}

	//--------------------------------------------------------------------

}