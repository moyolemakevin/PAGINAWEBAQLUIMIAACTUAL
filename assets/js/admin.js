// Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle
    initializeSidebar();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize notifications
    initializeNotifications();
    
    // Initialize data tables if present
    initializeDataTables();
    
    // Initialize charts if present
    initializeCharts();
    
    // Initialize file upload functionality
    initializeFileUpload();
});

// Sidebar functionality
function initializeSidebar() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }
    
    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarCollapse.contains(e.target)) {
                sidebar.classList.remove('active');
                content.classList.remove('active');
            }
        }
    });
}

// Tooltips initialization
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Notifications system
function initializeNotifications() {
    // Create notification container if it doesn't exist
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        document.body.appendChild(container);
    }
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Hide and remove notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, duration);
}

// Data Tables initialization
function initializeDataTables() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
}

// Charts initialization
function initializeCharts() {
    // Chart.js charts
    if (typeof Chart !== 'undefined') {
        initializeSalesChart();
        initializeUserChart();
        initializeProductChart();
    }
}

function initializeSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                datasets: [{
                    label: 'Ventas',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

function initializeUserChart() {
    const ctx = document.getElementById('userChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Nuevos', 'Activos', 'Inactivos'],
                datasets: [{
                    data: [300, 150, 100],
                    backgroundColor: ['#27ae60', '#3498db', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

function initializeProductChart() {
    const ctx = document.getElementById('productChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Software', 'Aceites', 'Figuras', 'Suscripciones'],
                datasets: [{
                    label: 'Productos',
                    data: [65, 59, 80, 81],
                    backgroundColor: ['#3498db', '#27ae60', '#f39c12', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// File Upload functionality
function initializeFileUpload() {
    console.log('Inicializando sistema de subida de archivos...');
    
    const uploadAreas = document.querySelectorAll('.upload-area');
    console.log('Áreas de subida encontradas:', uploadAreas.length);
    
    uploadAreas.forEach((area, index) => {
        const input = area.querySelector('input[type="file"]');
        const preview = area.parentElement.querySelector('[id$="ImagePreview"], [id$="imagePreview"]');
        const folder = area.getAttribute('data-folder') || 'products';
        
        console.log(`Área ${index + 1}:`, {
            hasInput: !!input,
            hasPreview: !!preview,
            folder: folder,
            previewId: preview ? preview.id : 'no-id'
        });
        
        if (input && preview) {
            // Click to upload
            area.addEventListener('click', (e) => {
                e.preventDefault();
                input.click();
            });
            
            // Drag and drop
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });
            
            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });
            
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    console.log('Archivos arrastrados:', files.length);
                    handleFileUpload(files, preview, folder);
                }
            });

            // File selection
            input.addEventListener('change', (e) => {
                console.log('Archivos seleccionados:', e.target.files.length);
                handleFileUpload(input, preview, folder);
            });
            
            console.log(`✅ Área ${index + 1} configurada correctamente`);
        } else {
            console.warn(`⚠️ Área ${index + 1} no tiene input o preview:`, {
                input: input ? input.id : 'no-input',
                preview: preview ? preview.id : 'no-preview'
            });
        }
    });
    
    // Verificar que csrfToken esté disponible
    if (typeof csrfToken === 'undefined') {
        console.error('❌ csrfToken no está definido');
    } else {
        console.log('✅ csrfToken disponible');
    }
}

function handleFileUpload(source, preview, folder = 'products') {
    let files = [];
    if (source instanceof FileList || Array.isArray(source)) {
        files = Array.from(source);
    } else if (source && source.files) {
        files = Array.from(source.files);
    }
    if (files.length === 0) return;
    
    
    // Create FormData for upload
    const formData = new FormData();
    files.forEach(file => {
        formData.append('images[]', file);
    });
    formData.append('csrf_token', csrfToken);
    formData.append('folder', folder);
    
    // Show upload progress
    const progressBar = document.createElement('div');
    progressBar.className = 'upload-progress';
    progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
    preview.appendChild(progressBar);
    
    // Upload files
    fetch('../admin/upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Upload response:', data);
        progressBar.remove();
        
        if (data.success) {
            // Display uploaded images
            data.files.forEach(file => {
                const item = createImagePreviewItem(file.thumbnail, file.original);
                preview.appendChild(item);
            });
            
            // Update hidden input with all images in preview
            updateImagesJson();
            const hiddenInput = preview.parentElement.querySelector('[id$="ImagesJson"]');
            if (hiddenInput) {
                try {
                    const images = JSON.parse(hiddenInput.value || '[]');
                    if (hiddenInput.id === 'logoImagesJson') {
                        const urlInput = document.getElementById('logoUrl');
                        if (urlInput) urlInput.value = images[0] || '';
                    } else if (hiddenInput.id === 'faviconImagesJson') {
                        const urlInput = document.getElementById('faviconUrl');
                        if (urlInput) urlInput.value = images[0] || '';
                    } else if (hiddenInput.id === 'heroImagesJson') {
                        const urlInput = document.getElementById('heroImageUrl');
                        if (urlInput) urlInput.value = images[0] || '';
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                }
            }
            
            showNotification('Imágenes subidas correctamente', 'success');
            if (data.errors && data.errors.length > 0) {
                showNotification('Algunas imágenes no se subieron: ' + data.errors.join('; '), 'warning');
            }
        } else {
            const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
            showNotification('Error al subir imágenes: ' + msg, 'error');
            // Limpiar preview si falla
            preview.innerHTML = '';
            updateImagesJson();
        }
    })
    .catch(error => {
        console.error('Upload error details:', error);
        progressBar.remove();
        showNotification('Error al subir imágenes: ' + error.message, 'error');
        preview.innerHTML = '';
        updateImagesJson();
    });
}

function createImagePreviewItem(thumbnail, original) {
    const item = document.createElement('div');
    item.className = 'image-preview-item';

    const img = document.createElement('img');
    let src = thumbnail || original;
    if (src) {
        src = src.startsWith('http') ? src : '../' + src.replace(/^\/+/, '');
    }
    img.src = src;
    img.alt = 'Preview';
    img.dataset.original = original;

    // Fallback si la miniatura falla al cargar
    img.onerror = () => {
        if (original && img.src !== original) {
            const originalSrc = original.startsWith('http') ? original : '../' + original.replace(/^\/+/, '');
            img.src = originalSrc;
        }
    };

    const removeBtn = document.createElement('button');
    removeBtn.className = 'remove-btn';
    removeBtn.innerHTML = '<i class="fas fa-times"></i>';
    removeBtn.onclick = () => {
        item.remove();
        updateImagesJson();
    };

    item.appendChild(img);
    item.appendChild(removeBtn);

    return item;
}

function updateImagesJson() {
    const previews = document.querySelectorAll('.image-preview');
    previews.forEach(preview => {
        const images = Array.from(preview.querySelectorAll('img')).map(img => {
            // Usar dataset.original si está disponible, sino extraer de src
            let imagePath = img.dataset.original;
            if (!imagePath) {
                imagePath = img.src;
                // Remover la parte '../' si existe
                if (imagePath.includes('../')) {
                    imagePath = imagePath.split('../')[1];
                }
            }
            return imagePath;
        });
        
        let hiddenInput = preview.parentElement.querySelector('[id$="ImagesJson"], [id$="image_json"], [id$="images_json"]');
        if (!hiddenInput) {
            const container = preview.closest('.mb-3, form');
            if (container) {
                hiddenInput = container.querySelector('[id$="ImagesJson"], [id$="image_json"], [id$="images_json"]');
            }
        }
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(images);
            // Para configuraciones (logo y favicon)
            if (hiddenInput.id === 'logoImagesJson') {
                const urlInput = document.getElementById('logoUrl');
                if (urlInput) urlInput.value = images[0] || '';
            } else if (hiddenInput.id === 'faviconImagesJson') {
                const urlInput = document.getElementById('faviconUrl');
                if (urlInput) urlInput.value = images[0] || '';
            } else if (hiddenInput.id === 'heroImagesJson') {
                const urlInput = document.getElementById('heroImageUrl');
                if (urlInput) urlInput.value = images[0] || '';
            }
        }
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// AJAX helper functions
function ajaxRequest(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error en la solicitud', 'error');
    });
}

// File upload handling
function previewFile(input, previewId) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.querySelector('.table');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

// Bulk actions
function selectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkActions();
}

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (bulkActions) {
        bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }
}

