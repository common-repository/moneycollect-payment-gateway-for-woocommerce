<?php
/**
 * Plugin Name: Moneycollect Payment Gateway for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/monetcollect-payments-gateway/
 * Description: Moneycollect Payment
 * Version: 1.2.08
 * Tested up to: 5.8
 * Required PHP version: 7.0
 * Author: MoneyCollect
 * Author URI:
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.zh-cn.html
 */

if (! defined ( 'ABSPATH' ))
    exit (); // Exit if accessed directly

const MONEYCOLLECT_NAME = 'Moneycollect';
const MONEYCOLLECT_VERSION = '1.2.08';
const MONEYCOLLECT_METHOD = [
    'alipay',
    'alipayhk',
    'bancontact',
    'boleto',
    'creditcard',
    'dana',
    'enets',
    'eps',
    'fpx',
    'gcash',
    'giropay',
    'ideal',
    'kakaopay',
    'klarna',
    'konbini',
    'mybank',
    'payeasy',
    'paysafecard',
    'payu',
    'pix',
    'poli',
    'przelewy24',
    'sofort',
    'tng',
    'truemoney',
    'wechatpay'
];
define('MONEYCOLLECT_DIR',rtrim(plugin_dir_path(__FILE__),'/'));
define('MONEYCOLLECT_URL',rtrim(plugin_dir_url(__FILE__),'/'));

function woocommerce_moneycollect_missing_wc_notice() {
    /* translators: 1. URL link. */
    echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'MoneyCollect requires WooCommerce to be installed and active. You can download %s here.', 'moneycollect' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

function woocommerce_moneycollect_wc_not_supported() {
    /* translators: $1. Minimum WooCommerce version. $2. Current WooCommerce version. */
    echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'MoneyCollect requires WooCommerce %1$s or greater to be installed and active. WooCommerce %2$s is no longer supported.', 'moneycollect' ), MONEYCOLLECT_VERSION, WC_VERSION ) . '</strong></p></div>';
}

add_action( 'plugins_loaded', 'moneycollect_loaded' );
function moneycollect_loaded(){

    load_plugin_textdomain( 'moneycollect', false,   plugin_basename( dirname( __FILE__ ) ) . '/languages/'  );

    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'woocommerce_moneycollect_missing_wc_notice' );
        return;
    }

    if ( version_compare( WC_VERSION, MONEYCOLLECT_VERSION, '<' ) ) {
        add_action( 'admin_notices', 'woocommerce_moneycollect_wc_not_supported' );
        return;
    }

    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-gateway.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-setting.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-api.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-fun.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-logger.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-customer.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-return.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-webhook.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-token.php');
    require_once (MONEYCOLLECT_DIR.'/includes/class-wc-mc-blocks.php');

    foreach (MONEYCOLLECT_METHOD as $key => $value){
        require_once(MONEYCOLLECT_DIR.'/includes/payment-methods/class-mc-'.$value.'.php');
    }

}

add_action( 'woocommerce_blocks_loaded', 'woocommerce_gateway_dummy_moneycollect_block_support' );
function woocommerce_gateway_dummy_moneycollect_block_support()
{
    if( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ){
        require_once(MONEYCOLLECT_DIR . '/includes/payment-methods/blocks/class-mc-creditcard-blocks.php');
        require_once(MONEYCOLLECT_DIR . '/includes/payment-methods/blocks/class-mc-checkout-blocks.php');
        add_action(
            'woocommerce_blocks_payment_method_type_registration',
            function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
                $payment_method_registry->register( new WC_Mc_Creditcard_Blocks_Support() );
            }
        );

        foreach( MONEYCOLLECT_METHOD as $value ){
            if( $value != 'creditcard' ){
                add_action(
                    'woocommerce_blocks_payment_method_type_registration',
                    function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) use ( $value ) {
                        $payment_method_registry->register( new WC_Mc_Checkout_Blocks_Support( $value ) );
                    }
                );
            }
        }


    }
}


add_filter('woocommerce_payment_gateways','moneycollect_add_gateway',10,1);
function moneycollect_add_gateway($methods){
    foreach (MONEYCOLLECT_METHOD as $key => $value){
        $val = ucfirst($value);
        $methods[] = 'WC_Gateway_Mc_'.$val;
    }
    return $methods;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'moneycollect_plugin_edit_link' );
function moneycollect_plugin_edit_link( $links ){
    return array_merge(
        array(
            'settings' => '<a href="'.admin_url( 'admin.php?page=wc-settings&tab=checkout&section=moneycollect').'">'.__( 'Settings', 'woocommerce' ).'</a>'
        ),
        $links
    );
}

add_action( 'woocommerce_thankyou', 'moneycollect_thankyou_page'  );

function moneycollect_thankyou_page( $order_id ) {
    $order = wc_get_order( $order_id );
    if( $order->get_payment_method() === 'moneycollect' ){
        wc_print_notices();
    }
}
