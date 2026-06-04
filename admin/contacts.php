<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'helpers.php';

AdminAuth::requireLogin();

$pdo = getDBConnection();

// Get filter values
$searchQuery = $_GET['search'] ?? '';

// Build query
$query = 'SELECT * FROM contact_messages WHERE 1=1';
$params = [];

if (!empty($searchQuery)) {
    $query .= ' AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

$query .= ' ORDER BY submitted_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$contacts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - Admin Dashboard</title>
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
                        <a href="donations.php" class="menu-item">
                            <i class="fas fa-donate"></i> Donations
                        </a>
                        <a href="volunteers.php" class="menu-item">
                            <i class="fas fa-users"></i> Volunteers
                        </a>
                        <a href="contacts.php" class="menu-item active">
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
                            <i class="fas fa-envelope"></i> Contact Messages
                        </h1>
                        <a href="handlers/export_csv.php?table=contact_messages" class="btn btn-success" title="Export to CSV">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-content">
                                    <h6>Total Messages</h6>
                                    <h3><?php echo count($contacts); ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card">
                                <div class="stat-content">
                                    <h6>Responded</h6>
                                    <h3><?php echo count(array_filter($contacts, fn($c) => !empty($c['response_sent_at']))); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search by name, email, or subject"
                                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="contacts.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Contacts Table -->
                    <div class="card">
                        <div class="table-responsive">
                            <?php if (!empty($contacts)): ?>
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Responded</th>
                                            <th>Notes</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($contacts as $contact): ?>
                                            <tr>
                                                <td>#<?php echo $contact['id']; ?></td>
                                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                                <td><?php echo truncateText($contact['subject'], 25); ?></td>
                                                <td><?php echo formatDateShort($contact['submitted_at']); ?></td>
                                                <td>
                                                    <?php 
                                                    if (!empty($contact['response_sent_at'])) {
                                                        echo '<span class="badge bg-success">Yes</span>';
                                                    } else {
                                                        echo '<span class="badge bg-warning text-dark">No</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo !empty($contact['notes']) ? '<i class="fas fa-sticky-note text-warning"></i>' : '-'; ?></td>
                                                <td>
                                                    <button 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewModal"
                                                        onclick="viewContact(<?php echo htmlspecialchars(json_encode($contact)); ?>)"
                                                    >
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <form method="POST" action="handlers/delete_contact.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
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
                                <div class="alert alert-info m-3 mb-0">No contact messages found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Name</strong></label>
                        <p id="viewName"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Email</strong></label>
                        <p id="viewEmail"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Subject</strong></label>
                        <p id="viewSubject"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Message</strong></label>
                        <p id="viewMessage" style="white-space: pre-wrap; word-wrap: break-word;"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Date</strong></label>
                        <p id="viewDate"></p>
                    </div>
                    
                    <hr>
                    
                    <h6>Admin Notes & Response Tracking</h6>
                    
                    <form method="POST" action="handlers/update_contact.php" id="updateForm">
                        <input type="hidden" id="updateId" name="id">
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="updateNotes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="markResponded" 
                                    name="mark_responded"
                                >
                                <label class="form-check-label" for="markResponded">
                                    Mark as responded
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
    <script>
        function viewContact(contact) {
            document.getElementById('updateId').value = contact.id;
            document.getElementById('viewName').textContent = contact.name;
            document.getElementById('viewEmail').textContent = contact.email;
            document.getElementById('viewSubject').textContent = contact.subject;
            document.getElementById('viewMessage').textContent = contact.message;
            document.getElementById('viewDate').textContent = new Date(contact.submitted_at).toLocaleString();
            document.getElementById('updateNotes').value = contact.notes || '';
            document.getElementById('markResponded').checked = contact.response_sent_at != null;
        }
    </script>
</body>
</html>
