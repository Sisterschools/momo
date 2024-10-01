<!DOCTYPE html>
<html>

<head>
    <title>Welcome to MoMo Education!</title>
</head>

<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>Your account has been created successfully.</p>
    <p>Please click the following link to set your password:</p>

    <a href="{{ $url }}">{{ $url }}</a>

    <p>If you did not request this, no further action is required.</p>
</body>

</html>