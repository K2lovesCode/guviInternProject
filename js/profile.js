$(document).ready(function () {
    const token = localStorage.getItem("session_token");
    if (!token) window.location.href = 'login.html';

    $('#btnLogout').click(function () {
        localStorage.removeItem('session_token');
        window.location.href = 'login.html';
    });

    //profile fetching
    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        data: { token: token },
        success: function (data) {

            $('#age').val(data.age || '');
            $('#dob').val(data.dob || '');
            $('#contact').val(data.contact || '');
        },
        error: function (xhr) {
            console.error("Fetch profile failed", xhr.responseText);
        }
    });

    // update profile logic
    $('#btnUpdate').click(function () {
        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            data: {
                token: localStorage.getItem("session_token"),
                age: $('#age').val(),
                dob: $('#dob').val(),
                contact: $('#contact').val()
            },
            success: function (res) {
                alert("Profile Updated Successfully!");
            },
            error: function (xhr) {
                alert("Failed to update profile. " + xhr.responseText);
            }
        });
    });
});