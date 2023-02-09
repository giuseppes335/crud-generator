# Crud generator

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
http://127.0.0.1:8000/demo.php?migrate
</pre>

Run sql query
<pre>
INSERT INTO templates (name, path) VALUES ('Template 1', 'private/templates/template1');
</pre>

Edit private/templates/template1/application.php as follows
<pre>
$this->mysqli = new mysqli(
    '127.0.0.1', 
    'root', 
    '',
    'demos'
);
</pre>

Open browser and run Preview
<pre>
http://127.0.0.1:8000
</pre>
