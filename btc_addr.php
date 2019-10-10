<?php
/*
Plugin Name: BTC Address
Plugin URI:
Description: Creates a shortcode to display the balance of a Bitcoin address
Version: 1.0
Author: Daniel Lyne
Author URI:
License: GPLv2
*/

// [btc addr="address" cur="currency"]
add_shortcode( 'btc', 'btc_address_shortcode' );

// Function for shortcode HTML return
function btc_address_shortcode( $atts ) {
    // Extracts parameters from the shortcode
    $parameters = shortcode_atts( array(
        'addr' => 'bc1qw6sg9ktmzuqqplhhhnguuxaet45zd5kynrmcet',
        'cur' => 'USD',
    ), $atts );

    $balance = return_btc_balance( $parameters['addr'] );
    $in_currency = return_currency_equivalent( $balance, $parameters['cur'] );

    $output = 'Address: ';
    $output .= $parameters['addr'];
    $output .= '<br>Balance: ';
    $output .= $balance;
    $output .= ' BTC | ';
    $output .= $in_currency;
    $output .= ' ';
    $output .= $parameters['cur'];

    return $output;
}

// Returns the value of the Bitcoin balance in a given currency
function return_currency_equivalent( $btc, $cur ) {
    $url = 'https://www.blockonomics.co/api/price?currency=';
    $url .= $cur;
    $ch = curl_init( $url );

    // Sets up and processes the curl
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $response = curl_exec( $ch );
    curl_close( $ch );

    // Return the value per BTC multiplied by the amount of BTC
    $processed_response = json_decode( $response, true );
    return number_format( ($processed_response['price'] * $btc), 2);
}

// Returns the balance of a Bitcoin address
function return_btc_balance( $addr ) {
    $url = 'https://www.blockonomics.co/api/balance';
    $ch = curl_init($url);

    // Prepares the fields to be sent to the Blockonomics API
    $fields = '{"addr":"';
    $fields .= $addr;
    $fields .= '"}';

    // Sets up curl
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    
    // Processes curl
    $response = curl_exec( $ch );
    curl_close( $ch );
    
    // Return response converted from Satoshis to Bitcoin
    $processed_response = json_decode( $response, true );
    
    echo '<script>';
    echo 'console.log('. $response .')';
    echo '</script>';

    return $processed_response['response'][0]['confirmed']*0.00000001;
}