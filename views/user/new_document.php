<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Criar Novo Documento</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/user/my-documents" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Meus Documentos
                    </a>
                </div>
            </div>

            <div class="wizard">
                <div class="wizard-inner">
                    <div class="connecting-line"></div>
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="nav-item active">
                            <a href="#step1" data-bs-toggle="tab" aria-controls="step1" role="tab" 
                               class="nav-link active" aria-expanded="true">
                                <span class="round-tab">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <span class="d-none d-md-block">Tipo de Documento</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item disabled">
                            <a href="#step2" data-bs-toggle="tab" aria-controls="step2" role="tab" 
                               class="nav-link disabled" aria-expanded="false">
                                <span class="round-tab">
                                    <i class="fas fa-list-alt"></i>
                                </span>
                                <span class="d-none d-md-block">Modelo</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item disabled">
                            <a href="#step3" data-bs-toggle="tab" aria-controls="step3" role="tab" 
                               class="nav-link disabled" aria-expanded="false">
                                <span class="round-tab">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <span class="d-none d-md-block">Preencher Dados</span>
                            </a>
                        </li>
                        <li role="presentation" class="nav-item disabled">
                            <a href="#step4" data-bs-toggle="tab" aria-controls="step4" role="tab" 
                               class="nav-link disabled" aria-expanded="false">
                                <span class="round-tab">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="d-none d-md-block">Finalizar</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <form id="documentForm" method="post" action="/user/document/create" class="needs-validation" novalidate>
                    <div class="tab-content">
                        <!-- Step 1: Document Type Selection -->
                        <div class="tab-pane active" role="tabpanel" id="step1">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Selecione o Tipo de Documento</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Selecione o tipo de documento que deseja criar. Cada tipo tem características e modelos específicos.
                                    </div>
                                    
                                    <div class="row document-types-grid">
                                        <?php foreach ($documentTypes as $type): ?>
                                            <div class="col-md-4 mb-4">
                                                <div class="card h-100 document-type-card" data-type-id="<?php echo $type['id']; ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title">
                                                            <span class="badge bg-primary"><?php echo htmlspecialchars($type['code']); ?></span>
                                                            <?php echo htmlspecialchars($type['name']); ?>
                                                        </h5>
                                                        <p class="card-text"><?php echo htmlspecialchars($type['description']); ?></p>
                                                    </div>
                                                    <div class="card-footer bg-transparent text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary select-document-type" 
                                                                data-type-id="<?php echo $type['id']; ?>"
                                                                data-type-name="<?php echo htmlspecialchars($type['name']); ?>">
                                                            Selecionar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <input type="hidden" id="document_type_id" name="document_type_id" required>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-secondary" onclick="location.href='/user/dashboard'">Cancelar</button>
                                    <button type="button" class="btn btn-primary next-step" disabled>Próximo</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 2: Template Selection -->
                        <div class="tab-pane" role="tabpanel" id="step2">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Selecione o Modelo</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Escolha um dos modelos disponíveis para o tipo de documento: <strong id="selected-type-name"></strong>
                                    </div>
                                    
                                    <div id="templates-container" class="row">
                                        <!-- Templates will be loaded here dynamically -->
                                        <div class="col-12 text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-2">Carregando modelos disponíveis...</p>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" id="template_id" name="template_id" required>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-secondary prev-step">Voltar</button>
                                    <button type="button" class="btn btn-primary next-step" disabled>Próximo</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 3: Fill Variables -->
                        <div class="tab-pane" role="tabpanel" id="step3">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Preencha os Dados do Documento</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Preencha os campos abaixo para gerar o documento. Estes dados serão utilizados para substituir as variáveis no modelo.
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="title" class="form-label">Título do Documento</label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                            <div class="invalid-feedback">
                                                Por favor, informe o título do documento.
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="description" class="form-label">Descrição (opcional)</label>
                                            <input type="text" class="form-control" id="description" name="description">
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div id="variables-form-container">
                                        <!-- Variables form fields will be generated here dynamically -->
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-2">Carregando variáveis do modelo...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-secondary prev-step">Voltar</button>
                                    <button type="button" class="btn btn-primary next-step">Próximo</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Step 4: Preview & Finalize -->
                        <div class="tab-pane" role="tabpanel" id="step4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Revisar e Finalizar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle"></i> 
                                        Confira as informações abaixo e clique em "Gerar Documento" para finalizar.
                                    </div>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <h5>Informações do Documento</h5>
                                            <table class="table table-bordered table-sm">
                                                <tr>
                                                    <th width="40%">Tipo de Documento:</th>
                                                    <td id="review-document-type"></td>
                                                </tr>
                                                <tr>
                                                    <th>Modelo:</th>
                                                    <td id="review-template"></td>
                                                </tr>
                                                <tr>
                                                    <th>Título:</th>
                                                    <td id="review-title"></td>
                                                </tr>
                                                <tr>
                                                    <th>Descrição:</th>
                                                    <td id="review-description"></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Variáveis Preenchidas</h5>
                                            <div id="review-variables-container">
                                                <table class="table table-bordered table-sm" id="review-variables-table">
                                                    <thead>
                                                        <tr>
                                                            <th width="40%">Variável</th>
                                                            <th>Valor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- Variables will be displayed here dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-warning">
                                        <i class="fas fa-info-circle"></i> 
                                        Ao clicar em "Gerar Documento", o sistema irá processar as informações e criar o documento com ajuda de inteligência artificial.
                                        Este processo pode levar alguns segundos.
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <button type="button" class="btn btn-secondary prev-step">Voltar</button>
                                    <button type="submit" class="btn btn-success" id="submit-btn">
                                        <i class="fas fa-file-alt"></i> Gerar Documento
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<style>
.wizard {
    margin-bottom: 40px;
}

