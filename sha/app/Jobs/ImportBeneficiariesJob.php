<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportBeneficiariesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Insert into DB in chunks
        
        // $datareq = array('time'=>date('Y-m-d H:i:s'));
        // $myfile = fopen(public_path()."/ben-logs-queue.txt", "a") or die("Unable to open file!");
        // fwrite($myfile,json_encode($datareq));
        // fclose($myfile);
        DB::table('benificiaries')->insert($this->data);
    }
}
