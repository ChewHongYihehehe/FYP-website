<?php

include 'connect.php';





// Handle add / delete / edit operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    } elseif (isset($_POST['edit_category'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name]);
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif (isset($_POST['add_color'])) {
        $name = $_POST['color_name'];
        $stmt = $pdo->prepare("INSERT INTO color (color_name) VALUES (:color_name)");
        $stmt->execute(['color_name' => $name]);
    } elseif (isset($_POST['edit_color'])) {
        $id = $_POST['id'];
        $name = $_POST['color_name'];
        $stmt = $pdo->prepare("UPDATE color SET color_name = :color_name WHERE id = :id");
        $stmt->execute(['id' => $id, 'color_name' => $name]);
    } elseif (isset($_POST['delete_color'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM color WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif (isset($_POST['add_brand'])) {
        $name = $_POST['brand_name'];
        $stmt = $pdo->prepare("INSERT INTO brand (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    } elseif (isset($_POST['edit_brand'])) {
        $id = $_POST['id'];
        $name = $_POST['brand_name'];
        $stmt = $pdo->prepare("UPDATE brand SET name = :name WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name]);
    } elseif (isset($_POST['delete_brand'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM brand WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } elseif (isset($_POST['add_size'])) {
        $value = $_POST['size'];
        $stmt = $pdo->prepare("INSERT INTO sizes (size) VALUES (:size)");
        $stmt->execute(['size' => $value]);
    } elseif (isset($_POST['edit_size'])) {
        $id = $_POST['id'];
        $value = $_POST['size'];
        $stmt = $pdo->prepare("UPDATE sizes SET size = :size WHERE id = :id");
        $stmt->execute(['id' => $id, 'size' => $value]);
    } elseif (isset($_POST['delete_size'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM sizes WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch data from categories table
$categoriesStmt = $conn->query("SELECT * FROM categories");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data from color table
$colorStmt = $conn->query("SELECT * FROM color");
$colors = $colorStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data from brand table
$brandStmt = $conn->query("SELECT * FROM brand");
$brands = $brandStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch data from size table
$sizeStmt = $conn->query("SELECT * FROM sizes");
$sizes = $sizeStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        .actions a {
            margin-right: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
        }

        .confirm-delete {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
    </style>

    <script>
        function showModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function confirmDelete(entity, id) {
            const confirmation = document.getElementById('confirmDeleteModal');
            confirmation.style.display = 'flex';
            document.getElementById('deleteForm').action = `?delete_${entity}=${id}`;
        }

        function openEditCategoryModal(id, name) {
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            showModal('editCategoryModal');
        }

        function openEditBrandModal(id, name) {
            document.getElementById('edit_brand_id').value = id;
            document.getElementById('edit_brand_name').value = name;
            showModal('editBrandModal');
        }

        function openEditSizeModal(id, size) {
            document.getElementById('edit_size_id').value = id;
            document.getElementById('edit_size_value').value = size;
            showModal('editSizeModal');
        }
    </script>
</head>

<body>
    <h1>Category Page</h1>
    <h2>Categories</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['id']) ?></td>
                    <td><?= htmlspecialchars($category['name']) ?></td>
                    <td class="actions">
                        <button onclick="showModal('editCategoryModal_<?= $category['id'] ?>')">Edit</button>
                        <button onclick="confirmDelete('category', <?= $category['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <div id="editCategoryModal<?= $category['id'] ?>" class="modal">
                    <div class="modal-content">
                        <h3>Edit Category</h3>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                            <button type="submit" name="edit_category">Save</button>
                            <button type="button" onclick="closeModal('editCategoryModal<?= $category['id'] ?>')">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="showModal('addCategoryModal')">Add New Category</button>

    <h2>Colors</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($colors as $color): ?>
                <tr>
                    <td><?= htmlspecialchars($color['id']) ?></td>
                    <td><?= htmlspecialchars($color['color_name']) ?></td>
                    <td class="actions">
                        <button onclick="showModal('editColorModal_<?= $category['id'] ?>')">Edit</button>
                        <button onclick="confirmDelete('color', <?= $category['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <div id="editColorModal<?= $color['id'] ?>" class="modal">
                    <div class="modal-content">
                        <h3>Edit Color</h3>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $color['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($color['name']) ?>" required>
                            <button type="submit" name="edit_color">Save</button>
                            <button type="button" onclick="closeModal('editColorModal<?= $color['id'] ?>')">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="showModal('addColorModal')">Add New Color</button>

    <h2>Brands</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($brands as $brand): ?>
                <tr>
                    <td><?= htmlspecialchars($brand['id']) ?></td>
                    <td><?= htmlspecialchars($brand['name']) ?></td>
                    <td class="actions">
                        <button onclick="showModal('editBrandModal_<?= $brand['id'] ?>')">Edit</button>
                        <button onclick="confirmDelete('brand', <?= $category['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <div id="editBrandModal<?= $brand['id'] ?>" class="modal">
                    <div class="modal-content">
                        <h3>Edit Brand</h3>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($brand['name']) ?>" required>
                            <button type="submit" name="edit_brand">Save</button>
                            <button type="button" onclick="closeModal('editBrandModal<?= $brand['id'] ?>')">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="showModal('addBrandModal')">Add New Brand</button>

    <h2>Sizes</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sizes as $size): ?>
                <tr>
                    <td><?= htmlspecialchars($size['id']) ?></td>
                    <td><?= htmlspecialchars($size['size']) ?></td>
                    <td class="actions">
                        <button onclick="showModal('editSizeModal_<?= $size['id'] ?>')">Edit</button>
                        <button onclick="confirmDelete('size', <?= $size['id'] ?>)">Delete</button>
                    </td>
                </tr>
                <div id="editSizeModal<?= $size['id'] ?>" class="modal">
                    <div class="modal-content">
                        <h3>Edit Size</h3>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $size['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                            <button type="submit" name="edit_category">Save</button>
                            <button type="button" onclick="closeModal('editCategoryModal<?= $category['id'] ?>')">Cancel</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="showModal('addSizeModal')">Add New Size</button>

    <!-- Modals -->
    <!-- Category -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <h3>Add New Category</h3>
            <form method="post">
                <input type="text" name="name" placeholder="Category Name" required>
                <button type="submit" name="add_category">Add</button>
                <button type="button" onclick="closeModal('addCategoryModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="editCategoryModal" class="modal">
        <div class="modal-content">
            <h3>Edit Category</h3>
            <form method="post">
                <input type="hidden" name="edit_id" id="edit_category_id">
                <input type="text" name="name" id="edit_category_name" placeholder="Category Name" required>
                <button type="submit" name="edit_category">Save Changes</button>
                <button type="button" onclick="closeModal('editCategoryModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this item?</h3>
            <form method="post" id="deleteForm">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal('confirmDeleteModal')">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Color -->
    <div id="addColorModal" class="modal">
        <div class="modal-content">
            <h3>Add New Color</h3>
            <form method="post">
                <input type="text" name="color_name" placeholder="Color Name" required>
                <button type="submit" name="add_color">Add</button>
                <button type="button" onclick="closeModal('addColorModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="editColorModal" class="modal">
        <div class="modal-content">
            <h3>Edit Color</h3>
            <form method="post">
                <input type="hidden" name="edit_id" id="edit_color_id">
                <input type="text" name="name" id="edit_color_name" placeholder="Color Name" required>
                <button type="submit" name="edit_color">Save Changes</button>
                <button type="button" onclick="closeModal('editColorModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this item?</h3>
            <form method="post" id="deleteForm">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal('confirmDeleteModal')">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Brand -->
    <div id="addBrandModal" class="modal">
        <div class="modal-content">
            <h3>Add New Brand</h3>
            <form method="post">
                <input type="text" name="brand_name" placeholder="Brand Name" required>
                <button type="submit" name="add_brand">Add</button>
                <button type="button" onclick="closeModal('addBrandModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="editBrandModal" class="modal">
        <div class="modal-content">
            <h3>Edit Brand</h3>
            <form method="post">
                <input type="hidden" name="edit_id" id="edit_brand_id">
                <input type="text" name="name" id="edit_brand_name" placeholder="Brand Name" required>
                <button type="submit" name="edit_brand">Save Changes</button>
                <button type="button" onclick="closeModal('editBrandModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this item?</h3>
            <form method="post" id="deleteForm">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal('confirmDeleteModal')">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Size -->
    <div id="addSizeModal" class="modal">
        <div class="modal-content">
            <h3>Add New Size</h3>
            <form method="post">
                <input type="text" name="size" placeholder="Size Value" required>
                <button type="submit" name="add_size">Add</button>
                <button type="button" onclick="closeModal('addSizeModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="editSizeModal" class="modal">
        <div class="modal-content">
            <h3>Edit Size</h3>
            <form method="post">
                <input type="hidden" name="edit_id" id="edit_size_id">
                <input type="text" name="size" id="edit_size_value" placeholder="Size Value" required>
                <button type="submit" name="edit_size">Save Changes</button>
                <button type="button" onclick="closeModal('editSizeModal')">Cancel</button>
            </form>
        </div>
    </div>

    <div id="confirmDeleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this item?</h3>
            <form method="post" id="deleteForm">
                <button type="submit">Yes, Delete</button>
                <button type="button" onclick="closeModal('confirmDeleteModal')">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>