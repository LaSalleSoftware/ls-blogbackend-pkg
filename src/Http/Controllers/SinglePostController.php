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

use Lasallesoftware\Blogbackend\Http\Resources\PostResource;
use Lasallesoftware\Blogbackend\Http\Resources\TagResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


/**
 * Class LoginController
 *
 * @package Lasallesoftware\Library\Authentication\Http\Controllers
 */
class SinglePostController extends CommonController
{
    public function ShowSinglePost(Request $request)
    {

        // let's get back to this controller... just want to suppress error messages for now.
        return response()->json([
            'error' => ['error_message' => 'Not Found'],
        ], 404);


        $token = $request->bearerToken();




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


        $category = DB::table('categories')->where('id', $post->category_id)->first();

        $personbydomain = DB::table('personbydomains')->where('id', $post->personbydomain_id)->first();
        $author = $personbydomain->person_first_name . " " . $personbydomain->person_surname;

        $transformedPost = [
            'title'            => $post->title,
            'slug'             => $post->slug,
            'author'           => $author,
            'category_name'    => $category->title,
            'excerpt'          => $post->excerpt,
            'content'          => $post->content,
            'meta_description' => $post->meta_description,
            'featured_image'   => $post->featured_image,
            'date'             => $post->publish_on->format('F d, Y'),
        ];



        // THE TAGS
       $tags = $this->getPostTags($post->id);

       if (is_null($tags)) {
           $transformedTags = null;
       } else {

           foreach ($tags as $tag) {
               $transformedTags[] = [
                 'title' => $tag->title,
               ];
           }
       }




       // THE POST UPDATES
        $transformedPostupdates = [];
        if (!is_null($post->postupdate)) {

            foreach ($post->postupdate as $postupdate) {
                if (
                    ($postupdate->installed_domain_id == $installed_domain_id) &&
                    ($postupdate->enabled) &&
                    ($postupdate->publish_on <= Carbon::today())
                ) {

                    $transformedPostupdates[] = [
                        'title'   => $postupdate->title,
                        'excerpt' => $postupdate->excerpt,
                        'content' => $postupdate->content,
                        'date'    => $postupdate->publish_on->format('F d, Y'),
                    ];
                }
            }
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
    }


    public function getThePost($slug)
    {
        return Post::with('tag', 'postupdate')
            ->where('slug', $slug)
            ->first()
        ;
    }

    public function getPostTags($postId)
    {
        // get the tag_id's from the pivot table
        $tagIds = DB::table('post_tag')
            ->select('tag_id')
            ->where('post_id', $postId)
            ->get()
        ;

        if (count($tagIds) == 0) return null;

        // isolate the tag Ids into an array
        foreach($tagIds as $tagId) {
            $tagIdsArray[] = $tagId->tag_id;
        }


        return DB::table('tags')
            ->select('id', 'title')
            ->whereIn('id', $tagIdsArray)
            ->where('enabled', 1)
            ->where('installed_domain_id', $this->getLookupDomainId())
            ->get()
        ;
    }

    public function getLookupDomainId()
    {
        return 1;
    }
}
