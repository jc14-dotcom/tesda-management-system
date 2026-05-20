<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Alcatt Portal — Complete Your Profile</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-dvh overflow-hidden bg-grayTheme-light text-grayTheme-dark antialiased">
        <main class="flex h-dvh overflow-hidden">

            {{-- Left: Brand Panel --}}
            <div class="relative hidden overflow-hidden lg:flex lg:w-[36%] lg:flex-col bg-primary">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,_rgba(244,180,0,0.13),_transparent_50%)]"></div>
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/5"></div>
                <div class="absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-white/5"></div>
                <div class="absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.10)_1px,transparent_1px)] bg-[size:28px_28px]"></div>

                <div class="relative z-10 flex h-full flex-col px-10 py-10">
                    <a href="/" class="inline-flex items-center gap-3">
                        <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-10 w-10 object-contain" alt="Alcatt Portal" />
                        <span class="text-xl font-extrabold tracking-tight text-white">Alcatt Portal</span>
                    </a>

                    <div class="flex flex-1 flex-col justify-center">
                        {{-- Step progress --}}
                        <div class="mb-8 flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-white/70">Privacy Agreement</span>
                            </div>
                            <div class="h-px w-6 bg-white/30"></div>
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-sm font-bold text-white">2</div>
                                <span class="text-sm font-semibold text-white">Profile Setup</span>
                            </div>
                        </div>

                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                            <svg class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>

                        <h2 class="mt-6 text-3xl font-black leading-snug tracking-tight text-white">
                            Set up your<br>
                            <span class="text-accent">profile details.</span>
                        </h2>
                        <p class="mt-4 max-w-xs text-sm leading-7 text-white/65">
                            Before accessing the system, please complete your personal and employment information. This is required to continue.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-accent">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Personal Details</p>
                                    <p class="text-xs text-white/60">Full name, date of birth, contact info</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">Employment Role</p>
                                    <p class="text-xs text-white/60">Trainer, assessor, or both</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-white/40">© {{ date('Y') }} Alcatt Portal. All rights reserved.</p>
                </div>
            </div>

            {{-- Right: Form Panel --}}
            <div class="flex flex-1 flex-col overflow-y-auto px-6 py-8 lg:px-10 bg-[radial-gradient(circle_at_top_right,_rgba(43,45,126,0.06),_transparent_40%),linear-gradient(135deg,_#f8f9ff_0%,_#f3f4f6_100%)]">

                {{-- Mobile logo --}}
                <div class="mb-4 flex w-full max-w-2xl items-center gap-3 lg:hidden">
                    <img src="{{ asset('assets/alcatt-logo.png') }}" class="h-8 w-8 object-contain" alt="Alcatt Portal" />
                    <span class="text-lg font-extrabold text-grayTheme-dark">Alcatt Portal</span>
                </div>

                {{-- Mobile step indicator --}}
                <div class="mb-4 flex w-full max-w-2xl items-center gap-2 lg:hidden">
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-success text-xs font-bold text-white">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="h-px w-5 bg-grayTheme-border"></div>
                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-accent text-xs font-bold text-white">2</div>
                    <span class="ml-1 text-xs font-semibold text-grayTheme-dark">Profile Setup (Step 2 of 2)</span>
                </div>

                <div class="w-full max-w-2xl mx-auto">
                    {{-- Card header --}}
                    <div class="rounded-t-[20px] border border-b-0 border-grayTheme-border bg-white px-7 py-5 sm:px-8">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-soft">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl font-extrabold tracking-tight text-grayTheme-dark">Complete Your Profile</h1>
                                <p class="mt-1 text-sm text-grayTheme-medium">
                                    Fill in your details to access the portal. Fields marked with <span class="text-red-500 font-semibold">*</span> are required.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2 rounded-lg border border-primary/20 bg-primary-soft/40 px-3 py-2 text-xs font-medium text-primary">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            You must complete this form before you can access the system.
                        </div>
                    </div>

                    {{-- Form body --}}
                    @php
                        $initialPositionRoles = old('position_roles', $profile?->position_roles ?? []);
                    @endphp

                    <form
                        id="profile-completion-form"
                        method="post"
                        action="{{ route('profile.complete.store') }}"
                        class="border border-b-0 border-grayTheme-border bg-white px-7 py-6 sm:px-8 space-y-8"
                        x-data='profileDetailsForm({
                            initialFirstName: @json(old("first_name", $profile?->first_name)),
                            initialMiddleName: @json(old("middle_name", $profile?->middle_name)),
                            initialLastName: @json(old("last_name", $profile?->last_name)),
                            initialSuffix: @json(old("suffix", $profile?->suffix)),
                            initialDateOfBirth: @json(old("date_of_birth", $profile?->date_of_birth?->format("Y-m-d"))),
                            initialGender: @json(old("gender", $profile?->gender)),
                            initialContactNumber: @json(old("contact_number", $profile?->contact_number)),
                            initialAddress: @json(old("address", $profile?->address)),
                            initialPositionRoles: @json($initialPositionRoles),
                            initialTrainerQualificationTitles: @json(old('trainer_qualification_titles', $profile?->trainer_qualification_titles ?? [])),
                            initialAssessorQualificationTitles: @json(old('assessor_qualification_titles', $profile?->assessor_qualification_titles ?? [])),
                            isAdmin: false
                        })'
                        @submit.prevent="submitted = true; updateValidation(); if (!hasErrors()) $el.submit()"
                    >
                        @csrf

                        @if ($errors->any())
                            <div class="flex items-center gap-3 rounded-lg border border-danger/30 bg-danger-soft px-4 py-3 text-sm font-medium text-danger">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                                <span>Please fix the errors below before continuing.</span>
                            </div>
                        @endif

                        {{-- ── Personal Details ────────────────────────────────────── --}}
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
                                {{-- First Name --}}
                                <div>
                                    <x-input-label for="pc_first_name" :value="__('First Name')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        </span>
                                        <x-text-input id="pc_first_name" name="first_name" type="text" class="block w-full pl-9"
                                            x-bind:class="showError('firstName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            :value="old('first_name', $profile?->first_name)" placeholder="Juan" maxlength="255" required
                                            x-model.trim="firstName"
                                            @input="touched.firstName = true; handleNameInput('firstName')"
                                            @blur="touched.firstName = true; updateValidation()" />
                                    </div>
                                    <p x-show="showError('firstName')" class="mt-2 text-sm text-red-600" x-text="errors.firstName"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                                </div>

                                {{-- Middle Name --}}
                                <div>
                                    <x-input-label for="pc_middle_name" :value="__('Middle Name')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        </span>
                                        <x-text-input id="pc_middle_name" name="middle_name" type="text" class="block w-full pl-9"
                                            x-bind:class="showError('middleName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            :value="old('middle_name', $profile?->middle_name)" placeholder="Dela" maxlength="255" required
                                            x-model.trim="middleName"
                                            @input="touched.middleName = true; handleNameInput('middleName')"
                                            @blur="touched.middleName = true; updateValidation()" />
                                    </div>
                                    <p x-show="showError('middleName')" class="mt-2 text-sm text-red-600" x-text="errors.middleName"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
                                </div>

                                {{-- Last Name --}}
                                <div>
                                    <x-input-label for="pc_last_name" :value="__('Last Name')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        </span>
                                        <x-text-input id="pc_last_name" name="last_name" type="text" class="block w-full pl-9"
                                            x-bind:class="showError('lastName') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            :value="old('last_name', $profile?->last_name)" placeholder="Cruz" maxlength="255" required
                                            x-model.trim="lastName"
                                            @input="touched.lastName = true; handleNameInput('lastName')"
                                            @blur="touched.lastName = true; updateValidation()" />
                                    </div>
                                    <p x-show="showError('lastName')" class="mt-2 text-sm text-red-600" x-text="errors.lastName"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                                </div>

                                {{-- Suffix --}}
                                <div>
                                    <x-input-label for="pc_suffix" :value="__('Suffix')" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" /></svg>
                                        </span>
                                        <select id="pc_suffix" name="suffix" class="form-input pl-9" x-model="suffix" @change="updateValidation()">
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

                                {{-- Date of Birth --}}
                                <div>
                                    <x-input-label for="pc_date_of_birth" :value="__('Date of Birth')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </span>
                                        <x-text-input id="pc_date_of_birth" name="date_of_birth" type="date" class="block w-full pl-9"
                                            x-bind:class="showError('dateOfBirth') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            :value="old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))" required
                                            x-model="dateOfBirth"
                                            @change="touched.dateOfBirth = true; updateValidation()" />
                                    </div>
                                    <p x-show="showError('dateOfBirth')" class="mt-2 text-sm text-red-600" x-text="errors.dateOfBirth"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                                </div>

                                {{-- Sex --}}
                                <div>
                                    <x-input-label for="pc_gender" :value="__('Sex')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        </span>
                                        <select id="pc_gender" name="gender" class="form-input pl-9"
                                            x-bind:class="showError('gender') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            x-model="gender" required
                                            @change="touched.gender = true; updateValidation()">
                                            <option value="">Select sex</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <p x-show="showError('gender')" class="mt-2 text-sm text-red-600" x-text="errors.gender"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                                </div>

                                {{-- Contact Number --}}
                                <div>
                                    <x-input-label for="pc_contact_number" :value="__('Contact Number')" :required="true" />
                                    <div class="relative mt-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-grayTheme-medium">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        </span>
                                        <x-text-input id="pc_contact_number" name="contact_number" type="text" class="block w-full pl-9"
                                            x-bind:class="showError('contactNumber') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                            :value="old('contact_number', $profile?->contact_number)"
                                            placeholder="09123456789" inputmode="numeric" autocomplete="tel" maxlength="11" required
                                            x-model="contactNumber"
                                            @input="touched.contactNumber = true; handleContactInput()"
                                            @blur="touched.contactNumber = true; updateValidation()" />
                                    </div>
                                    <p x-show="showError('contactNumber')" class="mt-2 text-sm text-red-600" x-text="errors.contactNumber"></p>
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                                </div>
                            </div>

                            {{-- Address --}}
                            <div>
                                <x-input-label for="pc_address" :value="__('Address')" />
                                <div class="relative mt-1">
                                    <span class="pointer-events-none absolute left-3 top-2.5 text-grayTheme-medium">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </span>
                                    <textarea id="pc_address" name="address"
                                        class="form-input min-h-[7rem] pl-9 pt-2"
                                        x-bind:class="showError('address') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                                        rows="3" placeholder="Street, barangay, city, province"
                                        x-model.trim="address"
                                        @input="touched.address = true; updateValidation()"
                                        @blur="touched.address = true; updateValidation()">{{ old('address', $profile?->address) }}</textarea>
                                </div>
                                <p class="mt-1 text-xs text-grayTheme-medium">Include street name, barangay, city, and province.</p>
                                <p x-show="showError('address')" class="mt-2 text-sm text-red-600" x-text="errors.address"></p>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>
                        </div>

                        {{-- ── TESDA Role & Qualification Details ───────────────────── --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 border-b border-grayTheme-border pb-3">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-accent-soft">
                                    <svg class="h-4 w-4 text-accent-active" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <h3 class="text-sm font-bold text-grayTheme-dark">TESDA Role &amp; Qualification Details</h3>
                            </div>

                            {{-- Position / Roles --}}
                            <div>
                                <x-input-label :value="__('Position / Job Role')" :required="true" />
                                <p class="mt-1 text-sm text-grayTheme-medium">Select one role or both if you serve as both a trainer and an assessor.</p>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-grayTheme-border bg-white px-4 py-3 text-sm font-medium text-grayTheme-dark shadow-sm transition hover:border-primary/50"
                                        :class="positionRoles.includes('trainer') ? 'border-primary bg-primary-soft/40' : ''">
                                        <input type="checkbox" name="position_roles[]" value="trainer"
                                            class="rounded border-grayTheme-border text-primary focus:ring-primary/30"
                                            x-model="positionRoles"
                                            @change="touched.positionRoles = true; updateValidation()" />
                                        <span>Trainer</span>
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-grayTheme-border bg-white px-4 py-3 text-sm font-medium text-grayTheme-dark shadow-sm transition hover:border-primary/50"
                                        :class="positionRoles.includes('assessor') ? 'border-primary bg-primary-soft/40' : ''">
                                        <input type="checkbox" name="position_roles[]" value="assessor"
                                            class="rounded border-grayTheme-border text-primary focus:ring-primary/30"
                                            x-model="positionRoles"
                                            @change="touched.positionRoles = true; updateValidation()" />
                                        <span>Assessor</span>
                                    </label>
                                </div>
                                <p x-show="showError('positionRoles')" class="mt-2 text-sm text-red-600" x-text="errors.positionRoles"></p>
                                <x-input-error class="mt-2" :messages="$errors->get('position_roles')" />
                            </div>

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

                    </form>

                    {{-- Footer with action buttons --}}
                    <div class="rounded-b-[20px] border border-grayTheme-border bg-white px-7 py-5 shadow-modal sm:px-8">
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <button
                                type="button"
                                onclick="document.getElementById('logout-form').submit()"
                                class="inline-flex items-center gap-2 rounded-button border border-grayTheme-border bg-white px-4 py-2.5 text-sm font-semibold text-grayTheme-dark shadow-sm transition hover:bg-grayTheme-light"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </button>
                            <button
                                type="submit"
                                form="profile-completion-form"
                                class="btn-primary gap-2"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Save &amp; Continue
                            </button>
                        </div>
                    </div>
                </div>

                <p class="mt-4 w-full max-w-2xl mx-auto text-xs text-grayTheme-medium">
                    You can update these details any time in your profile settings.
                </p>
            </div>

        </main>
    </body>
</html>
