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

      $this->page = $page;

      $this->parseUrl($url);

    }

    /**
     *
     */
    public function convert() {

      $this->addScheme();

//      $link = parse_url($currentUrl);
//      $link['path'] = !empty($link['path']) ? $link['path'] : '/';
//      $realDomain = $link['scheme'] . '://' . rtrim($link['host'], '/') . '/';
//
//      $linkWithoutParams = $realDomain . trim($link['path'], '/');
//      $linkPath = $this->urlHost . $this->urlPath;
//
//      $getBaseUrl = $page->attribute('//base/@href')->item(0);
//      if (!empty($getBaseUrl)) {
//        $getBaseUrl = rtrim($getBaseUrl, '/') . '/';
//      }
//      $srcElements = $page->elements('//*[@src] | //*[@href] | //form[@action]');
//      foreach ($srcElements as $element) {
//        if ($element->hasAttribute('src') == true) {
//          $attrName = 'src';
//        } elseif ($element->hasAttribute('href') == true) {
//          $attrName = 'href';
//        } elseif ($element->hasAttribute('action') == true and $element->tagName == 'form') {
//          $attrName = 'action';
//        } else {
//          continue;
//        }
//        $oldPath = $element->getAttribute($attrName);
//        # don`t change javascript in href
//        if (preg_match('!^\s*javascript\s*:\s*!', $oldPath)) {
//          continue;
//        }
//        if (empty($oldPath)) {
//          # URL is empty. So current url is used
//          $newPath = $currentUrl;
//        } else if ((strpos($oldPath, './') === 0)) {
//          # Current level
//          $newPath = $linkPath . substr($oldPath, 2);
//        } else if ($oldPath[0] == '/') {
//          # start with single slash
//          $newPath = $realDomain . ltrim($oldPath, '/');
//        } else if ($oldPath[0] == '?') {
//          # params only
//          $newPath = $linkWithoutParams . $oldPath;
//        } elseif ((!preg_match('!^[a-z]+://!', $oldPath))) {
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
    protected function addScheme() {

      $xpath = '//*[starts-with(@src,"//")] | //*[starts-with(@href,"//")] | //form[starts-with(@action, "//")]';

      $this->processElementAttribute($xpath, function ($value) {
        $value = $this->urlSchema . preg_replace('!^//!', '', $value);
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

        $element->setAttribute($attrName, $newAttributeValue);
      }
    }

    /**
     * @param $url
     */
    protected function parseUrl($url) {

      if (!is_string($url)) {
        throw new \InvalidArgumentException('Invalid $currentUrl. Expect string ' . gettype($url) . ' given');
      }

      $urlInfo = parse_url($url);
      if (empty($urlInfo['scheme']) or empty($urlInfo['host'])) {
        throw new \InvalidArgumentException("Invalid url. Can`t fetch scheme or domain in url:" . $url);
      }


      $this->urlSchema = $urlInfo['scheme'] . '://';

      $user = isset($urlInfo['user']) ? $urlInfo['user'] : '';
      $pass = isset($urlInfo['pass']) ? (':' . $urlInfo['pass']) : '';

      if (!empty($user) or !empty($pass)) {
        $authString = $user . $pass . '@';
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
     * @return string
     */
    public function getUrlSchema() {
      return $this->urlSchema;
    }

    /**
     * @return string
     */
    public function getUrlHost() {
      return $this->urlHost;
    }

    /**
     * @return string
     */
    public function getUrlPath() {
      return $this->urlPath;
    }

    /**
     * @return null|string
     */
    public function getUrlQuery() {
      return $this->urlQuery;
    }

    /**
     * @return null|string
     */
    public function getUrlFragment() {
      return $this->urlFragment;
    }

  }