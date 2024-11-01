<?php
namespace Woocommerce\Mundipagg\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Woocommerce\Mundipagg\Core;
use Woocommerce\Mundipagg\Helper\Utils;
use Woocommerce\Mundipagg\Model\Setting;
use Woocommerce\Mundipagg\Model\Account;
use Woocommerce\Mundipagg\Model\Customer;

class Accounts
{
	protected $wallet_endpoint;

	const WALLET_ENDPOINT = 'wallet-mundipagg';

	const OPT_WALLET_ENDPOINT = 'woocommerce_mundipagg_wallet_endpoint';

	public function __construct()
	{
		$this->wallet_endpoint = get_option( self::OPT_WALLET_ENDPOINT, self::WALLET_ENDPOINT );

		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_filter( 'woocommerce_account_settings', array( $this, 'settings_account' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'menu_items' ) );
		add_filter( 'woocommerce_get_query_vars', array( $this, 'query_vars' ) );
		add_action( "woocommerce_account_{$this->wallet_endpoint}_endpoint", array( $this, 'wallet_content' ) );
		add_action( 'woocommerce_api_' . Account::WALLET_ENDPOINT, array( $this, 'remove_credit_card' ) );
	}

	public function menu_items( $items )
	{
		$last_value = end( $items );
		$last_key   = key( $items );

		unset( $items[ $last_key ] );

		$items[ $this->wallet_endpoint ] = __( 'Wallet', 'woo-mundipagg-payments' );
		$items[ $last_key ]              = $last_value;

		return $items;
	}

	public function query_vars( $vars )
	{
		$vars[ $this->wallet_endpoint ] = $this->wallet_endpoint;

		return $vars;
	}

	public function add_endpoints()
	{
		global $woocommerce;

		add_rewrite_endpoint( $this->wallet_endpoint, $woocommerce->query->get_endpoints_mask() );
	}

	public function settings_account( $settings )
	{
		$wallet = array(
			'title'    => __( 'Wallet', 'woo-mundipagg-payments' ),
			'desc'     => __( 'Your wallet for Mundipagg registered credit cards', 'woo-mundipagg-payments' ),
			'id'       => self::OPT_WALLET_ENDPOINT,
			'type'     => 'text',
			'default'  => $this->wallet_endpoint,
			'desc_tip' => true,
		);

		array_splice( $settings, count( $settings ) - 2, 0, array( $wallet ) );

		return $settings;
	}

	public function wallet_content()
	{
		Utils::get_template( 'templates/myaccount/wallet' );
	}

	public function remove_credit_card()
	{
		if ( ! Utils::is_request_ajax() || Utils::server( 'REQUEST_METHOD' ) !== 'POST' ) {
			exit( 0 );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'User not loggedin.', 'woo-mundipagg-payments' ) );
		}

		$customer    = new Customer( get_current_user_id() );
		$saved_cards = $customer->cards;
		$card_id     = Utils::post( 'card_id' );

		if ( ! isset( $saved_cards[ $card_id ] ) ) {
			wp_send_json_error( __( 'Card not found.', 'woo-mundipagg-payments' ) );
		}

		unset( $saved_cards[ $card_id ] );

		$customer->cards = $saved_cards;

		wp_send_json_success( __( 'Card removed successfully.', 'woo-mundipagg-payments' ) );
	}
}
