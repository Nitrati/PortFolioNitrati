<?php
// metodi.php
// Pagina nella quale ci sono i metodi da richiamare per operazioni di CRUD su JSON

$dataFile = __DIR__ . '/data.json';

// Inizializza il file JSON se non esiste
function initDataFile() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
}

// Ottieni tutti i contenuti
function getContents() {
    global $dataFile;
    initDataFile();
    $json = file_get_contents($dataFile);
    return json_decode($json, true) ?: [];
}

// Salva i contenuti nel JSON
function saveContents($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// 1) Inserimento contenuto
function insertContent($section, $content) {
    $data = getContents();
    $newId = time(); // Usa timestamp come ID univoco semplice
    $data[] = [
        'id' => $newId,
        'section' => $section,
        'content' => $content
    ];
    saveContents($data);
}

// 2) Modifica contenuto esistente
function updateContent($id, $section, $content) {
    $data = getContents();
    foreach ($data as &$item) {
        if ($item['id'] == $id) {
            $item['section'] = $section;
            $item['content'] = $content;
            break;
        }
    }
    saveContents($data);
}

// 3) Cancellazione contenuto
function deleteContent($id) {
    $data = getContents();
    $data = array_filter($data, function($item) use ($id) {
        return $item['id'] != $id; // Filtra via l'elemento con l'ID da cancellare
    });
    // Re-indicizza l'array dopo array_filter e salva
    saveContents(array_values($data));
}
?>