<?php
session_start();

// Verifica se l'amministratore è loggato
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

include_once("metodi.php");

// Gestione delle azioni (Insert, Update, Delete)
$action = $_POST['action'] ?? '';
if ($action) {
    $id = $_POST['id'] ?? null;
    $section = $_POST['section'] ?? '';
    $content = $_POST['content'] ?? '';

    switch($action) {
        case 'insert':
            insertContent($section, $content);
            break;
        case 'update':
            if ($id !== null) updateContent($id, $section, $content);
            break;
        case 'delete':
            if ($id !== null) deleteContent($id);
            break;
    }
    // Reindirizza per evitare il reinvio del form al ricaricamento della pagina (PRG pattern)
    header('Location: admin.php');
    exit();
}

// Recupera i contenuti per mostrarli nella tabella
$contents = getContents();
$editItem = null;

// Gestione per mostrare il form di modifica
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    foreach ($contents as $c) {
        if ($c['id'] == $editId) {
            $editItem = $c;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pannello di Amministrazione</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        .form-section {
            background-color: #f9fbfd;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e1e5eb;
            margin-bottom: 40px;
        }
        .form-section h3 {
            margin-top: 0;
            color: #4f7eff;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .btn {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .btn-primary { background-color: #4f7eff; }
        .btn-primary:hover { background-color: #3a65db; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-danger { background-color: #dc3545; padding: 6px 12px; font-size: 14px; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-edit { background-color: #ffc107; color: #212529; padding: 6px 12px; font-size: 14px; text-decoration: none; border-radius: 4px; display: inline-block;}
        .btn-edit:hover { background-color: #e0a800; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        .actions-col {
            width: 160px;
            text-align: center;
        }
        .content-cell {
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .cancel-link {
            margin-left: 10px;
            color: #6c757d;
            text-decoration: none;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Pannello di Amministrazione</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Sezione per Modifica (se attivo) o Inserimento -->
    <div class="form-section">
        <?php if ($editItem): ?>
            <h3>Modifica Contenuto</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($editItem['id']); ?>">
                <div class="form-group">
                    <label>Sezione (es. autobiografia, pcto, ecc.)</label>
                    <input type="text" name="section" value="<?php echo htmlspecialchars($editItem['section']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Contenuto</label>
                    <textarea name="content" rows="6" required><?php echo htmlspecialchars($editItem['content']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-warning">Salva Modifiche</button>
                <a href="admin.php" class="cancel-link">Annulla</a>
            </form>
        <?php else: ?>
            <h3>Aggiungi Nuovo Contenuto</h3>
            <form method="POST">
                <input type="hidden" name="action" value="insert">
                <div class="form-group">
                    <label>Sezione (es. autobiografia, pcto, ecc.)</label>
                    <input type="text" name="section" placeholder="Inserisci il nome della sezione" required>
                </div>
                <div class="form-group">
                    <label>Contenuto</label>
                    <textarea name="content" rows="4" placeholder="Inserisci il testo qui..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Inserisci Contenuto</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Lista Contenuti -->
    <h3>Contenuti Presenti nel Sito</h3>
    <?php if (count($contents) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sezione</th>
                    <th>Contenuto</th>
                    <th class="actions-col">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($contents as $c): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($c['section']); ?></strong></td>
                    <td class="content-cell" title="<?php echo htmlspecialchars($c['content']); ?>">
                        <?php echo htmlspecialchars($c['content']); ?>
                    </td>
                    <td class="actions-col">
                        <a href="admin.php?edit=<?php echo $c['id']; ?>" class="btn-edit">Modifica</a>
                        
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Sei sicuro di voler eliminare questo contenuto? L\'azione è irreversibile.');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <button type="submit" class="btn btn-danger">Elimina</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #666; font-style: italic;">Nessun contenuto presente. Aggiungi il primo usando il modulo qui sopra.</p>
    <?php endif; ?>

</div>

</body>
</html>
