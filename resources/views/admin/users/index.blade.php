<x-app-layout>
    <div class="py-12">
        <div class="page-container space-y-6">
            <x-page-header
                title="Users"
                subtitle="Manage account access and user details."
                eyebrow="Administration"
            />

            <div class="surface">
                <div class="p-6 text-grayTheme-dark">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-grayTheme-medium">
                                <tr>
                                    <th class="py-2">Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Role</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Certificates</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="py-2 font-medium text-grayTheme-dark">{{ $user->name }}</td>
                                        <td class="py-2">{{ $user->email }}</td>
                                        <td class="py-2">
                                            {{ $user->hasRole('admin') ? 'Admin' : 'User' }}
                                        </td>
                                        <td class="py-2">
                                            {{ $user->profile?->status ?? 'active' }}
                                        </td>
                                        <td class="py-2">{{ $user->certificates_count }}</td>
                                        <td class="py-2 text-right">
                                            <a class="text-primary hover:text-primary-hover" href="{{ route('admin.users.show', $user) }}">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
