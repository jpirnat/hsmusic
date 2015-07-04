<?php
	include_once 'dbinfo.php';

	try
	{
		$db = new PDO("mysql:host={$dbhost};port={$dbport};dbname={$dbname}", $dbuser, $dbpass);
	}
	catch (PDOException $e)
	{
		echo 'Connection failed: ' . $e->getMessage();
	}

	$stmt = $db->query('SELECT song_id, album, track_number, song_title, song_url, song_length FROM songs');

	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

	function getSongArtists($db, $song_id)
	{
		$stmt = $db->prepare('SELECT artcred_text FROM artist_credits WHERE song_id=:song_id');
		$stmt->bindValue(':song_id', $song_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	function getSongRemixes($db, $song_id)
	{
		$stmt = $db->prepare('SELECT song_title, song_url FROM songs WHERE song_id IN (SELECT beingremixed_id FROM remixes WHERE song_id=:song_id)');
		$stmt->bindValue(':song_id', $song_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	function getSongCommentaries($db, $song_id)
	{
		$stmt = $db->prepare('SELECT commentary_url FROM Commentaries WHERE song_id=:song_id');
		$stmt->bindValue(':song_id', $song_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="tablesorter/jquery-latest.js"></script> 
		<script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script> 
		<link rel="stylesheet" type="text/css" href="tablesorter/themes/blue/style.css">
	</head>
	<body>

		<table id="myTable" class="tablesorter">
			<thead>
				<tr>
					<th>Album</th>
					<th>Track</th>
					<th>Title</th>
					<th>Length</th>
					<th>Artist</th>
					<th>Remixes</th>
					<th>Commentary</th>
				</tr>
			</thead>
			<tbody>
<?php foreach ($results as $row): ?>
<tr>
	<td><?php echo $row['album']; ?></td>
	<td><?php echo $row['track_number']; ?></td>
	<td><a href="<?php echo $row['song_url']; ?>"><?php echo $row['song_title']; ?></a></td>
	<td><?php echo $row['song_length']; ?></td>

	<td>
<?php // artists
$artists = getSongArtists($db, $row['song_id']);
foreach ($artists as $artist)
{
	echo $artist['artcred_text']. '<br>';
}
?>
	</td>
	
	<td>
<?php // remixes
$remixes = getSongRemixes($db, $row['song_id']);
foreach ($remixes as $remix)
{
	echo "<a href='{$remix['song_url']}'>{$remix['song_title']}</a><br>";
}
?>
	</td>
	
	<td>
<?php // commentary
$commentaries = getSongCommentaries($db, $row['song_id']);
foreach ($commentaries as $commentary)
{
	echo $commentary['commentary_url'] . '<br>';
}
?>
	</td>
</tr>
<?php endforeach; ?>
			</tbody>
		</table>

<script type="text/javascript">
$(document).ready(function() {
	$("#myTable").tablesorter();
}); 
</script>

	</body>
</html>