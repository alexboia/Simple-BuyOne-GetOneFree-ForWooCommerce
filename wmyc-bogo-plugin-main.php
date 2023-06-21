<?php
/**
 * Plugin Name: Buy one get one free
 * Author: MyClar Software Solutions SRL
 * Author URI: http://myclar.ro/
 * Version: 1.0.1
 * Description: Simple buy one get one free plugin.
 * Plugin URI: 
 * Text Domain: wmyc-bogo
 * WC requires at least: 5.0.0
 * WC tested up to: 6.3.1
 */

//Check that we're not being directly called
defined('ABSPATH') or die;

require_once __DIR__ . '/wmyc-bogo-plugin-header.php';
require_once __DIR__ . '/wmyc-bogo-plugin-functions.php';

wmyc_bogo_run();