@php
    $status = \App\CentralLogics\Helpers::checkStatus($preauth_register->id, 'cpd');

@endphp

<div class="row g-5 mt-2">
    <div class="col-12">
        <table class="table">
            <thead class="table-white">
                <tr class="border-1">
                    <th class="text-primary p-2">Overall findings on the documents by {{auth()->user()->role->name}}</th>
                    <th class="float-left p-2">@if($status) <span class="text-primary"><strong>Correct</strong></span> @else <span class="text-danger"><strong>Incorrect</strong></span> @endif</th>
                </tr>
            </thead>
        </table>
    </div>
</div>