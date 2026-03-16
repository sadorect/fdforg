<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class UserManager extends AdminComponent
{
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_USERS];

    public $search = '';

    public $roleFilter = '';

    public array $selectedUsers = [];

    public $showForm = false;

    public $editing = false;

    public $userId;

    public $name = '';

    public $email = '';

    public $password = '';

    public $bio = '';

    public $is_admin = false;

    public $email_verified = true;

    public $role_ids = [];

    public ?string $statusMessage = null;

    public string $statusType = 'success';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->clearSelection();
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->clearSelection();
        $this->resetPage();
    }

    public function updatedSelectedUsers(): void
    {
        $this->selectedUsers = $this->selectedUserIds();
    }

    public function updatedIsAdmin(bool $value): void
    {
        if ($value) {
            return;
        }

        if (auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS)) {
            $this->role_ids = [];
        }
    }

    public function dismissStatus(): void
    {
        $this->clearStatus();
    }

    public function render()
    {
        $canManageAccessAssignments = auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS) ?? false;

        $users = $this->userQuery()
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roles' => $canManageAccessAssignments ? Role::orderBy('name')->get() : collect(),
            'canManageAccessAssignments' => $canManageAccessAssignments,
        ])->layout('layouts.admin')
            ->title('User Management');
    }

    public function create(): void
    {
        $this->clearStatus();
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $this->clearStatus();
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio ?? '';
        $this->is_admin = (bool) $user->is_admin;
        $this->email_verified = $user->email_verified_at !== null;
        $this->role_ids = $user->roles()->pluck('roles.id')->all();
        $this->password = '';
        $this->showForm = true;
        $this->editing = true;
    }

    public function save(): void
    {
        $data = $this->validate($this->rules());
        $canManageAccessAssignments = auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS) ?? false;

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'bio' => $data['bio'] ?? null,
            'email_verified_at' => $data['email_verified'] ? now() : null,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($this->editing) {
            $user = User::findOrFail($this->userId);
            $payload['is_admin'] = $canManageAccessAssignments ? (bool) $data['is_admin'] : (bool) $user->is_admin;
            $user->update($payload);
            $this->reportStatus('success', 'User updated successfully.');
        } else {
            $payload['is_admin'] = $canManageAccessAssignments ? (bool) $data['is_admin'] : false;
            if (empty($data['password'])) {
                $payload['password'] = Hash::make('password');
            }
            $user = User::create($payload);
            $this->reportStatus('success', 'User created successfully.');
        }

        if ($canManageAccessAssignments) {
            $user->roles()->sync($payload['is_admin'] ? ($data['role_ids'] ?? []) : []);
        }

        $this->resetForm();
    }

    public function deleteUser(int $id): void
    {
        $result = $this->deleteUsers([$id]);
        $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$id]));

        if ($result['deleted'] === 1) {
            $this->reportStatus('success', 'User deleted successfully.');

            return;
        }

        if ($result['selfBlocked'] > 0) {
            $this->reportStatus('error', 'You cannot delete your own account.');

            return;
        }

        $this->reportStatus('error', 'Cannot delete user with linked LMS or content records.');
    }

    public function toggleAdmin(int $id): void
    {
        if (! auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS)) {
            $this->reportStatus('error', 'Only access administrators can change admin status.');

            return;
        }

        if (auth()->id() === $id) {
            $this->reportStatus('error', 'You cannot change your own admin role.');

            return;
        }

        $user = User::findOrFail($id);
        $newAdminState = ! $user->is_admin;

        $user->update(['is_admin' => $newAdminState]);

        if (! $newAdminState) {
            $user->roles()->sync([]);
        }

        $this->reportStatus(
            'success',
            $newAdminState
                ? "Granted admin access to {$user->name}."
                : "Removed admin access for {$user->name} and cleared assigned roles."
        );
    }

    public function selectVisibleUsers(): void
    {
        $this->selectedUsers = $this->visibleUserIds();
    }

    public function clearSelection(): void
    {
        $this->selectedUsers = [];
    }

    public function bulkMarkEmailVerified(): void
    {
        $selectedIds = $this->selectedUserIds();

        if ($selectedIds === []) {
            $this->reportStatus('error', 'Select at least one user first.');

            return;
        }

        $count = User::whereIn('id', $selectedIds)->update(['email_verified_at' => now()]);

        $this->reportStatus('success', $this->formatCountMessage($count, 'Verified email for :count user.', 'Verified email for :count users.'));
    }

    public function bulkMarkEmailUnverified(): void
    {
        $selectedIds = $this->selectedUserIds();

        if ($selectedIds === []) {
            $this->reportStatus('error', 'Select at least one user first.');

            return;
        }

        $count = User::whereIn('id', $selectedIds)->update(['email_verified_at' => null]);

        $this->reportStatus('success', $this->formatCountMessage($count, 'Marked :count user as unverified.', 'Marked :count users as unverified.'));
    }

    public function bulkGrantAdmin(): void
    {
        if (! auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS)) {
            $this->reportStatus('error', 'Only access administrators can change admin status.');

            return;
        }

        $selectedIds = $this->selectedUserIds();

        if ($selectedIds === []) {
            $this->reportStatus('error', 'Select at least one user first.');

            return;
        }

        $count = User::whereIn('id', $selectedIds)->update(['is_admin' => true]);

        $this->reportStatus('success', $this->formatCountMessage($count, 'Granted admin access to :count user.', 'Granted admin access to :count users.'));
    }

    public function bulkRevokeAdmin(): void
    {
        if (! auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS)) {
            $this->reportStatus('error', 'Only access administrators can change admin status.');

            return;
        }

        $selectedIds = $this->selectedUserIds();

        if ($selectedIds === []) {
            $this->reportStatus('error', 'Select at least one user first.');

            return;
        }

        $users = User::whereIn('id', $selectedIds)->get();
        $updatedCount = 0;
        $selfBlocked = 0;

        foreach ($users as $user) {
            if ($user->id === auth()->id()) {
                $selfBlocked++;

                continue;
            }

            $user->update(['is_admin' => false]);
            $user->roles()->sync([]);
            $updatedCount++;
        }

        if ($updatedCount === 0) {
            $this->reportStatus('error', 'You cannot change your own admin role.');

            return;
        }

        $message = $this->formatCountMessage(
            $updatedCount,
            'Removed admin access for :count user and cleared assigned roles.',
            'Removed admin access for :count users and cleared assigned roles.'
        );

        if ($selfBlocked > 0) {
            $message .= ' Skipped your current account.';
        }

        $this->reportStatus('success', $message);
    }

    public function bulkDeleteUsers(): void
    {
        $selectedIds = $this->selectedUserIds();

        if ($selectedIds === []) {
            $this->reportStatus('error', 'Select at least one user first.');

            return;
        }

        $result = $this->deleteUsers($selectedIds);
        $messages = [];

        if ($result['deleted'] > 0) {
            $messages[] = $this->formatCountMessage($result['deleted'], 'Deleted :count user.', 'Deleted :count users.');
        }

        if ($result['linkedBlocked'] > 0) {
            $messages[] = $this->formatCountMessage($result['linkedBlocked'], 'Skipped :count user with linked LMS or content records.', 'Skipped :count users with linked LMS or content records.');
        }

        if ($result['selfBlocked'] > 0) {
            $messages[] = $this->formatCountMessage($result['selfBlocked'], 'Skipped :count current account.', 'Skipped :count current accounts.');
        }

        $this->clearSelection();

        $this->reportStatus(
            $result['deleted'] > 0 ? 'success' : 'error',
            implode(' ', $messages)
        );
    }

    public function cancel(): void
    {
        $this->clearStatus();
        $this->resetForm();
    }

    public function clearAssignedRoles(): void
    {
        if (! auth()->user()?->hasPermission(AdminPermissions::MANAGE_ROLES_PERMISSIONS)) {
            return;
        }

        $this->role_ids = [];
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],
            'password' => [$this->editing ? 'nullable' : 'required', 'string', 'min:8'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'is_admin' => ['boolean'],
            'email_verified' => ['boolean'],
            'role_ids' => ['array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }

    private function userQuery(): Builder
    {
        return User::query()
            ->with('roles')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('bio', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->roleFilter !== '', function ($query) {
                if ($this->roleFilter === 'admin') {
                    $query->where('is_admin', true);
                } elseif ($this->roleFilter === 'user') {
                    $query->where('is_admin', false);
                }
            });
    }

    private function visibleUserIds(): array
    {
        return $this->userQuery()
            ->orderBy('name')
            ->paginate(15)
            ->getCollection()
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    private function selectedUserIds(): array
    {
        return collect($this->selectedUsers)
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function deleteUsers(array $ids): array
    {
        $deleted = 0;
        $linkedBlocked = 0;
        $selfBlocked = 0;

        foreach (User::whereIn('id', $ids)->get() as $user) {
            if ($user->id === auth()->id()) {
                $selfBlocked++;

                continue;
            }

            if ($this->hasLinkedRecords($user->id)) {
                $linkedBlocked++;

                continue;
            }

            $user->delete();
            $deleted++;
        }

        return [
            'deleted' => $deleted,
            'linkedBlocked' => $linkedBlocked,
            'selfBlocked' => $selfBlocked,
        ];
    }

    private function hasLinkedRecords(int $userId): bool
    {
        return BlogPost::where('author_id', $userId)->exists()
            || Course::where('instructor_id', $userId)->exists()
            || Enrollment::where('user_id', $userId)->exists();
    }

    private function reportStatus(string $type, string $message): void
    {
        $this->statusType = $type;
        $this->statusMessage = $message;
        session()->flash($type, $message);
    }

    private function clearStatus(): void
    {
        $this->statusType = 'success';
        $this->statusMessage = null;
    }

    private function formatCountMessage(int $count, string $singular, string $plural): string
    {
        return str_replace(':count', (string) $count, $count === 1 ? $singular : $plural);
    }

    private function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'password',
            'bio',
            'is_admin',
            'email_verified',
            'role_ids',
            'showForm',
            'editing',
        ]);
        $this->is_admin = false;
        $this->email_verified = true;
    }
}
