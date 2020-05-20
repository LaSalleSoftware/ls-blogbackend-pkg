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
 * @copyright  (c) 2019-2020 The South LaSalle Trading Corporation
 * @license    http://opensource.org/licenses/MIT
 * @author     Bob Bloom
 * @email      bob.bloom@lasallesoftware.ca
 * @link       https://lasallesoftware.ca
 * @link       https://packagist.org/packages/lasallesoftware/ls-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/ls-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\JWT\Middleware;

// LaSalle Software
use Lasallesoftware\Blogbackend\JWT\Validation\JWTValidation;
use Lasallesoftware\Library\Authentication\Models\Json_web_token;

// Laravel class
use Illuminate\Http\Request;

// Third party class
use Lcobucci\JWT\Parser;

// PHP class
use Closure;

class JWTMiddleware
{
    /**
     * @var Lasallesoftware\Blogbackend\JWT\JWTValidation
     */
    protected $jwtvalidation;

    /**
     * @var Lasallesoftware\Library\Authentication\Models\Json_web_token
     */
    protected $jwtModel;

    /**
     * Create a new middleware instance.
     *
     * @param  \Lasallesoftware\Blogbackend\JWT\JWTValidation                  $jwtvalidation
     * @param  \Lasallesoftware\Library\Authentication\Models\Json_web_token   $jwtModel
     * @return void
     */
    public function __construct(JWTValidation $jwtvalidation, Json_web_token $jwtModel)
    {
        $this->jwtvalidation = $jwtvalidation;
        $this->jwtModel      = $jwtModel;
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

        // Validate the JWT
        $validationResult = $this->jwtvalidation->validateJWT($jwtToken);

        if (!$validationResult['result']) {
            return response()->json([
                'error'  => 'invalid token',
                'reason' => $validationResult['claim'] . ' claim is invalid',
            ], 403);
        }

        $this->jwtModel->saveWithJWT($jwtToken);

        return $next($request);
    }
}
