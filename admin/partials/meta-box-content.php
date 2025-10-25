<?php
/**
 * Content Meta Box Template
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
$content = get_post_meta( $post->ID, '_fb_content', true );
?>

<div class="festival-banner-meta-box">
	<p class="description">
		<?php esc_html_e( 'Add your banner content here. Use headings, formatting, and emojis to make it eye-catching!', 'festival-banner' ); ?>
	</p>

	<?php
	// WYSIWYG Editor.
	$editor_settings = array(
		'textarea_name' => 'fb_content',
		'textarea_rows' => 10,
		'media_buttons' => true,
		'teeny'         => false,
		'quicktags'     => true,
		'tinymce'       => array(
			'toolbar1' => 'formatselect,bold,italic,underline,strikethrough,bullist,numlist,link,unlink,forecolor,undo,redo',
			'toolbar2' => '',
		),
	);

	wp_editor( $content, 'fb_content', $editor_settings );
	?>

	<p class="description" style="margin-top: 10px;">
		<strong><?php esc_html_e( 'Tip:', 'festival-banner' ); ?></strong>
		<?php esc_html_e( 'Keep content concise for better visibility. Use emojis (🎉 🎄 🎁) to grab attention.', 'festival-banner' ); ?>
	</p>
</div>