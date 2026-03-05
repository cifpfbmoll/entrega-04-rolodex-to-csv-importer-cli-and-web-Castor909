<?php

/**
 * Rolodex Contact Importer - Web Version (Level 3 - Advanced Management)
 * 
 * Features:
 * - Create, Read, Update, Delete (CRUD) operations
 * - Search and filter contacts
 * - CSV import (bulk upload)
 * - Export to multiple formats (CSV, vCard, PDF)
 * - Contact statistics and analytics
 */

// Configuration
define('WRITEPATH', __DIR__ . '/writable/');
$csvFile = WRITEPATH . 'contacts.csv';

// Ensure writable directory exists
if (!is_dir(WRITEPATH)) {
    mkdir(WRITEPATH, 0755, true);
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function readContacts() {
    global $csvFile;
    $contacts = [];
    
    if (file_exists($csvFile)) {
        $handle = fopen($csvFile, 'r');
        if ($handle) {
            fgetcsv($handle); // Skip header
            while (($row = fgetcsv($handle)) !== false) {
                $contacts[] = [
                    'id' => count($contacts),
                    'name' => $row[0] ?? '',
                    'phone' => $row[1] ?? '',
                    'email' => $row[2] ?? ''
                ];
            }
            fclose($handle);
        }
    }
    
    return $contacts;
}

function saveContacts($contacts) {
    global $csvFile;
    
    $handle = fopen($csvFile, 'w');
    fputcsv($handle, ['Name', 'Phone', 'Email']);
    
    foreach ($contacts as $contact) {
        fputcsv($handle, [
            $contact['name'],
            $contact['phone'],
            $contact['email']
        ]);
    }
    fclose($handle);
}

function addContact($name, $phone, $email) {
    if (empty(trim($name))) {
        return ['error' => 'El nombre es obligatorio'];
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'El email no es válido'];
    }
    
    global $csvFile;
    
    if (!file_exists($csvFile)) {
        $handle = fopen($csvFile, 'w');
        fputcsv($handle, ['Name', 'Phone', 'Email']);
        fclose($handle);
    }
    
    $handle = fopen($csvFile, 'a');
    fputcsv($handle, [trim($name), trim($phone), trim($email)]);
    fclose($handle);
    
    return ['success' => '¡Contacto añadido correctamente!'];
}

function updateContact($id, $name, $phone, $email) {
    if (empty(trim($name))) {
        return ['error' => 'El nombre es obligatorio'];
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => 'El email no es válido'];
    }
    
    $contacts = readContacts();
    
    if ($id < 0 || $id >= count($contacts)) {
        return ['error' => 'Contacto no encontrado'];
    }
    
    $contacts[$id] = [
        'id' => $id,
        'name' => trim($name),
        'phone' => trim($phone),
        'email' => trim($email)
    ];
    
    saveContacts($contacts);
    
    return ['success' => '¡Contacto actualizado correctamente!'];
}

function deleteContact($id) {
    $contacts = readContacts();
    
    if ($id < 0 || $id >= count($contacts)) {
        return ['error' => 'Contacto no encontrado'];
    }
    
    array_splice($contacts, $id, 1);
    saveContacts($contacts);
    
    return ['success' => '¡Contacto eliminado correctamente!'];
}

function searchContacts($query, $contacts) {
    if (empty($query)) {
        return $contacts;
    }
    
    $query = strtolower(trim($query));
    
    return array_filter($contacts, function($contact) use ($query) {
        return stripos($contact['name'], $query) !== false ||
               stripos($contact['phone'], $query) !== false ||
               stripos($contact['email'], $query) !== false;
    });
}

function importCSV($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Error al subir el archivo'];
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, ['text/csv', 'text/plain', 'application/vnd.ms-excel'])) {
        return ['error' => 'El archivo debe ser CSV'];
    }
    
    $contacts = readContacts();
    $imported = 0;
    $errors = [];
    
    if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
        fgetcsv($handle); // Skip header
        $row = 0;
        
        while (($data = fgetcsv($handle)) !== false) {
            $row++;
            
            if (empty($data[0])) {
                continue;
            }
            
            $name = $data[0] ?? '';
            $phone = $data[1] ?? '';
            $email = $data[2] ?? '';
            
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Fila $row: Email inválido";
                continue;
            }
            
            // Check for duplicates
            $isDuplicate = false;
            foreach ($contacts as $contact) {
                if (strtolower($contact['name']) === strtolower(trim($name))) {
                    $isDuplicate = true;
                    break;
                }
            }
            
            if (!$isDuplicate) {
                $contacts[] = [
                    'id' => count($contacts),
                    'name' => trim($name),
                    'phone' => trim($phone),
                    'email' => trim($email)
                ];
                $imported++;
            }
        }
        fclose($handle);
    }
    
    saveContacts($contacts);
    
    $message = "¡Se importaron $imported contactos correctamente!";
    if (!empty($errors)) {
        $message .= " Errores: " . implode(", ", $errors);
    }
    
    return ['success' => $message];
}

