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
                @if(auth()->user()->hasRole('admin'))
                    {{ __('Personal Details') }}
                @else
                    {{ __('Personal & TESDA Registration Details') }}
                @endif
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
            initialPositionRoles: @json($initialPositionRoles),
            initialTrainerQualificationTitles: @json(old('trainer_qualification_titles', $profile?->trainer_qualification_titles ?? [])),
            initialAssessorQualificationTitles: @json(old('assessor_qualification_titles', $profile?->assessor_qualification_titles ?? [])),
            isAdmin: @json(auth()->user()->hasRole('admin'))
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
                    <x-input-label for="first_name" :value="__('First Name')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <x-text-input
                            id="first_name"
                            name="first_name"
                            type="text"
                            class="block w-full pl-9"
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
                    </div>
                    <p id="first-name-error" x-show="showError('firstName')" class="mt-2 text-sm text-red-600" x-text="errors.firstName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                </div>

                <div>
                    <x-input-label for="middle_name" :value="__('Middle Name')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <x-text-input
                            id="middle_name"
                            name="middle_name"
                            type="text"
                            class="block w-full pl-9"
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
                    </div>
                    <p id="middle-name-error" x-show="showError('middleName')" class="mt-2 text-sm text-red-600" x-text="errors.middleName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                </div>

                <div>
                    <x-input-label for="last_name" :value="__('Last Name')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <x-text-input
                            id="last_name"
                            name="last_name"
                            type="text"
                            class="block w-full pl-9"
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
                    </div>
                    <p id="last-name-error" x-show="showError('lastName')" class="mt-2 text-sm text-red-600" x-text="errors.lastName"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                </div>

                <div>
                    <x-input-label for="suffix" :value="__('Suffix')" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </span>
                        <select
                            id="suffix"
                            name="suffix"
                            class="form-input pl-9"
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
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
                </div>

                <div>
                    <x-input-label for="date_of_birth" :value="__('Date of Birth')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <x-text-input
                            id="date_of_birth"
                            name="date_of_birth"
                            type="date"
                            class="block w-full pl-9"
                            x-bind:class="showError('dateOfBirth') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                            :value="old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))"
                            x-model="dateOfBirth"
                            required
                            @change="touched.dateOfBirth = true; updateValidation()"
                            x-bind:aria-invalid="showError('dateOfBirth')"
                            x-bind:aria-describedby="showError('dateOfBirth') ? 'date-of-birth-error' : null"
                        />
                    </div>
                    <p id="date-of-birth-error" x-show="showError('dateOfBirth')" class="mt-2 text-sm text-red-600" x-text="errors.dateOfBirth"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Sex')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <select
                            id="gender"
                            name="gender"
                            class="form-input pl-9"
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
                    </div>
                    <p id="gender-error" x-show="showError('gender')" class="mt-2 text-sm text-red-600" x-text="errors.gender"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                </div>

                <div>
                    <x-input-label for="contact_number" :value="__('Contact Number')" :required="true" />
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </span>
                        <x-text-input
                            id="contact_number"
                            name="contact_number"
                            type="text"
                            class="block w-full pl-9"
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
                    </div>
                    <p id="contact-number-error" x-show="showError('contactNumber')" class="mt-2 text-sm text-red-600" x-text="errors.contactNumber"></p>
                    <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                </div>
            </div>
        </div>

        <div>
            <x-input-label class="text-base sm:text-[1.05rem]" for="address" :value="__('Address')" />
            <div class="relative mt-2">
                <span class="pointer-events-none absolute left-3 top-2.5 text-grayTheme-medium">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <textarea
                    id="address"
                    name="address"
                    class="form-input min-h-[8rem] pl-9 pt-2"
                    x-bind:class="showError('address') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    rows="3"
                    placeholder="Enter complete address"
                    x-model.trim="address"
                    @input="touched.address = true; updateValidation()"
                    @blur="touched.address = true; updateValidation()"
                    x-bind:aria-invalid="showError('address')"
                    x-bind:aria-describedby="showError('address') ? 'address-error' : null"
                >{{ old('address', $profile?->address) }}</textarea>
            </div>
            <p class="mt-2 text-sm leading-relaxed text-grayTheme-medium">Street name, building number, subdivision, barangay, city, and other necessary address details.</p>
            <p id="address-error" x-show="showError('address')" class="mt-2 text-sm text-red-600" x-text="errors.address"></p>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        @unless(auth()->user()->hasRole('admin'))
        <div class="space-y-4">
        <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
                <svg class="h-4 w-4 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-grayTheme-dark">TESDA Role &amp; Qualification Details</h3>
        </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <x-input-label class="text-base sm:text-[1.05rem]" :value="__('Position / Job Role')" :required="true" />
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

                <div class="md:col-span-2">
                    <div class="grid gap-6 md:grid-cols-2">
                        {{-- Trainer Qualification Titles --}}
                        <div x-show="positionRoles.includes('trainer')" x-transition.opacity.duration.200ms>
                            <x-input-label :value="__('Trainer Qualification Title(s)')" />
                            <p class="mt-0.5 text-xs text-grayTheme-medium">TESDA qualification title(s) as a trainer.</p>
                            <div class="mt-2 space-y-2">
                                <template x-for="(title, index) in trainerQualificationTitles" :key="'trainer-' + index">
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                            </span>
                                            <input
                                                type="text"
                                                :name="'trainer_qualification_titles[' + index + ']'"
                                                class="form-input block w-full pl-9"
                                                placeholder="e.g. Automotive Servicing NC II"
                                                maxlength="255"
                                                x-model.trim="trainerQualificationTitles[index]"
                                                @input="updateValidation()"
                                                @blur="touched.trainerQualificationTitles = true; updateValidation()"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeTrainerTitle(index)"
                                            x-show="index > 0"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50"
                                            title="Remove"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addTrainerTitle()" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-primary transition hover:text-primary/80">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Add another title
                            </button>
                            <p x-show="showError('trainerQualificationTitles')" class="mt-2 text-sm text-red-600" x-text="errors.trainerQualificationTitles"></p>
                            <x-input-error class="mt-2" :messages="$errors->get('trainer_qualification_titles')" />
                        </div>

                        {{-- Assessor Qualification Titles --}}
                        <div x-show="positionRoles.includes('assessor')" x-transition.opacity.duration.200ms>
                            <x-input-label :value="__('Assessor Qualification Title(s)')" />
                            <p class="mt-0.5 text-xs text-grayTheme-medium">TESDA qualification title(s) as an assessor.</p>
                            <div class="mt-2 space-y-2">
                                <template x-for="(title, index) in assessorQualificationTitles" :key="'assessor-' + index">
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                            </span>
                                            <input
                                                type="text"
                                                :name="'assessor_qualification_titles[' + index + ']'"
                                                class="form-input block w-full pl-9"
                                                placeholder="e.g. Automotive Servicing NC II"
                                                maxlength="255"
                                                x-model.trim="assessorQualificationTitles[index]"
                                                @input="updateValidation()"
                                                @blur="touched.assessorQualificationTitles = true; updateValidation()"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeAssessorTitle(index)"
                                            x-show="index > 0"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50"
                                            title="Remove"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addAssessorTitle()" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-primary transition hover:text-primary/80">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                Add another title
                            </button>
                            <p x-show="showError('assessorQualificationTitles')" class="mt-2 text-sm text-red-600" x-text="errors.assessorQualificationTitles"></p>
                            <x-input-error class="mt-2" :messages="$errors->get('assessor_qualification_titles')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endunless

        @unless(auth()->user()->hasRole('admin'))
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
        @endunless

        <div class="flex items-center gap-4">
            <x-primary-button class="gap-2" disabled x-bind:disabled="loading || !isDirty() || hasErrors()" x-bind:class="(loading || !isDirty() || hasErrors()) ? 'opacity-60 cursor-not-allowed' : ''">
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
