<?php
namespace WmycBogo {
	class Assets {
		public static function prepare() {
			self::_loadTextDomain();
		}

		private static function _loadTextDomain() {
			load_plugin_textdomain('wmyc-bogo', false, 
				plugin_basename(WMYC_BOGO_PLUGIN_LANG)
			);
		}

		public static function init() {
			add_action('init', 
				array(__CLASS__, 'prepare')
			);
		}
	}
}