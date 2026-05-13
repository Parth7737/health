<div class="nav-align-top">
    <ul class="nav nav-tabs ct-tabs" role="tablist">
        <li class="nav-item active">
                <a href="{{route('hospital.dashboard')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.dashboard' ? 'bg-primary text-white' : '' }} ">
                    Dashboard                                                  
                </a>
        </li>
        <li class="nav-item">
                <a href="{{route('hospital.edcindex')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.edcindex' || Route::currentRouteName() == 'sec.initiate.actionlist' || Route::currentRouteName() == 'sec.initiate.action' ? 'bg-primary text-white' : '' }}">
                EDC                                                  
                </a>
        </li>                                             
    </ul>
</div>