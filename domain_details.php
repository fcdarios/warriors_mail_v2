<?php 

require_once __DIR__ . '/functions.php';

// Authentication checking
require __DIR__ . '/login.function.php';

html_start(__('domain_details'), 0, false, false);

if (isset($_POST['save']))
{
	$cont = $_POST['cont'];
	$file = $_POST['archive'];
	$contenido[]=array();
	for ($i = 0; $i <= $cont; $i++)
	{
		
		if ($_POST['action'.$i])
		{
			$regla = $_POST['action'.$i]."\t".$_POST['regla'.$i]."\t".$_POST['mensaje1'.$i]."\t".$_POST['mensaje2'.$i]; 
			array_push($contenido,$regla);
		}
		else
		{
			array_push($contenido,$_POST[$i]);
		}
		 
	}

	$archive = "/etc/MailScanner/domain/".$file;
	$open = fopen($archive,'w');
    for ($i=1; $i < count($contenido);$i++)
    {
    	fwrite($open,$contenido[$i].PHP_EOL);
    }
    fclose($open);
}

$archive = $_GET['archive'];

$ruta = "/etc/MailScanner/domain/".$archive;
$open = fopen($ruta,'r');
$contenido = fread($open,filesize($ruta));
fclose($open);
$contenido = explode("\n",$contenido);
echo '<form action="domain_details.php?archive='.$archive.'" method="POST">';
echo '<input name="cont" id="cont" type="hidden" value="">';
echo '<input name="archive" type="hidden" value="'.$archive.'">';
for ($i = 0; $i < count($contenido); $i++)
{
	$comen = strpos($contenido[$i], "#");
	if ($comen === 0 or empty($contenido[$i]))
	{
		echo '<input type="hidden" name="'.$i.'" value="'.$contenido[$i].'">';
	}
	else
	{
		$conten = explode("\t", $contenido[$i]);
		echo '<div class="row" id="'.$i.'">';
		echo '<div class="col-md-2">';
		echo '<select name="action'.$i.'" class="form-control">
			<option value="'.$conten[0].'">'.$conten[0].'</option>
			<optgroup label="_________">
        		<option value="allow">allow</option>
				<option value="deny">deny</option>
    		</optgroup>
		</select>';
		echo '</div>';
		echo '<div class="col-md-6">';
		echo '<input type="text" name="regla'.$i.'" value="'.$conten[1].'" class="form-control">';
		echo '</div>';
		echo '<div class="col-md-6">';
		echo '<input type="text" name="mensaje1'.$i.'"value="'.$conten[2].'" class="form-control">';
		echo '</div>';
		echo '<div class="col-md-6">';
		echo '<input type="text" name="mensaje2'.$i.'" value="'.$conten[3].'" class="form-control">';
		echo '</div>';
		echo '<div class="col-md-2">';
		echo '<a class="btn btn-danger" onclick="removeRule(\''.$i.'\')"><i class="fa fa-trash-o"></i> Delete</a>';
		echo '</div>';
		echo '</div>';
		echo "</br>";
	}
	
}
echo '<div id="add"></div>';
echo '<div class="row"><div class="col-md-12"><a class="btn btn-primary" onclick="addRules();"><i class="fa fa-plus"></i> Add Rule</a></div></div></br>';
echo '<div class="row"><div class="col-md-12"><button type="submit" class="btn btn-success" name="save">Save</button></div></div>';
echo '</form>';
?>

<script>
var nextinputdom = <?php echo count($contenido)-1 ?>;
document.getElementById("cont").value = nextinputdom;

function addRules(){
	nextinputdom++;
	rule ='<div class="row" id="'+nextinputdom+'"><div class="col-md-2"><select name="action'+nextinputdom+'" class="form-control"><option value="allow">allow</option><option value="deny">deny</option></select></div><div class="col-md-6"><input type="text" name="regla'+nextinputdom+'" class="form-control"></div><div class="col-md-6"><input type="text" name="mensaje1'+nextinputdom+'" class="form-control"></div><div class="col-md-6"><input type="text" name="mensaje2'+nextinputdom+'" class="form-control"></div><div class="col-md-2"><a class="btn btn-danger" onclick="removeRule('+nextinputdom+')"><i class="fa fa-trash-o"></i> Delete</a></div></div></br>';
	$("#add").append(rule); 
	document.getElementById('cont').value = nextinputdom;
 }
 function removeRule(id){
    $("#"+id).remove();
    }
</script>

<?php
// Add footer
html_end();
// Close any open db connections
dbclose();