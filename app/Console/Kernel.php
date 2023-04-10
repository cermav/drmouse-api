<?php

namespace App\Console;

use App\Mail\AppointmentEmail;
use App\Models\Doctor;
use App\Models\PetAppointment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->call(function () {
            $todayPlusWeek = date_add(date_create(),
                date_interval_create_from_date_string
                ("7 days"));
            $todayPlusWeek = date_format($todayPlusWeek, "Y-m-d");

            $events = PetAppointment::where('date', $todayPlusWeek)
                ->whereNotNull('mail')
                ->get();

            foreach ($events as $event) {
                $email = $event->mail;
                Mail::to($email)->send(
                    new AppointmentEmail($event, Doctor::find
                    ($event->doctor_id))
                );
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
