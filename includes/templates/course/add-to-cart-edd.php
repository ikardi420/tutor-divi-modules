<?php
/**
 * Course Price EDD template
 *
 * @package DTLMS/PriceEDDTemplate
 *
 * @version v2.0.0
 */

$product_id = isset( $data['product_id'] ) ? $data['product_id'] : tutor_utils()->get_course_product_id();
$download   = class_exists( '\\EDD_Download' ) ? new \EDD_Download( $product_id ) : '';
if ( '' === $download ) {
	esc_html_e( 'Easy Digital Download is not exists.', 'tutor-lms-divi-modules' );
	return;
}
if ( $download->ID ) {
	if ( ! is_user_logged_in() ) {
		/**
		 * Add required loggedin class
		 *
		 * @since v 1.5.5
		 */
		$button_behavior = edd_get_download_button_behavior( $download->ID );
		$args            = apply_filters(
			'edd_purchase_link_defaults',
			array(
				'text'  => $button_behavior == 'direct' ? edd_get_option( 'buy_now_text', __( 'Buy Now', 'easy-digital-downloads' ) ) : edd_get_option( 'add_to_cart_text', __( 'Purchase', 'easy-digital-downloads' ) ),
				'style' => edd_get_option( 'button_style', 'button' ),
				'color' => edd_get_option( 'checkout_color', 'blue' ),
				'class' => 'edd-submit cart-required-login',
			)
		);
		$button_text     = edd_currency_filter( edd_format_amount( $download->price ) ) . '&nbsp;&ndash;&nbsp;' . $args['text'];
		$button_class    = implode( ' ', array( $args['style'], $args['color'], $args['class'] ) );

		echo wp_kses_post( '<button class="' . $button_class . '">' . $button_text . '</button>' );
	} else {
		echo edd_get_purchase_link( array( 'download_id' => $download->ID ) );
	}
}
