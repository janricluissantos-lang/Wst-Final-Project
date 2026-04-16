<?php
	// Include the database connection
	include 'db.php';

	// Initialize variables for form fields and messages
	$message = '';
	$messageType = '';

	// Check if the form was submitted via POST
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		// Sanitize and retrieve each form field
		$title = trim($_POST['title'] ?? '');
		$description = trim($_POST['description'] ?? '');
		$ingredients = trim($_POST['ingredients'] ?? '');
		$steps = trim($_POST['steps'] ?? '');
		$berry_type = trim($_POST['berry_type'] ?? 'Berry');
		$prep_time = intval($_POST['prep_time'] ?? 0);
		$servings = intval($_POST['servings'] ?? 0);
		$image_url = '';
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
					$image_url = $destPath; // store relative path in DB
				} else {
					$message = 'Image upload failed. Please try again.';
					$messageType = 'error';
				}
			}
		}

		// Basic validation – title, description, ingredients, steps are required
		if (empty($title) || empty($description) || empty($ingredients) || empty($steps)) {
			$message = 'Please fill in all required fields.';
			$messageType = 'error';
		} else {
			// Prepare a safe SQL statement to prevent SQL injection
			$stmt = $conn->prepare(
				"INSERT INTO recipes (title, description, ingredients, steps, berry_type, prep_time, servings, image_url)
				 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
			);

			// Bind the form values to the statement (s = string, i = integer)
			$stmt->bind_param('sssssiis', $title, $description, $ingredients, $steps, $berry_type, $prep_time, $servings, $image_url);

			// Execute and check if it succeeded
			if ($stmt->execute()) {
				// Redirect to homepage after successful insert
				header('Location: index.php?added=1');
				exit;
			} else {
				$message = 'Something went wrong. Please try again.';
				$messageType = 'error';
			}

			$stmt->close();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Add Recipe – BerryCloud</title>

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />

	<!-- Main stylesheet -->
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
				<a href="add.php" class="btn-add active-add">+ Add Recipe</a>
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

		<!-- Back link -->
		<a href="index.php" class="back-link">← Back to Recipes</a>

		<!-- Page heading -->
		<h1 class="form-page-title">Add a Recipe 🍰</h1>
		<p class="form-page-sub">Share your berry-inspired creation with the community.</p>

		<!-- Show error message if validation failed -->
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

		<!-- ======= ADD RECIPE FORM ======= -->
		<div class="form-card">
			<form method="POST" action="add.php" id="addForm" enctype="multipart/form-data">

				<!-- Recipe Title -->
				<div class="form-group">
					<label class="form-label" for="title">Recipe Title <span class="required">*</span></label>
					<input
						type="text"
						id="title"
						name="title"
						class="form-input"
						placeholder="e.g. Blueberry Lemon Tart"
						value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
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
					><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
				</div>

				<!-- Berry Type & Prep Time -->
				<div class="form-row">
					<div class="form-group">
						<label class="form-label" for="berry_type">Berry Type</label>
						<select id="berry_type" name="berry_type" class="form-select">
							<?php
								// List of berry options for the dropdown
								$berries = ['Strawberry', 'Blueberry', 'Raspberry', 'Blackberry', 'Cranberry', 'Mixed Berry', 'Other'];
								$selected = $_POST['berry_type'] ?? 'Strawberry';
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
							value="<?= htmlspecialchars($_POST['prep_time'] ?? '') ?>"
						/>
					</div>
				</div>

				<!-- Servings & Image URL -->
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
							value="<?= htmlspecialchars($_POST['servings'] ?? '') ?>"
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

				<!-- Image Preview -->
				<div class="form-group" id="previewWrap" style="display:none;">
					<label class="form-label">Image Preview</label>
					<img id="imagePreview" src="" alt="Preview" class="img-preview" />
				</div>

				<!-- Ingredients -->
				<div class="form-group">
					<label class="form-label" for="ingredients">Ingredients <span class="required">*</span></label>
					<p class="form-hint">Enter one ingredient per line.</p>
					<textarea
						id="ingredients"
						name="ingredients"
						class="form-textarea textarea-tall"
						placeholder="1 cup fresh strawberries&#10;2 tbsp sugar&#10;1 tsp lemon juice"
						required
					><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
				</div>

				<!-- Steps -->
				<div class="form-group">
					<label class="form-label" for="steps">Instructions <span class="required">*</span></label>
					<p class="form-hint">Enter one step per line.</p>
					<textarea
						id="steps"
						name="steps"
						class="form-textarea textarea-tall"
						placeholder="Wash and hull the strawberries.&#10;Mix with sugar and lemon juice.&#10;Chill for 30 minutes before serving."
						required
					><?= htmlspecialchars($_POST['steps'] ?? '') ?></textarea>
				</div>

				<!-- Submit / Cancel -->
				<div class="form-actions">
					<button type="submit" class="btn-submit">🧁 Publish Recipe</button>
					<a href="index.php" class="btn-cancel">Cancel</a>
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