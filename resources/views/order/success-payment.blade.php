<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful</title>
    <style>
        /* Add any additional styling or adjustments here */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: #4caf50; /* Green */
        }

        .mdi {
            font-size: 100px;
        }

        .headline {
            font-size: 2em;
            margin-bottom: 20px;
        }

        .subheading {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #4caf50; /* Green */
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #45a049; /* Slightly darker green on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span class="text-success mdi mdi-check-circle-outline" style="font-size: 100px;">&#10003;</span>
                <h1 class="headline mb-4">Payment Successful!</h1>
                <p class="subheading mb-4">
                    Thank you for your purchase. A receipt has been sent to your email.
                </p>
                <a href="/" class="btn btn-primary">Go back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>