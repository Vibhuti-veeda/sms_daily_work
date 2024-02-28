@extends('layouts.admin')
@section('title','Study Tracking')
@section('content')

<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">All Study Tracking </h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                All Study Tracking List 
                            </li>
                        </ol>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="accordion" id="accordionExample">
            
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#studyCollapseFilter" aria-expanded="true" aria-controls="studyCollapseFilter">
                        Filters
                    </button>
                </h2>
                <div id="studyCollapseFilter" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <form method="post" action="{{ route('admin.studyTrackingList') }}">
                            @csrf

                            <div class="row">

                                <div class="col-md-3">
                                    <label class="control-label">Project Managers</label>
                                    <select class="form-control select2" name="project_manager" style="width: 100%;">
                                        <option value="">Select Project Managers</option>
                                        @if(!is_null($projectManagers))
                                            @foreach($projectManagers as $pk => $pv)
                                                <option @if($projectManagerName == $pv->id) selected @endif value="{{ $pv->id }}">
                                                    {{ $pv->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="control-label">BR Location</label>
                                    <select class="form-control select2" name="br_location" style="width: 100%;">
                                        <option value="">Select BR Location</option>
                                        @if(!is_null($brLocation))
                                            @foreach($brLocation as $bk => $bv)
                                                <option @if($brLocationName == $bv->id) selected @endif value="{{ $bv->id }}">
                                                    {{ $bv->location_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                

                                <div class="col-md-1 pt-1">
                                    <button type="submit" class="btn btn-primary vendors save_button mt-4">Submit</button>
                                </div>
                                @if(isset($filter) && ($filter == 1))
                                    <div class="col-md-1 pt-1">
                                        <a href="{{ route('admin.studyTrackingList') }}" class="btn btn-danger mt-4 cancel_button" id="filter" name="save_and_list" value="save_and_list" style="margin-left:-10px !important;">
                                            Reset
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable-buttons" class="table table-striped table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Study Number</th>
                                        <th>Drug</th>
                                        <th>Scope</th>
                                        <th>Regulatory submission (USFDA/ EU/ TGA/ Canada etc)</th>
                                        <th>BA Site</th>
                                        <th>Pilot /Pivotal</th>
                                        <th>CDISC  Requirement (Yes/ No)</th>
                                        <th>PM</th>
                                        <th>Sponsor</th>
                                        <th>No. Of Subject</th>
                                        <th>Male</th>
                                        <th>Female</th>
                                        @if(!is_null($activityName))
                                            @foreach($activityName as $ank => $anv)
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (SSD) </th>
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (ASD) </th>
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (SDR) </th>
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (SED) </th>
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (AED) </th>
                                                <th>{{ ($anv->activity_name != '') ? $anv->activity_name : '---' }} (EDR) </th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @php $srNo = 1; @endphp
                                    @if(!is_null($studyTracking))
                                        @foreach($studyTracking as $stk => $stv)
                                            <tr>
                                                <td>{{ $srNo++ }}</td>
                                                <td>{{ ($stv->study_no != '') ? $stv->study_no : '---' }}</td>
                                                <td>
                                                    @if(!is_null($stv->drugDetails)) 
                                                    @php $drug = ''; @endphp
                                                    @foreach($stv->drugDetails as $dk => $dv)
                                                        @if((!is_null($dv->drugName)) && !is_null($dv->drugDosageName) && ($dv->dosage != '') && !is_null($dv->drugUom) && !is_null($dv->drugType) && ($dv->drugType->type == 'TEST'))
                                                            @php 
                                                                $drug = $dv->drugName->drug_name.' - '.$dv->drugDosageName->para_value .' - '.$dv->dosage .''.$dv->drugUom->para_value;
                                                            @endphp
                                                        @endif    
                                                        
                                                    @endforeach
                                                    <p>{{ $drug != '' ? $drug : '---' }}</p>
                                                @endif
                                                </td>

                                                <td>
                                                    @if(!is_null($stv->studyScope)) 
                                                        @php $scope = array(); @endphp
                                                        @foreach($stv->studyScope as $ssk => $ssv)
                                                            @if((!is_null($ssv->scopeName)) && ($ssv->scopeName->para_value != ''))
                                                                @php 
                                                                    $scope[] = $ssv->scopeName->para_value;
                                                                @endphp
                                                            @endif    
                                                            
                                                        @endforeach
                                                        <p>{{ implode(' | ', $scope) }}</p>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if(!is_null($stv->studyRegulatory)) 
                                                        @php $submission = array(); @endphp
                                                        @foreach($stv->studyRegulatory as $srk => $srv)
                                                            @if((!is_null($srv->paraSubmission)) && ($srv->paraSubmission->para_value != ''))
                                                                @php 
                                                                    $submission[] = $srv->paraSubmission->para_value;
                                                                @endphp
                                                            @endif    
                                                            
                                                        @endforeach
                                                        <p>{{ implode(' | ', $submission) }}</p>
                                                    @endif
                                                </td>

                                                <td>
                                                    {{ ((!is_null($stv->brLocationName)) && ($stv->brLocationName->location_name != '') ) ? $stv->brLocationName->location_name : '---' }}
                                                </td>

                                                <td>
                                                    {{ ((!is_null($stv->studySubTypeName)) && ($stv->studySubTypeName->para_value != '') ) ? $stv->studySubTypeName->para_value : '---' }}
                                                </td>

                                                <td>
                                                    {{ $stv->cdisc_require == 1 ? 'Yes' : 'No' }}
                                                </td>

                                                <td>
                                                    {{ ((!is_null($stv->projectManager)) && ($stv->projectManager->name != '') ) ? $stv->projectManager->name : '---' }}
                                                </td>

                                                <td>
                                                    {{ ((!is_null($stv->sponsorName)) && ($stv->sponsorName->sponsor_name != '') ) ? $stv->sponsorName->sponsor_name : '---' }}
                                                </td>

                                                <td>
                                                    {{ ($stv->no_of_subject != '') ? $stv->no_of_subject : '---' }}
                                                </td>

                                                <td>
                                                    {{ ($stv->no_of_male_subjects != '') ? $stv->no_of_male_subjects : '---' }}
                                                </td>

                                                <td>
                                                    {{ ($stv->no_of_female_subjects != '') ? $stv->no_of_female_subjects : '---' }}
                                                </td>
                                                @if(!is_null($activityName))
                                                    @foreach($activityName as $ank => $anv)
                                                        @php $activityFound = false; @endphp
                                                        @if(!is_null($stv->schedule))
                                                            @foreach($stv->schedule as $sck => $scv)
                                                                @if($scv->activity_id == $anv->id)
                                                                    @php $activityFound = true; @endphp
                                                                    <td>
                                                                        {{ ($scv->scheduled_start_date != '') ? date('d M Y', strtotime($scv->scheduled_start_date)) : '---' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ ($scv->actual_start_date != '') ? date('d M Y', strtotime($scv->actual_start_date)) : '---' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ ($scv->start_delay_remark != '') ? $scv->start_delay_remark : '---' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ ($scv->scheduled_end_date != '') ? date('d M Y', strtotime($scv->scheduled_end_date)) : '---' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ ($scv->actual_end_date != '') ? date('d M Y', strtotime($scv->actual_end_date)) : '---' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ ($scv->end_delay_remark != '') ? $scv->end_delay_remark : '---' }}
                                                                    </td>
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if(!$activityFound)
                                                            <td>---</td>
                                                            <td>---</td>
                                                            <td>---</td>
                                                            <td>---</td>
                                                            <td>---</td>
                                                            <td>---</td>
                                                        @endif
                                                    @endforeach
                                                @endif
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
    </div>
</div>

@endsection

