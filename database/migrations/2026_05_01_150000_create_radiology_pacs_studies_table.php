<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_pacs_studies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->nullable()->index();
            $table->unsignedBigInteger('diagnostic_order_item_id')->nullable()->index();
            $table->string('accession_no', 100)->nullable()->index();
            $table->string('patient_identifier', 100)->nullable()->index();
            $table->string('study_instance_uid', 128)->unique();
            $table->string('modality', 32)->nullable();
            $table->string('status', 32)->default('received')->index();
            $table->string('source', 40)->default('modality');
            $table->text('viewer_url')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_pacs_studies');
    }
};
