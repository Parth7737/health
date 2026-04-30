<form id="adminSpecialitiesForm">
    @csrf
    <div class="table-responsive mt-5 text-nowrap">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Sr No.</th>
                    <th>Speciality Name</th>
                    <th>Code</th>
                    <th>Available</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($specialities as $value)
                @php $availableSpeciality = App\CentralLogics\Helpers::getSingleSpecialities($hospital->id, $value->id); @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $value->name }}</td>
                    <td>{{ $value->code }} <input type="hidden" value="{{ $value->id }}" name="speciality_id[]"></td>
                    <td>
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="available{{ $value->id }}" name="available_{{ $value->id }}" value="1" {{ ($availableSpeciality && $availableSpeciality->available == 1) ? 'checked' : '' }} />
                        </div>
                    </td>
                    <td>
                        <input type="text" name="remark_{{ $value->id }}" class="form-control" value="{{ $availableSpeciality && $availableSpeciality->remark ? $availableSpeciality->remark : '' }}" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end mt-2">
        <button type="button" class="btn btn-primary adminSaveSpecialities">SAVE</button>
    </div>
</form>
<script>
$('.adminSaveSpecialities').click(function() {
    var formData = new FormData($('#adminSpecialitiesForm')[0]);
    $('#adminSpecialitiesForm input[type="checkbox"]').each(function() {
        if (!this.checked) formData.append(this.name, 0);
    });
    loader('show');
    $.ajax({
        url: '{{ route("admin.hospitals.update.specialities", $hospital->id) }}',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(r) {
            loader('hide');
            if (r.success) {
                if (typeof successMessage === 'function') successMessage(r.message);
                else alert(r.message);
                loadAdminStep(r.step);
            }
        },
        error: function(xhr) {
            loader('hide');
            errorMessage('Something went wrong. Please try again later.');
        }
    });
});
</script>
