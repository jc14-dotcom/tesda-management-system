import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const normalizeTrim = (value) => (value ?? '').trim();
const collapseSpaces = (value) => normalizeTrim(value).replace(/\s+/g, ' ');
const sanitizeNameInput = (value) => collapseSpaces(value).replace(/[^A-Za-z\s'-]/g, '');
const isValidName = (value) => /^[A-Za-z][A-Za-z\s'-]*$/.test(value);
const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
const sanitizeContactNumber = (value) => (value ?? '').replace(/\D/g, '').slice(0, 11);

Alpine.data('sidebarLayout', () => ({
	desktopCollapsed: false,
	mobileOpen: false,
	init() {
		const stored = window.localStorage.getItem('sidebar-collapsed');

		if (stored === null) {
			this.desktopCollapsed = false;
			return;
		}

		this.desktopCollapsed = stored === 'true';
	},
	toggleDesktopSidebar() {
		this.desktopCollapsed = ! this.desktopCollapsed;
		window.localStorage.setItem('sidebar-collapsed', this.desktopCollapsed ? 'true' : 'false');
	},
	closeMobileSidebar() {
		this.mobileOpen = false;
	},
}));

Alpine.data('loadMoreList', ({ nextUrl, partialParam }) => ({
	nextUrl,
	partialParam,
	loading: false,
	async loadMore() {
		if (!this.nextUrl || this.loading) {
			return;
		}

		this.loading = true;

		try {
			const url = new URL(this.nextUrl, window.location.origin);
			url.searchParams.set(this.partialParam, '1');

			const response = await fetch(url.toString(), {
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
				},
			});

			if (!response.ok) {
				throw new Error('Failed to load more results.');
			}

			const payload = await response.json();
			if (payload?.html) {
				this.$refs.list.insertAdjacentHTML('beforeend', payload.html);
				if (window.Alpine?.initTree) {
					window.Alpine.initTree(this.$refs.list);
				}
			}
			this.nextUrl = payload?.nextUrl ?? null;
		} catch (error) {
			console.error(error);
		} finally {
			this.loading = false;
		}
	},
}));

Alpine.data('profilePhotoPreview', ({ initialUrl = null } = {}) => ({
	previewUrl: initialUrl,
	originalUrl: initialUrl,
	menuOpen: false,
	toggleMenu() {
		this.menuOpen = ! this.menuOpen;
	},
	triggerUpload() {
		this.menuOpen = false;
		this.$refs.profilePhotoInput?.click();
	},
	confirmRemovePhoto() {
		this.menuOpen = false;

		if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
			this.resetPreview();
			if (this.$refs.profilePhotoInput) {
				this.$refs.profilePhotoInput.value = '';
			}
			this.$dispatch('profile-photo-changed', { dirty: false });
			return;
		}

		if (!this.originalUrl) {
			return;
		}

		if (!window.confirm('Remove your profile picture?')) {
			return;
		}

		const form = window.document.getElementById('profile-photo-remove-form');
		if (form) {
			form.requestSubmit();
		}
	},
	selectFile(event) {
		const file = event.target.files?.[0];
		if (!file) {
			this.resetPreview();
			return;
		}

		if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
			URL.revokeObjectURL(this.previewUrl);
		}

		this.previewUrl = URL.createObjectURL(file);
		this.$dispatch('profile-photo-changed', { dirty: true });
	},
	resetPreview() {
		if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
			URL.revokeObjectURL(this.previewUrl);
		}

		this.previewUrl = this.originalUrl;
	},
}));

