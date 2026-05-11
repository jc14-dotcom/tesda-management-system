<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Certificates') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Track TESDA certifications by level or classification, then record the related program or qualification title.') }}
        </p>
    </header>

    <form method="post" action="{{ route('certificates.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="certificate_name" :value="__('Certificate Name')" />
                <x-text-input id="certificate_name" name="certificate_name" type="text" class="mt-1 block w-full" :value="old('certificate_name')" required />
                <x-input-error class="mt-2" :messages="$errors->get('certificate_name')" />
            </div>

            <div>
                <x-input-label for="certificate_type" :value="__('TESDA Classification / Level')" />
                <select id="certificate_type" name="certificate_type" class="mt-1 form-input" required>
                    <option value="nc_i" @selected(old('certificate_type') === 'nc_i')>NC I</option>
                    <option value="nc_ii" @selected(old('certificate_type') === 'nc_ii')>NC II</option>
                    <option value="nc_iii" @selected(old('certificate_type') === 'nc_iii')>NC III</option>
                    <option value="nc_iv" @selected(old('certificate_type') === 'nc_iv')>NC IV</option>
                    <option value="nttc" @selected(old('certificate_type') === 'nttc')>NTTC</option>
                    <option value="trainer" @selected(old('certificate_type') === 'trainer')>Trainer</option>
                    <option value="assessor" @selected(old('certificate_type') === 'assessor')>Assessor</option>
                    <option value="other" @selected(old('certificate_type') === 'other')>Other</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Use this for the TESDA level or credential classification, such as NC II or NTTC.</p>
                <x-input-error class="mt-2" :messages="$errors->get('certificate_type')" />
            </div>

            <div>
                <x-input-label for="qualification_title" :value="__('Program / Qualification Title')" />
                <x-text-input id="qualification_title" name="qualification_title" type="text" class="mt-1 block w-full" :value="old('qualification_title')" />
                <p class="mt-1 text-xs text-gray-500">Example: Bookkeeping, Cookery, Food and Beverage Services</p>
                <x-input-error class="mt-2" :messages="$errors->get('qualification_title')" />
            </div>

            <div>
                <x-input-label for="certificate_number" :value="__('Certificate Number')" />
                <x-text-input id="certificate_number" name="certificate_number" type="text" class="mt-1 block w-full" :value="old('certificate_number')" />
                <x-input-error class="mt-2" :messages="$errors->get('certificate_number')" />
            </div>

            <div>
                <x-input-label for="issued_by" :value="__('Issued By')" />
                <x-text-input id="issued_by" name="issued_by" type="text" class="mt-1 block w-full" :value="old('issued_by')" />
                <x-input-error class="mt-2" :messages="$errors->get('issued_by')" />
            </div>

            <div>
                <x-input-label for="issue_date" :value="__('Issue Date')" />
                <x-text-input id="issue_date" name="issue_date" type="date" class="mt-1 block w-full" :value="old('issue_date')" />
                <x-input-error class="mt-2" :messages="$errors->get('issue_date')" />
            </div>

            <div>
                <x-input-label for="expiration_date" :value="__('Expiration Date')" />
                <x-text-input id="expiration_date" name="expiration_date" type="date" class="mt-1 block w-full" :value="old('expiration_date')" />
                <x-input-error class="mt-2" :messages="$errors->get('expiration_date')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="certificate_file" :value="__('Certificate File (Image or PDF)')" />
                <input id="certificate_file" name="certificate_file" type="file" class="mt-1 block w-full rounded-button border-grayTheme-border bg-white text-grayTheme-dark shadow-sm focus:border-primary focus:ring-primary/30" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,image/*,application/pdf" />
                <p class="mt-1 text-xs text-gray-500">Upload a scanned copy or photo of the certificate. Accepted formats: PDF or image files.</p>
                <x-input-error class="mt-2" :messages="$errors->get('certificate_file')" />
            </div>
        </div>

        <div>
            <x-input-label for="remarks" :value="__('Remarks')" />
            <textarea id="remarks" name="remarks" class="mt-1 form-input" rows="3">{{ old('remarks') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('remarks')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Add Certificate') }}</x-primary-button>

            @if (session('status') === 'certificate-added')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Added.') }}</p>
            @endif
        </div>
    </form>

    <div class="mt-6">
        <h3 class="text-sm font-semibold text-gray-700">Existing Certificates</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="text-left text-gray-500">
                    <tr>
                        <th class="py-2">Name</th>
                            <th class="py-2">TESDA Classification</th>
                            <th class="py-2">Program / Qualification</th>
                            <th class="py-2">File</th>
                        <th class="py-2">Expires</th>
                        <th class="py-2">Status</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($certificates as $certificate)
                            @php($certificateFile = $certificate->documents->first())
                        <tr>
                            <td class="py-2 font-medium text-gray-900">{{ $certificate->certificate_name }}</td>
                                <td class="py-2">{{ $certificate->certificate_type_label }}</td>
                                <td class="py-2">{{ $certificate->qualification_title ?? '—' }}</td>
                                <td class="py-2">
                                    @if ($certificateFile)
                                        <a class="text-primary hover:text-primary-hover" href="{{ route('documents.download', $certificateFile) }}">
                                            {{ $certificateFile->original_name }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                            <td class="py-2">{{ $certificate->expiration_date?->format('Y-m-d') ?? '—' }}</td>
                            <td class="py-2">{{ ucfirst($certificate->status) }}</td>
                            <td class="py-2 text-right">
                                <form method="post" action="{{ route('certificates.destroy', $certificate) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-600 hover:text-red-900" type="submit">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">No certificates yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($certificates, 'links'))
            <div class="mt-4">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</section>
