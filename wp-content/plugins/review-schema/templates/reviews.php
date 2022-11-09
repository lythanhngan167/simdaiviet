<?php
/**
 * Review grid one template
 *
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 */
use Rtrs\Models\Review;
use Rtrs\Helpers\Functions;

$get_post_type = get_post_type( get_the_ID() );
$p_meta        = Functions::getMetaByPostType( get_post_type() );
$parent_class  = isset( $p_meta['parent_class'] ) ? $p_meta['parent_class'][0] : '';
?>  
<div class="rtrs-review-wrap <?php echo esc_attr( $parent_class ); ?> rtrs-review-post-type-<?php echo esc_attr( $get_post_type ); ?> rtrs-review-sc-<?php echo esc_attr( $p_meta['sc_id'] ); ?>" id="comments">
	<?php if ( have_comments() ) : ?> 
		<?php
			$total_rating = Review::getTotalRatings( get_the_ID() );
			$layout       = isset( $p_meta['summary_layout'] ) ? $p_meta['summary_layout'][0] : 'one';

			$s_affiliate = ( get_post_meta( get_the_ID(), 'rtrs_affiliate', true ) == '1' );
			$s_layout    = get_post_meta( get_the_ID(), 'rtrs_summary_layout', true );

		if ( $s_affiliate && $s_layout ) {
			$layout = $s_layout;
		}

		if ( $total_rating ) {
			Functions::get_template_part(
				'summary/layout-' . $layout,
				[
					'total_rating' => $total_rating,
					'p_meta'       => $p_meta,
				]
			);
		}
		?>

		<?php if ( $total_rating ) { ?>
		<div class="rtrs-sorting-bar">
			<h3 class="rtrs-sorting-title"> 
			<?php
			$review_title = esc_html(  sprintf(
				_n( 'Reviewed by %s user', 'Reviewed by %s users', $total_rating, 'review-schema' ) ,
				$total_rating
			) );
			echo apply_filters( 'rtrs_review_sorting_title', $review_title, $total_rating, get_the_ID() );
			?>
			</h3>

			<div class="rtrs-sorting-select">
				<?php
					$filter = ( isset( $p_meta['filter'] ) && $p_meta['filter'][0] == '1' );
				if ( $filter ) {
					$filter_option = isset( $p_meta['filter_option'] ) ? $p_meta['filter_option'] : '';
					?>
				<div>
					<label><i class="rtrs-sort"></i> <?php esc_html_e( 'Sort:', 'review-schema' ); ?></label> 
					<select class="rtrs_review_filter rtrs-sort-filter" name="rtrs_review_sort_filter" data-type="sort">
						<option value="all"><?php esc_html_e( 'All Review', 'review-schema' ); ?></option>  
					<?php if ( in_array( 'top_rated', $filter_option ) ) { ?>
						<option value="top_rated"><?php esc_html_e( 'Top Rated', 'review-schema' ); ?></option>
						<?php } if ( in_array( 'low_rated', $filter_option ) ) { ?>
						<option value="low_rated"><?php esc_html_e( 'Low Rated', 'review-schema' ); ?></option>
						<?php } if ( in_array( 'recommended', $filter_option ) ) { ?>
						<option value="recommended"><?php esc_html_e( 'Recommended', 'review-schema' ); ?></option> 
						<?php } if ( in_array( 'highlighted', $filter_option ) ) { ?>
						<option value="highlighted"><?php esc_html_e( 'Highlighted', 'review-schema' ); ?></option>  
						<?php } if ( in_array( 'latest_first', $filter_option ) ) { ?>
						<option value="latest_first"><?php esc_html_e( 'Latest First', 'review-schema' ); ?></option> 
						<?php } if ( in_array( 'oldest_first', $filter_option ) ) { ?>
						<option value="oldest_first"><?php esc_html_e( 'Oldest First', 'review-schema' ); ?></option>  
						<?php } ?>
					</select>
				</div>
				<?php } ?>

				<?php
					$filter = ( isset( $p_meta['filter'] ) && $p_meta['filter'][0] == '1' );
				if ( $filter ) {
					?>
					<div>
						<label><i class="rtrs-filter"></i> <?php esc_html_e( 'Filter:', 'review-schema' ); ?></label> 
						<select class="rtrs_review_filter" name="rtrs_review_rating_filter" data-type="rating">
							<option value=""><?php esc_html_e( 'All Star', 'review-schema' ); ?></option>   
							<option value="5"><?php esc_html_e( '5 Star', 'review-schema' ); ?></option>  
							<option value="4"><?php esc_html_e( '4 Star', 'review-schema' ); ?></option>  
							<option value="3"><?php esc_html_e( '3 Star', 'review-schema' ); ?></option>  
							<option value="2"><?php esc_html_e( '2 Star', 'review-schema' ); ?></option>  
							<option value="1"><?php esc_html_e( '1 Star', 'review-schema' ); ?></option>  
						</select>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<div class="rtrs-review-box">
			<ul class="rtrs-review-list"> 
				<?php
					// Gather comments for a specific page/post
					$comments = get_comments(
						[
							'post_id' => get_the_ID(),
							'status'  => 'approve', // Change this to the type of comments to be displayed
						]
					);

					wp_list_comments(
						[
							'style'      => 'li',
							'short_ping' => true,
							'callback'   => [ Review::class, 'comment_list' ],
						],
						$comments
					);
				?>
			</ul>
		</div> 

		<?php
		$pagination_type = isset( $p_meta['pagination_type'] ) ? $p_meta['pagination_type'][0] : 'number';
		if ( $pagination_type == 'number' ) {
			?>
			<?php if ( get_the_comments_pagination() ) { ?>
			<div class="rtrs-paginate">
				<?php
				paginate_comments_links(
					[
						'prev_text' => '<i class="rtrs-angle-left"></i>',
						'next_text' => '<i class="rtrs-angle-right"></i>',
					]
				);
				?>
			</div>
			<?php } ?>
		<?php } elseif ( $pagination_type == 'number-ajax' ) { ?>
			<?php if ( get_the_comments_pagination() ) { ?>
			<div class="rtrs-paginate rtrs-paginate-ajax" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>">
				<?php
				paginate_comments_links(
					[
						'prev_text' => '<i class="rtrs-angle-left"></i>',
						'next_text' => '<i class="rtrs-angle-right"></i>',
					]
				);
				?>
			</div>
			<?php } ?>
		<?php } elseif ( $pagination_type == 'load-more' ) { ?>
			<div class="rtrs-paginate rtrs-paginate-load-more rtrs-align-center">
				<a href="#" id="rtrs-load-more" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>"><?php echo esc_html_e( 'Load More', 'review-schema' ); ?></a>
			</div>
		<?php } elseif ( $pagination_type == 'auto-scroll' ) { ?>
			<div class="rtrs-paginate rtrs-paginate-onscroll" data-max="<?php echo esc_attr( get_comment_pages_count() ); ?>"></div>
		<?php } ?>

		<?php
	endif;

	if ( is_user_logged_in() ) {
		global $current_user;
		$is_commented = get_comments(
			[
				'user_id'    => $current_user->ID,
				'post_id'    => $post->ID,
				'meta_query' => [
					[
						'key'     => 'rating',
						'value'   => [ 1, 5 ],
						'compare' => 'BETWEEN',
					],
				],
			]
		);

		$multiple_review = rtrs()->get_options( 'rtrs_review_settings', [ 'multiple_review', 'no' ] );

		if ( $is_commented && $multiple_review == 'no' ) {
			echo '<div class="rtrs-multiple-comment">';
				comment_form();
			echo '</div>';
		} else {
			comment_form();
		}
	} else {
		comment_form();
	}
	?>
	  
	<script>
		jQuery( document ).ready(function($) {
			$('#comment_form').removeAttr('novalidate');
		});
	</script>
</div> 
