<!DOCTYPE html>
<html>

<head>
    <title>Invoices System</title>
</head>

<body>
    <p>Dear {{ $name }},</p>

    <p>Welcome to Invoices System.</p>

    <p><strong>Temporary Password: {{ $password }}</strong></p>

    <ol>
        <li>Go to <a href="https://invoices.ftech.com.my/">Invoices System</a>.</li>
        <li>Log in to your account using the temporary password provided.</li>
        <li>Go to "Change Password" at the menu.</li>
        <li>Enter the current password and a new password that is unique and secure.</li>
    </ol>

    <p>Thank you.</p>
</body>

</html>
