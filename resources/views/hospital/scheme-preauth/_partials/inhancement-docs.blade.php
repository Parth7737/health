
@php 

if(isset($temp_enhancement_id)){
    $bed_side_photo = App\Models\PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Bed Side Photo')->first();
    $clinical_notes = App\Models\PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Clinical Notes')->first();
    $any_other_document = App\Models\PreauthEnhancementDoc::where('preauth_register_id',$preauth_register_id)->where('temp_enhancement_id',$temp_enhancement_id)->withoutGlobalScopes()->where('name','Any Other Document')->first();
}
@endphp
<tr>
    <td></td>
    <td>Bed Side Photo<span class="text-danger">*</span></td>
    <td>
        <div class="mb-4">
            <div class="file-upload-section">
                <div class="file-upload-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px" fill="#6200ea">
                        <path
                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                    </svg>
                    <p>
                        <strong>Browse</strong></p>
                </div>
                <input type="file"
                    class="file-input d-none" name="bed_side_photo"/>
                <div
                    class="uploaded-file file-upload-display d-none">
                    <span
                        class="file-name">Sample.pdf</span>
                    <i class="fas fa-trash "></i>
                    <button
                        class="remove-file-btn bg-transparent border-0 p-0">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                </div>
                @if(@$bed_side_photo)
                    <label><a href="{{ asset('public/storage/'.@$bed_side_photo->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                @endif
            </div>
        </div>
    </td>
</tr>
<tr>
    <td></td>
    <td>Clinical Notes<span class="text-danger">*</span></td>
    <td>
        <div class="mb-4">
            <div class="file-upload-section">
                <div class="file-upload-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px" fill="#6200ea">
                        <path
                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                    </svg>
                    <p>
                        <strong>Browse</strong></p>
                </div>
                <input type="file"
                    class="file-input d-none" name="clinical_notes"/>
                <div
                    class="uploaded-file file-upload-display d-none">
                    <span
                        class="file-name">Sample.pdf</span>
                    <i class="fas fa-trash "></i>
                    <button
                        class="remove-file-btn bg-transparent border-0 p-0">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                </div>
                @if(@$clinical_notes)
                    <label><a href="{{ asset('public/storage/'.@$clinical_notes->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                @endif
            </div>
        </div>
    </td>
</tr>
<tr>
    <td></td>
    <td>Any Other Document</td>
    <td>
        <div class="mb-4">
            <div class="file-upload-section">
                <div class="file-upload-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        height="24px"
                        viewBox="0 -960 960 960"
                        width="24px" fill="#6200ea">
                        <path
                            d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                    </svg>
                    <p>
                        <strong>Browse</strong></p>
                </div>
                <input type="file"
                    class="file-input d-none" name="any_other_document"/>
                <div
                    class="uploaded-file file-upload-display d-none">
                    <span
                        class="file-name">Sample.pdf</span>
                    <i class="fas fa-trash "></i>
                    <button
                        class="remove-file-btn bg-transparent border-0 p-0">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                </div>
                @if(@$any_other_document)
                    <label><a href="{{ asset('public/storage/'.@$any_other_document->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                @endif
            </div>
        </div>
    </td>
</tr>