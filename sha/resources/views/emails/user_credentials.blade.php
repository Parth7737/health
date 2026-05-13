<!DOCTYPE html>
<html>
<head>
    <title>Your Account Details</title>
</head>
<body>
    <h1>Welcome to SHA!</h1>

    <p>Dear {{$name}},</p>

    <p>We are pleased to inform you that your account has been successfully created. Below are your login details:</p>

    <p><strong>User ID:</strong> {{ $userId }}</p>
    <p><strong>Password:</strong> {{ $password }}</p>

    <p>For your security, please ensure to change your password after your first login.</p>

    <p>If you have any questions or need further assistance, feel free to contact our support team.</p>

    <p>Thank you for choosing our services.</p>

    <p>Best regards,<br>
        SHA <br>
        (State Health Authority)
    </p>
</body>
</html>
