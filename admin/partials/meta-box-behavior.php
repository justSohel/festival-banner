<?php
/**
 * Behavior Meta Box Template
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin/partials
 * @var        WP_Post $post Current post object
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Get saved data.
$is_dismissible = get_post_meta( $post->ID, '_fb_is_dismissible', true );
$position       = get_post_meta( $post->ID, '_fb_position', true );

// Default to true.
// if ( '' === $is_dismissible ) {
// 	$is_dismissible = true;
// }

// Check if modal (always dismissible).
$is_modal = ( 'modal' === $position );
?>

<div class="festival-banner-meta-box">
	<p>
		<label>
			<input 
				type="checkbox" 
				id="fb_is_dismissible" 
				name="fb_is_dismissible" 
				value="1"
				<?php checked( $is_dismissible, true ); ?>
				<?php disabled( $is_modal, true ); ?>
			>
			<strong><?php esc_html_e( 'Allow users to dismiss this banner', 'festival-banner' ); ?></strong>
		</label>
	</p>

	<p class="description">
		<?php esc_html_e( 'Dismissed banners reappear after browser session ends (refresh/new tab shows again).', 'festival-banner' ); ?>
	</p>

	<?php if ( $is_modal ) : ?>
		<p class="description" style="padding: 10px; background: #e0f2ff; border-left: 3px solid #0073aa; margin-top: 15px;">
			<strong>ℹ️ <?php esc_html_e( 'Note:', 'festival-banner' ); ?></strong>
			<?php esc_html_e( 'Modal banners are always dismissible.', 'festival-banner' ); ?>
		</p>
	<?php endif; ?>
</div>