$(document).ready(function(){

    let day;
    let month;
    let year;
    let time;

    $('td').click(function(e) {
        if (e.target.id == 'disabled') {
            return;
        }

        $('#saveReservation').off();
        $('#reservation_form').hide();
        $('#messages').empty();

        day = $('.head-day').text();
        month = $('.head-month').text().split(' - ')[0];
        year = $('.head-month').text().split(' - ')[1];

        $.ajax({
            url: '/day/' + day + '/month/' + month + '/year/' + year,
            method : "GET",
            success : function(data){
                showReservationHours(data);
            },fail : function (){
                window.location.href = '/';
                alert('fail');
            }
        });

    });

    function showReservationHours($responseData) {
        $('#reservation_section').show();
        $('#reservation_hours').empty();
        $('#messages').empty();

        let isHourBooked = false;

        for (const i in $responseData['reservationHours']) {
            if (Object.hasOwnProperty.call($responseData['reservationHours'], i)) {
                const reservationAvailebleHour = $responseData['reservationHours'][i];
                isHourBooked = false;
                for (const j in $responseData['reservations']) {
                    if (Object.hasOwnProperty.call($responseData['reservations'], j)) {
                        const reservationHour = $responseData['reservations'][j];
                        if(reservationAvailebleHour.normalize() === reservationHour.normalize()) {
                            isHourBooked = true;
                        }
                    }
                }

                if(isHourBooked) {
                    $('#reservation_hours').append('<button type="button" class="btn btn-danger hour-booked" disabled>' + reservationAvailebleHour + '</button>');
                } else {
                    $('#reservation_hours').append('<button type="button" class="btn btn-success hour-available">' + reservationAvailebleHour + '</button>');
                }

            }
        }
        
        $('.hour-available').click(function(e) {
            e.preventDefault();
            $('#reservation_form').show();
            $('#messages').empty();

            $('#daytime').val(day + ' ' + month + ' ' + year + '  at  ' + $(this).text());
            time = $(this).text();
        });

        $('#saveReservation').click(function(e) {
            e.preventDefault();
            $('#messages').empty();

            $.ajax({
    			url: "/reservation/save",
    			method: "POST",
    			data: {
                    name: $('#name').val(),
    				email: $('#email').val(),
    				day: day,
    				month: month,
                    year: year,
    				time: time,
                    token: $('#token').val()
    			},
                complete : function(data){
        			switch(data.status){
        			case 200:
                        $('button:contains('+ time +')').removeClass("btn-success hour-available").addClass("btn-danger hour-booked").attr("disabled", "disabled");
                        $('#reservation_form').hide();
                        $('#name').val("");
                        $('#email').val("");
                        showMessages(data.responseJSON, 'success');
        				break;
        			case 400:
        				showMessages(data.responseJSON, 'error');
        				break;
                    case 500:
                        showMessages(data.responseJSON, 'error');
                        break;       				
        			}
        		}
    		});
        });
    }

    function showMessages(messages, messageType) {
        for (const key in messages) {
            if (Object.hasOwnProperty.call(messages, key)) {
                const message = messages[key];
                $('#messages').append('<p class=' + messageType +'>' + message + '</p>')
                console.log(message);
            }
        }
    }

})