@extends('layouts.dec.app')
@section('title','Dashboard | DEC Approver')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('dec.dashboard')}}" class="menu-link bottom-menu-icons">
                                 <i class="ri-home-4-line"></i>
                           </a>
                        </li>
                        <li class="menu-item">
                           <a href="javascript:void(0)" onclick="location.reload();" class="menu-link bottom-menu-icons">
                                 <i class="ri-restart-line"></i>
                           </a>
                        </li>
                     </ul>
               </div>
            </div>
            <div class="col-md-7">
            </div>
         </div>
   </div>
</aside>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
         <div class="row g-6 mb-5">
            <div class="col-sm-12 col-lg-3">
               <p class="mb-1">Hello, <span class="theme-color">{{auth()->user()->name}}</span></p>
               <div class="d-flex ">
                  <h6 class="mb-0 mb-md-0">Your Applications!</h6>
               </div>
            </div>
            <div class="card shadow-none border-0 p-0 mb-6">
               <div class="card-header p-0">
                  <div class="nav-align-top">
                     <ul class="nav nav-tabs ct-tabs" role="tablist">
                        <li class="nav-item active">
                              <a href="{{route('dec.dashboard')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'dec.dashboard' ? 'bg-primary text-white' : '' }} ">
                                 Dashboard                                                  
                              </a>
                        </li>  
                        <li class="nav-item">
                              <a href="{{route('dec.worklist')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'dec.worklist' ? 'bg-primary text-white' : '' }}">
                              Worklist                                                  
                              </a>
                        </li>  
                        <!-- <li class="nav-item">
                              <a href="#" class="nav-link btn-outline-primary">
                              EDC                                                  
                              </a>
                        </li> 
                        <li class="nav-item">
                              <a href="#" class="nav-link btn-outline-primary">
                              Annual Declartion                                                  
                              </a>
                        </li>                                                -->
                     </ul>
                  </div>
               </div>
            </div>

            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">                     
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $scheme_types = App\CentralLogics\Helpers::getCommanData('SchemeType'); @endphp
                                <select class="form-select select2" id="scheme_id" name="scheme_id">
                                    <option value=""></option>
                                    @foreach($scheme_types as $scheme_type)
                                        <option value="{{ $scheme_type->id }}">{{ $scheme_type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="scheme_id">Scheme Name</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div
                                class="form-floating form-floating-outline mb-3">
                                @php $FacilityType = App\CentralLogics\Helpers::getCommanData('FacilityType'); @endphp
                                <select class="form-select select2" id="facility_type" name="facility_type">
                                    <option value=""></option>
                                    @foreach($FacilityType as $scheme_type)
                                        <option value="{{ $scheme_type->id }}">{{ $scheme_type->name }}</option>
                                    @endforeach
                                </select>
                                <label for="facility_type">Hospital Type</label>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="d-flex justify-content-first mb-3">
                                <button class="btn btn-outline-primary me-2" id="search"><i class="ri-search-line"></i> Search</button>
                                <button class="btn btn-outline-info" id="reset"><i class="ri-loop-right-line"></i> Reset</button>
                            </div>
                        </div>
                    </div>                   
                </div>
            </div>
         </div>
         <div class="mb-5" id="loadstatitacs">
            
         </div>

         <!-- Chart -->
         <div class="row g-6 mb-5">
            <div class="col-12 col-xxl-4 col-md-6 g-6">
               <div class="card h-100">
                  <div class="card-header">
                     <div class="d-flex justify-content-between">
                        <div class="form-check">
                           <input class="form-check-input" type="radio" checked name="type" id="option1" value="empanelled">
                           <label class="form-check-label" for="option1">
                                 Empanelled
                           </label>
                        </div>
                        <div class="form-check ms-4">
                           <input class="form-check-input" type="radio" name="type" id="option2" value="pending">
                           <label class="form-check-label" for="option2">
                                 Pending
                           </label>
                        </div> 
                        <div class="justify-content-end ">
                           <a href="javascript:;" class="float-end " id="downloadmyChart"><i class="ri-download-line"></i></a>
                        </div>
                     </div>                     
                  </div>
                  <div class="card-body pb-1 px-0" style="position: relative;">
                     <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
                  </div>
               </div>
            </div>

            <div class="col-12 col-xxl-4 col-md-6 g-6">
               <div class="card h-100">
                  <div class="card-header">
                     <div class="d-flex justify-content-between">
                        <h6>Empanelled Hospital Bed Size</h6>
                        <div class="justify-content-end ">
                           <a href="javascript:;" class="float-end " id="downloadbedsizechart"><i class="ri-download-line"></i></a>
                        </div>
                     </div>
                  </div>
                  <div class="card-body pb-1 px-0" style="position: relative;">
                     <canvas id="bedsize" style="width:100%;max-width:600px"></canvas>
                  </div>
               </div>
            </div>

            <div class="col-12 col-xxl-4 col-md-6 g-6">
               <div class="card h-100">
                  <div class="card-header">
                     <div class="d-flex justify-content-between">
                        <div class="form-check">
                           <input class="form-check-input" type="radio" checked name="status" id="option11" value="In-Active">
                           <label class="form-check-label" for="option11">
                              InActive
                           </label>
                        </div>
                        <div class="form-check ms-4">
                           <input class="form-check-input" type="radio" name="status" id="option22" value="Suspended">
                           <label class="form-check-label" for="option22">
                              Suspended
                           </label>
                        </div>
                        <div class="form-check ms-4">
                           <input class="form-check-input" type="radio" name="status" id="option3" value="De-Empanelled">
                           <label class="form-check-label" for="option3">
                              De-Empanelled
                           </label>
                        </div>
                        <div class="justify-content-end ">
                           <a href="javascript:;" class="float-end " id="downloadstatuschart"><i class="ri-download-line"></i></a>
                        </div>
                     </div>
                  </div>
                  <div class="card-body pb-1 px-0" style="position: relative;">
                     <canvas id="statuschart" style="width:100%;max-width:600px"></canvas>
                  </div>
               </div>
            </div>

            <div class="col-12 col-xxl-13 col-md-13 g-6">
               <div class="card h-100">
                  <div class="card-header">
                     <div class="d-flex justify-content-between">
                        <h6>Trends</h6>
                        <div class="justify-content-end ">
                           <a href="javascript:;" class="float-end trends" id="downloadtrendsChart"><i class="ri-download-line"></i></a>
                        </div>
                     </div>
                  </div>
                  <div class="card-body pb-1 px-0" style="position: relative;">
                     <canvas id="trendschart" style="width:100%;height:400px;"></canvas>
                  </div>
               </div>
            </div>

            <div class="col-12 col-xxl-13 col-md-13 g-6">
               <div class="card h-100">
                  <div class="card-header">
                     <div class="d-flex justify-content-between">
                        <h6>Specialities</h6>
                        <div class="justify-content-end ">
                           <a href="javascript:;" class="float-end specialities" id="downloadspecialitiesChart"><i class="ri-download-line"></i></a>
                        </div>
                     
                     </div>
                  </div>
                  <div class="card-body pb-1 px-0" style="position: relative;">
                     <canvas id="specialitychart" style="width:100%;"></canvas>
                  </div>
               </div>
            </div>
         </div>
   </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>

   function loadstatitacs() {
      const formData = new FormData();

      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);
      ldrshow();

      $.ajax({
         url: '{{route("dec.loadstatitacs")}}', 
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (data) {
            ldrhide();
            $(`#loadstatitacs`).html(data.html || data);
         },
         error: function (xhr, status, error) {
            ldrhide(); // Hide loader on error
            errorMessage("Failed to load the step. Please try again.");
         }
      });
   }

   // FacilityType Chart
   let myChartInstance;

   function renderChart(labels, data, colors) {
      const ctx = document.getElementById("myChart");

      // Destroy existing chart instance if it exists
      if (myChartInstance) {
         myChartInstance.destroy();
      }

      myChartInstance = new Chart(ctx, {
         type: "doughnut",
         data: {
            labels: labels,
            datasets: [{
               backgroundColor: colors,
               data: data
            }]
         },
         options: {
            title: {
               display: false
            },
         }
      });
   }

   // Bed Size chart 
   let bedChartInstance;

   function renderBedSizeChart(labels, data, colors) {
      const ctx = document.getElementById('bedsize').getContext('2d');

      if (bedChartInstance) {
         bedChartInstance.destroy();
      }

      bedChartInstance = new Chart(ctx, {
         type: 'doughnut',
         data: {
         labels: labels,
         datasets: [{
            data: data,
            backgroundColor: colors,
            borderWidth: 0,
            cutout: '60%'
         }]
         },
         options: {
         responsive: true,
         maintainAspectRatio: false,
         rotation: 180 * (Math.PI / 180),
         circumference: 180 * (Math.PI / 180),
         layout: {
            padding: {
               bottom: 30
            }
         },
         plugins: {
            legend: {
               display: true,
               position: 'bottom',
               align: 'center',
               labels: {
               boxWidth: 12,
               padding: 10,
               font: {
                  size: 12
               }
               }
            },
            tooltip: {
               enabled: true
            }
         }
         }
      });
   }

   // Status Chart
   let myStatusChartInstance;

   function renderStatusChart(labels, data, colors) {
      const ctx = document.getElementById("statuschart");

      // Destroy existing chart instance if it exists
      if (myStatusChartInstance) {
         myStatusChartInstance.destroy();
      }

      myStatusChartInstance = new Chart(ctx, {
         type: "doughnut",
         data: {
            labels: labels,
            datasets: [{
               backgroundColor: colors,
               data: data
            }]
         },
         options: {
            title: {
               display: false
            },
         }
      });
   }

   // Trands Chart
   let monthlyChart;

   function renderTrendschart(labels, empanelled, pendingDec, pendingSec) {
      const ctx = document.getElementById('trendschart').getContext('2d');

      if (monthlyChart) {
         monthlyChart.destroy();
      }

      monthlyChart = new Chart(ctx, {
         type: "line",
         data: {
               labels: labels,
               datasets: [
                  {
                     label: "Empanelled",
                     data: empanelled,
                     borderColor: "green",
                     fill: false
                  },
                  {
                     label: "Pending DEC",
                     data: pendingDec,
                     borderColor: "orange",
                     fill: false
                  },
                  {
                     label: "Pending SEC",
                     data: pendingSec,
                     borderColor: "red",
                     fill: false
                  }
               ]
         },
         options: {
               responsive: true,
               maintainAspectRatio: false,
               plugins: {
                  legend: {
                     display: true,
                     position: 'top'
                  }
               },
               scales: {
                  x: {
                     ticks: {
                           maxRotation: 90,
                           minRotation: 45
                     }
                  },
                  y: {
                     beginAtZero: true
                  }
               }
         }
      });
   }

   // Ajax requests
   loadstatitacs();
   fetchChartData();
   fetchBedSizeChartData();
   fetchStatusChartData();
   loadTrandsStatusChart();
   loadspecialitieschart();
   
   $('input[name="type"]').on('change', function () {
      const selectedType = $(this).val();
      fetchChartData(selectedType);
   });
   
   $('input[name="status"]').on('change', function () {
      const selectedType = $(this).val();
      fetchStatusChartData(selectedType);
   });
   
   
   function fetchChartData(type = 'empanelled') {
      const formData = new FormData();
      formData.append('type', type);

      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);

      $.ajax({
         url: '{{ route("dec.hospitaltypechart") }}',
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (response) {
            ldrhide();
            renderChart(response.labels, response.data, response.colors);
         }
      });
   }
   function fetchBedSizeChartData() {
      const formData = new FormData();

      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);

      $.ajax({
         url: '{{ route("dec.bedsizechart") }}',
         type: 'POST',
         headers: {
         'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         data: formData,
         processData: false,
         contentType: false,
         success: function(response) {
            renderBedSizeChart(response.labels, response.data, response.colors);
         }
      });
   }
   function fetchStatusChartData(status = 'In-Active') {
      const formData = new FormData();
      formData.append('status', status);
      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);

      $.ajax({
         url: '{{ route("dec.statusChart") }}',
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (response) {
            ldrhide();
            renderStatusChart(response.labels, response.data, response.colors);
         }
      });
   }
   function loadTrandsStatusChart() {
      const formData = new FormData();
      // formData.append('status', status);
      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);
      $.ajax({
         url: '{{ route("dec.trandsChart") }}',
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function (response) {
            ldrhide();
            renderTrendschart(response.labels, response.empanelled, response.pending_dec, response.pending_sec);
         }
      });
   }

   let specialityChart; 
   function loadspecialitieschart() {
      const formData = new FormData();
      // formData.append('status', status);
      var scheme_id = $("#scheme_id").val();
      var facility_type = $("#facility_type").val();
      formData.append('scheme_id', scheme_id);
      formData.append('facility_type', facility_type);
      $.ajax({
         url: '{{ route("dec.specialiitieschart") }}', // Adjust route name as needed
         headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false,
         contentType: false,
         success: function(response) {
            const ctx = document.getElementById('specialitychart').getContext('2d');

            if (specialityChart) {
               specialityChart.destroy();
            }

            if (response.data.length === 0) {
               ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
               return;
            }
   
            specialityChart = new Chart(ctx, {
               type: 'horizontalBar',
               data: {
                  labels: response.labels,
                  datasets: [{
                     label: '',
                     data: response.data,
                     backgroundColor: response.colors,
                     borderColor: response.colors,
                     borderWidth: 1
                  }]
               },
               options: {
                  title: {
                     display: false,
                  },
                  legend: {
                     display: false,
                     position: 'right'
                  },
                  scales: {
                     xAxes: [{
                        ticks: {
                           beginAtZero: true
                        }
                     }],
                     yAxes: [{
                        barPercentage: 0.7
                     }]
                  }
               }
            });
         },
         error: function(xhr) {
            console.error('Error loading speciality chart:', xhr);
         }
      });
   }

   $("#search").on("click", function(){
      loadstatitacs();
      fetchChartData();
      fetchBedSizeChartData();
      fetchStatusChartData();
      loadTrandsStatusChart();
      loadspecialitieschart();
   });

   $("#reset").on("click", function() {
      $("#facility_type").val("").trigger('change'); 
      $("#scheme_id").val("").trigger('change'); 
      loadstatitacs();
      fetchChartData();
      fetchBedSizeChartData();
      fetchStatusChartData();
      loadTrandsStatusChart();
      loadspecialitieschart();
   })

   document.getElementById('downloadtrendsChart').addEventListener('click', function () {
      if (monthlyChart) {
         const a = document.createElement('a');
         a.href = monthlyChart.toBase64Image();
         a.download = 'trends-chart.png';
         a.click();
      }
   });

   document.getElementById('downloadspecialitiesChart').addEventListener('click', function () {
      if (specialityChart) {
         const a = document.createElement('a');
         a.href = specialityChart.toBase64Image();
         a.download = 'speciality-chart.png';
         a.click();
      }
   });

   document.getElementById('downloadmyChart').addEventListener('click', function () {
      if (myChartInstance) {
         const a = document.createElement('a');
         a.href = myChartInstance.toBase64Image();
         a.download = 'chart.png';
         a.click();
      }
   });

   document.getElementById('downloadbedsizechart').addEventListener('click', function () {
      if (bedChartInstance) {
         const a = document.createElement('a');
         a.href = bedChartInstance.toBase64Image();
         a.download = 'bedsize-chart.png';
         a.click();
      }
   });

   document.getElementById('downloadstatuschart').addEventListener('click', function () {
      if (myStatusChartInstance) {
         const a = document.createElement('a');
         a.href = myStatusChartInstance.toBase64Image();
         a.download = 'status-chart.png';
         a.click();
      }
   });

  
</script>
@endpush