.wizard .nav-tabs {
    position: relative;
    margin-bottom: 30px;
    border-bottom-color: transparent;
}

.wizard .nav-tabs > li {
    width: 25%;
    text-align: center;
}

.wizard .nav-tabs > li > a {
    border: none;
    background-color: transparent;
    padding: 15px 0;
}

.wizard .nav-tabs > li a.disabled {
    cursor: not-allowed;
}

.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
    border: none;
    color: #007bff;
}

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 75%;
    margin: 0 auto;
    left: 0;
    right: 0;
    top: 50%;
    z-index: 1;
}

.wizard .nav-tabs > li a .round-tab {
    width: 50px;
    height: 50px;
    line-height: 50px;
    display: inline-block;
    border-radius: 50%;
    background: #fff;
    text-align: center;
    z-index: 2;
    position: relative;
    border: 2px solid #e0e0e0;
}

.wizard .nav-tabs > li a.active .round-tab {
    border: 2px solid #007bff;
}

.wizard .nav-tabs > li a.disabled .round-tab {
    border: 2px solid #f5f5f5;
}

.wizard .nav-tabs > li a i {
    color: #555;
}

.wizard .nav-tabs > li a.active i, .wizard .nav-tabs > li a:hover i {
    color: #007bff;
}

.wizard .nav-tabs > li a.disabled i {
    color: #999;
}

