<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'review-schema' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
</p> 

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>"><?php esc_html_e( 'Select Affiliate:', 'review-schema' ); ?></label>
	<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'shortcode_id' ) ); ?>">
	<option value=""><?php esc_html_e( 'Select', 'review-schema' ); ?></option>
    <?php 
		$shortcode_args = new WP_Query( array(
			'post_type' => rtrs()->getPostTypeAffiliate(),
			'posts_per_page' => -1
		));

		while( $shortcode_args->have_posts() ): $shortcode_args->the_post(); 
			$selected = ( $instance['shortcode_id'] == get_the_ID() ) ? 'selected' : '';
			echo '<option '. esc_attr( $selected ) .' value="'. esc_attr( get_the_ID() ) .'">'. esc_html( get_the_title() ) .'</option>';
		endwhile; 
		wp_reset_postdata();
	?>
	</select>
</p>