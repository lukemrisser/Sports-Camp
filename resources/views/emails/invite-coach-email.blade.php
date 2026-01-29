<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coach Invite</title>
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
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ config('app.name', 'Falcon Teams') }}</h1>
        </div>

        <div class="content">
            <p>Hello {{ $coachName }}</p>
        </div>

        <div style="margin: 20px 0;">
            <p> We want you to be a coach! </p>
            <p> Click the link below and sign up using your email ending in {{ $emailDomain }}</p>
            <p style="margin-top: 12px;"><a href="{{ $inviteUrl }}"
                    style="background:#0a3f94;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;">Accept
                    Invite / Register</a></p>
            <p style="font-size:12px;color:#666;margin-top:8px;">Or copy/paste this link into your browser: <br><a
                    href="{{ $inviteUrl }}">{{ $inviteUrl }}</a></p>
        </div>
