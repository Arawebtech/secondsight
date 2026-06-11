<?php require_once('header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM tbl_end_category WHERE ecat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php
	$p_ids = [];

	// Getting all ecat ids
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE ecat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$p_ids[] = $row['p_id'];
	}


	for($i=0;$i<count($p_ids);$i++) {

		// Getting photo ID to unlink from folder
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$p_featured_photo = $row['p_featured_photo'];
			if($p_featured_photo != '' && file_exists('../assets/img/product/'.$p_featured_photo)) {
				unlink('../assets/img/product/'.$p_featured_photo);
			}
		}

		// Getting other photo ID to unlink from folder
		$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$photo = $row['photo'];
			if($photo != '' && file_exists('../assets/img/product/'.$photo)) {
				unlink('../assets/img/product/'.$photo);
			}
		}

		// Getting variant photos to unlink from folder
		$statement = $pdo->prepare("SELECT photo FROM tbl_product_price WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$photo = $row['photo'];
			if($photo != '' && file_exists('../assets/img/product-detail/'.$photo)) {
				unlink('../assets/img/product-detail/'.$photo);
			}
		}

		// Delete from tbl_product
		$statement = $pdo->prepare("DELETE FROM tbl_product WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_product_photo
		$statement = $pdo->prepare("DELETE FROM tbl_product_photo WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_product_size
		$statement = $pdo->prepare("DELETE FROM tbl_product_size WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_product_color
		$statement = $pdo->prepare("DELETE FROM tbl_product_color WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_product_price
		$statement = $pdo->prepare("DELETE FROM tbl_product_price WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_rating
		$statement = $pdo->prepare("DELETE FROM tbl_rating WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));

		// Delete from tbl_payment using linked order_id
		$statement = $pdo->prepare("SELECT DISTINCT order_id FROM tbl_order WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) {
			$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE order_id=?");
			$statement1->execute(array($row['order_id']));
		}

		// Delete from tbl_order
		$statement = $pdo->prepare("DELETE FROM tbl_order WHERE p_id=?");
		$statement->execute(array($p_ids[$i]));
	}

	// Delete from tbl_end_category
	$statement = $pdo->prepare("DELETE FROM tbl_end_category WHERE ecat_id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: end-category.php');
?>