$('#btnLogin').click(function () {
    $.ajax({
        url: 'php/login.php',
        type: 'POST',
        data: { username: $('#user').val(), password: $('#pass').val() },
        success: function (data) {
            if (data.status === 'success') {
                alert("Login Successful!");
                // stores in local storage
                localStorage.setItem("session_token", data.token);
                window.location.href = 'profile.html';
            } else {
                alert(data.message || "Login Failed");
            }
        }
    });
});