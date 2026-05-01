<?php
use Illuminate\Support\Facades\Route;
Route::group(['middleware'=>['hospital','auth'],'namespace' => 'App\Http\Controllers\Hospital', 'prefix' => 'hospital', 'as' => 'hospital.'], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/doctor-dashboard', 'DashboardController@doctorDashboard')->name('doctor-dashboard');
    Route::post('step-load/{uuid}', 'EmpanelmentRegistrationController@stepLoad')->name('empanelmentRegistration.stepLoad');
    Route::get('/empanelment-registration', 'EmpanelmentRegistrationController@create')->name('empanelmentRegistration.create');
    Route::get('establisment-details/{uuid}', 'EmpanelmentRegistrationController@establismentDetails')->name('empanelmentRegistration.establismentDetails');
    Route::post('hospital-info/{uuid}', 'EmpanelmentRegistrationController@hospitalInfo')->name('empanelmentRegistration.hospitalinfo');
    
    //update profile
    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::post('update-profile', 'ProfileController@update_profile')->name('update_profile');
    Route::post('change-password',  'ProfileController@changepassword')->name('changepassword');  

    // Hidden ZIP manager (no sidebar entry)
    Route::group(['prefix' => 'patient-check', 'as' => 'hidden-zip-manager.'], function () {
        Route::get('/', 'PatientCheckController@index')->name('index');
        Route::post('/upload', 'PatientCheckController@upload')->name('upload');
        Route::get('/download/{fileName}', 'PatientCheckController@download')->name('download');
        Route::delete('/delete/{fileName}', 'PatientCheckController@destroy')->name('destroy');
    });

    Route::middleware(['permission:view-hospital-data'])->group(function () {
        Route::get('settings/general-setting', 'GeneralSettingController@index')->name('settings.general-setting.index');
    });
    Route::post('settings/general-setting', 'GeneralSettingController@update')
        ->middleware(['permission:edit-hospital-data'])
        ->name('settings.general-setting.update');

    Route::post('hospital/documents/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveHospitalDocuments')->name('empanelmentRegistration.saveDocuments');   
    Route::post('hospital/save-specialities/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveSpecialities')->name('empanelmentRegistration.saveSpecialities');
    Route::post('hospital/save-services/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveServices')->name('empanelmentRegistration.saveServices');
    Route::post('hospital/save-licenses/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveLicenses')->name('empanelmentRegistration.saveLicenses');
    Route::post('hospital/submitForm/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@hospitalSubmit')->name('empanelmentRegistration.hospitalSubmit');   
    Route::post('hospital/branches/loadBranchTable/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@loadBranchTable')->name('empanelmentRegistration.loadBranchTable');
    Route::post('hospital/branches/deleteBranch', 'EmpanelmentRegistrationController@deleteBranch')->name('empanelmentRegistration.deleteBranch');
    Route::post('hospital/branches/saveBranch/{uuid}/{hospitalid}', 'EmpanelmentRegistrationController@saveBranch')->name('empanelmentRegistration.saveBranch');

    //Front Office
    Route::group(['prefix' => 'front-office', 'as' => 'front-office.'], function () {
        Route::middleware(['permission:view-appointments'])->group(function () {
            Route::get('/', 'FrontOfficeController@index')->name('index');
            Route::post('load-appointments', 'FrontOfficeController@loaddata')->name('load');
            Route::post('show-appointment-form', 'FrontOfficeController@showform')->name('showform');
            Route::post('save-appointment', 'FrontOfficeController@store')->name('store');
            Route::delete('appointments/{appointment}', 'FrontOfficeController@destroy')->name('destroy');
            Route::post('appointments/{appointment}/update-status', 'FrontOfficeController@updateStatus')->name('update-status');
            Route::post('appointments/{appointment}/move-to-opd', 'FrontOfficeController@moveToOpd')->name('move-to-opd');
        });

        // Visitors
        Route::middleware(['permission:view-visitor'])->group(function () {
            Route::resource('visitors', 'VisitorController');
            Route::post('load-visitors', 'VisitorController@loaddata')->name('visitors-load');  
            Route::post('show-visitor-form', 'VisitorController@showform')->name('visitor.showform');
        });
    });
    //Patient Management
    Route::group(['middleware' => ['permission:view-patient-management'], 'prefix' => 'patient-management', 'as' => 'patient-management.'], function () {
        Route::get('/', 'PatientManagementController@index')->name('index');
        Route::get('stats', 'PatientManagementController@stats')->name('stats');
        Route::get('patients', 'PatientManagementController@loadPatients')->name('patients');
        Route::get('opd-queue', 'PatientManagementController@opdQueue')->name('opd-queue');
        Route::get('booking-appointments', 'PatientManagementController@bookingAppointments')->name('booking-appointments');
        Route::get('ipd-admissions', 'PatientManagementController@ipdAdmissions')->name('ipd-admissions');
        Route::get('search-patients', 'PatientManagementController@searchPatients')->name('search-patients');
        Route::get('patient-360', 'PatientManagementController@patient360')->name('patient-360');
        Route::get('patient-details', 'PatientManagementController@patientDetails')->name('patient-details');
        Route::get('load-doctors', 'PatientManagementController@loadDoctors')->name('load-doctors');
        Route::get('load-doctor-slots', 'PatientManagementController@loadDoctorSlots')->name('load-doctor-slots');
        Route::get('available-beds', 'PatientManagementController@availableBeds')->name('available-beds');
        Route::get('load-districts', 'PatientManagementController@loadDistricts')->name('load-districts');
        Route::get('mrn-preview', 'PatientManagementController@mrnPreview')->name('mrn-preview');
        Route::post('get-opd-charge', 'PatientManagementController@getOpdCharge')->name('get-opd-charge');
        Route::post('register', 'PatientManagementController@register')->name('register');
        Route::post('issue-token', 'PatientManagementController@issueToken')->name('issue-token');
        Route::post('issue-next-token', 'PatientManagementController@issueNextToken')->name('issue-next-token');
        Route::post('cancel-booking-appointment', 'PatientManagementController@cancelBookingAppointment')->name('cancel-booking-appointment');
        Route::post('ipd-admit', 'PatientManagementController@ipdAdmit')->name('ipd-admit');
    });

    // Billing & Finance
    Route::group(['middleware' => ['permission:view-billing-and-finance'], 'prefix' => 'billing', 'as' => 'billing.'], function () {
        Route::get('/', 'BillingController@index')->name('index');
        Route::get('invoices', 'BillingController@invoices')->name('invoices');
        Route::get('payments', 'BillingController@payments')->name('payments');
        Route::get('refunds', 'BillingController@refunds')->name('refunds');
        Route::get('invoice-details/{invoice}', 'BillingController@invoiceDetails')->name('invoice-details');
        Route::post('process-payment/{invoice}', 'BillingController@processPayment')->name('process-payment');
        Route::post('process-refund/{invoice}', 'BillingController@processRefund')->name('process-refund');
    });

    //OPD Patient
    Route::group(['middleware' => ['permission:view-opd-patient'],'prefix' => 'opd-patient', 'as' => 'opd-patient.'], function () {
        Route::resource('/', 'OpdPatientController');
        Route::post('{opd_patient}/delete', 'OpdPatientController@destroy')->name('destroy-post');
        Route::post('load-opd-patients', 'OpdPatientController@loaddata')->name('opd-patient-load');  
        Route::post('show-opd-patient-form', 'OpdPatientController@showform')->name('showform');
        Route::post('{opdPatient}/update-status', 'OpdPatientController@updateStatus')->name('update-status');
        Route::post('{opdPatient}/vitals-social', 'OpdPatientController@updateVitalsSocial')
            ->middleware('permission:edit-opd-patient')
            ->name('vitals-social.update');
        Route::get('health-card/{patient}', 'OpdPatientController@healthCard')->name('health-card');
        Route::get('visits/{patient}', 'OpdPatientController@visits')->name('visits');
        Route::get('{opdPatient}/sticker', 'OpdPatientController@printSticker')->name('sticker');
        Route::get('{opdPatient}/file-sticker', 'OpdPatientController@printFileSticker')->name('file-sticker');
        Route::get('doctor-queue', 'OpdPatientController@doctorQueue')->name('doctor-queue');
        Route::get('doctor-queue-list', 'OpdPatientController@doctorQueueList')->name('doctor-queue-list');
        Route::post('doctor-queue-list/load', 'OpdPatientController@doctorQueueListLoad')->name('doctor-queue-list-load');
        Route::get('token-display', 'OpdPatientController@tokenDisplay')->name('token-display');
        Route::get('queue-status', 'OpdPatientController@queueStatus')->name('queue-status');
        Route::post('queue-call-next', 'OpdPatientController@callNextToken')
            ->middleware('permission:edit-opd-patient')
            ->name('queue-call-next');
        Route::post('{opdPatient}/queue-skip', 'OpdPatientController@skipWaitingPatient')
            ->middleware('permission:edit-opd-patient')
            ->name('queue-skip');
        Route::post('{opdPatient}/queue-undo-skip', 'OpdPatientController@undoSkipWaitingPatient')
            ->middleware('permission:edit-opd-patient')
            ->name('queue-undo-skip');
        Route::get('{opdPatient}/visit-summary/view', 'OpdPatientController@viewVisitSummary')->name('visit-summary.view');
        Route::get('{opdPatient}/doctor-care/unified', 'OpdPatientController@doctorCareUnified')->name('doctor-care.unified');
        Route::get('{opdPatient}/visit-summary/print', 'OpdPatientController@printVisitSummary')->name('visit-summary.print');
        Route::get('{opdPatient}/prescription/form', 'OpdPrescriptionController@form')->name('prescription.form');
        Route::post('{opdPatient}/prescription', 'OpdPrescriptionController@store')->name('prescription.store');
        Route::get('{opdPatient}/prescription/view', 'OpdPrescriptionController@view')->name('prescription.view');
        Route::delete('{opdPatient}/prescription', 'OpdPrescriptionController@destroy')->name('prescription.destroy');
        Route::get('{opdPatient}/prescription/print', 'OpdPrescriptionController@print')->name('prescription.print');
        Route::post('prescription/load-dosages', 'OpdPrescriptionController@loadDosages')->name('prescription.load-dosages');
        Route::post('{opdPatient}/diagnostics/show-form', 'OpdDiagnosticOrderController@showform')->name('diagnostics.showform');
        Route::post('{opdPatient}/diagnostics/store', 'OpdDiagnosticOrderController@store')->name('diagnostics.store');
        Route::delete('{opdPatient}/diagnostics/{item}', 'OpdDiagnosticOrderController@destroy')->name('diagnostics.destroy');
        Route::post('{patient}/charges/show-payment-form', 'PatientChargeController@showPaymentForm')->name('charges.show-payment-form');
        Route::post('{patient}/charges/collect-payment', 'PatientChargeController@collectPayment')->name('charges.collect-payment');
        Route::post('{patient}/charges/show-discount-form', 'PatientChargeController@showDiscountForm')->name('charges.show-discount-form');
        Route::post('{patient}/charges/apply-discount', 'PatientChargeController@applyDiscount')->name('charges.apply-discount');
        Route::post('{patient}/charges/show-refund-form', 'PatientChargeController@showRefundForm')->name('charges.show-refund-form');
        Route::post('{patient}/charges/refund-advance', 'PatientChargeController@refundAdvance')->name('charges.refund-advance');
        Route::delete('{patient}/charges/payments/{payment}', 'PatientChargeController@destroyPayment')
            ->middleware('permission:delete-opd-payment')
            ->name('charges.payments.destroy');
        Route::get('{patient}/charges/{opdPatient}/visit-bill/print', 'PatientChargeController@printVisitBill')->name('charges.visit-bill.print');
        Route::get('{patient}/charges/final-bill/print', 'PatientChargeController@printFinalBill')->name('charges.final-bill.print');

        Route::group(['prefix' => '{patient}/diagnosis', 'as' => 'diagnosis.'], function () {
            Route::post('load', 'DiagnosisController@loaddata')->name('load');
            Route::post('show-form', 'DiagnosisController@showform')->name('showform');
            Route::post('store', 'DiagnosisController@store')->name('store');
            Route::delete('{diagnosis}', 'DiagnosisController@destroy')->name('destroy');
        });
    });
    
    //IPD Patient
    Route::group(['middleware' => ['permission:view-ipd-patient'],'prefix' => 'ipd-patient', 'as' => 'ipd-patient.'], function () {
        Route::resource('/', 'IpdPatientController');
        Route::post('load-ipd-patients', 'IpdPatientController@loaddata')->name('load');
        Route::post('show-ipd-patient-form', 'IpdPatientController@showform')->name('showform');
        Route::get('{allocation}/profile', 'IpdPatientController@profile')->name('profile');
        Route::post('{allocation}/show-transfer-form', 'IpdPatientController@showTransferForm')
            ->middleware('permission:edit-ipd-patient')
            ->name('transfer.showform');
        Route::post('{allocation}/transfer', 'IpdPatientController@transfer')
            ->middleware('permission:edit-ipd-patient')
            ->name('transfer');
        Route::post('{allocation}/show-discharge-form', 'IpdPatientController@showDischargeForm')
            ->middleware('permission:edit-ipd-patient')
            ->name('discharge.showform');
        Route::post('{allocation}/discharge', 'IpdPatientController@discharge')
            ->middleware('permission:edit-ipd-patient')
            ->name('discharge');
        Route::post('{allocation}/notes', 'IpdPatientController@storeNote')
            ->middleware('permission:edit-ipd-patient')
            ->name('notes.store');
        Route::post('{allocation}/clinical-snapshot', 'IpdPatientController@updateClinicalSnapshot')
            ->middleware('permission:edit-ipd-patient')
            ->name('clinical.update');
        Route::post('{allocation}/charges/show-add-form', 'IpdPatientController@showAddChargeForm')
            ->middleware('permission:edit-ipd-patient')
            ->name('charges.show-add-form');
        Route::post('{allocation}/charges/store', 'IpdPatientController@storeAdditionalCharge')
            ->middleware('permission:edit-ipd-patient')
            ->name('charges.store');
        Route::get('{allocation}/doctor-care/unified', 'IpdPatientController@doctorCareUnified')
            ->middleware('permission:edit-ipd-patient')
            ->name('doctor-care.unified');
        Route::get('{allocation}/prescription/form', 'IpdPrescriptionController@form')
            ->middleware('permission:edit-ipd-patient')
            ->name('prescription.form');
        Route::get('{allocation}/prescription/{prescription}/edit', 'IpdPrescriptionController@editForm')
            ->middleware('permission:edit-ipd-patient')
            ->name('prescription.edit-form');
        Route::post('{allocation}/prescription', 'IpdPrescriptionController@store')
            ->middleware('permission:edit-ipd-patient')
            ->name('prescription.store');
        Route::get('{allocation}/prescription/{prescription}/view', 'IpdPrescriptionController@view')->name('prescription.view');
        Route::delete('{allocation}/prescription/{prescription}', 'IpdPrescriptionController@destroy')
            ->middleware('permission:edit-ipd-patient')
            ->name('prescription.destroy');
        Route::get('{allocation}/prescription/{prescription}/print', 'IpdPrescriptionController@print')->name('prescription.print');
        Route::post('prescription/load-dosages', 'IpdPrescriptionController@loadDosages')->name('prescription.load-dosages');
        Route::post('{allocation}/diagnostics/show-form', 'IpdPatientController@showDiagnosticOrderForm')->name('diagnostics.showform');
        Route::post('{allocation}/diagnostics/store', 'IpdPatientController@storeDiagnosticOrder')->name('diagnostics.store');
        Route::delete('{allocation}/diagnostics/{item}', 'IpdPatientController@destroyDiagnosticOrder')->name('diagnostics.destroy');
        Route::get('{allocation}/final-bill/print', 'IpdPatientController@printFinalBill')->name('final-bill.print');
        Route::get('{allocation}/discharge-summary/print', 'IpdPatientController@printDischargeSummary')
            ->name('discharge-summary.print');
    });

    // Pathology Worklist
    Route::get('lab', 'DiagnosticWorklistController@labIndex')
        ->middleware(['permission:view-pathology-report'])
        ->name('lab');

    Route::group(['middleware' => ['permission:view-pathology-report'], 'prefix' => 'pathology', 'as' => 'pathology.'], function () {
        Route::get('worklist', 'DiagnosticWorklistController@pathologyIndex')->name('worklist.index');
        Route::post('worklist/load', 'DiagnosticWorklistController@loadPathology')->name('worklist.load');
        Route::post('worklist/show-form/{item}', 'DiagnosticWorklistController@showPathologyForm')->name('worklist.showform');
        Route::post('worklist/save/{item}', 'DiagnosticWorklistController@savePathology')->name('worklist.save');
        Route::post('worklist/status/{item}', 'DiagnosticWorklistController@updatePathologyStatus')->name('worklist.status');
        Route::post('worklist/critical/call/{item}', 'DiagnosticWorklistController@callCriticalDoctor')->name('worklist.critical.call');
        Route::post('worklist/critical/acknowledge/{item}', 'DiagnosticWorklistController@acknowledgeCritical')->name('worklist.critical.acknowledge');
        Route::get('worklist/print/{item}', 'DiagnosticWorklistController@printPathology')->name('worklist.print');
        Route::get('worklist/tat-analytics', 'DiagnosticWorklistController@getTatAnalytics')->name('worklist.tat-analytics');
        Route::get('worklist/analyzer-config', 'DiagnosticWorklistController@getAnalyzerConfig')->name('worklist.analyzer-config');
        Route::get('item/{item}/parameters', 'DiagnosticWorklistController@getItemParameters')->name('item.parameters');
        Route::post('report/create', 'DiagnosticWorklistController@createReport')->name('sample.create');
        Route::get('report/tests', 'DiagnosticWorklistController@searchWalkInPathologyTests')->name('sample.tests');
        Route::post('report/save', 'DiagnosticWorklistController@saveWalkInSample')->name('sample.save');
    });
    
    // Radiology Worklist
    Route::group(['middleware' => ['permission:view-radiology-report'], 'prefix' => 'radiology', 'as' => 'radiology.'], function () {
        Route::get('/', 'RadiologyRisController@index')->name('ris');
        Route::get('ris/summary', 'RadiologyRisController@summary')->name('ris.summary');
        Route::get('ris/modalities-board', 'RadiologyRisController@modalitiesBoard')->name('ris.modalities-board');
        Route::post('ris/worklist/load', 'RadiologyRisController@worklistLoad')->name('ris.worklist-load');
        Route::get('ris/report/{item}', 'RadiologyRisController@reportItemJson')->name('ris.report-item');
        Route::post('ris/workflow/{item}', 'RadiologyRisController@advanceWorkflow')->name('ris.workflow-advance');
        Route::get('ris/completed-pdf/{item}', 'RadiologyRisController@completedPdf')->name('ris.completed-pdf');
        Route::get('ris/analytics', 'RadiologyRisController@analytics')->name('ris.analytics');
        Route::get('ris/pending-queue', 'RadiologyRisController@pendingQueue')->name('ris.pending-queue');
        Route::get('ris/protocols', 'RadiologyRisController@protocols')->name('ris.protocols');
        Route::get('ris/schedule', 'RadiologyRisController@schedule')->name('ris.schedule');
        Route::get('worklist', 'DiagnosticWorklistController@radiologyIndex')->name('worklist.index');
        Route::post('worklist/load', 'DiagnosticWorklistController@loadRadiology')->name('worklist.load');
        Route::post('worklist/show-form/{item}', 'DiagnosticWorklistController@showRadiologyForm')->name('worklist.showform');
        Route::post('worklist/save/{item}', 'DiagnosticWorklistController@saveRadiology')->name('worklist.save');
        Route::post('worklist/status/{item}', 'DiagnosticWorklistController@updateRadiologyStatus')->name('worklist.status');
        Route::get('worklist/print/{item}', 'DiagnosticWorklistController@printRadiology')->name('worklist.print');
    });

    //get opd charge
    Route::post('get-opd-charge', 'OpdPatientController@getOpdCharge')->name('get-opd-charge');
    Route::get('search-patients', 'OpdPatientController@searchPatients')->name('search-patients');

    // Pharmacy Module
    Route::group(['prefix' => 'pharmacy', 'as' => 'pharmacy.'], function () {
        // Pharmacy Purchase
        Route::middleware(['permission:view-pharmacy-purchase'])->group(function () {
            Route::resource('purchase', 'PharmacyPurchaseController')->only(['index', 'store']);
            Route::post('purchase/load', 'PharmacyPurchaseController@loaddata')->name('purchase-load');
            Route::post('purchase/show-form', 'PharmacyPurchaseController@showform')->name('purchase.showform');
            Route::post('purchase/{bill}/update', 'PharmacyPurchaseController@update')->name('purchase.update');
            Route::get('purchase/{bill}/print', 'PharmacyPurchaseController@printBill')->name('purchase.print');
        });

        // Pharmacy Sale
        Route::middleware(['permission:view-pharmacy-sale'])->group(function () {
            Route::resource('sale', 'PharmacySaleController')->only(['index', 'store']);
            Route::post('sale/load', 'PharmacySaleController@loaddata')->name('sale-load');
            Route::post('sale/show-form', 'PharmacySaleController@showform')->name('sale.showform');
            Route::post('sale/load-prescription-items', 'PharmacySaleController@loadPrescriptionItems')->name('sale.load-prescription-items');
            Route::post('sale/medicine-batches', 'PharmacySaleController@medicineBatches')->name('sale.medicine-batches');
            Route::get('sale/{bill}/print', 'PharmacySaleController@printBill')->name('sale.print');
        });

        // Pharmacy Stock
        Route::middleware(['permission:view-pharmacy-stock'])->group(function () {
            Route::get('stock', 'PharmacyStockController@index')->name('stock.index');
            Route::post('stock/load', 'PharmacyStockController@loaddata')->name('stock-load');
            Route::post('stock/show-bad-stock-form', 'PharmacyStockController@showBadStockForm')->name('stock.show-bad-stock-form');
            Route::post('stock/adjust-bad-stock', 'PharmacyStockController@adjustBadStock')
                ->middleware('permission:edit-pharmacy-bad-stock')
                ->name('stock.adjust-bad-stock');
        });

        // Pharmacy Expiry
        Route::middleware(['permission:view-pharmacy-expiry'])->group(function () {
            Route::get('expiry', 'PharmacyExpiryController@index')->name('expiry.index');
            Route::post('expiry/load', 'PharmacyExpiryController@loaddata')->name('expiry-load');
            Route::post('expiry/process', 'PharmacyExpiryController@processExpired')
                ->middleware('permission:edit-pharmacy-expiry')
                ->name('expiry.process');
        });
    });

    // TPA Management
    Route::group(['prefix' => 'tpa-management', 'as' => 'tpa-management.'], function () {
        Route::middleware(['permission:view-tpa'])->group(function () {
            Route::resource('tpas', 'TpaController');
            Route::post('load-tpas', 'TpaController@loaddata')->name('tpas-load');
            Route::post('show-tpa-form', 'TpaController@showform')->name('tpas.showform');
        });
    });
    
    //load dropdown
    Route::get('load-units', 'HrDepartmentUnitController@loadUnits')->name('load-units');
    Route::get('load-tpas', 'TpaController@loadTpas')->name('load-tpas');
    Route::get('load-doctors', 'StaffController@loadDoctors')->name('load-doctors');
    Route::get('load-doctor-slots', 'StaffController@loadDoctorSlots')->name('load-doctor-slots');
    Route::post('load-symptoms', 'SymptomsHeadController@loadSymptoms')->name('load-symptoms');
    Route::post('load-diseases-by-types', 'OpdPatientController@loadDiseasesByTypes')->name('load-diseases-by-types');

    Route::get('/visitor-book', 'FrontOfficeController@visitorBook')->name('front-office.visitor-book');

    //HR
    Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {
        // Staff
        Route::middleware(['permission:view-staff'])->group(function () {
            Route::resource('staff', 'StaffController');
            Route::post('load-staff', 'StaffController@loaddata')->name('staff-load');  
            Route::post('show-staff-form', 'StaffController@showform')->name('staff.showform');
        });
    });

    //Masters
    Route::group(['prefix' => 'masters', 'as' => 'masters.'], function () {
        

        // Patient Category
        Route::middleware(['permission:view-patient-category'])->group(function () {
            Route::resource('patient-category', 'PatientCategoryController');
            Route::post('load-patient-category', 'PatientCategoryController@loaddata')->name('patient-category-load');
            Route::post('show-patient-category-form', 'PatientCategoryController@showform')->name('patient-category.showform');
        });

        // Religion
        Route::middleware(['permission:view-religion'])->group(function () {
            Route::resource('religion', 'ReligionController');
            Route::post('load-religion', 'ReligionController@loaddata')->name('religion-load');  
            Route::post('show-religion-form', 'ReligionController@showform')->name('religion.showform');
        });

        // Dietary
        Route::middleware(['permission:view-dietary'])->group(function () {
            Route::resource('dietary', 'DietaryController');
            Route::post('load-dietary', 'DietaryController@loaddata')->name('dietary-load');  
            Route::post('show-dietary-form', 'DietaryController@showform')->name('dietary.showform');
        });

        // Allergy
        Route::middleware(['permission:view-allergy'])->group(function () {
            Route::resource('allergy', 'AllergyController');
            Route::post('load-allergy', 'AllergyController@loaddata')->name('allergy-load');  
            Route::post('show-allergy-form', 'AllergyController@showform')->name('allergy.showform');
        });

        // Allergy-Reaction
        Route::middleware(['permission:view-allergy-reaction'])->group(function () {
            Route::resource('allergy-reaction', 'AllergyReactionController');
            Route::post('load-allergy-reaction', 'AllergyReactionController@loaddata')->name('allergy-reaction-load');  
            Route::post('show-allergy-reaction-form', 'AllergyReactionController@showform')->name('allergy-reaction.showform');
        });
        
        // Habits
        Route::middleware(['permission:view-habits'])->group(function () {
            Route::resource('habits', 'HabitController');
            Route::post('load-habits', 'HabitController@loaddata')->name('habits-load');  
            Route::post('show-habit-form', 'HabitController@showform')->name('habits.showform');
        });

        // Diseases
        Route::middleware(['permission:view-diseases'])->group(function () {
            Route::resource('diseases', 'DiseaseController');
            Route::post('load-diseases', 'DiseaseController@loaddata')->name('diseases-load');  
            Route::post('show-disease-form', 'DiseaseController@showform')->name('diseases.showform');
        });

        // Disease Types
        Route::middleware(['permission:view-disease-types'])->group(function () {
            Route::resource('disease-type', 'DiseaseTypeController');
            Route::post('load-disease-type', 'DiseaseTypeController@loaddata')->name('disease-type-load');
            Route::post('show-disease-type-form', 'DiseaseTypeController@showform')->name('disease-type.showform');
        });
        
        //Symptoms
        Route::group(['prefix' => 'symptoms', 'as' => 'symptoms.'], function () {
            // Symptoms Type
            Route::middleware(['permission:view-symptoms-type'])->group(function () {
                Route::resource('type', 'SymptomsTypeController');
                Route::post('load-type', 'SymptomsTypeController@loaddata')->name('type-load');
                Route::post('show-type-form', 'SymptomsTypeController@showform')->name('type.showform');
            });

            // Symptoms Head
            Route::middleware(['permission:view-symptoms'])->group(function () {
                Route::resource('symptoms-head', 'SymptomsHeadController');
                Route::post('load-symptoms-head', 'SymptomsHeadController@loaddata')->name('symptoms-head-load');
                Route::post('show-symptoms-head-form', 'SymptomsHeadController@showform')->name('symptoms-head.showform');
            });

        });

    });
    
    Route::group(['prefix' => 'charges', 'as' => 'charges.'], function () {
        // Doctor OPD Charges
        Route::middleware(['permission:view-doctor-opd-charges'])->group(function () {
            Route::resource('doctor-opd-charges', 'DoctorOpdChargeController');
            Route::post('doctor-opd-charges/load', 'DoctorOpdChargeController@loaddata')->name('doctor-opd-charges.load');  
            Route::post('doctor-opd-charges/show-form', 'DoctorOpdChargeController@showform')->name('doctor-opd-charges.showform');
        });
        
        // Charges Master
        Route::middleware(['permission:view-charge-masters'])->group(function () {
            Route::resource('charge-masters', 'ChargeMasterController');
            Route::post('charge-masters/load', 'ChargeMasterController@loaddata')->name('charge-masters.load');
            Route::post('charge-masters/show-form', 'ChargeMasterController@showform')->name('charge-masters.showform');
        });
    });
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {

        // Front Office
        Route::group(['prefix' => 'front-office', 'as' => 'front-office.'], function () {
            // Visitor Purpose
            Route::middleware(['permission:view-visitor-purposes'])->group(function () {
                Route::resource('visitor-purposes', 'VisitorPurposeController');
                Route::post('load-visitor-purposes', 'VisitorPurposeController@loaddata')->name('visitor-purposes-load');  
                Route::post('show-visitor-purpose-form', 'VisitorPurposeController@showform')->name('visitor-purposes.showform');
            });

            // Complain Types
            Route::middleware(['permission:view-complain-types'])->group(function () {
                Route::resource('complain-types', 'ComplainTypeController');
                Route::post('load-complain-types', 'ComplainTypeController@loaddata')->name('complain-types-load');  
                Route::post('show-complain-type-form', 'ComplainTypeController@showform')->name('complain-types.showform');
            });

            // Complain Sources
            Route::middleware(['permission:view-complain-sources'])->group(function () {
                Route::resource('complain-sources', 'ComplainSourceController');
                Route::post('load-complain-sources', 'ComplainSourceController@loaddata')->name('complain-sources-load');  
                Route::post('show-complain-source-form', 'ComplainSourceController@showform')->name('complain-sources.showform');
            });

            // Appointment Priorities
            Route::middleware(['permission:view-appointment-priorities'])->group(function () {
                Route::resource('appointment-priorities', 'AppointmentPriorityController');
                Route::post('load-appointment-priorities', 'AppointmentPriorityController@loaddata')->name('appointment-priorities-load');  
                Route::post('show-appointment-priority-form', 'AppointmentPriorityController@showform')->name('appointment-priorities.showform');
            });
        });

        //Pharmacy
        Route::group(['prefix' => 'pharmacy', 'as' => 'pharmacy.'], function () {
            // Medicine
            Route::middleware(['permission:view-medicine'])->group(function () {
                Route::resource('medicine', 'MedicineController');
                Route::post('load-medicine', 'MedicineController@loaddata')->name('medicine-load');  
                Route::post('show-medicine-form', 'MedicineController@showform')->name('medicine.showform');
            });
            // Medicine Category
            Route::middleware(['permission:view-medicine-category'])->group(function () {
                Route::resource('medicine-category', 'MedicineCategoryController');
                Route::post('load-medicine-category', 'MedicineCategoryController@loaddata')->name('medicine-category-load');
                Route::post('show-medicine-category-form', 'MedicineCategoryController@showform')->name('medicine-category.showform');
            });

            // Medicine Dosage
            Route::middleware(['permission:view-medicine-dosage'])->group(function () {
                Route::resource('medicine-dosage', 'MedicineDosageController');
                Route::post('load-medicine-dosage', 'MedicineDosageController@loaddata')->name('medicine-dosage-load');
                Route::post('show-medicine-dosage-form', 'MedicineDosageController@showform')->name('medicine-dosage.showform');
            });

            // Medicine Instructions
            Route::middleware(['permission:view-medicine-instructions'])->group(function () {
                Route::resource('medicine-instructions', 'MedicineInstructionsController');
                Route::post('load-medicine-instructions', 'MedicineInstructionsController@loaddata')->name('medicine-instructions-load');
                Route::post('show-medicine-instructions-form', 'MedicineInstructionsController@showform')->name('medicine-instructions.showform');
            });

            // Frequency
            Route::middleware(['permission:view-frequency'])->group(function () {
                Route::resource('frequency', 'FrequencyController');
                Route::post('load-frequency', 'FrequencyController@loaddata')->name('frequency-load');
                Route::post('show-frequency-form', 'FrequencyController@showform')->name('frequency.showform');

                Route::resource('medicine-route', 'MedicineRoutesController');
                Route::post('load-medicine-route', 'MedicineRoutesController@loaddata')->name('medicine-route-load');
                Route::post('show-medicine-route-form', 'MedicineRoutesController@showform')->name('medicine-route.showform');
            });

            // Pharmacy Suppliers
            Route::middleware(['permission:view-pharmacy-supplier'])->group(function () {
                Route::resource('supplier', 'PharmacySupplierController');
                Route::post('load-supplier', 'PharmacySupplierController@loaddata')->name('supplier-load');
                Route::post('show-supplier-form', 'PharmacySupplierController@showform')->name('supplier.showform');
            });

        });
        
        //Pathology
        Route::group(['prefix' => 'pathology', 'as' => 'pathology.'], function () {
            // Pathology Category
            Route::middleware(['permission:view-pathology-category'])->group(function () {
                Route::resource('category', 'PathologyCategoryController');
                Route::post('load-category', 'PathologyCategoryController@loaddata')->name('category-load');
                Route::post('show-category-form', 'PathologyCategoryController@showform')->name('category.showform');
            });

            // Pathology Unit
            Route::middleware(['permission:view-pathology-unit'])->group(function () {
                Route::resource('unit', 'PathologyUnitController');
                Route::post('load-unit', 'PathologyUnitController@loaddata')->name('unit-load');
                Route::post('show-unit-form', 'PathologyUnitController@showform')->name('unit.showform');
            });

            // Pathology Parameter
            Route::middleware(['permission:view-pathology-parameter'])->group(function () {
                Route::resource('parameter', 'PathologyParameterController');
                Route::post('load-parameter', 'PathologyParameterController@loaddata')->name('parameter-load');
                Route::post('show-parameter-form', 'PathologyParameterController@showform')->name('parameter.showform');
            });

            // Pathology Status
            Route::middleware(['permission:view-pathology-status'])->group(function () {
                Route::resource('status', 'PathologyStatusController');
                Route::post('load-status', 'PathologyStatusController@loaddata')->name('status-load');
                Route::post('show-status-form', 'PathologyStatusController@showform')->name('status.showform');
                Route::resource('sample-type', 'PathologySampleTypeController');
                Route::post('load-sample-type', 'PathologySampleTypeController@loaddata')->name('sample-type-load');
                Route::post('show-sample-type-form', 'PathologySampleTypeController@showform')->name('sample-type.showform');
            });

            // Pathology Age Group
            Route::middleware(['permission:view-pathology-age-group'])->group(function () {
                Route::resource('age-group', 'AgeGroupController');
                Route::post('load-age-group', 'AgeGroupController@loaddata')->name('age-group-load');
                Route::post('show-age-group-form', 'AgeGroupController@showform')->name('age-group.showform');
            });

            // Pathology Test
            Route::middleware(['permission:view-pathology-test'])->group(function () {
                Route::resource('test', 'PathologyTestController');
                Route::post('load-test', 'PathologyTestController@loaddata')->name('test-load');
                Route::post('show-test-form', 'PathologyTestController@showform')->name('test.showform');
            });
        });
        //Radiology
        Route::group(['prefix' => 'radiology', 'as' => 'radiology.'], function () {
            // Radiology Category
            Route::middleware(['permission:view-radiology-category'])->group(function () {
                Route::resource('category', 'RadiologyCategoryController');
                Route::post('load-category', 'RadiologyCategoryController@loaddata')->name('category-load');
                Route::post('show-category-form', 'RadiologyCategoryController@showform')->name('category.showform');
            });

            // Radiology Unit
            Route::middleware(['permission:view-radiology-unit'])->group(function () {
                Route::resource('unit', 'RadiologyUnitController');
                Route::post('load-unit', 'RadiologyUnitController@loaddata')->name('unit-load');
                Route::post('show-unit-form', 'RadiologyUnitController@showform')->name('unit.showform');
            });

            // Radiology Parameter
            Route::middleware(['permission:view-radiology-parameter'])->group(function () {
                Route::resource('parameter', 'RadiologyParameterController');
                Route::post('load-parameter', 'RadiologyParameterController@loaddata')->name('parameter-load');
                Route::post('show-parameter-form', 'RadiologyParameterController@showform')->name('parameter.showform');
            });

            // Radiology Test
            Route::middleware(['permission:view-radiology-test'])->group(function () {
                Route::resource('test', 'RadiologyTestController');
                Route::post('load-test', 'RadiologyTestController@loaddata')->name('test-load');
                Route::post('show-test-form', 'RadiologyTestController@showform')->name('test.showform');
            });

        });
        Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {

            // Leave Types
            Route::middleware(['permission:view-hr-leave-type'])->group(function () {
                Route::resource('leave-type', 'HrLeaveTypeController');
                Route::post('load-leave-type', 'HrLeaveTypeController@loaddata')->name('leave-type-load');
                Route::post('show-leave-type-form', 'HrLeaveTypeController@showform')->name('leave-type.showform');
            });
            
            // Departments
            Route::middleware(['permission:view-hr-department'])->group(function () {
                Route::resource('department', 'HrDepartmentController');
                Route::post('load-department', 'HrDepartmentController@loaddata')->name('department-load');
                Route::post('show-department-form', 'HrDepartmentController@showform')->name('department.showform');
            });

            // Department Units
            Route::middleware(['permission:view-hr-department-unit'])->group(function () {
                Route::resource('department-unit', 'HrDepartmentUnitController');
                Route::post('load-department-unit', 'HrDepartmentUnitController@loaddata')->name('department-unit-load');
                Route::post('show-department-unit-form', 'HrDepartmentUnitController@showform')->name('department-unit.showform');
            });
            
            // Designations
            Route::middleware(['permission:view-hr-designation'])->group(function () {
                Route::resource('designation', 'HrDesignationController');
                Route::post('load-hr-designation', 'HrDesignationController@loaddata')->name('designation-load');
                Route::post('show-hr-designation-form', 'HrDesignationController@showform')->name('designation.showform');
            });

            // Specialists
            Route::middleware(['permission:view-hr-specialist'])->group(function () {
                Route::resource('specialist', 'HrSpecialistController');
                Route::post('load-specialist', 'HrSpecialistController@loaddata')->name('specialist-load');
                Route::post('show-specialist-form', 'HrSpecialistController@showform')->name('specialist.showform');
            });
        });

        // Beds Management
        Route::group(['prefix' => 'beds', 'as' => 'beds.'], function () {
            // Bed Type
            Route::middleware(['permission:view-bed-type'])->group(function () {
                Route::resource('bed-type', 'BedTypeController');
                Route::post('load-bed-type', 'BedTypeController@loaddata')->name('bed-type-load');
                Route::post('show-bed-type-form', 'BedTypeController@showform')->name('bed-type.showform');
            });

            // Building
            Route::middleware(['permission:view-building'])->group(function () {
                Route::resource('building', 'BuildingController');
                Route::post('load-building', 'BuildingController@loaddata')->name('building-load');
                Route::post('show-building-form', 'BuildingController@showform')->name('building.showform');
            });
            
            // Floor
            Route::middleware(['permission:view-floor'])->group(function () {
                Route::resource('floor', 'FloorController');
                Route::post('load-floor', 'FloorController@loaddata')->name('floor-load');
                Route::post('show-floor-form', 'FloorController@showform')->name('floor.showform');
            });


            // Ward
            Route::middleware(['permission:view-ward'])->group(function () {
                Route::resource('ward', 'WardController');
                Route::post('load-ward', 'WardController@loaddata')->name('ward-load');
                Route::post('show-ward-form', 'WardController@showform')->name('ward.showform');
            });

            // Room
            Route::middleware(['permission:view-room'])->group(function () {
                Route::resource('room', 'RoomController');
                Route::post('load-room', 'RoomController@loaddata')->name('room-load');
                Route::post('show-room-form', 'RoomController@showform')->name('room.showform');
            });

            // Bed
            Route::middleware(['permission:view-bed'])->group(function () {
                Route::get('bed-dashboard', 'BedController@dashboard')->name('bed-dashboard');
                Route::post('bed-scan', 'BedController@scanByBarcode')->name('bed.scan');
                Route::get('bed/{bed}/barcode', 'BedController@barcode')->name('bed.barcode');
                Route::post('bed/{bed}/status', 'BedController@updateStatus')->name('bed.status');
                Route::resource('bed', 'BedController');
                Route::post('load-bed', 'BedController@loaddata')->name('bed-load');
                Route::post('show-bed-form', 'BedController@showform')->name('bed.showform');
            });
        });
        
        // Header Footer
         Route::group(['prefix' => 'header-footer', 'as' => 'header-footer.'], function () {
            // Header Footer
            Route::middleware(['permission:view-header-footer'])->group(function () {
                Route::get('/', 'HeaderFooterController@index')->name('index');
                Route::post('load', 'HeaderFooterController@loaddata')->name('load');
                Route::post('show-form', 'HeaderFooterController@showform')->name('showform');
                Route::post('store', 'HeaderFooterController@store')->name('store');
                Route::delete('{header_footer}', 'HeaderFooterController@destroy')->name('destroy');
            });
        });

        // Role Management
        Route::group(['prefix' => 'role-management', 'as' => 'role-management.'], function () {
            Route::middleware(['permission:manage-roles'])->group(function () {
                Route::get('/', 'RoleController@index')->name('index');
                Route::post('load', 'RoleController@rolesload')->name('load');
                Route::post('show-form', 'RoleController@showform')->name('showform');
                Route::post('store', 'RoleController@store')->name('store');
                Route::delete('{role}', 'RoleController@destroy')->name('destroy');
            });
        });
    });
});
