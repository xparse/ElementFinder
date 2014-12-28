<?php

  namespace Xparse\Dom\ElementFinder;

  /**
   * @author Ivan Shcherbak <dev@funivan.com> 12/28/14
   */
  class LinkConverter {

    /**
     * Modify elements in page
     *
     * Convert relative links to absolute
     *
     * @param \Xparse\ElementFinder\ElementFinder $page
     * @param string $currentUrl
     * @return \Xparse\ElementFinder\ElementFinder
     */
    public static function convert(\Xparse\ElementFinder\ElementFinder $page, $currentUrl) {
      $link = parse_url($currentUrl);
      $link['path'] = !empty($link['path']) ? $link['path'] : '/';
      $realDomain = $link['scheme'] . '://' . rtrim($link['host'], '/') . '/';
      $linkWithoutParams = $realDomain . trim($link['path'], '/');
      $linkPath = $realDomain . trim(preg_replace('!/([^/]+)$!', '', $link['path']), '/');
      $getBaseUrl = $page->attribute('//base/@href')->item(0);
      if (!empty($getBaseUrl)) {
        $getBaseUrl = rtrim($getBaseUrl, '/') . '/';
      }
      $srcElements = $page->elements('//*[@src] | //*[@href] | //form[@action]');
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
        $oldPath = $element->getAttribute($attrName);
        # don`t change javascript in href
        if (preg_match('!^\s*javascript\s*:\s*!', $oldPath)) {
          continue;
        }
        if (empty($oldPath)) {
          # URL is empty. So current url is used
          $newPath = $currentUrl;
        } else if ((strpos($oldPath, './') === 0)) {
          # Current level
          $newPath = $linkPath . substr($oldPath, 2);
        } else if (strpos($oldPath, '//') === 0) {
          # Current level
          $newPath = $link['scheme'] . ':' . $oldPath;
        } else if ($oldPath[0] == '/') {
          # start with single slash
          $newPath = $realDomain . ltrim($oldPath, '/');
        } else if ($oldPath[0] == '?') {
          # params only
          $newPath = $linkWithoutParams . $oldPath;
        } elseif ((!preg_match('!^[a-z]+://!', $oldPath))) {
          # url without schema
          if (empty($getBaseUrl)) {
            $newPath = $linkPath . '/' . $oldPath;
          } else {
            $newPath = $getBaseUrl . $oldPath;
          }
        } else {
          $newPath = $oldPath;
        }
        $element->setAttribute($attrName, $newPath);
      }

    }
  }