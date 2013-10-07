<?php



	/**
	 * Base class for mapping Icons sections.
	 */
	Class IconsUploadHandler extends SectionUploadHandler
	{

		protected function getSource(){
			return 'icons';
		}

		protected function getField(){
			return 'image';
		}

		protected function getData(array &$fields){
			$info = pathinfo($this->file_data['name']);
			$title = basename($this->file_data['name'], '.'.$info['extension']);

			// Field: Multilingual Field
			foreach( FLang::getLangs() as $lc ){
				$fields['title'][$lc] = $title;
			}

			$fields['file'] = $title;
		}

	}
