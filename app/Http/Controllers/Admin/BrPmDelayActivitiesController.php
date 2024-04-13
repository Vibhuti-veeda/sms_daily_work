<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Study;
use App\Models\StudySchedule;
use App\Models\EmailNotification;

class BrPmDelayActivitiesController extends Controller
{
    //

    public function delayMailList(Request $request){
        
        // get role_id 16 user
        $getUser = Admin::select('id', 'name', 'role_id', 'email', 'is_active', 'is_delete')
                        ->where('role_id', 16)
                        ->where('is_active', 1)
                        ->where('is_delete', 0)
                        ->get();

        if(!is_null($getUser)){
            foreach($getUser as $guk => $guv){

                $getStudies = Study::where('is_active', 1)
                                    ->where('is_delete', 0)
                                    ->where('project_manager', $guv->id)
                                    ->where('created_by', 'BO')
                                    ->whereIn('study_status', ['ONGOING', 'UPCOMING'])
                                    ->pluck('id')
                                    ->toArray();

                $delayActivity = StudySchedule::where('is_active', 1)
                                                ->where('is_delete', 0)
                                                ->whereIn('study_id', $getStudies)
                                                ->with([
                                                    'studyNo' => function($q) {
                                                        $q->select('id', 'study_no')
                                                        ->where('is_active', 1)
                                                        ->where('is_delete', 0);
                                                    },
                                                ])
                                                ->where('activity_status', 'DELAY')
                                                ->orderBy('scheduled_start_date', 'ASC')
                                                ->get();

                $subject = 'Study Management System - Delay Activities';
                $name = $guv->name;
                $toEmail = $guv->email;
                $bccEmail = ['chandresh.v2590@veedacr.com', 'sani.c2654@veedacr.com'];

                $html = view('admin.mail.br_pm_delay_activities_list',compact('delayActivity', 'name'))->render();

                $email = new EmailNotification;
                $email->system = 'sms';
                $email->email_type = 'notification';
                $email->from_email = 'sms@veedacr.com';
                $email->to_email = $toEmail;
                $email->bcc = implode(',', $bccEmail);
                $email->subject = $subject;
                $email->body = $html;
                $email->flag = 'PENDING';
                $email->send_time = now();
                $email->save();

            }
                return redirect(route('admin.dashboard'))->with('messages', [
                    [
                        'type' => 'success',
                        'title' => 'Email',
                        'message' => 'Email successfully inserted!',
                    ],
                ]); 
        }
    }
}
