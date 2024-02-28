<?php

namespace App\Http\Controllers\Admin\Study;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudySchedule;
use App\Models\Study;
use App\Models\ActivityMaster;
use App\Models\Admin;
use App\Models\LocationMaster;

class StudyTrackingController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
        $this->middleware('checkpermission');
    }

    public function studyTrackingList(Request $request){

       /* $currentMonthStartDate = date('Y-m-01'); // First day of the current month
        $currentMonthEndDate = date('Y-m-t');    // Last day of the current month*/
        $filter = 0;
        $projectManagerName = '';
        $brLocationName = '';

        $projectManagers = Admin::whereIn('role_id', ['2', '3'])->where('is_active', 1)->where('is_delete', 0)->get();
        $brLocation = LocationMaster::where('location_type', 'BRSITE')->where('is_active', 1)->where('is_delete', 0)->get();

        $query = Study::select('id', 'study_no', 'br_location', 'study_sub_type', 'cdisc_require', 'project_manager', 'sponsor', 'no_of_subject', 'no_of_male_subjects', 'no_of_female_subjects')
                              ->where('is_active', 1)
                              ->where('is_delete', 0);

        if(isset($request->project_manager) && $request->project_manager != ''){
            $filter = 1;
            $projectManagerName = $request->project_manager;
            $query->where('project_manager',$projectManagerName);
        }

        if(isset($request->br_location) && $request->br_location != ''){
            $filter = 1;
            $brLocationName = $request->br_location;
            $query->where('br_location',$brLocationName);
        }

        $activityName = ActivityMaster::select('id', 'activity_name','is_active')
                                      ->where('id', '>=', 4)
                                      ->where('id', '<=', 26)
                                      ->where('is_active', 1)
                                      ->where('is_delete', 0)
                                      ->get();
                                      /*echo "<pre>";
                                        print_r($activityName->toArray());
                                        exit;*/ 


        $studyTracking = $query->with(['schedule' => function($q) {
                                    $q->select('id', 'study_id', 'activity_id', 'activity_name', 'scheduled_start_date', 'actual_start_date', 'start_delay_remark', 'scheduled_end_date', 'actual_end_date', 'end_delay_remark', 'activity_type')
                                      ->where('activity_id', '>=', 4)
                                      ->where('activity_id', '<=', 26)
                                      ->where('is_active', 1)
                                      ->where('is_delete', 0)
                                      ->where('scheduled_start_date', '>=', '2024-01-01')
                                      ->where('scheduled_start_date', '<=', '2024-01-31')
                                      ->where('actual_end_date', '!=' , NULL);
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
                                ->whereHas('schedule', function($q) {
                                    $q->where('activity_id', 26)
                                        //->where('activity_id', '<=', 26)
                                      ->where('is_active', 1)
                                      ->where('is_delete', 0)
                                      /*->where('scheduled_start_date', '>=', '2024-01-01')
                                      ->where('scheduled_start_date', '<=', '2024-01-31')*/
                                      ->where('actual_end_date', '!=' , NULL);
                                })
                                ->get();
                        /*echo "<pre>";
                        print_r($studyTracking->toArray());
                        exit;*/ 

        return view('admin.study.study_tracking.study_tracking_list', compact('activityName', 'studyTracking', 'projectManagers', 'projectManagerName', 'filter', 'brLocationName', 'brLocation'));
    }
}
