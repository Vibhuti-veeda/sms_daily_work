<div>
    
    <div class="form-group">
        <p>Hello <?php echo $name; ?>,</p>
    </div>

    <div class="form-group">
        <p style="color: blue;">
            <b>
                Kindly note that below milestone activity have been completed.
            </b>
        </p>
    </div>

    <table style="border-collapse: collapse; border-spacing: 0; width: 100%; border: 1px solid;">
        
        <tr style="border: 1px solid;">
            <center>
                <th style="border: 1px solid;">Study No</th>
                <th style="border: 1px solid;">Project Manager</th>
                <th style="border: 1px solid;">Activity Name</th>
                <th style="border: 1px solid;">Schedule End Date</th>
                <th style="border: 1px solid;">Actual End Date</th>
                <th style="border: 1px solid;">Actual End Date Filled Date-Time</th>
            </center>
        </tr>

        <tr style="border: 1px solid;">
            <center>
                <td style="border: 1px solid; text-align: center;"><?php echo $endMilestoneActivity->studyNo['study_no']; ?></td>
                <td style="border: 1px solid; text-align: center;"><?php echo $endMilestoneActivity->studyNo->projectManager['name']; ?></td>
                <td style="border: 1px solid; text-align: center;"><?php echo $endMilestoneActivity['activity_name']; ?></td>
                <td style="border: 1px solid; text-align: center;"><?php echo date('d M Y', strtotime($endMilestoneActivity['scheduled_end_date'])); ?></td>
                <td style="border: 1px solid; text-align: center;"><?php echo date('d M Y', strtotime($endMilestoneActivity['actual_end_date'])); ?></td>
                <td style="border: 1px solid; text-align: center;"><?php echo date('d M Y H:i:s', strtotime($endMilestoneActivity['actual_end_date_time'])); ?></td>
            </center>
        </tr>

    </table>

    <p>
        <b>Note:</b> Please do not reply to this email, this is system generated email from Study Management System.
    </p>
    
    <div class="form-group">
        <h4>Study Management System</h4>
    </div>
        
</div>