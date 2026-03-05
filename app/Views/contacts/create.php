<!DOCTYPE html>
<html lang="es">
<head>
    <title>➕ Nuevo Contacto</title>
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
            <h3 class="mb-2"><i class="bi bi-person-plus-fill"></i> Nuevo Contacto</h3>
            <p class="text-muted mb-0">Añade la información de tu nuevo contacto</p>
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
                <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i> Formulario de Contacto</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/contacts/store">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-person-fill"></i> Nombre Completo <span class="required-star">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="Ej: Juan Pérez García"
                               value="<?= old('name') ?>"
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
                               placeholder="Ej: 555-123-4567 o +34 666 123 456"
                               value="<?= old('phone') ?>">
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
                               placeholder="Ej: juan@ejemplo.com"
                               value="<?= old('email') ?>">
                        <small class="text-muted">Opcional, pero debe ser un email válido</small>
                    </div>
                    
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="/contacts" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle-fill"></i> Guardar Contacto
                        </button>
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
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container { 
            max-width: 600px; 
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
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px 0 0 8px;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h3 class="mb-2">➕ Nuevo Contacto</h3>
            <p class="mb-0">Añade la información de tu nuevo contacto</p>
        </div>
        
        <!-- Main Card -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Formulario de Contacto
                </h4>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="/contacts/store" id="contactForm">
                    <?= csrf_field() ?>
                    
                    <!-- Name Field -->
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <i class="bi bi-person me-1"></i>
                            Nombre Completo <span class="required-star">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name') ?>" 
                                   placeholder="Ej: Juan Pérez"
                                   required>
                        </div>
                        <small class="text-muted">El nombre es obligatorio y debe tener al menos 3 caracteres</small>
                    </div>
                    
                    <!-- Phone Field -->
                    <div class="mb-4">
                        <label for="phone" class="form-label">
                            <i class="bi bi-telephone me-1"></i>
                            Teléfono
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-telephone-fill"></i>
                            </span>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?= old('phone') ?>" 
                                   placeholder="Ej: 555-123-4567"
                                   pattern="[0-9\-\+\(\)\s]+">
                        </div>
                        <small class="text-muted">Opcional. Formato: 555-123-4567 o (555) 123-4567</small>
                    </div>
                    
                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope me-1"></i>
                            Correo Electrónico
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope-fill"></i>
                            </span>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email') ?>" 
                                   placeholder="Ej: juan@ejemplo.com">
                        </div>
                        <small class="text-muted">Opcional. Debe ser un email válido si se proporciona</small>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card bg-light mb-4">
                        <div class="card-body py-3">
                            <h6 class="mb-3">
                                <i class="bi bi-lightning-fill me-1"></i>
                                Acciones Rápidas
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillSample()">
                                        <i class="bi bi-person-badge me-1"></i>
                                        Rellenar Ejemplo
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="clearForm()">
                                        <i class="bi bi-trash me-1"></i>
                                        Limpiar Formulario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="/contacts" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-1"></i>
                            Guardar Contacto
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Tips -->
        <div class="text-center mt-4">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Tip:</strong> Para añadir múltiples contactos rápidamente, 
                        considera usar la línea de comandos: <code>php contact-importer.php</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            
            if (name.length < 3) {
                e.preventDefault();
                alert('El nombre debe tener al menos 3 caracteres');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Guardando...';
            submitBtn.disabled = true;
        });
        
        // Fill sample data
        function fillSample() {
            document.getElementById('name').value = 'Juan Pérez García';
            document.getElementById('phone').value = '555-123-4567';
            document.getElementById('email').value = 'juan.perez@ejemplo.com';
        }
        
        // Clear form
        function clearForm() {
            document.getElementById('contactForm').reset();
        }
        
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
