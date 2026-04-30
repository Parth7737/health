
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-patient-category')
                <a class="nav-link {{ request()->routeIs('hospital.masters.patient-category.index') ? 'active' : '' }}" href="{{ route('hospital.masters.patient-category.index') }}">Patient Category</a>
            @endcan
            @can('view-religion')
                <a class="nav-link {{ request()->routeIs('hospital.masters.religion.index') ? 'active' : '' }}" href="{{ route('hospital.masters.religion.index') }}">Religion</a>
            @endcan
            @can('view-dietary')
                <a class="nav-link {{ request()->routeIs('hospital.masters.dietary.index') ? 'active' : '' }}" href="{{ route('hospital.masters.dietary.index') }}">Dietary</a>
            @endcan
            @can('view-allergy')
                <a class="nav-link {{ request()->routeIs('hospital.masters.allergy.index') ? 'active' : '' }}" href="{{ route('hospital.masters.allergy.index') }}">Allergies</a>
            @endcan
            @can('view-allergy-reaction')
                <!-- <a class="nav-link {{ request()->routeIs('hospital.masters.allergy-reaction.index') ? 'active' : '' }}" href="{{ route('hospital.masters.allergy-reaction.index') }}">Allergies Reactions</a> -->
            @endcan
            @can('view-habits')
                <a class="nav-link {{ request()->routeIs('hospital.masters.habits.index') ? 'active' : '' }}" href="{{ route('hospital.masters.habits.index') }}">Habits</a>
            @endcan
            @can('view-diseases')
                <a class="nav-link {{ request()->routeIs('hospital.masters.diseases.index') ? 'active' : '' }}" href="{{ route('hospital.masters.diseases.index') }}">Diseases</a>
            @endcan
            @can('view-disease-types')
                <a class="nav-link {{ request()->routeIs('hospital.masters.disease-type.index') ? 'active' : '' }}" href="{{ route('hospital.masters.disease-type.index') }}">Disease Types</a>
            @endcan
            @can('view-symptoms-type')
                <a class="nav-link {{ request()->routeIs('hospital.masters.symptoms.type.index') ? 'active' : '' }}" href="{{ route('hospital.masters.symptoms.type.index') }}">Symptoms Types</a>
            @endcan
            @can('view-symptoms')
                <a class="nav-link {{ request()->routeIs('hospital.masters.symptoms.symptoms-head.index') ? 'active' : '' }}" href="{{ route('hospital.masters.symptoms.symptoms-head.index') }}">Symptoms</a>
            @endcan
        </div>
    </div>
</div>