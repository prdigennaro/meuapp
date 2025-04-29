<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }
        
        .form-signin {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }
        
        .form-signin .card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .form-signin .card-header {
            border-radius: 1rem 1rem 0 0;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 1.5rem;
        }
        
        .form-signin .card-body {
            padding: 2rem;
        }
        
        .form-signin .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-signin .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .form-signin .btn-primary {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <main class="form-signin">
        <div class="card">
            <div class="card-header">
                <div class="logo-container">
                    <img src="/assets/images/logo.svg" alt="Logo da Câmara Municipal de Arapongas" class="img-fluid">
                </div>
                <h1 class="h3 mb-0 fw-normal">Sistema de Gestão Documental</h1>
                <p class="mb-0">Câmara Municipal de Arapongas</p>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="/login">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                        <label for="password">Senha</label>
                    </div>
                    <button class="btn btn-primary mt-3" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>Entrar
                    </button>
                </form>
                
                <div class="mt-4 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Para acessar o sistema, entre em contato com o administrador.
                    </small>
                </div>
            </div>
            <div class="card-footer text-center py-3">
                <small class="text-muted">
                    <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?>
                </small>
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>