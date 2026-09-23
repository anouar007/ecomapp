import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/layouts/frontend.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

new_script = """        });
    })();
    </script>
    
    <script>
    // Toggle Mobile Navbar Menu
    document.addEventListener('DOMContentLoaded', function() {
        var menuBtn = document.getElementById('spMobileMenuBtn');
        var navMenu = document.getElementById('spNavMenu');
        
        if (menuBtn && navMenu) {
            menuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                navMenu.classList.toggle('mobile-open');
                
                // Toggle active class on button for hamburger animation if needed
                this.classList.toggle('active');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!navMenu.contains(event.target) && !menuBtn.contains(event.target)) {
                    navMenu.classList.remove('mobile-open');
                    menuBtn.classList.remove('active');
                }
            });
        }
    });
    </script>
    @stack('scripts')"""

content = content.replace("        });\n    })();\n    </script>\n    @stack('scripts')", new_script)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Mobile menu script added")
