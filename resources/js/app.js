/**
 * Pharmacia - Système de Gestion de Pharmacie
 * Script JavaScript principal
 */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializeDropdowns();
    initializeAlerts();
    initializeActiveMenuItems();
    initializeDeleteConfirmations();
    setupThemeToggle();
});

/**
 * Initialisation de la barre latérale (sidebar)
 */
function initializeSidebar() {
    // Gestion du toggle sidebar pour les écrans mobiles
    const sidebarToggler = document.getElementById('sidebarCollapse');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggler && sidebar) {
        sidebarToggler.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Fermer le sidebar quand on clique en dehors
        document.addEventListener('click', function(event) {
            if (
                !sidebar.contains(event.target) && 
                !sidebarToggler.contains(event.target) && 
                sidebar.classList.contains('show')
            ) {
                sidebar.classList.remove('show');
            }
        });
    }
    
    // Gestion des sous-menus
    const dropdownLinks = document.querySelectorAll('.menu-link.has-dropdown');
    
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Trouver l'élément parent et le sous-menu
            const menuItem = this.parentElement;
            
            // Toggle active class
            if (menuItem.classList.contains('active')) {
                menuItem.classList.remove('active');
            } else {
                // Si on veut fermer les autres menus quand on en ouvre un
                // document.querySelectorAll('.menu-item.active').forEach(item => {
                //    if (item !== menuItem) item.classList.remove('active');
                // });
                
                menuItem.classList.add('active');
            }
        });
    });
}

/**
 * Initialisation des dropdowns de Bootstrap
 */
function initializeDropdowns() {
    // Si un élément dropdown existe mais n'est pas initialisé
    const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    
    dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
}

/**
 * Initialisation des alertes (avec auto-fermeture)
 */
function initializeAlerts() {
    // Fermer automatiquement les alertes de succès après 5 secondes
    const alerts = document.querySelectorAll('.alert:not(.alert-danger):not(.alert-warning)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
    
    // Ajout du bouton de fermeture si non présent
    const alertsWithoutButton = document.querySelectorAll('.alert:not(.alert-dismissible)');
    
    alertsWithoutButton.forEach(alert => {
        const closeButton = document.createElement('button');
        closeButton.setAttribute('type', 'button');
        closeButton.className = 'btn-close';
        closeButton.setAttribute('data-bs-dismiss', 'alert');
        closeButton.setAttribute('aria-label', 'Fermer');
        
        alert.classList.add('alert-dismissible');
        alert.appendChild(closeButton);
    });
}

/**
 * Marquer les éléments de menu actifs
 */
function initializeActiveMenuItems() {
    // Récupérer l'URL actuelle
    const currentPath = window.location.pathname;
    
    // Trouver tous les liens de menu
    const menuLinks = document.querySelectorAll('.menu-link');
    
    menuLinks.forEach(link => {
        // Vérifier si le lien correspond au chemin actuel
        if (link.getAttribute('href') === currentPath) {
            // Marquer l'élément comme actif
            link.parentElement.classList.add('active');
            
            // Si le lien est dans un sous-menu, ouvrir le menu parent
            const parentDropdown = link.closest('.menu-dropdown');
            if (parentDropdown) {
                parentDropdown.parentElement.classList.add('active');
            }
        }
    });
}

/**
 * Initialisation des boîtes de dialogue de confirmation
 */
function initializeDeleteConfirmations() {
    // Pour tous les boutons avec une action de suppression
    const deleteButtons = document.querySelectorAll('.delete-btn, [data-action="delete"]');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const confirmMessage = this.getAttribute('data-confirm') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

/**
 * Basculer entre les thèmes clair et sombre
 */
function setupThemeToggle() {
    const themeToggleBtn = document.querySelector('a[href*="theme.toggle"]');
    
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Envoyer une requête AJAX pour basculer le thème
            fetch(this.href, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Basculer le thème dans le frontend
                    const htmlElement = document.documentElement;
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    htmlElement.setAttribute('data-bs-theme', newTheme);
                    
                    // Mettre à jour l'icône
                    const themeIcon = themeToggleBtn.querySelector('i');
                    if (themeIcon) {
                        if (newTheme === 'dark') {
                            themeIcon.className = 'bi bi-sun';
                        } else {
                            themeIcon.className = 'bi bi-moon';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error toggling theme:', error);
            });
        });
    }
}

