<?php

/**
 * Main plugin class file.
 *
 * @package WordPress Test Plugin/Includes
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main plugin class.
 */
class WP_Test_Plugin
{

	/**
	 * The single instance of WP_Test_Plugin.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of WP_Test_Plugin_Admin_API
	 *
	 * @var WP_Test_Plugin_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct($file = '', $version = '1.0.0')
	{
		$this->_version = $version;
		$this->_token   = 'wp_test_plugin';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname($this->file);
		$this->assets_dir = trailingslashit($this->dir) . 'assets';
		$this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

		$this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook($this->file, array($this, 'install'));

		// Load frontend JS & CSS.
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10);

		// Load admin JS & CSS.
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

		//Add the site info shortcode
		add_shortcode('wpt_site_info', array($this, 'site_info'), 10);

		// Show the site_info if the "show_in_footer" option is enabled		
		if ($this->get_option("show_in_footer", false)) {
			add_action('wp_footer', array($this, 'site_info'));
		}

		// Load API for generic admin functions.
		if (is_admin()) {
			$this->admin = new WP_Test_Plugin_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action('init', array($this, 'load_localisation'), 0);
	} // End __construct ()

	/**
	 * Register post type function.
	 *
	 * @param string $post_type Post Type.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param string $description Description.
	 * @param array  $options Options array.
	 *
	 * @return bool|string|WP_Test_Plugin_Post_Type
	 */
	public function register_post_type($post_type = '', $plural = '', $single = '', $description = '', $options = array())
	{

		if (!$post_type || !$plural || !$single) {
			return false;
		}

		$post_type = new WP_Test_Plugin_Post_Type($post_type, $plural, $single, $description, $options);

		return $post_type;
	}

	/**
	 * Wrapper function to register a new taxonomy.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $plural Plural Label.
	 * @param string $single Single Label.
	 * @param array  $post_types Post types to register this taxonomy for.
	 * @param array  $taxonomy_args Taxonomy arguments.
	 *
	 * @return bool|string|WP_Test_Plugin_Taxonomy
	 */
	public function register_taxonomy($taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array())
	{

		if (!$taxonomy || !$plural || !$single) {
			return false;
		}

		$taxonomy = new WP_Test_Plugin_Taxonomy($taxonomy, $plural, $single, $post_types, $taxonomy_args);

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   1.0.0
	 */
	public function enqueue_styles()
	{
		wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/frontend.css', array(), $this->_version);
		wp_enqueue_style($this->_token . '-frontend');
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/frontend' . $this->script_suffix . '.js', array('jquery'), $this->_version, true);
		wp_enqueue_script($this->_token . '-frontend');
	} // End enqueue_scripts ()

	/**
	 * Admin enqueue style.
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return void
	 */
	public function admin_enqueue_styles($hook = '')
	{
		wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/admin.css', array(), $this->_version);
		wp_enqueue_style($this->_token . '-admin');
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function admin_enqueue_scripts($hook = '')
	{
		wp_register_script($this->_token . '-admin', esc_url($this->assets_url) . 'js/admin' . $this->script_suffix . '.js', array('jquery'), $this->_version, true);
		wp_enqueue_script($this->_token . '-admin');
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation()
	{
		load_plugin_textdomain('wp-test-plugin', false, dirname(plugin_basename($this->file)) . '/lang/');
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = 'wp-test-plugin';

		$locale = apply_filters('plugin_locale', get_locale(), $domain);

		load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
		load_plugin_textdomain($domain, false, dirname(plugin_basename($this->file)) . '/lang/');
	} // End load_plugin_textdomain ()

	/**
	 * Main WP_Test_Plugin Instance
	 *
	 * Ensures only one instance of WP_Test_Plugin is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object WP_Test_Plugin instance
	 * @see WP_Test_Plugin()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance($file = '', $version = '1.0.0')
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self($file, $version);
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Cloning of WP_Test_Plugin is forbidden')), esc_attr($this->_version));
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, esc_html(__('Unserializing instances of WP_Test_Plugin is forbidden')), esc_attr($this->_version));
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function install()
	{
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	private function _log_version_number()
	{ //phpcs:ignore
		update_option($this->_token . '_version', $this->_version);
	} // End _log_version_number ()

	public function get_option($option_name, $default = "")
	{
		return get_option("wpt_" . $option_name, $default);
	}

	public function site_info()
	{
		$post_type = ucfirst(get_post_type());
		$site_info_header = $this->get_option("custom_header", "Site Details");
		$header_size = $this->get_option("header_size", "h4");
		$header_tag_open = "<$header_size>";
		$header_tag_close = "</$header_size>";

?>
		<div id="site-info">
			<?php echo $header_tag_open; ?><?php _e($site_info_header, "wp-test-plugin"); ?><?php echo $header_tag_close; ?>
			<div>
				<b><?php _e("Site URL", "wp-test-plugin"); ?>:</b>
				<span><a href="<?php echo get_bloginfo('url'); ?>"><?php echo get_bloginfo('url'); ?></a></span>
			</div>

			<div>
				<b><?php _e("Site Name", "wp-test-plugin"); ?>:</b>
				<span><?php echo get_bloginfo('name'); ?></span>
			</div>

			<div>
				<b><?php _e("Site Admin E-mail", "wp-test-plugin"); ?>:</b>
				<span><a href="mailto:<?php echo get_bloginfo('admin_email'); ?>"><?php echo get_bloginfo('admin_email'); ?></a></span>
			</div>

			<div>
				<b><?php _e(sprintf("Current %s ID", $post_type), "wp-test-plugin"); ?>:</b>
				<span><?php echo get_the_ID(); ?></span>
			</div>

			<div>
				<b><?php _e(sprintf("Current %s Title", $post_type), "wp-test-plugin"); ?>:</b>
				<span><?php echo get_the_title(); ?></span>
			</div>
		</div>
<?php
	}
}
