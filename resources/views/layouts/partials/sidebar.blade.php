@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp 
<!-- Page Sidebar Start-->
    <div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
        <div>
            <div class="logo-wrapper"><a href="{{ route('hospital.dashboard') }}"><img class="img-fluid for-light" src="{{asset('public/front/assets/images/logo/logo.png')}}" alt=""><img class="img-fluid for-dark" src="{{asset('public/front/assets/images/logo/logo_dark.png')}}" alt=""></a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
            </div>
            <div class="logo-icon-wrapper"><a href="{{ route('hospital.dashboard') }}"><img class="img-fluid" src="{{asset('public/front/assets/images/logo/logo-icon.png')}}" alt=""></a></div>
            <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="sidebar-menu">
                  <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="{{ route('hospital.dashboard') }}"><img class="img-fluid" src="{{asset('public/front/assets/images/logo/logo-icon.png')}}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Front Office" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg class="stroke-icon" xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M691.33-615.33H758V-682h-66.67v66.67Zm0 167.33H758v-66.67h-66.67V-448Zm0 166.67H758V-348h-66.67v66.67ZM654-120v-66.67h199.33v-586.66H470V-680l-66.67-47.33V-840H920v720H654Zm-614 0v-393.33l274-196 273.33 196V-120H362.67v-200.67h-97.34V-120H40Zm66.67-66.67h92v-200.66h230.66v200.66h91.34v-292.66L314-627.33 106.67-479.05v292.38ZM654-550ZM429.33-186.67v-200.66H198.67v200.66-200.66h230.66v200.66Z"/></svg>
                        <span class="lan-3">Front Office</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="OPD - Out Patient" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                       <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M540-80.67q-110.67 0-185.33-77.66Q280-236 280-344.67v-31q-85.33-12-142.67-77.3Q80-518.28 80-606.67V-840h120v-40h66.67v146.67H200v-40h-53.33v166.45q0 69.55 48.66 118.21Q244-440 313.33-440q69.34 0 118-48.67Q480-537.33 480-606.88v-166.45h-53.33v40H360V-880h66.67v40h120v233.33q0 88.39-57.34 153.7-57.33 65.3-142.66 77.3v31q0 81.67 55.16 139.5Q457-147.33 540-147.33q79 0 136.17-57.6 57.16-57.61 57.16-139.89v-73.51q-35-10.67-57.5-40.34-22.5-29.66-22.5-68 0-47.22 33.08-80.27Q719.49-640 766.75-640 814-640 847-606.94q33 33.05 33 80.27 0 38.34-22.5 68Q835-429 800-418.33v73.66q0 110-76.33 187-76.34 77-183.67 77ZM766.55-480q19.78 0 33.28-13.38 13.5-13.39 13.5-33.17t-13.38-33.28q-13.38-13.5-33.17-13.5-19.78 0-33.28 13.38T720-526.78q0 19.78 13.38 33.28 13.39 13.5 33.17 13.5Zm.12-46.67Z"/></svg>
                        <span class="lan-3">OPD - Out Patient</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="IPD - In Patient" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M146.67-80q-27.5 0-47.09-19.58Q80-119.17 80-146.67v-666.66q0-27.5 19.58-47.09Q119.17-880 146.67-880h386.66q27.5 0 47.09 19.58Q600-840.83 600-813.33v666.66q0 27.5-19.58 47.09Q560.83-80 533.33-80H146.67Zm0-449.67q19.33-15 43-22.66Q213.33-560 240-560h200q26.67 0 50.33 7.67 23.67 7.66 43 22.66v-283.66H146.67v283.66Zm193.27-77q-30.27 0-51.77-21.56-21.5-21.55-21.5-51.83 0-30.27 21.56-51.77 21.55-21.5 51.83-21.5 30.27 0 51.77 21.56 21.5 21.55 21.5 51.83 0 30.27-21.56 51.77-21.55 21.5-51.83 21.5ZM780-340 640-480l140-140 46.67 46.67-59 60H920v66.66H767.67l59 60L780-340ZM146.67-146.67h386.66V-400q0-39-27.16-66.17Q479-493.33 440-493.33H240q-39 0-66.17 27.16Q146.67-439 146.67-400v253.33Zm160-60h66.66v-80h80v-66.66h-80v-80h-66.66v80h-80v66.66h80v80Zm-160 60h386.66-386.66Z"/></svg>
                        <span class="lan-3">IPD - In Patient</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Patient Claims" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M146.67-80q-27 0-46.84-19.83Q80-119.67 80-146.67v-506.66q0-27 19.83-46.84Q119.67-720 146.67-720H320v-93.33q0-27 19.83-46.84Q359.67-880 386.67-880h186.66q27 0 46.84 19.83Q640-840.33 640-813.33V-720h173.33q27 0 46.84 19.83Q880-680.33 880-653.33v506.66q0 27-19.83 46.84Q840.33-80 813.33-80H146.67Zm0-66.67h666.66v-506.66H146.67v506.66Zm240-573.33h186.66v-93.33H386.67V-720Zm-240 573.33v-506.66 506.66Zm300-220v120h66.66v-120h120v-66.66h-120v-120h-66.66v120h-120v66.66h120Z"/></svg>
                        <span class="lan-3">Patient Claims</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Pharmacy" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M120-120v-66.67l84.67-250-84.67-250v-66.66h520.67L701.33-920 778-890.67l-50 137.34h112v66.66l-85.33 250 85.33 250V-120H120Zm328-160.67h66.67v-122.66h122.66V-470H514.67v-122.67H448V-470H325.33v66.67H448v122.66Zm-258.67 94h581.34l-85.34-250 85.34-250H189.33l85.34 250-85.34 250Zm290.67-250Z"/></svg>
                        <span class="lan-3">Pharmacy</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Pathology" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M320-293.33V-360h102.33q-5 16-6.83 32.67-1.83 16.66-1.83 34H320ZM320-80q-83 0-141.5-58.5T120-280v-360q-33 0-56.5-23.5T40-720v-80q0-33 23.5-56.5T120-880h400q33 0 56.5 23.5T600-800v80q0 33-23.5 56.5T520-640v140.33q-14 9.67-26 21.34-12 11.66-22.33 25H320V-520h133.33v-120H186.67v360q0 55.56 38.89 94.44 38.88 38.89 94.44 38.89 34 0 62.5-15.66 28.5-15.67 46.83-42 6.67 17.14 15 32.57 8.34 15.43 19.67 30.43-27.67 28.66-64.35 45Q362.97-80 320-80ZM106.67-706.67h426.66v-106.66H106.67v106.66Zm559.9 520q44.76 0 75.76-30.9 31-30.91 31-75.67 0-44.76-30.9-75.76-30.91-31-75.67-31Q622-400 591-369.09q-31 30.9-31 75.66t30.91 75.76q30.9 31 75.66 31Zm204.1 144L763.33-150q-22 14.67-46.21 22.33Q692.9-120 666.67-120q-72.23 0-122.78-50.58-50.56-50.58-50.56-122.83 0-72.26 50.58-122.76t122.84-50.5q72.25 0 122.75 50.56Q840-365.56 840-293.33q0 26.23-7.67 50.45-7.66 24.21-22.33 46.21L917.33-89.33l-46.66 46.66Zm-764-664v-106.66 106.66Z"/></svg>
                        <span class="lan-3">Pathology</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Radiology" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M200-120v-66.67h205.33v-96h-10q-81.66 0-138.5-56.83Q200-396.33 200-478q0-61 34.5-111t91.5-71q5.33-38 35.17-61.67Q391-745.33 430-743.33l-21.33-60 40-14.38-14-36.96L504-880l13.33 37.33 39.34-14 112 296.67-41.34 14.67 14 37.33-68 24.67L560-520.67l-41.33 15.34L494-572.67q-15 16-35.17 23.34-20.16 7.33-42.39 6Q392-544.67 371-558.5q-21-13.83-34.33-34.83-32.34 16.66-51.17 47.64T266.67-478q0 53.61 37.52 91.14 37.53 37.53 91.14 37.53h338v66.66H512v96h248V-120H200Zm352.67-454L600-591.33l-76-200L476-774l76.67 200Zm-128.79-22.67q19.79 0 33.29-13.38t13.5-33.17q0-19.78-13.39-33.28Q443.9-690 424.12-690q-19.79 0-33.29 13.38-13.5 13.39-13.5 33.17t13.39 33.28q13.38 13.5 33.16 13.5ZM552.67-574ZM424-644.67Zm1.33 0Z"/></svg>
                        <span class="lan-3">Radiology</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Operation Theatre" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M496-346 346-496l336.67-336.67q10-10 23.83-10 13.83 0 23.83 10l102.34 102.34q10 10 10 23.83 0 13.83-10 23.83L496-346Zm0-94.67L762.67-707 707-762.67 440.67-496 496-440.67ZM440-120l67.33-66.67H880V-120H440Zm-237 0q-46 0-88.5-18T40-188l265-264 104 104q14 14 22 32t8 38q0 20-8 38.5T409-207l-19 19q-32 32-74.5 50T227-120h-24Zm0-66.67h24q32.67 0 63-12.5t53.33-35.5L363-254.33q10-10 9-23.67-1-13.67-11-23.67l-56-56L142.33-196q14.67 4.67 30 7 15.34 2.33 30.67 2.33ZM762.67-707 707-762.67 762.67-707ZM305-357.67Z"/></svg>
                        <span class="lan-3">Operation Theatre</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Blood Bank" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-80q-137 0-228.5-94T160-408q0-100 79.5-217.5T480-880q161 137 240.5 254.5T800-408q0 140-91.5 234T480-80Zm0-66.67q109.33 0 181.33-74.5 72-74.5 72-187.04 0-76.79-64.5-174.46-64.5-97.66-188.83-208.66-124.33 111-188.83 208.66-64.5 97.67-64.5 174.46 0 112.54 72 187.04 72 74.5 181.33 74.5ZM360-253.33h240V-320H360v66.67ZM446.67-360h66.66v-86.67H600v-66.66h-86.67V-600h-66.66v86.67H360v66.66h86.67V-360ZM480-480Z"/></svg>
                        <span class="lan-3">Blood Bank</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Vaccine" data-bs-placement="right">
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="m313.33-60-66.66-50v-176.67h-53.34q-27 0-46.83-19.83t-19.83-46.83v-313.34H120q-14.17 0-23.75-9.61-9.58-9.62-9.58-23.84 0-14.21 9.58-23.71t23.75-9.5h126.67v-80H220q-14.17 0-23.75-9.62t-9.58-23.83q0-14.22 9.58-23.72 9.58-9.5 23.75-9.5h120q14.17 0 23.75 9.62 9.58 9.61 9.58 23.83 0 14.22-9.58 23.72-9.58 9.5-23.75 9.5h-26.67v80H440q14.17 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71t-23.75 9.5h-6.67v313.34q0 27-19.83 46.83t-46.83 19.83h-53.34V-60Zm-120-293.33h173.34v-73.34H290q-9.33 0-16.33-7-7-7-7-16.33 0-9.33 7-16.33 7-7 16.33-7h76.67v-73.34H290q-9.33 0-16.33-7-7-7-7-16.33 0-9.33 7-16.33 7-7 16.33-7h76.67v-73.34H193.33v313.34ZM600-80q-27 0-46.83-19.83-19.84-19.84-19.84-46.84v-264q0-31 8.67-48.33t21-31.33q16.33-18 23.33-28.17 7-10.17 7-21.5v-26.67h-6.66q-14.17 0-23.75-9.61-9.59-9.62-9.59-23.84 0-14.21 9.84-23.71 9.83-9.5 23.5-9.5h200q14.16 0 23.75 9.61 9.58 9.62 9.58 23.84 0 14.21-9.58 23.71-9.59 9.5-23.75 9.5H780V-540q0 10.67 8.33 22.67 8.34 12 25 30 12.34 14 19.5 30.33 7.17 16.33 7.17 46.33v264q0 27-19.83 46.84Q800.33-80 773.33-80H600Zm0-306.67h173.33v-24q0-17-9.66-30-9.67-13-21.34-27.33-13.66-17-21.33-33-7.67-16-7.67-39v-26.67H660V-540q0 22-7.17 38-7.16 16-20.83 33-11.67 14.33-21.83 27.83-10.17 13.5-10.17 30.5v24Zm0 120h173.33V-340H600v73.33Zm0 120h173.33V-220H600v73.33Zm0-120h173.33H600Z"/></svg>
                        <span class="lan-3">Vaccine</span></a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Live Consultation" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-icon" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M146.67-160q-27 0-46.84-19.83Q80-199.67 80-226.67v-506.66q0-27 19.83-46.84Q119.67-800 146.67-800h506.66q27 0 46.84 19.83Q720-760.33 720-733.33V-530l160-160v420L720-430v203.33q0 27-19.83 46.84Q680.33-160 653.33-160H146.67Zm0-66.67h506.66v-506.66H146.67v506.66Zm0 0v-506.66 506.66Z"/></svg>
                            <span class="lan-6">Live Consultation</span>
                        </a>
                        <ul class="sidebar-submenu">
                          <li><a href="javascript:;">Live Consultation</a></li>
                          <li><a href="javascript:;">Live Meeting</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="TPA Management" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-80q-138.33-33-229.17-157.5Q160-362 160-520v-240.67l320-120 320 120V-505q-15.67-7.33-33-13.17-17.33-5.83-33.67-8.16V-714L480-808l-253.33 94v194q0 66.33 20.5 124.67Q267.67-337 300.5-290.5t74.17 79.83q41.33 33.34 83 50.67 7.66 18.67 21.66 38.33 14 19.67 27 32.67-6.33 3.33-13.16 5.17Q486.33-82 480-80Zm208.33 0q-79.33 0-135.5-56.5-56.16-56.5-56.16-134.83 0-79.96 56.16-136.31Q608.99-464 688.67-464q79 0 135.5 56.36 56.5 56.35 56.5 136.31 0 78.33-56.5 134.83Q767.67-80 688.33-80ZM480-484Zm192 324h35.33v-93.33h93.34v-35.34h-93.34V-382H672v93.33h-93.33v35.34H672V-160Z"/></svg>
                          <span>TPA Management</span>
                        </a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Finance" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M546.67-426.67q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM240-293.33q-27.5 0-47.08-19.59-19.59-19.58-19.59-47.08v-373.33q0-27.5 19.59-47.09Q212.5-800 240-800h613.33q27.5 0 47.09 19.58Q920-760.83 920-733.33V-360q0 27.5-19.58 47.08-19.59 19.59-47.09 19.59H240ZM333.33-360H760q0-39 27.17-66.17 27.16-27.16 66.16-27.16V-640q-39 0-66.16-27.17Q760-694.33 760-733.33H333.33q0 39-27.16 66.16Q279-640 240-640v186.67q39 0 66.17 27.16Q333.33-399 333.33-360ZM800-160H106.67q-27.5 0-47.09-19.58Q40-199.17 40-226.67V-680h66.67v453.33H800V-160ZM240-360v-373.33V-360Z"/></svg>
                            <span class="lan-6">Finance</span>
                        </a>
                        <ul class="sidebar-submenu">
                          <li><a href="javascript:;">Income</a></li>
                          <li><a href="javascript:;">Expenses</a></li>
                          <li><a href="javascript:;">Finance Summary</a></li>
                        </ul>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Ambulance" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M446.67-806.67V-960h66.66v153.33h-66.66ZM263-752.33 149-867l47-47.67L310.67-800 263-752.33ZM153.33-40q-14.16 0-23.75-9.58Q120-59.17 120-73.33V-396l84-243.1q6-18.23 21.5-29.57Q241-680 260.67-680H370v-76.67h145.67q-23.34 30.67-37.5 66.84-14.17 36.16-16.84 76.5h-198l-59.66 170.66H511q14.33 19.34 32 36.5Q560.67-389 581.33-376H186.67v186.67h586.66V-339q17.67-3.67 34.28-9.1 16.61-5.42 32.39-13.57v288.34q0 14.16-9.58 23.75Q820.83-40 806.67-40h-27.34q-14.16 0-23.75-9.58Q746-59.17 746-73.33v-49.34H213.33v49.34q0 14.16-9.58 23.75Q194.17-40 180-40h-26.67Zm93.34-209.33h120q14.33 0 23.83-9.62 9.5-9.62 9.5-23.83 0-14.22-9.58-23.72-9.59-9.5-23.75-9.5h-120v66.67Zm466.66 0V-316h-120q-14.33 0-23.83 9.62-9.5 9.61-9.5 23.83 0 14.22 9.58 23.72 9.59 9.5 23.75 9.5h120ZM186.67-376v186.67V-376Zm509-134L836-650.67l-32.67-32.66-107.66 107.66-52.34-53L610.67-595l85 85Zm27.66-282.67q81.34 0 138.67 57.33 57.33 57.33 57.33 138.67 0 81.34-57.33 138.67-57.33 57.33-138.67 57.33-81.34 0-138.67-57.33-57.33-57.33-57.33-138.67 0-81.34 57.33-138.67 57.33-57.33 138.67-57.33Z"/></svg>
                          <span>Ambulance</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Birth & Death Record" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480v-380l241 181q31-47 70.5-97T480-880q45 50 86 101.5t72 99.5l242-181v380q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-66.67q139.58 0 236.46-96.87 96.87-96.88 96.87-236.46v-246.67L620-582q-41-66-70.67-108.33-29.66-42.34-69.33-90.34-39.67 48.34-70.67 92Q378.33-645 340-582L146.67-726.67V-480q0 139.58 96.87 236.46 96.88 96.87 236.46 96.87ZM480-464Z"/></svg>
                          <span>Birth & Death Record</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Human Resource" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M40-160v-160q0-30.67 21.5-52t51.83-21.33H251q18 0 34.33 9 16.34 9 27.34 24.66 29 41 73.16 63.67Q430-273.33 480-273.33q50.33 0 94.5-22.67t73.5-63.67q11.67-15.66 27.5-24.66t33.5-9h137.67q30.66 0 52 21.33Q920-350.67 920-320v160H653.33v-109.67q-35.66 30.34-80.16 46.67-44.5 16.33-93.17 16.33-48.33 0-92.67-16.5Q343-239.67 306.67-270v110H40Zm440-166.67q-36 0-69-16.83T357-390q-16.33-23.67-39.83-37.83-23.5-14.17-50.84-17.5Q293-477.67 358-495.5t122-17.83q57 0 122.33 17.83 65.34 17.83 92 50.17-27 3.33-50.66 17.5-23.67 14.16-40 37.83Q583-360 550-343.33q-33 16.66-70 16.66ZM160-453.33q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q113.33-680 160-680q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Zm640 0q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q753.33-680 800-680q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Zm-320-120q-46.67 0-80-33.34-33.33-33.33-33.33-80 0-47.66 33.33-80.5Q433.33-800 480-800q47.67 0 80.5 32.83 32.83 32.84 32.83 80.5 0 46.67-32.83 80-32.83 33.34-80.5 33.34Z"/></svg>
                          <span>Human Resource</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Messaging" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M880-80.67 720.67-240h-414q-27.5 0-47.09-19.58Q240-279.17 240-306.67v-66.66h440q27.5 0 47.08-19.59 19.59-19.58 19.59-47.08v-280h66.66q27.5 0 47.09 19.58Q880-680.83 880-653.33v572.66ZM146.67-441l65.66-65.67h401v-306.66H146.67V-441ZM80-280v-533.33q0-27.5 19.58-47.09Q119.17-880 146.67-880h466.66q27.5 0 47.09 19.58Q680-840.83 680-813.33v306.66q0 27.5-19.58 47.09Q640.83-440 613.33-440H240L80-280Zm66.67-226.67v-306.66 306.66Z"/></svg>
                          <span>Messaging</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Download Center" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M254-160q-89 0-151.5-62T40-373q0-78.67 49.33-138 49.34-59.33 125.34-73 15.66-80 83-144.67 67.33-64.66 149.66-64.66 27 0 46.84 16.83Q514-759.67 514-733.33v276.66l76.67-76L638-485.33 480.67-328 323.33-485.33l47.34-47.34 76.66 76V-730Q364.67-718 318-655.17q-46.67 62.84-46.67 133.84H252q-60 0-102.67 42.33-42.66 42.33-42.66 105t43.66 105Q194-226.67 254-226.67h493.33q44 0 75-31t31-75q0-44-31-75t-75-31h-62v-82.66q0-61.34-29.33-108.17t-76-75.83v-74.34q78.67 31 125.33 101.84Q752-607 752-521.33v16q71 1.33 119.5 50.5Q920-405.67 920-332.67q0 71-50.83 121.84Q818.33-160 747.33-160H254Zm226-350.67Z"/></svg>
                          <span>Download Center</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Inventory" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-582.67q-15.3 0-25.65-10.35Q444-603.37 444-618.67q0-15.3 10.35-25.65 10.35-10.35 25.65-10.35 15.3 0 25.65 10.35Q516-633.97 516-618.67q0 15.3-10.35 25.65-10.35 10.35-25.65 10.35ZM446.67-720v-200h66.66v200h-66.66ZM286.53-80q-30.86 0-52.7-21.97Q212-123.95 212-154.81q0-30.86 21.98-52.69 21.97-21.83 52.83-21.83t52.69 21.97q21.83 21.98 21.83 52.84 0 30.85-21.97 52.69Q317.38-80 286.53-80Zm402.66 0q-30.86 0-52.69-21.97-21.83-21.98-21.83-52.84 0-30.86 21.97-52.69 21.98-21.83 52.84-21.83 30.85 0 52.69 21.97Q764-185.38 764-154.52q0 30.85-21.97 52.69Q720.05-80 689.19-80ZM54.67-813.33V-880h121l170 362.67H630.8l158.87-280h75L698-489.33q-11 19.33-28.87 30.66-17.88 11.34-39.13 11.34H328.67l-52 96H764v66.66H282.67q-40.11 0-61.06-33-20.94-33-2.28-67L280-496 133.33-813.33H54.67Z"/></svg>
                          <span>Inventory</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Front CMS" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M106.67-120q-27 0-46.84-19.83Q40-159.67 40-186.67v-98.66q0-27 19.83-46.84Q79.67-352 106.67-352H368q27 0 46.83 19.83 19.84 19.84 19.84 46.84v98.66q0 27-19.84 46.84Q395-120 368-120H106.67Zm488 0q-27 0-46.84-19.83Q528-159.67 528-186.67v-586.66q0-27 19.83-46.84Q567.67-840 594.67-840h258.66q27 0 46.84 19.83Q920-800.33 920-773.33v586.66q0 27-19.83 46.84Q880.33-120 853.33-120H594.67Zm-488-66.67H368v-98.66H106.67v98.66Zm488 0h258.66v-586.66H594.67v586.66ZM724.12-240q14.21 0 23.71-9.83 9.5-9.84 9.5-23.84t-9.61-23.5q-9.62-9.5-23.84-9.5-14.21 0-23.71 9.59-9.5 9.58-9.5 23.75 0 13.66 9.61 23.5 9.62 9.83 23.84 9.83ZM106.67-445.33q-27 0-46.84-19.84Q40-485 40-512v-261.33q0-27 19.83-46.84Q79.67-840 106.67-840H368q27 0 46.83 19.83 19.84 19.84 19.84 46.84V-512q0 27-19.84 46.83Q395-445.33 368-445.33H106.67Zm175-208q14 0 23.5-9.59 9.5-9.58 9.5-23.75 0-13.66-9.59-23.5-9.58-9.83-23.75-9.83-13.66 0-23.5 9.83-9.83 9.84-9.83 23.84t9.83 23.5q9.84 9.5 23.84 9.5Zm-175 131.66 82-109.66 90.66 120.66H368v-262.66H106.67v251.66ZM237.33-236ZM724-480ZM237.33-642Z"/></svg>
                          <span>Front CMS</span>
                        </a>
                    </li>

                    <li class="sidebar-list"  data-bs-toggle="tooltip" title="Patient" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                          <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-573.33q-66 0-109.67-43.67-43.66-43.67-43.66-109.67t43.66-109.66Q414-880 480-880t109.67 43.67q43.66 43.66 43.66 109.66T589.67-617Q546-573.33 480-573.33Zm0-66.67q37 0 61.83-24.83 24.84-24.84 24.84-61.84t-24.84-61.83Q517-813.33 480-813.33t-61.83 24.83q-24.84 24.83-24.84 61.83t24.84 61.84Q443-640 480-640ZM160-80v-266.33q0-36.67 18.33-64.17 18.34-27.5 48.34-42.23 51-23.94 114.83-42.27 63.83-18.33 138.57-18.33T618.24-495q63.43 18.33 114.43 42.33 30.66 14.67 49 42.17Q800-383 800-346.33v199.66q0 27-19.83 46.84Q760.33-80 733.33-80H390q-43.21 0-73.27-30.09-30.06-30.08-30.06-73.33t30.06-73.25q30.06-30 73.27-30h116.33L575-437.33q-24-4.67-48-7-24-2.34-47.07-2.34-72.24 0-128.42 16.84Q295.33-413 255-392.67q-12.67 6.34-20.5 19-7.83 12.67-7.83 27.34V-80H160Zm230-66.67h52L476-220h-86q-14.67 0-25.67 11-11 11-11 25.67 0 14.66 11 25.66t25.67 11Zm125.33 0h218v-199.66q0-14.67-8.16-27.34-8.17-12.66-20.5-19-12-6-28-12.83t-36-14.17l-125.34 273Zm-35.33-580Zm0 437.34Z"/></svg>
                          <span>Patient</span>
                        </a>
                    </li>

                    <li class="sidebar-list" data-bs-toggle="tooltip" title="Reports" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M120-120v-77.33L186.67-264v144H120Zm163.33 0v-237.33L350-424v304h-66.67Zm163.34 0v-304l66.66 67.67V-120h-66.66ZM610-120v-236.33L676.67-423v303H610Zm163.33 0v-397.33L840-584v464h-66.67ZM120-346.33v-94.34l280-278.66 160 160L840-840v94.33L560-465 400-625 120-346.33Z"/></svg>
                            <span class="lan-6">Reports</span>
                        </a>
                        <ul class="sidebar-submenu">
                          <li><a href="javascript:;">Transaction Report</a></li>
                          <li><a href="javascript:;">Appointment Report</a></li>
                          <li><a href="javascript:;">OPD Report</a></li>
                          <li><a href="javascript:;">IPD Report</a></li>
                          <li><a href="javascript:;">OPD Balance Report</a></li>
                          <li><a href="javascript:;">IPD Balance Report</a></li>
                          <li><a href="javascript:;">Discharge Patient</a></li>
                          <li><a href="javascript:;">Pharmacy Bill Report</a></li>
                          <li><a href="javascript:;">Expiry Medicine Report</a></li>
                          <li><a href="javascript:;">Pathology Patient Report</a></li>
                          <li><a href="javascript:;">Radiology Patient Report</a></li>
                          <li><a href="javascript:;">OT Report</a></li>
                          <li><a href="javascript:;">Blood Issue Report</a></li>
                          <li><a href="javascript:;">Blood Donor Report</a></li>
                          <li><a href="javascript:;">Live Consultantion Report</a></li>
                          <li><a href="javascript:;">Live Meeting Report</a></li>
                          <li><a href="javascript:;">TPA Report</a></li>
                          <li><a href="javascript:;">Income Report</a></li>
                          <li><a href="javascript:;">Income Group Report</a></li>
                          <li><a href="javascript:;">Expense Report</a></li>
                          <li><a href="javascript:;">Expense Group Report</a></li>
                          <li><a href="javascript:;">Ambulance Report</a></li>
                          <li><a href="javascript:;">Birth Report</a></li>
                          <li><a href="javascript:;">Death Report</a></li>
                          <li><a href="javascript:;">Staff Attendance Report</a></li>
                          <li><a href="javascript:;">Selfie Attendance Report</a></li>
                          <li><a href="javascript:;">User Log</a></li>
                          <li><a href="javascript:;">Patient Login Credential</a></li>
                          <li><a href="javascript:;">Email/SMS Log</a></li>
                          <li><a href="javascript:;">Inventory Stock Report</a></li>
                          <li><a href="javascript:;">Inventory Item Report</a></li>
                          <li><a href="javascript:;">Inventory Issue Report</a></li>
                        </ul>
                    </li>

                     <li class="sidebar-list" data-bs-toggle="tooltip" title="Setup" data-bs-placement="right">
                        <a class="sidebar-link sidebar-title" href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#3f475a"><path d="M480-480ZM382-80l-18.67-126.67q-17-6.33-34.83-16.66-17.83-10.34-32.17-21.67L178-192.33 79.33-365l106.34-78.67q-1.67-8.33-2-18.16-.34-9.84-.34-18.17 0-8.33.34-18.17.33-9.83 2-18.16L79.33-595 178-767.67 296.33-715q14.34-11.33 32.34-21.67 18-10.33 34.66-16L382-880h196l18.67 126.67q17 6.33 35.16 16.33 18.17 10 31.84 22L782-767.67 880.67-595l-84 60.67q-18.67-9-38-14.5-19.34-5.5-40.67-8.5l74.33-55.34-39.66-70.66L649-638.67q-22.67-25-50.83-41.83-28.17-16.83-61.84-22.83l-13.66-110h-85l-14 110q-33 7.33-61.5 23.83T311-639l-103.67-44.33-39.66 70.66L259-545.33Q254.67-529 252.33-513 250-497 250-480q0 16.67 2.33 32.67 2.34 16 6.67 32.33l-91.33 67.67 39.66 70.66L311-321.33q19 19 41.5 33.83 22.5 14.83 48.17 23.83 3 54.34 25.16 101Q448-116 484-80H382Zm31-285q5.33-16 12.17-31.5Q432-412 441-426.33q-13-9.34-20-23.17-7-13.83-7-30.5 0-27.67 19.5-47.17t47.17-19.5q16.66 0 30.66 7.17t23.34 20.17q14.33-9 29.66-15.84Q579.67-542 595.67-547q-17.34-30-47.67-48.17-30.33-18.16-67.33-18.16-55.67 0-94.5 39-38.84 39-38.84 94.33 0 36.67 17.84 67.17Q383-382.33 413-365Zm267 205 120-120-120-120-28 28 72 72H560v40h163l-71 72 28 28Zm0 80q-83 0-141.5-58.5T480-280q0-83 58.5-141.5T680-480q83 0 141.5 58.5T880-280q0 83-58.5 141.5T680-80Z"/></svg>
                            <span class="lan-6">Setup</span>
                        </a>
                        <ul class="sidebar-submenu">
                          <li><a href="javascript:;">Settings</a></li>
                          <li><a href="javascript:;">Masters</a></li>
                          <li><a href="javascript:;">Hospital Charges</a></li>
                          <li><a href="javascript:;">Bed</a></li>
                          <li><a href="javascript:;">Print Header Footer</a></li>
                          <li><a href="javascript:;">Front Office</a></li>
                          <li><a href="javascript:;">Pharmacy</a></li>
                          <li><a href="javascript:;">Pathology</a></li>
                          <li><a href="javascript:;">Radiology</a></li>
                          <li><a href="javascript:;">Symptoms</a></li>
                          <li><a href="javascript:;">Zoom Setting</a></li>
                          <li><a href="javascript:;">Finance</a></li>
                          <li><a href="javascript:;">Birth & Death Record</a></li>
                          <li><a href="javascript:;">Human Resource</a></li>
                          <li><a href="javascript:;">Inventory</a></li>
                        </ul>
                    </li>
                  </ul>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </nav>
        </div>
    </div>
<!-- Page Sidebar Ends-->