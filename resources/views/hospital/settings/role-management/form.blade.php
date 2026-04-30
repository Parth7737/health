<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">Add/Edit Role</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" id="id" name="id" value="{{ $id }}">

        <div class="col-md-12">
            <label class="form-label">Role Name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ @$role->name }}"
                class="form-control"
                {{ !empty($isGlobalRole) ? 'readonly' : '' }}
            >
            @if(!empty($isGlobalRole))
                <small class="text-info">Admin role name cannot be changed. You can only manage permissions for this hospital.</small>
            @endif
        </div>

        <div class="alert alert-info mt-3 mb-0" role="alert">
            You can manage only those permissions that are already available on your current login. Inherited permissions removed here will stay blocked for this hospital.
        </div>

        @if($groupedPermissions)
        <div class="form-check form-switch form-check-inline mt-2">
            <input class="form-check-input switch-primary check-size" id="check_all" type="checkbox" role="switch">
            <p><i data-feather="info"></i> Check All</p>
        </div>
        @endif

        @foreach($groupedPermissions as $module => $permissions)
            @php $module_data = App\Models\Module::find($module); @endphp
            <div class="row">
                <strong>{{ @$module_data->name }}</strong><br>
            </div>
            <div class="row">
                @foreach($permissions as $k => $permission)
                    <div class="col-md-3">
                        <div class="form-check form-switch form-check-inline">
                            <input class="form-check-input switch-primary check-size permission" id="permission{{ $k }}" name="permissions[]" type="checkbox" role="switch" value="{{ $permission->name }}" {{ in_array($permission->name, @$rolePermissions) ? 'checked' : '' }}>
                            <p><i data-feather="info"></i> {{ $permission->name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
