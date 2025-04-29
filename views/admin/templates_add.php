<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Novo Modelo de Documento</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/admin/templates" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informações do Modelo</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/templates/create" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do Modelo</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="invalid-feedback">
                                    Por favor, informe o nome do modelo.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="document_type_id" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="document_type_id" name="document_type_id" required>
                                    <option value="">Selecione o tipo de documento</option>
                                    <?php foreach ($documentTypes as $type): ?>
                                        <option value="<?php echo $type['id']; ?>">
                                            <?php echo htmlspecialchars($type['name']); ?> (<?php echo htmlspecialchars($type['code']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor, selecione o tipo de documento.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                            <div class="form-text">
                                Uma breve descrição sobre este modelo e sua finalidade (opcional).
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Conteúdo do Modelo</label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Use <code>{{nome_da_variavel}}</code> para definir variáveis que serão preenchidas pelo usuário.
                                <br>Exemplo: <code>Prezado(a) Sr(a). {{nome_destinatario}}, vimos por meio deste...</code>
                            </div>
                            <div id="editor-toolbar"></div>
                            <div id="editor" style="height: 500px; border: 1px solid #ccc;"></div>
                            <textarea class="d-none" id="content" name="content" required></textarea>
                            <div class="invalid-feedback">
                                Por favor, informe o conteúdo do modelo.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6>Variáveis Detectadas</h6>
                            <div id="variables-container" class="border rounded p-3 bg-light">
                                <p class="text-muted">Nenhuma variável detectada. Adicione variáveis usando <code>{{nome_da_variavel}}</code> no conteúdo do modelo.</p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/admin/templates" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Modelo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'script': 'sub'}, { 'script': 'super' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['clean']
    ];

    const quill = new Quill('#editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow'
    });
    
    // Function to detect variables in the content
    function detectVariables() {
        const content = quill.root.innerHTML;
        const regex = /{{(.*?)}}/g;
        const matches = content.match(regex);
        const variablesContainer = document.getElementById('variables-container');
        
        if (matches && matches.length > 0) {
            // Extract variable names and remove duplicates
            const variables = [...new Set(matches.map(match => match.slice(2, -2).trim()))];
            
            let variablesHtml = '';
            variables.forEach(variable => {
                variablesHtml += `<span class="badge bg-primary me-2 mb-2">${variable}</span>`;
            });
            
            variablesContainer.innerHTML = variablesHtml;
        } else {
            variablesContainer.innerHTML = '<p class="text-muted">Nenhuma variável detectada. Adicione variáveis usando <code>{{nome_da_variavel}}</code> no conteúdo do modelo.</p>';
        }
    }
    
    // Update variables whenever content changes
    quill.on('text-change', function() {
        detectVariables();
        
        // Update hidden textarea with content for form submission
        document.getElementById('content').value = quill.root.innerHTML;
    });
    
    // Form validation
    const form = document.querySelector('.needs-validation');
    
    form.addEventListener('submit', function(event) {
        // Update hidden textarea with content before validation
        document.getElementById('content').value = quill.root.innerHTML;
        
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Check if content is empty
        const content = document.getElementById('content').value.trim();
        if (content === '' || content === '<p><br></p>') {
            event.preventDefault();
            alert('Por favor, informe o conteúdo do modelo.');
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
