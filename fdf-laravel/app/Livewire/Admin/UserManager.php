<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
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

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('bio', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter !== '', function ($query) {
                if ($this->roleFilter === 'admin') {
                    $query->where('is_admin', true);
                } elseif ($this->roleFilter === 'user') {
                    $query->where('is_admin', false);
                }
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ])->layout('layouts.admin')
            ->title('User Management');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
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

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'bio' => $data['bio'] ?? null,
            'is_admin' => (bool) $data['is_admin'],
            'email_verified_at' => $data['email_verified'] ? now() : null,
        ];

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if ($this->editing) {
            $user = User::findOrFail($this->userId);
            $user->update($payload);
            session()->flash('success', 'User updated successfully.');
        } else {
            if (empty($data['password'])) {
                $payload['password'] = Hash::make('password');
            }
            $user = User::create($payload);
            session()->flash('success', 'User created successfully.');
        }

        $user->roles()->sync($data['role_ids'] ?? []);
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if (auth()->id() === $id) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $hasLinkedRecords =
            BlogPost::where('author_id', $id)->exists() ||
            Course::where('instructor_id', $id)->exists() ||
            Enrollment::where('user_id', $id)->exists();

        if ($hasLinkedRecords) {
            session()->flash('error', 'Cannot delete user with linked LMS or content records.');
            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function toggleAdmin(int $id): void
    {
        if (auth()->id() === $id) {
            session()->flash('error', 'You cannot change your own admin role.');
            return;
        }

        $user = User::findOrFail($id);
        $user->update(['is_admin' => !$user->is_admin]);
    }

    public function cancel(): void
    {
        $this->resetForm();
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
