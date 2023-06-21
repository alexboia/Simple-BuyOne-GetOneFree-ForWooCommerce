<?php
namespace WmycBogo {
	class Installer {
		private static function _currentUserCanActivatePlugins() {
			return current_user_can('activate_plugins');
		}

		public static function activate() {
			if (self::_currentUserCanActivatePlugins()) {
				wmyc_bogo_write_log('Plugin activated.');
			} else {
				wmyc_bogo_write_log('Attempted to activate plug-in without appropriate access permissions.');
				wp_die('Attempted to activate plug-in without appropriate access permissions.');
			}
		}

		public static function uninstall() {
			Settings::removeAll();
			ProductHandler::removeAllMetadata();
		}

		public static function init() {
			register_activation_hook(WMYC_BOGO_PLUGIN_MAIN, 
				array(__CLASS__, 'activate')
			);

			register_uninstall_hook(WMYC_BOGO_PLUGIN_MAIN, 
				array(__CLASS__, 'uninstall')
			);
		}
	}
}