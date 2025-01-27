<?php
/*
* Define class WooZoneLite_ActionAdminAjax
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('WooZoneLite_ActionAdminAjax') != true) {
	class WooZoneLite_ActionAdminAjax
	{
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*/
		public $the_plugin = null;

		static protected $_instance;


		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct( $parent )
		{
			$this->the_plugin = $parent;

			// ajax requests
			add_action('wp_ajax_WooZoneLite_CleanLogTables', array( $this, 'db_clean_log_tables' ));
			add_action('wp_ajax_WooZoneLite_AttributesCleanDuplicate', array( $this, 'attributes_clean_duplicate' ));
			add_action('wp_ajax_WooZoneLite_CategorySlugCleanDuplicate', array( $this, 'category_slug_clean_duplicate' ));
			add_action('wp_ajax_WooZoneLite_clean_orphaned_amz_meta', array( $this, 'clean_orphaned_amz_meta' ));
			add_action('wp_ajax_WooZoneLite_delete_zeropriced_products', array( $this, 'delete_zeropriced_products' ));
			add_action('wp_ajax_WooZoneLite_clean_orphaned_prod_assets', array( $this, 'clean_orphaned_prod_assets' ));
			add_action('wp_ajax_WooZoneLite_clean_orphaned_prod_assets_wp', array( $this, 'clean_orphaned_prod_assets_wp' ));
			add_action('wp_ajax_WooZoneLite_fix_product_attributes', array( $this, 'fix_product_attributes' ));
			add_action('wp_ajax_WooZoneLite_fix_node_childrens', array( $this, 'fix_node_childrens' ));
			add_action('wp_ajax_WooZoneLite_fix_issues', array( $this, 'fix_issues' ));
			
			// cronjobs panel
			add_action('wp_ajax_WooZoneLite_cronjobs', array( $this, 'cronjobs_actions' ));
			
			// report
			add_action('wp_ajax_WooZoneLite_report_settings', array( $this, 'report_actions' ));
			
			if ( $this->the_plugin->is_admin ) {
				// Insane Mode - cache delete!
				add_action('wp_ajax_WooZoneLite_import_cache', array( $this, 'import_cache' ));
				
				// Images Cache - remote amazon images
				add_action('wp_ajax_WooZoneLite_images_cache', array( $this, 'images_cache' ));
			}

			// wizard
			add_action('wp_ajax_WooZoneLiteWizard', array( $this, 'wizard_ajax_request' ), 10, 2);

			// amazon multiple keys
			add_action('wp_ajax_WooZoneLite_AmzMultiKeysAjax', array( $this, 'amzmultikeys_actions' ));

			// woocustom ajax actions
			//add_action('wp_ajax_WooZoneLite_woocustom', array( $this, 'woocustom_ajax_actions') );
		}
		
		/**
		* Singleton pattern
		*
		* @return Singleton instance
		*/
		static public function getInstance()
		{
			if (!self::$_instance) {
				self::$_instance = new self;
			}
			
			return self::$_instance;
		}
		
		/**
		 * Clean Log Tables
		 *
		 */
		public function db_clean_log_tables( $retType='die' ) {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'db_clean_log_tables' ) die(json_encode($ret));
			
			if( isset( $_REQUEST['tables'] ) && $_REQUEST['tables'] != '' && isset($_REQUEST['clean_option']) ) {
				return $this->the_plugin->get_ws_object( 'generic' )->log_tables_clean($_REQUEST['tables'], $_REQUEST['clean_option']);
			}
		}
		
		
		/**
		 * Clean Duplicate Attributes
		 *
		 */
		public function attributes_clean_duplicate( $retType='die' ) {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'attr_clean_duplicate' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->attrclean_clean_all();
		}
		
		/**
		 * Clean Duplicate Category Slug
		 *
		 */
		public function category_slug_clean_duplicate( $retType='die' ) {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'category_slug_clean_duplicate' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->category_slug_clean_all();
		}
		
		/**
		 * Clean Orphaned Amz Meta
		 *
		 */
		public function clean_orphaned_amz_meta( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'clean_orphaned_amz_meta' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->clean_orphaned_amz_meta_all();
		}

		/**
		 * Clean Orphaned Amz Meta
		 *
		 */
		public function delete_zeropriced_products( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'delete_zeropriced_products' ) die(json_encode($ret));
			
			return $this->the_plugin->get_ws_object( 'generic' )->delete_zeropriced_products_all();
		}
		
		/**
		 * Clean Orphaned Amazon Products Assets
		 *
		 */
		public function clean_orphaned_prod_assets( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'msg_html'          => ''
			);

			if ($action != 'clean_orphaned_prod_assets' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->clean_orphaned_prod_assets_all();
		}
		
		/**
		 * Clean Orphaned Amazon Products Assets WP
		 *
		 */
		public function clean_orphaned_prod_assets_wp( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'msg_html'          => ''
			);

			if ($action != 'clean_orphaned_prod_assets_wp' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->clean_orphaned_prod_assets_all_wp();
		}
		
		/**
		 * Clean Orphaned Amazon Products Assets
		 *
		 */
		public function fix_product_attributes( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'msg_html'          => ''
			);

			if ($action != 'fix_product_attributes' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->fix_product_attributes_all();
		}
		
		/**
		 * Clean Orphaned Amazon Products Assets
		 *
		 */
		public function fix_node_childrens( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'msg_html'          => ''
			);

			if ($action != 'fix_node_childrens' ) die(json_encode($ret));

			return $this->the_plugin->get_ws_object( 'generic' )->fix_node_childrens();
		}

		/**
		 * Cronjobs Panel - ajax actions
		 *
		 */
		public function cronjobs_actions( $retType='die' ) {    
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/cronjobs/cronjobs.panel.php' );
			$cronObj = new WooZoneLiteCronjobsPanel($this->the_plugin, array());

			$cronObj->ajax_request();
		}
		
		/**
		 * Report Panel - ajax actions
		 *
		 */
		public function report_actions( $retType='die' ) {    
			// Initialize the WooZoneLiteReport class
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/report/init.php' );
			$reportObj = new WooZoneLiteReport();

			$reportObj->ajax_request_settings();
		}
		
		/**
		 * Insane Mode - cache delete!
		 */
		public function import_cache() {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'start_date'        => date('Y-m-d H:i:s'),
				'start_time'        => 0,
				'end_time'          => 0,
				'duration'          => 0,
				'msg'               => '',
				'msg_html'          => ''
			);

			if ( in_array($action, array('getStatus', 'cache_delete')) ) {
			  
				require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/insane_import/init.php' );
				$im = WooZoneLiteInsaneImport::getInstance();
				
				$providers = array_keys( array('amazon' => 'amz') );
				$cacheSettings = $im->getCacheSettings();

			} else {
				$ret['msg_html'] = 'unknown request';
				die(json_encode($ret));
			}

			if ( in_array($action, array('getStatus', 'cache_delete')) ) {

				//$notifyStatus = $this->the_plugin->get_theoption('psp_Minify');
				//if ( $notifyStatus === false || !isset($notifyStatus["cache"]) ) ;
				//else {
					//$ret['msg_html'] = $notifyStatus["cache"]["msg_html"];
					
					$ret = array_merge($ret, array(
						'status'    => 'valid',
						'msg'       => 'success',
					));
					$ret['start_time'] = $this->the_plugin->microtime_float();
  
					$cache_count = $this->cache_count(array(
						'action'			=> $action,
						'providers'			=> $providers,
						'cacheSettings'		=> $cacheSettings,
						'start_date'		=> $ret['start_date'],
					));
					$ret['msg_html'] = implode(PHP_EOL, $cache_count['html']);
				//}
				
				$ret['end_time'] = $this->the_plugin->microtime_float();
				$ret['duration'] = number_format( ($ret['end_time'] - $ret['start_time']), 2 );
				
				die(json_encode($ret));
			}
			
			//$notifyStatus = $this->the_plugin->get_theoption('psp_Minify');

			//$notifyStatus["cache"] = $ret;
			//$this->the_plugin->save_theoption('psp_Minify', $notifyStatus);
			die(json_encode($ret));
		}

		private function cache_count( $pms=array() ) {
			extract($pms);

			$ret = array(
				'html'	=> array()
			);
			$ln = $this->the_plugin->localizationName;
			{
				{
					$cache_types = array('search', 'prods');

					$html = array(); $found = 0;
					$html[] = '<table class="wp-list-table widefat striped">';
					$html[] = 	'<thead>';
					$html[] = 		'<tr><th>' . __('Provider', $ln) . '</th><th colspan=2 style="text-align: center;">' . sprintf( __('Number of files in cache | date: %s.', $ln), $start_date ) . '</th></tr>';
					$html[] = 		'<tr><th></th><th>' . __('Search Products', $ln) . '</th><th>' . __('Product details', $ln) . '</th></tr>';
					$html[] = 	'</thead>';
					$html[] = 	'<tfoot></tfoot>';
					$html[] = 	'<tbody>';
					foreach ($providers as $provider) {

						$html[] = '<tr><td>' . strtoupper($provider) . '</td>';
						foreach ($cache_types as $cache_type) {

							$cache_folder = $cache_type . '_folder';
							$cache_folder = $cacheSettings["$cache_folder"];
							$cache_folder .= $provider . '/';
							
							if ( 'cache_delete' == $action ) {
								$files = glob( $cache_folder . '*.*' );
								//var_dump('<pre>', $cache_folder . '*.*', $files , '</pre>');
								if ( is_array( $files ) ) {
									array_map( "unlink", $files );
								}
							}
							
							$nb = (int) $this->the_plugin->u->get_folder_files_recursive( $cache_folder );

							$html[] = '<td>' . '<span class="success">' . sprintf( __('%s files', $ln), $nb ) . '</span>' . '</td>';

							$found++;
						}
						$html[] = '</tr>';
					}
					//echo __FILE__ . ":". __LINE__;die . PHP_EOL;
					$html[] = 	'</tbody>';
					$html[] = '</table>';
					
					if ( !$found ) $html = array();
					
					$html[] = '<span>' . __('Expiration (value in minutes): ', $ln);
					foreach ($cache_types as $cache_type) {
						
						$cache_lifetime = $cache_type . '_lifetime';
						$cache_lifetime = $cacheSettings["$cache_lifetime"];
							
						$html[] = 'search' == $cache_type ? __('Search Products: ', $ln) : __('Product details: ', $ln);
						$html[] = $cache_lifetime . '&nbsp;';
					}
					$html[] = '</span>';
				}
			}
			$ret['html'] = $html;
			return $ret;
		}
	
		/**
		 * Fix issues
		 *
		 */
		public function fix_issues( $retType='die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'msg_html'          => ''
			);
   
			if (!in_array($action, array(
				'fix_issue_request_amazon',
				'sync_restore_status',
				'reset_products_stats',
				'options_prefix_change',
				'unblock_cron',
				'reset_sync_stats'
			))) die(json_encode($ret));

			$config = $this->the_plugin->settings();

			$theHelper = $this->the_plugin->get_ws_object( 'generic' );
			//$theHelper = $this->the_plugin->get_ws_object_new( $this->the_plugin->cur_provider, 'new_helper', array(
			//	'the_plugin' => $this->the_plugin,
			//));
			//:: disabled on 2018-feb
			//require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'aa-framework/amz.helper.class.php' );
			//if ( class_exists('WooZoneLiteAmazonHelper') ) {
			//	//$theHelper = WooZoneLiteAmazonHelper::getInstance( $this->the_plugin );
			//	$theHelper = new WooZoneLiteAmazonHelper( $this->the_plugin );
			//}
			//:: end disabled on 2018-feb
			$what = 'main_aff_id';
			
			if ( is_object($theHelper) ) {
				return $theHelper->fix_issues();
			}
			
			$ret = array(
				'status'		=> 'valid',
				'msg_html'		=> 'Invalid amazon helper object!', 
			);
			if ( $retType == 'die' ) die(json_encode($ret));
			else return $ret;
		}

		/**
		 * Insane Mode - cache delete!
		 */
		public function images_cache() {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'            => 'invalid',
				'start_date'        => date('Y-m-d H:i:s'),
				'start_time'        => 0,
				'end_time'          => 0,
				'duration'          => 0,
				'msg'               => '',
				'msg_html'          => ''
			);

			if ( in_array($action, array('getStatus', 'cache_delete')) ) {

				$this->the_plugin->cacheit->cacheitInit();

			} else {
				$ret['msg_html'] = 'unknown request';
				die(json_encode($ret));
			}

			if ( in_array($action, array('getStatus', 'cache_delete')) ) {

				//$notifyStatus = $this->the_plugin->get_theoption('psp_Minify');
				//if ( $notifyStatus === false || !isset($notifyStatus["cache"]) ) ;
				//else {
					//$ret['msg_html'] = $notifyStatus["cache"]["msg_html"];
					
					$ret = array_merge($ret, array(
						'status'    => 'valid',
						'msg'       => 'success',
					));
					$ret['start_time'] = $this->the_plugin->microtime_float();
  
					$cache_count = $this->images_cache_cache_count(array(
						'action'			=> $action,
						'cacheit'			=> $this->the_plugin->cacheit->cacheit,
						'start_date'		=> $ret['start_date'],
					));
					$ret['msg_html'] = implode(PHP_EOL, $cache_count['html']);
				//}
				
				$ret['end_time'] = $this->the_plugin->microtime_float();
				$ret['duration'] = number_format( ($ret['end_time'] - $ret['start_time']), 2 );
				
				die(json_encode($ret));
			}
			
			die(json_encode($ret));
		}

		private function images_cache_cache_count( $pms=array() ) {
			extract($pms);

			$ret = array(
				'html'	=> array()
			);
			$ln = $this->the_plugin->localizationName;
			{
				{
					$html = array();
					$html[] = '<table class="wp-list-table widefat striped">';
					$html[] = 	'<thead>';
					$html[] = 		'<tr>';
					$html[] = 			'<th style="text-align: left;">' . __('Cached', $ln) . '</th>';
					$html[] = 			'<th style="text-align: left;">Number of rows in cache</th>';
					$html[] = 			'<th style="text-align: left;">Extra Info</th>';
					$html[] = 		'</tr>';
					$html[] = 	'</thead>';
					$html[] = 	'<tfoot></tfoot>';
					$html[] = 	'<tbody>';
					foreach ($cacheit as $key => $val) { // foreach 1

						$cache_types = $val->return_cache();

						foreach ($cache_types as $cache_type => $cache_content) { // foreach 2
							if ( 'cache_delete' == $action ) {
								$cacheit["$key"]->empty_cache();
							}
						} // end foreach 2

						$cache_types = $val->return_cache();

						$html[] = '<tr>';
						$html[] = 		'<td>' . $key . '</td>';

						$extrainfo = array();

						$html2 = array();
						foreach ($cache_types as $cache_type => $cache_content) { // foreach 2

							$nbrows = count(array_keys($cache_content));
							$html2[] = '<div><div style="display: inline-block; width: 30%;">' . $cache_type . '</div><div style="display: inline-block;">' . $nbrows . ' rows</div></div>';

							switch ($cache_type) {
								case 'session':
									break;

								case 'file':
									$cache_folder_full = $cacheit["$key"]->cache_folder['path'] . $cacheit["$key"]->cache_folder['filename'];
									$cache_folder_rel = $cacheit["$key"]->cache_folder['relpath'] . $cacheit["$key"]->cache_folder['filename'];
									$filesize = $this->the_plugin->u->filesize($cache_folder_full);
									$extrainfo[] = $cache_folder_rel . ' ' . $filesize;
									break;

								case 'wpoption':

									break;
							}

						} // end foreach 2
						
						$html[] = 		'<td>' . implode(PHP_EOL, $html2) . '</td>';
						$html[] = 		'<td>' . implode(PHP_EOL, $extrainfo) . '</td>';
						$html[] = '</tr>';

					} // end foreach 1

					$html[] = 	'</tbody>';
					$html[] = '</table>';
				}
			}
			$ret['html'] = $html;
			return $ret;
		}


		/**
		 * Wizard Ajax Requests
		 *
		 */
		public function wizard_ajax_request( $retType='die', $pms=array() )
		{
			$requestData = array(
				'action'             => isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
			);
			extract($requestData);

			$ret = array(
				'status'        => 'invalid',
				'msg'           => '',
			);

			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/wizard/init.php' );
			//$WooZoneLiteWizard = new WooZoneLiteWizard();
			$WooZoneLiteWizard = WooZoneLiteWizard::getInstance( false );

			if ($action == 'check_amz_keys' ) {

				//$this->the_plugin->main_aff_id()
				$WooZoneLiteWizard->setupAmazonHelper( array(
					'AccessKeyID' 			=> isset($_REQUEST['AccessKeyID']) ? (string) $_REQUEST['AccessKeyID'] : '',
					'SecretAccessKey' 		=> isset($_REQUEST['SecretAccessKey']) ? (string) $_REQUEST['SecretAccessKey'] : '',
					'country' 				=> isset($_REQUEST['country']) ? (string) $_REQUEST['country'] : '',
					'main_aff_id' 			=> isset($_REQUEST['main_aff_id']) ? (string) $_REQUEST['main_aff_id'] : '',
					'associateTag' 			=> isset($_REQUEST['associateTag']) ? (string) $_REQUEST['associateTag'] : '',
				), array(
					'provider' 	=> 'amazon',
				));

				$WooZoneLiteWizard->get_ws_object( $this->the_plugin->cur_provider )->check_amazon();
			}
			else if ($action == 'load_step' ) {
				$req = array(
					'step' 					=> isset($_REQUEST['step']) ? $_REQUEST['step'] : '',
				);
				$step_html = $WooZoneLiteWizard->load_step( $req );

				$ret = array_replace_recursive($ret, array(
					'status'		=> 'valid',
					'html'			=> $step_html,
				));
			}

			if ( $retType == 'return' ) { return $ret; }
			else { die( json_encode( $ret ) ); }
		}

		/**
		 * Amazon Multiple Keys - ajax actions
		 *
		 */
		public function amzmultikeys_actions( $retType='die' ) {
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/amazon/amzmultikeys/init.php' );
			$cfg = $this->the_plugin->cfg;
			$module = $cfg['modules']['amazon'];
			$obj = new WooZoneLiteMultipleAmazonKeys($cfg, $module);

			$obj->ajax_request();
		}

		/**
		 * WooCustom - ajax actions
		 *
		 */
		/*public function woocustom_ajax_actions( $retType='die' ) {
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/woocustom/init.php' );
			$cfg = $this->the_plugin->cfg;
			$module = $cfg['modules']['woocustom'];
			$obj = new WooZoneLiteWooCustom();

			$obj->ajax_requests();
		}*/
	}
}

// Initialize the WooZoneLite_ActionAdminAjax class
//$WooZoneLite_ActionAdminAjax = new WooZoneLite_ActionAdminAjax();
