<?php

namespace App\Events;

use App\Models\Medication;
use App\Models\Record;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicationMarkedUncompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Record $record;
    public Medication $medication;
    public ?string $reason;
    public User $user;

    /**
     * Create a new event instance.
     *
     * @param Record $record イベントに関連する内服記録
     * @param Medication $medication 未完了とマークされた薬
     * @param string|null $reason 未完了の理由（任意）
     * @param User $user 未完了とマークしたユーザー
     */
    public function __construct(Record $record, Medication $medication, ?string $reason, User $user)
    {
        $this->record = $record;
        $this->medication = $medication;
        $this->reason = $reason;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     * 現時点ではブロードキャストはしないので空の配列を返します。
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
