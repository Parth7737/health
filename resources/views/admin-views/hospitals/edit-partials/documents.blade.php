@include('hospital.empanelment._partials.documents', ['hospital' => $hospital, 'uuid' => $hospital->uuid ?? '','allStepCompleted' => $allStepCompleted, 'is_admin_edit' => true])
