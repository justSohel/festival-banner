<?php
/**
 * Display Settings Meta Box Template
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
$position          = get_post_meta( $post->ID, '_fb_position', true );
$floating_position = get_post_meta( $post->ID, '_fb_floating_position', true );
$side_position     = get_post_meta( $post->ID, '_fb_side_position', true );
$modal_delay       = get_post_meta( $post->ID, '_fb_modal_delay', true );
$display_type      = get_post_meta( $post->ID, '_fb_display_type', true );
$specific_pages    = get_post_meta( $post->ID, '_fb_specific_pages', true );

// Defaults.
$position          = $position ? $position : 'top_bar';
$floating_position = $floating_position ? $floating_position : 'bottom_right';
$side_position     = $side_position ? $side_position : 'right';
$modal_delay       = $modal_delay ? $modal_delay : 3;
$display_type      = $display_type ? $display_type : 'all_pages';
$specific_pages    = is_array( $specific_pages ) ? $specific_pages : array();
?>

<div class="festival-banner-meta-box">
	<p>
		<strong><?php esc_html_e( 'Position:', 'festival-banner' ); ?></strong>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_position" 
				value="top_bar" 
				<?php checked( $position, 'top_bar' ); ?>
				class="fb-position-radio"
			>
			<?php esc_html_e( 'Top Bar', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_position" 
				value="bottom_bar" 
				<?php checked( $position, 'bottom_bar' ); ?>
				class="fb-position-radio"
			>
			<?php esc_html_e( 'Bottom Bar', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_position" 
				value="floating" 
				<?php checked( $position, 'floating' ); ?>
				class="fb-position-radio"
			>
			<?php esc_html_e( 'Floating', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_position" 
				value="modal" 
				<?php checked( $position, 'modal' ); ?>
				class="fb-position-radio"
			>
			<?php esc_html_e( 'Modal', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_position" 
				value="side" 
				<?php checked( $position, 'side' ); ?>
				class="fb-position-radio"
			>
			<?php esc_html_e( 'Side Banner', 'festival-banner' ); ?>
		</label>
	</p>

	<!-- Conditional: Floating Position -->
	<div class="fb-conditional-field fb-floating-fields" style="<?php echo ( 'floating' === $position ) ? '' : 'display:none;'; ?>">
		<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<label for="fb_floating_position">
				<strong><?php esc_html_e( 'Floating Position:', 'festival-banner' ); ?></strong>
			</label>
			<select name="fb_floating_position" id="fb_floating_position" style="width: 100%; margin-top: 5px;">
				<option value="top_left" <?php selected( $floating_position, 'top_left' ); ?>>
					<?php esc_html_e( 'Top Left', 'festival-banner' ); ?>
				</option>
				<option value="top_right" <?php selected( $floating_position, 'top_right' ); ?>>
					<?php esc_html_e( 'Top Right', 'festival-banner' ); ?>
				</option>
				<option value="bottom_left" <?php selected( $floating_position, 'bottom_left' ); ?>>
					<?php esc_html_e( 'Bottom Left', 'festival-banner' ); ?>
				</option>
				<option value="bottom_right" <?php selected( $floating_position, 'bottom_right' ); ?>>
					<?php esc_html_e( 'Bottom Right', 'festival-banner' ); ?>
				</option>
			</select>
		</p>
	</div>

	<!-- Conditional: Side Position -->
	<div class="fb-conditional-field fb-side-fields" style="<?php echo ( 'side' === $position ) ? '' : 'display:none;'; ?>">
		<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<label for="fb_side_position">
				<strong><?php esc_html_e( 'Side Position:', 'festival-banner' ); ?></strong>
			</label>
			<select name="fb_side_position" id="fb_side_position" style="width: 100%; margin-top: 5px;">
				<option value="left" <?php selected( $side_position, 'left' ); ?>>
					<?php esc_html_e( 'Left', 'festival-banner' ); ?>
				</option>
				<option value="right" <?php selected( $side_position, 'right' ); ?>>
					<?php esc_html_e( 'Right', 'festival-banner' ); ?>
				</option>
				<option value="both" <?php selected( $side_position, 'both' ); ?>>
					<?php esc_html_e( 'Both Sides', 'festival-banner' ); ?>
				</option>
			</select>
		</p>
	</div>

	<!-- Conditional: Modal Delay -->
	<div class="fb-conditional-field fb-modal-fields" style="<?php echo ( 'modal' === $position ) ? '' : 'display:none;'; ?>">
		<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<label for="fb_modal_delay">
				<strong><?php esc_html_e( 'Show after (seconds):', 'festival-banner' ); ?></strong>
			</label>
			<input 
				type="number" 
				id="fb_modal_delay" 
				name="fb_modal_delay" 
				value="<?php echo esc_attr( $modal_delay ); ?>" 
				min="0" 
				max="60"
				style="width: 100%; margin-top: 5px;"
			>
			<span class="description"><?php esc_html_e( 'Range: 0-60 seconds', 'festival-banner' ); ?></span>
		</p>
	</div>

	<hr style="margin: 20px 0;">

	<!-- Display Type -->
	<p>
		<strong><?php esc_html_e( 'Show banner on:', 'festival-banner' ); ?></strong>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_display_type" 
				value="all_pages" 
				<?php checked( $display_type, 'all_pages' ); ?>
				class="fb-display-type-radio"
			>
			<?php esc_html_e( 'All pages (site-wide)', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_display_type" 
				value="homepage_only" 
				<?php checked( $display_type, 'homepage_only' ); ?>
				class="fb-display-type-radio"
			>
			<?php esc_html_e( 'Homepage only', 'festival-banner' ); ?>
		</label>
	</p>

	<p>
		<label>
			<input 
				type="radio" 
				name="fb_display_type" 
				value="specific_pages" 
				<?php checked( $display_type, 'specific_pages' ); ?>
				class="fb-display-type-radio"
			>
			<?php esc_html_e( 'Specific pages', 'festival-banner' ); ?>
		</label>
	</p>

	<!-- Conditional: Specific Pages -->
	<div class="fb-conditional-field fb-specific-pages-field" style="<?php echo ( 'specific_pages' === $display_type ) ? '' : 'display:none;'; ?>">
		<p style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd;">
			<label for="fb_specific_pages">
				<strong><?php esc_html_e( 'Select Pages:', 'festival-banner' ); ?></strong>
			</label>
			<?php
			// Get all published pages.
			$pages = get_pages( array( 'post_status' => 'publish' ) );
			
			if ( ! empty( $pages ) ) {
				echo '<div style="max-height: 200px; overflow-y: auto; margin-top: 10px; border: 1px solid #ddd; padding: 10px; background: white;">';
				foreach ( $pages as $page ) {
					$checked = in_array( $page->ID, $specific_pages, true ) ? 'checked' : '';
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<label style="display: block; margin-bottom: 5px;">';
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '<input type="checkbox" name="fb_specific_pages[]" value="' . esc_attr( $page->ID ) . '" ' . $checked . '> ';
					echo esc_html( $page->post_title );
					echo '</label>';
				}
				echo '</div>';
			} else {
				echo '<p class="description">' . esc_html__( 'No pages found. Create pages first.', 'festival-banner' ) . '</p>';
			}
			?>
		</p>
	</div>
</div>