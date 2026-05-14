import {
    normalizeTrim,
    collapseSpaces,
    sanitizeNameInput,
    isValidName,
    isValidEmail,
    sanitizeContactNumber,
} from './utils.js';

/**
 * Profile-related Alpine components (profile page only).
 * Grouped here so they are only part of the bundle parsed on pages that need them.
 */
export function registerProfileComponents(Alpine) {
    // ─── Profile photo preview & upload ───────────────────────────────────────
    Alpine.data('profilePhotoPreview', ({ initialUrl = null } = {}) => ({
        previewUrl: initialUrl,
        originalUrl: initialUrl,
        menuOpen: false,

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
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

            if (!this.originalUrl) return;

            if (!window.confirm('Remove your profile picture?')) return;

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

    // ─── Account info form (name + email + photo) ─────────────────────────────
    Alpine.data(
        'profileInfoForm',
        ({ initialName = '', initialEmail = '' } = {}) => ({
            initialName,
            initialEmail,
            name: initialName,
            email: initialEmail,
            photoChanged: false,
            errors: { name: '', email: '' },
            touched: { name: false, email: false },
            submitted: false,
            loading: false,

            init() {
                this.updateValidation();
            },

            isDirty() {
                return (
                    normalizeTrim(this.name) !== normalizeTrim(this.initialName) ||
                    normalizeTrim(this.email) !== normalizeTrim(this.initialEmail) ||
                    this.photoChanged
                );
            },

            validateName() {
                const value = normalizeTrim(this.name);
                if (!value) return 'Name is required.';
                if (value.length > 255) return 'Name must be 255 characters or less.';
                return '';
            },

            validateEmail() {
                const value = normalizeTrim(this.email);
                if (!value) return 'Email is required.';
                if (value.length > 255) return 'Email must be 255 characters or less.';
                if (!isValidEmail(value)) return 'Enter a valid email address.';
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
                if (!this.isDirty() || this.hasErrors() || this.loading) return;

                this.loading = true;
                const form = event.target;
                const fd = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: (form.getAttribute('method') || 'POST').toUpperCase(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            Accept: 'application/json',
                        },
                        body: fd,
                        credentials: 'same-origin',
                    });

                    if (res.ok) {
                        const payload = await res.json().catch(() => null);
                        window.dispatchEvent(
                            new CustomEvent('show-toast', {
                                detail: { type: 'success', title: 'Saved', message: 'Profile saved.' },
                            }),
                        );
                        if (payload?.profile_photo_url) {
                            window.dispatchEvent(
                                new CustomEvent('profile-saved', {
                                    detail: { profile_photo_url: payload.profile_photo_url },
                                }),
                            );
                        }
                        this.initialName = this.name;
                        this.initialEmail = this.email;
                        this.photoChanged = false;
                    } else if (res.status === 422) {
                        const payload = await res.json().catch(() => ({}));
                        if (payload?.errors) {
                            Object.keys(this.errors).forEach((k) => (this.errors[k] = ''));
                            for (const [field, msgs] of Object.entries(payload.errors)) {
                                if (field === 'name' || field === 'user.name')
                                    this.errors.name = msgs.join(' ');
                                if (field === 'email' || field === 'user.email')
                                    this.errors.email = msgs.join(' ');
                            }
                        }
                        window.dispatchEvent(
                            new CustomEvent('show-error-modal', {
                                detail: {
                                    title: 'Validation error',
                                    message: payload?.message ?? 'Please review the highlighted fields.',
                                    fieldErrors: payload?.errors ?? {},
                                },
                            }),
                        );
                    } else {
                        const payload = await res.json().catch(() => null);
                        window.dispatchEvent(
                            new CustomEvent('show-error-modal', {
                                detail: {
                                    title: 'Save failed',
                                    message: payload?.message ?? 'An unexpected error occurred.',
                                },
                            }),
                        );
                    }
                } catch (err) {
                    console.error(err);
                    window.dispatchEvent(
                        new CustomEvent('show-error-modal', {
                            detail: { title: 'Save failed', message: err.message || 'Network error' },
                        }),
                    );
                } finally {
                    this.loading = false;
                }
            },
        }),
    );

    // ─── Employment / personal details form ───────────────────────────────────
    Alpine.data(
        'profileDetailsForm',
        ({
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
            initialPositionRoles: Array.isArray(initialPositionRoles)
                ? initialPositionRoles
                : [],
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
            loading: false,

            init() {
                this.firstName = sanitizeNameInput(this.firstName);
                this.middleName = sanitizeNameInput(this.middleName);
                this.lastName = sanitizeNameInput(this.lastName);
                this.suffix = (this.suffix ?? '').toString().toLowerCase().replace(/\./g, '');
                this.gender = (this.gender ?? '').toString().toLowerCase();
                this.contactNumber = sanitizeContactNumber(this.contactNumber);
                this.employmentStatus = (this.employmentStatus ?? '')
                    .toString()
                    .trim()
                    .toLowerCase();
                this.positionRoles = Array.isArray(this.positionRoles)
                    ? this.positionRoles.map((r) => (r ?? '').toString().trim().toLowerCase())
                    : [];
                this.initialPositionRoles = Array.isArray(this.initialPositionRoles)
                    ? this.initialPositionRoles.map((r) =>
                          (r ?? '').toString().trim().toLowerCase(),
                      )
                    : [];
                this.address = normalizeTrim(this.address);
                this.companyId = normalizeTrim(this.companyId);
                this.tesdaRegistryNumber = normalizeTrim(this.tesdaRegistryNumber);
                this.qualificationTitle = normalizeTrim(this.qualificationTitle);
                this.remarks = normalizeTrim(this.remarks);
                this.updateValidation();
            },

            arraysEqual(left, right) {
                if (
                    !Array.isArray(left) ||
                    !Array.isArray(right) ||
                    left.length !== right.length
                )
                    return false;
                return [...left].sort().join('|') === [...right].sort().join('|');
            },

            isDirty() {
                return (
                    normalizeTrim(this.firstName) !==
                        normalizeTrim(this.initialFirstName) ||
                    normalizeTrim(this.middleName) !==
                        normalizeTrim(this.initialMiddleName) ||
                    normalizeTrim(this.lastName) !== normalizeTrim(this.initialLastName) ||
                    (this.suffix ?? '') !== (this.initialSuffix ?? '') ||
                    (this.gender ?? '') !== (this.initialGender ?? '') ||
                    normalizeTrim(this.contactNumber) !==
                        normalizeTrim(this.initialContactNumber) ||
                    normalizeTrim(this.address) !== normalizeTrim(this.initialAddress) ||
                    normalizeTrim(this.companyId) !== normalizeTrim(this.initialCompanyId) ||
                    !this.arraysEqual(this.positionRoles, this.initialPositionRoles) ||
                    (this.employmentStatus ?? '') !== (this.initialEmploymentStatus ?? '') ||
                    (this.dateOfBirth ?? '') !== (this.initialDateOfBirth ?? '') ||
                    (this.dateHired ?? '') !== (this.initialDateHired ?? '') ||
                    normalizeTrim(this.tesdaRegistryNumber) !==
                        normalizeTrim(this.initialTesdaRegistryNumber) ||
                    normalizeTrim(this.qualificationTitle) !==
                        normalizeTrim(this.initialQualificationTitle) ||
                    normalizeTrim(this.remarks) !== normalizeTrim(this.initialRemarks)
                );
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
                if (!trimmed) return required ? 'This field is required.' : '';
                if (trimmed.length > 255) return 'Must be 255 characters or less.';
                if (!isValidName(trimmed))
                    return 'Use letters, spaces, hyphens, and apostrophes only.';
                return '';
            },

            validateContactNumber() {
                const value = sanitizeContactNumber(this.contactNumber);
                if (!value) return 'Contact number is required.';
                if (!/^09\d{9}$/.test(value))
                    return 'Use an 11-digit number starting with 09.';
                return '';
            },

            validateEmploymentStatus() {
                const allowed = [
                    'regular', 'probationary', 'contractual', 'part-time',
                    'internship', 'self-employed', 'unemployed',
                ];
                if (!this.employmentStatus) return 'Employment status is required.';
                if (!allowed.includes(this.employmentStatus))
                    return 'Select a valid employment status.';
                return '';
            },

            validatePositionRoles() {
                if (!Array.isArray(this.positionRoles) || this.positionRoles.length === 0)
                    return 'Select at least one role.';
                const allowed = ['trainer', 'assessor'];
                if (this.positionRoles.find((r) => !allowed.includes(r)))
                    return 'Select a valid role.';
                return '';
            },

            validateRequiredValue(value, label, maxLength = null) {
                const trimmed = normalizeTrim(value);
                if (!trimmed) return `${label} is required.`;
                if (maxLength !== null && trimmed.length > maxLength)
                    return `Must be ${maxLength} characters or less.`;
                return '';
            },

            validateMaxLength(value, max) {
                const trimmed = normalizeTrim(value);
                return trimmed.length > max ? `Must be ${max} characters or less.` : '';
            },

            updateValidation() {
                this.errors.firstName = this.validateName(this.firstName, { required: true });
                this.errors.middleName = this.validateName(this.middleName, {
                    required: true,
                });
                this.errors.lastName = this.validateName(this.lastName, { required: true });
                this.errors.dateOfBirth = this.validateRequiredValue(
                    this.dateOfBirth,
                    'Date of birth',
                );
                this.errors.gender = this.validateRequiredValue(this.gender, 'Sex');
                this.errors.contactNumber = this.validateContactNumber();
                this.errors.address = this.validateRequiredValue(this.address, 'Address', 500);
                this.errors.employmentStatus = this.validateEmploymentStatus();
                this.errors.positionRoles = this.validatePositionRoles();
                this.errors.companyId = this.validateRequiredValue(
                    this.companyId,
                    'Company ID',
                    255,
                );
                this.errors.dateHired = this.validateRequiredValue(
                    this.dateHired,
                    'Date hired',
                );
                this.errors.tesdaRegistryNumber = this.validateRequiredValue(
                    this.tesdaRegistryNumber,
                    'TESDA registry number',
                    255,
                );
                this.errors.qualificationTitle = this.validateMaxLength(
                    this.qualificationTitle,
                    255,
                );
                this.errors.remarks = this.validateMaxLength(this.remarks, 1000);
            },

            hasErrors() {
                return Object.values(this.errors).some(Boolean);
            },

            showError(field) {
                return Boolean(this.errors[field]) && (this.touched[field] || this.submitted);
            },

            async submitForm(event) {
                this.submitted = true;
                this.updateValidation();
                if (this.hasErrors() || !this.isDirty() || this.loading) return;

                this.loading = true;
                const form = event.target;
                const fd = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: (form.getAttribute('method') || 'POST').toUpperCase(),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            Accept: 'application/json',
                        },
                        body: fd,
                        credentials: 'same-origin',
                    });

                    if (res.ok) {
                        window.dispatchEvent(
                            new CustomEvent('show-toast', {
                                detail: {
                                    type: 'success',
                                    title: 'Saved',
                                    message: 'Profile details saved.',
                                },
                            }),
                        );
                        // Sync initial snapshot to current values
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
                            Object.keys(this.errors).forEach((k) => (this.errors[k] = ''));
                            for (const [field, msgs] of Object.entries(payload.errors)) {
                                const key = field.replace(/\./g, '');
                                if (key in this.errors) {
                                    this.errors[key] = Array.isArray(msgs)
                                        ? msgs.join(' ')
                                        : String(msgs);
                                }
                            }
                        }
                        window.dispatchEvent(
                            new CustomEvent('show-error-modal', {
                                detail: {
                                    title: 'Validation error',
                                    message:
                                        payload?.message ??
                                        'Please review the highlighted fields.',
                                    fieldErrors: payload?.errors ?? {},
                                },
                            }),
                        );
                    } else {
                        const payload = await res.json().catch(() => null);
                        window.dispatchEvent(
                            new CustomEvent('show-error-modal', {
                                detail: {
                                    title: 'Save failed',
                                    message:
                                        payload?.message ?? 'An unexpected error occurred.',
                                },
                            }),
                        );
                    }
                } catch (err) {
                    console.error(err);
                    window.dispatchEvent(
                        new CustomEvent('show-error-modal', {
                            detail: {
                                title: 'Save failed',
                                message: err.message || 'Network error',
                            },
                        }),
                    );
                } finally {
                    this.loading = false;
                }
            },
        }),
    );
}
