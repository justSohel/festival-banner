<?php
/**
 * Schedule Meta Box Template
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
$start_date     = get_post_meta( $post->ID, '_fb_start_date', true );
$end_date       = get_post_meta( $post->ID, '_fb_end_date', true );
$is_recurring   = get_post_meta( $post->ID, '_fb_is_recurring', true );
$recurring_year = get_post_meta( $post->ID, '_fb_recurring_year', true );

// Check if schedule is enabled.
$enable_schedule = ! empty( $start_date ) || ! empty( $end_date );

// Default recurring year to current year.
$recurring_year = $recurring_year ? $recurring_year : (int) date( 'Y' );

// Format dates for display.
$start_date_value = $start_date ? date( 'Y-m-d', strtotime( $start_date ) ) : '';
$start_time_value = $start_date ? date( 'H:i', strtotime( $start_date ) ) : '00:00';
$end_date_value   = $end_date ? date( 'Y-m-d', strtotime( $end_date ) ) : '';
$end_time_value   = $end_date ? date( 'H:i', strtotime( $end_date ) ) : '23:59';
?>

<div class="festival-banner-meta-box">
	<p>
		<label>
			<input 
				type="checkbox" 
				id="fb_enable_schedule" 
				name="fb_enable_schedule" 
				value="1"
				<?php checked( $enable_schedule, true ); ?>
			>
			<strong><?php esc_html_e( 'Schedule this banner', 'festival-banner' ); ?></strong>
		</label>
	</p>

	<div id="fb-schedule-fields" style="<?php echo $enable_schedule ? '' : 'display:none;'; ?>">
		
		<!-- Start Date & Time -->
		<p style="margin-top: 15px;">
			<label for="fb_start_date">
				<strong><?php esc_html_e( 'Start Date & Time:', 'festival-banner' ); ?></strong>
			</label>
			<input 
				type="date" 
				id="fb_start_date" 
				name="fb_start_date_temp" 
				value="<?php echo esc_attr( $start_date_value ); ?>"
				style="width: 100%; margin-top: 5px;"
			>
			<input 
				type="time" 
				id="fb_start_time" 
				name="fb_start_time_temp" 
				value="<?php echo esc_attr( $start_time_value ); ?>"
				style="width: 100%; margin-top: 5px;"
			>
			<!-- Hidden field to combine date + time -->
			<input type="hidden" id="fb_start_date_combined" name="fb_start_date" value="<?php echo esc_attr( $start_date ); ?>">
		</p>

		<!-- End Date & Time -->
		<p style="margin-top: 15px;">
			<label for="fb_end_date">
				<strong><?php esc_html_e( 'End Date & Time:', 'festival-banner' ); ?></strong>
			</label>
			<input 
				type="date" 
				id="fb_end_date" 
				name="fb_end_date_temp" 
				value="<?php echo esc_attr( $end_date_value ); ?>"
				style="width: 100%; margin-top: 5px;"
			>
			<input 
				type="time" 
				id="fb_end_time" 
				name="fb_end_time_temp" 
				value="<?php echo esc_attr( $end_time_value ); ?>"
				style="width: 100%; margin-top: 5px;"
			>
			<!-- Hidden field to combine date + time -->
			<input type="hidden" id="fb_end_date_combined" name="fb_end_date" value="<?php echo esc_attr( $end_date ); ?>">
		</p>

		<!-- Recurring -->
		<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
			<label>
				<input 
					type="checkbox" 
					id="fb_is_recurring" 
					name="fb_is_recurring" 
					value="1"
					<?php checked( $is_recurring, true ); ?>
				>
				<strong><?php esc_html_e( 'Recurring yearly', 'festival-banner' ); ?></strong>
			</label>
		</p>

		<p class="description" style="margin-left: 25px;">
			<?php esc_html_e( '💡 You can create next year\'s version after this banner expires.', 'festival-banner' ); ?>
		</p>

		<!-- Hidden recurring year field -->
		<input type="hidden" name="fb_recurring_year" value="<?php echo esc_attr( $recurring_year ); ?>">

		<!-- Timezone info -->
		<p style="margin-top: 15px; padding: 10px; background: #f0f0f1; border-left: 3px solid #2271b1;">
			<strong><?php esc_html_e( 'Timezone:', 'festival-banner' ); ?></strong>
			<?php
			$timezone_string = get_option( 'timezone_string' );
			if ( empty( $timezone_string ) ) {
				$timezone_string = sprintf( 'UTC%s', get_option( 'gmt_offset' ) );
			}
			echo esc_html( $timezone_string );
			?>
			<br>
			<span class="description">
				<?php
				printf(
					/* translators: %s: Settings page URL */
					esc_html__( 'From WordPress Settings. %s', 'festival-banner' ),
					'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '" target="_blank">' . esc_html__( 'Change timezone', 'festival-banner' ) . '</a>'
				);
				?>
			</span>
		</p>
	</div>

	<p class="description" style="margin-top: 15px;">
		<strong><?php esc_html_e( 'Note:', 'festival-banner' ); ?></strong>
		<?php esc_html_e( 'Leave unchecked to run indefinitely.', 'festival-banner' ); ?>
	</p>
</div>

<script>
// Combine date and time fields before form submission
(function() {
	if (typeof jQuery !== 'undefined') {
		jQuery(document).ready(function($) {
			// Function to combine date and time
			function combineDatetime(dateId, timeId, hiddenId) {
				var date = $('#' + dateId).val();
				var time = $('#' + timeId).val();
				if (date && time) {
					$('#' + hiddenId).val(date + ' ' + time + ':00');
				}
			}

			// Update on change
			$('#fb_start_date, #fb_start_time').on('change', function() {
				combineDatetime('fb_start_date', 'fb_start_time', 'fb_start_date_combined');
			});

			$('#fb_end_date, #fb_end_time').on('change', function() {
				combineDatetime('fb_end_date', 'fb_end_time', 'fb_end_date_combined');
			});

			// Initial combination
			combineDatetime('fb_start_date', 'fb_start_time', 'fb_start_date_combined');
			combineDatetime('fb_end_date', 'fb_end_time', 'fb_end_date_combined');
		});
	}
})();
</script>