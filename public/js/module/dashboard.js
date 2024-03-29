$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Dashboard view Change
$(document).on('change', '.dashboardView', function(){
    
    var id = $(this).val();
    $.ajax({
        url: "/sms-admin/dashboard/view/change-dashboard-view",
        method:'POST',
        data:{ id: id },
        success: function(data){
            if (id == 'ALL') {
                $('.allView').show();
                $('.personalView').hide();
            } else {
                $('.allView').hide();
                $('.personalView').show();
                $('.personalView').empty().append(data.html);
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
            url: "/sms-admin/dashboard/view/change-studies-life-cycle-train",
            method:'POST',
            data:{ id: id },
            success: function(data){
                if (id == 'ALL') {
                    $('.displayActivity').show();
                    $('.displayStudyActivity').hide();
                } else {
                    $('.displayActivity').hide();
                    $('.displayStudyActivity').show();
                    $('.displayStudyActivity').empty().html(data.html);
                }
            }
        });
    });
});