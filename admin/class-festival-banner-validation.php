<?php
/**
 * Validation functionality for the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 */

/**
 * Validation functionality.
 *
 * Validates and sanitizes all input data.
 *
 * @package    Festival_Banner
 * @subpackage Festival_Banner/admin
 * @author     Your Name <email@example.com>
 */
class Festival_Banner_Validation {

	/**
	 * Validate datetime format.
	 *
	 * @since  1.0.0
	 * @param  string $datetime The datetime string to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_datetime( $datetime ) {
		if ( empty( $datetime ) ) {
			return false;
		}

		// Try to parse the datetime.
		$timestamp = strtotime( $datetime );
		if ( false === $timestamp ) {
			return false;
		}

		// Check if it's a valid date.
		$date_parts = explode( ' ', $datetime );
		if ( count( $date_parts ) < 1 ) {
			return false;
		}

		// Validate date part.
		$date = $date_parts[0];
		$date_components = explode( '-', $date );
		
		if ( count( $date_components ) !== 3 ) {
			return false;
		}

		list( $year, $month, $day ) = $date_components;
		
		return checkdate( (int) $month, (int) $day, (int) $year );
	}

	/**
	 * Validate date range.
	 *
	 * Ensures end date is after start date.
	 *
	 * @since  1.0.0
	 * @param  string $start_date The start date.
	 * @param  string $end_date   The end date.
	 * @return bool True if valid range, false otherwise.
	 */
	public static function validate_date_range( $start_date, $end_date ) {
		if ( empty( $start_date ) || empty( $end_date ) ) {
			return true; // Empty dates are allowed.
		}

		$start_timestamp = strtotime( $start_date );
		$end_timestamp   = strtotime( $end_date );

		if ( false === $start_timestamp || false === $end_timestamp ) {
			return false;
		}

		return $end_timestamp >= $start_timestamp;
	}

	/**
	 * Validate URL.
	 *
	 * @since  1.0.0
	 * @param  string $url The URL to validate.
	 * @return bool True if valid URL, false otherwise.
	 */
	public static function validate_url( $url ) {
		if ( empty( $url ) ) {
			return true; // Empty URLs are allowed.
		}

		// Check if it's a valid URL format.
		if ( false === filter_var( $url, FILTER_VALIDATE_URL ) ) {
			// Could be a relative URL, check if it starts with /.
			if ( 0 === strpos( $url, '/' ) ) {
				return true;
			}
			return false;
		}

		return true;
	}

	/**
	 * Validate hex color.
	 *
	 * @since  1.0.0
	 * @param  string $color The color to validate.
	 * @return bool True if valid hex color, false otherwise.
	 */
	public static function validate_hex_color( $color ) {
		if ( empty( $color ) ) {
			return false;
		}

		// Remove # if present.
		$color = ltrim( $color, '#' );

		// Check if it's a valid hex color (3 or 6 characters).
		if ( ! preg_match( '/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate enum value.
	 *
	 * Checks if value is in allowed list.
	 *
	 * @since  1.0.0
	 * @param  string $value   The value to validate.
	 * @param  array  $allowed Array of allowed values.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_enum( $value, $allowed ) {
		return in_array( $value, $allowed, true );
	}

	/**
	 * Validate integer range.
	 *
	 * @since  1.0.0
	 * @param  int $value The value to validate.
	 * @param  int $min   Minimum value.
	 * @param  int $max   Maximum value.
	 * @return bool True if within range, false otherwise.
	 */
	public static function validate_int_range( $value, $min, $max ) {
		$value = (int) $value;
		return ( $value >= $min && $value <= $max );
	}

	/**
	 * Sanitize content (HTML).
	 *
	 * @since  1.0.0
	 * @param  string $content The content to sanitize.
	 * @return string Sanitized content.
	 */
	public static function sanitize_content( $content ) {
		return wp_kses_post( $content );
	}

	/**
	 * Sanitize URL.
	 *
	 * @since  1.0.0
	 * @param  string $url The URL to sanitize.
	 * @return string Sanitized URL.
	 */
	public static function sanitize_url( $url ) {
		return esc_url_raw( $url );
	}

	/**
	 * Sanitize hex color.
	 *
	 * @since  1.0.0
	 * @param  string $color The color to sanitize.
	 * @return string|null Sanitized color or null if invalid.
	 */
	public static function sanitize_color( $color ) {
		$color = sanitize_hex_color( $color );
		return $color ? $color : null;
	}

	/**
	 * Sanitize array of integers.
	 *
	 * @since  1.0.0
	 * @param  array $array The array to sanitize.
	 * @return array Sanitized array.
	 */
	public static function sanitize_int_array( $array ) {
		if ( ! is_array( $array ) ) {
			return array();
		}

		return array_map( 'absint', $array );
	}

	/**
	 * Get validation error message.
	 *
	 * @since  1.0.0
	 * @param  string $field The field name.
	 * @param  string $type  The error type.
	 * @return string Error message.
	 */
	public static function get_error_message( $field, $type ) {
		$messages = array(
			'required'     => sprintf(
				/* translators: %s: Field name */
				__( '%s is required.', 'festival-banner' ),
				$field
			),
			'invalid_url'  => sprintf(
				/* translators: %s: Field name */
				__( '%s must be a valid URL.', 'festival-banner' ),
				$field
			),
			'invalid_date' => sprintf(
				/* translators: %s: Field name */
				__( '%s must be a valid date.', 'festival-banner' ),
				$field
			),
			'invalid_range' => sprintf(
				/* translators: %s: Field name */
				__( 'End date must be after start date.', 'festival-banner' )
			),
			'invalid_color' => sprintf(
				/* translators: %s: Field name */
				__( '%s must be a valid hex color.', 'festival-banner' ),
				$field
			),
		);

		return isset( $messages[ $type ] ) ? $messages[ $type ] : __( 'Invalid input.', 'festival-banner' );
	}
}