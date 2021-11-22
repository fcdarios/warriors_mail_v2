<?php

require_once __DIR__ . '/functions.php';
require __DIR__ . '/login.function.php';

function service($action)
{
	$archive = "/var/www/html/mailscanner/status/daemon.txt";
	$open = fopen($archive, 'w');
	fwrite($open, $action);
	fclose($open);
	header('Location: mcp.php');
}

if (isset($_POST['save'])) {
	$archive = "/etc/MailScanner/mcp/mcp.cf";
	$open = fopen($archive, 'r');
	if (filesize($archive)) {
		$contenido = fread($open, filesize($archive));
	}
	fclose($open);
	if (filesize($archive)) {
		$filas = file($archive);
		$ultima_linea = $filas[count($filas) - 1];
		$num = $ultima_linea[20] + 1;
		$contenido = explode("\n", $contenido);
	} else {
		$num = 1;
		$contenido = array();
	}
	$phrases = "header    SAMPLE_RULE" . $num . "    Subject =~ /" . $_POST['phrases'] . "/i";
	$description = "describe    SAMPLE_RULE" . $num . "    " . $_POST['description'];
	$score = "score    SAMPLE_RULE" . $num . "    " . $_POST['score'];
	array_push($contenido, $phrases);
	array_push($contenido, $description);
	array_push($contenido, $score);
	$open = fopen($archive, 'w');
	for ($i = 0; $i < count($contenido); $i++) {
		fwrite($open, $contenido[$i] . PHP_EOL);
	}
	fclose($open);
	$action = "reload";
	service($action);
}

if (isset($_POST['save2'])) {
	$archive = "/etc/MailScanner/mcp/mcp.cf";
	$open = fopen($archive, 'r');
	if (filesize($archive)) {
		$contenido = fread($open, filesize($archive));
	}
	fclose($open);
	if (filesize($archive)) {
		$filas = file($archive);
		$ultima_linea = $filas[count($filas) - 1];
		$num = $ultima_linea[20] + 1;
		$contenido = explode("\n", $contenido);
	} else {
		$num = 1;
		$contenido = array();
	}
	$phrases = "body    SAMPLE_RULE" . $num . "    /" . $_POST['phrases'] . "/i";
	$description = "describe    SAMPLE_RULE" . $num . "    " . $_POST['description'];
	$score = "score    SAMPLE_RULE" . $num . "    " . $_POST['score'];
	array_push($contenido, $phrases);
	array_push($contenido, $description);
	array_push($contenido, $score);
	$open = fopen($archive, 'w');
	for ($i = 0; $i < count($contenido); $i++) {
		fwrite($open, $contenido[$i] . PHP_EOL);
	}
	fclose($open);
	$action = "reload";
	service($action);
}

if (isset($_POST['delete'])) {
	$archive = "/etc/MailScanner/mcp/mcp.cf";
	$open = fopen($archive, 'r');
	if (filesize($archive)) {
		$contenido = fread($open, filesize($archive));
	}
	fclose($open);
	$contenido = explode("\n", $contenido);
	for ($i = 0; $i < 3; $i++) {
		$contenido[$_POST['fila' . $i]] = "";
	}
	$open = fopen($archive, 'w');
	$salto = 0;
	for ($i = 0; $i < count($contenido); $i++) {
		if (!empty($contenido[$i])) {
			fwrite($open, $contenido[$i] . PHP_EOL);
			$salto++;
		}
		if ($salto == 3) {
			fwrite($open, PHP_EOL);
			$salto = 0;
		}
	}
	fclose($open);
	$action = "reload";
	service($action);
}

function contenidoSubject()
{	
	
	$archive = "/etc/MailScanner/mcp/mcp.cf";
	$open = fopen($archive, 'r');
	if (filesize($archive)) {
		$contenido = fread($open, filesize($archive));
	}
	fclose($open);
	$contenido = explode("\n", $contenido);
	$rows = issetRows($contenido, 'header');

	if ( $rows ) {
		echo '<table class="table table-sm table-striped table-wr">';
		echo '<thead>';
		echo '<tr><th>'.__('mcp_phrases69').'</th><th>'.__('mcp_description69').'</th><th>'.__('mcp_score69').'</th><th>'.__('action_0212').'</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		for ($i = 0; $i < count($contenido); $i++) {
			$find = strpos($contenido[$i], "header");
			if ($find === 0) {
				$numFila = 0;
				echo '<form action="mcp.php" method="POST">';
				echo '<tr>';
				for ($j = $i; $j < $i + 3; $j++) {
					$aux = explode("    ", $contenido[$j]);
					$aux[2] = str_replace("Subject =~ /", "", $aux[2]);
					$aux[2] = str_replace("/i", "", $aux[2]);
					echo '<td><input type="hidden" name="fila' . $numFila . '" value="' . $j . '">' . $aux[2] . '</td>';
					$numFila++;
				}	
				echo '<td><button class="btn btn-wr-red font-weight-bold btn-sm" name="delete">'.__('delete04').'</button></td>';
				echo '</tr>';
				echo '</form>';
			}
		}
		echo '</tbody>';
		echo "</table>";
	}else {
		echo __('norowfound03');
	}
}


