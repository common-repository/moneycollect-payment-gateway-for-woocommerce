<?php

final class WC_Mc_Creditcard_Blocks_Support extends WC_Wc_Mc_Blocks
{

    public function __construct()
    {
        $this->name = 'moneycollect';
        $this->gateway = new WC_Gateway_Mc_Creditcard();
    }

    /**
     * Returns if this payment method should be active. If false, the scripts will not be enqueued.
     *
     * @return boolean
     */
    public function is_active()
    {
        return $this->gateway->is_available();
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles()
    {
        $script_path = '/assets/js/blocks/moneycollect/index.js';
        $script_asset_path = MONEYCOLLECT_DIR . 'assets/js/frontend/blocks.asset.php';

        $script_asset = file_exists( $script_asset_path )
            ? require($script_asset_path)
            : array(
                'dependencies' => array(),
                'version'      => MONEYCOLLECT_VERSION
            );

        $handle = 'wc-mc-moneycollect-blocks';

        wp_register_script(
            $handle,
            MONEYCOLLECT_URL . $script_path,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );

        if( function_exists( 'wp_set_script_translations' ) ){
            wp_set_script_translations( $handle, 'woocommerce-gateway-' . $this->name, MONEYCOLLECT_URL . 'languages/' );
        }

        return [$handle];
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data()
    {
        $params = $this->gateway->javascript_params();


        $icons = [];

        foreach( $this->gateway->icon_card as $key => $value ){
            if( $this->settings[$key] === 'yes' ){
                $icons[] = [
                    'id'  => 'mc-card-' . $key,
                    'src' => MONEYCOLLECT_URL . '/assets/images/card/' . $key . '.png',
                    'alt' => $value
                ];
            }
        }


        return array_merge( [
            'title'          => $this->settings['title'],
            'description'    => $this->settings['description'],
            'checkout_model' => $this->settings['checkout_model'],
            'icons'          => $icons,
            'showSavedCards' => $this->get_show_saved_cards(),
            'showSaveOption' => $this->get_show_saved_cards(),
            'supports'       => array_filter( $this->gateway->supports, [$this->gateway, 'supports'] )
        ], $params );
    }


    private function get_show_saved_cards() {
        return isset($this->settings['save_card']) && $this->settings['save_card'] === 'yes' && $this->settings['checkout_model'] === '1';
    }



}