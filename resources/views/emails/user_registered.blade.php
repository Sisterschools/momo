<!DOCTYPE html>
<html>

<head>
    <title>Welcome to MoMo Education!</title>
</head>

<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Your account has been created successfully.</p>
    <p>Here are your login details:</p>
    <p>Email: {{ $user->email }}</p>
    <p>Password: {{ $password }}</p>
    <p>Please change your password after logging in for the first time.</p>
</body>

</html>