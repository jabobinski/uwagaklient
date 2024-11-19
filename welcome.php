<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona Startowa</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .welcome-screen {
            text-align: center;
            opacity: 1;
            transition: opacity 0.8s ease, transform 0.8s ease;
        }

        .welcome-screen.fade-out {
            opacity: 0;
            transform: scale(0.95);
        }

        .welcome-screen img {
            width: 200px;
            margin-bottom: 20px;
        }

        .welcome-screen h1 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .button-group button {
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button-yes {
            background-color: #28a745;
            color: #ffffff;
        }

        .button-yes:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .button-no {
            background-color: #dc3545;
            color: #ffffff;
        }

        .button-no:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="welcome-screen" id="welcomeScreen">
        <img src="logo.png" alt="Logo"> <!-- Podmień logo na swoją grafikę -->
        <h1>MOMENCIK! Czy jesteś Tajemniczym Klientem?</h1>
        <div class="button-group">
            <button class="button-yes" onclick="disconnect()">Tak</button>
            <button class="button-no" onclick="decline()">Nie</button>
        </div>
    </div>

    <script>
        function decline() {
            // Ustaw cookie oznaczające, że użytkownik kliknął "Nie"
            document.cookie = "user_declined=true; path=/; max-age=86400"; // Ważne przez 1 dzień
            const screen = document.getElementById('welcomeScreen');
            screen.classList.add('fade-out'); // Dodaje klasę do animacji zanikania
            setTimeout(() => {
                window.location.href = 'index.php'; // Przekierowanie po zakończeniu animacji
            }, 800); // Czas trwania animacji (zgodny z CSS)
        }

        function disconnect() {
            document.body.innerHTML = '<h1 style="text-align: center; margin-top: 20%;">Połączenie przerwane</h1>';
            setTimeout(() => {
                window.location.href = 'about:blank'; // Zerwanie połączenia
            }, 2000);
        }
    </script>
</body>
</html>
