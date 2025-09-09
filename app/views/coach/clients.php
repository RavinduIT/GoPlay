<?php $currentPage = 'clients'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Clients - GoPlay</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/coach/sidebar.css">
    <link rel="stylesheet" href="/public/css/pages/coach-clients.css">
</head>
<body>
    <div class="coach-dashboard">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <h1>My Clients</h1>
                    <p class="header-subtitle">Manage your clients and track their progress</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-secondary" id="exportClientsBtn">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    <button class="btn btn-primary" id="addClientBtn">
                        <i class="fas fa-user-plus"></i>
                        Add Client
                    </button>
                </div>
            </div>

            <!-- Client Stats -->
            <div class="client-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="totalClients">45</div>
                        <div class="stat-label">Total Clients</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon active">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="activeClients">38</div>
                        <div class="stat-label">Active Clients</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon new">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="newClients">3</div>
                        <div class="stat-label">New This Month</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon retention">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number" id="retentionRate">92%</div>
                        <div class="stat-label">Retention Rate</div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="clients-filters">
                <div class="filter-group">
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="trial">Trial</option>
                        <option value="suspended">Suspended</option>
                    </select>
                    
                    <select id="programFilter">
                        <option value="">All Programs</option>
                        <option value="cricket-fundamentals">Cricket Fundamentals</option>
                        <option value="advanced-techniques">Advanced Techniques</option>
                        <option value="youth-development">Youth Development</option>
                        <option value="professional-training">Professional Training</option>
                    </select>
                    
                    <select id="sortFilter">
                        <option value="name">Sort by Name</option>
                        <option value="joined">Recently Joined</option>
                        <option value="progress">By Progress</option>
                        <option value="sessions">By Sessions</option>
                    </select>
                </div>
                
                <div class="search-group">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="clientSearch" placeholder="Search clients...">
                    </div>
                </div>
            </div>

            <!-- Client Views -->
            <div class="clients-container">
                <div class="view-controls">
                    <button class="view-btn active" data-view="grid">
                        <i class="fas fa-th"></i>
                        Grid
                    </button>
                    <button class="view-btn" data-view="list">
                        <i class="fas fa-list"></i>
                        List
                    </button>
                </div>

                <!-- Grid View -->
                <div id="gridView" class="clients-view active">
                    <div id="clientsGrid" class="clients-grid">
                        <!-- Dynamic content -->
                    </div>
                </div>

                <!-- List View -->
                <div id="listView" class="clients-view">
                    <div class="clients-table-container">
                        <table id="clientsTable" class="clients-table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Program</th>
                                    <th>Progress</th>
                                    <th>Sessions</th>
                                    <th>Last Session</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Client Modal -->
    <div id="addClientModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Add New Client</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addClientForm">
                    <div class="form-section">
                        <h4>Personal Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="fullName" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="tel" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label>Age</label>
                                <input type="number" name="age" min="5" max="100">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Emergency Contact</label>
                                <input type="tel" name="emergencyContact">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Training Information</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Training Program</label>
                                <select name="program" required>
                                    <option value="">Select Program</option>
                                    <option value="cricket-fundamentals">Cricket Fundamentals</option>
                                    <option value="advanced-techniques">Advanced Techniques</option>
                                    <option value="youth-development">Youth Development</option>
                                    <option value="professional-training">Professional Training</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Experience Level</label>
                                <select name="experienceLevel">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="professional">Professional</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Preferred Session Type</label>
                                <select name="sessionType">
                                    <option value="individual">Individual</option>
                                    <option value="group">Group</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Goals</label>
                                <input type="text" name="goals" placeholder="e.g., Improve batting technique">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Medical & Additional Information</h4>
                        <div class="form-group">
                            <label>Medical Conditions</label>
                            <textarea name="medicalConditions" rows="2" placeholder="Any medical conditions or injuries to consider"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="notes" rows="2" placeholder="Any additional information about the client"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachClients.closeModal('addClientModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachClients.saveClient()">Add Client</button>
            </div>
        </div>
    </div>

    <!-- Client Details Modal -->
    <div id="clientDetailsModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3>Client Details</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div id="clientDetailsContent">
                    <!-- Dynamic client details -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachClients.closeModal('clientDetailsModal')">Close</button>
                <button type="button" class="btn btn-primary" id="editClientBtn">Edit Client</button>
                <button type="button" class="btn btn-success" id="scheduleSessionBtn">Schedule Session</button>
            </div>
        </div>
    </div>

    <!-- Progress Tracking Modal -->
    <div id="progressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Track Progress</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="progressForm">
                    <div class="form-group">
                        <label>Session Date</label>
                        <input type="date" name="sessionDate" required>
                    </div>
                    <div class="form-group">
                        <label>Skills Assessment</label>
                        <div class="skills-grid">
                            <div class="skill-item">
                                <label>Batting</label>
                                <input type="range" name="batting" min="0" max="10" value="5">
                                <span class="skill-value">5/10</span>
                            </div>
                            <div class="skill-item">
                                <label>Bowling</label>
                                <input type="range" name="bowling" min="0" max="10" value="5">
                                <span class="skill-value">5/10</span>
                            </div>
                            <div class="skill-item">
                                <label>Fielding</label>
                                <input type="range" name="fielding" min="0" max="10" value="5">
                                <span class="skill-value">5/10</span>
                            </div>
                            <div class="skill-item">
                                <label>Fitness</label>
                                <input type="range" name="fitness" min="0" max="10" value="5">
                                <span class="skill-value">5/10</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Session Notes</label>
                        <textarea name="notes" rows="4" placeholder="Progress notes, areas for improvement, achievements..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Next Session Goals</label>
                        <textarea name="nextGoals" rows="2" placeholder="Goals for the next session"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="coachClients.closeModal('progressModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="coachClients.saveProgress()">Save Progress</button>
            </div>
        </div>
    </div>

    <script src="/public/js/pages/coach-clients.js"></script>
</body>
</html>