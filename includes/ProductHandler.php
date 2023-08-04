<?php
namespace WmycBogo {

    use Exception;
    use stdClass;
    use WC_Product;
    use WP_Post;

	class ProductHandler {
		const ID_NO_ASSIGNED_BOGO = -1;

		const ID_USE_PARENT_BOGO_SETTINGS = -2;

		const METAKEY_BOGO_FREE_PRODUCT_ID = 'wmyc_bogo_free_product_id';

		const METAKEY_BOGO_FREE_PRODUCT_FROM_DATE = 'wmyc_bogo_free_product_from_date';

		const METAKEY_BOGO_FREE_PRODUCT_TO_DATE = 'wmyc_bogo_free_product_to_date';

		const OPTION_KEY_BAGO_FREE_PRODUCT_IDS = 'wmyc_bago_free_product_ids';

		private static $_cachedEligibleBogoProductsOptions = null;

		public static function getBagoFreeProductIds() {
			return get_option(self::OPTION_KEY_BAGO_FREE_PRODUCT_IDS, array());
		}

		/**
		 * @return WC_Product[]
		 */
		public static function getBagoFreeProducts() {
			$products = array();
			$productIds = self::getBagoFreeProductIds();

			foreach ($productIds as $productId) {
				$product = wc_get_product($productId);
				if ($product != null) {
					$products[] = $product;
				}
			}

			return $products;
		}

		public static function addBagoFreeProductId($productId) {
			if (!empty($productId)) {
				$bagoFreeProductIds = self::getBagoFreeProductIds();
				if (!in_array($productId, $bagoFreeProductIds)) {
					$bagoFreeProductIds[] = $productId;
					self::_setBagoFreeProductIds($bagoFreeProductIds);
				}				
			}
		}

		private static function _setBagoFreeProductIds(array $productIds) {
			update_option(self::OPTION_KEY_BAGO_FREE_PRODUCT_IDS, $productIds);
		}

		public static function removeBagoFreeProductId($productId) {
			if (!empty($productId)) {
				$bagoFreeProductIds = self::getBagoFreeProductIds();
				$indexOfRemoveProductId = array_search($productId, $bagoFreeProductIds);
				if ($indexOfRemoveProductId !== false) {
					unset($bagoFreeProductIds[$indexOfRemoveProductId]);
					$bagoFreeProductIds = array_values($bagoFreeProductIds);
					self::_setBagoFreeProductIds($bagoFreeProductIds);
				}
			}
		}

		public static function isBagoFreeProduct($productId) {
			if (!empty($productId)) {
				$bagoFreeProductIds = self::getBagoFreeProductIds();
				return in_array($productId, $bagoFreeProductIds);
			} else {
				return false;
			}
		}

