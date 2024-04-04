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
                    toastr.success('Study life cycle activity is activated', 'Study Life Cycle');    
                } else if(study_life_cycle == 0){
                    toastr.success('Study life cycle activity is deactivated', 'Study Life Cycle');    
                } else {
                    toastr.error('Something Went Wrong!');    
                }
            } else {
                toastr.error('Something Went Wrong!');
            }
        }
    });
});

// open close according

$(document).ready(function(){
    // Collapse all accordion items except the first one
    $('#accordionExample .accordion-collapse').collapse('hide');

    // Handle accordion button clicks
    $('.accordion-button').click(function(){
        // Collapse all accordion items
        $('#accordionExample .accordion-collapse').collapse('hide');
        // Expand the clicked accordion item
        $(this).parents('.accordion-item').find('.accordion-collapse').collapse('show');
    });
});