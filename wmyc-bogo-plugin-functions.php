<?php
defined('WMYC_BOGO_LOADED') or die;

function wmyc_bogo_write_log ($message)  {
	if (is_array($message) || is_object($message)) {
		ob_start();
		var_dump($message);
		$message = ob_get_clean();
		error_log($message);
	} else {
		error_log($message);
	}
}

function wmyc_bogo_run() {
	require_once __DIR__ . '/includes/ViewEngine.php';
	require_once __DIR__ . '/includes/Settings.php';
	require_once __DIR__ . '/includes/Assets.php';
	require_once __DIR__ . '/includes/FreeProductInfo.php';
	require_once __DIR__ . '/includes/ProductHandler.php';
	require_once __DIR__ . '/includes/CartHandler.php';
	require_once __DIR__ . '/includes/Installer.php';

	\WmycBogo\Assets::init();
	\WmycBogo\Settings::init();
	\WmycBogo\ProductHandler::init();
	\WmycBogo\CartHandler::init();
	\WmycBogo\Installer::init();
}