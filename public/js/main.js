
$(document).ready(function (){
    //Zavření PopUp upozornění
    $('.deleteBtn').on('click', function() {
        $('div.popUp').remove();
    });

    /*
    $('#calendarActual').click(function (event) {
        console.log("calendarActual");
        var formData = {
            reminder: $("#reminder").val(),
            timer: $("#timer").val(),
            calendarActual: "calendarActual",
        };

        $.ajax({
            type: "POST",
            url: "bakateam/menu",
            data: formData,
            dataType: "json",
            encode: true,
        }).done(function (data) {
            console.log(data);
        });

        event.preventDefault();
    });

    */


})
