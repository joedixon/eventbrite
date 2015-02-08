<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include(PATH_THIRD.'eventbrite_ee/config.php');

/**
 * Low Likes Update class
 *
 * @package        eventbrite
 * @author         Joe Dixon <hello@grazemedia.com>
 * @link           https://wwww.grazemedia.com
 * @copyright      Copyright (c) 2013, Graze Media
 */
class Eventbrite_ee_upd {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * This version
	 *
	 * @access      public
	 * @var         string
	 */
	public $version = EVENTBRITE_VERSION;

	/**
	 * EE Superobject
	 *
	 * @access      private
	 * @var         object
	 */
	private $EE;

	/**
	 * Class name
	 *
	 * @access      private
	 * @var         array
	 */
	private $class_name;

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access     public
	 * @return     void
	 */
	public function __construct()
	{

		// --------------------------------------
		// Set class name
		// --------------------------------------

		$this->class_name = ucfirst(EVENTBRITE_PACKAGE);
	}

	// --------------------------------------------------------------------

	/**
	 * Install the module
	 *
	 * @access      public
	 * @return      bool
	 */
	public function install()
	{
		// --------------------------------------
		// Install tables
		// --------------------------------------

		// Load DB Forge class
		ee()->load->dbforge();

		// Define fields to create
		ee()->dbforge->add_field(array(
			'user_key' => array('type' => 'varchar', 'constraint' => '50'),
		));

		// Creates the table
		ee()->dbforge->create_table('eventbrite_settings');

		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		ee()->db->insert('modules', array(
			'module_name'    => $this->class_name,
			'module_version' => $this->version,
			'has_cp_backend' => 'y'
		));

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstall the module
	 *
	 * @return	bool
	 */
	public function uninstall()
	{
		// --------------------------------------
		// get module id
		// --------------------------------------

		$query = ee()->db->select('module_id')
		       ->from('modules')
		       ->where('module_name', $this->class_name)
		       ->get();

		// --------------------------------------
		// remove references from module_member_groups
		// --------------------------------------

		ee()->db->where('module_id', $query->row('module_id'));
		ee()->db->delete('module_member_groups');

		// --------------------------------------
		// remove references from modules
		// --------------------------------------

		ee()->db->where('module_name', $this->class_name);
		ee()->db->delete('modules');

		// --------------------------------------
		// Uninstall tables
		// --------------------------------------

		ee()->load->dbforge();
		ee()->dbforge->drop_table('eventbrite_settings');

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the module
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function update($current = '')
	{
		// --------------------------------------
		// Same version? A-okay, daddy-o!
		// --------------------------------------

		if ($current == '' || version_compare($current, $this->version) === 0)
		{
			return FALSE;
		}

		// // Update to next version
		// if (version_compare($current, 'next-version', '<'))
		// {
		// 	// ...
		// }

		// Return TRUE to update version number in DB
		return TRUE;
	}

	// --------------------------------------------------------------------

} // End class

/* End of file upd.low_likes.php */