@php($type = '')
@if($investigation->type)
    @php($type = $investigation->type)
@endif
<div class="pdfview">
    <iframe src="{{asset('public/storage/'.@$investigation->file) }}" width="100%" height="500px" ></iframe>
</div>