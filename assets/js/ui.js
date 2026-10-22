// UI helpers and event listeners
function initializeEventListeners() {
    const reducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    // Enhanced add to cart with loading states
    document.addEventListener('click', function(e) {
        const button = e.target.classList.contains('add-to-cart')
            ? e.target
            : e.target.closest('.add-to-cart');

        if (!button) {
            return;
        }

        e.preventDefault();
        const productId = button.getAttribute('data-product-id') || button.getAttribute('onclick')?.match(/\d+/)?.[0];
        const name = button.getAttribute('data-product-name');
        const price = parseFloat(button.getAttribute('data-product-price')) || 0;
        const image = button.getAttribute('data-product-image');
        const quantity = parseInt(button.getAttribute('data-quantity'), 10) || 1;

        if (productId) {
            addRippleEffect(button, e);
            button.disabled = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Agregando...';
            button.classList.add('loading');

            setTimeout(() => {
                if (typeof addToCart === 'function') {
                    addToCart(productId, name, price, image, quantity);
                }

                button.innerHTML = '<i class="fas fa-check me-2"></i>Agregado!';
                button.classList.remove('loading');
                button.classList.add('success');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('success');
                    button.disabled = false;
                }, 1500);
            }, 800);
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

    // Newsletter with backend save
    const newsletterForms = document.querySelectorAll('form[id*="newsletter"]');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const nameInput = this.querySelector('input[name="name"]');
            const email = emailInput ? emailInput.value.trim() : '';
            const name = nameInput ? nameInput.value.trim() : '';

            if (!validateEmail(email)) {
                showNotification('Por favor ingresa un email válido', 'danger');
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            const originalHTML = button ? button.innerHTML : '';
            if (button) {
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Suscribiendo...';
                button.disabled = true;
            }

            fetch('newsletter_submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, name })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showNotification('¡Suscripción registrada!', 'success');
                    this.reset();
                    if (button) {
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Suscrito!';
                        button.classList.add('btn-success');
                        setTimeout(() => {
                            button.innerHTML = originalHTML;
                            button.classList.remove('btn-success');
                            button.disabled = false;
                        }, 2000);
                    }
                } else {
                    showNotification(data.message || 'No se pudo suscribir', 'danger');
                    if (button) {
                        button.innerHTML = originalHTML;
                        button.disabled = false;
                    }
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('Error al suscribir: ' + err.message, 'danger');
                if (button) {
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                }
            });
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
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: reducedMotion ? 'auto' : 'smooth',
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

    // Quick view backdrop tuning
    const quickViewModal = document.getElementById('quickViewModal');
    if (quickViewModal) {
        document.body.classList.remove('quick-view-open');
        quickViewModal.addEventListener('show.bs.modal', () => {
            document.body.classList.add('quick-view-open');
        });
        quickViewModal.addEventListener('hidden.bs.modal', () => {
            document.body.classList.remove('quick-view-open');
        });
    }
}

function showNotification(message, type = 'info') {
    const compactViewport = window.matchMedia
        && window.matchMedia('(max-width: 575.98px)').matches;
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    });

    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type} position-fixed shadow-lg`;
    notification.style.cssText = `
        top: ${compactViewport ? '12px' : '20px'};
        right: ${compactViewport ? '12px' : '20px'};
        z-index: 9999;
        min-width: ${compactViewport ? '0' : '320px'};
        width: ${compactViewport ? 'calc(100vw - 24px)' : 'auto'};
        max-width: ${compactViewport ? 'unset' : '420px'};
        border-radius: 15px;
        border: none;
        animation: slideInRight 0.3s ease-out;
        backdrop-filter: blur(10px);
    `;

    let icon = '';
    let bgColor = '';
    switch (type) {
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

    document.body.appendChild(notification);

    const closeButton = notification.querySelector('.btn-close');
    closeButton.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    });

    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

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
        errorMessage = 'Ingresa un email valido';
    } else if (fieldType === 'tel' && value && !/^\+?[\d\s-()]+$/.test(value)) {
        isValid = false;
        errorMessage = 'Ingresa un telefono valido';
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
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

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

function createScrollToTopButton() {
    const reducedMotion = window.matchMedia
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
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
            behavior: reducedMotion ? 'auto' : 'smooth'
        });
    });

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
