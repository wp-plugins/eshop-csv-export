<?php
/*
Plugin Name: eShop CSV Export
Plugin URI: http://csv-imp.paulswebsolutions.com/eshop-csv-export
Description: Export all your eShop products into a single CSV file.
Version: 1.1
Author: Paul's Web Solutions
Author URI: http://www.paulswebsolutions.com

    Copyright 2007-2011  Paul's Web Solutions (email: paul@paulswebsolutions.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// Load libraries
require_once( 'view.php' );
require_once( 'csv.php' );
require_once( 'eShop.php' );

// Initialise main class
if ( !class_exists( 'eshop_csv_exp' ) ) {

	class eshop_csv_exp {

		var $view;
		var $csv;
		var $eshop;
		var $backup_url;

		function eshop_csv_exp( ) { // Constructor
			$this->view = new view( );
			$this->csv = new CSV( );
			$this->eshop = new eShop( );
			$backup_url = '';
		}

		function eshop_csv_exp_admin_pages( ) {

			if ( $_POST['action'] == 'report' && $_FILES['uploadedfile']['name'] == '' ) {
				$error = 'Invalid file';
				$_POST['action'] = 'import';
			}
			$subdir = '/uploads';
			$filename = 'eshop-products-' . date('Ymd');

			// Make sure eshop plugin is installed and activated
			$active_plugins = get_option( 'active_plugins' );
			if ( !in_array( 'eshop/eshop.php', $active_plugins ) ) {
				$_POST['action'] = 'checkfailed';
			}

			switch ( $_POST['action'] ) {
				case 'checkfailed':
					$this->view->page( 'checkfailed', array( ) );
				default:
					$options = array( 'export_link' => $this->getExportLink( $subdir, $filename ) );
					$this->view->page( 'export', $options );
			}
		}

		function getExportLink( $subdir, $filename ) {
			$csv_data = $this->eshop->export_products( );
			// Intercept 'ID' field and change to 'id' to prevent an excel bug.  Must reverse when importing too.
			if ( $csv_data[0][0] == 'ID' ) { $csv_data[0][0] = 'id'; }

			if ( $this->csv->saveToFile( $csv_data, $filename, WP_CONTENT_DIR . $subdir ) ) {
				$url = WP_CONTENT_URL . "/plugins/eshop-csv-export/download.php?csvfile=$filename";
				update_post_meta( 1, '_eshop_csv_exp_backup', $url );
			} else {
				$url = FALSE;
			}

			return $url;
		}

	}
}

// Instantiate
if (class_exists("eshop_csv_exp")) {
	$eshop_csv_exp = new eshop_csv_exp();
}

// Initialize the admin panel
if (!function_exists("eshop_csv_exp_ap")) {
	function eshop_csv_exp_ap() {
		global $eshop_csv_exp;
		if (!isset($eshop_csv_exp)) {
			return;
		}
		if (function_exists( 'add_submenu_page' ) ) {
			add_submenu_page( 'tools.php', __( 'eShop CSV Export' ), __( 'eShop CSV Export' ), 'administrator', basename(__FILE__), array(&$eshop_csv_exp, 'eshop_csv_exp_admin_pages'));
		}
	}	
}

if ( !function_exists( "ecsvi_header" ) ) {
	function ecsvi_header( ) {
		$ecsvi_url = get_bloginfo('wpurl') . '/wp-content/plugins/eshop-csv-import/css/eshop_csv_exp.css';
		echo '<link type="text/css" rel="stylesheet" href="' . $ecsvi_url . '" />' . "\n";
	}
}

if ( !function_exists( 'csv_imp_stylesheet' ) ) {
  function csv_imp_stylesheet() {
      $StyleUrl = WP_PLUGIN_URL . '/eshop-csv-export/css/styles.css';
      $StyleFile = WP_PLUGIN_DIR . '/eshop-csv-export/css/styles.css';
      if ( file_exists($StyleFile) ) {
          wp_register_style('csv_imp_styles', $StyleUrl);
          wp_enqueue_style( 'csv_imp_styles');
      }
  }
}
//Actions and Filters	
if ( isset( $eshop_csv_exp ) ) {
	add_action( 'admin_menu', 'eshop_csv_exp_ap' );
	add_action( 'admin_head', 'ecsvi_header');

  $eqStyleUrl = WP_PLUGIN_URL . '/eqentia/css/eqentia_styles.css';
  $eqStyleFile = WP_PLUGIN_DIR . '/eqentia/css/eqentia_styles.css';
  
  wp_register_style('csv_imp_stylesheet', WP_PLUGIN_URL . '/eshop-csv-export/css/styles.css');
	add_action( 'admin_print_styles', 'csv_imp_stylesheet' );
}

?>
