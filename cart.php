<?php
require_once 'config/config.php';
require_once 'classes/Product.php';

$product = new Product();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-flask me-2"></i>
                AlquimiaTechnologic
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-boxes me-1"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle me-1"></i>Nosotros
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-envelope me-1"></i>Contacto
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-outline-light btn-sm" id="darkModeToggle" title="Modo oscuro">
                            <i class="fas fa-moon"></i>
                        </button>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative active" href="cart.php">
                            <i class="fas fa-shopping-cart fs-5"></i>
                            <span class="badge bg-warning text-dark position-absolute" id="cart-count">0</span>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu shadow-lg border-0">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Mi Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="orders.php">
                                    <i class="fas fa-shopping-bag me-2"></i>Mis Pedidos
                                </a></li>
                                <?php if (isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="admin/dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Panel Admin
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="auth/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">
                                <i class="fas fa-user-plus me-1"></i>Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cart Section -->
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="section-title text-gradient mb-4" data-aos="fade-up">
                        <i class="fas fa-shopping-cart me-3"></i>
                        Tu Carrito de Compras
                    </h1>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4" data-aos="fade-right">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-boxes me-2"></i>
                                Productos en el Carrito
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="cart-items">
                                <!-- Cart items will be loaded here via JavaScript -->
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                                    <h4 class="text-muted mt-3">Tu carrito está vacío</h4>
                                    <p class="text-muted">Agrega algunos productos para comenzar</p>
                                    <a href="products.php" class="btn btn-primary hover-lift">
                                        <i class="fas fa-shopping-bag me-2"></i>
                                        Explorar Productos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-left">
                    <div class="card shadow-lg border-0 sticky-top" style="top: 120px;">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>
                                Resumen de Compra
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Envío:</span>
                                <span class="text-success">Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total:</strong>
                                <strong id="total" class="text-primary">$0</strong>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg hover-lift" onclick="checkout()">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Proceder al Pago
                                </button>
                                <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20completar%20mi%20compra%20del%20carrito%20de%20AlquimiaTechnologic" 
                                   class="btn btn-success btn-lg hover-lift" target="_blank" id="whatsapp-checkout">
                                    <i class="fab fa-whatsapp me-2"></i>
                                    Completar por WhatsApp
                                </a>
                                <button class="btn btn-outline-danger btn-sm hover-lift" onclick="clearCart()">
                                    <i class="fas fa-trash me-2"></i>
                                    Vaciar Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light">
        <div class="container">
            <div class="row py-5">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-flask me-2"></i>
                        AlquimiaTechnologic
                    </h5>
                    <p class="text-muted">
                        Transformando tu día a día con productos únicos y servicios innovadores 
                        que elevan tu estilo de vida.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Productos</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Software</a></li>
                        <li><a href="#" class="text-muted">Aceites Esenciales</a></li>
                        <li><a href="#" class="text-muted">Figuras de Yeso</a></li>
                        <li><a href="#" class="text-muted">Suscripciones</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Empresa</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Sobre Nosotros</a></li>
                        <li><a href="#" class="text-muted">Contacto</a></li>
                        <li><a href="#" class="text-muted">Blog</a></li>
                        <li><a href="#" class="text-muted">Careers</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Soporte</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-muted">Centro de Ayuda</a></li>
                        <li><a href="#" class="text-muted">Política de Privacidad</a></li>
                        <li><a href="#" class="text-muted">Términos</a></li>
                        <li><a href="#" class="text-muted">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Contacto</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Anbato, Ecuador</li>
                        <li><i class="fas fa-phone me-2"></i>+593 983015307</li>
                        <li><i class="fas fa-envelope me-2"></i>kevinmoyolema13@gmail.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2025 AlquimiaTechnologic. Desarrollado por AlquimiaTechnologic. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">Hecho con <i class="fas fa-heart text-danger"></i> en Ecuador</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        AOS.init();
        
        // Cart functionality
        function getCart() {
            try {
                return JSON.parse(localStorage.getItem('cart')) || [];
            } catch (e) {
                console.error('Error reading cart from localStorage', e);
                localStorage.removeItem('cart');
                return [];
            }
        }

        function loadCart() {
            const cart = getCart();
            const cartContainer = document.getElementById('cart-items');
            
            if (cart.length === 0) {
                cartContainer.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">Tu carrito está vacío</h4>
                        <p class="text-muted">Agrega algunos productos para comenzar</p>
                        <a href="products.php" class="btn btn-primary hover-lift">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Productos
                        </a>
                    </div>
                `;
                updateCartSummary();
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                return;
            }
            
            // Cargar productos del carrito con la información almacenada
            let cartHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const product = {
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    image: item.image
                };
                
                const itemTotal = product.price * item.quantity;
                total += itemTotal;
                
                cartHTML += `
                    <div class="row align-items-center mb-3 cart-item" data-id="${item.id}">
                        <div class="col-md-2">
                            <img src="${product.image}" alt="${product.name}" class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${product.name}</h6>
                            <small class="text-muted">ID: ${product.id}</small>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-bold">$${product.price.toLocaleString()}</span>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, -1)">-</button>
                                <input type="number" class="form-control text-center" value="${item.quantity}" min="1" onchange="updateQuantity(${item.id}, this.value - ${item.quantity})">
                                <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-bold">$${itemTotal.toLocaleString()}</span>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartContainer.innerHTML = cartHTML;
            updateCartSummary();
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        }
        
        function updateQuantity(productId, change) {
            let cart = getCart();
            const item = cart.find(item => item.id == productId);
            
            if (item) {
                item.quantity = Math.max(1, item.quantity + change);
                localStorage.setItem('cart', JSON.stringify(cart));
                loadCart();
                updateCartCount();
            }
        }
        
        function removeFromCart(productId) {
            let cart = getCart();
            const newCart = cart.filter(item => item.id != productId);
            localStorage.setItem('cart', JSON.stringify(newCart));
            loadCart();
            updateCartCount();
            showNotification('Producto eliminado del carrito', 'info');
        }
        
        function updateCartSummary() {
            const cart = getCart();
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('subtotal').textContent = `$${total.toLocaleString()}`;
            document.getElementById('total').textContent = `$${total.toLocaleString()}`;
            
            // Update WhatsApp link with cart items
            const cartItems = cart.map(item => `${item.name} x${item.quantity}`).join(', ');
            const whatsappLink = `https://wa.me/593983015307?text=Hola%2C%20quiero%20completar%20mi%20compra%20del%20carrito%20de%20AlquimiaTechnologic%20-%20${encodeURIComponent(cartItems)}%20-%20Total:%20$${total.toLocaleString()}`;
            document.getElementById('whatsapp-checkout').href = whatsappLink;
        }
        
        function clearCart() {
            if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
                localStorage.removeItem('cart');
                loadCart();
                updateCartCount();
                showNotification('Carrito vaciado', 'info');
            }
        }
        
        function checkout() {
            // In a real app, this would redirect to payment gateway
            alert('Funcionalidad de pago en desarrollo. Usa el botón de WhatsApp para completar tu compra.');
        }
        
        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        });
    </script>
</body>
</html> 