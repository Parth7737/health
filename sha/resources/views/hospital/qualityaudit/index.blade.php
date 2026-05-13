@extends('layouts.hospital.app')
@section('title','Dashboard | Quality Audit')
@section('content')

@include('hospital.base.topheader')
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
        @include('hospital.base.topbarmenu')
        
        <div class="row g-6">
            @include('hospital.base.basicdetails')
            <div class="bg-white rounded-3 box-shadow p-5 mt-5">
                <div class="card shadow-none border-0 p-0 mb-6">
                    <div class="card-header p-0">
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs ct-tabs" role="tablist">
                                
                                <li class="nav-item">
                                    <button type="button"
                                        class="nav-link navstep1" role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-scheme"
                                        aria-controls="tab-scheme"
                                        aria-selected="true" onclick="loadStep(1, 1);">
                                        Dashboard
                                    </button>
                                </li>
                                @php($k = 2)
                                @foreach($auditcategory as $key => $value)
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link navstep{{$k}}" role="tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#tab-scheme"
                                            aria-controls="tab-scheme"
                                            aria-selected="true" onclick="loadStep('{{$k}}', '{{$value->id}}');">
                                            {{$value->name}}
                                        </button>
                                    </li>
                                    @php($k++)
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-5">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade step1" id="tab-dashboard" role="tabpanel"></div>
                            @php($j = 2)
                            @foreach($auditcategory as $key => $value)
                                <div class="tab-pane fade allstep step{{$j}}" id="tab-{{Str::slug($value->name)}}" role="tabpanel"></div>
                                @php($j++)
                            @endforeach                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>
@endsection

@push('scripts')
    <script>
        loadStep(1, 1);
        function loadStep(step, id) {
            $(".allstep").html('');
            ldrshow();
            $('.nav-link').removeClass('active');
            $('.tab-pane').removeClass('show active');
            $(`.step${step}`).addClass('show active');
            $(`.navstep${step}`).addClass('active');
            if(step != ""){
                $.ajax({
                    url: '{{route("hospital.load-quality-audit-step", [base64_encode($hospital->uuid), base64_encode($hospital->id)])}}', 
                    type: 'POST',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'step' : step,
                        'id': id
                    },
                    success: function (data) {
                        ldrhide();
                                               
                        $(`.step${step}`).on('click', function(event) {
                            if (event.target.closest('.nav-item .active')) {
                                setSlider(event.target.closest('.nav-item'));
                            }
                        });
                        
                        // Populate the content of the step
                        $(`.step${step}`).html(data.html || data);
                        loadSelect2();
                       
                    },
                    error: function (xhr, status, error) {
                        ldrhide(); // Hide loader on error
                        console.error("Error loading step:", error);
                        alert("Failed to load the step. Please try again.");
                    }
                });
            }
        }
    </script>
@endpush
