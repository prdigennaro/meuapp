<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'admin'): ?>
            <!-- Admin Menu -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Administração</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') === 0) ? 'active' : ''; ?>" href="/admin/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/users') === 0) ? 'active' : ''; ?>" href="/admin/users">
                        <i class="fas fa-users me-1"></i>
                        Usuários
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/departments') === 0) ? 'active' : ''; ?>" href="/admin/departments">
                        <i class="fas fa-building me-1"></i>
                        Departamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/document-types') === 0) ? 'active' : ''; ?>" href="/admin/document-types">
                        <i class="fas fa-file-contract me-1"></i>
                        Tipos de Documentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/templates') === 0) ? 'active' : ''; ?>" href="/admin/templates">
                        <i class="fas fa-file-alt me-1"></i>
                        Modelos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/workflows') === 0) ? 'active' : ''; ?>" href="/admin/workflows">
                        <i class="fas fa-sitemap me-1"></i>
                        Fluxos de Trabalho
                    </a>
                </li>
            </ul>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- User Menu -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Documentos</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/dashboard') === 0) ? 'active' : ''; ?>" href="/user/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/new-document') === 0) ? 'active' : ''; ?>" href="/user/new-document">
                        <i class="fas fa-file-signature me-1"></i>
                        Novo Documento
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/my-documents') === 0) ? 'active' : ''; ?>" href="/user/my-documents">
                        <i class="fas fa-file-alt me-1"></i>
                        Meus Documentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/user/incoming-documents') === 0) ? 'active' : ''; ?>" href="/user/incoming-documents">
                        <i class="fas fa-inbox me-1"></i>
                        Documentos Recebidos
                    </a>
                </li>
            </ul>
            
            <!-- Settings Menu -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Configurações</span>
            </h6>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/profile') === 0) ? 'active' : ''; ?>" href="/profile">
                        <i class="fas fa-user-cog me-1"></i>
                        Meu Perfil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        Sair
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</nav>