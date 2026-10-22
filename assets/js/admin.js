// Admin Panel JavaScript

function initializeSidebar() {
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (sidebarCollapse && sidebar && content) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }

    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar || !sidebarCollapse) return;
            if (!sidebar.contains(e.target) && !sidebarCollapse.contains(e.target)) {
                sidebar.classList.remove('active');
                content.classList.remove('active');
            }
        }
    });
}

function initializeTooltips() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function initializeNotifications() {
    if (!document.getElementById('notification-container')) {
        const container = document.createElement('div');
        container.id = 'notification-container';
        document.body.appendChild(container);
    }
}

function showNotification(message, type = 'info', duration = 5000) {
    const container = document.getElementById('notification-container');
    if (!container) return;

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;

    container.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, duration);
}

function initializeDataTables() {
    if (typeof window.jQuery === 'undefined' || !window.jQuery.fn) {
        return;
    }
    if (typeof window.jQuery.fn.DataTable !== 'undefined') {
        window.jQuery('.datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
}

function initializeCharts() {
    if (typeof Chart !== 'undefined') {
        initializeSalesChart();
        initializeUserChart();
        initializeProductChart();
    }
}

function getDashboardMetrics() {
    if (typeof window.dashboardMetrics === 'object' && window.dashboardMetrics) {
        return window.dashboardMetrics;
    }
    return null;
}

function initializeSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    const metrics = getDashboardMetrics();
    const salesMetric = metrics && metrics.sales ? metrics.sales : null;
    const labels = salesMetric && Array.isArray(salesMetric.labels) && salesMetric.labels.length
        ? salesMetric.labels
        : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
    const data = salesMetric && Array.isArray(salesMetric.data) && salesMetric.data.length
        ? salesMetric.data
        : [12, 19, 3, 5, 2, 3];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ventas',
                data: data,
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

function initializeUserChart() {
    const ctx = document.getElementById('visitsChart');
    if (!ctx) return;

    const metrics = getDashboardMetrics();
    const visitsMetric = metrics && metrics.visits ? metrics.visits : null;
    const labels = visitsMetric && Array.isArray(visitsMetric.labels) && visitsMetric.labels.length
        ? visitsMetric.labels
        : ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
    const data = visitsMetric && Array.isArray(visitsMetric.data) && visitsMetric.data.length
        ? visitsMetric.data
        : [120, 90, 150, 110, 130, 100];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visitas',
                data: data,
                backgroundColor: 'rgba(39, 174, 96, 0.6)',
                borderColor: '#27ae60'
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

function initializeProductChart() {
    const ctx = document.getElementById('productChart');
    if (!ctx) return;

    const metrics = getDashboardMetrics();
    const productMetric = metrics && metrics.topProducts ? metrics.topProducts : null;
    const labels = productMetric && Array.isArray(productMetric.labels) && productMetric.labels.length
        ? productMetric.labels
        : ['Software', 'Aceites', 'Figuras', 'Suscripciones'];
    const data = productMetric && Array.isArray(productMetric.data) && productMetric.data.length
        ? productMetric.data
        : [65, 59, 80, 81];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Unidades vendidas',
                data: data,
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

function initializeFileUpload() {
    const uploadAreas = document.querySelectorAll('.upload-area');

    uploadAreas.forEach(area => {
        const input = area.querySelector('input[type="file"]');
        const preview = area.parentElement.querySelector('[id$="ImagePreview"], [id$="imagePreview"]');
        const folder = area.getAttribute('data-folder') || 'products';

        if (!input || !preview) {
            return;
        }

        area.addEventListener('click', (e) => {
            e.preventDefault();
            input.click();
        });

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
                handleFileUpload(files, preview, folder);
            }
        });

        input.addEventListener('change', () => {
            handleFileUpload(input, preview, folder);
        });
    });
}

function handleFileUpload(source, preview, folder = 'products') {
    let files = [];
    if (source instanceof FileList || Array.isArray(source)) {
        files = Array.from(source);
    } else if (source && source.files) {
        files = Array.from(source.files);
    }
    if (files.length === 0) return;

    if (typeof csrfToken === 'undefined') {
        showNotification('No se encontro el token CSRF', 'error');
        return;
    }

    const formData = new FormData();
    files.forEach(file => {
        formData.append('images[]', file);
    });
    formData.append('csrf_token', csrfToken);
    formData.append('folder', folder);

    const progressBar = document.createElement('div');
    progressBar.className = 'upload-progress';
    progressBar.innerHTML = '<div class="upload-progress-bar" style="width: 0%"></div>';
    preview.appendChild(progressBar);

    fetch('../admin/upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        progressBar.remove();

        if (data.success) {
            data.files.forEach(file => {
                const item = createImagePreviewItem(file.thumbnail, file.original);
                preview.appendChild(item);
            });

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

            showNotification('Imagenes subidas correctamente', 'success');
            if (data.errors && data.errors.length > 0) {
                showNotification('Algunas imagenes no se subieron: ' + data.errors.join('; '), 'warning');
            }
        } else {
            const msg = data.errors && data.errors.length ? data.errors.join('; ') : data.message;
            showNotification('Error al subir imagenes: ' + msg, 'error');
            preview.innerHTML = '';
            updateImagesJson();
        }
    })
    .catch(error => {
        progressBar.remove();
        showNotification('Error al subir imagenes: ' + error.message, 'error');
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
          const normalizeInputPath = (path) => {
              if (!path) return '';
              let out = path.trim();
              while (out.startsWith('../')) {
                  out = out.substring(3);
              }
              if (out.startsWith(window.location.origin)) {
                  out = out.substring(window.location.origin.length);
              }
              out = out.replace(/^\/+/, '');
              return out;
          };

          const images = Array.from(preview.querySelectorAll('img')).map(img => {
              let imagePath = img.dataset.original;
              if (!imagePath) {
                  imagePath = img.src;
                  if (imagePath.includes('../')) {
                    imagePath = imagePath.split('../')[1];
                }
            }
            return imagePath;
        });

        const selector = [
            '[id$="ImagesJson"]',
            '[id$="imagesJson"]',
            '[id$="image_json"]',
            '[id$="images_json"]',
            '[name$="ImagesJson"]',
            '[name$="imagesJson"]',
            '[name$="image_json"]',
            '[name$="images_json"]'
        ].join(', ');

        let hiddenInput = preview.parentElement.querySelector(selector);
        if (!hiddenInput) {
            const container = preview.closest('.mb-3, form, .card, .modal-body');
            if (container) {
                hiddenInput = container.querySelector(selector);
            }
        }
          if (hiddenInput) {
              hiddenInput.value = JSON.stringify(images);
              if (hiddenInput.id === 'logoImagesJson') {
                  const urlInput = document.getElementById('logoUrl');
                  if (urlInput) urlInput.value = normalizeInputPath(images[0]) || '';
              } else if (hiddenInput.id === 'faviconImagesJson') {
                  const urlInput = document.getElementById('faviconUrl');
                  if (urlInput) urlInput.value = normalizeInputPath(images[0]) || '';
              } else if (hiddenInput.id === 'heroImagesJson') {
                  const urlInput = document.getElementById('heroImageUrl');
                  if (urlInput) urlInput.value = normalizeInputPath(images[0]) || '';
              }
          }
      });
  }

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

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.querySelector('.table');
            if (!table) return;
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

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

function confirmDelete(message = 'Estas seguro de que quieres eliminar este elemento?') {
    return confirm(message);
}

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
    showNotification('Exportacion a Excel en desarrollo', 'info');
}

function printPage() {
    window.print();
}

function toggleTheme() {
    const body = document.body;
    body.classList.toggle('dark-theme');

    const theme = body.classList.contains('dark-theme') ? 'dark' : 'light';
    localStorage.setItem('admin-theme', theme);

    showNotification(`Tema cambiado a ${theme === 'dark' ? 'oscuro' : 'claro'}`, 'info');
}

function loadTheme() {
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
}

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
    showNotification('Datos guardados automaticamente', 'info', 2000);
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

function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('.btn-save');
            if (saveButton) {
                saveButton.click();
            }
        }

        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            const newButton = document.querySelector('.btn-new');
            if (newButton) {
                newButton.click();
            }
        }

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

document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
    initializeSidebar();
    initializeTooltips();
    initializeNotifications();
    initializeDataTables();
    initializeCharts();
    initializeSearch();
    initializeAutoSave();
    initializeKeyboardShortcuts();
    initializeFileUpload();

    const forms = document.querySelectorAll('form[data-autosave]');
    forms.forEach(form => {
        loadFormData(form);
    });
});
