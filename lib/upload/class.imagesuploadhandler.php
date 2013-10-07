<?php



	/**
	 * This class is a mapper for my Images Section.
	 */
	Class ImagesUploadHandler extends SectionUploadHandler
	{
		protected function getSource(){
			return 'images';
		}

		protected function getField(){
			return 'image';
		}

		protected function getData(array &$fields){
			// if new entry, set these fields
			if( $this->entry->get( 'id' ) === null ){
				$info = pathinfo( $this->file_data['name'] );
				$title = basename( $this->file_data['name'], '.'.$info['extension'] );

				// Field: Multilingual Field
				foreach(FLang::getLangs() as $lc){
					$fields['title'][$lc] = $title;
				}

				// Field: Checkbox
				$fields['public'] = 'yes';
			}

			// cropper fields
			$ratios = array('1-1', '2-1', '3-1', '3-2', '4-1', '4-3', '16-9');

			foreach( $ratios as $ratio ){
				$this->imageCropper( $fields, $ratio );
			}
		}

		private function imageCropper(&$fields, $ratio){
			$fields["crop-$ratio"] = array(
				'cropped' => 'no',
				'x1' => '',
				'x2' => '',
				'y1' => '',
				'y2' => '',
				'width' => '',
				'height' => '',
				'ratio' => '',
				'preview_url' => '',
				'preview_scale' => '100'
			);
		}

	}
