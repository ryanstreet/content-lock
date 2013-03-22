<?php
/**
 * Plugin Name: Content Lock
 * Plugin URI: http://www.coolryan.com/plugins/content-lock/
 * Description: Show or hide your content with a wide variety of options
 * Author Cool Ryan
 * Version: 1.0
 * Author URI: http://www.coolryan.com/
 */

/**
 * 
 * Allows content to be displayed flexibly throughout your page/post with a wide variety of shortcodes.
 * You can also nest your shortcodes for nearly infinite display options.
 * Debugging mode that allows you to ensure your content is being displayed the way you want it to.
 * 
 * @author ryan.street
 * @package Content_Lock
 * 
 */
class Content_Lock {
	
	protected $debug = false;
	protected $atts = array();
	
	public function __construct() {
		// Debug Functions
		$this->set_debug();
		add_action('wp_footer', array(&$this, 'debug_output'));
		
		// Shortcodes
		add_shortcode('logged_in', array(&$this, 'shortcode_logged_in')); // Logged In
		add_shortcode('logged_out', array(&$this, 'shortcode_logged_out')); // Logged out
		add_shortcode('came_from', array(&$this, 'shortcode_came_from')); // Referer
		add_shortcode('user_is', array(&$this, 'shortcode_user_is')); // role
		add_shortcode('user_is_not', array(&$this, 'shortcode_user_is_not')); // role
		add_shortcode('user_can', array(&$this, 'shortcode_user_can')); // Capability
		add_shortcode('user_cannot', array(&$this, 'shortcode_user_cannot')); // Capability
		add_shortcode('user_can_not', array(&$this, 'shortcode_user_cannot')); // in case of misspellings
		add_shortcode('has_key', array(&$this, 'shortcode_has_key')); // URL Key
		add_shortcode('password', array(&$this, 'shortcode_password'));
		add_shortcode('click', array(&$this, 'shortcode_click')); // Action Based
	}
	
	
	/**
	 * Shows content if user is logged in
	 * 
	 * Usage: [logged_in][/logged_in]
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_logged_in($atts, $content = null) {
		if(is_user_logged_in()) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if user is logged out
	 * 
	 * Usage: [logged_out][/logged_out]
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_logged_out($atts, $content = null) {
		if(!is_user_logged_in()) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if user came from a certain URL
	 * 
	 * Usage: [came_from="http://www.google.com/"][/came_from]
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_came_from($atts, $content = null) {
		$this->atts['came_from'] = shortcode_atts( array(
			'location' => $this->fix_attribute($atts[0])
		), $atts );
		
		if($_SERVER['HTTP_REFERER'] == $this->atts['came_from']['location']) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if user is a certain user role
	 * 
	 * Usage: [user_is="administrator"][/user_is] or [user_is role="administrator"][/user_is]
	 * Accepted arguments: administrator|author|contributor|editor|subscriber
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return $content
	 */
	public function shortcode_user_is($atts, $content = null) {
		global $user;
		$this->atts['user_is'] = shortcode_atts(array(
			'role' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(user_can($user, $this->atts['user_is']['role'])) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if user is not a certain user role
	 * 
	 * Usage: [user_is_not="administrator"][/user_is_not] or [user_is_not role="administrator"][/user_is_not]
	 * Accepted arguments: administrator|author|contributor|editor|subscriber
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_user_is_not($atts, $content = null) {
		global $user;
		$this->atts['user_is_not'] = shortcode_atts(array(
			'role' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(!user_can($user, $this->atts['user_is_not']['role'])) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if a certain perform a certain capability
	 * 
	 * Usage: [user_can="edit_posts"][/user_can] or [user_can capability="edit_posts"][/user_can]
	 * 
	 * @see http://www.wordpress.org/Roles_And_Capabilities
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_user_can($atts, $content = null) {
		global $user;
		
		$this->atts['user_can'] = shortcode_atts(array(
			'capability' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(user_can($user, $this->atts['user_can']['capability'])) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if a user cannot perform a certain capability
	 * 
	 * Usage: [user_cannot="edit_posts"][/user_cannot] or [user_cannot capability="edit_posts"][/user_cannot]
	 * 
	 * @see http://www.wordpress.org/Roles_And_Capabilities
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_user_cannot($atts, $content = null) {
		global $user;
		
		$this->atts['user_cannot'] = shortcode_atts(array(
			'capability' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(!user_can($user, $this->atts['user_cannot']['capability'])) {
			return do_shortcode($content);
		}
	}
	
	
	/**
	 * Shows content if the url parameter "lock_key" is set and matches the accepted argument
	 * 
	 * Usage: [has_key="mysecretkey"][/has_key] or [has_key key="mysecretkey"][/has_key]
	 * 
	 * Example URL: http://www.domain.com/?lock_key=mysecretkey
	 * 
	 * @param array $atts
	 * @param string $content
	 * @param string $content
	 */
	public function shortcode_has_key($atts, $content = null) {
		$this->atts['has_key'] = shortcode_atts(array(
			'key' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(isset($_GET['lock_key']) && $_GET['lock_key'] == $this->atts['has_key']['key']) {
			return do_shortcode($content);
		} 
	} 
	
	
	/**
	 * Shows content if you put in a correct password from the form on the page.
	 * 
	 * Usage: [password="mypassword"][/password]
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_password($atts, $content = null) {
		$this->atts['password'] = shortcode_atts(array(
			'password' => $this->fix_attribute($atts[0])
		), $atts);
		
		if(isset($_POST['lock_password']) && $_POST['lock_password'] == $this->atts['password']['password']) {
			return do_shortcode($content);
		}
		
		else return $this->password_display();
	}
	
	
	/**
	 * Shows content if a user clicks on a link on the page.  Content also hides if link is clicked again.
	 * 
	 * Usage: [click][/click]
	 * 
	 * Parameters:
	 * 		text - link text
	 * 		class - div class that wraps content.
	 * 
	 * @param array $atts
	 * @param string $content
	 * @return string $content
	 */
	public function shortcode_click($atts, $content = null) {
		wp_enqueue_script('jquery' ,'', '', '', TRUE);
		
		$this->atts['click'] = shortcode_atts( array(
			'text' => 'More...',
			'class' => 'spoiler',
			
		), $atts );
		
		$unique = round(rand(1,1000));
		
		$return = '<a href="javascript:void(0);" onclick="jQuery(\'#more-div-' . $unique .'\').toggle();">'.$this->atts['click']['text'].'</a>';
		$return .= '<div id="more-div-' .$unique. '" style="display:none;" class="'.$this->atts['click']['class'].'">'.do_shortcode($content).'</div>';
		
		return $return;
		
	}
	
	
	/**
	 * Displays the password form field for the password shortcode.
	 * 
	 * @param array $atts
	 * @return string $field
	 */
	protected function password_display($atts = array()) {
		
		$field = '<form id="'.$atts['form_id'].'" action="'.$_SERVER['REQUEST_URI'] . '" method="POST">';
		$field .= '<span class="">Please enter a password to view this content</span><br />';
		$field .= 'Password: <input type="password" name="lock_password" />';
		$field .= '</form>';
		
		return $field;
	}
	
	
	/**
	 * Shows debugging information in the footer if the url parameter "content_lock_deubg" is set.
	 */
	protected function set_debug() {
		if(isset($_REQUEST['content_lock_debug'])) {
			$this->debug = TRUE;
			$this->debug_type = $_REQUEST['content_lock_debug'];
		}
	}
	
	
	/**
	 * Cleans up attributes for bbcode style shortcodes
	 * 
	 * @param mixed $attribute
	 */
	protected function fix_attribute($attribute) {
		return str_ireplace(array('="', '"'), '', $attribute);
	}
	
	
	/**
	 * Displays all attributes set throughout the content
	 */
	public function display_atts() {
		var_dump($this->atts);
	}
	
	
	/**
	 * Debugging output that is displayed at the bottom of the page when debugging is turned on. 
	 */
	public function debug_output() {
		if($this->debug && current_user_can('administrator')) {
			echo '<pre><strong>Content Lock Debug</strong>';
			switch($this->debug_type) {
				case 'came_from':
					echo 'Referring page is: ' .$_SERVER['HTTP_REFERER'];
				default:
					$this->display_atts();
			}
			echo '</pre>';
		}
	}
}

$content_lock = new Content_Lock();