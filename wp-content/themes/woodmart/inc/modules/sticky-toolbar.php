<?php

if ( ! function_exists( 'woodmart_get_sticky_toolbar_fields' ) ) {
	/**
	 * All available fields for Theme Settings sorter option.
	 *
	 * @since 3.6
	 */
	function woodmart_get_sticky_toolbar_fields() {
		$product_attributes = array();

		$fields = array(
			'enabled'  => array(
				'shop'     => esc_html__( 'Shop page', 'woodmart' ),
				'sidebar'  => esc_html__( 'Off canvas sidebar', 'woodmart' ),
				'wishlist' => esc_html__( 'Wishlist', 'woodmart' ),
				'cart'     => esc_html__( 'Cart', 'woodmart' ),
				'account'  => esc_html__( 'My account', 'woodmart' ),
			),
			'disabled' => array(
				'mobile'   => esc_html__( 'Mobile menu', 'woodmart' ),
				'home'     => esc_html__( 'Home page', 'woodmart' ),
				'blog'     => esc_html__( 'Blog page', 'woodmart' ),
				'compare'  => esc_html__( 'Compare', 'woodmart' ),
			),
		);

		if ( apply_filters( 'woodmart_toolbar_search', false ) ) {
			$fields['disabled']['search'] = esc_html__( 'Search', 'woodmart' );
		}

		return $fields;
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_template' ) ) {
	/**
	 * Sticky toolbar template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_template() {
		$fields  = woodmart_get_opt( 'sticky_toolbar_fields' );
		$classes = '';

		if ( isset( $fields['enabled']['placebo'] ) ) {
			unset( $fields['enabled']['placebo'] );
		}

		if ( ! woodmart_get_opt( 'sticky_toolbar' ) || ! $fields['enabled'] ) {
			return;
		}

		if ( woodmart_get_opt( 'sticky_toolbar_label' ) ) {
			$classes .= ' woodmart-toolbar-label-show';
		}

		?>
			<div class="woodmart-toolbar<?php echo esc_attr( $classes ); ?>">
		<?php
		foreach ( $fields['enabled'] as $key => $value ) {
			switch ( $key ) {
				case 'wishlist':
					woodmart_sticky_toolbar_wishlist_template();
					break;
				case 'cart':
					woodmart_sticky_toolbar_cart_template();
					break;
				case 'compare':
					woodmart_sticky_toolbar_compare_template();
					break;
				case 'search':
					woodmart_sticky_toolbar_search_template();
					break;
				case 'account':
					woodmart_sticky_toolbar_account_template();
					break;
				case 'home':
					woodmart_sticky_toolbar_page_link_template( $key );
					break;
				case 'blog':
					woodmart_sticky_toolbar_page_link_template( $key );
					break;
				case 'shop':
					woodmart_sticky_toolbar_page_link_template( $key );
					break;
				case 'mobile':
					woodmart_sticky_toolbar_mobile_menu_template();
					break;
				case 'sidebar':
					woodmart_sticky_sidebar_button( false, true );
					break;
				case 'search':
					woodmart_sticky_toolbar_search_template();
					break;
			}
		}
		?>
			</div>
		<?php

	}

	add_action( 'woodmart_before_wp_footer', 'woodmart_sticky_toolbar_template' );
}

if ( ! function_exists( 'woodmart_sticky_toolbar_wishlist_template' ) ) {
	/**
	 * Sticky toolbar wishlist template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_wishlist_template() {
		if ( ! woodmart_woocommerce_installed() || ! class_exists( 'YITH_WCWL' ) ) {
			return;
		}

		$settings      = whb_get_settings();
		$product_count = false;
		$classes       = '';

		if ( isset( $settings['wishlist']['hide_product_count'] ) ) {
			$product_count = ! $settings['wishlist']['hide_product_count'];
		}

		if ( ! $product_count ) {
			$classes .= ' without-product-count';
		}

		?>
		<div class="woodmart-wishlist-info-widget whb-wishlist-icon<?php echo esc_attr( $classes ); ?>" title="<?php echo esc_attr__( 'My wishlist', 'woodmart' ); ?>">
			<a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
				<span class="wishlist-info-wrap">
					<span class="wishlist-icon">
						<?php if ( $product_count ) : ?>
							<span class="wishlist-count">
								<?php echo YITH_WCWL()->count_products(); ?>
							</span>
						<?php endif; ?>
					</span>
				</span>
				<span class="woodmart-toolbar-label">
					<?php esc_html_e( 'Wishlist', 'woodmart' ); ?>
				</span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_cart_template' ) ) {
	/**
	 * Sticky toolbar cart template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_cart_template() {
		if ( ! woodmart_woocommerce_installed() || ( ! is_user_logged_in() && woodmart_get_opt( 'login_prices' ) ) ) {
			return;
		}

		$settings = whb_get_settings();
		$opener   = false;
		$classes  = '';

		if ( isset( $settings['cart']['position'] ) ) {
			$opener = $settings['cart']['position'] == 'side';
		}

		if ( $opener ) {
			$classes .= ' cart-widget-opener';
		}

		?>
		<div class="woodmart-shopping-cart woodmart-cart-design-5 woodmart-cart-alt<?php echo esc_attr( $classes ); ?>" title="<?php echo esc_attr__( 'My cart', 'woodmart' ); ?>">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				<span class="woodmart-cart-wrapper">
					<span class="woodmart-cart-icon"></span>
					<span class="woodmart-cart-totals">
						<?php woodmart_cart_count(); ?>
					</span>
				</span>
				<span class="woodmart-toolbar-label">
					<?php esc_html_e( 'Cart', 'woodmart' ); ?>
				</span>
			</a>
		</div>
		<?php
	}
}


if ( ! function_exists( 'woodmart_sticky_toolbar_compare_template' ) ) {
	/**
	 * Sticky toolbar compare template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_compare_template() {
		if ( ! woodmart_woocommerce_installed() || ! woodmart_get_opt( 'compare' ) ) {
			return;
		}

		$settings      = whb_get_settings();
		$product_count = false;
		$classes       = '';

		if ( isset( $settings['compare']['hide_product_count'] ) ) {
			$product_count = ! $settings['compare']['hide_product_count'];
		}

		if ( ! $product_count ) {
			$classes .= ' without-product-count';
		}

		?>
		<div class="woodmart-compare-info-widget whb-compare-icon<?php echo esc_attr( $classes ); ?>" title="<?php echo esc_attr__( 'Compare products', 'woodmart' ); ?>">
			<a href="<?php echo esc_url( woodmart_get_compare_page_url() ); ?>">
				<span class="compare-info-wrap">
					<span class="compare-icon">
						<?php if ( $product_count ) : ?>
							<span class="compare-count"><?php echo woodmart_get_compare_count(); ?></span>
						<?php endif; ?>
					</span>
				</span>
				<span class="woodmart-toolbar-label">
					<?php esc_html_e( 'Compare', 'woodmart' ); ?>
				</span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_search_template' ) ) {
	/**
	 * Sticky toolbar search template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_search_template() {
		?>
		<div class="whb-search search-button mobile-search-icon">
			<a href="#">
				<span class="search-button-icon"></span>
				<span class="woodmart-toolbar-label">
					<?php esc_html_e( 'Search', 'woodmart' ); ?>
				</span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_account_template' ) ) {
	/**
	 * Sticky toolbar account template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_account_template() {
		if ( ! woodmart_woocommerce_installed() ) {
			return;
		}

		$settings = whb_get_settings();
		$is_side  = isset( $settings['account'] ) && 'side' === $settings['account']['form_display'];
		$classes  = ! is_user_logged_in() && $is_side ? ' login-side-opener' : '';

		?>
		<div class="whb-header-links woodmart-navigation woodmart-header-links">
			<ul class="menu">
				<li class="item-level-0 my-account-with-icon menu-item-register menu-simple-dropdown item-event-hover<?php echo esc_attr( $classes ); ?>">
					<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>">
						<span class="woodmart-toolbar-label">
							<?php esc_html_e( 'My account', 'woodmart' ); ?>
						</span>
					</a>
				</li>
			</ul>		
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_page_link_template' ) ) {
	/**
	 * Sticky toolbar page link template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_page_link_template( $key ) {
		$url = '';

		switch ( $key ) {
			case 'blog':
				$url  = get_permalink( get_option( 'page_for_posts' ) );
				$text = esc_html__( 'Blog', 'woodmart' );
				break;
			case 'home':
				$url  = get_home_url();
				$text = esc_html__( 'Home', 'woodmart' );
				break;
			case 'shop':
				$url  = woodmart_woocommerce_installed() ? get_permalink( wc_get_page_id( 'shop' ) ) : get_home_url();
				$text = esc_html__( 'Shop', 'woodmart' );
				break;
		}

		?>
		<div class="woodmart-toolbar-<?php echo esc_attr( $key ); ?> woodmart-toolbar-item">
			<a href="<?php echo esc_url( $url ); ?>">
				<span class="woodmart-toolbar-label">
					<?php esc_html_e( $text ); ?>
				</span>
			</a>
		</div>
		<?php
	}
}

if ( ! function_exists( 'woodmart_sticky_toolbar_mobile_menu_template' ) ) {
	/**
	 * Sticky toolbar mobile menu template
	 *
	 * @since 3.6
	 */
	function woodmart_sticky_toolbar_mobile_menu_template() {
		?>
		<div class="woodmart-burger-icon mobile-nav-icon whb-mobile-nav-icon mobile-style-icon">
			<span class="woodmart-burger"></span>
			<span class="woodmart-toolbar-label">
				<?php esc_html_e( 'Menu', 'woodmart' ); ?>
			</span>
		</div>
		<?php
	}
}
