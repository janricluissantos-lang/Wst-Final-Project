<?php
	include 'db.php';

	$id = intval($_GET['id'] ?? 0);
	$message = '';
	$messageType = '';

	// Fetch the existing recipe to pre-fill the form
	$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$recipe = $result->fetch_assoc();
	$stmt->close();

	// If recipe doesn't exist, redirect home
	if (!$recipe) {
		header('Location: index.php');
		exit;
	}

	// Handle form submission
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		$title = trim($_POST['title'] ?? '');
		$description = trim($_POST['description'] ?? '');
		$ingredients = trim($_POST['ingredients'] ?? '');
		$steps = trim($_POST['steps'] ?? '');
		$berry_type = trim($_POST['berry_type'] ?? 'Berry');
		$prep_time = intval($_POST['prep_time'] ?? 0);
		$servings = intval($_POST['servings'] ?? 0);

		// Handle image upload – keep the old image if no new one is uploaded
		$image_url = $recipe['image_url'];

		if (!empty($_FILES['image_file']['name'])) {
			$uploadDir = 'uploads/';
			if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

			$ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
			$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

			if (!in_array($ext, $allowed)) {
				$message = 'Invalid image type. Allowed: JPG, PNG, GIF, WEBP.';
				$messageType = 'error';
			} elseif ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
				$message = 'Image must be under 5MB.';
				$messageType = 'error';
			} else {
				$filename = uniqid('berry_', true) . '.' . $ext;
				$destPath = $uploadDir . $filename;
				if (move_uploaded_file($_FILES['image_file']['tmp_name'], $destPath)) {
					// Delete the old image file if it exists locally
					if ($recipe['image_url'] && file_exists($recipe['image_url'])) {
						unlink($recipe['image_url']);
					}
					$image_url = $destPath;
				} else {
					$message = 'Image upload failed. Please try again.';
					$messageType = 'error';
				}
			}
		}

		// Validation
		if (empty($message)) {
			if (empty($title) || empty($description) || empty($ingredients) || empty($steps)) {
				$message = 'Please fill in all required fields.';
				$messageType = 'error';
			} else {
				$stmt = $conn->prepare(
					"UPDATE recipes SET title=?, description=?, ingredients=?, steps=?, berry_type=?, prep_time=?, servings=?, image_url=? WHERE id=?"
				);
				// s=string, i=integer: title, desc, ingredients, steps, berry_type, prep_time, servings, image_url, id
				$stmt->bind_param('sssssiisi', $title, $description, $ingredients, $steps, $berry_type, $prep_time, $servings, $image_url, $id);

				if ($stmt->execute()) {
					header('Location: recipe.php?id=' . $id . '&edited=1');
					exit;
				} else {
					$message = 'Something went wrong. Please try again.';
					$messageType = 'error';
				}
				$stmt->close();

				// Keep form values from POST on error
				$recipe['title'] = $title;
				$recipe['description'] = $description;
				$recipe['ingredients'] = $ingredients;
				$recipe['steps'] = $steps;
				$recipe['berry_type'] = $berry_type;
				$recipe['prep_time'] = $prep_time;
				$recipe['servings'] = $servings;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Edit Recipe – BerryCloud</title>

	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>

	<!-- ======= NAVBAR ======= -->
	<header class="navbar">
		<div class="nav-inner">
			<a href="index.php" class="logo">
				<span class="logo-icon">🧁</span>
				<span class="logo-text">BerryCloud</span>
			</a>
			<nav class="nav-links">
				<a href="index.php" class="nav-link">Browse</a>
				<a href="add.php" class="btn-add">+ Add Recipe</a>
			</nav>
			<button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
				<span></span><span></span><span></span>
			</button>
		</div>
		<div class="mobile-menu" id="mobileMenu">
			<a href="index.php">Browse Recipes</a>
			<a href="add.php">+ Add Recipe</a>
		</div>
	</header>

	<!-- ======= FORM PAGE ======= -->
	<main class="form-page">

		<a href="recipe.php?id=<?= $id ?>" class="back-link">← Back to Recipe</a>

		<h1 class="form-page-title">Edit Recipe ✏️</h1>
		<p class="form-page-sub">Update your berry-inspired creation.</p>

		<?php if ($message): ?>
			<script>
				document.addEventListener('DOMContentLoaded', () => {
					showToast(
						'<?= $messageType === "error" ? "error" : "success" ?>',
						'<?= $messageType === "error" ? "Oops!" : "Done!" ?>',
						'<?= addslashes(htmlspecialchars($message)) ?>'
					);
				});
			</script>
		<?php endif; ?>

		<div class="form-card">
			<form method="POST" action="edit.php?id=<?= $id ?>" id="editForm" enctype="multipart/form-data">

				<!-- Recipe Title -->
				<div class="form-group">
					<label class="form-label" for="title">Recipe Title <span class="required">*</span></label>
					<input
						type="text"
						id="title"
						name="title"
						class="form-input"
						placeholder="e.g. Blueberry Lemon Tart"
						value="<?= htmlspecialchars($recipe['title']) ?>"
						required
					/>
				</div>

				<!-- Description -->
				<div class="form-group">
					<label class="form-label" for="description">Description <span class="required">*</span></label>
					<textarea
						id="description"
						name="description"
						class="form-textarea"
						placeholder="A short description of your recipe…"
						required
					><?= htmlspecialchars($recipe['description']) ?></textarea>
				</div>

				<!-- Berry Type & Prep Time -->
				<div class="form-row">
					<div class="form-group">
						<label class="form-label" for="berry_type">Berry Type</label>
						<select id="berry_type" name="berry_type" class="form-select">
							<?php
								$berries = ['Strawberry', 'Blueberry', 'Raspberry', 'Blackberry', 'Cranberry', 'Mixed Berry', 'Other'];
								$selected = $recipe['berry_type'] ?? 'Strawberry';
								foreach ($berries as $b) {
									$isSelected = ($b === $selected) ? 'selected' : '';
									echo "<option value=\"$b\" $isSelected>$b</option>";
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<label class="form-label" for="prep_time">Prep Time (minutes)</label>
						<input
							type="number"
							id="prep_time"
							name="prep_time"
							class="form-input"
							placeholder="e.g. 20"
							min="1"
							value="<?= htmlspecialchars($recipe['prep_time']) ?>"
						/>
					</div>
				</div>

				<!-- Servings & Image Upload -->
				<div class="form-row">
					<div class="form-group">
						<label class="form-label" for="servings">Servings</label>
						<input
							type="number"
							id="servings"
							name="servings"
							class="form-input"
							placeholder="e.g. 4"
							min="1"
							value="<?= htmlspecialchars($recipe['servings']) ?>"
						/>
					</div>

					<div class="form-group">
						<label class="form-label" for="image_file">Recipe Image</label>
						<input
							type="file"
							id="image_file"
							name="image_file"
							class="form-input"
							accept="image/jpeg,image/png,image/gif,image/webp"
						/>
						<p class="form-hint">JPG, PNG, GIF or WEBP · Max 5MB</p>
					</div>
				</div>

				<!-- Current / Preview Image -->
				<div class="form-group" id="previewWrap" style="<?= $recipe['image_url'] ? 'display:block;' : 'display:none;' ?>">
					<label class="form-label">
						<?= $recipe['image_url'] ? 'Current Image' : 'Image Preview' ?>
					</label>
					<img
						id="imagePreview"
						src="<?= htmlspecialchars($recipe['image_url'] ?? '') ?>"
						alt="Preview"
						class="img-preview"
					/>
				</div>

				<!-- Ingredients -->
				<div class="form-group">
					<label class="form-label" for="ingredients">Ingredients <span class="required">*</span></label>
					<p class="form-hint">Enter one ingredient per line.</p>
					<textarea
						id="ingredients"
						name="ingredients"
						class="form-textarea textarea-tall"
						required
					><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
				</div>

				<!-- Steps -->
				<div class="form-group">
					<label class="form-label" for="steps">Instructions <span class="required">*</span></label>
					<p class="form-hint">Enter one step per line.</p>
					<textarea
						id="steps"
						name="steps"
						class="form-textarea textarea-tall"
						required
					><?= htmlspecialchars($recipe['steps']) ?></textarea>
				</div>

				<!-- Submit / Cancel -->
				<div class="form-actions">
					<button type="submit" class="btn-submit">💾 Save Changes</button>
					<a href="recipe.php?id=<?= $id ?>" class="btn-cancel">Cancel</a>
				</div>

			</form>
		</div>

	</main>

	<!-- ======= FOOTER ======= -->
	<footer class="footer">
		<p>🧁 <strong>BerryCloud</strong> - Made with love by dessert enthusiasts.</p>
	</footer>

	<script src="js/main.js"></script>
	<script src="js/add.js"></script>
	<script src="js/toast.js"></script> 
</body>
</html>