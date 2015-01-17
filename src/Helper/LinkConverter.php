<?php

  namespace Xparse\ElementFinder\Helper;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class LinkConverter {

    /**
     * @var \Xparse\ElementFinder\ElementFinder
     */
    protected $page = null;

    /**
     * @var string
     */
    protected $urlSchema = '';

    /**
     * @var string
     */
    protected $urlHost = '';

    /**
     * @var string
     */
    protected $urlPath = '';

    /**
     * @var null|string
     */
    protected $urlQuery = null;

    /**
     * @var null|string
     */
    protected $urlFragment = null;

    /**
     * @param \Xparse\ElementFinder\ElementFinder $page
     * @param $url
     */
    public function __construct(\Xparse\ElementFinder\ElementFinder $page, $url) {

      if (!is_string($url)) {
        throw new \InvalidArgumentException('Invalid $currentUrl. Expect string ' . gettype($url) . ' given');
      }

      $urlInfo = parse_url($url);
      if (empty($urlInfo['scheme']) or empty($urlInfo['host'])) {
        throw new \InvalidArgumentException("Invalid url. Can`t fetch scheme or domain in url:" . $url);
      }

      $this->page = $page;

      $this->urlSchema = $urlInfo['scheme'] . '://';

      $user = isset($urlInfo['user']) ? $urlInfo['user'] : '';
      $pass = isset($urlInfo['pass']) ? $urlInfo['pass'] : '';

      if (!empty($user) or !empty($pass)) {
        $authString = $user . ':' . $pass . '@';
      } else {
        $authString = '';
      }

      $host = $this->urlSchema . $authString . $urlInfo['host'];

      if (isset($urlInfo['port'])) {
        $host .= ':' . $urlInfo['port'];
      }

      $this->urlHost = $host;
      $this->urlPath = isset($urlInfo['path']) ? $urlInfo['path'] : '/';
      $this->urlQuery = isset($urlInfo['query']) ? ('?' . $urlInfo['query']) : null;
      $this->urlFragment = isset($urlInfo['fragment']) ? ('#' . $urlInfo['fragment']) : null;

    }

    /**
     *
     */
    public function convert() {

      # order matters
      $this->convertScheme();
      $this->convertEmptyUrl();

      $this->convertTopUrls();

      $this->convertCurrentLevelUrl();
      $this->convertUrlsWithQuery();
      $this->convertUrlsWithFragment();

      $this->convertOtherUrls();

//      $link = parse_url($currentUrl);
//      $link['path'] = !empty($link['path']) ? $link['path'] : '/';
//      $realDomain = $link['scheme'] . '://' . rtrim($link['host'], '/') . '/';
//
//      $linkWithoutParams = $realDomain . trim($link['path'], '/');
//      $linkPath = $this->urlHost . $this->urlPath;
//
//      foreach ($srcElements as $element) {
//     
//       if ((!preg_match('!^[a-z]+://!', $oldPath))) {
//          # url without scheme
//          if (empty($getBaseUrl)) {
//            $newPath = $linkPath . '/' . $oldPath;
//          } else {
//            $newPath = $getBaseUrl . $oldPath;
//          }
//        } else {
//          $newPath = $oldPath;
//        }
//        $element->setAttribute($attrName, $newPath);
//      }

    }

    /**
     * Convert urls which start from double slash
     * FROM : <a href="//funivan.com/">
     * TO   : <a href="http://funivan.com/">
     *
     * @return $this
     */
    protected function convertScheme() {

      $xpath = '//*[starts-with(@src,"//")] | //*[starts-with(@href,"//")] | //form[starts-with(@action, "//")]';

      $this->processElementAttribute($xpath, function ($value) {
        $value = $this->urlSchema . preg_replace('!^//!', '', $value);
        return $value;
      });

      return $this;
    }

    /**
     * Set current link to empty urls urls
     * FROM : <a href="">
     * TO   : <a href="http://funivan.com/?post-id=1#test-url">
     *
     * @return $this
     */
    protected function convertEmptyUrl() {

      $xpath = '//*[@src=""] | //*[@href=""] | //form[@action=""]';

      $this->processElementAttribute($xpath, function () {
        $value = $this->urlHost . $this->urlPath . $this->urlQuery . $this->urlFragment;
        return $value;
      });

      return $this;
    }

    /**
     * Set current link to empty urls urls
     * FROM : <a href="./test.html">
     * TO   : <a href="http://funivan.com/section/test.html">
     *
     * @return $this
     */
    protected function convertCurrentLevelUrl() {
      $xpath = '//*[starts-with(@src,"./")] | //*[starts-with(@href,"./")] | //form[starts-with(@action, "./")]';

      $currentUrlPath = $this->getEffectedUrlSection();

      $this->processElementAttribute($xpath, function ($value) use ($currentUrlPath) {
        $value = $currentUrlPath . substr($value, 2);
        return $value;
      });

      return $this;
    }

    /**
     * Set current link to empty urls urls
     * FROM : <a href="./test.html">
     * TO   : <a href="http://funivan.com/section/test.html">
     *
     * @return $this
     */
    protected function convertTopUrls() {
      $xpath = '//*[starts-with(@src,"/")] | //*[starts-with(@href, "/")] | //form[starts-with(@action, "/")]';

      $this->processElementAttribute($xpath, function ($value) {
        $value = $this->urlHost . '/' . ltrim($value, '/');
        return $value;
      });

      return $this;
    }

    /**
     * Links with query params
     * FROM : <a href="?user=123">
     * TO   : <a href="http://funivan.com/section/?user=123">
     *
     * @return $this
     */
    protected function convertUrlsWithQuery() {

      $xpath = '//*[starts-with(@src,"?")] | //*[starts-with(@href, "?")] | //form[starts-with(@action, "?")]';

      $this->processElementAttribute($xpath, function ($value) {
        $value = $this->urlHost . $this->urlPath . $value;
        return $value;
      });

      return $this;
    }

    /**
     * Links with query params
     * FROM : <a href="#df">
     * TO   : <a href="http://funivan.com/section/?user=123#df">
     *
     * @return $this
     */
    protected function convertUrlsWithFragment() {

      $xpath = '//*[starts-with(@src,"#")] | //*[starts-with(@href, "#")] | //form[starts-with(@action, "#")]';

      $this->processElementAttribute($xpath, function ($value) {
        $value = $value = $this->urlHost . $this->urlPath . $this->urlQuery . $value;
        return $value;
      });

      return $this;
    }

    /**
     * Links with query params
     * FROM : <a href="#df">
     * TO   : <a href="http://funivan.com/section/?user=123#df">
     *
     * @return $this
     */
    protected function convertOtherUrls() {

      $xpath = '//*[@src] | //*[@href] | //form[@action]';

      $currentUrlPath = $this->getEffectedUrlSection();

      $this->processElementAttribute($xpath, function ($value) use ($currentUrlPath) {
        if (preg_match('!^[a-z]+://!', $value)) {
          return $value;
        }

        $value = $currentUrlPath . $value;
        return $value;
      });

      return $this;
    }

    /**
     * @param string $xpath
     * @param callable $function
     * @throws \Exception
     */
    protected function processElementAttribute($xpath, callable $function) {

      $srcElements = $this->page->elements($xpath);

      foreach ($srcElements as $element) {
        if ($element->hasAttribute('src') == true) {
          $attrName = 'src';
        } elseif ($element->hasAttribute('href') == true) {
          $attrName = 'href';
        } elseif ($element->hasAttribute('action') == true and $element->tagName == 'form') {
          $attrName = 'action';
        } else {
          continue;
        }
        $oldAttributeValue = $element->getAttribute($attrName);

        $newAttributeValue = $function($oldAttributeValue);

        if (!is_string($newAttributeValue)) {
          throw new \Exception('Invalid result from callback function. Expect string ' . gettype($newAttributeValue) . ' given');
        }

        if ($newAttributeValue != $oldAttributeValue) {
          $element->setAttribute($attrName, $newAttributeValue);
        }
      }
    }

    /**
     * @return string
     */
    protected function getEffectedUrlSection() {
      $path = $this->urlPath;
      if (substr($path, -1) !== '/') {
        $path = $path . '/';
      } else {
        $path = preg_replace('!/[^/]+$!', '/', $path);
      }
      $currentUrlPath = $this->urlHost . $path;
      return $currentUrlPath;
    }

  }