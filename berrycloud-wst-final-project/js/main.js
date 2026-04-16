// -----------------------------------------------
// Mobile menu toggle (hamburger open/close)
// -----------------------------------------------
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');

if (menuToggle && mobileMenu) {
	menuToggle.addEventListener('click', function () {
		// Toggle the "open" class to show/hide the dropdown
		mobileMenu.classList.toggle('open');

		// Animate the hamburger lines into an X when open
		const spans = menuToggle.querySelectorAll('span');
		const isOpen = mobileMenu.classList.contains('open');

		if (isOpen) {
			spans[0].style.transform = 'translateY(7px) rotate(45deg)';
			spans[1].style.opacity = '0';
			spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
		} else {
			spans[0].style.transform = '';
			spans[1].style.opacity = '';
			spans[2].style.transform = '';
		}
	});
}

// -----------------------------------------------
// Delete confirmation modal
// -----------------------------------------------
let _deleteHref = null;

function confirmDelete(href) {
	_deleteHref = href;
	const overlay = document.getElementById('deleteModal');
	if (overlay) overlay.classList.add('open');
	return false; // always prevent default link navigation
}

// Wire up modal buttons once DOM is ready
document.addEventListener('DOMContentLoaded', function () {
	const overlay = document.getElementById('deleteModal');
	const btnCancel = document.getElementById('modalCancel');
	const btnConf = document.getElementById('modalConfirm');

	if (btnCancel) {
		btnCancel.addEventListener('click', function () {
			overlay.classList.remove('open');
			_deleteHref = null;
		});
	}

	if (btnConf) {
		btnConf.addEventListener('click', function () {
			if (_deleteHref) window.location.href = _deleteHref;
		});
	}

	// Close on overlay backdrop click
	if (overlay) {
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) {
				overlay.classList.remove('open');
				_deleteHref = null;
			}
		});
	}

	// Close on Escape key
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && overlay && overlay.classList.contains('open')) {
			overlay.classList.remove('open');
			_deleteHref = null;
		}
	});
});

// -----------------------------------------------
// Flash message auto-dismiss (if a success/error
// alert is present on the page, hide it after 4s)
// -----------------------------------------------
const alert = document.querySelector('.alert');

if (alert) {
	setTimeout(function () {
		// Fade out the alert smoothly
		alert.style.transition = 'opacity 0.5s ease';
		alert.style.opacity = '0';

		// Remove from DOM after fade completes
		setTimeout(function () {
			alert.remove();
		}, 500);
	}, 4000); // Wait 4 seconds before fading
}