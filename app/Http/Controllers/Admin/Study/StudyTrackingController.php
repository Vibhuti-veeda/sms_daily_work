<?php

namespace App\Http\Controllers\Admin\Study;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GlobalController;
use Illuminate\Http\Request;
use App\Models\StudySchedule;
use App\Models\Study;
use App\Models\ActivityMaster;
use App\Models\Admin;
use App\Models\LocationMaster;
use Carbon\Carbon;

class StudyTrackingController extends GlobalController
{
    public function __construct(){
        $this->middleware('admin');
        $this->middleware('checkpermission');
    }

    public function studyTrackingList(Request $request){

        $filter = 0;
        $projectManagerName = '';
        $brLocationName = '';
        $startDate = '';
        $endDate = '';

        // Fetch activity names
        $activityName = ActivityMaster::whereBetween('id', [4, 26])
                                       ->where('is_active', 1)
                                       ->where('is_delete', 0)
                                       ->get();

        // Fetch project managers and BR locations
        $projectManagers = Admin::whereIn('role_id', ['2', '3'])->where('is_active', 1)->where('is_delete', 0)->get();
        $brLocation = LocationMaster::where('location_type', 'BRSITE')->where('is_active', 1)->where('is_delete', 0)->get();

        // Default date range: current month's start date and end date
        $startDate = Carbon::now()->startOfMonth()->format('d-m-Y');
        $endDate = Carbon::now()->endOfMonth()->format('d-m-Y');

        // If custom date range is provided in request, update start and end dates
        if ($request->start_date != '' && $request->end_date != '') {
            $filter = 1;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        }

        // Query to fetch study data
        $query = Study::select('id', 'study_no', 'br_location', 'study_sub_type', 'cdisc_require','project_manager', 'sponsor', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects')->where('is_active', 1)->where('is_delete', 0);

        // Apply filters if provided
        if (isset($request->project_manager) && $request->project_manager != '') {
            $filter = 1;
            $projectManagerName = $request->project_manager;
            $query->where('project_manager', $projectManagerName);
        }

        if (isset($request->br_location) && $request->br_location != '') {
            $filter = 1;
            $brLocationName = $request->br_location;
            $query->where('br_location', $brLocationName);
        }

        // Fetch study data with related models
        $studyTracking = $query->with(['schedule' => function ($q) use ($startDate, $endDate) {
                                    $q->select('id', 'study_id', 'activity_id', 'activity_name', 'scheduled_start_date', 'actual_start_date', 'start_delay_remark', 'scheduled_end_date', 'actual_end_date', 'end_delay_remark', 'activity_type')
                                      ->where('activity_id', '>=', 4)
                                      ->where('activity_id', '<=', 26)
                                      ->where('is_active', 1)
                                      ->where('is_delete', 0);
                                    },
                                    'drugDetails' => function($q) {
                                        $q->select('id', 'study_id', 'drug_id', 'dosage_form_id', 'uom_id', 'dosage', 'type')->with([
                                            'drugName' => function($q){
                                                $q->select('id', 'drug_name');
                                            },
                                            'drugDosageName' => function($q){
                                                $q->select('id', 'para_value');
                                            },
                                            'drugUom' => function($q){
                                                $q->select('id', 'para_value');
                                            },
                                            'drugType' => function($q){
                                                $q->select('id', 'manufacturedby', 'type');
                                            },
                                        ]);
                                    },
                                    'studyRegulatory' =>function($q){
                                        $q->select('id', 'regulatory_submission', 'project_id')
                                          ->with([
                                            'paraSubmission' => function($q){
                                                $q->select('id', 'para_value');
                                            }
                                        ]);
                                    },
                                    'studyScope' =>function($q){
                                        $q->select('id', 'project_id', 'scope')
                                          ->with([
                                            'scopeName' => function($q){
                                                $q->select('id', 'para_value');
                                            }
                                        ]);
                                    },
                                    'brLocationName' =>function($q){
                                        $q->select('id', 'location_name')
                                        ->where('is_active', 1)
                                        ->where('is_delete', 0);
                                    },
                                    'studySubTypeName' => function($q) {
                                        $q->select('id', 'para_value', 'para_code');
                                    },
                                    'projectManager' => function($q){
                                        $q->select('id', 'name');
                                    },
                                    'sponsorName' => function($q){
                                        $q->select('id', 'sponsor_name');
                                    },
                                ])
                                ->whereHas('schedule', function ($q) use ($startDate, $endDate) {
                                    $q->where('activity_id', 4)
                                    ->where('is_active', 1)
                                    ->where('is_delete', 0)
                                    ->where('scheduled_start_date', '>=', Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d'))
                                    ->where('scheduled_start_date', '<=', Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d'));    
                                })
                                ->get();

        return view('admin.study.study_tracking.study_tracking_list', compact('activityName', 'studyTracking', 'projectManagers','projectManagerName', 'filter', 'brLocationName',
            'brLocation', 'startDate', 'endDate'));
    }
}
