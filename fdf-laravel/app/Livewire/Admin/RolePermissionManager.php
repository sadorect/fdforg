<?php

namespace App\Livewire\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class RolePermissionManager extends Component
{
    use WithPagination;

    public $searchRoles = '';
    public $searchPermissions = '';

    public $showRoleForm = false;
    public $editingRole = false;
    public $roleId;
    public $role_name = '';
    public $role_slug = '';
    public $role_description = '';
    public $role_permission_ids = [];
    public $role_user_ids = [];
    public $role_is_system = false;

    public $showPermissionForm = false;
    public $editingPermission = false;
    public $permissionId;
    public $permission_name = '';
    public $permission_slug = '';
    public $permission_description = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearchRoles(): void
    {
        $this->resetPage('rolesPage');
    }

    public function updatingSearchPermissions(): void
    {
        $this->resetPage('permissionsPage');
    }

    public function render()
    {
        $roles = Role::query()
            ->withCount(['permissions', 'users'])
            ->when($this->searchRoles !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchRoles . '%')
                        ->orWhere('slug', 'like', '%' . $this->searchRoles . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'rolesPage');

        $permissions = Permission::query()
            ->withCount('roles')
            ->when($this->searchPermissions !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchPermissions . '%')
                        ->orWhere('slug', 'like', '%' . $this->searchPermissions . '%');
                });
            })
            ->orderBy('name')
            ->paginate(10, ['*'], 'permissionsPage');

        return view('livewire.admin.role-permission-manager', [
            'roles' => $roles,
            'permissions' => $permissions,
            'allPermissions' => Permission::orderBy('name')->get(),
            'adminUsers' => User::where('is_admin', true)->orderBy('name')->get(),
        ])->layout('layouts.admin')
            ->title('Roles & Permissions');
    }

    public function createRole(): void
    {
        $this->resetRoleForm();
        $this->showRoleForm = true;
        $this->editingRole = false;
    }

    public function editRole(int $id): void
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);

        $this->roleId = $role->id;
        $this->role_name = $role->name;
        $this->role_slug = $role->slug;
        $this->role_description = $role->description ?? '';
        $this->role_permission_ids = $role->permissions->pluck('id')->all();
        $this->role_user_ids = $role->users->pluck('id')->all();
        $this->role_is_system = (bool) $role->is_system;
        $this->editingRole = true;
        $this->showRoleForm = true;
    }

    public function saveRole(): void
    {
        $data = $this->validate($this->roleRules());
        $payload = [
            'name' => $data['role_name'],
            'slug' => $data['role_slug'],
            'description' => $data['role_description'] ?? null,
            'is_system' => (bool) $data['role_is_system'],
        ];

        if ($this->editingRole) {
            $role = Role::findOrFail($this->roleId);
            $role->update($payload);
        } else {
            $role = Role::create($payload);
        }

        $role->permissions()->sync($data['role_permission_ids'] ?? []);
        $role->users()->sync($data['role_user_ids'] ?? []);

        session()->flash('success', $this->editingRole ? 'Role updated successfully.' : 'Role created successfully.');
        $this->resetRoleForm();
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            session()->flash('error', 'System roles cannot be deleted.');
            return;
        }

        $role->delete();
        session()->flash('success', 'Role deleted successfully.');
    }

    public function createPermission(): void
    {
        $this->resetPermissionForm();
        $this->showPermissionForm = true;
        $this->editingPermission = false;
    }

    public function editPermission(int $id): void
    {
        $permission = Permission::findOrFail($id);

        $this->permissionId = $permission->id;
        $this->permission_name = $permission->name;
        $this->permission_slug = $permission->slug;
        $this->permission_description = $permission->description ?? '';
        $this->editingPermission = true;
        $this->showPermissionForm = true;
    }

    public function savePermission(): void
    {
        $data = $this->validate($this->permissionRules());
        $payload = [
            'name' => $data['permission_name'],
            'slug' => $data['permission_slug'],
            'description' => $data['permission_description'] ?? null,
        ];

        if ($this->editingPermission) {
            Permission::findOrFail($this->permissionId)->update($payload);
            session()->flash('success', 'Permission updated successfully.');
        } else {
            Permission::create($payload);
            session()->flash('success', 'Permission created successfully.');
        }

        $this->resetPermissionForm();
    }

    public function deletePermission(int $id): void
    {
        Permission::findOrFail($id)->delete();
        session()->flash('success', 'Permission deleted successfully.');
    }

    public function updatedRoleName(string $value): void
    {
        if (!$this->editingRole || $this->role_slug === '') {
            $this->role_slug = Str::slug($value);
        }
    }

    public function updatedPermissionName(string $value): void
    {
        if (!$this->editingPermission || $this->permission_slug === '') {
            $this->permission_slug = Str::slug($value);
        }
    }

    public function cancelRole(): void
    {
        $this->resetRoleForm();
    }

    public function cancelPermission(): void
    {
        $this->resetPermissionForm();
    }

    private function roleRules(): array
    {
        return [
            'role_name' => ['required', 'string', 'max:255'],
            'role_slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'slug')->ignore($this->roleId),
            ],
            'role_description' => ['nullable', 'string', 'max:1000'],
            'role_is_system' => ['boolean'],
            'role_permission_ids' => ['array'],
            'role_permission_ids.*' => ['integer', 'exists:permissions,id'],
            'role_user_ids' => ['array'],
            'role_user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    private function permissionRules(): array
    {
        return [
            'permission_name' => ['required', 'string', 'max:255'],
            'permission_slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug')->ignore($this->permissionId),
            ],
            'permission_description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function resetRoleForm(): void
    {
        $this->reset([
            'showRoleForm',
            'editingRole',
            'roleId',
            'role_name',
            'role_slug',
            'role_description',
            'role_permission_ids',
            'role_user_ids',
            'role_is_system',
        ]);
    }

    private function resetPermissionForm(): void
    {
        $this->reset([
            'showPermissionForm',
            'editingPermission',
            'permissionId',
            'permission_name',
            'permission_slug',
            'permission_description',
        ]);
    }
}
