<?php
	include 'db.php';

	// Get the recipe ID from the URL, default to 0 if not provided
	$id = intval($_GET['id'] ?? 0);

	// Fetch the specific recipe from the database
	$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$recipe = $result->fetch_assoc();
	$stmt->close();

	// If no recipe found, redirect to homepage
	if (!$recipe) {
		header('Location: index.php');
		exit;
	}

	// Split ingredients and steps by newline into arrays for list rendering
	$ingredients = array_filter(array_map('trim', explode("\n", $recipe['ingredients'])));
	$steps = array_filter(array_map('trim', explode("\n", $recipe['steps'])));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?= htmlspecialchars($recipe['title']) ?> – BerryCloud</title>

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
				<a href="index.php" class="nav-link active">Browse</a>
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

	<!-- ======= RECIPE DETAIL ======= -->
	<main class="recipe-detail">

		<!-- Back link -->
		<a href="index.php" class="back-link">← Back to Recipes</a>

		<!-- Recipe image -->
		<img
			src="<?= htmlspecialchars($recipe['image_url'] ?: 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=800&q=80') ?>"
			alt="<?= htmlspecialchars($recipe['title']) ?>"
			class="recipe-detail-img"
		/>

		<!-- Header -->
		<div class="recipe-detail-header">
			<span class="recipe-detail-badge"><?= htmlspecialchars($recipe['berry_type'] ?: 'Berry') ?></span>
			<h1 class="recipe-detail-title"><?= htmlspecialchars($recipe['title']) ?></h1>

			<div class="recipe-detail-meta">
				<?php if ($recipe['prep_time']): ?>
					<span>⏱ <?= htmlspecialchars($recipe['prep_time']) ?> min prep</span>
				<?php endif; ?>
				<?php if ($recipe['servings']): ?>
					<span>🍽 <?= htmlspecialchars($recipe['servings']) ?> servings</span>
				<?php endif; ?>
				<?php if ($recipe['created_at']): ?>
					<span>📅 <?= date('M j, Y', strtotime($recipe['created_at'])) ?></span>
				<?php endif; ?>
			</div>

			<p class="recipe-detail-desc"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
		</div>

		<!-- Ingredients -->
		<h2 class="recipe-section-title">Ingredients</h2>
		<ul class="ingredients-list">
			<?php foreach ($ingredients as $ingredient): ?>
				<li><?= htmlspecialchars($ingredient) ?></li>
			<?php endforeach; ?>
		</ul>

		<!-- Steps -->
		<h2 class="recipe-section-title" style="margin-top: 32px;">Instructions</h2>
		<ol class="steps-list">
			<?php foreach ($steps as $step): ?>
				<li><?= htmlspecialchars($step) ?></li>
			<?php endforeach; ?>
		</ol>

		<!-- Action buttons -->
		<div class="recipe-detail-actions">
			<a href="edit.php?id=<?= $recipe['id'] ?>" class="btn-primary">✏️ Edit Recipe</a>
			<a href="delete.php?id=<?= $recipe['id'] ?>" class="btn-cancel" onclick="return confirmDelete(this.href)" style="color: #c0505a; border-color: #f9c0c7;">🗑 Delete Recipe</a>
		</div>

	</main>

	<!-- ======= FOOTER ======= -->
	<footer class="footer">
		<p>🧁 <strong>BerryCloud</strong> - Made with love by dessert enthusiasts.</p>
	</footer>

	<!-- Delete confirmation modal -->
	<div class="modal-overlay" id="deleteModal">
		<div class="modal">
			<div class="modal-icon">🗑️</div>
			<h2 class="modal-title">Delete Recipe?</h2>
			<p class="modal-desc">This action is permanent and cannot be undone. Your recipe will be removed from BerryCloud.</p>
			<div class="modal-actions">
				<button class="btn-modal-cancel" id="modalCancel">Keep it</button>
				<button class="btn-modal-confirm" id="modalConfirm">Yes, delete</button>
			</div>
		</div>
	</div>

	<script src="js/main.js"></script>
	<script src="js/toast.js"></script>

	<?php if (isset($_GET['edited'])): ?>
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				showToast('success', 'Recipe updated!', 'Your changes have been saved.');
				history.replaceState(null, '', 'recipe.php?id=<?= $recipe['id'] ?>');
			});
		</script>
	<?php endif; ?>
</body>
</html>