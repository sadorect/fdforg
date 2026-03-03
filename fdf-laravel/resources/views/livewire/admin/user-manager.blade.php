<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
            <p class="text-sm text-gray-600">Manage user accounts, admin privileges, and role assignments.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New User</button>
    </div>

    <div class="grid grid-cols-1 gap-4 rounded-lg bg-white p-4 shadow sm:grid-cols-3">
        <div>
            <label class="text-sm font-medium text-gray-700">Search</label>
            <input wire:model.live="search" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Name, email, bio">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Role</label>
            <select wire:model.live="roleFilter" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                <option value="">All users</option>
                <option value="admin">Admins</option>
                <option value="user">Non-admins</option>
            </select>
        </div>
    </div>

    @if($showForm)
        <div class="rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold">{{ $editing ? 'Edit User' : 'Create User' }}</h2>
            <form wire:submit="save" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Name *</label>
                    <input wire:model="name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Email *</label>
                    <input wire:model="email" type="email" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">{{ $editing ? 'New Password (optional)' : 'Password *' }}</label>
                    <input wire:model="password" type="password" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="is_admin" type="checkbox" class="rounded border-gray-300">
                        Admin
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input wire:model="email_verified" type="checkbox" class="rounded border-gray-300">
                        Email Verified
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Assigned Roles</label>
                    <select wire:model="role_ids" multiple class="mt-1 h-32 w-full rounded-md border-gray-300 text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_ids.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Bio</label>
                    <textarea wire:model="bio" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
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
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">User</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Assigned Roles</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Verified</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Created</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
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
                                <button wire:click="edit({{ $user->id }})" class="text-sm text-blue-600 hover:text-blue-800">Edit</button>
                                <button wire:click="delete({{ $user->id }})" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="border-t border-gray-100 p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
