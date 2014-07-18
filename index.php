<?php
	session_start();
	
	include_once("dbinfo.php");
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		
	if ($mysqli->connect_error)
	{
		die('Connect Error: ' . $mysqli->connect_error());
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="/../include/tablesorter/jquery-latest.js"></script> 
		<script type="text/javascript" src="/../include/tablesorter/jquery.tablesorter.js"></script> 
		<link rel="stylesheet" type="text/css" href="/../include/tablesorter/themes/blue/style.css">
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
<?php
	$sql = "SELECT song_id, album, track_number, song_title, song_url, song_length FROM Songs";
	$result = $mysqli->query($sql);

	while ($row = $result->fetch_assoc())
	{
		$song_id = $row["song_id"];
		$album = $row["album"];
		$tracknumber = $row["track_number"];
		$songtitle = $row["song_title"];
		$songurl = $row["song_url"];
		$songlength = $row["song_length"];
?>
<tr>
	<td><?php echo $album; ?></td>
	<td><?php echo $tracknumber; ?></td>
	<td><a href="<?php echo $songurl; ?>"><?php echo $songtitle; ?></a></td>
	<td><?php echo $songlength; ?></td>
<?php
		// artist credits
		echo "\t<td>";
		$artist_sql = "SELECT artcred_text FROM Artist_Credits WHERE song_id=$song_id";
		$artist_result = $mysqli->query($artist_sql);
		while ($artist_row = $artist_result->fetch_assoc())
		{	
			echo "\n\t\t" . $artist_row["artcred_text"] . " <br/>";
		}
		echo "\n\t</td>\n";
		
		// remixes
		echo "\t<td>";
		$remix_sql = "SELECT song_title, song_url FROM Songs WHERE song_id IN (SELECT beingremixed_id FROM Remixes WHERE song_id=$song_id)";
		$remix_result = $mysqli->query($remix_sql);
		while ($remix_row = $remix_result->fetch_assoc())
		{
			$remix_title = $remix_row["song_title"];
			$remix_url = $remix_row["song_url"];
			echo "\n\t\t<a href=\"$remix_url\">$remix_title</a> <br/>";
		}
		echo "\n\t</td>\n";
		
		// commentary
		echo "\t<td>";
		$comm_sql = "SELECT commentary_url FROM Commentaries WHERE song_id=$song_id";
		$comm_result = $mysqli->query($comm_sql);
		while ($comm_row = $comm_result->fetch_assoc())
		{
			$comm_url = $comm_row["commentary_url"];
			echo "\n\t\t$comm_url <br/>";
		}
		echo "\n\t</td>\n";
		
		echo "</tr>\n";
	} // while ($row = $result->fetch_assoc())
?>
			</tbody>
		</table>

<script type="text/javascript">
$(document).ready(
	function()
	{
		$("#myTable").tablesorter();
	}
); 
</script>

	</body>
</html>