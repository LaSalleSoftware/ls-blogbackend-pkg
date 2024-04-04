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
 * @copyright  (c) 2019-2024 The South LaSalle Trading Corporation
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

// Third party classes
use Carbon\CarbonImmutable;


/**
 * Class HomepageBlogPostsController
 *
 * @package Lasallesoftware\Blogbackend\Http\Controllers
 */
class HomepageBlogPostsController extends AllBlogPostsBaseController
{
    /**
     * Find the requested post, and provide the response.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    public function HomepageBlogPosts(Request $request)
    {
        // Create an UUID
        $comment = 'Lasallesoftware\Blogbackend\Http\Controllers\HomepageBlogPostsController->HomepageBlogPosts()';
        $uuid = $this->createAnUuid(2, $comment, 1);

        // Get the posts
        $posts = $this->getRecentBlogPosts($request);

        // If a post was not found...
        if ( (is_null($posts) || (count($posts) == 0)) ) {
            return $this->sendTheNoPostsFoundErrorResponse();
        }

        // Found post(s)!
        // ...put the posts info together
        $transformedPosts = $this->getTransformedPosts($posts);


        // Podcast episodes
        // process when the class exists in the back-end, and the front-end sent podcast_show ID's
        if ( (class_exists('Lasallesoftware\Podcastbackend\Http\Controllers\PodcastBaseController')) &&
             (! is_null($request->input('podcast_shows'))) )
        {
            $podcastShowIDs        = $request->input('podcast_shows');                // array
            $numberPodcastEpisodes = $request->input('number_of_podcast_episodes');   // array

            $transformedPodcastEpisodes = $this->getAllTransformedPodcastEpisodesForAllPodcastShows($podcastShowIDs, $numberPodcastEpisodes);
        } else {
            $transformedPodcastEpisodes = null;
        }        


        // Video episodes
        // process when the class exists in the back-end, and the front-end sent video_show ID's
        if ( (class_exists('Lasallesoftware\Videobackend\Http\Controllers\VideoBaseController')) &&
             (! is_null($request->input('video_shows'))) ) 
        {
            $videoShowIDs        = $request->input('video_shows');                // array
            $numberVideoEpisodes = $request->input('number_of_video_episodes');   // array

            $transformedVideoEpisodes = $this->getAllTransformedVideoEpisodesForAllVideoShows($videoShowIDs, $numberVideoEpisodes);

        } else {
            $transformedVideoEpisodes = null;
        }
        

        // Ok, let's send the data along.
        return response()->json([
            'posts'            => $transformedPosts,
            'podcast_episodes' => $transformedPodcastEpisodes,
            'video_episodes'   => $transformedVideoEpisodes,
        ], 200);

    }



    // **************************************************************************************************************
    // START: BLOG POSTS
    // **************************************************************************************************************

    /**
     * Get recent posts.
     *
     * @param  Illuminate\Http\Request  $request
     * @return mixed
     */
    private function getRecentBlogPosts($request)
    {
        return Post::where('installed_domain_id', $this->getInstalledDomainId($request))
            ->where('enabled', 1)
            ->where('publish_on', '<', Carbon::now(null) )
            ->orderBy('publish_on', 'desc')
            ->take($this->getNumberOfBlogPostsToDisplayOnTheHomePageFromTheHeader($request))
            ->get()
        ;
    }

    /**
     * Get the number of blog posts to display on the home page, as specified in the request's header.
     *
     * @param  Illuminate\Http\Request  $request
     * @return string                             Such as "hackintosh.ls-basicfrontend-app.com" (omit quotes).
     */
    private function getNumberOfBlogPostsToDisplayOnTheHomePageFromTheHeader($request)
    {
        $numberPosts = $request->header('NumberOfBlogPostsToDisplayOnTheHomePage');

        if ((is_null($numberPosts)) || (!$numberPosts) || ($numberPosts == '')) return 0;

        return $numberPosts;
    }
    // **************************************************************************************************************
    // END: BLOG POSTS
    // **************************************************************************************************************

    

    // **************************************************************************************************************
    // START: PODCAST EPISODES
    // **************************************************************************************************************

    /**
     * Get all the podcast episodes for all the podcast shows
     *
     * @param  array  $podcastShowIDs                A simple array of the podcast show ID's
     * @param  int    $numberPodcastEpisodes         The number of podcast episodes to select
     * @return void
     */
    private function getAllTransformedPodcastEpisodesForAllPodcastShows($podcastShowIDs, $numberPodcastEpisodes)
    {
        $transformedPodcastEpisodes = [];
        
        foreach ($podcastShowIDs as $podcastShowID) {
            $episodes = $this->getRecentPodcastEpisodes($podcastShowID, $numberPodcastEpisodes);
            $transformedPodcastEpisodes[] = $this->transformAllPodcastEpisodes($episodes);
        }

        return $transformedPodcastEpisodes;
    }

