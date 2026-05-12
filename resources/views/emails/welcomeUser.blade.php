<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome {{ $employee->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">

    <div style="
        max-width:600px;
        margin:auto;
        background:white;
        padding:30px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,0.1);
    ">

        <h1 style="color:#2c3e50;">
            Welcome {{ $employee->name }} 👋
        </h1>

        <p>
            We are happy to welcome you to our HR System.
        </p>

        <p>
            Your account has been created successfully.
        </p>

        <hr>

        <h3>Login Information:</h3>

        <p>
            <strong>Email:</strong>
            {{ $employee->email }}
        </p>

        <p>
            <strong>Password:</strong>
            {{ $passwordUnHashed }}
        </p>

        <hr>

        <p>
            Please change your password after your first login for security reasons.
        </p>

        <p>
            If you have any questions, feel free to contact the HR team.
        </p>

        <br>

        <p>
            Best Regards,<br>
            HR System Team
        </p>

    </div>

</body>
</html>
