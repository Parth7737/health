<div class="row g-6 mb-5">
    <div class="card shadow-none border-0 p-0 mb-6">
        <div class="card-header p-0">
            <div class="nav-align-top">
                <ul class="nav nav-tabs ct-tabs" role="tablist">
                <li class="nav-item active">
                    <a href="{{route('hospital.single-empanelment-dashboard', base64_encode($hospital->uuid))}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.single-empanelment-dashboard' ? 'bg-primary text-white' : '' }} ">
                        Dashboard                                                  
                    </a>
                </li>  
                <li class="nav-item">
                    <a href="{{route('hospital.update-application', base64_encode($hospital->uuid))}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.update-application' ? 'bg-primary text-white' : '' }}">
                        Update Application                                                  
                    </a>
                </li>  
                <li class="nav-item">
                    <a href="{{route('hospital.withdraw-application', base64_encode($hospital->uuid))}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.withdraw-application' ? 'bg-primary text-white' : '' }}">
                        Withdraw                                                  
                    </a>
                </li> 
                <li class="nav-item">
                    <a href="{{route('hospital.qualityaudit', base64_encode($hospital->uuid))}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.qualityaudit' ? 'bg-primary text-white' : '' }}">
                        Quality Audit                                         
                    </a>
                </li>    
                <li class="nav-item">
                    <a href="#" class="nav-link btn-outline-primary">
                        Hospital Profile                                     
                    </a>
                </li>   
                <li class="nav-item">
                    <a href="{{route('hospital.annualdeclaration', base64_encode($hospital->uuid))}}" class="nav-link btn-outline-primary {{ Route::currentRouteName() == 'hospital.annualdeclaration' ? 'bg-primary text-white' : '' }}">
                    Annual Declaration                                
                    </a>
                </li>                                           
                </ul>
            </div>
        </div>
    </div>
</div>