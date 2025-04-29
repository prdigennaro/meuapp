    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
    // Activate sidebar links based on current page
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
        
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath || 
                (href !== '/' && currentPath.startsWith(href))) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>