<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strato Docker Mirror - Apache Web Server</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .main-card {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            border: none;
            border-radius: 1rem;
        }
        .card-header {
            background-color: #667eea;
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
        }
        .status-badge {
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
        }
        .admin-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <a href="admin/" class="btn btn-light admin-link">
        <i class="bi bi-gear-fill"></i> Admin Panel
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card main-card">
                    <div class="card-header text-center">
                        <h1 class="mb-0"><i class="bi bi-server"></i> Strato Docker Mirror</h1>
                        <p class="mb-0 mt-2">Apache Web Server with MySQL</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- PHP Version -->
                        <div class="mb-4">
                            <h5><i class="bi bi-code-square"></i> PHP Information</h5>
                            <p class="mb-2">
                                <strong>Version:</strong> 
                                <span class="badge bg-success status-badge"><?= PHP_VERSION ?></span>
                            </p>
                        </div>

                        <!-- Database Connection -->
                        <div class="mb-4">
                            <h5><i class="bi bi-database"></i> MySQL Database</h5>
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
                                echo '<div class="alert alert-success">';
                                echo '<i class="bi bi-check-circle-fill"></i> ✅ <strong>MySQL Connected</strong><br>';
                                echo 'Version: ' . htmlspecialchars($ver) . '<br>';
                                echo 'Host: ' . htmlspecialchars($host) . '<br>';
                                echo 'Database: ' . htmlspecialchars($db);
                                echo '</div>';
                            } catch (Exception $e) {
                                echo '<div class="alert alert-danger">';
                                echo '<i class="bi bi-x-circle-fill"></i> ❌ <strong>MySQL Connection Failed</strong><br>';
                                echo htmlspecialchars($e->getMessage());
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <!-- Quick Links -->
                        <div class="mb-4">
                            <h5><i class="bi bi-link-45deg"></i> Quick Links</h5>
                            <div class="d-grid gap-2">
                                <a href="admin/" class="btn btn-primary">
                                    <i class="bi bi-gear-fill"></i> Admin Panel
                                </a>
                                <a href="http://localhost:40020" target="_blank" class="btn btn-info">
                                    <i class="bi bi-database-gear"></i> phpMyAdmin
                                </a>
                                <a href="env-check.php" class="btn btn-secondary">
                                    <i class="bi bi-info-circle"></i> Environment Check
                                </a>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> Server Time: <?= date('Y-m-d H:i:s') ?><br>
                                <i class="bi bi-hdd"></i> Document Root: <?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
