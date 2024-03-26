$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// activity Status 
$(document).on('change', '.studyLifeCycleStatus', function(){
    if(this.checked){
        study_life_cycle = 1;
    } else {
        study_life_cycle = 0;
    }
    var id = $(this).data('id');

    $.ajax({
        url: "/sms-admin/study-life-cycle/view/update-study-life-cycle",
        method:'POST',
        data:{ study_life_cycle: study_life_cycle, id:id},
        success: function(data){
            if(data == 'true'){
                if(study_life_cycle == 1){
                    toastr.success('Activity activated');    
                } else if(study_life_cycle == 0){
                    toastr.success('Activity deactivated');    
                } else {
                    toastr.error('Something Went Wrong!');    
                }
            } else {
                toastr.error('Something Went Wrong!');
            }
        }
    });
});

// Change study life cycle train
$(document).ready(function(){

    // Change study life cycle train
    $(document).on('change', '.studiesView', function(){
        var id = $(this).val();     
        $.ajax({
            url: "/sms-admin/study-life-cycle/view/change-studies-life-cycle-train",
            method:'POST',
            data:{ id: id },
            success: function(data){
                if (id == 'ALL') {
                    $('.displayActivity').show();
                    $('.displayStudyActivity').hide();
                } else {
                    $('.displayActivity').hide();
                    $('.displayStudyActivity').show();
                    $('.displayStudyActivity').empty().append(data.html);
                }
            }
        });
    });
});
