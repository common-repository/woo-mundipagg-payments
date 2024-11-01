<?php
if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

if ( ! $model->settings->is_active_billet_and_card() ) {
	return;
}

use Woocommerce\Mundipagg\Core;
use Woocommerce\Mundipagg\View\Checkouts;
use Woocommerce\Mundipagg\Helper\Utils;
use Woocommerce\Mundipagg\Model\Setting;

$installments_type = Setting::get_instance()->cc_installment_type;
$billet_and_card   = true;
$ref_billet        = md5( rand( 1, 1000 ) );
$ref_card          = md5( rand( 1, 1000 ) );

?>

<li>
	<div id="tab-billet-and-card" class="payment_box panel entry-content">

		<fieldset class="wc-credit-card-form wc-payment-form">

			<?php Utils::get_template( 'templates/checkout/choose-credit-card' ); ?>

			<div class="form-row form-row-wide">
				<p class="form-row form-row-first">
					<label for="billet-value">
						<?php esc_html_e( 'Value (Boleto)', 'woo-mundipagg-payments' ); ?><span class="required">*</span>
					</label>
					<input id="billet-value"
							name="billet_value"
							data-mask="#.##0,00"
							data-mask-reverse="true"
							data-element="billet-value"
							data-value="1"
							data-required="true"
							class="input-text wc-credit-card-form-card-expiry">
					<?php
						Utils::get_template(
							'templates/checkout/field-enable-multicustomers',
							array(
								'ref'               => $ref_billet,
								'type'              => 'billet',
								'without_container' => true,
							)
						);
					?>
				</p>

				<p class="form-row form-row-last">
					<label for="card-order-value">
						<?php esc_html_e( 'Value (Credit Card)', 'woo-mundipagg-payments' ); ?> <span class="required">*</span>
					</label>
					<input id="card-order-value"
							name="card_order_value"
							data-element="card-order-value"
							data-value="2"
							data-required="true"
							data-mask="#.##0,00"
							data-mask-reverse="true"
							class="input-text wc-credit-card-form-card-expiry">
					<?php
						Utils::get_template(
							'templates/checkout/field-enable-multicustomers',
							array(
								'ref'               => $ref_card,
								'type'              => 'card',
								'without_container' => true,
							)
						);
					?>
				</p>
			</div>

			<div class="wc-credit-card-info" data-element="fields-cc-data">
				<?php
					Utils::get_template(
						'templates/checkout/common-card-item',
						compact( 'wc_order', 'installments_type' )
					);
				?>
			</div>

			<p class="form-row form-row-first">

				<label for="installments">
					<?php esc_html_e( 'Installments quantity', 'woo-mundipagg-payments' ); ?><span class="required">*</span>
				</label>

				<select id="installments"
						<?php /*phpcs:ignore*/ echo Utils::get_component( 'installments' ); ?>
						data-total="<?php echo esc_html( $wc_order->get_total() ); ?>"
						data-type="<?php echo intval( $installments_type ); ?>"
						data-action="select2"
						data-required="true"
						data-element="installments"
						name="installments">

					<?php
					if ( $installments_type != 2 ) {
						Checkouts::render_installments( $wc_order );
					} else {
						echo '<option value="">...</option>';
					};
					?>

				</select>
			</p>

			<?php Utils::get_template( 'templates/checkout/field-save-card' ); ?>

		</fieldset>

		<?php
			Utils::get_template(
				'templates/checkout/multicustomers-form',
				array(
					'ref'   => $ref_billet,
					'type'  => 'billet',
					'title' => 'Dados comprador (Boleto)',
				)
			);

			Utils::get_template(
				'templates/checkout/multicustomers-form',
				array(
					'ref'   => $ref_card,
					'type'  => 'card',
					'title' => 'Dados comprador (Cartão)',
				)
			);
		?>

		<input style="display:none;"
			data-action="choose-payment"
			data-element="billet-and-card"
			type="radio"
			name="payment_method"
			value="billet_and_card">
	</div>
</li>
