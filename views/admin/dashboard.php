<?php include 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'views/partials/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Painel Administrativo</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Imprimir Relatório
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshStats">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Dashboard Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Documentos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['totalDocuments']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Usuários</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['totalUsers']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Departamentos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['totalDepartments']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Fluxos de Trabalho</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['totalWorkflows']; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row mb-4">
                <div class="col-xl-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Documentos por Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="documentsByStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Documentos por Tipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie">
                                <canvas id="documentsByTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Documents Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documentos Recentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="recentDocumentsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Protocolo</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Criado por</th>
                                    <th>Departamento</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($stats['recentDocuments'])): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum documento encontrado</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($stats['recentDocuments'] as $document): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($document['protocol_number']); ?></td>
                                            <td>
                                                <a href="/user/document?id=<?php echo $document['id']; ?>">
                                                    <?php echo htmlspecialchars($document['title']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($document['document_type_name']); ?></td>
                                            <td><?php echo htmlspecialchars($document['created_by_name']); ?></td>
                                            <td><?php echo htmlspecialchars($document['current_department_name']); ?></td>
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
                                            <td><?php echo date('d/m/Y H:i', strtotime($document['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart for Documents by Status
    const statusCtx = document.getElementById('documentsByStatusChart').getContext('2d');
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
                    <?php echo isset($documentsByStatus[DOC_STATUS_DRAFT]) ? $documentsByStatus[DOC_STATUS_DRAFT] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_PENDING]) ? $documentsByStatus[DOC_STATUS_PENDING] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_IN_REVIEW]) ? $documentsByStatus[DOC_STATUS_IN_REVIEW] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_APPROVED]) ? $documentsByStatus[DOC_STATUS_APPROVED] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_REJECTED]) ? $documentsByStatus[DOC_STATUS_REJECTED] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_COMPLETED]) ? $documentsByStatus[DOC_STATUS_COMPLETED] : 0; ?>,
                    <?php echo isset($documentsByStatus[DOC_STATUS_ARCHIVED]) ? $documentsByStatus[DOC_STATUS_ARCHIVED] : 0; ?>
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
    const typeCtx = document.getElementById('documentsByTypeChart').getContext('2d');
    const typeLabels = [];
    const typeData = [];
    const typeColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
        '#5a5c69', '#858796', '#75c1e6', '#9966cc', '#f08080'
    ];
    
    <?php
    $index = 0;
    foreach ($documentsByType as $typeName => $count) {
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
    
    // Initialize DataTable
    $(document).ready(function() {
        // Primeiro, verificar se a tabela está estruturada corretamente
        const tableHeaders = $('#recentDocumentsTable thead th').length;
        let allRowsCorrect = true;
        
        // Se a tabela estiver vazia, não tentamos inicializar o DataTables
        if ($('#recentDocumentsTable tbody tr').length === 1 && 
            $('#recentDocumentsTable tbody tr td[colspan]').length === 1) {
            // Tabela vazia com mensagem "Nenhum documento encontrado"
            $('#recentDocumentsTable').addClass('table-striped');
            console.log('Tabela vazia, não inicializando DataTables');
            return;
        }
        
        // Verifica se todas as linhas têm o mesmo número de colunas que o cabeçalho
        $('#recentDocumentsTable tbody tr').each(function() {
            const rowCells = $(this).find('td').length;
            if (rowCells !== tableHeaders && !$(this).find('td[colspan]').length) {
                allRowsCorrect = false;
                console.error(`Linha com ${rowCells} células, esperado ${tableHeaders}`);
            }
        });
        
        if (allRowsCorrect) {
            try {
                $('#recentDocumentsTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json'
                    },
                    order: [[6, 'desc']], // Sort by date column
                    pageLength: 5,
                    responsive: true
                });
            } catch (e) {
                console.error('Erro ao inicializar DataTable:', e);
                // Modo de fallback se o DataTable falhar
                $('#recentDocumentsTable').addClass('table-striped');
            }
        } else {
            console.error('Estrutura da tabela incorreta, não inicializando DataTables');
            $('#recentDocumentsTable').addClass('table-striped');
        }
    });
    
    // Refresh stats button
    document.getElementById('refreshStats').addEventListener('click', function() {
        location.reload();
    });
});
</script>

<?php include 'views/partials/footer.php'; ?>
