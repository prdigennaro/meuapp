<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gerenciamento de Usuários</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/users/add" class="btn btn-sm btn-primary">
                        <i class="fas fa-user-plus"></i> Novo Usuário
                    </a>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Usuários</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="#" id="exportCSV">Exportar CSV</a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()">Imprimir</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" id="refreshUsers">Atualizar Lista</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="usersTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Departamento</th>
                                    <th>Nível de Acesso</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum usuário encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['department_name']); ?></td>
                                            <td>
                                                <?php if ($user['role'] == 'admin'): ?>
                                                    <span class="badge bg-danger">Administrador</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary">Usuário</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info editUserBtn" 
                                                            data-id="<?php echo $user['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                            data-department="<?php echo $user['department_id']; ?>"
                                                            data-role="<?php echo $user['role']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" class="btn btn-danger deleteUserBtn" 
                                                                data-id="<?php echo $user['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($user['name']); ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-secondary" disabled title="Você não pode excluir seu próprio usuário">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Edit User Modal -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editUserForm" method="post" action="/admin/users/update" class="needs-validation" novalidate>
                            <div class="modal-body">
                                <input type="hidden" id="edit_user_id" name="id">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_name" class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" id="edit_name" name="name" required>
                                        <div class="invalid-feedback">
                                            Por favor, informe o nome completo.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="edit_email" name="email" required>
                                        <div class="invalid-feedback">
                                            Por favor, informe um e-mail válido.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_password" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="edit_password" name="password">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleEditPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            A senha deve ter pelo menos 8 caracteres.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_department_id" class="form-label">Departamento</label>
                                        <select class="form-select" id="edit_department_id" name="department_id" required>
                                            <option value="">Selecione o departamento</option>
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?php echo $department['id']; ?>">
                                                    <?php echo htmlspecialchars($department['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecione o departamento.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_role" class="form-label">Nível de Acesso</label>
                                        <select class="form-select" id="edit_role" name="role" required>
                                            <option value="user">Usuário</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecione o nível de acesso.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Delete User Modal -->
            <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteUserModalLabel">Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Tem certeza que deseja excluir o usuário <strong id="delete_user_name"></strong>?</p>
                            <p class="text-danger">Esta ação não pode ser desfeita!</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form id="deleteUserForm" method="post" action="/admin/users/delete">
                                <input type="hidden" id="delete_user_id" name="id">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $(document).ready(function() {
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
            },
            order: [[0, 'asc']]
        });
    });
    
    // Toggle edit password visibility
    const toggleEditPassword = document.getElementById('toggleEditPassword');
    const editPassword = document.getElementById('edit_password');
    
    toggleEditPassword.addEventListener('click', function() {
        const type = editPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        editPassword.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Edit user button
    const editUserButtons = document.querySelectorAll('.editUserBtn');
    
    editUserButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');
            const userEmail = this.getAttribute('data-email');
            const userDepartment = this.getAttribute('data-department');
            const userRole = this.getAttribute('data-role');
            
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_name').value = userName;
            document.getElementById('edit_email').value = userEmail;
            document.getElementById('edit_department_id').value = userDepartment;
            document.getElementById('edit_role').value = userRole;
            document.getElementById('edit_password').value = '';
            
            const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editUserModal.show();
        });
    });
    
    // Delete user button
    const deleteUserButtons = document.querySelectorAll('.deleteUserBtn');
    
    deleteUserButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const userName = this.getAttribute('data-name');
            
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('delete_user_name').textContent = userName;
            
            const deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
            deleteUserModal.show();
        });
    });
    
    // Export CSV
    document.getElementById('exportCSV').addEventListener('click', function(e) {
        e.preventDefault();
        
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "ID,Nome,Email,Departamento,Nível de Acesso,Criado em\n";
        
        const table = document.getElementById('usersTable');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                const rowData = [
                    cells[0].textContent.trim(),
                    cells[1].textContent.trim(),
                    cells[2].textContent.trim(),
                    cells[3].textContent.trim(),
                    cells[4].textContent.trim(),
                    cells[5].textContent.trim()
                ];
                csvContent += rowData.join(',') + '\n';
            }
        });
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "usuarios.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
    
    // Refresh users
    document.getElementById('refreshUsers').addEventListener('click', function(e) {
        e.preventDefault();
        location.reload();
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
