<?php
/**
 * Bottom Bar Banner Template
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

<div class="<?php echo esc_attr( $classes ); ?>" 
     id="fb-banner-<?php echo esc_attr( $banner->ID ); ?>"
     data-banner-id="<?php echo esc_attr( $banner->ID ); ?>"
     data-position="bottom_bar"
     data-animation="<?php echo esc_attr( $banner->animation ); ?>"
     style="<?php echo esc_attr( $styles ); ?>">
    
    <div class="fb-banner__container">
        
        <div class="fb-banner__content">
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
        
        <?php if ( $banner->is_dismissible ) : ?>
            <button class="fb-banner__dismiss" 
                    aria-label="<?php esc_attr_e( 'Dismiss banner', 'festival-banner' ); ?>"
                    data-banner-id="<?php echo esc_attr( $banner->ID ); ?>">
                <span aria-hidden="true">&times;</span>
            </button>
        <?php endif; ?>
        
    </div>
    
</div>