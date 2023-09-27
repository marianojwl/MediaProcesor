<!DOCTYPE html>
<?php
require_once 'config.php';
use marianojwl\MediaProcessor\MediaProcessor;
use marianojwl\MediaProcessor\Request;

$mp = new MediaProcessor(10, $_ENV['ORIGINALS_PATH'], $_ENV['PROCESSED_PATH']);


/**
 * ============================================================================|
 * >> SETUP                                                                    |
 * ============================================================================|
 * @param mixed $host Usually 'localhost'                                      |
 * @param string $user Username.                                               |
 * @param string $password Password.                                           |
 * @param string $name Database name.                                          |
 * @param array $ignore Array containing table names to ignore.                |
 *                                                                             |
 * Example:                                                                    |
 * $db = new Database("localhost","root","","myDB", ["table_to_be_ignored"] ); |
 * ============================================================================|
 */
use marianojwl\GenericMySqlCRUD\Database;
$db = new Database("localhost","root","","mediaprocessor", [] );
?>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <title><?=$db->getName()?></title>
</head>
<body>
<!-- NAV BAR / -->
<?php
$tables = $db->getTables();
?>
<nav class="navbar navbar-expand-sm">
  <div class="container-fluid">
    <a class="navbar-brand" href="?"><?=$db->getName()?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav">
        <?php
        foreach($tables as $table)
            echo '<li class="nav-item"><a class="nav-link" href="?table='.$table->getName().'">'.$table->getName().'</a></li>' . PHP_EOL
        ?>
      </ul>
    </div>
  </div>
</nav>
<!-- / NAV BAR -->

<!-- MAIN SECTION -->
<main>
<div class="container mt-3">
<?php
/**
 * DETERMINES WHICH TABLE WE ARE WOKING ON
 */
$tableName = $_GET["table"] ?? "";
$table = $db->getTable($tableName);
if($table !== null) {
?>
    
        <?php
        switch($_GET['action']??"") {
            case "new":
                echo '<h3 class="mb-3">New record for '.$table->getName().'</h3>' . PHP_EOL;
                $table->setTagClass("table table-striped table-bordered")->renderForm(); 
                break;
            case "view":
                echo '<h3 class="mb-3">View record from '.$table->getName().'</h3>' . PHP_EOL;
                $keyValue = $_GET[ $table->getPrimaryKey() ];
                //$formValues = $table->getRecordByPrimaryKey( $keyValue );
                //$table->renderForm( $formValues );
                echo '<div class="row">'.PHP_EOL;
                echo '<div class="col">'.PHP_EOL;
                echo '<h4>Record Sheet</h4>'.PHP_EOL;
                echo '<div class="table-responsive">'.PHP_EOL;
                echo $table->setTagClass("table table-bordered table-striped")->getRecordSheet($keyValue);
                echo '</div><!-- table-responsive -->'.PHP_EOL;
                echo '</div><!-- col -->'.PHP_EOL;

                
                foreach($db->getReferencialTables($table) as $rt) {
                  echo '<div class="col">'.PHP_EOL;
                  echo '<h4>Related Data</h4>'.PHP_EOL;
                  echo $rt->setTagClass("table table-bordered table-striped")->getReferrerRecordSheets($keyValue, $table->getName(), $rt);
                  echo '</div><!-- col -->'.PHP_EOL;
                }
                echo '</div><!-- row -->'.PHP_EOL;
                break;
            case "edit":
                echo '<h3 class="mb-3">Edit record from '.$table->getName().'</h3>' . PHP_EOL;
                $keyValue = $_GET[ $table->getPrimaryKey() ];
                $formValues = $table->getRecordByPrimaryKey( $keyValue );
                $table->renderForm( $formValues );
                break;
            case "insert":
                    $table->insert();
                break;
            case "update":
                    $table->update();
                break;
            case "delete":
                    $table->delete();
                break;
            default:
            ?>
            <h3 class="mb-3">Records for <?=$table->getName()?></h3>
            <div class="my-3"><a class="btn btn-primary" href="?table=<?=$table->getName()?>&action=new">Add New</a></div>
            <div class="table-responsive">
            <?php $table->setTagClass("table table-striped table-bordered table-responsive")->renderRecords(); ?> 
            </div>
            <?php
                break;
        }
        ?>        
    


<?php
} else {
  ?>
  <h3 class="mb-3">Available Tables</h3>
<div class="row">
  <?php
  foreach($db->getTables() as $table)
   echo '<div class="col">' . $table->setTagClass("table table-striped table-bordered table-responsive")->showInfo() . '</div>' . PHP_EOL;
  ?>
</div>
  <?php
}
?>
</div>
</main>
<!-- / MAIN SECTION -->
</body>
</html>

