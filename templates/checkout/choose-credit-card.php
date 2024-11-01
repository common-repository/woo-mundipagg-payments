<?php
if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Woocommerce\Mundipagg\Core;
use Woocommerce\Mundipagg\Model\Customer;
use Woocommerce\Mundipagg\Model\Setting;
use Woocommerce\Mundipagg\Helper\Utils;

if ( ! is_user_logged_in() ) {
	return;
}

$setting = Setting::get_instance();

if ( ! $setting->is_allowed_save_credit_card() ) {
	return;
}

$customer = new Customer( get_current_user_id() );
$suffix   = isset( $suffix ) ? $suffix : '';

if ( ! $customer->cards ) {
	return;
}

?>

<p class="form-row form-row-wide">

	<?php esc_html_e( 'Credit cards save', 'woo-mundipagg-payments' ); ?><br>

	<select name="card_id<?php echo esc_html( $suffix ); ?>" id="field-choose-card"
			data-action="select2"
			data-installments-type="<?php echo intval( Setting::get_instance()->cc_installment_type ); ?>"
			data-element="choose-credit-card">
		<option value="">
			<?php esc_html_e( 'Saved credit card', 'woo-mundipagg-payments' ); ?>
		</option>

		<?php
		foreach ( $customer->cards as $id => $card ) :
			printf(
				'<option data-brand="%3$s" value="%2$s">(%1$s) •••• •••• •••• %4$s</option>',
				esc_html( strtoupper( $card['brand'] ) ),
				esc_attr( $id ),
				esc_html( strtolower( $card['brand'] ) ),
				esc_html( $card['last_four_digits'] )
			);
		endforeach;
		?>
	</select>
 </p>
