<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
        h1 {
            margin: 0 0 20px 0;
            font-size: 24px;
        }
        .message {
            font-size: 18px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .timestamp {
            color: #333;
            font-size: 14px;
        }
        .back-link {
            margin-top: 30px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($status === 'success')
            <div class="icon success">✅</div>
            <h1 class="success">Update Complete!</h1>
            <div class="message success">{{ $message }}</div>
        @else
            <div class="icon error">❌</div>
            <h1 class="error">Update Failed</h1>
            <div class="message error">{{ $message }}</div>
        @endif
        
        <div class="timestamp">
            Last updated: {{ $timestamp }}
        </div>
        
        <div class="back-link">
            <a href="/">← Back to Home</a>
        </div>
    </div>
</body>
</html>
