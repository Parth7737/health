<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Hospital;
use App\Models\Medicine;
use App\Models\MedicineInteraction;
use App\Models\MedicineAllergyMapping;
use App\Models\Allergy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PharmacyClinicalSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $hospital;
    protected $medicineA;
    protected $medicineB;
    protected $allergy;

    protected function setUp(): void
    {
        parent::setUp();

        // Find or create hospital
        $this->hospital = Hospital::first();
        if (!$this->hospital) {
            $this->hospital = Hospital::create([
                'name' => 'Test Hospital',
            ]);
        }
        
        // Find or create hospital user with role Master Admin
        $this->user = User::where('email', 'admin@paracare.com')->first();
        if (!$this->user) {
            $this->user = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@paracare.com',
                'password' => \Hash::make('123456'),
                'hospital_id' => $this->hospital->id
            ]);
            $this->user->assignRole('Master Admin');
        } else {
            $this->user->hospital_id = $this->hospital->id;
            $this->user->save();
        }

        // Ensure permissions exist
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view-medicine', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create-medicine', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'delete-medicine', 'guard_name' => 'web']);

        // Grant permissions and clear cache
        $this->user->givePermissionTo(['view-medicine', 'create-medicine', 'delete-medicine']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Setup test medicines
        $this->medicineA = Medicine::firstOrCreate(
            ['name' => 'Test Medicine Alpha', 'hospital_id' => $this->hospital->id],
            ['requires_rx' => true, 'is_high_risk' => false]
        );

        $this->medicineB = Medicine::firstOrCreate(
            ['name' => 'Test Medicine Beta', 'hospital_id' => $this->hospital->id],
            ['requires_rx' => true, 'is_high_risk' => false]
        );

        // Setup test allergy
        $this->allergy = Allergy::firstOrCreate(
            ['name' => 'Test Allergy Class', 'hospital_id' => $this->hospital->id]
        );

        // Clean up any existing records for test medicines to ensure test is reproducible
        MedicineInteraction::where(function ($q) {
            $q->where('medicine_id', $this->medicineA->id)
              ->where('interact_medicine_id', $this->medicineB->id);
        })->orWhere(function ($q) {
            $q->where('medicine_id', $this->medicineB->id)
              ->where('interact_medicine_id', $this->medicineA->id);
        })->delete();

        MedicineAllergyMapping::where('medicine_id', $this->medicineA->id)
            ->where('allergy_id', $this->allergy->id)
            ->delete();
    }

    public function test_medicine_interactions_index_page_is_accessible()
    {
        $response = $this->actingAs($this->user)
            ->get(route('hospital.settings.pharmacy.medicine-interaction.index'));

        $response->assertStatus(200);
        $response->assertSee('Medicine Interactions');
    }

    public function test_medicine_interaction_crud_actions()
    {
        // 1. Create medicine interaction
        $response = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-interaction.store'), [
                'medicine_id' => $this->medicineA->id,
                'interact_medicine_id' => $this->medicineB->id,
                'severity' => 'major',
                'clinical_effect' => 'Potential interaction effect.',
                'recommendation' => 'Monitor patient closely.'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        
        $this->assertDatabaseHas('medicine_interactions', [
            'hospital_id' => $this->hospital->id,
            'medicine_id' => $this->medicineA->id,
            'interact_medicine_id' => $this->medicineB->id,
            'severity' => 'major'
        ]);

        $interaction = MedicineInteraction::where('medicine_id', $this->medicineA->id)
            ->where('interact_medicine_id', $this->medicineB->id)
            ->first();

        // 2. Load Table data
        $tableResponse = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-interaction-load'));
        $tableResponse->assertStatus(200);
        $tableResponse->assertJsonStructure(['data']);

        // 3. Edit / Show Form
        $formResponse = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-interaction.showform'), [
                'id' => $interaction->id
            ]);
        $formResponse->assertStatus(200);
        $formResponse->assertSee($this->medicineA->name);

        // 4. Update medicine interaction
        $updateResponse = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-interaction.store'), [
                'id' => $interaction->id,
                'medicine_id' => $this->medicineA->id,
                'interact_medicine_id' => $this->medicineB->id,
                'severity' => 'critical',
                'clinical_effect' => 'Critical interaction effect.',
                'recommendation' => 'Do not co-prescribe.'
            ]);
        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('medicine_interactions', [
            'id' => $interaction->id,
            'severity' => 'critical'
        ]);

        // 5. Delete medicine interaction
        $deleteResponse = $this->actingAs($this->user)
            ->delete(route('hospital.settings.pharmacy.medicine-interaction.destroy', ['medicine_interaction' => $interaction->id]));

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('medicine_interactions', [
            'id' => $interaction->id
        ]);
    }

    public function test_medicine_allergy_mappings_index_page_is_accessible()
    {
        $response = $this->actingAs($this->user)
            ->get(route('hospital.settings.pharmacy.medicine-allergy-mapping.index'));

        $response->assertStatus(200);
        $response->assertSee('Medicine Allergy Mappings');
    }

    public function test_medicine_allergy_mapping_crud_actions()
    {
        // 1. Create mapping
        $response = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-allergy-mapping.store'), [
                'medicine_id' => $this->medicineA->id,
                'allergy_id' => $this->allergy->id
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $this->assertDatabaseHas('medicine_allergy_mappings', [
            'hospital_id' => $this->hospital->id,
            'medicine_id' => $this->medicineA->id,
            'allergy_id' => $this->allergy->id
        ]);

        $mapping = MedicineAllergyMapping::where('medicine_id', $this->medicineA->id)
            ->where('allergy_id', $this->allergy->id)
            ->first();

        // 2. Load Table data
        $tableResponse = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-allergy-mapping-load'));
        $tableResponse->assertStatus(200);
        $tableResponse->assertJsonStructure(['data']);

        // 3. Edit / Show Form
        $formResponse = $this->actingAs($this->user)
            ->post(route('hospital.settings.pharmacy.medicine-allergy-mapping.showform'), [
                'id' => $mapping->id
            ]);
        $formResponse->assertStatus(200);
        $formResponse->assertSee($this->medicineA->name);

        // 4. Delete mapping
        $deleteResponse = $this->actingAs($this->user)
            ->delete(route('hospital.settings.pharmacy.medicine-allergy-mapping.destroy', ['medicine_allergy_mapping' => $mapping->id]));

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('medicine_allergy_mappings', [
            'id' => $mapping->id
        ]);
    }
}
