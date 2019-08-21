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

namespace Lasallesoftware\Blogbackend\Http\Controllers;

// LaSalle Software

use Lasallesoftware\Library\Common\Http\Controllers\CommonController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class TestAPIController extends CommonController
{
    public function Index(Request $request)
    {
        $token = $request->bearerToken();

        return response()->json([
            'message' => 'hello test api controller::index!',
            'token'   => $token,
            'domain'  => 'nothing!',
        ], 200);


        // let's get back to this controller... just want to suppress error messages for now.
        /*
        return response()->json([
            'error' => ['error_message' => 'Not Found'],
        ], 404);


        $token = $request->bearerToken();

*/


        /*
        // THESE THINGS COME FROM THE RECEIVED API REQUEST. FOR NOW, JUST ASSUME!
        $slug                = 'who-is-john-dean';
        $installed_domain_id = 1;


        // THE POST
        $post = $this->getThePost($slug);

        if ($post->installed_domain_id != $installed_domain_id) {

            return response()->json([
                'error' => ['error_message' => 'Unauthorized'],
            ], 401);
        }

        if (!$post->enabled) {

            return response()->json([
                'error' => ['error_message' => 'Not Found'],
            ], 404);
        }

        if ($post->publish_on > Carbon::now(null)  ) {

            return response()->json([
                'error' => ['error_message' => 'Not Found'],
            ], 404);
        }















        // 200 OK
        // 201 Created
        // 202 Accepted
        // 401 Unauthorized
        // 404 Not found
        // 418 I'm a teapot  https://httpstatuses.com/418
        return response()->json([
            'post'        => $transformedPost,
            'tags'        => $transformedTags,
            'postupdates' => $transformedPostupdates,
            'token' => $token,
            'domain' => 'nothing!',
        ], 200);

        */
    }





}
