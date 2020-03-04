<?php

namespace App\Jobs;

use App\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoctorImage implements ShouldQueue
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
            try {

                $imageLink = 'https://api.drmouse.cz/storage/profile/' . $doctor->user->avatar;

                // Open file
                $handle = @fopen($imageLink, 'r');

                // Check if file exists
                if(!$handle){
                    // update doctor
                    $doctor->user->update([
                        'avatar' => null
                    ]);
                    echo $imageLink . ' not found';
                }else{
                    echo "found";
                    fclose($handle);
                }

/*
                // get image path
                dd($imageLink);


                info($doctor->user->name);
*/
            }
            catch (\Exception $ex) {
                dd($ex);
            }
        }
    }
}
