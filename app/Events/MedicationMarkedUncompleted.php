<?php

namespace App\Events;

use App\Models\Medication;
use App\Models\Record;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicationMarkedUncompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The record instance.
     *
     * @var \App\Models\Record
     */
    public $record;

    /**
     * The medication instance.
     *
     * @var \App\Models\Medication
     */
    public $medication;

    /**
     * The reason not taken.
     *
     * @var string|null
     */
    public $reasonNotTaken;

    /**
     * The user instance.
     *
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