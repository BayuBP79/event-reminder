<?php

namespace App\Livewire\Event;

use Carbon\Carbon;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EventParticipant;

class Table extends Component
{
    use WithPagination;

    // form pop-up properties
    public $modalTitle = 'Add New Event';
    public $modalButtonText = 'Create';
    public $showModal = false;

    //filter prop
    public $perPage = 10,
        $search = '',
        $timeFilter = '',
        $sortBy = 'created_at',
        $sortDirection = 'DESC';

    //data properties
    public $eventId = null,
        $eventData = null,
        $participants = [['name' => '', 'email' => '']],
        $title = '',
        $description = '',
        $event_date,
        $currentUser;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'event_date' => 'required|date',
    ];

    public function mount()
    {
        $this->event_date = Carbon::now()->format('Y-m-d\TH:i');
        $this->currentUser = auth()->user()->id;
    }

    public function render()
    {
        return view('livewire.event.table', [
            'events' => Event::search($this->search)
                ->when($this->timeFilter !== '', function ($query) {
                    $query->where('category_id', $this->timeFilter);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage),
        ]);
    }

    public function editEvent($eventId)
    {
        $this->toggleModal();
        $this->modalTitle = 'Edit Category';
        $this->modalButtonText = 'Update';
        $this->eventId = $eventId;

        $this->eventData = Event::findOrFail($eventId);
        $this->title = $this->eventData->title;
        $this->description = $this->eventData->description;
        $this->event_date = Carbon::parse($this->event_date)->format('Y-m-d\TH:i');
        $this->participants = EventParticipant::where('event_id', $this->eventId)
            ->get(['name', 'email'])
            ->toArray();
    }

    public function addParticipant()
    {
        array_unshift($this->participants, ['name' => '', 'email' => '']);
    }

    public function removeParticipant($index)
    {
        unset($this->participants[$index]);
        $this->participants = array_values($this->participants);
    }

    public function save()
    {
        $this->validate();

        if ($this->eventId) {
            $this->eventData->update([
                'title' => $this->title,
                'user_id' => $this->currentUser,
                'description' => $this->description,
                'event_date' => Carbon::parse($this->event_date)->format('Y-m-d\TH:i'),
            ]);

            $existingParticipants = EventParticipant::where('event_id', $this->eventData->id)->pluck('email');
            $newParticipantEmails = array_column($this->participants, 'email');
            $emailsToRemove = $existingParticipants->diff($newParticipantEmails);
            EventParticipant::where('event_id', $this->eventData->id)
                ->whereIn('email', $emailsToRemove)
                ->delete();
        } else {
            $this->eventData = Event::create([
                'id' => Event::generateEventId(),
                'user_id' => $this->currentUser,
                'title' => $this->title,
                'description' => $this->description,
                'event_date' => Carbon::parse($this->event_date)->format('Y-m-d\TH:i'),
            ]);
        }

        foreach ($this->participants as $participant) {
            if (filter_var($participant['email'], FILTER_VALIDATE_EMAIL)) {
                EventParticipant::updateOrCreate(['event_id' => $this->eventData->id, 'email' => $participant['email']], ['event_id' => $this->eventData->id, 'email' => $participant['email'], 'name' => $participant['name']]);
            }
        }

        $this->toggleModal();
    }

    public function deleteEvent(Event $event)
    {
        if ($event) {
            $event->delete();
            session()->flash('message', 'Purchase Order deleted successfully!');
        } else {
            session()->flash('error', 'Purchase Order not found!');
        }

        $this->render();
    }

    public function setSortBy($sortBy)
    {
        if ($this->sortBy === $sortBy) {
            $this->sortDirection = $this->sortDirection === 'ASC' ? 'DESC' : 'ASC';
            return;
        }
        $this->sortBy = $sortBy;
        $this->sortDirection = 'DESC';
    }

    public function toggleModal()
    {
        $this->showModal = !$this->showModal;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['eventId', 'eventData', 'participants', 'title', 'description', 'event_date', 'modalTitle', 'modalButtonText']);
    }
}
