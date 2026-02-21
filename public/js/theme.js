/**
 * Theme Toggle - Light/Dark Mode
 */

const themeToggle = document.getElementById('themeToggle');
const htmlElement = document.documentElement;

// Initialize theme
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        htmlElement.classList.add('dark');
        updateThemeToggleButton();
    } else {
        document.body.classList.remove('dark-mode');
        htmlElement.classList.remove('dark');
        updateThemeToggleButton();
    }
}

// Update toggle button
function updateThemeToggleButton() {
    if (themeToggle) {
        const isDark = document.body.classList.contains('dark-mode');
        themeToggle.textContent = isDark ? '☀️' : '🌙';
    }
}

// Toggle theme
function toggleTheme() {
    const isDark = document.body.classList.contains('dark-mode');
    
    if (isDark) {
        document.body.classList.remove('dark-mode');
        htmlElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        document.body.classList.add('dark-mode');
        htmlElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
    
    updateThemeToggleButton();
}

// Event listener
if (themeToggle) {
    themeToggle.addEventListener('click', toggleTheme);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initTheme);