<?php



	/**
	 * This class is a mapper for my Files Section.
	 */
	Class FilesUploadHandler extends SectionUploadHandler
	{

		protected function getSource(){
			return 'files';
		}

		protected function getField(){
			return 'file';
		}

		protected function getData(array &$fields){
			// Field: Multilingual Field
			foreach( FLang::getLangs() as $lc ){
				$fields['title'][$lc] = $this->file_data['name'];
			}

			// Field: Multilingual file upload
			unset($fields[$this->getField()]);
			$fields[$this->getField()][FLang::getMainLang()] = $this->file_data;

			// Field: Checkbox
			$fields['public'] = 'yes';

			// Field: Date / Time
			$fields['publish-date']['start'][0] = date( 'c' );
		}

	}
