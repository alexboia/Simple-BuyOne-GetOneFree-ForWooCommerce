<?php
namespace WmycBogo {

    use WC_Product;

	class FreeProductInfo {
		/**
		 * @var WC_Product
		 */
		private $_product;

		private $_fromDate;

		private $_toDate;

		private $_quantityPolicy;

		private $_canAccumulate;

		public function __construct(WC_Product $product, 
				$fromDate, 
				$toDate, 
				$quantityPolicy = null, 
				$canAccumulate = true) {
			$this->_product = $product;
			$this->_fromDate = $fromDate;
			$this->_toDate = $toDate;
			$this->_quantityPolicy = $quantityPolicy;
			$this->_canAccumulate = $canAccumulate;
		}

		public function getProduct() {
			return $this->_product;
		}

		public function getProductId() {
			return $this->_product->get_id();
		}

		public function canBeOrdered() {
			return $this->_product->is_in_stock() 
				|| $this->_product->backorders_allowed();
		}

		public function getFromDate() {
			return $this->_fromDate;
		}

		public function getToDate() {
			return $this->_toDate;
		}

		public function getQuantityPolicy() {
			return $this->_quantityPolicy;
		}

		public function canAccumulate() {
			return $this->_canAccumulate;
		}
	}
}