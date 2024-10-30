<?php
/*
Plugin Name: Loft Maintenance
Plugin URI: http://www.loftocean.com/
Description: A toolkit to help you lock down your site when your site is not ready to go public.
Version: 1.0.0
Author: Loft Ocean
Author URI: http://www.loftocean.com/
Text Domain: loft-maintenance
Domain Path: /languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


/**
 * Loft Maintenance main file
 * 
 * @package   Loft Maintenance
 * @version   1.0
 * @link	  http://www.loftocean.com/
 * @author	  Suihai Huang from Loft Ocean Team
 */

// Not allowed by directly accessing.
if(!defined('ABSPATH')){
	die('Access not allowed!');
}

if(!class_exists('Loft_Maintenance')){
	register_activation_hook(__FILE__, 'loft_maintenance_activate'); // 
	register_deactivation_hook(__FILE__, 'loft_maintenance_deactivate');
	/*
	 * Update the plugin version for initial version
	 */
	function loft_maintenance_activate(){
		update_option('loft_maintenance_plugin_version', '1.0.0');
	}
	/**
	 * Do nothing for initial version
	 */
	function loft_maintenance_deactivate(){ }
	/**
	 * Define the constant used in this plugin
	 */
	define('LOFTMAINTENANCE_ROOT', dirname(__FILE__) . '/');

	/**
	 * Main plugin class
	 * @since Loft Maintenance version 1.0.0
	 */
	class Loft_Maintenance{
		public function __construct(){
			load_plugin_textdomain('loft-maintenance', false, dirname(plugin_basename(__FILE__)) . '/languages/'); // Load the text domain
			add_action('init', array($this, 'load_settings')); // Load the plugin settings
			add_action('wp', array($this, 'load_front'), 9999); // Load the front main class
		}
		/**
		 * @description load plugin settings page
		 */
		public function load_settings(){
			if(is_admin()){
				require_once LOFTMAINTENANCE_ROOT . 'class-loft-maintenance-settings.php';
			}
		}
		/**
		 * @description load functions for front end
		 */
		public function load_front(){
			if(!is_admin()){
				require_once LOFTMAINTENANCE_ROOT . 'class-loft-maintenance-front.php';
			}
		}
	}
	new Loft_Maintenance(); // Enable Loft_Maintenance
}
?>
