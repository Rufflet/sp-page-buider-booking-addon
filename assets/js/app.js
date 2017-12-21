/**
 * @package SP Page Builder Calendar Booking Addon
 * @author Alexander Yershov (https://www.upwork.com/o/profiles/users/_~0175333b8b4aefa403/)
*/
jQuery(function ($) {
    var enabledHrs = [7,8,9,10,11,12,13,14,15,16,23]
    $('#datetimepicker').datetimepicker({
        inline: true,
        sideBySide: true,
        defaultDate: moment(new Date()).add(1,'days').hours(enabledHrs[0]).minutes(0).seconds(0).milliseconds(0),
        minDate: moment(new Date()).add(1,'days').hours(enabledHrs[0]).minutes(0).seconds(0).milliseconds(0),
        enabledHours: enabledHrs
    }).on('dp.change', function (event) {
        document.querySelector("input[name=datetime]").value = event.date.format("dddd, MMMM Do YYYY, h:mm:ss a");
    });
    //  Put default date to the hidden input
    document.querySelector("input[name=datetime]").value = moment(new Date()).add(1,'days').hours(enabledHrs[0]).minutes(0).seconds(0).milliseconds(0).format("dddd, MMMM Do YYYY, h:mm:ss a");

    //  Calendar Booking Form
    $(document).on('submit', '#booking-form', function(event) {
        
        event.preventDefault();

        var $self   = $(this);
        var value   = $(this).serializeArray();
        var request = {
            'option' : 'com_sppagebuilder',
            'task' : 'ajax',
            'addon' : 'booking',
            //  'g-recaptcha-response' : $self.find('#g-recaptcha-response').val(),
            'data' : value
        };

        $.ajax({
            type   : 'POST',
            data   : request,
            beforeSend: function(){
                $self.find('.sppb-btn > .fa').addClass('fa-spinner fa-spin');
            },
            success: function (response) {
                var results = $.parseJSON(response);

                try {
                    var data = $.parseJSON(results.data);
                    var content = data.content;
                    var type = 'json';
                } catch (e) {
                    var content = results.data;
                    var type = 'strings';
                }

                if (type == 'json') {
                if(data.status) {
                    $self.trigger('reset');
                }
                } else {
                    $self.trigger('reset');
                }

                $self.find('.sppb-btn > .fa-spin').removeClass('fa-spinner fa-spin');
                $self.next('.sppb-booking-form-status').html(content).fadeIn().delay(4000).fadeOut(500);
                setTimeout(function(){ window.location.replace("/submitted") }, 1000);
            }
        });

        return false;
    });


    
})