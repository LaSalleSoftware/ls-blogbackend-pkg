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

namespace Lasallesoftware\Blogbackend\Models;

// LaSalle Software
use Lasallesoftware\Library\Common\Models\CommonModel;

// Laravel class
use Illuminate\Support\Carbon;

// Laravel facade
use Illuminate\Support\Facades\Auth;


/**
 * Class Post
 *
 * @package Lasallesoftware\Library\Blogbackend\Models
 */
class Post extends CommonModel
{
    ///////////////////////////////////////////////////////////////////
    //////////////          PROPERTIES              ///////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'posts';

    /**
     * Which fields may be mass assigned
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_description',
        'enabled',
        'featured_image',
        'publish_on',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * LaSalle Software handles the created_at and updated_at fields, so false.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'publish_on'    => 'date',
    ];


    ///////////////////////////////////////////////////////////////////
    //////////////         MODEL EVENTS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * The "booting" method of the model.
     *
     * Laravel will execute this function automatically
     * https://github.com/laravel/framework/blob/e6c8aa0e39d8f91068ad1c299546536e9f25ef63/src/Illuminate/Database/Eloquent/Model.php#L197
     *
     * @return void
     */
    protected static function boot()
    {
        // parent's boot function should occur first
        parent::boot();

        // Do this when the "creating" model event is dispatched
        // https://laracasts.com/discuss/channels/eloquent/is-there-any-way-to-listen-for-an-eloquent-event-in-the-model-itself
        //
        static::creating(function($post) {
            self::populatePersonbydomainidField($post);
            self::populateTitleField($post);
            self::populateSlugField($post);
            self::populateContentField($post);
            self::populateExcerptField($post);
            self::populateMetadescriptionField($post);
            self::populatePublishonField($post);  //**the creationRule negates the need for this**
            self::populateUpdateFields($post);    //**definitely only when creating!**
        });

        // Do this when the "updating" model event is dispatched
        static::updating(function($post) {
            self::populatePersonbydomainidField($post);
            self::populateTitleField($post);
            self::populateSlugField($post);
            self::populateContentField($post);
            self::populateExcerptField($post);
            self::populateMetadescriptionField($post);
            self::populatePublishonField($post);
        });
    }


    /**
     * Populate the "personbydomain_id" field
     *
     * @param  Post  $post
     */
    private static function populatePersonbydomainidField(Post $post)
    {
        // without any "save", this following statement actually populates the field!

        // if we are in the midst of the database seeding for the blog test data, then
        // no one is actually logged in, so we want to use the personbydomain_id specified
        // in the test data
        if (defined("BLOGBACKENDDBSEEDINGTESTDATA")) {
            return;
        }

        $post->personbydomain_id = Auth::id();
    }

    /**
     * Populate the "title" field.
     *
     * @param  Post  $post
     */
    private static function populateTitleField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->title = self::deepWashText($post->title);
    }


    /**
     * Populate the "slug" field.
     *
     * @param  Post  $post
     */
    private static function populateSlugField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->slug = self::makeSlug($post->slug, $post->title, 'posts', $post->id);
    }

    /**
     * Populate the "content" field.
     *
     * @param  Post  $post
     */
    private static function populateContentField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->content = self::washContent($post->content);
    }

    /**
     * Populate the "excerpt" field.
     *
     * @param  Post  $post
     */
    private static function populateExcerptField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->excerpt = self::makeExcerpt($post->excerpt, $post->content);
    }

    /**
     * Populate the "meta_description" field.
     *
     * @param  Post  $post
     */
    private static function populateMetadescriptionField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->meta_description = self::makeMetadescription($post->meta_description, $post->content);
    }

    /**
     * Populate the "publish_on" field.
     *
     * @param  Post  $post
     */
    private static function populatePublishonField(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->publish_on = (($post->publish_on == "") || (is_null($post->publish_on))) ? Carbon::now(null) : $post->publish_on;
    }

    /**
     * Populate the "updated_at" and "updated_by" fields.
     *
     * The purpose of this unusual action is to sort posts by the "updated_at" field in the post dropdown. The updated
     * fields are not populated during created so, just for the purpose of sorting for the dropdown, we are putting in
     * the "created_at" value into the "updated_at" field. Just out of habit, populating the "updated_by" field as well.
     *
     * @param  Post  $post
     */
    private static function populateUpdateFields(Post $post)
    {
        // without any "save", this following statement actually populates the field!
        $post->updated_at = $post->created_at;
        $post->updated_by = $post->created_by;
    }


    ///////////////////////////////////////////////////////////////////
    //////////////        RELATIONSHIPS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /*
    * A post may have one, and only one, category.
    * A category may be associated with multiple posts.
    *
    * Method name must be:
    *    * the model name,
    *    * NOT the table name,
    *    * singular;
    *    * lowercase.
    *
    * @return Eloquent
    */
    public function category()
    {
        return $this->BelongsTo('Lasallesoftware\Blogbackend\Models\Category');
    }

    /*
     * One post may have lots of tags associated with it.
     * One tag may be associated with a lot of different posts.
     * Therefore, need a pivot table to implement this "many to many" relationship.
     *
     * Method name must be:
     *    * the model name,
     *    * NOT the table name,
     *    * singular;
     *    * lowercase.
     *
     * @return Eloquent
     */
    public function tag()
    {
        return $this->belongsToMany('Lasallesoftware\Blogbackend\Models\Tag', 'post_tag', 'post_id', 'tag_id');
    }

    /*
     * An post may have one, and only one, installed_domain.
     * An installed_domain may be associated with multiple posts.
     *
     * Method name must be:
     *    * the model name,
     *    * NOT the table name,
     *    * singular;
     *    * lowercase.
     *
     * @return Eloquent
     */
    public function installed_domain()
    {
        return $this->belongsTo('Lasallesoftware\Library\Profiles\Models\Installed_domain');
    }

    /*
     * One to one relationship with personbydomain.
     *
     * Method name must be:
     *    * the model name,
     *    * NOT the table name,
     *    * singular;
     *    * lowercase.
     *
     * @return Eloquent
     */
    public function personbydomain()
    {
        return $this->hasOne('Lasallesoftware\Library\Authentication\Models\Personbydomain');
    }

    /*
     * After a blog post is published, there may be updates to that blog post. Instead of appending that blog post,
     * I create a new database record in the postupdates db table. This record includes a "post_id" field, which
     * associates this particular postupdates record with the posts that it is updating.
     *
     * One postupdates db record must be associated with one, and only one, posts db record.
     * Multiple postupdates db records can be associated with the same posts db record.
     *
     * Method name must be:
     *    * the model name,
     *    * NOT the table name,
     *    * singular;
     *    * lowercase.
     *
     * @return Eloquent
     */
    public function postupdate()
    {
        return $this->hasMany('Lasallesoftware\Blogbackend\Models\Postupdate');
    }


    ///////////////////////////////////////////////////////////////////
    //////////////         CUSTOM STUFF             ///////////////////
    ///////////////////////////////////////////////////////////////////

    // blank
}
