@if(@$procedures)
@php $i=1; @endphp
@foreach(@$procedures as $procedure)
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ @$procedure->speciality->name }}</td>
    <td>
        @php
            $fullText = @$procedure->procedure->procedure_name;
            $truncatedText = \Illuminate\Support\Str::words($fullText, 5, '...');
        @endphp

        <div class="short-text" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $truncatedText }}
        </div>
        <div class="full-text" style="max-width: 250px; word-wrap: break-word; display: none;">
            {{ $fullText }}
        </div>

        @if(\Illuminate\Support\Str::wordCount($fullText) > 5)
            <a href="javascript:;" class="toggle-text">Show More</a>
        @endif
    </td>
    <td>{{ (@$procedure->stratification_price !=0)?"₹".number_format(@$procedure->stratification_price, 2):'NA' }}</td>
    <td>{{ @$procedure->no_of_days }}</td>
    <td>₹{{ number_format(@$procedure->procedure_price+@$procedure->stratification_price, 2) }}</td>
    <td>{{ @$procedure->procedure->icd_code }}</td>
    @php $is_hide=0; @endphp
    @if(isset($is_enhancement) && $procedure->is_enhancement == 0)
    @php $is_hide=1; @endphp
    @endif
    @if(!isset($is_action_hide))
        <td>
            @if($is_hide == 0)
                @if(isset($is_resubmission))
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteTempProcedure('{{ $procedure->id }}')" title="Delete procedure">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @elseif(isset($is_enhancement) && $procedure->is_enhancement)
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteEnhancementProcedure('{{ $procedure->id }}')" title="Delete procedure">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @elseif(!isset($is_enhancement))
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteProcedure('{{ $procedure->id }}')" title="Delete procedure">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @endif
            @endif
        </td>
    @endif
</tr>
@if(($procedure->implant_id && (!isset($is_enhancement) && !isset($is_resubmission))) || ($procedure->implant_id && (isset($is_enhancement) || isset($is_resubmission) && $procedure->is_implant_enhance_or_resubmission == 0)) )
<tr>
    <td>{{ $i++ }}</td>
    <td>{{ @$procedure->speciality->name }}</td>
    <td>
        @php
            $fullText = @$procedure->implant->name;
            $truncatedText = \Illuminate\Support\Str::words($fullText, 5, '...');
        @endphp

        <div class="short-text" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $truncatedText }}
        </div>
        <div class="full-text" style="max-width: 250px; word-wrap: break-word; display: none;">
            {{ $fullText }}
        </div>

        @if(\Illuminate\Support\Str::wordCount($fullText) > 5)
            <a href="javascript:;" class="toggle-text">Show More</a>
        @endif
    </td>
    <td>{{ 'N/A' }}</td>
    <td>{{ @$procedure->implant_qty }}</td>
    <td>{{ (@$procedure->implant_price !=0)?"₹".number_format(@$procedure->implant_price, 2):'NA' }}</td>
    <td>{{ 'N/A' }}</td>
    @if(!isset($is_action_hide))
        <td>
            @if($is_hide == 0)
                @if(isset($is_resubmission))
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteTempImplant('{{ $procedure->id }}','resubmission')" title="Delete implant">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @elseif(isset($is_enhancement) && $procedure->is_enhancement)
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteTempImplant('{{ $procedure->id }}','enhancement')" title="Delete implant">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @elseif(!isset($is_enhancement))
                    <button type="button" class=" bg-transparent border-0 p-0"
                        onClick="deleteImplant('{{ $procedure->id }}')" title="Delete implant">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 -960 960 960"
                            width="24px"
                            fill="undefined">
                            <path
                                d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                        </svg>
                    </button>
                @endif
            @endif
        </td>
    @endif
</tr>
@endif
@endforeach
@endif
<script>
    document.addEventListener('click', function (event) {
    // Check if the clicked element has the class "toggle-text"
    if (event.target.classList.contains('toggle-text')) {
        const parent = event.target.parentElement;
        const shortText = parent.querySelector('.short-text');
        const fullText = parent.querySelector('.full-text');

        if (fullText.style.display === 'none') {
            shortText.style.display = 'none';
            fullText.style.display = 'inline';
            event.target.textContent = 'Show Less';
        } else {
            fullText.style.display = 'none';
            shortText.style.display = 'inline';
            event.target.textContent = 'Show More';
        }
    }
});

</script>
