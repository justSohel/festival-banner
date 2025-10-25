<?php
/**
 * Appearance Meta Box Template
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
$bg_color        = get_post_meta( $post->ID, '_fb_bg_color', true );
$text_color      = get_post_meta( $post->ID, '_fb_text_color', true );
$cta_bg_color    = get_post_meta( $post->ID, '_fb_cta_bg_color', true );
$cta_text_color  = get_post_meta( $post->ID, '_fb_cta_text_color', true );
$animation       = get_post_meta( $post->ID, '_fb_animation', true );

// Defaults.
$bg_color       = $bg_color ? $bg_color : '#000000';
$text_color     = $text_color ? $text_color : '#ffffff';
$cta_bg_color   = $cta_bg_color ? $cta_bg_color : '#ffffff';
$cta_text_color = $cta_text_color ? $cta_text_color : '#000000';
$animation      = $animation ? $animation : 'fade';
?>

<div class="festival-banner-meta-box">
	
	<!-- Background Color -->
	<p>
		<label for="fb_bg_color">
			<strong><?php esc_html_e( 'Background Color:', 'festival-banner' ); ?></strong>
		</label>
		<input 
			type="text" 
			id="fb_bg_color" 
			name="fb_bg_color" 
			value="<?php echo esc_attr( $bg_color ); ?>" 
			class="fb-color-picker"
			style="width: 100%; margin-top: 5px;"
		>
	</p>

	<!-- Text Color -->
	<p style="margin-top: 15px;">
		<label for="fb_text_color">
			<strong><?php esc_html_e( 'Text Color:', 'festival-banner' ); ?></strong>
		</label>
		<input 
			type="text" 
			id="fb_text_color" 
			name="fb_text_color" 
			value="<?php echo esc_attr( $text_color ); ?>" 
			class="fb-color-picker"
			style="width: 100%; margin-top: 5px;"
		>
	</p>

	<hr style="margin: 20px 0;">

	<!-- Button Background Color -->
	<p>
		<label for="fb_cta_bg_color">
			<strong><?php esc_html_e( 'Button Background:', 'festival-banner' ); ?></strong>
		</label>
		<input 
			type="text" 
			id="fb_cta_bg_color" 
			name="fb_cta_bg_color" 
			value="<?php echo esc_attr( $cta_bg_color ); ?>" 
			class="fb-color-picker"
			style="width: 100%; margin-top: 5px;"
		>
	</p>

	<!-- Button Text Color -->
	<p style="margin-top: 15px;">
		<label for="fb_cta_text_color">
			<strong><?php esc_html_e( 'Button Text Color:', 'festival-banner' ); ?></strong>
		</label>
		<input 
			type="text" 
			id="fb_cta_text_color" 
			name="fb_cta_text_color" 
			value="<?php echo esc_attr( $cta_text_color ); ?>" 
			class="fb-color-picker"
			style="width: 100%; margin-top: 5px;"
		>
	</p>

	<hr style="margin: 20px 0;">

	<!-- Animation -->
	<p>
		<label for="fb_animation">
			<strong><?php esc_html_e( 'Animation:', 'festival-banner' ); ?></strong>
		</label>
		<select name="fb_animation" id="fb_animation" style="width: 100%; margin-top: 5px;">
			<option value="fade" <?php selected( $animation, 'fade' ); ?>>
				<?php esc_html_e( 'Fade', 'festival-banner' ); ?>
			</option>
			<option value="slide" <?php selected( $animation, 'slide' ); ?>>
				<?php esc_html_e( 'Slide', 'festival-banner' ); ?>
			</option>
			<option value="none" <?php selected( $animation, 'none' ); ?>>
				<?php esc_html_e( 'None', 'festival-banner' ); ?>
			</option>
		</select>
	</p>

</div>