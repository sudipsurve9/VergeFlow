<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Selection - Vault64</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .portal-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .portal-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
            text-decoration: none;
            color: inherit;
        }
        .portal-header {
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .super-admin-header {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 50%, #d35400 100%);
        }
        .portal-body {
            padding: 2rem;
        }
        .portal-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list i {
            color: #667eea;
            margin-right: 0.5rem;
            width: 20px;
        }
        .super-admin .feature-list i {
            color: #f39c12;
        }
        .btn-portal {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-super-admin {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            color: white;
        }
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="back-link">
        <i class="fas fa-arrow-left me-2"></i>Back to Site
    </a>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-5">
                <h1 class="text-white mb-3">
                    <i class="fas fa-shield-alt me-3"></i>VergeFlow Portal Access
                </h1>
                <p class="text-white-50 lead">Choose your access level</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-5 mb-4">
                <a href="{{ route('admin.login') }}" class="portal-card">
                    <div class="portal-header admin-header">
                        <div class="portal-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>Admin Portal</h3>
                        <p>Store Management & Operations</p>
                    </div>
                    <div class="portal-body">
                        <ul class="feature-list">
                            <li><i class="fas fa-box"></i>Product Management</li>
                            <li><i class="fas fa-shopping-cart"></i>Order Processing</li>
                            <li><i class="fas fa-users"></i>Customer Management</li>
                            <li><i class="fas fa-chart-bar"></i>Sales Analytics</li>
                            <li><i class="fas fa-cog"></i>Store Settings</li>
                        </ul>
                        <button class="btn btn-portal btn-admin mt-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Access Admin Portal
                        </button>
                    </div>
                </a>
            </div>
            
            <div class="col-md-5 mb-4">
                <a href="{{ route('super_admin.login') }}" class="portal-card">
                    <div class="portal-header super-admin-header">
                        <div class="portal-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3>Super Admin Portal</h3>
                        <p>System Administration & Multi-Client Management</p>
                    </div>
                    <div class="portal-body super-admin">
                        <ul class="feature-list">
                            <li><i class="fas fa-building"></i>Client Management</li>
                            <li><i class="fas fa-users-cog"></i>User Administration</li>
                            <li><i class="fas fa-server"></i>System Settings</li>
                            <li><i class="fas fa-shield-alt"></i>Security Controls</li>
                            <li><i class="fas fa-chart-line"></i>Global Analytics</li>
                        </ul>
                        <button class="btn btn-portal btn-super-admin mt-3">
                            <i class="fas fa-crown me-2"></i>Access Super Admin Portal
                        </button>
                    </div>
                </a>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Need help?</strong> Contact your system administrator for access credentials.
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 