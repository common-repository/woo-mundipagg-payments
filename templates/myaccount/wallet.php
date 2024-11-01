<?php
if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Woocommerce\Mundipagg\Core;
use Woocommerce\Mundipagg\Model\Customer;
use Woocommerce\Mundipagg\Model\Setting;
use Woocommerce\Mundipagg\Helper\Utils;
use Woocommerce\Mundipagg\Model\Account;

$customer = new Customer( get_current_user_id() );

do_action( 'woocommerce_before_account_wallet' );

if ( $customer->cards ) :
	$api_route = get_home_url( null, '/wc-api/' . Account::WALLET_ENDPOINT );
	$swal_data = apply_filters( Core::tag_name( 'account_wallet_swal_data' ), array(
		'title'          => __( 'Waiting...', 'woo-mundipagg-payments' ),
		'text'           => __( 'We are processing your request.', 'woo-mundipagg-payments' ),
		'confirm_title'  => __( 'Are you sure?', 'woo-mundipagg-payments' ),
		'confirm_text'   => __( 'You won\'t be able to revert this!', 'woo-mundipagg-payments' ),
		'confirm_button' => __( 'Yes, delete it!', 'woo-mundipagg-payments' ),
		'cancel_button'  => __( 'No, cancel!', 'woo-mundipagg-payments' ),
		'confirm_color'  => '#3085d6',
		'cancel_color'   => '#d33',
	) );
?>

<table class="woocommerce-wallet-table shop_table shop_table_responsive"
	data-swal='<?php echo wp_json_encode( $swal_data, JSON_HEX_APOS ); ?>'
	data-api-request="<?php echo esc_url( $api_route ); ?>"
	<?php echo /** phpcs:ignore */ Utils::get_component( 'wallet' ); ?>>
	<thead>
		<tr>
			<th class="woocommerce-wallet-name">
				<?php esc_html_e( 'Name', 'woo-mundipagg-payments' ); ?>
			</th>
			<th class="woocommerce-wallet-last-digits">
				<?php esc_html_e( 'Last digits', 'woo-mundipagg-payments' ); ?>
			</th>
			<th class="woocommerce-wallet-status">
				<?php esc_html_e( 'Status', 'woo-mundipagg-payments' ); ?>
			</th>
			<th class="woocommerce-wallet-expire">
				<?php esc_html_e( 'Expire', 'woo-mundipagg-payments' ); ?>
			</th>
			<th class="woocommerce-wallet-brand">
				<?php esc_html_e( 'Brand', 'woo-mundipagg-payments' ); ?>
			</th>
			<th class="woocommerce-wallet-brand">
				<?php esc_html_e( 'Action', 'woo-mundipagg-payments' ); ?>
			</th>
		</tr>
	</thead>
	<tbody>

		<?php foreach ( $customer->cards as $card_id => $card ) : ?>

		<tr>
			<td>
				<?php echo esc_html( $card['holder_name'] ); ?>
			</td>
			<td>
				<?php echo esc_html( $card['last_four_digits'] ); ?>
			</td>
			<td>
				<?php echo esc_html( $card['status'] ); ?>
			</td>
			<td>
				<?php printf( '%s/%s', esc_html( $card['exp_month'] ), esc_html( $card['exp_year'] ) ); ?>
			</td>
			<td>
				<?php echo esc_html( $card['brand'] ); ?>
			</td>
			<td>
				<button class="woocommerce-button button" data-action="remove-card" data-value="<?php echo esc_attr( $card_id ); ?>">
					<?php esc_html_e( 'Remove', 'woo-mundipagg-payments' ); ?>
				</button>
			</td>
		</tr>

		<?php endforeach; ?>

	</tbody>
</table>

<?php

else :

	printf(
		'<p class="woocommerce-message woocommerce-info">%s</p>',
		esc_html__( 'No credit card saved.', 'woo-mundipagg-payments' )
	);

endif;

do_action( 'woocommerce_after_account_orders' );
