/**
 * Festival Banner Admin Scripts
 *
 * @package Festival_Banner
 * @since   1.0.0
 */

(function($) {
	'use strict';

	/**
	 * Main admin object
	 */
	const FestivalBannerAdmin = {

		/**
		 * Initialize all admin functionality
		 */
		init: function() {
			this.initColorPickers();
			this.initConditionalFields();
			this.initScheduleToggle();
			this.initDateTimeCombiner();
			this.initFormValidation();
		},

		/**
		 * Initialize WordPress color pickers
		 */
		initColorPickers: function() {
			if ($.fn.wpColorPicker) {
				$('.fb-color-picker').wpColorPicker({
					change: function(event, ui) {
						// Trigger change event for validation
						$(this).trigger('colorchange', ui.color.toString());
					}
				});
			}
		},

		/**
		 * Initialize conditional field visibility
		 */
		initConditionalFields: function() {
			// Position-based conditional fields
			this.handlePositionChange();
			$('.fb-position-radio').on('change', this.handlePositionChange);

			// Display type conditional fields
			this.handleDisplayTypeChange();
			$('.fb-display-type-radio').on('change', this.handleDisplayTypeChange);
		},

		/**
		 * Handle position radio button changes
		 */
		handlePositionChange: function() {
			const selectedPosition = $('.fb-position-radio:checked').val();

			// Hide all conditional fields first
			$('.fb-floating-fields, .fb-side-fields, .fb-modal-fields').hide();

			// Show relevant fields based on position
			switch(selectedPosition) {
				case 'floating':
					$('.fb-floating-fields').slideDown(200);
					break;
				case 'side':
					$('.fb-side-fields').slideDown(200);
					break;
				case 'modal':
					$('.fb-modal-fields').slideDown(200);
					// Force dismissible checkbox to be checked and disabled
					$('#fb_is_dismissible').prop('checked', true).prop('disabled', true);
					break;
				default:
					// Enable dismissible for non-modal positions
					$('#fb_is_dismissible').prop('disabled', false);
					break;
			}
		},

		/**
		 * Handle display type radio button changes
		 */
		handleDisplayTypeChange: function() {
			const selectedType = $('.fb-display-type-radio:checked').val();

			// Hide specific pages field
			$('.fb-specific-pages-field').hide();

			// Show if specific pages selected
			if (selectedType === 'specific_pages') {
				$('.fb-specific-pages-field').slideDown(200);
			}
		},

		/**
		 * Initialize schedule toggle functionality
		 */
		initScheduleToggle: function() {
			const $scheduleCheckbox = $('#fb_enable_schedule');
			const $scheduleFields = $('#fb-schedule-fields');

			// Toggle schedule fields
			$scheduleCheckbox.on('change', function() {
				if ($(this).is(':checked')) {
					$scheduleFields.slideDown(200);
				} else {
					$scheduleFields.slideUp(200);
				}
			});
		},

		/**
		 * Initialize date/time field combiner
		 */
		initDateTimeCombiner: function() {
			const self = this;

			// Combine date and time on change
			$('#fb_start_date, #fb_start_time').on('change', function() {
				self.combineDatetime('fb_start_date', 'fb_start_time', 'fb_start_date_combined');
			});

			$('#fb_end_date, #fb_end_time').on('change', function() {
				self.combineDatetime('fb_end_date', 'fb_end_time', 'fb_end_date_combined');
			});

			// Initial combination on page load
			self.combineDatetime('fb_start_date', 'fb_start_time', 'fb_start_date_combined');
			self.combineDatetime('fb_end_date', 'fb_end_time', 'fb_end_date_combined');
		},

		/**
		 * Combine date and time fields
		 */
		combineDatetime: function(dateId, timeId, hiddenId) {
			const date = $('#' + dateId).val();
			const time = $('#' + timeId).val();
			
			if (date && time) {
				const datetime = date + ' ' + time + ':00';
				$('#' + hiddenId).val(datetime);
			} else if (date) {
				$('#' + hiddenId).val(date + ' 00:00:00');
			}
		},

		/**
		 * Initialize form validation
		 */
		initFormValidation: function() {
			const self = this;

			// Validate on form submit
			$('#post').on('submit', function(e) {
				let isValid = true;
				const errors = [];

				// Clear previous error states
				$('.festival-banner-error').removeClass('festival-banner-error');
				$('.festival-banner-error-message').remove();

				// Validate CTA URL if CTA text is present
				const ctaText = $('#fb_cta_text').val().trim();
				const ctaUrl = $('#fb_cta_url').val().trim();

				if (ctaText && !ctaUrl) {
					self.showFieldError('#fb_cta_url', 'Please enter a URL for the button.');
					errors.push('CTA URL required when button text is provided');
					isValid = false;
				}

				// Validate URL format
				if (ctaUrl && !self.isValidUrl(ctaUrl)) {
					self.showFieldError('#fb_cta_url', 'Please enter a valid URL.');
					errors.push('Invalid URL format');
					isValid = false;
				}

				// Validate date range
				if ($('#fb_enable_schedule').is(':checked')) {
					const startDate = new Date($('#fb_start_date_combined').val());
					const endDate = new Date($('#fb_end_date_combined').val());

					if (startDate && endDate && endDate < startDate) {
						self.showFieldError('#fb_end_date', 'End date must be after start date.');
						errors.push('Invalid date range');
						isValid = false;
					}
				}

				// Validate modal delay range
				if ($('.fb-position-radio:checked').val() === 'modal') {
					const delay = parseInt($('#fb_modal_delay').val());
					if (delay < 0 || delay > 60) {
						self.showFieldError('#fb_modal_delay', 'Delay must be between 0 and 60 seconds.');
						errors.push('Invalid modal delay');
						isValid = false;
					}
				}

				// If validation fails, prevent submit and show notice
				if (!isValid) {
					e.preventDefault();
					self.showValidationNotice(errors);
					
					// Scroll to first error
					const $firstError = $('.festival-banner-error').first();
					if ($firstError.length) {
						$('html, body').animate({
							scrollTop: $firstError.offset().top - 100
						}, 500);
					}
				}

				return isValid;
			});

			// Real-time URL validation
			let urlTimeout;
			$('#fb_cta_url').on('input', function() {
				const $field = $(this);
				clearTimeout(urlTimeout);
				
				urlTimeout = setTimeout(function() {
					const url = $field.val().trim();
					if (url && !self.isValidUrl(url)) {
						$field.addClass('festival-banner-error');
					} else {
						$field.removeClass('festival-banner-error');
					}
				}, 500);
			});
		},

		/**
		 * Validate URL format
		 */
		isValidUrl: function(url) {
			// Allow relative URLs starting with /
			if (url.startsWith('/')) {
				return true;
			}

			// Validate full URLs
			try {
				new URL(url);
				return true;
			} catch (e) {
				return false;
			}
		},

		/**
		 * Show field validation error
		 */
		showFieldError: function(fieldId, message) {
			const $field = $(fieldId);
			$field.addClass('festival-banner-error');
			
			// Add error message
			const $errorMsg = $('<span class="festival-banner-error-message">' + message + '</span>');
			$field.after($errorMsg);
		},

		/**
		 * Show validation notice at top of page
		 */
		showValidationNotice: function(errors) {
			// Remove existing notices
			$('.festival-banner-validation-notice').remove();

			// Create notice
			const $notice = $('<div class="notice notice-error is-dismissible festival-banner-validation-notice"><p><strong>Validation Error:</strong> Please fix the following issues before publishing:</p><ul></ul></div>');
			
			// Add errors to list
			const $list = $notice.find('ul');
			errors.forEach(function(error) {
				$list.append('<li>' + error + '</li>');
			});

			// Insert notice
			$('.wrap h1').after($notice);

			// Make dismissible
			$(document).trigger('wp-updates-notice-added');
		}
	};

	/**
	 * Initialize on document ready
	 */
	$(document).ready(function() {
		FestivalBannerAdmin.init();
	});

})(jQuery);