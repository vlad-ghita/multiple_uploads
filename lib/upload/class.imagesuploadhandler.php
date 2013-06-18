<?php



	/**
	 * This class is a mapper for an `Images` Section with `Title (Input)` and `Image (File upload)`.
	 */
	Final Class ImagesUploadHandler extends SectionUploadHandler
	{

		protected function getSource(){
			return 'images'; // section handle
		}

		protected function getField(){
			return 'image'; // field handle
		}

		protected function getData(array &$fields){
			// if new entry, set these fields
			if( $this->entry->get( 'id' ) === null ){
				$info  = pathinfo( $this->file_data['name'] );
				$title = basename( $this->file_data['name'], '.'.$info['extension'] );

				// Field: Text box
				$fields['title'] = $title;

				// Field: Multilingual Field
//				foreach(FLang::getLangs() as $lc){
//					$fields['title'][$lc] = $title;
//				}

				// Field: Checkbox
//				$fields['public'] = 'yes';
			}

			// Field: Image Cropper
//			$fields["crop-2-1"] = array(
//				'cropped' => 'no',
//				'x1' => '',
//				'x2' => '',
//				'y1' => '',
//				'y2' => '',
//				'width' => '',
//				'height' => '',
//				'ratio' => '',
//				'preview_url' => '',
//				'preview_scale' => '100'
//			);
		}

	}
