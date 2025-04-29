<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Departamentos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                        <i class="fas fa-plus"></i> Novo Departamento
                    </button>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Departamentos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="departmentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Usuários</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($departments)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum departamento encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($departments as $department): ?>
                                        <tr>
                                            <td><?php echo $department['id']; ?></td>
                                            <td><?php echo htmlspecialchars($department['name']); ?></td>
                                            <td><?php echo htmlspecialchars($department['description']); ?></td>
                                            <td>
                                                <?php 
                                                // Get count of users in this department
                                                if (isset($usersByDepartment[$department['id']])) {
                                                    $count = count($usersByDepartment[$department['id']]);
                                                    echo $count . ' usuário(s)';
                                                } else {
                                                    echo '0 usuário(s)';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($department['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info editDepartmentBtn" 
                                                            data-id="<?php echo $department['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($department['name']); ?>"
                                                            data-description="<?php echo htmlspecialchars($department['description']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteDepartmentBtn" 
                                                            data-id="<?php echo $department['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($department['name']); ?>"
                                                            data-has-users="<?php echo (isset($usersByDepartment[$department['id']]) && count($usersByDepartment[$department['id']]) > 0) ? '1' : '0'; ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
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
            
            <!-- Info Card -->
            <div class="card bg-info text-white shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h5><i class="fas fa-info-circle"></i> Sobre os Departamentos</h5>
                            <p>Os departamentos representam as divisões organizacionais da Câmara Municipal de Arapongas.</p>
                            <p>Cada usuário do sistema deve estar vinculado a um departamento específico.</p>
                            <p>Os fluxos de trabalho de documentos são roteados entre departamentos, e cada etapa de um fluxo é atribuída a um departamento responsável.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Novo Departamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/departments/add" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Departamento</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do departamento.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Editar Departamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/departments/update" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome do Departamento</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do departamento.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
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

<!-- Delete Department Modal -->
<div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDepartmentModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o departamento <strong id="delete_department_name"></strong>?</p>
                <div id="delete_warning_users" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle"></i> Este departamento possui usuários vinculados. Não é possível excluí-lo.
                </div>
                <p class="text-danger">Esta ação não pode ser desfeita!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/departments/delete">
                    <input type="hidden" id="delete_department_id" name="id">
                    <button type="submit" id="delete_department_btn" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $(document).ready(function() {
        try {
            $('#departmentsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                },
                order: [[0, 'asc']],
                responsive: true,
                columnDefs: [
                    { targets: '_all', defaultContent: '' }
                ]
            });
        } catch (e) {
            console.error('Erro ao inicializar DataTable:', e);
            $('#departmentsTable').addClass('table-striped');
        }
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
    
    // Edit department
    const editButtons = document.querySelectorAll('.editDepartmentBtn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            
            const editModal = new bootstrap.Modal(document.getElementById('editDepartmentModal'));
            editModal.show();
        });
    });
    
    // Delete department
    const deleteButtons = document.querySelectorAll('.deleteDepartmentBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const hasUsers = this.getAttribute('data-has-users') === '1';
            
            document.getElementById('delete_department_id').value = id;
            document.getElementById('delete_department_name').textContent = name;
            
            const warningUsersElement = document.getElementById('delete_warning_users');
            const deleteButton = document.getElementById('delete_department_btn');
            
            if (hasUsers) {
                warningUsersElement.classList.remove('d-none');
                deleteButton.disabled = true;
            } else {
                warningUsersElement.classList.add('d-none');
                deleteButton.disabled = false;
            }
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteDepartmentModal'));
            deleteModal.show();
        });
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
