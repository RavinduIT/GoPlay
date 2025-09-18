<?php 
$title = 'Training Programs - Coach Dashboard';
$additionalCSS = ['/public/css/coach/programs.css'];
$additionalJS = ['/public/js/pages/coach-programs.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* Professional Training Programs Styles */
    :root {
        --primary-color: #2563eb;
        --primary-dark: #1d4ed8;
        --primary-light: #dbeafe;
        --secondary-color: #10b981;
        --accent-color: #f59e0b;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --success-color: #10b981;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --text-light: #9ca3af;
        --background-white: #ffffff;
        --background-light: #f9fafb;
        --background-gray: #f3f4f6;
        --border-color: #e5e7eb;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --border-radius: 8px;
        --border-radius-lg: 12px;
        --transition: all 0.3s ease;
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
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
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--background-white);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--background-gray);
    }

    /* Programs Grid */
    .programs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .program-card {
        background: var(--background-white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: var(--transition);
    }

    .program-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .program-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        position: relative;
    }

    .program-type {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .program-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
    }

    .program-description {
        opacity: 0.9;
        font-size: 0.9rem;
        margin: 0;
    }

    .program-stats {
        position: absolute;
        top: 1rem;
        right: 1rem;
        text-align: center;
    }

    .program-duration {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .duration-label {
        font-size: 0.7rem;
        opacity: 0.8;
    }

    .program-content {
        padding: 1.5rem;
    }

    .program-details {
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

    .program-participants {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--background-light);
        border-radius: var(--border-radius);
    }

    .participants-avatars {
        display: flex;
        margin-left: -0.5rem;
    }

    .participant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid white;
        margin-left: -0.5rem;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .participants-count {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .program-progress {
        margin-bottom: 1.5rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .progress-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .progress-percentage {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--background-gray);
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .program-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
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

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .status-draft {
        background: rgba(156, 163, 175, 0.1);
        color: var(--text-secondary);
    }

    .status-completed {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-color);
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

        .programs-grid {
            grid-template-columns: 1fr;
        }

        .program-details {
            grid-template-columns: 1fr;
        }

        .program-actions {
            flex-direction: column;
        }
    }
</style>

<div class="dashboard-container">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Training Programs</h1>
                <p class="page-subtitle">Manage and create comprehensive training programs for your clients</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportPrograms()">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <button class="btn btn-primary" onclick="createProgram()">
                    <i class="fas fa-plus"></i>
                    Create Program
                </button>
            </div>
        </div>

        <!-- Programs Grid -->
        <div class="programs-grid" id="programsGrid">
            <!-- Program Cards will be populated by JavaScript -->
        </div>

        <!-- Empty State (Hidden by default) -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3 class="empty-title">No Training Programs Yet</h3>
            <p class="empty-description">
                Create your first training program to start organizing structured workouts for your clients.
            </p>
            <button class="btn btn-primary" onclick="createProgram()">
                <i class="fas fa-plus"></i>
                Create Your First Program
            </button>
        </div>
    </main>
</div>

<script>
// Sample training programs data
const samplePrograms = [
    {
        id: 1,
        title: "Strength & Conditioning Fundamentals",
        description: "A comprehensive 12-week program focusing on building core strength and cardiovascular endurance.",
        type: "Strength Training",
        duration: "12 weeks",
        sessions: 36,
        difficulty: "Beginner",
        participants: [
            { name: "John D", avatar: "JD" },
            { name: "Sarah M", avatar: "SM" },
            { name: "Mike R", avatar: "MR" }
        ],
        totalParticipants: 8,
        progress: 65,
        status: "active",
        createdDate: "2024-01-15",
        lastUpdated: "2024-03-10"
    },
    {
        id: 2,
        title: "Athletic Performance Enhancement",
        description: "Advanced training program designed for competitive athletes looking to improve performance.",
        type: "Performance",
        duration: "8 weeks",
        sessions: 24,
        difficulty: "Advanced",
        participants: [
            { name: "Alex T", avatar: "AT" },
            { name: "Emma W", avatar: "EW" }
        ],
        totalParticipants: 5,
        progress: 45,
        status: "active",
        createdDate: "2024-02-01",
        lastUpdated: "2024-03-08"
    },
    {
        id: 3,
        title: "Weight Loss & Fitness",
        description: "A balanced program combining cardio and strength training for effective weight management.",
        type: "Weight Loss",
        duration: "10 weeks",
        sessions: 30,
        difficulty: "Intermediate",
        participants: [
            { name: "Lisa K", avatar: "LK" },
            { name: "David P", avatar: "DP" },
            { name: "Rachel S", avatar: "RS" },
            { name: "Tom B", avatar: "TB" }
        ],
        totalParticipants: 12,
        progress: 80,
        status: "active",
        createdDate: "2024-01-08",
        lastUpdated: "2024-03-12"
    },
    {
        id: 4,
        title: "Flexibility & Mobility",
        description: "Focus on improving flexibility, mobility, and injury prevention through targeted exercises.",
        type: "Flexibility",
        duration: "6 weeks",
        sessions: 18,
        difficulty: "Beginner",
        participants: [
            { name: "Grace L", avatar: "GL" }
        ],
        totalParticipants: 3,
        progress: 100,
        status: "completed",
        createdDate: "2024-01-22",
        lastUpdated: "2024-03-05"
    },
    {
        id: 5,
        title: "Sports-Specific Training",
        description: "Customized training program for specific sports performance and skill development.",
        type: "Sport Specific",
        duration: "16 weeks",
        sessions: 48,
        difficulty: "Advanced",
        participants: [],
        totalParticipants: 0,
        progress: 0,
        status: "draft",
        createdDate: "2024-03-01",
        lastUpdated: "2024-03-01"
    }
];

// Difficulty icons and colors
const difficultyConfig = {
    'Beginner': { icon: 'fas fa-seedling', color: '#10b981' },
    'Intermediate': { icon: 'fas fa-chart-line', color: '#f59e0b' },
    'Advanced': { icon: 'fas fa-fire', color: '#ef4444' }
};

// Type icons
const typeIcons = {
    'Strength Training': 'fas fa-dumbbell',
    'Performance': 'fas fa-trophy',
    'Weight Loss': 'fas fa-heart',
    'Flexibility': 'fas fa-yoga',
    'Sport Specific': 'fas fa-futbol'
};

// Render programs
function renderPrograms() {
    const grid = document.getElementById('programsGrid');
    const emptyState = document.getElementById('emptyState');

    if (samplePrograms.length === 0) {
        grid.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    grid.style.display = 'grid';
    emptyState.style.display = 'none';

    grid.innerHTML = samplePrograms.map(program => `
        <div class="program-card">
            <div class="program-header">
                <div class="program-type">${program.type}</div>
                <h3 class="program-title">${program.title}</h3>
                <p class="program-description">${program.description}</p>
                <div class="program-stats">
                    <div class="program-duration">${program.duration.split(' ')[0]}</div>
                    <div class="duration-label">${program.duration.split(' ')[1]}</div>
                </div>
            </div>
            
            <div class="program-content">
                <div class="program-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="${typeIcons[program.type]}"></i>
                        </div>
                        <span>${program.sessions} Sessions</span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon" style="background: ${difficultyConfig[program.difficulty].color}20; color: ${difficultyConfig[program.difficulty].color}">
                            <i class="${difficultyConfig[program.difficulty].icon}"></i>
                        </div>
                        <span>${program.difficulty}</span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <span>Created ${formatDate(program.createdDate)}</span>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span>Updated ${formatDate(program.lastUpdated)}</span>
                    </div>
                </div>

                <div class="program-participants">
                    <div class="participants-avatars">
                        ${program.participants.slice(0, 3).map(p => `
                            <div class="participant-avatar" title="${p.name}">${p.avatar}</div>
                        `).join('')}
                        ${program.totalParticipants > 3 ? `
                            <div class="participant-avatar">+${program.totalParticipants - 3}</div>
                        ` : ''}
                    </div>
                    <span class="participants-count">${program.totalParticipants} participant${program.totalParticipants !== 1 ? 's' : ''}</span>
                </div>

                ${program.status !== 'draft' ? `
                    <div class="program-progress">
                        <div class="progress-header">
                            <span class="progress-label">Progress</span>
                            <span class="progress-percentage">${program.progress}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${program.progress}%"></div>
                        </div>
                    </div>
                ` : ''}

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="status-badge status-${program.status}">
                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                        ${program.status.charAt(0).toUpperCase() + program.status.slice(1)}
                    </span>
                </div>

                <div class="program-actions">
                    <button class="btn btn-outline btn-sm" onclick="viewProgram(${program.id})">
                        <i class="fas fa-eye"></i>
                        View
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="editProgram(${program.id})">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function createProgram() {
    alert('Create Program functionality - This would open a modal or navigate to create program page');
}

function viewProgram(id) {
    console.log('View program:', id);
    alert(`View Program ${id} - This would show detailed program information`);
}

function editProgram(id) {
    console.log('Edit program:', id);
    alert(`Edit Program ${id} - This would open the program editor`);
}

function exportPrograms() {
    alert('Export Programs - This would generate a downloadable report');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    renderPrograms();
});
</script>