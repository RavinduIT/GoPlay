<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Coach Application - GoPlay';
$additionalCSS = ['/public/css/pages/provider-application.css'];
$additionalJS = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Include Navbar -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="application-container">
        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Personal Info</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Professional Details</div>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Documents</div>
            </div>
        </div>

        <!-- Form Header -->
        <div class="form-header">
            <div class="header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h1>Coach Application</h1>
            <p>Fill out the form below to register as a professional coach</p>
        </div>

        <!-- Application Form -->
        <form id="coachForm" class="application-form" enctype="multipart/form-data">
            <input type="hidden" name="provider_type" value="coach">

            <!-- Step 1: Personal Information -->
            <div class="form-step active" data-step="1">
                <h2 class="step-title">Personal Information</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="first_name" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="last_name" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="dateOfBirth">Date of Birth *</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-next" onclick="nextStep(1)">
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Professional Details -->
            <div class="form-step" data-step="2">
                <h2 class="step-title">Professional Details</h2>

                <div class="form-group">
                    <label for="sportSpecialization">Sport Specialization *</label>
                    <select id="sportSpecialization" name="sport_specialization" required>
                        <option value="">Select Sport</option>
                        <option value="football">Football</option>
                        <option value="cricket">Cricket</option>
                        <option value="basketball">Basketball</option>
                        <option value="tennis">Tennis</option>
                        <option value="badminton">Badminton</option>
                        <option value="swimming">Swimming</option>
                        <option value="athletics">Athletics</option>
                        <option value="yoga">Yoga/Fitness</option>
                    </select>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="experienceYears">Years of Experience *</label>
                        <input type="number" id="experienceYears" name="experience_years" min="0" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="sessionRate">Proposed Session Rate (LKR) *</label>
                        <input type="number" id="sessionRate" name="session_rate" min="0" step="0.01" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="qualifications">Qualifications & Certifications *</label>
                    <textarea id="qualifications" name="qualifications" rows="4" placeholder="List your coaching qualifications, certifications, and training..." required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="specialties">Coaching Specialties</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="beginners">
                            <span>Beginners</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="intermediate">
                            <span>Intermediate</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="advanced">
                            <span>Advanced</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="youth">
                            <span>Youth Training</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="fitness">
                            <span>Fitness & Conditioning</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="specialties[]" value="rehabilitation">
                            <span>Rehabilitation</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bio">Professional Bio *</label>
                    <textarea id="bio" name="bio" rows="5" placeholder="Tell us about your coaching philosophy, achievements, and what makes you unique..." required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="previousExperience">Previous Coaching Experience *</label>
                    <textarea id="previousExperience" name="previous_experience" rows="4" placeholder="Describe your previous coaching roles, teams, or individuals you've trained..." required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label>Availability *</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekday_morning">
                            <span>Weekday Mornings</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekday_afternoon">
                            <span>Weekday Afternoons</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekday_evening">
                            <span>Weekday Evenings</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekend_morning">
                            <span>Weekend Mornings</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekend_afternoon">
                            <span>Weekend Afternoons</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="availability[]" value="weekend_evening">
                            <span>Weekend Evenings</span>
                        </label>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-prev" onclick="prevStep(2)">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn-next" onclick="nextStep(2)">
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Documents -->
            <div class="form-step" data-step="3">
                <h2 class="step-title">Required Documents</h2>

                <div class="form-group">
                    <label for="nicDocument">National ID Card (NIC) *</label>
                    <div class="file-upload-area">
                        <input type="file" id="nicDocument" name="nic_document" accept="image/*,.pdf" >
                        <div class="file-upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 5MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="certifications">Coaching Certifications/Licenses *</label>
                    <div class="file-upload-area">
                        <input type="file" id="certifications" name="certifications[]" accept="image/*,.pdf" multiple >
                        <div class="file-upload-placeholder">
                            <i class="fas fa-certificate"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Images (Max 5MB each)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="profilePhoto">Professional Profile Photo *</label>
                    <div class="file-upload-area">
                        <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" required>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-user-circle"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>Image only (Max 5MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="additionalDocuments">Additional Documents (Optional)</label>
                    <div class="file-upload-area">
                        <input type="file" id="additionalDocuments" name="additional_documents[]" accept="image/*,.pdf" multiple>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-file-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>Achievements, awards, references, etc.</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label class="checkbox-label terms-checkbox">
                        <input type="checkbox" id="termsAgree" name="terms_agree" required>
                        <span>I agree to the <a href="<?= $_base ?>/terms" target="_blank">Terms and Conditions</a> and <a href="<?= $_base ?>/privacy" target="_blank">Privacy Policy</a> *</span>
                    </label>
                    <span class="error-message"></span>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-prev" onclick="prevStep(3)">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Include Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script src="<?= $_base ?>/public/js/provider-application.js"></script>
</body>
</html>
