TooBasic
========

A very basic Controller and Template class to kickstart a simple PHP application. It is action-driven; meaning you define methods like **getIndex()**, **getPage($name)** and **postForm()**. These methods get called when users navigate to /index, /page/something or POST to /form respectively.

Usage
=====

Clone this repository (or use submodules) in the directory where you want to store your own code. Then create `index.php` like this:

```php
<?php

spl_autoload_register(function($class){
	if (0 === strpos($class, 'TooBasic\\'))
		require(__DIR__ .'/'. str_replace('\\', '/', $class) .'.php');
});

class My_First_Site extends TooBasic\Controller
{
}

My_First_Site::dispatch($_SERVER['QUERY_STRING']);
````

Additionally, TooBasic\Template expects a *tpl* directory with at least a **_wrapper.php** file.

The above `dispatch()` call works for URLs like `http://example.com/MyFirstSite/?page/contact`. If you want nice URLs you should send all requests to index.php. For Apache; create a .htaccess file with these contents:

```
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^.*$ /index.php
```

For nginx you'll probably need root-rights to add something like this to your server-configuration file:

````
location /
{
	include         fastcgi_params;
	fastcgi_param   SCRIPT_FILENAME $document_root/index.php;
	fastcgi_pass    unix:/var/run/php-fpm/php-fpm.sock;
}
````

Examples
========

A more complete usage example which demonstrates most features:

```php
<?php

spl_autoload_register(function($class){
	if (0 === strpos($class, 'TooBasic\\'))
		require(__DIR__ .'/'. str_replace('\\', '/', $class) .'.php');
});

class My_First_Site extends TooBasic\Controller
{
	protected $_db;
	protected $_tpl;

	// This method gets called before the actual action-method
	protected function _construct(string $method, string $action, array $params)
	{
		$this->_db = new Pdo(/* missing parameters */);
		$this->_tpl = new TooBasic\Template;

		// Define some variables which are available in all templates
		$this->_tpl->title = 'My first site';
		$this->_tpl->time = date('r');
	}

	public function getPage($name)
	{
		$query = $this->_db->prepare("SELECT * FROM Page WHERE !deleted AND name = ?");

		if (!$query->execute(['name' => $name]))
			throw new Exception("Could not find page in database");

		// Make $this->page available in the template
		$this->_tpl->page = $query->fetch();

		// Fetch the 'page' template; it's contents are then wrapped
		// in the header and footer contained in tpl/_wrapper.php
		print $this->_tpl->get('page')->getWrapped();
	}

	// 'index' is the default action
	public function getIndex()
	{
		return $this->getPage('index');
	}

	// This gets called when a user POSTs to /form
	public function postForm()
	{
		$this->_tpl->user = htmlspecialchars($_POST['user']);

		return $this->getPage('thanks');
	}

	// You can also define a generic method which gets called when the specific action is unknown
	public function get($action)
	{
		throw new Exception('Sorry we have never heard of this "'. $action .'" you speak of');
	}

	// This action gets called when an error occurs; eg the action is unknown
	protected function _handle(Exception $e)
	{
		if (!headers_sent())
			http_response_code(500);

		// This is an alternative syntax for Template; it is equal
		// to $this->_tpl->get('error')->getWrapped() but has a
		// separate scope. Thus, it doesn't know about 'title' in
		// this example, only 'exception' is available
		TooBasic\Template::show('error', ['exception' => $e]);
	}
}

My_First_Site::dispatch();
````

As for templates; create tpl/_wrapper.php and tpl/page.php. The _wrapper.php could contain:
```html
<!DOCTYPE html>
<html>
<body>
<?=$this->content?>
</body>
</html>
```

while the page.php could contain
```html
<h1><?=$this->page['title']?></h1>
<p><?=$this->page['content']?></p>
```
