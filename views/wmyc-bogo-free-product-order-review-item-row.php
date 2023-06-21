<?php
	defined('WMYC_BOGO_LOADED') or die;
?>
	</td>
</tr>

<tr class="woocommerce-cart-form__cart-item cart_item wmyc_bogo_cart_item">
	<td class="product-name">
		<?php echo esc_html($data->product->get_name()); ?> <strong class="product-quantity">&times; <?php echo $data->productQty; ?></strong>
	</td>
	<td class="product-subtotal">
		<?php echo esc_html__('Free!', 'wmyc-bogo'); ?>
	</td>
</tr>