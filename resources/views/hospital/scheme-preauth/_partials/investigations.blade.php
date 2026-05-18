@if(@$investigations)
@foreach(@$investigations as $investigation)
@php $preauth_investigation = App\Models\PreauthInvestigation::where('preauth_register_id',$preauth_register_id)->where('investigation_id',$investigation->id)->first(); @endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>
        {{ $investigation->name ?? '' }} 
        {!! !$preauth_investigation && $investigation->is_required ? '<span class="text-danger">*</span>' : '' !!}
    </td>
    <td>
        <div class="mb-4">
            <div class="file-upload-section">
                @if(!isset($is_preview))
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
                    class="file-input d-none" name="investigation_{{$investigation->id}}_doc"/>
                <div
                    class="uploaded-file file-upload-display d-none">
                    <span
                        class="file-name">Sample.pdf</span>
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
                <br/><small class="text-danger fs-11">Upload a only pdf format file and max size should be 5MB</small><br/>
                @endif
                @if($preauth_investigation)
                    <label><a href="{{ asset('public/storage/'.@$preauth_investigation->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                @endif
            </div>
        </div>
    </td>
</tr>
@endforeach
@endif