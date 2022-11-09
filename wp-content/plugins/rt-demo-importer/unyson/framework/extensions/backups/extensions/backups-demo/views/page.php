<?php if ( ! defined( 'FW' ) ) die( 'Forbidden' );
/**
 * @var FW_Ext_Backups_Demo[] $demos
 */

/**
 * @var FW_Extension_Backups $backups
 */
$backups = fw_ext('backups');

if ($backups->is_disabled()) {
	$confirm = '';
} else {
	$confirm = esc_html__(
		'IMPORTANT: Installing this demo will delete all existing data and contents of your website, so use it only in fresh website. Do you want to continue?', 'fw' //@kowsar
	);
}
?>

<style>



</style>
<script>

</script>
<h2><?php esc_html_e('Demo Content Install', 'fw') ?></h2>
<?php echo apply_filters( 'rt_demo_installer_warning', '' );//@kowsar ?>
<div>
	<?php if ( !class_exists('ZipArchive') ): ?>
		<div class="error below-h2">
			<p>
				<strong><?php _e( 'Important', 'fw' ); ?></strong>:
				<?php printf(
					__( 'You need to activate %s.', 'fw' ),
					'<a href="http://php.net/manual/en/book.zip.php" target="_blank">'. __('zip extension', 'fw') .'</a>'
				); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ($http_loopback_warning = fw_ext_backups_loopback_test()) : ?>
		<div class="error">
			<p><strong><?php _e( 'Important', 'fw' ); ?>:</strong> <?php echo $http_loopback_warning; ?></p>
		</div>
		<script type="text/javascript">var fw_ext_backups_loopback_failed = true;</script>
	<?php endif; ?>
</div>

<p></p>

<div class="theme-browser rendered" id="fw-ext-backups-demo-list">
<?php //echo '<pre>'; print_r($demos); echo '</pre>'; ?>

<?php
$item_holder = array();
foreach ($demos as $demo) {
	$item_id = $demo->get_id();
	$pos = substr($item_id, 0, 9);
	$item_holder[] = $pos;
}
if (in_array("elementor", $item_holder)) {	
 ?>
<!-- tab start -->
<main class="main">
    <div class="tab">
      <div class="tab-menu">
        <button class="tab-menu-link active" data-content="first">
          <span data-title="first"><?php esc_html_e('WPBakery Page Builder', 'rt-demo-importer'); ?></span>
        </button>
        <button class="tab-menu-link" data-content="second">
          <span data-title="second"><?php esc_html_e('Elementor Page Builder', 'rt-demo-importer'); ?></span>
        </button>
      </div>
      <div class="tab-bar">
	  
        <div class="tab-bar-content active" id="first">
          <div class="texts">
				<?php foreach ($demos as $demo) { 
					$item_id = $demo->get_id();
					$pos = substr($item_id, 0, 4);
					if ($pos == 'demo') {
				?>
					<div class="theme fw-ext-backups-demo-item" id="demo-<?php echo esc_attr($demo->get_id()) ?>">
						<div class="theme-screenshot">
							<img src="<?php echo esc_attr($demo->get_screenshot()); ?>" alt="Screenshot" />
						</div>
						<?php if ($demo->get_preview_link()): ?>
							<a class="more-details" target="_blank" href="<?php echo esc_attr($demo->get_preview_link()) ?>">
								<?php esc_html_e('Live Preview', 'fw') ?>
							</a>
						<?php endif; ?>
						<h3 class="theme-name"><?php echo esc_html($demo->get_title()); ?></h3>
						<div class="theme-actions">
							<a class="button button-primary"
							   href="#" onclick="return false;"
							   data-confirm="<?php echo esc_attr($confirm); ?>"
							   data-install="<?php echo esc_attr($demo->get_id()) ?>"><?php esc_html_e('Install', 'fw'); ?></a>
						</div>
					</div>
				<?php 
					}
				} ?>
          </div>
        </div>
		
        <div class="tab-bar-content" id="second">
          <div class="texts">
				<?php foreach ($demos as $demo) {
					$item_id = $demo->get_id();
					$pos = substr($item_id, 0, 9);					
					if ($pos == 'elementor') { ?>
					<div class="theme fw-ext-backups-demo-item" id="demo-<?php echo esc_attr($demo->get_id()) ?>">
						<div class="theme-screenshot">
							<img src="<?php echo esc_attr($demo->get_screenshot()); ?>" alt="Screenshot" />
						</div>
						<?php if ($demo->get_preview_link()): ?>
							<a class="more-details" target="_blank" href="<?php echo esc_attr($demo->get_preview_link()) ?>">
								<?php esc_html_e('Live Preview', 'fw') ?>
							</a>
						<?php endif; ?>
						<h3 class="theme-name"><?php echo esc_html($demo->get_title()); ?></h3>
						<div class="theme-actions">
						<?php if ( did_action( 'elementor/loaded' ) ) { ?>
							<a class="button button-primary"
							   href="#" onclick="return false;"
							   data-confirm="<?php echo esc_attr($confirm); ?>"
							   data-install="<?php echo esc_attr($demo->get_id()) ?>"><?php esc_html_e('Install', 'fw'); ?></a>
						<?php } else { ?>
							<button onclick="myFunction()">Elementor Not Active</button>
							 <script>
							function myFunction() {
							  alert("To Get this demo, Install Elementor Plugin First.");
							}
							</script>
							
						<?php } ?>
						</div>
					</div>
				<?php }
				} ?>
          </div>
        </div>
		
      </div>
    </div>
</main>
	<?php  } else { ?>
<?php foreach ( $demos as $demo ){ ?>
	<div class="theme fw-ext-backups-demo-item" id="demo-<?php echo esc_attr($demo->get_id()) ?>">
		<div class="theme-screenshot">
			<img src="<?php echo esc_attr($demo->get_screenshot()); ?>" alt="Screenshot" />
		</div>
		<?php if ($demo->get_preview_link()): ?>
			<a class="more-details" target="_blank" href="<?php echo esc_attr($demo->get_preview_link()) ?>">
				<?php esc_html_e('Live Preview', 'fw') ?>
			</a>
		<?php endif; ?>
		<h3 class="theme-name"><?php echo esc_html($demo->get_title()); ?></h3>
		<div class="theme-actions">
			<a class="button button-primary"
			   href="#" onclick="return false;"
			   data-confirm="<?php echo esc_attr($confirm); ?>"
			   data-install="<?php echo esc_attr($demo->get_id()) ?>"><?php esc_html_e('Install', 'fw'); ?></a>
		</div>
	</div>
<?php } ?>
<?php } ?>

</div>
