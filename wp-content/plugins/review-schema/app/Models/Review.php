<?php 

namespace Rtrs\Models; 
use Rtrs\Helpers\Functions;

class Review {
 
    /**
     *  Average ratings
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getAvgRatings( $id ) {
        $comments = get_approved_comments( $id );
    
        if ( $comments ) {
            $i = 0;
            $total = 0;
            foreach( $comments as $comment ){
                $rate = get_comment_meta( $comment->comment_ID, 'rating', true );
                if( isset( $rate ) && '' !== $rate ) {
                    $i++;
                    $total += $rate;
                }
            }
    
            if ( 0 === $i ) {
                return false;
            } else {
                return round( $total / $i, 1 );
            }
        } else {
            return false;
        }
    }

    /**
     *  Average rating by star
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getAvgRatingByStar( $id ) {
        $comments = get_approved_comments( $id );
    
        if ( $comments ) { 
            $total = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 ];
            foreach( $comments as $comment ){
                $rate = get_comment_meta( $comment->comment_ID, 'rating', true );
                if( isset( $rate ) && '' !== $rate ) {
                    if ( $rate >= 4.01 && $rate <= 5 ) {
                        $total[5]++;
                    } else if ( $rate >= 3.01 && $rate <= 4 ) {
                        $total[4]++;
                    } else if ( $rate >= 2.01 && $rate <= 3 ) {
                        $total[3]++;
                    } else if ( $rate >= 1.01 && $rate <= 2 ) {
                        $total[2]++;
                    } else if ( $rate >= 1 && $rate <= 1.99 ) {
                        $total[1]++;
                    }
                }
            }
            
            return $total;
            // if ( 0 === $i ) {
            //     return false;
            // } else {
            //     return round( $total / $i, 1 );
            // }
        } else {
            return false;
        }
    }

    /**
     *  Get Total Rating
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getTotalRatings( $id ) {
        $args = [
            'meta_query' => array( 
                array( 
                  'key' => 'rating', 
                  'compare' => 'EXISTS', 
                ), 
            ), 
        ];
        $comments = get_approved_comments( $id, $args );
    
        if ( $comments ) {
            return count($comments);
        }
    }
    
    /**
     *  Get Best Rating
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getBestRating( $id ) {
        $args = [
            'order'   => 'DSC',
            'number'   => 1,
            'meta_key' => 'rating',
            'orderby' => 'meta_value_num'
        ];
        $comments = get_approved_comments( $id, $args );
        $rating = null;
        if ( $comments ) { 
            foreach( $comments as $comment ){
                $rating = get_comment_meta( $comment->comment_ID, 'rating', true ); 
            }  
        }  
        return $rating;
    }

    /**
     *  Get Worst Rating
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getWorstRating( $id ) {
        $args = [
            'order'   => 'ASC',
            'number'   => 1,
            'meta_key' => 'rating',
            'orderby' => 'meta_value_num'
        ];
        $comments = get_approved_comments( $id, $args );
        $rating = null;
        if ( $comments ) { 
            foreach( $comments as $comment ){
                $rating = get_comment_meta( $comment->comment_ID, 'rating', true ); 
            }  
        }  
        return $rating;
    }

    /**
     *  Average ratings
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getCriteriaAvgRatings( $p_id ) { 
        // get criteria rating
        $criteria_total_sum = $criteria_avg = $criteria_name_avg = []; 

        $comments = get_approved_comments( $p_id ); 
        if ( $comments ) { 
            $i = 0;
            // get total of each criteria by comments
            foreach( $comments as $comment_key => $comment ) {
                $rating = get_comment_meta( $comment->comment_ID, 'rt_rating_criteria', true );
                if( isset( $rating ) && '' !== $rating ) {
                    $i++; 
                }

                //calculate criteria 
                if ( $rating ) {
                    foreach( $rating as $rate_key => $rate ) {   
                        if ( isset( $criteria_total_sum[$rate_key] ) ) {
                            $criteria_total_sum[$rate_key] += $rate;
                        } else {
                            $criteria_total_sum[$rate_key] = $rate;
                        } 
                    }  
                }
            } 

            // get avg of criteria
            foreach ($criteria_total_sum as $c_key => $value) {
                $criteria_avg[] = round( $value / $i, 1 );
            }

            // adjust avg rating with criteria name
            if ( $multi_criteria = Functions::getCriteriaByPostType() ) { 
                foreach( $multi_criteria as $criteria_key => $value ): 
                    $criteria_name_avg[$criteria_key]['title'] = $value;
                    if ( isset( $criteria_avg[$criteria_key] ) ) {
                        $criteria_name_avg[$criteria_key]['avg'] = $criteria_avg[$criteria_key];
                    } else {
                        $criteria_name_avg[$criteria_key]['avg'] = 5;
                    }
                endforeach;  
            }

            return $criteria_name_avg;

        } else {
            return [];
        }
    }

    /**
     *  Get total recommedation
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function getTotalRecommendation( $id ) {
        $comments = get_approved_comments( $id );
    
        if ( $comments ) {
            $i = 0; 
            foreach( $comments as $comment ){
                $rate = get_comment_meta( $comment->comment_ID, 'rt_recommended', true );
                if( isset( $rate ) && 1 == $rate ) {
                    $i++; 
                }
            } 
            return $i;

        } else {
            return 0;
        }
    }

    /**
     *  Comment list
     *
     * @package Review Schema
     * @since 1.0
     */
    public static function comment_list($comment, $args, $depth) { 
        extract($args, EXTR_SKIP);
        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        } 
        $comment_id = get_comment_ID();
    ?>

    <<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? ' rtrs-main-review' : 'parent rtrs-main-review' ) ?> id="div-comment-<?php comment_ID() ?>"> 
        <?php  
            global $rt_post_id;

            $get_post_type = ( $rt_post_id ) ? get_post_type( $rt_post_id ) : get_post_type(); 
            $p_meta = Functions::getMetaByPostType( $get_post_type );  

            $layout = isset( $p_meta['review_layout'] ) ? $p_meta['review_layout'][0] : 'one';  
            
            $comment_details = get_comment( $comment_id ); 
            $review_edit = rtrs()->get_options('rtrs_review_settings', array( 'review_edit', 'yes' ));

            Functions::get_template_part( 'review/layout-' . $layout, array( 
                    'comment' => $comment,
                    'add_below' => $add_below, 
                    'depth' => $depth, 
                    'args' => $args, 
                    'p_meta' => $p_meta,
                    'comment_details' => $comment_details, 
                    'review_edit' => $review_edit, 
                ) 
            ); 
        ?> 
    <?php 
    } 
}