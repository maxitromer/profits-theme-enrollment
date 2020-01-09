<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Integrate Woocommerce Data with Profits Theme
function woocommerce_to_profits_theme ( $profits_url, $sku, $status, $email, $fname ) {

	if ( !empty( $sku ) ) {

		// Define cases for every status

		// $statuses_to_abandoned_cart = array ('pending', 'failed','processing', );
		$statuses_to_enroll         = array ('completed', 'active');
		// $statuses_to_pause          = array ('on-hold', 'pending-cancel' );
		$statuses_to_cancel         = array ('cancelled', 'refunded', 'expired' );

		// EXTRACT THE FORMS FOR EVERY ACTION IN THE SKU
		$sku_push_product = explode("-", explode("_", $sku)['2']);

		// SEARCH IN EVERY FORM ACTION
		foreach ($sku_push_product as $key => $push_product) {
			
			// IF THE ACTION FORM IS FOR THE PROFITS THEME

			if (substr( $push_product, 0, 2 ) == 'PT') {
		
				// EXTRACT THE FORM NUMBER

				$product_id = (int)substr($push_product, 2);

				// IF THE STATUS IS IN THE ENROLL ARRAY ...

				if ( in_array ( $status, $statuses_to_enroll ) ) {

					// ENROLL IN PROFITS THEME

					push_profits_theme( $profits_url, $email, $product_id, 'enroll', $fname );

				// IF THE STATUS IS IN THE CANCEL ARRAY ...

				}  else if ( in_array ( $status, $statuses_to_cancel ) ) {	

					// DOWNGRADE IN PROFITS THEME ...

					push_profits_theme( $profits_url, $email, $product_id, 'downgrade', $fname );

				}
				
			}

		}

	}

}


// Enroll or downgrade to profits theme
function push_profits_theme($profits_url, $email, $id_producto, $transaction_type, $fname = null) {

	// Crea la matriz de datos que va a ser enviada
	$post_data['member_name'] = $fname;
	$post_data['member_email'] = $email;
	$post_data['product_id'] = $id_producto; // ID del producto en Profits Theme
	 
	// Recorre la matriz y prepara los datos para su publicacion (key1=value1)
	foreach ( $post_data as $key => $value) {
	    $post_items[] = $key . '=' . $value;
	}

	// Define the transaction resolved by the function	 
	if ($transaction_type == 'downgrade') {

		$transaction_type = 'downgrade';

	} else {

		$transaction_type = 'quick';

	}

	// Crea la cadena final que sera publicada usando implode()
	$post_string = implode ('&', $post_items);
	 
	// Crea la cURL coneccion con la pagina donde enviara los datos
	$curl_connection = 
	  curl_init( $profits_url . '?mode=register&type=' . $transaction_type );
	 
	if (!$curl_connection) {

		$return_value = "ERROR: Couldn't initialize a cURL handle";

	    return $return_value;

	}
	 
	// Configura opciones
	curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_connection, CURLOPT_HEADER        , true);  // we want headers
	curl_setopt($curl_connection, CURLOPT_NOBODY        , true);  // we don't need body
	curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl_connection, CURLOPT_POST          , 1);
	 
	// Configura los datos a ser enviados
	curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
	 
	// Realiza la solicitud
	$result = curl_exec($curl_connection);


	if ( empty( $result ) ) {

	    // some kind of error happened
	    $error  = curl_error( $curl_connection );

	    curl_close( $curl_connection ); // close cURL handler

	    $return_value = "ERROR: " . $err;

	    return $return_value;

	} else {

	    $info = curl_getinfo( $curl_connection );

	    curl_close( $curl_connection ); // close cURL handler

	    if ( empty($info['http_code']) ) {

	    		$return_value = "ERROR: No HTTP code was returned";

	    		return $return_value;

	    } else {

	        // load the HTTP codes
	        $http_codes = parse_ini_file("curl_results.ini");
	       
	        // echo results
	        $return_value = "RESULT CODE: " . $info['http_code'] . " - STATUS: " . $http_codes[$info['http_code']] . " - TOTAL TIME: " . $info['total_time'] . " seconds";

	        return $return_value;

	    }

	}

}


?> 