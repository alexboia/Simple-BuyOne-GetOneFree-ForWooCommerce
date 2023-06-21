<?php
namespace WmycBogo {
	class Settings {
		const SETTINGS_SECTION_ID = 'wmyc-bogo';

		const OPT_KEY_PRODUCT_PROMO_BOGO_FREE_PRODUCT_MESSAGE = 'wmyc_bogo_product_promo_free_product_message';

		const OPT_KEY_PRODUCT_PROMO_BAGO_FREE_PRODUCT_MESSAGE = 'wmyc_bago_product_promo_free_product_message';

		public static function getWooCommerceSettingsTab(array $sections) {
			$sections[self::SETTINGS_SECTION_ID] = __('Buy one get one free', 'wmyc-bogo');
			return $sections;
		}

		public static function getBogoFreeProductMessageFormat() {
			$message = get_option(self::OPT_KEY_PRODUCT_PROMO_BOGO_FREE_PRODUCT_MESSAGE);
			if (!$message) { 
				//Fallback to a default message
				$message = self::_getDefaultBogoFreeProductMessageFormat();
			}
			return $message;
		}

		private static function _getDefaultBogoFreeProductMessageFormat() {
			return __('Buy this and you get %s for free!', 'wmyc-bogo');
		}

		public static function getBagoFreeProductMessageText() {
			$message = get_option(self::OPT_KEY_PRODUCT_PROMO_BAGO_FREE_PRODUCT_MESSAGE);
			if (!$message) {
				$message = self::_getDefaultBagoFreeProductMessageText();
			}
			return $message;
		}

		private static function _getDefaultBagoFreeProductMessageText() {
			return __('This product is free for any order you place!', 'wmyc-bogo');
		}

		public static function getWooCommerceSettingsFields($settings, $currentSection) {
			if ($currentSection == self::SETTINGS_SECTION_ID) {
				$ourSettings = array();
		
				$ourSettings[] = array(
					'name' => __('Buy one get one free', 'wmyc-bogo'),
					'type' => 'title',
					'desc' => __('The following options are used to configure MyClar - Buy one get one free module', 'wmyc-bogo')
				);

				$ourSettings[] = array(
					'name'=> __('Buy-one-get-one-free product promo message banner format', 'wmyc-bogo'),
					'desc_tip' => __('This message will be displayed if a product is assigned a free product. Use %s to specify where the free product name and link will be inserted.', 'wmyc-bogo'),
					'id' => self::OPT_KEY_PRODUCT_PROMO_BOGO_FREE_PRODUCT_MESSAGE,
					'type' => 'text',
					'css' => 'min-width:300px;',
					'value' => self::getBogoFreeProductMessageFormat()
				);

				$ourSettings[] = array(
					'name'=> __('Buy-any-get-one-free product promo message banner format', 'wmyc-bogo'),
					'desc_tip' => __('This message will be displayed whenever a product is granted for free on any order.', 'wmyc-bogo'),
					'id' => self::OPT_KEY_PRODUCT_PROMO_BAGO_FREE_PRODUCT_MESSAGE,
					'type' => 'text',
					'css' => 'min-width:300px;',
					'value' => self::getBagoFreeProductMessageText()
				);

				$ourSettings[] = array(
					'type' => 'sectionend', 
					'id' => self::SETTINGS_SECTION_ID
				);

				return $ourSettings;
			}

			return $settings;
		}

		public static function init() {
			add_filter('woocommerce_get_sections_products', 
				array(__CLASS__, 'getWooCommerceSettingsTab'), 
				10,
				1
			);

			add_filter('woocommerce_get_settings_products', 
				array(__CLASS__, 'getWooCommerceSettingsFields'),
				10, 
				2
			);
		}

		public static function removeAll() {
			delete_option(self::OPT_KEY_PRODUCT_PROMO_BOGO_FREE_PRODUCT_MESSAGE);
			delete_option(self::OPT_KEY_PRODUCT_PROMO_BAGO_FREE_PRODUCT_MESSAGE);
		}
	}
}