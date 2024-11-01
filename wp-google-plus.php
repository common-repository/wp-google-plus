<?php
/*
Plugin Name: WP Google Plus
Plugin URI: http://www.garmantech.com/wordpress-plugins/wp-google-plus/
Description: Add Google Plus rel tags to the head of your website.
Version: 1.0.1
Author: Garman Technical Services
Author URI: http://www.garmantech.com/wordpress-plugins/
License: GPLv2
*/

/*  Copyright 2011  Garman Technical Services  (email : contact@garmantech.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('plugins_loaded', 'init_gt_google_plus');
function init_gt_google_plus() { $gt_google_plus = new gt_google_plus; }

class gt_google_plus {

	function __construct() {
		add_action('wp_head', array(&$this,'add_code'));
		$this->framework();
	}

	function add_code() {
		$plus_id = get_option('gplus_id');
		echo '
<link href="https://plus.google.com/'.$plus_id.'" rel="publisher" />
<script type="text/javascript">(function() 
{var po = document.createElement("script");
po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/plusone.js";
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(po, s);
})();</script>';
	}

	function options_page() { ?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2>WP Google Plus</h2>
			<form action="options.php" method="post">
					<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">	
						<form action="options.php" method="post">
							<div id="settings">
								<div class="inside">
									<?php settings_fields('gplus'); ?>
									<?php do_settings_sections(basename(__FILE__,'.php')); ?>
								</div>
							</div>
							<p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p>
						</form>
					</div>
				</div>
				<div class="postbox-container" style="width:29%;">
					<div class="metabox-holder">	




					</div>
				</div>
			</form>
			<div style="position:fixed; top:25%; right:5px;"><a href="#" onClick="script: Zenbox.show(); return false;"><img src="https://apps.garmantech.com/files/support_right.png" /></a></div>
			<script type="text/javascript" src="//asset0.zendesk.com/external/zenbox/v2.3/zenbox.js"></script>
			<style type="text/css" media="screen, projection">
			  @import url(//asset0.zendesk.com/external/zenbox/v2.3/zenbox.css);
			</style>
			<script type="text/javascript">
			  if (typeof(Zenbox) !== "undefined") {
			    Zenbox.init({
			      dropboxID:	"20029372",
			      url:		"https://garmantech.zendesk.com",
			      tabID:		"support",
			      tabColor:	"black",
			      tabPosition:	"Right",
			      hide_tab:	true,
			    });
			  }
			</script>
		</div>
	<?php }

	function admin_menus() {
		add_options_page('Google Plus', 'Google Plus', 'edit_theme_options', basename(__FILE__,'.php'), array(&$this,'options_page'));
	}
	
	function on_activation(){
	}
	
	function on_deactivation(){
		$settings = $this->settings_list();
		foreach ($settings as $setting) {
			delete_option($setting['name']);
		}
	}
	
	function register_settings() {
		$settings = $this->settings_list();
		add_settings_section('gplus_options', 'Google Plus Options', array(&$this,'settings_main'), basename(__FILE__,'.php'));
		foreach ($settings as $setting) {
			add_option($setting['name'], $setting['value']);
			register_setting('gplus',$setting['name']);
			add_settings_field($setting['name'], $setting['display'], array(&$this,'settings_'.$setting['name']), basename(__FILE__,'.php'), 'gplus_options');
		}
	}

	function settings_main(){ echo '<p>Setup your WordPress site with Google Plus!</p>'; }
	function settings_gplus_id(){ $this->textbox('gplus_id','your Google Plus ID goes here'); }

	function textbox($name,$hint) {
		echo '<input type="textbox" name="'.$name.'" value="'.get_option($name).'" />';
		echo '<em>('.$hint.')</em>';
	}

	function settings_list() {
		$settings = array(
			array(
				'display' => 'Google Plus ID',
				'name' => 'gplus_id',
				'value' => null,
			),
		);
		return $settings;
	}
	
	function plugin_data($key){
		//[Name],[PluginURI],[Version],[Description] ,[Author],[AuthorURI],[TextDomain],[DomainPath],[Network],[Title],[AuthorName]
		$data = get_plugin_data(__FILE__);
		return $data[$key];
	}
	
	function settings_link($links) {
		$support_link = '<a href="http://www.garmantech.com/wordpress-plugins/support/" target="_blank">Support</a>';
		array_unshift($links, $support_link);
		$settings_link = '<a href="options-general.php?page='.basename(__FILE__,'.php').'">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	
	function framework() {
		if (is_admin()) {
			add_action('admin_menu', array(&$this,'admin_menus'));
			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array(&$this,'settings_link'));
		}
		add_action('admin_init', array(&$this, 'register_settings'));
		register_activation_hook(__FILE__, array(&$this, 'on_activation'));
		register_deactivation_hook(__FILE__, array(&$this, 'on_deactivation'));
	}
	
}

/**
 * Foo_Widget Class
 */
add_action( 'widgets_init', create_function( '', 'register_widget("GPlusBadge");' ) );
class GPlusBadge extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::WP_Widget( /* Base ID */'gplusbadge', /* Name */'Google Plus Badge', array( 'description' => 'Adds a Google Plus badge to your website.' ) );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		echo '<g:plus href="https://plus.google.com/'.get_option('gplus_id').'" size="'.$instance['size'].'"></g:plus>';
		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['size'] = strip_tags($new_instance['size']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Size:'); ?></label> 
			<select class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
				<option value="badge" <?php echo ($instance['size'] == 'badge' ? 'selected="selected"' : '') ?>>Badge</option>
				<option value="smallbadge" <?php echo ($instance['size'] == 'smallbadge' ? 'selected="selected"' : '') ?>>Small Badge</option>
			</select>
		</p>
		<?php 
	}

} // class Foo_Widget
