// Main JavaScript file for AlquimiaTechnologic - Enhanced Version
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeCart();
    initializeEventListeners();
    initializeAnimations();
    initializeScrollEffects();
    initializeLazyLoading();
    initializeParallax();
    initializeCounters();
    initializeTypingEffect();
    initializeDarkMode();

    // Add loading screen
    hideLoadingScreen();
});

// Enhanced Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function initializeCart() {
    updateCartCount();
    updateCartDisplay();
    animateCartIcon();
}

function updateCartCount() {
    const cartCount = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
        cartCountElement.style.display = cartCount > 0 ? 'flex' : 'none';
        
        // Add bounce animation when count changes
        if (cartCount > 0) {
            cartCountElement.style.animation = 'bounce 0.6s ease-out';
            setTimeout(() => {
                cartCountElement.style.animation = '';
            }, 600);
        }
    }
}

function addToCart(productId, name, price, image, quantity = 1) {
    try {
        cart = JSON.parse(localStorage.getItem('cart')) || [];
    } catch (e) {
        console.error('Failed to read cart from localStorage', e);
        cart = cart || [];
    }

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

    try {
        localStorage.setItem('cart', JSON.stringify(cart));
    } catch (e) {
        console.error('Failed to save cart to localStorage', e);
    }

    updateCartCount();
    updateCartDisplay();
    showNotification('🛒 Producto agregado al carrito', 'success');
    createFloatingCartIcon();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id != productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    updateCartDisplay();
    showNotification('🗑️ Producto eliminado del carrito', 'info');
}

function updateCartQuantity(productId, quantity) {
    const item = cart.find(item => item.id == productId);
    if (item) {
        if (quantity <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = quantity;
            localStorage.setItem('cart', JSON.stringify(cart));
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
    showNotification('🧹 Carrito vaciado', 'info');
}

function updateCartDisplay() {
    // If the current page defines a loadCart() helper use it
    if (typeof window.loadCart === 'function') {
        window.loadCart();
        return;
    }

    const cartContainer = document.getElementById('cart-items');
    const cartData = cart || JSON.parse(localStorage.getItem('cart')) || [];

    if (cartContainer) {
        if (cartData.length === 0) {
            cartContainer.innerHTML =
                '<p class="text-center text-muted">Tu carrito está vacío</p>';
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

// Enhanced Event listeners
function initializeEventListeners() {
    // Enhanced add to cart with loading states
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
            e.preventDefault();
            const button = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
            const productId = button.getAttribute('data-product-id') || button.getAttribute('onclick')?.match(/\d+/)?.[0];
            const name = button.getAttribute('data-product-name');
            const price = parseFloat(button.getAttribute('data-product-price')) || 0;
            const image = button.getAttribute('data-product-image');
            const quantity = parseInt(button.getAttribute('data-quantity')) || 1;
            
            if (productId) {
                // Add loading state with ripple effect
                addRippleEffect(button, e);
                button.disabled = true;
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Agregando...';
                button.classList.add('loading');
                
                // Simulate API call delay
                setTimeout(() => {
                    addToCart(productId, name, price, image, quantity);
                    
                    // Reset button with success animation
                    button.innerHTML = '<i class="fas fa-check me-2"></i>¡Agregado!';
                    button.classList.remove('loading');
                    button.classList.add('success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('success');
                        button.disabled = false;
                    }, 1500);
                }, 800);
            }
        }
    });
    
    // Enhanced search with autocomplete
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const query = this.value.trim();
            if (query.length > 2) {
                showSearchSuggestions(query);
            } else {
                hideSearchSuggestions();
            }
        }, 300));
    });
    
    // Enhanced newsletter with validation
    const newsletterForms = document.querySelectorAll('form[id*="newsletter"]');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (validateEmail(email)) {
                const button = this.querySelector('button[type="submit"]');
                const originalHTML = button.innerHTML;
                
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suscribiendo...';
                button.disabled = true;
                
                setTimeout(() => {
                    showNotification('🎉 ¡Bienvenido a nuestra comunidad!', 'success');
                    this.reset();
                    
                    button.innerHTML = '<i class="fas fa-check me-2"></i>¡Suscrito!';
                    button.classList.add('btn-success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('btn-success');
                        button.disabled = false;
                    }, 2000);
                }, 1500);
            } else {
                showNotification('❌ Por favor ingresa un email válido', 'danger');
            }
        });
    });
    
    // Enhanced scroll to top
    const scrollToTopBtn = createScrollToTopButton();
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Enhanced form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
}

// Enhanced Animations
function initializeAnimations() {
    // Enhanced intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const animationType = element.getAttribute('data-aos') || 'fade-in-up';
                
                element.classList.add('animate-in');
                element.style.animationDelay = element.getAttribute('data-aos-delay') || '0ms';
                
                observer.unobserve(element);
            }
        });
    }, observerOptions);
    
    // Observe elements with animation attributes
    const elementsToAnimate = document.querySelectorAll('[data-aos], .product-card, .category-card, .feature-box');
    elementsToAnimate.forEach(element => {
        observer.observe(element);
    });
    
    // Add hover animations to cards
    const cards = document.querySelectorAll('.product-card, .category-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });
}

