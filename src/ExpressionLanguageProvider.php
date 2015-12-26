<?php

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return array(
          new ExpressionFunction('global', function ($arg) {
              return sprintf('$GLOBALS[%s]', $arg);
          }, function (array $variables, $arg) {
              return $GLOBALS[$arg];
          }),
        );
    }
}
