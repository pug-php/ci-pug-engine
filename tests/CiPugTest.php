<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

include_once __DIR__.'/Controller.php';

class CiPugTest extends TestCase
{
    public function testView()
    {
        $controller = new Controller();
        $html = $controller->view('test', true);

        $this->assertSame('<p>Hello world!</p>', $html);

        ob_start();
        $self = $controller->view([
            'bar' => 'biz',
        ]);
        $html = ob_get_contents();
        ob_end_clean();

        $this->assertSame($self, $controller);
        $this->assertSame('<div><span></span></div>', $html);
    }

    public function testRenderView()
    {
        $controller = new Controller();
        $html = $controller->renderView('test');

        $this->assertSame('<p>Hello world!</p>', $html);
    }

    public function testDisplayView()
    {
        $controller = new Controller();

        ob_start();
        $self = $controller->displayView('test');
        $html = ob_get_contents();
        ob_end_clean();

        $this->assertSame($self, $controller);
        $this->assertSame('<p>Hello world!</p>', $html);
    }

    public function testSetViewPath()
    {
        $controller = new Controller();
        $controller->settings([
            'view_path' => __DIR__.'/sub-dir/',
        ]);
        $html = $controller->view('test', true);

        $this->assertSame('<p>Subdirectory</p>', $html);
    }

    public function testSetCache()
    {
        $directory = sys_get_temp_dir().'/foo';
        $controller = new Controller();
        $controller->settings([
            'cache' => $directory,
        ]);
        $controller->view('test', true);
        $count = 0;
        foreach (scandir($directory) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (substr($file, -4) === '.php') {
                $count++;
            }
            unlink("$directory/$file");
        }
        rmdir($directory);

        $this->assertSame(1, $count);
    }

    public function testUseDefaultCache()
    {
        $directory = __DIR__.'/cache/jade';
        $controller = new Controller();
        $controller->settings([
            'cache' => true,
        ]);
        $controller->view('test', true);
        $count = 0;
        foreach (scandir($directory) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (substr($file, -4) === '.php') {
                $count++;
            }
            unlink("$directory/$file");
        }
        rmdir($directory);
        rmdir(__DIR__.'/cache');

        $this->assertSame(1, $count);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionCode 1
     * @expectedExceptionMessage Cache folder does not exists and cannot be created.
     */
    public function testSettings()
    {
        $readOnlyDirectory = null;
        foreach (['H:\\', 'G:\\', 'F:\\', 'E:\\', 'D:\\', 'C:\\', '/'] as $directory) {
            if (file_exists($directory) && !is_writable($directory)) {
                $readOnlyDirectory = $directory;
                break;
            }
        }
        $writable = true;
        if ($readOnlyDirectory) {
            $directory = "${readOnlyDirectory}foo";
            $writable = @mkdir($directory) && rmdir($directory);
        }
        if ($writable) {
            $this->markTestSkipped('No read-only directory found to do the cache test');
        }
        $controller = new Controller();
        $controller->settings([
            'cache' => $directory,
        ]);
        $controller->view('test', true);
        foreach (scandir($directory) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            unlink("$directory/$file");
        }
        rmdir($directory);
    }

    public function testLegacyJadeFileExtension()
    {
        $controller = new Controller();
        $html = $controller->view('foo/bar', true);

        $this->assertSame('<div><span></span></div>', $html);
        $this->assertTrue($controller->isJadeFileAllowed());

        $controller->disallowJadeFile();
        $output = '';

        try {
            $html = $controller->view('foo/bar', true);
            // Pug-php 2 compatibility
            if (preg_match('/foo[\\\\\\/]bar[\\\\\\/]index\.pug$/', $html)) {
                $output = 'foo/bar/index.pug not found';
            }
        } catch (\Phug\CompilerException $exp) {
            $output = $exp->getMessage();
        }

        $this->assertRegExp('/foo[\\\\\\/]bar[\\\\\\/]index\.pug not found/', $output);
        $this->assertFalse($controller->isJadeFileAllowed());

        $controller->allowJadeFile();
        $html = $controller->view('foo/bar', true);

        $this->assertSame('<div><span></span></div>', $html);
        $this->assertTrue($controller->isJadeFileAllowed());

        $controller->disallowJadeFile();
    }
}
