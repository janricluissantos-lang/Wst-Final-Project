const _toastIcons = { success: '✓', error: '✕', info: 'i', warning: '!' };

function showToast(type, title, msg, duration = 4000) {
	let container = document.getElementById('berry-toast-container');
	if (!container) {
		container = document.createElement('div');
		container.id = 'berry-toast-container';
		document.body.appendChild(container);
	}

	const t = document.createElement('div');
	t.className = 'berry-toast ' + type;
	t.innerHTML = `
		<div class="bt-icon">${_toastIcons[type]}</div>
		<div class="bt-body">
			<p class="bt-title">${title}</p>
			<p class="bt-msg">${msg}</p>
		</div>
		<button class="bt-close" onclick="dismissToast(this.closest('.berry-toast'))">×</button>
		<div class="bt-progress" style="animation-duration:${duration}ms"></div>
	`;
	container.appendChild(t);
	setTimeout(() => dismissToast(t), duration);
}

function dismissToast(t) {
	if (!t || t.classList.contains('hiding')) return;
	t.classList.add('hiding');
	setTimeout(() => t.remove(), 300);
}