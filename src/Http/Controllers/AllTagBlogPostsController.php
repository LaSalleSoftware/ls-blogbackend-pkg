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
 * @copyright  (c) 2019-2025 The South LaSalle Trading Corporation
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
use Lasallesoftware\Blogbackend\Models\Tag;
use Lasallesoftware\Blogbackend\Models\Post;

// Laravel Framework
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// Laravel Facade
use Illuminate\Support\Facades\DB;


/**
 * Class AllTagBlogPostsController
 *
 * @package Lasallesoftware\Blogbackend\Http\Controllers
 */
class AllTagBlogPostsController extends AllBlogPostsBaseController
{
    /**
     * Find the requested post, and provide the response.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    public function AllTagBlogPosts(Request $request)
    {
        // Create an UUID
        $comment = 'Lasallesoftware\Blogbackend\Http\Controllers\AllTagBlogPostsController->AllTagBlogPosts()';
        $uuid = $this->createAnUuid(2, $comment, 1);

        // Get the posts
        $posts = $this->getTagBlogPosts($request);

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
            'prev_page_url' => $posts->previousPageUrl(),
            'next_page_url' => $posts->nextPageUrl(),
            'sponsors'      => $this->get_PHP_Serverless_Project_Sponsors(),
        ], 200);

    }

    /**
     * Get all the posts for the tag
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    private function getTagBlogPosts($request)
    {
        $tagId = $this->getTagIdFromTheTitleField($request);

        return Post::with('tag')
            ->where('installed_domain_id', $this->getInstalledDomainId($request))
            ->where('enabled', 1)
            ->where('publish_on', '<', Carbon::now(null) )
            ->whereHas('tag', function(Builder $query) use ($tagId) {
                $query->where('tag_id', $tagId);
            })
            ->orderBy('publish_on', 'desc')
            ->simplePaginate($this->getNumberOfItemsDisplayedOnPaginatedPage($request))
        ;
    }

    /**
     * Get the tags's ID from the TITLE.
     *
     * @param  Illuminate\Http\Request  $request
     * @return int
     */
    private function getTagIdFromTheTitleField($request)
    {
        return DB::table('tags')
            ->where('title', ucwords($request->query('slug')))
            ->pluck('id')
            ->first()
        ;
    }
}
