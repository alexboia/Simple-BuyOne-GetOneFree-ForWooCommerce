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