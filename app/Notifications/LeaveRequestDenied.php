<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestDenied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public LeaveRequest $leaveRequest
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $manager = $this->leaveRequest->manager;
        $leaveTypeFormatted = str_replace('_', ' ', ucwords($this->leaveRequest->leave_type, '_'));

        return (new MailMessage)
            ->subject('Leave Request Denied')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your leave request has been denied by '.$manager->name.'.')
            ->line('**Leave Details:**')
            ->line('Leave Type: '.$leaveTypeFormatted)
            ->line('Dates: '.$this->leaveRequest->start_date->format('M d, Y').' - '.$this->leaveRequest->end_date->format('M d, Y'))
            ->line('Duration: '.$this->leaveRequest->total_days.' working day(s)')
            ->line('**Reason for Denial:**')
            ->line($this->leaveRequest->manager_notes)
            ->action('View Request', route('leave-requests.show', $this->leaveRequest))
            ->line('Your leave balance has been restored.')
            ->line('If you have questions about this decision, please contact your manager.')
            ->salutation('Best regards, Horizon Sentinel');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'manager_id' => $this->leaveRequest->manager_id,
            'manager_name' => $this->leaveRequest->manager->name,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->format('Y-m-d'),
            'end_date' => $this->leaveRequest->end_date->format('Y-m-d'),
            'total_days' => $this->leaveRequest->total_days,
            'manager_notes' => $this->leaveRequest->manager_notes,
            'action_url' => route('leave-requests.show', $this->leaveRequest),
            'message' => 'Your leave request has been denied',
        ];
    }
}
