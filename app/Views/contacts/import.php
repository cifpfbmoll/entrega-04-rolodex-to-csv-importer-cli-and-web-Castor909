<!DOCTYPE html>
<html lang="es">
<head>
    <title>📥 Importar Contactos</title>
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
            max-width: 700px; 
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
        .file-upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-upload-area:hover {
            border-color: #764ba2;
            background: #f0f1ff;
        }
        .file-upload-area.drag-over {
            border-color: #764ba2;
            background: #e8ebff;
        }
        .file-input {
            display: none;
        }
        .format-example {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.85rem;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h3 class="mb-2"><i class="bi bi-upload"></i> Importar Contactos</h3>
            <p class="text-muted mb-0">Carga múltiples contactos desde un archivo CSV</p>
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
                <h5 class="mb-0"><i class="bi bi-file-earmark-csv me-2"></i> Selecciona tu archivo CSV</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/contacts/import" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="csv_file" class="form-label">
                            <i class="bi bi-file-csv"></i> Archivo CSV
                        </label>
                        <div class="file-upload-area" id="uploadArea">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: #667eea; margin-bottom: 1rem;"></i>
                            <h6 class="mb-2">Arrastra tu archivo aquí</h6>
                            <p class="text-muted mb-0">O haz clic para seleccionar</p>
                            <input type="file" 
                                   class="file-input" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv,text/csv" 
                                   required>
                        </div>
                        <div id="fileName" class="mt-2"></div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Solo archivos CSV (máximo tamaño recomendado: 10 MB)
                        </small>
                    </div>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="bi bi-lightbulb-fill me-2"></i> Formato esperado:
                        </h6>
                        <p class="mb-2">Tu archivo CSV debe tener las siguientes columnas en la primera fila:</p>
                        <div class="format-example">
Name,Phone,Email
Juan García,555-1234,juan@mail.com
María López,555-5678,maria@mail.com
                        </div>
                        <p class="mt-3 mb-0">
                            <small>
                                <span class="badge bg-warning text-dark me-2">Nota:</span>
                                Los contactos con nombres duplicados serán ignorados automáticamente.<br>
                                Los campos Teléfono y Email son opcionales.
                            </small>
                        </p>
                    </div>

                    <div class="alert alert-secondary">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-triangle me-2"></i> Validaciones:
                        </h6>
                        <ul class="mb-0">
                            <li>Nombre: Campo obligatorio</li>
                            <li>Email: Debe ser válido si se proporciona (ejemplo@domain.com)</li>
                            <li>Teléfono: Acepta cualquier formato</li>
                        </ul>
                    </div>
                    
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                        <a href="/contacts" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="bi bi-upload"></i> Importar Contactos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('csv_file');
        const fileNameDiv = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');

        // Click to upload
        uploadArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                updateFileName();
            }
        });

        // File input change
        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                const fileSize = (fileInput.files[0].size / 1024).toFixed(2) + ' KB';
                
                fileNameDiv.innerHTML = `
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>${fileName}</strong> (${fileSize})
                    </div>
                `;
                
                submitBtn.disabled = false;
            } else {
                fileNameDiv.innerHTML = '';
                submitBtn.disabled = true;
            }
        }
    </script>
</body>
</html>
