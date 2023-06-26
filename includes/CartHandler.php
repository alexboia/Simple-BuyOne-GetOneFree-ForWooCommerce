<?php
namespace WmycBogo {

    use stdClass;
    use WC_Cart;
    use WC_Order;
    use WC_Order_Item_Product;
    use WC_Product;

	class CartHandler {
		public static function onAddBogoFreeProductCartItemData(array $cartItemData, 
				$productId, 
				$variationId, 
				$qty) {
			$newCartItemData = $cartItemData;
			$targetProduct = wc_get_product($productId);
			$targetVariation = $variationId >= 0 
				? wc_get_product($variationId) 
				: null;
			
			if ($targetProduct != null && empty($cartItemData['wmyc_bogo_product_id'])) {
				$freeProductInfo = ProductHandler::getBogoFreeProduct($targetProduct, $targetVariation);

				if ($freeProductInfo != null && $freeProductInfo->canBeOrdered()) {
					$freeProduct = $freeProductInfo->getProduct();
					if ($freeProduct != null) {
						$dataToAdd = self::_determineNewCartItemDataToAddForFreeProduct($freeProduct, $qty);
						$newCartItemData = array_merge($newCartItemData, $dataToAdd);
					}
				}
			}

			return $newCartItemData;
		}

		private static function _determineNewCartItemDataToAddForFreeProduct(\WC_Product $freeProduct, $qty) {
			if ($freeProduct->get_type() == 'variation') {
				$newCartItemData = array(
					'wmyc_bogo_product_id' => $freeProduct->get_parent_id(),
					'wmyc_bogo_variation_id' => $freeProduct->get_id(),
					'wmyc_bogo_product_qty' => $qty
				);
			} else {
				$newCartItemData = array(
					'wmyc_bogo_product_id' => $freeProduct->get_id(),
					'wmyc_bogo_variation_id' => 0,
					'wmyc_bogo_product_qty' => $qty
				);
			}

			return $newCartItemData;
		}

		public static function onProductQuantityUpdateSetBogoFreeProductQuantity($cartItemKey, $quantity, WC_Cart $cart) {
			$cartContents = $cart->get_cart_contents();
			if (!empty($cartContents[$cartItemKey])) {
				$cartItem = $cartContents[$cartItemKey];
				if (self::_hasBogoFreeProductInfo($cartItem)) {
					$cartItem['wmyc_bogo_product_qty'] = $quantity;
					$cartContents[$cartItemKey] = $cartItem;
					$cart->set_cart_contents($cartContents);
				}	
			}
		}

		private static function _hasBogoFreeProductInfo(array $cartItem) {
			return !empty($cartItem['wmyc_bogo_product_id']) 
				&& isset($cartItem['wmyc_bogo_variation_id']) 
				&& !empty($cartItem['wmyc_bogo_product_qty']);
		}

		public static function onCheckoutAddFreeBogoOrderLineItemIfNeeded(WC_Order_Item_Product $item, 
				$cartItemKey, 
				array $cartItem, 
				WC_Order $order) {

			if (self::_hasBogoFreeProductInfo($cartItem)) {
				$bogoLineItem = self::_createOrderLineItemForCartItemBogoFreeProductInfo($cartItem);
				if ($bogoLineItem != null) {
					$order->add_item($bogoLineItem);
				}
			}
		}

		/**
		 * @return \WC_Order_Item_Product
		 */
		private static function _createOrderLineItemForCartItemBogoFreeProductInfo(array $cartItem) {
			$bogoProduct = wc_get_product($cartItem['wmyc_bogo_product_id']);
			if ($bogoProduct != null) {
				$bogoVariation = null;
				if ($cartItem['wmyc_bogo_variation_id'] > 0) {
					$bogoVariation = wc_get_product($cartItem['wmyc_bogo_variation_id']);
				}

				$bogoLineItem = self::_createOrderLineItemForFreeProduct($bogoProduct, 
					$bogoVariation, 
					$cartItem['wmyc_bogo_product_qty']);
			} else {
				$bogoLineItem = null;
			}

			return $bogoLineItem;
		}

