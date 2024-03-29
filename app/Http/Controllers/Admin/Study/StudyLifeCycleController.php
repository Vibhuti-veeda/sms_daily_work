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
                                ->whereNotNull('scheduled_end_date')
                                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)");
                            })
                            ->get();

        

        return view('admin.study.study_life_cycle.study_life_cycle_train',compact('studyLifeCycleTrain', 'getStudies'));
    }

    public function changeStudyLifeCycleTrain(Request $request){ 
        
        if($request->id === 'ALL'){
            return redirect()->route('admin.studyLifeCycleTrain');
        } else {

            // Retrieve project manager's information from the study
            $study = Study::with('projectManager')->findOrFail($request->id); // Assuming 'Study' is your model for the studies table

            // Access project manager's name through the relationship
            $projectManagerName = $study->projectManager->name;

            $studyLifeCycleIds = ActivityMaster::where('is_active', 1)->where('is_delete', 0)->where('study_life_cycle', 1)->pluck('id')->toArray();

            // Retrieve activities with activity_id 2 and 3
            $activityAsc = StudySchedule::where('study_id', $request->id)
                ->where('is_active', 1)
                ->where('is_delete', 0)
                ->whereNotNull('scheduled_end_date')
                ->whereIn('activity_id', [2, 3])
                ->orderBy('period_no', 'asc')
                ->get();

            // Retrieve activities with activity_id not in [2, 3]
            $otherActivities = StudySchedule::where('study_id', $request->id)
                ->where('is_active', 1)
                ->where('is_delete', 0)
                ->whereNotNull('scheduled_end_date')
                ->whereIn('activity_id', $studyLifeCycleIds)
                ->whereNotIn('activity_id', [2, 3])
                ->orderByRaw("FIELD(activity_type, 123, 113, 114, 116, 115)")
                ->get();

            $getActivity = $activityAsc->merge($otherActivities);

            $firstActivityDate = $getActivity->first()->actual_end_date ?? $getActivity->first()->scheduled_end_date ?? null;
            $lastActivityDate = $getActivity->last()->actual_end_date ?? $getActivity->last()->scheduled_end_date ?? null;


           // Calculate total width required based on the number of activities
            $totalWidth = 400 * count($getActivity); // Assuming each activity takes 400px width

            // Set a minimum width for the card
            $minWidth = 1770; // Adjust as needed

            // Determine the final width of the card (maximum of calculated width and minimum width)
            $finalWidth = max($totalWidth, $minWidth);

            // Construct HTML with dynamic width and minimum width
            $html = '<div class="col-lg-12" style="border: 2px solid; overflow-x: scroll;">
                        <div class="card card-stepper text-black" style="border-radius: 16px; min-width: '.$minWidth.'px; width: '.$finalWidth.'px; height: 290px;">
                            <div class="card-body p-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="mb-3">' . $projectManagerName . ' (' . ($firstActivityDate ? date('d M Y', strtotime($firstActivityDate)) : 'N/A') . ' to ' . ($lastActivityDate ? date('d M Y', strtotime($lastActivityDate)) : 'N/A') . ')</h5>
                                    </div>
                                </div>
                                <ul id="progressbar-2" class="d-flex">';

            if(!is_null($getActivity)) {
                foreach($getActivity as $gak => $gav) {
                    $html .= '<li class="step0 text-center mt-5 ' . ($gav->actual_end_date != '' ? 'active' : '') . '">
                                <div class="ps-2 mb-5 pb-5" style="position: relative; top: -115px; width: 305px; text-align: left;"> 
                                    <p class="text-start fw-bold date" style="transform: skew(7deg, -22deg);">' . ($gav->actual_end_date != '' ? date('d M Y', strtotime($gav->actual_end_date)) : ($gav->scheduled_end_date != '' ? date('d M Y', strtotime($gav->scheduled_end_date)) : '')) . '</p>
                                </div>
                                <div class="pb-3" style="position: relative; top: -129px; width: 100%; text-align: left;">';

                    $activityName = $gav->activity_name;
                    $activityName = wordwrap($activityName, 15, "\n", true);
                    $html .= '<p class="fw-bold activityName">' . nl2br($activityName) . '</p>
                            </div>
                        </li>';
                }
            }

            $html .= '</ul>
                    </div>
                </div>
            </div>';
            
            return response()->json(['html'=>$html]);
        }
    }
}