    /**
     * Get recent podcast episodes
     *
     * @param  int   $podcastShowID                 ID of podcast_episodes table
     * @param  int   $numberPodcastEpisodes         The number of podcast episodes to select
     * @return collection | null
     */
    private function getRecentPodcastEpisodes($podcastShowID, $numberPodcastEpisodes)
    {
        return DB::table('podcast_episodes')
            ->where([
                ['podcast_show_id', '=', $podcastShowID],
                ['website_enabled', '=', true],
                ['website_publish_on', '<', CarbonImmutable::now(config('app.timezone'))],
            ])
            ->orderBy('website_publish_on', 'desc')
            ->take($numberPodcastEpisodes)
            ->get()
        ;
    }

    /**
     * Transform episodes
     * 
     * @param   collection  $podcastEpisodes      Collection of podcast episodes (retrieved from the database)
     * @return  array
     */
    private function transformAllPodcastEpisodes($podcastEpisodes)
    {
        $transformedPodcastEpisodes = [];

        foreach ($podcastEpisodes as $podcastEpisode) {
            $transformedPodcastEpisodes[] = $this->transformPodcastEpisode($podcastEpisode);
        }

        return $transformedPodcastEpisodes;
    }

    /**
     * Transform an individual podcast episode, specifically for the home page
     *
     * @param  object  $podcastEpisode
     * @return array
     */
    private function transformPodcastEpisode($podcastEpisode)
    {
        return [
            'podcast_show_id'         => $podcastEpisode->podcast_show_id,
            'title'                   => $podcastEpisode->title,
            'website_excerpt'         => $podcastEpisode->website_excerpt,
            'website_featured_image'  => $podcastEpisode->website_featured_image,
            'itunes_link'             => $podcastEpisode->itunes_link,
            'itunes_enclosure_url'    => $podcastEpisode->itunes_enclosure_url,
            'website_publish_on'      => $podcastEpisode->website_publish_on,
         ];
    }
    // **************************************************************************************************************
    // END: PODCAST EPISODES
    // **************************************************************************************************************



    // **************************************************************************************************************
    // START: VIDEO EPISODES
    // **************************************************************************************************************

    /**
     * Get all the video episodes for all the video shows
     *
     * @param  array  $videoShowIDs                A simple array of the video show ID's
     * @param  int    $numberVideoEpisodes         The number of video episodes to select
     * @return void
     */
    private function getAllTransformedVideoEpisodesForAllVideoShows($videoShowIDs, $numberVideoEpisodes)
    {
        $transformedVideoEpisodes = [];
        
        foreach ($videoShowIDs as $videoShowID) {
            $videoEpisodes = $this->getRecentVideoEpisodes($videoShowID, $numberVideoEpisodes);
            $transformedVideoEpisodes[] = $this->transformAllVideoEpisodes($videoEpisodes);
        }

        return $transformedVideoEpisodes;
    }

    /**
     * Get recent video episodes
     *
     * @param  int   $videoShowID                 ID of video_episodes table
     * @param  int   $numberVideoEpisodes         The number of video episodes to select
     * @return collection | null
     */
    private function getRecentVideoEpisodes($videoShowID, $numberVideoEpisodes)
    {
        return DB::table('video_episodes')
            ->where([
                ['video_show_id', '=', $videoShowID],
                ['website_enabled', '=', true],
                ['website_publish_on', '<', CarbonImmutable::now(config('app.timezone'))],
            ])
            ->orderBy('website_publish_on', 'desc')
            ->take($numberVideoEpisodes)
            ->get()
        ;
    }

    /**
     * Transform video episodes
     * 
     * @param   collection  $videoEpisodes      Collection of video episodes (retrieved from the database)
     * @return  array
     */
    private function transformAllVideoEpisodes($videoEpisodes)
    {
        $transformedVideoEpisodes = [];

        foreach ($videoEpisodes as $videoEpisode) {
            $transformedVideoEpisodes[] = $this->transformVideoEpisode($videoEpisode);
        }

        return $transformedVideoEpisodes;
    }

    /**
     * Transform an individual video episode, specifically for the home page
     *
     * @param  object  $videoEpisode
     * @return array
     */
    private function transformVideoEpisode($videoEpisode)
    {
        return [
            'video_show_id'           => $videoEpisode->video_show_id,
            'title'                   => $videoEpisode->title,
            'slug'                    => $videoEpisode->slug,
            'website_excerpt'         => $videoEpisode->website_excerpt,
            'website_featured_image'  => $videoEpisode->website_featured_image,
            'website_publish_on'      => $videoEpisode->website_publish_on,
         ];
    }    
    // **************************************************************************************************************
    // END: VIDEO EPISODES
    // **************************************************************************************************************    
}