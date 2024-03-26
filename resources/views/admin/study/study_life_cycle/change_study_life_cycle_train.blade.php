<div class="col-lg-12" style="border: 2px solid; overflow-x: scroll;">
    <div class="card card-stepper text-black" style="border-radius: 16px; width: 8000px;">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h5 class="mb-0">Studies</h5>
                </div>
                <div class="text-end">
                    <p class="mb-0">Expected Arrival <span>01/12/19</span></p>
                </div>
            </div>

            <ul id="progressbar-2" class="d-flex">
                @if(!is_null($getActivity))
                    @foreach($getActivity as $gak => $gav)
                        <li class="step0 active text-center">
                            <div class="pb-3" style="position: absolute; top: 40px; width: 100%; text-align: left;">
                                @php
                                    $activityName = $gav->activity_name;
                                    $activityName = wordwrap($activityName, 10, "\n", true);
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