Alpine.data('profileInfoForm', ({ initialName = '', initialEmail = '' } = {}) => ({
	initialName,
	initialEmail,
	name: initialName,
	email: initialEmail,
	photoChanged: false,
	errors: {
		name: '',
		email: '',
	},
	touched: {
		name: false,
		email: false,
	},
	submitted: false,
	loading: false,
	init() {
		this.updateValidation();
	},
	isDirty() {
		return normalizeTrim(this.name) !== normalizeTrim(this.initialName)
			|| normalizeTrim(this.email) !== normalizeTrim(this.initialEmail)
			|| this.photoChanged;
	},
	validateName() {
		const value = normalizeTrim(this.name);
		if (!value) {
			return 'Name is required.';
		}
		if (value.length > 255) {
			return 'Name must be 255 characters or less.';
		}
		return '';
	},
	validateEmail() {
		const value = normalizeTrim(this.email);
		if (!value) {
			return 'Email is required.';
		}
		if (value.length > 255) {
			return 'Email must be 255 characters or less.';
		}
		if (!isValidEmail(value)) {
			return 'Enter a valid email address.';
		}
		return '';
	},
	updateValidation() {
		this.errors.name = this.validateName();
		this.errors.email = this.validateEmail();
	},
	hasErrors() {
		return Boolean(this.errors.name || this.errors.email);
	},
	showError(field) {
		return Boolean(this.errors[field]) && (this.touched[field] || this.submitted);
	},
	async submitForm(event) {
		this.submitted = true;
		this.updateValidation();
		if (!this.isDirty() || this.hasErrors()) {
			return;
		}
		if (this.loading) return;

		this.loading = true;
		const form = event.target;
		const url = form.action;
		// Use the form's method (usually POST) so multipart file uploads are parsed by PHP.
		// Keep the `_method` hidden input in the FormData for method override.
		const method = (form.getAttribute('method') || 'POST').toUpperCase();
		const fd = new FormData(form);

		try {
			const res = await fetch(url, {
				method: method.toUpperCase(),
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
					'Accept': 'application/json',
				},
				body: fd,
				credentials: 'same-origin',
			});

			if (res.ok) {
				const payload = await res.json().catch(() => null);
				window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'success', title: 'Saved', message: 'Profile saved.' } }));
				if (payload?.profile_photo_url) {
					window.dispatchEvent(new CustomEvent('profile-saved', { detail: { profile_photo_url: payload.profile_photo_url } }));
				}
				// update initial snapshot
				this.initialName = this.name;
				this.initialEmail = this.email;
				this.photoChanged = false;
			} else if (res.status === 422) {
				const payload = await res.json().catch(() => ({}));
				// map server validation errors to local errors
				if (payload?.errors) {
					Object.keys(this.errors).forEach(k => this.errors[k] = '');
					for (const [field, msgs] of Object.entries(payload.errors)) {
						// map common field names
						if (field === 'name' || field === 'user.name') this.errors.name = msgs.join(' ');
						if (field === 'email' || field === 'user.email') this.errors.email = msgs.join(' ');
					}
				}
				window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Validation error', message: payload?.message ?? 'Please review the highlighted fields.', fieldErrors: payload?.errors ?? {} } }));
			} else {
				const payload = await res.json().catch(() => null);
				window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Save failed', message: payload?.message ?? 'An unexpected error occurred.' } }));
			}
		} catch (err) {
			console.error(err);
			window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Save failed', message: err.message || 'Network error' } }));
		} finally {
			this.loading = false;
		}
	},
}));

