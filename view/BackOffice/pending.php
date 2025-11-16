<?php
if (!isset($pendings)) $pendings = [];
?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pending Users - BackOffice</title>
  <link rel="stylesheet" href="<?=htmlspecialchars(BASE_URL)?>assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
  <h1>Pending Users</h1>
  <?php if (!empty($_SESSION['message'])): ?>
    <div class="alert alert-success"><?=htmlspecialchars($_SESSION['message'])?></div>
    <?php unset($_SESSION['message']); endif; ?>
  <?php if (empty($pendings)): ?>
    <p>No pending users.</p>
  <?php else: ?>
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($pendings as $u): ?>
        <tr>
          <td><?=htmlspecialchars($u['id'])?></td>
          <td><?=htmlspecialchars($u['email'])?></td>
          <td><?=htmlspecialchars($u['role'])?></td>
          <td><?=htmlspecialchars($u['created_at'])?></td>
          <td>
            <form method="post" action="<?=BASE_URL?>controller/admin.php" style="display:inline">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="id" value="<?=htmlspecialchars($u['id'])?>">
              <button class="btn btn-success btn-sm" type="submit">Approve</button>
            </form>
            <form method="post" action="<?=BASE_URL?>controller/admin.php" style="display:inline">
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="id" value="<?=htmlspecialchars($u['id'])?>">
              <button class="btn btn-danger btn-sm" type="submit">Reject</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>