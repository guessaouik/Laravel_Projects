<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Confirmation</title>
</head>
<body>
    <h2>EMAIL CONFIRMATION</h2>
    <p>Dear client,</p>
    <p>
        I hope this email finds you well. I am reaching out to you regarding an important matter - the confirmation of your email address for password reset. We value your security and want to ensure that your account remains protected.
    </p>
    <p>Please take a moment to review the following details:</p>
    <ul>
        <li><strong>Email Address:</strong> {{ $emailData['email'] }}</li>
        <li><strong>Verification Code:</strong> {{ $emailData['code'] }}</li>
    </ul>
    <p>
        To confirm your email address and proceed with the password reset, please enter the provided verification code when prompted. This code serves as an additional layer of security to verify your identity and protect your account.
    </p>
    <p>
        If you did not initiate this password reset request, please disregard this email. No action is required on your part, and your account remains secure.
    </p>
    <p>
        Thank you for your attention to this matter. Should you have any questions or concerns, please don't hesitate to contact our support team at [Support Email Address]. We are here to assist you.
    </p>
    <p>Best regards,</p>
    <p>GUESSAOUI KHALED</p>
    <p>MedCare Service</p>
</body>
</html>
