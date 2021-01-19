<?php

/*
Plugin Name: Ionicon Field for ACF
Plugin URI: https://github.com/Jackardios/acf-ionicon-field
Description: Adds a new 'Ionicon' field to Advanced Custom Fields plugin.
Version: 1.0.0
Author: Salakhutdinov Salavat
Author URI: https://github.com/Jackardios
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('jackardios_acf_plugin_ionicon')) :

	class jackardios_acf_plugin_ionicon
	{
		// vars
		var $settings;

		/*
		*  __construct
		*
		*  This function will setup the class functionality
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	void
		*  @return	void
		*/

		function __construct()
		{

			// settings
			// - these will be passed into the field class.
			$this->settings = array(
				'version'	=> '1.0.0',
				'url'		=> plugin_dir_url(__FILE__),
				'path'		=> plugin_dir_path(__FILE__)
			);


			// include field
			add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
			add_action('acf/register_fields', 		array($this, 'include_field')); // v4
		}


		/*
		*  include_field
		*
		*  This function will include the field type class
		*
		*  @type	function
		*  @date	17/02/2016
		*  @since	1.0.0
		*
		*  @param	$version (int) major ACF version. Defaults to false
		*  @return	void
		*/
		function include_field($version = false)
		{
			// support empty $version
			if (!$version) $version = 5;

			// load acf-ionicon
			load_plugin_textdomain('acf-ionicon', false, plugin_basename(dirname(__FILE__)) . '/lang');

			// include
			if ($version >= 5) {
				include_once('fields/class-jackardios-acf-field-ionicon-v' . $version . '.php');
			} else {
				add_action('admin_notices', array($this, 'show_unsupported_version_notice'));
			}
		}

		/*
		*  show_unsupported_version_notice
		*
		*  Show 'Unsupported version of ACF' notice
		*
		*  @return	void
		*/
		function show_unsupported_version_notice()
		{
?>
			<div class="notice notice-error">
				<p><?php __('"Ionicon Field for ACF" does not support ACF version 4 and below', 'acf-ionicon') ?></p>
			</div>
<?php
		}
	}


	// initialize
	new jackardios_acf_plugin_ionicon();


// class_exists check
endif;
