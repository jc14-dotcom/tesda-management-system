<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Certificates') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Track TESDA certifications by level or classification, then record the related program or qualification title.') }}
        </p>
    </header>
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
                <x-file-input
                    name="certificate_file"
                    id="certificate_file"
                    :accept="'.pdf,.jpg,.jpeg,.png,.webp,.gif,.bmp,.tif,.tiff,image/*,application/pdf'"
                    :required="true"
                    :help="__('Upload a scanned copy or photo of the certificate. Accepted formats: PDF or image files.')"
                >
                    {{ __('Certificate File (Image or PDF)') }}
                </x-file-input>
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
        <form method="get" class="mt-3 flex flex-wrap items-end gap-3 text-sm">
            <input type="hidden" name="tab" value="certificates" />
            @if ($docType !== 'all')
                <input type="hidden" name="doc_type" value="{{ $docType }}" />
            @endif
            <div>
                <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_status">Status</label>
                <select id="cert_status" name="cert_status" class="mt-1 form-input">
                    <option value="all" @selected($certStatus === 'all')>All</option>
                    <option value="valid" @selected($certStatus === 'valid')>Valid</option>
                    <option value="expiring" @selected($certStatus === 'expiring')>Expiring</option>
                    <option value="expired" @selected($certStatus === 'expired')>Expired</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-grayTheme-medium" for="cert_window">Expiration Window</label>
                <select id="cert_window" name="cert_window" class="mt-1 form-input">
                    <option value="0" @selected($certWindow === 0)>All dates</option>
                    <option value="30" @selected($certWindow === 30)>Next 30 days</option>
                    <option value="60" @selected($certWindow === 60)>Next 60 days</option>
                    <option value="90" @selected($certWindow === 90)>Next 90 days</option>
                </select>
            </div>
            <button class="btn-primary" type="submit">Apply</button>
        </form>

        <div
            class="mt-3"
            x-data="loadMoreList({ nextUrl: @js($certificates->nextPageUrl()), partialParam: 'certificates_partial' })"
        >
            <div class="overflow-x-auto">
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
                    <tbody class="divide-y" x-ref="list">
                        @if ($certificates->isEmpty())
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">No certificates yet.</td>
                            </tr>
                        @else
                            @include('profile.partials.certificate-rows', ['certificates' => $certificates])
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                {{ $certificates->links() }}
                <button
                    type="button"
                    class="btn-secondary"
                    x-show="nextUrl"
                    x-on:click="loadMore"
                    :disabled="loading"
                >
                    <span x-show="!loading">Load more</span>
                    <span x-show="loading">Loading...</span>
                </button>
            </div>
        </div>
    </div>
</section>
