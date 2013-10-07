<?php



	/**
	 * This class is a mapper for my Images Section.
	 */
	Class PaymentMethodsUploadHandler extends SectionUploadHandler
	{

		public function getSource(){
			return 'payment-methods';
		}

		protected function getField(){
			return 'image';
		}

		protected function getData(array &$fields){
			$info = pathinfo($this->file_data['name']);
			$title = basename($this->file_data['name'], '.'.$info['extension']);

			// Field: Multilingual Field
			require_once(EXTENSIONS.'/frontend_localisation/lib/class.FLang.php');
			foreach( FLang::getLangs() as $lc ){
				$fields['title'][$lc] = $title;
			}
		}

	}
