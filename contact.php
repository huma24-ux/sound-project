<?php
include 'db.php';
include 'auth_check.php';
include 'header.php';

$message = "";

// ========== SAVE (CREATE or UPDATE) ==========
if (isset($_POST['save_contact'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);
    $interested_in_production = isset($_POST['interested_in_production']) ? 1 : 0;
    $updates_subscription = isset($_POST['updates_subscription']) ? 1 : 0;
    $created_at = date('Y-m-d H:i:s');

    if ($id == 0) {
        // INSERT
        $sql = "INSERT INTO contact (name, email, subject, message, interested_in_production, updates_subscription, created_at)
                VALUES ('$name', '$email', '$subject', '$message_text', $interested_in_production, $updates_subscription, '$created_at')";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>✅ Contact added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        // UPDATE
        $sql = "UPDATE contact SET 
                    name='$name',
                    email='$email',
                    subject='$subject',
                    message='$message_text',
                    interested_in_production=$interested_in_production,
                    updates_subscription=$updates_subscription
                WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-warning'>✏️ Contact updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error: " . mysqli_error($conn) . "</div>";
        }
    }
}

// ========== DELETE ==========
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM contact WHERE id=$del_id")) {
        echo "<script>alert('Contact deleted successfully!'); window.location.href='contact.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting: " . mysqli_error($conn) . "');</script>";
    }
}

// ========== FETCH ==========
$contacts = mysqli_query($conn, "SELECT * FROM contact ORDER BY created_at DESC");
?>

<div class="container bg-white p-4 rounded shadow-sm mt-4">
    <h3 class="text-center mb-3">📩 Contact Management</h3>

    <?= $message; ?>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal" onclick="openAddModal()">+ Add Contact</button>
    </div>

    <table class="table table-bordered table-striped text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th>
                <th>Interested in Production</th><th>Updates Subscription</th><th>Created At</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($contacts)) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['email']); ?></td>
                <td><?= htmlspecialchars($row['subject']); ?></td>
                <td><?= htmlspecialchars($row['message']); ?></td>
                <td><?= $row['interested_in_production'] ? 'Yes' : 'No'; ?></td>
                <td><?= $row['updates_subscription'] ? 'Yes' : 'No'; ?></td>
                <td><?= $row['created_at']; ?></td>
                <td>
                    <button class="btn btn-sm btn-warning"
                        onclick="openEditModal(
                            '<?= $row['id']; ?>',
                            '<?= htmlspecialchars(addslashes($row['name'])); ?>',
                            '<?= htmlspecialchars(addslashes($row['email'])); ?>',
                            '<?= htmlspecialchars(addslashes($row['subject'])); ?>',
                            '<?= htmlspecialchars(addslashes($row['message'])); ?>',
                            '<?= $row['interested_in_production']; ?>',
                            '<?= $row['updates_subscription']; ?>'
                        )"><i class='fas fa-edit'></i></button>

                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $row['id']; ?>)">
                        <i class='fas fa-trash-alt'></i>
                    </button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Add Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="id">

            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Message</label>
                    <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
                </div>
                <div class="col-md-6 mt-2">
                    <input type="checkbox" name="interested_in_production" id="interested_in_production">
                    <label for="interested_in_production">Interested in Production</label>
                </div>
                <div class="col-md-6 mt-2">
                    <input type="checkbox" name="updates_subscription" id="updates_subscription">
                    <label for="updates_subscription">Updates Subscription</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="save_contact" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openAddModal() {
    document.getElementById("modalTitle").innerText = "Add Contact";
    document.getElementById("id").value = "";
    document.getElementById("name").value = "";
    document.getElementById("email").value = "";
    document.getElementById("subject").value = "";
    document.getElementById("message").value = "";
    document.getElementById("interested_in_production").checked = false;
    document.getElementById("updates_subscription").checked = false;
    var modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

function openEditModal(id, name, email, subject, message, interested, updates) {
    document.getElementById("modalTitle").innerText = "Edit Contact";
    document.getElementById("id").value = id;
    document.getElementById("name").value = name;
    document.getElementById("email").value = email;
    document.getElementById("subject").value = subject;
    document.getElementById("message").value = message;
    document.getElementById("interested_in_production").checked = (interested == 1);
    document.getElementById("updates_subscription").checked = (updates == 1);
    var modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this contact?")) {
        window.location.href = 'contact.php?delete=' + id;
    }
}
</script>

<?php include 'footer.php'; ?>
