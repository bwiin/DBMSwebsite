********************************************************
Installation
********************************************************
1) Download and install XAMPP 7.1.1+
2) Copy the src folder into C:/xampp/htcdocs
3) Run the XAMPP control panel and start the Apache server and MySQL server
4) Click on Admin button in the control panel to access PHPmyadmin
5) Import the database "eventsdb.sql" into PHPmyadmin using the import tab

** If you cannot connect to the database edit globalConfig.php in src to match your
** MySQL account settings. By default user: root and password "" should work.

6) Click on 'eventsdb' and Copy and Paste the contents of "dummyData.sql" into the SQL tab in PHPmyadmin.
   Run the query to insert the dummy data to populate the database (uncheck check foreign constraints and Press go)
7) Now you can run the App through localhost of your browser
   the main page of the app is: "localhost/src/login.php" in your browser