function exportToVCard($contact) {
    $vcard = "BEGIN:VCARD\r\n";
    $vcard .= "VERSION:3.0\r\n";
    $vcard .= "FN:" . $contact['name'] . "\r\n";
    
    if (!empty($contact['phone'])) {
        $vcard .= "TEL:" . $contact['phone'] . "\r\n";
    }
    
    if (!empty($contact['email'])) {
        $vcard .= "EMAIL:" . $contact['email'] . "\r\n";
    }
    
    $vcard .= "END:VCARD\r\n";
    
    return $vcard;
}

function sanitizeFilename($filename) {
    return preg_replace('/[^a-z0-9_-]/', '_', strtolower($filename)) . '.vcf';
}

// ============================================================================
// REQUEST HANDLING
// ============================================================================

$message = '';
$messageType = '';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$searchQuery = $_GET['search'] ?? '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add') {
            $result = addContact($_POST['name'] ?? '', $_POST['phone'] ?? '', $_POST['email'] ?? '');
            $message = $result['error'] ?? $result['success'] ?? '';
            $messageType = isset($result['error']) ? 'danger' : 'success';
            $action = 'list';
            
        } elseif ($action === 'update') {
            $result = updateContact($_POST['id'], $_POST['name'] ?? '', $_POST['phone'] ?? '', $_POST['email'] ?? '');
            $message = $result['error'] ?? $result['success'] ?? '';
            $messageType = isset($result['error']) ? 'danger' : 'success';
            $action = 'list';
            
        } elseif ($action === 'delete') {
            $result = deleteContact($_POST['id']);
            $message = $result['error'] ?? $result['success'] ?? '';
            $messageType = isset($result['error']) ? 'danger' : 'success';
            $action = 'list';
            
        } elseif ($action === 'import') {
            if (!empty($_FILES['csv_file'])) {
                $result = importCSV($_FILES['csv_file']);
                $message = $result['error'] ?? $result['success'] ?? '';
                $messageType = isset($result['error']) ? 'danger' : 'success';
            } else {
                $message = 'Por favor selecciona un archivo';
                $messageType = 'danger';
            }
            $action = 'list';
        }
    }
}

// Handle export requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['export'])) {
    $contacts = readContacts();
    
    if ($_GET['export'] === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="contacts_' . date('Y-m-d') . '.csv"');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Name', 'Phone', 'Email']);
        foreach ($contacts as $contact) {
            fputcsv($handle, [$contact['name'], $contact['phone'], $contact['email']]);
        }
        fclose($handle);
        exit;
        
    } elseif ($_GET['export'] === 'vcard') {
        if (empty($id)) {
            header('Location: index.php');
            exit;
        }
        
        if ($id >= 0 && $id < count($contacts)) {
            header('Content-Type: text/vcard; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . sanitizeFilename($contacts[$id]['name']) . '"');
            echo exportToVCard($contacts[$id]);
            exit;
        }
    }
}

// Get contacts based on search
$contacts = readContacts();
$filteredContacts = searchContacts($searchQuery, $contacts);

