<!DOCTYPE html>
<html lang="es">
<head>
    <title>📇 Mis Contactos - Rolodex Manager</title>
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
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.85rem;
            color: #495057;
        }
        .hero-section {
            background: white;
            color: #333;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
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
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="mb-1"><i class="bi bi-person-rolodex"></i> Mis Contactos</h1>
            <p class="text-muted mb-0">Gestiona y organiza tu agenda de contactos de forma segura y eficiente</p>
        </div>

        <!-- Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="bi bi-people-fill" style="font-size: 2rem; color: #667eea;"></i>
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Contactos</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-telephone-fill" style="font-size: 2rem; color: #28a745;"></i>
                <div class="stat-number"><?= $stats['withPhone'] ?></div>
                <div class="stat-label">Con Teléfono</div>
            </div>
            <div class="stat-card">
                <i class="bi bi-envelope-fill" style="font-size: 2rem; color: #ffc107;"></i>
                <div class="stat-number"><?= $stats['withEmail'] ?></div>
                <div class="stat-label">Con Email</div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-tools"></i> Acciones</span>
                    <div class="action-buttons">
                        <a href="/contacts/create" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Nuevo Contacto
                        </a>
                        <a href="/contacts/import" class="btn btn-light btn-sm">
                            <i class="bi bi-upload"></i> Importar CSV
                        </a>
                        <a href="/contacts/export-csv" class="btn btn-light btn-sm">
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
                        <a href="/contacts" class="btn btn-secondary">
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
                    Resultados para "<?= htmlspecialchars($searchQuery) ?>" (<?= $resultsCount ?> encontrados)
                <?php else: ?>
                    Todos los Contactos (<?= count($contacts) ?>)
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($contacts)): ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No hay contactos</h5>
                        <p>Comienza añadiendo tu primer contacto</p>
                        <a href="/contacts/create" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle"></i> Añadir Contacto
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th style="width: 240px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contacts as $contact): ?>
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
                                        <a href="/contacts/edit/<?= $contact['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="/contacts/delete/<?= $contact['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar a <?= addslashes($contact['name']) ?>?')">
                                            <i class="bi bi-trash"></i> Borrar
                                        </a>
                                        <a href="/contacts/export-vcard/<?= $contact['id'] ?>" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-download"></i> vCard
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { 
            max-width: 900px; 
            padding-top: 2rem;
        }
        .card { 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            border: none;
            border-radius: 12px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            border: none;
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
        }
        .table {
            border-radius: 8px;
            overflow: hidden;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #495057;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .stats-badge {
            background-color: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="mb-3">📇 Gestor de Contactos</h1>
            <p class="mb-4">Convierte tu Rolodex físico en una agenda digital moderna</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="/contacts/create" class="btn btn-light">
                    <i class="bi bi-person-plus"></i> Añadir Contacto
                </a>
                <a href="/contacts/export" class="btn btn-outline-light">
                    <i class="bi bi-download"></i> Exportar CSV
                </a>
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-people-fill me-2"></i>
                        Lista de Contactos
                    </h4>
                    <span class="stats-badge">
                        <i class="bi bi-person-badge me-1"></i>
                        <?= count($contacts) ?> contacto(s)
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($contacts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-person-x display-1 text-muted"></i>
                        <h5 class="text-muted mt-3">No hay contactos todavía</h5>
                        <p class="text-muted">Comienza añadiendo tu primer contacto o usa la línea de comandos:</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="/contacts/create" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Añadir Primer Contacto
                            </a>
                            <button class="btn btn-outline-secondary" onclick="copyCommand()">
                                <i class="bi bi-terminal"></i> Copiar Comando CLI
                            </button>
                        </div>
                        <div id="commandAlert" class="alert alert-info mt-3" style="display: none;">
                            <small><code>php contact-importer.php</code></small>
                        </div>
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
                                        <strong><?= esc($contact['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($contact['phone'])): ?>
                                            <a href="tel:<?= esc($contact['phone']) ?>" class="text-decoration-none">
                                                <i class="bi bi-telephone-fill text-success"></i>
                                                <?= esc($contact['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($contact['email'])): ?>
                                            <a href="mailto:<?= esc($contact['email']) ?>" class="text-decoration-none">
                                                <i class="bi bi-envelope-fill text-primary"></i>
                                                <?= esc($contact['email']) ?>
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
                    
                    <!-- Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <i class="bi bi-people display-4 text-primary"></i>
                                <h5 class="mt-2"><?= count($contacts) ?></h5>
                                <small class="text-muted">Total Contactos</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <?php 
                                $withPhone = count(array_filter($contacts, fn($c) => !empty($c['phone'])));
                                ?>
                                <i class="bi bi-telephone display-4 text-success"></i>
                                <h5 class="mt-2"><?= $withPhone ?></h5>
                                <small class="text-muted">Con Teléfono</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <?php 
                                $withEmail = count(array_filter($contacts, fn($c) => !empty($c['email'])));
                                ?>
                                <i class="bi bi-envelope display-4 text-info"></i>
                                <h5 class="mt-2"><?= $withEmail ?></h5>
                                <small class="text-muted">Con Email</small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer Tips -->
        <div class="text-center mt-4">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <small class="text-muted">
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
        function copyCommand() {
            navigator.clipboard.writeText('php contact-importer.php');
            const alert = document.getElementById('commandAlert');
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
