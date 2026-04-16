<?php
	include 'db.php';

	$id = intval($_GET['id'] ?? 0);

	// Fetch the recipe to verify it exists and get its image path
	$stmt = $conn->prepare("SELECT id, title, image_url FROM recipes WHERE id = ?");
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

	// Delete the recipe from the database
	$stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$stmt->close();

	// Delete the uploaded image file from the server if it exists
	if ($recipe['image_url'] && file_exists($recipe['image_url'])) {
		unlink($recipe['image_url']);
	}

	// Redirect to homepage with a success flag
	header('Location: index.php?deleted=1');
	exit;
?>