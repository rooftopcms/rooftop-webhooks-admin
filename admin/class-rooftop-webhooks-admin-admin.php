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

if(!class_exists('Redisent')){
    require_once VENDOR_PATH . 'chrisboulton/php-resque/lib/Redisent/Redisent.php';
}

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

    private $redis_key;

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

        $this->redis_key = 'site_id:'.get_current_blog_id().':webhooks';
        $this->redis = new Predis\Client();
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
            $method = ($_SERVER['REQUEST_METHOD'] && $_POST && array_key_exists('id', $_POST)) ? "PATCH" : $_SERVER['REQUEST_METHOD'];

            switch($method) {
                case 'GET':
                    if(!array_key_exists('id', $_GET) && !array_key_exists('new', $_GET)){
                        $this->webhooks_admin_index();
                    }elseif(array_key_exists('new', $_GET)){
                        $this->webhooks_admin_form();
                    }elseif(array_key_exists('id', $_GET)) {
                        $this->webhooks_view_form();
                    }
                    break;
                case 'POST':
                    $this->webhook_create();
                    break;
                case 'PATCH':
                    $this->webhooks_update();
                    break;
                case 'DELETE':
                    $this->webhook_delete();
                    break;
            }
        });
    }

    public function webhooks_admin_index() {
        $webhook_endpoints = $this->get_api_endpoints();

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-index.php';
    }

    public function webhooks_admin_form() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-new.php';
    }

    public function webhooks_view_form() {
        $endpoint = $this->get_api_endpoint_with_id($_GET['id']);

        if($endpoint){
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-show.php';
        }else {
            new WP_Error(404, "Endpoint not found");
            return;
        }
    }

    private function webhook_create(){
        $endpoint = (object)array('url' => $_POST['url'], 'environment' => $_POST['environment']);
        if($this->validate($endpoint)) {
            $endpoint->id = $this->redis->incr($this->redis_key.':id');

            $all_endpoints = $this->get_api_endpoints();
            $all_endpoints[] = $endpoint;
            $this->set_api_endpoints($all_endpoints);

            exit("New endpoint added");
        }else {
            echo "New endpoint not valid";
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-view-form.php';
        }
    }

    private function webhooks_update(){
        $all_endpoints = $this->get_api_endpoints();
        $endpoint = $this->get_api_endpoint_with_id($_POST['id']);

        if($endpoint) {
            $index = array_search($endpoint, $all_endpoints);
            $endpoint->url = $_POST['url'];
            $endpoint->environment = $_POST['environment'];

            if($this->validate($endpoint)){
                $all_endpoints[$index] = $endpoint;
                $this->set_api_endpoints($all_endpoints);

                echo "Webhook updated";
                $this->webhooks_admin_index();
            }else {
                return new WP_Error(500, "Could not validate webhook");
                exit;
            }
        }
    }

    private function webhook_delete() {
        $all_endpoints = $this->get_api_endpoints();
        $endpoint = $this->get_api_endpoint_with_id($_POST['id']);

        if($endpoint) {
            $index = array_search($endpoint, $all_endpoints);
            unset($all_endpoints[$index]);
            $this->set_api_endpoints($all_endpoints);
        }
    }

    private function get_api_endpoint_with_id($id) {
        $all_endpoints = $this->get_api_endpoints();
        $endpoints = array_filter($all_endpoints, function($endpoint) use($id){
            return $endpoint->id == $id;
        });

        if(count($endpoints)==1){
            return array_values($endpoints)[0];
        }else {
            return false;
        }
    }

    private function validate($endpoint) {
        // fixme: validate the environment, url presence and that the url doesnt resolve to a local address
        $results = array();
        $results[] = strlen($endpoint->environment)>0;
        $results[] = strlen($endpoint->url)>0;

        if(count(array_unique($results))==1 && $results[0]==true){
            return true;
        }else {
            return false;
        }
    }

    private function set_api_endpoints($endpoints) {
        $this->redis->set($this->redis_key, json_encode($endpoints));
    }

    private function get_api_endpoints() {
        $endpoints = json_decode($this->redis->get($this->redis_key));
        if(!is_array($endpoints)) {
            return array();
        }

        return $endpoints;
    }
}
