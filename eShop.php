<?php

	class eShop {

		var $pwsd = false;
		var $post_fields = array( );
		var $eshop_options = array( );

		function eShop( ) {
			$this->post_fields = array( 'ID', 'post_date', 'post_title', 'post_content', 'post_excerpt', 'post_parent', 'post_name', 'post_type', 'ping_status', 'comment_status', 'menu_order' );
			$this->eshop_options = get_option( 'eshop_plugin_settings' );
		}

		function export_products( ) {
			global $wpdb;
			$posts_table = $wpdb->prefix . 'posts';
			$postmeta_table = $wpdb->prefix . 'postmeta';
			$post_fields = implode( ", ", $this->post_fields );
			$sql = $wpdb->prepare( "SELECT DISTINCT $post_fields, m.meta_value FROM 
							$posts_table, $postmeta_table as m WHERE post_status in ('publish','future') AND ID = m.post_id AND m.meta_key = '_eshop_product' ORDER BY post_modified DESC", 'm.post_id' );

			$posts = $wpdb->get_results( $sql );

			if ( isset( $posts[0] ) ) {
      	$sql = "SELECT DISTINCT meta_key FROM $postmeta_table WHERE meta_key NOT LIKE '\_%'";
				$custom_fields = $wpdb->get_col( $sql );
if ( $this->pwsd ) print_r( $custom_fields );
				
				$this->format_product_data( $posts );

				$post1 = get_object_vars($posts[0]);

				$eshop1 = unserialize( $post1['meta_value'] );
				$eshop1 = $this->index_eshop_array( $eshop1 );
				$eshop1 = $this->array_flatten( $eshop1, true, 'eshop_' );
				array_push( $eshop1, 'eshop_stock_avail' );

				unset( $post1['meta_value'] );

		  	$product_array[] = array_merge( array_keys( $post1 ), $eshop1 );

				$meta_array = array( );

				foreach ( $custom_fields as $cf ) {
					$sql = $wpdb->prepare( "SELECT post_id, meta_value FROM $postmeta_table WHERE meta_key = '%s'", $cf );
					$results = $wpdb->get_results( $sql, 'OBJECT_K' );
					$meta_array[$cf] = $results;
				}

				foreach ( $posts as $p ) {
					$p = get_object_vars( $p );
					$id = $p['ID'];

					$stock_avail = get_post_meta( $id, '_eshop_stock', TRUE );
					$stock_avail = ( $stock_avail == 1 ) ? 'yes' : 'no';

					$cfs = array( );
					foreach ( $custom_fields as $cf ) {
						$cfs[] = $meta_array[$cf][$id]->meta_value;
					}

					$prod = $this->array_flatten( unserialize( $p['meta_value'] ), false );

					unset ($p['meta_value']);
					$product_array[] = array_merge( array_values( $p ), $prod, array( $stock_avail ) );
				}
			}
      return $product_array;
		}

		function format_product_data( &$posts ) {
			$template	= $this->get_eshop_product_template( );
			foreach( $posts as &$p ) {
				$esprod = unserialize( $p->meta_value );
				$tt = $template;
				foreach( $esprod as $k => $v ) {
					if ( $k != 'products' ) {
						$tt[$k] = $v;
					} else {
						foreach( $tt['products'] as $n => &$array ) {
							if ( isset( $esprod['products'][$n] ) ) {
								foreach( $esprod['products'][$n] as $key => $val ) {
									if ( isset( $tt['products'][$n][$key] ) ) {
										$tt['products'][$n][$key] = $val;
									}
								}
							}
						}
					}
					
				}
				if ( is_array( $tt['optset'] ) ) {
					$tt['optset'] = implode( ',', $tt['optset'] );
				}

				$p->meta_value = serialize( $tt );
			}
		}

		function get_eshop_product_template( ) {
			$plugins = get_option( '_site_transient_update_plugins' );
			$eshop_version = $plugins->checked['eshop/eshop.php'];

			switch ($eshop_version) {
				case '5.8.1':
				case '5.8.2':
				case '5.9.1':
				case '5.9.2':
				case '5.9.3':
				case '6.0.0':
				case '6.0.1':
				case '6.0.2':
					$template = array(
						'sku' => '',
						'description' => '',
						'products' => array( 1 => array( 'option' => '', 'price' => '', 'weight' => '', 'download' => '', 'stkqty' => '' ) ),
						'shiprate' => '',
						'featured' => '',
						'sale' => '',
						'cart_radio' => '',
						'optset' => ''
					);
					break;
				case '6.2.0':
				default:
					$template = array(
						'sku' => '',
						'description' => '',
						'products' => array( 1 => array( 'option' => '', 'price' => '', 'tax' => '', 'saleprice' => '', 'weight' => '', 'download' => '', 'stkqty' => '' ) ),
						'shiprate' => '',
						'featured' => '',
						'sale' => '',
						'cart_radio' => '',
						'optset' => ''
					);
			}

			for( $c = 2; $c <= $this->eshop_options['options_num']; $c++ ) {
				$new_array = array( );
				foreach( $template['products'][1] as $k => $v ) {
					$new_array[$k] = $v;
				}
				$template['products'][$c] = $new_array;
			}
			return $template;
		}

		function array_flatten( $data, $get_key = true, $prefix = "" ) {
      $output = array( );
			foreach ( $data as $key => $val ) {
				if ( is_array( $val ) ) {
					$output = array_merge( $output, $this->array_flatten( $val, $get_key, $prefix ) );
				} else {
					$output[] = $get_key? $prefix . $key : $val;
				}
			}
			return $output;
		}

		function index_eshop_array( $eshop_array ) {

			$products = $eshop_array['products'];

			foreach( $products as $n => $array ) {
				foreach( $array as $k => $v ) {
					$index = $k . $n;
					$new_products[$n][$index] = $v;
				}
			}
			$eshop_array['products'] = $new_products;

			return $eshop_array;
		}
	}
?>
