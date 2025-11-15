<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification
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
        $employee = $this->leaveRequest->user;
        $leaveTypeFormatted = str_replace('_', ' ', ucwords($this->leaveRequest->leave_type, '_'));

        return (new MailMessage)
            ->subject('New Leave Request Pending Your Review')
            ->greeting('Hello '.$notifiable->name.',')
            ->line($employee->name.' has submitted a new leave request that requires your review.')
            ->line('**Leave Details:**')
            ->line('Employee: '.$employee->name)
            ->line('Leave Type: '.$leaveTypeFormatted)
            ->line('Dates: '.$this->leaveRequest->start_date->format('M d, Y').' - '.$this->leaveRequest->end_date->format('M d, Y'))
            ->line('Duration: '.$this->leaveRequest->total_days.' working day(s)')
            ->when($this->leaveRequest->employee_notes, function ($message) {
                return $message->line('Employee Notes: '.$this->leaveRequest->employee_notes);
            })
            ->action('Review Request', route('manager.show-request', $this->leaveRequest))
            ->line('Please review this request at your earliest convenience.')
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
            'employee_id' => $this->leaveRequest->user_id,
            'employee_name' => $this->leaveRequest->user->name,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->format('Y-m-d'),
            'end_date' => $this->leaveRequest->end_date->format('Y-m-d'),
            'total_days' => $this->leaveRequest->total_days,
            'action_url' => route('manager.show-request', $this->leaveRequest),
            'message' => $this->leaveRequest->user->name.' submitted a leave request for review',
        ];
    }
}
