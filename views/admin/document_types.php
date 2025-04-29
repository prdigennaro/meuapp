<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tipos de Documentos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentTypeModal">
                        <i class="fas fa-plus"></i> Novo Tipo de Documento
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
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Tipos de Documentos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="documentTypesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($documentTypes)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum tipo de documento encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($documentTypes as $type): ?>
                                        <tr>
                                            <td><?php echo $type['id']; ?></td>
                                            <td><?php echo htmlspecialchars($type['name']); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($type['code']); ?></span></td>
                                            <td><?php echo htmlspecialchars($type['description']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($type['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info editTypeBtn" 
                                                            data-id="<?php echo $type['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($type['name']); ?>"
                                                            data-code="<?php echo htmlspecialchars($type['code']); ?>"
                                                            data-description="<?php echo htmlspecialchars($type['description']); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteTypeBtn" 
                                                            data-id="<?php echo $type['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($type['name']); ?>">
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
                            <h5><i class="fas fa-info-circle"></i> Sobre os Tipos de Documentos</h5>
                            <p>Os tipos de documentos definem as categorias de documentos que podem ser criados no sistema. Exemplos incluem Estudo Técnico Preliminar (ETP), Termo de Referência (TR), Editais e outros documentos específicos da Lei 14.133.</p>
                            <p>Cada tipo de documento pode ter modelos específicos associados a ele para a geração de documentos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add Document Type Modal -->
<div class="modal fade" id="addDocumentTypeModal" tabindex="-1" aria-labelledby="addDocumentTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentTypeModalLabel">Novo Tipo de Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/document-types/add" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do tipo de documento.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Código</label>
                        <input type="text" class="form-control" id="code" name="code" required>
                        <div class="form-text">
                            O código deve ser único e usado para identificar o tipo de documento (ex: ETP, TR, EDITAL).
                        </div>
                        <div class="invalid-feedback">
                            Por favor, informe o código do tipo de documento.
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

<!-- Edit Document Type Modal -->
<div class="modal fade" id="editDocumentTypeModal" tabindex="-1" aria-labelledby="editDocumentTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDocumentTypeModalLabel">Editar Tipo de Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="/admin/document-types/update" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback">
                            Por favor, informe o nome do tipo de documento.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_code" class="form-label">Código</label>
                        <input type="text" class="form-control" id="edit_code" name="code" required>
                        <div class="form-text">
                            O código deve ser único e usado para identificar o tipo de documento (ex: ETP, TR, EDITAL).
                        </div>
                        <div class="invalid-feedback">
                            Por favor, informe o código do tipo de documento.
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

<!-- Delete Document Type Modal -->
<div class="modal fade" id="deleteDocumentTypeModal" tabindex="-1" aria-labelledby="deleteDocumentTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDocumentTypeModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o tipo de documento <strong id="delete_type_name"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita! Todos os modelos associados a este tipo de documento também serão afetados.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/document-types/delete">
                    <input type="hidden" id="delete_id" name="id">
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
        $('#documentTypesTable').DataTable({
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
    
    // Edit document type
    const editButtons = document.querySelectorAll('.editTypeBtn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const code = this.getAttribute('data-code');
            const description = this.getAttribute('data-description');
            
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_description').value = description;
            
            const editModal = new bootstrap.Modal(document.getElementById('editDocumentTypeModal'));
            editModal.show();
        });
    });
    
    // Delete document type
    const deleteButtons = document.querySelectorAll('.deleteTypeBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_type_name').textContent = name;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteDocumentTypeModal'));
            deleteModal.show();
        });
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
