<?php


final class WC_Mc_Checkout_Blocks_Support extends WC_Wc_Mc_Blocks {

    public function __construct( $name )
    {
        if(  $name == 'alipayhk' ){
            $this->name = 'moneycollect_alipay_hk';
        }
        else{
            $this->name = 'moneycollect_'.$name;
        }

        $class = 'WC_Gateway_Mc_' . ucfirst($name);
        $this->gateway = new $class();
    }
}