@extends('layouts.admin')
@section('title','Study Schedule Status')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Study Life Cycle
                    </h4>

                        <div class="form-group">
                            <label>Studies</label>
                            <select class="form-control select2 studiesView" name="studiesView" id="studiesView" data-placeholder="Select Studies" >
                                <option value="ALL">
                                    All
                                </option>
                                @if(!is_null($getStudies))
                                    @foreach($getStudies as $gsk => $gsv)
                                        <option value="{{ $gsv->id }}">
                                            {{ $gsv->study_no }}   
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item active">Study Life Cycle</li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.studyLifeCycleList')}}">
                                    Select Activity
                                </a>
                            </li>
                        </ol>
                    </div>    
                </div>
            </div>
        </div>

        <div class="row displayActivity">
            <div class="col-lg-12" style="border: 2px solid; overflow-x: scroll;">
                <div class="card card-stepper text-black" style="border-radius: 16px; width: 3000px; height: 500px;">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Studies</h5>
                            </div>
                            <div class="text-end">
                                <p class="mb-0">Expected Arrival <span>01/12/19</span></p>
                            </div>
                        </div>

                        <ul id="progressbar-2" class="d-flex">
                            @if(!is_null($studyLifeCycleTrain))
                                @foreach($studyLifeCycleTrain as $sltk => $sltv)
                                    <li class="step0 active text-center mt-5">
                                        <div class="pb-3" style="position: absolute; top: 40px; width: 100%; text-align: left;">
                                            @php
                                                $activityName = $sltv->activity_name;
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
        </div>

        <div class="row mt-4 displayStudyActivity" style="display: none;">

        </div>                 
    </div>
</div>

@endsection