// Delete confirmation
function confirmDelete(message = '¿Estás seguro de que quieres eliminar este elemento?') {
    return confirm(message);
}

// Export functionality
function exportData(format) {
    const table = document.querySelector('.table');
    if (!table) return;
    
    let data = [];
    const headers = [];
    const headerCells = table.querySelectorAll('thead th');
    
    headerCells.forEach(cell => {
        headers.push(cell.textContent.trim());
    });
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('td');
        cells.forEach(cell => {
            rowData.push(cell.textContent.trim());
        });
        data.push(rowData);
    });
    
    if (format === 'csv') {
        exportToCSV(headers, data);
    } else if (format === 'excel') {
        exportToExcel(headers, data);
    }
}

function exportToCSV(headers, data) {
    let csv = headers.join(',') + '\n';
    data.forEach(row => {
        csv += row.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function exportToExcel(headers, data) {
    // This would require a library like SheetJS
    showNotification('Exportación a Excel en desarrollo', 'info');
}

// Print functionality
function printPage() {
    window.print();
}

// Theme switcher
function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark-theme');
    
    const theme = body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('admin-theme', theme);
    
    showNotification(`Tema cambiado a ${theme === 'dark' ? 'oscuro' : 'claro'}`, 'info');
}

// Load saved theme
function loadTheme() {
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
}

// Initialize theme on load
loadTheme();

// Auto-save functionality
function initializeAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                saveFormData(form);
            });
        });
    });
}

function saveFormData(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    localStorage.setItem(`autosave_${form.id}`, JSON.stringify(data));
    showNotification('Datos guardados automáticamente', 'info', 2000);
}

function loadFormData(form) {
    const saved = localStorage.getItem(`autosave_${form.id}`);
    if (saved) {
        const data = JSON.parse(saved);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
            }
        });
    }
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S: Save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('.btn-save');
            if (saveButton) {
                saveButton.click();
            }
        }
        
        // Ctrl/Cmd + N: New
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            const newButton = document.querySelector('.btn-new');
            if (newButton) {
                newButton.click();
            }
        }
        
        // Escape: Close modals
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }
    });
}

// Initialize all functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializeTooltips();
    initializeNotifications();
    initializeDataTables();
    initializeCharts();
    initializeSearch();
    initializeAutoSave();
    initializeKeyboardShortcuts();
    
    // Load saved form data
    const forms = document.querySelectorAll('form[data-autosave]');
    forms.forEach(form => {
        loadFormData(form);
    });
}); 