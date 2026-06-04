<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'helpers.php';

AdminAuth::requireLogin();

$pdo = getDBConnection();

// Get filter values
$statusFilter = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// Build query
$query = 'SELECT * FROM donations WHERE 1=1';
$params = [];

if (!empty($statusFilter)) {
    $query .= ' AND status = ?';
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $query .= ' AND (name LIKE ? OR email LIKE ?)';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

$query .= ' ORDER BY donated_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$donations = $stmt->fetchAll();

$totalAmount = array_sum(array_column($donations, 'amount'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-heart text-danger"></i> Home of Hope Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <strong><?php echo htmlspecialchars(AdminAuth::getCurrentUsername()); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2">
                <div class="admin-sidebar">
                    <div class="sidebar-menu">
                        <a href="dashboard.php" class="menu-item">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <a href="donations.php" class="menu-item active">
                            <i class="fas fa-donate"></i> Donations
                        </a>
                        <a href="volunteers.php" class="menu-item">
                            <i class="fas fa-users"></i> Volunteers
                        </a>
                        <a href="contacts.php" class="menu-item">
                            <i class="fas fa-envelope"></i> Contacts
                        </a>
                        <a href="newsletter.php" class="menu-item">
                            <i class="fas fa-newspaper"></i> Newsletter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10">
                <div class="content-area">
                    <?php displayAlert(); ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title">
                            <i class="fas fa-donate"></i> Donations
                        </h1>
                        <a href="handlers/export_csv.php?table=donations" class="btn btn-success" title="Export to CSV">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-content">
                                    <h6>Total Donations</h6>
                                    <h3><?php echo count($donations); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-content">
                                    <h6>Total Amount</h6>
                                    <h3><?php echo formatCurrency($totalAmount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search by name or email"
                                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                                    >
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="verified" <?php echo $statusFilter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="donations.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Donations Table -->
                    <div class="card">
                        <div class="table-responsive">
                            <?php if (!empty($donations)): ?>
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donations as $donation): ?>
                                            <tr>
                                                <td>#<?php echo $donation['id']; ?></td>
                                                <td><?php echo htmlspecialchars($donation['name']); ?></td>
                                                <td><?php echo htmlspecialchars($donation['email']); ?></td>
                                                <td><strong><?php echo formatCurrency($donation['amount']); ?></strong></td>
                                                <td><?php echo getStatusBadge($donation['status']); ?></td>
                                                <td><?php echo formatDateShort($donation['donated_at']); ?></td>
                                                <td><?php echo !empty($donation['notes']) ? '<i class="fas fa-sticky-note text-warning"></i>' : '-'; ?></td>
                                                <td>
                                                    <button 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editModal"
                                                        onclick="editDonation(<?php echo htmlspecialchars(json_encode($donation)); ?>)"
                                                    >
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form method="POST" action="handlers/delete_donation.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $donation['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info m-3 mb-0">No donations found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Donation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="handlers/update_donation.php">
                    <div class="modal-body">
                        <input type="hidden" id="editId" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="editName" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" id="editAmount" name="amount" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="verified">Verified</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="editNotes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
    <script>
        function editDonation(donation) {
            document.getElementById('editId').value = donation.id;
            document.getElementById('editName').value = donation.name;
            document.getElementById('editEmail').value = donation.email;
            document.getElementById('editAmount').value = donation.amount;
            document.getElementById('editStatus').value = donation.status;
            document.getElementById('editNotes').value = donation.notes || '';
        }
    </script>
</body>
</html>
