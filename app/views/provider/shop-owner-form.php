<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Shop Owner Application - GoPlay';
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
                <div class="step-label">Business Details</div>
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
                <i class="fas fa-store"></i>
            </div>
            <h1>Shop Owner Application</h1>
            <p>Fill out the form below to register as a shop owner</p>
        </div>

        <!-- Application Form -->
        <form id="shopOwnerForm" class="application-form" enctype="multipart/form-data">
            <input type="hidden" name="provider_type" value="shop_owner">

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

            <!-- Step 2: Business Details -->
            <div class="form-step" data-step="2">
                <h2 class="step-title">Business Details</h2>

                <div class="form-group">
                    <label for="shopName">Shop/Business Name *</label>
                    <input type="text" id="shopName" name="shop_name" required>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="businessRegistrationNumber">Business Registration Number *</label>
                    <input type="text" id="businessRegistrationNumber" name="business_registration_number" required>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="shopAddress">Shop Address *</label>
                    <textarea id="shopAddress" name="shop_address" rows="3" required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="shopCity">Shop City *</label>
                        <input type="text" id="shopCity" name="shop_city" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="shopPostal">Postal Code *</label>
                        <input type="text" id="shopPostal" name="shop_postal" required>
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="productCategories">Product Categories *</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="sports_equipment">
                            <span>Sports Equipment</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="athletic_wear">
                            <span>Athletic Wear</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="footwear">
                            <span>Footwear</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="accessories">
                            <span>Accessories</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="fitness_gear">
                            <span>Fitness Gear</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="product_categories[]" value="nutritional_supplements">
                            <span>Nutritional Supplements</span>
                        </label>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="businessType">Business Type *</label>
                    <select id="businessType" name="business_type" required>
                        <option value="">Select Business Type</option>
                        <option value="retail_store">Retail Store</option>
                        <option value="online_only">Online Only</option>
                        <option value="both">Both Retail & Online</option>
                        <option value="manufacturer">Manufacturer</option>
                        <option value="distributor">Distributor</option>
                    </select>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="yearEstablished">Year Established *</label>
                        <input type="number" id="yearEstablished" name="year_established" min="1900" max="2024" required>
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="numberOfEmployees">Number of Employees</label>
                        <input type="number" id="numberOfEmployees" name="number_of_employees" min="1">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="businessDescription">Business Description *</label>
                    <textarea id="businessDescription" name="business_description" rows="4" placeholder="Describe your business, products, and what makes you unique..." required></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="brandNames">Brand Names You Carry</label>
                    <textarea id="brandNames" name="brand_names" rows="3" placeholder="List the brands you sell or manufacture..."></textarea>
                    <span class="error-message"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="websiteUrl">Website URL (if any)</label>
                        <input type="url" id="websiteUrl" name="website_url" placeholder="https://example.com">
                        <span class="error-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="socialMedia">Social Media Handle</label>
                        <input type="text" id="socialMedia" name="social_media" placeholder="@yourshop">
                        <span class="error-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Delivery Options *</label>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="delivery_options[]" value="store_pickup">
                            <span>Store Pickup</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="delivery_options[]" value="local_delivery">
                            <span>Local Delivery</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="delivery_options[]" value="nationwide_delivery">
                            <span>Nationwide Delivery</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="delivery_options[]" value="courier_service">
                            <span>Courier Service</span>
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
                        <input type="file" id="nicDocument" name="nic_document" accept="image/*,.pdf" required>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 5MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="businessRegistration">Business Registration Certificate *</label>
                    <div class="file-upload-area">
                        <input type="file" id="businessRegistration" name="business_registration" accept="image/*,.pdf" >
                        <div class="file-upload-placeholder">
                            <i class="fas fa-file-contract"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 5MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="taxDocument">Tax Registration/VAT Number *</label>
                    <div class="file-upload-area">
                        <input type="file" id="taxDocument" name="tax_document" accept="image/*,.pdf" >
                        <div class="file-upload-placeholder">
                            <i class="fas fa-file-invoice"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PDF or Image (Max 5MB)</span>
                        </div>
                    </div>
                    <span class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="shopImages">Shop/Product Images (Upload up to 5 images) *</label>
                    <div class="file-upload-area">
                        <input type="file" id="shopImages" name="shop_images[]" accept="image/*" multiple required>
                        <div class="file-upload-placeholder">
                            <i class="fas fa-images"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>Images only (Max 5MB each)</span>
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
                            <span>Product catalogs, licenses, etc.</span>
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
