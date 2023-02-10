# Crud generator


Edit private/templates/template1/application.php with correct credentials
<pre>
$this->mysqli = new mysqli(
    '127.0.0.1', 
    $database_username, 
    $database_password,
    'demos'
);
</pre>

Launch local mysql database and create schema
<pre>
demos
</pre>

Launch local server
<pre>
php -S 127.0.0.1:8000
</pre>

Open browser at
<pre>
http://127.0.0.1:8000?migrate
</pre>

Run sql query
<pre>
INSERT INTO templates (name, path) VALUES ('Template 1', 'private/templates/template1');
</pre>