Alpine.data('profileDetailsForm', ({
	initialFirstName = '',
	initialMiddleName = '',
	initialLastName = '',
	initialSuffix = '',
	initialDateOfBirth = '',
	initialGender = '',
	initialContactNumber = '',
	initialAddress = '',
	initialCompanyId = '',
	initialPositionRoles = [],
	initialEmploymentStatus = '',
	initialDateHired = '',
	initialTesdaRegistryNumber = '',
	initialQualificationTitle = '',
	initialRemarks = '',
} = {}) => ({
	initialFirstName,
	initialMiddleName,
	initialLastName,
	initialSuffix,
	initialGender,
	initialContactNumber,
	initialAddress,
	initialCompanyId,
	initialPositionRoles: Array.isArray(initialPositionRoles) ? initialPositionRoles : [],
	initialEmploymentStatus,
	initialDateOfBirth,
	initialDateHired,
	initialTesdaRegistryNumber,
	initialQualificationTitle,
	initialRemarks,
	firstName: initialFirstName,
	middleName: initialMiddleName,
	lastName: initialLastName,
	suffix: initialSuffix,
	dateOfBirth: initialDateOfBirth,
	gender: initialGender,
	contactNumber: initialContactNumber,
	address: initialAddress,
	companyId: initialCompanyId,
	positionRoles: Array.isArray(initialPositionRoles) ? initialPositionRoles : [],
	employmentStatus: initialEmploymentStatus,
	dateHired: initialDateHired,
	tesdaRegistryNumber: initialTesdaRegistryNumber,
	qualificationTitle: initialQualificationTitle,
	remarks: initialRemarks,
	errors: {
		firstName: '',
		middleName: '',
		lastName: '',
		suffix: '',
		dateOfBirth: '',
		gender: '',
		contactNumber: '',
		positionRoles: '',
		employmentStatus: '',
		companyId: '',
		address: '',
		dateHired: '',
		tesdaRegistryNumber: '',
		qualificationTitle: '',
		remarks: '',
	},
	touched: {
		firstName: false,
		middleName: false,
		lastName: false,
		suffix: false,
		dateOfBirth: false,
		gender: false,
		contactNumber: false,
		positionRoles: false,
		employmentStatus: false,
		companyId: false,
		address: false,
		dateHired: false,
		tesdaRegistryNumber: false,
		qualificationTitle: false,
		remarks: false,
	},
	submitted: false,
	init() {
		this.firstName = sanitizeNameInput(this.firstName);
		this.middleName = sanitizeNameInput(this.middleName);
		this.lastName = sanitizeNameInput(this.lastName);
		this.suffix = (this.suffix ?? '').toString().toLowerCase().replace(/\./g, '');
		this.gender = (this.gender ?? '').toString().toLowerCase();
		this.contactNumber = sanitizeContactNumber(this.contactNumber);
		this.employmentStatus = (this.employmentStatus ?? '').toString().trim().toLowerCase();
		this.positionRoles = Array.isArray(this.positionRoles)
			? this.positionRoles.map((role) => (role ?? '').toString().trim().toLowerCase())
			: [];
		this.initialPositionRoles = Array.isArray(this.initialPositionRoles)
			? this.initialPositionRoles.map((role) => (role ?? '').toString().trim().toLowerCase())
			: [];
		this.address = normalizeTrim(this.address);
		this.companyId = normalizeTrim(this.companyId);
		this.tesdaRegistryNumber = normalizeTrim(this.tesdaRegistryNumber);
		this.qualificationTitle = normalizeTrim(this.qualificationTitle);
		this.remarks = normalizeTrim(this.remarks);
		this.updateValidation();
	},
	arraysEqual(left, right) {
		if (!Array.isArray(left) || !Array.isArray(right) || left.length !== right.length) {
			return false;
		}

		return [...left].sort().join('|') === [...right].sort().join('|');
	},
	isDirty() {
		return normalizeTrim(this.firstName) !== normalizeTrim(this.initialFirstName)
			|| normalizeTrim(this.middleName) !== normalizeTrim(this.initialMiddleName)
			|| normalizeTrim(this.lastName) !== normalizeTrim(this.initialLastName)
			|| (this.suffix ?? '') !== (this.initialSuffix ?? '')
			|| (this.gender ?? '') !== (this.initialGender ?? '')
			|| normalizeTrim(this.contactNumber) !== normalizeTrim(this.initialContactNumber)
			|| normalizeTrim(this.address) !== normalizeTrim(this.initialAddress)
			|| normalizeTrim(this.companyId) !== normalizeTrim(this.initialCompanyId)
			|| !this.arraysEqual(this.positionRoles, this.initialPositionRoles)
			|| (this.employmentStatus ?? '') !== (this.initialEmploymentStatus ?? '')
			|| (this.dateOfBirth ?? '') !== (this.initialDateOfBirth ?? '')
			|| (this.dateHired ?? '') !== (this.initialDateHired ?? '')
			|| normalizeTrim(this.tesdaRegistryNumber) !== normalizeTrim(this.initialTesdaRegistryNumber)
			|| normalizeTrim(this.qualificationTitle) !== normalizeTrim(this.initialQualificationTitle)
			|| normalizeTrim(this.remarks) !== normalizeTrim(this.initialRemarks);
	},
	handleNameInput(field) {
		this[field] = sanitizeNameInput(this[field]);
		this.updateValidation();
	},
	handleContactInput() {
		this.contactNumber = sanitizeContactNumber(this.contactNumber);
		this.updateValidation();
	},
	validateName(value, { required }) {
		const trimmed = collapseSpaces(value);
		if (!trimmed) {
			return required ? 'This field is required.' : '';
		}
		if (trimmed.length > 255) {
			return 'Must be 255 characters or less.';
		}
		if (!isValidName(trimmed)) {
			return 'Use letters, spaces, hyphens, and apostrophes only.';
		}
		return '';
	},
	validateContactNumber() {
		const value = sanitizeContactNumber(this.contactNumber);
		if (!value) {
			return 'Contact number is required.';
		}
		if (!/^09\d{9}$/.test(value)) {
			return 'Use an 11-digit number starting with 09.';
		}
		return '';
	},
	validateEmploymentStatus() {
		const allowed = ['regular', 'probationary', 'contractual', 'part-time', 'internship', 'self-employed', 'unemployed'];
		if (!this.employmentStatus) {
			return 'Employment status is required.';
		}
		if (!allowed.includes(this.employmentStatus)) {
			return 'Select a valid employment status.';
		}
		return '';
	},
	validatePositionRoles() {
		if (!Array.isArray(this.positionRoles) || this.positionRoles.length === 0) {
			return 'Select at least one role.';
		}
		const allowed = ['trainer', 'assessor'];
		const invalid = this.positionRoles.find((role) => !allowed.includes(role));
		if (invalid) {
			return 'Select a valid role.';
		}
		return '';
	},
	validateRequiredValue(value, label, maxLength = null) {
		const trimmed = normalizeTrim(value);
		if (!trimmed) {
			return `${label} is required.`;
		}
		if (maxLength !== null && trimmed.length > maxLength) {
			return `Must be ${maxLength} characters or less.`;
		}
		return '';
	},
	validateMaxLength(value, max) {
		const trimmed = normalizeTrim(value);
		return trimmed.length > max ? `Must be ${max} characters or less.` : '';
	},
	updateValidation() {
		this.errors.firstName = this.validateName(this.firstName, { required: true });
		this.errors.middleName = this.validateName(this.middleName, { required: true });
		this.errors.lastName = this.validateName(this.lastName, { required: true });
		this.errors.dateOfBirth = this.validateRequiredValue(this.dateOfBirth, 'Date of birth');
		this.errors.gender = this.validateRequiredValue(this.gender, 'Sex');
		this.errors.contactNumber = this.validateContactNumber();
		this.errors.address = this.validateRequiredValue(this.address, 'Address', 500);
		this.errors.employmentStatus = this.validateEmploymentStatus();
		this.errors.positionRoles = this.validatePositionRoles();
		this.errors.companyId = this.validateRequiredValue(this.companyId, 'Company ID', 255);
		this.errors.dateHired = this.validateRequiredValue(this.dateHired, 'Date hired');
		this.errors.tesdaRegistryNumber = this.validateRequiredValue(this.tesdaRegistryNumber, 'TESDA registry number', 255);
		this.errors.qualificationTitle = this.validateMaxLength(this.qualificationTitle, 255);
		this.errors.remarks = this.validateMaxLength(this.remarks, 1000);
	},
	hasErrors() {
		return Object.values(this.errors).some(Boolean);
	},
	showError(field) {
		return Boolean(this.errors[field]) && (this.touched[field] || this.submitted);
	},
	loading: false,
	async submitForm(event) {
		this.submitted = true;
		this.updateValidation();
		if (this.hasErrors() || !this.isDirty()) {
			return;
		}
		if (this.loading) return;

		this.loading = true;
		const form = event.target;
		const url = form.action;
		// Use the form's method (usually POST) so multipart file uploads are parsed by PHP.
		const method = (form.getAttribute('method') || 'POST').toUpperCase();
		const fd = new FormData(form);

		try {
			const res = await fetch(url, {
				method: method.toUpperCase(),
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
					'Accept': 'application/json',
				},
				body: fd,
				credentials: 'same-origin',
			});

			if (res.ok) {
				const payload = await res.json().catch(() => null);
				window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'success', title: 'Saved', message: 'Profile details saved.' } }));
				// update initial snapshot
				this.initialFirstName = this.firstName;
				this.initialMiddleName = this.middleName;
				this.initialLastName = this.lastName;
				this.initialSuffix = this.suffix;
				this.initialGender = this.gender;
				this.initialContactNumber = this.contactNumber;
				this.initialAddress = this.address;
				this.initialCompanyId = this.companyId;
				this.initialPositionRoles = [...this.positionRoles];
				this.initialEmploymentStatus = this.employmentStatus;
				this.initialDateOfBirth = this.dateOfBirth;
				this.initialDateHired = this.dateHired;
				this.initialTesdaRegistryNumber = this.tesdaRegistryNumber;
				this.initialQualificationTitle = this.qualificationTitle;
				this.initialRemarks = this.remarks;
			} else if (res.status === 422) {
				const payload = await res.json().catch(() => ({}));
				if (payload?.errors) {
					// reset errors
					Object.keys(this.errors).forEach(k => this.errors[k] = '');
					for (const [field, msgs] of Object.entries(payload.errors)) {
						// convert server field names to local keys where needed
						const key = field.replace(/\./g, '');
						if (key in this.errors) {
							this.errors[key] = Array.isArray(msgs) ? msgs.join(' ') : String(msgs);
						}
					}
				}
				window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Validation error', message: payload?.message ?? 'Please review the highlighted fields.', fieldErrors: payload?.errors ?? {} } }));
			} else {
				const payload = await res.json().catch(() => null);
				window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Save failed', message: payload?.message ?? 'An unexpected error occurred.' } }));
			}
		} catch (err) {
			console.error(err);
			window.dispatchEvent(new CustomEvent('show-error-modal', { detail: { title: 'Save failed', message: err.message || 'Network error' } }));
		} finally {
			this.loading = false;
		}
	},
}));

// Notifications (toasts + modal) manager
Alpine.data('notifications', () => ({
	toasts: [],
	modal: { open: false, title: '', message: '', fieldErrors: {} },
	addToast(detail) {
		const id = Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
		const t = { id, type: detail.type || 'success', title: detail.title || '', message: detail.message || '', progress: 100, removing: false };
		this.toasts.push(t);
		// progress animation
		const interval = setInterval(() => {
			t.progress = Math.max(0, t.progress - 100 / (4 * 10));
		}, 100);
		// remove after 4s
		setTimeout(() => {
			clearInterval(interval);
			this.removeToast(id);
		}, 4200);
	},
	removeToast(id) {
		const idx = this.toasts.findIndex(x => x.id === id);
		if (idx === -1) return;
		this.toasts[idx].removing = true;
		setTimeout(() => {
			this.toasts.splice(idx, 1);
		}, 300);
	},
	openErrorModal(detail) {
		this.modal.title = detail.title || 'Error';
		this.modal.message = detail.message || '';
		this.modal.fieldErrors = detail.fieldErrors || {};
		this.modal.open = true;
	},
	closeModal() {
		this.modal.open = false;
	}
}));

Alpine.start();
