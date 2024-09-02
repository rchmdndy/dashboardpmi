<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Success</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: #f4f7f6;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.card {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 400px;
    width: 100%;
}

.logo {
    width: 100px;
    margin-bottom: 20px;
}

h1 {
    color: #333;
    margin-bottom: 10px;
}

p {
    color: #666;
    margin-bottom: 20px;
}

.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #45a049;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="card">
        <img src="{{ url('images/logo_asli.png') }}" class="logo" alt="PMI Logo">
            <h1>Berhasil!</h1>
            <p>Terima kasih telah menggunakan layanan kami, Kami akan selalu berusaha meningkatkan kinerja kami. Salam Hangat</p>
        </div>
    </div>
</body>
</html>
