<?php
/**
 * Modal Banner Template
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/public/partials
 * @var        object $banner Banner data object
 * @var        string $classes CSS classes
 * @var        string $styles Inline styles
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="fb-modal-backdrop" 
     id="fb-modal-backdrop-<?php echo esc_attr( $banner->ID ); ?>"
     data-banner-id="<?php echo esc_attr( $banner->ID ); ?>"
     data-delay="<?php echo esc_attr( $banner->modal_delay ); ?>"
     style="display: none;">
    
    <div class="<?php echo esc_attr( $classes ); ?>" 
         id="fb-banner-<?php echo esc_attr( $banner->ID ); ?>"
         data-banner-id="<?php echo esc_attr( $banner->ID ); ?>"
         data-position="modal"
         data-animation="<?php echo esc_attr( $banner->animation ); ?>"
         role="dialog"
         aria-modal="true"
         aria-labelledby="fb-banner-title-<?php echo esc_attr( $banner->ID ); ?>"
         style="<?php echo esc_attr( $styles ); ?>">
        
        <button class="fb-banner__close" 
                aria-label="<?php esc_attr_e( 'Close modal', 'festival-banner' ); ?>"
                data-banner-id="<?php echo esc_attr( $banner->ID ); ?>">
            <span aria-hidden="true">&times;</span>
        </button>
        
        <div class="fb-banner__content" id="fb-banner-title-<?php echo esc_attr( $banner->ID ); ?>">
            <?php echo wp_kses_post( $banner->content ); ?>
        </div>
        
        <?php if ( ! empty( $banner->cta_text ) && ! empty( $banner->cta_url ) ) : ?>
            <a href="<?php echo esc_url( $banner->cta_url ); ?>" 
               class="fb-banner__cta"
               <?php echo $banner->cta_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
               style="background-color: <?php echo esc_attr( $banner->cta_bg_color ); ?>; color: <?php echo esc_attr( $banner->cta_text_color ); ?>;">
                <?php echo esc_html( $banner->cta_text ); ?>
            </a>
        <?php endif; ?>
        
    </div>
    
</div>