		public static function setBogoFreeProduct(\WC_Product $targetProduct, FreeProductInfo $freeProductInfo) {
			$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_ID, 
				$freeProductInfo->getProductId());
			$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE, 
				$freeProductInfo->getFromDate());
			$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE, 
				$freeProductInfo->getToDate());
			$targetProduct->save_meta_data();
		}

		/**
		 * @return null|FreeProductInfo 
		 */
		public static function getBogoFreeProduct(\WC_Product $targetProduct, \WC_Product|null $targetVariation = null) {
			$freeProductInfo = null;
			$freeProductId = self::_determineFreeProductId($targetProduct, $targetVariation);

			if ($freeProductId > 0) {
				$freeProduct = wc_get_product($freeProductId);
				if ($freeProduct instanceof \WC_Product) {
					$fromDate = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE);
					$toDate = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE);
					$freeProductInfo = new FreeProductInfo($freeProduct, 
						$fromDate, 
						$toDate);
				}
			}

			return $freeProductInfo;
		}

		private static function _determineFreeProductId(\WC_Product $targetProduct, \WC_Product|null $targetVariation) {
			$freeProductId = 0;
			
			if ($targetVariation != null) {
				$freeProductId = $targetVariation->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_ID, true);
				if (empty($freeProductId) || !is_numeric($freeProductId)) {
					$freeProductId = 0;
				}
			}

			if ($freeProductId == 0) {
				$freeProductId = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_ID, true);
				if (empty($freeProductId) || !is_numeric($freeProductId)) {
					$freeProductId = 0;
				}
			}

			return $freeProductId;
		}

		public static function removeBogoFreeProduct(\WC_Product $targetProduct) {
			$targetProduct->delete_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_ID);
			$targetProduct->delete_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE);
			$targetProduct->delete_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE);
			$targetProduct->save_meta_data();
		}

		public static function getCurrentProduct() {
			return isset($GLOBALS['post']) 
				? wc_get_product($GLOBALS['post']) 
				: null;
		}

		public static function renderAdminProductRelatedProductsOptions() {
			$product = self::getCurrentProduct();
			$productId = $product->get_id();

			$freeProductInfo = self::getBogoFreeProduct($product);

			$data = new \stdClass();
			$data->bogoFreeProductId = null;
			$data->bogoFreeProductFromDate = null;
			$data->bogoFreeProductToDate = null;
			$data->availableProductsForBogo = self::_getEligibleBogoProductsOptions($productId);
			$data->isBagoFreeProduct = self::isBagoFreeProduct($productId);

			if ($freeProductInfo != null) {
				$data->bogoFreeProductId = $freeProductInfo
					->getProductId();
				$data->bogoFreeProductFromDate = $freeProductInfo
					->getFromDate();
				$data->bogoFreeProductToDate = $freeProductInfo
					->getToDate();
			}

			echo ViewEngine::render('wmyc-bogo-admin-base-product-related.php', 
				$data);
		}

		public static function renderAdminVariableProductRelatedProductsOptions($loop, array $variationDdata, \WP_Post $variation) {
			$data = new \stdClass();
			$productId = $variation->ID;
			$variationProduct = wc_get_product($productId);
			$parentProduct = wc_get_product($variationProduct->get_parent_id());

			$freeProductInfo = self::getBogoFreeProduct($parentProduct, $variationProduct);

			$data = new \stdClass();
			$data->variationId = $productId;
			$data->bogoFreeProductId = null;
			$data->bogoFreeProductFromDate = null;
			$data->bogoFreeProductToDate = null;
			$data->isBagoFreeProduct = self::isBagoFreeProduct($productId);
			$data->availableProductsForBogo = self::_getEligibleBogoProductsOptions($parentProduct->get_id());

			if ($freeProductInfo != null) {
				$data->bogoFreeProductId = $freeProductInfo->getProductId();
				$data->bogoFreeProductFromDate = $freeProductInfo->getFromDate();
				$data->bogoFreeProductToDate = $freeProductInfo->getToDate();
			} else {
				$data->bogoFreeProductId = (string)self::ID_USE_PARENT_BOGO_SETTINGS;
			}

			echo ViewEngine::render('wmyc-bogo-admin-variation-product-related.php', 
				$data);
		}

		private static function _getEligibleBogoProductsOptions($excludeProductId) {
			$allOptions = self::_getAllEligibleBogoProductsOptions();

			if (!empty($excludeProductId)) {
				$options = array();
				$testExcludeProductId = (string)$excludeProductId;

				foreach ($allOptions as $key => $value) {
					$exclude = ($testExcludeProductId === $key) 
						|| (stripos($key, $testExcludeProductId . '_') === 0);

					if (!$exclude) {
						$options[$key] = $value;
					}
				}
			} else {
				$options = $allOptions;
			}
			
			return $options;
		}

		private static function _getAllEligibleBogoProductsOptions() {
			if (self::$_cachedEligibleBogoProductsOptions === null) {
				$options = array();
				/** @var \WC_Product[] $products */
				$products = wc_get_products(array(
					'type' => array( 'simple', 'external', 'variable' ),
					'status' => 'publish',
					'orderby' => 'name',
					'order' => 'DESC',
					'limit' => -1
				));
	
				foreach ($products as $p) {
					if ($p->is_visible()) {
						$productId = $p->get_id();
						if ($p->get_type() != 'variable') {
							$options[(string)$productId] = $p->get_name();
						} else {
							/** @var \WC_Product[] $variations */
							$variations = self::_getProductVariationsForSelection($productId);
							if (!empty($variations)) {
								foreach ($variations as $v) {
									if ($v->is_visible()) {
										$finalProductId = $productId . '_' . $v->get_id();
										$finalProductName = $v->get_name();
										$options[$finalProductId] = $finalProductName;
									}
								}
							}
						}
					}
				}

				self::$_cachedEligibleBogoProductsOptions = $options;
			}
			
			$options = self::$_cachedEligibleBogoProductsOptions;
			return $options;
		}

		/**
		 * @return \WC_Product[]
		 */
		private static function _getProductVariationsForSelection($productId) {
			return wc_get_products(
				array(
					'status' => array( 'publish' ),
					'type' => 'variation',
					'parent' => $productId,
					'limit'  => -1,
					'orderby' => array(
						'menu_order' => 'ASC',
						'ID'         => 'DESC',
					),
					'return'  => 'objects'
				)
			);
		}

		public static function saveAdminProductOptions($postId) {
			$product = wc_get_product($postId);
			if ($product != null) {
				self::_processBogoFreeProductInformation($product);
				self::_processBagoFreeProductInformation($product);
			}
		}

		private static function _processBogoFreeProductInformation(WC_Product $product) {
			$bogoFreeProductId = self::_getBogoFreeProductIdFromHttpPost();
			self::_saveBogoFreeProductId($product, 
				$bogoFreeProductId);
		}

		private static function _saveBogoFreeProductId(WC_Product $product, array $bogoFreeProductId) {
			if ($bogoFreeProductId['product_id'] > 0)	{
				$bogoFreeProduct = wc_get_product($bogoFreeProductId['product_id']);
				if ($bogoFreeProduct != null) {
					$bogoFreeProductInfo = new FreeProductInfo($bogoFreeProduct, 
						null, 
						null);

					self::setBogoFreeProduct($product, 
						$bogoFreeProductInfo);
				}
			} else if ($bogoFreeProductId['product_id'] == self::ID_NO_ASSIGNED_BOGO) {
				self::removeBogoFreeProduct($product);
			}
		}

		private static function _getBogoFreeProductIdFromHttpPost() {
			$bogoFreeProductInfo = isset($_POST['wmyc_bogo_free_product'])
				? sanitize_text_field($_POST['wmyc_bogo_free_product'])
				: null;

			return self::_parseStringBogoFreeProductInfo($bogoFreeProductInfo);
		}

		private static function _parseStringBogoFreeProductInfo($bogoFreeProductInfo) {
			if (stripos($bogoFreeProductInfo, '_') !== false) {
				$bogoFreeProductInfoParts = explode('_', $bogoFreeProductInfo, 2);
				$bogoFreeProductIds = array(
					'product_id' => intval($bogoFreeProductInfoParts[0]),
					'variation_id' => intval($bogoFreeProductInfoParts[1])
				);
			} else {
				$bogoFreeProductIds = array(
					'product_id' => intval($bogoFreeProductInfo),
					'variation_id' => 0
				);
			}

			return $bogoFreeProductIds;
		}

		private static function _processBagoFreeProductInformation(WC_Product $product){
			if (self::_getBagoFreeProductEnableFromHttpPost()) {
				self::addBagoFreeProductId($product->get_id());
			} else {
				self::removeBagoFreeProductId($product->get_id());
			}
		}

		private static function _getBagoFreeProductEnableFromHttpPost() {
			return isset($_POST['wmyc_bago_free_product_enable']) 
				&& $_POST['wmyc_bago_free_product_enable'] === 'yes';
		}

		public static function saveAdminProductVariationOptions($variationId, $index) {
			$variation = wc_get_product_object('variation', $variationId);
			if ($variation != null) {
				self::_processBogoFreeProductInformationForVariation($variation);
				self::_processBagoFreeProductInformationForVariation($variation);
			}
		}

		private static function _processBogoFreeProductInformationForVariation(\WC_Product_Variation $variation) {
			$bogoFreeProductId = self::_getBogoFreeProductIdFromHttpPostForVariation($variation->get_id());
			self::_saveBogoFreeProductId($variation, 
				$bogoFreeProductId);
		}

		private static function _getBogoFreeProductIdFromHttpPostForVariation($variationId) {
			$bogoFreeProductInfo = isset($_POST['wmyc_bogo_free_product_variation']) 
					&& isset($_POST['wmyc_bogo_free_product_variation'][$variationId])
				? sanitize_text_field($_POST['wmyc_bogo_free_product_variation'][$variationId])
				: null;

			return self::_parseStringBogoFreeProductInfo($bogoFreeProductInfo);
		}

		private static function _processBagoFreeProductInformationForVariation(\WC_Product_Variation $variation) {	
			if (self::_getBagoFreeProductEnableFromHttpPostForVariation($variation->get_id())) {
				self::addBagoFreeProductId($variation->get_id());
			} else {
				self::removeBagoFreeProductId($variation->get_id());
			}
		}

		private static function _getBagoFreeProductEnableFromHttpPostForVariation($variationId) {
			return isset($_POST['wmyc_bago_free_product_variation_enable']) 
				&& isset($_POST['wmyc_bago_free_product_variation_enable'][$variationId])
				&& $_POST['wmyc_bago_free_product_variation_enable'][$variationId] === 'yes';
		}

		public static function addBogoFreeProductPromoBannerIfNeeded($shortDescription) {
			$newShortDescription = $shortDescription;
			
			if (is_product()) {
				$product = self::getCurrentProduct();
				if (self::_shouldAddBogoFreeProductPromoBannerIfNeeded($product)) {
					$bogoFreeProduct = self::getBogoFreeProduct($product);
					if ($bogoFreeProduct != null && $bogoFreeProduct->canBeOrdered()) {
						$newShortDescription .= self::_getBogoFreeProductPromoMessageHtml($bogoFreeProduct->getProduct());
					}
				}
			}

			return $newShortDescription;
		}

		private static function _shouldAddBogoFreeProductPromoBannerIfNeeded($product) {
			return $product instanceof WC_Product;
		}

		private static function _getBogoFreeProductPromoMessageHtml(WC_Product $product) {
			$data = new \stdClass();
			$data->useDefaultStyling = self::_useDefaultPromoFreeProductMessageStyling($product);
			$data->message = self::_getBogoFreeProductMessageText($product);
			
			return ViewEngine::render('wmyc-bogo-free-product-promo-message.php', 
				$data);
		}

		private static function _useDefaultPromoFreeProductMessageStyling(\WC_Product $product) {
			return apply_filters('wmyc_bogo_default_bogo_free_product_message_styling', 
				true, 
				$product);
		}

		private static function _getBogoFreeProductMessageText(WC_Product $product) {
			$messageFormat = Settings::getBogoFreeProductMessageFormat();
			$bogoFreeProductDescription = sprintf('<a href="%s" target="_blank" class="wmycaf-promo-bogo-free-product-link">%s</a>', 
				$product->get_permalink(), 
				$product->get_name());

			return sprintf($messageFormat, 
				$bogoFreeProductDescription);
		}

		public static function addBagoFreeProductPromoBannerIfNeeded($shortDescription) {
			$newShortDescription = $shortDescription;

			if (is_product()) {
				$product = self::getCurrentProduct();
				if (self::_shouldAddBagoFreeProductPromoBannerIfNeeded($product)) {
					$newShortDescription .= self::_getBagoFreeProductPromoMessageHtml($product);
				}
			}

			return $newShortDescription;
		}

		private static function _shouldAddBagoFreeProductPromoBannerIfNeeded($product) {
			return $product instanceof WC_Product 
				&& self::isBagoFreeProduct($product->get_id()) 
				&& $product->is_in_stock();
		}

		private static function _getBagoFreeProductPromoMessageHtml(WC_Product $product) {
			$data = new \stdClass();
			$data->useDefaultStyling = self::_useDefaultPromoFreeProductMessageStyling($product);
			$data->message = self::_getBagoFreeProductMessageText($product);
			
			return ViewEngine::render('wmyc-bogo-free-product-promo-message.php', 
				$data);
		}

		private static function _getBagoFreeProductMessageText(WC_Product $product) {		
			return Settings::getBagoFreeProductMessageText();
		}

		public static function removeAllMetadata() {
			delete_option(self::OPTION_KEY_BAGO_FREE_PRODUCT_IDS);
			delete_post_meta_by_key(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE);
			delete_post_meta_by_key(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE);
			delete_post_meta_by_key(self::METAKEY_BOGO_FREE_PRODUCT_ID);
		}

		public static function init() {
			add_action('woocommerce_product_options_related',
				array(__CLASS__, 'renderAdminProductRelatedProductsOptions'),
				10);

			add_action('woocommerce_product_after_variable_attributes',
				 array(__CLASS__, 'renderAdminVariableProductRelatedProductsOptions'),
				  10, 
				  3);

			add_action('woocommerce_process_product_meta_simple', 
				array(__CLASS__, 'saveAdminProductOptions'), 
				10, 
				1);

			add_action('woocommerce_process_product_meta_external', 
				array(__CLASS__, 'saveAdminProductOptions'), 
				10, 
				1);

			add_action('woocommerce_process_product_meta_variable', 
				array(__CLASS__, 'saveAdminProductOptions'), 
				10, 
				1);

			add_action('woocommerce_save_product_variation', 
				array(__CLASS__, 'saveAdminProductVariationOptions'), 
				10, 
				2);

			add_filter('woocommerce_short_description', 
				array(__CLASS__, 'addBogoFreeProductPromoBannerIfNeeded'),
				10,
				1);

			add_filter('woocommerce_short_description', 
				array(__CLASS__, 'addBagoFreeProductPromoBannerIfNeeded'), 
				11, 
				1);
		}
	}
}