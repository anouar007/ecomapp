// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const userAvatar = document.querySelector('.user-avatar');
    const userDropdown = document.querySelector('.user-dropdown');
    const backdrop = document.querySelector('.sidebar-backdrop');

    function isMobile() {
        return window.innerWidth <= 991;
    }

    // Toggle sidebar
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (isMobile()) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.toggle('show');
                if (backdrop) backdrop.classList.toggle('show', sidebar.classList.contains('show'));
                document.body.classList.toggle('sidebar-open', sidebar.classList.contains('show'));
            } else {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');

                // Save desktop state to localStorage
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        });

        // Restore sidebar state from localStorage only on desktop
        if (!isMobile()) {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('expanded');
            }
        }
    }

    // Close mobile sidebar on backdrop click or outside click
    if (backdrop) {
        backdrop.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        });
    }

    document.addEventListener('click', function (e) {
        if (isMobile() && sidebar && sidebar.classList.contains('show')) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        }
    });

    // Set active menu item based on current URL
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.sidebar-menu-link');

    menuLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    // Add smooth scroll behavior
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

    // Animated number counter for stats
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };

    // Animate stat cards on load
    const statValues = document.querySelectorAll('.stat-card-value');
    statValues.forEach(stat => {
        const finalValue = parseInt(stat.textContent);
        if (!isNaN(finalValue)) {
            animateValue(stat, 0, finalValue, 1000);
        }
    });
});

// Toggle submenu function
function toggleSubmenu(e) {
    e.preventDefault();
    e.stopPropagation();

    const menuItem = e.currentTarget.closest('.sidebar-submenu');
    const arrow = e.currentTarget.querySelector('.submenu-arrow');
    const submenuItems = menuItem.querySelector('.submenu-items');

    // Toggle active state
    menuItem.classList.toggle('active');

    // Animate arrow
    if (menuItem.classList.contains('active')) {
        arrow.style.transform = 'rotate(180deg)';
        submenuItems.style.maxHeight = submenuItems.scrollHeight + 'px';
    } else {
        arrow.style.transform = 'rotate(0deg)';
        submenuItems.style.maxHeight = '0';
    }
}

