-- ============================================
-- GoPlay Maintenance System
-- Database Schema for Ground Owner Maintenance Management
-- ============================================

-- Drop existing tables if they exist (for clean install)
DROP TABLE IF EXISTS maintenance_progress_updates;
DROP TABLE IF EXISTS facility_inspections;
DROP TABLE IF EXISTS facility_maintenance_tasks;

-- ============================================
-- Facility Maintenance Tasks Table
-- ============================================
CREATE TABLE facility_maintenance_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    owner_id INT NOT NULL,

    -- Task Details
    title VARCHAR(255) NOT NULL,
    description TEXT,
    task_type ENUM('cleaning', 'repair', 'inspection', 'equipment', 'landscaping', 'painting', 'safety', 'other') NOT NULL,
    category ENUM('preventive', 'corrective', 'emergency', 'routine') DEFAULT 'routine',

    -- Priority & Status
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('scheduled', 'in-progress', 'completed', 'cancelled', 'overdue') DEFAULT 'scheduled',

    -- Scheduling
    scheduled_date DATE NOT NULL,
    estimated_duration DECIMAL(5,2) COMMENT 'Duration in hours',
    completed_date DATETIME,

    -- Cost Tracking
    estimated_cost DECIMAL(10,2) DEFAULT 0,
    actual_cost DECIMAL(10,2) DEFAULT 0,

    -- Assignment
    assigned_to VARCHAR(255),
    required_tools TEXT,

    -- Blocking
    block_bookings BOOLEAN DEFAULT FALSE,
    send_notifications BOOLEAN DEFAULT TRUE,

    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,

    -- Foreign Keys
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_facility (facility_id),
    INDEX idx_owner (owner_id),
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Maintenance Progress Updates Table
-- ============================================
CREATE TABLE maintenance_progress_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,

    -- Update Details
    update_text TEXT NOT NULL,
    progress_percentage INT DEFAULT 0,

    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,

    -- Foreign Keys
    FOREIGN KEY (task_id) REFERENCES facility_maintenance_tasks(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_task (task_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Facility Inspections Table
-- ============================================
CREATE TABLE facility_inspections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    facility_id INT NOT NULL,
    owner_id INT NOT NULL,

    -- Inspection Details
    inspection_type ENUM('routine', 'safety', 'quality', 'equipment', 'compliance') NOT NULL,
    inspector VARCHAR(255),

    -- Scheduling
    inspection_date DATE NOT NULL,
    inspection_time TIME,

    -- Results
    status ENUM('scheduled', 'completed', 'failed', 'cancelled') DEFAULT 'scheduled',
    pass_status ENUM('passed', 'failed', 'conditional', 'pending') DEFAULT 'pending',
    score INT COMMENT 'Health score 0-100',

    -- Details
    notes TEXT,
    findings TEXT,
    recommendations TEXT,

    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME,

    -- Foreign Keys
    FOREIGN KEY (facility_id) REFERENCES sports_facilities(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Indexes
    INDEX idx_facility (facility_id),
    INDEX idx_owner (owner_id),
    INDEX idx_inspection_date (inspection_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data (Optional - for testing)
-- ============================================

-- Insert sample maintenance task for testing
-- (Assumes facility_id 1 and owner_id 100 exist)
INSERT INTO facility_maintenance_tasks
(facility_id, owner_id, title, description, task_type, category, priority, status, scheduled_date, estimated_duration, estimated_cost, assigned_to, required_tools)
VALUES
(1, 100, 'Replace Damaged Goal Post', 'The left goal post has a crack and needs replacement', 'repair', 'corrective', 'high', 'scheduled', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 4.0, 15000.00, 'Maintenance Team', 'Welding equipment, new goal post, safety gear'),
(1, 100, 'Weekly Grass Cutting', 'Regular weekly maintenance for the field grass', 'landscaping', 'routine', 'medium', 'scheduled', DATE_ADD(CURDATE(), INTERVAL 1 DAY), 2.0, 5000.00, 'Landscaping Team', 'Lawn mower, trimmer, rake'),
(1, 100, 'Deep Clean Facility', 'Complete deep cleaning of the entire facility', 'cleaning', 'routine', 'medium', 'scheduled', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 3.0, 8000.00, 'Cleaning Staff', 'Cleaning supplies, pressure washer');

-- Insert sample inspection
INSERT INTO facility_inspections
(facility_id, owner_id, inspection_type, inspector, inspection_date, inspection_time, status, notes)
VALUES
(1, 100, 'safety', 'Safety Inspector John', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:00:00', 'scheduled', 'Monthly safety inspection as per regulations');

-- ============================================
-- Useful Queries for the Application
-- ============================================

-- Get all active tasks for an owner
-- SELECT * FROM facility_maintenance_tasks
-- WHERE owner_id = ? AND status IN ('scheduled', 'in-progress')
-- ORDER BY priority DESC, scheduled_date ASC;

-- Get overdue tasks
-- SELECT * FROM facility_maintenance_tasks
-- WHERE owner_id = ? AND status = 'scheduled' AND scheduled_date < CURDATE();

-- Get monthly cost summary
-- SELECT
--   SUM(actual_cost) as total_actual,
--   SUM(estimated_cost) as total_estimated,
--   COUNT(*) as task_count
-- FROM facility_maintenance_tasks
-- WHERE owner_id = ?
--   AND scheduled_date >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
--   AND scheduled_date < DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH);

-- Get tasks calendar for a month
-- SELECT
--   scheduled_date,
--   COUNT(*) as task_count,
--   SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled_count,
--   SUM(CASE WHEN status = 'in-progress' THEN 1 ELSE 0 END) as in_progress_count,
--   SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
-- FROM facility_maintenance_tasks
-- WHERE owner_id = ?
--   AND scheduled_date >= ?
--   AND scheduled_date <= ?
-- GROUP BY scheduled_date;
