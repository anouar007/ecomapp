/**
 * Wina Shop Admin - Sidebar Controller, Quick Search, Rail Tooltips & Mobile Drawer
 */
(function () {
    'use strict';

    if (window.__winaAppInitialized) {
        return;
    }
    window.__winaAppInitialized = true;

    const STORAGE_COLLAPSED_KEY = 'sidebarCollapsed';
    const STORAGE_SECTIONS_KEY = 'sidebarCollapsedSections';

    function isMobile() {
        return window.innerWidth <= 991;
    }

    function normalizeText(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function isTypingTarget(el) {
        if (!el) return false;
        const tag = el.tagName;
        return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || el.isContentEditable;
    }

    function animateStatValues() {
        document.querySelectorAll('.stat-card-value').forEach(stat => {
            if (stat.dataset.animated === '1') return;
            const raw = (stat.textContent || '').trim();
            if (!/^\d+$/.test(raw)) return;
            const finalValue = parseInt(raw, 10);
            if (isNaN(finalValue) || finalValue <= 0) return;

            stat.dataset.animated = '1';
            const duration = 700;
            let startTimestamp = null;

            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                stat.textContent = String(Math.floor(progress * finalValue));
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    stat.textContent = String(finalValue);
                }
            };
            window.requestAnimationFrame(step);
        });
    }

    function initSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const sidebarToggle = document.querySelector('.navbar-toggler');
        const backdrop = document.querySelector('.sidebar-backdrop');
        const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
        const collapseBtn = document.getElementById('sidebar-collapse-btn');
        const searchBox = document.getElementById('sidebar-search-box');
        const searchInput = document.getElementById('sidebar-quick-search');
        const searchClear = document.getElementById('sidebar-search-clear');
        const searchTrigger = document.getElementById('sidebar-search-trigger');
        const searchEmpty = document.getElementById('sidebar-search-empty');
        const navScroll = document.getElementById('sidebar-nav-scroll');

        if (!sidebar) return;

        // Create single body-mounted floating tooltip for collapsed desktop icon rail
        let tooltipEl = document.getElementById('sidebar-floating-tooltip');
        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.id = 'sidebar-floating-tooltip';
            tooltipEl.className = 'sidebar-floating-tooltip';
            tooltipEl.setAttribute('role', 'tooltip');
            document.body.appendChild(tooltipEl);
        }

        function hideTooltip() {
            tooltipEl.classList.remove('is-visible');
        }

        function showTooltip(link) {
            if (isMobile() || !sidebar.classList.contains('collapsed')) {
                hideTooltip();
                return;
            }
            const label = link.getAttribute('data-tooltip') || link.querySelector('span')?.textContent?.trim();
            if (!label) return;

            const badge = link.getAttribute('data-tooltip-badge');
            tooltipEl.textContent = '';

            const labelNode = document.createElement('span');
            labelNode.textContent = label;
            tooltipEl.appendChild(labelNode);

            if (badge) {
                const badgeNode = document.createElement('span');
                badgeNode.className = 'tooltip-badge';
                badgeNode.textContent = badge;
                tooltipEl.appendChild(badgeNode);
            }

            const rect = link.getBoundingClientRect();
            const sidebarRect = sidebar.getBoundingClientRect();
            tooltipEl.style.left = `${Math.round(sidebarRect.right + 10)}px`;
            tooltipEl.style.top = `${Math.round(rect.top + rect.height / 2)}px`;
            tooltipEl.classList.add('is-visible');
        }

        function updateMobileTogglerState(isOpen) {
            if (!sidebarToggle) return;
            const icon = sidebarToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars', !isOpen);
                icon.classList.toggle('fa-xmark', isOpen);
            }
            if (isMobile()) {
                sidebarToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
        }

        function setCollapsed(collapsed, persist = true) {
            if (isMobile()) return;
            sidebar.classList.toggle('collapsed', collapsed);
            if (mainContent) mainContent.classList.toggle('expanded', collapsed);
            document.documentElement.classList.remove('sidebar-precollapsed');

            if (collapseBtn) {
                const textEl = collapseBtn.querySelector('span');
                if (textEl) {
                    textEl.textContent = collapsed ? 'Agrandir le menu' : 'Réduire le menu';
                }
                collapseBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            }

            if (sidebarToggle) {
                sidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            }

            if (collapsed && searchInput && searchInput.value) {
                searchInput.value = '';
                applySearchFilter('');
            }

            if (!collapsed) {
                hideTooltip();
            }

            if (persist) {
                try {
                    localStorage.setItem(STORAGE_COLLAPSED_KEY, String(collapsed));
                } catch (e) {}
            }
        }

        function openMobile() {
            sidebar.classList.remove('collapsed');
            if (mainContent) mainContent.classList.remove('expanded');
            document.documentElement.classList.remove('sidebar-precollapsed');
            sidebar.classList.add('show');
            if (backdrop) backdrop.classList.add('show');
            document.body.classList.add('sidebar-open');
            updateMobileTogglerState(true);
            hideTooltip();
        }

        function closeMobile() {
            sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.classList.remove('sidebar-open');
            updateMobileTogglerState(false);
        }

        function toggleDesktop() {
            setCollapsed(!sidebar.classList.contains('collapsed'), true);
        }

        // Collapsible Section Groups
        function readCollapsedSections() {
            try {
                const raw = localStorage.getItem(STORAGE_SECTIONS_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                return new Set(Array.isArray(parsed) ? parsed : []);
            } catch (e) {
                return new Set();
            }
        }

        function saveCollapsedSections(set) {
            try {
                localStorage.setItem(STORAGE_SECTIONS_KEY, JSON.stringify(Array.from(set)));
            } catch (e) {}
        }

        function applySectionCollapseUI(sectionKey, collapsed) {
            const header = sidebar.querySelector(`.sidebar-section-header[data-section-key="${sectionKey}"]`);
            if (header) {
                header.classList.toggle('is-collapsed', collapsed);
                header.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            }
            sidebar.querySelectorAll(`.sidebar-menu-item[data-section="${sectionKey}"]`).forEach(item => {
                item.classList.toggle('is-section-collapsed', collapsed);
            });
        }

        function ensureActiveSectionExpanded() {
            const activeLink = sidebar.querySelector('.sidebar-menu-link.active');
            if (!activeLink) return null;
            const activeItem = activeLink.closest('.sidebar-menu-item[data-section]');
            if (!activeItem) return activeLink;

            const sectionKey = activeItem.getAttribute('data-section');
            if (sectionKey) {
                applySectionCollapseUI(sectionKey, false);
            }
            return activeLink;
        }

        function syncSectionsFromStorage() {
            const collapsedSet = readCollapsedSections();
            sidebar.querySelectorAll('.sidebar-section-header[data-section-key]').forEach(header => {
                const key = header.getAttribute('data-section-key');
                applySectionCollapseUI(key, collapsedSet.has(key));
            });
            ensureActiveSectionExpanded();
        }

        // Quick Search Filter
        function applySearchFilter(rawQuery) {
            const query = normalizeText(rawQuery);
            const hasQuery = query.length > 0;

            if (searchBox) {
                searchBox.classList.toggle('has-value', hasQuery);
            }

            if (!hasQuery) {
                sidebar.querySelectorAll('.sidebar-menu-item').forEach(item => {
                    item.classList.remove('is-search-hidden');
                });
                sidebar.querySelectorAll('.sidebar-section-header').forEach(header => {
                    header.classList.remove('is-search-hidden');
                });
                if (searchEmpty) searchEmpty.classList.remove('is-visible');
                syncSectionsFromStorage();
                return;
            }

            const sectionMatchCounts = {};
            let totalVisible = 0;

            sidebar.querySelectorAll('.sidebar-menu-item[data-section]').forEach(item => {
                const link = item.querySelector('.sidebar-menu-link');
                const sectionKey = item.getAttribute('data-section') || '';
                const text = normalizeText((link?.textContent || '') + ' ' + (link?.getAttribute('data-tooltip') || ''));
                const matches = text.includes(query);

                item.classList.toggle('is-search-hidden', !matches);
                if (matches) {
                    item.classList.remove('is-section-collapsed');
                    sectionMatchCounts[sectionKey] = (sectionMatchCounts[sectionKey] || 0) + 1;
                    totalVisible++;
                }
            });

            sidebar.querySelectorAll('.sidebar-section-header[data-section-key]').forEach(header => {
                const key = header.getAttribute('data-section-key');
                const count = sectionMatchCounts[key] || 0;
                header.classList.toggle('is-search-hidden', count === 0);
            });

            if (searchEmpty) {
                searchEmpty.classList.toggle('is-visible', totalVisible === 0);
            }
        }

        function scrollActiveIntoView() {
            const activeLink = ensureActiveSectionExpanded();
            if (!activeLink || !navScroll) return;

            const containerRect = navScroll.getBoundingClientRect();
            const linkRect = activeLink.getBoundingClientRect();
            if (linkRect.top < containerRect.top + 8 || linkRect.bottom > containerRect.bottom - 8) {
                activeLink.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }

        function syncFromPreference(isPosRoute = false) {
            if (isMobile()) {
                sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                document.documentElement.classList.remove('sidebar-precollapsed');
                return;
            }

            if (isPosRoute) {
                setCollapsed(true, false);
                return;
            }

            let savedCollapsed = false;
            try {
                savedCollapsed = localStorage.getItem(STORAGE_COLLAPSED_KEY) === 'true';
            } catch (e) {}
            setCollapsed(savedCollapsed, false);
        }

        // Initial desktop state & sections setup
        const isInitialPos = window.location.pathname.toLowerCase().startsWith('/pos');
        syncFromPreference(isInitialPos);
        syncSectionsFromStorage();

        // Scroll active item into view if outside fold on first load
        window.requestAnimationFrame(() => scrollActiveIntoView());

        // Event: Navbar Hamburger Toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                if (isMobile()) {
                    if (sidebar.classList.contains('show')) {
                        closeMobile();
                    } else {
                        openMobile();
                    }
                } else {
                    toggleDesktop();
                }
            });
        }

        // Event: Sidebar Footer Collapse Button (Desktop)
        if (collapseBtn) {
            collapseBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleDesktop();
            });
        }

        // Event: Mobile Close Button & Backdrop
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                closeMobile();
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeMobile);
        }

        // Event: Outside click closes mobile drawer
        document.addEventListener('click', function (e) {
            if (!isMobile() || !sidebar.classList.contains('show')) return;
            if (!sidebar.contains(e.target) && (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                closeMobile();
            }
        });

        // Event: Click or Enter/Space on collapsible section headers
        sidebar.querySelectorAll('.sidebar-section-header[data-section-key]').forEach(header => {
            const toggleGroup = () => {
                if (sidebar.classList.contains('collapsed') && !isMobile()) return;
                if (searchInput && searchInput.value.trim() !== '') return;

                const key = header.getAttribute('data-section-key');
                const nextCollapsed = !header.classList.contains('is-collapsed');
                applySectionCollapseUI(key, nextCollapsed);

                const collapsedSet = readCollapsedSections();
                if (nextCollapsed) {
                    collapsedSet.add(key);
                } else {
                    collapsedSet.delete(key);
                }
                saveCollapsedSections(collapsedSet);
            };

            header.addEventListener('click', toggleGroup);
            header.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleGroup();
                }
            });
        });

        // Event: Quick Search Input, Clear, Trigger, Enter-to-open
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                applySearchFilter(searchInput.value);
            });

            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    if (searchInput.value) {
                        searchInput.value = '';
                        applySearchFilter('');
                    } else {
                        searchInput.blur();
                    }
                    return;
                }

                if (e.key === 'Enter') {
                    const firstMatch = sidebar.querySelector('.sidebar-menu-item:not(.is-search-hidden) .sidebar-menu-link');
                    if (firstMatch) {
                        e.preventDefault();
                        firstMatch.click();
                    }
                }
            });
        }

        if (searchClear && searchInput) {
            searchClear.addEventListener('click', function () {
                searchInput.value = '';
                applySearchFilter('');
                searchInput.focus();
            });
        }

        if (searchTrigger && searchInput) {
            searchTrigger.addEventListener('click', function () {
                setCollapsed(false, true);
                window.setTimeout(() => searchInput.focus(), 120);
            });
        }

        // Event: Collapsed Desktop Rail Floating Tooltip
        sidebar.addEventListener('mouseover', function (e) {
            const link = e.target.closest('.sidebar-menu-link');
            if (link && sidebar.contains(link)) {
                showTooltip(link);
            }
        });

        sidebar.addEventListener('mouseout', function (e) {
            const link = e.target.closest('.sidebar-menu-link');
            if (link && !link.contains(e.relatedTarget)) {
                hideTooltip();
            }
        });

        sidebar.addEventListener('focusin', function (e) {
            const link = e.target.closest('.sidebar-menu-link');
            if (link) showTooltip(link);
        });

        sidebar.addEventListener('focusout', function () {
            hideTooltip();
        });

        sidebar.addEventListener('click', function (e) {
            if (e.target.closest('.sidebar-menu-link')) {
                hideTooltip();
            }
        });

        if (navScroll) {
            navScroll.addEventListener('scroll', hideTooltip, { passive: true });
        }

        // Event: Keyboard Shortcuts (Ctrl/Cmd+B toggles sidebar, / searches menu, Esc closes mobile drawer)
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && !e.shiftKey && !e.altKey && e.key.toLowerCase() === 'b') {
                e.preventDefault();
                if (isMobile()) {
                    sidebar.classList.contains('show') ? closeMobile() : openMobile();
                } else {
                    toggleDesktop();
                }
                return;
            }

            if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey && !isTypingTarget(document.activeElement)) {
                if (searchInput && !isMobile()) {
                    e.preventDefault();
                    if (sidebar.classList.contains('collapsed')) {
                        setCollapsed(false, true);
                    }
                    window.setTimeout(() => searchInput.focus(), 80);
                }
                return;
            }

            if (e.key === 'Escape' && isMobile() && sidebar.classList.contains('show')) {
                closeMobile();
            }
        });

        // Event: Swipe Left to Close Mobile Drawer
        let touchStartX = 0;
        let touchStartY = 0;
        sidebar.addEventListener('touchstart', function (e) {
            if (!isMobile() || !sidebar.classList.contains('show') || e.touches.length !== 1) return;
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        sidebar.addEventListener('touchend', function (e) {
            if (!isMobile() || !sidebar.classList.contains('show') || !touchStartX || e.changedTouches.length !== 1) return;
            const deltaX = e.changedTouches[0].clientX - touchStartX;
            const deltaY = e.changedTouches[0].clientY - touchStartY;
            touchStartX = 0;
            touchStartY = 0;
            if (deltaX < -55 && Math.abs(deltaX) > Math.abs(deltaY) * 1.2) {
                closeMobile();
            }
        }, { passive: true });

        // Event: Clean Resize Synchronization across Mobile <-> Desktop Breakpoint
        let wasMobile = isMobile();
        window.addEventListener('resize', function () {
            const nowMobile = isMobile();
            if (nowMobile === wasMobile) return;
            wasMobile = nowMobile;

            if (nowMobile) {
                closeMobile();
                sidebar.classList.remove('collapsed');
                if (mainContent) mainContent.classList.remove('expanded');
                hideTooltip();
            } else {
                closeMobile();
                const isPos = window.location.pathname.toLowerCase().startsWith('/pos');
                syncFromPreference(isPos);
            }
        });

        window.WinaSidebar = {
            setCollapsed,
            toggleDesktop,
            openMobile,
            closeMobile,
            syncFromPreference,
            onActiveLinkChanged() {
                syncSectionsFromStorage();
                scrollActiveIntoView();
                hideTooltip();
            }
        };
    }

    // Delegated Smooth Anchor Scrolling (guards against invalid href="#" selectors)
    document.addEventListener('click', function (e) {
        const anchor = e.target.closest('a[href^="#"]');
        if (!anchor) return;
        const href = anchor.getAttribute('href');
        if (!href || href.length <= 1 || anchor.hasAttribute('data-bs-toggle')) return;

        try {
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch (err) {}
    });

    // Boot once on DOM ready (also re-runs stat animations cleanly on SPA page swap)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initSidebar();
            animateStatValues();
        }, { once: true });
    } else {
        initSidebar();
        animateStatValues();
    }

    document.addEventListener('spa:navigated', animateStatValues);
})();

// Legacy submenu toggle helper preserved for compatibility
function toggleSubmenu(e) {
    e.preventDefault();
    e.stopPropagation();

    const menuItem = e.currentTarget.closest('.sidebar-submenu');
    if (!menuItem) return;
    const arrow = e.currentTarget.querySelector('.submenu-arrow');
    const submenuItems = menuItem.querySelector('.submenu-items');

    menuItem.classList.toggle('active');

    if (menuItem.classList.contains('active')) {
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        if (submenuItems) submenuItems.style.maxHeight = submenuItems.scrollHeight + 'px';
    } else {
        if (arrow) arrow.style.transform = 'rotate(0deg)';
        if (submenuItems) submenuItems.style.maxHeight = '0';
    }
}
