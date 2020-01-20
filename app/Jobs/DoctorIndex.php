<?php

namespace App\Jobs;

use App\Doctor;
use App\Http\Controllers\HelperController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoctorIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // go throught all doctors
        foreach (Doctor::all() as $doctor) {
            // update doctor
            $doctor->update([
                'search_name' => HelperController::parseName( preg_replace('/\x93/', '', iconv("UTF-8", "UTF-8//IGNORE",  $doctor->user->name) ) ),
                'profile_completedness' => HelperController::calculateProfileCompletedness($doctor)
            ]);
            info($doctor->user->name);
        }
    }
}
