Required :
<ol start=1> 
	<li> PHP version 8.1 or XAMPP (for easy use in windows)</li>
	<li> Composer</li>
	<li> PostgreSql</li>
</ol>
How To :
<ol start=1> 
<li> Install PHP or XAMPP </li>
<li> Connect php with path in environment variable (if in windows / ignore this step if using ubuntu or mac)</li>
<li> Install Composer</li>
<li> Install PostgreSql and make sure the password is "root" </li>
<li> start PHP or XAMPP (only apache if using XAMPP)</li>
<li> extract project file or clone for github (git@github.com:ofaasd/simple-attendance-app.git)</li>
<li> Go inside project folder (attendance-app)</li>
<li> run composer update </li>
<li> if using clone from github rename .env.example file become .env file (ignore this step if using extract file)</li>
<li> Right click on Databases > create > Databases (create database name "attendance-db") </li>
<li> Right click on attendance-db chose restore </li>
<li> Select Filename by click folder on right field </li>
<li> Change extension on sql and search your attendance-db.sql </li>
<li> click restore and refresh the database </li>
<li> Go to laravel file then run php run serve on terminal </li>
</ol>
