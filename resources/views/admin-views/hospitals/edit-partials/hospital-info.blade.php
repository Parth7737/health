<form id="adminHospitalInfoForm">
    @csrf
    <div class="inside-left-info-box mb-3">
        <h4 class="colored-verticle-title mb-2 text-primary">Hospital Information</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="name">Hospital Name<span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ @$hospital->name }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="code">Hospital Code<span class="text-danger">*</span></label>
                    <input type="text" id="code" name="code" class="form-control" value="{{ @$hospital->code }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    @php $types = App\CentralLogics\Helpers::getCommanData('HospitalType'); @endphp
                    <label for="type_id">Type<span class="text-danger">*</span></label>
                    <select class="form-select select2" id="type_id" name="type_id" required>
                        <option value="">Select</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ @$hospital->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="hospital_phone">Hospital Phone<span class="text-danger">*</span></label>
                    <input type="text" id="hospital_phone" name="hospital_phone" class="form-control" value="{{ @$hospital->phone }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="hospital_email">Hospital Email<span class="text-danger">*</span></label>
                    <input type="email" id="hospital_email" name="hospital_email" class="form-control" value="{{ @$hospital->email }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="pincode">Pincode<span class="text-danger">*</span></label>
                    <input type="text" id="pincode" name="pincode" class="form-control" value="{{ @$hospital->pincode }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="city">City<span class="text-danger">*</span></label>
                    <input type="text" id="city" name="city" class="form-control" value="{{ @$hospital->city }}" required />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="landmark">Landmark</label>
                    <input type="text" id="landmark" name="landmark" class="form-control" value="{{ @$hospital->landmark }}" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-3">
                    <label for="address">Hospital Address<span class="text-danger">*</span></label>
                    <textarea class="form-control h-px-100" id="address" name="address" required>{{ @$hospital->address }}</textarea>
                </div>
            </div>
        </div>
    </div>
    @php $chairman = $hospital->user ? \App\Models\User::where('hospital_id', $hospital->id)->whereHas('roles', fn($q) => $q->where('name', 'Chairman'))->first() : null; @endphp
    @if($hospital->hospital_type == 'Multi-Branch' && $hospital->parent_id == 0)
        <div class="inside-left-info-box mb-3">
            <h4 class="colored-verticle-title mb-2 text-primary">Chairman Details</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label for="chairman_name">Chairman/Head<span class="text-danger">*</span></label>
                        <input type="text" id="chairman_name" name="chairman_name" class="form-control" value="{{ @$chairman->name }}" required />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label for="chairman_email">Chairman/Head Email<span class="text-danger">*</span></label>
                        <input type="email" id="chairman_email" name="chairman_email" class="form-control" value="{{ @$chairman->email }}" required />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Leave blank to keep current" />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label for="confirmation_password">Confirm Password</label>
                        <input type="password" id="confirmation_password" name="confirmation_password" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-md-12 mt-2">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary adminSaveHospitalInfo">SAVE</button>
        </div>
    </div>
</form>
<script>
$('.adminSaveHospitalInfo').click(function() {
    var formData = new FormData($('#adminHospitalInfoForm')[0]);
    loader('show');
    $.ajax({
        url: '{{ route("admin.hospitals.update.info", $hospital->id) }}',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(r) {
            loader('hide');
            if (r.success) {
                if (typeof sendmsg === 'function') sendmsg('success', r.message);
                else alert(r.message);
                loadAdminStep(r.step);
            }
        },
        error: function(xhr) {
            loader('hide');
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                var msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                sendmsg('error',msg);
            } else {
                errorMessage('Something went wrong. Please try again later.');
            }
        }
    });
});
if (typeof loadSelect2 === 'function') loadSelect2();
</script>
