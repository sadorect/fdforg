<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Support\AdminPermissions;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class EventManager extends AdminComponent
{
    use WithFileUploads;
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_EVENTS];

    public $search = '';

    public $statusFilter = '';

    public $showForm = false;

    public $editing = false;

    public $eventId;

    public $title = '';

    public $slug = '';

    public $description = '';

    public $excerpt = '';

    public $start_date = '';

    public $end_date = '';

    public $time = '';

    public $location = '';

    public $venue = '';

    public $price = '';

    public $registration_required = false;

    public $image = null;

    public $existingImagePath = null;

    public $status = 'upcoming';

    public $is_virtual = false;

    public $meeting_link = '';

    public $max_attendees = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $events = Event::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('location', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->orderBy('start_date', 'desc')
            ->paginate(12);

        return view('livewire.admin.event-manager', [
            'events' => $events,
        ])->layout('layouts.admin')
            ->title('Event Management');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $event = Event::findOrFail($id);

        $this->eventId = $event->id;
        $this->title = $event->title;
        $this->slug = $event->slug;
        $this->description = $event->description;
        $this->excerpt = $event->excerpt ?? '';
        $this->start_date = $event->start_date?->format('Y-m-d\TH:i') ?? '';
        $this->end_date = $event->end_date?->format('Y-m-d\TH:i') ?? '';
        $this->time = $event->time ?? '';
        $this->location = $event->location ?? '';
        $this->venue = $event->venue ?? '';
        $this->price = $event->price ?? '';
        $this->registration_required = (bool) $event->registration_required;
        $this->image = null;
        $this->existingImagePath = $event->image;
        $this->status = $event->status;
        $this->is_virtual = (bool) $event->is_virtual;
        $this->meeting_link = $event->meeting_link ?? '';
        $this->max_attendees = $event->max_attendees ?? '';

        $this->showForm = true;
        $this->editing = true;
    }

    public function save(): void
    {
        $data = $this->validate($this->rules());

        if (! $data['is_virtual']) {
            $data['meeting_link'] = null;
        }

        if ($this->max_attendees === '' || $this->max_attendees === null) {
            $data['max_attendees'] = null;
        }

        if ($this->image) {
            $data['image'] = $this->image->store('events', 'public');
        } elseif ($this->editing) {
            $data['image'] = $this->existingImagePath;
        } else {
            $data['image'] = null;
        }

        unset($data['existingImagePath']);

        if ($this->editing) {
            Event::findOrFail($this->eventId)->update($data);
            session()->flash('success', 'Event updated successfully.');
        } else {
            Event::create($data);
            session()->flash('success', 'Event created successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Event::findOrFail($id)->delete();
        session()->flash('success', 'Event deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editing) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('events', 'slug')->ignore($this->eventId),
            ],
            'description' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'time' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'venue' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:255'],
            'registration_required' => ['boolean'],
            'image' => ['nullable', 'image', 'max:4096', 'mimes:jpg,jpeg,png,webp'],
            'status' => ['required', Rule::in(['upcoming', 'featured', 'past', 'cancelled'])],
            'is_virtual' => ['boolean'],
            'meeting_link' => ['nullable', 'url', 'max:2048'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'eventId',
            'title',
            'slug',
            'description',
            'excerpt',
            'start_date',
            'end_date',
            'time',
            'location',
            'venue',
            'price',
            'registration_required',
            'image',
            'existingImagePath',
            'status',
            'is_virtual',
            'meeting_link',
            'max_attendees',
            'showForm',
            'editing',
        ]);
        $this->status = 'upcoming';
        $this->is_virtual = false;
    }
}