		private static function _createOrderLineItemForFreeProduct(\WC_Product $product, \WC_Product|null $variation, $quantity) {
			$lineItem = new WC_Order_Item_Product();
			$lineItem->set_quantity($quantity);
			$lineItem->set_variation_id($variation != null ? $variation->get_id() : 0);
			$lineItem->set_product_id($product->get_id());
			$lineItem->set_name($variation != null ? $variation->get_name() : $product->get_name());
			$lineItem->set_tax_class($variation != null ? $variation->get_tax_class() : $product->get_tax_class());
			$lineItem->set_taxes(array());
			$lineItem->set_total(0);
			$lineItem->set_total_tax(0);
			$lineItem->set_subtotal(0);
			$lineItem->set_subtotal_tax(0);
			$lineItem->set_backorder_meta();
			return $lineItem;
		}

		public static function renderBogoFreeProductCartItemRow($subtotal, array $cartItem, $cartItemKey) {
			$subtotalHtml = $subtotal;
			if (self::_shouldRenderBogoFreeproductCartItemRow($cartItem)) {
				$bogoProduct = wc_get_product($cartItem['wmyc_bogo_product_id']);
				$bogoProductQuantity = $cartItem['wmyc_bogo_product_qty'];

				$subtotalHtml .= self::_renderFreeProductCartItemRow($bogoProduct, 
					$bogoProductQuantity);
			}
			return $subtotalHtml;
		}

		private static function _shouldRenderBogoFreeproductCartItemRow(array $cartItem) {
			return (is_cart() || is_checkout()) 
				&& self::_hasBogoFreeProductInfo($cartItem);
		}

		private static function _renderFreeProductCartItemRow(WC_Product $product, $quantity) {
			$data = new stdClass();
			$data->product = $product;
			$data->productPermalink = $product->get_permalink();
			$data->productThumbnail = $product->get_image();
			$data->productQty = $quantity;

			if (is_cart()) {
				return ViewEngine::render('wmyc-bogo-free-product-cart-item-row.php', 
					$data);
			} else if (is_checkout()) {
				return ViewEngine::render('wmyc-bogo-free-product-order-review-item-row.php', 
					$data);
			} else {
				return '';
			}
		}

		public static function renderBagoFreeProductsItemsRows() {
			if (self::cartHasItems()) {
				$bagoFreeProducts = ProductHandler::getBagoFreeProducts();
				foreach ($bagoFreeProducts as $bagoProduct) {
					if ($bagoProduct->is_in_stock()) {
						echo self::_renderFreeProductCartItemRow($bagoProduct, 1);
					}
				}
			}
		}

		public static function cartHasItems() {
			return WC()->cart->get_cart_contents_count() > 0;
		}

		public static function onCheckoutCreateOrderAddBagoFreeProductItems(WC_Order $order, $data) {
			$bagoFreeProducts = ProductHandler::getBagoFreeProducts();
			foreach ($bagoFreeProducts as $bagoProduct) {
				if ($bagoProduct->is_in_stock()) {
					$addProduct = $bagoProduct;
					$addVariation = null;

					if ($bagoProduct->get_type() == 'variation') {
						$addProduct = wc_get_product($bagoProduct->get_parent_id());
						$addVariation = $bagoProduct;
					}

					$bagoLineLitem = self::_createOrderLineItemForFreeProduct($addProduct, $addVariation, 1);
					$order->add_item($bagoLineLitem);
				}
			}
		}

		public static function init() {
			add_filter('woocommerce_add_cart_item_data', 
				array(__CLASS__, 'onAddBogoFreeProductCartItemData'), 
				10, 
				4);

			add_action('woocommerce_checkout_create_order_line_item', 
				array(__CLASS__, 'onCheckoutAddFreeBogoOrderLineItemIfNeeded'), 
				10, 
				4);

			add_filter('woocommerce_cart_item_set_quantity', 
				array(__CLASS__, 'onProductQuantityUpdateSetBogoFreeProductQuantity'), 
				10, 
				3);

			add_filter('woocommerce_cart_item_subtotal', 
				array(__CLASS__, 'renderBogoFreeProductCartItemRow'), 
				10, 
				3);

			add_action('woocommerce_cart_contents',
				array(__CLASS__, 'renderBagoFreeProductsItemsRows'), 
				10, 
				0);

			add_action('woocommerce_review_order_after_cart_contents',
				array(__CLASS__, 'renderBagoFreeProductsItemsRows'), 
				10, 
				0);

			add_action('woocommerce_checkout_create_order', 
				array(__CLASS__, 'onCheckoutCreateOrderAddBagoFreeProductItems'), 
				10, 
				2);
		}
	}
}