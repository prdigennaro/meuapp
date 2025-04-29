<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Modelos de Documentos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" onclick="location.href='/admin/templates/add'">
                        <i class="fas fa-plus"></i> Novo Modelo
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
                    <h6 class="m-0 font-weight-bold text-primary">Modelos Cadastrados</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="templatesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Tipo de Documento</th>
                                    <th>Variáveis</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($templates)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Nenhum modelo encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($templates as $template): ?>
                                        <tr>
                                            <td><?php echo $template['id']; ?></td>
                                            <td><?php echo htmlspecialchars($template['name']); ?></td>
                                            <td><?php echo htmlspecialchars($template['document_type_name']); ?></td>
                                            <td>
                                                <?php 
                                                $variables = json_decode($template['variables'], true);
                                                if (!empty($variables)) {
                                                    echo '<span class="badge bg-info">' . count($variables) . ' variáveis</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">Nenhuma variável</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($template['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary viewTemplateBtn" 
                                                            data-id="<?php echo $template['id']; ?>"
                                                            title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-info editTemplateBtn" 
                                                            onclick="location.href='/admin/templates/edit/<?php echo $template['id']; ?>'"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success cloneTemplateBtn" 
                                                            data-id="<?php echo $template['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($template['name']); ?>"
                                                            title="Clonar">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger deleteTemplateBtn" 
                                                            data-id="<?php echo $template['id']; ?>"
                                                            data-name="<?php echo htmlspecialchars($template['name']); ?>"
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
                            <h5><i class="fas fa-info-circle"></i> Sobre os Modelos de Documentos</h5>
                            <p>Os modelos de documentos são templates que serão usados para gerar documentos com a ajuda da inteligência artificial.</p>
                            <p>Para criar um modelo, você deve definir o conteúdo e marcar as variáveis que serão preenchidas pelo usuário usando a sintaxe <code>{{nome_da_variavel}}</code>.</p>
                            <p>Exemplo: <code>Prezado(a) Sr(a). {{nome_destinatario}}, vimos por meio deste...</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- View Template Modal -->
<div class="modal fade" id="viewTemplateModal" tabindex="-1" aria-labelledby="viewTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTemplateModalLabel">Visualizar Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Nome do Modelo:</h6>
                            <p id="view_template_name"></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Tipo de Documento:</h6>
                            <p id="view_template_type"></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6>Descrição:</h6>
                    <p id="view_template_description"></p>
                </div>
                
                <div class="mb-3">
                    <h6>Variáveis:</h6>
                    <div id="view_template_variables"></div>
                </div>
                
                <div class="mb-3">
                    <h6>Conteúdo do Modelo:</h6>
                    <div id="view_template_content" class="border p-3 bg-light"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Clone Template Modal -->
<div class="modal fade" id="cloneTemplateModal" tabindex="-1" aria-labelledby="cloneTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cloneTemplateModalLabel">Clonar Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja clonar o modelo <strong id="clone_template_name"></strong>?</p>
                <p>Um novo modelo será criado com o mesmo conteúdo e variáveis.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/templates/clone">
                    <input type="hidden" id="clone_template_id" name="id">
                    <button type="submit" class="btn btn-success">Clonar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Template Modal -->
<div class="modal fade" id="deleteTemplateModal" tabindex="-1" aria-labelledby="deleteTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTemplateModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o modelo <strong id="delete_template_name"></strong>?</p>
                <p class="text-danger">Esta ação não pode ser desfeita! Se existirem documentos gerados com este modelo, eles não serão afetados, mas não será possível criar novos documentos com este modelo.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="/admin/templates/delete">
                    <input type="hidden" id="delete_template_id" name="id">
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
        try {
            $('#templatesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                },
                order: [[0, 'asc']],
                responsive: true,
                columnDefs: [
                    { targets: '_all', defaultContent: '' }  // Define conteúdo padrão para todas as colunas
                ]
            });
        } catch (e) {
            console.error('Erro ao inicializar DataTable:', e);
            // Modo de fallback se o DataTable falhar
            $('#templatesTable').addClass('table-striped');
        }
    });
    
    // View template
    const viewButtons = document.querySelectorAll('.viewTemplateBtn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.getAttribute('data-id');
            
            // Fetch template data
            fetch(`/api/templates/get?id=${templateId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Erro ao carregar o modelo: ' + data.error);
                        return;
                    }
                    
                    document.getElementById('view_template_name').textContent = data.name;
                    document.getElementById('view_template_type').textContent = data.document_type_name;
                    document.getElementById('view_template_description').textContent = data.description || 'Sem descrição';
                    
                    // Display variables as badges
                    const variablesContainer = document.getElementById('view_template_variables');
                    variablesContainer.innerHTML = '';
                    
                    const variables = JSON.parse(data.variables);
                    
                    if (variables && variables.length > 0) {
                        variables.forEach(variable => {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-primary me-1 mb-1';
                            badge.textContent = variable;
                            variablesContainer.appendChild(badge);
                        });
                    } else {
                        variablesContainer.textContent = 'Nenhuma variável encontrada';
                    }
                    
                    // Display template content with highlighted variables
                    let content = data.content;
                    content = content.replace(/{{(.*?)}}/g, '<span class="highlight-variable">{{$1}}</span>');
                    document.getElementById('view_template_content').innerHTML = content;
                    
                    const viewModal = new bootstrap.Modal(document.getElementById('viewTemplateModal'));
                    viewModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao carregar dados do modelo.');
                });
        });
    });
    
    // Clone template
    const cloneButtons = document.querySelectorAll('.cloneTemplateBtn');
    
    cloneButtons.forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.getAttribute('data-id');
            const templateName = this.getAttribute('data-name');
            
            document.getElementById('clone_template_id').value = templateId;
            document.getElementById('clone_template_name').textContent = templateName;
            
            const cloneModal = new bootstrap.Modal(document.getElementById('cloneTemplateModal'));
            cloneModal.show();
        });
    });
    
    // Delete template
    const deleteButtons = document.querySelectorAll('.deleteTemplateBtn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const templateId = this.getAttribute('data-id');
            const templateName = this.getAttribute('data-name');
            
            document.getElementById('delete_template_id').value = templateId;
            document.getElementById('delete_template_name').textContent = templateName;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteTemplateModal'));
            deleteModal.show();
        });
    });
});
</script>

<style>
.highlight-variable {
    background-color: #e7f3fe;
    border: 1px solid #c0d8ea;
    border-radius: 3px;
    padding: 0 3px;
    color: #0d6efd;
    font-weight: bold;
}

.modal-xl {
    max-width: 90%;
}
</style>

<?php include 'views/partials/footer.php'; ?>
