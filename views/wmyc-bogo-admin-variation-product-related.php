<?php
/**
 * Copyright (c) 2022-2023 Alexandru Boia
 *
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 *	1. Redistributions of source code must retain the above copyright notice, 
 *		this list of conditions and the following disclaimer.
 *
 * 	2. Redistributions in binary form must reproduce the above copyright notice, 
 *		this list of conditions and the following disclaimer in the documentation 
 *		and/or other materials provided with the distribution.
 *
 *	3. Neither the name of the copyright holder nor the names of its contributors 
 *		may be used to endorse or promote products derived from this software without 
 *		specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY 
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */
	defined('WMYC_BOGO_LOADED') or die;
?>

<div>
	<p class="form-row form-row-full">
		<label for="wmyc_bogo_free_product_variation_<?php echo esc_attr($data->variationId); ?>"><?php echo esc_html__('Assign free product', 'wmyc-bogo'); ?></label>
		<?php echo wc_help_tip(__('When the current product is added to the cart, the product you select here will also be added, for free.', 'wmyc-bogo'));?>
		<select name="wmyc_bogo_free_product_variation[<?php echo esc_attr($data->variationId); ?>]" id="wmyc_bogo_free_product_variation_<?php echo esc_attr($data->variationId); ?>" class="select short">
			<option value="-1"><?php echo esc_html__('None', 'wmyc-bogo'); ?></option>
			<option value="-2" <?php echo $data->bogoFreeProductId == '-2' ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Use parent settings', 'wmyc-bogo'); ?></option>
			<?php foreach ($data->availableProductsForBogo as $id => $name): ?>
				<option value="<?php echo esc_attr($id); ?>" <?php echo $data->bogoFreeProductId == $id ? 'selected="selected"' : ''; ?>><?php echo esc_html($name); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p class="form-row form-row-full">
		<label for="wmyc_bago_free_product_variation_enable_<?php echo esc_attr($data->variationId); ?>"><?php echo esc_html__('Grant for free on any order', 'wmyc-bogo'); ?></label>
		<?php echo wc_help_tip(__('When checked, on any order a customer places, the current product will be granted for free.', 'wmyc-bogo'));?>
		<select name="wmyc_bago_free_product_variation_enable[<?php echo esc_attr($data->variationId); ?>]" id="wmyc_bago_free_product_variation_enable_<?php echo esc_attr($data->variationId); ?>">
			<option value="use_parent" <?php echo $data->useBagoFreeProductParentSettings ? 'selected="selected"' : '' ?>><?php echo esc_html__('Use parent settings', 'wmyc-bogo'); ?></option>
			<option value="yes" <?php echo $data->isBagoFreeProduct ? 'selected="selected"' : '' ?>><?php echo esc_html__('Yes', 'wmyc-bogo') ?></option>
			<option value="no" <?php echo !$data->isBagoFreeProduct && !$data->useBagoFreeProductParentSettings  ? 'selected="selected"' : '' ?>><?php echo esc_html__('No', 'wmyc-bogo') ?></option>
		</select>
	</p>
</div>