/**
 * Affiche un toast de notification
 * @param {string} message - Le message à afficher
 * @param {string} type - Le type de notification (success, danger, warning, info)
 */
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    
    if (!toastContainer) {
        // Créer le conteneur de toast s'il n'existe pas
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
    }
    
    // Créer le toast
    const toastId = 'toast-' + new Date().getTime();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    // Contenu du toast
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
    `;
    
    document.getElementById('toast-container').appendChild(toast);
    
    // Initialiser et afficher le toast
    const bsToast = new bootstrap.Toast(toast, {
        autohide: true,
        delay: 5000
    });
    
    bsToast.show();
    
    // Supprimer le toast après qu'il soit caché
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

/**
 * Formater un nombre en devise (€)
 * @param {number} amount - Le montant à formater
 * @returns {string} Le montant formaté
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

/**
 * Formater une date en format français
 * @param {string|Date} date - La date à formater
 * @returns {string} La date formatée
 */
function formatDate(date) {
    if (!date) return '';
    
    const dateObj = date instanceof Date ? date : new Date(date);
    
    return dateObj.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Récupérer les initiales d'un nom
 * @param {string} name - Le nom complet
 * @returns {string} Les initiales
 */
function getInitials(name) {
    if (!name) return '';
    
    return name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase();
}

/**
 * Fonction debounce pour limiter l'exécution de fonctions fréquentes
 * @param {Function} func - La fonction à exécuter
 * @param {number} wait - Délai d'attente en ms
 * @returns {Function} Fonction avec debounce
 */
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

/**
 * Affiche ou masque un indicateur de chargement
 * @param {boolean} show - Afficher ou masquer
 */
function toggleLoading(show) {
    let loadingOverlay = document.querySelector('.loading-overlay');
    
    if (show) {
        if (!loadingOverlay) {
            loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(loadingOverlay);
        }
        
        loadingOverlay.style.display = 'flex';
    } else if (loadingOverlay) {
        loadingOverlay.style.display = 'none';
    }
}

/**
 * Ajout d'écouteur pour les formulaires de recherche (empêche les soumissions vides)
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchForms = document.querySelectorAll('form[role="search"]');
    
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            
            if (searchInput && searchInput.value.trim() === '') {
                e.preventDefault();
            }
        });
    });
});

/**
 * Gérer les requêtes AJAX
 * @param {string} url - L'URL de la requête
 * @param {Object} options - Options fetch
 * @returns {Promise} La promesse de la requête
 */
function ajaxRequest(url, options = {}) {
    // Définir les options par défaut
    const defaultOptions = {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    };
    
    // Fusionner les options par défaut avec les options fournies
    const fetchOptions = { ...defaultOptions, ...options };
    
    // Si le corps est un objet et la méthode n'est pas GET, convertir en JSON
    if (
        fetchOptions.body && 
        typeof fetchOptions.body === 'object' && 
        !(fetchOptions.body instanceof FormData) &&
        fetchOptions.method !== 'GET'
    ) {
        fetchOptions.body = JSON.stringify(fetchOptions.body);
    }
    
    // Afficher l'indicateur de chargement
    toggleLoading(true);
    
    return fetch(url, fetchOptions)
        .then(response => {
            // Vérifier si la réponse est OK
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            // Déterminer le type de contenu
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text();
            }
        })
        .catch(error => {
            console.error('Ajax Request Error:', error);
            showToast('Une erreur s\'est produite: ' + error.message, 'danger');
            throw error;
        })
        .finally(() => {
            // Masquer l'indicateur de chargement
            toggleLoading(false);
        });
}