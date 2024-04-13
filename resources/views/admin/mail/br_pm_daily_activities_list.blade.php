<div>
    
    <div class="form-group">
        <p>Hello <?php echo $name; ?>,</p>
    </div>

    <div class="form-group">
        <p style="color: blue;">
            <b>
                Below is the list of activities which are planned for today, kindly take necessary action.
            </b>
        </p>
    </div>

    @if(count($dailyActivity)>0)
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: blue; width: 100%; column-span:4;">
                    <b>
                        Post Study
                    </b>
                </th>
            </tr>

            <tr style="border: 1px solid;">
                <center>
                    <th style="border: 1px solid;">Sr.No</th>
                    <th style="border: 1px solid;">Study No</th>
                    <th style="border: 1px solid;">Activity Name</th>
                    <th style="border: 1px solid;">Schedule Start Date</th>
                </center>
            </tr>
        
            @if(!is_null($dailyActivity))
                @foreach($dailyActivity as $dk => $dv)
                    <center>
                        <tr style="border: 1px solid;">
                            <td style="border: 1px solid;"><?php echo $loop->iteration; ?></td>
                            <td style="border: 1px solid;"><?php echo $dv->studyNo['study_no']; ?></td>
                            <td style="border: 1px solid;"><?php echo $dv['activity_name']; ?></td>
                            <td style="border: 1px solid;"><?php echo date('d M Y', strtotime($dv['scheduled_start_date'])); ?></td>
                        </tr>
                    </center>
                @endforeach
            @endif
            
        </table>
    @else 
        <table style="border-collapse: collapse; border-spacing: 0; width: 85%; border: 1px solid;">
            <tr>
                <th style="color: red;">
                    No planned activities
                </th>
            </tr>
        </table>
    @endif

    <p>
        <b>Note:</b> Please do not reply to this email, this is system generated email from Study Management System.
    </p>
    
    <div class="form-group">
        <h4>Study Management System</h4>
    </div>
        
</div>