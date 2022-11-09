<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'RT_TaxMeta_Fields' ) ){

	class RT_TaxMeta_Fields extends RT_Postmeta_Fields {

		public function display_fields( $fields, $term_id, $type = 'edit' ){

			foreach ( $fields as $key => $field ) {
				// Display group field
				if( $field['type'] == 'group' ){
					$parent_key = $key. "['$key']";
					foreach ( $field['value'] as $key2 => $field2 ) {
						$parent_key = $key. "[$key2]";
						$default = get_term_meta( $term_id, $key, true );
						$default = empty( $default[$key2] ) ? false : $default[$key2];
						$this->display_single_field( $parent_key, $field2, $term_id, $type, $default );
					}
				}
				// Display single field
				else{
					$this->display_single_field( $key, $field, $term_id, $type );
				}
			}
		}

		private function display_single_field( $key, $field, $term_id, $type, $default = false ){
			// Set default value
			if ( !$default ) {
				$default = get_term_meta( $term_id, $key, true );
			}

			if ( $field['type'] != 'multi_checkbox' && empty( $default ) && !empty( $field['default'] ) ) {
				$default = $field['default'];
			}

			// class
			if ( !empty( $field['class'] ) ) {
				$class = 'class="rtfm-meta-field '. esc_attr( $field['class'] ). '"';
			}
			else {
				$class = 'class="rtfm-meta-field"';
			}

			if ( $type == 'add' ) {
				$this->add_field_html( $key, $field, $default, $class );
			}
			else {
				$this->edit_field_html( $key, $field, $default, $class );
			}
		}

		private function edit_field_html( $key, $field, $default, $class ) {
			$desc = '';
			if ( !empty( $field['desc'] ) ){
				$desc = '<div class="rt-postmeta-desc">' . wp_kses_post( $field['desc'] ) . '</div>';
			}
			?>
		    <tr class="form-field term-<?php echo esc_attr( $key );?>-wrap">
		        <th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
		        <td>
		        	<?php
		        	if ( method_exists( $this, $field['type'] ) ) {
		        		$this->{$field['type']}( $key, $field, $default, $class );
		        	}
		        	echo $desc;
		        	?>
		        </td>
		    </tr>
			<?php
		}

		private function add_field_html( $key, $field, $default, $class ) {
			$desc = '';
			if ( !empty( $field['desc'] ) ){
				$desc = '<div class="rt-postmeta-desc">' . wp_kses_post( $field['desc'] ) . '</div>';
			}
			?>
			<div class="form-field term-<?php echo esc_attr( $key );?>-wrap">
				<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	        	<?php
	        	if ( method_exists( $this, $field['type'] ) ) {
	        		$this->{$field['type']}( $key, $field, $default, $class );
	        		echo $desc;
	        	}
	        	?>
			</div>
			<?php
		}
	}
}