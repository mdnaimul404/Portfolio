<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Message Sent</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;900&display=swap');

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(270deg, #ff6a00, #ee0979, #00d2ff, #3a1c71, #f7971e);
            background-size: 1000% 1000%;
            animation: gradientAnimation 30s ease infinite;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
        }

        @keyframes gradientAnimation {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: 900;
            letter-spacing: 2px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            animation: popIn 0.8s ease forwards;
        }

        p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            max-width: 500px;
            line-height: 1.5;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.3);
            animation: fadeInText 1s ease forwards;
        }

        a {
            text-decoration: none;
            background-color: rgba(255, 255, 255, 0.9);
            color: #3a1c71;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 40px;
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
            font-size: 1.1rem;
            display: inline-block;
            animation: popIn 1.2s ease forwards;
        }

        a:hover {
            background-color: #fff;
            color: #ee0979;
            box-shadow: 0 12px 40px rgba(238, 9, 121, 0.7);
            transform: translateY(-4px);
        }

        /* Animations */
        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.6);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeInText {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <h1>Thank you!</h1>
    <p>Your message has been sent successfully. I will get back to you soon.</p>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
