<div class="col-lg-12" style="border: 2px solid; overflow-x: scroll;">
    <div class="card card-stepper text-black" style="border-radius: 16px; width: 3000px; height: 500px;">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h5 class="mb-3">Project Manager: {{ $projectManagerName }} ({{ $firstActivityDate ? date('d M Y', strtotime($firstActivityDate)) : 'N/A' }} to {{ $lastActivityDate ? date('d M Y', strtotime($lastActivityDate)) : 'N/A' }})</h5>
                </div>
            </div>

            <ul id="progressbar-2" class="d-flex">
                @if(!is_null($getActivity))
                    @foreach($getActivity as $gak => $gav)
                        <li class="step0 text-center mt-5 {{ ($gav->actual_end_date != '') ? 'active' : ''  }}">
                            <div class="ps-2 mb-5" style="position: absolute; top: -80px; width: 100%; text-align: left;"> 
                                <p class="text-start fw-bold date" style="transform: skew(7deg, -7deg);">{{($gav->actual_end_date != '') ? date('d M Y', strtotime($gav->actual_end_date)) : (($gav->scheduled_end_date != '') ? date('d M Y', strtotime($gav->scheduled_end_date)) : '') }}</p>
                            </div>
                            <div class="pb-3" style="position: absolute; top: 40px; width: 100%; text-align: left;">
                                @php
                                    $activityName = $gav->activity_name;
                                    $activityName = wordwrap($activityName, 5, "\n", true);
                                @endphp

                                <p class="fw-bold activityName">{!! nl2br($activityName) !!}</p> 
                            </div>

                        </li>
                    @endforeach
                @endif  
            </ul>
        </div>
    </div>
</div>