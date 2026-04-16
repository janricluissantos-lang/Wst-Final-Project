const imageFileInput = document.getElementById('image_file');
const previewWrap = document.getElementById('previewWrap');
const imagePreview = document.getElementById('imagePreview');

if (imageFileInput) {
	imageFileInput.addEventListener('change', function () {
		const file = this.files[0];
		if (file && file.type.startsWith('image/')) {
			const reader = new FileReader();
			reader.onload = function (e) {
				imagePreview.src = e.target.result;
				previewWrap.style.display = 'block';
			};
			reader.readAsDataURL(file);
		} else {
			previewWrap.style.display = 'none';
		}
	});
}

// -----------------------------------------------
// Character counter for the description field
// Shows how many characters are typed
// -----------------------------------------------
const descField = document.getElementById('description');

if (descField) {
	// Create a small counter element below the textarea
	const counter = document.createElement('p');
	counter.className = 'char-counter';
	counter.textContent = '0 characters';
	descField.parentNode.appendChild(counter);

	descField.addEventListener('input', function () {
		const len = this.value.length;
		counter.textContent = len + ' character' + (len !== 1 ? 's' : '');
	});
}

// -----------------------------------------------
// Form submit loading state
// Disables the submit button and shows a loading
// message while the form is being submitted
// -----------------------------------------------
const addForm = document.getElementById('addForm');
const submitBtn = addForm ? addForm.querySelector('.btn-submit') : null;

if (addForm && submitBtn) {
	addForm.addEventListener('submit', function () {
		submitBtn.textContent = 'Publishing…';
		submitBtn.disabled = true;
		submitBtn.style.opacity = '0.7';
	});
}