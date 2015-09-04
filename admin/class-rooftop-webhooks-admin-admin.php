<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Webhooks_Admin
 * @subpackage Rooftop_Webhooks_Admin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Webhooks_Admin
 * @subpackage Rooftop_Webhooks_Admin/admin
 * @author     Error <info@errorstudio.co.uk>
 */

class Rooftop_Webhooks_Admin_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $this->redis = new Redisent('localhost');
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Webhooks_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Webhooks_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-webhooks-admin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Webhooks_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Webhooks_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-webhooks-admin-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function webhook_menu_links() {
        $rooftop_api_menu_slug = "rooftop-api-authentication-overview";
        add_submenu_page($rooftop_api_menu_slug, "Webhooks", "Webhooks", "manage_options", $this->plugin_name."-overview", function() {
            $method = $_SERVER['REQUEST_METHOD'];


            switch($method) {
                case 'GET':
                    if(!array_key_exists('id', $_GET)){
                        $this->webhooks_admin_form();
                    }else {
                        $this->webhooks_view_form();
                    }
                    break;
                case 'POST':
                    $this->webhook_create();
                    break;
                case 'PUT':
                    $this->webhooks_update();
                    break;
                case 'DELETE':
                    $this->webhook_delete();
                    break;
            }
        });
    }

    public function webhooks_admin_form() {
        $webhok_endpoints = $this->get_api_endpoints();

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-form.php';
    }
    public function webhooks_view_form() {
        $endpoint = array('id' => 1, 'webhook_url' => "http://woedowe.ngrok.com:1234", 'webhook_mode' => 'test');
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-view-form.php';
    }
    private function webhook_create(){
    }
    private function webhooks_update(){
    }
    private function webhook_delete(){
    }

    private function get_api_endpoints() {
        $endpoints = array();
        $endpoints[] = array('id' => 1,'webhook_url' => "http://woedowe.ngrok.com:1234", 'webhook_mode' => 'test');
        $endpoints[] = array('id' => 2,'webhook_url' => "http://username:password@woedowe.ngrok.com", 'webhook_mode' => 'live');

        return $endpoints;
    }
    private function set_api_endpoints($endpoints) {
        return true;
    }
}
