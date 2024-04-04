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
                    // Construct HTML dynamically using data received from the server
                    var html = '<div class="col-lg-12" style="border: 2px solid; overflow-x: scroll; overflow-y: hidden;">'+
                                    '<div class="card card-stepper text-black" style="border-radius: 16px; min-width: '+ data.minWidth +'px; width: '+ data.finalWidth +'px; height: 222px;">'+
                                        '<div class="card-body pt-3">'+
                                            '<div class="d-flex justify-content-between align-items-center mb-3">'+
                                                '<div>'+
                                                    '<h5 class="mb-3">' + data.projectManagerName + ' (' + formatDateString(data.firstActivityDate) + ' to ' + formatDateString(data.lastActivityDate) + ')</h5>'+
                                                '</div>'+
                                            '</div>'+
                                            '<ul id="progressbar-2" class="d-flex">';

                    if(data.getActivity) {
                        data.getActivity.forEach(function(gav) {
                            // Wrap activity name ensuring the last word is not cut off
                            var activityName = gav.activity_name.replace(/(.{1,25})(\s|$)/g, '$1<br>');

                            if(gav.period_no != 1){
                                activityName += '(P' + gav.period_no + ')';
                            }
                            // Format date
                            var activityDate = gav.actual_end_date ? formatDateString(gav.actual_end_date) : (gav.scheduled_end_date ? formatDateString(gav.scheduled_end_date) : 'N/A');

                            html += '<li class="step0 text-center mt-5 ' + (gav.actual_end_date ? 'active' : '') + '">'+
                                        '<div class="ps-2 mb-5 pb-5" style="position: relative; top: -100px; width: 200px; text-align: left;">'+
                                            '<p class="text-start fw-bold date" style="transform: skew(7deg, -22deg);">' + activityDate + '</p>'+
                                        '</div>'+
                                        '<div class="pb-1" style="position: relative; top: -129px; width 100%; text-align: left;">'+
                                            '<p class="fw-bold activityName">' + activityName+ '</p>'+ // Insert the wrapped activityName
                                    '</div>'+
                                '</li>';
                        });
                    }

                    html += '</ul>'+
                            '</div>'+
                        '</div>'+
                    '</div>';
                    $('.displayStudyActivity').empty().html(html);
                }
            }
        });
    });

    // Function to format date string
    function formatDateString(dateString) {
        if (!dateString) return 'N/A';
        var date = new Date(dateString);
        var day = date.getDate();
        var month = date.toLocaleString('en-us', { month: 'short' });
        var year = date.getFullYear();
        return day + ' ' + month + ' ' + year;
    }

});