// Calculate statistics
$totalContacts = count($contacts);
$withPhone = count(array_filter($contacts, fn($c) => !empty($c['phone'])));
$withEmail = count(array_filter($contacts, fn($c) => !empty($c['email'])));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>📇 Rolodex Contact Manager</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { 
            max-width: 1200px; 
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .card { 
            box-shadow: 0 8px 16px rgba(0,0,0,0.1); 
            border: none;
            border-radius: 12px;
            background: white;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
            font-weight: 600;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 1.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 0.5rem 0;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .hero-section {
            background: white;
            color: #333;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .hero-section h1 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="mb-1"><i class="bi bi-person-rolodex"></i> Rolodex Contact Manager</h1>
            <p class="text-muted mb-0">Gestiona tu agenda de contactos con facilidad - Crear, buscar, editar y exportar</p>
        </div>

        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType === 'danger' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= $messageType === 'danger' ? 'exclamation-triangle-fill' : 'check-circle-fill' ?> me-2"></i>
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="bi bi-people-fill" style="font-size: 2rem; color: #667eea;"></i>
                <div class="stat-number"><?= $totalContacts ?></div>
                <div class="stat-label">Total Contactos</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-telephone-fill" style="font-size: 2rem; color: #28a745;"></i>
                <div class="stat-number"><?= $withPhone ?></div>
                <div class="stat-label">Con Teléfono</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-envelope-fill" style="font-size: 2rem; color: #ffc107;"></i>
                <div class="stat-number"><?= $withEmail ?></div>
                <div class="stat-label">Con Email</div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-tools"></i> Acciones</span>
                    <div class="action-buttons">
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus-circle"></i> Nuevo Contacto
                        </button>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                        <a href="index.php?export=csv" class="btn btn-light btn-sm">
                            <i class="bi bi-download"></i> Exportar CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, teléfono o email..." value="<?= htmlspecialchars($searchQuery) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <?php if (!empty($searchQuery)): ?>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Contacts Table -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-check"></i> 
                <?php if (!empty($searchQuery)): ?>
                    Resultados de búsqueda para "<?= htmlspecialchars($searchQuery) ?>" (<?= count($filteredContacts) ?> encontrados)
                <?php else: ?>
                    Mis Contactos (<?= count($contacts) ?> total)
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($filteredContacts)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No hay contactos</h5>
                        <p>Comienza añadiendo tu primer contacto</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th style="width: 220px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filteredContacts as $contact): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($contact['name']) ?></strong></td>
                                        <td>
                                            <?php if (!empty($contact['phone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($contact['phone']) ?>" class="text-decoration-none">
                                                    <i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($contact['phone']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($contact['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="text-decoration-none">
                                                    <i class="bi bi-envelope-fill"></i> <?= htmlspecialchars($contact['email']) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $contact['id'] ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $contact['id'] ?>">
                                                <i class="bi bi-trash"></i> Borrar
                                            </button>
                                            <a href="index.php?export=vcard&id=<?= $contact['id'] ?>" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-download"></i> vCard
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $contact['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Contacto</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label"><i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($contact['name']) ?>" required>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                                                            <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($contact['phone']) ?>">
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                                                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($contact['email']) ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal<?= $contact['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header border-danger">
                                                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Confirmar Eliminación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que deseas eliminar a <strong><?= htmlspecialchars($contact['name']) ?></strong>?</p>
                                                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Contact Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Nuevo Contacto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person-fill"></i> Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-telephone-fill"></i> Teléfono</label>
                            <input type="tel" class="form-control" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Añadir Contacto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload"></i> Importar Contactos desde CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="import">
                        
                        <div class="mb-3">
                            <label class="form-label">Selecciona archivo CSV</label>
                            <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                            <small class="text-muted d-block mt-2">
                                El archivo debe tener columnas: Nombre, Teléfono, Email<br>
                                Los contactos duplicados serán ignorados.
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Formato esperado:</strong><br>
                            <code>Name,Phone,Email</code><br>
                            <code>Juan Pérez,555-1234,juan@mail.com</code>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { 
            max-width: 1000px; 
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .card { 
            box-shadow: 0 8px 32px rgba(0,0,0,0.1); 
            border: none;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .alert {
            border-radius: 10px;
            border: none;
            animation: slideIn 0.3s ease;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .hero-section {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .required-star {
            color: #dc3545;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="display-4 mb-3">📇 Rolodex Contact Importer</h1>
            <p class="lead mb-4">Digitaliza tu agenda física con nuestra herramienta web</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="#add-contact" class="btn btn-light btn-lg">
                    <i class="bi bi-person-plus me-2"></i>Añadir Contacto
                </a>
                <a href="<?= $csvFile ?>" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-download me-2"></i>Descargar CSV
                </a>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= strpos($message, 'error') !== false ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?= strpos($message, 'error') !== false ? 'exclamation-triangle' : 'check-circle' ?>-fill me-2"></i>
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="bi bi-people-fill display-4 mb-2"></i>
                    <h3><?= $total ?></h3>
                    <small>Total Contactos</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="bi bi-telephone-fill display-4 mb-2"></i>
                    <h3><?= $withPhone ?></h3>
                    <small>Con Teléfono</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stats-card">
                    <i class="bi bi-envelope-fill display-4 mb-2"></i>
                    <h3><?= $withEmail ?></h3>
                    <small>Con Email</small>
                </div>
            </div>
        </div>
        
        <!-- Contacts List -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Lista de Contactos
                    </h4>
                    <span class="badge bg-white text-dark">
                        <i class="bi bi-person-badge me-1"></i>
                        <?= $total ?> contacto(s)
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($contacts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No hay contactos todavía</h5>
                        <p class="text-muted">Añade tu primer contacto usando el formulario de abajo</p>
                        <a href="#add-contact" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Añadir Primer Contacto
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-person me-1"></i> Nombre</th>
                                    <th><i class="bi bi-telephone me-1"></i> Teléfono</th>
                                    <th><i class="bi bi-envelope me-1"></i> Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $contact): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($contact['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($contact['phone'])): ?>
                                            <a href="tel:<?= htmlspecialchars($contact['phone']) ?>" class="text-decoration-none">
                                                <i class="bi bi-telephone-fill text-success"></i>
                                                <?= htmlspecialchars($contact['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($contact['email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="text-decoration-none">
                                                <i class="bi bi-envelope-fill text-primary"></i>
                                                <?= htmlspecialchars($contact['email']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Add Contact Form -->
        <div class="card" id="add-contact">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Añadir Nuevo Contacto
                </h4>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-person me-1"></i>
                                Nombre Completo <span class="required-star">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Ej: Juan Pérez"
                                   required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">
                                <i class="bi bi-telephone me-1"></i>
                                Teléfono
                            </label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   placeholder="Ej: 555-123-4567">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1"></i>
                                Email
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Ej: juan@ejemplo.com">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            Guardar Contacto
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="text-center mt-4">
            <div class="card bg-light bg-opacity-10">
                <div class="card-body py-3">
                    <small class="text-white">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tip:</strong> También puedes usar la línea de comandos con 
                        <code>php contact-importer.php</code> para una rápida entrada de datos
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll to add contact form
        document.querySelectorAll('a[href="#add-contact"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector('#add-contact').scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Phone formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
