// Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const userAvatar = document.querySelector('.user-avatar');
    const userDropdown = document.querySelector('.user-dropdown');

    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('expanded');

            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });

        // Restore sidebar state from localStorage
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    }

    // User dropdown toggle
    if (userAvatar && userDropdown) {
        userAvatar.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!userDropdown.contains(e.target) && !userAvatar.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

    // Close sidebar on mobile when clicking outside
    if (window.innerWidth <= 991) {
        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

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

// Toggle profile dropdown (if exists)
function toggleProfileMenu() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
}

// Close profile dropdown when clicking outside
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('profileDropdown');
    const userMenu = document.querySelector('.user-menu');

    if (dropdown && userMenu && !userMenu.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

