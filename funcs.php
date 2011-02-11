<?php

	if ( !function_exists( '__ei' ) ) {
		function __ei( $internationalisation_string ) {
			return __($internationalisation_string, 'eshop_csv_imp' );
		}
	}
	
	if ( !function_exists( 'pr' ) ) {
		function pr( $object ) {
			print '<pre>';
			print_r( $object );
			print '</pre>';
		}
	}

?>
