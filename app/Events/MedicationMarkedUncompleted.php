<?php

namespace App\Events;

use App\Models\Record;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicationMarkedUncompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Models\Record
     */
    public $record;

    /**
     * @var \App\Models\Medication
     */
    public $medication;

    /**
     * @var string|null
     */
    public $reasonNotTaken;

    /**
     * @var \App\Models\User
     */
    public $user;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Record $record
     * @param \App\Models\Medication $medication
     * @param string|null $reasonNotTaken
     * @param \App\Models\User $user
     */
    public function __construct(Record $record, Medication $medication, ?string $reasonNotTaken, User $user)
    {
        $this->record = $record;
        $this->medication = $medication;
        $this->reasonNotTaken = $reasonNotTaken;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}