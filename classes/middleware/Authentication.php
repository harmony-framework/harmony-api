<?php

use Slim\Http\Request;

class Authentication
{
    private $options = [
        'secure' => true,
        'relaxed' => ['localhost', '127.0.0.1'],
        'path' => null,
        'passthrough' => null,
        'authenticator' => null,
        'error' => null,
        'header' => 'Authorization',
        'regex' => '/Bearer\s+(.*)$/i',
        'parameter' => 'authorization',
        'cookie' => 'authorization',
        'argument' => 'authorization'
    ];

    public function __construct(array $options = [])
    {
        /** Rewrite options */
        $this->fill($options);
    }

    private function fill($options = array())
    {
        foreach ($options as $key => $value) {
            $method = "set" . ucfirst($key);
            if (method_exists($this, $method)) {
                call_user_func(array($this, $method), $value);
            }
        }
    }

    /**
     * middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $scheme = $request->getUri()->getScheme();
        $host = $request->getUri()->getHost();
        /** If rules say we should not authenticate call next and return. */
        if (false === $this->shouldAuthenticate($request)) {
            return $next($request, $response);
        }
        if (empty($this->options['authenticator']))
            throw new \RuntimeException('authenticator option has not been set or it is not callable.');
        
        try {
            if ($this->options['authenticator']($request, $this) === false) {
                return $this->error($request, $response);
            }
            return $next($request, $response);
        } catch (Exception $e) {
            return $this->error($request, $response);
        }
        
        
        return $response;
    }

    public function error($request, $response)
    {
        $res['message'] = 'Invalid authentication token';
        return $response->withJson($res, 401, JSON_PRETTY_PRINT);
    }

    public function shouldAuthenticate(Request $request)
    {
        $uri = $request->getUri()->getPath();
        $uri = '/' . trim($uri, '/');
        /** If request path is matches passthrough should not authenticate. */
        foreach ((array)$this->options["passthrough"] as $passthrough) {
            $passthrough = rtrim($passthrough, "/");
            if (preg_match("@^{$passthrough}(/.*)?$@", $uri)) {
                return false;
            }
        }
        /** Otherwise check if path matches and we should authenticate. */
        foreach ((array)$this->options["path"] as $path) {
            
            $path = rtrim($path, "/");
            if (preg_match("@^{$path}(/.*)?$@", $uri)) {
                return true;
            }
        }
        return false;
    }


    /** Getters & Setters **/
    public function setPath($path)
    {
        $this->options['path'] = (array) $path;
        return $this;
    }
    public function getPath()
    {
        return $this->options['path'];
    }
    public function setPassthrough($passthrough)
    {
        $this->options['passthrough'] = (array) $passthrough;
        return $this;
    }
    public function getPassthrough()
    {
        return $this->options['passthrough'];
    }
    public function setAuthenticator(Callable $authenticator)
    {
        $this->options['authenticator'] = $authenticator;
        return $this;
    }
    public function getAuthenticator()
    {
        return $this->options['authenticator'];
    }
}