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

/**
 * Class Category
 *
 * @package Lasallesoftware\Library\Blogbackend\Models
 */
class Tag extends CommonModel
{
    ///////////////////////////////////////////////////////////////////
    //////////////          PROPERTIES              ///////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'tags';

    /**
     * Which fields may be mass assigned
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'enabled',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * LaSalle Software handles the created_at and updated_at fields, so false.
     *
     * @var bool
     */
    public $timestamps = false;


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
        static::creating(function($tag) {
            self::populateTitleField($tag);
        });

        // Do this when the "updating" model event is dispatched
        static::updating(function($tag) {
            self::populateTitleField($tag);
        });
    }


    /**
     * Populate the "lookup_domain_id" field.
     *
     * @param  Tag  $tag
     */
    private static function populateTitleField(Tag $tag)
    {
        // without any "save", this following statement actually populates the field!
        $tag->title = self::deepWashText($tag->title);
    }


    ///////////////////////////////////////////////////////////////////
    //////////////        RELATIONSHIPS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * An tag may have one, and only one, installed_domain.
     * An installed_domain may be associated with multiple tags.
     *
     * Method name must be the model name, *not* the table name
     *
     * @return Eloquent
     */
    public function installed_domain()
    {
        return $this->belongsTo('Lasallesoftware\Library\Profiles\Models\Installed_domain');
    }

    /*
     * One post may have lots of tags associated with it.
     * One tag may be associated with a lot of different posts.
     * Therefore, need a pivot table to implement this "many to many" relationship.
     *
     * Method name must be the model name, *not* the table name
     *
     * @return Eloquent
     */
    public function post()
    {
        return $this->belongsToMany('Lasallesoftware\Blogbackend\Models\Post', 'post_tag', 'tag_id', 'post_id');
    }
}
