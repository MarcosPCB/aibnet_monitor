<!DOCTYPE html>
<html>
<head>
    <title>{{ $detalhes['title'] }}</title>
    <meta charset="UTF-8">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    <style type="text/css">
        body {
            background-color: black;
            color: white;
            font-family: "Roboto";
            color: #BBBBBB;
            line-height: 2.3rem;
            font-size: 1.5rem;
            line-break: normal;
        }

        .d-flex {
            display: flex;
        }

        .flex-column {
            flex-direction: column;
        }

        .flex-row {
            flex-direction: row;
        }

        .justify-content-center {
            justify-content: center;
        }

        .align-items-center {
            align-items: center;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .hero::before {
            content: '';
            background: color-mix(in srgb, black, transparent 30%);
            position: absolute;
            inset: 0;
            z-index: -1;
        }

        .btn {
            padding: 20px 40px;
            background: #232d96;
            border: 0;
            transition: all 0.4s ease-in-out;
            color: white;
            border-radius: 4px;
            pointer-events: all;
            font-family: "Roboto";
            font-weight: 500;
            cursor: pointer;
            font-size: large;
        }

        .btn:hover {
            background: color-mix(in srgb, #232d96, transparent 20%);
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column justify-content-center align-items-center hero" style="margin: 10%;">
        <img src="hero-bg.jpg" style="width: 100%; height: 100%; position: absolute; object-fit: cover; inset: 0; z-index: -2;">
        <img src="logo.png" style="width: 25%; height: 25%;">
        <br>
        <h1 class="text-center" style="color: white">{{ $detalhes['title'] }}</h1>
        <hr style="width: 256px">
        <p>{{ $detalhes['body'] }}</p>
        <br>
        <button class="btn">{{ $detalhes['button'] }}</button>
    </div>
</body>
</html>
