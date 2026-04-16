<?php
	// Include the database connection
	include 'db.php';

	// Fetch all recipes from the database, newest first
	$result = $conn->query("SELECT * FROM recipes ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>BerryCloud – Berry-Inspired Recipes</title>

	<!-- Google Fonts: Playfair Display for headings, DM Sans for body -->
	<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />

	<!-- Main stylesheet -->
	<link rel="stylesheet" href="css/style.css" />
</head>
<body>

	<!-- ======= HEADER / NAVBAR ======= -->
	<header class="navbar">
		<div class="nav-inner">

			<!-- Logo -->
			<a href="index.php" class="logo">
				<span class="logo-icon">🧁</span>
				<span class="logo-text">BerryCloud</span>
			</a>

			<!-- Navigation links -->
			<nav class="nav-links">
				<a href="index.php" class="nav-link active">Browse</a>
				<a href="add.php" class="btn-add">+ Add Recipe</a>
			</nav>

			<!-- Mobile menu toggle -->
			<button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
				<span></span><span></span><span></span>
			</button>

		</div>

		<!-- Mobile dropdown menu -->
		<div class="mobile-menu" id="mobileMenu">
			<a href="index.php">Browse Recipes</a>
			<a href="add.php">+ Add Recipe</a>
		</div>
	</header>

	<!-- ======= HERO SECTION ======= -->
	<section class="hero">
		<div class="hero-content">
			<p class="hero-eyebrow">A community for dessert lovers</p>
			<h1 class="hero-title">Life is sweeter<br /><em>with berries.</em></h1>
			<p class="hero-desc">Discover, share, and celebrate berry-inspired recipes from our growing community of dessert enthusiasts.</p>
			<a href="add.php" class="btn-primary">Share Your Recipe</a>
		</div>

		<!-- Decorative floating blobs -->
		<div class="hero-blob blob-1"></div>
		<div class="hero-blob blob-2"></div>
		<div class="hero-blob blob-3"></div>
	</section>

	<?php if (isset($_GET['added'])): ?>
		<div class="alert alert-success" style="max-width:1100px;margin:16px auto 0;padding:14px 24px;">
			🧁 Recipe published successfully!
		</div>
	<?php elseif (isset($_GET['deleted'])): ?>
		<div class="alert alert-error" style="max-width:1100px;margin:16px auto 0;padding:14px 24px;">
			🗑 Recipe deleted.
		</div>
	<?php endif; ?>

	<!-- ======= RECIPES SECTION ======= -->
	<main class="recipes-section">
		<div class="section-header">
			<h2 class="section-title">Latest Recipes</h2>
			<p class="section-sub">Fresh from our community kitchen</p>
		</div>

		<!-- Recipe Cards Grid -->
		<div class="recipe-grid" id="recipeGrid">

			<?php if ($result && $result->num_rows > 0): ?>
				<?php while ($row = $result->fetch_assoc()): ?>

					<!-- Single recipe card -->
					<article class="recipe-card">

						<!-- Recipe image -->
						<a href="recipe.php?id=<?= $row['id'] ?>" class="card-img-link">
							<div class="card-img-wrap">
								<img
									src="<?= htmlspecialchars($row['image_url'] ?: 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&q=80') ?>"
									alt="<?= htmlspecialchars($row['title']) ?>"
									class="card-img"
									loading="lazy"
								/>
								<!-- Berry tag badge -->
								<span class="card-badge"><?= htmlspecialchars($row['berry_type'] ?: 'Berry') ?></span>
							</div>
						</a>

						<!-- Card content -->
						<div class="card-body">
							<h3 class="card-title">
								<a href="recipe.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a>
							</h3>
							<p class="card-desc"><?= htmlspecialchars(mb_strimwidth($row['description'], 0, 90, '…')) ?></p>

							<!-- Card meta: prep time & servings -->
							<div class="card-meta">
								<span class="meta-item">⏱ <?= htmlspecialchars($row['prep_time'] ?: '-') ?> min</span>
								<span class="meta-item">🍽 <?= htmlspecialchars($row['servings'] ?: '-') ?> servings</span>
							</div>

							<!-- Action buttons -->
							<div class="card-actions">
								<a href="recipe.php?id=<?= $row['id'] ?>" class="btn-view">View</a>
								<a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
								<!-- Delete triggers a confirm dialog -->
								<a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirmDelete(this.href)">Delete</a>
							</div>
						</div>

					</article>

				<?php endwhile; ?>

			<?php else: ?>
				<!-- Empty state when no recipes exist yet -->
				<div class="empty-state">
					<div class="empty-icon">🍰</div>
					<h3>No recipes yet!</h3>
					<p>Be the first to share a berry-inspired creation.</p>
					<a href="add.php" class="btn-primary">Add First Recipe</a>
				</div>
			<?php endif; ?>

		</div><!-- end .recipe-grid -->
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

	<?php if (isset($_GET['added'])): ?>
		<script>document.addEventListener('DOMContentLoaded',()=>{
			showToast('success','Recipe published!','Your berry recipe is now live on BerryCloud.');
			history.replaceState(null, '', 'index.php');
		});</script>
	<?php elseif (isset($_GET['deleted'])): ?>
		<script>document.addEventListener('DOMContentLoaded',()=>{
			showToast('warning','Recipe deleted','The recipe has been permanently removed.');
			history.replaceState(null, '', 'index.php');
		});</script>
	<?php endif; ?>
</body>
</html>