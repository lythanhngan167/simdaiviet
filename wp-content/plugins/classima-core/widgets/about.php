<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

namespace radiustheme\Classima_Core;

use \WP_Widget;
use \RT_Widget_Fields;

class About_Widget extends WP_Widget {
	public function __construct() {
		$id = CLASSIMA_CORE_THEME_PREFIX . '_about';
		parent::__construct(
            $id, // Base ID
            esc_html__( 'Classima: About', 'classima-core' ), // Name
            array( 'description' => esc_html__( 'Classima: About', 'classima-core' )
        ) );
	}

	public function widget( $args, $instance ){
		echo wp_kses_post( $args['before_widget'] );

		if ( !empty( $instance['logo'] ) ) {
			$html = wp_get_attachment_image_src( $instance['logo'], 'full' );
			$width = $html[1];
			$height = $html[2];
			$html = $html[0];
			$html = '<div class="rtin-img"><img src="' . $html . '" width="'.$width.'" height="'.$height.'" alt="' . $html . '"></div>';
		}
		elseif ( !empty( $instance['title'] ) ) {
			$html = apply_filters( 'widget_title', $instance['title'] );
			$html = $args['before_title'] . $html .$args['after_title'];
		}
		else {
			$html = '';
		}

		echo wp_kses_post( $html );
		?>
		<p class="rtin-des"><?php if( !empty( $instance['description'] ) ) echo wp_kses_post( $instance['description'] ); ?></p>
		<ul class="rtin-socials">
			<?php
			if( !empty( $instance['facebook'] ) ){
				?><li class="rtin-facebook"><a href="<?php echo esc_url( $instance['facebook'] ); ?>" target="_blank"><i class="fab fa-fw fa-facebook-f"></i></a></li><?php
			}
			if( !empty( $instance['twitter'] ) ){
				?><li class="rtin-twitter"><a href="<?php echo esc_url( $instance['twitter'] ); ?>" target="_blank"><i class="fab fa-fw fa-twitter"></i></a></li><?php
			}
			if( !empty( $instance['linkedin'] ) ){
				?><li class="rtin-linkedin"><a href="<?php echo esc_url( $instance['linkedin'] ); ?>" target="_blank"><i class="fab fa-fw fa-linkedin-in"></i></a></li><?php
			}
			if( !empty( $instance['pinterest'] ) ){
				?><li class="rtin-pinterest"><a href="<?php echo esc_url( $instance['pinterest'] ); ?>" target="_blank"><i class="fab fa-fw fa-pinterest"></i></a></li><?php
			}
			if( !empty( $instance['instagram'] ) ){
				?><li class="rtin-instagram"><a href="<?php echo esc_url( $instance['instagram'] ); ?>" target="_blank"><i class="fab fa-fw fa-instagram"></i></a></li><?php
			}
			if( !empty( $instance['youtube'] ) ){
				?><li class="rtin-youtube"><a href="<?php echo esc_url( $instance['youtube'] ); ?>" target="_blank"><i class="fab fa-fw fa-youtube"></i></a></li><?php
			}
			if( !empty( $instance['rss'] ) ){
				?><li class="rtin-rss"><a href="<?php echo esc_url( $instance['rss'] ); ?>" target="_blank"><i class="fas fa-fw fa-rss"></i></a></li><?php
			}
			?>
		</ul>

		<?php
		echo wp_kses_post( $args['after_widget'] );
	}

	public function update( $new_instance, $old_instance ){
		$instance                  = array();
		$instance['title']         = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['logo']          = ( ! empty( $new_instance['logo'] ) ) ? sanitize_text_field( $new_instance['logo'] ) : '';
		$instance['description']   = ( ! empty( $new_instance['description'] ) ) ? wp_kses_post( $new_instance['description'] ) : '';
		$instance['facebook']      = ( ! empty( $new_instance['facebook'] ) ) ? sanitize_text_field( $new_instance['facebook'] ) : '';
		$instance['twitter']       = ( ! empty( $new_instance['twitter'] ) ) ? sanitize_text_field( $new_instance['twitter'] ) : '';
		$instance['linkedin']      = ( ! empty( $new_instance['linkedin'] ) ) ? sanitize_text_field( $new_instance['linkedin'] ) : '';
		$instance['pinterest']     = ( ! empty( $new_instance['pinterest'] ) ) ? sanitize_text_field( $new_instance['pinterest'] ) : '';
		$instance['instagram']     = ( ! empty( $new_instance['instagram'] ) ) ? sanitize_text_field( $new_instance['instagram'] ) : '';
		$instance['youtube']       = ( ! empty( $new_instance['youtube'] ) ) ? sanitize_text_field( $new_instance['youtube'] ) : '';
		$instance['rss']           = ( ! empty( $new_instance['rss'] ) ) ? sanitize_text_field( $new_instance['rss'] ) : '';
		return $instance;
	}

	public function form( $instance ){
		$defaults = array(
			'title'       => '',
			'logo'        => '',
			'description' => '',
			'facebook'    => '',
			'twitter'     => '',
			'linkedin'    => '',
			'pinterest'   => '',
			'instagram'   => '',
			'youtube'     => '',
			'rss'         => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$fields = array(
			'title'       => array(
				'label'   => esc_html__( 'Title', 'classima-core' ),
				'type'    => 'text',
			),
			'logo'        => array(
				'label'   => esc_html__( 'Logo', 'classima-core' ),
				'type'    => 'image',
			),
			'description' => array(
				'label'   => esc_html__( 'Description', 'classima-core' ),
				'type'    => 'textarea',
			),
			'facebook'    => array(
				'label'   => esc_html__( 'Facebook URL', 'classima-core' ),
				'type'    => 'url',
			),
			'twitter'     => array(
				'label'   => esc_html__( 'Twitter URL', 'classima-core' ),
				'type'    => 'url',
			),
			'linkedin'    => array(
				'label'   => esc_html__( 'Linkedin URL', 'classima-core' ),
				'type'    => 'url',
			),
			'pinterest'   => array(
				'label'   => esc_html__( 'Pinterest URL', 'classima-core' ),
				'type'    => 'url',
			),
			'instagram'   => array(
				'label'   => esc_html__( 'Instagram URL', 'classima-core' ),
				'type'    => 'url',
			),
			'youtube'    => array(
				'label'   => esc_html__( 'YouTube URL', 'classima-core' ),
				'type'    => 'url',
			),
			'rss'         => array(
				'label'   => esc_html__( 'Rss Feed URL', 'classima-core' ),
				'type'    => 'url',
			),
		);

		RT_Widget_Fields::display( $fields, $instance, $this );
	}
}