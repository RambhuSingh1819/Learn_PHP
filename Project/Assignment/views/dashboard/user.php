<?php

$pageTitle = 'Dashboard - Secure Auth System';
?>

<div class="container my-5 py-4 animate-fade-in-up">
    <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom border-secondary border-opacity-20">
        <div>
            <h1 class="display-font text-white mb-1">Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
            <p class="text-muted mb-0">Manage your profile, update security credentials, and review settings.</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-20  border border-primary border-opacity-30 px-3 py-2 rounded-pill fw-600">
                <i class="bi bi-person-badge-fill me-1"></i> User Role: <?= htmlspecialchars($user['role']) ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Edit Profile -->
        <div class="col-lg-6">
            <div class="glass-card h-100">
                <h3 class="display-font text-white fs-4 mb-3"><i class="bi bi-person-gear text-primary me-2"></i>Profile Details</h3>
                
                <form action="<?= url('/profile/update') ?>" method="POST" data-ajax="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="profile_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="profile_name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Gmail Address</label>
                        <input type="email" class="form-control text-muted" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background-color: rgba(15, 23, 42, 0.4) !important; cursor: not-allowed; border-color: rgba(255,255,255,0.03) !important;">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Gmail addresses cannot be modified after registration.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6">
            <div class="glass-card">
                <h3 class="display-font text-white fs-4 mb-3"><i class="bi bi-shield-lock text-primary me-2"></i>Security Credentials</h3>
                
                <form action="<?= url('/profile/change-password') ?>" method="POST" data-ajax="true" data-validate="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="••••••••" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="new_password" placeholder="••••••••" required>
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Min. 4 characters</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-key me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- File Manager Card -->
        <div class="col-12 mt-4">
            <div class="glass-card">
                <h3 class="display-font text-white fs-4 mb-3"><i class="bi bi-folder2-open text-primary me-2"></i>My Files & Documents</h3>
                <p class="text-muted small">Upload files and images to your personal storage. Supported formats: images (JPG, PNG, GIF), PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP. Max size: 5MB.</p>
                
                <!-- Upload Form -->
                <form action="<?= url('/profile/upload-file') ?>" method="POST" enctype="multipart/form-data" class="mb-4" data-ajax="true" data-file-upload="true">
                    <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                    <div class="input-group">
                        <input type="file" name="file" id="userFile" class="form-control" required style="background: rgba(15, 23, 42, 0.4); border-color: var(--glass-border); color: #fff;">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-upload me-1"></i> Upload File
                        </button>
                    </div>
                </form>

                <!-- File List Table -->
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-secondary border-opacity-10 align-middle mb-0" style="background: transparent;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--glass-border); background: transparent;">
                                <th scope="col" style="background: transparent;" class="text-muted py-3">File / Preview</th>
                                <th scope="col" style="background: transparent;" class="text-muted py-3">Type</th>
                                <th scope="col" style="background: transparent;" class="text-muted py-3">Size</th>
                                <th scope="col" style="background: transparent;" class="text-muted py-3">Uploaded At</th>
                                <th scope="col" style="background: transparent;" class="text-muted py-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($files)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5" style="background: transparent;">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-muted opacity-40"></i>
                                        No files uploaded yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($files as $file): ?>
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); background: transparent;">
                                        <td style="background: transparent;" class="py-3">
                                            <div class="d-flex align-items-center">
                                                <!-- Icon/Preview -->
                                                <div class="me-3 position-relative">
                                                    <?php 
                                                    $ext = strtolower($file['filetype']);
                                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                                    if ($isImage): 
                                                    ?>
                                                        <a href="<?= url('/uploads/' . htmlspecialchars($file['filepath'])) ?>" target="_blank">
                                                            <img src="<?= url('/uploads/' . htmlspecialchars($file['filepath'])) ?>" alt="<?= htmlspecialchars($file['filename']) ?>" class="rounded object-fit-cover border border-secondary border-opacity-30" style="width: 48px; height: 48px;">
                                                        </a>
                                                    <?php else: ?>
                                                        <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-20 text-white rounded border border-secondary border-opacity-20" style="width: 48px; height: 48px;">
                                                            <?php if ($ext === 'pdf'): ?>
                                                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                                            <?php elseif (in_array($ext, ['doc', 'docx'])): ?>
                                                                <i class="bi bi-file-earmark-word-fill text-primary fs-3"></i>
                                                            <?php elseif (in_array($ext, ['xls', 'xlsx'])): ?>
                                                                <i class="bi bi-file-earmark-excel-fill text-success fs-3"></i>
                                                            <?php elseif ($ext === 'zip'): ?>
                                                                <i class="bi bi-file-earmark-zip-fill text-warning fs-3"></i>
                                                            <?php else: ?>
                                                                <i class="bi bi-file-earmark-text-fill text-muted fs-3"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- Filename -->
                                                <div>
                                                    <a href="<?= url('/uploads/' . htmlspecialchars($file['filepath'])) ?>" target="_blank" class="text-white text-decoration-none fw-500 hover-underline">
                                                        <?= htmlspecialchars($file['filename']) ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="background: transparent;" class="py-3">
                                            <span class="badge bg-secondary bg-opacity-25 text-white border border-secondary border-opacity-10 px-2 py-1 rounded">
                                                <?= strtoupper(htmlspecialchars($file['filetype'])) ?>
                                            </span>
                                        </td>
                                        <td style="background: transparent;" class="py-3 text-muted">
                                            <?php 
                                            $size = (int)$file['filesize'];
                                            if ($size >= 1048576) {
                                                echo number_format($size / 1048576, 2) . ' MB';
                                            } else {
                                                echo number_format($size / 1024, 2) . ' KB';
                                            }
                                            ?>
                                        </td>
                                        <td style="background: transparent;" class="py-3 text-muted">
                                            <?= date('M d, Y H:i', strtotime($file['uploaded_at'])) ?>
                                        </td>
                                        <td style="background: transparent;" class="py-3 text-end">
                                            <form action="<?= url('/profile/delete-file') ?>" method="POST" data-ajax="true" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file permanently?');">
                                                <input type="hidden" name="csrf_token" value="<?= SessionService::getCSRFToken() ?>">
                                                <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Delete File">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
