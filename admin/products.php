<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

// Verificar si es administrador
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL . '/auth/login.php');
}

$product = new Product();
$category = new Category();

// Obtener productos
$products = $product->getAllProducts();
$categories = $category->getAllCategories();
$showAddModal = isset($_GET['action']) && $_GET['action'] === 'add';
$editProductId = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Productos - Admin <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <h3>
                    <i class="fas fa-flask"></i>
                    Admin Panel
                </h3>
            </div>
            
            <ul class="list-unstyled components">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="active">
                    <a href="products.php">
                        <i class="fas fa-box"></i>
                        Productos
                    </a>
                </li>
                <li>
                    <a href="categories.php">
                        <i class="fas fa-tags"></i>
                        Categorías
                    </a>
                </li>
                <li>
                    <a href="orders.php">
                        <i class="fas fa-shopping-cart"></i>
                        Pedidos
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <i class="fas fa-users"></i>
                        Usuarios
                    </a>
                </li>
                <li>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i>
                        Mensajes
                    </a>
                </li>
                <li>
                    <a href="team.php">
                        <i class="fas fa-users-cog"></i>
                        Equipo
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        Configuración
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <a href="../index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-eye me-2"></i>Ver Sitio
                </a>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i>Salir
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../auth/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h3 mb-0">Gestionar Productos</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus me-2"></i>Nuevo Producto
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-box me-2"></i>
                            Lista de Productos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Precio</th>
                                        <th>Stock</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $prod): ?>
                                        <tr>
                                            <td><?php echo $prod['id']; ?></td>
                                            <td>
                                                <?php 
                                                $imageUrl = '../assets/images/placeholder.jpg';
                                                if (!empty($prod['images'])) {
                                                    $images = json_decode($prod['images'], true);
                                                    if ($images && is_array($images) && !empty($images[0])) {
                                                        $imageUrl = $images[0];
                                                    }
                                                }
                                                
                                                // Verificar si la imagen existe y no está vacía
                                                $imgPath = strpos($imageUrl, 'http') === 0 ? $imageUrl : '../' . ltrim($imageUrl, '/');
                                                if (strpos($imageUrl, 'http') === 0) {
                                                    $localPath = null;
                                                } else {
                                                    $cleanPath = preg_replace('#^(\.\./)+#', '', ltrim($imageUrl, '/'));
                                                    $localPath = realpath(__DIR__ . '/../' . $cleanPath) ?: (__DIR__ . '/../' . $cleanPath);
                                                }
                                                
                                                // Si es una imagen local, verificar que existe y no esté vacía
                                                if ($localPath && (!file_exists($localPath) || filesize($localPath) < 100)) {
                                                    $imageUrl = '../assets/images/placeholder.jpg';
                                                    $imgPath = '../assets/images/placeholder.jpg';
                                                }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>"
                                                     alt="<?php echo htmlspecialchars($prod['name']); ?>"
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                     onerror="this.src='../assets/images/placeholder.jpg'">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($prod['name']); ?></strong>
                                                <?php if (isset($prod['is_featured']) && $prod['is_featured']): ?>
                                                    <span class="badge bg-warning ms-2">Destacado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $cat = $category->getCategoryById($prod['category_id']);
                                                echo $cat ? $cat['name'] : 'Sin categoría';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($prod['discount_percentage']) && $prod['discount_percentage'] > 0): ?>
                                                    <span class="text-decoration-line-through text-muted">
                                                        $<?php echo number_format($prod['price'], 0, ',', '.'); ?>
                                                    </span>
                                                    <br>
                                                    <span class="text-success fw-bold">
                                                        $<?php echo number_format($prod['price'] * (1 - $prod['discount_percentage'] / 100), 0, ',', '.'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="fw-bold">$<?php echo number_format($prod['price'], 0, ',', '.'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Disponible</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">Activo</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editProduct(<?php echo $prod['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteProduct(<?php echo $prod['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control" name="price" min="0" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" min="0" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descuento (%)</label>
                                    <input type="number" class="form-control" name="discount_percentage" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Producto</label>
                                    <div class="upload-section">
                                        <!-- Botón para seleccionar imagen -->
                                        <div class="mb-2">
                                            <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" class="form-control" style="display: none;">
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('imageUpload').click()">
                                                <i class="fas fa-folder-open me-2"></i>Seleccionar Imágenes
                                            </button>
                                        </div>
                                        
                                        <!-- Preview de imagen seleccionada -->
                                        <div id="selectedImagePreview" class="mb-3" style="display: none;">
                                            <h6>Imagen Seleccionada:</h6>
                                            <div class="selected-images-container"></div>
                                            <button type="button" class="btn btn-success mt-2" onclick="uploadSelectedImages()">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>Subir Imágenes
                                            </button>
                                        </div>
                                        
                                        <!-- Lista de imágenes subidas -->
                                        <div id="productImagePreview" class="image-preview mt-2"></div>
                                    </div>
                                    <input type="hidden" name="images_json" id="imagesJson">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" id="featured">
                                        <label class="form-check-label" for="featured">
                                            Producto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Guardar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre del Producto</label>
                                    <input type="text" class="form-control" name="name" id="editProductName" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="category_id" id="editProductCategory" required>
                                        <option value="">Seleccionar categoría</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control" name="price" id="editProductPrice" min="0" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Stock</label>
                                    <input type="number" class="form-control" name="stock" id="editProductStock" min="0" value="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Descuento (%)</label>
                                    <input type="number" class="form-control" name="discount_percentage" id="editProductDiscount" min="0" max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Descripción</label>
                                    <textarea class="form-control" name="description" id="editProductDescription" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Producto</label>
                                    <div class="upload-section">
                                        <!-- Botón para seleccionar imagen -->
                                        <div class="mb-2">
                                            <input type="file" id="editImageUpload" name="images[]" multiple accept="image/*" class="form-control" style="display: none;">
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('editImageUpload').click()">
                                                <i class="fas fa-folder-open me-2"></i>Seleccionar Imágenes
                                            </button>
                                        </div>
                                        
                                        <!-- Preview de imagen seleccionada -->
                                        <div id="editSelectedImagePreview" class="mb-3" style="display: none;">
                                            <h6>Imagen Seleccionada:</h6>
                                            <div class="selected-images-container"></div>
                                            <button type="button" class="btn btn-success mt-2" onclick="uploadSelectedImages()">
                                                <i class="fas fa-cloud-upload-alt me-2"></i>Subir Imágenes
                                            </button>
                                        </div>
                                        
                                        <!-- Lista de imágenes subidas -->
                                        <div id="editImagePreview" class="image-preview mt-2"></div>
                                    </div>
                                    <input type="hidden" name="images_json" id="editImagesJson">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="featured" id="editProductFeatured">
                                        <label class="form-check-label" for="editProductFeatured">
                                            Producto Destacado
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="updateProduct()">Actualizar Producto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
        // CSRF Token
        const csrfToken = '<?php echo generateCSRFToken(); ?>';
        
        // Inicializar sistema de subida cuando se cargan los modales
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar sistema de subida
            initializeFileUpload();
            
            // Configurar eventos para selección de imágenes
            setupImageSelection();
            
            // Reinicializar cuando se abren los modales
            const addModal = document.getElementById('addProductModal');
            const editModal = document.getElementById('editProductModal');
            
            if (addModal) {
                addModal.addEventListener('shown.bs.modal', function() {
                    console.log('Modal de agregar producto abierto');
                    initializeFileUpload();
                    setupImageSelection();
                });
            }
            
            if (editModal) {
                editModal.addEventListener('shown.bs.modal', function() {
                    console.log('Modal de editar producto abierto');
                    initializeFileUpload();
                    setupImageSelection();
                });
            }
        });
        
        // Configurar selección de imágenes
        function setupImageSelection() {
            const imageUpload = document.getElementById('imageUpload');
            const editImageUpload = document.getElementById('editImageUpload');
            
            if (imageUpload) {
                imageUpload.addEventListener('change', function(e) {
                    handleImageSelection(e.target.files, 'selectedImagePreview', 'selected-images-container');
                });
            }
            
            if (editImageUpload) {
                editImageUpload.addEventListener('change', function(e) {
                    handleImageSelection(e.target.files, 'editSelectedImagePreview', 'editSelectedImagesContainer');
                });
            }
        }
        
        // Manejar selección de imágenes
        function handleImageSelection(files, previewId, containerId) {
            const preview = document.getElementById(previewId);
            const container = preview.querySelector('.selected-images-container') || preview.querySelector('#' + containerId);
            
            if (files.length === 0) {
                preview.style.display = 'none';
                return;
            }
            
            preview.style.display = 'block';
            container.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const item = document.createElement('div');
                    item.className = 'selected-image-item';
                    item.style.cssText = 'display: inline-block; margin: 5px; text-align: center;';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;';
                    img.alt = file.name;
                    
                    const name = document.createElement('div');
                    name.textContent = file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name;
                    name.style.cssText = 'font-size: 12px; margin-top: 5px; color: #666;';
                    
                    item.appendChild(img);
                    item.appendChild(name);
                    container.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        }
        
        // Subir imágenes seleccionadas
        function uploadSelectedImages() {
            const imageUpload = document.getElementById('imageUpload');
            const editImageUpload = document.getElementById('editImageUpload');
            
            let files = null;
            let preview = null;
            
            if (imageUpload && imageUpload.files.length > 0) {
                files = imageUpload.files;
                preview = document.getElementById('productImagePreview');
            } else if (editImageUpload && editImageUpload.files.length > 0) {
                files = editImageUpload.files;
                preview = document.getElementById('editImagePreview');
            }
            
            if (!files || files.length === 0) {
                alert('Por favor selecciona al menos una imagen');
                return;
            }
            
            // Crear FormData para subida
            const formData = new FormData();
            Array.from(files).forEach(file => {
                formData.append('images[]', file);
            });
            formData.append('csrf_token', csrfToken);
            formData.append('folder', 'products');
            
            // Mostrar progreso
            const progressBar = document.createElement('div');
            progressBar.className = 'upload-progress';
            progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
            preview.appendChild(progressBar);
            
            // Subir archivos
            fetch('../admin/upload_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.remove();
                
                if (data.success) {
                    // Mostrar imágenes subidas
                    data.files.forEach(file => {
                        const item = createImagePreviewItem(file.thumbnail, file.original);
                        preview.appendChild(item);
                    });
                    
                    // Actualizar campos ocultos
                    updateImagesJson();
                    
                    // Limpiar selección
                    if (imageUpload) {
                        imageUpload.value = '';
                        document.getElementById('selectedImagePreview').style.display = 'none';
                    }
                    if (editImageUpload) {
                        editImageUpload.value = '';
                        document.getElementById('editSelectedImagePreview').style.display = 'none';
                    }
                    
                    showNotification('Imágenes subidas correctamente', 'success');
                } else {
                    const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
                    showNotification('Error al subir imágenes: ' + msg, 'error');
                }
            })
            .catch(error => {
                progressBar.remove();
                showNotification('Error al subir imágenes: ' + error.message, 'error');
                console.error('Upload error:', error);
            });
        }
        
        // Product management functions
        function editProduct(productId) {
            // Load product data via AJAX
            const formData = new FormData();
            formData.append('action', 'get_product');
            formData.append('id', productId);
            formData.append('csrf_token', csrfToken);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    document.getElementById('editProductId').value = product.id;
                    document.getElementById('editProductName').value = product.name;
                    document.getElementById('editProductCategory').value = product.category_id;
                    document.getElementById('editProductPrice').value = product.price;
                    document.getElementById('editProductStock').value = product.stock_quantity || 0;
                    document.getElementById('editProductDiscount').value = product.discount_percentage || 0;
                    document.getElementById('editProductDescription').value = product.description;
                    document.getElementById('editProductFeatured').checked = product.is_featured == 1;
                    
                    // Cargar imágenes si existen
                    if (product.images) {
                        try {
                            const images = JSON.parse(product.images);
                            const preview = document.getElementById('editImagePreview');
                            preview.innerHTML = '';
                            
                            images.forEach(imageData => {
                                const item = document.createElement('div');
                                item.className = 'image-preview-item';

                                const img = document.createElement('img');
                                // Manejar tanto objetos como strings
                                let imageUrl = imageData;
                                if (typeof imageData === 'object' && imageData.original) {
                                    imageUrl = imageData.original;
                                }
                                
                                img.src = imageUrl.startsWith('http') ? imageUrl : '../' + imageUrl.replace(/^\/+/, '');
                                img.dataset.original = imageUrl;
                                img.alt = 'Product image';
                                
                                const removeBtn = document.createElement('button');
                                removeBtn.className = 'remove-btn';
                                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                                removeBtn.onclick = () => {
                                    item.remove();
                                    updateImagesJson();
                                };
                                
                                item.appendChild(img);
                                item.appendChild(removeBtn);
                                preview.appendChild(item);
                            });
                            
                            document.getElementById('editImagesJson').value = product.images;
                        } catch (e) {
                            console.error('Error parsing images:', e);
                        }
                    }
                    
                    const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del producto');
            });
        }
        
        function saveProduct() {
            const form = document.getElementById('addProductForm');
            const formData = new FormData(form);
            formData.append('action', 'create');
            formData.append('csrf_token', csrfToken);
            
            // Obtener imágenes JSON
            const imagesJson = document.getElementById('imagesJson').value || '[]';
            formData.append('images_json', imagesJson);
            
            // Obtener stock (si no existe, usar 0)
            if (!formData.get('stock')) {
                formData.append('stock', '0');
            }
            
            // Obtener featured
            formData.append('featured', form.querySelector('input[name="featured"]').checked ? 1 : 0);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    modal.hide();
                    form.reset();
                    document.getElementById('productImagePreview').innerHTML = '';
                    document.getElementById('imagesJson').value = '';
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el producto');
            });
        }
        
        function updateProduct() {
            const form = document.getElementById('editProductForm');
            const formData = new FormData(form);
            formData.append('action', 'update');
            formData.append('csrf_token', csrfToken);
            
            // Obtener imágenes JSON
            const imagesJson = document.getElementById('editImagesJson').value || '[]';
            formData.append('images_json', imagesJson);
            
            // Obtener stock (si no existe, usar 0)
            if (!formData.get('stock')) {
                formData.append('stock', '0');
            }
            
            // Obtener featured
            formData.append('featured', form.querySelector('input[name="featured"]').checked ? 1 : 0);
            
            fetch('process_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el producto');
            });
        }
        
        function deleteProduct(productId) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', productId);
                formData.append('csrf_token', csrfToken);
                
                fetch('process_product.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                });
            }
        }
        
    </script>
</body>
</html>