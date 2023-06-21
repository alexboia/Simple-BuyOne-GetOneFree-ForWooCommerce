<?php
	defined('WMYC_BOGO_LOADED') or die;
?>

	</td>
</tr>

<tr class="woocommerce-cart-form__cart-item cart_item wmyc_bogo_cart_item">
	<td></td>
	<td class="product-thumbnail">
		<a href="<?php echo esc_attr($data->productPermalink) ?>"><?php echo $data->productThumbnail; ?></a>
	</td>
	<td class="product-name">
		<a href="<?php echo esc_attr($data->productPermalink) ?>"><?php echo esc_html($data->product->get_name()); ?></a>
	</td>
	<td class="product-price">
		<?php echo esc_html__('Free!', 'wmyc-bogo'); ?>
	</td>
	<td class="product-quantity">
		<?php echo $data->productQty; ?>
	</td>
	<td class="product-subtotal">
		<?php echo esc_html__('Free!', 'wmyc-bogo'); ?>
	</td>
</tr>