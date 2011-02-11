<?php
	if ( !$export_link ):
?>

	<h2>No products found</h2>
	<p>It looks like you haven't created any eShop products yet.  Create a page or post, fill out the eShop fields, update, then come back to this page and your export file should be waiting for you! :)</p>

<?php	else: ?>
	<h2>Done!</h2>
	<p><a href='<?php echo $export_link; ?>'>Click here</a> to download your CSV file.

<?php endif; ?>
