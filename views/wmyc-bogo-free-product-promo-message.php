<?php
	defined('WMYC_BOGO_LOADED') or die;
?>

<?php if ($data->useDefaultStyling): ?>
	<style type="text/css">
		.wmycaf-promo-bogo-free-product {
			background-color: #f5f5f5;
			padding: 16px 32px 16px 32px;
			margin-bottom: 16px;
			margin-top: 16px;
			position: relative;
			background-color: #f7f6f7;
			color: #515151;
			list-style: none outside;
			text-align: center;
		}
	</style>
<?php endif; ?>

<div class="wmycaf-promo-bogo-free-product">
	<?php echo $data->message; ?>
</div>