<?php
//15/04/2026
// login.php
// Questo file gestisce il login dell'amministratore. In una produzione reale, dovresti utilizzare un database per memorizzare le credenziali e implementare misure di sicurezza adeguate (hashing delle password, protezione contro SQL injection, ecc.).
include_once("php/metodi.php"); // Includi il file con i metodi CRUD per la gestione dei contenuti
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Dati di accesso hardcoded (da sostituire con un database in produzione)
    $validUsername = 'admin';
    $validPassword = 'password123';

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['logged_in'] = true;
        header('Location: admin.php'); // Reindirizza alla pagina admin
        exit();
    } else {
        echo 'Credenziali non valide. Riprova.';
    }
}
//metodi crud per inserimento, modifica, cancellazione dei contenuti da parte dell'amministratore all'interno del sito
if(isset($_POST['action']))
    {
        $action = $_POST['action'];
        switch($action) {
            case 'insert':
                insertContent($section, $content);
                break;
            case 'update':
                updateContent($id, $section, $content);
                break;
            case 'delete':
                deleteContent($id);
                break;
            default:
                echo 'Azione non valida.';
        }
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login Admin</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>