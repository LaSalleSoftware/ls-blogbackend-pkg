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

namespace Lasallesoftware\Blogbackend\Models;

// LaSalle Software
use Lasallesoftware\Librarybackend\Common\Models\CommonModel;

// Laravel class
use Illuminate\Support\Carbon;

// Laravel facade
use Illuminate\Support\Facades\Auth;

/**
 * Class Postupdate
 *
 * @package Lasallesoftware\Librarybackend\Blogbackend\Models
 */
class Postupdate extends CommonModel
{
    ///////////////////////////////////////////////////////////////////
    //////////////          PROPERTIES              ///////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'postupdates';

    /**
     * Which fields may be mass assigned
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'post_id',
        'enabled',
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
        static::creating(function($postupdate) {
            self::populatePersonbydomainidField($postupdate);
            self::populateInstalledDomainidField($postupdate);
            self::populateTitleField($postupdate);
            self::populateContentField($postupdate);
            self::populateExcerptField($postupdate);
            self::populatePublishonField($postupdate);
        });

        // Do this when the "updating" model event is dispatched
        static::updating(function($postupdate) {
            self::populatePersonbydomainidField($postupdate);
            self::populateInstalledDomainidField($postupdate);
            self::populateTitleField($postupdate);
            self::populateContentField($postupdate);
            self::populateExcerptField($postupdate);
            self::populatePublishonField($postupdate);
        });
    }


    /**
     * Populate the "personbydomain_id" field
     *
     * @param  Postupdate  $postupdate
     */
    private static function populatePersonbydomainidField($postupdate)
    {
        // without any "save", this following statement actually populates the field!

        // if we are in the midst of the database seeding for the blog test data, then
        // no one is actually logged in, so we want to use the personbydomain_id specified
        // in the test data
        if (defined("BLOGBACKENDDBSEEDINGTESTDATA")) {
            return;
        }

        $postupdate->personbydomain_id = Auth::id();
    }

    /**
     * Populate the "installed_domain_id" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populateInstalledDomainidField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        // need the installed_domain_id of the post!
        $postupdate->installed_domain_id = $postupdate->post->installed_domain_id;
    }

    /**
     * Populate the "lookup_domain_id" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populateTitleField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        $postupdate->title = self::deepWashText($postupdate->title);
    }


    /**
     * Populate the "slug" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populateSlugField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        $postupdate->slug = self::makeSlug($postupdate->slug, $postupdate->title, 'posts', $postupdate->id);
    }

    /**
     * Populate the "content" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populateContentField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        $postupdate->content = self::washContent($postupdate->content);
    }

    /**
     * Populate the "excerpt" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populateExcerptField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        $postupdate->excerpt = self::makeExcerpt($postupdate->excerpt, $postupdate->content);
    }

    /**
     * Populate the "publish_on" field.
     *
     * @param  Postupdate  $postupdate
     */
    private static function populatePublishonField(Postupdate $postupdate)
    {
        // without any "save", this following statement actually populates the field!
        $postupdate->publish_on = (($postupdate->publish_on == "") || (is_null($postupdate->publish_on))) ? Carbon::now(null) : $postupdate->publish_on;
    }


    ///////////////////////////////////////////////////////////////////
    //////////////        RELATIONSHIPS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * After a blog post is published, there may be updates to that blog post. Instead of appending that blog post,
     * I create a new database record in the postupdates db table. This record includes a "post_id" field, which
     * associates this particular postupdates record with the posts that it is updating.
     *
     * One postupdates db record must be associated with one, and only one, posts db record.
     * Multiple postupdates db records can be associated with the same posts db record.
     *
     * Method name must be the model name, *not* the table name
     *
     * @return Eloquent
     */
    public function post()
    {
        return $this->belongsTo('Lasallesoftware\Blogbackend\Models\Post');
    }
}
