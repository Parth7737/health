<div class="accordion accordion-popout mt-4" id="accordionSUBPopout">
	<div class="accordion-item medicalsteptab @if(!$checkstepComplete['medicalstep']) active @else hide @endif">
		<h2 class="accordion-header" id="headingPopoutOne">
			<button type="button" class="accordion-button medicalstep theme-color @if($checkstepComplete['medicalstep']) theme-color hide collapsed @else pending-color @endif"
				data-bs-toggle="collapse"
				data-bs-target="#accordionSUBPopoutOne"
				aria-expanded="true" aria-controls="accordionSUBPopoutOne">
			Medical Information
			</button>
		</h2>
		<div id="accordionSUBPopoutOne"
			class="accordion-collapse medicalstepcollapse collapse @if(!$checkstepComplete['medicalstep']) show @else hide collapse @endif"
			aria-labelledby="headingPopoutOne"
			data-bs-parent="#accordionSUBPopout">
			<div class="accordion-body">
				@include('hospital.empanelment._partials.humanresource.ceo')
				@include('hospital.empanelment._partials.humanresource.nodalofficer')
				@include('hospital.empanelment._partials.humanresource.humanresource')
				@include('hospital.empanelment._partials.humanresource.nonmedicalhr')
			</div>
		</div>
	</div>
	<div class="accordion-item servicesteptab @if(!$checkstepComplete['servicestep']) active @else hide @endif">
		<h2 class="accordion-header" id="headingPopoutTwo">
			<button type="button"
				class="accordion-button servicestep theme-color @if($checkstepComplete['servicestep']) theme-color hide collapsed @else pending-color @endif collapsed"
				data-bs-toggle="collapse"
				data-bs-target="#accordionSUBPopoutTwo"
				aria-expanded="false"
				aria-controls="accordionSUBPopoutTwo">
			Support Services Human Resources
			</button>
		</h2>
		<div id="accordionSUBPopoutTwo" class="accordion-collapse sservicestepcollapse collapse @if(!$checkstepComplete['servicestep']) show @else hide collapse @endif"
			aria-labelledby="headingPopoutTwo"
			data-bs-parent="#accordionSUBPopout">
			<div class="accordion-body">
				@include('hospital.empanelment._partials.humanresource.supportservice')
				
			</div>
		</div>
	</div>
	<div class="accordion-item specialiststeptab @if(!$checkstepComplete['specialiststep']) active @else hide @endif">
		<h2 class="accordion-header" id="headingPopoutThree">
			<button type="button"
				class="accordion-button specialiststep theme-color @if($checkstepComplete['specialiststep']) theme-color hide collapsed @else pending-color @endif collapsed"
				data-bs-toggle="collapse"
				data-bs-target="#accordionSUBPopoutThree"
				aria-expanded="false"
				aria-controls="accordionSUBPopoutThree">
			Specialist
			</button>
		</h2>
		<div id="accordionSUBPopoutThree" class="accordion-collapse specialiststepcollapse @if(!$checkstepComplete['specialiststep']) show @else hide collapse @endif"
			aria-labelledby="headingPopoutThree"
			data-bs-parent="#accordionSUBPopout">
			<div class="accordion-body">
				@include('hospital.empanelment._partials.humanresource.specialities')
			</div>
		</div>
	</div>
</div>

<script>
	function CheckHumanResourceStepCompleteOrNot(step, isLoad = 0) {
		$('.nav-link').removeClass('active');
		$('.tab-pane').removeClass('show active');
		$(`.step${step}`).addClass('show active');
		$(`.navstep${step}`).addClass('active');
		setTimeout(() => {
			$(`.step${step}`).on('click', function(event) {
				if (event.target.closest('.nav-item .active')) {
					setSlider(event.target.closest('.nav-item'));
				}
			});
			$(`.step${(step-1)}Icon`).show();
			// Populate the content of the step
			// $(`.step${step}`).html(data.html || data);
			if(isLoad) {
				$(`.step${step}Icon`).hide();
				loadStep(step);
			}
		}, 1000);
	}
</script>