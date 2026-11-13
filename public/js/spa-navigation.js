/**
 * Wina Shop Admin - High-Performance Zero-Refresh SPA Navigation Engine
 * Provides instant, seamless transitions between admin dashboard sections
 * without full-page reloads, preserving layout, sidebar state, and assets.
 */
(function() {
    'use strict';

    // Protect against double initialization
    if (window.SpaNavigator) return;

    // =========================================================================
    // 1. TOP PROGRESS BAR (YouTube / GitHub / Linear Style)
    // =========================================================================
    const ProgressBar = {
        el: null,
        timer: null,
        progress: 0,

        init() {
            if (this.el) return;
            this.el = document.createElement('div');
            this.el.id = 'spa-progress-bar';
            this.el.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                height: 3px;
                width: 0%;
                background: linear-gradient(90deg, #dc2626 0%, #f59e0b 50%, #dc2626 100%);
                box-shadow: 0 0 10px rgba(220, 38, 38, 0.7), 0 0 4px rgba(245, 158, 11, 0.5);
                z-index: 999999;
                transition: width 0.18s ease, opacity 0.25s ease;
                pointer-events: none;
                opacity: 0;
            `;
            document.body.appendChild(this.el);
        },

        start() {
            this.init();
            if (this.timer) clearInterval(this.timer);
            this.progress = 18;
            this.el.style.opacity = '1';
            this.el.style.width = '18%';

            this.timer = setInterval(() => {
                if (this.progress < 70) {
                    this.progress += Math.random() * 12 + 4;
                    this.el.style.width = `${Math.min(this.progress, 70)}%`;
                } else if (this.progress < 90) {
                    this.progress += 1.5;
                    this.el.style.width = `${this.progress}%`;
                }
            }, 100);
        },

        finish() {
            if (this.timer) clearInterval(this.timer);
            if (!this.el) return;
            this.el.style.width = '100%';
            setTimeout(() => {
                this.el.style.opacity = '0';
                setTimeout(() => {
                    this.el.style.width = '0%';
                }, 250);
            }, 120);
        }
    };

    // =========================================================================
    // 2. IN-MEMORY CACHE FOR INSTANT 0-MS NAVIGATION
    // =========================================================================
    const Cache = {
        store: new Map(),
        maxEntries: 15,
        ttl: 45 * 1000, // 45 seconds cache TTL

        get(url) {
            const entry = this.store.get(url);
            if (!entry) return null;
            if (Date.now() - entry.time > this.ttl) {
                this.store.delete(url);
                return null;
            }
            return entry.html;
        },

        set(url, html) {
            if (this.store.size >= this.maxEntries) {
                const oldest = this.store.keys().next().value;
                this.store.delete(oldest);
            }
            this.store.set(url, { html, time: Date.now() });
        },

        clear() {
            this.store.clear();
        }
    };

    // =========================================================================
    // 3. SPA NAVIGATOR ENGINE
    // =========================================================================
    let currentAbortController = null;
    let prefetchTimer = null;

    const SpaNavigator = {
        isNavigating: false,

        /**
         * Determine whether a link should be intercepted by the SPA engine
         */
        isEligibleLink(link, event) {
            if (!link) return false;
            if (event.defaultPrevented) return false;
            if (event.button !== 0) return false; // Left click only
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;

            // Target attribute check
            const target = link.getAttribute('target');
            if (target && target !== '_self') return false;

            // Download attribute
            if (link.hasAttribute('download')) return false;

            // Explicit opt-outs
            if (link.hasAttribute('data-no-pjax') || link.hasAttribute('data-native') || link.classList.contains('no-pjax')) {
                return false;
            }

            // Bootstrap modals or dropdown toggles
            if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-bs-target')) {
                return false;
            }

            // Href validity
            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                return false;
            }

            // Check origin
            let url;
            try {
                url = new URL(link.href, window.location.origin);
            } catch (e) {
                return false;
            }

            if (url.origin !== window.location.origin) return false;

            // Bypass exports, downloads, PDFs, and auth actions
            const path = url.pathname.toLowerCase();
            const bypassedPatterns = [
                '/export',
                '.csv',
                '.xlsx',
                '.xls',
                '.pdf',
                '/logout',
                '/login',
                '/register'
            ];
            for (let pattern of bypassedPatterns) {
                if (path.includes(pattern)) return false;
            }

            return true;
        },

        /**
         * Main navigation function
         */
        async navigate(url, pushState = true, isBackForward = false) {
            const targetUrl = new URL(url, window.location.origin).href;

            // Don't re-navigate to the exact same page unless forced (still close mobile drawer)
            if (!isBackForward && targetUrl === window.location.href) {
                this.closeMobileSidebar();
                return;
            }

            // Abort previous in-flight request
            if (currentAbortController) {
                currentAbortController.abort();
            }
            currentAbortController = new AbortController();

            this.isNavigating = true;
            ProgressBar.start();

            const mainContent = document.querySelector('main.main-content');
            if (mainContent) {
                mainContent.style.transition = 'opacity 0.12s ease-out';
                mainContent.style.opacity = '0.65';
                mainContent.style.pointerEvents = 'none';
            }

            try {
                // Check cache first
                let html = Cache.get(targetUrl);

                if (!html) {
                    const response = await fetch(targetUrl, {
                        signal: currentAbortController.signal,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-SPA-Navigation': '1'
                        }
                    });

                    // If redirected to login or error, fallback to full browser navigation
                    if (response.redirected || !response.ok) {
                        window.location.href = response.url || targetUrl;
                        return;
                    }

                    html = await response.text();
                    Cache.set(targetUrl, html);
                }

                // Parse document
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');

                const newMain = newDoc.querySelector('main.main-content');
                if (!newMain) {
                    // Not a standard admin layout, fallback natively
                    window.location.href = targetUrl;
                    return;
                }

                // 1. Update Document Title
                if (newDoc.title) {
                    document.title = newDoc.title;
                }

                // 2. Cleanup Bootstrap state (modals, backdrops, tooltips)
                this.cleanupBootstrap();

                // 3. Destroy old Chart.js instances to avoid memory leaks
                this.cleanupCharts();

                // 4. Synchronize Page-Specific Stylesheets (@push('styles'))
                this.syncStyles(newDoc);

                // 5. Swap Main Content HTML & Classes (preserve sidebar-collapsed expanded state)
                if (mainContent) {
                    const wasExpanded = mainContent.classList.contains('expanded');
                    mainContent.innerHTML = newMain.innerHTML;
                    mainContent.className = newMain.className || 'main-content';
                    if (wasExpanded) {
                        mainContent.classList.add('expanded');
                    }
                }

                // 6. Synchronize Page-Specific Scripts Container
                const curScriptsContainer = document.getElementById('spa-page-scripts');
                const newScriptsContainer = newDoc.getElementById('spa-page-scripts');
                if (curScriptsContainer && newScriptsContainer) {
                    curScriptsContainer.innerHTML = newScriptsContainer.innerHTML;
                }

                // 7. Update Browser History State
                if (pushState) {
                    window.history.pushState({ spa: true, url: targetUrl }, newDoc.title, targetUrl);
                }

                // 8. Sync Sidebar Collapse Preference (e.g. restore when leaving POS) & Active Nav
                const targetPath = new URL(targetUrl).pathname.toLowerCase();
                if (window.WinaSidebar && typeof window.WinaSidebar.syncFromPreference === 'function') {
                    window.WinaSidebar.syncFromPreference(targetPath.startsWith('/pos'));
                }
                this.syncActiveNavigation(newDoc, targetUrl);

                // 9. Close Mobile Sidebar Drawer if open
                this.closeMobileSidebar();

                // 10. Scroll to top (or to anchor)
                if (!isBackForward) {
                    const hash = new URL(targetUrl).hash;
                    if (hash) {
                        const targetAnchor = document.querySelector(hash);
                        if (targetAnchor) {
                            targetAnchor.scrollIntoView({ behavior: 'smooth' });
                        }
                    } else {
                        window.scrollTo({ top: 0, behavior: 'instant' });
                    }
                }

                // 11. Execute page-specific scripts in proper sequence
                await this.executeScripts(mainContent, curScriptsContainer || newScriptsContainer);

                // 12. Re-initialize Global UI Helpers (Tooltips, Dropdowns, Sortable)
                this.reinitializeUI();

                // Finish
                ProgressBar.finish();
                if (mainContent) {
                    mainContent.style.opacity = '1';
                    mainContent.style.pointerEvents = '';
                }

                this.isNavigating = false;

                // Dispatch custom event for plugins and extensions
                document.dispatchEvent(new CustomEvent('spa:navigated', {
                    detail: { url: targetUrl, title: newDoc.title }
                }));

            } catch (err) {
                if (err.name === 'AbortError') return;
                console.warn('[SPA Navigator] Fallback to native navigation:', err);
                window.location.href = targetUrl;
            }
        },

        /**
         * Clean up open Bootstrap modals, overlays, and tooltips
         */
        cleanupBootstrap() {
            try {
                // Hide any visible modal
                document.querySelectorAll('.modal.show').forEach(modalEl => {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const instance = bootstrap.Modal.getInstance(modalEl);
                        if (instance) instance.hide();
                    }
                });

                // Remove backdrop artifacts
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');

                // Remove lingering tooltips and popovers
                document.querySelectorAll('.tooltip, .popover').forEach(el => el.remove());
            } catch (e) {}
        },

        /**
         * Destroy active Chart.js instances before replacing canvas DOM nodes
         */
        cleanupCharts() {
            try {
                if (typeof Chart !== 'undefined' && Chart.instances) {
                    Object.values(Chart.instances).forEach(chart => {
                        try { chart.destroy(); } catch (e) {}
                    });
                }
            } catch (e) {}
        },

        /**
         * Synchronize <style> and <link rel="stylesheet"> from @push('styles')
         */
        syncStyles(newDoc) {
            const curContainer = document.getElementById('spa-page-styles');
            const newContainer = newDoc.getElementById('spa-page-styles');
            if (curContainer && newContainer) {
                curContainer.innerHTML = newContainer.innerHTML;
            }
        },

        /**
         * Sync active state on sidebar links and top navbar actions
         */
        syncActiveNavigation(newDoc, targetUrl) {
            const targetPath = new URL(targetUrl).pathname.replace(/\/+$/, '') || '/';
            const sidebarLinks = document.querySelectorAll('.sidebar-menu-link');

            // 1. Clear current active classes
            sidebarLinks.forEach(link => link.classList.remove('active'));

            // 2. Read new active links from Laravel's rendered response
            const newActiveLinks = newDoc.querySelectorAll('.sidebar-menu-link.active');
            let matched = false;
            if (newActiveLinks.length > 0) {
                newActiveLinks.forEach(newLink => {
                    const href = newLink.getAttribute('href');
                    if (!href) return;
                    let match = null;
                    sidebarLinks.forEach(link => {
                        if (link.getAttribute('href') === href) {
                            match = link;
                        }
                    });
                    if (match) {
                        match.classList.add('active');
                        matched = true;
                    }
                });
            }

            if (!matched) {
                // Fallback normalized pathname matching
                sidebarLinks.forEach(link => {
                    const linkHref = link.getAttribute('href');
                    if (!linkHref) return;
                    try {
                        const linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/+$/, '') || '/';
                        if (linkPath === targetPath) {
                            link.classList.add('active');
                        }
                    } catch (e) {}
                });
            }

            // 3. Sync POS Top Navbar active pills (desktop & mobile)
            const isPos = targetPath === '/pos' || targetPath.startsWith('/pos/');
            const desktopPosBtn = document.querySelector('.navbar .nav-btn-pos');
            if (desktopPosBtn) {
                desktopPosBtn.classList.toggle('active', isPos);
            }
            const mobilePosBtn = document.querySelector('.navbar .nav-btn-shop-mobile[href*="/pos"]');
            if (mobilePosBtn) {
                mobilePosBtn.classList.toggle('text-white', isPos);
                mobilePosBtn.classList.toggle('bg-danger', isPos);
                mobilePosBtn.classList.toggle('border-danger', isPos);
                const mobileIcon = mobilePosBtn.querySelector('i');
                if (mobileIcon) mobileIcon.classList.toggle('text-white', isPos);
            }

            // 4. Expand collapsed section if active link lives inside it & ensure visibility
            if (window.WinaSidebar && typeof window.WinaSidebar.onActiveLinkChanged === 'function') {
                window.WinaSidebar.onActiveLinkChanged();
            }
        },

        /**
         * Automatically close mobile drawer when navigating
         */
        closeMobileSidebar() {
            if (window.WinaSidebar && typeof window.WinaSidebar.closeMobile === 'function') {
                window.WinaSidebar.closeMobile();
                return;
            }
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            const toggler = document.querySelector('.navbar-toggler');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                if (backdrop) backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
                if (toggler) {
                    const icon = toggler.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-xmark');
                        icon.classList.add('fa-bars');
                    }
                }
            }
        },

        /**
         * Sequentially execute scripts found in the new content
         */
        async executeScripts(mainContainer, scriptsContainer) {
            const scriptElements = [];

            if (mainContainer) {
                scriptElements.push(...mainContainer.querySelectorAll('script'));
            }
            if (scriptsContainer) {
                scriptElements.push(...scriptsContainer.querySelectorAll('script'));
            }

            for (const oldScript of scriptElements) {
                const newScript = document.createElement('script');

                // Duplicate attributes
                for (let i = 0; i < oldScript.attributes.length; i++) {
                    const attr = oldScript.attributes[i];
                    newScript.setAttribute(attr.name, attr.value);
                }

                if (oldScript.src) {
                    // External script: wait for load
                    await new Promise(resolve => {
                        newScript.onload = resolve;
                        newScript.onerror = resolve; // Continue on error
                        document.body.appendChild(newScript);
                    });
                } else {
                    // Inline script: execute immediately
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                }

                // Cleanup temporary script element
                oldScript.remove();
                if (!newScript.src) {
                    newScript.remove();
                }
            }

            // Trigger lifecycle events for any scripts waiting on DOMContentLoaded
            try {
                document.dispatchEvent(new Event('DOMContentLoaded'));
                window.dispatchEvent(new Event('load'));
                window.dispatchEvent(new Event('resize'));
            } catch (e) {}
        },

        /**
         * Re-initialize Bootstrap dropdowns, tooltips, and interactive components
         */
        reinitializeUI() {
            try {
                // Dropdowns
                const userTrigger = document.getElementById('user-menu-trigger');
                if (userTrigger && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    bootstrap.Dropdown.getOrCreateInstance(userTrigger);
                }

                // Tooltips
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        new bootstrap.Tooltip(el);
                    });
                }
            } catch (e) {}
        },

        /**
         * Background prefetch on link hover for 0-ms instant feel
         */
        prefetch(url) {
            try {
                const targetUrl = new URL(url, window.location.origin).href;
                if (Cache.get(targetUrl)) return; // Already cached

                fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-SPA-Navigation': '1'
                    }
                })
                .then(r => r.ok ? r.text() : null)
                .then(html => {
                    if (html) Cache.set(targetUrl, html);
                })
                .catch(() => {});
            } catch (e) {}
        },

        /**
         * Clear cache on data mutations (POST/PUT/DELETE)
         */
        clearCache() {
            Cache.clear();
        }
    };

    // =========================================================================
    // 4. EVENT LISTENERS SETUP
    // =========================================================================

    // Click delegation for smooth link navigation
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;

        if (SpaNavigator.isEligibleLink(link, e)) {
            e.preventDefault();
            SpaNavigator.navigate(link.href, true);
        }
    });

    // Hover prefetch delegation on desktop (after 90ms hover)
    document.addEventListener('mouseover', function(e) {
        if (window.innerWidth < 992) return; // Skip on mobile to save data
        const link = e.target.closest('.sidebar-menu-link, .subnav-pill, .quick-action-pill');
        if (!link) return;

        if (prefetchTimer) clearTimeout(prefetchTimer);
        prefetchTimer = setTimeout(() => {
            if (SpaNavigator.isEligibleLink(link, { defaultPrevented: false, button: 0 })) {
                SpaNavigator.prefetch(link.href);
            }
        }, 90);
    });

    document.addEventListener('mouseout', function() {
        if (prefetchTimer) clearTimeout(prefetchTimer);
    });

    // Intercept GET filter/search forms inside main.main-content for smooth instant filtering
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.method.toUpperCase() !== 'GET') return;
        if (form.hasAttribute('data-no-pjax') || form.classList.contains('no-pjax')) return;

        // Only intercept GET forms inside the main content (e.g. search, filters, pagination)
        if (!form.closest('main.main-content')) return;

        e.preventDefault();
        const formData = new FormData(form);
        const searchParams = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value !== '') {
                searchParams.append(key, value);
            }
        }

        const actionUrl = new URL(form.action || window.location.href, window.location.origin);
        actionUrl.search = searchParams.toString();

        SpaNavigator.navigate(actionUrl.href, true);
    });

    // Invalidate cache on any native form submission or mutation
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form && form.method.toUpperCase() !== 'GET') {
            SpaNavigator.clearCache();
        }
    });

    // Handle browser Back and Forward buttons
    window.addEventListener('popstate', function() {
        SpaNavigator.navigate(window.location.href, false, true);
    });

    // Save initial history state
    if (!window.history.state) {
        window.history.replaceState({ spa: true, url: window.location.href }, document.title, window.location.href);
    }

    // Expose globally
    window.SpaNavigator = SpaNavigator;

})();

