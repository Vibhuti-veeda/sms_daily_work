<?php

namespace App\Http\Controllers\Admin\Study;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParaCode;
use App\Models\ActivityMaster;
use App\Models\ActivityMasterTrail;
use App\Models\Study;
use App\Models\StudySchedule;

use Auth;



class StudyLifeCycleController extends Controller
{
    public function __construct(){
        $this->middleware('admin');
        $this->middleware('checkpermission');
    }

    // get Activity Name
    public function studyLifeCycleList(){

        $activitySchedule = ParaCode::where('para_code','=','ActivityType')
                                    ->orderByRaw("FIELD(id, 123, 113, 114, 116, 115)")
                                    ->where('is_active', 1)
                                    ->where('is_delete', 0)
                                    ->with([
                                        'activities' =>function($q){
                                            $q->where('is_active', 1)
                                            ->where('is_delete', 0);
                                        }
                                    ])
                                    ->get();

        return view('admin.study.study_life_cycle.study_life_cycle_list',compact('activitySchedule'));
    }

    // update studyLifeCycle
    public function updateStudyLifeCycle(Request $request){

        $activityMaster = ActivityMaster::where('id',$request->id)->update(['study_life_cycle' => $request->study_life_cycle]);

        $statusActivityMaster = ActivityMaster::where('id',$request->id)->first();

        $activityTrail = new ActivityMasterTrail;
        $activityTrail->activity_master_id = $statusActivityMaster->id;
        $activityTrail->activity_name = $statusActivityMaster->activity_name;
        $activityTrail->days_required = $statusActivityMaster->days_required;
        $activityTrail->minimum_days_allowed = $statusActivityMaster->minimum_days_allowed;
        $activityTrail->maximum_days_allowed = $statusActivityMaster->maximum_days_allowed;
        $activityTrail->activity_type = $statusActivityMaster->activity_type;
        $activityTrail->buffer_days = $statusActivityMaster->buffer_days;
        $activityTrail->responsibility = $statusActivityMaster->responsibility;
        $activityTrail->activity_days = $statusActivityMaster->activity_days;
        $activityTrail->next_activity = $statusActivityMaster->next_activity;
        $activityTrail->is_dependent = $statusActivityMaster->is_dependent;
        $activityTrail->previous_activity = $statusActivityMaster->previous_activity;
        $activityTrail->is_milestone = $statusActivityMaster->is_milestone;
        $activityTrail->milestone_percentage = $statusActivityMaster->milestone_percentage;
        $activityTrail->milestone_amount = $statusActivityMaster->milestone_amount;
        $activityTrail->parent_activity = $statusActivityMaster->parent_activity;
        $activityTrail->is_parellel = $statusActivityMaster->is_parellel;
        $activityTrail->is_group_specific = $statusActivityMaster->is_group_specific;
        $activityTrail->is_period_specific = $statusActivityMaster->is_period_specific;
        $activityTrail->sequence_no = $statusActivityMaster->sequence_no;
        $activityTrail->study_life_cycle = $statusActivityMaster->study_life_cycle;

        if (Auth::guard('admin')->user()->id != '') {
            $activityTrail->updated_by_user_id = Auth::guard('admin')->user()->id;
        }

        $activityTrail->save();

        return $activityMaster ? 'true' : 'false';
    }

    // tracking in display Activity Name
    public function studyLifeCycleTrain(Request $request){
        
        $studyLifeCycleTrain = ActivityMaster::where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->where('study_life_cycle', 1)
                                                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)")
                                                ->get();

        $studyLifeCycleIds = ActivityMaster::where('is_active', 1)->where('is_delete', 0)->where('study_life_cycle', 1)->pluck('id')->toArray();                    
        $getStudies = Study::select('id', 'study_no', 'study_status')
                            ->where('is_active', 1)
                            ->where('is_delete', 0)
                            ->where('study_status', 'ONGOING')
                            ->with([
                                'schedule' => function($q) use($studyLifeCycleIds){
                                    $q->where('is_active', 1)
                                    ->where('is_delete', 0)
                                    ->whereIn('activity_id', $studyLifeCycleIds)
                                    ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                                }
                            ])
                            ->whereHas('schedule', function($q) use($studyLifeCycleIds){
                                $q->whereIn('activity_id', $studyLifeCycleIds)
                                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                            })
                            ->get();

        

        return view('admin.study.study_life_cycle.study_life_cycle_train',compact('studyLifeCycleTrain', 'getStudies'));
    }

    public function changeStudyLifeCycleTrain(Request $request){ 

        $studyLifeCycleIds = ActivityMaster::where('is_active', 1)->where('is_delete', 0)->where('study_life_cycle', 1)->pluck('id')->toArray();

        if($request->id === 'ALL'){

            // $studyLifeCycleIds = ActivityMaster::where('is_active', 1)->where('is_delete', 0)->where('study_life_cycle', 1)->pluck('id')->toArray();

            $studyLifeCycleTrain = ActivityMaster::where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->where('study_life_cycle', 1)
                                                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)")
                                                ->get();

            $getStudies = Study::select('id', 'study_no', 'study_status')
                            ->where('is_active', 1)
                            ->where('is_delete', 0)
                            ->where('study_status', 'ONGOING')
                            ->with([
                                'schedule' => function($q) use($studyLifeCycleIds){
                                    $q->where('is_active', 1)
                                    ->where('is_delete', 0)
                                    ->whereIn('activity_id', $studyLifeCycleIds)
                                    ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                                }
                            ])
                            ->whereHas('schedule', function($q) use($studyLifeCycleIds){
                                $q->whereIn('activity_id', $studyLifeCycleIds)
                                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                            })
                            ->get();

            return view('admin.study.study_life_cycle.study_life_cycle_train',compact('studyLifeCycleTrain', 'getStudies'));
        } else {
            // $studyLifeCycleIds = ActivityMaster::where('is_active', 1)->where('is_delete', 0)->where('study_life_cycle', 1)->pluck('id')->toArray();

            $getActivity = StudySchedule::where('study_id', $request->id)
                                        ->where('is_active', 1)
                                        ->where('is_delete', 0)
                                        ->whereIn('activity_id', $studyLifeCycleIds)
                                        ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)")
                                        ->get();

            /*$getActivity = Study::select('id', 'study_no', 'study_status')
                                ->where('is_active', 1)
                                ->where('is_delete', 0)
                                ->where('id', $request->id)
                                ->where('study_status', 'ONGOING')
                                ->with([
                                    'schedule' => function($q) use($studyLifeCycleIds){
                                        $q->where('is_active', 1)
                                        ->where('is_delete', 0)
                                        
                                        ->whereIn('activity_id', $studyLifeCycleIds)
                                        ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                                    }
                                ])
                                ->whereHas('schedule', function($q) use($studyLifeCycleIds){
                                    $q->whereIn('activity_id', $studyLifeCycleIds)
                                    ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                                })
                                ->get();*/

            $html = view('admin.study.study_life_cycle.change_study_life_cycle_train',compact('getActivity'))->render();
        
            return response()->json(['html'=>$html]);
        }
    }
}
