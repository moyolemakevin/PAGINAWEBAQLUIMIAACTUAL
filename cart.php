<?php
require_once __DIR__ . '/config/bootstrap.php';

$product = new Product();
$userModel = new User();
$userData = isLoggedIn() ? $userModel->getUserById($_SESSION['user_id']) : null;
$authEnabled = !isPublicAuthDisabled();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo assetUrl('assets/css/style.css'); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo assetUrl(getSafeSiteImageSetting('favicon_url', 'assets/images/favicon.jpeg')); ?>">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
    <?php
$currentPage = 'cart';
include __DIR__ . '/partials/navbar.php';
?>
<main id="main-content">

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
                            
                            <?php if (isLoggedIn()): ?>
                                <div class="mb-3">
                                    <h6 class="fw-bold">Datos del comprador</h6>
                                    <div class="small text-muted mb-2">Completa o actualiza tus datos de contacto.</div>
                                    <input type="text" id="buyer-name" class="form-control mb-2" placeholder="Nombre completo" value="<?php echo htmlspecialchars(trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''))); ?>">
                                    <input type="email" id="buyer-email" class="form-control mb-2" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>">
                                    <input type="text" id="buyer-phone" class="form-control mb-2" placeholder="Teléfono (WhatsApp)" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                                    <input type="text" id="buyer-id" class="form-control mb-2" placeholder="RUC / Cédula (opcional)" value="<?php echo htmlspecialchars($userData['document'] ?? ''); ?>">
                                    <textarea id="buyer-address" class="form-control" rows="2" placeholder="Dirección de envío"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                                </div>
                            <?php elseif ($authEnabled): ?>
                                <div class="alert alert-info mb-3">
                                    Debes iniciar sesión o registrarte para completar la compra.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning mb-3">
                                    La compra con cuenta está deshabilitada temporalmente en este dominio por seguridad.
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2">
                                <?php if (isLoggedIn()): ?>
                                    <button class="btn btn-primary btn-lg hover-lift" onclick="checkout()">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Proceder al Pago
                                    </button>
                                <?php elseif ($authEnabled): ?>
                                    <a class="btn btn-primary btn-lg hover-lift" href="auth/login.php?redirect=<?php echo urlencode('cart.php'); ?>">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Iniciar sesión para pagar
                                    </a>
                                    <a class="btn btn-outline-secondary btn-lg hover-lift" href="auth/register.php?redirect=<?php echo urlencode('cart.php'); ?>">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Registrarme
                                    </a>
                                <?php else: ?>
                                    <a class="btn btn-primary btn-lg hover-lift" href="contact.php">
                                        <i class="fas fa-envelope me-2"></i>
                                        Contactar para comprar
                                    </a>
                                <?php endif; ?>
                                <a href="https://wa.me/593983015307?text=Hola%2C%20quiero%20completar%20mi%20compra%20del%20carrito%20de%20Moyoalquimia"
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

    </main>
<?php include __DIR__ . '/partials/footer.php'; ?>

    <!-- Scripts -->
    <?php include __DIR__ . '/partials/scripts.php'; ?>
    
    <script>
        const userLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        const authEnabled = <?php echo $authEnabled ? 'true' : 'false'; ?>;

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
            
            let cartHTML = '';

            cart.forEach((item) => {
                const itemTotal = item.price * item.quantity;
                
                cartHTML += `
                    <div class="row align-items-center mb-3 cart-item" data-id="${item.id}">
                        <div class="col-md-2">
                            <img src="${item.image}" alt="${item.name}" class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-md-4">
                            <h6 class="mb-1">${item.name}</h6>
                            <small class="text-muted">ID: ${item.id}</small>
                        </div>
                        <div class="col-md-2">
                            <span class="fw-bold">$${item.price.toLocaleString()}</span>
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
                if (typeof updateCartCount === 'function') updateCartCount();
            }
        }
        
        function removeFromCart(productId) {
            let cart = getCart();
            const newCart = cart.filter(item => item.id != productId);
            localStorage.setItem('cart', JSON.stringify(newCart));
            loadCart();
            if (typeof updateCartCount === 'function') updateCartCount();
            if (typeof showNotification === 'function') {
                showNotification('Producto eliminado del carrito', 'info');
            }
        }
        
        function updateCartSummary() {
            const cart = getCart();
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('subtotal').textContent = `$${total.toLocaleString()}`;
            document.getElementById('total').textContent = `$${total.toLocaleString()}`;
            
            const cartItems = cart.map(item => `${item.name} x${item.quantity}`).join(', ');
            const whatsappLink = `https://wa.me/593983015307?text=Hola%2C%20quiero%20completar%20mi%20compra%20del%20carrito%20de%20Moyoalquimia%20-%20${encodeURIComponent(cartItems)}%20-%20Total:%20$${total.toLocaleString()}`;
            document.getElementById('whatsapp-checkout').href = whatsappLink;
        }
        
        function clearCart() {
            if (confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
                localStorage.removeItem('cart');
                loadCart();
                if (typeof updateCartCount === 'function') updateCartCount();
                if (typeof showNotification === 'function') {
                    showNotification('Carrito vaciado', 'info');
                }
            }
        }
        
        function checkout() {
            if (!userLoggedIn) {
                if (!authEnabled) {
                    window.location.href = 'contact.php';
                    return;
                }
                window.location.href = 'auth/login.php?redirect=' + encodeURIComponent('cart.php');
                return;
            }
            const cart = getCart();
            if (cart.length === 0) {
                if (typeof showNotification === 'function') showNotification('Tu carrito está vacío', 'warning');
                return;
            }
            const buyerName = document.getElementById('buyer-name').value.trim();
            const buyerEmail = document.getElementById('buyer-email').value.trim();
            const buyerPhone = document.getElementById('buyer-phone').value.trim();
            if (!buyerName || !buyerEmail || !buyerPhone) {
                if (typeof showNotification === 'function') showNotification('Completa nombre, correo y teléfono.', 'warning');
                return;
            }
            alert('Funcionalidad de pago en desarrollo. Usa el botón de WhatsApp para completar tu compra.');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            if (window.AOS) {
                AOS.init();
            }
            loadCart();
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        });
    </script>
</body>
</html>
