<?php

/**
 * This file is part of the Lasalle Software blog back-end package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  (c) 2019 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\JWT\Middleware;

// LaSalle Software
use Lasallesoftware\Blogbackend\JWT\Validation\JWTValidation;

// Laravel class
use Illuminate\Http\Request;

// Third party class
use Lcobucci\JWT\Parser;

// PHP class
use Closure;

class JWTMiddleware
{
    /**
     * The JWTValidation instance.
     *
     * @var Lasallesoftware\Blogbackend\JWT\JWTValidation;
     */
    protected $jwtvalidation;

    /**
     * Create a new middleware instance.
     *
     * @param  \Lasallesoftware\Blogbackend\JWT\JWTValidation  $jwtvalidation
     * @return void
     */
    public function __construct(JWTValidation $jwtvalidation)
    {
        $this->jwtvalidation = $jwtvalidation;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Parse the incoming JWT string into an  Lcobucci\JWT object
        $jwtToken = (new Parser())->parse((string) $request->bearerToken());

        $validationResult = $this->jwtvalidation->validateJWT($jwtToken);

        if (!$validationResult['result']) {
            return response()->json([
                'message' => 'invalid token',
                'errors' => $validationResult['claim'] . ' claim is invalid',
            ], 403);
        }

        return $next($request);
    }
}
