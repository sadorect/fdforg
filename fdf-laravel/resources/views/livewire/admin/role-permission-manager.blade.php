<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Roles & Permissions</h1>
            <p class="text-sm text-gray-600">Define permissions, build roles, and assign roles to admin users.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="createPermission" class="rounded-md bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">New Permission</button>
            <button wire:click="createRole" class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Role</button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="space-y-4 rounded-lg bg-white p-4 shadow">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Roles</h2>
                <input wire:model.live="searchRoles" type="text" class="w-64 rounded-md border-gray-300 text-sm" placeholder="Search roles">
            </div>

            @if($showRoleForm)
                <div class="rounded-md border border-gray-200 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900">{{ $editingRole ? 'Edit Role' : 'Create Role' }}</h3>
                    <form wire:submit="saveRole" class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-700">Name *</label>
                            <input wire:model="role_name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                            @error('role_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Slug *</label>
                            <input wire:model="role_slug" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                            @error('role_slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Description</label>
                            <textarea wire:model="role_description" rows="2" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('role_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Permissions</label>
                            <select wire:model="role_permission_ids" multiple class="mt-1 h-32 w-full rounded-md border-gray-300 text-sm">
                                @foreach($allPermissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            @error('role_permission_ids.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Assigned Admin Users</label>
                            <select wire:model="role_user_ids" multiple class="mt-1 h-32 w-full rounded-md border-gray-300 text-sm">
                                @foreach($adminUsers as $adminUser)
                                    <option value="{{ $adminUser->id }}">{{ $adminUser->name }} ({{ $adminUser->email }})</option>
                                @endforeach
                            </select>
                            @error('role_user_ids.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input wire:model="role_is_system" type="checkbox" class="rounded border-gray-300">
                            System role (protected from deletion)
                        </label>
                        <div class="flex gap-2">
                            <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700">
                                {{ $editingRole ? 'Update Role' : 'Create Role' }}
                            </button>
                            <button type="button" wire:click="cancelRole" class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-gray-200">Cancel</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Role</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Permissions</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Users</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($roles as $role)
                            <tr>
                                <td class="px-3 py-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $role->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $role->slug }}</p>
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $role->permissions_count }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $role->users_count }}</td>
                                <td class="px-3 py-2 text-right">
                                    <div class="inline-flex gap-2 text-sm">
                                        <button wire:click="editRole({{ $role->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                        <button wire:click="deleteRole({{ $role->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $roles->links() }}
        </section>

        <section class="space-y-4 rounded-lg bg-white p-4 shadow">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Permissions</h2>
                <input wire:model.live="searchPermissions" type="text" class="w-64 rounded-md border-gray-300 text-sm" placeholder="Search permissions">
            </div>

            @if($showPermissionForm)
                <div class="rounded-md border border-gray-200 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-gray-900">{{ $editingPermission ? 'Edit Permission' : 'Create Permission' }}</h3>
                    <form wire:submit="savePermission" class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-700">Name *</label>
                            <input wire:model="permission_name" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                            @error('permission_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Slug *</label>
                            <input wire:model="permission_slug" type="text" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
                            @error('permission_slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-700">Description</label>
                            <textarea wire:model="permission_description" rows="2" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('permission_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700">
                                {{ $editingPermission ? 'Update Permission' : 'Create Permission' }}
                            </button>
                            <button type="button" wire:click="cancelPermission" class="rounded-md bg-gray-100 px-3 py-2 text-sm text-gray-700 hover:bg-gray-200">Cancel</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="overflow-hidden rounded-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Permission</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Roles</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($permissions as $permission)
                            <tr>
                                <td class="px-3 py-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $permission->slug }}</p>
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $permission->roles_count }}</td>
                                <td class="px-3 py-2 text-right">
                                    <div class="inline-flex gap-2 text-sm">
                                        <button wire:click="editPermission({{ $permission->id }})" class="text-blue-600 hover:text-blue-800">Edit</button>
                                        <button wire:click="deletePermission({{ $permission->id }})" class="text-red-600 hover:text-red-800">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-6 text-center text-sm text-gray-500">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $permissions->links() }}
        </section>
    </div>
</div>
