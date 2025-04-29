<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Etapas do Fluxo de Trabalho</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-secondary me-2" onclick="location.href='/admin/workflows'">
                        <i class="fas fa-arrow-left"></i> Voltar para Fluxos
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStepModal">
                        <i class="fas fa-plus"></i> Nova Etapa
                    </button>
                </div>
            </div>
            
            <!-- Workflow Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Detalhes do Fluxo</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Nome:</strong> <?php echo htmlspecialchars($workflow['name']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($workflow['document_type_name']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Total de Etapas:</strong> <?php echo count($steps); ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Descrição:</strong> <?php echo !empty($workflow['description']) ? htmlspecialchars($workflow['description']) : 'Nenhuma descrição fornecida'; ?></p>
                        </div>
                    </div>
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
            
            <!-- Steps Flow Diagram -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Diagrama do Fluxo</h6>
                </div>
                <div class="card-body">
                    <div class="workflow-diagram">
                        <?php if (empty($steps)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                                <h4>Nenhuma etapa cadastrada</h4>
                                <p class="text-muted">Adicione etapas para visualizar o diagrama do fluxo.</p>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-center workflow-steps">
                                <?php foreach ($steps as $index => $step): ?>
                                    <div class="workflow-step <?php echo $step['is_finale'] ? 'finale' : ''; ?>">
                                        <div class="step-number"><?php echo $step['order']; ?></div>
                                        <div class="step-content">
                                            <h6><?php echo htmlspecialchars($step['name']); ?></h6>
                                            <p class="mb-1"><small>Departamento: <?php echo htmlspecialchars($step['department_name']); ?></small></p>
                                            <?php if ($step['is_finale']): ?>
                                                <span class="badge bg-success">Etapa Final</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($index < count($steps) - 1): ?>
                                        <div class="workflow-arrow">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Steps Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Etapas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="stepsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="10%">Ordem</th>
                                    <th>Nome</th>
                                    <th>Departamento</th>
                                    <th>Descrição</th>
                                    <th width="10%">Etapa Final</th>
                                    <th width="15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($steps)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhuma etapa cadastrada</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($steps as $step): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $step['order']; ?></td>
                                            <td><?php echo htmlspecialchars($step['name']); ?></td>
                                            <td><?php echo htmlspecialchars($step['department_name']); ?></td>
                                            <td><?php echo htmlspecialchars($step['description']); ?></td>
                                            <td class="text-center">
                                                <?php if ($step['is_finale']): ?>
                                                    <span class="badge bg-success"><i class="fas fa-check"></i> Sim</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Não</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info editStepBtn" 
                                                            data-id="<?php echo $step['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($step['name']); ?>"
                                                            data-description="<?php echo htmlspecialchars($step['description']); ?>"
                                                            data-department-id="<?php echo $step['department_id']; ?>"
                                                            data-order="<?php echo $step['order']; ?>"
                                                            data-is-finale="<?php echo $step['is_finale']; ?>"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteStepBtn" 
                                                            data-id="<?php echo $step['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($step['name']); ?>"
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
                            <h5><i class="fas fa-info-circle"></i> Sobre as Etapas do Fluxo</h5>
                            <p>As etapas definem a sequência de análise e aprovação pela qual um documento deve passar.</p>
                            <p>Cada etapa é associada a um departamento específico que será responsável pela análise naquele ponto do fluxo.</p>
                            <p>A ordem define a sequência das etapas. O documento seguirá da primeira à última etapa conforme definido aqui.</p>
                            <p>A opção "Etapa Final" indica que o documento chegou ao fim do fluxo de trabalho.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Step Modal -->
<div class="modal fade" id="addStepModal" tabindex="-1" aria-labelledby="addStepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStepModalLabel">Nova Etapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/workflows/steps/add" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="workflow_id" value="<?php echo $workflow['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da Etapa</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome da etapa.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="department_id" class="form-label">Departamento Responsável</label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Selecione o departamento</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo $department['id']; ?>">
                                    <?php echo htmlspecialchars($department['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione o departamento responsável.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="order" class="form-label">Ordem da Etapa</label>
                        <input type="number" class="form-control" id="order" name="order" 
                               min="1" value="<?php echo count($steps) + 1; ?>" required>
                        <div class="form-text">
                            Posição desta etapa no fluxo de trabalho.
                        </div>
                        <div class="invalid-feedback">
                            Por favor, informe a ordem da etapa.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_finale" name="is_finale" value="1">
                        <label class="form-check-label" for="is_finale">
                            Esta é a etapa final do fluxo
                        </label>
                        <div class="form-text">
                            Marque esta opção se esta for a última etapa do fluxo de trabalho.
                        </div>
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

<!-- Edit Step Modal -->
<div class="modal fade" id="editStepModal" tabindex="-1" aria-labelledby="editStepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStepModalLabel">Editar Etapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/workflows/steps/update" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_workflow_id" name="workflow_id" value="<?php echo $workflow['id']; ?>">
                    
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome da Etapa</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome da etapa.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_department_id" class="form-label">Departamento Responsável</label>
                        <select class="form-select" id="edit_department_id" name="department_id" required>
                            <option value="">Selecione o departamento</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo $department['id']; ?>">
                                    <?php echo htmlspecialchars($department['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione o departamento responsável.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_order" class="form-label">Ordem da Etapa</label>
                        <input type="number" class="form-control" id="edit_order" name="order" min="1" required>
                        <div class="form-text">
                            Posição desta etapa no fluxo de trabalho.
                        </div>
                        <div class="invalid-feedback">
                            Por favor, informe a ordem da etapa.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_finale" name="is_finale" value="1">
                        <label class="form-check-label" for="edit_is_finale">
                            Esta é a etapa final do fluxo
                        </label>
                        <div class="form-text">
                            Marque esta opção se esta for a última etapa do fluxo de trabalho.
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

<!-- Delete Step Modal -->
<div class="modal fade" id="deleteStepModal" tabindex="-1" aria-labelledby="deleteStepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteStepModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a etapa <strong id="delete_step_name"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita! A ordem das etapas restantes será reajustada automaticamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/workflows/steps/delete">
                    <input type="hidden" id="delete_step_id" name="id">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.workflow-diagram {
    overflow-x: auto;
    padding: 20px 10px;
}

.workflow-steps {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
}

.workflow-step {
    border: 2px solid #4e73df;
    border-radius: 8px;
    min-width: 180px;
    padding: 10px;
    margin: 0 5px;
    background-color: #f8f9fc;
    position: relative;
}

.workflow-step.finale {
    border-color: #1cc88a;
    background-color: #f8fffa;
}

.step-number {
    position: absolute;
    top: -15px;
    left: 10px;
    background-color: #4e73df;
    color: white;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.workflow-step.finale .step-number {
    background-color: #1cc88a;
}

.workflow-arrow {
    margin: 0 10px;
    color: #4e73df;
    font-size: 1.5rem;
}

.step-content {
    margin-top: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    $(document).ready(function() {
        $('#stepsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
            },
            order: [[0, 'asc']],
            pageLength: 25
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
    
    // Edit step
    const editButtons = document.querySelectorAll('.editStepBtn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const departmentId = this.getAttribute('data-department-id');
            const order = this.getAttribute('data-order');
            const isFinale = this.getAttribute('data-is-finale') === '1';
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_department_id').value = departmentId;
            document.getElementById('edit_order').value = order;
            document.getElementById('edit_is_finale').checked = isFinale;
            
            const editModal = new bootstrap.Modal(document.getElementById('editStepModal'));
            editModal.show();
        });
    });
    
    // Delete step
    const deleteButtons = document.querySelectorAll('.deleteStepBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete_step_id').value = id;
            document.getElementById('delete_step_name').textContent = name;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteStepModal'));
            deleteModal.show();
        });
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
