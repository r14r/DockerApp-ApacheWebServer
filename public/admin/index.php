<?php
// Admin panel for managing .htaccess files
session_start();

// Simple authentication (you should implement proper authentication in production)
$admin_password = getenv('ADMIN_PASSWORD') ?: 'admin123';
$authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['authenticated'] = true;
        $authenticated = true;
    } else {
        $error = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle .htaccess file operations
$htaccess_path = dirname(__DIR__) . '/.htaccess';
$message = '';

if ($authenticated && isset($_POST['save_htaccess'])) {
    if (isset($_POST['htaccess_content'])) {
        if (file_put_contents($htaccess_path, $_POST['htaccess_content'])) {
            $message = '<div class="alert alert-success">✅ .htaccess file updated successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ Failed to update .htaccess file</div>';
        }
    }
}

// Read current .htaccess content
$htaccess_content = '';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Apache Web Server</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #667eea;
            color: white;
            font-weight: 600;
        }
        .code-editor {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            min-height: 400px;
        }
        .quick-links {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body>
    <?php if (!$authenticated): ?>
        <!-- Login Form -->
        <div class="container">
            <div class="row justify-content-center" style="margin-top: 100px;">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header text-center">
                            <h4><i class="bi bi-shield-lock"></i> Admin Login</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Admin Panel -->
        <div class="admin-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col">
                        <h1><i class="bi bi-gear-fill"></i> Apache Web Server Admin Panel</h1>
                        <p class="mb-0">Manage your server configuration and access phpMyAdmin</p>
                    </div>
                    <div class="col-auto">
                        <a href="?logout" class="btn btn-light">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <?= $message ?>

            <!-- Quick Links Card -->
            <div class="card quick-links">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-link-45deg"></i> Quick Access</h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="http://localhost:40020" target="_blank" class="btn btn-outline-primary w-100">
                                <i class="bi bi-database"></i> Open phpMyAdmin
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="../" target="_blank" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-house"></i> View Website
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="../env-check.php" target="_blank" class="btn btn-outline-info w-100">
                                <i class="bi bi-info-circle"></i> Environment Info
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-square"></i> System Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>PHP Version:</strong><br>
                            <span class="badge bg-success"><?= PHP_VERSION ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Server Software:</strong><br>
                            <span class="badge bg-info"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Document Root:</strong><br>
                            <code><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></code>
                        </div>
                        <div class="col-md-3">
                            <strong>Server Time:</strong><br>
                            <?= date('Y-m-d H:i:s') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Connection Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-database-check"></i> Database Connection
                </div>
                <div class="card-body">
                    <?php
                    $host = getenv('MYSQL_HOST') ?: 'db';
                    $user = getenv('MYSQL_USER') ?: 'appuser';
                    $pass = getenv('MYSQL_PASSWORD') ?: 'apppass';
                    $db   = getenv('MYSQL_DATABASE') ?: 'appdb';
                    
                    try {
                        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ]);
                        $ver = $pdo->query("SELECT VERSION()")->fetchColumn();
                        echo '<div class="alert alert-success mb-0">';
                        echo '<i class="bi bi-check-circle"></i> ✅ MySQL connected successfully';
                        echo '<br><strong>Version:</strong> ' . htmlspecialchars($ver);
                        echo '<br><strong>Host:</strong> ' . htmlspecialchars($host);
                        echo '<br><strong>Database:</strong> ' . htmlspecialchars($db);
                        echo '<br><strong>User:</strong> ' . htmlspecialchars($user);
                        echo '</div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger mb-0">';
                        echo '<i class="bi bi-x-circle"></i> ❌ MySQL connection failed: ' . htmlspecialchars($e->getMessage());
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <!-- .htaccess Editor Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark-code"></i> .htaccess File Editor
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="htaccess_content" class="form-label">
                                <strong>File Path:</strong> <code><?= htmlspecialchars($htaccess_path) ?></code>
                            </label>
                            <textarea 
                                class="form-control code-editor" 
                                id="htaccess_content" 
                                name="htaccess_content" 
                                rows="15"><?= htmlspecialchars($htaccess_content) ?></textarea>
                            <div class="form-text">
                                <i class="bi bi-exclamation-triangle text-warning"></i> 
                                <strong>Warning:</strong> Incorrect .htaccess configuration can break your website. Make sure you understand the changes before saving.
                            </div>
                        </div>
                        <button type="submit" name="save_htaccess" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                            <i class="bi bi-arrow-clockwise"></i> Reload
                        </button>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-question-circle"></i> Help & Documentation
                </div>
                <div class="card-body">
                    <h6>Common .htaccess Directives:</h6>
                    <ul>
                        <li><code>Options -Indexes</code> - Disable directory listing</li>
                        <li><code>RewriteEngine On</code> - Enable URL rewriting</li>
                        <li><code>Header set</code> - Set security headers</li>
                        <li><code>ErrorDocument 404</code> - Custom error pages</li>
                    </ul>
                    <h6 class="mt-3">phpMyAdmin Access:</h6>
                    <p>Access phpMyAdmin at: <a href="http://localhost:40020" target="_blank">http://localhost:40020</a></p>
                    <p><strong>Login credentials:</strong> Use root/secret or appuser/apppass</p>
                </div>
            </div>
        </div>

        <footer class="text-center py-4 mt-4">
            <div class="container">
                <p class="text-muted mb-0">
                    <i class="bi bi-server"></i> Apache Web Server Admin Panel | 
                    <a href="https://httpd.apache.org/docs/2.4/howto/htaccess.html" target="_blank">.htaccess Documentation</a>
                </p>
            </div>
        </footer>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
