<?php

/**
 * @package custom-importer-exporter
 * @version 1.0
 */
/*
	Plugin Name: Custom Importer & Exporter
	Plugin URI:
	Description: Import & Export Term, Posts
	Author: Protech.Inc
	Version: 1.0
	Text Domain: custom-importer-exporter
	Domain Path: /languages/
	Author URI: https://www.pro-tech.co.jp/
	License: GPL2
 */
if (!defined('ABSPATH')) { exit; } // Exit if accessed directly 

require_once dirname(__FILE__) . '/inc/define.php';
require_once dirname(__FILE__) . '/inc/class-custom-importer-exporter.php';

/**
 * Setting of the menu of the management screen
 */
function cie_menuSettings() {
	load_plugin_textdomain(
			'custom-importer-exporter', false, plugin_basename(dirname(__FILE__)) . '/languages');
	new CustomImporterExporter();
}

add_action('admin_menu', 'cie_menuSettings');