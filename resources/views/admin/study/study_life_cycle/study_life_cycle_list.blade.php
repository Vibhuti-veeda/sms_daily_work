@extends('layouts.admin')
@section('title','Study Life Cycle')
@section('content')

<div class="page-content">
    <div class="container-fluid">

       <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Study Life Cycle
                    </h4>

                   <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard')}}">
                                    Dashboard 
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Select Study</li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>
        @if(!is_null($activitySchedule))
            @foreach($activitySchedule as $ask => $asv)
                <div class="accordion mb-3" id="accordionExample">
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading{{$ask}}">
                            <button class="accordion-button fw-medium collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$ask}}" aria-expanded="false" aria-controls="collapse{{$ask}}">
                                {{$asv->para_value}} Activities
                            </button>
                        </h2>    
                        <div id="collapse{{$ask}}" class="accordion-collapse collapse" aria-labelledby="heading{{$ask}}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <form id="studyScheduleMonitoringForm{{$ask}}" action="#" method="post">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="form-group mb-3">
                                                        <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; overflow: auto; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>Sr. No</th>
                                                                    <th>Select</th>
                                                                    <th>Activity Name</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php $srNo = 1; @endphp
                                                                @if(!is_null($asv->activities))
                                                                    @foreach($asv->activities as $ack => $acv)
                                                                        <tr>
                                                                            <td>{{ $srNo++ }}</td>
                                                                            <td> 
                                                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                                                    <input class="form-check-input studyLifeCycleStatus" type="checkbox" id="customSwitch{{ $ack }}" value="{{ ($acv->study_life_cycle)}}" @if($acv->study_life_cycle == 1) checked @endif  data-id="{{ $acv->id }}" >
                                                                                    <label class="form-check-label" for="customSwitch{{ $ack }}"></label>
                                                                                </div>
                                                                            </td>
                                                                            <td>{{ ($acv->activity_name != '') ? $acv->activity_name : '' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <br>
        @endif
                       
    </div>
</div>

@endsection

