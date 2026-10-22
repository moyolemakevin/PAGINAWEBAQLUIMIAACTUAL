// Cart helpers
let cart = [];

function readCartFromStorage() {
    try {
        const stored = localStorage.getItem('cart');
        return stored ? JSON.parse(stored) : [];
    } catch (e) {
        localStorage.removeItem('cart');
        return [];
    }
}

function writeCartToStorage(items) {
    try {
        localStorage.setItem('cart', JSON.stringify(items));
    } catch (e) {
        // ignore storage errors
    }
}

function initializeCart() {
    cart = readCartFromStorage();
    updateCartCount();
    updateCartDisplay();
    animateCartIcon();
}

function updateCartCount() {
    cart = readCartFromStorage();
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElements = document.querySelectorAll('[data-cart-count]');
    cartCountElements.forEach((cartCountElement) => {
        cartCountElement.textContent = cartCount;
        cartCountElement.style.display = cartCount > 0 ? 'flex' : 'none';

        if (cartCount > 0) {
            cartCountElement.style.animation = 'bounce 0.6s ease-out';
            setTimeout(() => {
                cartCountElement.style.animation = '';
            }, 600);
        }
    });
}

function addToCart(productId, name, price, image, quantity = 1) {
    cart = readCartFromStorage();
    const existingItem = cart.find(item => item.id == productId);
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: price,
            image: image,
            quantity: quantity,
            timestamp: new Date().toISOString()
        });
    }

    writeCartToStorage(cart);
    updateCartCount();
    updateCartDisplay();

    if (typeof showNotification === 'function') {
        showNotification('Producto agregado al carrito', 'success');
    }
    createFloatingCartIcon();
}

function removeFromCart(productId) {
    cart = readCartFromStorage().filter(item => item.id != productId);
    writeCartToStorage(cart);
    updateCartCount();
    updateCartDisplay();
    if (typeof showNotification === 'function') {
        showNotification('Producto eliminado del carrito', 'info');
    }
}

function updateCartQuantity(productId, quantity) {
    cart = readCartFromStorage();
    const item = cart.find(item => item.id == productId);
    if (item) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = quantity;
            writeCartToStorage(cart);
            updateCartCount();
            updateCartDisplay();
        }
    }
}

function clearCart() {
    cart = [];
    localStorage.removeItem('cart');
    updateCartCount();
    updateCartDisplay();
    if (typeof showNotification === 'function') {
        showNotification('Carrito vaciado', 'info');
    }
}

function updateCartDisplay() {
    // If the current page defines a loadCart() helper use it
    if (typeof window.loadCart === 'function') {
        window.loadCart();
        return;
    }

    const cartContainer = document.getElementById('cart-items');
    const cartData = cart.length ? cart : readCartFromStorage();
    if (cartContainer) {
        if (cartData.length === 0) {
            cartContainer.innerHTML =
                '<p class="text-center text-muted">Tu carrito esta vacio</p>';
            return;
        }
        let html = '';
        cartData.forEach(item => {
            const total = item.price * item.quantity;
            html += `<div class="d-flex align-items-center mb-2">
                        <img src="${item.image}" alt="${item.name}" style="width:40px;height:40px;object-fit:cover" class="me-2 rounded">
                        <div class="flex-grow-1">${item.name} x${item.quantity}</div>
                        <small class="text-muted">$${total.toLocaleString()}</small>
                    </div>`;
        });
        cartContainer.innerHTML = html;
    }
}

function animateCartIcon() {
    const cartIcon = document.querySelector('.fa-shopping-cart');
    if (cartIcon) {
        cartIcon.addEventListener('click', function() {
            this.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    }
}

function createFloatingCartIcon() {
    const compactViewport = window.matchMedia
        && window.matchMedia('(max-width: 991.98px)').matches;
    const bottomOffset = compactViewport ? 88 : 20;
    const floatingIcon = document.createElement('div');
    floatingIcon.innerHTML = '<i class="fas fa-shopping-cart"></i>';
    floatingIcon.style.cssText = `
        position: fixed;
        bottom: ${bottomOffset}px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        z-index: 1000;
        animation: floatUp 2s ease-out forwards;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
    `;

    document.body.appendChild(floatingIcon);
    setTimeout(() => floatingIcon.remove(), 2000);
}
