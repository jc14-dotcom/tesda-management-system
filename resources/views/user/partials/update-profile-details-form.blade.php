<section class="surface p-6 sm:p-8">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Personal and Employment Details') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your TESDA-related profile information.') }}
        </p>
    </header>

    <form method="post" action="{{ route('account.profile.details') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $profile?->middle_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>

            <div>
                <x-input-label for="suffix" :value="__('Suffix')" />
                <x-text-input id="suffix" name="suffix" type="text" class="mt-1 block w-full" :value="old('suffix', $profile?->suffix)" />
                <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
            </div>

            <div>
                <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
            </div>

            <div>
                <x-input-label for="gender" :value="__('Sex')" />
                <select id="gender" name="gender" class="mt-1 form-input">
                    @php($selectedSex = old('gender', $profile?->gender))
                    <option value="male" @selected($selectedSex === 'male')>Male</option>
                    <option value="female" @selected($selectedSex === 'female')>Female</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <div>
                <x-input-label for="contact_number" :value="__('Contact Number')" />
                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number', $profile?->contact_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
            </div>

            <div>
                <x-input-label for="company_id" :value="__('Company ID')" />
                <x-text-input id="company_id" name="company_id" type="text" class="mt-1 block w-full" :value="old('company_id', $profile?->company_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('company_id')" />
            </div>

            <div>
                <x-input-label for="position_title" :value="__('Position / Job Title')" />
                <x-text-input id="position_title" name="position_title" type="text" class="mt-1 block w-full" :value="old('position_title', $profile?->position_title)" />
                <x-input-error class="mt-2" :messages="$errors->get('position_title')" />
            </div>

            <div>
                <x-input-label for="employment_status" :value="__('Employment Status')" />
                <x-text-input id="employment_status" name="employment_status" type="text" class="mt-1 block w-full" :value="old('employment_status', $profile?->employment_status)" />
                <x-input-error class="mt-2" :messages="$errors->get('employment_status')" />
            </div>

            <div class="md:col-span-2 rounded-card border border-grayTheme-border bg-grayTheme-light px-4 py-3">
                <div class="text-sm font-semibold text-grayTheme-dark">Account Status</div>
                <p class="mt-1 text-sm text-grayTheme-medium">
                    {{ ucfirst($profile?->status ?? 'active') }}
                </p>
                <p class="mt-1 text-xs text-grayTheme-medium">This should be managed by admin or HR, not edited directly by the user.</p>
            </div>

            <div>
                <x-input-label for="date_hired" :value="__('Date Hired')" />
                <x-text-input id="date_hired" name="date_hired" type="date" class="mt-1 block w-full" :value="old('date_hired', $profile?->date_hired?->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('date_hired')" />
            </div>

            <div>
                <x-input-label for="region" :value="__('Region')" />
                <x-text-input id="region" name="region" type="text" class="mt-1 block w-full" :value="old('region', $profile?->region)" />
                <x-input-error class="mt-2" :messages="$errors->get('region')" />
            </div>

            <div>
                <x-input-label for="branch" :value="__('Branch')" />
                <x-text-input id="branch" name="branch" type="text" class="mt-1 block w-full" :value="old('branch', $profile?->branch)" />
                <x-input-error class="mt-2" :messages="$errors->get('branch')" />
            </div>

            <div>
                <x-input-label for="tesda_registry_number" :value="__('TESDA Registry Number')" />
                <x-text-input id="tesda_registry_number" name="tesda_registry_number" type="text" class="mt-1 block w-full" :value="old('tesda_registry_number', $profile?->tesda_registry_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('tesda_registry_number')" />
            </div>

            <div>
                <x-input-label for="qualification_title" :value="__('Qualification Title')" />
                <x-text-input id="qualification_title" name="qualification_title" type="text" class="mt-1 block w-full" :value="old('qualification_title', $profile?->qualification_title)" />
                <x-input-error class="mt-2" :messages="$errors->get('qualification_title')" />
            </div>
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <textarea id="address" name="address" class="mt-1 form-input" rows="3">{{ old('address', $profile?->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div>
            <x-input-label for="remarks" :value="__('Remarks')" />
            <textarea id="remarks" name="remarks" class="mt-1 form-input" rows="3">{{ old('remarks', $profile?->remarks) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('remarks')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-details-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
