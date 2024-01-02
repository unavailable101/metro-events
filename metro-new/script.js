// function cancelEvent(eventId) {
    
//     var cancellationReason = document.getElementById("cancelReason").value;
        
//         console.log(cancellationReason);
        
//         $.ajax({
//             method: "POST",
//             url: "api-admin.php", 
//             data: {
//                 functionName: "eventCancel",
//                 eventId: eventId,
//                 cancellationReason: cancellationReason
//             },
//             success: function(response) {
//                 console.log('Response from PHP function:', response);
//                 var jsonResponse = JSON.parse(response);
//                 if (jsonResponse.success) {
//                     window.location.href = 'create-events.php';
//                 } else {
//                     alert('failed!');
//                 }
//             },
//             error: function(xhr, status, error) {
//                 console.error('Error:', error);
//             }
//         });
// }

function requestJoin(eventId, uid){
    // console.log('called');
    // alert("Request to Join Event Success! " + eventId + " " + uid);
    $.ajax({
        method: "POST",
        url: "api-user.php", 
        data: {
            functionName: "joinRequest",
            eventId: eventId,
            uid: uid
        },
        success: function(response) {
            if (response.trim() === 'sent-request' ){
                showNotification('Already sent a request');
            } else {
                console.log('Response from PHP function:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

function requestOrg(eventId, uid){
    // console.log('called');
    // alert("Requesting to be an Organizer Success! " + eventId + " " + uid);
    $.ajax({
        method: "POST",
        url: "api-user.php", 
        data: {
            functionName: "orgRequest",
            eventId: eventId,
            uid: uid
        },
        success: function(response) {
            if (response.trim() === 'sent-request') {
                showNotification('Already sent a request');
            } else {
                console.log('Response from PHP function:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

//for pop up div alert
// function showPopup() {
//     const notification = document.getElementById('notification');
//     notification.classList.remove('hidden');
//     setTimeout(function() {
//         notification.classList.add('hidden');
//     }, 3000);
// }
// showPopup();

//ignore the comment below
//unique lng ni sha, for user request to join/org
//di mu gana ang uban if ako i sagol
function showNotification(message){
    const notificationDiv = document.createElement('div');
    notificationDiv.classList.add('notification');
    notificationDiv.innerHTML = `<span class="message">${message}</span>`;
    document.body.appendChild(notificationDiv);

    setTimeout(function () {
        notificationDiv.classList.add('hidden');
        setTimeout(function () {
            notificationDiv.remove();
        }, 500);
    }, 3000);
}