@include('hospital.empanelment._partials.services', ['hospital' => $hospital, 'services' => $services, 'uuid' => $hospital->uuid ?? '', 'is_admin_edit' => true])
