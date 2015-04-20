# ci-jade
ci-jade is a library for CodeIgniter to enable [Jade Template Engine](http://jade-lang.com/) to render
views with *.jade* extension.

- all files in the view folder ending with .jade can be rendered from the
controllers but you can still use any other files as views. So you're free
to use Jade for all your views or just some of them.
- use the ```'cache' => true``` setting to render your views only once
and save rendered files the *cache* folder, then serve cached file with
no performance loss, views will be loaded as fast as the equivalent
php views. You can also use ```'cache' => '/your/cutom/path```
- ci-jade wait for you to load it. Only controllers with ```use Jade;```
will load the wrapper, and until you call ```$this->settings()``` or
```$this->view()``` in the controller, the template engine will not be
loaded.

## Installation

You need PHP 5.4 or later to run ci-jade. If you use a earlier version
of PHP we recommand you to upgrade. If you cannot you can download
the [library version](https://github.com/kylekatarnls/ci-jade) of
ci-jade (available on PHP 5.3).

Open a terminal in the **application** folder in your CodeIgniter
project, then enter:

```bash
composer require ci-jade/ci-jade
```

Be sure ```$config['composer_autoload'] = TRUE;``` in your
**application/config/config.php** file

## How to use it?

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  use Jade;

  public function index()
  {
    $this->view('myview');
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

  use Jade;

  public function index()
  {
    $this->load->vars([
      'title' => 'My Jade View',
      'authors' => [
        'Luke',
        'Leia',
        'Lando'
      ]
    ]);
    $this->view('myview');
  }
}

```

or

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  use Jade;

  public function index()
  {
    $this->view('myview', [
      'title' => 'My Jade View',
      'authors' => [
        'Luke',
        'Leia',
        'Lando'
      ]
    ]);
  }
}
```
All variables from ```->view()``` or ```->load->vars()``` are
merged and available in the view.


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

  use Jade;

  public function index()
  {
    $this->settings([
      'cache' => TRUE
    ]);
    $this->view('myview');
  }
}

```

Or you can use a custom storage folder:
```php
$this->settings([
  'cache' => '/tmp/my-cache-folder'
]);
```

If the folder does not exists, the library will try to create it.

## Custom views folder

If you do no store your *.jade* files in **application/views**,
use the ```view_path``` setting:
```php
$this->settings([
  'view_path' => APPPATH . 'jade-templates'
]);
```
