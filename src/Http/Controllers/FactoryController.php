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
 * @copyright  (c) 2019-2022 The South LaSalle Trading Corporation
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
use Lasallesoftware\Librarybackend\Common\Http\Controllers\CommonController;

use Lasallesoftware\Blogbackend\Database\Factories\CategoryFactory;
use Lasallesoftware\Blogbackend\Database\Factories\TagFactory;
use Lasallesoftware\Blogbackend\Database\Factories\PostFactory;
use Lasallesoftware\Blogbackend\Database\Factories\PostupdateFactory;

// Laravel facade
use Illuminate\Support\Facades\DB;


class FactoryController extends CommonController
{
    public function Bob(CategoryFactory $categoryFactory,
                        TagFactory $tagFactory,
                        PostFactory $postFactory,
                        PostupdateFactory $postupdateFactory)
    {

        // let's have 5 categories
        $categoryFactory->createCategoryRecordsFromTheCategoryFactory(5);

        // let's have 25 tags
        $tagFactory->createTagRecordsFromTheTagFactory(25);

        // let's create some posts & postupdates together
        $postFactory->createPostRecordsFromThePostFactory(1, 1, 1, 8);
        $post = DB::table('posts')->latest()->first();
        $postupdateFactory->createPostupdateRecordsFromThePostupdateFactory(5, 1, 1, $post->id);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 8]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 9]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 10]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 11]);

        $postFactory->createPostRecordsFromThePostFactory(1, 1, 1, 9);
        $post = DB::table('posts')->latest()->first();
        $postupdateFactory->createPostupdateRecordsFromThePostupdateFactory(3, 1, 1, $post->id);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 8]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 9]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 10]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 11]);

        $postFactory->createPostRecordsFromThePostFactory(1, 1, 2, 10);
        $post = DB::table('posts')->latest()->first();
        $postupdateFactory->createPostupdateRecordsFromThePostupdateFactory(6, 1, 2, $post->id);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 8]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 9]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 10]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 11]);

        $postFactory->createPostRecordsFromThePostFactory(1, 1, 2, 11);
        $post = DB::table('posts')->latest()->first();
        $postupdateFactory->createPostupdateRecordsFromThePostupdateFactory(1, 1, 2, $post->id);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 8]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 9]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 10]);
        DB::table('post_tag')->insert(['post_id' => $post->id, 'tag_id' => 11]);


        // let's create some posts without postupdates
        $postFactory->createPostRecordsFromThePostFactory(12, 1, 1, 12);
        $postFactory->createPostRecordsFromThePostFactory(12, 1, 2, 12);
        $postFactory->createPostRecordsFromThePostFactory(12, 1, 3, 12);
        $postFactory->createPostRecordsFromThePostFactory(12, 1, 4, 12);
        $postFactory->createPostRecordsFromThePostFactory(12, 1, 5, 12);

        // seems that there are only so many different titles created, and it is choking my slug!
        //$postFactory->createPostRecordsFromThePostFactory(250, 1, 3, 11);



        return "<h1>the factories are created!</h1>";
    }
}
