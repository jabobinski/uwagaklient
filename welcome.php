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
        }

        .welcome-screen {
            text-align: center;
        }

        .welcome-screen img {
            width: 200px; /* Dostosuj rozmiar grafiki */
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
            transition: background-color 0.3s;
        }

        .button-yes {
            background-color: #28a745; /* Zielony */
            color: #ffffff;
        }

        .button-yes:hover {
            background-color: #218838;
        }

        .button-no {
            background-color: #dc3545; /* Czerwony */
            color: #ffffff;
        }

        .button-no:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="welcome-screen">
        <img src="detective.png" alt="Logo"> <!-- Podmień logo na swoją grafikę -->
        <h1>MOMENCIK! Czy jesteś Tajemniczym Klientem?</h1>
        <div class="button-group">
            <button class="button-yes" onclick="disconnect()">Tak</button>
            <button class="button-no" onclick="redirectToHome()">Nie</button>
        </div>
    </div>

    <script>
        function redirectToHome() {
            window.location.href = 'index.php'; // Zmień na adres swojej strony głównej
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