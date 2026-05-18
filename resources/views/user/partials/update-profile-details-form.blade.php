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
                <x-input-label for="position_title" :value="__('Position / Job Title')" />
                <x-text-input id="position_title" name="position_title" type="text" class="mt-1 block w-full" :value="old('position_title', $profile?->position_title)" />
                <x-input-error class="mt-2" :messages="$errors->get('position_title')" />
            </div>

            <div>
                <x-input-label for="region" :value="__('Municipality / City')" />
                <select id="region" name="region" class="mt-1 form-input">
                    @php($selectedRegion = old('region', $profile?->region))
                    <option value="">Select municipality or city</option>
                    <option value="Alaminos" @selected($selectedRegion === 'Alaminos')>Alaminos</option>
                    <option value="Bay" @selected($selectedRegion === 'Bay')>Bay</option>
                    <option value="Biñan" @selected($selectedRegion === 'Biñan')>Biñan (City)</option>
                    <option value="Cabuyao" @selected($selectedRegion === 'Cabuyao')>Cabuyao (City)</option>
                    <option value="Calamba" @selected($selectedRegion === 'Calamba')>Calamba (City)</option>
                    <option value="Calauan" @selected($selectedRegion === 'Calauan')>Calauan</option>
                    <option value="Cavinti" @selected($selectedRegion === 'Cavinti')>Cavinti</option>
                    <option value="Famy" @selected($selectedRegion === 'Famy')>Famy</option>
                    <option value="Kalayaan" @selected($selectedRegion === 'Kalayaan')>Kalayaan</option>
                    <option value="Liliw" @selected($selectedRegion === 'Liliw')>Liliw</option>
                    <option value="Los Baños" @selected($selectedRegion === 'Los Baños')>Los Baños</option>
                    <option value="Luisiana" @selected($selectedRegion === 'Luisiana')>Luisiana</option>
                    <option value="Lumban" @selected($selectedRegion === 'Lumban')>Lumban</option>
                    <option value="Mabitac" @selected($selectedRegion === 'Mabitac')>Mabitac</option>
                    <option value="Magdalena" @selected($selectedRegion === 'Magdalena')>Magdalena</option>
                    <option value="Majayjay" @selected($selectedRegion === 'Majayjay')>Majayjay</option>
                    <option value="Nagcarlan" @selected($selectedRegion === 'Nagcarlan')>Nagcarlan</option>
                    <option value="Pakil" @selected($selectedRegion === 'Pakil')>Pakil</option>
                    <option value="Pagsanjan" @selected($selectedRegion === 'Pagsanjan')>Pagsanjan</option>
                    <option value="Pila" @selected($selectedRegion === 'Pila')>Pila</option>
                    <option value="Rizal" @selected($selectedRegion === 'Rizal')>Rizal</option>
                    <option value="San Pablo" @selected($selectedRegion === 'San Pablo')>San Pablo (City)</option>
                    <option value="San Pedro" @selected($selectedRegion === 'San Pedro')>San Pedro (City)</option>
                    <option value="Santa Cruz" @selected($selectedRegion === 'Santa Cruz')>Santa Cruz (Provincial Capital)</option>
                    <option value="Santa Maria" @selected($selectedRegion === 'Santa Maria')>Santa Maria</option>
                    <option value="Santa Rosa" @selected($selectedRegion === 'Santa Rosa')>Santa Rosa (City)</option>
                    <option value="Siniloan" @selected($selectedRegion === 'Siniloan')>Siniloan</option>
                    <option value="Victoria" @selected($selectedRegion === 'Victoria')>Victoria</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('region')" />
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
