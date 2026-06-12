<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HospitalPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create modules for hospital features
        $modules = [
            //visitor
            'Visitors' => 'visitor',

            // Billing & Finance
            'Billing & Finance' => 'billing-and-finance',

            //Patient Management
            'Patient Management' => 'patient-management',

            // Front Office
            // 'Appointments' => 'appointments',
            // 'Visitor Purpose' => 'visitor-purposes',
            // 'Complain Type' => 'complain-types',
            // 'Complain Source' => 'complain-sources',
            // 'Appointment Priority' => 'appointment-priorities',
            
            //hospital charges
            'Charge Masters' => 'charge-masters',
            'Doctor OPD Charges' => 'doctor-opd-charges',

            // Masters
            'Religion' => 'religion',
            'Dietary' => 'dietary',
            'Allergy' => 'allergy',
            // 'Allergy Reaction' => 'allergy-reaction',
            'Habit' => 'habits',
            'Disease' => 'diseases',
            'Disease Type' => 'disease-types',

            //Pharmacy
            'Medicine' => 'medicine',
            'Medicine Interactions' => 'medicine-interaction',
            'Medicine Allergy Mapping' => 'medicine-allergy-mapping',
            'Medicine Category' => 'medicine-category',
            'Medicine Dosage' => 'medicine-dosage',
            'Medicine Unit' => 'medicine-unit',
            'Medicine Instructions' => 'medicine-instructions',
            'Frequency' => 'frequency',
            'Pharmacy Purchase' => 'pharmacy-purchase',
            'Pharmacy GRN' => 'pharmacy-grn',
            'Pharmacy Sale' => 'pharmacy-sale',
            'Pharmacy Stock' => 'pharmacy-stock',
            'Pharmacy Expiry' => 'pharmacy-expiry',
            'Pharmacy Bad Stock' => 'pharmacy-bad-stock',
            'Pharmacy Supplier' => 'pharmacy-supplier',

            // Nursing
            'Nursing MAR' => 'nursing-mar',

            // Pathology
            'Pathology Category' => 'pathology-category',
            'Pathology Unit' => 'pathology-unit',
            'Pathology Parameter' => 'pathology-parameter',
            // 'Pathology Status' => 'pathology-status',
            'Pathology Age Group' => 'pathology-age-group',
            'Pathology Test' => 'pathology-test',
            'Pathology Order' => 'pathology-order',
            'Pathology Report' => 'pathology-report',

            // Radiology
            'Radiology Category' => 'radiology-category',
            'Radiology Unit' => 'radiology-unit',
            'Radiology Parameter' => 'radiology-parameter',
            'Radiology Test' => 'radiology-test',
            'Radiology Order' => 'radiology-order',
            'Radiology Report' => 'radiology-report',

            // Symptoms
            'Symptoms Type' => 'symptoms-type',
            'Symptoms' => 'symptoms',

            // HR
            'HR Designation' => 'hr-designation',
            'HR Specialist' => 'hr-specialist',
            'HR Department' => 'hr-department',
            'HR Department Unit' => 'hr-department-unit',
            'HR Leave Type' => 'hr-leave-type',
            'HR Training Category' => 'hr-training-category',
            'HR Recruitment' => 'hr-recruitment',

            // Beds Management
            'Bed Type' => 'bed-type',
            'Floor' => 'floor',
            'Building' => 'building',
            'Ward' => 'ward',
            'Room' => 'room',
            'Bed' => 'bed',

            //Staff
            'Staff' => 'staff',

            // TPA Management
            'TPA' => 'tpa',

            // Print Header Footer
            'Print Header Footer' => 'header-footer',

            // Masters
            'Floor' => 'floor',
            'Patient Category' => 'patient-category',
        ];

        foreach ($modules as $moduleName => $moduleSlug) {
            $module = Module::firstOrCreate(
                ['name' => $moduleName],
                ['name' => $moduleName]
            );

            // Create CRUD permissions for each module
            $permissions = [
                'view' => "Can view {$moduleSlug}",
                'create' => "Can create {$moduleSlug}",
                'edit' => "Can edit {$moduleSlug}",
                'delete' => "Can delete {$moduleSlug}",
            ];

            foreach ($permissions as $action => $description) {
                Permission::firstOrCreate(
                    ['name' => "{$action}-{$moduleSlug}"],
                    [
                        'name' => "{$action}-{$moduleSlug}",
                        'module' => $module->id,
                        'guard_name' => 'web',
                    ]
                );
            }
        }

        $recruitmentPermissions = [
            'view-hr-recruitment',
            'create-hr-recruitment',
            'edit-hr-recruitment',
            'delete-hr-recruitment',
        ];
        foreach (['Master Admin', 'Administrator', 'Chairman'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($recruitmentPermissions);
            }
        }

        $trainingCategoryPermissions = [
            'view-hr-training-category',
            'create-hr-training-category',
            'edit-hr-training-category',
            'delete-hr-training-category',
        ];
        foreach (['Master Admin', 'Administrator', 'Chairman'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($trainingCategoryPermissions);
            }
        }

        $nursingMarPermissions = [
            'view-nursing-mar',
            'create-nursing-mar',
            'edit-nursing-mar',
            'delete-nursing-mar',
        ];
        foreach (['Master Admin', 'Administrator', 'Chairman'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($nursingMarPermissions);
            }
        }

        // Hospital Data
        $manual_modules = [
            'hospital-data' => ['view', 'edit'],
            'opd-payment' => ['delete'],
            'roles' => ['manage'],
            'pharmacy-rx-validation' => ['approve', 'reject', 'escalate'],
        ];

        foreach ($manual_modules as $moduleSlug => $actions) {
            $module = Module::firstOrCreate(
                ['name' => ucwords(str_replace('-', ' ', $moduleSlug))],
                ['name' => ucwords(str_replace('-', ' ', $moduleSlug))]
            );

            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' =>  "{$action}-{$moduleSlug}",
                    'module' => $module->id,
                    'guard_name' => 'web',
                ]);
            }
        }

        $rxValidationPermissions = [
            'approve-pharmacy-rx-validation',
            'reject-pharmacy-rx-validation',
            'escalate-pharmacy-rx-validation',
        ];
        foreach (['Master Admin', 'Administrator', 'Chairman'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($rxValidationPermissions);
            }
        }

        // Purchase manual permissions
        $purchaseManualPermissions = [
            'approve-pharmacy-purchase',
            'reject-pharmacy-purchase',
        ];
        
        $purchaseModule = Module::where('name', 'Pharmacy Purchase')->first();
        $purchaseModuleId = $purchaseModule ? $purchaseModule->id : null;
        
        foreach ($purchaseManualPermissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'name' => $permissionName,
                    'module' => $purchaseModuleId,
                    'guard_name' => 'web',
                ]
            );
        }

        foreach (['Master Admin', 'Administrator', 'Chairman'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->first();
            if ($role) {
                $role->givePermissionTo($purchaseManualPermissions);
            }
        }

    }
}

