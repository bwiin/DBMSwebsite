<?php 
	session_start(); 
	include_once 'dbConnect.php';
    include_once 'functions.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />	
	<link rel="stylesheet" type="text/css" href="css/main.css" />
</head>

<body>

	<div id="wrapper">
		
		<div id="header">
			Create RSO
		</div><!-- #header -->
		
		<!-- Join RSO Popup Window -->
    <form id="article" action="verifyRSO.php" method="get">

        <div class="container">

            <?php myUniversitySelector($mysqli); ?>

            <label><b>Name</b></label><br />
            <input type="text" placeholder="Enter RSO Name" name="rsoName" required><br />

            <label><b>Description</b></label><br />
            <input type="text" placeholder="Enter Description" name="rsoDescr" required><br />

            <div>
                <table id="rsotbl" width="100%">
                    <tr>
                        <td style="text-align: left;" width="70px">Email 1</td>
                        <td><input  type="text" placeholder="Admin Student" name="student[]" value="<?php echo $_SESSION['email']?>" required<br/></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" width="70px">Email 2</td>
                        <td><input type="text" placeholder="Student 2" name="student[]" required<br/></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" width="70px">Email 3</td>
                        <td><input type="text" placeholder="Student 3" name="student[]" required<br/></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" width="70px">Email 4</td>
                        <td><input type="text" placeholder="Student 4" name="student[]" required<br/></td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" width="70px">Email 5</td>
                        <td><input type="text" placeholder="Student 5" name="student[]" required<br/></td>
                    </tr>
                </table>
            </div>

            <button type="submit" style="width:75%;">Request New RSO</button>
        </div>
    </form>
    </div>
</div>
		
		<div id="footer">
			<a href="redirect.php">Home</a> | COP4710 &#9733 Group 17 &#9733 Term Project
		</div><!-- #footer -->
		
	</div><!-- #wrapper -->
	
</body>

</html>