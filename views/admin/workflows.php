<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Fluxos de Trabalho</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkflowModal">
                        <i class="fas fa-plus"></i> Novo Fluxo de Trabalho
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
                    <h6 class="m-0 font-weight-bold text-primary">Fluxos de Trabalho Cadastrados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="workflowsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Tipo de Documento</th>
                                    <th>Etapas</th>
                                    <th>Descrição</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($workflows)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum fluxo de trabalho encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($workflows as $workflow): ?>
                                        <tr>
                                            <td><?php echo $workflow['id']; ?></td>
                                            <td><?php echo htmlspecialchars($workflow['name']); ?></td>
                                            <td><?php echo htmlspecialchars($workflow['document_type_name']); ?></td>
                                            <td>
                                                <?php 
                                                // Get count of steps
                                                $workflowSteps = (new WorkflowStep($conn))->getWorkflowStepsByWorkflow($workflow['id']);
                                                echo count($workflowSteps) . ' etapa(s)';
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($workflow['description']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($workflow['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="/admin/workflows/steps/<?php echo $workflow['id']; ?>" 
                                                       class="btn btn-primary" title="Gerenciar Etapas">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-info editWorkflowBtn" 
                                                            data-id="<?php echo $workflow['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($workflow['name']); ?>"
                                                            data-description="<?php echo htmlspecialchars($workflow['description']); ?>"
                                                            data-document-type-id="<?php echo $workflow['document_type_id']; ?>"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteWorkflowBtn" 
                                                            data-id="<?php echo $workflow['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($workflow['name']); ?>"
                                                            title="Excluir">
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
                            <h5><i class="fas fa-info-circle"></i> Sobre os Fluxos de Trabalho</h5>
                            <p>Os fluxos de trabalho definem as etapas pelas quais um documento deve passar, bem como os departamentos responsáveis por cada etapa.</p>
                            <p>Cada fluxo é associado a um tipo específico de documento e pode conter múltiplas etapas sequenciais.</p>
                            <p>Os usuários podem iniciar um fluxo de trabalho para seus documentos, que serão então encaminhados entre os departamentos conforme definido nas etapas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Workflow Modal -->
<div class="modal fade" id="addWorkflowModal" tabindex="-1" aria-labelledby="addWorkflowModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkflowModalLabel">Novo Fluxo de Trabalho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/workflows/add" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Fluxo</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do fluxo de trabalho.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="document_type_id" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="document_type_id" name="document_type_id" required>
                            <option value="">Selecione o tipo de documento</option>
                            <?php foreach ($documentTypes as $type): ?>
                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione o tipo de documento.
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

<!-- Edit Workflow Modal -->
<div class="modal fade" id="editWorkflowModal" tabindex="-1" aria-labelledby="editWorkflowModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkflowModalLabel">Editar Fluxo de Trabalho</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/workflows/update" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome do Fluxo</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do fluxo de trabalho.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_document_type_id" class="form-label">Tipo de Documento</label>
                        <select class="form-select" id="edit_document_type_id" name="document_type_id" required>
                            <option value="">Selecione o tipo de documento</option>
                            <?php foreach ($documentTypes as $type): ?>
                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione o tipo de documento.
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

<!-- Delete Workflow Modal -->
<div class="modal fade" id="deleteWorkflowModal" tabindex="-1" aria-labelledby="deleteWorkflowModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteWorkflowModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o fluxo de trabalho <strong id="delete_workflow_name"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita! Todas as etapas deste fluxo também serão excluídas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/workflows/delete">
                    <input type="hidden" id="delete_workflow_id" name="id">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $(document).ready(function() {
        $('#workflowsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
            },
            order: [[0, 'asc']]
        });
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
    
    // Edit workflow
    const editButtons = document.querySelectorAll('.editWorkflowBtn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const documentTypeId = this.getAttribute('data-document-type-id');
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_document_type_id').value = documentTypeId;
            
            const editModal = new bootstrap.Modal(document.getElementById('editWorkflowModal'));
            editModal.show();
        });
    });
    
    // Delete workflow
    const deleteButtons = document.querySelectorAll('.deleteWorkflowBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete_workflow_id').value = id;
            document.getElementById('delete_workflow_name').textContent = name;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteWorkflowModal'));
            deleteModal.show();
        });
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
