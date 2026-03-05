<!DOCTYPE html>
<html lang="es">
<head>
    <title>✏️ Editar Contacto</title>
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
            max-width: 600px; 
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
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .required-star {
            color: #dc3545;
        }
        .hero-section {
            background: white;
            color: #333;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .hero-section h3 {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h3 class="mb-2"><i class="bi bi-pencil-square"></i> Editar Contacto</h3>
            <p class="text-muted mb-0">Actualiza la información de tu contacto</p>
        </div>
        
        <!-- Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-fill me-2"></i> Editando: <?= htmlspecialchars($contact['name']) ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/contacts/update/<?= $contact['id'] ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person-fill"></i> Nombre Completo <span class="required-star">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="Nombre completo"
                               value="<?= htmlspecialchars($contact['name']) ?>"
                               required 
                               autofocus>
                        <small class="text-muted">Campo requerido</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone-fill"></i> Teléfono
                        </label>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               placeholder="Número de teléfono"
                               value="<?= htmlspecialchars($contact['phone']) ?>">
                        <small class="text-muted">Opcional</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-fill"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               placeholder="Dirección de email"
                               value="<?= htmlspecialchars($contact['email']) ?>">
                        <small class="text-muted">Opcional, debe ser un email válido</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="/contacts" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
