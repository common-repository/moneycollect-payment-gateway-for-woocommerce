<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wc_Mc_Payment_Token
{

    var $gateway_id = 'moneycollect';

    public function __construct() {
        add_filter( 'woocommerce_get_customer_payment_tokens', [ $this, 'get_payment_tokens' ], 10, 3 );
        //add_filter( 'woocommerce_payment_methods_list_item', [ $this, 'get_account_saved_payment_methods_list_item_sepa' ], 10, 2 );
        add_action( 'woocommerce_payment_token_deleted', [ $this, 'deleted_payment_tokens' ], 10, 2 );
        //add_action( 'woocommerce_payment_token_set_default', [ $this, 'set_payment_default' ] );
    }

    public function get_payment_tokens( $tokens, $customer_id, $gateway_id )
    {
        if ( is_user_logged_in() && class_exists( 'WC_Payment_Token_CC' ) ) {
            //if( $gateway_id == $this->gateway_id ){

                $customer = new Wc_Mc_Payment_Customer( $customer_id );

                $payment_methods = $customer->get_payment_methods($this->gateway_id);

                $stored_tokens = [];

                foreach ( $tokens as $token ) {
                    if( $customer->get_id() == '' ){
                        if( $token->get_gateway_id() == $this->gateway_id ){
                            WC_Payment_Tokens::delete( $token->get_id() );
                        }
                    }else{
                        $stored_tokens[] = $token->get_token();
                    }
                }

                if( $payment_methods ){
                    foreach ( $payment_methods as $method ) {
                        if( isset($method['card']) && !in_array( $method['id'], $stored_tokens) ) {

                            $token = new WC_Payment_Token_CC();
                            $token->set_token( $method['id'] );
                            $token->set_gateway_id( $this->gateway_id );
                            $token->set_card_type( strtolower( $method['card']['brand'] ) );
                            $token->set_last4( $method['card']['last4'] );
                            $token->set_expiry_month( $method['card']['expMonth'] );
                            $token->set_expiry_year( $method['card']['expYear'] );

                            $token->set_user_id( $customer_id );
                            $token->save();
                            $tokens[ $token->get_id() ] = $token;
                            $this->set_payment_default($token->get_id());

                        }
                    }
                }

            //}
        }
        return $tokens;
    }


    public function deleted_payment_tokens($token_id, $token){
        if( $token->get_gateway_id() == $this->gateway_id ){
            $customer = new Wc_MC_Payment_Customer(get_current_user_id());
            $customer->delete_payment_method($token->get_token());
        }
    }

    public function set_payment_default( $token_id ) {
        $token = WC_Payment_Tokens::get( $token_id );
        if ( $token->get_gateway_id() == $this->gateway_id ) {
            $token->set_default(true);
            $token->save();
        }
    }

}

new Wc_Mc_Payment_Token();



