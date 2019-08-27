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
use Lasallesoftware\Blogbackend\Models\Post;
use Lasallesoftware\Blogbackend\Models\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class BlogplayController extends CommonController
{
    public function Bob(Request $request)
    {
        return response()->json([
            'message'        => "well hello from blogplayController::bob()!!!!",
        ], 200);
    }
}
