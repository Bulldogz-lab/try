<?php
$page_title = 'Add Property';
$active_page = 'add_property';

include '../../includes/session.php';
if ($_SESSION['role'] !== 'admin') {
    echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: "error",
        title: "Unauthorized",
        text: "You do not have permission to access this page.",
        timer: 1500,
        showConfirmButton: false,
        allowOutsideClick: false
    }).then(() => {
        history.back();
    });
</script>
</body>
</html>';
    exit;
}

include '../../includes/layout_open.php';

$old = $_SESSION['form_old'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
$success = $_SESSION['form_success'] ?? false;

unset($_SESSION['form_old'], $_SESSION['form_errors'], $_SESSION['form_success']);

function old($key, $default = '')
{
  global $old;
  return htmlspecialchars($old[$key] ?? $default);
}

function err($key)
{
  global $errors;
  if (!isset($errors[$key]))
    return '';
  return '<div class="form-error">' . htmlspecialchars($errors[$key]) . '</div>';
}

function errClass($key)
{
  global $errors;
  return isset($errors[$key]) ? ' input-error' : '';
}
?>

<style>
  .form-error {
    color: var(--danger);
    font-size: 12px;
    margin-top: 4px;
  }

  .input-error {
    border-color: var(--danger) !important;
  }

  .alert {
    padding: 12px 16px;
    border-radius: var(--radius);
    margin-bottom: 18px;
    font-size: 14px;
  }

  .alert-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #6ee7b7;
  }

  .alert-danger {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fca5a5;
  }
</style>

<div class="page-header">
  <div class="top-header">
    <h2>Add New Property</h2>
    <div class="page-header-sub">Fill in the details to register a new property</div>
  </div>
  <a href="properties_list.php" class="btn btn-secondary">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <polyline points="15 18 9 12 15 6" />
    </svg>
    Back to List
  </a>
</div>

<div class="page-inner">
  <div class="cards-area">

    <?php if ($success): ?>
      <div class="alert alert-success">
        Property has been successfully added.
        <a href="properties_list.php" style="margin-left:8px;font-weight:600;">View all properties</a>
      </div>
    <?php endif; ?>

    <?php if (isset($errors['db'])): ?>
      <div class="alert alert-danger">⚠️ <?= htmlspecialchars($errors['db']) ?></div>
    <?php elseif (!empty($errors)): ?>
      <div class="alert alert-danger">⚠️ Please fix the highlighted fields before saving.</div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header"><span class="card-title">Property Information</span></div>

      <form method="POST" action="../../process/admin-process/process_add_property.php" novalidate>
        <div class="form-grid">

          <div class="form-group">
            <label>Property Name <span style="color:var(--danger)">*</span></label>
            <input type="text" name="name" class="<?= errClass('name') ?>" placeholder="e.g. Skyline Apartments"
              value="<?= old('name') ?>" required />
            <?= err('name') ?>
          </div>

          <div class="form-group">
            <label>Street Address <span style="color:var(--danger)">*</span></label>
            <input type="text" name="address" class="<?= errClass('address') ?>" placeholder="e.g. 12 Oak Street"
              value="<?= old('address') ?>" />
            <?= err('address') ?>
          </div>

          <div class="form-group">
            <label>City <span style="color:var(--danger)">*</span></label>
            <input type="text" name="city" class="<?= errClass('city') ?>" placeholder="e.g. New York"
              value="<?= old('city') ?>" />
            <?= err('city') ?>
          </div>

          <div class="form-group">
            <label>State / Province</label>
            <input type="text" name="state" placeholder="e.g. NY" value="<?= old('state') ?>" />
          </div>

          <div class="form-group">
            <label>ZIP / Postal Code</label>
            <input type="text" name="zip" placeholder="e.g. 10001" value="<?= old('zip') ?>" />
          </div>

        </div>

        <div class="form-actions" style="margin-top:20px;">
          <button type="reset" class="btn btn-secondary">Reset</button>
          <button type="submit" class="btn btn-primary">Save Property</button>
        </div>

      </form>
    </div>

  </div>
</div>

<?php include '../../includes/layout_close.php'; ?>