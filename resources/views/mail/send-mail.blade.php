<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Email</title>
</head>
<body>
    <h2>Confirm Your Subscription to Our Newsletter</h2>
    <p>Dear {{ $email  }}</p>
    <p>Thank you for joining our community! We're excited to have you as a subscriber to our newsletter.</p>
    <p>Thank You</p>
    <p>Regards,</p>
    {{ config('app.name') }}
    <p><a target="_blank" href="https://www.instagram.com/normalizestrange/">@normalizestrange</a></p>
</body>
</html>

