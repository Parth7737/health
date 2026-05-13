<div class="inside-left-info-box  {{$hospital->house_keeping && $hospital->medico_count ? 'success' : 'pending' }} mt-4 nonhrpanel">
    <h4 class="colored-verticle-title">
        NON Medical Resource 
        <span class="status-dot">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
            </svg>
        </span>
    </h4>
    <form id="nonhrform">
        <div class="row g-5">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Sr No.</th>
                        <th>Name</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tbody
                    class="table-border-bottom-0">
                    <tr>
                        <td> 1 </td>
                        <td>Medico</td>
                        <td>
                            <div class="form-floating form-floating-outline nonhrerror">
                                <input type="text"
                                    oninput="sanitize(this, 'n');"
                                    id=""
                                    value="{{$hospital->medico_count}}"
                                    name="medico_count"
                                    class="form-control"
                                    placeholder="text" required />
                                <label for="">Count</label>
                            </div>
                        </td>                        
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>House Keeping</td>
                        <td>
                            <div class="d-flex nonhrerror">
                                <div class="form-check ">
                                    <input class="form-check-input" required type="radio" value="Inhouse" {{$hospital->house_keeping == 'Inhouse' ? 'checked' : ''}} name="house_keeping" id="Inhouse">
                                    <label class="form-check-label" for="Inhouse">
                                        Inhouse
                                    </label>
                                </div>
                                <div class="form-check mr-2">
                                    <input class="form-check-input" required type="radio"  name="house_keeping"  {{$hospital->house_keeping == 'Outsourced' ? 'checked' : ''}} id="Outsourced">
                                    <label class="form-check-label" value="Outsourced" for="Outsourced">
                                        Outsourced
                                    </label>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            @if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary nonhr" type="button">SAVE</button>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
    $('.nonhr').click(function () {
     
     ldrshow();
     $('.error').remove();
     // Create a FormData object
     var formData = new FormData($('#nonhrform')[0]);
    
     // Send an AJAX request
     $.ajax({
        url: '{{route("hospital.empanelmentRegistration.saveNoNHR", [$uuid, $hospital_id])}}',
        headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false, 
        success: function (response) {
           ldrhide();
           $('.nonhrpanel').removeClass('pending').addClass('success');
           successMessage(response.message);
           if(response.completedStep['medicalstep'] && response.completedStep['servicestep'] && response.completedStep['specialiststep']) {
                CheckHumanResourceStepCompleteOrNot(6, true);
            } else {
                CheckHumanResourceStepCompleteOrNot(5, false);
            }
        },
        error: function (xhr) {
           ldrhide();
           $('.error').remove();
           
           if (xhr.status === 422) { 
              let errors = xhr.responseJSON.errors;
              for (let field in errors) {
                 $(`[name="${field}"]`).closest('.nonhrerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
              }
           } else {
              alert('Something went wrong. Please try again later.');
           }
        }
     });
  });
</script>