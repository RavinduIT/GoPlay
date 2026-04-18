<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Ground Owner Application - GoPlay';
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
            <link rel="stylesheet" href="<?= $_base ?><?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <script>window.BASE_URL = '<?= $_base ?>';</script>
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
                <div class="step-label">Facility Details</div>
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
                <i class="fas fa-landmark"></i>
            </div>
            <h1>Ground Owner Application</h1>
            <p>Fill out the form below to register as a ground owner</p>
        </div>

        <!-- Application Form -->
        <form id="groundOwnerForm" class="application-form" enctype="multipart/form-data">
            <input type="hidden" name="provider_type" value="ground_owner">

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
                    <label for="address">Residential Address *</label>
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
                        <label for="postalCode">Postal Code *</label>
                        <input type="text" id="postalCode" name="postal_code" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-next" onclick="nextStep(1)">
                        Next Step <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Facility Details -->
            <div class="form-step" data-step="2">
                <h2 class="step-title">Facility Details</h2>

                <div class="form-group">
                    <label for="facilityName">Facility/Ground Name *</label>
                    <input type="text" id="facilityName" name="facility_name" required>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="facilityAddress">Facility Address *</label>
                    <textarea id="facilityAddress" name="facility_address" rows="3" required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="facilityCity">Facility City *</label>
                        <input type="text" id="facilityCity" name="facility_city" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="facilityPostal">Postal Code *</label>
                        <input type="text" id="facilityPostal" name="facility_postal" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sportTypes">Sport Types Available *</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="football">
                            <span>Football</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="cricket">
                            <span>Cricket</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="basketball">
                            <span>Basketball</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="tennis">
                            <span>Tennis</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="badminton">
                            <span>Badminton</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sport_types[]" value="swimming">
                            <span>Swimming</span>
                        </label>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="numberOfCourts">Number of Courts/Fields *</label>
                        <input type="number" id="numberOfCourts" name="number_of_courts" min="1" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="proposedHourlyRate">Proposed Hourly Rate (LKR) *</label>
                        <input type="number" id="proposedHourlyRate" name="proposed_hourly_rate" min="0" step="0.01" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="amenities">Available Amenities</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="parking">
                            <span>Parking</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="changing_rooms">
                            <span>Changing Rooms</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="floodlights">
                            <span>Floodlights</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="refreshments">
                            <span>Refreshments</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="first_aid">
                            <span>First Aid</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="amenities[]" value="security">
                            <span>Security</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="facilityDescription">Facility Description *</label>
                    <textarea id="facilityDescription" name="facility_description" rows="4" required></textarea>
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
                        <input type="file" id="nicDocument" name="nic_document" accept="image/*,.pdf" required>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 10MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="businessRegistration">Business Registration Certificate</label>
                    <div class="file-upload-area">
                        <input type="file" id="businessRegistration" name="business_registration" accept="image/*,.pdf">
                        <div class="file-upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 10MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="ownershipProof">Proof of Ownership/Lease Agreement</label>
                    <div class="file-upload-area">
                        <input type="file" id="ownershipProof" name="ownership_proof" accept="image/*,.pdf">
                        <div class="file-upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 10MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="facilityImages">Facility Images (Upload up to 5 images)</label>
                    <div class="file-upload-area">
                        <input type="file" id="facilityImages" name="facility_images[]" accept="image/*" multiple>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-images"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>Images only (Max 5MB each)</span>
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
