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
 * @link       https://packagist.org/packages/lasallesoftware/lsv2-blogbackend-pkg
 * @link       https://github.com/LaSalleSoftware/lsv2-blogbackend-pkg
 *
 */

namespace Lasallesoftware\Blogbackend\Http\Controllers;

// LaSalle Software
use Lasallesoftware\Library\Common\Http\Controllers\CommonController;

// Laravel Framework
use Illuminate\Http\Request;


/**
 * Class AllBlogPostsBaseController
 *
 * @package Lasallesoftware\Blogbackend\Http\Controllers
 */
class AllBlogPostsBaseController extends CommonController
{
    /**
     * Transform a collection of posts (plural!) to whisk off to the front-end.
     *
     * Yes, I am fully aware of that thing called https://laravel.com/docs/6.x/eloquent-resources. Need I really have
     * to play with every toy in the toy box?
     *
     * @param  collection   $posts    Posts fetched via eloquent, such as from Posts::all().
     * @return array
     */
    public function getTransformedPosts($posts)
    {
        $transformedPosts = [];

        foreach ($posts as $post) {

            $featured_image = $this->getFeaturedImage($post);

            $transformedPost = [
                'title'               => $post->title,
                'slug'                => $post->slug,
                'author'              => $this->getAuthorNameFromThePersonbydomain($post->personbydomain_id),
                'excerpt'             => $post->excerpt,
                'featured_image'      => $featured_image['image'],
                'featured_image_type' => $featured_image['type'],
                'publish_on'          => $post->publish_on,
            ];

            $transformedPosts[] = $transformedPost;
        }

        return $transformedPosts;
    }

    /**
     * Get the number of posts to display on a paginated page.
     *
     * The "$page" in "simplePagination($page)" is the number of posts to be displayed.
     *
     * The query parameter "itemsDisplayedOnPaginatedPage" should be set by my front-end, which is set by the
     * front-end's config parameter "lasallesoftware-frontendapp.lasalle_pagination_number_of_items_displayed_per_page".
     *
     * If there is something wrong with the incoming query param, then 15 posts will be displayed.
     *
     * Which set-of-15-posts will be displayed? The URL's query parameter "page" specifies which set-of-nn will be
     * displayed. Laravel automatically grabs the $request->query('page') query parameter,
     *
     * https://laravel.com/docs/6.x/pagination
     *
     * @param  Illuminate\Http\Request  $request
     * @return int
     */
    public function getNumberOfItemsDisplayedOnPaginatedPage($request)
    {
        // if the incoming query parameter is specified, return it!
        if ($request->has('itemsDisplayedOnPaginatedPage')) {

            $itemsDisplayedOnPaginatedPage = $request->query('itemsDisplayedOnPaginatedPage');

            if (($itemsDisplayedOnPaginatedPage != 0)      &&
                ($itemsDisplayedOnPaginatedPage != '0')    &&
                ($itemsDisplayedOnPaginatedPage != 'none') &&
                ($itemsDisplayedOnPaginatedPage != 'blank'))
            {
                return intval($itemsDisplayedOnPaginatedPage);
            }
        }

        // still here? return 15!
        return 15;
    }
}
