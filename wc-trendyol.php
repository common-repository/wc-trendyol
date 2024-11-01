<?php

	/**
	 * The plugin bootstrap file
	 *
	 * This file is read by WordPress to generate the plugin information in the plugin
	 * admin area. This file also includes all of the dependencies used by the plugin,
	 * registers the activation and deactivation functions, and defines a function
	 * that starts the plugin.
	 *
	 * @link              https://hayatikodla.net/
	 * @since             1.0.0
	 * @package           Wc_Trendyol
	 *
	 * @wordpress-plugin
	 * Plugin Name:       Trendyol Woocommerce (Ücretsiz)
	 * Plugin URI:        https://https://hayatikodla.net
	 * Description:       Woocoommerce için trendyol eklentisi
	 * Version:           3.1.3
	 * Requires at least: 6.5
	 * Requires PHP:      7.4
	 * Requires Plugins:  woocommerce
	 * Author:            Hasan Yüksektepe
	 * Author URI:        https://hayatikodla.net//
	 * License:           GPL-2.0+
	 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
	 * Text Domain:       wc-trendyol
	 * Domain Path:       /languages
	 */

	// If this file is called directly, abort.
	if(!defined('WPINC')){
		die;
	}

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define('WC_TRENDYOL_VERSION', '3.1.3');
	if(!defined('WC_TRENDYOL_DIR_PATH')){
		define('WC_TRENDYOL_DIR_PATH', __DIR__);
	}
	if(!defined('WC_TRENDYOL_DIR_URL')){
		define('WC_TRENDYOL_DIR_URL', plugin_dir_url(__FILE__));
	}
	if(!defined('WC_TRENDYOL_API_URL')){
		define('WC_TRENDYOL_API_URL', 'https://wc_plugin.hayatikodla.com');
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wc-trendyol-activator.php
	 */
	function activate_wc_trendyol(){
		require_once plugin_dir_path(__FILE__).'includes/class-wc-trendyol-activator.php';
		Wc_Trendyol_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wc-trendyol-deactivator.php
	 */
	function deactivate_wc_trendyol(){
		require_once plugin_dir_path(__FILE__).'includes/class-wc-trendyol-deactivator.php';
		Wc_Trendyol_Deactivator::deactivate();
	}

	register_activation_hook(__FILE__, 'activate_wc_trendyol');
	register_deactivation_hook(__FILE__, 'deactivate_wc_trendyol');

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path(__FILE__).'includes/class-wc-trendyol.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_wc_trendyol(){

		$plugin = new Wc_Trendyol();
		$plugin->run();

	}

	add_action('plugins_loaded', function(){

		if(!function_exists('is_plugin_active')){
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$woocommerce_plugin      = 'woocommerce/woocommerce.php';
		$woocommerce_plugin_path = ABSPATH.'wp-content/plugins/'.$woocommerce_plugin;
		if(is_plugin_active($woocommerce_plugin) and file_exists($woocommerce_plugin_path)){
			run_wc_trendyol();
		}
	}, 18);
