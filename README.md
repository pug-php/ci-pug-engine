# ci-pug
ci-pug is a library for CodeIgniter to enable [Pug Template Engine](http://jade-lang.com/) to render
views with *.pug* or *.jade* extension.

- all files in the view folder ending with .pug or .jade can be rendered from the
controllers but you can still use any other files as views. So you're free
to use Pug for all your views or just some of them.
- use the ```'cache' => true``` setting to render your views only once
and save rendered files the *cache* folder, then serve cached file with
no performance loss, views will be loaded as fast as the equivalent
php views. You can also use ```'cache' => '/your/cutom/path```
- ci-pug wait for you to load it. Only controllers with ```use CiPug;```
will load the wrapper, and until you call ```$this->settings()``` or
```$this->view()``` in the controller, the template engine will not be
loaded.

## Installation

You need PHP 5.4 or later to run ci-pug. If you use a earlier version
of PHP we recommand you to upgrade. If you cannot you can download
the [library version](https://github.com/pug-php/ci-pug) of
ci-pug (available on PHP 5.3).

Open a terminal in the **application** folder in your CodeIgniter
project, then enter:

```bash
composer require ci-pug/ci-pug
```

Be sure ```$config['composer_autoload'] = true;``` in your
**application/config/config.php** file

## How to use it?

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  use CiPug;

  public function index()
  {
    $this->view('myview');
  }
}

```

#### application/views/myview.pug
```pug
doctype html
html(lang='en')
  head
    title My Pug View
  body
    h1 Hello World!
```

## Pass variables

#### application/controllers/Main.php
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

  use CiPug;

  public function index()
  {
    $this->addVars([
      'title' => 'My Pug View',
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

  use CiPug;

  public function index()
  {
    $this->view('myview', [
      'title' => 'My Pug View',
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


#### application/views/myview.pug
```pug
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

  use CiPug;

  public function index()
  {
    $this->settings([
      'cache' => true
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

## Return the view instead of displaying it

```php
$content = $this->view('foo', true);
echo str_replace('<hr>', '----------', $content);
// works also with vars
$content = $this->view('foo', [
  'var1' => 1,
  'var2' => 2
], true);
// and also with view auto-selection
$content = $this->view(true);
// see view auto-selection section below
```

## Controller settings

This feature requires PHP 5.6 and allow you to specify settings for the whole controller.

```php
class Welcome extends CI_Controller {

  use CiPug;

  const SETTINGS = [
    'cache' => true
    // here, you can add any option
  ];

  public foo() {
    $this->view('welcome/foo'); // will use the SETTINGS class constant
  }

  public bar() {
    $this
    ->settings([
      'cache' => false// will override the SETTINGS class constant
    ])
    ->view('welcome/bar');
  }
}
```

Tip: you can create and abstract controller with ```use CiPug;``` and SETTINGS constant,
then extend this abstract class from several controllers.

## View auto-selection

If you do not specify the view file, the most logic one with the given
class and method will be taken:

```php
class Pug_Controller extends CI_Controller {
  use CiPug;
}
class Foo extends Pug_Controller {
  public function index() {
    $this->view(); // load application/views/foo/index.pug
    // or application/views/foo.pug if it does not exists
  }
  public function bar() {
    $this->view(); // load application/views/foo/bar.pug
    // or application/views/foo/bar/index.pug if it does not exists
  }
}
class Yep extends Pug_Controller {
  public function index() {
    $this->view([
      'some' => 'var'
    ]); // load application/views/yep/index.pug
    // or application/views/yep.pug if it does not exists
  }
  public function nop() {
    $content = $this->view(true); // load application/views/yep/nop.pug
    // or application/views/yep/nop/index.pug if it does not exists
  }
  public function dontKnow() {
    $this->view('yep/nop'); // load application/views/yep/nop.pug
    // or application/views/yep/nop/index.pug if it does not exists
  }
}
```

## Custom views folder

If you do no store your *.pug* files in **application/views**,
use the ```view_path``` setting:
```php
$this->settings([
  'view_path' => APPPATH . 'pug-templates'
]);
```

## Disallow `.jade` legacy file extension

If you use only up-to-date `.pug` file extension, you can disallow the `.jade` extension
to avoid unnecessary file existence checks:

```php
class Pug_Controller extends CI_Controller {
  use CiPug;

  public function __construct()
  {
    parent::__construct();
    $this->disallowJadeFile();
  }
}
```
