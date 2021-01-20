<div class="tab-content">
	<?php
	$products = array(
		"themes"  => array(
			'food-cart' => array(
				'price'     => 49,
				'title'     => "FoodCart – Restaurant WordPress Theme",
				'image_url' => TLPFoodMenu()->getAssetsUrl() . 'images/food-cart.png',
				'url'       => 'https://www.radiustheme.com/downloads/foodcart-restaurant-wordpress-theme/',
				'demo_url'  => 'https://www.radiustheme.com/demo/wordpress/themes/foodcart/',
				'buy_url'   => 'https://www.radiustheme.com/downloads/foodcart-restaurant-wordpress-theme/',
				'doc_url'   => 'https://radiustheme.com/demo/wordpress/themes/foodcart/docs/'
			),
			'red-chili' => array(
				'price'     => 39,
				'title'     => "RedChili - Restaurant WordPress Theme",
				'image_url' => TLPFoodMenu()->getAssetsUrl() . 'images/red-chili.png',
				'url'       => 'https://themeforest.net/item/red-chili-restaurant-wordpress-theme/20166175',
				'demo_url'  => 'https://radiustheme.com/demo/wordpress/redchili/',
				'buy_url'   => 'https://themeforest.net/item/red-chili-restaurant-wordpress-theme/20166175',
				'doc_url'   => 'https://radiustheme.com/demo/wordpress/redchili/docs/'
			)
		),
		"plugins" => array(
			"food-menu-pro" => array(
				'price'     => 19,
				'title'     => "Food Menu PRO Plugin for WordPress",
				'image_url' => TLPFoodMenu()->getAssetsUrl() . 'images/food-menu-pro.png',
				'url'       => 'https://www.radiustheme.com/downloads/food-menu-pro-wordpress/',
				'demo_url'  => 'http://radiustheme.com/demo/wordpress/foodmenupro/',
				'buy_url'   => 'https://www.radiustheme.com/downloads/food-menu-pro-wordpress/',
				'doc_url'   => 'https://www.radiustheme.com/setup-configure-food-menu-pro-wordpress/'
			)
		)
	);
	echo TLPFoodMenu()->get_product_list_html( $products );
	?>
</div>