function contenidoBody()
{
	$archive = "/etc/MailScanner/mcp/mcp.cf";
	$open = fopen($archive, 'r');
	if (filesize($archive)) {
		$contenido = fread($open, filesize($archive));
	}
	fclose($open);
	$contenido = explode("\n", $contenido);
	$rows = issetRows($contenido, 'body');
	

	if ( $rows ) {
		echo '<table class="table table-sm table-striped table-wr">';
		echo '<thead>';
		echo '<tr><th>'.__('mcp_phrases69').'</th><th>'.__('mcp_description69').'</th><th>'.__('mcp_score69').'</th><th>'.__('action_0212').'</th></tr>';
		echo '</thead>';
		echo '<tbody>';
		for ($i = 0; $i < count($contenido); $i++) {
			$find = strpos($contenido[$i], "body");
			if ($find === 0) {
				$numFila = 0;
				echo '<form action="mcp.php" method="POST">';
				echo '<tr>';
				for ($j = $i; $j < $i + 3; $j++) {
					$aux = explode("    ", $contenido[$j]);
					$aux[2] = str_replace("/i", "", $aux[2]);
					$aux[2] = str_replace("/", "", $aux[2]);
					echo '<td><input type="hidden" name="fila' . $numFila . '" value="' . $j . '">' . $aux[2] . '</td>';
					$numFila++;
				}
				echo '<td><button class="btn btn-wr-red font-weight-bold btn-sm" name="delete">'.__('delete04').'</button></td>';
				echo '</tr>';
				echo '</form>';
			}
		}
		echo '</tbody>';
		echo "</table>";
	}else {
		echo __('norowfound03');
	}
}

function issetRows($contenido, $find_content){
	$rows = false;
	for ($i = 0; $i < count($contenido); $i++) {
		$find = strpos($contenido[$i], $find_content);
		if ($find === 0) {
			$rows = true;
			break;
		}
	}


	return $rows;
}

html_head(__('mcp'), 0, false, false);
html_body('MCP');

echo '<div class="container-fluid">';
echo '<ol class="breadcrumb my-4 title_page">';
echo '<li class="breadcrumb-item title_page_li">' . __('mcp03') . '</li>';
echo '</ol>';
echo '<div class="row">';

if ($_SESSION['user_type'] === 'A') {

?>

	<div class="col-lg-6">
		<div class="card card-wr">
			<div class="card-header card-header-warriors">
				<i class="fas fa-cogs"></i>
				<?php echo __('mcp_subject69'); ?>
			</div>
			<div class="card-body card-body-warriors">
				<form action="mcp.php" method="POST">
					<div class="row">
						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_phrases69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="text" 
									size="50" 
									name="phrases" 
								/>
						</div>

						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_description69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="text" 
									size="50" 
									name="description" 
								/>
						</div>
						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_score69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="number" 
									size="50" 
									name="score" 
								/>
						</div>
						<div class="col-xl-10 col-sm-8 col-6"></div>
						<div class="col-xl-2 col-sm-4 col-6 mt-2">
							<button class="pt-2 btn btn-block btn-wr-red font-weight-bold btn-sm" type="submit" name="save" value="Save">
								<?php echo __('save09'); ?>
							</button>
						</div>
               </div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="card card-wr">
			<div class="card-header card-header-warriors">
				<i class="fas fa-toolbox"></i>
				<?php echo __('mcp_body69'); ?>
			</div>
			<div class="card-body card-body-warriors">
			<form action="mcp.php" method="POST">
					<div class="row">
						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_phrases69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="text" 
									size="50" 
									name="phrases" 
								/>
						</div>

						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_description69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="text" 
									size="50" 
									name="description" 
								/>
						</div>
						<div class="col-xl-2 col-6">
							<label class="small w-label m-0 p-0"><?php echo __('mcp_score69'); ?></label>
						</div>   
						<div class="col-xl-10 col-12 my-1">
								<input 
									class="form-control w-input-text" 
									type="number" 
									size="50" 
									name="score" 
								/>
						</div>
						<div class="col-xl-10 col-sm-8 col-6"></div>
						<div class="col-xl-2 col-sm-4 col-6 mt-2">
							<button class="pt-2 btn btn-block btn-wr-red font-weight-bold btn-sm" type="submit" name="save2" value="Save">
								<?php echo __('save09'); ?>
							</button>
						</div>
               </div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-xl-6">
		<div class="card card-wr">
			<div class="card-header card-header-warriors">
				<i class="fas fa-table"></i>
				<?php echo __('mcp_body69'); ?>
			</div>
			<div class="card-body card-body-warriors">
				<div class="table-responsive table-res-wr">
				<?php 
					contenidoSubject(); 
				?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-6">
		<div class="card card-wr">
			<div class="card-header card-header-warriors">
				<i class="fas fa-table"></i>
				<?php echo __('mcp_body69'); ?>
			</div>
			<div class="card-body card-body-warriors">
				<div class="table-responsive table-res-wr">
				<?php 
					contenidoBody(); 
				?>
				</div>
			</div>
		</div>
	</div>

<?php

} else {
	?>

	<div class="col-12 permission-denied">
		<div>
			<?php echo __('permissionsdenied99'); ?>
		</div> 
	</div>

	<?php
}



// -------------------------------------
echo '</div>';
echo '</div>';


// Add footer
html_end_new();

// Close any open db connections
dbclose();

?>