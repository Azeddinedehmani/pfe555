// Fichier public/js/pharmacia.js

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du thème sombre/clair
    initializeTheme();
    
    // Gestion du menu sidebar sur mobile
    initializeSidebar();
    
    // Gestion des alertes flash
    initializeAlerts();
});

/**
 * Initialisation du thème clair/sombre
 */
function initializeTheme() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    
    // Vérifier la préférence de thème enregistrée
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    // Appliquer le thème initial
    if (isDarkMode) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    
    // Gestion du bouton de bascule de thème
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
        });
    }
}

/**
 * Initialisation du menu latéral sur mobile
 */
function initializeSidebar() {
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        
        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleBtn = sidebarToggleBtn.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggleBtn && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }
}

/**
 * Initialisation des alertes flash
 */
function initializeAlerts() {
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
    
    alerts.forEach(alert => {
        // Faire disparaître les alertes après 5 secondes
        setTimeout(() => {
            alert.classList.add('opacity-0');
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
        
        // Ajouter un bouton de fermeture
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
        closeBtn.className = 'ml-auto';
        closeBtn.addEventListener('click', () => {
            alert.remove();
        });
        
        const flexDiv = alert.querySelector('.flex');
        if (flexDiv) {
            flexDiv.appendChild(closeBtn);
        }
    });
}

/**
 * Utilitaire de formatage de monnaie
 */
function formatCurrency(amount, currency = 'DH') {
    return parseFloat(amount).toFixed(2) + ' ' + currency;
}

/**
 * Utilitaire pour les appels API avec fetch
 */
async function apiRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Erreur API:', error);
        throw error;
    }
}

/**
 * Fonction de confirmation personnalisée
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Gestion des codes-barres
 */
function handleBarcodeInput(inputField, callback) {
    let barcodeBuffer = '';
    let lastKeyTime = 0;
    const barcodeTimeBuffer = 50; // temps en ms entre les caractères du scanner
    
    inputField.addEventListener('keypress', function(e) {
        // Si la touche Enter est pressée et que le buffer a des données, c'est probablement un scan
        if (e.key === 'Enter' && barcodeBuffer !== '') {
            e.preventDefault();
            const barcode = barcodeBuffer;
            barcodeBuffer = '';
            callback(barcode);
        }
    });
    
    inputField.addEventListener('keydown', function(e) {
        const currentTime = new Date().getTime();
        
        // Si le temps écoulé depuis la dernière frappe est court, c'est probablement un scanner
        if (currentTime - lastKeyTime <= barcodeTimeBuffer) {
            barcodeBuffer += e.key;
        } else {
            barcodeBuffer = e.key;
        }
        
        lastKeyTime = currentTime;
    });
}