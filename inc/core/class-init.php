<?php

namespace fse_wpeaxf\Inc\Core;
use fse_wpeaxf as NS;
use fse_wpeaxf\Inc\Admin as Admin;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       http://www.fse-online.co.uk
 * @since      1.0.0
 *
 * @author     FSE Online Ltd
 */
 class Init {

 	/**
 	 * The loader that's responsible for maintaining and registering all hooks that power
 	 * the plugin.
 	 *
 	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
 	 */
 	protected $loader;

 	/**
 	 * The unique identifier of this plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   protected
 	 * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
 	 */
 	protected $plugin_basename;

 	/**
 	 * The current version of the plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   protected
 	 * @var      string    $version    The current version of the plugin.
 	 */
 	protected $version;

 	/**
 	 * The text domain of the plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   protected
 	 * @var      string    $version    The current version of the plugin.
 	 */
 	protected $plugin_text_domain;


 	// define the core functionality of the plugin.
 	public function __construct() {

 		$this->plugin_name = NS\PLUGIN_NAME;
 		$this->version = NS\PLUGIN_VERSION;
 				$this->plugin_basename = NS\PLUGIN_BASENAME;
 				$this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

 		$this->load_dependencies();
 		$this->set_locale();
 		$this->define_admin_hooks();
 	}

 	/**
 	 * Loads the following required dependencies for this plugin.
 	 *
 	 * - Loader - Orchestrates the hooks of the plugin.
 	 * - Internationalization_i18n - Defines internationalization functionality.
 	 * - Admin - Defines all hooks for the admin area.
 	 * - Frontend - Defines all hooks for the public side of the site.
 	 *
 	 * @access    private
 	 */
 	private function load_dependencies() {
 		$this->loader = new Loader();

 	}

 	/**
 	 * Define the locale for this plugin for internationalization.
 	 *
 	 * Uses the Internationalization_i18n class in order to set the domain and to register the hook
 	 * with WordPress.
 	 *
 	 * @access    private
 	 */
 	private function set_locale() {

 		$plugin_i18n = new Internationalization_i18n( $this->plugin_text_domain );

 		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

 	}

 	/**
 	 * Register all of the hooks related to the admin area functionality
 	 * of the plugin.
 	 *
 	 * Callbacks are documented in inc/admin/class-admin.php
 	 *
 	 * @access    private
 	 */
 	private function define_admin_hooks() {

 		$plugin_admin = new Admin\Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

    // Add plugin's cron job
 		$this->loader->add_action( 'fse_wpeaxf_check_daily', $plugin_admin, 'do_cron_job' );

    // Setup plugin action links found on the Plugins page
 		$this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'plugin_action_links' );

 		// Add a top-level admin menu for our plugin
 		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

 		// When a form is submitted to admin-post.php
 		$this->loader->add_action( 'admin_post_fse_wpeaxf_form_response', $plugin_admin, 'the_form_response');

 		// Register admin notices
 		$this->loader->add_action( 'admin_notices', $plugin_admin, 'print_plugin_admin_notices');
 	}

 	/**
 	 * Run the loader to execute all of the hooks with WordPress.
 	 */
 	public function run() {
 		$this->loader->run();
 	}

 	/**
 	 * The name of the plugin used to uniquely identify it within the context of
 	 * WordPress and to define internationalization functionality.
 	 */
 	public function get_plugin_name() {
 		return $this->plugin_name;
 	}

 	/**
 	 * The reference to the class that orchestrates the hooks with the plugin.
 	 *
 	 * @return    Loader    Orchestrates the hooks of the plugin.
 	 */
 	public function get_loader() {
 		return $this->loader;
 	}

 	/**
 	 * Retrieve the version number of the plugin.
 	 *
 	 * @since     1.0.0
 	 * @return    string    The version number of the plugin.
 	 */
 	public function get_version() {
 		return $this->version;
 	}

 	/**
 	 * Retrieve the text domain of the plugin.
 	 *
 	 * @since     1.0.0
 	 * @return    string    The text domain of the plugin.
 	 */
 	public function get_plugin_text_domain() {
 		return $this->plugin_text_domain;
 	}

 }
