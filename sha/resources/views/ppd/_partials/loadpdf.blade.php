@php($type = '')
@if($investigation->type)
    @php($type = $investigation->type)
@endif
<div class="pdfview">
    <iframe src="{{asset('public/storage/'.@$investigation->file) }}" width="100%" height="500px" ></iframe>
</div>
@if($preauth_register->status == \App\Models\PreauthRegister::STATUS_PREAUTH_PENDING)
<div class="d-flex justify-content-end mt-3 mb-3">
    <button class="btn btn-outline-primary btn-lg" type="button" onclick="verifyDocument('Correct', '{{$investigation->id}}', '{{$type}}');" id="preview">Correct</button>
    <button type="button" class="btn btn-outline-danger ms-2" onclick="verifyDocument('InCorrect', '{{$investigation->id}}', '{{$type}}');" >In Correct</button>
</div>
@endif
