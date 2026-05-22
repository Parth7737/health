@php
    $hide_document_form = $hide_document_form ?? false;
    $is_admin_edit = $is_admin_edit ?? false;
    $documents = App\CentralLogics\Helpers::getCommanData('EmpanelmentDocument');
    if (!isset($allStepCompleted) && isset($uuid)) {
        $allStepCompleted = \App\CentralLogics\Helpers::checkAllStepIsCompleteOrNot($uuid);
    }
@endphp
@if (!$hide_document_form && sizeof($documents) > 0)
<div id="documentsCard" class="eo-card">
   <div class="eo-card-hdr">
      <h3 class="eo-card-title"><i class="fas fa-folder-open" style="color:#ffca28"></i> Document upload</h3>
      <p class="eo-panel-sub">Upload PDFs for each required item. Mandatory rows are marked with <span class="eo-req">*</span>.</p>
   </div>
   <div class="eo-card-body">
        <div class="table-responsive text-nowrap">
        <table class="table eo-staff-table mb-0">
            <thead>
                <tr>
                    <th style="width: 5%">Sr No.</th>
                    <th style="width: 35%">Name</th>
                    <th style="width: 35%">Document</th>
                    <th style="width: 25%">Remarks</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($documents as $key => $value)
                @php
                    $isRequired = false;
                    if($value->is_required) {
                        $isRequired = true;
                    }
                    $existData = App\CentralLogics\Helpers::getSingleDocument(@$hospital->id, $value->id);
                @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$value->name}} @if($value->is_required) <span class="text-danger">*</span> @endif</td>
                    <td>
                    <div class="file-upload-section docerror eo-license-document">
                        <div class="file-upload-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6200ea">
                                <path d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                            </svg>
                            <p><strong>Browse</strong></p>
                        </div>
                        <input type="file" class="file-input d-none" required name="document_{{$value->id}}_doc" {{$isRequired ? 'required' : ''}} id="document_{{$value->id}}_doc" />
                        <div class="uploaded-file file-upload-display d-none">
                            <span class="file-name">Sample.pdf</span>
                            <button type="button" class="remove-file-btn bg-transparent border-0 p-0">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                                <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <small>Upload only PDF format file</small>
                    @if(@$existData->document)
                    <br>
                    <label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$existData->document) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                    @endif
                    </td>
                    <td>
                    <input type="text" id="remark{{$value->id}}" oninput="sanitize(this, 'b');" value="{{$existData && $existData->remarks ? $existData->remarks : ''}}" name="{{$value->id}}_remarkdoc" class="form-control" placeholder="" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
   </div>
</div>

@endif