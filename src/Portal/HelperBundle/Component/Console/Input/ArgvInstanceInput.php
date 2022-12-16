<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Portal\HelperBundle\Component\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;

class ArgvInstanceInput extends ArgvInput
{
    private $tokenss;
    
    private $instance;
    
    /**
     * Constructor.
     *
     * @param array|null           $argv       An array of parameters from the CLI (in the argv format)
     * @param InputDefinition|null $definition A InputDefinition instance
     */
    public function __construct(array $argv = null, InputDefinition $definition = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];
        }
        
        // strip the application name
        $firstElement = array_shift($argv);

        $this->tokenss = $argv;
        $this->unsetToken('--instance');
        $argv = $this->tokenss;
        array_unshift($argv, $firstElement);
        parent::__construct($argv, $definition);
    }
    
    protected function unsetToken(string $unsetToken)
    {
        $tokens = $this->tokenss;
        if (is_array($tokens)) {
            foreach ($tokens as $key => $token) {
                $strPos = strpos($token, $unsetToken);
                if ($strPos !== false) {
                    $arrInstance = explode('=', $tokens[$key]);
                    if (count($arrInstance) == 2 && $arrInstance[1] !== 'main') {
                        $this->setInstance($arrInstance[1]);
                    }
                    unset($tokens[$key]);
                }
            }
        }
        $this->tokenss = $tokens;
    }
    
    private function setInstance($instance)
    {
        $this->instance = $instance;
    }
    
    public function getInstance()
    {
        return $this->instance;
    }
}
