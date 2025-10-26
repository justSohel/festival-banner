/**
 * Festival Banner Public Scripts
 *
 * @package Festival_Banner
 * @since   1.0.0
 */

(function() {
	'use strict';

	/**
	 * Main public object
	 */
	const FestivalBanner = {

		dismissedKey: 'festival_banner_dismissed',
		scrollThreshold: 100,

		/**
		 * Initialize all functionality
		 */
		init: function() {
			// Wait for DOM to be ready
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', () => {
					this.setup();
				});
			} else {
				this.setup();
			}
		},

		/**
		 * Setup all features
		 */
		setup: function() {
			this.loadDismissedBanners();
			this.hideDismissedBanners();
			this.initDismissButtons();
			this.initTopBarSticky();
			this.initBottomBarPush();
			this.initModalDelays();
		},

		/**
		 * Load dismissed banners from sessionStorage
		 */
		loadDismissedBanners: function() {
			try {
				const dismissed = sessionStorage.getItem(this.dismissedKey);
				this.dismissedBanners = dismissed ? JSON.parse(dismissed) : [];
			} catch (e) {
				console.warn('Festival Banner: sessionStorage not available');
				this.dismissedBanners = [];
			}
		},

		/**
		 * Hide banners that were dismissed in this session
		 */
		hideDismissedBanners: function() {
			if (!this.dismissedBanners || this.dismissedBanners.length === 0) {
				return;
			}

			this.dismissedBanners.forEach(bannerId => {
				const banner = document.querySelector(`[data-banner-id="${bannerId}"]`);
				if (banner) {
					banner.style.display = 'none';
				}
			});
		},

		/**
		 * Initialize dismiss button listeners
		 */
		initDismissButtons: function() {
			const dismissButtons = document.querySelectorAll('.fb-banner__dismiss, .fb-banner__close');
			
			dismissButtons.forEach(button => {
				button.addEventListener('click', (e) => {
					e.preventDefault();
					const bannerId = button.getAttribute('data-banner-id');
					this.dismissBanner(bannerId);
				});
			});

			// Also dismiss modal when clicking backdrop
			const modalBackdrops = document.querySelectorAll('.fb-modal-backdrop');
			modalBackdrops.forEach(backdrop => {
				// Only dismiss on backdrop click, not modal content
				backdrop.addEventListener('click', (e) => {
					if (e.target === backdrop) {
						// Note: Based on planning, backdrop click should NOT dismiss
						// User must click X button
						// Keeping this comment for clarity
					}
				});
			});
		},

		/**
		 * Dismiss a banner
		 */
		dismissBanner: function(bannerId) {
			// Find banner element
			const banner = document.querySelector(`[data-banner-id="${bannerId}"]`);
			if (!banner) {
				return;
			}

			// Add dismissing class for animation
			banner.classList.add('fb-banner--dismissing');

			// Wait for animation, then remove
			setTimeout(() => {
				// For modal, hide backdrop
				if (banner.classList.contains('fb-modal-backdrop')) {
					banner.style.display = 'none';
				} else if (banner.closest('.fb-modal-backdrop')) {
					banner.closest('.fb-modal-backdrop').style.display = 'none';
				} else {
					banner.style.display = 'none';
				}

				// If top bar, remove body margin
				if (banner.classList.contains('fb-banner--top-bar')) {
					document.body.style.marginTop = '0';
				}

				// Save to sessionStorage
				this.saveDismissed(bannerId);
			}, 300);
		},

		/**
		 * Save dismissed banner to sessionStorage
		 */
		saveDismissed: function(bannerId) {
			try {
				if (!this.dismissedBanners.includes(bannerId)) {
					this.dismissedBanners.push(bannerId);
					sessionStorage.setItem(this.dismissedKey, JSON.stringify(this.dismissedBanners));
				}
			} catch (e) {
				console.warn('Festival Banner: Could not save to sessionStorage');
			}
		},

		/**
		 * Initialize top bar sticky behavior
		 */
		initTopBarSticky: function() {
			const topBanners = document.querySelectorAll('.fb-banner--top-bar');
			
			if (topBanners.length === 0) {
				return;
			}

			topBanners.forEach(banner => {
				// Get initial position
				const initialTop = banner.offsetTop;
				const bannerHeight = banner.offsetHeight;

				// Add margin to body to prevent content jump
				if (!banner.classList.contains('fb-sticky')) {
					document.body.style.marginTop = bannerHeight + 'px';
				}

				// Scroll handler
				const handleScroll = () => {
					const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

					if (scrollTop > this.scrollThreshold) {
						if (!banner.classList.contains('fb-sticky')) {
							banner.classList.add('fb-sticky');
						}
					} else {
						banner.classList.remove('fb-sticky');
					}
				};

				// Listen to scroll
				window.addEventListener('scroll', handleScroll, { passive: true });
				
				// Initial check
				handleScroll();
			});
		},

		/**
		 * Initialize bottom bar push content behavior
		 */
		initBottomBarPush: function() {
			const bottomBanners = document.querySelectorAll('.fb-banner--bottom-bar');
			
			if (bottomBanners.length === 0) {
				return;
			}

			bottomBanners.forEach(banner => {
				const checkPosition = () => {
					const windowHeight = window.innerHeight;
					const documentHeight = document.documentElement.scrollHeight;
					const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
					const scrollBottom = scrollTop + windowHeight;

					// Check if user is near bottom (within 200px)
					if (documentHeight - scrollBottom < 200) {
						if (!banner.classList.contains('fb-push-content')) {
							banner.classList.add('fb-push-content');
							// Add padding to body
							const bannerHeight = banner.offsetHeight;
							document.body.style.paddingBottom = bannerHeight + 'px';
						}
					} else {
						banner.classList.remove('fb-push-content');
						document.body.style.paddingBottom = '0';
					}
				};

				// Listen to scroll
				window.addEventListener('scroll', checkPosition, { passive: true });
				
				// Initial check
				checkPosition();
			});
		},

		/**
		 * Initialize modal delays
		 */
		initModalDelays: function() {
			const modalBackdrops = document.querySelectorAll('.fb-modal-backdrop');
			
			modalBackdrops.forEach(backdrop => {
				const bannerId = backdrop.getAttribute('data-banner-id');
				const delay = parseInt(backdrop.getAttribute('data-delay')) || 3;

				// Check if this banner was dismissed
				if (this.dismissedBanners && this.dismissedBanners.includes(bannerId)) {
					return;
				}

				// Show after delay
				setTimeout(() => {
					this.showModal(backdrop);
				}, delay * 1000);
			});
		},

		/**
		 * Show modal
		 */
		showModal: function(backdrop) {
			// Prevent body scroll
			document.body.style.overflow = 'hidden';

			// Show modal
			backdrop.style.display = 'flex';

			// Focus trap (keep focus within modal)
			this.trapFocus(backdrop);

			// ESC key to close
			const closeOnEsc = (e) => {
				if (e.key === 'Escape') {
					const bannerId = backdrop.getAttribute('data-banner-id');
					this.dismissBanner(bannerId);
					document.body.style.overflow = '';
					document.removeEventListener('keydown', closeOnEsc);
				}
			};
			document.addEventListener('keydown', closeOnEsc);
		},

		/**
		 * Trap focus within modal
		 */
		trapFocus: function(modal) {
			const focusableElements = modal.querySelectorAll(
				'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
			);
			
			if (focusableElements.length === 0) {
				return;
			}

			const firstElement = focusableElements[0];
			const lastElement = focusableElements[focusableElements.length - 1];

			// Focus first element
			firstElement.focus();

			// Trap focus
			modal.addEventListener('keydown', (e) => {
				if (e.key !== 'Tab') {
					return;
				}

				if (e.shiftKey) {
					// Shift + Tab
					if (document.activeElement === firstElement) {
						e.preventDefault();
						lastElement.focus();
					}
				} else {
					// Tab
					if (document.activeElement === lastElement) {
						e.preventDefault();
						firstElement.focus();
					}
				}
			});
		}
	};

	/**
	 * Initialize when script loads
	 */
	FestivalBanner.init();

})();