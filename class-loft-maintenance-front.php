<?php
// Not allowed by directly accessing.
if(!defined('ABSPATH')){
	die('Access not allowed!');
}

/**
 * Main class for front end
 * 
 * @package   Loft Maintenance
 * @version   1.0
 * @link	  http://www.loftocean.com/
 * @author	  Suihai Huang from Loft Ocean Team
 * @since version 1.0
 */

if(!class_exists('Loft_Maintenance_Front')){
	class Loft_Maintenance_Front{
		private $maintenance_mode;
		private $maintenance_page_id;
		private $public_page_ids;
		public function __construct(){
			$this->set_variables();
			add_action('template_redirect', array($this, 'page_redirection')); // Page redirection for 404 page or maintenance page if current in maintenance mode
		}
		/**
		 * @description set maintenance mode status, maintenance main page id and other public pages if set.
		 */
		private function set_variables(){
			$this->maintenance_mode = get_option('loft_maintenance_mode', '');
			$this->maintenance_page_id = get_option('loft_maintenance_page_id', -1);
			$this->public_page_ids = get_option('loft_maintenance_other_public_pages', array());
		}
		/**
		 * @description redirect pages
		 *  1. Redirect to maintenance main page if current in maintenance mode and access to the pages not allowed
		 *       Site administrator has full privileges to all front end even enable the maintenance mode
		 *       Will not redirect the 404 error page
		 *     
		 */
		public function page_redirection(){
			if(!(is_user_logged_in() && is_super_admin()) && !is_404() && $this->maintenance_mode_enabled() && !$this->in_maintenance_list($this->get_current_page_id())){
				wp_redirect(get_permalink($this->maintenance_page_id));
				exit();
			}
		}
		/**
		 * @description helper function get current page id
		 * @return boolean if access to archive page then return false, otherwise return true
		*/
		private function get_current_page_id(){
			$queriedObject  = get_queried_object();
			return ('WP_Post' === get_class($queriedObject)) ? $queriedObject->ID : false;
		}
		/*
		 * @description test maintenance page exists and the site is in maintenance mode.
		 * @return boolean
		 */
		function maintenance_mode_enabled(){
			return ($this->maintenance_mode === 'on') && (get_post_status($this->maintenance_page_id) !== false); 
		}

		/*
		 * @description test if current page in public page list or the main maintenance page
		 * @param int $pid current page id
		 * @return boolean
		 */
		function in_maintenance_list($pid){
			$list = (!empty($this->public_page_ids) && is_array($this->public_page_ids)) ? $this->public_page_ids : array();
			((get_post_status($this->maintenance_page_id) !== false) && !in_array($this->maintenance_page_id, $list)) ? array_push($list, $this->maintenance_page_id) : '';
			return !empty($pid) && in_array($pid, $list);
		}
	}
	new Loft_Maintenance_Front();
}