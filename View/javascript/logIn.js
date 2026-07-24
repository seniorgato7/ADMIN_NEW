/**
 * logIn.js - Resident Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.querySelector('.login-form');
    const loginBtn = document.querySelector('.btn-primary');

    // 1. Inject Loader Styles and Modal HTML automatically
    const injectAuthModal = () => {
        if (document.getElementById('authModal')) return;

        const modalDiv = document.createElement('div');
        modalDiv.id = 'authModal';

        Object.assign(modalDiv.style, {
            display: 'none',
            position: 'fixed',
            inset: '0',
            background: 'rgba(15, 23, 42, 0.98)',
            zIndex: '10000',
            flexDirection: 'column',
            justifyContent: 'center',
            alignItems: 'center',
            backdropFilter: 'blur(10px)',
            fontFamily: 'sans-serif'
        });

        modalDiv.innerHTML = `
            <style>
                @keyframes spinGold { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                .auth-loader {
                    width: 60px; height: 60px;
                    border: 6px solid #1e293b;
                    border-bottom-color: #d49006;
                    border-radius: 50%;
                    animation: spinGold 1s linear infinite;
                    margin-bottom: 25px;
                }
            </style>
            <div class="auth-loader"></div>
            <div style="color:#d49006; font-weight:800; text-transform:uppercase; letter-spacing:3px; font-size:14px;">Authenticating User</div>
            <p style="color:#64748b; font-size:12px; margin-top:10px;">Verifying credentials with Imperial House records...</p>
        `;
        document.body.appendChild(modalDiv);
    };

    injectAuthModal();

    // 2. Form Submission Handling
    if (loginForm) {
        loginForm.addEventListener('submit', () => {
            const authModal = document.getElementById('authModal');
            if (authModal) {
                authModal.style.display = 'flex';
            }
            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.style.opacity = '0.7';
                loginBtn.innerText = 'AUTHENTICATING...';
            }
        });
    }

    // 3. Subtle Input Animations
    const inputs = document.querySelectorAll('.login-form input');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.style.transform = 'scale(1.02)';
            input.parentElement.style.transition = 'transform 0.2s ease';
        });
        input.addEventListener('blur', () => {
            input.parentElement.style.transform = 'scale(1)';
        });
    });
});