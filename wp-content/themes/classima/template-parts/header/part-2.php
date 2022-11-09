<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.4.1
 */

namespace radiustheme\Classima;

use Rtcl\Helpers\Link;

$nav_menu_args = Helper::nav_menu_args();

$light_logo = empty( RDTheme::$options['logo_light']['url'] ) ? Helper::get_img( 'logo-light.png' ) : RDTheme::$options['logo_light'];
$dark_logo = empty( RDTheme::$options['logo']['url'] ) ? Helper::get_img( 'logo-dark.png' ) : RDTheme::$options['logo'];

$has_header_icons = RDTheme::$options['header_icon'] || ( RDTheme::$options['header_btn_txt'] && RDTheme::$options['header_btn_url'] ) ? true: false;
$login_icon_title = is_user_logged_in() ? esc_html__( 'My Account', 'classima' ) : esc_html__( 'Login/Register', 'classima' );
?>
<div class="main-header-inner">
    <div class="site-branding">

        <?php if (!empty($dark_logo['url'])): ?>
            <a class="dark-logo" href="<?php echo esc_url( home_url( '/' ) );?>"><img src="<?php echo esc_url( $dark_logo['url'] ); ?>" width="<?php echo isset($dark_logo['width']) ? esc_attr( $dark_logo['width'] ) : '150'; ?>" height="<?php echo isset($dark_logo['height']) ? esc_attr( $dark_logo['height'] ) : '45'; ?>"  alt="<?php esc_attr( bloginfo( 'name' ) ) ;?>"></a>
        <?php else: ?>
            <a class="dark-logo" href="<?php echo esc_url( home_url( '/' ) );?>"><img src="<?php echo esc_url( $dark_logo ); ?>" width="150" height="45" alt="<?php esc_attr( bloginfo( 'name' ) ) ;?>"></a>
        <?php endif; ?>

        <?php if (!empty($light_logo['url'])): ?>
            <a class="light-logo" href="<?php echo esc_url( home_url( '/' ) );?>"><img src="<?php echo esc_url( $light_logo['url'] ); ?>" height="<?php echo isset($light_logo['height']) ? esc_attr( $light_logo['height'] ) : '45'; ?>" width="<?php echo isset($light_logo['width']) ? esc_attr( $dark_logo['width'] ) : '150'; ?>" alt="<?php esc_attr( bloginfo( 'name' ) ) ;?>"></a>
        <?php else: ?>
            <a class="light-logo" href="<?php echo esc_url( home_url( '/' ) );?>"><img src="<?php echo esc_url( $light_logo ); ?>" width="150" height="45" alt="<?php esc_attr( bloginfo( 'name' ) ) ;?>"></a>
        <?php endif; ?>

    </div>
	<div class="main-navigation-area">
		<div id="main-navigation" class="main-navigation"><?php wp_nav_menu( $nav_menu_args );?></div>
	</div>

	<?php if ( $has_header_icons ): ?>

		<div class="header-icon-area">

			<?php if ( Helper::is_chat_enabled() ): ?>
				<a class="header-chat-icon rtcl-chat-unread-count" title="<?php esc_html_e( 'Chat','classima' );?>" href="<?php echo esc_url( Link::get_my_account_page_link( 'chat' ) ); ?>"><i class="far fa-comments" aria-hidden="true"></i></a>
			<?php endif; ?>

			<?php if ( RDTheme::$options['header_icon'] && class_exists( 'RtclPro' ) ): ?>
				<a class="header-login-icon" data-toggle="tooltip" title="<?php echo esc_attr( $login_icon_title );?>" href="<?php echo esc_url( Link::get_my_account_page_link() ); ?>"><i class="far fa-user" aria-hidden="true"></i></a>
			<?php endif; ?>

			<?php if ( RDTheme::$options['header_btn_txt'] && RDTheme::$options['header_btn_url'] ): ?>
				<div class="header-btn-area">
					<a class="header-btn" href="<?php echo esc_url( RDTheme::$options['header_btn_url'] );?>"><i class="fas fa-plus" aria-hidden="true"></i><?php echo esc_html( RDTheme::$options['header_btn_txt'] );?></a>
				</div>
			<?php endif; ?>
		</div>
		
	<?php endif; ?>
</div>