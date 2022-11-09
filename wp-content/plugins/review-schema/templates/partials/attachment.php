<?php  
/**
 * Review helpful template
 * @author      RadiusTheme
 * @package     review-schema/templates
 * @version     1.0.0
 * 
 * @var use Rtrs\Helpers\Functions 
 * 
 */
use Rtrs\Helpers\Functions;   
?>  
<?php  
    if ( $get_attachment = get_comment_meta( get_comment_ID(), 'rt_attachment', true ) ) {  
?>
<div class="rtrs-review-item-media">
    <?php 
    if ( isset( $p_meta['image_review'] ) && $p_meta['image_review'][0] == '1' ) { 
        if ( isset( $get_attachment['imgs'] ) ) {  
        foreach( $get_attachment['imgs'] as $img) { ?> 
            <div class="rtrs-media-item rtrs-media-image">
                <a class="rtrs-attachment-img" data-featherlight="image" href="<?php echo wp_get_attachment_image_url( $img, '' ); ?>"><?php echo wp_get_attachment_image( $img, array('70', '70'), "thumbnail" ); ?></a>
            </div>
        <?php } } 
    } ?>  

    <?php   
    if ( isset( $p_meta['video_review'] ) && $p_meta['video_review'][0] == '1' ) { 
        if ( isset( $get_attachment['videos'] ) ) {  
            foreach( $get_attachment['videos'] as $video ) { ?>   
                <div class="rtrs-media-item rtrs-media-video">
                    <?php
                        $self_video = ( isset( $get_attachment['video_source'] ) && $get_attachment['video_source'] == 'self' ); 
                        $youtube_video_id = '';
                        if ( !$self_video ) {
                            $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
                            preg_match($pattern, $video, $matches);
                            $youtube_video_id = (isset($matches[1])) ? $matches[1] : '';
                        } 

                        $image_url = $self_video ? Functions::get_default_placeholder_url() : 'https://img.youtube.com/vi/'. $youtube_video_id .'/default.jpg'; 

                        $video_url = $self_video ? wp_get_attachment_url( $video ) : 'https://www.youtube.com/embed/'.$youtube_video_id; 
                    ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="width: 80px;" alt="<?php esc_attr_e( 'Review Schema', 'review-schema' ); ?>">  
                    <?php if ( !$self_video ) { ?>
                        <a href="<?php echo esc_url( $video_url ); ?>?rel=0&amp;autoplay=1" data-featherlight="iframe" data-featherlight-iframe-width="640" data-featherlight-iframe-height="480" data-featherlight-iframe-frameborder="0" data-featherlight-iframe-allow="autoplay; encrypted-media" data-featherlight-iframe-allowfullscreen="true" class="rtrs-video-icon"><i class="rtrs-play"></i></a> 
                    <?php } else { ?>
                        <a href="#" data-video-url="<?php echo esc_url( $video_url ); ?>" class="rtrs-video-icon rtrs-play-self-video"><i class="rtrs-play"></i></a> 
                    <?php } ?>
                </div>
            <?php 
            } 
        }
    } 
    ?>  
</div>
<?php } ?> 
