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
</tr>
@if($procedure->implant_id)
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