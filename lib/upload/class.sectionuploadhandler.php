<?php



	require_once ('upload.class.php');



	/**
	 * Upload Handler for a Section
	 */
	Abstract Class SectionUploadHandler extends UploadHandler
	{

		/**
		 * Entry that will be created.
		 *
		 * @var Entry
		 */
		protected $entry = null;

		/**
		 * Errors appearing during the save process.
		 *
		 * @var array
		 */
		protected $errors = array();

		/**
		 * Data from uploaded file, matching @see fieldUpload
		 *
		 * @var array
		 */
		protected $file_data = array();

		/**
		 * ID of the section
		 *
		 * @var int
		 */
		protected $section_id = null;



		/**
		 * This must return the handle of the section.
		 *
		 * @return string - section handle
		 */
		abstract protected function getSource();

		/**
		 * This must return the handle of the upload field.
		 *
		 * @return string
		 */
		abstract protected function getField();

		/**
		 * This should return an array suitable for Entry::setDataFromPost()
		 *
		 * @param array $fields
		 *
		 * @return array
		 */
		abstract protected function getData(array &$fields);



		public function post(){
			$this->bootSymphony();

			require_once (TOOLKIT.'/class.sectionmanager.php');
			$this->section_id = SectionManager::fetchIDFromHandle( $this->getSource() );

			$this->processEntry();
			$this->returnInfo();
		}

		private function bootSymphony(){
			define('DOCROOT', rtrim( $this->dirname( __FILE__ ), '\\/' ));
			define('DOMAIN', rtrim( rtrim( $_SERVER['HTTP_HOST'], '\\/' ).$this->dirname( $_SERVER['PHP_SELF'] ), '\\/' ));

			require(DOCROOT.'/symphony/lib/boot/bundle.php');
			require_once(CORE."/class.frontend.php");

			Frontend::instance();

			$this->initFLang();
		}

		private function dirname($path, $cnt = 4){
			$path = dirname( $path );

			if( $cnt > 0 ){
				return $this->dirname( $path, $cnt - 1 );
			}

			return $path;
		}

		private function initFLang(){
			$ext_fl = ExtensionManager::fetchStatus(array('handle' => 'frontend_localisation'));
			if( $ext_fl[0] !== EXTENSION_ENABLED ){
				return;
			}

			require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLang.php');

			// initialize Language codes
			$langs = Symphony::Configuration()->get( 'langs', 'frontend_localisation' );
			FLang::setLangs( $langs );

			// initialize Main language
			$main_lang = Symphony::Configuration()->get( 'main_lang', 'frontend_localisation' );
			if( !FLang::setMainLang( $main_lang ) ){
				$langs = FLang::getLangs();

				if( isset($langs[0]) && !FLang::setLangCode( $langs[0] ) ){
					// do something useful here if no lang is set ...
				}
			}

			// read current language
			$language = General::sanitize( (string) $_REQUEST['fl-language'] );
			$region   = General::sanitize( (string) $_REQUEST['fl-region'] );

			// set language code
			if( false === FLang::setLangCode( $language, $region ) ){

				// language code is not supported, fallback to main lang
				if( false === FLang::setLangCode( FLang::getMainLang() ) ){
					// do something useful here if no lang is set ...
				}
			}

			// set backend language from frontend language
			Lang::set( FLang::getLangCode() );
		}

		protected function processEntry(){
			$entry_id = is_numeric( $_POST['entry-id'] ) ? $_POST['entry-id'] : null;

			require_once (TOOLKIT.'/class.entrymanager.php');

			if( $entry_id !== null ){
				$entry = EntryManager::fetch( $entry_id );
				$entry = $entry[0];

				if( !$entry instanceof Entry ){
					return false;
				}

				$this->entry = $entry;
			}

			else{
				$this->entry = EntryManager::create();
				$this->entry->set( 'section_id', $this->section_id );
			}

			// File data
			foreach($_FILES['files'] as $key => $elem){
				$this->file_data[$key] = $elem[0];
			}

			$fields = array();

			// The upload field
			$fields[$this->getField()] = $this->file_data;

			// Other fields
			$this->getData( $fields );

			if( __ENTRY_FIELD_ERROR__ == $this->entry->checkPostData( $fields, $this->errors, $entry_id !== null ) ){
				return false;
			}

			if( __ENTRY_OK__ != $this->entry->setDataFromPost( $fields, $this->errors, false, $entry_id !== null ) ){
				return false;
			}


			if( !$this->entry->commit() ) return false;

			/**
			 * After entry data was committed to database.
			 *
			 * @delegate Multiple_Uploads_EntryPostCommit
			 *
			 * @param string $context
			 * '*'
			 * @param int    $section_id
			 * @param Entry  $entry
			 */
			Symphony::ExtensionManager()->notifyMembers( 'Multiple_Uploads_EntryPostCommit', '*', array(
				'section_id' => $this->section_id,
				'entry'      => $this->entry
			) );
		}

		private function returnInfo(){
			$upload_id   = FieldManager::fetchFieldIDFromElementName( $this->getField(), $this->section_id );
			$upload_data = $this->entry->getData( $upload_id );

			if( empty($this->errors) ){
				$info[] = array(
					'name'          => $this->file_data['name'],
					'size'          => $this->file_data['size'],
					'url'           => SYMPHONY_URL."/publish/{$this->getSource()}/edit/{$this->entry->get('id')}/",
					'thumbnail_url' => URL.'/image/4/100/100/0'.$upload_data['file'],
					'delete_url'    => URL,
					'delete_type'   => 'DELETE',
					'entry_id'      => $this->entry->get( 'id' ),
					'file'          => $upload_data['file']
				);
			}
			else{
				$info[] = array(
					'name'  => $this->file_data['name'],
					'error' => implode( ' || ', $this->errors )
				);
			}


			header( 'Vary: Accept' );
			$json     = json_encode( $info );
			$redirect = isset($_REQUEST['redirect']) ? stripslashes( $_REQUEST['redirect'] ) : null;

			if( $redirect ){
				header( 'Location: '.sprintf( $redirect, rawurlencode( $json ) ) );
				return;
			}

			if( isset($_SERVER['HTTP_ACCEPT']) && (strpos( $_SERVER['HTTP_ACCEPT'], 'application/json' ) !== false) ){
				header( 'Content-type: application/json' );
			}
			else{
				header( 'Content-type: text/plain' );
			}

			echo $json;
		}

	}
