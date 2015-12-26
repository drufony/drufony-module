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
          new ExpressionFunction('array_merge', function ($arg1, $arg2) {
              return sprintf('array_merge(%s, %s)', $arg1, $arg2);
          }, function (array $variables, $arg1, $arg2) {
              return array_merge($arg1, $arg2);
          }),
          new ExpressionFunction('variable_get', function ($arg1, $arg2) {
              return sprintf('variable_get(%s, %s)', $arg1, $arg2);
          }, function (array $variables, $arg1, $arg2) {
              return variable_get($arg1, $arg2);
          }),
        );
    }
}