// Scroll Effects
function initializeScrollEffects() {
    let ticking = false;
    
    function updateScrollEffects() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        // Parallax effect for hero section
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroSection.style.transform = `translateY(${rate}px)`;
        }
        
        // Navbar transparency
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        
        ticking = false;
    }
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateScrollEffects);
            ticking = true;
        }
    });
}

// Enhanced Lazy Loading
function initializeLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Create placeholder with loading animation
                img.style.filter = 'blur(5px)';
                img.style.transition = 'filter 0.3s';
                
                img.src = img.dataset.src;
                img.addEventListener('load', () => {
                    img.style.filter = 'blur(0)';
                    img.classList.add('loaded');
                });
                
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Parallax Effects
function initializeParallax() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    });
}

// Counter Animation
function initializeCounters() {
    const counters = document.querySelectorAll('.counter');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.textContent);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current);
                }, 16);
                
                counterObserver.unobserve(counter);
            }
        });
    });
    
    counters.forEach(counter => counterObserver.observe(counter));
}

// Typing Effect
function initializeTypingEffect() {
    const typingElements = document.querySelectorAll('.typing-effect');
    
    typingElements.forEach(element => {
        const text = element.textContent;
        element.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        // Start typing when element is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    typeWriter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(element);
    });
}

// Dark Mode Toggle
function initializeDarkMode() {
    const toggle = document.getElementById('darkModeToggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedMode = localStorage.getItem('darkMode');

    if (savedMode === 'enabled' || (savedMode === null && prefersDark)) {
        document.body.classList.add('dark-mode');
        if (toggle) toggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    if (toggle) {
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
                toggle.innerHTML = '<i class="fas fa-sun"></i>';
            } else {
                localStorage.setItem('darkMode', 'disabled');
                toggle.innerHTML = '<i class="fas fa-moon"></i>';
            }
        });
    }
}

// Enhanced Notifications
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type} position-fixed shadow-lg`;
    notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 320px;
        border-radius: 15px;
        border: none;
        animation: slideInRight 0.3s ease-out;
        backdrop-filter: blur(10px);
    `;
    
    // Add icon based on type
    let icon = '';
    let bgColor = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle me-2"></i>';
            bgColor = 'linear-gradient(135deg, #10b981, #059669)';
            break;
        case 'danger':
            icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
            bgColor = 'linear-gradient(135deg, #ef4444, #dc2626)';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-circle me-2"></i>';
            bgColor = 'linear-gradient(135deg, #f59e0b, #d97706)';
            break;
        default:
            icon = '<i class="fas fa-info-circle me-2"></i>';
            bgColor = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
    }
    
    notification.style.background = bgColor;
    notification.style.color = 'white';
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            ${icon}
            <span class="flex-grow-1">${message}</span>
            <button type="button" class="btn-close btn-close-white ms-3" aria-label="Close"></button>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(notification);
    
    // Add close functionality
    const closeButton = notification.querySelector('.btn-close');
    closeButton.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Enhanced Utility Functions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type;
    const isRequired = field.hasAttribute('required');
    
    let isValid = true;
    let errorMessage = '';
    
    if (isRequired && !value) {
        isValid = false;
        errorMessage = 'Este campo es requerido';
    } else if (fieldType === 'email' && value && !validateEmail(value)) {
        isValid = false;
        errorMessage = 'Ingresa un email válido';
    } else if (fieldType === 'tel' && value && !/^\+?[\d\s-()]+$/.test(value)) {
        isValid = false;
        errorMessage = 'Ingresa un teléfono válido';
    }
    
    if (isValid) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        clearFieldError(field);
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Enhanced Visual Effects
function addRippleEffect(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

function createFloatingCartIcon() {
    const floatingIcon = document.createElement('div');
    floatingIcon.innerHTML = '<i class="fas fa-shopping-cart"></i>';
    floatingIcon.style.cssText = `
        position: fixed;
        bottom: 20px;
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

function createScrollToTopButton() {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-arrow-up"></i>';
    button.className = 'scroll-to-top';
    button.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 20px;
        cursor: pointer;
        z-index: 1000;
        opacity: 0;
        transform: translateY(100px);
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
    `;
    
    button.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add show class styles
    const style = document.createElement('style');
    style.textContent = `
        .scroll-to-top.show {
            opacity: 1;
            transform: translateY(0);
        }
        .scroll-to-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(button);
    return button;
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

function hideLoadingScreen() {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.remove();
            }, 500);
        }, 1000);
    }
}

function showSearchSuggestions(query) {
    // This would typically make an API call to get suggestions
    console.log('Searching for:', query);
}

function hideSearchSuggestions() {
    const suggestions = document.querySelector('.search-suggestions');
    if (suggestions) {
        suggestions.remove();
    }
}

// Add custom CSS animations
const customAnimations = document.createElement('style');
customAnimations.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes ripple {
        to { transform: scale(2); opacity: 0; }
    }
    
    @keyframes floatUp {
        0% { transform: translateY(0) scale(1); opacity: 1; }
        50% { transform: translateY(-50px) scale(1.1); opacity: 0.8; }
        100% { transform: translateY(-100px) scale(0.8); opacity: 0; }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .animate-in {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    
    .navbar.scrolled {
        background: rgba(99, 102, 241, 0.98) !important;
        backdrop-filter: blur(20px);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(customAnimations); 