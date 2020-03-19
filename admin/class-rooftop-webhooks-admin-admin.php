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

    /*******
     * Add the Webhooks admin interface
     *******/
    public function webhook_menu_links() {
        $rooftop_webhook_menu_slug = "rooftop-overview";
        add_menu_page("Webhooks", "Webhooks", "manage_options", $this->plugin_name."-overview", function() {
            if($_POST && array_key_exists('method', $_POST)) {
                $method = strtoupper($_POST['method']);
            }elseif($_POST && array_key_exists('id', $_POST)) {
                $method = 'PATCH';
            }else {
                $method = $_SERVER['REQUEST_METHOD'];
            }

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
                    $id = $_POST['id'];
                    $this->webhook_delete($id);
                    break;
            }
        });
    }

    /**
     * list all webhooks
     */
    private function webhooks_admin_index() {
        $webhook_endpoints = $this->get_webhook_endpoints();

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-index.php';
    }

    /**
     * render the form
     */
    private function webhooks_admin_form() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-new.php';
    }

    /**
     * render the edit/delete form
     *
     */
    private function webhooks_view_form() {
        $endpoint = $this->get_webhook_endpoint_with_id($_GET['id']);

        if($endpoint){
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-show.php';
        }else {
            new WP_Error(404, "Endpoint not found");
            return;
        }
    }

    /**
     * Create a new webhook endpoint
     */
    private function webhook_create() {
        $endpoint = (object)array('url' => $_POST['url']);
        $errors = null;

        if($this->validate($endpoint, $errors)) {
            $endpoint->id = uniqid();

            $all_endpoints = $this->get_webhook_endpoints();
            $all_endpoints[] = $endpoint;
            $this->set_webhook_endpoints($all_endpoints);

            $this->renderMessage("Webhook created", "success");
            $this->webhooks_admin_index();
        }else {
            echo "<div class='wrap'>New endpoint not valid</div>";
            require_once plugin_dir_path( __FILE__ ) . 'partials/render-errors.php';
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-new.php';
            return new WP_Error(500, "Could not validate webhook");
            exit;
        }
    }

    /**
     * Update a webhook in the existing collection
     */
    private function webhooks_update() {
        $all_endpoints = $this->get_webhook_endpoints();
        $endpoint = $this->get_webhook_endpoint_with_id($_POST['id']);
        if($endpoint) {
            $index = array_search($endpoint, $all_endpoints);
            $endpoint->url = $_POST['url'];

            $errors = [];
            if($this->validate($endpoint, 'update', $errors)){
                $all_endpoints[$index] = $endpoint;
                $this->set_webhook_endpoints($all_endpoints);

                $this->renderMessage("Webhook updated", "success");
                $this->webhooks_admin_index();
            }else {
                echo "<div class='wrap'>Endpoint not saved</div>";
                require_once plugin_dir_path( __FILE__ ) . 'partials/render-errors.php';
                require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-webhooks-admin-show.php';
                return new WP_Error(500, "Could not validate webhook");
                exit;
            }
        }
    }
    
    /**
     * @param $id
     *
     * Remove a webhook if we find its ID in the stored webhooks
     *
     */
    private function webhook_delete($id) {
        $all_endpoints = $this->get_webhook_endpoints();
        $endpoint = $this->get_webhook_endpoint_with_id($id);
        
        if($endpoint) {
            $index = array_search($endpoint, $all_endpoints);
            unset($all_endpoints[$index]);
            $this->set_webhook_endpoints(array_values($all_endpoints));
            
            $this->renderMessage("Webhook deleted", "deleted");
            $this->webhooks_admin_index();
        }
    }

    private function renderMessage($message, $messageType) {
        echo "<div id='message' class='${messageType} notice is-dismissible'>";
        echo "    <p><strong>${message}</strong></p>";
        echo "<button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button></div>";
    }
    
    /**
     * @param $id
     * @return bool
     *
     * find a webhook by its ID
     *
     */
    private function get_webhook_endpoint_with_id($id) {
        $all_endpoints = $this->get_webhook_endpoints();
        $endpoints = array_filter($all_endpoints, function($endpoint) use($id) {
            return $endpoint->id == $id;
        });

        if(count($endpoints)==1) {
            return array_values($endpoints)[0];
        }else {
            return false;
        }
    }

    /**
     * @param $endpoint
     * @param null $op
     * @param null $errors
     * @return bool
     *
     *
     * validate the webhook can be saved. mutate &$errors with any validation errors and return a bool
     */
    private function validate($endpoint, $op = null, &$errors = null) {
        $validation_results = array('url' => []);
        $endpoints = $this->get_webhook_endpoints();

        if(!strlen($endpoint->url)>0){
            $validation_results['url'][] = "No URL given";
        }

        $url = parse_url($endpoint->url);
        if(gethostbyname($url['host']) == "127.0.0.1"){
            $validation_results['url'][] = "URL Resolves to localhost";
        }

        $urls = array_map(function($e){
            return $e->url;
        }, $endpoints);

        if(in_array($endpoint->url, $urls) && $op != 'update'){
            $validation_results['url'][] = "You've already added this endpoint";
        }

        $validation_errors = array_filter(array_values($validation_results));
        if(count($validation_errors)){
            $errors = array_filter($validation_results);
            return false;
        }

        return true;
    }

    /**
     * @param $endpoints
     *
     * saves a JSON encoded array of endpoints
     */
    private function set_webhook_endpoints($endpoints) {
        update_option( 'webhook_endpoints', $endpoints );
    }

    /**
     * @return array|mixed|object
     *
     * Fetch the webhook endpoints from the blog options and return as an array of objects
     */
    private function get_webhook_endpoints() {
        $endpoints = get_option( 'webhook_endpoints', array() );
        $endpoints = array_map( function( $i ) {
            return (object)$i;
        }, $endpoints );

        if(!is_array($endpoints)) {
            return array();
        }

        return $endpoints;
    }

    public function post_webhook( $post_id ) {
        $post = get_post($post_id);

        $request_body = array(
            'id' => $post_id,
            'type' => $post->post_type
        );

        $request_body = apply_filters( 'prepare_'.$post->post_status.'_webhook_payload', $request_body );

        $this->queueJob( $request_body );
    }

    public function menu_webhook( $menu_id ) {
        $request_body = array(
            'id' => $menu_id,
            'type' => 'menu'
        );

        $request_body = apply_filters( 'prepare_term_webhook_payload', $request_body );

        $this->queueJob( $request_body );
    }

    public function term_webhook( $term_id ) {
        $request_body = array(
            'id' => $term_id,
            'type' => 'taxonomy'
        );

        $request_body = apply_filters( 'prepare_term_webhook_payload', $request_body );

        $this->queueJob( $request_body );
    }

    // Todo: pass this onto a queue service. 
    private function queueJob( $payload ) {
        $all_endpoints = $this->get_webhook_endpoints();
        
        foreach( $all_endpoints as $endpoint ) {
            try {
                $ch = curl_init( $endpoint->url );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            }catch (Exception $e) {
            }
        }
    }
}
