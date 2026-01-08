<?php
require_once 'db.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?success=deleted");
        exit();
    } catch(PDOException $e) {
        $error = "Error deleting item: " . $e->getMessage();
    }
}

// Fetch all items
try {
    $stmt = $pdo->query("SELECT * FROM barang ORDER BY created_at DESC");
    $barang = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error fetching items: " . $e->getMessage();
    $barang = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Barang - Manajemen Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-primary">Manajemen Barang</h1>
                    <a href="add.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </a>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        switch($_GET['success']) {
                            case 'added': echo 'Barang berhasil ditambahkan!'; break;
                            case 'updated': echo 'Barang berhasil diupdate!'; break;
                            case 'deleted': echo 'Barang berhasil dihapus!'; break;
                        }
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Daftar Barang</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($barang)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada barang</h5>
                                <p class="text-muted">Tambahkan barang pertama Anda untuk memulai.</p>
                                <a href="add.php" class="btn btn-primary">Tambah Barang Pertama</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Qty</th>
                                            <th>Deskripsi</th>
                                            <th>Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($barang as $item): ?>
                                            <tr>
                                                <td><?php echo $item['id']; ?></td>
                                                <td><?php echo htmlspecialchars($item['nama']); ?></td>
                                                <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                                <td><?php echo $item['qty']; ?></td>
                                                <td><?php echo htmlspecialchars(substr($item['deskripsi'], 0, 50)) . (strlen($item['deskripsi']) > 50 ? '...' : ''); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $item['id']; ?>, '<?php echo htmlspecialchars($item['nama']); ?>')">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus barang "<span id="deleteItemName"></span>"?</p>
                    <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="deleteLink" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteItemName').textContent = name;
            document.getElementById('deleteLink').href = 'index.php?delete=' + id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
