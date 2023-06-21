<?php
namespace WmycBogo {

    use Exception;
    use WC_Product;

	class ProductHandler {
		const METAKEY_BOGO_FREE_PRODUCT_ID = 'wmyc_bogo_free_product_id';

		const METAKEY_BOGO_FREE_PRODUCT_FROM_DATE = 'wmyc_bogo_free_product_from_date';

		const METAKEY_BOGO_FREE_PRODUCT_TO_DATE = 'wmyc_bogo_free_product_to_date';

		const OPTION_KEY_BAGO_FREE_PRODUCT_IDS = 'wmyc_bago_free_product_ids';

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
			if ($targetProduct instanceof \WC_Product_Simple) {
				$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_ID, 
					$freeProductInfo->getProductId());
				$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE, 
					$freeProductInfo->getFromDate());
				$targetProduct->update_meta_data(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE, 
					$freeProductInfo->getToDate());
				$targetProduct->save_meta_data();
			}
		}

		/**
		 * @return null|FreeProductInfo 
		 */
		public static function getBogoFreeProduct(\WC_Product $targetProduct) {
			$freeProductInfo = null;
			if ($targetProduct instanceof \WC_Product_Simple) {
				$freeProductId = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_ID, true);
				if (!empty($freeProductId) && is_numeric($freeProductId)) {
					$freeProduct = wc_get_product($freeProductId);
					if ($freeProduct instanceof \WC_Product) {
						$fromDate = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_FROM_DATE);
						$toDate = $targetProduct->get_meta(self::METAKEY_BOGO_FREE_PRODUCT_TO_DATE);
						$freeProductInfo = new FreeProductInfo($freeProduct, 
							$fromDate, 
							$toDate);
					}
				}
			}
			return $freeProductInfo;
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
			$freeProductInfo = self::getBogoFreeProduct($product);

			$data = new \stdClass();
			$data->bogoFreeProductId = null;
			$data->bogoFreeProductFromDate = null;
			$data->bogoFreeProductToDate = null;
			$data->availableProductsForBogo = self::_getActiveSimpleProductsOptions();
			$data->isBagoFreeProduct = self::isBagoFreeProduct($product->get_id());

			if ($freeProductInfo != null) {
				$data->bogoFreeProductId = $freeProductInfo->getProductId();
				$data->bogoFreeProductFromDate = $freeProductInfo->getFromDate();
				$data->bogoFreeProductToDate = $freeProductInfo->getToDate();
			}

			echo ViewEngine::render('wmyc-bogo-admin-simple-products-related.php', 
				$data);
		}

		private static function _getActiveSimpleProductsOptions() {
			$options = array();
			/** @var \WC_Product[] $products */
			$products = wc_get_products(array(
				'type' => array( 'simple' ),
				'status' => 'publish',
				'orderby' => 'name',
				'order' => 'DESC',
				'limit' => -1
			));

			foreach ($products as $p) {
				if ($p->is_visible()) {
					$options[$p->get_id()] = $p->get_name();
				}
			}

			return $options;
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
			if ($bogoFreeProductId > 0)	{
				$bogoFreeProduct = wc_get_product($bogoFreeProductId);
				if ($bogoFreeProduct != null) {
					$bogoFreeProductInfo = new FreeProductInfo($bogoFreeProduct, 
						null, 
						null);

					self::setBogoFreeProduct($product, 
						$bogoFreeProductInfo);
				}
			} else if ($bogoFreeProductId == -1) {
				self::removeBogoFreeProduct($product);
			}
		}

		private static function _getBogoFreeProductIdFromHttpPost() {
			return isset($_POST['wmyc_bogo_free_product'])
				? intval($_POST['wmyc_bogo_free_product'])
				: 0;
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

			add_action('woocommerce_process_product_meta_simple', 
				array(__CLASS__, 'saveAdminProductOptions'), 
				10, 
				1);

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