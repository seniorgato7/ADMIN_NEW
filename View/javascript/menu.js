/* =========================
    IMPERIAL HOUSE NAVIGATION
========================= */
document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.left-menu-item');
    const sections = document.querySelectorAll('.app-page');
    const sidebar = document.getElementById('leftMenu');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileBreakpoint = 768;
    const isMobile = () => window.innerWidth <= mobileBreakpoint;

    function setSidebarCollapsed(isCollapsed) {
        if (!sidebar) return;

        document.body.classList.toggle('sidebar-collapsed', isCollapsed);
        sidebar.classList.toggle('is-collapsed', isCollapsed);

        if (sidebarToggle) {
            sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
        }

        localStorage.setItem('imperial_sidebar_collapsed', isCollapsed ? '1' : '0');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            setSidebarCollapsed(!document.body.classList.contains('sidebar-collapsed'));
        });
    }

    // Function to switch pages
    function showPage(pageId) {
        // 1. Hide all sections
        sections.forEach(section => {
            section.style.display = 'none';
        });

        // 2. Show target section
        const target = document.getElementById('section-' + pageId);
        if (target) {
            target.style.display = 'block';

            // SAVE the current page to browser memory
            localStorage.setItem('imperial_last_page', pageId);
        }
    }

    // CLICK HANDLER
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // --- FIX: Prevent Logout from triggering page switches ---
            if (this.classList.contains('logout-item')) {
                return; // Exit here; logOut.js will handle the modal
            }

            const targetPage = this.getAttribute('data-page');
            if (!targetPage) return; // Safety check

            // Update Sidebar UI
            menuItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Switch the page
            showPage(targetPage);

            if (isMobile()) {
                setSidebarCollapsed(true);
            }
        });
    });

    // RECOVERY LOGIC: Check if there's a saved page, otherwise default to dashboard
    const savedPage = localStorage.getItem('imperial_last_page') || 'dashboard';

    // Update the Sidebar UI to match the saved page
    menuItems.forEach(item => {
        const pageAttr = item.getAttribute('data-page');
        if (pageAttr === savedPage) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });

    // Load the saved page on startup
    showPage(savedPage);

    const savedCollapsed = localStorage.getItem('imperial_sidebar_collapsed');
    const defaultCollapsed = isMobile(); // icon rail by default sa mobile
    setSidebarCollapsed(isMobile() ? true : (savedCollapsed === null ? defaultCollapsed : savedCollapsed === '1'));

    window.addEventListener('resize', () => {
        if (isMobile()) {
            setSidebarCollapsed(true);
        }
    });
});
