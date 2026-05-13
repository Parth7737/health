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
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn p-0 dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown">
                        <i
                            class="ri-more-2-line"></i>
                    </button>
                    @if(isset($is_resubmission))
                        <div
                            class="dropdown-menu">
                            <a class="dropdown-item"
                                onClick="deleteTempProcedure('{{ $procedure->id }}')"><i
                                    class="ri-delete-bin-7-line me-1"></i>
                                Delete</a>
                        </div>
                    @elseif(isset($is_enhancement) && $procedure->is_enhancement)
                        <div
                            class="dropdown-menu">
                            <a class="dropdown-item"
                                onClick="deleteEnhancementProcedure('{{ $procedure->id }}')"><i
                                    class="ri-delete-bin-7-line me-1"></i>
                                Delete</a>
                        </div>
                    @else
                        @if(!isset($is_enhancement))
                            <div
                                class="dropdown-menu">
                                <a class="dropdown-item"
                                    onClick="deleteProcedure('{{ $procedure->id }}')"><i
                                        class="ri-delete-bin-7-line me-1"></i>
                                    Delete</a>
                            </div>
                        @endif
                    @endif
                </div>
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
                <div class="dropdown">
                    <button
                        type="button"
                        class="btn p-0 dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown">
                        <i
                            class="ri-more-2-line"></i>
                    </button>
                    @if(isset($is_resubmission))
                        <div
                            class="dropdown-menu">
                            <a class="dropdown-item"
                                onClick="deleteTempImplant('{{ $procedure->id }}','resubmission')"><i
                                    class="ri-delete-bin-7-line me-1"></i>
                                Delete</a>
                        </div>
                    @elseif(isset($is_enhancement) && $procedure->is_enhancement)
                        <div
                            class="dropdown-menu">
                            <a class="dropdown-item"
                                onClick="deleteTempImplant('{{ $procedure->id }}','enhancement')"><i
                                    class="ri-delete-bin-7-line me-1"></i>
                                Delete</a>
                        </div>
                    @else
                        @if(!isset($is_enhancement))
                            <div
                                class="dropdown-menu">
                                <a class="dropdown-item"
                                    onClick="deleteImplant('{{ $procedure->id }}')"><i
                                        class="ri-delete-bin-7-line me-1"></i>
                                    Delete</a>
                            </div>
                        @endif
                    @endif
                </div>
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