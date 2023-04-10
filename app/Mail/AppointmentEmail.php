<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $doctor;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($event, $doctor)
    {
        // fill data
        $this->event = $event;
        $this->doctor = $doctor;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.appointment-notification')
            ->with([
                'title' => $this->event->title,
                'start' => $this->event->start,
                'end' => $this->event->end,
                'vet' => $this->doctor->search_name,
                'city' => $this->doctor->city,
                'street' => $this->doctor->street,
                'PSC' => $this->doctor->post_code,
                'phone' => $this->doctor->phone,
            ]);
    }


}
