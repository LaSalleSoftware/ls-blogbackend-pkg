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

namespace Lasallesoftware\Blogbackend\Http\Controllers;

// LaSalle Software
use Lasallesoftware\Blogbackend\Http\Controllers\AllBlogPostsBaseController;
use Lasallesoftware\Blogbackend\Models\Post;

// Laravel Framework
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// Laravel Facade
use Illuminate\Support\Facades\DB;

/**
 * Class BlogRSSFeedController
 *
 * @package Lasallesoftware\Blogbackend\Http\Controllers
 */
class BlogRSSFeedController extends AllBlogPostsBaseController
{
    /**
     * Find the requested post, and provide the response.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    public function BlogRSSFeed(Request $request)
    {
        // Get the posts
        $posts = $this->getAllTheBlogPosts($request);

        // If a post was not found...
        if ( (is_null($posts) || (count($posts) == 0)) ) {
            return $this->sendTheNoPostsFoundErrorResponse();
        }

        // Found post(s)!
        // ...put the posts info together
        $transformedPosts = $this->getTransformedPosts($posts);

        // Ok, let's send the data along.
        return response()->json([
            'posts'         => $transformedPosts,
        ], 200);

    }

    /**
     * Get all the posts.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    private function getAllTheBlogPosts($request)
    {
        return Post::where('installed_domain_id', $this->getInstalledDomainId($request))
            ->where('enabled', 1)
            ->where('publish_on', '<', Carbon::now(null) )
            ->orderBy('publish_on', 'desc')
            ->get()
        ;
    }
}
