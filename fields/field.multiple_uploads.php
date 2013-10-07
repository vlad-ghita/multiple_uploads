<?php

	if( !defined( '__IN_SYMPHONY__' ) ) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');



	class FieldMultiple_Uploads extends Field
	{

		/**
		 * Compatible field types. Only Entry URL atm.
		 *
		 * @var array
		 */
		public $field_types;



		/*------------------------------------------------------------------------------------------------*/
		/*  Definition  */
		/*------------------------------------------------------------------------------------------------*/

		public function __construct(){
			parent::__construct();

			$this->_name = 'Multiple Uploads';

			$this->field_types = array('selectbox_link_plus');
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Settings  */
		/*------------------------------------------------------------------------------------------------*/

		public function displaySettingsPanel(XMLElement &$wrapper, $errors = null){
			parent::displaySettingsPanel( $wrapper, $errors );

			$callback = Administration::instance()->getPageCallback();

			if( $callback['context'][0] != 'edit' ) return;

			$section = SectionManager::fetch( $callback['context'][1] );
			if( is_array( $section ) ) $section = current( $section );
			if( !$section instanceof Section ) return;

			$s_fields = $section->fetchFields();
			if( !is_array( $s_fields ) ) return;

			$fields = array();
			foreach($s_fields as $f){
				/** @var $f Field */
				if( in_array( $f->get( 'type' ), $this->field_types ) ){
					$fields[] = array(
						$f->get( 'id' ),
						$f->get( 'id' ) == $this->get( 'related_field_id' ),
						$f->get( 'label' )
					);
				}
			}

			if( empty($fields) ) return;

			$label = Widget::Label( __( 'Related View' ) );
			$label->appendChild(
				Widget::Select( 'fields['.$this->get( 'sortorder' ).'][related_field_id][]', $fields )
			);

			$wrapper->appendChild( $label );
		}

		public function commit(){
			if( !parent::commit() ) return false;

			$id     = $this->get( 'id' );
			$handle = $this->handle();

			if( $id === false ) return false;

			$fields['field_id'] = $id;

			$related_field_id = $this->get( 'related_field_id' );
			if( !is_array( $related_field_id ) ) $related_field_id = array($related_field_id);
			$fields['related_field_id'] = empty($related_field_id) ? '' : implode( ',', $related_field_id );

			Symphony::Database()->query( "DELETE FROM `tbl_fields_{$handle}` WHERE `field_id` = '{$id}' LIMIT 1" );

			return Symphony::Database()->insert( $fields, "tbl_fields_{$handle}" );
		}



		/*------------------------------------------------------------------------------------------------*/
		/*  Publish  */
		/*------------------------------------------------------------------------------------------------*/

		public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $prefix = null, $postfix = null){
			$callback = Administration::instance()->getPageCallback();

			$sblp_id = $this->get( 'related_field_id' );
			$sblp_f  = FieldManager::fetch( $sblp_id );
			if( is_array( $sblp_f ) ) $sblp_f = current( $sblp_f );

			$rel_id    = $sblp_f->get( 'related_field_id' );
			$rel_id    = $rel_id[0];
			$rel_field = FieldManager::fetch( $rel_id );
			if( is_array( $rel_field ) ) $rel_field = current( $rel_field );

			$rel_section = SectionManager::fetch( $rel_field->get( 'parent_section' ) );

			$script = new XMLElement('script', null, array('type' => 'text/javascript'));
			$script->setValue( sprintf( "
					Symphony.Multiple_Uploads = Symphony.Multiple_Uploads || {};
					Symphony.Multiple_Uploads['field-%s'] = {
						'class_name': '%s',
						'view': 'sblp-view-%s',
						'entry_id': '%s'
					};",
				$this->get( 'id' ),
				Extension_Multiple_Uploads::className( $rel_section->get( 'handle' ) ),
				$sblp_id,
				$callback['context']['entry_id']
			) );
			$script->setSelfClosingTag( false );
			Administration::instance()->Page->addElementToHead( $script );

			Extension_Multiple_Uploads::appendAssets( 'single' );

			$label = Widget::Label( $this->get( 'label' ) );
			$wrapper->appendChild( $label );

			// iframe
			$iframe = new XMLElement('iframe');
			$iframe->setAttribute( 'src', URL.'/extensions/multiple_uploads/lib/publish_single/?id=field-'.$this->get( 'id' ) );
			$iframe->setAttribute( 'id', 'multiple-uploads-iframe-field-'.$this->get( 'id' ) );

			$wrapper->appendChild( $iframe );
		}


		public function appendFieldSchema($f){

		}

	}
