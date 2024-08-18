<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .suspended-container {
            text-align: center;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .suspended-container h1 {
            font-size: 24px;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        .suspended-container p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        .suspended-container a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .suspended-container a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="suspended-container">
        <h1>Account Suspended</h1>
        <p>Your account has been temporarily suspended. Please contact the administrator for more details.</p>
        <a href="{{ route('landing') }}">Come back to Home</a>
    </div>
</body>
</html>
