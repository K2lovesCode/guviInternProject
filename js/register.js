$('#btnRegister').click(function () {
    $.ajax({
        url: 'php/register.php',
        type: 'POST',
        data: {
            username: $('#reg_user').val(),
            password: $('#reg_pass').val()
        },
        success: function (data) {
            if (data.status === 'success') {
                alert("Registration Successful!");
                window.location.href = 'login.html';
            } else {
                alert(data.message || "Registration Failed");
            }
        }
    });
});