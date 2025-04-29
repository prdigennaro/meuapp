<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Painel do Usuário</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="/user/new-document" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-plus"></i> Novo Documento
                    </a>
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshStats">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Meus Documentos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['myDocuments']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-center">
                            <a href="/user/my-documents" class="small stretched-link text-decoration-none">Ver todos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Documentos Pendentes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pendingDocuments']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-center">
                            <a href="/user/my-documents?status=pending" class="small stretched-link text-decoration-none">Ver pendentes</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Documentos do Departamento</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['departmentDocuments']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-center">
                            <a href="/user/incoming-documents" class="small stretched-link text-decoration-none">Ver recebidos</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Novo Documento</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <a href="/user/new-document" class="btn btn-sm btn-warning">
                                            <i class="fas fa-plus"></i> Criar
                                        </a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-center">
                            <a href="/user/new-document" class="small stretched-link text-decoration-none">Criar documento</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-xl-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Meus Documentos por Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="myDocumentsByStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Meus Documentos por Tipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="myDocumentsByTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Documents Row -->
            <div class="row">
                <div class="col-xl-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Meus Documentos Recentes</h6>
                            <a href="/user/my-documents" class="btn btn-sm btn-primary">Ver Todos</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Protocolo</th>
                                            <th>Título</th>
                                            <th>Tipo</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($stats['recentCreatedDocuments'])): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Nenhum documento encontrado</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($stats['recentCreatedDocuments'] as $document): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($document['protocol_number']); ?></td>
                                                    <td>
                                                        <a href="/user/document?id=<?php echo $document['id']; ?>">
                                                            <?php echo htmlspecialchars($document['title']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($document['document_type_name']); ?></td>
                                                    <td>
                                                        <span class="badge rounded-pill 
                                                            <?php
                                                            switch ($document['status']) {
                                                                case DOC_STATUS_DRAFT:
                                                                    echo 'bg-secondary';
                                                                    break;
                                                                case DOC_STATUS_PENDING:
                                                                    echo 'bg-warning text-dark';
                                                                    break;
                                                                case DOC_STATUS_IN_REVIEW:
                                                                    echo 'bg-info text-dark';
                                                                    break;
                                                                case DOC_STATUS_APPROVED:
                                                                    echo 'bg-success';
                                                                    break;
                                                                case DOC_STATUS_REJECTED:
                                                                    echo 'bg-danger';
                                                                    break;
                                                                case DOC_STATUS_COMPLETED:
                                                                    echo 'bg-primary';
                                                                    break;
                                                                case DOC_STATUS_ARCHIVED:
                                                                    echo 'bg-dark';
                                                                    break;
                                                            }
                                                            ?>">
                                                            <?php
                                                            switch ($document['status']) {
                                                                case DOC_STATUS_DRAFT:
                                                                    echo 'Rascunho';
                                                                    break;
                                                                case DOC_STATUS_PENDING:
                                                                    echo 'Pendente';
                                                                    break;
                                                                case DOC_STATUS_IN_REVIEW:
                                                                    echo 'Em Análise';
                                                                    break;
                                                                case DOC_STATUS_APPROVED:
                                                                    echo 'Aprovado';
                                                                    break;
                                                                case DOC_STATUS_REJECTED:
                                                                    echo 'Rejeitado';
                                                                    break;
                                                                case DOC_STATUS_COMPLETED:
                                                                    echo 'Concluído';
                                                                    break;
                                                                case DOC_STATUS_ARCHIVED:
                                                                    echo 'Arquivado';
                                                                    break;
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($document['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Documentos Recebidos pelo Departamento</h6>
                            <a href="/user/incoming-documents" class="btn btn-sm btn-primary">Ver Todos</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Protocolo</th>
                                            <th>Título</th>
                                            <th>Criado por</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($stats['recentDepartmentDocuments'])): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Nenhum documento encontrado</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($stats['recentDepartmentDocuments'] as $document): ?>
                                                <?php if ($document['created_by'] != $_SESSION['user_id']): // Only show documents created by others ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($document['protocol_number']); ?></td>
                                                        <td>
                                                            <a href="/user/document?id=<?php echo $document['id']; ?>">
                                                                <?php echo htmlspecialchars($document['title']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($document['created_by_name']); ?></td>
                                                        <td>
                                                            <span class="badge rounded-pill 
                                                                <?php
                                                                switch ($document['status']) {
                                                                    case DOC_STATUS_DRAFT:
                                                                        echo 'bg-secondary';
                                                                        break;
                                                                    case DOC_STATUS_PENDING:
                                                                        echo 'bg-warning text-dark';
                                                                        break;
                                                                    case DOC_STATUS_IN_REVIEW:
                                                                        echo 'bg-info text-dark';
                                                                        break;
                                                                    case DOC_STATUS_APPROVED:
                                                                        echo 'bg-success';
                                                                        break;
                                                                    case DOC_STATUS_REJECTED:
                                                                        echo 'bg-danger';
                                                                        break;
                                                                    case DOC_STATUS_COMPLETED:
                                                                        echo 'bg-primary';
                                                                        break;
                                                                    case DOC_STATUS_ARCHIVED:
                                                                        echo 'bg-dark';
                                                                        break;
                                                                }
                                                                ?>">
                                                                <?php
                                                                switch ($document['status']) {
                                                                    case DOC_STATUS_DRAFT:
                                                                        echo 'Rascunho';
                                                                        break;
                                                                    case DOC_STATUS_PENDING:
                                                                        echo 'Pendente';
                                                                        break;
                                                                    case DOC_STATUS_IN_REVIEW:
                                                                        echo 'Em Análise';
                                                                        break;
                                                                    case DOC_STATUS_APPROVED:
                                                                        echo 'Aprovado';
                                                                        break;
                                                                    case DOC_STATUS_REJECTED:
                                                                        echo 'Rejeitado';
                                                                        break;
                                                                    case DOC_STATUS_COMPLETED:
                                                                        echo 'Concluído';
                                                                        break;
                                                                    case DOC_STATUS_ARCHIVED:
                                                                        echo 'Arquivado';
                                                                        break;
                                                                }
                                                                ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('d/m/Y', strtotime($document['created_at'])); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if (count(array_filter($stats['recentDepartmentDocuments'], function($doc) { return $doc['created_by'] != $_SESSION['user_id']; })) === 0): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Nenhum documento recebido</td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart for Documents by Status
    const statusCtx = document.getElementById('myDocumentsByStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: [
                'Rascunho', 
                'Pendente', 
                'Em Análise', 
                'Aprovado', 
                'Rejeitado', 
                'Concluído', 
                'Arquivado'
            ],
            datasets: [{
                data: [
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_DRAFT]) ? $myDocumentsByStatus[DOC_STATUS_DRAFT] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_PENDING]) ? $myDocumentsByStatus[DOC_STATUS_PENDING] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_IN_REVIEW]) ? $myDocumentsByStatus[DOC_STATUS_IN_REVIEW] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_APPROVED]) ? $myDocumentsByStatus[DOC_STATUS_APPROVED] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_REJECTED]) ? $myDocumentsByStatus[DOC_STATUS_REJECTED] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_COMPLETED]) ? $myDocumentsByStatus[DOC_STATUS_COMPLETED] : 0; ?>,
                    <?php echo isset($myDocumentsByStatus[DOC_STATUS_ARCHIVED]) ? $myDocumentsByStatus[DOC_STATUS_ARCHIVED] : 0; ?>
                ],
                backgroundColor: [
                    '#6c757d', // Rascunho (secondary)
                    '#ffc107', // Pendente (warning)
                    '#17a2b8', // Em Análise (info)
                    '#28a745', // Aprovado (success)
                    '#dc3545', // Rejeitado (danger)
                    '#007bff', // Concluído (primary)
                    '#343a40'  // Arquivado (dark)
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    // Chart for Documents by Type
    const typeCtx = document.getElementById('myDocumentsByTypeChart').getContext('2d');
    const typeLabels = [];
    const typeData = [];
    const typeColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#5a5c69', '#858796', '#75c1e6', '#9966cc', '#f08080'
    ];
    
    <?php
    $index = 0;
    foreach ($myDocumentsByType as $typeName => $count) {
        echo "typeLabels.push('" . addslashes($typeName) . "');\n";
        echo "typeData.push(" . $count . ");\n";
        $index++;
    }
    ?>
    
    const typeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: typeColors.slice(0, typeData.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    // Refresh stats button
    document.getElementById('refreshStats').addEventListener('click', function() {
        location.reload();
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
