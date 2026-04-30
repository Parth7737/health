<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{ User, Hospital, ExpiredDocument, Notifications, HospitalSpeciality};
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentExpiryMail;


class DocumentExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'document:expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToCheck = [60, 45, 30, 15, 10, 5, 1];
        // echo Carbon::today()->addDays(5);
        // echo Carbon::today()->addDays(2);
        // exit;
        foreach ($daysToCheck as $daysLeft) {
            Hospital::where('status', 'Empanelled')->chunk(50, function ($hospitals) use ($daysLeft) {
                foreach ($hospitals as $hospital) {
                    $licenses = $hospital->licenses()->whereDate('expiry_date', Carbon::today()->addDays($daysLeft))->get();
                    foreach ($licenses as $license) {
                        $refTable = 'hospital_licenses';
                        if ($daysLeft == 1) {
                            $hospital->status = 'In-Active';
                            $hospital->save();
                        } else {
                            $this->sendNotification($hospital, $license, $daysLeft, $refTable);
                        }
                    }

                    $hospitalAccreditation = $hospital->hospitalAccreditation()->whereDate('valid_till', Carbon::today()->addDays($daysLeft))->get();
                    foreach ($hospitalAccreditation as $Accreditation) {
                        $refTable = 'hospital_accreditations';
                        if ($daysLeft == 1) {
                            $hospital->status = 'In-Active';
                            $hospital->save();
                        } else {
                            $this->sendAccrNotification($hospital, $Accreditation, $daysLeft, $refTable);
                        }
                    }

                    $specialities = $hospital->hospitalTeam()->whereDate('registration_certificate_expiry', Carbon::today()->addDays($daysLeft))->get();
                    foreach ($specialities as $key => $speciality) {
                        $refTable = 'hospital_teams';
                        if ($daysLeft == 1) {
                            $hospital->status = 'In-Active';
                            $hospital->save();
                        } else {
                            $this->sendspecialitiesNotification($hospital, $speciality, $daysLeft, $refTable);
                        }
                    }

                }
            });
        }
    }

    private function sendspecialitiesNotification($hospital, $speciality, $daysLeft, $refTable) {
        $notification = Notifications::create([
            'user_id' => $hospital->user_id,
            'hospital_id' => $hospital->id,
            'type' => 'DocumentExpired',
            'date' => Carbon::today()->format('Y-m-d'),
            'message' => "Your Hospital( ".$hospital->facility_name."-".$hospital->hospital_id.") document (Name: Registration Certificate) expires in {$daysLeft} days.",
        ]);

        // Check if notification exists for the same document
        $expiredDocument = ExpiredDocument::updateOrCreate(
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $speciality->id,
                'document_ref_table' => $refTable,
            ],
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $speciality->id,
                'document_ref_table' => $refTable,
                'document_name' => 'Registration Certificate',
                'notification_id' => $notification->id,
                'expiry_date' => $speciality->registration_certificate_expiry,
            ]
        );

        $notification->update(['ref_id' => $expiredDocument->id]);

        $userdata = User::find($hospital->user_id);
        $data['facility_name'] = $hospital->facility_name;
        $data['document_name'] =  'Registration Certificate';
        // $data['message'] = $message;
        $data['daysLeft'] = $daysLeft;
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$speciality->registration_certificate); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new DocumentExpiryMail($data));
        } catch (\Exception $e) {
            
        }

        $this->info("Notification sent for hospital ID: {$hospital->id}, document: Registration Certificate (expires in $daysLeft days)");
    }

    private function sendAccrNotification($hospital, $accrediation, $daysLeft, $refTable) {
        $notification = Notifications::create([
            'user_id' => $hospital->user_id,
            'hospital_id' => $hospital->id,
            'type' => 'DocumentExpired',
            'date' => Carbon::today()->format('Y-m-d'),
            'message' => "Your Hospital( ".$hospital->facility_name."-".$hospital->hospital_id.") document (Name: {$accrediation->accred->name}) expires in {$daysLeft} days.",
        ]);

        // Check if notification exists for the same document
        $expiredDocument = ExpiredDocument::updateOrCreate(
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $accrediation->id,
                'document_ref_table' => $refTable,
            ],
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $accrediation->id,
                'document_ref_table' => $refTable,
                'document_name' => $accrediation->accred->name,
                'notification_id' => $notification->id,
                'expiry_date' => $accrediation->valid_till,
            ]
        );

        $userdata = User::find($hospital->user_id);
        // Link notification to the expired document
        $notification->update(['ref_id' => $expiredDocument->id]);
        $data['facility_name'] = $hospital->facility_name;
        $data['document_name'] = $accrediation->accred->name;
        // $data['message'] = $message;
        $data['daysLeft'] = $daysLeft;
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$license->certificate); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new DocumentExpiryMail($data));
        } catch (\Exception $e) {
            
        }
        $this->info("Notification sent for hospital ID: {$hospital->id}, document: {$accrediation->accred->name} (expires in $daysLeft days)");
    }

    private function sendNotification($hospital, $license, $daysLeft, $refTable)
    {
        $message =  "Your document (Name: {$license->licenseType->name}) expires in {$daysLeft} days.";
        $notification = Notifications::create([
            'user_id' => $hospital->user_id,
            'hospital_id' => $hospital->id,
            'type' => 'DocumentExpired',
            'date' => Carbon::today()->format('Y-m-d'),
            'message' => "Your Hospital( ".$hospital->facility_name."-".$hospital->hospital_id.") document (Name: {$license->licenseType->name}) expires in {$daysLeft} days.",
        ]);

        // Check if notification exists for the same document
        $expiredDocument = ExpiredDocument::updateOrCreate(
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $license->id,
                'document_ref_table' => $refTable,
            ],
            [
                'user_id' => $hospital->user_id,
                'hospital_id' => $hospital->id,
                'document_id' => $license->id,
                'document_ref_table' => $refTable,
                'document_name' => $license->licenseType->name,
                'notification_id' => $notification->id,
                'expiry_date' => $license->expiry_date,
            ]
        );

        $userdata = User::find($hospital->user_id);
        // Link notification to the expired document
        $notification->update(['ref_id' => $expiredDocument->id]);
        $data['facility_name'] = $hospital->facility_name;
        $data['document_name'] = $license->licenseType->name;
        // $data['message'] = $message;
        $data['daysLeft'] = $daysLeft;
        $data['userdata'] = $userdata;
        $filePath = asset('public/storage/'.$license->document); // Path to your document
        $data['filePath'] = $filePath;

        try {
            Mail::to($userdata->email)->send(new DocumentExpiryMail($data));
        } catch (\Exception $e) {
            
        }

        $this->info("Notification sent for hospital ID: {$hospital->id}, document: {$license->licenseType->name} (expires in $daysLeft days)");
    }
}
