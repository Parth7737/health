<div class="table-responsive mt-5 text-nowrap">
    <h5>Dashboard</h5>
    <div>{{App\CentralLogics\Helpers::checkmonthaudit($hospital->id)}}</div>
    <table class="table table-bordered">
        <thead class="">
            <tr class="text-center">
               <th rowspan="2">Chapters</th>
               <th rowspan="2">No. of Standards</th>
               <th colspan="4">No. of Means of Verifications</th>
            </tr>
            <tr class="text-center">
                <th>Total</th>
                <th>Compliant</th>
                <th>Non-Compliant</th>
                <th>Compliant%</th>
            </tr>
        </thead>
        <tbody>
            @php
                $standard=$audit=$compliance=$ncompliance=0;
            @endphp
            @foreach($auditcategory as $key => $value)
                @php
                    $s = $value->auditSubCategories()->count();
                    $aut = $value->auditlist()->count();
                    $comp = App\CentralLogics\Helpers::complianceCount($hospital->id, $value->id);
                    $ncomp = App\CentralLogics\Helpers::noncomplianceCount($hospital->id, $value->id);
                    $standard += $s;
                    $audit += $aut;
                    $compliance += $comp;
                    $ncompliance += $ncomp;
                @endphp
                <tr class="text-center">
                    <td>{{$value->name}}</td>
                    <td class="standard">{{$s}}</td>
                    <td>{{$aut}}</td>
                    <td>{{ $comp }}</td>
                    <td>{{ $ncomp }}</td>
                    <td>{{ $aut > 0 ? round(($comp / $aut) * 100, 2) : 0 }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="text-center ">
                <td class="text-danger" style="font-weight:bold;">Total</td>
                <td class="text-danger" style="font-weight:bold;">{{ $standard }}</td>
                <td class="text-danger" style="font-weight:bold;">{{ $audit }}</td>
                <td class="text-danger" style="font-weight:bold;">{{ $compliance }}</td>
                <td class="text-danger" style="font-weight:bold;">{{ $ncompliance }}</td>
                <td class="text-danger" style="font-weight:bold;">{{ $audit > 0 ? round(($compliance / $audit) * 100, 2) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>
</div>