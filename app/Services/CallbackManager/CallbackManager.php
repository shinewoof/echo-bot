<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018/10/1
 * Time: 下午 10:37
 */

namespace App\Services\CallbackManager;

use Closure;
use InvalidArgumentException;
use App\Services\CallbackManager\Contracts\CallbackContract;
use Illuminate\Foundation\Application;

class CallbackManager
{
    /**
     * @var array
     */
    protected $resolvers = [];
    /**
     * @var array
     */
    protected $callbackClasses = [];
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     * @param string|Closure $resolver
     * @return $this
     */
    public function addCallback($name, $resolver)
    {
        $this->resolvers[$name] = $resolver;
        return $this;
    }

    /**
     * @param string $name
     * @return CallbackContract
     */
    public function getCallback($name = '')
    {
        $name = $name ?: $this->getDefaultName();

        if (! isset($this->callbackClasses[$name])) {
            return $this->resolve($name);
        }
        return $this->callbackClasses[$name];
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return 'BaseCallback';
    }

    protected function resolve($name)
    {
        if (! isset($this->resolvers[$name])) {
            throw new InvalidArgumentException('Invalid Argument');
        }
        if ($this->resolvers[$name] instanceof Closure) {
            $this->callbackClasses[$name] = $this->resolvers[$name]($this->app);
        } else {
            $this->callbackClasses[$name] = $this->app->make($this->resolvers[$name]);
        }

        return $this->callbackClasses[$name];
    }
}