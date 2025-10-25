<?php
/**
 * CTA Meta Box Template
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
$cta_text    = get_post_meta( $post->ID, '_fb_cta_text', true );
$cta_url     = get_post_meta( $post->ID, '_fb_cta_url', true );
$cta_new_tab = get_post_meta( $post->ID, '_fb_cta_new_tab', true );
?>

<div class="festival-banner-meta-box">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="fb_cta_text">
						<?php esc_html_e( 'Button Text', 'festival-banner' ); ?>
					</label>
				</th>
				<td>
					<input 
						type="text" 
						id="fb_cta_text" 
						name="fb_cta_text" 
						value="<?php echo esc_attr( $cta_text ); ?>" 
						class="regular-text"
						placeholder="<?php esc_attr_e( 'e.g., Shop Now', 'festival-banner' ); ?>"
					>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="fb_cta_url">
						<?php esc_html_e( 'Button URL', 'festival-banner' ); ?>
					</label>
				</th>
				<td>
					<input 
						type="url" 
						id="fb_cta_url" 
						name="fb_cta_url" 
						value="<?php echo esc_attr( $cta_url ); ?>" 
						class="regular-text"
						placeholder="<?php esc_attr_e( 'https://example.com/shop', 'festival-banner' ); ?>"
					>
					<p class="description">
						<?php esc_html_e( 'Enter a full URL (https://...) or relative URL (/shop)', 'festival-banner' ); ?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"></th>
				<td>
					<label>
						<input 
							type="checkbox" 
							id="fb_cta_new_tab" 
							name="fb_cta_new_tab" 
							value="1"
							<?php checked( $cta_new_tab, true ); ?>
						>
						<?php esc_html_e( 'Open link in new tab', 'festival-banner' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>

	<p class="description">
		<strong><?php esc_html_e( 'Note:', 'festival-banner' ); ?></strong>
		<?php esc_html_e( 'Leave both fields blank to hide the button.', 'festival-banner' ); ?>
	</p>
</div>