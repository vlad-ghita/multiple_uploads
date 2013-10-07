<?php

	Class Extension_Multiple_Uploads extends Extension
	{

		private $field_table = 'tbl_fields_multiple_uploads';

		private static $assets_loaded = false;



		/*------------------------------------------------------------------------------------------------*/
		/*  Installation  */
		/*------------------------------------------------------------------------------------------------*/

		public function install(){
			return Symphony::Database()->query( sprintf(
				"CREATE TABLE `%s` (
					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`field_id` INT(11) UNSIGNED NOT NULL,
					`related_field_id` VARCHAR(255) NULL,
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
				$this->field_table
			) );
		}

		public function uninstall(){
			try{
				Symphony::Database()->query( sprintf(
					"DROP TABLE `%s`",
					$this->field_table
				) );
			}
			catch( DatabaseException $dbe ){
				// table deosn't exist
			}
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Delegates  */
		/*------------------------------------------------------------------------------------------------*/

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'AdminPagePreGenerate',
					'callback' => 'dAdminPagePreGenerate'
				)
			);
		}

		public function dAdminPagePreGenerate($context){
			$callback = Administration::instance()->getPageCallback();

			if( $callback['context']['page'] === 'index' ){
				$path = EXTENSIONS."/multiple_uploads/lib/upload";

				$class_name = self::className( $callback['context']['section_handle'] );
				$class_file = "$path/class.$class_name.php";
				if( !file_exists( $class_file ) ) return;

				require_once "$path/class.sectionuploadhandler.php";
				require_once $class_file;

				if( !class_exists( $class_name ) ) return;


				$script = new XMLElement('script', null, array('type' => 'text/javascript'));
				$script->setValue( sprintf( "
					Symphony.Multiple_Uploads = {
						'class_name' : '%s'
					};",
					$class_name
				) );
				$script->setSelfClosingTag( false );
				Administration::instance()->Page->addElementToHead( $script );

				$this->appendAssets( 'index' );
				$this->button( $context );
				$this->iframe( $context );
			}
		}

		private function button($context){
			/** @var $cxt XMLElement */
			$cxt = $context['oPage']->Context;
			if( !$cxt instanceof XMLElement ) return;

			$actions = $cxt->getChildByName( 'ul', 0 );
			if( !$actions instanceof XMLElement ) return;

			// add button
			$li = new XMLelement('li');

			$li->appendChild( Widget::Anchor(
				__( 'Upload files' ),
				'javascript:void(0)',
				null,
				'button multiple-uploads',
				'multiple-uploads'
			) );

			$actions->appendChild( $li );
		}

		private function iframe($context){
			/** @var $contents XMLElement */
			$contents = $context['oPage']->Contents;
			if( !$contents instanceof XMLElement ) return;

			$iframe = new XMLElement('iframe');
			$iframe->setAttribute( 'src', URL.'/extensions/multiple_uploads/lib/publish_index/' );
			$iframe->setAttribute( 'style', 'display:none' );
			$iframe->setAttribute( 'id', 'multiple-uploads-iframe' );

			$contents->appendChild( $iframe );
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Utilities  */
		/*------------------------------------------------------------------------------------------------*/

		public static function className($handle){
			$class_name = explode( '-', $handle );
			$class_name = implode( '', $class_name );
			$class_name = strtolower($class_name);
			$class_name = "{$class_name}uploadhandler";

			return $class_name;
		}

		public static function appendAssets($where = 'single'){
			if(
				!self::$assets_loaded
				&& class_exists( 'Administration' )
				&& Administration::instance() instanceof Administration
				&& Administration::instance()->Page instanceof HTMLPage
			){
				$page = Administration::instance()->Page;

				switch( $where ){
					case 'index':
						$page->addStylesheetToHead( URL.'/extensions/multiple_uploads/assets/multiple_uploads.publish_index.css', "screen" );
						$page->addScriptToHead( URL.'/extensions/multiple_uploads/assets/multiple_uploads.publish_index.js', null, false );
						break;

					case 'single':
						$page->addStylesheetToHead( URL.'/extensions/multiple_uploads/assets/multiple_uploads.publish_single.css', "screen" );
						$page->addScriptToHead( URL.'/extensions/multiple_uploads/assets/multiple_uploads.publish_single.js', null, false );
						break;
				}

				self::$assets_loaded = true;
			}
		}

	}
