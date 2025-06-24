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
use Lasallesoftware\Blogbackend\Models\Post;

// Laravel Framework
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// Laravel Facade
use Illuminate\Support\Facades\DB;

/**
 * Class SinglePostController
 *
 * @package Lasallesoftware\Blogbackend\Http\Controllers
 */
class SingleBlogPostController extends AllBlogPostsBaseController
{
    /**
     * Find the requested post, and provide the response.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    public function SingleBlogPost(Request $request)
    {
        // Create an UUID
        $comment = 'Lasallesoftware\Blogbackend\Http\Controllers\SingleBlogPostController->SingleBlogPost()';
        $uuid = $this->createAnUuid(2, $comment, 1);

        // Get the requested post
        $post = $this->getThePost($request->query('slug'));

        // If the requested post was not found...
        if (is_null($post)) {
            return $this->sendTheNoPostsFoundErrorResponse();
        }


        // The post was found! Now do some checks...

        // ...does the post belong to the correct domain?
        if (!$this->isPostBelongToTheCorrectDomain($request, $post)) {
            return response()->json([
                'error'  => __('lasallesoftwareblogbackend::blogbackend.error_status_code_404'),
                'reason' => __('lasallesoftwareblogbackend::blogbackend.error_reason_post_belongs_to_another_domain'),
            ], 404);
        }

       

        // ...is the post enabled?
        if (!$post->enabled) {

            // if the post is not supposed to be previewed in the front-end...
            if (!$post->preview_in_frontend) {

                return response()->json([
                    'error'  => __('lasallesoftwareblogbackend::blogbackend.error_status_code_404'),
                    'reason' => __('lasallesoftwareblogbackend::blogbackend.error_reason_post_is_not_enabled'),
                ], 404);
            }
        }


        // ...is the post supposed to be published? If the post is supposed to be previewed in the front-end, then ignore this check
        if (($post->publish_on > Carbon::now(null)) && (!$post->preview_in_frontend)) {

            return response()->json([
                'error'  => __('lasallesoftwareblogbackend::blogbackend.error_status_code_404'),
                'reason' => __('lasallesoftwareblogbackend::blogbackend.post_is_not_published'),
            ], 404);
        }



        // Checks pass. Let's gather all the data we need about this post...

        // ...figure out what featured image to use
        $featured_image = $this->getFeaturedImage($post);

        // ...put all the post info together
        $transformedPost = [
            'title'               => $post->title,
            'slug'                => $post->slug,
            'author'              => $this->getAuthorNameFromThePersonbydomain($post->personbydomain_id),
            'category'            => $this->getCategoryTitleFromTheId($post->category_id),
            'excerpt'             => $post->excerpt,
            'content'             => $post->content,
            'meta_description'    => $post->meta_description,
            'featured_image'      => $featured_image['image'],
            'featured_image_type' => $featured_image['type'],
            'featured_image_social_meta_tag' => $featured_image['social_meta_tag'],
            'publish_on'          => $post->publish_on,

            // "enabled" and "preview_in_frontend" included for the Preview in Frontend feature.
            // See Lasallesoftware\Blogfrontend\Http\Controllers\DisplaySinglePostController
            'enabled'             => $post->enabled,
            'preview_in_frontend' => $post->preview_in_frontend,
        ];

        // ...put the tag info together
        $tags = $this->getPostTags($post);

        // ...put the post updates info together
        $postUpdates = $this->getPostUpdates($post);

        // Ok, let's send the data along.
        return response()->json([
           'post'                => $transformedPost,
           'tags'                => $tags,
           'postupdates'         => $postUpdates,
           'sponsors'            => $this->get_PHP_Serverless_Project_Sponsors(),
        ], 200);

    }

    /**
     * Get the post from the slug
     *
     * @param  string  $slug
     * @return mixed
     */
    private function getThePost($slug)
    {
        return Post::with('tag', 'postupdate')
            ->where('slug', $slug)
            ->first()
        ;
    }

    /**
     * Does the post belong to the correct domain?
     *
     * @param  Illuminate\Http\Request  $request
     * @param  object                   $post
     * @return bool
     */
    private function isPostBelongToTheCorrectDomain($request, $post)
    {
        // The request is coming from which installed domain (returns the "title" field of the installed_domains db table)?
        $installedDomainTitle = $this->getInstalledDomainFromTheRequest($request);

        // The post belongs to which installed domain?
        $installed_domain_id = $this->getInstalledDomainIdFromTheTitleField($installedDomainTitle);

        // Compare!
        return $post->installed_domain_id == $installed_domain_id ? true : false;
    }

    /**
     * Get the category's title from the category's ID
     *
     * @param  int  $categoryId
     * @return string
     */
    private function getCategoryTitleFromTheId($categoryId)
    {
        return DB::table('categories')
            ->where('id', $categoryId)
            ->pluck('title')
            ->first()
        ;
    }

    /**
     * @param  object  $post
     * @return array
     */
    private function getPostUpdates($post)
    {
        $postupdates = Post::find($post->id)->postupdate;

        $transformedPostupdates = [];

        if (!is_null($postupdates)) {

            foreach ($postupdates as $postupdate) {
                if (
                    ($postupdate->installed_domain_id == $post->installed_domain_id) &&
                    ($postupdate->enabled)                                           &&
                    ($postupdate->publish_on <= Carbon::today())
                ) {

                    $transformedPostupdates[] = [
                        'title'      => $postupdate->title,
                        'excerpt'    => $postupdate->excerpt,
                        'content'    => $postupdate->content,
                        'publish_on' => $postupdate->publish_on,
                    ];
                }
            }
        }

        return $transformedPostupdates;
    }

    /**
     * Get the tags associated with a specific post
     *
     * @param  object    $post
     * @return collection|null
     */
    public function getPostTags($post)
    {
        // get the tag_id's from the pivot table
        $tagIds = DB::table('post_tag')
            ->select('tag_id')
            ->where('post_id', $post->id)
            ->get()
        ;

        if (count($tagIds) == 0) return null;

        // isolate the tag Ids into an array
        foreach($tagIds as $tagId) {
            $tagIdsArray[] = $tagId->tag_id;
        }

        return DB::table('tags')
            ->select('title')
            ->whereIn('id', $tagIdsArray)
            ->where('enabled', 1)
            ->where('installed_domain_id', $post->installed_domain_id)
            ->get()
        ;
    }
}
