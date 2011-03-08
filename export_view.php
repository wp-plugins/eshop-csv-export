<?php
	if ( !$export_link ):
?>

	<h2>No products found</h2>
	<p>It looks like you haven't created any eShop products yet.  Create a page or post, fill out the eShop fields, update, then come back to this page and your export file should be waiting for you! :)</p>

<?php	else: ?>
	<h2>Done!</h2>
	<p><a href='<?php echo $export_link; ?>'>Click here</a> to download your CSV file.</p>

<p><strong>Good plugins take a lot of time to write and support.  The best way to 'vote' for continued development is to make a donation.  Thank you!</strong><br/>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="FQUR9BG2XY3TC">
<input type="image" src="https://www.paypal.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1">
</form>
</p>

<?php endif; ?>
