<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px -20px;
        }

        .content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }

        .camp-name {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'Falcon Teams') }}</h1>
        </div>

        <div class="content">
            <p> {{ $parentName }},</p>

            <div style="margin: 20px 0;">
                <p>We are pleased to confirm that we have received your payment for {{ $campName }}.</p>

                <p>Here are the details of your payment:</p>
                <ul>
                    <li><strong>Camper Name:</strong> {{ $camperName }}</li>
                    <li><strong>Camp Name:</strong> {{ $campName }}</li>
                    <li><strong>Payment Amount:</strong> ${{ number_format($paymentAmount, 2) }}</li>
                    <li><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($paymentDate)->format('F j, Y') }}</li>
                </ul>

                <p>If you have any questions or need further assistance, please feel free to contact us at (Support
                    Email)</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Falcon Teams') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
