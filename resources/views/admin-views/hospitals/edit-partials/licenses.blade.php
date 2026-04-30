@include('hospital.empanelment._partials.licenses', ['hospital' => $hospital, 'licenses' => $licenses, 'uuid' => $hospital->uuid ?? '', 'is_admin_edit' => true])
