<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'RT_Widget_Fields' ) ){

	class RT_Widget_Fields {
		
		public static function display( $fields, $instance, $object ){
			foreach ( $fields as $key => $field ) {
				$label   = $field['label'];
				$desc    = !empty( $field['desc'] ) ? $field['desc'] : false;
				$id      = $object->get_field_id( $key );
				$name    = $object->get_field_name( $key );
				$value   = $instance[$key];
				$options = !empty( $field['options'] ) ? $field['options'] : false;
				
				if ( method_exists( __CLASS__, $field['type'] ) ) {
					echo '<div class="rt-widget-field">';
					if ( version_compare( phpversion() , '5.3.0', '<' ) ) {
						call_user_func( __CLASS__ . '::'. $field['type'], $id, $name, $value, $label, $options, $field );
					}
					else {
						call_user_func( array( __CLASS__, $field['type'] ), $id, $name, $value, $label, $options, $field );
					}
					if ( $desc ) {
						echo '<div class="desc">' . $desc . '</div>';
					}
					echo '</div>';
				}
			}
		}

		public static function text( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}

		public static function url( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_url( $value ); ?>" />
			<?php
		}

		public static function number( $id, $name, $value, $label, $options, $field ){
			$min  = isset( $field['min'] ) ? $field['min'] : 1;
			$max  = isset( $field['max'] ) ? $field['max'] : '';
			$step = isset( $field['step'] ) ? $field['step'] : 1;
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<input class="widefat" type="number" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}

		public static function textarea( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<textarea class="widefat" rows="3" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
			<?php
		}

		public static function checkbox( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id ); ?>"><input class="widefat" type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name );?>" <?php echo $value ? ' checked="checked"' : '';?> /> <?php echo esc_html( $label );?></label>
			<?php
		}

		public static function select( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<select name="<?php echo esc_attr( $name );?>" id="<?php echo esc_attr( $id );?>">
				<?php foreach ( $options as $key => $option ): ?>
					<?php $selected = ( $key == $value ) ? ' selected="selected"' : ''; ?>
					<option value="<?php echo esc_attr( $key );?>"<?php echo $selected; ?>><?php echo esc_html( $option )?></option>
				<?php endforeach; ?>
			</select>
			<?php
		}

		// Using components of RT_Postmeta_Fields
		public static function image( $id, $name, $value, $label, $options, $field ){
			$image = '';
			$disstyle = '';

			if ( $value ) {
				$image = wp_get_attachment_image_src( $value, 'thumbnail' );
				$image = $image[0];
			}
			else{
				$disstyle = 'display:none;';
			}

			echo '
			<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . ':</label>
			<div class="rt_metabox_image">
			<input name="'. esc_attr( $name ) .'" type="hidden" class="custom_upload_image" value="'. esc_attr( $value ) .'" />
			<img src="'. esc_url( $image ) .'" class="custom_preview_image" style="'. esc_attr( $disstyle ) .'" alt="" />
			<input class="rt_upload_image upload_button_'. esc_attr( $id ) .' button-primary" type="button" value="' . esc_attr__( 'Choose Image', 'rt-framework' ). '" />
			<div class="rt_remove_image_wrap" style="'. esc_attr( $disstyle ) .'"><a href="#" class="rt_remove_image button" >' . esc_html__( 'Remove Image', 'rt-framework' ). '</a></div>
			</div>
			';
		}

		public static function multi_select( $id, $name, $value, $label, $options, $field ){
			?>
			<label for="<?php echo esc_attr( $id );?>"><?php echo esc_html( $label );?>:</label>
			<?php
			if ( empty( $value ) ) {
				$value = array();
			}
			echo '<select class="rt-multi-select" data-placeholder=" ' . esc_attr__( 'Click here to select options', 'rt-framework' ) . '" multiple="multiple"'.
			' name="' . esc_attr( $name ) . '[]"'.
			' id="' . esc_attr( $id ) . '">';
			foreach ( $options as $key => $option ) {
				echo '<option',
				in_array( $key, $value ) ? ' selected="selected"' : '',
				' value="' . esc_attr( $key ) . '"'.
				'>' .
				esc_html( $option ) . 
				'</option>';
			}
			echo '</select>';
		}

	}
}