<?php
/**
 * @package wp-enqueue-cdn
 */
/*
Plugin Name: WP Enqueue CDN
Plugin URI: http://www.litefeel.com/wp-enqueue-cdn/
Description: WP Resources URL Optimization is a Wordpress plugin optimized browser cache, it will greatly enhance the website page display speed and reduce the pressure of the server to handle static files.Default wp added after the static files the query string to ensure that the static files are modified immediately after the performance to the browser (front-end), this way there is a drawback: the browser will request the server regardless of whether the file is modified,If the file has been modified it will download the new file, if the file has not been modified http status code 304 is returned to inform the browser reads the local cache. The goal of the plugin: do not have to re-initiate the request to the server, and directly read the browser cache when the file has not been modified.
Version: 1.5.1
Author: lite3
Author URI: http://www.litefeel.com/

Copyright (c) 2011
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt
*/

if (!class_exists('WP_ENQUEUE_CND')) {
	include('lite3-wp-plugin-base.php');
	class WP_ENQUEUE_CND extends LITE3_WP_Plugin_Base {

		var $wwwurl  = '';
		var $wwwpath = '';
		var $abs_wpurl = '';
		var $wwwurl_len = 0;
		
		var $abs_resources_path = '';
		var $resources_path = '';
		var $resources_url = '';
		
		function __construct() {
			parent::__construct('1.5.0', 'wpenqueuecdn_option');
			add_filter('init', array(&$this,'wpruo_init'));
		}
		public function get_default_options(){
			$options = array();
			$options['only_remove_query_string'] = true;
			return $options;
		}
		
		public function wpruo_init() {
			if(is_admin()) {
				load_plugin_textdomain( 'wpresourcesurloptimization', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
				add_action('admin_menu', array($this, 'add'));
				add_filter( 'plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
			}
			add_filter('script_loader_src', array(&$this, 'optimize_style_script_url'), 1000);
			add_filter('style_loader_src', array(&$this, 'optimize_style_script_url'), 1000);
		}
		
		public function optimize_style_script_url($src) {
			// echo "src=$src <br/>";
			// return $src;
			// src=http://cdn.litefeel.com/static/css/inove/style20140623.css?ver=20140407 
			// src=http://libs.useso.com/js/jquery/1.11.0/jquery.min.js?ver=1.11.0 
			// src=http://cdn.litefeel.com/static/js/inove/script20140712.js?ver=20140712 
			
			if (strpos($src, 'swfobject.js') !== false) {
				return 'http://libs.useso.com/js/swfobject/2.2/swfobject.min.js';
			}
			if (strpos($src, 'jquery.min.js') !== false) {
				return 'http://libs.useso.com/js/jquery/1.11.0/jquery.min.js';
			}
			if (strpos($src, 'prettify.css')) {
				return 'http://apps.bdimg.com/libs/prettify/r298/prettify.min.css';
			}
			if (strpos($src, 'prettify.js')) {
				return 'http://apps.bdimg.com/libs/prettify/r298/prettify.min.js';
			}
			if (strpos($src, '//fonts.googleapis.com/')) {
				return str_replace('//fonts.googleapis.com/','//fonts.useso.com/',$src);
			}
			return $src;
		}
		
		

		public function absolute_path($path) {
			$list = explode('/', $path);
			$len = count($list);
			for($i = 0; $i < $len; $i++) {
				$del = 0;
				if($list[$i] === '' || $list[$i] === '.') $del = 1;
				else if($list[$i] === '..') $del = 2;
				if($del != 0) {
					if($i < $del - 1) return false;
					$i -= $del;
					$len -= $del;
					array_splice($list, $i + 1, $del);
				}
			}
			$path = implode('/', $list);
			return $path;
		}
		
		public function ignore_query_string($src) {
			$pos = strpos($src, '?');
			if($pos !== FALSE) {
				$src = substr($src, 0, $pos);
			}
			return $src;
		}
		
		public function check_version() {
			if(!file_exists($this->resources_path)) {
				mkdir($this->resources_path, 0755, TRUE);
			}
			$file = $this->resources_path . $this->version;
			if(!file_exists($file)) {
				$this->del_tree($this->resources_path, TRUE);
				file_put_contents($file, $this->version);
			}
		}
		
		

		public function plugin_action_links( $links, $file ) {
			if ( $file != plugin_basename( __FILE__ )) return $links;

			$settings_link = '<a href="options-general.php?page=wp-enqueue-cdn/wp-enqueue-cdn.php">' . __( 'Settings', 'wpresourcesurloptimization' ) . '</a>';
			array_push( $links, $settings_link );
			return $links;
		}

		public function add() {
			if(isset($_POST['wpruo_save'])) {
				$options = $this->get_options();

				// set cache file
				if(!$_POST['only_remove_query_string']) {
					$options['only_remove_query_string'] = (bool)false;
				} else {
					$options['only_remove_query_string'] = (bool)true;
				}
				$this->update_options($options);
			} elseif(isset($_POST['wpruo_reset'])) {
				$this->reset_options();
			} elseif (isset($_POST['wpruo_clear_cache'])) {
				$this->clear_cache();
			}

			add_options_page('WP Resources URL Optimization', 'WP Resources URL Optimization', 10, __FILE__, array($this, 'display'));
		}

		public function display() {
		$options = $this->get_options();
?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e('WP Resources URL Optimization Options', 'wpresourcesurloptimization'); ?></h2>

	<div id="poststuff" class="has-right-sidebar">
		<div class="inner-sidebar">
			<div id="donate" class="postbox" style="border:2px solid #080;">
				<h3 class="hndle" style="color:#080;cursor:default;"><?php _e('Donation', 'wpresourcesurloptimization'); ?></h3>
				<div class="inside">
					<p><?php _e('If you like this plugin, please donate to support development and maintenance!', 'wpresourcesurloptimization'); ?>
					<br /><br /><strong><a href="https://me.alipay.com/lite3" target="_blank"><?php _e('Donate by alipay', 'wpresourcesurloptimization'); ?></a></strong><style>#donate form{display:none;}</style>
					</p>
				</div>
			</div>

			<div class="postbox">
				<h3 class="hndle" style="cursor:default;"><?php _e('About Author', 'wpresourcesurloptimization'); ?></h3>
				<div class="inside">
					<ul>
						<li><a href="http://www.litefeel.com/" target="_blank"><?php _e('Author Blog', 'wpresourcesurloptimization'); ?></a></li>
						<li><a href="http://www.litefeel.com/plugins/" target="_blank"><?php _e('More Plugins', 'wpresourcesurloptimization'); ?></a></li>
					</ul>
				</div>					
			</div>
		</div>

		<div id="post-body">
			<div id="post-body-content">

<form action="#" method="POST" enctype="multipart/form-data" name="wp-enqueue-cdn_form">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Only remove query string of URL', 'wpresourcesurloptimization'); ?></th>
					<td>
						<label>
							<input name="only_remove_query_string" type="checkbox" <?php if($options['only_remove_query_string']) echo 'checked="checked"'; ?> />
							 <?php _e('Only remove query string of URL, if not, Cache js, css and css sprite file to resource directory.', 'wpresourcesurloptimization'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
		<input class="button-primary" type="submit" name="wpruo_save" value="<?php _e('Update Options', 'wpresourcesurloptimization'); ?>" />
		<input class="button-primary" type="submit" name="wpruo_reset" value="<?php _e('Reset Settings to Defaults', 'wpresourcesurloptimization'); ?>" />
		<input class="button-primary" type="submit" name="wpruo_clear_cache" value="<?php _e('Clear cache directory', 'wpresourcesurloptimization'); ?>" />
		</p>
</form>
			</div>
		</div>
	</div>
</div>

<?php
		}
	}
	$wp_resources_URL_optimization = new WP_ENQUEUE_CND();
}

?>
