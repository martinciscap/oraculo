<?php 
set_time_limit(0);
require 'oracle.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Martin Cisneros Capistran">

    <title>Oráculo</title>

    <!-- Le styles -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">    
    <link href="assets/css/bootstrap-responsive.min.css" rel="stylesheet">
    <style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}
		.sidebar-nav {
			padding: 9px 0;
		}
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->    
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
   		<div class="navbar-inner">
        	<div class="container-fluid">
        		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
		            <span class="icon-bar"></span>
          		</a>
          		<a class="brand" href="#">Oráculo</a>    
          		<div class="nav-collapse collapse">            
		            <ul class="nav">					
		            	<li><a href="index.php?query=true">Query</a></li>              	              			  	         
		            	<li><a href="index.php?tablesList=true">Check Tables List</a></li>
	              	</ul>
				</div><!--/.nav-collapse -->      
			</div>
		</div>
    </div>
    <div class="container-fluid">	
    	<!--<div class="row-fluid">
      		<div class="alert alert-success">		  
		  		<strong><i class="icon-ok"></i> Consulta ejecutada correctamente</strong>
			</div>
		</div>-->
		<div class="row-fluid">
	      	<?php
	      	if (isset($_GET['query']) && $_GET['query'] == true) {
	      		?>
	      		<div class="span12">
	      			<form class="well" method="post" action="index.php">
	      				<fieldset>
	      					<textarea name="queryArea" style="width:100%;height:90px;"></textarea>
	      				</fieldset>
	      				<button type="submit" class="btn btn-primary">Ok</button>
	      			</form>
	      		</div>
	      		<?php
			}
	        if (isset($_POST['queryArea'])) {
	        	?>        		        		      		
	    		<div class="span12">                  		
	    			<form class="well" method="post" action="index.php">
	    				<fieldset>
	  						<textarea name="queryArea" style="width:100%;height:90px;"><?php echo $_POST['queryArea']; ?></textarea>
	  					</fieldset>
	  					<button type="submit" class="btn btn-primary">Ok</button>
	  				</form>
	      			<br>
					<h1>Query</h1> 				
					<?php					
					//$stid = oci_parse($oracle, $_POST['queryArea']);								
					//oci_execute($stid);
					//echo 'numero de rows: '.oci_fetch_all($stid, $stid);
					$stid = oci_parse($oracle, $_POST['queryArea']);								
					oci_execute($stid);
					?>
					<table class="table table-condensed table-bordered table-striped">					
						<tbody>					
							<?php 
							while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) { 
								?>
								<tr>
									<?php
									foreach ($row as $item) {
										?>
										<td><?php echo($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?></td>
										<?php
									}
									?>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>				
				<?php
			}
	      	if (!isset($_GET['table']) && isset($_GET['tablesList'])) {
		    	?>		    	
		        <div class="span12">
		        	<table class="table table-condensed table-bordered">
		        		<thead>
		        			<tr>
		        				<th>TABLES</th>		        	
		        				<th>ACTIONS</th>
		        			</tr>
		        		</thead>
		        		<tbody>
		        			<?php
							$stid = oci_parse($oracle, "select tname from tab");
							oci_execute($stid);
							while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
								foreach ($row as $item) {
									?>
									<tr>
										<td>
											<a href="index.php?table=<?php echo ($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?>"><?php echo ($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?></a>
										</td>
										<td>
											<a href="index.php?table=<?php echo ($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?>"><i class="icon-ok"></i></a> 
											<a href="index.php?table=<?php echo ($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?>"><i class="icon-book"></i></a>
										</td>
									</tr>
									<?php
								}
							}
							?>              
		        		</tbody>
		        	</table>		        	
		        </div><!--/span-->
		        <?php
				}
	        ?>        
	    </div>
	    <div class="row-fluid">
	        <div class="span12">          
	        	<?php
	        	if (isset($_GET['table'])) {
	        		?>
	        		<h1><?php echo $_GET['table']; ?> Fields</h1>
	        		<?php
					$stid = oci_parse($oracle, "SELECT column_name, data_type, data_length, nullable FROM all_tab_columns WHERE table_name = '$_GET[table]'");
					oci_execute($stid);
					?>
					<table class="table table-condensed table-bordered table-striped">
						<?php
						while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
							?>
							<tr>
								<?php
								foreach ($row as $item) {
									?>
									<td><?php echo($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?></td>
									<?php
								}
								?>
							</tr>
							<?php
							}
						?>
					</table>
					<br>				
					<h1><?php echo $_GET['table']; ?> Content</h1> 
					<h5 style="color:red;">OJO: Sólo se están desplegando los 100 primeros rows, si quieres TODA la informacion de esta tabla da <a href="index.php?table=<?php echo $_GET['table']; ?>&rownum=all">click aquí</a></h5>
					<table class="table table-condensed table-bordered table-striped">
						<thead>
							<tr>
								<?php
								$stid = oci_parse($oracle, "SELECT column_name FROM all_tab_columns WHERE table_name = '$_GET[table]'");
								oci_execute($stid);
								while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {							
									foreach ($row as $item) {
										?>
										<th><?php echo($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?></th>
										<?php
									}
								}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							if (isset($_GET['rownum']) && $_GET['rownum'] == 'all') {
								$stid = oci_parse($oracle, "SELECT * FROM $_GET[table]");
							} else {
								$stid = oci_parse($oracle, "SELECT * FROM $_GET[table] WHERE ROWNUM<=100");
							}					
							oci_execute($stid);
							while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
								?>
								<tr>
									<?php
									foreach ($row as $item) {
										?>
										<td><?php echo($item !== null ? htmlentities($item, ENT_QUOTES) : '&nbsp;'); ?></td>
										<?php
									}
									?>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<?php
					}
	        	?>
	        </div>
	    </div>	    
	</div>
</body>
</html>