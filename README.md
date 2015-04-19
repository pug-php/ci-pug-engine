# ci-jade
ci-jade is a library for CodeIgniter to enable [Jade Template Engine](http://jade-lang.com/) to render
views with *.jade* extension.

- ci-jade is dependencies-free, so you have just to unzip and put the **Jade.php**
file and **Jade** folder into **application/libraries**
- but if you use [composer](http://getcomposer.org) you can also
add ```"kylekatarnls/jade-php": "~1.1"``` to your **composer.json** to keep
the Jade renderer engine up to date. The composer dependency will be used
instead of the embedded engine.
- all files in the view folder ending with .jade can be rendered from the
controllers but you can still use any other files as views. So you're free
to use Jade for all your views or just some of them.
- use the ```'cache' => true``` setting to render your views only once
and save rendered files the *cache* folder, then serve cached file with
no performance loss, views will be loaded as fast as the equivalent
php views. You can also use ```'cache' => '/your/cutom/path```
- ci-jade wait for you to load it. Until you call
```$this->load->library('jade')``` in your controller, no file from
the library or the template engine will be load, so performances
remains exactly the same for your other pages.

## Easy installation

- Download [Jade library](https://github.com/kylekatarnls/ci-jade/archive/master.zip)
- Extract its content in the **application/libraries** folder in your
CodeIgniter project.

## Installation with Composer

Open a terminal in your CodeIgniter project

```bash
cd application
composer require kylekatarnls/jade-php
cd libraries
wget https://raw.githubusercontent.com/kylekatarnls/ci-jade/master/Jade.php
```

If **wget** is not available on your OS, download [Jade.php](https://raw.githubusercontent.com/kylekatarnls/ci-jade/master/Jade.php)
in your project **application/libraries** folder.

Be sure ```$config['composer_autoload'] = TRUE;``` in your
**application/config/config.php** file

## How to use it?

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade');
    $this->jade->view('myview');
  }
}

```

#### application/views/myview.jade
```jade
doctype html
html(lang='en')
  head
    title My Jade View
  body
    h1 Hello World!
```

## Pass variables

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade');
    $this->load->vars([
      'title' => 'My Jade View',
      'authors' => [
        'Luke',
        'Leia',
        'Lando'
      ]
    ]);
    $this->jade->view('myview');
  }
}

```

#### application/views/myview.jade
```jade
doctype html
html(lang='en')
  head
    title=title
  body
    ul
      each author in authors
        li=author
```

## Keep rendered views in cache

We recommend you to do it in production to serve the views faster.

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  public function index()
  {
    $this->load->library('jade', [
      'cache' => TRUE
    ]);
    $this->jade->view('myview');
  }
}

```

Or you can use a custom storage folder:
```php
$this->load->library('jade', [
  'cache' => '/tmp/my-cache-folder'
]);
```

If the folder does not exists, the library will try to create it.

## Custom views folder

If you do no store your *.jade* files in **application/views**,
use the ```view_path``` setting:
```php
$this->load->library('jade', [
  'view_path' => APPPATH . 'jade-templates'
]);
```
