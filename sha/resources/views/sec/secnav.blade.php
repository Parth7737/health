<div class="nav-align-top">
    <ul class="nav nav-tabs ct-tabs" role="tablist">
        <li class="nav-item active">
                <a href="{{route('sec.dashboard')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'sec.dashboard' ? 'bg-primary text-white' : '' }} ">
                    Dashboard                                                  
                </a>
        </li>  
        <li class="nav-item">
                <a href="{{route('sec.worklist')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'sec.worklist' ? 'bg-primary text-white' : '' }}">
                Worklist                                                  
                </a>
        </li>  
        <li class="nav-item">
                <a href="{{route('sec.edcindex')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'sec.edcindex' || Route::currentRouteName() == 'sec.initiate.actionlist' || Route::currentRouteName() == 'sec.initiate.action' ? 'bg-primary text-white' : '' }}">
                EDC                                                  
                </a>
        </li> 
        <!-- <li class="nav-item">
                <a href="#" class="nav-link btn-outline-primary">
                External Verifier Audit                                                  
                </a>
        </li>  -->
        <li class="nav-item">
            <a href="{{route('sec.annualdeclaration')}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'sec.annualdeclaration' ? 'bg-primary text-white' : '' }}">
                Annual Declartion                                                  
            </a>
        </li>                                               
    </ul>
</div>