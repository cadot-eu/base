<?php
require_once("../vendor/autoload.php");

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use BrowserStack\Local;
use Facebook\WebDriver\WebDriverDimension;
use Zenstruck\Browser;

# Creates an instance of Local
$bs_local = new Local();

# You can also set an environment variable - "BROWSERSTACK_ACCESS_KEY".
$bs_local_args = array("key" => "zrpD1GvfgvowcSg56Utc");
# Starts the Local instance with the required arguments
$bs_local->stop();
$bs_local->start($bs_local_args);

# Check if BrowserStack local instance is running
if ($bs_local->isRunning()) echo 'BrowserStack instance is running';
$caps = array(

  [
    "os_version" => "10",
    "os" => "Windows",
    "browser" => "chrome",
    "name" => "windows_10",
    "resolution" => "1920x1080"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "ie",
    "name" => "windows_10",
    "resolution" => "1920x1080"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "firefox",
    "name" => "windows_10",
    "resolution" => "1920x1080"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "edge",
    "name" => "windows_10",
    "resolution" => "1920x1080"
  ],
  #---------------------------
  #     windows 10 1366x768
  #---------------------------
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "chrome",
    "name" => "windows_10",
    "resolution" => "1366x768"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "ie",
    "name" => "windows_10",
    "resolution" => "1366x768"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "firefox",
    "name" => "windows_10",
    "resolution" => "1366x768"
  ],
  [
    "os_version" => "10",
    "os" => "windows",
    "browser" => "edge",
    "name" => "windows_10",
    "resolution" => "1366x768"
  ],
  #---------------------------
  #     MAC
  #---------------------------
  [
    "os" => "OS X",
    "os_version" => "El Capitan",
    "name" => "Mac_10.8",
    "browser" => "chrome"
  ],
  [
    "os" => "OS X",
    "os_version" => "El Capitan",
    "browser" => "safari",
    "name" => "Mac_10.8"
  ],
  [
    "os" => "OS X",
    "os_version" => "El Capitan",
    "browser" => "firefox",
    "name" => "Mac_10.8"
  ],
  #---------------------------
  #     android
  #---------------------------
  [
    "os" => "android",
    "os_version" => "9.0",
    "browser" => "android",
    "device" => "Samsung Galaxy Tab S6",
    "name" => "samsung_galaxy_tab_s6",
    "real_mobile" => "true"
  ],
  [
    "os" => "android",
    "os_version" => "5.0",
    "browser" => "android",
    "device" => "Samsung Galaxy S6",
    "name" => "samsung_s6",
    "real_mobile" => "true"
  ],
  [
    "os" => "android",
    "os_version" => "7.0",
    "browser" => "android",
    "device" => "Samsung Galaxy S8",
    "name" => "samsung_s8",
    "real_mobile" => "true"
  ],
  [
    "os" => "android",
    "os_version" => "11.0",
    "browser" => "android",
    "device" => "Samsung Galaxy S21",
    "name" => "samsung_s21",
    "real_mobile" => "true"
  ],
  [
    "device" => "Google Pixel 5",
    "os_browser" => "11.0",
    "real_mobile" => "true",
    "name" => "android 11"
  ],
  #---------------------------
  #     iphone
  #---------------------------
  [
    "os" => "ios",
    "os_version" => "11",
    "browser" => "iphone",
    "device" => "iPhone 6",
    "name" => "iphone6",
    "real_mobile" => "true"
  ],
  [
    "device" => "iPhone 12 Pro",
    "os_browser" => "14",
    "real_mobile" => "true",
    "name" => "iPhone_12_Pro"
  ],
  [
    "os" => "ios",
    "os_version" => "11",
    "browser" => "ipad",
    "device" => "iPad 6th",
    "name" => "ipad6",
    "real_mobile" => "true"
  ]
);
$commun = array(
  "browserstack.local" => "true",
  "browserstack.video" => "false",
  "browserstack.selenium_version" => "3.14.0",
  "browserstack.seleniumLogs" => "false",
  "browser_version" => "latest",
  "build" => "browserstack-buil-script"

);
foreach ($caps as $cap) {
  $cap = array_merge($cap, $commun);
  $web_driver = RemoteWebDriver::create("https://mic19:zrpD1GvfgvowcSg56Utc@hub-cloud.browserstack.com/wd/hub", $cap);
  try {
    $web_driver->manage()->window()->maximize();
    $web_driver->get("http://127.0.0.1:49157");
    // $height = $web_driver->execute("return Math.max( document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight )");
    // echo $height;
    $body = $web_driver->FindElement(WebDriverBy::tagName('body'));
    $header = intval($web_driver->FindElement(WebDriverBy::tagName('header'))->getSize()->getHeight());
    if (!empty($body)) {
      $hauteur = intval(explode('x', $cap["resolution"])[1]);
      $nom = $cap['name'] . '-' . $cap['os'] . '-' . $cap["os_version"] . '-' . $cap['browser'];
      if (isset($cap['resolution'])) $nom .= '-' . $cap['resolution'];
      if (isset($cap['device'])) $nom .= '-' . $cap['device'];

      @mkdir('capture/' . $nom);
      //$result = imagecreate($body->getSize()->getWidth(), 1);
      //imagepng($result, 'capture/result.png');
      $Wheight = $web_driver->manage()->window()->getSize()->getHeight();
      //for ($i = 0; $i < $body->getSize()->getHeight() / $hauteur; $i++) { // 1000
      //if ($i > 0) {
      $web_driver->executeScript('window.scrollTo(0,' . $Wheight . ' );');
      //}
      $web_driver->takeScreenshot('capture/' . $nom . '/capture.png');
      $img = imagecreatefrompng('capture/' . $nom . '/capture.png');
      if ($IMG->getHeight < 1000) {
        for ($i = 0; $i < $body->getSize()->getHeight() / $hauteur; $i++) {
          $web_driver->executeScript('window.scrollTo(0,' . $i * $hauteur - $header . ' );');
          $web_driver->takeScreenshot('capture/' . $nom . '/capture' . $i . '.png');
        }
      }
      //$web_driver->takeScreenshot('capture.png');
      // $web_driver->wait(2);
      // if ($i > 0) {
      //   $img = imagecreatefrompng('capture/' . $nom . '/capture' . $i . '.png');
      //   $size = min(imagesx($img), imagesy($img));
      //   $img2 = imagecrop($img, ['x' => 0, 'y' => $header, 'width' => imagesx($img), 'height' => imagesy($img) - $header]);
      //   if ($img2 !== false) {
      //     imagepng($img2, 'capture.png');
      //     imagedestroy($img2);
      //   }
      //   imagedestroy($img);
      // }
      //imgjoin();
      //}
    }
  } catch (Exception $e) {
    echo 'Message: ' . $e->getMessage();
  }
  $web_driver->quit();
}

# Stop the Local instance
$bs_local->stop();

function imgjoin()
{
  $top_file = 'capture/result.png';
  $bottom_file = 'capture.png';
  $top = imagecreatefrompng($top_file);
  $bottom = imagecreatefrompng($bottom_file);

  // get current width/height
  list($top_width, $top_height) = getimagesize($top_file);
  list($bottom_width, $bottom_height) = getimagesize($bottom_file);

  // compute new width/height
  $new_width = ($top_width > $bottom_width) ? $top_width : $bottom_width;
  $new_height = $top_height + $bottom_height;

  // create new image and merge
  $new = imagecreate($new_width, $new_height);
  imagecopy($new, $top, 0, 0, 0, 0, $top_width, $top_height);
  imagecopy($new, $bottom, 0, $top_height + 1, 0, 0, $bottom_width, $bottom_height);

  // save to file
  imagepng($new, 'capture/result.png');
}
