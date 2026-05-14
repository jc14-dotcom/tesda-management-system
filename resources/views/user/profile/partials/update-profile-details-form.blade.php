<section class="surface p-6 sm:p-8">
    @php
        $initialPositionRoles = old('position_roles', []);

        if (empty($initialPositionRoles) && filled($profile?->position_title)) {
            $initialPositionRoles = collect(preg_split('/\s*,\s*/', $profile->position_title, -1, PREG_SPLIT_NO_EMPTY))
                ->map(fn ($role) => strtolower(trim($role)))
                ->values()
                ->all();
        }
    @endphp

    <header class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent-soft">
            <svg class="h-5 w-5 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <div>
            <h2 class="text-base font-bold text-grayTheme-dark">
                {{ __('Personal and Employment Details') }}
            </h2>
            <p class="mt-0.5 text-sm text-grayTheme-medium">
                {{ __('Update your TESDA-related profile information.') }}
            </p>
        </div>
    </header>

    <form
        method="post"
        action="{{ route('account.profile.details') }}"
        class="mt-6 space-y-8"
        x-data='profileDetailsForm({
            initialFirstName: @json(old('first_name', $profile?->first_name)),
            initialMiddleName: @json(old('middle_name', $profile?->middle_name)),
            initialLastName: @json(old('last_name', $profile?->last_name)),
            initialSuffix: @json(old('suffix', $profile?->suffix)),
            initialDateOfBirth: @json(old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))),
            initialGender: @json(old('gender', $profile?->gender)),
            initialContactNumber: @json(old('contact_number', $profile?->contact_number)),
            initialAddress: @json(old('address', $profile?->address)),
            initialCompanyId: @json(old('company_id', $profile?->company_id)),
            initialPositionRoles: @json($initialPositionRoles),
            initialEmploymentStatus: @json(old('employment_status', $profile?->employment_status)),
            initialDateHired: @json(old('date_hired', $profile?->date_hired?->format('Y-m-d'))),
            initialTesdaRegistryNumber: @json(old('tesda_registry_number', $profile?->tesda_registry_number)),
            initialQualificationTitle: @json(old('qualification_title', $profile?->qualification_title)),
            initialRemarks: @json(old('remarks', $profile?->remarks))
        })'
        @submit.prevent="submitForm($event)"
    >
        @csrf
        @method('patch')

        <div class="space-y-4">
        <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary-soft">
                <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-grayTheme-dark">Personal Details</h3>
        </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" />
                    <x-text-input
                        id="first_name"
                        name="first_name"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('firstName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('first_name', $profile?->first_name)"
                        placeholder="Juan"
                        maxlength="255"
                        required
                        x-model.trim="firstName"
                        @input="touched.firstName = true; handleNameInput('firstName')"
                        @blur="touched.firstName = true; updateValidation()"
                        x-bind:aria-invalid="showError('firstName')"
                        x-bind:aria-describedby="showError('firstName') ? 'first-name-error' : null"
                    />
                    <p id="first-name-error" x-show="showError('firstName')" class="mt-2 text-sm text-red-600" x-text="errors.firstName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <div>
                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                    <x-text-input
                        id="middle_name"
                        name="middle_name"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('middleName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('middle_name', $profile?->middle_name)"
                        placeholder="Dela"
                        maxlength="255"
                        required
                        x-model.trim="middleName"
                        @input="touched.middleName = true; handleNameInput('middleName')"
                        @blur="touched.middleName = true; updateValidation()"
                        x-bind:aria-invalid="showError('middleName')"
                        x-bind:aria-describedby="showError('middleName') ? 'middle-name-error' : null"
                    />
                    <p id="middle-name-error" x-show="showError('middleName')" class="mt-2 text-sm text-red-600" x-text="errors.middleName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                </div>

                <div>
                    <x-input-label for="last_name" :value="__('Last Name')" />
                    <x-text-input
                        id="last_name"
                        name="last_name"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('lastName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('last_name', $profile?->last_name)"
                        placeholder="Cruz"
                        maxlength="255"
                        required
                        x-model.trim="lastName"
                        @input="touched.lastName = true; handleNameInput('lastName')"
                        @blur="touched.lastName = true; updateValidation()"
                        x-bind:aria-invalid="showError('lastName')"
                        x-bind:aria-describedby="showError('lastName') ? 'last-name-error' : null"
                    />
                    <p id="last-name-error" x-show="showError('lastName')" class="mt-2 text-sm text-red-600" x-text="errors.lastName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div>
                    <x-input-label for="suffix" :value="__('Suffix')" />
                    <select
                        id="suffix"
                        name="suffix"
                        class="mt-1 form-input"
                        x-model="suffix"
                        @change="updateValidation()"
                    >
                        <option value="">None</option>
                        <option value="jr">Jr.</option>
                        <option value="sr">Sr.</option>
                        <option value="ii">II</option>
                        <option value="iii">III</option>
                        <option value="iv">IV</option>
                        <option value="v">V</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
                </div>

                <div>
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                    <x-text-input
                        id="date_of_birth"
                        name="date_of_birth"
                        type="date"
                        class="mt-1 block w-full"
                        x-bind:class="showError('dateOfBirth') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))"
                        x-model="dateOfBirth"
                        required
                        @change="touched.dateOfBirth = true; updateValidation()"
                        x-bind:aria-invalid="showError('dateOfBirth')"
                        x-bind:aria-describedby="showError('dateOfBirth') ? 'date-of-birth-error' : null"
                    />
                    <p id="date-of-birth-error" x-show="showError('dateOfBirth')" class="mt-2 text-sm text-red-600" x-text="errors.dateOfBirth"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Sex')" />
                    <select
                        id="gender"
                        name="gender"
                        class="mt-1 form-input"
                        x-bind:class="showError('gender') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        x-model="gender"
                        required
                        @change="touched.gender = true; updateValidation()"
                        x-bind:aria-invalid="showError('gender')"
                        x-bind:aria-describedby="showError('gender') ? 'gender-error' : null"
                    >
                        <option value="">Select sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <p id="gender-error" x-show="showError('gender')" class="mt-2 text-sm text-red-600" x-text="errors.gender"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                </div>

                <div>
                    <x-input-label for="contact_number" :value="__('Contact Number')" />
                    <x-text-input
                        id="contact_number"
                        name="contact_number"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('contactNumber') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('contact_number', $profile?->contact_number)"
                        placeholder="09123456789"
                        inputmode="numeric"
                        autocomplete="tel"
                        maxlength="11"
                        pattern="^09\d{9}$"
                        required
                        x-model="contactNumber"
                        @input="touched.contactNumber = true; handleContactInput()"
                        @blur="touched.contactNumber = true; updateValidation()"
                        x-bind:aria-invalid="showError('contactNumber')"
                        x-bind:aria-describedby="showError('contactNumber') ? 'contact-number-error' : null"
                    />
                    <p id="contact-number-error" x-show="showError('contactNumber')" class="mt-2 text-sm text-red-600" x-text="errors.contactNumber"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label class="text-base sm:text-[1.05rem]" for="address" :value="__('Address')" />
            <textarea
                id="address"
                name="address"
                class="mt-2 form-input min-h-[8rem]"
                x-bind:class="showError('address') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                rows="3"
                placeholder="Enter complete address"
                required
                x-model.trim="address"
                @input="touched.address = true; updateValidation()"
                @blur="touched.address = true; updateValidation()"
                x-bind:aria-invalid="showError('address')"
                x-bind:aria-describedby="showError('address') ? 'address-error' : null"
            >{{ old('address', $profile?->address) }}</textarea>
            <p class="mt-2 text-base leading-relaxed text-grayTheme-medium">Street name, building number, subdivision, barangay, city, and other necessary address details.</p>
            <p id="address-error" x-show="showError('address')" class="mt-2 text-sm text-red-600" x-text="errors.address"></p>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="space-y-4">
        <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
                <svg class="h-4 w-4 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-grayTheme-dark">Employment Details</h3>
        </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="employment_status" :value="__('Employment Status')" />
                    <select
                        id="employment_status"
                        name="employment_status"
                        class="mt-1 form-input"
                        x-bind:class="showError('employmentStatus') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        x-model="employmentStatus"
                        required
                        @change="updateValidation()"
                        @blur="touched.employmentStatus = true; updateValidation()"
                        x-bind:aria-invalid="showError('employmentStatus')"
                        x-bind:aria-describedby="showError('employmentStatus') ? 'employment-status-error' : null"
                    >
                        <option value="">Select status</option>
                        <option value="regular">Regular</option>
                        <option value="probationary">Probationary</option>
                        <option value="contractual">Contractual</option>
                        <option value="part-time">Part-time</option>
                        <option value="internship">Internship</option>
                        <option value="self-employed">Self-employed</option>
                        <option value="unemployed">Unemployed</option>
                    </select>
                    <p id="employment-status-error" x-show="showError('employmentStatus')" class="mt-2 text-sm text-red-600" x-text="errors.employmentStatus"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('employment_status')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label class="text-base sm:text-[1.05rem]" :value="__('Position / Job Role')" />
                    <p class="mt-1 text-base leading-relaxed text-grayTheme-medium">Select one role or both roles if the user is assigned as both a trainer and an assessor.</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <label class="flex items-center gap-3 rounded-lg border border-grayTheme-border bg-white px-4 py-3 text-sm font-medium text-grayTheme-dark shadow-sm transition hover:border-primary/50">
                            <input
                                type="checkbox"
                                name="position_roles[]"
                                value="trainer"
                                class="rounded border-grayTheme-border text-primary focus:ring-primary/30"
                                x-model="positionRoles"
                                @change="touched.positionRoles = true; updateValidation()"
                            />
                            <span>Trainer</span>
                        </label>

                        <label class="flex items-center gap-3 rounded-lg border border-grayTheme-border bg-white px-4 py-3 text-sm font-medium text-grayTheme-dark shadow-sm transition hover:border-primary/50">
                            <input
                                type="checkbox"
                                name="position_roles[]"
                                value="assessor"
                                class="rounded border-grayTheme-border text-primary focus:ring-primary/30"
                                x-model="positionRoles"
                                @change="touched.positionRoles = true; updateValidation()"
                            />
                            <span>Assessor</span>
                        </label>

                    </div>
                    <p id="position-roles-error" x-show="showError('positionRoles')" class="mt-2 text-sm text-red-600" x-text="errors.positionRoles"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('position_roles')" />
                </div>

                <div>
                    <x-input-label for="company_id" :value="__('Company ID')" />
                    <x-text-input
                        id="company_id"
                        name="company_id"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('companyId') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('company_id', $profile?->company_id)"
                        placeholder="EMP-00123"
                        maxlength="255"
                        required
                        x-model.trim="companyId"
                        @input="touched.companyId = true; updateValidation()"
                        @blur="touched.companyId = true; updateValidation()"
                        x-bind:aria-invalid="showError('companyId')"
                        x-bind:aria-describedby="showError('companyId') ? 'company-id-error' : null"
                    />
                    <p id="company-id-error" x-show="showError('companyId')" class="mt-2 text-sm text-red-600" x-text="errors.companyId"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
                </div>

                <div>
                    <x-input-label for="date_hired" :value="__('Date Hired')" />
                    <x-text-input
                        id="date_hired"
                        name="date_hired"
                        type="date"
                        class="mt-1 block w-full"
                        x-bind:class="showError('dateHired') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('date_hired', $profile?->date_hired?->format('Y-m-d'))"
                        x-model="dateHired"
                        required
                        @change="touched.dateHired = true; updateValidation()"
                        x-bind:aria-invalid="showError('dateHired')"
                        x-bind:aria-describedby="showError('dateHired') ? 'date-hired-error' : null"
                    />
                    <p id="date-hired-error" x-show="showError('dateHired')" class="mt-2 text-sm text-red-600" x-text="errors.dateHired"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('date_hired')" />
                </div>

                <div>
                    <x-input-label for="tesda_registry_number" :value="__('TESDA Registry Number')" />
                    <x-text-input
                        id="tesda_registry_number"
                        name="tesda_registry_number"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('tesdaRegistryNumber') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('tesda_registry_number', $profile?->tesda_registry_number)"
                        placeholder="TESDA-2026-0001"
                        maxlength="255"
                        required
                        x-model.trim="tesdaRegistryNumber"
                        @input="touched.tesdaRegistryNumber = true; updateValidation()"
                        @blur="touched.tesdaRegistryNumber = true; updateValidation()"
                        x-bind:aria-invalid="showError('tesdaRegistryNumber')"
                        x-bind:aria-describedby="showError('tesdaRegistryNumber') ? 'tesda-registry-error' : null"
                    />
                    <p id="tesda-registry-error" x-show="showError('tesdaRegistryNumber')" class="mt-2 text-sm text-red-600" x-text="errors.tesdaRegistryNumber"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('tesda_registry_number')" />
                </div>

                <div>
                    <x-input-label for="qualification_title" :value="__('Qualification Title')" />
                    <x-text-input
                        id="qualification_title"
                        name="qualification_title"
                        type="text"
                        class="mt-1 block w-full"
                        x-bind:class="showError('qualificationTitle') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                        :value="old('qualification_title', $profile?->qualification_title)"
                        placeholder="Automotive Servicing NC II"
                        maxlength="255"
                        x-model.trim="qualificationTitle"
                        @input="updateValidation()"
                        @blur="touched.qualificationTitle = true; updateValidation()"
                        x-bind:aria-invalid="showError('qualificationTitle')"
                        x-bind:aria-describedby="showError('qualificationTitle') ? 'qualification-title-error' : null"
                    />
                    <p id="qualification-title-error" x-show="showError('qualificationTitle')" class="mt-2 text-sm text-red-600" x-text="errors.qualificationTitle"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('qualification_title')" />
                </div>
            </div>
        </div>

        <div class="rounded-card border border-grayTheme-border bg-grayTheme-light px-4 py-4">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0 text-grayTheme-medium" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <span class="text-sm font-semibold text-grayTheme-dark">Account Status</span>
                </div>
                @php
                    $status = $profile?->status ?? 'active';
                    $statusClasses = match(strtolower($status)) {
                        'active'   => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                        'inactive' => 'bg-red-100 text-red-700 ring-1 ring-red-200',
                        'pending'  => 'bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200',
                        default    => 'bg-grayTheme-border text-grayTheme-medium ring-1 ring-grayTheme-border',
                    };
                @endphp
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusClasses }}">{{ ucfirst($status) }}</span>
            </div>
            <p class="mt-2 text-xs text-grayTheme-medium">This is managed by admin or HR and cannot be edited directly.</p>
        </div>

        <div>
            <x-input-label for="remarks" :value="__('Remarks')" />
            <textarea
                id="remarks"
                name="remarks"
                class="mt-1 form-input"
                x-bind:class="showError('remarks') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                rows="3"
                placeholder="Add any additional notes"
                x-model.trim="remarks"
                @input="updateValidation()"
                @blur="touched.remarks = true; updateValidation()"
                x-bind:aria-invalid="showError('remarks')"
                x-bind:aria-describedby="showError('remarks') ? 'remarks-error' : null"
            >{{ old('remarks', $profile?->remarks) }}</textarea>
            <p id="remarks-error" x-show="showError('remarks')" class="mt-2 text-sm text-red-600" x-text="errors.remarks"></p>
            <x-input-error class="mt-2" :messages="$errors->get('remarks')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="gap-2" x-bind:disabled="loading || !isDirty() || hasErrors()" x-bind:class="(loading || !isDirty() || hasErrors()) ? 'opacity-60 cursor-not-allowed' : ''">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-details-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-green-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
