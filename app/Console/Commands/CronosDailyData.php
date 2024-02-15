<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Study;
use App\Models\StudySchedule;
use App\Models\CronosData;
use GuzzleHttp\Client;

class CronosDailyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:checkCronosData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch & check if studies new data found then every 5 minute insert into Cronos data table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cronos = new Client([ 'verify' => false]);
        $request = $cronos->get('https://itdev/cronos_data_api/index.php/api/cronos-data-list');
        $data = json_decode($request->getBody()->getContents(),true);
        $cronosData = $data['data'];

        if(!is_null($cronosData)){
            CronosData::truncate();
            foreach ($cronosData as $cdk => $cdv) {

                $string = $cdv['Project_Site_Name'];
                $positionOfG = strpos($string, 'G');

                if ($positionOfG !== false) {
                    $numberAfterG = preg_replace("/[^1-9]/", "", substr($string, $positionOfG + 1));

                    if ($numberAfterG !== "") {

                        $data = new CronosData;
                        $data->study_no = $cdv['Project_No'].'-G'.$numberAfterG;
                        $data->period = $cdv['iperiod'];
                        $data->check_in_start = $cdv['Check_In_Start'] ? date('Y-m-d H:i', strtotime($cdv['Check_In_Start'])) : Null;
                        $data->check_in_end = $cdv['Check_In_End'] ? date('Y-m-d H:i', strtotime($cdv['Check_In_End'])) : Null;
                        $data->check_in_subject = $cdv['No_Of_Subject'];
                        $data->dosing_start = $cdv['Dosing_Start_Date'] ? date('Y-m-d H:i', strtotime($cdv['Dosing_Start_Date'])) : Null;
                        $data->dosing_end = $cdv['Dosing_End_Date'] ? date('Y-m-d H:i', strtotime($cdv['Dosing_End_Date'])) : Null;
                        $data->dosing_subject = $cdv['No_of_Dosed_Subject'];
                        $data->last_sample_start = $cdv['Subject_Sample_Collection_Start_Date'] ? date('Y-m-d H:i', strtotime($cdv['Subject_Sample_Collection_Start_Date'])) : Null;
                        $data->last_sample_end = $cdv['Subject_Sample_Collection_End_Date'] ? date('Y-m-d H:i', strtotime($cdv['Subject_Sample_Collection_End_Date'])) : Null;
                        $data->is_paper_based_study = $cdv['Is_Paper_Based_Study'];
                        $data->save();

                        $getStudy = Study::where('study_no', $cdv['Project_No'].'-G'.$numberAfterG)->first();

                        if($cdv['Check_In_Start'] != ''){
                            $updateStartCheckIn = StudySchedule::where('study_id', $getStudy->id)
                                                                ->where('period_no', $cdv['iperiod'])
                                                                ->where('activity_id', 2)
                                                                ->update([
                                                                    'actual_start_date' => date('Y-m-d', strtotime($cdv['Check_In_Start'])),
                                                                    'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->check_in_start)),
                                                                ]);
                        }

                        if($cdv['Check_In_End'] != ''){
                            $updateEndCheckIn = StudySchedule::where('study_id', $getStudy->id)
                                                            ->where('period_no', $cdv['iperiod'])
                                                            ->where('activity_id', 2)
                                                            ->update([
                                                                'actual_end_date' => date('Y-m-d', strtotime($cdv['Check_In_End'])),
                                                                'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->check_in_end)),
                                                            ]);
                        }

                        if($cdv['Dosing_Start_Date'] != ''){
                            $updateStartDosing = StudySchedule::where('study_id', $getStudy->id)
                                                                ->where('period_no', $cdv['iperiod'])
                                                                ->where('activity_id', 3)
                                                                ->update([
                                                                    'actual_start_date' => date('Y-m-d', strtotime($cdv['Dosing_Start_Date'])),
                                                                    'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->dosing_start)),
                                                                ]);
                        }

                        if($cdv['Dosing_End_Date'] != ''){
                            $updateEndDosing = StudySchedule::where('study_id', $getStudy->id)
                                                            ->where('period_no', $cdv['iperiod'])
                                                            ->where('activity_id', 3)
                                                            ->update([
                                                                'actual_end_date' => date('Y-m-d', strtotime($cdv['Dosing_End_Date'])),
                                                                'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->dosing_end)),
                                                            ]);
                        }

                        if($cdv['Subject_Sample_Collection_Start_Date'] != ''){
                            $updateStartLastSample = StudySchedule::where('study_id', $getStudy->id)
                                                                    ->where('period_no', $cdv['iperiod'])
                                                                    ->where('activity_id', 4)
                                                                    ->update([
                                                                        'actual_start_date' => date('Y-m-d', strtotime($cdv['Subject_Sample_Collection_Start_Date'])),
                                                                        'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->last_sample_start)),
                                                                    ]);
                        }

                        if($cdv['Subject_Sample_Collection_End_Date'] != ''){
                            $updateEndLastSample = StudySchedule::where('study_id', $getStudy->id)
                                                                ->where('period_no', $cdv['iperiod'])
                                                                ->where('activity_id', 4)
                                                                ->update([
                                                                    'actual_end_date' => date('Y-m-d', strtotime($cdv['Subject_Sample_Collection_End_Date'])),
                                                                    'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->last_sample_end)),
                                                                ]);
                        }
                    }
                } else {

                    $data = new CronosData;
                    $data->study_no = $cdv['Project_Site_Name'];
                    $data->period = $cdv['iperiod'];
                    $data->check_in_start = $cdv['Check_In_Start'] ? date('Y-m-d H:i', strtotime($cdv['Check_In_Start'])) : Null;
                    $data->check_in_end = $cdv['Check_In_End'] ? date('Y-m-d H:i', strtotime($cdv['Check_In_End'])) : Null;
                    $data->check_in_subject = $cdv['No_Of_Subject'];
                    $data->dosing_start = $cdv['Dosing_Start_Date'] ? date('Y-m-d H:i', strtotime($cdv['Dosing_Start_Date'])) : Null;
                    $data->dosing_end = $cdv['Dosing_End_Date'] ? date('Y-m-d H:i', strtotime($cdv['Dosing_End_Date'])) : Null;
                    $data->dosing_subject = $cdv['No_of_Dosed_Subject'];
                    $data->last_sample_start = $cdv['Subject_Sample_Collection_Start_Date'] ? date('Y-m-d H:i', strtotime($cdv['Subject_Sample_Collection_Start_Date'])) : Null;
                    $data->last_sample_end = $cdv['Subject_Sample_Collection_End_Date'] ? date('Y-m-d H:i', strtotime($cdv['Subject_Sample_Collection_End_Date'])) : Null;
                    $data->is_paper_based_study = $cdv['Is_Paper_Based_Study'];
                    $data->save();

                    $getStudy = Study::where('study_no', $cdv['Project_Site_Name'])->first();

                    if($cdv['Check_In_Start'] != ''){
                        $updateStartCheckIn = StudySchedule::where('study_id', $getStudy->id)
                                                            ->where('period_no', $cdv['iperiod'])
                                                            ->where('activity_id', 2)
                                                            ->update([
                                                                'actual_start_date' => date('Y-m-d', strtotime($cdv['Check_In_Start'])),
                                                                'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->check_in_start)),
                                                            ]);
                    }

                    if($cdv['Check_In_End'] != ''){
                        $updateEndCheckIn = StudySchedule::where('study_id', $getStudy->id)
                                                        ->where('period_no', $cdv['iperiod'])
                                                        ->where('activity_id', 2)
                                                        ->update([
                                                            'actual_end_date' => date('Y-m-d', strtotime($cdv['Check_In_End'])),
                                                            'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->check_in_end)),
                                                        ]);
                    }

                    if($cdv['Dosing_Start_Date'] != ''){
                        $updateStartDosing = StudySchedule::where('study_id', $getStudy->id)
                                                            ->where('period_no', $cdv['iperiod'])
                                                            ->where('activity_id', 3)
                                                            ->update([
                                                                'actual_start_date' => date('Y-m-d', strtotime($cdv['Dosing_Start_Date'])),
                                                                'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->dosing_start)),
                                                            ]);
                    }

                    if($cdv['Dosing_End_Date'] != ''){
                        $updateEndDosing = StudySchedule::where('study_id', $getStudy->id)
                                                        ->where('period_no', $cdv['iperiod'])
                                                        ->where('activity_id', 3)
                                                        ->update([
                                                            'actual_end_date' => date('Y-m-d', strtotime($cdv['Dosing_End_Date'])),
                                                            'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->dosing_end)),
                                                        ]);
                    }

                    if($cdv['Subject_Sample_Collection_Start_Date'] != ''){
                        $updateStartLastSample = StudySchedule::where('study_id', $getStudy->id)
                                                                ->where('period_no', $cdv['iperiod'])
                                                                ->where('activity_id', 4)
                                                                ->update([
                                                                    'actual_start_date' => date('Y-m-d', strtotime($cdv['Subject_Sample_Collection_Start_Date'])),
                                                                    'actual_start_date_time' => date('Y-m-d H:i:s', strtotime($data->last_sample_start)),
                                                                ]);
                    }

                    if($cdv['Subject_Sample_Collection_End_Date'] != ''){
                        $updateEndLastSample = StudySchedule::where('study_id', $getStudy->id)
                                                            ->where('period_no', $cdv['iperiod'])
                                                            ->where('activity_id', 4)
                                                            ->update([
                                                                'actual_end_date' => date('Y-m-d', strtotime($cdv['Subject_Sample_Collection_End_Date'])),
                                                                'actual_end_date_time' => date('Y-m-d H:i:s', strtotime($data->last_sample_end)),
                                                            ]);
                    }
                }
                
            }
        }

    }
}
