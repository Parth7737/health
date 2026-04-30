<div class="modal-header bg-primary ">
    <h5 class="modal-title text-white" id="view_modal_dataModelLabel">Status Modal</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="row g-3" id="changestatus">    
    <div class="modal-body">   
        <input type="hidden" id="hospitalid" name="hospital_id" value="{{$id}}">
        <div class="col-md-12">
            <label class="form-label">Status</label>
            <select class="form-select" name="status" id="hospital_status" >
                <option value="0">Pending</option>
                <option value="1">Approved</option>
                <option value="2">Rejected</option>
            </select>
        </div>   
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
 </form>