.document-type-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.document-type-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.document-type-card.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.template-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.template-card.selected {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.variable-field {
    margin-bottom: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDocumentType = null;
    let currentTemplate = null;
    let currentTemplateVariables = [];
    let currentStep = 1;
    
    // Document type selection
    const documentTypeCards = document.querySelectorAll('.document-type-card');
    const selectTypeButtons = document.querySelectorAll('.select-document-type');
    const documentTypeInput = document.getElementById('document_type_id');
    
    selectTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const typeId = this.getAttribute('data-type-id');
            const typeName = this.getAttribute('data-type-name');
            
            // Clear previous selection
            documentTypeCards.forEach(card => card.classList.remove('selected'));
            
            // Select this card
            const parentCard = this.closest('.document-type-card');
            parentCard.classList.add('selected');
            
            // Update hidden input
            documentTypeInput.value = typeId;
            currentDocumentType = {
                id: typeId,
                name: typeName
            };
            
            // Show type name in next step
            document.getElementById('selected-type-name').textContent = typeName;
            
            // Enable next button
            document.querySelector('#step1 .next-step').disabled = false;
        });
    });
    
    // Navigation between steps
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    const wizardTabs = document.querySelectorAll('.nav-tabs .nav-link');
    
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (currentStep < 4) {
                // Process current step
                if (currentStep === 1) {
                    // Load templates for the selected document type
                    loadTemplates(currentDocumentType.id);
                } else if (currentStep === 2) {
                    // Load variables form for the selected template
                    loadVariablesForm(currentTemplate.id);
                } else if (currentStep === 3) {
                    // Populate review page
                    populateReviewPage();
                }
                
                // Move to next step
                currentStep++;
                
                // Update tabs
                wizardTabs.forEach((tab, index) => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-expanded', 'false');
                    
                    if (index < currentStep) {
                        tab.classList.remove('disabled');
                    }
                    
                    if (index === currentStep - 1) {
                        tab.classList.add('active');
                        tab.setAttribute('aria-expanded', 'true');
                    }
                });
                
                // Show the next step
                document.querySelectorAll('.tab-pane').forEach((pane, index) => {
                    pane.classList.remove('active');
                    if (index === currentStep - 1) {
                        pane.classList.add('active');
                    }
                });
            }
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (currentStep > 1) {
                // Move to previous step
                currentStep--;
                
                // Update tabs
                wizardTabs.forEach((tab, index) => {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-expanded', 'false');
                    
                    if (index === currentStep - 1) {
                        tab.classList.add('active');
                        tab.setAttribute('aria-expanded', 'true');
                    }
                });
                
                // Show the previous step
                document.querySelectorAll('.tab-pane').forEach((pane, index) => {
                    pane.classList.remove('active');
                    if (index === currentStep - 1) {
                        pane.classList.add('active');
                    }
                });
            }
        });
    });
    
    // Load templates based on document type
    function loadTemplates(documentTypeId) {
        const templatesContainer = document.getElementById('templates-container');
        templatesContainer.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando modelos disponíveis...</p>
            </div>
        `;
        
        // Disable next button until a template is selected
        document.querySelector('#step2 .next-step').disabled = true;
        
        // Make an AJAX request to get templates
        fetch(`/api/templates/by-document-type?document_type_id=${documentTypeId}`)
            .then(response => response.json())
            .then(templates => {
                if (templates.length === 0) {
                    templatesContainer.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                            <h4>Nenhum modelo encontrado</h4>
                            <p class="text-muted">Não existem modelos cadastrados para este tipo de documento.</p>
                        </div>
                    `;
                    return;
                }
                
                let templatesHtml = '';
                templates.forEach(template => {
                    templatesHtml += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 template-card" data-template-id="${template.id}">
                                <div class="card-body">
                                    <h5 class="card-title">${template.name}</h5>
                                    <p class="card-text">${template.description || 'Sem descrição disponível.'}</p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-tags"></i> ${template.variables_count} variáveis
                                        </small>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-template" 
                                            data-template-id="${template.id}"
                                            data-template-name="${template.name}">
                                        Selecionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                templatesContainer.innerHTML = templatesHtml;
                
                // Add event listeners to template selection buttons
                document.querySelectorAll('.select-template').forEach(button => {
                    button.addEventListener('click', function() {
                        const templateId = this.getAttribute('data-template-id');
                        const templateName = this.getAttribute('data-template-name');
                        
                        // Clear previous selection
                        document.querySelectorAll('.template-card').forEach(card => card.classList.remove('selected'));
                        
                        // Select this card
                        const parentCard = this.closest('.template-card');
                        parentCard.classList.add('selected');
                        
                        // Update hidden input
                        document.getElementById('template_id').value = templateId;
                        currentTemplate = {
                            id: templateId,
                            name: templateName
                        };
                        
                        // Enable next button
                        document.querySelector('#step2 .next-step').disabled = false;
                    });
                });
            })
            .catch(error => {
                console.error('Error loading templates:', error);
                templatesContainer.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h4>Erro ao carregar modelos</h4>
                        <p class="text-muted">Ocorreu um erro ao carregar os modelos. Por favor, tente novamente.</p>
                    </div>
                `;
            });
    }
    
    // Load variables form based on template
    function loadVariablesForm(templateId) {
        const variablesContainer = document.getElementById('variables-form-container');
        variablesContainer.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando variáveis do modelo...</p>
            </div>
        `;
        
        // Make an AJAX request to get template variables
        fetch(`/api/templates/variables?id=${templateId}`)
            .then(response => response.json())
            .then(data => {
                const variables = JSON.parse(data);
                currentTemplateVariables = variables;
                
                if (variables.length === 0) {
                    variablesContainer.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle"></i> 
                            Este modelo não possui variáveis definidas. Você pode prosseguir para gerar o documento.
                        </div>
                    `;
                    return;
                }
                
                let formHtml = '';
                variables.forEach((variable, index) => {
                    formHtml += `
                        <div class="variable-field">
                            <label for="var_${variable}" class="form-label">${formatVariableName(variable)}</label>
                            <textarea class="form-control" id="var_${variable}" name="var_${variable}" rows="2" required></textarea>
                            <div class="invalid-feedback">
                                Por favor, preencha este campo.
                            </div>
                        </div>
                    `;
                    
                    // Add separator after every second variable except the last one
                    if (index % 2 === 1 && index < variables.length - 1) {
                        formHtml += '<hr>';
                    }
                });
                
                variablesContainer.innerHTML = formHtml;
            })
            .catch(error => {
                console.error('Error loading variables:', error);
                variablesContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Ocorreu um erro ao carregar as variáveis do modelo. Por favor, tente novamente.
                    </div>
                `;
            });
    }
    
    // Populate review page with form data
    function populateReviewPage() {
        // Document info
        document.getElementById('review-document-type').textContent = currentDocumentType.name;
        document.getElementById('review-template').textContent = currentTemplate.name;
        document.getElementById('review-title').textContent = document.getElementById('title').value;
        document.getElementById('review-description').textContent = document.getElementById('description').value || 'Não informada';
        
        // Variables
        const variablesTableBody = document.querySelector('#review-variables-table tbody');
        variablesTableBody.innerHTML = '';
        
        if (currentTemplateVariables.length === 0) {
            document.getElementById('review-variables-container').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i> 
                    Este modelo não possui variáveis definidas.
                </div>
            `;
        } else {
            currentTemplateVariables.forEach(variable => {
                const value = document.getElementById(`var_${variable}`).value;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatVariableName(variable)}</td>
                    <td>${value}</td>
                `;
                variablesTableBody.appendChild(row);
            });
        }
    }
    
    // Format variable name for display (convert snake_case to Title Case)
    function formatVariableName(variable) {
        return variable
            .split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
    
    // Form validation
    const form = document.querySelector('#documentForm');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
        
        if (form.checkValidity()) {
            // Show loading state on submit button
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gerando...';
            submitBtn.disabled = true;
        }
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
