<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Safecracker HTML5 Attributes Extension
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Extension
 * @author		Justin Kimbrell
 * @link		http://www.objectivehtml.com
 * @author		Jason Varga
 * @link		http://pixelfear.com
 */

class Safecracker_html5_attributes_ext {
	
	public $settings 		= array();
	public $description		= 'Add HTML5 attributes to the available Channel form Parameters.';
	public $docs_url		= '';
	public $name			= 'Channel form HTML5 Attributes';
	public $settings_exist	= 'n';
	public $version			= '1.1';
	
	private $EE, $obj, $params;
	
	/**
	 * Constructor
	 *
	 * @param 	mixed	Settings array or empty string if none exist.
	 */
	public function __construct($settings = '')
	{
		$this->EE =& get_instance();
		$this->settings = $settings;
	}// ----------------------------------------------------------------------
	
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @see http://codeigniter.com/user_guide/database/index.html for
	 * more information on the db class.
	 *
	 * @return void
	 */
	public function activate_extension()
	{
		// Setup custom settings in this array.
		$this->settings = array();

		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'channel_form_entry_form_tagdata_end',
			'hook'		=> 'channel_form_entry_form_tagdata_end',
			'settings'	=> serialize($this->settings),
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);

		$this->EE->db->insert('extensions', $data);	
	}	

	// ----------------------------------------------------------------------

	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('extensions');
	}

	// ----------------------------------------------------------------------

	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return 	mixed	void on update / false if none
	 */
	function update_extension($current = '')
	{
		if ($current == '' OR $current == $this->version)
		{
			return FALSE;
		}
	}	
	
	// ----------------------------------------------------------------------

	public function channel_form_entry_form_tagdata_end($tagdata, $channel_form_obj)
	{
		//$this->obj =& $obj;

		// data-* attributes
		$params = ee()->TMPL->tagparams;
		$attrs = '';

		foreach ($params as $attr => $val)
		{
			if (preg_match('/^data-/', $attr)) {
				$attrs .= $attr . '="'.$val.'" ';
			}
		}

		// additional html5 attributes
		$params = array(
			'autocomplete',
			'novalidate'
		);

		foreach($params as $param)
		{
			if($value = $this->param($param, FALSE))
			{
				$attrs .= $param . '="'.$value.'" ';
			}
		}

		// modify the tag data with our new form attributes
		$tagdata = str_replace('<form ', '<form '.$attrs , $tagdata);

		return $tagdata;
	}

	private function param($param, $default = FALSE, $boolean = FALSE, $required = FALSE)
	{
		$name	= $param;
		$param 	= ee()->TMPL->fetch_param($param);
		
		if($required && !$param) show_error('You must define a "'.$name.'" parameter in the '.__CLASS__.' tag.');
			
		if($param === FALSE && $default !== FALSE)
		{
			$param = $default;
		}
		else
		{				
			if($boolean)
			{
				$param = strtolower($param);
				$param = ($param == 'true' || $param == 'yes') ? TRUE : FALSE;
			}			
		}
		
		return $param;			
	}
}

/* End of file ext.safecracker_html5_attributes.php */
/* Location: /system/expressionengine/third_party/safecracker_html5_attributes/ext.safecracker_html5_attributes.php */