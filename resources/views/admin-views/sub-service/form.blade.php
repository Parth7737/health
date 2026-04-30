<div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">{{ @$id ? 'Edit' : 'Add'}} Sub Service</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" id="savedata" method="POST" enctype="multipart/form-data">
    <div class="modal-body">   
        <input type="hidden" id="id" name="id" value="{{$id}}">
        <div class="row">
            <div class="col-md-4 col-lg-4">
                @php $services = App\Models\Service::get(); @endphp
                <div class="form-group">
                    <label for="">Select Service<span class="text-danger"> *</span></label>
                    <select name="service_id" required id="service_id" class="form-control">
                        <option value="">Select</option>
                        @foreach($services as $key => $value)
                            <option value="{{$value->id}}" @if(@$data->service_id == $value->id) selected @endif>{{$value->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group">
                    <label for="">Name<span class="text-danger"> *</span></label>
                    <input type="text" value="{{@$data->name}}" required class="form-control" name="name" id="name">
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="form-group mt-4">
                    <label for="is_required">Is Required <input type="checkbox" class="" name="is_required" id="is_required" @if(@$data->is_required == 1) checked @endif value="1"></label>
                </div>
            </div>

            <h5 class="title mt-2">Sub Service Action</h5>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <td>Type</td>
                        <td>Label</td>
                        <td>Value</td>
                        <td>Is Text Input</td>
                        <td>Is Image</td>
                        <td>SubLabel</td>
                        <td>Is Bed Count?</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody class="body">
                    @if($data)
                        @foreach(@$data->actions as $key => $value)
                        <tr>
                            <td>
                                <select name="type[{{ $key }}]" required id="type{{ $key }}" class="form-control">
                                    <option value="">Select</option>
                                    <option value="radio" @if($value->type == 'radio') selected @endif>Radio</option>
                                    <option value="text" @if($value->type == 'text') selected @endif>Text</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" value="{{$value->label}}" required name="label[{{ $key }}]" id="label{{ $key }}" class="form-control">
                            </td>
                            <td>
                                <input type="text" value="{{$value->value}}" name="value[{{ $key }}]" id="value{{ $key }}" class="form-control">
                            </td>
                            <td>
                                <input type="checkbox" @if($value->is_text_input == 1) checked @endif name="is_text_input[{{ $key }}]" id="is_text_input{{ $key }}" class="">
                            </td>
                            <td>
                                <input type="checkbox" @if($value->is_image == 1) checked @endif name="is_image[{{ $key }}]" id="is_image{{ $key }}" class="">
                            </td>
                            <td>
                                <input type="text" value="{{$value->sublabel}}" name="sublabel[{{ $key }}]" id="sublabel{{ $key }}" class="form-control">
                            </td>
                            <td>
                                <input type="checkbox" @if($value->bed_count == 1) checked @endif name="bed_count[{{ $key }}]" id="bed_count{{ $key }}" class="">
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <select name="type[0]" required id="type0" class="form-control">
                                    <option value="">Select</option>
                                    <option value="radio">Radio</option>
                                    <option value="text">Text</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" required name="label[0]" id="label0" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="value[0]" id="value0" class="form-control">
                            </td>
                            <td>
                                <input type="checkbox" name="is_text_input[0]" id="is_text_input0" class="">
                            </td>
                            <td>
                                <input type="checkbox" name="is_image[0]" id="is_image0" class="">
                            </td>
                            <td>
                                <input type="text" name="sublabel[0]" id="sublabel0" class="form-control">
                            </td>
                            <td>
                                <input type="checkbox" name="bed_count[0]" id="bed_count0" class="">
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <button type="button" id="addmore" class="btn btn-info"><i class="fa fa-plus"></i>Add More</button>
                        </td>
                    </tr>
                </tfoot>
            </table>                        
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
<script>
$(document).ready(function() {
    var rowIndex = '{{$data ? count($data->actions) + 1:1}}';

    $('#addmore').click(function() {
        var newRow = `
            <tr>
                <td>
                    <select name="type[${rowIndex}]" id="type${rowIndex}" required class="form-control">
                        <option value="">Select</option>
                        <option value="radio">Radio</option>
                        <option value="text">Text</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="label[${rowIndex}]" required id="label${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="text" name="value[${rowIndex}]"  id="value${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="checkbox" name="is_text_input[${rowIndex}]" id="is_text_input${rowIndex}" class="">
                </td>
                <td>
                    <input type="checkbox" name="is_image[${rowIndex}]" id="is_image${rowIndex}" class="">
                </td>
                <td>
                    <input type="text" name="sublabel[${rowIndex}]" id="sublabel${rowIndex}" class="form-control">
                </td>
                <td>
                    <input type="checkbox" name="bed_count[${rowIndex}]" id="bed_count${rowIndex}" class="">
                </td>
                <td>
                    <button type="button" class="btn btn-danger delete-row"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        
        // Append the new row to the table body
        $('.body').append(newRow);

        // Increment the row index
        rowIndex++;
    });

    // Delegate click event to delete-row button to remove the row
    $('body').on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
    });
});
</script>