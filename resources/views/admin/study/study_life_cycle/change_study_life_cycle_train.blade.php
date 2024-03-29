
<div class="col-lg-12" style="border: 2px solid; overflow-x: scroll;">
    <div class="card card-stepper text-black" style="border-radius: 16px; width: 4000px; height: 270px;">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-3">{{ $projectManagerName }} ({{ $firstActivityDate ? date('d M Y', strtotime($firstActivityDate)) : 'N/A' }} to {{ $lastActivityDate ? date('d M Y', strtotime($lastActivityDate)) : 'N/A' }})</h5>
                </div>
            </div>

            <ul id="progressbar-2" class="d-flex">
                @if(!is_null($getActivity))
                    @foreach($getActivity as $gak => $gav)
                        <li class="step0 text-center mt-5 {{ ($gav->actual_end_date != '') ? 'active' : ''  }}">
                            <div class="ps-2 mb-5 pb-5" style="position: relative; top: -115px; width: 305px; text-align: left;"> 
                                <p class="text-start fw-bold date" style="transform: skew(7deg, -22deg);">{{($gav->actual_end_date != '') ? date('d M Y', strtotime($gav->actual_end_date)) : (($gav->scheduled_end_date != '') ? date('d M Y', strtotime($gav->scheduled_end_date)) : '') }}</p>
                            </div>
                            <div class="pb-3" style="position: relative; top: -129px; width: 100%; text-align: left;">
                                @php
                                    $activityName = $gav->activity_name;
                                    $activityName = wordwrap($activityName, 15, "\n", true);
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