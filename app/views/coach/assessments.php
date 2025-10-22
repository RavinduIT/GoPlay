<?php 
$title = 'Client Assessments - Coach Dashboard';
$additionalCSS = ['/public/css/coach/assessments.css'];
$additionalJS = ['/public/js/pages/coach-assessments.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Professional Assessments Styles */
    :root {
        --primary-color: #3b82f6;
        --primary-dark: #2563eb;
        --primary-light: #60a5fa;
        --primary-hover: #2563eb;
        --secondary-color: #06b6d4;
        --accent-color: #f59e0b;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --success-color: #10b981;
        --info-color: #0ea5e9;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-light: #9ca3af;
        --text-muted: #94a3b8;
        --background-white: #ffffff;
        --background-light: #f9fafb;
        --background-gray: #f3f4f6;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        --border-radius: 10px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-fast: all 0.15s ease-in-out;
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
        background: var(--background-light);
    }

    .main-content {
        flex: 1;
        padding: 2rem;
        margin-left: 280px;
    }

    .page-header {
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        padding: 2.5rem;
        border-bottom: none;
        border-radius: 0 0 20px 20px;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow-xl);
        color: white;
        position: relative;
        overflow: hidden;
        animation: slideDown 0.5s ease-out;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.5;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.8;
        }
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .page-subtitle {
        color: rgba(255, 255, 255, 0.9);
        margin-top: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: var(--border-radius);
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .btn-primary {
        background: white;
        color: var(--primary-color);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover {
        background: rgba(255, 255, 255, 0.95);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        background: var(--background-white);
        padding: 0.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .filter-tab {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 500;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-tab.active {
        background: var(--primary-color);
        color: white;
    }

    .filter-tab:not(.active):hover {
        background: var(--background-gray);
        color: var(--text-primary);
    }

    /* Assessment Cards */
    .assessments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .assessment-card {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: var(--transition);
    }

    .assessment-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .assessment-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
        color: white;
        position: relative;
    }

    .assessment-type {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .assessment-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .assessment-client {
        opacity: 0.9;
        font-size: 0.9rem;
        margin: 0;
    }

    .assessment-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-completed {
        background: rgba(16, 185, 129, 0.2);
        color: white;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: white;
    }

    .status-scheduled {
        background: rgba(59, 130, 246, 0.2);
        color: white;
    }

    .assessment-content {
        padding: 1.5rem;
    }

    .assessment-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .detail-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 50%;
        font-size: 0.7rem;
    }

    .assessment-metrics {
        margin-bottom: 1.5rem;
    }

    .metrics-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }

    .metric-item {
        text-align: center;
        padding: 1rem;
        background: var(--background-light);
        border-radius: var(--border-radius);
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1;
    }

    .metric-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .metric-change {
        font-size: 0.7rem;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .metric-improved {
        color: var(--success-color);
    }

    .metric-declined {
        color: var(--danger-color);
    }

    .assessment-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }

    .btn-outline:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Quick Stats */
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--background-white);
        padding: 1.5rem;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        text-align: center;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem auto;
        background: var(--primary-light);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }

    .stat-label {
        color: var(--text-secondary);
        margin-top: 0.5rem;
        font-size: 0.9rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .empty-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .empty-description {
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .assessments-grid {
            grid-template-columns: 1fr;
        }

        .assessment-details {
            grid-template-columns: 1fr;
        }

        .assessment-actions {
            flex-direction: column;
        }

        .filter-tabs {
            flex-wrap: wrap;
        }

        .quick-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Client Assessments</h1>
                <p class="page-subtitle">Track and monitor your clients' fitness progress and performance</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportAssessments()">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <button class="btn btn-primary" onclick="scheduleAssessment()">
                    <i class="fas fa-plus"></i>
                    Schedule Assessment
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-value">24</div>
                <div class="stat-label">Total Assessments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Pending Reviews</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value">18</div>
                <div class="stat-label">Completed This Month</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-value">85%</div>
                <div class="stat-label">Improvement Rate</div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterAssessments('all')">All</button>
            <button class="filter-tab" onclick="filterAssessments('completed')">Completed</button>
            <button class="filter-tab" onclick="filterAssessments('pending')">Pending</button>
            <button class="filter-tab" onclick="filterAssessments('scheduled')">Scheduled</button>
        </div>

        <!-- Assessments Grid -->
        <div class="assessments-grid" id="assessmentsGrid">
            <!-- Assessment Cards will be populated by JavaScript -->
        </div>

        <!-- Empty State (Hidden by default) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="empty-title">No Assessments Found</h3>
            <p class="empty-description">
                Schedule your first client assessment to start tracking fitness progress and performance metrics.
            </p>
            <button class="btn btn-primary" onclick="scheduleAssessment()">
                <i class="fas fa-plus"></i>
                Schedule First Assessment
            </button>
        </div>
    </main>
</div>

<script>
// Sample assessments data
const sampleAssessments = [
    {
        id: 1,
        type: "Initial Fitness",
        title: "Comprehensive Fitness Assessment",
        client: "John Davidson",
        clientId: 1,
        status: "completed",
        scheduledDate: "2024-03-10",
        completedDate: "2024-03-10",
        duration: "60 min",
        assessor: "Dr. Smith",
        metrics: {
            bodyWeight: { value: 82, unit: "kg", change: -2.5, improved: true },
            bodyFat: { value: 15.2, unit: "%", change: -1.8, improved: true },
            vo2Max: { value: 45, unit: "ml/kg/min", change: +3.2, improved: true },
            flexibility: { value: 8.5, unit: "/10", change: +1.2, improved: true }
        },
        notes: "Excellent progress in cardiovascular endurance and flexibility.",
        nextAssessment: "2024-04-10"
    },
    {
        id: 2,
        type: "Performance",
        title: "Athletic Performance Evaluation",
        client: "Sarah Mitchell",
        clientId: 2,
        status: "pending",
        scheduledDate: "2024-03-15",
        duration: "45 min",
        assessor: "Coach Johnson",
        metrics: {
            speed: { value: 12.8, unit: "m/s", change: null, improved: null },
            agility: { value: null, unit: "sec", change: null, improved: null },
            power: { value: null, unit: "watts", change: null, improved: null },
            endurance: { value: null, unit: "min", change: null, improved: null }
        },
        notes: "Focus on explosive power and agility improvements.",
        nextAssessment: null
    },
    {
        id: 3,
        type: "Functional",
        title: "Functional Movement Screen",
        client: "Mike Rodriguez",
        clientId: 3,
        status: "completed",
        scheduledDate: "2024-03-08",
        completedDate: "2024-03-08",
        duration: "30 min",
        assessor: "Dr. Wilson",
        metrics: {
            mobility: { value: 16, unit: "/21", change: +2, improved: true },
            stability: { value: 8, unit: "/10", change: +1, improved: true },
            balance: { value: 9, unit: "/10", change: 0, improved: null },
            coordination: { value: 7.5, unit: "/10", change: +0.5, improved: true }
        },
        notes: "Significant improvement in shoulder mobility and core stability.",
        nextAssessment: "2024-04-08"
    },
    {
        id: 4,
        type: "Strength",
        title: "Strength & Power Assessment",
        client: "Emma Wilson",
        clientId: 4,
        status: "scheduled",
        scheduledDate: "2024-03-18",
        duration: "50 min",
        assessor: "Coach Davis",
        metrics: {
            benchPress: { value: null, unit: "kg", change: null, improved: null },
            squat: { value: null, unit: "kg", change: null, improved: null },
            deadlift: { value: null, unit: "kg", change: null, improved: null },
            pullUps: { value: null, unit: "reps", change: null, improved: null }
        },
        notes: "Baseline strength assessment for new training program.",
        nextAssessment: null
    },
    {
        id: 5,
        type: "Recovery",
        title: "Recovery & Wellness Check",
        client: "David Parker",
        clientId: 5,
        status: "completed",
        scheduledDate: "2024-03-05",
        completedDate: "2024-03-05",
        duration: "25 min",
        assessor: "Dr. Brown",
        metrics: {
            hrv: { value: 42, unit: "ms", change: +5, improved: true },
            sleepQuality: { value: 7.8, unit: "/10", change: +0.8, improved: true },
            stressLevel: { value: 3.2, unit: "/10", change: -1.1, improved: true },
            energy: { value: 8.5, unit: "/10", change: +1.5, improved: true }
        },
        notes: "Excellent recovery metrics. Continue current protocol.",
        nextAssessment: "2024-04-05"
    }
];

// Type icons and colors
const typeConfig = {
    'Initial Fitness': { icon: 'fas fa-heartbeat', color: '#ef4444' },
    'Performance': { icon: 'fas fa-trophy', color: '#f59e0b' },
    'Functional': { icon: 'fas fa-user-circle', color: '#10b981' },
    'Strength': { icon: 'fas fa-dumbbell', color: '#8b5cf6' },
    'Recovery': { icon: 'fas fa-bed', color: '#06b6d4' }
};

let currentFilter = 'all';

// Render assessments
function renderAssessments() {
    const grid = document.getElementById('assessmentsGrid');
    const emptyState = document.getElementById('emptyState');

    let filteredAssessments = sampleAssessments;
    
    if (currentFilter !== 'all') {
        filteredAssessments = sampleAssessments.filter(assessment => assessment.status === currentFilter);
    }

    if (filteredAssessments.length === 0) {
        grid.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    grid.style.display = 'grid';
    emptyState.style.display = 'none';

    grid.innerHTML = filteredAssessments.map(assessment => `
        <div class="assessment-card">
            <div class="assessment-header">
                <div class="assessment-type">${assessment.type}</div>
                <h3 class="assessment-title">${assessment.title}</h3>
                <p class="assessment-client">Client: ${assessment.client}</p>
                <div class="assessment-status status-${assessment.status}">
                    ${assessment.status.charAt(0).toUpperCase() + assessment.status.slice(1)}
                </div>
            </div>
            
            <div class="assessment-content">
                <div class="assessment-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <span>${assessment.status === 'completed' ? 'Completed' : 'Scheduled'}: ${formatDate(assessment.completedDate || assessment.scheduledDate)}</span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span>Duration: ${assessment.duration}</span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <span>Assessor: ${assessment.assessor}</span>
                    </div>
                    ${assessment.nextAssessment ? `
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <span>Next: ${formatDate(assessment.nextAssessment)}</span>
                        </div>
                    ` : ''}
                </div>

                ${assessment.status === 'completed' ? `
                    <div class="assessment-metrics">
                        <div class="metrics-title">Key Metrics</div>
                        <div class="metrics-grid">
                            ${Object.entries(assessment.metrics).map(([key, metric]) => `
                                <div class="metric-item">
                                    <div class="metric-value">${metric.value || '--'}</div>
                                    <div class="metric-label">${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())} ${metric.unit}</div>
                                    ${metric.change !== null ? `
                                        <div class="metric-change ${metric.improved ? 'metric-improved' : 'metric-declined'}">
                                            ${metric.change > 0 ? '+' : ''}${metric.change}
                                        </div>
                                    ` : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}

                <div class="assessment-actions">
                    <button class="btn btn-outline btn-sm" onclick="viewAssessment(${assessment.id})">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    ${assessment.status === 'scheduled' || assessment.status === 'pending' ? `
                        <button class="btn btn-primary btn-sm" onclick="startAssessment(${assessment.id})">
                            <i class="fas fa-play"></i>
                            ${assessment.status === 'scheduled' ? 'Start' : 'Complete'}
                        </button>
                    ` : `
                        <button class="btn btn-primary btn-sm" onclick="shareResults(${assessment.id})">
                            <i class="fas fa-share"></i>
                            Share Results
                        </button>
                    `}
                </div>
            </div>
        </div>
    `).join('');
}

// Filter functionality
function filterAssessments(filter) {
    currentFilter = filter;
    
    // Update active tab
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    renderAssessments();
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function scheduleAssessment() {
    alert('Schedule Assessment - This would open a scheduling modal with client selection and assessment type');
}

function viewAssessment(id) {
    console.log('View assessment:', id);
    alert(`View Assessment ${id} - This would show detailed assessment results and history`);
}

function startAssessment(id) {
    console.log('Start assessment:', id);
    alert(`Start Assessment ${id} - This would open the assessment interface`);
}

function shareResults(id) {
    console.log('Share results:', id);
    alert(`Share Results ${id} - This would generate a shareable report for the client`);
}

function exportAssessments() {
    alert('Export Assessments - This would generate a comprehensive report of all assessments');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    renderAssessments();
});
</script>