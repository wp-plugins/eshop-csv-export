<h2>Done!</h2>
<ul>
<li><strong>CREATED: </strong><?php echo count($stats['Insert']); ?></li>
<li><strong>MODIFIED: </strong><?php echo count($stats['Update']); ?></li>
<li><strong>DELETED (Moved to trash): </strong><?php echo count($stats['Delete']); ?></li>
</ul>
<br/>
<h2>Backup</h2>
<p>This backup was created before you made any changes.  This is your last chance to download and save it!</p>
<blockquote>
<?php echo "<a href='$backup_link'>Download Backup</a>"; ?>
</blockquote>
<br/>

<?php if ( count($stats['Error']) > 0 ): ?>

<h2 class="ecsvi_red">Errors</h2>

<table class="ecsvi" cellspacing="0" cellpadding="0">
<thead>
<tr><th>ID</th><th>Message</th></tr>
</thead>
<tbody>
<?php
	$rows = '';
	foreach ( $stats['Error'] as $error ) {
		$message = 'Unable to find a post with this ID.  Have you previously deleted it?  Try exporting to CSV to get an accurate list of posts.';
		$rows .= sprintf( "<tr><td>%s</td><td>%s</td></tr>", $error, $message );
	}
	echo $rows;
?>
</tbody>
</table>
<br/>

<?php endif; ?>



