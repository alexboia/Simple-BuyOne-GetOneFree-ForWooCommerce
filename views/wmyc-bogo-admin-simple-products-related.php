<?php
	defined('WMYC_BOGO_LOADED') or die;
?>

<div class="options_group show_if_simple">
	<p class="form-field">
		<label for="wmyc_bogo_free_product"><?php echo esc_html__('Assign free product', 'wmyc-bogo'); ?></label>
		<select name="wmyc_bogo_free_product" id="wmyc_bogo_free_product" class="select short">
			<option value="-1"><?php echo esc_html__('None', 'wmyc-bogo'); ?></option>
			<?php foreach ($data->availableProductsForBogo as $id => $name): ?>
				<option value="<?php echo esc_attr($id); ?>" <?php echo $data->bogoFreeProductId == $id ? 'selected="selected"' : ''; ?>><?php echo esc_html($name); ?></option>
			<?php endforeach; ?>
		</select>
		<?php echo wc_help_tip(__('When the current product is added to the cart, the product you select here will also be added, for free.', 'wmyc-bogo'));?>
	</p>
</div>
<div class="options_group show_if_simple">
	<p class="form-field">
		<label for="wmyc_bago_free_product_enable"><?php echo esc_html__('Grant for free on any order', 'wmyc-bogo'); ?></label>
		<input id="wmyc_bago_free_product_enable" 
			name="wmyc_bago_free_product_enable" 
			type="checkbox" 
			value="yes" 
			<?php echo $data->isBagoFreeProduct ? 'checked="checked"' : ''; ?> 
		/>
		<?php echo wc_help_tip(__('When checked, on any order a customer places, the current product will be granted for free.', 'wmyc-bogo'));?>
	</p>
</div>