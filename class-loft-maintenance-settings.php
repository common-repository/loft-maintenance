<?php
// Not allowed by directly accessing.
if(!defined('ABSPATH')){
	die('Access not allowed!');
}

/**
 * Main class for plugin settings page
 * 
 * @package   Loft Maintenance
 * @version   1.0
 * @link	  http://www.loftocean.com/
 * @author	  Suihai Huang from Loft Ocean Team
 * @since version 1.0
 */

if(!class_exists('Loft_Maintenance_Settings')){
	class Loft_Maintenance_Settings{
		private $page_id; // Plugin setting page id
		private $section_id; // Plugin setting section id
		public function __construct(){
			$this->page_id = 'loft-maintenance-settings';
			$this->section_id = 'loft_maintenance_settings_section';
			add_action('admin_init', array($this, 'register_setting_fields')); // Register setting fields and settings for saving
			add_action('admin_menu', array($this, 'add_settings_menu')); // Add plugin setting menu
		}
		/**
		 * @description add plugin settings menu
		 */
		public function add_settings_menu(){
			add_options_page(
				esc_html__('Loft Maintenance Settings', 'loft-maintenance'), // Page title on html head
				esc_html__('Loft Maintenance', 'loft-maintenance'), // Menu item label
				'manage_options',
				$this->page_id,
				array($this, 'render_settings_page')
			); // Register the plugin option subpage
		}
		/**
		 * @description render the plugin settings page
		 */
		public function render_settings_page(){
?>
			<div class="wrap">
				<h1><?php esc_html_e('Loft Maintenance Settings', 'loft-maintenance'); ?></h1>
				<form method="post" action="<?php echo admin_url('options.php'); ?>">
					<?php do_settings_sections($this->page_id); ?>
					<?php settings_fields($this->section_id); ?>
					<?php $this->get_save_button(); ?>
				</form>
			</div>
<?php
		}
		/**
		 * @description register setting fields and settings for saving
		 */
		public function register_setting_fields(){
			$section_id = $this->section_id;
			add_settings_section($section_id, '', '', $this->page_id); // Register plugin setting section

			add_settings_field('loft_maintenance_settings_mode', esc_html__('Maintenance Mode', 'loft-maintenance'), array($this, 'maintenance_mode'), $this->page_id, $section_id);
			add_settings_field('loft_maintenance_settings_page_id', esc_html__('Maintenance Page', 'loft-maintenance'), array($this, 'maintenance_page'), $this->page_id, $section_id);
			add_settings_field('loft_maintenance_settings_other_public_pages', esc_html__('Other Public Pages', 'loft-maintenance'),  array($this, 'maintenance_other_pages'), $this->page_id, $section_id);

			// Register the settings for saving
			register_setting($section_id, 'loft_maintenance_mode'); // Register setting maintenance mode
			register_setting($section_id, 'loft_maintenance_page_id'); // Register setting maintenance page
			register_setting($section_id, 'loft_maintenance_other_public_pages'); // Register setting other public pages
		}
		/**
		 * @description show the maintenance mode setting html 
		 */
		public function maintenance_mode($args){
			$checked = (get_option('loft_maintenance_mode', '') === 'on') ? ' checked' : '';
			echo '<label><input' . $checked . ' name="loft_maintenance_mode" type="checkbox" value="on">' . esc_html__('Enable', 'loft-maintenance') . '</label>';
			echo '<p>' . esc_html__('Enable maintenance mode, then your site is locked to all anonymous visitors. Only logged in users can access to the site.', 'loft-maintenance') . '</p>';
		}
		/**
		 * @description html of maintenance page setting
		 */
		public function maintenance_page($args){
			wp_dropdown_pages(
				array(
					'name' => 'loft_maintenance_page_id',
					'echo' => 1,
					'show_option_none' => '&mdash; ' . esc_html__('Select', 'loft-maintenance') . ' &mdash;',
					'option_none_value' => '-1',
					'selected' => get_option('loft_maintenance_page_id')
				)
			);
		}
		/**
		 * @description html of other public pages setting when on maintenance mode
		 */
		public function maintenance_other_pages($args){
			$list = get_option('loft_maintenance_other_public_pages', array());
			$list = (!empty($list) && is_array($list)) ? $list : array();
			$maintenance_page = get_option('loft_maintenance_page_id', -1);
	 		$html = '';
			$pages = $this->get_pages(); // Get page list
			if($pages){
				$html .= '<ul>';
				foreach($pages as $p){
					$page = $p['page'];
					$pid = $page->ID;
					$title = esc_html($page->post_title);
					$checked = in_array($pid, $list) ? ' checked' : '';
					$html .= '<li>' . $this->get_indentation($p['depth']) . '<label for="loft_maintenance_other_page_' . $pid . '"><input type="checkbox"' . $checked . ' id="loft_maintenance_other_page_' . $pid . '" name="loft_maintenance_other_public_pages[]" value="' . $pid . '">&nbsp;' . $title . '</label></li>';
				}
				$html .= '</ul>';
			}
			echo $html; 
		}
		/**
		* @description helper function get the page list
		* @param int parent page id
		* @param int depth
		* @return mix if any page exists return array otherwise return false
		*/
		private function get_pages($parent = 0, $depth = 0){
			$return = array();
			$pages = get_pages(array('parent' => $parent));
			if($pages){
				foreach($pages as $p){
					array_push($return, array('page' => $p, 'depth' => $depth));
					$subs = $this->get_pages($p->ID, ($depth + 1));
					$return = $subs ? array_merge($return, $subs) : $return;
				}
			}
			return empty($return) ? false : $return;
		}
		/**
		* @description helper function get the indentation for each item
		* @param int depth
		* @return string
		*/
		private function get_indentation($depth = 0){
			$indentation = '';
			for($i = 0; $i < $depth; $i++){
				$indentation .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			return $indentation;
		}
		/**
		 * @description get save button
		 * @return save button html
		*/
		private function get_save_button(){
			echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="' . esc_attr__('Save Changes', 'loft-maintenance') . '"></p>';
		}
	}
	new Loft_Maintenance_Settings();
}