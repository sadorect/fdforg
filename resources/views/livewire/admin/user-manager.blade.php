<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-sm text-gray-600">Manage user accounts, admin privileges, and role assignments.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New User</button>
    </div>

    @if($statusMessage)
        <div class="rounded-lg border px-4 py-3 shadow-sm {{ $statusType === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-green-200 bg-green-50 text-green-800' }}" role="{{ $statusType === 'error' ? 'alert' : 'status' }}" aria-live="{{ $statusType === 'error' ? 'assertive' : 'polite' }}">
            <div class="flex items-start justify-between gap-4">
                <p class="text-sm font-medium">{{ $statusMessage }}</p>
                <button type="button" wire:click="dismissStatus" class="text-xs font-semibold uppercase tracking-wide {{ $statusType === 'error' ? 'text-red-700 hover:text-red-900' : 'text-green-700 hover:text-green-900' }}">
                    Dismiss
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-3">
        <div>
            <label for="user-search" class="text-sm font-medium text-gray-700">Search</label>
            <input id="user-search" wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Name, email, bio">
        </div>
        <div>
            <label for="user-role-filter" class="text-sm font-medium text-gray-700">Role</label>
            <select id="user-role-filter" wire:model.live="roleFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All users</option>
                <option value="admin">Admins</option>
                <option value="user">Non-admins</option>
            </select>
        </div>
    </div>

    @php($selectedCount = count($selectedUsers))
    <div class="rounded-lg bg-white p-4 shadow">
        <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Bulk Actions</h2>
                <p class="text-xs text-gray-500">
                    {{ $selectedCount }} user{{ $selectedCount === 1 ? '' : 's' }} selected.
                    Use the row checkboxes or select everyone on the current page.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="selectVisibleUsers" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                    Select Page ({{ $users->count() }})
                </button>
                <button type="button" wire:click="clearSelection" class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                    Clear
                </button>
                <button type="button" wire:click="bulkMarkEmailVerified" class="rounded-md bg-emerald-100 px-3 py-2 text-sm font-medium text-emerald-800 hover:bg-emerald-200">
                    Verify Email
                </button>
                <button type="button" wire:click="bulkMarkEmailUnverified" class="rounded-md bg-amber-100 px-3 py-2 text-sm font-medium text-amber-800 hover:bg-amber-200">
                    Mark Unverified
                </button>
                @if($canManageAccessAssignments)
                    <button type="button" wire:click="bulkGrantAdmin" class="rounded-md bg-indigo-100 px-3 py-2 text-sm font-medium text-indigo-800 hover:bg-indigo-200">
                        Grant Admin
                    </button>
                    <button type="button" wire:click="bulkRevokeAdmin" class="rounded-md bg-slate-100 px-3 py-2 text-sm font-medium text-slate-800 hover:bg-slate-200">
                        Remove Admin
                    </button>
                @endif
                <button
                    type="button"
                    wire:click="bulkDeleteUsers"
                    wire:confirm="Delete the selected users? Protected accounts will be skipped."
                    class="rounded-md bg-red-100 px-3 py-2 text-sm font-medium text-red-800 hover:bg-red-200"
                >
                    Delete Selected
                </button>
            </div>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit User' : 'Create User' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="user-form-name" class="text-sm font-medium text-gray-700">Name *</label>
                    <input id="user-form-name" wire:model="name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="user-form-email" class="text-sm font-medium text-gray-700">Email *</label>
                    <input id="user-form-email" wire:model="email" type="email" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="user-form-password" class="text-sm font-medium text-gray-700">{{ $editing ? 'New Password (optional)' : 'Password *' }}</label>
                    <input id="user-form-password" wire:model="password" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    @if($canManageAccessAssignments)
                        <label for="user-form-is-admin" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input id="user-form-is-admin" wire:model="is_admin" type="checkbox" class="rounded border-gray-300">
                            Admin
                        </label>
                    @else
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                            Admin access and role assignment are managed in Roles &amp; Permissions.
                        </div>
                    @endif
                    <label for="user-form-email-verified" class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input id="user-form-email-verified" wire:model="email_verified" type="checkbox" class="rounded border-gray-300">
                        Email Verified
                    </label>
                </div>

                @if($canManageAccessAssignments)
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between gap-3">
                            <label for="user-form-role-ids" class="text-sm font-medium text-gray-700">Assigned Roles</label>
                            <button type="button" wire:click="clearAssignedRoles" class="text-xs font-semibold text-gray-500 hover:text-gray-700">
                                Clear all roles
                            </button>
                        </div>
                        <select id="user-form-role-ids" wire:model="role_ids" multiple class="mt-1 h-32 w-full rounded-md border-gray-300 text-sm">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_ids.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-2 text-xs text-gray-500">Leave this empty if the user should have no scoped admin tools yet.</p>
                        @if($is_admin && count($role_ids) === 0)
                            <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                This account can sign into admin, but it will only see basic pages until at least one role is assigned.
                            </div>
                        @endif
                    </div>
                @endif

                <div class="md:col-span-2">
                    <label for="user-form-bio" class="text-sm font-medium text-gray-700">Bio</label>
                    <textarea id="user-form-bio" wire:model="bio" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        {{ $editing ? 'Update User' : 'Create User' }}
                    </button>
                    <button type="button" wire:click="cancel" class="rounded-md bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Select</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Access Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Assigned Roles</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Verified</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Created</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3 align-top">
                            <input
                                type="checkbox"
                                wire:model.live="selectedUsers"
                                value="{{ $user->id }}"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                aria-label="Select user {{ $user->name }}"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $user->is_admin ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $user->is_admin ? 'Admin' : 'User' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ $role->name }}</span>
                                @empty
                                    <span class="text-xs text-gray-500">No roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex gap-2">
                                <button wire:click="edit({{ $user->id }})" class="text-sm text-blue-600 hover:text-blue-800" aria-label="Edit user {{ $user->name }}">Edit</button>
                                @if($canManageAccessAssignments)
                                    <button wire:click="toggleAdmin({{ $user->id }})" class="text-sm text-indigo-600 hover:text-indigo-800" aria-label="{{ $user->is_admin ? 'Remove admin access for' : 'Grant admin access to' }} user {{ $user->name }}">
                                        {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                    </button>
                                @endif
                                <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Delete {{ $user->name }}?" class="text-sm text-red-600 hover:text-red-800" aria-label="Delete user {{ $user->name }}">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
