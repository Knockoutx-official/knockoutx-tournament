/**
 * Knockoutx - Main Application File
 * All app logic and event listeners
 */

// Check if user is logged in
function checkUserLogin() {
    const authToken = localStorage.getItem('authToken');
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    
    if (authToken) {
        // User is logged in
        if (loginBtn) loginBtn.innerHTML = '<a href="dashboard.html">Dashboard</a>';
        if (signupBtn) signupBtn.innerHTML = '<button onclick="logout()">Logout</button>';
    }
}

// Mobile menu toggle
document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
});

// Login button
document.getElementById('loginBtn')?.addEventListener('click', function() {
    window.location.href = 'auth/login.html';
});

// Sign up button
document.getElementById('signupBtn')?.addEventListener('click', function() {
    window.location.href = 'auth/signup.html';
});

// Get started button
document.querySelector('button:contains("Get Started Now")')?.addEventListener('click', function() {
    const isLoggedIn = localStorage.getItem('authToken');
    if (isLoggedIn) {
        window.location.href = 'dashboard.html';
    } else {
        window.location.href = 'auth/signup.html';
    }
});

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Knockoutx App Initialized');
    checkUserLogin();
});

// Logout function
function logout() {
    localStorage.removeItem('authToken');
    localStorage.removeItem('userEmail');
    window.location.href = 'index.html';
}

// Utility: Format currency (Indian Rupees)
function formatINR(amount) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(amount);
}

// Utility: Show alert
function showAlert(message, type = 'info') {
    // Using browser alert for now, replace with better UI later
    console.log(`[${type.toUpperCase()}] ${message}`);
    alert(message);
}